<?php
use Core\Database;

require base_path("core/middleware/adminAuth.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $_SESSION['error'][] = 'Method not allowed';
    header('Location: /main?tab=hcm');
    exit();
}

if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {

    $_SESSION['error'][] = 'Invalid security token';
    header('Location: /main?tab=hcm');
    exit();
}

$employeeId = $_POST['employee_id'] ?? null;
$adminNotes = trim($_POST['admin_notes'] ?? '');
$noteTitle = 'Employee Profile';
$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$position = trim($_POST['position'] ?? '');
$department = trim($_POST['department'] ?? '');
$startDate = $_POST['start_date'] ?? null;
$hiredDate = $_POST['hired_date'] ?? null;
$hourlyRate = $_POST['hourly_rate'] ?? 0;
$status = $_POST['status'] ?? 'new';
$onboardingStatus = $_POST['onboarding_status'] ?? 'Onboarding';
$role = $_POST['role'] ?? 'employee';


if (!$employeeId) {
    $_SESSION['error'][] = 'Employee ID is required';
    header('Location: /main?tab=hcm');
    exit();
}

$config = require base_path('config/config.php');
$db = new Database($config['database']);

try {
    $db->beginTransaction();

    $db->query(
        "UPDATE employees 
        SET full_name = ?, email = ?, phone = ?, position = ?, department = ?, start_date = ?, hired_date = ?, hourly_rate = ?, status = ?, onboarding_status = ?, role = ?
        WHERE id = ?",
        [
            $fullName,
            $email,
            $phone,
            $position,
            $department,
            $startDate,
            $hiredDate,
            $hourlyRate,
            $status,
            $onboardingStatus,
            $role,
            $employeeId
        ]
    );

    if ($adminNotes !== '') {
        $db->query(
            "INSERT INTO admin_notes 
            (employee_id, note_title, note_content, note_type, created_by)
            VALUES (?, ?, ?, 'general', ?)",
            [
                $employeeId,
                $noteTitle,
                $adminNotes,
                $_SESSION['user_id'] ?? null
            ]
        );
    }

    $db->commit();

    $_SESSION['success'][] = 'Updated successfully';
    header('Location: /main?tab=hcm');
    exit();

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    $_SESSION['error'][] = 'Something went wrong';
    error_log("Admin note error: " . $e->getMessage());

    header('Location: /main?tab=hcm');
    exit();
}