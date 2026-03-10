<?php

use Core\Database;
require base_path("core/middleware/adminAuth.php");
$config = require base_path('config/config.php');
$db = new Database($config['database']);

try {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Invalid CSRF token');
    }

    $required = ['employee_id', 'evaluation_id', 'pip_id', 'improvement_areas', 'pip_start_date', 'pip_end_date'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }

    $employee_id = (int) $_POST['employee_id'];
    $evaluation_id = (int) $_POST['evaluation_id'];
    $pip_id = (int) $_POST['pip_id'];

    $improvement_areas = trim(htmlspecialchars($_POST['improvement_areas'], ENT_QUOTES, 'UTF-8'));
    $goal1 = !empty($_POST['goal1']) ? trim(htmlspecialchars($_POST['goal1'], ENT_QUOTES, 'UTF-8')) : null;
    $goal2 = !empty($_POST['goal2']) ? trim(htmlspecialchars($_POST['goal2'], ENT_QUOTES, 'UTF-8')) : null;
    $goal3 = !empty($_POST['goal3']) ? trim(htmlspecialchars($_POST['goal3'], ENT_QUOTES, 'UTF-8')) : null;

    $pip_start_date = $_POST['pip_start_date'];
    $pip_end_date = $_POST['pip_end_date'];

    if (strtotime($pip_start_date) > strtotime($pip_end_date)) {
        throw new Exception('Start date cannot be after end date');
    }

    $db->beginTransaction();

    $existing = $db->query(
        "SELECT id FROM performance_improvement_plans 
         WHERE id = ? AND employee_id = ? AND evaluation_id = ?",
        [$pip_id, $employee_id, $evaluation_id]
    )->fetch_one();

    if (!$existing) {
        throw new Exception('PIP not found or access denied');
    }

    $averageScore = null;

    if (!empty($_POST['criteria_score']) && is_array($_POST['criteria_score'])) {

        $scores = array_map('intval', $_POST['criteria_score']);
        $averageScore = array_sum($scores) / count($scores);

        $db->query("DELETE FROM performance_criteria_scores WHERE evaluation_id = ?", [$evaluation_id]);

        $labels = [
            1 => 'Job Knowledge',
            2 => 'Quality of Work',
            3 => 'Customer Service',
            4 => 'Teamwork & Collaboration',
            5 => 'Attendance & Punctuality'
        ];

        $descriptions = [
            1 => 'Understanding of role and standards',
            2 => 'Accuracy and attention to detail',
            3 => 'Customer interaction quality',
            4 => 'Team cooperation',
            5 => 'Reliability and punctuality'
        ];

        foreach ($scores as $num => $score) {
            $db->query(
                "INSERT INTO performance_criteria_scores 
                 (evaluation_id,criteria_number,criteria_label,criteria_description,score,comments)
                 VALUES (?,?,?,?,?,?)",
                [$evaluation_id, $num, $labels[$num] ?? '', $descriptions[$num] ?? '', $score, '']
            );
        }

        $interpretation =
            $averageScore < 2.5 ? 'Needs Improvement' :
            ($averageScore < 3.5 ? 'Meets Expectations' :
                ($averageScore < 4.5 ? 'Exceeds Expectations' : 'Outstanding'));

        $db->query(
            "UPDATE performance_evaluations 
             SET overall_score = ?, interpretation = ?, updated_at = NOW()
             WHERE id = ?",
            [$averageScore, $interpretation, $evaluation_id]
        );

    } else {
        $eval = $db->query(
            "SELECT overall_score FROM performance_evaluations WHERE id = ?",
            [$evaluation_id]
        )->fetch_one();
        $averageScore = $eval['overall_score'] ?? 3.0;
    }

    if ($averageScore >= 3.5) {

        $db->query("DELETE FROM performance_improvement_plans WHERE id = ?", [$pip_id]);

        $db->query(
            "UPDATE employees SET status = 'Probationary', updated_at = NOW() WHERE id = ?",
            [$employee_id]
        );

        // $db->query(
        //     "INSERT INTO activity_logs (user_id,action,details,created_at)
        //      VALUES (?,?,?,NOW())",
        //     [
        //         $_SESSION['user_id'] ?? null,
        //         'PIP_COMPLETED',
        //         "PIP ID: {$pip_id} completed. Employee ID: {$employee_id} returned to Probationary."
        //     ]
        // );

        $_SESSION['success'] = ['Employee improved. PIP closed successfully.'];

    } else {

        $db->query(
            "UPDATE performance_improvement_plans 
             SET improvement_areas=?,goal1=?,goal2=?,goal3=?,pip_start_date=?,pip_end_date=?
             WHERE id=?",
            [$improvement_areas, $goal1, $goal2, $goal3, $pip_start_date, $pip_end_date, $pip_id]
        );

        $_SESSION['success'] = ['Performance Improvement Plan updated successfully'];
    }

    $db->commit();

} catch (Exception $e) {

    if ($db->inTransaction()) {
        $db->rollBack();
    }

    error_log("PIP Update Error: " . $e->getMessage());
    $_SESSION['error'] = [$e->getMessage()];
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();