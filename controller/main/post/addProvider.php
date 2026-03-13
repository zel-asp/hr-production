<?php

use Core\Database;

require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// ============================================
// HANDLE ADD BENEFIT PROVIDER
// ============================================
if (isset($_POST['add_provider'])) {
    // CSRF check - move this inside the POST handler
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'][] = "Invalid security token.";
        header('Location: /main?tab=hmo&error=invalid_token');
        exit();
    }

    try {
        $provider_name = $_POST['provider_name'];
        $contact_info = $_POST['contact_info'] ?? null;
        $notes = $_POST['notes'] ?? null;

        // Remove default coverage/premium fields since they're not in your modal
        // (You removed them from the modal, so remove them from handler too)

        $db->query("
            INSERT INTO benefit_providers (provider_name, contact_info, notes) 
            VALUES (?, ?, ?)
        ", [$provider_name, $contact_info, $notes]);

        // Redirect with success message
        header("Location: /main?tab=hmo");
        exit();
    } catch (\Throwable $th) {
        error_log("Error adding provider: " . $th->getMessage());

        // Check for duplicate provider name
        if (strpos($th->getMessage(), 'Duplicate entry') !== false) {
            $error = "provider_exists";
        } else {
            $error = "add_failed";
        }

        header("Location: /main?tab=hmo&");
        exit();
    }
}