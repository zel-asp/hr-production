<?php
use Core\Database;


$config = require base_path('config/config.php');
$db = new Database($config['database']);


// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'][] = 'Invalid request method';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Get form data
$csrfToken = $_POST['csrf_token'] ?? '';
$documentType = $_POST['document_type'] ?? '';
$publicUrl = $_POST['public_url'] ?? '';

// Validate CSRF token
if (!isset($csrfToken) || !isset($_SESSION['csrf_token']) || $csrfToken !== $_SESSION['csrf_token']) {
    $_SESSION['error'][] = 'Invalid security token';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Check if employee is logged in
if (!isset($_SESSION['employee']['employee_record_id'])) {
    $_SESSION['error'][] = 'You must be logged in';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Get employee ID - handle both array and direct ID
$employeeData = $_SESSION['employee']['employee_record_id'];
if (is_array($employeeData) && isset($employeeData['id'])) {
    $employeeId = $employeeData['id'];
} else {
    $employeeId = $employeeData;
}

if (empty($documentType) || empty($publicUrl)) {
    $_SESSION['error'][] = 'Missing document type or URL';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Map document type to database column - ADD ALL DOCUMENT TYPES HERE
$documentColumn = '';
switch ($documentType) {
    case 'nbi_clearance':
        $documentColumn = 'nbi_clearance';
        break;
    case 'medical_result':
        $documentColumn = 'medical_result';
        break;
    case 'birth_certificate':
        $documentColumn = 'birth_certificate';
        break;
    case 'resume':
        $documentColumn = 'resume';
        break;
    case 'sss_document':
        $documentColumn = 'sss_document';
        break;
    case 'philhealth_document':
        $documentColumn = 'philhealth_document';
        break;
    case 'pagibig_document':
        $documentColumn = 'pagibig_document';
        break;
    default:
        $_SESSION['error'][] = 'Invalid document type';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit();
}

try {
    $db->beginTransaction();

    // Check if employee exists
    $employee = $db->query("SELECT id FROM employees WHERE id = ?", [$employeeId])->fetch_one();
    if (!$employee) {
        throw new Exception('Employee not found');
    }

    // Update the specific document field in employees table
    $db->query("
        UPDATE employees 
        SET {$documentColumn} = ?, updated_at = NOW()
        WHERE id = ?
    ", [$publicUrl, $employeeId]);

    $db->commit();

    $_SESSION['success'][] = ucfirst(str_replace('_', ' ', $documentType)) . ' uploaded successfully';

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'][] = 'Error saving document: ' . $e->getMessage();
    error_log("Error uploading document: " . $e->getMessage());
}

// Redirect back
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit();