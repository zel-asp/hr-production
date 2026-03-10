<?php

use Core\Database;
require base_path("core/middleware/adminAuth.php");
$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Verify CSRF token
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = "Invalid security token";
    header('Location: /main?tab=training');
    exit();
}

// Required fields validation
$required_fields = [
    'training_type' => 'Training type',
    'competency_id' => 'Competency',
    'start_date' => 'Start date',
    'end_date' => 'End date',
    'employee_id' => 'Employee'
];

foreach ($required_fields as $field => $label) {
    if (empty($_POST[$field])) {
        $_SESSION['error'][] = "{$label} is required";
    }
}

// Validate dates
if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
    if ($_POST['end_date'] < $_POST['start_date']) {
        $_SESSION['error'][] = "End date cannot be before start date";
    }
}

// Validate training type
$allowed_training_types = ['internal', 'external', 'certification'];

if (!empty($_POST['training_type']) && !in_array($_POST['training_type'], $allowed_training_types)) {
    $_SESSION['error'][] = "Invalid training type selected";
}

$providerId = null;

if (isset($_POST['training_type']) && $_POST['training_type'] === 'external') {

    if (!empty($_POST['provider_id'])) {

        $providerId = (int) $_POST['provider_id'];

    } elseif (!empty($_POST['new_provider_name'])) {

        $newProviderName = trim($_POST['new_provider_name']);

        $db->query(
            "INSERT INTO training_providers (name, type, created_at) 
             VALUES (:name, 'external', NOW())",
            [':name' => $newProviderName]
        );

        $providerId = $db->lastInsertId();

    } else {

        $_SESSION['error'][] = "Please select or add an external training provider";
    }
}

if (!empty($_SESSION['error'])) {
    header('Location: /main?tab=training');
    exit();
}

// Prepare data for insertion
$data = [
    'training_type' => $_POST['training_type'],
    'competency_id' => (int) $_POST['competency_id'],
    'start_date' => $_POST['start_date'],
    'end_date' => $_POST['end_date'],
    'start_time' => !empty($_POST['start_time']) ? $_POST['start_time'] : null,
    'end_time' => !empty($_POST['end_time']) ? $_POST['end_time'] : null,
    'venue' => !empty($_POST['venue']) ? $_POST['venue'] : null,
    'employee_id' => (int) $_POST['employee_id'],
    'provider_id' => $providerId,
    'status' => 'Scheduled',
];

try {

    $sql = "INSERT INTO training_schedule (
        training_type, 
        competency_id, 
        start_date, 
        end_date, 
        start_time, 
        end_time, 
        venue, 
        employee_id,
        provider_id, 
        status
    ) VALUES (
        :training_type, 
        :competency_id, 
        :start_date, 
        :end_date, 
        :start_time, 
        :end_time, 
        :venue, 
        :employee_id, 
        :provider_id,
        :status
    )";

    $db->query($sql, $data);

    $_SESSION['success'][] = 'Scheduled successfully!';

    header('Location: /main?tab=training');
    exit();

} catch (PDOException $e) {

    $_SESSION['error'][] = 'Database error: ' . $e->getMessage();

    header('Location: /main?tab=training');
    exit();
}