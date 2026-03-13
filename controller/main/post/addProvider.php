<?php


use Core\Database;

require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// ============================================
// HANDLE ADD BENEFIT PROVIDER
// ============================================
if (isset($_POST['add_provider'])) {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'][] = "Invalid security token.";
        header('Location: /main?tab=hmo');
        exit();
    }

    try {
        $provider_name = trim($_POST['provider_name']);
        $contact_info = !empty($_POST['contact_info']) ? trim($_POST['contact_info']) : null;
        $notes = !empty($_POST['notes']) ? trim($_POST['notes']) : null;

        // Validate provider name
        if (empty($provider_name)) {
            $_SESSION['error'][] = "Provider name is required.";
            header('Location: /main?tab=hmo');
            exit();
        }

        $db->query("
            INSERT INTO benefit_providers (provider_name, contact_info, notes) 
            VALUES (?, ?, ?)
        ", [$provider_name, $contact_info, $notes]);

        // Set success message in session
        $_SESSION['success'][] = "Benefit provider '{$provider_name}' added successfully!";

        // Redirect back to HMO tab
        header("Location: /main?tab=hmo");
        exit();

    } catch (\Throwable $th) {
        error_log("Error adding provider: " . $th->getMessage());

        // Check for duplicate provider name
        if (strpos($th->getMessage(), 'Duplicate entry') !== false) {
            $_SESSION['error'][] = "A provider with the name '{$provider_name}' already exists.";
        } else {
            $_SESSION['error'][] = "Failed to add provider. Please try again.";
        }

        header("Location: /main?tab=hmo");
        exit();
    }
}