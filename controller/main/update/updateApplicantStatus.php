<?php
use Core\Database;

header('Content-Type: application/json');

$config = require base_path('config/config.php');
$db = new Database($config['database']);

try {


    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input)
        throw new Exception("Invalid JSON");

    $id = (int) ($input['id'] ?? 0);
    $status = ucfirst(strtolower(trim($input['status'] ?? '')));
    $startDate = $input['start_date'] ?? null;
    $csrf = $input['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
        throw new Exception("Invalid CSRF token");
    }

    $validStatuses = ['New', 'Review', 'Interview', 'Rejected', 'Hired'];
    if (!in_array($status, $validStatuses))
        throw new Exception("Invalid status");

    $current = $db->query(
        "SELECT * FROM applicants WHERE id = :id",
        ['id' => $id]
    )->find();

    if (!$current)
        throw new Exception("Applicant not found");

    $applicant = $current[0];

    if ($status === 'Hired') {
        // Validate start date is provided
        if (empty($startDate)) {
            throw new Exception("Start date is required for hired applicants");
        }

        $db->query("
            UPDATE applicants 
            SET status = :status,
                hired_date = IF(hired_date IS NULL, NOW(), hired_date),
                start_date = :start_date
            WHERE id = :id
        ", [
            'status' => $status,
            'start_date' => $startDate,
            'id' => $id
        ]);

        // Check if already exists in employees
        $exists = $db->query(
            "SELECT id FROM employees WHERE applicant_id = :id",
            ['id' => $id]
        )->find();

        if (!$exists) {
            // Insert into employees including phone, department, and start date
            $employeeNumber = 'EMP-' . str_pad($id, 3, '0', STR_PAD_LEFT);
            $db->query("
                INSERT INTO employees 
                    (applicant_id, employee_number, full_name, email, phone, position, department, start_date, hired_date, status, age, gender, resume) 
                VALUES 
                    (:applicant_id, :employee_number, :full_name, :email, :phone, :position, :department, :start_date, :hired_date, 'Probationary', :age, :gender, :resume)
            ", [
                'applicant_id' => $id,
                'employee_number' => $employeeNumber,
                'full_name' => $applicant['full_name'],
                'email' => $applicant['email'],
                'phone' => $applicant['phone'] ?? null,
                'position' => $applicant['position'],
                'department' => $applicant['department'] ?? null,
                'start_date' => $startDate,
                'hired_date' => $applicant['hired_date'] ?? date('Y-m-d'),
                'age' => $applicant['age'],
                'gender' => $applicant['gender'],
                'resume' => $applicant['resume_path']
            ]);
        }

        $_SESSION['success'][] = "Applicant successfully marked as Hired with start date {$startDate}.";
        $message = "Applicant successfully marked as Hired with start date {$startDate}.";

    } else {
        // Update applicant status and reset hired_date and start_date
        $db->query("
            UPDATE applicants 
            SET status = :status,
                hired_date = NULL,
                start_date = NULL
            WHERE id = :id
        ", ['status' => $status, 'id' => $id]);

        $db->query("DELETE FROM employees WHERE applicant_id = :id", ['id' => $id]);

        $_SESSION['success'][] = "Applicant status updated to {$status}.";
        $message = "Applicant status updated to {$status}.";
    }

    echo json_encode([
        'success' => true,
        'message' => $message
    ]);

} catch (Throwable $e) {
    $_SESSION['error'][] = $e->getMessage();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}