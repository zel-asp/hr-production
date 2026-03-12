<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);
require base_path("core/middleware/adminAuth.php");

try {
    // Handle Delete Job Posting
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete-jobBtn'])) {

        // Debug - log everything
        error_log("=== DELETE ATTEMPTED ===");
        error_log("POST data: " . print_r($_POST, true));

        // Check CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            error_log("CSRF FAILED - Token mismatch");
            $_SESSION['error'][] = "Invalid request.";
            header('Location: /main?tab=recruitment');
            exit;
        }

        $jobId = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
        error_log("Job ID received: " . $jobId);

        if ($jobId <= 0) {
            error_log("INVALID JOB ID: " . $jobId);
            $_SESSION['error'][] = "Invalid job ID.";
            header('Location: /main?tab=recruitment');
            exit;
        }

        // First check if job exists
        $check = $db->query("SELECT id FROM job_postings WHERE id = ?", [$jobId])->find();
        error_log("Job exists check: " . ($check ? "YES" : "NO"));

        if (!$check) {
            error_log("JOB NOT FOUND: " . $jobId);
            $_SESSION['error'][] = "Job posting not found.";
            header('Location: /main?tab=recruitment');
            exit;
        }

        // Attempt delete
        $deleted = $db->query("DELETE FROM job_postings WHERE id = ?", [$jobId]);
        error_log("Delete result: " . ($deleted ? "SUCCESS" : "FAILED"));

        if ($deleted) {
            error_log("DELETE SUCCESSFUL for job ID: " . $jobId);
            $_SESSION['success'][] = "Post deleted successfully.";
        } else {
            error_log("DELETE FAILED for job ID: " . $jobId);
            $_SESSION['error'][] = "Failed to delete the post.";
        }

        header('Location: /main?tab=recruitment');
        exit;
    }
} catch (\Throwable $th) {
    error_log("DELETE EXCEPTION: " . $th->getMessage());
    error_log("Stack trace: " . $th->getTraceAsString());
    $_SESSION['error'][] = "An unexpected error occurred: " . $th->getMessage();
    header('Location: /main?tab=recruitment');
    exit;
}