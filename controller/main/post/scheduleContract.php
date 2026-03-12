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

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Get form data
$applicantId = isset($_POST['applicant_id']) ? (int) $_POST['applicant_id'] : 0;
$contractDate = $_POST['contract_date'] ?? '';
$contractTime = $_POST['contract_time'] ?? null;
$contractLocation = $_POST['contract_location'] ?? 'HR Office';
$name = $_POST['employee_name'] ?? '';
$position = $_POST['position'] ?? '';
$contractNotes = $_POST['contract_notes'] ?? '';
$ratePerHour = isset($_POST['hourly_rate']) ? floatval($_POST['hourly_rate']) : 0;

// Validate required fields
if (!$applicantId || empty($contractDate) || empty($contractLocation)) {
    $_SESSION['error'][] = 'Missing required fields';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Validate date is not in the past
if (strtotime($contractDate) < strtotime(date('Y-m-d'))) {
    $_SESSION['error'][] = 'Contract date cannot be in the past';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

try {
    $db->beginTransaction();

    // Get applicant details for reference
    $applicant = $db->query("
        SELECT full_name, position, email 
        FROM applicants 
        WHERE id = ?
    ", [$applicantId])->fetch_one();

    if (!$applicant) {
        throw new Exception('Applicant not found');
    }

    // Update hourly rate if provided
    if ($ratePerHour > 0) {
        $db->query("
            UPDATE applicants 
            SET rate_per_hour = ? 
            WHERE id = ?
        ", [$ratePerHour, $applicantId]);
    }

    // Check if a contract already exists for this applicant
    $existingContract = $db->query("
        SELECT id FROM schedule_contract 
        WHERE applicant_id = ?
    ", [$applicantId])->fetch_one();

    if ($existingContract) {
        // Update existing contract
        $db->query("
            UPDATE schedule_contract 
            SET contract_date = ?,
                contract_time = ?,
                contract_location = ?,
                contract_notes = ?,
                hourly_rate = ?,
                employee_name = ?,
                position = ?
            WHERE applicant_id = ?
        ", [
            $contractDate,
            $contractTime,
            $contractLocation,
            $contractNotes,
            $ratePerHour,
            $name,
            $position,
            $applicantId
        ]);
    } else {
        // Insert new contract
        $db->query("
            INSERT INTO schedule_contract 
            (applicant_id, contract_date, contract_time, contract_location, contract_notes, 
            hourly_rate, employee_name, position)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $applicantId,
            $contractDate,
            $contractTime,
            $contractLocation,
            $contractNotes,
            $ratePerHour,
            $name,
            $position
        ]);
    }

    // Update applicant's contract signing date
    $db->query('UPDATE applicants SET contract_signing_date = ? WHERE id = ?', [$contractDate, $applicantId]);

    $db->commit();

    // Format success message with date
    $formattedDate = date('F j, Y', strtotime($contractDate));
    $formattedTime = !empty($contractTime) ? date('g:i A', strtotime($contractTime)) : 'to be determined';

    // FIX: Define the message variable
    $message = "Contract scheduled successfully for " . ($name ?: $applicant['full_name']);

    $_SESSION['success'][] = $message . " on {$formattedDate} at {$formattedTime}";

    // Optional: Send email notification to employee
    // sendContractNotification($applicant['email'], $applicant['full_name'], $contractDate, $contractTime, $contractLocation, $contractNotes);

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'][] = 'Error scheduling contract: ' . $e->getMessage();
    error_log("Error scheduling contract: " . $e->getMessage());
}

// Redirect back
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit();