<?php
use Core\Database;

// Important: Set header before any output
header('Content-Type: application/json');

require base_path('core/middleware/employeeAuth.php');

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Get employee ID from session
$employeeData = $_SESSION['employee']['employee_record_id'] ?? null;
if (is_array($employeeData) && isset($employeeData['id'])) {
    $employeeId = $employeeData['id'];
} else {
    $employeeId = $employeeData;
}

if (!$employeeId) {
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]);
    exit();
}

$today = date('Y-m-d');

try {
    // Get current attendance status
    $currentAttendance = $db->query("
        SELECT * FROM attendance 
        WHERE employee_id = ? AND date = ? AND status != 'clocked_out' 
        ORDER BY id DESC LIMIT 1
    ", [$employeeId, $today])->fetch_one();

    $status = 'clocked_out';
    $elapsedSeconds = 0;
    $attendanceId = null;
    $pauseTotal = 0;

    if ($currentAttendance) {
        $status = $currentAttendance['status'];
        $attendanceId = $currentAttendance['id'];
        $pauseTotal = $currentAttendance['pause_total'] ?? 0;

        if ($status == 'clocked_in') {
            $elapsedSeconds = time() - strtotime($currentAttendance['clock_in']) - ($pauseTotal * 60);
        } elseif ($status == 'paused') {
            $elapsedSeconds = strtotime($currentAttendance['pause_start']) - strtotime($currentAttendance['clock_in']) - ($pauseTotal * 60);
        }
    }

    echo json_encode([
        'success' => true,
        'status' => $status,
        'attendance_id' => $attendanceId,
        'elapsed_seconds' => max(0, $elapsedSeconds),
        'pause_total' => $pauseTotal
    ]);

} catch (Exception $e) {
    error_log("Error fetching attendance status: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching status'
    ]);
}