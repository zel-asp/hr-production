<?php
use Core\Database;
require base_path("core/middleware/adminAuth.php");
$config = require base_path('config/config.php');
$db = new Database($config['database']);


// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'][] = 'Invalid request method';
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
$employeeId = isset($_POST['employee_id']) ? (int) $_POST['employee_id'] : 0;
$missingCount = isset($_POST['missing_count']) ? (int) $_POST['missing_count'] : 0;
$missingItems = $_POST['missing_items'] ?? '';

if (!$employeeId || !$missingCount) {
    $_SESSION['error'][] = 'Missing required information';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Get current admin user ID (assuming logged in admin)
$adminId = $_SESSION['employee']['employee_record_id']['id'] ?? null;

try {
    $db->beginTransaction();

    // Get employee details
    $employee = $db->query("
        SELECT full_name, employee_number 
        FROM employees 
        WHERE id = ?
    ", [$employeeId])->fetch_one();

    if (!$employee) {
        throw new Exception('Employee not found');
    }

    // Create note title
    $noteTitle = "Missing Requirements - " . date('Y-m-d');

    // Create note content
    $missingList = explode(', ', $missingItems);
    $formattedMissing = "";
    foreach ($missingList as $index => $item) {
        $formattedMissing .= ($index + 1) . ". " . trim($item) . "\n";
    }

    $noteContent = "Employee: {$employee['full_name']} ({$employee['employee_number']})\n";
    $noteContent .= "Missing Requirements: {$missingCount}\n\n";
    $noteContent .= "Missing Items:\n{$formattedMissing}\n";
    $noteContent .= "Notification sent on: " . date('Y-m-d H:i:s');

    // Insert into admin_notes
    $db->query("
        INSERT INTO admin_notes 
        (employee_id, note_title, note_content, note_type, status, created_by, created_at, updated_at)
        VALUES (?, ?, ?, 'confidential', 'active', ?, NOW(), NOW())
    ", [$employeeId, $noteTitle, $noteContent, $adminId]);

    $db->commit();

    $_SESSION['success'][] = 'Notification sent to employee successfully';

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'][] = 'Error sending notification: ' . $e->getMessage();
    error_log("Error sending notification: " . $e->getMessage());
}

// Redirect back
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit();