<?php

use Core\Database;

require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /main?tab=competency');
    exit();
}

if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    $_SESSION['error'][] = "Invalid CSRF token.";
    header('Location: /login');
    exit();
}

$employee_id = (int) ($_POST['assigned_to'] ?? 0);
$competency_id = (int) ($_POST['competency_id'] ?? 0);
$assessor_id = (int) ($_POST['assessor_id'] ?? 0);
$assessment_date = $_POST['assessment_date'] ?? '';
$proficiency_level = (int) ($_POST['proficiency_level'] ?? 0);
$assessment_notes = trim($_POST['assessment_notes'] ?? '');

if (
    !$employee_id ||
    !$competency_id ||
    !$assessor_id ||
    !$assessment_date ||
    !$proficiency_level ||
    $assessment_notes === ''
) {
    $_SESSION['error'][] = "All fields are required.";
    header('Location: /main?tab=competency');
    exit();
}

if ($proficiency_level < 1 || $proficiency_level > 5) {
    $_SESSION['error'][] = "Invalid proficiency level.";
    header('Location: /main?tab=competency');
    exit();
}

$competency = $db->query(
    "SELECT required_level FROM competencies WHERE id = ?",
    [$competency_id]
)->fetch_one();

if (!$competency) {
    $_SESSION['error'][] = "Invalid competency selected.";
    header('Location: /main?tab=competency');
    exit();
}

$required_level = (int) $competency['required_level'];
$status = $proficiency_level >= $required_level ? 'Passed' : 'Needs Improvement';

$existing = $db->query(
    "SELECT id FROM competency_assessments 
    WHERE employee_id = ? 
    AND competency_id = ? 
    AND assessment_date = ?",
    [$employee_id, $competency_id, $assessment_date]
)->fetch_one();

if ($existing) {
    $_SESSION['error'][] = "Assessment already exists for this date.";
    header('Location: /main?tab=competency');
    exit();
}
try {
    // Start transaction
    $db->beginTransaction();

    // Insert assessment
    $db->query(
        "INSERT INTO competency_assessments 
            (employee_id, competency_id, assessor_id, proficiency_level, assessment_notes, assessment_date, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)",
        [
            $employee_id,
            $competency_id,
            $assessor_id,
            $proficiency_level,
            $assessment_notes,
            $assessment_date,
            $status
        ]
    );

    // Get latest assessment
    $latestAssessment = $db->query(
        "SELECT status FROM competency_assessments 
        WHERE employee_id = ? 
        ORDER BY assessment_date DESC, id DESC 
        LIMIT 1",
        [$employee_id]
    )->fetch_one();

    if ($latestAssessment) {
        $newStatus = $latestAssessment['status'] === 'Needs Improvement' ? 'failed' : 'completed';

        // Update training_schedule
        $db->query(
            "UPDATE training_schedule 
            SET assessment_status = ? 
            WHERE employee_id = ? 
            AND competency_id = ?",
            [$newStatus, $employee_id, $competency_id]
        );
    }

    $db->commit();
    $_SESSION['success'][] = "Competency assessment submitted successfully.";
    header('Location: /main?tab=competency');
    exit();

} catch (\Exception $e) {
    $db->rollback();
    $_SESSION['error'][] = "Failed to submit assessment: " . $e->getMessage();
    header('Location: /main?tab=competency');
    exit();
}
