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
$required = ['mentee_id', 'mentor_id', 'duration', 'goals'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'][] = 'All fields are required';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit();
    }
}

$config = require base_path('config/config.php');
$db = new Database($config['database']);

$menteeId = (int) $_POST['mentee_id'];
$mentorId = (int) $_POST['mentor_id'];
$duration = $_POST['duration'];
$goals = $_POST['goals'];

try {
    $db->beginTransaction();

    // Check if mentee exists and is eligible
    $mentee = $db->query("
        SELECT id FROM employees 
        WHERE id = ? AND status IN ('Probationary', 'Onboarding')
    ", [$menteeId])->fetch_one();

    if (!$mentee) {
        throw new Exception('Invalid mentee selected');
    }

    // Check if mentor exists and is eligible
    $mentor = $db->query("
        SELECT id FROM employees 
        WHERE id = ? AND role IN ('mentor', 'evaluator', 'admin')
    ", [$mentorId])->fetch_one();

    if (!$mentor) {
        throw new Exception('Invalid mentor selected');
    }

    // Check for existing active assignment
    $existing = $db->query("
        SELECT id FROM mentor_assignments 
        WHERE mentee_employee_id = ? AND status = 'Active'
    ", [$menteeId])->fetch_one();

    if ($existing) {
        throw new Exception('This employee already has an active mentor');
    }

    // Create mentor assignment
    $db->query("
        INSERT INTO mentor_assignments 
        (mentee_employee_id, mentor_employee_id, program_duration, goals, status, created_at)
        VALUES (?, ?, ?, ?, 'Active', NOW())
    ", [$menteeId, $mentorId, $duration, $goals]);

    $db->commit();

    $_SESSION['success'][] = 'Mentor assigned successfully';

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'][] = $e->getMessage();
    error_log("Error assigning mentor: " . $e->getMessage());
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit();