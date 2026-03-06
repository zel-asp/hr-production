<?php
use Core\Database;

require base_path('core/middleware/employeeAuth.php');

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

// Validate required fields
$required = ['mentee_id', 'rating', 'comment'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'][] = 'All fields are required';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit();
    }
}

$menteeId = (int) $_POST['mentee_id'];
$mentorId = $_SESSION['employee']['employee_record_id'] ?? null;

$rating = (int) $_POST['rating'];
$comment = trim($_POST['comment']);

if (!$mentorId) {
    $_SESSION['error'][] = 'You must be logged in';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Validate rating range
if ($rating < 1 || $rating > 5) {
    $_SESSION['error'][] = 'Rating must be between 1 and 5';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

try {
    $db->beginTransaction();

    // Check if mentor is assigned to this mentee
    $assignment = $db->query("
        SELECT id FROM mentor_assignments 
        WHERE mentor_employee_id = ? AND mentee_employee_id = ? AND status = 'Active'
    ", [$mentorId, $menteeId])->fetch_one();

    if (!$assignment) {
        throw new Exception('You are not assigned as a mentor to this employee');
    }

    // Insert rating
    $db->query("
        INSERT INTO mentor_ratings 
        (mentee_employee_id, mentor_employee_id, rating, comment, rating_date, created_at)
        VALUES (?, ?, ?, ?, CURDATE(), NOW())
    ", [$menteeId, $mentorId, $rating, $comment]);

    $db->commit();

    $_SESSION['success'][] = 'Rating submitted successfully';

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'][] = $e->getMessage();
    error_log("Error submitting mentor rating: " . $e->getMessage());
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit();