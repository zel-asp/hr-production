<?php
use Core\Database;

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'][] = 'Invalid request method';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Get form data
$assessmentId = isset($_POST['assessment_id']) ? (int) $_POST['assessment_id'] : 0;
$employeeId = isset($_POST['employee_id']) ? (int) $_POST['employee_id'] : 0;
$employeeName = $_POST['employee_name'] ?? '';
$competencyName = $_POST['competency_name'] ?? '';
$currentLevel = isset($_POST['current_level']) ? (int) $_POST['current_level'] : 0;
$requiredLevel = isset($_POST['required_level']) ? (int) $_POST['required_level'] : 0;
$selectedInterventions = isset($_POST['interventions']) ? $_POST['interventions'] : [];

// Validate required fields
if (!$assessmentId || !$employeeId || empty($employeeName) || empty($competencyName) || empty($selectedInterventions)) {
    $_SESSION['error'][] = 'Missing required information';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

try {
    $db->beginTransaction();

    $successCount = 0;
    $interventionTitles = [
        'excellence' => $competencyName . ' Excellence Training',
        'workshop' => 'Professional Skills Workshop',
        'mentoring' => 'One-on-One Mentoring Program',
        'elearning' => 'Self-paced eLearning Module',
        'certification' => 'Professional Certification Program'
    ];

    $interventionTypes = [
        'excellence' => 'Online course',
        'workshop' => 'In-person session',
        'mentoring' => 'Mentoring',
        'elearning' => 'Online course',
        'certification' => 'Certification'
    ];

    $interventionDurations = [
        'excellence' => '4 hours',
        'workshop' => '1 day',
        'mentoring' => '3 months',
        'elearning' => '2 hours',
        'certification' => '3 months'
    ];

    $interventionBadges = [
        'excellence' => 'Recommended',
        'workshop' => 'Optional',
        'mentoring' => 'Intensive',
        'elearning' => 'Flexible',
        'certification' => 'Advanced'
    ];

    foreach ($selectedInterventions as $intervention) {
        // Check if already assigned to prevent duplicates
        $existing = $db->query("
            SELECT id FROM intervention_assignments 
            WHERE employee_id = ? 
            AND competency_name = ? 
            AND intervention_title = ? 
            AND status IN ('pending', 'in_progress')
        ", [
            $employeeId,
            $competencyName,
            $interventionTitles[$intervention] ?? $intervention
        ])->find();

        if ($existing) {
            continue; // Skip if already assigned and pending/in progress
        }

        // Calculate due date (30 days from now)
        $dueDate = date('Y-m-d', strtotime('+30 days'));

        // Insert intervention assignment
        $db->query("
            INSERT INTO intervention_assignments (
                employee_id,
                competency_assessment_id,
                employee_name,
                competency_name,
                current_level,
                required_level,
                intervention_title,
                intervention_type,
                duration,
                badge_text,
                assigned_date,
                due_date,
                status,
                notes,
                created_at,
                updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $employeeId,
            $assessmentId,
            $employeeName,
            $competencyName,
            $currentLevel,
            $requiredLevel,
            $interventionTitles[$intervention] ?? $intervention,
            $interventionTypes[$intervention] ?? 'Training',
            $interventionDurations[$intervention] ?? 'TBD',
            $interventionBadges[$intervention] ?? 'Standard',
            date('Y-m-d'), // assigned_date = today
            $dueDate,
            'pending',
            "Assigned based on competency gap assessment #{$assessmentId}"
        ]);

        $successCount++;
    }

    if ($successCount > 0) {
        $_SESSION['success'][] = "Successfully assigned {$successCount} intervention(s) to {$employeeName} for {$competencyName}.";
    } else {
        $_SESSION['info'][] = "No new interventions were assigned. They may already be assigned.";
    }

    $db->commit();

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'][] = 'Error assigning interventions: ' . $e->getMessage();
    error_log("Error assigning interventions: " . $e->getMessage());
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit();