<?php
use Core\Database;

require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: /main?tab=timesheet');
    exit();
}

// Verify this is a PATCH request via method spoofing
if (!isset($_POST['__method']) || $_POST['__method'] !== 'PATCH') {
    $_SESSION['error'][] = 'Invalid request method';
    header('Location: /main?tab=timesheet');
    exit();
}

// Check if we have summary_id directly
$summaryId = isset($_POST['summary_id']) ? (int) $_POST['summary_id'] : 0;

if ($summaryId) {
    // Update by summary_id
    try {
        $db->beginTransaction();

        // Get summary details for message
        $summary = $db->query("
            SELECT as2.id, as2.status, e.full_name, e.id as employee_id
            FROM attendance_summary as2
            JOIN employees e ON as2.employee_id = e.id
            WHERE as2.id = ?
        ", [$summaryId])->fetch_one(); // Changed from find() to fetch_one()

        if (!$summary) {
            throw new Exception('Attendance summary not found');
        }

        // Check if status exists before comparing
        $currentStatus = $summary['status'] ?? '';
        if ($currentStatus === 'Approved') {
            $_SESSION['info'][] = 'Timesheet is already approved.';
            $db->commit();
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/main?tab=timesheet'));
            exit();
        }

        // Update to Approved
        $db->query("
            UPDATE attendance_summary 
            SET status = 'Approved',
                updated_at = NOW()
            WHERE id = ?
        ", [$summaryId]);

        // Check if full_name exists before using it
        $employeeName = $summary['full_name'] ?? 'Employee';
        $_SESSION['success'][] = "Timesheet for {$employeeName} has been approved successfully.";
        $db->commit();

    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'][] = 'Error approving timesheet: ' . $e->getMessage();
        error_log("Error approving timesheet: " . $e->getMessage());
    }
} else {
    // Fallback to employee_id + period method
    $employeeId = isset($_POST['employee_id']) ? (int) $_POST['employee_id'] : 0;
    $periodStart = $_POST['period_start'] ?? null;
    $periodEnd = $_POST['period_end'] ?? null;

    if (!$employeeId || !$periodStart || !$periodEnd) {
        $_SESSION['error'][] = 'Missing required information';
        header('Location: /main?tab=timesheet');
        exit();
    }

    try {
        $db->beginTransaction();

        // Find the summary
        $summary = $db->query("
            SELECT id, status 
            FROM attendance_summary 
            WHERE employee_id = ? 
            AND period_start = ? 
            AND period_end = ?
        ", [$employeeId, $periodStart, $periodEnd])->fetch_one(); // Changed from find() to fetch_one()

        if (!$summary) {
            throw new Exception('No attendance summary found');
        }

        // Check if status exists before comparing
        $currentStatus = $summary['status'] ?? '';
        if ($currentStatus === 'Approved') {
            $_SESSION['info'][] = 'Timesheet is already approved.';
            $db->commit();
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/main?tab=timesheet'));
            exit();
        }

        // Update to Approved
        $db->query("
            UPDATE attendance_summary 
            SET status = 'Approved',
                updated_at = NOW()
            WHERE id = ?
        ", [$summary['id']]);

        // Get employee name separately
        $employee = $db->query("SELECT full_name FROM employees WHERE id = ?", [$employeeId])->fetch_one();
        $employeeName = $employee ? ($employee['full_name'] ?? 'Employee') : 'Employee';

        $_SESSION['success'][] = "Timesheet for {$employeeName} has been approved successfully.";
        $db->commit();

    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'][] = 'Error approving timesheet: ' . $e->getMessage();
        error_log("Error approving timesheet: " . $e->getMessage());
    }
}

// Make sure no output has been sent before this
if (!headers_sent()) {
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/main?tab=timesheet'));
} else {
    echo '<script>window.location.href="' . ($_SERVER['HTTP_REFERER'] ?? '/main?tab=timesheet') . '"</script>';
}
exit();