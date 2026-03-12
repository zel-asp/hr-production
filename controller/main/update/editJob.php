<?php
use Core\Database;
require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);

$success = [];
$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $jobId = intval($_POST['job_id']);
    $title = trim($_POST['job_title']);
    $location = trim($_POST['location']);
    $shift = trim($_POST['shift']);
    $salary = trim($_POST['salary_range']);

    // Validate
    if (empty($title) || empty($location) || empty($shift)) {
        $error[] = "All required fields must be filled.";
    }

    if (empty($error)) {
        try {
            $existing = $db->query(
                "SELECT COUNT(*) AS count FROM job_postings WHERE position = ? AND location = ? AND shift = ? AND id != ?",
                [$title, $location, $shift, $jobId]
            )->fetch_one();  // ← Returns a single row or false

            if ($existing && $existing['count'] > 0) {
                $error[] = "A similar job posting already exists.";
                $_SESSION['error'] = $error;
                header('Location: /main?tab=recruitment');
                exit;
            }

            $db->query(
                "UPDATE job_postings SET position = ?, location = ?, shift = ?, salary = ? WHERE id = ?",
                [$title, $location, $shift, $salary, $jobId]
            );

            $success[] = "Job posting updated successfully.";
            $_SESSION['success'] = $success;
            header('Location: /main?tab=recruitment');
            exit;

        } catch (\Throwable $th) {
            $error[] = "Something went wrong. Please try again.";
            $_SESSION['error'] = $error;
            header('Location: /main?tab=recruitment&modal=editJobModal');
            exit;
        }
    } else {
        $_SESSION['error'] = $error;
        header('Location: /main?tab=recruitment&modal=editJobModal');
        exit;
    }
} else {
    header('Location: /main?tab=recruitment&modal=editJobModal');
    exit;
}