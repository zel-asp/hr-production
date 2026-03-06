<?php
use Core\Database;

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

// Validate required fields
$required = ['employee_id', 'recognition_type', 'performance_highlight'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'][] = 'All fields are required';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit();
    }
}

$config = require base_path('config/config.php');
$db = new Database($config['database']);

$employeeId = (int) $_POST['employee_id'];
$recognitionType = $_POST['recognition_type'];
$performanceHighlight = $_POST['performance_highlight'];
$recognitionDate = !empty($_POST['recognition_date']) ? $_POST['recognition_date'] : date('Y-m-d');
$recognizedBy = $_SESSION['employee']['employee_record_id'] ?? null;

try {
    $db->beginTransaction();

    // Check if employee exists
    $employee = $db->query("SELECT id FROM employees WHERE id = ?", [$employeeId])->fetch_one();
    if (!$employee) {
        throw new Exception('Employee not found');
    }

    // Insert recognition
    $db->query("
        INSERT INTO employee_recognitions 
        (employee_id, recognition_type, performance_highlight, recognized_by, recognition_date, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ", [$employeeId, $recognitionType, $performanceHighlight, $recognizedBy, $recognitionDate]);

    $db->commit();

    $_SESSION['success'][] = 'Recognition given successfully!';

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'][] = $e->getMessage();
    error_log("Error giving recognition: " . $e->getMessage());
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit();