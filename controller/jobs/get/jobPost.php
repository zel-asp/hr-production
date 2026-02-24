<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Fetch all job postings securely
try {
    $jobPostings = $db->query("SELECT id, position, department, location, shift, salary, created_at FROM job_postings ORDER BY created_at DESC")->find();
} catch (\Throwable $th) {
    $jobPostings = [];
    error_log($th->getMessage());
}

view_path('jobs', 'index', [
    'jobPostings' => $jobPostings
]);