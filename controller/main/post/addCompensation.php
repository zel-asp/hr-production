<?php

use Core\Database;

require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// CSRF check
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = "Invalid security token.";
    header('Location: /main?tab=compensation');
    exit();
}

// Get and sanitize input
$employeeId = (int) $_POST['employee_id'];
$currentSalary = (float) $_POST['current_salary'];
$proposedSalary = (float) $_POST['proposed_salary'];
$reviewDate = $_POST['review_date'];
$effectiveDate = $_POST['effective_date'];
$reviewType = $_POST['review_type'];
$notes = $_POST['finance_notes'] ?? null;
$ratePerhour = $_POST['ratePerHour'] ?? null;
$createdBy = $_SESSION['admin']['id'] ?? null;

if (
    !$employeeId ||
    !$currentSalary ||
    !$proposedSalary ||
    !$reviewDate ||
    !$effectiveDate ||
    !$reviewType
) {
    $_SESSION['error'][] = "All required fields must be filled.";
    header('Location: /main?tab=compensation');
    exit();
}

$db->query("
    INSERT INTO compensation_reviews 
    (employee_id, current_salary, proposed_salary, review_type, review_date, effective_date, finance_notes, created_by, proposed_hourly_rate)
    VALUES 
    (:employee_id, :current_salary, :proposed_salary, :review_type, :review_date, :effective_date, :finance_notes, :created_by, :ratePerhour)
", [
    'employee_id' => $employeeId,
    'current_salary' => $currentSalary,
    'proposed_salary' => $proposedSalary,
    'review_type' => $reviewType,
    'review_date' => $reviewDate,
    'effective_date' => $effectiveDate,
    'finance_notes' => $notes,
    'created_by' => $createdBy,
    'ratePerhour' => $ratePerhour

]);

$_SESSION['success'][] = "Compensation review submitted successfully.";
header('Location: /main?tab=compensation');
exit();