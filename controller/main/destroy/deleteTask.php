<?php

use Core\Database;
require base_path("core/middleware/adminAuth.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $_SESSION['error'][] = 'Method not allowed';
    header("Location: /main?tab=learning#task$taskId");
    exit();
}

if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    $_SESSION['error'][] = 'Invalid security token';
    header("Location: /main?tab=learning#task$taskId");
    exit();
}

if (!isset($_POST['task_id']) || empty($_POST['task_id'])) {
    $_SESSION['error'][] = 'Task ID is required';
    header("Location: /main?tab=learning#task$taskId");
    exit();
}

$config = require base_path('config/config.php');
$db = new Database($config['database']);

$taskId = (int) $_POST['task_id'];

try {
    $db->beginTransaction();

    $task = $db->query(
        "SELECT * FROM tasks WHERE id = ?",
        [$taskId]
    )->fetch_one();

    if (!$task) {
        throw new Exception('Task not found');
    }

    // if only assigned staff or admins can delete
    $employeeId = $_SESSION['employee']['employee_record_id']['id'] ?? null;

    // Delete the task
    $db->query(
        "DELETE FROM tasks WHERE id = ?",
        [$taskId]
    );

    $db->commit();

    $_SESSION['success'][] = 'Task deleted successfully';

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    $_SESSION['error'][] = $e->getMessage();
    error_log("Delete task error: " . $e->getMessage());
}

// Redirect back to the previous page
header("Location: /main?tab=learning#task$taskId");
exit();