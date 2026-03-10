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

// Check if this is a process all request
if (!isset($_POST['process_all']) || $_POST['process_all'] != '1') {
    $_SESSION['error'][] = 'Invalid request';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Get current payroll period dates
$today = date('Y-m-d');
$currentDay = (int) date('d');

// Determine current payroll period
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

    // Get all active employees with their attendance and claims data
    $employees = $db->query("
        SELECT 
            e.id,
            e.full_name,
            e.hourly_rate,
            COALESCE((
                SELECT SUM(regular_hours) 
                FROM attendance 
                WHERE employee_id = e.id 
                AND date BETWEEN ? AND ?
                AND status = 'clocked_out'
            ), 0) as total_regular_hours,
            COALESCE((
                SELECT SUM(overtime_hours) 
                FROM attendance 
                WHERE employee_id = e.id 
                AND date BETWEEN ? AND ?
                AND status = 'clocked_out'
            ), 0) as total_overtime_hours,
            COALESCE((
                SELECT SUM(amount) 
                FROM expense_claims 
                WHERE employee_id = e.id 
                AND status = 'Approved'
                AND approved_at BETWEEN ? AND ?
            ), 0) as claims_amount,
            (
                SELECT COUNT(*) 
                FROM expense_claims 
                WHERE employee_id = e.id 
                AND status = 'Approved'
                AND approved_at BETWEEN ? AND ?
            ) as claims_count
        FROM employees e
        WHERE e.status IN ('Active', 'Regular', 'Probationary')
    ", [
        $periodStart,
        $periodEnd,
        $periodStart,
        $periodEnd,
        $periodStart,
        $periodEnd,
        $periodStart,
        $periodEnd
    ])->find();

    if (empty($employees)) {
        throw new Exception('No employees found for payroll processing');
    }

    // Get statutory deductions
    $statutoryDeductions = $db->query("
        SELECT SUM(deduction_amount) as total
        FROM statutory_deductions
    ")->fetch_one()['total'] ?? 800;

    $processed = 0;
    $updated = 0;
    $errors = [];

    foreach ($employees as $emp) {
        try {
            // Calculate gross pay
            $regularPay = $emp['total_regular_hours'] * ($emp['hourly_rate'] ?: 0);
            $overtimePay = $emp['total_overtime_hours'] * ($emp['hourly_rate'] ?: 0) * 1.25;
            $grossPay = $regularPay + $overtimePay;

            // Net pay = gross - deductions + claims
            $netPay = max(0, $grossPay - $statutoryDeductions + $emp['claims_amount']);

            // Check if payroll already exists
            $existingPayroll = $db->query("
                SELECT id FROM payroll_summary 
                WHERE employee_id = ? AND period_start = ? AND period_end = ?
            ", [$emp['id'], $periodStart, $periodEnd])->fetch_one();

            if ($existingPayroll) {
                // Update existing
                $db->query("
                    UPDATE payroll_summary 
                    SET total_regular_hours = ?,
                        total_overtime_hours = ?,
                        hourly_rate = ?,
                        gross_pay = ?,
                        total_deductions = ?,
                        net_pay = ?,
                        claims = ?,
                        status = 'Processed',
                        generated_at = NOW()
                    WHERE id = ?
                ", [
                    $emp['total_regular_hours'],
                    $emp['total_overtime_hours'],
                    $emp['hourly_rate'],
                    $grossPay,
                    $statutoryDeductions,
                    $netPay,
                    $emp['claims_amount'],
                    $existingPayroll['id']
                ]);
                $updated++;
            } else {
                // Insert new
                $db->query("
                    INSERT INTO payroll_summary (
                        employee_id, period_start, period_end,
                        total_regular_hours, total_overtime_hours, hourly_rate,
                        gross_pay, total_deductions, net_pay, claims,
                        status, generated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Processed', NOW())
                ", [
                    $emp['id'],
                    $periodStart,
                    $periodEnd,
                    $emp['total_regular_hours'],
                    $emp['total_overtime_hours'],
                    $emp['hourly_rate'],
                    $grossPay,
                    $statutoryDeductions,
                    $netPay,
                    $emp['claims_amount']
                ]);
                $processed++;
            }
        } catch (Exception $e) {
            $errors[] = "Error processing {$emp['full_name']}: " . $e->getMessage();
        }
    }

    $db->commit();

    $message = "Payroll processed successfully: {$processed} new, {$updated} updated";
    if (!empty($errors)) {
        $message .= " with " . count($errors) . " errors";
    }

    $_SESSION['success'][] = $message;

    if (!empty($errors)) {
        foreach ($errors as $error) {
            $_SESSION['error'][] = $error;
        }
    }

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'][] = 'Error processing payroll: ' . $e->getMessage();
    error_log("Error processing all payroll: " . $e->getMessage());
}

// Redirect back
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/main?tab=payroll'));
exit();