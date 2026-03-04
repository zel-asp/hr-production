<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);


try {
    // Handle Delete Job Posting
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete-jobBtn'])) {
        // Optional: CSRF token verification
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['error'][] = "Invalid request.";
            header('Location: /main?tab=recruitment');
            exit;
        }

        $jobId = intval($_POST['job_id']);

        if ($jobId <= 0) {
            $_SESSION['error'][] = "Invalid job ID.";
            header('Location: /main?tab=recruitment');
            exit;
        }

        $deleted = $db->query("DELETE FROM job_postings WHERE id = ?", [$jobId]);


        if ($deleted) {
            $_SESSION['success'][] = "Post deleted successfully.";
        } else {
            $_SESSION['error'][] = "Failed to delete the post.";
        }

        header('Location: /main?tab=recruitment');
        exit;
    }
} catch (\Throwable $th) {
    error_log($th->getMessage());
    $_SESSION['error'][] = "An unexpected error occurred.";
    header('Location: /main?tab=recruitment');
    exit;
}