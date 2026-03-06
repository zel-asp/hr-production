<?php
// download-upload.php

use Core\Database;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Get parameters from URL
$date = isset($_GET['date']) ? $_GET['date'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$department = isset($_GET['department']) ? $_GET['department'] : '';

// Build WHERE clause based on parameters
$whereConditions = [];
$params = [];

if (!empty($date)) {
    // Download for specific date
    $whereConditions[] = "es.schedule_date = ?";
    $params[] = $date;
    $filename = "schedule_" . $date . ".csv";
    $reportTitle = "Schedule for " . date('F j, Y', strtotime($date));
} elseif (!empty($startDate) && !empty($endDate)) {
    // Download for date range
    $whereConditions[] = "es.schedule_date BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    $filename = "schedule_" . $startDate . "_to_" . $endDate . ".csv";
    $reportTitle = "Schedule from " . date('M j, Y', strtotime($startDate)) . " to " . date('M j, Y', strtotime($endDate));
} elseif (!empty($filter)) {
    // Download based on filter
    $today = date('Y-m-d');

    switch ($filter) {
        case 'today':
            $whereConditions[] = "es.schedule_date = ?";
            $params[] = $today;
            $filename = "schedule_today_" . $today . ".csv";
            $reportTitle = "Today's Schedule - " . date('F j, Y');
            break;
        case 'upcoming':
            $whereConditions[] = "es.schedule_date >= ?";
            $params[] = $today;
            $filename = "schedule_upcoming_from_" . $today . ".csv";
            $reportTitle = "Upcoming Schedules from " . date('F j, Y');
            break;
        case 'week':
            $startOfWeek = date('Y-m-d', strtotime('monday this week'));
            $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
            $whereConditions[] = "es.schedule_date BETWEEN ? AND ?";
            $params[] = $startOfWeek;
            $params[] = $endOfWeek;
            $filename = "schedule_week_" . $startOfWeek . "_to_" . $endOfWeek . ".csv";
            $reportTitle = "This Week's Schedule - " . date('F j, Y') . " week";
            break;
        case 'month':
            $startOfMonth = date('Y-m-01');
            $endOfMonth = date('Y-m-t');
            $whereConditions[] = "es.schedule_date BETWEEN ? AND ?";
            $params[] = $startOfMonth;
            $params[] = $endOfMonth;
            $filename = "schedule_month_" . date('Y_m') . ".csv";
            $reportTitle = "This Month's Schedule - " . date('F Y');
            break;
        default:
            $filename = "schedule_export_" . date('Y-m-d') . ".csv";
            $reportTitle = "Schedule Export - " . date('F j, Y');
    }
} else {
    // Default: download all upcoming schedules
    $whereConditions[] = "es.schedule_date >= ?";
    $params[] = date('Y-m-d');
    $filename = "schedule_all_upcoming_" . date('Y-m-d') . ".csv";
    $reportTitle = "All Upcoming Schedules - " . date('F j, Y');
}

// Add department filter if provided
if (!empty($department)) {
    $whereConditions[] = "e.department = ?";
    $params[] = $department;
    $filename = str_replace('.csv', '_' . $department . '.csv', $filename);
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

try {
    // Get schedules from employee_schedules with employee and shift details
    $schedules = $db->query("
        SELECT 
            es.id as schedule_id,
            e.id as employee_id,
            e.employee_number,
            e.full_name as employee_name,
            e.position,
            e.department,
            es.schedule_date,
            DATE_FORMAT(es.schedule_date, '%m/%d/%Y') as formatted_date,
            DATE_FORMAT(es.schedule_date, '%W') as day_of_week,
            TIME_FORMAT(es.time_in, '%H:%i') as time_in,
            TIME_FORMAT(es.time_out, '%H:%i') as time_out,
            s.shift_name,
            s.shift_code,
            s.start_time as shift_start,
            s.end_time as shift_end,
            CASE 
                WHEN es.schedule_date < CURDATE() THEN 'Past'
                WHEN es.schedule_date = CURDATE() THEN 'Today'
                ELSE 'Upcoming'
            END as status
        FROM employee_schedules es
        JOIN employees e ON es.employee_id = e.id
        LEFT JOIN shifts s ON es.shift_id = s.id
        $whereClause
        ORDER BY es.schedule_date ASC, e.department ASC, e.full_name ASC
    ", $params)->find();

    if (empty($schedules)) {
        throw new Exception("No schedules found for the selected criteria");
    }

    // Set headers for download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // ============================================
    // REPORT HEADER
    // ============================================
    fputcsv($output, ['SCHEDULE REPORT']);
    fputcsv($output, [$reportTitle]);
    fputcsv($output, ['Generated:', date('F j, Y g:i A')]);
    if (!empty($department)) {
        fputcsv($output, ['Department Filter:', $department]);
    }
    fputcsv($output, ['Total Records:', count($schedules)]);
    fputcsv($output, []); // Empty line
    fputcsv($output, []); // Empty line

    // ============================================
    // MAIN SCHEDULE TABLE
    // ============================================
    fputcsv($output, ['SCHEDULE DETAILS']);
    fputcsv($output, []); // Empty line

    // Write headers
    fputcsv($output, [
        'Employee ID',
        'Employee Name',
        'Position',
        'Department',
        'Date',
        'Day',
        'Time In',
        'Time Out',
        'Shift Name',
        'Shift Code',
        'Shift Start',
        'Shift End',
        'Status'
    ]);

    // Write data rows
    foreach ($schedules as $schedule) {
        fputcsv($output, [
            $schedule['employee_id'],
            $schedule['employee_name'],
            $schedule['position'],
            $schedule['department'] ?? '—',
            $schedule['formatted_date'],
            $schedule['day_of_week'],
            $schedule['time_in'] ?? '—',
            $schedule['time_out'] ?? '—',
            $schedule['shift_name'] ?? '—',
            $schedule['shift_code'] ?? '—',
            $schedule['shift_start'] ?? '—',
            $schedule['shift_end'] ?? '—',
            $schedule['status']
        ]);
    }

    fputcsv($output, []); // Empty line
    fputcsv($output, []); // Empty line

    // ============================================
    // SUMMARY STATISTICS
    // ============================================
    fputcsv($output, ['SUMMARY STATISTICS']);
    fputcsv($output, []); // Empty line

    // Calculate summary statistics
    $totalSchedules = count($schedules);
    $pastCount = 0;
    $todayCount = 0;
    $upcomingCount = 0;
    $morningShifts = 0;
    $afternoonShifts = 0;
    $nightShifts = 0;
    $uniqueEmployees = [];

    foreach ($schedules as $schedule) {
        // Count by status
        if ($schedule['status'] == 'Past')
            $pastCount++;
        elseif ($schedule['status'] == 'Today')
            $todayCount++;
        elseif ($schedule['status'] == 'Upcoming')
            $upcomingCount++;

        // Count by shift type
        if (!empty($schedule['shift_name'])) {
            if (strpos($schedule['shift_name'], 'Morning') !== false) {
                $morningShifts++;
            } elseif (strpos($schedule['shift_name'], 'Afternoon') !== false) {
                $afternoonShifts++;
            } elseif (
                strpos($schedule['shift_name'], 'Graveyard') !== false ||
                strpos($schedule['shift_name'], 'Night') !== false
            ) {
                $nightShifts++;
            }
        }

        // Track unique employees
        $uniqueEmployees[$schedule['employee_id']] = true;
    }

    fputcsv($output, ['Metric', 'Value']);
    fputcsv($output, ['Total Schedules:', $totalSchedules]);
    fputcsv($output, ['Unique Employees:', count($uniqueEmployees)]);
    fputcsv($output, ['Past Schedules:', $pastCount]);
    fputcsv($output, ["Today's Schedules:", $todayCount]);
    fputcsv($output, ['Upcoming Schedules:', $upcomingCount]);
    fputcsv($output, []);
    fputcsv($output, ['Morning Shifts:', $morningShifts]);
    fputcsv($output, ['Afternoon Shifts:', $afternoonShifts]);
    fputcsv($output, ['Night Shifts:', $nightShifts]);

    fputcsv($output, []); // Empty line
    fputcsv($output, []); // Empty line

    // ============================================
    // DEPARTMENT BREAKDOWN
    // ============================================
    fputcsv($output, ['DEPARTMENT BREAKDOWN']);
    fputcsv($output, []); // Empty line

    // Group by department
    $deptStats = [];
    foreach ($schedules as $schedule) {
        $dept = $schedule['department'] ?? 'Unassigned';
        if (!isset($deptStats[$dept])) {
            $deptStats[$dept] = [
                'total' => 0,
                'past' => 0,
                'today' => 0,
                'upcoming' => 0,
                'morning' => 0,
                'afternoon' => 0,
                'night' => 0,
                'employees' => []
            ];
        }

        $deptStats[$dept]['total']++;
        $deptStats[$dept][strtolower($schedule['status'])]++;
        $deptStats[$dept]['employees'][$schedule['employee_id']] = true;

        if (!empty($schedule['shift_name'])) {
            if (strpos($schedule['shift_name'], 'Morning') !== false) {
                $deptStats[$dept]['morning']++;
            } elseif (strpos($schedule['shift_name'], 'Afternoon') !== false) {
                $deptStats[$dept]['afternoon']++;
            } elseif (
                strpos($schedule['shift_name'], 'Graveyard') !== false ||
                strpos($schedule['shift_name'], 'Night') !== false
            ) {
                $deptStats[$dept]['night']++;
            }
        }
    }

    // Department headers
    fputcsv($output, [
        'Department',
        'Total Schedules',
        'Unique Employees',
        'Past',
        'Today',
        'Upcoming',
        'Morning',
        'Afternoon',
        'Night'
    ]);

    // Department data
    foreach ($deptStats as $dept => $stats) {
        fputcsv($output, [
            $dept,
            $stats['total'],
            count($stats['employees']),
            $stats['past'] ?? 0,
            $stats['today'] ?? 0,
            $stats['upcoming'] ?? 0,
            $stats['morning'],
            $stats['afternoon'],
            $stats['night']
        ]);
    }

    // Department totals row
    fputcsv($output, [
        'TOTAL',
        $totalSchedules,
        count($uniqueEmployees),
        $pastCount,
        $todayCount,
        $upcomingCount,
        $morningShifts,
        $afternoonShifts,
        $nightShifts
    ]);

    fputcsv($output, []); // Empty line
    fputcsv($output, []); // Empty line

    // ============================================
    // REPORT FOOTER
    // ============================================
    fputcsv($output, ['END OF REPORT']);
    fputcsv($output, ['Generated by:', 'HR System']);
    fputcsv($output, ['Downloaded by:', $_SESSION['user_name'] ?? 'Admin User']);
    fputcsv($output, ['Download time:', date('F j, Y g:i A')]);

    fclose($output);
    exit;

} catch (Exception $e) {
    $_SESSION['error'][] = "Download failed: " . $e->getMessage();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}