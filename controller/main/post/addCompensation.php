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

// Handle INSERT (new compensation review)
if (isset($_POST['submitReview'])) {
    // Get and sanitize input
    $employeeId = (int) $_POST['employee_id'];
    $currentSalary = (float) $_POST['current_salary'];
    $proposedSalary = (float) $_POST['proposed_salary'];
    $reviewDate = $_POST['review_date'];
    $effectiveDate = $_POST['effective_date'];
    $reviewType = $_POST['review_type'];
    $notes = $_POST['finance_notes'] ?? null;
    $ratePerhour = $_POST['proposed_hourly_rate'] ?? null;
    $createdBy = $_SESSION['admin']['id'] ?? null;


    // Validate required fields
    if (!$employeeId || !$currentSalary || !$proposedSalary || !$reviewDate || !$effectiveDate || !$reviewType) {
        $_SESSION['error'][] = "All required fields must be filled.";
        header('Location: /main?tab=compensation');
        exit();
    }

    // Insert new compensation review
    $db->query("
        INSERT INTO compensation_reviews 
        (employee_id, current_salary, proposed_salary, review_type, review_date, effective_date, finance_notes, created_by, proposed_hourly_rate, status)
        VALUES 
        (:employee_id, :current_salary, :proposed_salary, :review_type, :review_date, :effective_date, :finance_notes, :created_by, :ratePerhour, 'pending_finance')
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
}

// Handle UPDATE (existing compensation review)
if (isset($_POST['update']) && $_POST['update'] === 'save') {

    // Get and sanitize input
    $reviewId = (int) $_POST['review_id'];
    $employeeId = (int) $_POST['employee_id'] ?? null;
    $currentSalary = (float) ($_POST['current_salary'] ?? 0);
    $proposedSalary = (float) ($_POST['proposed_salary'] ?? 0);
    $reviewDate = $_POST['review_date'] ?? null;
    $effectiveDate = $_POST['effective_date'] ?? null;
    $reviewType = $_POST['review_type'] ?? null;
    $notes = $_POST['finance_notes'] ?? null;
    $ratePerhour = $_POST['proposed_hourly_rate'] ?? null;

    // Validate review ID
    if (!$reviewId) {
        $_SESSION['error'][] = "Invalid review ID.";
        header('Location: /main?tab=compensation');
        exit();
    }

    // Check if review exists
    $review = $db->query("SELECT * FROM compensation_reviews WHERE id = :id", [
        'id' => $reviewId
    ])->find();

    if (!$review) {
        $_SESSION['error'][] = "Compensation review not found.";
        header('Location: /main?tab=compensation');
        exit();
    }

    // Validate required fields separately
    if (!$employeeId) {
        $_SESSION['error'][] = "Employee ID is required.";
    }

    if (!$currentSalary) {
        $_SESSION['error'][] = "Current Salary is required.";
    }

    if (!$proposedSalary) {
        $_SESSION['error'][] = "Proposed Salary is required.";
    }

    if (!$effectiveDate) {
        $_SESSION['error'][] = "Effective Date is required.";
    }

    if (!$reviewType) {
        $_SESSION['error'][] = "Review Type is required.";
    }

    // If there are errors, redirect
    if (!empty($_SESSION['error'])) {
        header('Location: /main?tab=compensation');
        exit();
    }

    // Update the compensation review
    $db->query("
        UPDATE compensation_reviews 
        SET 
            employee_id = :employee_id,
            current_salary = :current_salary,
            proposed_salary = :proposed_salary,
            review_type = :review_type,
            review_date = :review_date,
            effective_date = :effective_date,
            finance_notes = :finance_notes,
            proposed_hourly_rate = :ratePerhour,
            updated_at = NOW()
        WHERE id = :id
    ", [
        'employee_id' => $employeeId,
        'current_salary' => $currentSalary,
        'proposed_salary' => $proposedSalary,
        'review_type' => $reviewType,
        'review_date' => $reviewDate,
        'effective_date' => $effectiveDate,
        'finance_notes' => $notes,
        'ratePerhour' => $ratePerhour,
        'id' => $reviewId
    ]);

    $_SESSION['success'][] = "Compensation review updated successfully.";
    header('Location: /main?tab=compensation');
    exit();
}

// Handle APPROVE/REJECT actions
if (isset($_POST['action']) && in_array($_POST['action'], ['approve', 'reject'])) {

    $reviewId = (int) $_POST['review_id'];
    $action = $_POST['action'];
    $notes = $_POST['finance_notes'] ?? null;
    $approvedBy = $_SESSION['admin']['id'] ?? null;

    // Validate review ID
    if (!$reviewId) {
        $_SESSION['error'][] = "Invalid review ID.";
        header('Location: /main?tab=compensation');
        exit();
    }

    // Check if review exists
    $review = $db->query("SELECT * FROM compensation_reviews WHERE id = :id", [
        'id' => $reviewId
    ])->find();

    if (!$review) {
        $_SESSION['error'][] = "Compensation review not found.";
        header('Location: /main?tab=compensation');
        exit();
    }

    // Determine new status
    $newStatus = ($action === 'approve') ? 'approved' : 'rejected';

    // Update the review status
    $db->query("
        UPDATE compensation_reviews 
        SET 
            status = :status,
            finance_notes = :finance_notes,
            approved_by = :approved_by,
            approved_at = NOW(),
            updated_at = NOW()
        WHERE id = :id
    ", [
        'status' => $newStatus,
        'finance_notes' => $notes,
        'approved_by' => $approvedBy,
        'id' => $reviewId
    ]);

    // If approved, update the employee's salary
    if ($action === 'approve') {
        $db->query("
            UPDATE employees 
            SET 
                salary = :proposed_salary,
                hourly_rate = :proposed_hourly_rate,
                updated_at = NOW()
            WHERE id = :employee_id
        ", [
            'proposed_salary' => $review['proposed_salary'],
            'proposed_hourly_rate' => $review['proposed_hourly_rate'],
            'employee_id' => $review['employee_id']
        ]);

        $_SESSION['success'][] = "Compensation review approved and employee salary updated.";
    } else {
        $_SESSION['success'][] = "Compensation review rejected.";
    }

    header('Location: /main?tab=compensation');
    exit();
}

// Handle DELETE
if (isset($_POST['delete']) && $_POST['delete'] === 'delete') {

    // CSRF check
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'][] = "Invalid security token.";
        header('Location: /main?tab=compensation');
        exit();
    }

    $reviewId = (int) $_POST['review_id'];

    // Validate review ID
    if (!$reviewId) {
        $_SESSION['error'][] = "Invalid review ID.";
        header('Location: /main?tab=compensation');
        exit();
    }

    // Check if review exists and is deletable (only pending/draft)
    $review = $db->query("SELECT * FROM compensation_reviews WHERE id = :id", [
        'id' => $reviewId
    ])->find();

    if (!$review) {
        $_SESSION['error'][] = "Compensation review not found.";
        header('Location: /main?tab=compensation');
        exit();
    }

    // Delete the review
    $db->query("DELETE FROM compensation_reviews WHERE id = :id", [
        'id' => $reviewId
    ]);

    $_SESSION['success'][] = "Compensation review deleted successfully.";
    header('Location: /main?tab=compensation');
    exit();
}