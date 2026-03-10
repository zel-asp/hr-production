<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);

if (!isset($_SESSION['employee'])) {
    header('Location: /login');
    $_SESSION['error'][] = "You are unathorized, login first";
    exit();
}

$userId = $_SESSION['employee']['account_id'] ?? null;
$token = $_SESSION['employee']['token'] ?? null;
$role = $_SESSION['employee']['role'] ?? null;

if (!$userId || !$token || !$role) {
    unset($_SESSION['employee']);
    header('Location: /login');
    $_SESSION['error'][] = "You are unauthorized";
    exit();
}


$stmt = $db->query('SELECT session_token FROM employee_accounts WHERE id = ?', [$userId]);
$dbUser = $stmt->fetch_one();

if (!$dbUser || $dbUser['session_token'] !== $token) {

    $_SESSION['error'][] = "Session expired. Please login again.";

    unset($_SESSION['employee']);
    header('Location: /login');
    exit();
}

$employeeId = $_SESSION['employee']['employee_record_id'] ?? null;
if (!$employeeId) {
    $_SESSION['error'][] = "Employee record not found. Please contact HR.";
    header('Location: /login');
    exit();
}