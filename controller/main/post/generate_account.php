<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);

$_SESSION['success'] ??= [];
$_SESSION['error'] ??= [];

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applicant_id'])) {

    try {

        $applicantId = $_POST['applicant_id'];

        // ==========================
        // 1️⃣ Get employee from DB
        // ==========================
        $employee = $db->query(
            "SELECT employee_number, full_name, email,department 
             FROM employees 
             WHERE applicant_id = ? 
             LIMIT 1",
            [$applicantId]
        )->fetch_one();

        if (!$employee) {
            $_SESSION['error'][] = 'Employee record not found.';
            header('Location: /main?tab=onboarding');
            exit;
        }

        $employeeNumber = $employee['employee_number']; // EMP-031
        $email = $employee['email'];
        $fullName = $employee['full_name'];
        $department = $employee['department'];

        // ==========================
        // 2️⃣ Generate username
        // ==========================
        $username = explode('@', $email)[0];

        // ==========================
        // 3️⃣ Use employee number as password
        // ==========================
        $plainPassword = $employeeNumber; // EMP-031
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // ==========================
        // 4️⃣ Check if account exists
        // ==========================
        $existing = $db->query(
            "SELECT id FROM employee_accounts WHERE applicant_id = ?",
            [$applicantId]
        )->fetch_one();

        if ($existing) {
            $_SESSION['error'][] = 'An account already exists for this employee.';
            header('Location: /main?tab=onboarding&modal=generateAccountModal');
            exit;
        }

        // ==========================
        // 5️⃣ Insert account
        // ==========================
        $db->query(
            "INSERT INTO employee_accounts 
             (applicant_id, employee_id, username, password, email,department) 
             VALUES (?, ?, ?, ?, ?,?)",
            [$applicantId, $employeeNumber, $username, $hashedPassword, $email, $department]
        );

        // ==========================
        // 6️⃣ Success message
        // ==========================
        $_SESSION['success'][] = 'Employee account generated successfully.';
        $_SESSION['success'][] = "Email: $email";
        $_SESSION['success'][] = "Temporary Password: $plainPassword";

        header('Location: /main?tab=onboarding');
        exit;

    } catch (Exception $e) {

        echo "<pre>";
        echo "Error Message: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
        echo "</pre>";
        exit;
    }
}