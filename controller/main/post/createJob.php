<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);

require base_path("core/middleware/adminAuth.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize inputs
    $position = trim($_POST['position']);
    $salary = trim($_POST['rate_per_hour']);
    $shift = trim($_POST['shift']);
    $location = trim($_POST['location']);
    $department = trim($_POST['department']);

    // Validate inputs
    if (empty($position) || empty($salary) || empty($shift) || empty($location) || empty($department)) {
        $error[] = 'All fields are required.';
    }

    if (empty($error)) {
        try {
            // Fix: Check what your Database class returns
            $existing = $db->query(
                "SELECT COUNT(*) AS count FROM job_postings WHERE position = ? AND department = ? AND location = ? AND shift = ? AND salary = ?",
                [$position, $department, $location, $shift, $salary]
            )->find();

            // Debug: Uncomment this to see what's actually returned
            // var_dump($existing); exit;

            // Fix: Check the structure properly
            if ($existing) {
                // If $existing is an object or array with 'count' property
                if (is_array($existing) && isset($existing['count']) && $existing['count'] > 0) {
                    $error[] = 'This job posting already exists.';
                    $_SESSION['error'] = $error;
                    header('Location: /main?tab=recruitment&modal=newJobModal');
                    exit();
                }
                // If $existing is just the count value
                elseif (is_numeric($existing) && $existing > 0) {
                    $error[] = 'This job posting already exists.';
                    $_SESSION['error'] = $error;
                    header('Location: /main?tab=recruitment&modal=newJobModal');
                    exit();
                }
            }

            $db->query(
                "INSERT INTO job_postings (position, department, location, shift, salary) VALUES (?, ?, ?, ?, ?)",
                [$position, $department, $location, $shift, $salary]
            );

            $success[] = 'Job posting created successfully.';
            $_SESSION['success'] = $success;
            header('Location: /main?tab=recruitment');
            exit();

        } catch (\Throwable $th) {
            $error[] = (strpos($th->getMessage(), 'unique_job_posting') !== false)
                ? 'This job posting already exists.'
                : 'Something went wrong. Please try again.';

            $_SESSION['error'] = $error;
            header('Location: /main?tab=recruitment&modal=newJobModal');
            exit();
        }
    } else {
        $_SESSION['error'] = $error;
        header('Location: /main?tab=recruitment&modal=newJobModal');
        exit();
    }
}