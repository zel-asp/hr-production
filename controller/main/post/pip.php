<?php
use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);

$_SESSION['success'] ??= [];
$_SESSION['error'] ??= [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = ["Invalid CSRF token."];
        header('Location:/main?tab=performance');
        exit;
    }

    $employeeId = (int) $_POST['employee_id'];
    $evaluationId = (int) $_POST['evaluation_id'];
    $improvementAreas = $_POST['improvement_areas'] ?? '';
    $goal1 = $_POST['goal1'] ?? null;
    $goal2 = $_POST['goal2'] ?? null;
    $goal3 = $_POST['goal3'] ?? null;
    $pipStartDate = $_POST['pip_start_date'] ?? null;
    $pipEndDate = $_POST['pip_end_date'] ?? null;

    if (!$improvementAreas || !$pipStartDate || !$pipEndDate) {
        $_SESSION['error'][] = "All required fields must be filled.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    try {
        $db->beginTransaction();

        // Insert into PIP table
        $db->query(
            "INSERT INTO performance_improvement_plans 
            (employee_id, evaluation_id, improvement_areas, goal1, goal2, goal3, pip_start_date, pip_end_date)
            VALUES 
            (:employee_id, :evaluation_id, :improvement_areas, :goal1, :goal2, :goal3, :pip_start_date, :pip_end_date)",
            [
                ':employee_id' => $employeeId,
                ':evaluation_id' => $evaluationId,
                ':improvement_areas' => $improvementAreas,
                ':goal1' => $goal1,
                ':goal2' => $goal2,
                ':goal3' => $goal3,
                ':pip_start_date' => $pipStartDate,
                ':pip_end_date' => $pipEndDate
            ]
        );

        // Update employee's evaluation status to "Evaluated"
        $db->query(
            "UPDATE employees SET status = 'Improvement' WHERE id = :employee_id",
            [':employee_id' => $employeeId]
        );

        $db->commit();
        $_SESSION['success'][] = "Performance Improvement Plan created successfully and status marked as Improvement.";
    } catch (\Throwable $e) {
        if ($db->inTransaction())
            $db->rollBack();
        $_SESSION['error'][] = "Error: " . $e->getMessage();
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}