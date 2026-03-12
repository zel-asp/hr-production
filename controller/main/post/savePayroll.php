<?php
use Core\Database;
require base_path("core/middleware/adminAuth.php");

// Initialize session arrays
$_SESSION['error'] ??= [];
$_SESSION['success'] ??= [];

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'][] = 'Invalid request method';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Get form data
$employeeId = isset($_POST['employeeId']) ? (int) $_POST['employeeId'] : 0;
$regularHours = isset($_POST['regularHours']) ? (float) $_POST['regularHours'] : 0;
$overtime = isset($_POST['overtime']) ? (float) $_POST['overtime'] : 0;
$claims = isset($_POST['claims']) ? (float) $_POST['claims'] : 0;
$grossPay = isset($_POST['grossPay']) ? (float) $_POST['grossPay'] : 0;
$deduction = isset($_POST['deduction']) ? (float) $_POST['deduction'] : 0;
$netPay = isset($_POST['netPay']) ? (float) $_POST['netPay'] : 0;


// Validate required fields
if (!$employeeId) {
    $_SESSION['error'][] = 'Employee ID is required';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Get current payroll period dates
$today = date('Y-m-d');
$currentDay = (int) date('d');
$currentMonth = date('m');
$currentYear = date('Y');

// Determine current payroll period (same logic as in payroll section)
if ($currentDay <= 5) {
    // 1st cutoff (21st of previous month - 5th of current month)
    $periodStart = date('Y-m-21', strtotime('-1 month'));
    $periodEnd = date('Y-m-05');
} elseif ($currentDay <= 20) {
    // 2nd cutoff (6th - 20th of current month)
    $periodStart = date('Y-m-06');
    $periodEnd = date('Y-m-20');
} else {
    // 1st cutoff of next month (21st - end of month)
    $periodStart = date('Y-m-21');
    $periodEnd = date('Y-m-t');
}


try {
    $db->beginTransaction();

    // Check if employee exists
    $employee = $db->query("
        SELECT id, hourly_rate, full_name 
        FROM employees 
        WHERE id = ?
    ", [$employeeId])->fetch_one();

    if (!$employee) {
        throw new Exception('Employee not found');
    }

    // Check if payroll already exists for this employee and period
    $existingPayroll = $db->query("
        SELECT id FROM payroll_summary 
        WHERE employee_id = ? AND period_start = ? AND period_end = ?
    ", [$employeeId, $periodStart, $periodEnd])->fetch_one();

    if ($existingPayroll) {
        // Update existing record
        $db->query("
            UPDATE payroll_summary 
            SET total_regular_hours = ?,
                total_overtime_hours = ?,
                hourly_rate = ?,
                gross_pay = ?,
                total_deductions = ?,
                net_pay = ?,
                claims = ?,
                status = 'Processing',
                generated_at = NOW()
            WHERE id = ?
        ", [
            $regularHours,
            $overtime,
            $employee['hourly_rate'],
            $grossPay,
            $deduction,
            $netPay,
            $claims,
            $existingPayroll['id']
        ]);

        $message = "Payroll updated for {$employee['full_name']}";
    } else {
        // Insert new record
        $db->query("
            INSERT INTO payroll_summary (
                employee_id, 
                period_start, 
                period_end, 
                total_regular_hours, 
                total_overtime_hours, 
                hourly_rate, 
                gross_pay, 
                total_deductions, 
                net_pay, 
                claims, 
                status, 
                generated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Processing', NOW())
        ", [
            $employeeId,
            $periodStart,
            $periodEnd,
            $regularHours,
            $overtime,
            $employee['hourly_rate'],
            $grossPay,
            $deduction,
            $netPay,
            $claims
        ]);

        $message = "Payroll saved for {$employee['full_name']}";
    }

    $db->commit();

    $_SESSION['success'][] = $message;

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'][] = 'Error saving payroll: ' . $e->getMessage();
    error_log("Error saving payroll: " . $e->getMessage());
}

// Redirect back
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit();