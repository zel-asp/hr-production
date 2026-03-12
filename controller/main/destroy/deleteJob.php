<?php
use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);
require base_path("core/middleware/adminAuth.php");

// For DELETE requests via method spoofing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['__method']) && $_POST['__method'] === 'DELETE') {

    // Debug
    error_log("=== DELETE JOB ATTEMPTED ===");
    error_log("POST data: " . print_r($_POST, true));

    // Check CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'][] = "Invalid request.";
        header('Location: /main?tab=recruitment');
        exit;
    }

    $jobId = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;

    if ($jobId <= 0) {
        $_SESSION['error'][] = "Invalid job ID.";
        header('Location: /main?tab=recruitment');
        exit;
    }

    try {
        // Check if job exists
        $check = $db->query("SELECT id FROM job_postings WHERE id = ?", [$jobId])->find();

        if (!$check) {
            $_SESSION['error'][] = "Job posting not found.";
            header('Location: /main?tab=recruitment');
            exit;
        }

        // Delete job
        $deleted = $db->query("DELETE FROM job_postings WHERE id = ?", [$jobId]);

        if ($deleted) {
            $_SESSION['success'][] = "Post deleted successfully.";
        } else {
            $_SESSION['error'][] = "Failed to delete the post.";
        }

    } catch (\Throwable $th) {
        error_log("DELETE EXCEPTION: " . $th->getMessage());
        $_SESSION['error'][] = "An unexpected error occurred.";
    }

    header('Location: /main?tab=recruitment');
    exit;
}