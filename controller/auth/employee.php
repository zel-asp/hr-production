<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    $_SESSION['error'][] = "email and employee id are required.";
    header('Location: /login');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'][] = "invalid email format.";
    header('Location: /login');
    exit();
}

$account = $db->query(
    "SELECT id, employee_id, email, password, department, account_status, session_token, applicant_id
    FROM employee_accounts WHERE email = ? LIMIT 1",
    [$email]
)->fetch_one();

if (!$account) {
    $_SESSION['error'][] = "invalid credentials.";
    header('Location: /login');
    exit();
}

$applicantId = $account['applicant_id'];

$employee = $db->query(
    "SELECT id FROM employees WHERE applicant_id = ?",
    [$applicantId]
)->fetch_one();

$employeeId = null;
if ($employee && isset($employee['id'])) {
    $employeeId = $employee['id'];
}

if ($account['account_status'] !== 'Active') {
    $_SESSION['error'][] = "account is not active.";
    header('Location: /login');
    exit();
}

if (!password_verify($password, $account['password'])) {
    $_SESSION['error'][] = "password doesn't match";
    header('Location: /login');
    exit();
}

session_regenerate_id(true);

$session_token = bin2hex(random_bytes(32));
$hashedToken = hash('sha256', $session_token);

$db->query(
    "UPDATE employee_accounts SET last_login = NOW(), session_token = ? WHERE id = ?",
    [$hashedToken, $account['id']]
);

$_SESSION['employee'] = [
    'role' => 'employee',
    'account_id' => $account['id'],
    'applicant_id' => $account['applicant_id'],
    'employee_record_id' => $employeeId,
    'token' => $hashedToken,
    'employee_id' => $account['employee_id'],
    'email' => $account['email'],
    'department' => $account['department']
];

require base_path('core/middleware/employeeAuth.php');

$_SESSION['success'][] = "login successful!";
header('Location: /');

exit();