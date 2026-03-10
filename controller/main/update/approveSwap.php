<?php
use Core\Database;
require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'][] = 'Invalid request method';
    header('Location: /main?tab=shift');
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: /main?tab=shift');
    exit();
}

// Get request data
$requestId = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;

if (!$requestId) {
    $_SESSION['error'][] = 'Invalid request parameters';
    header('Location: /main?tab=shift');
    exit();
}

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Get the swap request details
$swapRequest = $db->query("
    SELECT * FROM shift_swap_requests WHERE id = ?
", [$requestId])->fetch_one();

if (!$swapRequest) {
    $_SESSION['error'][] = 'Swap request not found';
    header('Location: /main?tab=shift');
    exit();
}

// Check if request is still pending
if ($swapRequest['status'] !== 'Pending') {
    $_SESSION['error'][] = 'This request has already been processed';
    header('Location: /main?tab=shift');
    exit();
}

try {
    $db->beginTransaction();

    $approvedBy = $_SESSION['employee']['employee_record_id'] ?? null;

    // ============================================
    // APPROVE THE SWAP - ACTUALLY SWAP THE SHIFTS
    // ============================================

    // 1. Update the shift_id in employees table for both employees
    $db->query("
        UPDATE employees 
        SET shift_id = ?, updated_at = NOW()
        WHERE id = ?
    ", [$swapRequest['swap_with_shift_id'], $swapRequest['requester_employee_id']]);

    $db->query("
        UPDATE employees 
        SET shift_id = ?, updated_at = NOW()
        WHERE id = ?
    ", [$swapRequest['requester_shift_id'], $swapRequest['swap_with_employee_id']]);

    // 2. Get current schedules for both employees on the swap date
    $requesterSchedule = $db->query("
        SELECT * FROM employee_schedules 
        WHERE employee_id = ? AND schedule_date = ?
    ", [$swapRequest['requester_employee_id'], $swapRequest['swap_date']])->fetch_one();

    $swapperSchedule = $db->query("
        SELECT * FROM employee_schedules 
        WHERE employee_id = ? AND schedule_date = ?
    ", [$swapRequest['swap_with_employee_id'], $swapRequest['swap_date']])->fetch_one();

    // 3. Update requester's schedule to swapper's shift
    if ($requesterSchedule) {
        // Update existing schedule
        $db->query("
            UPDATE employee_schedules 
            SET shift_id = ?, 
                shift_code = (SELECT shift_code FROM shifts WHERE id = ?),
                time_in = (SELECT start_time FROM shifts WHERE id = ?),
                time_out = (SELECT end_time FROM shifts WHERE id = ?),
                updated_at = NOW()
            WHERE id = ?
        ", [
            $swapRequest['swap_with_shift_id'],
            $swapRequest['swap_with_shift_id'],
            $swapRequest['swap_with_shift_id'],
            $swapRequest['swap_with_shift_id'],
            $requesterSchedule['id']
        ]);
    } else {
        // Create new schedule for requester
        $db->query("
            INSERT INTO employee_schedules 
            (employee_id, shift_id, schedule_date, time_in, time_out, shift_code, status, created_at)
            VALUES (?, ?, ?, 
                (SELECT start_time FROM shifts WHERE id = ?),
                (SELECT end_time FROM shifts WHERE id = ?),
                (SELECT shift_code FROM shifts WHERE id = ?),
                'scheduled', NOW()
            )
        ", [
            $swapRequest['requester_employee_id'],
            $swapRequest['swap_with_shift_id'],
            $swapRequest['swap_date'],
            $swapRequest['swap_with_shift_id'],
            $swapRequest['swap_with_shift_id'],
            $swapRequest['swap_with_shift_id']
        ]);
    }

    // 4. Update swapper's schedule to requester's shift
    if ($swapperSchedule) {
        // Update existing schedule
        $db->query("
            UPDATE employee_schedules 
            SET shift_id = ?, 
                shift_code = (SELECT shift_code FROM shifts WHERE id = ?),
                time_in = (SELECT start_time FROM shifts WHERE id = ?),
                time_out = (SELECT end_time FROM shifts WHERE id = ?),
                updated_at = NOW()
            WHERE id = ?
        ", [
            $swapRequest['requester_shift_id'],
            $swapRequest['requester_shift_id'],
            $swapRequest['requester_shift_id'],
            $swapRequest['requester_shift_id'],
            $swapperSchedule['id']
        ]);
    } else {
        // Create new schedule for swapper
        $db->query("
            INSERT INTO employee_schedules 
            (employee_id, shift_id, schedule_date, time_in, time_out, shift_code, status, created_at)
            VALUES (?, ?, ?, 
                (SELECT start_time FROM shifts WHERE id = ?),
                (SELECT end_time FROM shifts WHERE id = ?),
                (SELECT shift_code FROM shifts WHERE id = ?),
                'scheduled', NOW()
            )
        ", [
            $swapRequest['swap_with_employee_id'],
            $swapRequest['requester_shift_id'],
            $swapRequest['swap_date'],
            $swapRequest['requester_shift_id'],
            $swapRequest['requester_shift_id'],
            $swapRequest['requester_shift_id']
        ]);
    }

    // 5. Update the swap request status to Approved
    $db->query("
        UPDATE shift_swap_requests 
        SET status = 'Approved', approved_by = ?, approved_at = NOW(), updated_at = NOW()
        WHERE id = ?
    ", [$approvedBy, $requestId]);

    $db->commit();

    $_SESSION['success'][] = 'Shift swap approved successfully. Employee shift assignments and schedules have been updated.';

} catch (\Throwable $th) {
    $db->rollBack();
    $_SESSION['error'][] = 'Error approving swap request: ' . $th->getMessage();
    error_log("Error approving swap request: " . $th->getMessage());
}

// Redirect back
header('Location: /main?tab=shift');
exit();