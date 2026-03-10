<?php

use Core\Database;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $_SESSION['error'][] = 'Method not allowed';
    header('Location: /?tab=leave');
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: /?tab=leave');
    exit();
}

if (!isset($_POST['request_id']) || empty($_POST['request_id'])) {
    $_SESSION['error'][] = 'Request ID is required';
    header('Location: /?tab=leave');
    exit();
}

$requestId = (int) $_POST['request_id'];
$config = require base_path('config/config.php');
$db = new Database($config['database']);

try {
    $db->beginTransaction();

    $request = $db->query(
        "SELECT * FROM leave_requests WHERE id = ?",
        [$requestId]
    )->fetch_one();

    if (!$request) {
        throw new Exception('Request not found');
    }

    $employeeId = $_SESSION['employee']['employee_record_id'] ?? null;

    if ($employeeId && $request['employee_id'] != $employeeId) {
        throw new Exception('You do not have permission to delete this request');
    }

    // Only allow deletion of pending requests
    if ($request['status'] !== 'Pending') {
        throw new Exception('Only pending requests can be deleted');
    }

    $db->query(
        "DELETE FROM leave_requests WHERE id = ?",
        [$requestId]
    );

    $db->commit();

    $_SESSION['success'][] = 'Request deleted successfully';

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    $_SESSION['error'][] = $e->getMessage();
    error_log("Delete request error: " . $e->getMessage());
}

header('Location: /?tab=leave');
exit();