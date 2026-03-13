<?php

use Core\Database;
require base_path("core/middleware/adminAuth.php");
$config = require base_path('config/config.php');
$db = new Database($config['database']);

// CSRF check
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = 'Invalid CSRF token';
    header('Location: /main?tab=hmo');
    exit();
}

// Required fields validation
if (
    empty($_POST['employee_ids']) || !is_array($_POST['employee_ids']) ||
    empty($_POST['benefit_type']) ||
    empty($_POST['provider_id']) ||
    empty($_POST['effective_date'])
) {
    $_SESSION['error'][] = 'Please select at least one employee and fill in all required fields';
    header('Location: /main?tab=hmo');
    exit();
}

try {
    // Sanitize common inputs
    $benefit_type = $_POST['benefit_type'];
    $provider_id = $_POST['provider_id'];
    $effective_date = $_POST['effective_date'];
    $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;

    $coverage_amount = !empty($_POST['coverage_amount'])
        ? preg_replace('/[^0-9.]/', '', $_POST['coverage_amount'])
        : null;

    $monthly_premium = !empty($_POST['monthly_premium'])
        ? preg_replace('/[^0-9.]/', '', $_POST['monthly_premium'])
        : null;

    $dependents = !empty($_POST['dependents'])
        ? $_POST['dependents']
        : null;

    // Sanitize employee IDs
    $employee_ids = array_map(function ($id) {
        return filter_var($id, FILTER_SANITIZE_NUMBER_INT);
    }, $_POST['employee_ids']);

    // Remove any empty or invalid IDs
    $employee_ids = array_filter($employee_ids, function ($id) {
        return is_numeric($id) && $id > 0;
    });

    if (empty($employee_ids)) {
        $_SESSION['error'][] = 'Invalid employee selection';
        header('Location: /main?tab=hmo');
        exit();
    }

    $db->beginTransaction();

    // Prepare the insert query once
    $insertQuery = "INSERT INTO employee_benefits 
        (employee_id, benefit_type, provider_id, effective_date, expiry_date, 
        coverage_amount, monthly_premium, dependents, created_at, updated_at) 
        VALUES 
        (:employee_id, :benefit_type, :provider_id, :effective_date, :expiry_date,
        :coverage_amount, :monthly_premium, :dependents, NOW(), NOW())";

    // Insert for each selected employee
    $successCount = 0;
    foreach ($employee_ids as $employee_id) {
        $existing = $db->query(
            "SELECT id FROM employee_benefits 
            WHERE employee_id = :employee_id 
            AND benefit_type = :benefit_type 
            AND (expiry_date IS NULL OR expiry_date >= CURDATE())",
            [
                'employee_id' => $employee_id,
                'benefit_type' => $benefit_type
            ]
        )->find();

        if ($existing) {
            continue;
        }

        $result = $db->query($insertQuery, [
            'employee_id' => $employee_id,
            'benefit_type' => $benefit_type,
            'provider_id' => $provider_id,
            'effective_date' => $effective_date,
            'expiry_date' => $expiry_date,
            'coverage_amount' => $coverage_amount,
            'monthly_premium' => $monthly_premium,
            'dependents' => $dependents
        ]);

        if ($result) {
            $successCount++;
        }

        if ($successCount > 0) {
            $placeholders = implode(',', array_fill(0, count($employee_ids), '?'));
            $db->query(
                "UPDATE employees SET benefit_status = 'enrolled' WHERE id IN ($placeholders)",
                $employee_ids
            );
        }

    }

    if ($successCount > 0) {
        $db->commit();
        $_SESSION['success'][] = $successCount . ' employee(s) successfully enrolled in benefits';
    } else {
        $db->rollBack();
        $_SESSION['error'][] = 'No employees were enrolled. They may already have active benefits of this type.';
    }

} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Benefit enrollment error: " . $e->getMessage());
    $_SESSION['error'][] = 'Failed to enroll employees in benefits. Please try again.';
}

header('Location: /main?tab=hmo');
exit();