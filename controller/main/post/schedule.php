<?php
// Add this at the very top for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
use Core\Database;
require base_path('vendor/autoload.php');
require base_path("core/middleware/adminAuth.php");


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = require base_path('config/config.php');
$db = new Database($config['database']);

set_time_limit(300);

// Clear previous session messages
unset($_SESSION['error']);
unset($_SESSION['success']);

// Response array
$response = [
    'success' => false,
    'message' => '',
    'processed' => 0,
    'shift_updates' => 0,
    'errors' => []
];

try {
    // Check if file was uploaded
    if (!isset($_FILES['attendance_file']) || $_FILES['attendance_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    $file = $_FILES['attendance_file'];
    $department = $_POST['department'] ?? '';

    // Validate file type
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension']);

    if (!in_array($extension, ['xlsx', 'xls'])) {
        throw new Exception('Invalid file type. Please upload .xlsx or .xls files only');
    }

    // Validate file size (10MB max)
    if ($file['size'] > 10 * 1024 * 1024) {
        throw new Exception('File size exceeds 10MB limit');
    }

    // Load the spreadsheet
    $spreadsheet = IOFactory::load($file['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // Remove header row
    $header = array_shift($rows);

    // Validate header columns
    $expected_headers = ['employee_id', 'employee_name', 'date', 'time_in', 'time_out', 'shift_id', 'department'];
    $header_valid = true;

    foreach ($expected_headers as $index => $expected) {
        if (!isset($header[$index]) || strtolower(trim($header[$index])) !== $expected) {
            $header_valid = false;
            break;
        }
    }

    if (!$header_valid) {
        throw new Exception('Invalid file format. Expected columns: employee_id, employee_name, date, time_in, time_out, shift_id, department');
    }

    // Begin transaction
    $db->beginTransaction();

    $processed = 0;
    $shift_updates = 0;
    $errors = [];
    $schedule_conflicts = [];
    $invalid_shifts = [];

    // Track employee shifts to update (avoid duplicates)
    $employee_shifts_to_update = [];

    // Process each row as a schedule entry
    foreach ($rows as $rowIndex => $row) {
        if (empty(array_filter($row))) {
            continue;
        }

        $rowNumber = $rowIndex + 2;

        try {
            $employee_id = trim($row[0] ?? '');
            $employee_name = trim($row[1] ?? '');
            $date_value = $row[2] ?? '';
            $time_in = $row[3] ?? '';
            $time_out = $row[4] ?? '';
            $shift_id = trim($row[5] ?? '');
            $dept = trim($row[6] ?? '');

            if (empty($employee_id) || empty($date_value)) {
                $errors[] = "Row {$rowNumber}: Missing required fields (employee_id or date)";
                continue;
            }

            // Parse date
            if (is_numeric($date_value)) {
                $date = Date::excelToDateTimeObject($date_value);
                $formatted_date = $date->format('Y-m-d');
            } else {
                $date = DateTime::createFromFormat('Y-m-d', $date_value) ?:
                    DateTime::createFromFormat('m/d/Y', $date_value) ?:
                    DateTime::createFromFormat('d/m/Y', $date_value);

                if (!$date) {
                    $errors[] = "Row {$rowNumber}: Invalid date format. Use YYYY-MM-DD";
                    continue;
                }
                $formatted_date = $date->format('Y-m-d');
            }

            // Parse times
            $formatted_time_in = null;
            $formatted_time_out = null;

            if (!empty($time_in)) {
                if (is_numeric($time_in)) {
                    $time = Date::excelToDateTimeObject($time_in);
                    $formatted_time_in = $time->format('H:i:s');
                } else {
                    $time = DateTime::createFromFormat('H:i', $time_in) ?:
                        DateTime::createFromFormat('H:i:s', $time_in);
                    if ($time) {
                        $formatted_time_in = $time->format('H:i:s');
                    }
                }
            }

            if (!empty($time_out)) {
                if (is_numeric($time_out)) {
                    $time = Date::excelToDateTimeObject($time_out);
                    $formatted_time_out = $time->format('H:i:s');
                } else {
                    $time = DateTime::createFromFormat('H:i', $time_out) ?:
                        DateTime::createFromFormat('H:i:s', $time_out);
                    if ($time) {
                        $formatted_time_out = $time->format('H:i:s');
                    }
                }
            }

            // Check if employee exists - with multiple format attempts
            $employee = null;
            $original_id = $employee_id;

            // Try 1: Direct match as entered
            $employee = $db->query("SELECT id, full_name, shift_id, employee_number FROM employees WHERE employee_number = ?", [
                $employee_id
            ])->fetch_one();

            // Try 2: If it's just a number, try with EMP- prefix
            if (!$employee && is_numeric($employee_id)) {
                $formatted_id = 'EMP-' . str_pad($employee_id, 3, '0', STR_PAD_LEFT);
                error_log("Trying formatted ID: $formatted_id for original: $original_id");

                $employee = $db->query("SELECT id, full_name, shift_id, employee_number FROM employees WHERE employee_number = ?", [
                    $formatted_id
                ])->fetch_one();

                if ($employee) {
                    $employee_id = $formatted_id; // Use the formatted version for display
                }
            }

            // Try 3: If it has EMP- but wrong padding (EMP-60 vs EMP-060)
            if (!$employee && preg_match('/^EMP-(\d+)$/i', $employee_id, $matches)) {
                $number = $matches[1];
                $formatted_id = 'EMP-' . str_pad($number, 3, '0', STR_PAD_LEFT);
                error_log("Repadding EMP number: $formatted_id for original: $original_id");

                $employee = $db->query("SELECT id, full_name, shift_id, employee_number FROM employees WHERE employee_number = ?", [
                    $formatted_id
                ])->fetch_one();
            }

            // Try 4: Try by ID (if they used the database ID)
            if (!$employee && is_numeric($employee_id)) {
                $employee = $db->query("SELECT id, full_name, shift_id, employee_number FROM employees WHERE id = ?", [
                    $employee_id
                ])->fetch_one();
            }

            if (!$employee) {
                $errors[] = "Row {$rowNumber}: Employee '{$original_id}' not found in database. Available: EMP-060 (Janzel Dolo), EMP-063 (Uzumaki Dela CRUZ)";

                // Log all available employees for debugging
                $all_emps = $db->query("SELECT employee_number, full_name FROM employees")->find();
                $available = [];
                foreach ($all_emps as $emp) {
                    $available[] = $emp['employee_number'] . ' (' . $emp['full_name'] . ')';
                }
                error_log("Available employees: " . implode(', ', $available));
                continue;
            }

            $db_employee_id = $employee['id'];
            error_log("Found employee: {$employee['full_name']} with ID: {$db_employee_id}, Number: {$employee['employee_number']}");

            if (!$employee) {
                $errors[] = "Row {$rowNumber}: Employee ID {$employee_id} not found in database";
                continue;
            }

            $db_employee_id = $employee['id'];

            // Check if employee is on leave for this date
            $leave = $db->query("
                SELECT lr.* 
                FROM leave_requests lr 
                WHERE lr.employee_id = ? 
                AND lr.status = 'Approved'
                AND ? BETWEEN lr.start_date AND lr.end_date
            ", [$db_employee_id, $formatted_date])->fetch_one();

            if ($leave) {
                $schedule_conflicts[] = [
                    'row' => $rowNumber,
                    'employee_name' => $employee['full_name'],
                    'date' => $formatted_date,
                    'leave_type' => $leave['leave_type'],
                    'leave_dates' => $leave['start_date'] . ' to ' . $leave['end_date']
                ];
                continue; // Skip scheduling for employees on leave
            }

            // Validate shift_id if provided
            $valid_shift_id = null;
            if (!empty($shift_id) && is_numeric($shift_id)) {
                $shift = $db->query("SELECT id, shift_name FROM shifts WHERE id = ?", [$shift_id])->fetch_one();

                if ($shift) {
                    $valid_shift_id = $shift['id'];

                    // UPDATE EMPLOYEE'S SHIFT ID IN EMPLOYEES TABLE
                    // Only update if it's different from current shift
                    if ($employee['shift_id'] != $valid_shift_id) {
                        // Mark this employee for shift update (avoid duplicates)
                        $employee_shifts_to_update[$db_employee_id] = [
                            'employee_id' => $db_employee_id,
                            'shift_id' => $valid_shift_id,
                            'employee_name' => $employee['full_name']
                        ];
                    }
                } else {
                    $invalid_shifts[] = "Row {$rowNumber}: Shift ID {$shift_id} not found in shifts table";
                }
            }

            // Check if schedule already exists for this employee on this date
            $existing = $db->query("SELECT id FROM employee_schedules WHERE employee_id = ? AND schedule_date = ?", [
                $db_employee_id,
                $formatted_date
            ])->fetch_one();

            if ($existing) {
                // Update existing schedule
                $db->query("
                    UPDATE employee_schedules 
                    SET shift_id = ?, time_in = ?, time_out = ?, department = ?, updated_at = NOW()
                    WHERE id = ?
                ", [$valid_shift_id, $formatted_time_in, $formatted_time_out, $dept, $existing['id']]);
            } else {
                // Insert new schedule
                $db->query("
                    INSERT INTO employee_schedules (
                        employee_id, shift_id, schedule_date, time_in, time_out, department, status, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, 'scheduled', NOW(), NOW())
                ", [$db_employee_id, $valid_shift_id, $formatted_date, $formatted_time_in, $formatted_time_out, $dept]);
            }

            $processed++;

        } catch (Exception $e) {
            $errors[] = "Row {$rowNumber}: " . $e->getMessage();
            continue;
        }
    }

    // UPDATE ALL EMPLOYEE SHIFT ASSIGNMENTS (after processing all rows)
    foreach ($employee_shifts_to_update as $employee_id => $shift_data) {
        try {
            $db->query("UPDATE employees SET shift_id = ?, updated_at = NOW() WHERE id = ?", [
                $shift_data['shift_id'],
                $employee_id
            ]);
            $shift_updates++;

            // Log the update
            error_log("Updated employee ID {$employee_id} ({$shift_data['employee_name']}) shift to ID {$shift_data['shift_id']}");

        } catch (Exception $e) {
            $errors[] = "Failed to update shift for employee ID {$employee_id}: " . $e->getMessage();
        }
    }
    // Commit transaction
    $db->commit();

    // Build response message
    $message = "Successfully processed {$processed} schedule entries";

    if ($shift_updates > 0) {
        $message .= " and updated {$shift_updates} employee shift assignments";
    }
    $message .= ".";

    if (!empty($schedule_conflicts)) {
        $message .= " Skipped " . count($schedule_conflicts) . " employees on leave.";
    }

    if (!empty($invalid_shifts)) {
        $message .= " Found " . count($invalid_shifts) . " invalid shift IDs.";
    }

    // Set success response
    $response['success'] = true;
    $response['message'] = $message;
    $response['processed'] = $processed;
    $response['shift_updates'] = $shift_updates;
    $response['conflicts'] = $schedule_conflicts;
    $response['invalid_shifts'] = $invalid_shifts;
    $response['errors'] = $errors;

    $_SESSION['success'][] = $response['message'];

    // Add warning for schedule conflicts
    if (!empty($schedule_conflicts)) {
        $_SESSION['error'][] = "Skipped " . count($schedule_conflicts) . " employees who are on leave:";
        foreach ($schedule_conflicts as $conflict) {
            $_SESSION['error'][] = "• {$conflict['employee_name']} on {$conflict['date']} ({$conflict['leave_type']})";
        }
    }

    // Add warnings for invalid shift IDs
    if (!empty($invalid_shifts)) {
        $_SESSION['error'][] = "Invalid shift IDs found:";
        foreach (array_slice($invalid_shifts, 0, 5) as $invalid) {
            $_SESSION['error'][] = "• {$invalid}";
        }
        if (count($invalid_shifts) > 5) {
            $_SESSION['error'][] = "• ... and " . (count($invalid_shifts) - 5) . " more";
        }
    }

    // Show shift updates summary
    if ($shift_updates > 0) {
        $_SESSION['success'][] = "Updated {$shift_updates} employee(s) default shift assignment.";
    }

    if (!empty($errors)) {
        $_SESSION['error'][] = "Processed with " . count($errors) . " warnings.";
    }

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollBack();
    }

    $response['message'] = $e->getMessage();
    $_SESSION['error'][] = $response['message'];
}

// Add any row-level errors to session
if (!empty($errors) && !isset($_SESSION['error'])) {
    foreach (array_slice($errors, 0, 5) as $error) {
        $_SESSION['error'][] = $error;
    }
    if (count($errors) > 5) {
        $_SESSION['error'][] = "... and " . (count($errors) - 5) . " more errors";
    }
}

$_SESSION['upload_result'] = $response;

// IMPORTANT: Ensure no output before this
if (ob_get_length())
    ob_clean();

// Return JSON response for AJAX
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    header('Location: /main?tab=shift');
    exit;
}