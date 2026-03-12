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

// At the top of both login controllers, add this function
function validateTurnstile($token)
{
    $secretKey = '0x4AAAAAACp0bIlf_1ZdwAgGfG5czc9ZDUs';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret' => $secretKey,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("Turnstile API error: HTTP $httpCode");
        return false;
    }

    $result = json_decode($response, true);
    return $result['success'] === true;
}

// After CSRF validation, add this:
$turnstileToken = $_POST['cf-turnstile-response'] ?? '';
if (empty($turnstileToken)) {
    $_SESSION['error'][] = "Please complete the CAPTCHA verification.";
    header('Location: /login');
    exit();
}

if (!validateTurnstile($turnstileToken)) {
    $_SESSION['error'][] = "CAPTCHA verification failed. Please try again.";
    header('Location: /login');
    exit();
}

// Rest of your login code continues here...

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