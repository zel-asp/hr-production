<?php
require_once __DIR__ . '/../cors-handler.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/keys.php';

$apiInfo = validateApiKey();
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// GET: Get payroll records
if ($method === 'GET') {
    $id = $_GET['id'] ?? null;
    $status = $_GET['status'] ?? null;
    $employeeId = $_GET['employee_id'] ?? null;
    
    if ($id) {
        $result = $db->query(
            "SELECT p.*, e.full_name, e.department, e.employee_number, e.email
             FROM payroll_summary p
             JOIN employees e ON p.employee_id = e.id
             WHERE p.id = ?",
            [$id]
        )->fetch_one();
    } else {
        $sql = "SELECT p.*, e.full_name, e.department, e.employee_number, e.email
                FROM payroll_summary p
                JOIN employees e ON p.employee_id = e.id";
        
        $params = [];
        
        if ($status && $employeeId) {
            $sql .= " WHERE p.status = ? AND p.employee_id = ?";
            $params = [$status, $employeeId];
        } elseif ($status) {
            $sql .= " WHERE p.status = ?";
            $params = [$status];
        } elseif ($employeeId) {
            $sql .= " WHERE p.employee_id = ?";
            $params = [$employeeId];
        }
        
        $sql .= " ORDER BY p.period_end DESC";
        $result = $db->query($sql, $params)->find();
    }
    
    sendResponse($result);
}

// POST: Handle payroll actions
elseif ($method === 'POST') {
    if (!canWrite($apiInfo)) {
        sendError('Write permission required', 403);
    }
    
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? '';
    
    if (!$id) {
        sendError('Payroll ID is required', 400);
    }
    
    // Get payroll details
    $payroll = $db->query(
        "SELECT p.*, e.full_name, e.email, e.id as employee_id
         FROM payroll_summary p
         JOIN employees e ON p.employee_id = e.id
         WHERE p.id = ?",
        [$id]
    )->fetch_one();
    
    if (!$payroll) {
        sendError('Payroll record not found', 404);
    }
    
    // PROCESS PAYROLL (HR only)
    if ($action === 'process') {
        if ($apiInfo['name'] !== 'HR System') {
            sendError('Only HR can process payroll', 403);
        }
        
        $db->query(
            "UPDATE payroll_summary SET status = 'Processing' WHERE id = ?",
            [$id]
        );
        
        sendResponse(['message' => 'Payroll is now processing']);
    }
    
    // APPROVE PAYROLL (Finance only)
    elseif ($action === 'approve') {
        if ($apiInfo['name'] !== 'Finance System') {
            sendError('Only finance can approve payroll', 403);
        }
        
        $db->query(
            "UPDATE payroll_summary SET status = 'Processed' WHERE id = ?",
            [$id]
        );
        
        // Format period for notification
        $period = date('M d', strtotime($payroll['period_start'])) . ' - ' . 
                  date('M d', strtotime($payroll['period_end']));
        
        // Notify employee
        notifyEmployee(
            $payroll['employee_id'],
            'Payroll Processed',
            "Your payroll for period {$period} has been processed. Net pay: ₱" . number_format($payroll['net_pay'], 2)
        );
        
        sendResponse(['message' => 'Payroll approved and processed']);
    }
    
    // MARK AS PAID (Finance only)
    elseif ($action === 'paid') {
        if ($apiInfo['name'] !== 'Finance System') {
            sendError('Only finance can mark payroll as paid', 403);
        }
        
        $db->query(
            "UPDATE payroll_summary SET status = 'Paid' WHERE id = ?",
            [$id]
        );
        
        sendResponse(['message' => 'Payroll marked as paid']);
    }
}