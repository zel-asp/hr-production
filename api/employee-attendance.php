<?php
require_once __DIR__ . '/../cors-handler.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/keys.php';

$apiInfo = validateApiKey();
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// Only allow GET requests
if ($method !== 'GET') {
    sendError('Method not allowed. Use GET.', 405);
}

// Get parameters
$department = $_GET['department'] ?? null;
$employee_id = $_GET['employee_id'] ?? null;
$date = $_GET['date'] ?? date('Y-m-d'); // Default to today
$include_history = $_GET['include_history'] ?? 'false';

try {
    // Base query to get employees with their shift information
    $sql = "
        SELECT 
            e.id,
            e.employee_number,
            e.full_name,
            e.email,
            e.phone,
            e.position,
            e.department,
            e.hourly_rate,
            e.status as employment_status,
            e.role,
            e.shift_id,
            s.shift_name,
            s.shift_code,
            s.start_time as shift_start,
            s.end_time as shift_end,
            s.grace_period_minutes
        FROM employees e
        LEFT JOIN shifts s ON e.shift_id = s.id
        WHERE 1=1
    ";

    $params = [];

    // Add filters
    if ($department) {
        $sql .= " AND e.department = ?";
        $params[] = $department;
    }

    if ($employee_id) {
        $sql .= " AND e.id = ?";
        $params[] = $employee_id;
    }

    $sql .= " ORDER BY e.department, e.full_name";

    // Get employees
    $employees = $db->query($sql, $params)->find();

    if (empty($employees)) {
        sendResponse([
            'date' => $date,
            'department' => $department,
            'total_employees' => 0,
            'employees' => []
        ]);
    }

    // Get attendance for the specified date
    $attendanceData = [];
    foreach ($employees as $emp) {
        $empId = $emp['id'];

        // Get today's attendance
        $attendance = $db->query(
            "SELECT 
                id,
                clock_in,
                clock_out,
                pause_start,
                pause_total,
                late_minutes,
                late_status,
                regular_hours,
                overtime_hours,
                status as attendance_status,
                date
             FROM attendance 
             WHERE employee_id = ? AND date = ?
             ORDER BY clock_in DESC
             LIMIT 1",
            [$empId, $date]
        )->fetch_one();

        // Get schedule for today
        $schedule = $db->query(
            "SELECT 
                es.*,
                s.shift_name,
                s.start_time as shift_start,
                s.end_time as shift_end,
                s.grace_period_minutes
             FROM employee_schedules es
             LEFT JOIN shifts s ON es.shift_id = s.id
             WHERE es.employee_id = ? AND es.schedule_date = ?
             LIMIT 1",
            [$empId, $date]
        )->fetch_one();

        // If no schedule found, use employee's default shift
        if (!$schedule && $emp['shift_id']) {
            $schedule = [
                'schedule_date' => $date,
                'shift_id' => $emp['shift_id'],
                'shift_name' => $emp['shift_name'],
                'shift_start' => $emp['shift_start'],
                'shift_end' => $emp['shift_end'],
                'grace_period_minutes' => $emp['grace_period_minutes'],
                'status' => 'scheduled',
                'is_default_shift' => true
            ];
        }

        // Determine present status
        $present = false;
        $status = 'absent';
        $status_color = 'red';

        if ($attendance) {
            if ($attendance['attendance_status'] === 'clocked_in' || $attendance['attendance_status'] === 'paused') {
                $present = true;
                $status = 'present';
                $status_color = 'green';
            } elseif ($attendance['attendance_status'] === 'clocked_out') {
                $present = true;
                $status = 'completed';
                $status_color = 'blue';
            }
        }

        // Calculate if employee is late
        $is_late = false;
        $late_minutes = 0;
        if ($attendance && $attendance['late_status'] === 'late') {
            $is_late = true;
            $late_minutes = $attendance['late_minutes'];
        }

        // Get attendance history if requested
        $history = null;
        if ($include_history === 'true') {
            $history = $db->query(
                "SELECT 
                    date,
                    clock_in,
                    clock_out,
                    regular_hours,
                    overtime_hours,
                    late_status,
                    late_minutes,
                    status
                 FROM attendance 
                 WHERE employee_id = ?
                 ORDER BY date DESC
                 LIMIT 30",
                [$empId]
            )->find();
        }

        $attendanceData[] = [
            'employee' => [
                'id' => $emp['id'],
                'employee_number' => $emp['employee_number'],
                'full_name' => $emp['full_name'],
                'email' => $emp['email'],
                'phone' => $emp['phone'],
                'position' => $emp['position'],
                'department' => $emp['department'],
                'hourly_rate' => $emp['hourly_rate'],
                'employment_status' => $emp['employment_status'],
                'role' => $emp['role']
            ],
            'shift' => $schedule ? [
                'shift_id' => $schedule['shift_id'],
                'shift_name' => $schedule['shift_name'] ?? $emp['shift_name'],
                'shift_code' => $schedule['shift_code'] ?? null,
                'start_time' => $schedule['shift_start'] ?? $emp['shift_start'],
                'end_time' => $schedule['shift_end'] ?? $emp['shift_end'],
                'grace_period' => $schedule['grace_period_minutes'] ?? $emp['grace_period_minutes'],
                'schedule_status' => $schedule['status'] ?? 'scheduled',
                'is_default_shift' => $schedule['is_default_shift'] ?? false
            ] : null,
            'attendance' => $attendance ? [
                'id' => $attendance['id'],
                'clock_in' => $attendance['clock_in'],
                'clock_out' => $attendance['clock_out'],
                'pause_start' => $attendance['pause_start'],
                'pause_total' => $attendance['pause_total'],
                'regular_hours' => $attendance['regular_hours'],
                'overtime_hours' => $attendance['overtime_hours'],
                'late_minutes' => $attendance['late_minutes'],
                'late_status' => $attendance['late_status'],
                'status' => $attendance['attendance_status']
            ] : null,
            'status' => [
                'present' => $present,
                'status' => $status,
                'color' => $status_color,
                'is_late' => $is_late,
                'late_minutes' => $late_minutes,
                'has_attendance' => $attendance ? true : false,
                'has_schedule' => $schedule ? true : false
            ],
            'history' => $history
        ];
    }

    // Calculate summary statistics
    $summary = [
        'total_employees' => count($employees),
        'present' => count(array_filter($attendanceData, fn($a) => $a['status']['present'])),
        'absent' => count(array_filter($attendanceData, fn($a) => !$a['status']['present'] && $a['status']['has_schedule'])),
        'late' => count(array_filter($attendanceData, fn($a) => $a['status']['is_late'])),
        'no_schedule' => count(array_filter($attendanceData, fn($a) => !$a['status']['has_schedule'])),
        'with_attendance' => count(array_filter($attendanceData, fn($a) => $a['status']['has_attendance'])),
        'completion_rate' => 0
    ];

    if ($summary['total_employees'] > 0) {
        $summary['completion_rate'] = round(($summary['with_attendance'] / $summary['total_employees']) * 100, 2);
    }

    sendResponse([
        'date' => $date,
        'department' => $department,
        'summary' => $summary,
        'employees' => $attendanceData
    ]);

} catch (Exception $e) {
    sendError('Failed to fetch employee attendance: ' . $e->getMessage(), 500);
}