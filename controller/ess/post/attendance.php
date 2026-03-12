<?php

use Core\Database;

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Set PHP timezone to Asia/Manila (Philippine Time)
date_default_timezone_set('Asia/Manila');

ob_start();

require base_path('core/middleware/employeeAuth.php');

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Force MySQL timezone to +08:00
try {
    $db->query("SET time_zone = '+08:00'");
} catch (\Throwable $th) {
    error_log("Failed to set MySQL timezone: " . $th->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    ob_clean();
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

if (!isset($input['csrf_token']) || !isset($_SESSION['csrf_token']) || $input['csrf_token'] !== $_SESSION['csrf_token']) {
    ob_clean();
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit();
}

$action = $input['action'] ?? '';
$attendanceId = $input['attendance_id'] ?? null;

// Log the request for debugging
error_log("Attendance request - Action: $action, Attendance ID: " . ($attendanceId ?? 'null'));

$employeeData = $_SESSION['employee']['employee_record_id'] ?? null;
if (is_array($employeeData) && isset($employeeData['id'])) {
    $employeeId = $employeeData['id'];
} else {
    $employeeId = $employeeData;
}

if (!$employeeId) {
    ob_clean();
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Employee not authenticated']);
    exit();
}

$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');
$currentTime = date('H:i:s');

try {
    // Get employee shift information
    $employeeShift = $db->query("
        SELECT s.*, e.shift_id 
        FROM employees e
        LEFT JOIN shifts s ON e.shift_id = s.id
        WHERE e.id = ?
    ", [$employeeId])->fetch_one();

    $db->beginTransaction();

    switch ($action) {
        case 'clock_in':
            // Check if there's ANY active attendance (not clocked_out) for today
            $existing = $db->query(
                "SELECT id, status FROM attendance 
                WHERE employee_id = ? AND date = ? AND status != 'clocked_out'",
                [$employeeId, $today]
            )->fetch_one();

            if ($existing) {
                // Log what we found
                error_log("Found existing active attendance: ID {$existing['id']} with status {$existing['status']}");

                $db->rollBack();
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'You are already clocked in',
                    'attendance_id' => $existing['id'],
                    'status' => $existing['status']
                ]);
                exit();
            }

            // Calculate late minutes based on shift
            $lateMinutes = 0;
            $lateStatus = 'on_time';

            if ($employeeShift && $employeeShift['start_time']) {
                $shiftStart = strtotime($today . ' ' . $employeeShift['start_time']);
                $clockInTime = strtotime($now);
                $gracePeriod = $employeeShift['grace_period_minutes'] ?? 15;

                $lateMinutes = max(0, floor(($clockInTime - $shiftStart) / 60));

                if ($lateMinutes > $gracePeriod) {
                    $lateStatus = 'late';
                } elseif ($lateMinutes > 0) {
                    $lateStatus = 'grace_period';
                }
            }

            $db->query(
                "INSERT INTO attendance (
                    employee_id, shift_id, clock_in, status, date, 
                    late_minutes, late_status, created_at
                ) VALUES (?, ?, NOW(), 'clocked_in', ?, ?, ?, NOW())",
                [
                    $employeeId,
                    $employeeShift['shift_id'] ?? null,
                    $today,
                    $lateMinutes,
                    $lateStatus
                ]
            );

            $newAttendanceId = $db->lastInsertId();

            $db->commit();

            $insertedTime = $db->query(
                "SELECT TIME_FORMAT(clock_in, '%h:%i %p') as time FROM attendance WHERE id = ?",
                [$newAttendanceId]
            )->fetch_one();

            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $lateMinutes > 0 ? 'Clocked in ' . $lateMinutes . ' minutes late' : 'Clocked in on time',
                'attendance_id' => $newAttendanceId,
                'status' => 'clocked_in',
                'elapsed_seconds' => 0,
                'pause_total' => 0,
                'late_minutes' => $lateMinutes,
                'late_status' => $lateStatus,
                'clock_in_time' => $insertedTime['time'] ?? date('h:i A')
            ]);
            break;

        case 'pause':
            // First, verify we have a valid attendance ID or find one
            if (!$attendanceId) {
                $activeAttendance = $db->query(
                    "SELECT id FROM attendance 
                    WHERE employee_id = ? AND date = ? AND status = 'clocked_in'",
                    [$employeeId, $today]
                )->fetch_one();

                if ($activeAttendance) {
                    $attendanceId = $activeAttendance['id'];
                    error_log("Auto-found attendance ID $attendanceId for pause");
                } else {
                    throw new Exception('No active shift found to pause');
                }
            }

            // Verify attendance belongs to employee and is clocked in
            $attendance = $db->query(
                "SELECT id FROM attendance 
                WHERE id = ? AND employee_id = ? AND status = 'clocked_in'",
                [$attendanceId, $employeeId]
            )->fetch_one();

            if (!$attendance) {
                throw new Exception('Cannot pause - no active shift found or shift is already paused');
            }

            // Update to paused status
            $db->query(
                "UPDATE attendance 
                SET pause_start = NOW(), status = 'paused', updated_at = NOW() 
                WHERE id = ? AND employee_id = ?",
                [$attendanceId, $employeeId]
            );

            // Get elapsed time so far
            $currentAttendance = $db->query(
                "SELECT 
                    TIMESTAMPDIFF(SECOND, clock_in, NOW()) - (pause_total * 60) as elapsed,
                    pause_total
                FROM attendance 
                WHERE id = ?",
                [$attendanceId]
            )->fetch_one();

            $db->commit();

            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Shift paused',
                'status' => 'paused',
                'elapsed_seconds' => (int) ($currentAttendance['elapsed'] ?? 0),
                'pause_total' => (int) ($currentAttendance['pause_total'] ?? 0),
                'attendance_id' => $attendanceId
            ]);
            break;

        case 'resume':
            // First, verify we have a valid attendance ID or find one
            if (!$attendanceId) {
                $pausedAttendance = $db->query(
                    "SELECT id, pause_start, pause_total FROM attendance 
                    WHERE employee_id = ? AND date = ? AND status = 'paused'",
                    [$employeeId, $today]
                )->fetch_one();

                if ($pausedAttendance) {
                    $attendanceId = $pausedAttendance['id'];
                    $attendance = $pausedAttendance;
                    error_log("Auto-found attendance ID $attendanceId for resume");
                } else {
                    throw new Exception('No paused shift found to resume');
                }
            } else {
                // Get pause start time
                $attendance = $db->query(
                    "SELECT pause_start, pause_total FROM attendance 
                    WHERE id = ? AND employee_id = ? AND status = 'paused'",
                    [$attendanceId, $employeeId]
                )->fetch_one();
            }

            if (!$attendance || !$attendance['pause_start']) {
                throw new Exception('Cannot resume - no paused shift found');
            }

            // Calculate pause duration in minutes
            $pauseMinutes = round((strtotime($now) - strtotime($attendance['pause_start'])) / 60);

            // Add pause minutes and set status back to clocked_in
            $db->query(
                "UPDATE attendance 
                SET pause_total = pause_total + ?, 
                    pause_start = NULL, 
                    status = 'clocked_in',
                    updated_at = NOW()
                WHERE id = ? AND employee_id = ?",
                [$pauseMinutes, $attendanceId, $employeeId]
            );

            // Get updated elapsed time
            $currentAttendance = $db->query(
                "SELECT 
                    TIMESTAMPDIFF(SECOND, clock_in, NOW()) - ((pause_total + ?) * 60) as elapsed,
                    pause_total + ? as new_pause_total
                FROM attendance 
                WHERE id = ?",
                [$pauseMinutes, $pauseMinutes, $attendanceId]
            )->fetch_one();

            $db->commit();

            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Shift resumed',
                'status' => 'clocked_in',
                'elapsed_seconds' => (int) ($currentAttendance['elapsed'] ?? 0),
                'pause_total' => (int) ($currentAttendance['new_pause_total'] ?? 0),
                'attendance_id' => $attendanceId
            ]);
            break;

        case 'clock_out':
            // First, verify we have a valid attendance ID or find one
            if (!$attendanceId) {
                $activeAttendance = $db->query(
                    "SELECT id, clock_in, pause_total, late_minutes, shift_id, status FROM attendance 
                    WHERE employee_id = ? AND date = ? AND status != 'clocked_out'",
                    [$employeeId, $today]
                )->fetch_one();

                if ($activeAttendance) {
                    $attendanceId = $activeAttendance['id'];
                    $attendance = $activeAttendance;
                    error_log("Auto-found attendance ID $attendanceId with status {$activeAttendance['status']} for clock out");
                } else {
                    throw new Exception('No active shift found to clock out');
                }
            } else {
                // Get attendance record
                $attendance = $db->query(
                    "SELECT clock_in, pause_total, late_minutes, shift_id, status FROM attendance 
                    WHERE id = ? AND employee_id = ? AND status != 'clocked_out'",
                    [$attendanceId, $employeeId]
                )->fetch_one();
            }

            if (!$attendance) {
                throw new Exception('No active shift found');
            }

            // Log the attendance status for debugging
            error_log("Clocking out attendance ID $attendanceId with status {$attendance['status']}");

            // Calculate total worked time
            $clockIn = strtotime($attendance['clock_in']);
            $clockOut = strtotime($now);
            $pauseMinutes = $attendance['pause_total'] ?? 0;

            // Total seconds worked (excluding pauses)
            $totalSeconds = $clockOut - $clockIn - ($pauseMinutes * 60);
            $totalHours = $totalSeconds / 3600;

            // Regular hours (max 8) and overtime
            $regularHours = min($totalHours, 8);
            $overtimeHours = max($totalHours - 8, 0);

            // Check if early clock-out (if shift has end time)
            $earlyDeparture = 0;
            if ($attendance['shift_id']) {
                $shift = $db->query("SELECT end_time FROM shifts WHERE id = ?", [$attendance['shift_id']])->fetch_one();
                if ($shift && $shift['end_time']) {
                    $shiftEnd = strtotime($today . ' ' . $shift['end_time']);
                    if ($clockOut < $shiftEnd) {
                        $earlyDeparture = floor(($shiftEnd - $clockOut) / 60);
                    }
                }
            }

            // Update attendance record
            $db->query(
                "UPDATE attendance 
                SET clock_out = NOW(),
                    regular_hours = ?,
                    overtime_hours = ?,
                    early_departure_minutes = ?,
                    status = 'clocked_out',
                    updated_at = NOW()
                WHERE id = ? AND employee_id = ?",
                [$regularHours, $overtimeHours, $earlyDeparture, $attendanceId, $employeeId]
            );

            // Update 15-day summary with late minutes
            updateAttendanceSummary($db, $employeeId, $regularHours, $overtimeHours, $attendance['late_minutes'] ?? 0);

            $db->commit();

            $message = 'Clocked out successfully';
            if ($earlyDeparture > 0) {
                $message .= ' (' . $earlyDeparture . ' minutes early)';
            }

            // Get clock out time for display
            $clockOutTime = $db->query(
                "SELECT TIME_FORMAT(clock_out, '%h:%i %p') as time FROM attendance WHERE id = ?",
                [$attendanceId]
            )->fetch_one();

            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $message,
                'status' => 'clocked_out',
                'regular_hours' => $regularHours,
                'overtime_hours' => $overtimeHours,
                'early_departure' => $earlyDeparture,
                'clock_out_time' => $clockOutTime['time'] ?? date('h:i A')
            ]);
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    // Log the error
    error_log("Attendance error: " . $e->getMessage());

    // Clear buffer and send error response
    ob_clean();
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Error $e) {
    // Catch PHP errors
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    // Log the error
    error_log("Attendance PHP error: " . $e->getMessage());

    ob_clean();
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred'
    ]);
}

// End output buffering and flush
ob_end_flush();

// attendance summary for periods ending on 5th and 20th of each month
function updateAttendanceSummary($db, $employeeId, $regularHours, $overtimeHours, $lateMinutes = 0)
{
    $today = date('Y-m-d');
    $currentDay = (int) date('j');
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Determine which pay period this belongs to (5th or 20th)
    if ($currentDay <= 5) {
        // Period from 21st of previous month to 5th of current month
        $periodEnd = "$currentYear-$currentMonth-05";

        // Calculate period start (21st of previous month)
        $prevMonth = $currentMonth - 1;
        $prevYear = $currentYear;

        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear = $currentYear - 1;
        }

        $periodStart = "$prevYear-" . str_pad($prevMonth, 2, '0', STR_PAD_LEFT) . "-21";

    } elseif ($currentDay <= 20) {
        // Period from 6th to 20th of current month
        $periodStart = "$currentYear-$currentMonth-06";
        $periodEnd = "$currentYear-$currentMonth-20";
    } else {
        // Period from 21st to end of month
        $periodStart = "$currentYear-$currentMonth-21";

        // Calculate period end (5th of next month)
        $nextMonth = $currentMonth + 1;
        $nextYear = $currentYear;

        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear = $currentYear + 1;
        }

        $periodEnd = "$nextYear-" . str_pad($nextMonth, 2, '0', STR_PAD_LEFT) . "-05";
    }

    if (!isset($periodStart) || !isset($periodEnd)) {
        error_log("Could not determine period for date: $today");
        return;
    }

    // Log the period being used for debugging
    error_log("Period for date $today: $periodStart to $periodEnd");

    // Check if summary exists for this period
    $summary = $db->query(
        "SELECT id, total_regular_hours, total_overtime_hours, total_late_minutes 
        FROM attendance_summary 
        WHERE employee_id = ? AND period_start = ? AND period_end = ?",
        [$employeeId, $periodStart, $periodEnd]
    )->fetch_one();

    if ($summary) {
        // Update existing summary
        $db->query(
            "UPDATE attendance_summary 
            SET total_regular_hours = total_regular_hours + ?,
                total_overtime_hours = total_overtime_hours + ?,
                total_late_minutes = total_late_minutes + ?,
                updated_at = NOW()
            WHERE id = ?",
            [$regularHours, $overtimeHours, $lateMinutes, $summary['id']]
        );
        error_log("Updated summary ID {$summary['id']}: +{$regularHours} reg, +{$overtimeHours} OT, +{$lateMinutes} late");
    } else {
        // Create new summary
        $db->query(
            "INSERT INTO attendance_summary 
            (employee_id, period_start, period_end, total_regular_hours, total_overtime_hours, total_late_minutes, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [$employeeId, $periodStart, $periodEnd, $regularHours, $overtimeHours, $lateMinutes]
        );
        $newId = $db->lastInsertId();
        error_log("Created new summary ID $newId for period $periodStart to $periodEnd");
    }
}