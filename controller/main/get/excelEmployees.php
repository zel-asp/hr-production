<?php

use Core\Database;

require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Get filter parameters
$searchTerm = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$departmentFilter = $_GET['department'] ?? '';
$roleFilter = $_GET['role'] ?? '';

// Build WHERE clause for filters
$whereConditions = [];
$params = [];

if (!empty($searchTerm)) {
    $whereConditions[] = "(e.full_name LIKE ? OR e.employee_number LIKE ? OR e.position LIKE ? OR e.department LIKE ?)";
    $searchParam = "%$searchTerm%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
}

if (!empty($statusFilter)) {
    $whereConditions[] = "e.status = ?";
    $params[] = $statusFilter;
}

if (!empty($departmentFilter)) {
    $whereConditions[] = "e.department = ?";
    $params[] = $departmentFilter;
}

if (!empty($roleFilter)) {
    $whereConditions[] = "e.role = ?";
    $params[] = $roleFilter;
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Fetch HCM Stats
try {
    // Total employees count
    $totalEmployees = $db->query("SELECT COUNT(*) as count FROM employees")->fetch_one()['count'] ?? 0;

    // Active employees count
    $activeEmployees = $db->query("SELECT COUNT(*) as count FROM employees WHERE status IN ('Active', 'Regular', 'Probationary')")->fetch_one()['count'] ?? 0;

    // On leave count
    $onLeaveCount = $db->query("
        SELECT COUNT(DISTINCT employee_id) as count 
        FROM leave_requests 
        WHERE CURDATE() BETWEEN start_date AND end_date 
        AND status = 'Approved'
    ")->fetch_one()['count'] ?? 0;

    // Probationary count
    $probationaryCount = $db->query("SELECT COUNT(*) as count FROM employees WHERE status = 'Probationary'")->fetch_one()['count'] ?? 0;

    // Job postings count
    $jobPostingsCount = $db->query("SELECT COUNT(*) as count FROM job_postings")->fetch_one()['count'] ?? 0;

    // Recent applicants count (last 30 days)
    $recentApplicantsCount = $db->query("
        SELECT COUNT(*) as count FROM applicants 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ")->fetch_one()['count'] ?? 0;

    // Total hired count
    $totalHired = $db->query("SELECT COUNT(*) as count FROM employees")->fetch_one()['count'] ?? 0;

    // Pending onboarding count
    $totalPending = $db->query("
        SELECT COUNT(*) as count FROM employees 
        WHERE onboarding_status != 'Onboarded'
    ")->fetch_one()['count'] ?? 0;

    // Active trainings count
    $activeTrainings = $db->query("
        SELECT COUNT(*) as count FROM training_schedule 
        WHERE status = 'Scheduled'
    ")->fetch_one()['count'] ?? 0;

    // Completed trainings count
    $analyticsTrainingsCompleted = $db->query("
        SELECT COUNT(*) as count FROM training_schedule 
        WHERE status = 'Completed'
    ")->fetch_one()['count'] ?? 0;

    // Recent evaluations count
    $recentEvaluationsCount = $db->query("
        SELECT COUNT(*) as count FROM performance_evaluations 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ")->fetch_one()['count'] ?? 0;

    // Pending evaluations count
    $pendingCount = $db->query("
        SELECT COUNT(*) as count FROM employees 
        WHERE evaluation_status = 'Pending'
    ")->fetch_one()['count'] ?? 0;

    // Recent recognitions count
    $recentRecognitionsCount = $db->query("
        SELECT COUNT(*) as count FROM employee_recognitions 
        WHERE recognition_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ")->fetch_one()['count'] ?? 0;

    // Payroll total net
    $payrollTotalNet = $db->query("
        SELECT COALESCE(SUM(net_pay), 0) as total 
        FROM payroll_summary 
        WHERE generated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ")->fetch_one()['total'] ?? 0;

    // Active percentage
    $activePercentage = $totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100) : 0;

} catch (\Throwable $th) {
    error_log("Stats fetch error: " . $th->getMessage());
    // Set default values if queries fail
    $totalEmployees = 0;
    $activeEmployees = 0;
    $onLeaveCount = 0;
    $probationaryCount = 0;
    $jobPostingsCount = 0;
    $recentApplicantsCount = 0;
    $totalHired = 0;
    $totalPending = 0;
    $activeTrainings = 0;
    $analyticsTrainingsCompleted = 0;
    $recentEvaluationsCount = 0;
    $pendingCount = 0;
    $recentRecognitionsCount = 0;
    $payrollTotalNet = 0;
    $activePercentage = 0;
}

// Fetch all employees for export
try {
    $employees = $db->query("
        SELECT 
            e.employee_number,
            e.full_name,
            e.email,
            e.phone,
            e.position,
            e.department,
            e.hourly_rate,
            e.start_date,
            e.hired_date,
            e.status,
            e.onboarding_status,
            e.role,
            e.evaluation_status,
            e.age,
            e.gender,
            ea.account_status,
            ea.last_login
        FROM employees e
        LEFT JOIN employee_accounts ea ON e.id = ea.employee_id
        $whereClause
        ORDER BY e.full_name ASC
    ", $params)->find();
} catch (\Throwable $th) {
    error_log("Export error: " . $th->getMessage());
    $employees = [];
}

// Set headers for CSV download
$filename = "hcm_employee_export_" . date('Y-m-d_His') . ".csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

// Open output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// ============================================
// ADD HCM STATS SECTION
// ============================================
fputcsv($output, ['HUMAN CAPITAL MANAGEMENT - EXECUTIVE SUMMARY']);
fputcsv($output, ['Generated On:', date('F j, Y H:i:s')]);
fputcsv($output, [
    'Filters Applied:',
    ($searchTerm ? "Search: $searchTerm" : 'No search filter') .
    ($statusFilter ? ", Status: $statusFilter" : '') .
    ($departmentFilter ? ", Dept: $departmentFilter" : '') .
    ($roleFilter ? ", Role: $roleFilter" : '')
]);
fputcsv($output, []); // Empty row for spacing

// HCM Lifecycle Stats Header
fputcsv($output, ['HCM LIFECYCLE STATISTICS']);
fputcsv($output, ['Category', 'Metric', 'Value']);
fputcsv($output, ['---', '---', '---']);

// Attract & Recruit
fputcsv($output, ['Attract & Recruit', 'Open Positions', $jobPostingsCount]);
fputcsv($output, ['Attract & Recruit', 'New Applicants (30 days)', $recentApplicantsCount]);

// Hire & Onboard
fputcsv($output, ['Hire & Onboard', 'Total Hired', $totalHired]);
fputcsv($output, ['Hire & Onboard', 'Pending Onboarding', $totalPending]);

// Train & Develop
fputcsv($output, ['Train & Develop', 'Active Trainings', $activeTrainings]);
fputcsv($output, ['Train & Develop', 'Completed Trainings', $analyticsTrainingsCompleted]);

// Manage & Evaluate
fputcsv($output, ['Manage & Evaluate', 'Recent Evaluations (30 days)', $recentEvaluationsCount]);
fputcsv($output, ['Manage & Evaluate', 'Pending Evaluations', $pendingCount]);

// Compensate & Recognize
fputcsv($output, ['Compensate & Recognize', 'Recent Recognitions (30 days)', $recentRecognitionsCount]);
fputcsv($output, ['Compensate & Recognize', 'Payroll Total (30 days)', '₱' . number_format($payrollTotalNet, 2)]);

// Retain
fputcsv($output, ['Retain', 'Active Employees', $activeEmployees]);
fputcsv($output, ['Retain', 'Retention Rate', $activePercentage . '%']);

fputcsv($output, []); // Empty row for spacing

// Employee Stats Header
fputcsv($output, ['EMPLOYEE STATISTICS']);
fputcsv($output, ['Metric', 'Value']);
fputcsv($output, ['---', '---']);
fputcsv($output, ['Total Employees', $totalEmployees]);
fputcsv($output, ['Active Employees', $activeEmployees]);
fputcsv($output, ['On Leave Today', $onLeaveCount]);
fputcsv($output, ['Probationary Employees', $probationaryCount]);

fputcsv($output, []); // Empty row for spacing
fputcsv($output, []); // Empty row for spacing

// ============================================
// EMPLOYEE DETAILS SECTION
// ============================================
fputcsv($output, ['EMPLOYEE DETAILS']);
fputcsv($output, []); // Empty row

// Add headers
fputcsv($output, [
    'Employee Number',
    'Full Name',
    'Email',
    'Phone',
    'Position',
    'Department',
    'Hourly Rate',
    'Monthly Rate',
    'Start Date',
    'Hired Date',
    'Status',
    'Onboarding Status',
    'Role',
    'Evaluation Status',
    'Age',
    'Gender',
    'Account Status',
    'Last Login'
]);

// Add data rows
foreach ($employees as $emp) {
    $monthlyRate = ($emp['hourly_rate'] ?? 0) * 8 * 22;

    fputcsv($output, [
        $emp['employee_number'] ?? '',
        $emp['full_name'] ?? '',
        $emp['email'] ?? '',
        $emp['phone'] ?? '',
        $emp['position'] ?? '',
        $emp['department'] ?? '',
        $emp['hourly_rate'] ? '₱' . number_format($emp['hourly_rate'], 2) : '₱0.00',
        '₱' . number_format($monthlyRate, 2),
        $emp['start_date'] ?? '',
        $emp['hired_date'] ?? '',
        $emp['status'] ?? '',
        $emp['onboarding_status'] ?? '',
        $emp['role'] ?? '',
        $emp['evaluation_status'] ?? '',
        $emp['age'] ?? '',
        $emp['gender'] ?? '',
        $emp['account_status'] ?? 'N/A',
        $emp['last_login'] ? date('Y-m-d H:i', strtotime($emp['last_login'])) : 'Never'
    ]);
}

// Add footer with total count
fputcsv($output, []); // Empty row
fputcsv($output, ['Total Employees Exported:', count($employees)]);
fputcsv($output, ['Export Date:', date('Y-m-d H:i:s')]);

fclose($output);
exit;