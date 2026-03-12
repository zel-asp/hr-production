<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login?type=hr');
    exit();
}

// Add Turnstile validation function at the top
function validateTurnstile($token)
{
    $secretKey = '0x4AAAAAACp0bIlf_1ZdwAgGfG5czc9ZDUs';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret' => $secretKey,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        error_log("cURL Error: " . $curlError);
        return false;
    }

    if ($httpCode !== 200) {
        error_log("Turnstile API error: HTTP $httpCode");
        return false;
    }

    $result = json_decode($response, true);
    return $result['success'] === true;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = "Invalid security token.";
    header('Location: /login?type=hr');
    exit();
}

// Validate Turnstile FIRST (before processing credentials)
$turnstileToken = $_POST['cf-turnstile-response'] ?? '';
if (empty($turnstileToken)) {
    $_SESSION['error'][] = "Please complete the CAPTCHA verification.";
    header('Location: /login?type=hr');
    exit();
}

if (!validateTurnstile($turnstileToken)) {
    $_SESSION['error'][] = "CAPTCHA verification failed. Please try again.";
    header('Location: /login?type=hr');
    exit();
}

// Get form data (FIX: Use correct field names)
$email = trim($_POST['hr_email'] ?? '');
$password = trim($_POST['hr_password'] ?? '');

if (empty($email) || empty($password)) {
    $_SESSION['error'][] = "Email and password are required.";
    header('Location: /login?type=hr');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'][] = "Invalid email format.";
    header('Location: /login?type=hr');
    exit();
}

// Find admin account only
$account = $db->query("
    SELECT 
        ea.id as account_id,
        ea.employee_id,
        ea.email,
        ea.password,
        ea.department,
        ea.account_status,
        ea.session_token,
        ea.applicant_id,
        e.id as employee_record_id,
        e.full_name,
        e.role,
        e.position,
        e.employee_number
    FROM employee_accounts ea
    JOIN employees e ON ea.employee_id = e.employee_number
    WHERE ea.email = ? AND e.role = 'admin'
    LIMIT 1
", [$email])->fetch_one();

if (!$account) {
    $_SESSION['error'][] = "Invalid credentials or not an admin.";
    header('Location: /login?type=hr');
    exit();
}

// Check account status
if ($account['account_status'] !== 'Active') {
    $_SESSION['error'][] = "Account is not active.";
    header('Location: /login?type=hr');
    exit();
}

// Verify password
if (!password_verify($password, $account['password'])) {
    $_SESSION['error'][] = "Invalid credentials.";
    header('Location: /login?type=hr');
    exit();
}

session_regenerate_id(true);

// Generate new session token
$session_token = bin2hex(random_bytes(32));
$hashedToken = hash('sha256', $session_token);

// Update last login and session token
$db->query(
    "UPDATE employee_accounts SET last_login = NOW(), session_token = ? WHERE id = ?",
    [$hashedToken, $account['account_id']]
);

// Set admin session data
$_SESSION['admin'] = [
    'account_id' => $account['account_id'],
    'applicant_id' => $account['applicant_id'],
    'employee_record_id' => $account['employee_record_id'],
    'employee_number' => $account['employee_number'],
    'full_name' => $account['full_name'],
    'email' => $account['email'],
    'role' => $account['role'],
    'position' => $account['position'],
    'department' => $account['department'],
    'token' => $hashedToken,
    'logged_in' => true,
    'login_time' => time()
];

// Simple admin auth middleware
require base_path('core/middleware/adminAuth.php');

$_SESSION['success'][] = "Welcome back, Admin " . $account['full_name'] . "!";

// Redirect to admin dashboard
header('Location: /main');
exit();