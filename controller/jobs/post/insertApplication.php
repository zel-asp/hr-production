<?php


use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$position = trim($_POST['position'] ?? '');
$experience = trim($_POST['experience'] ?? '');
$education = trim($_POST['education'] ?? '');
$skills = trim($_POST['skills'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$age = trim($_POST['age'] ?? '');
$coverNote = trim($_POST['cover_note'] ?? '');
$resumePath = trim($_POST['resume_url'] ?? null);


$errors = [];

if (empty($fullName))
    $errors[] = "Full name is required.";
if (empty($email))
    $errors[] = "Email is required.";
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = "Invalid email format.";
if (!empty($phone) && !preg_match('/^[0-9\-\+\(\) ]+$/', $phone))
    $errors[] = "Phone number format is invalid.";
if (empty($position))
    $errors[] = "Position is required.";
if (empty($experience))
    $errors[] = "Experience is required.";
if (empty($education))
    $errors[] = "Education / Certifications are required.";
if (empty($skills))
    $errors[] = "Skills are required.";

if (!empty($errors)) {
    $_SESSION['error'] = $errors;
    header('Location: /jobPosting#applySection');
    exit;
}


$existing = $db->query("SELECT COUNT(*) AS count FROM applicants WHERE email = ?", [$email])->fetch_one();
if ($existing && $existing['count'] > 0) {
    $_SESSION['error'] = ["An application with this email already exists."];
    header('Location: /jobPosting#applySection');
    exit;
}

$departments = $db->query("SELECT department FROM job_postings WHERE position = ?", [$position])->fetch_one();
$department = $departments['department'];

try {
    $db->query("
        INSERT INTO applicants 
        (full_name, email, phone, position, department, experience, education, skills, resume_path, cover_note, age, gender)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)
    ", [$fullName, $email, $phone, $position, $department, $experience, $education, $skills, $resumePath, $coverNote, $age, $gender]);

    $_SESSION['success'] = ["Application submitted successfully!"];
    header('Location: /jobPosting#applySection');
    exit;

} catch (\Throwable $e) {
    $_SESSION['error'] = ["Database error: Please try again later."];
    header('Location: /jobPosting#applySection');
    exit;
}