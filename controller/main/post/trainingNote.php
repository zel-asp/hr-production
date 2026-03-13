<?php
use Core\Database;

// Initialize session arrays
$_SESSION['error'] ??= [];
$_SESSION['success'] ??= [];

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'][] = 'Invalid request method';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// ============================================
// HANDLE SEND NOTIFICATION
// ============================================
if (isset($_POST['send_notification'])) {
    try {
        $employee_id = $_POST['employee_id'];
        $title = $_POST['title'];
        $message = $_POST['message'];
        $type = $_POST['type'] ?? 'general';

        $db->query("
            INSERT INTO admin_notes (employee_id, note_title, note_content, note_type, status) 
            VALUES (?, ?, ?, ?, 'active')
        ", [$employee_id, $title, $message, $type]);

        $_SESSION['success'] = "Notification sent successfully!";
        header("Location: /main?tab=training");
        exit();

    } catch (\Throwable $th) {
        error_log("Error: " . $th->getMessage());
        $_SESSION['error'] = "Failed to send notification.";
        header("Location: /main?tab=training");
        exit();
    }
}