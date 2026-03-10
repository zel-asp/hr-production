<?php
use Core\Database;

header('Content-Type: application/json');
require base_path("core/middleware/adminAuth.php");
$config = require base_path('config/config.php');
$db = new Database($config['database']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $_SESSION['error'][] = 'Method not allowed';
    header('Location: /main?tab=leave');
    exit();
}

if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: /main?tab=leave');
    exit();
}

if (!isset($_POST['request_id']) || empty($_POST['request_id'])) {
    $_SESSION['error'][] = 'Request ID is required';
    header('Location: /main?tab=leave');
    exit();
}

$config = require base_path('config/config.php');
$db = new Database($config['database']);

$requestId = (int) $_POST['request_id'];
$employeeId = $_SESSION['employee']['employee_record_id']['id'] ?? null;

try {
    $db->beginTransaction();

    $request = $db->query(
        "SELECT * FROM leave_requests WHERE id = ? AND status = 'Pending'",
        [$requestId]
    )->fetch_one();

    if (!$request) {
        throw new Exception('Pending leave request not found');
    }

    $db->query(
        "UPDATE leave_requests 
        SET status = 'Approved', 
            approved_by = ?, 
            approved_at = NOW() 
        WHERE id = ?",
        [$employeeId, $requestId]
    );

    $db->commit();
    $_SESSION['success'][] = 'Leave request approved successfully';

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $_SESSION['error'][] = $e->getMessage();
    error_log("Approve leave error: " . $e->getMessage());
}

header('Location: /main?tab=leave');
exit();