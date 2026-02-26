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

        $db->query(
            "UPDATE employees SET status = 'Regular' WHERE id = :employee_id",
            [':employee_id' => $employeeId]
        );

        $db->commit();
        $_SESSION['success'][] = "Employee has been successfully regularized.";
    } catch (\Throwable $e) {
        if ($db->inTransaction())
            $db->rollBack();
        $_SESSION['error'][] = "Error: " . $e->getMessage();
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
