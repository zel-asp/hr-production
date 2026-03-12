<?php
use Core\Database;

// Important: Set header before any output
header('Content-Type: application/json');

require base_path('core/middleware/employeeAuth.php');

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Check authentication
if (!isset($_SESSION['employee'])) {
    http_response_code(401);
    die('Unauthorized');
}

$employeeId = $_SESSION['employee']['employee_record_id']['id'] ?? $_SESSION['employee']['employee_record_id'] ?? null;
$payslipId = $_GET['id'] ?? null;

if (!$payslipId) {
    http_response_code(400);
    die('Invalid request');
}

// Fetch payslip data
$payslip = $db->query("
    SELECT 
        ps.*,
        e.full_name,
        e.employee_number,
        e.position,
        e.department,
        e.hourly_rate
    FROM payroll_summary ps
    JOIN employees e ON ps.employee_id = e.id
    WHERE ps.id = ? AND ps.employee_id = ?
", [$payslipId, $employeeId])->fetch_one();

if (!$payslip) {
    http_response_code(404);
    die('Payslip not found');
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="Payslip_' . $payslip['full_name'] . '_' . date('Y-m', strtotime($payslip['period_start'])) . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel to handle special characters
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Company Header
fputcsv($output, ['COMPANY NAME - PAYSLIP']);
fputcsv($output, []);

// Period
fputcsv($output, ['Period:', date('F d, Y', strtotime($payslip['period_start'])) . ' to ' . date('F d, Y', strtotime($payslip['period_end']))]);
fputcsv($output, []);

// Employee Information
fputcsv($output, ['EMPLOYEE INFORMATION']);
fputcsv($output, ['Employee Name:', $payslip['full_name'], 'Employee ID:', $payslip['employee_number']]);
fputcsv($output, ['Position:', $payslip['position'], 'Department:', $payslip['department']]);
fputcsv($output, ['Hourly Rate:', '₱ ' . number_format($payslip['hourly_rate'], 2)]);
fputcsv($output, []);

// Earnings
fputcsv($output, ['EARNINGS']);
fputcsv($output, ['Description', 'Hours', 'Rate', 'Amount']);

$regularPay = $payslip['total_regular_hours'] * $payslip['hourly_rate'];
fputcsv($output, ['Regular Hours', $payslip['total_regular_hours'], '₱ ' . number_format($payslip['hourly_rate'], 2), '₱ ' . number_format($regularPay, 2)]);

$overtimePay = $payslip['total_overtime_hours'] * $payslip['hourly_rate'] * 1.25;
fputcsv($output, ['Overtime Hours', $payslip['total_overtime_hours'], '₱ ' . number_format($payslip['hourly_rate'] * 1.25, 2), '₱ ' . number_format($overtimePay, 2)]);

fputcsv($output, ['Claims / Reimbursements', '', '', '₱ ' . number_format($payslip['claims'] ?? 0, 2)]);
fputcsv($output, ['GROSS PAY', '', '', '₱ ' . number_format($payslip['gross_pay'], 2)]);
fputcsv($output, []);

// Deductions
fputcsv($output, ['DEDUCTIONS']);
fputcsv($output, ['Description', '', '', 'Amount']);

// Sample deductions
$deductions = [
    'SSS' => 250.00,
    'PhilHealth' => 250.00,
    'Pag-IBIG' => 100.00,
    'Tax' => $payslip['total_deductions'] - 600.00
];

foreach ($deductions as $desc => $amount) {
    fputcsv($output, [$desc, '', '', '₱ ' . number_format($amount, 2)]);
}

fputcsv($output, ['TOTAL DEDUCTIONS', '', '', '₱ ' . number_format($payslip['total_deductions'], 2)]);
fputcsv($output, []);

// Net Pay
fputcsv($output, ['NET PAY', '', '', '₱ ' . number_format($payslip['net_pay'], 2)]);
fputcsv($output, []);
fputcsv($output, ['Status:', $payslip['status']]);
fputcsv($output, ['Generated:', date('F d, Y h:i A')]);

fclose($output);
exit;