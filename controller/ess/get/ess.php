<?php

use Core\Database;

require base_path('core/middleware/employeeAuth.php');

$config = require base_path('config/config.php');
$db = new Database($config['database']);

$employeeData = $_SESSION['employee']['employee_record_id'] ?? null;

if (is_array($employeeData) && isset($employeeData['id'])) {
    $employeeId = $employeeData['id'];
} else {
    $employeeId = $employeeData;
}

if (!$employeeId) {
    error_log('no employee id found in session');
    $_SESSION['error'][] = "employee not found";
    header('Location: /login');
    exit();
}

$employeeInfo = $db->query("
    SELECT 
        e.*,
        a.full_name as applicant_name,
        a.email as applicant_email,
        a.phone as applicant_phone,
        a.position as applied_position,
        a.experience,
        a.education,
        a.skills,
        a.status as applicant_status,
        a.hired_date,
        a.start_date,
        a.resume_path,
        a.created_at as applicant_created_at
    FROM employees e
    LEFT JOIN applicants a ON e.applicant_id = a.id
    WHERE e.id = ?
", [$employeeId])->fetch_one();

// Debug: Check what's returned
error_log('Employee Info: ' . print_r($employeeInfo, true));

if (!$employeeInfo) {
    error_log('No employee found with ID: ' . $employeeId);

    // Debug: Check what employees exist
    $allEmployees = $db->query("SELECT id, applicant_id, full_name FROM employees")->find();
    error_log('All employees: ' . print_r($allEmployees, true));

    $_SESSION['error'][] = "Employee information not found";
    header('Location: /login');
    exit();
}

// FIXED: Get employee account information
// First get the applicant_id from the employee
$applicantId = $employeeInfo['applicant_id'] ?? null;

if ($applicantId) {
    $employeeAccount = $db->query("
        SELECT 
            ea.*
        FROM employee_accounts ea
        WHERE ea.applicant_id = ?
    ", [$applicantId])->fetch_one();
} else {
    $employeeAccount = null;
    error_log('No applicant_id found for employee ID: ' . $employeeId);
}

// If no account found, try alternative query using employee_id from session
if (!$employeeAccount) {
    // Try to find by employee_id string (EMP-XXX format)
    $employeeAccount = $db->query("
        SELECT 
            ea.*
        FROM employee_accounts ea
        WHERE ea.employee_id = ?
    ", [$employeeInfo['employee_number'] ?? ''])->fetch_one();
}

// Handle case when no account found
if (!$employeeAccount) {
    $employeeAccount = []; // Set to empty array to avoid errors
    error_log('No account found for employee ID: ' . $employeeId . ' with applicant_id: ' . ($applicantId ?? 'null'));
}

// Get employee benefits if any
$employeeBenefits = $db->query("
    SELECT 
        eb.*,
        bp.provider_name,
        bp.contact_info as provider_contact
    FROM employee_benefits eb
    LEFT JOIN benefit_providers bp ON eb.provider_id = bp.id
    WHERE eb.employee_id = ?
", [$employeeId])->find();

if (!$employeeBenefits) {
    $employeeBenefits = [];
}

// For initial tasks (limited to 5)
$limitedTasks = $db->query("
    SELECT 
        t.id,
        t.task_type,
        t.task_description,
        t.due_date,
        t.priority,
        t.assigned_staff,
        t.status,
        e.full_name as employee_name
    FROM tasks t
    INNER JOIN employees e ON t.assigned_to = e.id
    WHERE t.assigned_to = ?
    ORDER BY 
        CASE t.status
            WHEN 'Not Started' THEN 1
            WHEN 'Ongoing' THEN 2
            WHEN 'Completed' THEN 3
        END,
        CASE t.priority
            WHEN 'urgent' THEN 1
            WHEN 'high' THEN 2
            WHEN 'medium' THEN 3
            WHEN 'low' THEN 4
        END,
        t.due_date ASC
    LIMIT 5
", [$employeeId])->find();

if (!$limitedTasks) {
    $limitedTasks = [];
}

// For all tasks
$allTasks = $db->query("
    SELECT 
        t.id,
        t.task_type,
        t.task_description,
        t.due_date,
        t.priority,
        t.assigned_staff,
        t.status,
        e.full_name as employee_name
    FROM tasks t
    INNER JOIN employees e ON t.assigned_to = e.id
    WHERE t.assigned_to = ?
    ORDER BY 
        CASE t.status
            WHEN 'Not Started' THEN 1
            WHEN 'Ongoing' THEN 2
            WHEN 'Completed' THEN 3
        END,
        CASE t.priority
            WHEN 'urgent' THEN 1
            WHEN 'high' THEN 2
            WHEN 'medium' THEN 3
            WHEN 'low' THEN 4
        END,
        t.due_date ASC
", [$employeeId])->find();

if (!$allTasks) {
    $allTasks = [];
}

// Get task statistics
$taskStats = $db->query("
    SELECT 
        COUNT(*) as total_tasks,
        SUM(CASE WHEN status = 'Not Started' THEN 1 ELSE 0 END) as not_started_count,
        SUM(CASE WHEN status = 'Ongoing' THEN 1 ELSE 0 END) as ongoing_count,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_count,
        SUM(CASE WHEN priority = 'urgent' AND status != 'Completed' THEN 1 ELSE 0 END) as urgent_count
    FROM tasks
    WHERE assigned_to = ?
", [$employeeId])->fetch_one();

if (!$taskStats) {
    $taskStats = [
        'total_tasks' => 0,
        'not_started_count' => 0,
        'ongoing_count' => 0,
        'completed_count' => 0,
        'urgent_count' => 0
    ];
}

// Get upcoming tasks (due soon)
$upcomingTasks = $db->query("
    SELECT 
        t.id,
        t.task_description,
        t.due_date,
        t.priority,
        t.status
    FROM tasks t
    WHERE t.assigned_to = ? 
        AND t.status != 'Completed' 
        AND t.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY t.due_date ASC
", [$employeeId])->find();

if (!$upcomingTasks) {
    $upcomingTasks = [];
}

// ADDED: Get leave requests based on employee_record_id

// Get recent leave requests (for dashboard display - limited to 5)
$recentLeaveRequests = $db->query("
    SELECT 
        lr.*,
        e.full_name as employee_name,
        e.employee_number,
        DATEDIFF(lr.end_date, lr.start_date) + 1 as calculated_days
    FROM leave_requests lr
    INNER JOIN employees e ON lr.employee_id = e.id
    WHERE lr.employee_id = ?
    ORDER BY 
        CASE lr.status
            WHEN 'Pending' THEN 1
            WHEN 'Approved' THEN 2
            WHEN 'Rejected' THEN 3
            WHEN 'Cancelled' THEN 4
        END,
        lr.created_at DESC
    LIMIT 5
", [$employeeId])->find();

if (!$recentLeaveRequests) {
    $recentLeaveRequests = [];
}

// Get all leave requests (for "View All" modal)
$allLeaveRequests = $db->query("
    SELECT 
        lr.*,
        e.full_name as employee_name,
        e.employee_number,
        DATEDIFF(lr.end_date, lr.start_date) + 1 as calculated_days
    FROM leave_requests lr
    INNER JOIN employees e ON lr.employee_id = e.id
    WHERE lr.employee_id = ?
    ORDER BY 
        CASE lr.status
            WHEN 'Pending' THEN 1
            WHEN 'Approved' THEN 2
            WHEN 'Rejected' THEN 3
            WHEN 'Cancelled' THEN 4
        END,
        lr.created_at DESC
", [$employeeId])->find();

if (!$allLeaveRequests) {
    $allLeaveRequests = [];
}

// Get leave request statistics
$leaveStats = $db->query("
    SELECT 
        COUNT(*) as total_requests,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved_count,
        SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected_count,
        SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_count,
        SUM(CASE WHEN leave_type = 'Annual Leave' AND status = 'Approved' THEN total_days ELSE 0 END) as annual_leave_used,
        SUM(CASE WHEN leave_type = 'Sick Leave' AND status = 'Approved' THEN total_days ELSE 0 END) as sick_leave_used
    FROM leave_requests
    WHERE employee_id = ?
", [$employeeId])->fetch_one();

if (!$leaveStats) {
    $leaveStats = [
        'total_requests' => 0,
        'pending_count' => 0,
        'approved_count' => 0,
        'rejected_count' => 0,
        'cancelled_count' => 0,
        'annual_leave_used' => 0,
        'sick_leave_used' => 0
    ];
}

$annualLeaveTotal = 18;
$sickLeaveTotal = 12;

$leaveBalances = [
    'annual' => [
        'total' => $annualLeaveTotal,
        'used' => (int) ($leaveStats['annual_leave_used'] ?? 0),
        'remaining' => $annualLeaveTotal - (int) ($leaveStats['annual_leave_used'] ?? 0)
    ],
    'sick' => [
        'total' => $sickLeaveTotal,
        'used' => (int) ($leaveStats['sick_leave_used'] ?? 0),
        'remaining' => $sickLeaveTotal - (int) ($leaveStats['sick_leave_used'] ?? 0)
    ]
];



// ATTENDANCE QUERIES

// Get current date info
$today = date('Y-m-d');
$currentMonth = date('Y-m');
$currentMonthName = date('F Y');
$currentDay = (int) date('j');

// Get employee's shift information
$employeeShift = $db->query("
    SELECT s.*, e.shift_id 
    FROM employees e
    LEFT JOIN shifts s ON e.shift_id = s.id
    WHERE e.id = ?
", [$employeeId])->fetch_one();

// Get current attendance status
$currentAttendance = $db->query("
    SELECT * FROM attendance 
    WHERE employee_id = ? AND date = ? AND status != 'clocked_out' 
    ORDER BY id DESC LIMIT 1
", [$employeeId, $today])->fetch_one();

$attendanceStatus = 'clocked_out';
$elapsedSeconds = 0;
$pauseTotal = 0;

if ($currentAttendance) {
    $attendanceStatus = $currentAttendance['status'];

    if ($attendanceStatus == 'clocked_in') {
        $elapsedSeconds = time() - strtotime($currentAttendance['clock_in']) - ($currentAttendance['pause_total'] * 60);
    } elseif ($attendanceStatus == 'paused') {
        $elapsedSeconds = strtotime($currentAttendance['pause_start']) - strtotime($currentAttendance['clock_in']) - ($currentAttendance['pause_total'] * 60);
    }

    $pauseTotal = $currentAttendance['pause_total'] ?? 0;
}

$showClockIn = ($attendanceStatus == 'clocked_out');

// Get current pay period
if ($currentDay <= 5) {
    $periodStart = date('Y-m-d', strtotime('first day of previous month')) . '-21';
    $periodEnd = date('Y-m-05');
    $periodName = 'Pay Period: 21st - 5th';
} elseif ($currentDay <= 20) {
    $periodStart = date('Y-m-06');
    $periodEnd = date('Y-m-20');
    $periodName = 'Pay Period: 6th - 20th';
} else {
    $periodStart = date('Y-m-21');
    $periodEnd = date('Y-m-t');
    $periodName = 'Pay Period: 21st - ' . date('t') . 'th';
}

// Get current period summary
$currentPeriod = $db->query("
    SELECT 
        COALESCE(total_regular_hours, 0) as regular_hours,
        COALESCE(total_overtime_hours, 0) as overtime_hours,
        COALESCE(total_late_minutes, 0) as late_minutes
    FROM attendance_summary 
    WHERE employee_id = ? AND period_start = ? AND period_end = ?
", [$employeeId, $periodStart, $periodEnd])->fetch_one();

if (!$currentPeriod) {
    $currentPeriod = ['regular_hours' => 0, 'overtime_hours' => 0, 'late_minutes' => 0];
}

// Get this week's attendance
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekEnd = date('Y-m-d', strtotime('sunday this week'));

$weekStats = $db->query("
    SELECT 
        COALESCE(SUM(regular_hours), 0) as week_regular,
        COALESCE(SUM(overtime_hours), 0) as week_overtime,
        COUNT(CASE WHEN clock_in IS NOT NULL THEN 1 END) as days_worked,
        COALESCE(SUM(late_minutes), 0) as total_late_minutes
    FROM attendance 
    WHERE employee_id = ? AND date BETWEEN ? AND ? AND status = 'clocked_out'
", [$employeeId, $weekStart, $weekEnd])->fetch_one();

if (!$weekStats) {
    $weekStats = ['week_regular' => 0, 'week_overtime' => 0, 'days_worked' => 0, 'total_late_minutes' => 0];
}

// Get this month's attendance
$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');

$monthStats = $db->query("
    SELECT 
        COALESCE(SUM(regular_hours), 0) as month_regular,
        COALESCE(SUM(overtime_hours), 0) as month_overtime,
        COUNT(CASE WHEN clock_in IS NOT NULL THEN 1 END) as days_worked,
        COALESCE(SUM(late_minutes), 0) as total_late_minutes,
        SUM(CASE WHEN late_status = 'late' THEN 1 ELSE 0 END) as late_days,
        SUM(CASE WHEN late_status = 'grace_period' THEN 1 ELSE 0 END) as grace_days,
        SUM(CASE WHEN early_departure_minutes > 0 THEN 1 ELSE 0 END) as early_departure_days,
        COALESCE(SUM(early_departure_minutes), 0) as total_early_minutes
    FROM attendance 
    WHERE employee_id = ? AND date BETWEEN ? AND ? AND status = 'clocked_out'
", [$employeeId, $monthStart, $monthEnd])->fetch_one();

if (!$monthStats) {
    $monthStats = [
        'month_regular' => 0,
        'month_overtime' => 0,
        'days_worked' => 0,
        'total_late_minutes' => 0,
        'late_days' => 0,
        'grace_days' => 0,
        'early_departure_days' => 0,
        'total_early_minutes' => 0
    ];
}

// Calculate expected work days (weekdays only)
$expectedWorkDays = 0;
$currentDate = strtotime($monthStart);
$endDate = strtotime($monthEnd);
while ($currentDate <= $endDate) {
    $dayOfWeek = date('N', $currentDate);
    if ($dayOfWeek < 6) { // Monday to Friday
        $expectedWorkDays++;
    }
    $currentDate = strtotime('+1 day', $currentDate);
}

$absences = max(0, $expectedWorkDays - $monthStats['days_worked']);

// Get recent attendance records
$recentAttendance = $db->query("
    SELECT 
        date, 
        clock_in, 
        clock_out, 
        regular_hours, 
        overtime_hours,
        late_minutes,
        late_status,
        early_departure_minutes,
        TIME_FORMAT(clock_in, '%h:%i %p') as clock_in_formatted,
        TIME_FORMAT(clock_out, '%h:%i %p') as clock_out_formatted
    FROM attendance 
    WHERE employee_id = ? AND status = 'clocked_out'
    ORDER BY date DESC
    LIMIT 10
", [$employeeId])->find();

if (!$recentAttendance) {
    $recentAttendance = [];
}

// Get daily breakdown for the month
$dailyBreakdown = $db->query("
    SELECT 
        date,
        DAYNAME(date) as day_name,
        regular_hours,
        overtime_hours,
        late_minutes,
        late_status,
        early_departure_minutes,
        TIME_FORMAT(clock_in, '%h:%i %p') as clock_in_time,
        TIME_FORMAT(clock_out, '%h:%i %p') as clock_out_time
    FROM attendance 
    WHERE employee_id = ? AND date BETWEEN ? AND ? AND status = 'clocked_out'
    ORDER BY date DESC
", [$employeeId, $monthStart, $monthEnd])->find();

if (!$dailyBreakdown) {
    $dailyBreakdown = [];
}

// Calculate totals for display
$weekTotal = $weekStats['week_regular'] + $weekStats['week_overtime'];
$monthTotal = $monthStats['month_regular'] + $monthStats['month_overtime'];

// Helper function to format hours and minutes
function formatHoursMinutes($decimalHours)
{
    $hours = floor($decimalHours);
    $minutes = round(($decimalHours - $hours) * 60);
    return $hours . 'h ' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . 'm';
}

// Helper function to format minutes
function formatMinutes($minutes)
{
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    if ($h > 0) {
        return $h . 'h ' . $m . 'm';
    }
    return $m . 'm';
}

// ALL-TIME ATTENDANCE STATISTICS

// Get all-time attendance statistics
$allTimeStats = $db->query("
    SELECT 
        COUNT(DISTINCT date) as total_days_worked,
        COALESCE(SUM(regular_hours), 0) as total_regular_hours,
        COALESCE(SUM(overtime_hours), 0) as total_overtime_hours,
        COALESCE(SUM(late_minutes), 0) as total_late_minutes,
        SUM(CASE WHEN late_status = 'late' THEN 1 ELSE 0 END) as total_late_days,
        SUM(CASE WHEN late_status = 'grace_period' THEN 1 ELSE 0 END) as total_grace_days,
        SUM(CASE WHEN early_departure_minutes > 0 THEN 1 ELSE 0 END) as total_early_days,
        COALESCE(SUM(early_departure_minutes), 0) as total_early_minutes,
        MIN(date) as first_work_date,
        MAX(date) as last_work_date
    FROM attendance 
    WHERE employee_id = ? AND status = 'clocked_out'
", [$employeeId])->fetch_one();

if (!$allTimeStats) {
    $allTimeStats = [
        'total_days_worked' => 0,
        'total_regular_hours' => 0,
        'total_overtime_hours' => 0,
        'total_late_minutes' => 0,
        'total_late_days' => 0,
        'total_grace_days' => 0,
        'total_early_days' => 0,
        'total_early_minutes' => 0,
        'first_work_date' => null,
        'last_work_date' => null
    ];
}

// Get attendance by month (for chart/trends)
$attendanceByMonth = $db->query("
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as month,
        DATE_FORMAT(date, '%M %Y') as month_name,
        COUNT(*) as days_worked,
        COALESCE(SUM(regular_hours), 0) as total_regular,
        COALESCE(SUM(overtime_hours), 0) as total_overtime,
        COALESCE(SUM(late_minutes), 0) as total_late,
        SUM(CASE WHEN late_status = 'late' THEN 1 ELSE 0 END) as late_days
    FROM attendance 
    WHERE employee_id = ? AND status = 'clocked_out'
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
", [$employeeId])->find();

if (!$attendanceByMonth) {
    $attendanceByMonth = [];
}

// Get all attendance records (paginated) - FIXED VERSION
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$perPage = 3;
$offset = ($page - 1) * $perPage;

// Ensure they are integers
$perPage = (int) $perPage;
$offset = (int) $offset;

$totalRecords = $db->query("
    SELECT COUNT(*) as count 
    FROM attendance 
    WHERE employee_id = ? AND status = 'clocked_out'
", [$employeeId])->fetch_one();

$totalPages = ceil($totalRecords['count'] / $perPage);

// FIX: Use the integer values directly in the query
$allAttendanceRecords = $db->query("
    SELECT 
        id,
        date,
        DAYNAME(date) as day_name,
        DATE_FORMAT(clock_in, '%h:%i %p') as clock_in_time,
        DATE_FORMAT(clock_out, '%h:%i %p') as clock_out_time,
        regular_hours,
        overtime_hours,
        late_minutes,
        late_status,
        early_departure_minutes,
        TIMEDIFF(clock_out, clock_in) as total_duration,
        created_at
    FROM attendance 
    WHERE employee_id = ? AND status = 'clocked_out'
    ORDER BY date DESC
    LIMIT $perPage OFFSET $offset
", [$employeeId])->find();

if (!$allAttendanceRecords) {
    $allAttendanceRecords = [];
}

// Calculate attendance rate
$earliestDate = $allTimeStats['first_work_date'] ? strtotime($allTimeStats['first_work_date']) : time();
$daysSince = ceil((time() - $earliestDate) / (60 * 60 * 24));
$workingDaysSince = 0;

// Count working days since first work date (approximate)
$tempDate = $earliestDate;
while ($tempDate <= time()) {
    $dayOfWeek = date('N', $tempDate);
    if ($dayOfWeek < 6) { // Monday to Friday
        $workingDaysSince++;
    }
    $tempDate = strtotime('+1 day', $tempDate);
}

$attendanceRate = $workingDaysSince > 0
    ? round(($allTimeStats['total_days_worked'] / $workingDaysSince) * 100, 1)
    : 0;


// ============================================
// CLAIMS MANAGEMENT SECTION
// ============================================

// Pagination for claims
$claimsPage = isset($_GET['claims_page']) ? max(1, (int) $_GET['claims_page']) : 1;
$claimsPerPage = 5;
$claimsOffset = ($claimsPage - 1) * $claimsPerPage;

// Filter parameters
$claimsStatusFilter = isset($_GET['claims_status']) ? $_GET['claims_status'] : 'all';
$claimsPeriodFilter = isset($_GET['claims_period']) ? $_GET['claims_period'] : '3';

// Calculate date range based on period filter
$claimsDateCondition = "";
$claimsParams = [];

if ($claimsPeriodFilter !== 'all') {
    $months = (int) $claimsPeriodFilter;
    $claimsDateCondition = "AND expense_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)";
    $claimsParams[] = $months;
}

// Status condition
$claimsStatusCondition = "";
if ($claimsStatusFilter !== 'all') {
    $claimsStatusCondition = "AND status = ?";
    $claimsParams[] = $claimsStatusFilter;
}

// Get total claims count for pagination
try {
    $totalClaimsSql = "
        SELECT COUNT(*) as count 
        FROM expense_claims 
        WHERE 1=1 
        $claimsStatusCondition
        $claimsDateCondition
    ";
    $totalClaims = $db->query($totalClaimsSql, $claimsParams)->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalClaims = 0;
    error_log("Error fetching total claims: " . $th->getMessage());
}

// Get paginated claims
try {
    $claimsList = $db->query("
        SELECT 
            c.*,
            e.full_name as employee_name,
            e.employee_number,
            DATE_FORMAT(c.expense_date, '%M %e, %Y') as formatted_date,
            DATE_FORMAT(c.created_at, '%M %e, %Y') as formatted_created,
            CASE 
                WHEN c.status = 'Pending' THEN 'bg-amber-100 text-amber-700'
                WHEN c.status = 'Approved' THEN 'bg-green-100 text-green-700'
                WHEN c.status = 'Paid' THEN 'bg-blue-100 text-blue-700'
                WHEN c.status = 'Rejected' THEN 'bg-red-100 text-red-700'
                ELSE 'bg-gray-100 text-gray-700'
            END as status_class,
            CONCAT('CL-', YEAR(c.created_at), '-', LPAD(c.id, 4, '0')) as claim_number
        FROM expense_claims c
        LEFT JOIN employees e ON c.employee_id = e.id
        WHERE 1=1
        $claimsStatusCondition
        $claimsDateCondition
        ORDER BY c.expense_date DESC, c.created_at DESC
        LIMIT $claimsPerPage OFFSET $claimsOffset
    ", $claimsParams)->find();
} catch (\Throwable $th) {
    $claimsList = [];
    error_log("Error fetching claims: " . $th->getMessage());
}

$totalClaimsPages = ceil($totalClaims / $claimsPerPage);

// Get icon based on category
function getClaimIcon($category)
{
    $category = strtolower($category ?? '');
    return match (true) {
        str_contains($category, 'travel') || str_contains($category, 'plane') => 'fa-plane',
        str_contains($category, 'meal') || str_contains($category, 'food') || str_contains($category, 'dinner') => 'fa-utensils',
        str_contains($category, 'transport') || str_contains($category, 'taxi') || str_contains($category, 'grab') => 'fa-taxi',
        str_contains($category, 'supplies') || str_contains($category, 'office') => 'fa-file-lines',
        str_contains($category, 'hotel') || str_contains($category, 'accommodation') => 'fa-hotel',
        str_contains($category, 'fuel') || str_contains($category, 'gas') => 'fa-gas-pump',
        default => 'fa-receipt'
    };
}

function getIconBgClass($category)
{
    $category = strtolower($category ?? '');
    return match (true) {
        str_contains($category, 'travel') || str_contains($category, 'plane') => 'bg-blue-100 text-blue-700',
        str_contains($category, 'meal') || str_contains($category, 'food') || str_contains($category, 'dinner') => 'bg-green-100 text-green-700',
        str_contains($category, 'transport') || str_contains($category, 'taxi') || str_contains($category, 'grab') => 'bg-purple-100 text-purple-700',
        str_contains($category, 'supplies') || str_contains($category, 'office') => 'bg-amber-100 text-amber-700',
        str_contains($category, 'hotel') || str_contains($category, 'accommodation') => 'bg-indigo-100 text-indigo-700',
        default => 'bg-gray-100 text-gray-700'
    };
}
$claimsActiveTab = isset($_GET['panel']) && $_GET['panel'] == 'history' ? 'history' : 'new';


// ============================================
// SHIFT & SCHEDULE MANAGEMENT SECTION
// ============================================

// Get current date info
$shiftCurrentDate = date('Y-m-d');
$shiftCurrentWeek = date('W');
$shiftCurrentYear = date('Y');
$shiftCurrentDay = date('w'); // 0 (Sunday) to 6 (Saturday)

// Calculate week dates (Monday to Sunday)
$shiftWeekStart = date('Y-m-d', strtotime('monday this week'));
$shiftWeekEnd = date('Y-m-d', strtotime('sunday this week'));
$shiftWeekLabel = date('M j', strtotime($shiftWeekStart)) . ' - ' . date('M j, Y', strtotime($shiftWeekEnd));
$shiftWeekNumber = $shiftCurrentWeek;

// Get shifts from database
try {
    $shiftAllShifts = $db->query("
        SELECT id, shift_name, shift_code, start_time, end_time, grace_period_minutes
        FROM shifts
        ORDER BY start_time ASC
    ")->find();
} catch (\Throwable $th) {
    $shiftAllShifts = [
        ['id' => 1, 'shift_name' => 'Morning Shift', 'shift_code' => 'MORNING', 'start_time' => '07:00:00', 'end_time' => '15:00:00'],
        ['id' => 2, 'shift_name' => 'Afternoon Shift', 'shift_code' => 'AFTERNOON', 'start_time' => '15:00:00', 'end_time' => '23:00:00'],
        ['id' => 3, 'shift_name' => 'Graveyard Shift', 'shift_code' => 'GRAVEYARD', 'start_time' => '23:00:00', 'end_time' => '07:00:00']
    ];
    error_log("Error fetching shifts: " . $th->getMessage());
}

// Get employee's current shift
try {
    $shiftEmployeeShift = $db->query("
        SELECT s.* 
        FROM employees e
        LEFT JOIN shifts s ON e.shift_id = s.id
        WHERE e.id = ?
    ", [$employeeId])->fetch_one();
} catch (\Throwable $th) {
    $shiftEmployeeShift = null;
    error_log("Error fetching employee shift: " . $th->getMessage());
}

// Get this week's schedule
$shiftWeekSchedule = [];
$shiftDays = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];

for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime("monday this week +{$i} days"));
    $dayName = $shiftDays[$i];

    // Determine shift for this day (simplified - you would have a schedule table in production)
    // For demo, assign shifts based on day
    if ($i < 3) { // Mon-Wed
        $shift = $shiftAllShifts[0] ?? null; // Morning
        $shiftType = 'morning';
        $bgColor = 'bg-blue-50';
        $borderColor = 'border-blue-100';
        $textColor = 'text-blue-700';
        $badgeColor = 'bg-blue-200 text-blue-800';
    } elseif ($i < 5) { // Thu-Fri
        $shift = $shiftAllShifts[1] ?? null; // Afternoon
        $shiftType = 'afternoon';
        $bgColor = 'bg-amber-50';
        $borderColor = 'border-amber-100';
        $textColor = 'text-amber-700';
        $badgeColor = 'bg-amber-200 text-amber-800';
    } else { // Sat-Sun
        $shift = null; // Day off
        $shiftType = 'off';
        $bgColor = 'bg-gray-50';
        $borderColor = 'border-gray-200';
        $textColor = 'text-gray-400';
        $badgeColor = 'bg-gray-200 text-gray-600';
    }

    $shiftWeekSchedule[] = [
        'date' => $date,
        'day' => $dayName,
        'day_index' => $i,
        'shift' => $shift,
        'shift_type' => $shiftType,
        'bg_color' => $bgColor,
        'border_color' => $borderColor,
        'text_color' => $textColor,
        'badge_color' => $badgeColor,
        'is_off' => ($shiftType == 'off')
    ];
}

// Get upcoming shifts (next 7 days from tomorrow)
$shiftUpcoming = [];
for ($i = 1; $i <= 7; $i++) {
    $date = date('Y-m-d', strtotime("+{$i} days"));
    $dayName = date('l', strtotime($date));
    $displayDate = ($i == 1) ? 'Tomorrow' : date('M j, Y', strtotime($date));

    // Determine shift (simplified)
    $dayOfWeek = date('w', strtotime($date));
    if ($dayOfWeek >= 1 && $dayOfWeek <= 3) { // Mon-Wed
        $shift = $shiftAllShifts[0] ?? null;
        $shiftType = 'Morning';
        $icon = 'fa-sun';
        $iconBg = 'bg-blue-100';
        $iconColor = 'text-blue-600';
        $badgeBg = 'bg-blue-50';
        $badgeText = 'text-blue-600';
    } elseif ($dayOfWeek >= 4 && $dayOfWeek <= 5) { // Thu-Fri
        $shift = $shiftAllShifts[1] ?? null;
        $shiftType = 'Afternoon';
        $icon = 'fa-moon';
        $iconBg = 'bg-amber-100';
        $iconColor = 'text-amber-600';
        $badgeBg = 'bg-amber-50';
        $badgeText = 'text-amber-600';
    } elseif ($dayOfWeek == 6) { // Sat
        $shift = $shiftAllShifts[2] ?? null;
        $shiftType = 'Graveyard';
        $icon = 'fa-moon-stars';
        $iconBg = 'bg-purple-100';
        $iconColor = 'text-purple-600';
        $badgeBg = 'bg-purple-50';
        $badgeText = 'text-purple-600';
    } else { // Sun
        $shift = null;
        $shiftType = 'Day Off';
        $icon = 'fa-bed';
        $iconBg = 'bg-gray-100';
        $iconColor = 'text-gray-600';
        $badgeBg = 'bg-gray-100';
        $badgeText = 'text-gray-600';
    }

    if ($shift) {
        $startTime = date('g:i A', strtotime($shift['start_time']));
        $endTime = date('g:i A', strtotime($shift['end_time']));
        $timeDisplay = "$startTime - $endTime";
    } else {
        $timeDisplay = 'Day Off';
    }

    $shiftUpcoming[] = [
        'date' => $date,
        'display_date' => $displayDate,
        'shift_type' => $shiftType,
        'time_display' => $timeDisplay,
        'icon' => $icon,
        'icon_bg' => $iconBg,
        'icon_color' => $iconColor,
        'badge_bg' => $badgeBg,
        'badge_text' => $badgeText,
        'has_shift' => ($shift !== null)
    ];
}

// Get employees for swap dropdown
try {
    $shiftEmployees = $db->query("
        SELECT id, full_name, 
               CASE 
                   WHEN shift_id = 1 THEN 'Morning Shift'
                   WHEN shift_id = 2 THEN 'Afternoon Shift'
                   WHEN shift_id = 3 THEN 'Graveyard Shift'
                   ELSE 'No Shift'
               END as current_shift
        FROM employees
        WHERE id != ? AND status IN ('Active', 'Regular', 'Probationary')
        ORDER BY full_name
        LIMIT 10
    ", [$employeeId])->find();
} catch (\Throwable $th) {
    $shiftEmployees = [];
    error_log("Error fetching employees: " . $th->getMessage());
}

// Get recent shift requests
try {
    $shiftRecentRequests = $db->query("
        SELECT 
            id,
            DATE_FORMAT(created_at, '%b %e, %Y') as request_date,
            'Swap Shift' as request_type,
            'Morning' as from_shift,
            'Afternoon' as to_shift,
            reason,
            status,
            CASE 
                WHEN status = 'Approved' THEN 'bg-green-50 text-green-600'
                WHEN status = 'Pending' THEN 'bg-yellow-50 text-yellow-600'
                WHEN status = 'Rejected' THEN 'bg-gray-100 text-gray-600'
                ELSE 'bg-gray-50 text-gray-600'
            END as status_class
        FROM shift_requests
        WHERE employee_id = ?
        ORDER BY created_at DESC
        LIMIT 5
    ", [$employeeId])->find();
} catch (\Throwable $th) {
    // If table doesn't exist, provide sample data
    $shiftRecentRequests = [
        [
            'request_date' => 'Mar 15, 2025',
            'request_type' => 'Swap Shift',
            'from_shift' => 'Morning',
            'to_shift' => 'Afternoon',
            'reason' => 'Family commitment',
            'status' => 'Approved',
            'status_class' => 'bg-green-50 text-green-600'
        ],
        [
            'request_date' => 'Mar 10, 2025',
            'request_type' => 'Time Off',
            'from_shift' => 'Mar 25',
            'to_shift' => 'Mar 27',
            'reason' => 'Vacation',
            'status' => 'Pending',
            'status_class' => 'bg-yellow-50 text-yellow-600'
        ],
        [
            'request_date' => 'Mar 8, 2025',
            'request_type' => 'Change Shift',
            'from_shift' => 'Graveyard',
            'to_shift' => 'Morning',
            'reason' => 'Health reasons',
            'status' => 'Rejected',
            'status_class' => 'bg-gray-100 text-gray-600'
        ]
    ];
    error_log("Error fetching shift requests: " . $th->getMessage());
}

// Format time function
function formatShiftTime($time)
{
    return date('g:i A', strtotime($time));
}

//schedule
// ============================================
// SHIFT SWAP MANAGEMENT SECTION
// ============================================

// Get employees for swap dropdown (EXCLUDING current employee)
try {
    $shiftEmployees = $db->query("
        SELECT 
            e.id, 
            e.full_name,
            s.id as shift_id,
            s.shift_name as current_shift,
            CONCAT(s.shift_name, ' (', DATE_FORMAT(s.start_time, '%h:%i %p'), ' - ', DATE_FORMAT(s.end_time, '%h:%i %p'), ')') as shift_display
        FROM employees e
        LEFT JOIN shifts s ON e.shift_id = s.id
        WHERE e.id != ? 
            AND e.status IN ('Active', 'Regular', 'Probationary', 'Onboarding')
        ORDER BY e.full_name
    ", [$employeeId])->find();
} catch (\Throwable $th) {
    $shiftEmployees = [];
    error_log("Error fetching employees for swap: " . $th->getMessage());
}

// Get swap requests SENT by this employee
try {
    $sentSwapRequests = $db->query("
        SELECT 
            ssr.*,
            e.full_name as swap_with_name,
            s.shift_name as requester_shift_name,
            es.shift_name as swap_with_shift_name,
            DATE_FORMAT(ssr.swap_date, '%M %e, %Y') as formatted_swap_date,
            DATE_FORMAT(ssr.created_at, '%M %e, %Y') as formatted_created_date,
            CASE 
                WHEN ssr.status = 'Pending' THEN 'bg-yellow-50 text-yellow-600'
                WHEN ssr.status = 'Approved' THEN 'bg-green-50 text-green-600'
                WHEN ssr.status = 'Rejected' THEN 'bg-red-50 text-red-600'
                WHEN ssr.status = 'Cancelled' THEN 'bg-gray-50 text-gray-600'
            END as status_class
        FROM shift_swap_requests ssr
        LEFT JOIN employees e ON ssr.swap_with_employee_id = e.id
        LEFT JOIN shifts s ON ssr.requester_shift_id = s.id
        LEFT JOIN shifts es ON ssr.swap_with_shift_id = es.id
        WHERE ssr.requester_employee_id = ?
        ORDER BY ssr.created_at DESC
        LIMIT 5
    ", [$employeeId])->find();
} catch (\Throwable $th) {
    $sentSwapRequests = [];
    error_log("Error fetching sent swap requests: " . $th->getMessage());
}

// Get swap requests RECEIVED by this employee (for approval)
try {
    $receivedSwapRequests = $db->query("
        SELECT 
            ssr.*,
            e.full_name as requester_name,
            s.shift_name as requester_shift_name,
            es.shift_name as swap_with_shift_name,
            DATE_FORMAT(ssr.swap_date, '%M %e, %Y') as formatted_swap_date,
            DATE_FORMAT(ssr.created_at, '%M %e, %Y') as formatted_created_date
        FROM shift_swap_requests ssr
        LEFT JOIN employees e ON ssr.requester_employee_id = e.id
        LEFT JOIN shifts s ON ssr.requester_shift_id = s.id
        LEFT JOIN shifts es ON ssr.swap_with_shift_id = es.id
        WHERE ssr.swap_with_employee_id = ? AND ssr.status = 'Pending'
        ORDER BY ssr.created_at DESC
    ", [$employeeId])->find();
} catch (\Throwable $th) {
    $receivedSwapRequests = [];
    error_log("Error fetching received swap requests: " . $th->getMessage());
}


// ============================================
// SCHEDULE MANAGEMENT SECTION
// ============================================

// Get current week
$scheduleCurrentDate = date('Y-m-d');
$scheduleWeekOffset = isset($_GET['schedule_week']) ? (int) $_GET['schedule_week'] : 0;

// Calculate week start (Monday) and end (Sunday) based on offset
$scheduleWeekStart = date('Y-m-d', strtotime("monday this week " . ($scheduleWeekOffset > 0 ? "+$scheduleWeekOffset weeks" : ($scheduleWeekOffset < 0 ? "$scheduleWeekOffset weeks" : ""))));
$scheduleWeekEnd = date('Y-m-d', strtotime("sunday this week " . ($scheduleWeekOffset > 0 ? "+$scheduleWeekOffset weeks" : ($scheduleWeekOffset < 0 ? "$scheduleWeekOffset weeks" : ""))));

$scheduleWeekNumber = date('W', strtotime($scheduleWeekStart));
$scheduleWeekYear = date('Y', strtotime($scheduleWeekStart));
$scheduleWeekLabel = date('M j', strtotime($scheduleWeekStart)) . ' - ' . date('M j, Y', strtotime($scheduleWeekEnd));

// Get all shifts for reference
try {
    $scheduleAllShifts = $db->query("
        SELECT id, shift_name, shift_code, start_time, end_time
        FROM shifts
        ORDER BY start_time ASC
    ")->find();

    // Create a map for easy lookup
    $scheduleShiftMap = [];
    foreach ($scheduleAllShifts as $shift) {
        $scheduleShiftMap[$shift['id']] = $shift;
    }
} catch (\Throwable $th) {
    $scheduleAllShifts = [];
    $scheduleShiftMap = [];
    error_log("Error fetching shifts: " . $th->getMessage());
}

// Get employee's schedule for the week
try {
    $scheduleEmployeeShifts = $db->query("
        SELECT 
            es.*,
            s.shift_name,
            s.start_time,
            s.end_time,
            s.shift_code,
            DATE_FORMAT(es.schedule_date, '%a') as day_abbr,
            DATE_FORMAT(es.schedule_date, '%b %e') as formatted_date,
            DAYOFWEEK(es.schedule_date) as day_of_week
        FROM employee_schedules es
        LEFT JOIN shifts s ON es.shift_id = s.id
        WHERE es.employee_id = ? 
        AND es.schedule_date BETWEEN ? AND ?
        AND es.status = 'scheduled'
        ORDER BY es.schedule_date ASC
    ", [$employeeId, $scheduleWeekStart, $scheduleWeekEnd])->find();

    // Create a map by date for easy access
    $scheduleByDate = [];
    foreach ($scheduleEmployeeShifts as $shift) {
        $scheduleByDate[$shift['schedule_date']] = $shift;
    }
} catch (\Throwable $th) {
    $scheduleEmployeeShifts = [];
    $scheduleByDate = [];
    error_log("Error fetching employee schedule: " . $th->getMessage());
}

// Generate the week days (Monday to Sunday)
$scheduleWeekDays = [];
$scheduleHasAnyShift = false;

for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime($scheduleWeekStart . " +$i days"));
    $dayName = date('l', strtotime($date));
    $dayAbbr = date('D', strtotime($date));
    $formattedDate = date('M j', strtotime($date));

    $shiftData = $scheduleByDate[$date] ?? null;

    if ($shiftData) {
        $scheduleHasAnyShift = true;
    }

    $scheduleWeekDays[] = [
        'date' => $date,
        'day_name' => $dayName,
        'day_abbr' => $dayAbbr,
        'formatted_date' => $formattedDate,
        'has_shift' => !empty($shiftData),
        'shift' => $shiftData,
        'is_today' => ($date == date('Y-m-d')),
        'is_past' => ($date < date('Y-m-d')),
        'is_future' => ($date > date('Y-m-d'))
    ];
}

// Get upcoming shifts (next 7 days from today)
try {
    $scheduleUpcomingShifts = $db->query("
        SELECT 
            es.*,
            s.shift_name,
            s.start_time,
            s.end_time,
            s.shift_code,
            DATE_FORMAT(es.schedule_date, '%b %e, %Y') as display_date,
            CASE 
                WHEN es.schedule_date = CURDATE() + INTERVAL 1 DAY THEN 'Tomorrow'
                ELSE DATE_FORMAT(es.schedule_date, '%b %e, %Y')
            END as display_label
        FROM employee_schedules es
        LEFT JOIN shifts s ON es.shift_id = s.id
        WHERE es.employee_id = ? 
        AND es.schedule_date > CURDATE()
        AND es.status = 'scheduled'
        ORDER BY es.schedule_date ASC
        LIMIT 5
    ", [$employeeId])->find();
} catch (\Throwable $th) {
    $scheduleUpcomingShifts = [];
    error_log("Error fetching upcoming shifts: " . $th->getMessage());
}

// Format time function
function formatScheduleTime($time)
{
    return $time ? date('g:i A', strtotime($time)) : '';
}

// Get shift badge color based on shift name
function getShiftBadgeColor($shiftName)
{
    $shiftName = strtolower($shiftName ?? '');
    if (strpos($shiftName, 'morning') !== false) {
        return 'bg-blue-50 text-blue-600';
    } elseif (strpos($shiftName, 'afternoon') !== false) {
        return 'bg-amber-50 text-amber-600';
    } elseif (strpos($shiftName, 'graveyard') !== false || strpos($shiftName, 'night') !== false) {
        return 'bg-purple-50 text-purple-600';
    } else {
        return 'bg-gray-50 text-gray-600';
    }
}

// Get shift dot color based on shift name
function getShiftDotColor($shiftName)
{
    $shiftName = strtolower($shiftName ?? '');
    if (strpos($shiftName, 'morning') !== false) {
        return 'bg-blue-500';
    } elseif (strpos($shiftName, 'afternoon') !== false) {
        return 'bg-amber-500';
    } elseif (strpos($shiftName, 'graveyard') !== false || strpos($shiftName, 'night') !== false) {
        return 'bg-purple-500';
    } else {
        return 'bg-gray-500';
    }
}

// Get shift icon based on shift name
function getShiftIcon($shiftName)
{
    $shiftName = strtolower($shiftName ?? '');
    if (strpos($shiftName, 'morning') !== false) {
        return 'fa-sun';
    } elseif (strpos($shiftName, 'afternoon') !== false) {
        return 'fa-moon';
    } elseif (strpos($shiftName, 'graveyard') !== false || strpos($shiftName, 'night') !== false) {
        return 'fa-moon-stars';
    } else {
        return 'fa-clock';
    }
}

// Determine if we're on current week
$scheduleIsCurrentWeek = ($scheduleWeekOffset == 0);

// ============================================
// MY MENTEES SECTION (for mentors)
// ============================================

// Get current mentor's ID (assuming logged in user is a mentor)
$mentorId = $employeeId; // From your session/authentication

// Get mentees assigned to this mentor
try {
    $mentorMentees = $db->query("
        SELECT 
            ma.*,
            mentee.id as mentee_id,
            mentee.full_name as mentee_name,
            mentee.position as mentee_position,
            mentee.department,
            mentee.hired_date,
            mentee.status,
            mentee.employee_number,
            TIMESTAMPDIFF(MONTH, mentee.hired_date, CURDATE()) as months_employed,
            DATE_FORMAT(ma.created_at, '%M %Y') as started_since,
            -- Get average rating for this mentee
            COALESCE((
                SELECT AVG(rating) FROM mentor_ratings 
                WHERE mentee_employee_id = mentee.id 
                AND mentor_employee_id = ?
            ), 0) as avg_rating,
            -- Get latest rating comment
            (
                SELECT comment FROM mentor_ratings 
                WHERE mentee_employee_id = mentee.id 
                AND mentor_employee_id = ?
                ORDER BY created_at DESC LIMIT 1
            ) as latest_comment,
            -- Count total ratings given
            (
                SELECT COUNT(*) FROM mentor_ratings 
                WHERE mentee_employee_id = mentee.id 
                AND mentor_employee_id = ?
            ) as rating_count,
            -- Determine status badge
            CASE 
                WHEN mentee.status = 'Probationary' AND TIMESTAMPDIFF(MONTH, mentee.hired_date, CURDATE()) >= 3 THEN 'Ready for Promotion'
                WHEN mentee.status = 'Probationary' AND TIMESTAMPDIFF(MONTH, mentee.hired_date, CURDATE()) < 3 THEN 'Probationary'
                WHEN mentee.status = 'Regular' THEN 'Regular'
                ELSE mentee.status
            END as status_badge,
            CONCAT(LEFT(mentee.full_name, 1), COALESCE(RIGHT(LEFT(mentee.full_name, INSTR(mentee.full_name, ' ') + 1), 1), RIGHT(mentee.full_name, 1))) as initials,
            CASE 
                WHEN (SELECT AVG(rating) FROM mentor_ratings WHERE mentee_employee_id = mentee.id AND mentor_employee_id = ?) >= 4.5 THEN 'bg-gradient-to-br from-amber-400 to-amber-500'
                WHEN (SELECT AVG(rating) FROM mentor_ratings WHERE mentee_employee_id = mentee.id AND mentor_employee_id = ?) >= 3.5 THEN 'bg-gradient-to-br from-blue-400 to-blue-500'
                WHEN (SELECT AVG(rating) FROM mentor_ratings WHERE mentee_employee_id = mentee.id AND mentor_employee_id = ?) >= 2.5 THEN 'bg-gradient-to-br from-green-400 to-green-500'
                ELSE 'bg-gradient-to-br from-purple-400 to-purple-500'
            END as gradient_class
        FROM mentor_assignments ma
        JOIN employees mentee ON ma.mentee_employee_id = mentee.id
        WHERE ma.mentor_employee_id = ? AND ma.status = 'Active'
        ORDER BY mentee.full_name
    ", [$mentorId, $mentorId, $mentorId, $mentorId, $mentorId, $mentorId, $mentorId])->find();
} catch (\Throwable $th) {
    $mentorMentees = [];
    error_log("Error fetching mentees: " . $th->getMessage());
}

// Get stats
$mentorTotalMentees = count($mentorMentees);
$mentorRatedThisMonth = 0;
$mentorTotalRating = 0;
$mentorRatingCount = 0;

foreach ($mentorMentees as $mentee) {
    if ($mentee['rating_count'] > 0) {
        $mentorRatingCount++;
        $mentorTotalRating += $mentee['avg_rating'];
    }

    // Check if rated this month
    $ratedThisMonth = $db->query("
        SELECT COUNT(*) as count FROM mentor_ratings 
        WHERE mentee_employee_id = ? AND mentor_employee_id = ?
        AND MONTH(rating_date) = MONTH(CURDATE())
        AND YEAR(rating_date) = YEAR(CURDATE())
    ", [$mentee['mentee_id'], $mentorId])->fetch_one()['count'] ?? 0;

    if ($ratedThisMonth > 0) {
        $mentorRatedThisMonth++;
    }
}

$mentorAverageRating = $mentorRatingCount > 0 ? round($mentorTotalRating / $mentorRatingCount, 1) : 0;

// Get recent ratings given by this mentor
try {
    $mentorRecentRatings = $db->query("
        SELECT 
            mr.*,
            mentee.full_name as mentee_name,
            mentee.position,
            DATE_FORMAT(mr.rating_date, '%b %e, %Y') as formatted_date,
            DATE_FORMAT(mr.created_at, '%b %e, %Y') as formatted_created,
            CONCAT(LEFT(mentee.full_name, 1), COALESCE(RIGHT(LEFT(mentee.full_name, INSTR(mentee.full_name, ' ') + 1), 1), RIGHT(mentee.full_name, 1))) as initials,
            DATEDIFF(CURDATE(), mr.rating_date) as days_ago
        FROM mentor_ratings mr
        JOIN employees mentee ON mr.mentee_employee_id = mentee.id
        WHERE mr.mentor_employee_id = ?
        ORDER BY mr.created_at DESC
        LIMIT 5
    ", [$mentorId])->find();
} catch (\Throwable $th) {
    $mentorRecentRatings = [];
    error_log("Error fetching recent ratings: " . $th->getMessage());
}

// Get all mentees for dropdown (for rating form)
try {
    $mentorMenteesDropdown = $db->query("
        SELECT 
            mentee.id,
            mentee.full_name,
            mentee.position,
            mentee.department,
            CONCAT(mentee.full_name, ' - ', mentee.position) as display_name
        FROM mentor_assignments ma
        JOIN employees mentee ON ma.mentee_employee_id = mentee.id
        WHERE ma.mentor_employee_id = ? AND ma.status = 'Active'
        ORDER BY mentee.full_name
    ", [$mentorId])->find();
} catch (\Throwable $th) {
    $mentorMenteesDropdown = [];
    error_log("Error fetching mentees dropdown: " . $th->getMessage());
}

// ============================================
// EMPLOYEE DOCUMENTS UPLOAD SECTION
// ============================================

// Get employee documents from database
$employeeDocuments = [];

// Check each document field
$documentFields = [
    'nbi_clearance' => 'NBI Clearance',
    'medical_result' => 'Medical Result',
    'resume' => 'Resume',
    'birth_certificate' => 'Birth Certificate',
    'sss_document' => 'SSS Document',
    'philhealth_document' => 'PhilHealth Document',
    'pagibig_document' => 'Pag-IBIG Document'
];

foreach ($documentFields as $field => $label) {
    if (!empty($employeeInfo[$field])) {
        $employeeDocuments[] = [
            'type' => $field,
            'label' => $label,
            'url' => $employeeInfo[$field],
            'uploaded_at' => $employeeInfo['updated_at'] ?? date('Y-m-d H:i:s'),
            'icon' => getDocumentIcon($field)
        ];
    }
}

// Sort documents by most recent first (based on updated_at)
usort($employeeDocuments, function ($a, $b) {
    return strtotime($b['uploaded_at']) - strtotime($a['uploaded_at']);
});

// Function to get icon based on document type
function getDocumentIcon($documentType)
{
    return match ($documentType) {
        'nbi_clearance' => 'fa-fingerprint',
        'medical_result' => 'fa-notes-medical',
        'birth_certificate' => 'fa-id-card',
        'resume' => 'fa-id-card',
        'sss_document' => 'fa-id-card',
        'philhealth_document' => 'fa-heartbeat',
        'pagibig_document' => 'fa-home',
        default => 'fa-file'
    };
}

// Function to get color based on document type
function getDocumentColor($documentType)
{
    return match ($documentType) {
        'nbi_clearance' => 'red',
        'medical_result' => 'teal',
        'birth_certificate' => 'green',
        'resume' => 'pink',
        'sss_document' => 'blue',
        'philhealth_document' => 'red',
        'pagibig_document' => 'yellow',
        default => 'gray'
    };
}

// ============================================
// NOTES & RECOGNITION SECTION
// ============================================

// Pagination
$notePage = isset($_GET['note_page']) ? max(1, (int) $_GET['note_page']) : 1;
$notePerPage = 5;
$noteOffset = ($notePage - 1) * $notePerPage;

// Get employee ID from session
$employeeId = $_SESSION['employee']['employee_record_id']['id'] ?? $_SESSION['employee']['employee_record_id'] ?? null;

// ============================================
// NOTES STATS
// ============================================

// Get total recognitions count
try {
    $noteTotalRecognitions = $db->query("
        SELECT COUNT(*) as count 
        FROM employee_recognitions 
        WHERE employee_id = ?
    ", [$employeeId])->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $noteTotalRecognitions = 0;
    error_log("Error fetching recognitions count: " . $th->getMessage());
}

// Get total admin notes count
try {
    $noteTotalNotes = $db->query("
        SELECT COUNT(*) as count 
        FROM admin_notes 
        WHERE employee_id = ? AND status = 'active'
    ", [$employeeId])->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $noteTotalNotes = 0;
    error_log("Error fetching notes count: " . $th->getMessage());
}

// Get new/unread count (notes from last 7 days)
try {
    $noteNewCount = $db->query("
        SELECT COUNT(*) as count 
        FROM admin_notes 
        WHERE employee_id = ? 
        AND status = 'active'
        AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ", [$employeeId])->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $noteNewCount = 0;
    error_log("Error fetching new notes count: " . $th->getMessage());
}

// ============================================
// RECENT ACTIVITY FEED (Paginated)
// ============================================

// Get total activity count for pagination
try {
    $noteTotalActivities = $db->query("
        SELECT COUNT(*) as total FROM (
            SELECT id, 'recognition' as type, created_at FROM employee_recognitions WHERE employee_id = ?
            UNION ALL
            SELECT id, 'note' as type, created_at FROM admin_notes WHERE employee_id = ? AND status = 'active'
        ) as activities
    ", [$employeeId, $employeeId])->fetch_one()['total'] ?? 0;
} catch (\Throwable $th) {
    $noteTotalActivities = 0;
    error_log("Error fetching total activities: " . $th->getMessage());
}

// Get paginated activity feed
try {
    $noteActivities = $db->query("
        (SELECT 
            'recognition' as type,
            id,
            recognition_type as title,
            performance_highlight as content,
            recognized_by as author_id,
            NULL as author_name,
            recognition_date as activity_date,
            created_at
        FROM employee_recognitions 
        WHERE employee_id = ?)
        
        UNION ALL
        
        (SELECT 
            'note' as type,
            id,
            note_title as title,
            note_content as content,
            created_by as author_id,
            (SELECT full_name FROM employees WHERE id = created_by) as author_name,
            created_at as activity_date,
            created_at
        FROM admin_notes 
        WHERE employee_id = ? AND status = 'active')
        
        ORDER BY created_at DESC
        LIMIT $notePerPage OFFSET $noteOffset
    ", [$employeeId, $employeeId])->find();
} catch (\Throwable $th) {
    $noteActivities = [];
    error_log("Error fetching activities: " . $th->getMessage());
}

$noteTotalPages = ceil($noteTotalActivities / $notePerPage);

// Format time difference function
function noteTimeAgo($datetime)
{
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) {
        return $diff . 's ago';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . 'm ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . 'h ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . 'd ago';
    } else {
        return date('M j', $time);
    }
}

// Add all variables to view
view_path('ess', 'index', [
    'tasks' => $limitedTasks,
    'allTasks' => $allTasks,
    'employeeInfo' => $employeeInfo,
    'employeeAccount' => $employeeAccount,
    'employeeBenefits' => $employeeBenefits,
    'taskStats' => $taskStats,
    'upcomingTasks' => $upcomingTasks,
    'recentLeaveRequests' => $recentLeaveRequests,
    'allLeaveRequests' => $allLeaveRequests,
    'leaveStats' => $leaveStats,
    'leaveBalances' => $leaveBalances,
    // Attendance variables
    'attendanceStatus' => $attendanceStatus,
    'elapsedSeconds' => $elapsedSeconds,
    'pauseTotal' => $pauseTotal,
    'showClockIn' => $showClockIn,
    'currentAttendance' => $currentAttendance,
    'employeeShift' => $employeeShift,
    'periodName' => $periodName,
    'currentPeriod' => $currentPeriod,
    'weekStats' => $weekStats,
    'monthStats' => $monthStats,
    'absences' => $absences,
    'weekTotal' => $weekTotal,
    'monthTotal' => $monthTotal,
    'currentMonthName' => $currentMonthName,
    'recentAttendance' => $recentAttendance,
    'dailyBreakdown' => $dailyBreakdown,
    'expectedWorkDays' => $expectedWorkDays,
    'formatHoursMinutes' => 'formatHoursMinutes',
    'formatMinutes' => 'formatMinutes',

    'allTimeStats' => $allTimeStats,
    'attendanceByMonth' => $attendanceByMonth,
    'allAttendanceRecords' => $allAttendanceRecords,
    'totalPages' => $totalPages,
    'currentPage' => $page,
    'attendanceRate' => $attendanceRate,
    'workingDaysSince' => $workingDaysSince,


    //claims
    'claimsPage' => $claimsPage,
    'claimsPerPage' => $claimsPerPage,
    'totalClaimsPages' => $totalClaimsPages,
    'totalClaims' => $totalClaims,
    'claimsStatusFilter' => $claimsStatusFilter,
    'claimsPeriodFilter' => $claimsPeriodFilter,
    'claimsList' => $claimsList,
    'claimsActiveTab' => $claimsActiveTab,

    // SHIFT & SCHEDULE VARIABLES
    'shiftCurrentDate' => $shiftCurrentDate,
    'shiftCurrentWeek' => $shiftCurrentWeek,
    'shiftWeekStart' => $shiftWeekStart,
    'shiftWeekEnd' => $shiftWeekEnd,
    'shiftWeekLabel' => $shiftWeekLabel,
    'shiftWeekNumber' => $shiftWeekNumber,
    'shiftAllShifts' => $shiftAllShifts,
    'shiftEmployeeShift' => $shiftEmployeeShift,
    'shiftWeekSchedule' => $shiftWeekSchedule,
    'shiftDays' => $shiftDays,
    'shiftUpcoming' => $shiftUpcoming,
    'shiftEmployees' => $shiftEmployees,
    'shiftRecentRequests' => $shiftRecentRequests,

    // SCHEDULE MANAGEMENT VARIABLES
    'scheduleCurrentDate' => $scheduleCurrentDate,
    'scheduleWeekOffset' => $scheduleWeekOffset,
    'scheduleWeekStart' => $scheduleWeekStart,
    'scheduleWeekEnd' => $scheduleWeekEnd,
    'scheduleWeekNumber' => $scheduleWeekNumber,
    'scheduleWeekYear' => $scheduleWeekYear,
    'scheduleWeekLabel' => $scheduleWeekLabel,
    'scheduleWeekDays' => $scheduleWeekDays,
    'scheduleHasAnyShift' => $scheduleHasAnyShift,
    'scheduleIsCurrentWeek' => $scheduleIsCurrentWeek,
    'scheduleUpcomingShifts' => $scheduleUpcomingShifts,
    'scheduleAllShifts' => $scheduleAllShifts,
    'scheduleShiftMap' => $scheduleShiftMap,
    'scheduleEmployeeShifts' => $scheduleEmployeeShifts,
    'scheduleByDate' => $scheduleByDate,
    'receivedSwapRequests' => $receivedSwapRequests,
    'sentSwapRequests' => $sentSwapRequests,

    // MENTOR RATING VARIABLES
    'mentorMentees' => $mentorMentees,
    'mentorTotalMentees' => $mentorTotalMentees,
    'mentorRatedThisMonth' => $mentorRatedThisMonth,
    'mentorAverageRating' => $mentorAverageRating,
    'mentorRecentRatings' => $mentorRecentRatings,
    'mentorMenteesDropdown' => $mentorMenteesDropdown,

    'employeeDocuments' => $employeeDocuments,

    // NOTES & RECOGNITION VARIABLES
    'notePage' => $notePage,
    'notePerPage' => $notePerPage,
    'noteTotalPages' => $noteTotalPages,
    'noteTotalActivities' => $noteTotalActivities,
    'noteTotalRecognitions' => $noteTotalRecognitions,
    'noteTotalNotes' => $noteTotalNotes,
    'noteNewCount' => $noteNewCount,
    'noteActivities' => $noteActivities,
]);