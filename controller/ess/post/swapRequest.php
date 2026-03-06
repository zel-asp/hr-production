<?php
// request-swap-schedule.php

use Core\Database;

require base_path('core/middleware/employeeAuth.php');

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Initialize session arrays
$_SESSION['error'] ??= [];
$_SESSION['success'] ??= [];

// Get current employee ID
$employeeData = $_SESSION['employee']['employee_record_id'] ?? null;
$employeeId = is_array($employeeData) ? ($employeeData['id'] ?? null) : $employeeData;

if (!$employeeId) {
    $_SESSION['error'][] = "Session expired. Please login again.";
    header('Location: /login');
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['error'][] = "Invalid security token. Please try again.";
    header('Location: /?tab=sched');
    exit();
}

// Validate required fields
$required = ['swap_with_employee_id', 'swap_date', 'reason'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'][] = "All fields are required.";
        header('Location: /?tab=sched');
        exit();
    }
}

// Sanitize and validate inputs
$swapWithId = filter_var($_POST['swap_with_employee_id'], FILTER_VALIDATE_INT);
$swapDate = $_POST['swap_date'];
$reason = trim(htmlspecialchars($_POST['reason']));
$requesterShiftId = !empty($_POST['requester_shift_id']) ? filter_var($_POST['requester_shift_id'], FILTER_VALIDATE_INT) : null;
$swapWithShiftId = !empty($_POST['swap_with_shift_id']) ? filter_var($_POST['swap_with_shift_id'], FILTER_VALIDATE_INT) : null;

// Validate date
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$maxDate = date('Y-m-d', strtotime('+30 days'));

if ($swapDate < $tomorrow || $swapDate > $maxDate) {
    $_SESSION['error'][] = "Invalid swap date. Please select a date within the next 30 days.";
    header('Location: /?tab=sched');
    exit();
}

// Check if swap with employee exists and is active
$swapEmployee = $db->query("
    SELECT id, full_name, shift_id 
    FROM employees 
    WHERE id = ? AND status IN ('Active', 'Regular', 'Probationary', 'Onboarding')
", [$swapWithId])->fetch_one();

if (!$swapEmployee) {
    $_SESSION['error'][] = "Selected employee not found or not active.";
    header('Location: /?tab=sched');
    exit();
}

// Check for existing pending swap request
$existingRequest = $db->query("
    SELECT id FROM shift_swap_requests 
    WHERE requester_employee_id = ? 
        AND swap_with_employee_id = ? 
        AND swap_date = ? 
        AND status = 'Pending'
", [$employeeId, $swapWithId, $swapDate])->fetch_one();

if ($existingRequest) {
    $_SESSION['error'][] = "You already have a pending swap request for this date with this employee.";
    header('Location: /?tab=sched');
    exit();
}

// Check if there's already a swap request from the other employee
$existingReverseRequest = $db->query("
    SELECT id FROM shift_swap_requests 
    WHERE requester_employee_id = ? 
        AND swap_with_employee_id = ? 
        AND swap_date = ? 
        AND status = 'Pending'
", [$swapWithId, $employeeId, $swapDate])->fetch_one();

if ($existingReverseRequest) {
    $_SESSION['error'][] = "This employee already has a pending swap request with you for this date.";
    header('Location: /?tab=sched');
    exit();
}

// Insert the swap request with BOTH shift IDs
try {
    $result = $db->query("
        INSERT INTO shift_swap_requests (
            requester_employee_id, 
            swap_with_employee_id, 
            swap_date, 
            requester_shift_id,
            swap_with_shift_id,
            reason, 
            status, 
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())
    ", [
        $employeeId,
        $swapWithId,
        $swapDate,
        $requesterShiftId,
        $swapWithShiftId,
        $reason
    ]);

    if ($result) {
        $_SESSION['success'][] = "Shift swap request submitted successfully! Waiting for approval.";
    } else {
        $_SESSION['error'][] = "Failed to submit swap request. Please try again.";
    }

} catch (\Throwable $th) {
    error_log("Error inserting shift swap request: " . $th->getMessage());
    $_SESSION['error'][] = "Database error occurred. Please try again.";
}

// Redirect back to ESS dashboard
header('Location: /?tab=sched');
exit();