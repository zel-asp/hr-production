<?php

use Core\Database;
require base_path("core/middleware/adminAuth.php");
$config = require base_path('config/config.php');
$db = new Database($config['database']);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'][] = 'Invalid CSRF token.';
    } else {

        // Sanitize inputs
        $assigned_to = filter_var($_POST['assigned_to'], FILTER_VALIDATE_INT);
        $task_type = trim($_POST['task_type']);
        $task_description = trim($_POST['task_description']);
        $due_date = $_POST['due_date'];
        $priority = ucfirst(strtolower(trim($_POST['priority'])));
        $assigned_staff = trim($_POST['assigned_staff']);

        // Validation
        if (!$assigned_to)
            $_SESSION['error'][] = 'Invalid employee selected.';
        if (empty($task_type))
            $_SESSION['error'][] = 'Task type is required.';
        if (empty($task_description))
            $_SESSION['error'][] = 'Task description is required.';
        if (empty($due_date) || !date_create($due_date))
            $_SESSION['error'][] = 'Invalid due date.';
        if (!in_array($priority, ['Low', 'Medium', 'High', 'Urgent']))
            $priority = 'Medium';

        if (empty($_SESSION['error'])) {
            try {
                // First, check if employee exists and get current onboarding status
                $employee = $db->query("
                    SELECT id, onboarding_status, full_name 
                    FROM employees 
                    WHERE id = :employee_id
                ", [
                    ':employee_id' => $assigned_to
                ])->fetch_one();

                if (!$employee) {
                    $_SESSION['error'][] = "Employee with ID {$assigned_to} not found.";
                    header('Location: /main?tab=learning');
                    exit();
                }

                // Check if employee is already onboarded
                if ($employee['onboarding_status'] === 'Onboarded') {
                    $_SESSION['warning'][] = "Warning: This employee is already Onboarded. Task will still be assigned.";
                }

                // start transaction to ensure both updates succeed together
                $db->beginTransaction();

                // insert new task
                $db->query("
                    INSERT INTO tasks (assigned_to, task_type, task_description, due_date, priority)
                    VALUES (:assigned_to, :task_type, :task_description, :due_date, :priority)
                ", [
                    ':assigned_to' => $assigned_to,
                    ':task_type' => $task_type,
                    ':task_description' => $task_description,
                    ':due_date' => $due_date,
                    ':priority' => $priority,
                ]);

                // Get the ID of the newly inserted task
                $taskId = $db->lastInsertId();

                // Update employee onboarding status to 'In Progress' only if it's 'Onboarding'
                // This prevents overwriting 'Onboarded' or 'In Progress'
                if ($employee['onboarding_status'] === 'Onboarding') {
                    $db->query("
                        UPDATE employees 
                        SET onboarding_status = 'In Progress', 
                            updated_at = NOW() 
                        WHERE id = :employee_id 
                        AND onboarding_status = 'Onboarding'
                    ", [
                        ':employee_id' => $assigned_to
                    ]);

                    $rowsAffected = $db->count();

                    if ($rowsAffected > 0) {
                        error_log("Employee ID {$assigned_to} onboarding_status updated from 'Onboarding' to 'In Progress' due to new task ID {$taskId}");
                    }
                } else {
                    // Log if status wasn't updated
                    error_log("Employee ID {$assigned_to} onboarding_status remains '{$employee['onboarding_status']}' (not updated to In Progress)");
                }

                // commit transaction if both queries succeeded
                $db->commit();

                // Success message based on status update
                if ($employee['onboarding_status'] === 'Onboarding') {
                    $_SESSION['success'][] = 'Task assigned successfully! Onboarding status updated to In Progress.';
                } elseif ($employee['onboarding_status'] === 'In Progress') {
                    $_SESSION['success'][] = 'Task assigned successfully! (Onboarding already in progress)';
                } elseif ($employee['onboarding_status'] === 'Onboarded') {
                    $_SESSION['success'][] = 'Task assigned successfully! (Employee is already Onboarded)';
                } else {
                    $_SESSION['success'][] = 'Task assigned successfully!';
                }

            } catch (PDOException $e) {
                // rollback if anything failed
                $db->rollBack();
                error_log("Error assigning task: " . $e->getMessage());
                $_SESSION['error'][] = 'Database error: ' . $e->getMessage();
            }
        }
    }

    header('Location: /main?tab=learning');
    exit;
}