<?php
// cancel-contract.php
use Core\Database;

require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);



try {
    $contract_id = filter_input(INPUT_POST, 'contract_id', FILTER_VALIDATE_INT);

    if (!$contract_id) {
        throw new Exception('Invalid contract ID');
    }

    // Get contract details before deletion
    $contract = $db->query(
        "SELECT sc.*, a.full_name, a.status as applicant_status 
         FROM schedule_contract sc
         JOIN applicants a ON a.id = sc.applicant_id
         WHERE sc.id = :id",
        ['id' => $contract_id]
    )->find();

    if (!$contract) {
        throw new Exception('Contract not found');
    }

    // Delete the contract schedule
    $db->query(
        "DELETE FROM schedule_contract WHERE id = :id",
        ['id' => $contract_id]
    );

    // Update applicant status back to whatever it was before
    // You might want to change this logic based on your needs
    $db->query(
        "UPDATE applicants SET status = 'Review' WHERE id = :applicant_id",
        ['applicant_id' => $contract[0]['applicant_id']]
    );

    $_SESSION['success'][] = "Contract schedule for {$contract[0]['employee_name']} has been cancelled.";

} catch (Exception $e) {
    $_SESSION['error'][] = $e->getMessage();
}

header('Location: /main?tab=applicant#contract');
exit();