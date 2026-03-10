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

// Check for method spoofing (PATCH via POST)
$method = $_POST['__method'] ?? 'POST';
if ($method !== 'PATCH') {
    $_SESSION['error'][] = 'Invalid method';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Get form data
$requisitionId = isset($_POST['requisition_id']) ? (int) $_POST['requisition_id'] : 0;
$status = $_POST['status'] ?? '';

// Validate input
if (!$requisitionId || !in_array($status, ['approved', 'declined'])) {
    $_SESSION['error'][] = 'Invalid request parameters';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

try {
    $db->beginTransaction();

    // Check if requisition exists and is pending
    $requisition = $db->query("
        SELECT id, status FROM job_requisitions 
        WHERE id = ?
    ", [$requisitionId])->fetch_one();

    if (!$requisition) {
        throw new Exception('Requisition not found');
    }

    if ($requisition['status'] !== 'pending') {
        throw new Exception('This requisition has already been processed');
    }

    // Update requisition status
    $db->query("
        UPDATE job_requisitions 
        SET status = ?, updated_at = NOW()
        WHERE id = ?
    ", [$status, $requisitionId]);

    $db->commit();

    // Set success message based on action
    $action = $status === 'approved' ? 'approved' : 'declined';
    $_SESSION['success'][] = 'Requisition ' . $action . ' successfully';

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'][] = 'Error: ' . $e->getMessage();
    error_log("Error updating requisition: " . $e->getMessage());
}

// Redirect back to previous page
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit();