<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Fetch all job postings securely
try {
    $jobPostings = $db->query(
        "SELECT id, position, department, location, shift, salary, created_at FROM job_postings ORDER BY created_at DESC"
    )->find();
} catch (\Throwable $th) {
    $jobPostings = [];
    error_log($th->getMessage());
}

// Fetch all applicants securely
try {
    $applicants = $db->query(
        "SELECT id, full_name, email, phone, position, experience, education, skills, resume_path, cover_note, created_at FROM applicants ORDER BY created_at DESC"
    )->find();
} catch (\Throwable $th) {
    $applicants = [];
    error_log($th->getMessage());
}

view_path('main', 'index', [
    'jobPostings' => $jobPostings,
    'applicants' => $applicants
]);