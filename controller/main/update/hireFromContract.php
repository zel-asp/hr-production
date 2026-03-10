<?php
// hire-applicant.php
use Core\Database;

require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: /main?tab=applicant#contract');
    exit();
}

try {
    $contract_id = filter_input(INPUT_POST, 'contract_id', FILTER_VALIDATE_INT);

    if (!$contract_id) {
        throw new Exception('Invalid contract ID');
    }

    // Get contract details with applicant data
    $contract = $db->query(
        "SELECT sc.*, a.id as applicant_id, a.email, a.phone, a.department, a.age, a.gender, a.resume_path, a.full_name 
         FROM schedule_contract sc
         JOIN applicants a ON a.id = sc.applicant_id
         WHERE sc.id = :id",
        ['id' => $contract_id]
    )->find();



    if (!$contract) {
        throw new Exception('Contract not found');
    }

    $contract = $contract[0]; // Get the first result

    // Update contract status to 'Ended'
    $db->query(
        "UPDATE schedule_contract SET status = 'Ended' WHERE id = :id",
        ['id' => $contract_id]
    );



    // Update applicant status to 'Hired' and set hired_date
    $db->query(
        "UPDATE applicants 
         SET status = 'Hired', 
             hired_date = CURDATE(),
             start_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY),
             rate_per_hour = :hourly_rate
         WHERE id = :applicant_id",
        [
            'applicant_id' => $contract['applicant_id'],
            'hourly_rate' => $contract['hourly_rate']
        ]
    );

    // Check if already exists in employees
    $exists = $db->query(
        "SELECT id FROM employees WHERE applicant_id = :id",
        ['id' => $contract['applicant_id']]
    )->find();

    if (!$exists) {
        // Insert into employees - using correct field names: full_name and hourly_rate
        $employeeNumber = 'EMP-' . str_pad($contract['applicant_id'], 3, '0', STR_PAD_LEFT);

        // Get the latest hired_date from the applicant
        $applicant = $db->query(
            "SELECT hired_date FROM applicants WHERE id = :id",
            ['id' => $contract['applicant_id']]
        )->find();

        $hired_date = $applicant[0]['hired_date'] ?? date('Y-m-d');

        $db->query("
            INSERT INTO employees 
                (applicant_id, employee_number, full_name, email, phone, position, 
                 department, start_date, hired_date, hourly_rate, status, age, gender, resume) 
            VALUES 
                (:applicant_id, :employee_number, :full_name, :email, :phone, :position, 
                 :department, :start_date, :hired_date, :hourly_rate, 'Probationary', :age, :gender, :resume)
        ", [
            'applicant_id' => $contract['applicant_id'],
            'employee_number' => $employeeNumber,
            'full_name' => $contract['full_name'],
            'email' => $contract['email'],
            'phone' => $contract['phone'] ?? null,
            'position' => $contract['position'],
            'department' => $contract['department'] ?? null,
            'start_date' => date('Y-m-d', strtotime('+1 day')),
            'hired_date' => $hired_date,
            'hourly_rate' => $contract['hourly_rate'],
            'age' => $contract['age'],
            'gender' => $contract['gender'],
            'resume' => $contract['resume_path']
        ]);
    }
} catch (Exception $e) {
    $_SESSION['error'][] = $e->getMessage();
}

header('Location: /main?tab=applicant#contract');
exit();