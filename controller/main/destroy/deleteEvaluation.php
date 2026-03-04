<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);




if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['__method'] === 'DELETE') {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'][] = 'Invalid security token. Please try again.';
        header('Location: /main?tab=performance');
        exit();
    }

    if (!isset($_POST['employee_id']) || !is_numeric($_POST['employee_id'])) {
        $_SESSION['error'][] = 'Invalid employee ID.';
        header('Location: /main?tab=performance');
        exit();
    }

    if (!isset($_POST['evaluation_id']) || !is_numeric($_POST['evaluation_id'])) {
        $_SESSION['error'][] = 'Invalid evaluation ID.';
        header('Location: /main?tab=performance');
        exit();
    }

    $employeeId = $_POST['employee_id'];
    $evaluationId = $_POST['evaluation_id'];

    try {
        $db->beginTransaction();

        $evaluation = $db->query(
            "SELECT id FROM performance_evaluations WHERE id = :id AND employee_id = :employee_id",
            [
                ':id' => $evaluationId,
                ':employee_id' => $employeeId
            ]
        )->find();

        if (!$evaluation) {
            $db->rollBack();
            $_SESSION['error'][] = 'Evaluation not found or does not belong to this employee.';
            header('Location: /main?tab=performance');
            exit();
        }

        $deleted = $db->query(
            "DELETE FROM performance_evaluations WHERE id = :id AND employee_id = :employee_id",
            [
                ':id' => $evaluationId,
                ':employee_id' => $employeeId
            ]
        );

        if ($deleted) {
            $updated = $db->query(
                "UPDATE employees SET status = 'Probationary', evaluation_status = 'Pending' WHERE id = :id",
                [':id' => $employeeId]
            );

            $db->commit();
            $_SESSION['success'][] = 'Employee evaluation deleted successfully. Employee status updated to Probationary and evaluation status set to Pending.';
        } else {
            $db->rollBack();
            $_SESSION['error'][] = 'Failed to delete evaluation';
        }

    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'][] = 'Error deleting evaluation: ' . $e->getMessage();
    }

    header('Location: /main?tab=performance');
    exit();
} else {
    header('Location: /main?tab=performance');
    exit();
}