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
    // REJECT THE SWAP - JUST UPDATE STATUS
    // ============================================

    $db->query("
        UPDATE shift_swap_requests 
        SET status = 'Rejected', approved_by = ?, approved_at = NOW(), updated_at = NOW()
        WHERE id = ?
    ", [$approvedBy, $requestId]);

    $db->commit();

    $_SESSION['success'][] = 'Shift swap request rejected successfully.';

} catch (\Throwable $th) {
    $db->rollBack();
    $_SESSION['error'][] = 'Error rejecting swap request: ' . $th->getMessage();
    error_log("Error rejecting swap request: " . $th->getMessage());
}

// Redirect back
header('Location: /main?tab=shift');
exit();