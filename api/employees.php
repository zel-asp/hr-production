<?php
require_once __DIR__ . '/../cors-handler.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/keys.php';

try {
    $apiInfo = validateApiKey();
    $db = getDB();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        sendError('Method not allowed', 405);
    }
    
    $department = $_GET['department'] ?? '';
    if (empty($department)) {
        sendError('Department parameter is required', 400);
    }
    
    $employees = $db->query(
        "SELECT e.id, e.employee_number, e.full_name, e.email, e.phone, 
                e.position, e.hourly_rate, e.department, e.start_date, e.status,
                e.role, e.gender, e.age,
                s.shift_name, s.start_time, s.end_time
         FROM employees e
         LEFT JOIN shifts s ON e.shift_id = s.id
         WHERE e.department = ? AND e.status != 'terminated'
         ORDER BY e.full_name",
        [$department]
    )->find();
    
    sendResponse([
        'department' => $department,
        'total' => count($employees),
        'employees' => $employees
    ]);
    
} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}