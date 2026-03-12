<?php
use Core\Database;

require base_path('core/middleware/employeeAuth.php');

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Start session messages
$_SESSION['error'] ??= [];
$_SESSION['success'] ??= [];

// Only allow post
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'][] = "Invalid request method.";
    header('Location: /?tab=profile');
    exit();
}

// CSRF check
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    $_SESSION['error'][] = "Invalid CSRF token. Please try again.";
    header('Location: /login');
    exit();
}

// Verify this is a PATCH request via method spoofing
if ($_POST['__method'] !== 'PATCH') {
    $_SESSION['error'][] = 'Invalid request method';
    header('Location: /?tab=profile');
    exit();
}

// Get data from form
$employee_number = $_POST['employee_number'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';


// Validate required fields
if (empty($employee_number) || empty($full_name) || empty($email)) {
    $_SESSION['error'][] = 'Missing required fields';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/?tab=profile'));
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'][] = 'Invalid email format';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/?tab=profile'));
    exit();
}

try {
    // Start transaction
    $db->beginTransaction();

    // Get current employee info using employee_number
    $employee = $db->query(
        "SELECT id, employee_number, full_name, email, phone 
         FROM employees 
         WHERE employee_number = :employee_number",
        ['employee_number' => $employee_number]
    )->find();

    if (!$employee) {
        throw new Exception('Employee not found');
    }

    // Check if email is already taken by another employee
    $existingEmail = $db->query(
        "SELECT id FROM employees WHERE email = :email AND employee_number != :employee_number",
        [
            'email' => $email,
            'employee_number' => $employee_number
        ]
    )->find();

    if ($existingEmail) {
        throw new Exception('Email is already in use by another employee');
    }

    // Update employee information
    $db->query(
        "UPDATE employees 
        SET full_name = :full_name,
            email = :email,
            phone = :phone,
            updated_at = NOW()
        WHERE employee_number = :employee_number",
        [
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'employee_number' => $employee_number
        ]
    );

    // Check if employee_accounts record exists
    $userAccount = $db->query(
        "SELECT id, password FROM employee_accounts WHERE employee_id = :employee_number",
        ['employee_number' => $employee_number]
    )->find();

    if ($userAccount) {
        // Update email in employee_accounts
        $db->query(
            "UPDATE employee_accounts 
            SET email = :email
            WHERE employee_id = :employee_number",
            [
                'email' => $email,
                'employee_number' => $employee_number
            ]
        );
    }

    // Handle password change if any password field is filled
    if (!empty($new_password) || !empty($confirm_password)) {

        // Check if user account exists for password change
        if (!$userAccount) {
            throw new Exception('User account not found for password change');
        }


        // Check if new password matches confirm password
        if ($new_password !== $confirm_password) {
            throw new Exception('New password and confirm password do not match');
        }

        // Validate password strength
        if (strlen($new_password) < 8) {
            throw new Exception('Password must be at least 8 characters long');
        }

        if (!preg_match('/[A-Z]/', $new_password)) {
            throw new Exception('Password must contain at least one uppercase letter');
        }

        if (!preg_match('/[a-z]/', $new_password)) {
            throw new Exception('Password must contain at least one lowercase letter');
        }

        if (!preg_match('/[0-9]/', $new_password)) {
            throw new Exception('Password must contain at least one number');
        }

        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in employee_accounts
        $db->query(
            "UPDATE employee_accounts 
             SET password = :password
             WHERE employee_id = :employee_number",
            [
                'password' => $hashed_password,
                'employee_number' => $employee_number
            ]
        );

        $_SESSION['success'][] = 'Password changed successfully';
    }

    // Update session with new information
    $_SESSION['employee']['full_name'] = $full_name;
    $_SESSION['employee']['email'] = $email;

    // Commit transaction
    $db->commit();

    $_SESSION['success'][] = 'Profile updated successfully';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/?tab=profile'));
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollBack();

    $_SESSION['error'][] = 'Error updating profile: ' . $e->getMessage();
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/?tab=profile'));
    exit();
}