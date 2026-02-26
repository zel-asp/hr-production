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

    $employee_id = intval($_POST['employee_id']);
    $review_start = $_POST['review_period_start'];
    $review_end = $_POST['review_period_end'];
    $review_type = $_POST['review_type'];
    $overall_score = floatval($_POST['overall_score']);
    $interpretation = $_POST['interpretation'];

    $criteria_scores = $_POST['criteria_score'];
    $criteria_comments = $_POST['criteria_comment'];

    $config = require base_path('config/config.php');
    $db = new Core\Database($config['database']);

    try {
        $db->beginTransaction();

        $db->query("
        INSERT INTO performance_evaluations
        (employee_id, review_period_start, review_period_end, review_type, overall_score, interpretation)
        VALUES (?, ?, ?, ?, ?, ?)
    ", [$employee_id, $review_start, $review_end, $review_type, $overall_score, $interpretation]);

        $evaluation_id = $db->lastInsertId();

        // Insert criteria scores
        $criteriaLabels = [
            1 => 'Job Knowledge',
            2 => 'Quality of Work',
            3 => 'Customer Service',
            4 => 'Teamwork & Collaboration',
            5 => 'Attendance & Punctuality'
        ];
        $criteriaDescriptions = [
            1 => 'Understanding of role, procedures, and standards',
            2 => 'Accuracy, thoroughness, and attention to detail',
            3 => 'Interaction with customers and handling complaints',
            4 => 'Working with colleagues and supporting team goals',
            5 => 'Reliability and adherence to schedule'
        ];

        foreach ($criteria_scores as $num => $score) {
            $comment = $criteria_comments[$num] ?? '';
            $label = $criteriaLabels[$num];
            $description = $criteriaDescriptions[$num];

            $db->query("
            INSERT INTO performance_criteria_scores
            (evaluation_id, criteria_number, criteria_label, criteria_description, score, comments)
            VALUES (?, ?, ?, ?, ?, ?)
        ", [$evaluation_id, $num, $label, $description, $score, $comment]);
        }

        // **Update evaluation_status for this employee**
        $db->query("
        UPDATE employees
        SET evaluation_status = 'Evaluated'
        WHERE id = ?
    ", [$employee_id]);

        // Commit transaction
        $db->commit();

        $_SESSION['success'] = ["Performance evaluation saved successfully!"];
        header('Location:/main?tab=performance');
        exit;

    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = ["Error saving evaluation: " . $e->getMessage()];
        header('Location:/main?tab=performance');
        exit;
    }
}