<?php

use Core\Database;

require base_path('core/middleware/employeeAuth.php');

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// ========================
// ONLY ALLOW POST
// ========================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'][] = "Invalid request method.";
    header('Location: /?tab=leave');
    exit();
}

// ========================
// CSRF CHECK
// ========================
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    $_SESSION['error'][] = "Invalid CSRF token. Please try again.";
    header('Location: /login');
    exit();
}

// ========================
// GET EMPLOYEE ID FROM SESSION
// ========================
// Get the employee_record_id from session (this is employees.id)
$employeeRecordId = $_POST['employee_record_id'] ?? $_SESSION['employee']['employee_record_id'] ?? null;

if (!$employeeRecordId) {
    // Fallback: try to get from employeeApplicantId
    $employeeData = $_SESSION['employee']['employeeApplicantId'] ?? null;

    if (is_array($employeeData) && isset($employeeData['id'])) {
        $employeeRecordId = $employeeData['id'];
    } else {
        $employeeRecordId = $employeeData;
    }
}

if (!$employeeRecordId) {
    $_SESSION['error'][] = "Employee not found. Please login again.";
    header('Location: /login');
    exit();
}

// Verify employee exists in database
$employee = $db->query(
    "SELECT id, employee_number, full_name, email, hired_date FROM employees WHERE id = ?",
    [$employeeRecordId]
)->fetch_one();

if (!$employee) {
    $_SESSION['error'][] = "Employee record not found.";
    header('Location: /?tab=leave');
    exit();
}

// ========================
// VALIDATE FORM INPUTS
// ========================
$leaveType = $_POST['leave_type'] ?? '';
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';
$reason = trim($_POST['reason'] ?? '');

// Validate leave type
$validLeaveTypes = ['Annual Leave', 'Sick Leave', 'Personal Day', 'Remote Work'];
if (!in_array($leaveType, $validLeaveTypes)) {
    $_SESSION['error'][] = "Invalid leave type selected.";
    header('Location: /?tab=leave');
    exit();
}

// Validate dates
if (empty($startDate) || empty($endDate)) {
    $_SESSION['error'][] = "Start date and end date are required.";
    header('Location: /?tab=leave');
    exit();
}

$startTimestamp = strtotime($startDate);
$endTimestamp = strtotime($endDate);

if ($startTimestamp === false || $endTimestamp === false) {
    $_SESSION['error'][] = "Invalid date format.";
    header('Location: /?tab=leave');
    exit();
}

if ($endTimestamp < $startTimestamp) {
    $_SESSION['error'][] = "End date cannot be before start date.";
    header('Location: /?tab=leave');
    exit();
}

// ========================
// CHECK FOR PAST DATES
// ========================
$today = strtotime(date('Y-m-d'));
if ($startTimestamp < $today) {
    $_SESSION['error'][] = "Start date cannot be in the past.";
    header('Location: /?tab=leave');
    exit();
}

// Calculate total days
$totalDays = 0;
$current = $startTimestamp;
while ($current <= $endTimestamp) {
    // Optional: Skip weekends (Saturday and Sunday)
    // $dayOfWeek = date('N', $current);
    // if ($dayOfWeek < 6) { // 1-5 are Monday-Friday
    //     $totalDays++;
    // }
    $totalDays++;
    $current = strtotime('+1 day', $current);
}

if ($totalDays <= 0) {
    $_SESSION['error'][] = "Invalid date range.";
    header('Location: /?tab=leave');
    exit();
}

// ========================
// CHECK FOR DUPLICATE LEAVE REQUESTS
// ========================

// Check 1: Same date range with same leave type (including pending)
$duplicateCheck = $db->query(
    "SELECT id, status, leave_type, start_date, end_date 
     FROM leave_requests 
     WHERE employee_id = ? 
     AND start_date = ? 
     AND end_date = ? 
     AND leave_type = ?
     AND status IN ('Pending', 'Approved')",
    [$employeeRecordId, $startDate, $endDate, $leaveType]
)->fetch_one();

if ($duplicateCheck) {
    $_SESSION['error'][] = "You already have a " . strtolower($duplicateCheck['status']) . " leave request for these dates with the same leave type.";
    header('Location: /?tab=leave');
    exit();
}

// Check 2: Overlapping dates (any leave type)
$overlapCheck = $db->query(
    "SELECT id, leave_type, start_date, end_date, status 
     FROM leave_requests 
     WHERE employee_id = ? 
     AND status IN ('Pending', 'Approved')
     AND (
         (start_date <= ? AND end_date >= ?) OR
         (start_date <= ? AND end_date >= ?) OR
         (start_date >= ? AND end_date <= ?)
     )",
    [
        $employeeRecordId,
        $endDate,
        $startDate,  // Case 1: Existing leave overlaps with new dates
        $startDate,
        $startDate, // Case 2: New start date falls within existing leave
        $startDate,
        $endDate    // Case 3: New dates completely inside existing leave
    ]
)->find();

if (!empty($overlapCheck)) {
    $overlapping = $overlapCheck[0];
    $_SESSION['error'][] = "This date range overlaps with your existing {$overlapping['status']} {$overlapping['leave_type']} request from " .
        date('M j, Y', strtotime($overlapping['start_date'])) . " to " .
        date('M j, Y', strtotime($overlapping['end_date'])) . ".";
    header('Location: /?tab=leave');
    exit();
}

// ========================
// CHECK LEAVE QUOTA (Last 3 months)
// ========================

// Calculate date 3 months ago
$threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));

// Count leave requests in the last 3 months (excluding Rejected)
$recentLeaveCount = $db->query(
    "SELECT COUNT(*) as total 
     FROM leave_requests 
     WHERE employee_id = ? 
     AND created_at >= ?
     AND status != 'Rejected'
     AND leave_type != 'Remote Work'", // Optional: exclude Remote Work from quota
    [$employeeRecordId, $threeMonthsAgo]
)->fetch_one();

$recentLeaveTotal = $recentLeaveCount['total'] ?? 0;

// Define maximum leaves per 3 months (adjust as needed)
$maxLeavesPerQuarter = 10;

if ($recentLeaveTotal >= $maxLeavesPerQuarter) {
    $_SESSION['error'][] = "You have reached the maximum limit of {$maxLeavesPerQuarter} leave requests in the last 3 months. " .
        "Please wait before submitting another request or contact HR for assistance.";
    header('Location: /?tab=leave');
    exit();
}

// ========================
// CHECK ANNUAL LEAVE BALANCE (Optional)
// ========================

// Calculate total approved leave days in current year
$yearStart = date('Y-01-01');
$yearEnd = date('Y-12-31');

$annualLeaveUsed = $db->query(
    "SELECT SUM(total_days) as total_used 
     FROM leave_requests 
     WHERE employee_id = ? 
     AND status = 'Approved'
     AND start_date BETWEEN ? AND ?
     AND leave_type = 'Annual Leave'",
    [$employeeRecordId, $yearStart, $yearEnd]
)->fetch_one();

$annualLeaveUsedDays = $annualLeaveUsed['total_used'] ?? 0;

// Define annual leave entitlement (adjust based on employee tenure or company policy)
$annualLeaveEntitlement = 15; // 15 days per year

// Check if this new request would exceed annual leave balance
if ($leaveType === 'Annual Leave' && ($annualLeaveUsedDays + $totalDays) > $annualLeaveEntitlement) {
    $_SESSION['error'][] = "Insufficient annual leave balance. You have {$annualLeaveEntitlement} days per year and have already used {$annualLeaveUsedDays} days. " .
        "This request for {$totalDays} days would exceed your balance.";
    header('Location: /?tab=leave');
    exit();
}

// ========================
// CHECK FOR PREGNANCY-RELATED LEAVES (If applicable)
// ========================
// This is just an example - adjust based on your company policies
if ($leaveType === 'Sick Leave' && stripos($reason, 'pregnancy') !== false) {
    // Check if employee has already taken maternity leave this year
    $maternityLeaveTaken = $db->query(
        "SELECT COUNT(*) as total 
         FROM leave_requests 
         WHERE employee_id = ? 
         AND leave_type = 'Maternity Leave'
         AND status = 'Approved'
         AND YEAR(start_date) = YEAR(CURDATE())",
        [$employeeRecordId]
    )->fetch_one();

    if (($maternityLeaveTaken['total'] ?? 0) > 0) {
        $_SESSION['error'][] = "Maternity leave has already been processed for this year. Please contact HR for assistance.";
        header('Location: /?tab=leave');
        exit();
    }
}

// ========================
// INSERT LEAVE REQUEST
// ========================
try {
    $db->beginTransaction();

    // Insert the leave request
    $db->query(
        "INSERT INTO leave_requests (
            employee_id, 
            leave_type, 
            start_date, 
            end_date, 
            total_days, 
            reason, 
            status, 
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())",
        [
            $employeeRecordId,
            $leaveType,
            $startDate,
            $endDate,
            $totalDays,
            $reason
        ]
    );

    $leaveRequestId = $db->lastInsertId();

    // Optional: Log the action
    error_log("Leave request #{$leaveRequestId} created for employee #{$employeeRecordId} ({$employee['full_name']}) - {$totalDays} days");

    $db->commit();

    $_SESSION['success'][] = "Leave request submitted successfully!";

} catch (Exception $e) {
    $db->rollBack();
    error_log("Error submitting leave request: " . $e->getMessage());
    $_SESSION['error'][] = "An error occurred while submitting your request. Please try again.";
}

// ========================
// REDIRECT BACK
// ========================
header('Location: /?tab=leave');
exit();