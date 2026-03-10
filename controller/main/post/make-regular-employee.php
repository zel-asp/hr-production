<?php
use Core\Database;
require base_path("core/middleware/adminAuth.php");
$config = require base_path('config/config.php');
$db = new Database($config['database']);




if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = ["Invalid CSRF token."];
        header('Location:/main?tab=performance');
        exit;
    }
    $employeeId = (int) $_POST['employee_id'];
    $evaluationId = (int) $_POST['evaluation_id'];
    $effectiveDate = $_POST['effective_date'] ?? null;
    $employmentType = $_POST['employment_type'] ?? null;
    $managerComments = $_POST['manager_comments'] ?? '';

    if (!$effectiveDate || !$employmentType) {
        $_SESSION['error'][] = "All required fields must be filled.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    try {
        $db->beginTransaction();

        // Insert regular employment record
        $db->query(
            "INSERT INTO regular_employment (employee_id, evaluation_id, effective_date, employment_type, manager_comments)
            VALUES (:employee_id, :evaluation_id, :effective_date, :employment_type, :manager_comments)",
            [
                ':employee_id' => $employeeId,
                ':evaluation_id' => $evaluationId,
                ':effective_date' => $effectiveDate,
                ':employment_type' => $employmentType,
                ':manager_comments' => $managerComments
            ]
        );

        // Update employee status to Regular
        $db->query(
            "UPDATE employees SET status = 'Regular' WHERE id = :employee_id",
            [':employee_id' => $employeeId]
        );

        // ============================================
        // CHECK IF ALL TASKS ARE COMPLETED
        // ============================================

        // Check if there are any pending tasks for this employee
        $pendingTasks = $db->query(
            "SELECT COUNT(*) as pending_count 
             FROM tasks 
             WHERE assigned_to = :employee_id 
             AND status != 'Completed'",
            [':employee_id' => $employeeId]
        )->fetch_one();

        // Also get total tasks count for logging/info
        $totalTasks = $db->query(
            "SELECT COUNT(*) as total_count 
             FROM tasks 
             WHERE assigned_to = :employee_id",
            [':employee_id' => $employeeId]
        )->fetch_one();

        // If no pending tasks (all tasks completed), update onboarding_status to 'Onboarded'
        if ($pendingTasks && $pendingTasks['pending_count'] == 0) {

            // Check current onboarding status
            $currentStatus = $db->query(
                "SELECT onboarding_status FROM employees WHERE id = :employee_id",
                [':employee_id' => $employeeId]
            )->fetch_one();

            // Only update if not already Onboarded
            if ($currentStatus && $currentStatus['onboarding_status'] !== 'Onboarded') {

                $db->query(
                    "UPDATE employees 
                     SET onboarding_status = 'Onboarded', 
                         updated_at = NOW() 
                     WHERE id = :employee_id",
                    [':employee_id' => $employeeId]
                );

                // Log the onboarding completion
                error_log("Employee ID {$employeeId} has completed all tasks (" .
                    ($totalTasks['total_count'] ?? 0) . " tasks) and is now Onboarded");

                // Add special success message
                $_SESSION['success'][] = "Congratulations! Employee has completed all onboarding tasks and is now fully onboarded!";
            } else {
                error_log("Employee ID {$employeeId} already has onboarding_status = 'Onboarded'");
            }
        } else {
            // Log how many tasks are still pending
            $pendingCount = $pendingTasks['pending_count'] ?? 0;
            $totalCount = $totalTasks['total_count'] ?? 0;

            if ($totalCount > 0) {
                error_log("Employee ID {$employeeId} regularized but still has {$pendingCount} pending tasks out of {$totalCount} total");

                // Optional: Add info message about pending tasks
                $_SESSION['info'][] = "Employee regularized but still has {$pendingCount} pending onboarding task(s).";
            }
        }

        $db->commit();

        // Main success message
        $_SESSION['success'][] = "Employee has been successfully regularized.";

    } catch (\Throwable $e) {
        if ($db->inTransaction())
            $db->rollBack();
        $_SESSION['error'][] = "Error: " . $e->getMessage();
        error_log("Error in regularization process: " . $e->getMessage());
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}