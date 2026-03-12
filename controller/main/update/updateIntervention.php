<?php
use Core\Database;

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'][] = 'Invalid request method';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// CSRF validation
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Check for method spoofing
$method = $_POST['__method'] ?? 'POST';
if ($method !== 'PATCH') {
    $_SESSION['error'][] = 'Invalid method';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Get form data
$interventionId = isset($_POST['intervention_id']) ? (int) $_POST['intervention_id'] : 0;
$interventionTitle = $_POST['intervention_title'] ?? '';
$newLevel = isset($_POST['new_level']) ? (int) $_POST['new_level'] : 0;
$dueDate = $_POST['due_date'] ?? null;
$notes = $_POST['notes'] ?? '';

if (!$interventionId || !$interventionTitle || !$newLevel) {
    $_SESSION['error'][] = 'Missing required fields';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Validate new level is between 1 and 5
if ($newLevel < 1 || $newLevel > 5) {
    $_SESSION['error'][] = 'New level must be between 1 and 5';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

try {
    $db->beginTransaction();

    // Get the current intervention with its assessment ID and competency details
    $intervention = $db->query("
        SELECT 
            i.*, 
            i.competency_assessment_id, 
            i.employee_id, 
            i.employee_name,
            i.competency_name, 
            i.current_level,
            ca.competency_id,
            ca.status as assessment_status,
            ca.proficiency_level as current_assessment_level,
            c.name as competency_name_from_db,
            c.required_level
        FROM intervention_assignments i
        LEFT JOIN competency_assessments ca ON i.competency_assessment_id = ca.id
        LEFT JOIN competencies c ON ca.competency_id = c.id
        WHERE i.id = ?
    ", [$interventionId])->fetch_one();

    if (!$intervention) {
        throw new Exception('Intervention not found');
    }

    // Always set status to completed when updating
    $newStatus = 'completed';
    $completionDate = date('Y-m-d');

    // Update the current intervention as completed
    $db->query("
        UPDATE intervention_assignments 
        SET intervention_title = ?,
            new_level = ?,
            due_date = ?,
            notes = ?,
            status = ?,
            completion_date = ?
        WHERE id = ?
    ", [
        $interventionTitle,
        $newLevel,
        $dueDate,
        $notes,
        $newStatus,
        $completionDate,
        $interventionId
    ]);

    // If this intervention is linked to a competency assessment
    if (!empty($intervention['competency_assessment_id'])) {
        $assessmentId = $intervention['competency_assessment_id'];

        // Get the required level (default to 3 if not found)
        $requiredLevel = $intervention['required_level'] ?? 3;

        // Check if new level meets or exceeds required level
        if ($newLevel >= $requiredLevel) {
            // Update competency assessment as Passed with the new level
            $db->query("
                UPDATE competency_assessments 
                SET proficiency_level = ?,
                    status = 'Passed',
                    assessment_date = CURDATE()
                WHERE id = ?
            ", [
                $newLevel,
                $assessmentId
            ]);

            // Update the notified flag
            $db->query("
                UPDATE competency_assessments 
                SET notified = 1 
                WHERE id = ?
            ", [$assessmentId]);

            $_SESSION['success'][] = "Intervention completed. Employee has PASSED the competency assessment with level {$newLevel} (required: {$requiredLevel}).";

        } else {
            // Update competency assessment with new level but keep status as Needs Improvement
            $db->query("
                UPDATE competency_assessments 
                SET proficiency_level = ?,
                    status = 'Needs Improvement'
                WHERE id = ?
            ", [
                $newLevel,
                $assessmentId
            ]);

            $_SESSION['success'][] = "Intervention completed. Employee still NEEDS IMPROVEMENT (level {$newLevel} below required {$requiredLevel}).";
        }
    } else {
        $_SESSION['success'][] = 'Intervention completed successfully (not linked to any assessment)';
    }

    $db->commit();

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'][] = 'Error updating intervention: ' . $e->getMessage();
    error_log("Error updating intervention: " . $e->getMessage());
}

// Redirect back
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit();