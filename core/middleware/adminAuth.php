<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Check if admin session exists
if (!isset($_SESSION['admin'])) {
    $_SESSION['error'][] = "You are unauthorized, login first";
    header('Location: /login?type=hr');
    exit();
}

// Get admin session data
$accountId = $_SESSION['admin']['account_id'] ?? null;
$token = $_SESSION['admin']['token'] ?? null;
$role = $_SESSION['admin']['role'] ?? null;

// Validate session data exists
if (!$accountId || !$token || !$role) {
    unset($_SESSION['admin']);
    $_SESSION['error'][] = "You are unauthorized";
    header('Location: /login?type=hr');
    exit();
}

// Verify role is admin
if ($role !== 'admin') {
    unset($_SESSION['admin']);
    $_SESSION['error'][] = "Admin access required";
    header('Location: /login?type=hr');
    exit();
}

// Verify session token matches database
$stmt = $db->query('SELECT session_token FROM employee_accounts WHERE id = ?', [$accountId]);
$dbUser = $stmt->fetch_one();

if (!$dbUser || $dbUser['session_token'] !== $token) {
    $_SESSION['error'][] = "Session expired. Please login again.";
    unset($_SESSION['admin']);
    header('Location: /login?type=hr');
    exit();
}

// Verify employee record exists
$employeeId = $_SESSION['admin']['employee_record_id'] ?? null;
if (!$employeeId) {
    $_SESSION['error'][] = "Employee record not found. Please contact HR.";
    unset($_SESSION['admin']);
    header('Location: /login?type=hr');
    exit();
}

// Refresh login time on activity
$_SESSION['admin']['login_time'] = time();
