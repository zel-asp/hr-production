<?php
use Core\Database;

require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: /main?tab=timesheet');
    exit();
}

// Verify this is a PATCH request via method spoofing
if ($_POST['__method'] !== 'PATCH') {
    $_SESSION['error'][] = 'Invalid request method';
    header('Location: /main?tab=timesheet');
    exit();
}

// Get form data
$claim_id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$claim_id || !$action) {
    $_SESSION['error'][] = 'Missing required fields';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/main?tab=timesheet'));
    exit();
}

// Determine new status based on action
$new_status = ($action === 'approve') ? 'Waiting' : 'Rejected';

try {
    // Update the claim status
    $db->query(
        "UPDATE expense_claims 
         SET status = :status,
             approved_at = CASE WHEN :status = 'Waiting' THEN NOW() ELSE NULL END,
             approved_by = :user_id
         WHERE id = :id",
        [
            'status' => $new_status,
            'user_id' => $_SESSION['user_id'] ?? null,
            'id' => $claim_id
        ]
    );

    $_SESSION['success'][] = "Claim #{$claim_id} has been {$new_status} successfully.";

} catch (Exception $e) {
    $_SESSION['error'][] = 'Error updating claim: ' . $e->getMessage();
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/main?tab=timesheet'));
exit();