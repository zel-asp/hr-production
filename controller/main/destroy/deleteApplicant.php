<?php
use Core\Database;

require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $csrf = $_POST['csrf_token'] ?? '';

    // CSRF check
    if (empty($csrf) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
        $_SESSION['error'][] = "Invalid CSRF token.";
        header('Location: /main?tab=applicant');
        exit;
    }

    // Validate ID
    if ($id <= 0) {
        $_SESSION['error'][] = "Invalid applicant ID.";
        header('Location: /main?tab=applicant');
        exit;
    }

    try {
        $db->query("DELETE FROM applicants WHERE id = :id LIMIT 1", ['id' => $id]);

        if ($db->count()) {
            $_SESSION['success'][] = "Applicant deleted successfully.";
        } else {
            $_SESSION['error'][] = "Applicant not found or already deleted.";
        }
    } catch (\Throwable $th) {
        $_SESSION['error'][] = "Error: " . $th->getMessage();
    }

    header('Location: /main?tab=applicant');
    exit;
}