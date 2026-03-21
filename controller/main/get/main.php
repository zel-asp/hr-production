<?php

use Core\Database;

require base_path("core/middleware/adminAuth.php");

$config = require base_path('config/config.php');
$db = new Database($config['database']);

// ============================================
// JOB POSTINGS SECTION
// ============================================
try {
    $jobPostings = $db->query(
        "SELECT id, position, department, location, shift, salary, created_at 
        FROM job_postings ORDER BY created_at DESC"
    )->find();
} catch (\Throwable $th) {
    $jobPostings = [];
    error_log($th->getMessage());
}

// ============================================
// APPLICANTS SECTION
// ============================================
try {
    $allApplicants = $db->query(
        "SELECT id, full_name, email, phone, position, experience,contract_signing_date, education, skills, resume_path, cover_note, status, hired_date, start_date, created_at, age, gender, shift, rate_per_hour
        FROM applicants ORDER BY created_at DESC"
    )->find();
} catch (\Throwable $th) {
    $allApplicants = [];
    error_log($th->getMessage());
}

try {
    $recentApplicants = $db->query(
        "SELECT id, full_name, email, phone, position, experience, education, skills, resume_path, cover_note, status, created_at 
        FROM applicants ORDER BY created_at DESC LIMIT 5"
    )->find();
} catch (\Throwable $th) {
    $recentApplicants = [];
    error_log($th->getMessage());
}

// ============================================
// EMPLOYEES SECTION
// ============================================
try {
    $hiredEmployees = $db->query(
        "SELECT id, employee_number, full_name, position, department, hired_date, start_date, role, hourly_rate
        FROM employees
        ORDER BY hired_date DESC"
    )->find();
} catch (\Throwable $th) {
    $hiredEmployees = [];
    error_log($th->getMessage());
}

try {
    $employeesForBenefits = $db->query(
        "SELECT id, full_name, employee_number, position, department, status, benefit_status 
        FROM employees 
        WHERE status = 'Active' OR status = 'Onboarding' OR status = 'Probationary'
        ORDER BY full_name ASC"
    )->find();
} catch (\Throwable $th) {
    $employeesForBenefits = [];
    error_log("Error fetching employees for benefits: " . $th->getMessage());
}
// ============================================
// READY FOR CONTRACT SIGNING SECTION
// ============================================

// Pagination
$contractReadyPage = isset($_GET['contract_page']) ? max(1, (int) $_GET['contract_page']) : 1;
$contractReadyPerPage = 10;
$contractReadyOffset = ($contractReadyPage - 1) * $contractReadyPerPage;

// Filter parameters
$contractReadySearch = isset($_GET['contract_search']) ? $_GET['contract_search'] : '';
$contractReadyDept = isset($_GET['contract_dept']) ? $_GET['contract_dept'] : '';

// Build WHERE clause for filters
$contractWhereConditions = [];
$contractParams = [];

// Only get applicants with status 'Contract' and NOT in employees table
$contractWhereConditions[] = "a.status = 'Contract'";
$contractWhereConditions[] = "NOT EXISTS (SELECT 1 FROM employees e WHERE e.applicant_id = a.id)";

if (!empty($contractReadySearch)) {
    $contractWhereConditions[] = "(a.full_name LIKE ? OR a.email LIKE ? OR a.position LIKE ?)";
    $searchTerm = "%$contractReadySearch%";
    $contractParams[] = $searchTerm;
    $contractParams[] = $searchTerm;
    $contractParams[] = $searchTerm;
}

if (!empty($contractReadyDept)) {
    $contractWhereConditions[] = "a.department = ?";
    $contractParams[] = $contractReadyDept;
}

$contractWhereClause = "WHERE " . implode(" AND ", $contractWhereConditions);

// Get total count for pagination
try {
    $totalContractReady = $db->query("
        SELECT COUNT(DISTINCT a.id) as count
        FROM applicants a
        $contractWhereClause
    ", $contractParams)->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalContractReady = 0;
    error_log("Error counting contract ready applicants: " . $th->getMessage());
}

// Get applicants with contract status and contract data
try {
    $contractReadyApplicants = $db->query("
        SELECT 
            a.id,
            a.full_name,
            a.email,
            a.phone,
            a.position,
            a.department,
            a.rate_per_hour,
            a.experience,
            a.education,
            a.skills,
            a.hired_date,
            a.start_date,
            a.interview_date,
            a.resume_path,
            a.created_at,
            a.age,
            a.gender,
            sc.id as contract_id,
            sc.contract_date,
            sc.contract_time,
            sc.contract_location,
            sc.contract_notes,
            DATE_FORMAT(sc.contract_date, '%M %d, %Y') as formatted_contract_date,
            DATE_FORMAT(sc.contract_time, '%h:%i %p') as formatted_contract_time,
            CASE 
                WHEN sc.id IS NULL THEN 'not_scheduled'
                WHEN sc.contract_date < CURDATE() THEN 'done'
                ELSE 'scheduled'
            END as contract_status,
            CASE 
                WHEN sc.id IS NULL THEN 'bg-gray-100 text-gray-600 border-gray-200'
                WHEN sc.contract_date < CURDATE() THEN 'bg-red-50 text-red-600 border-red-200'
                ELSE 'bg-green-50 text-green-600 border-green-200'
            END as status_class,
            CASE 
                WHEN sc.id IS NULL THEN 'Not Scheduled'
                WHEN sc.contract_date < CURDATE() THEN 'Done'
                ELSE 'Scheduled'
            END as status_text,
            CONCAT(LEFT(a.full_name, 1), COALESCE(RIGHT(LEFT(a.full_name, INSTR(a.full_name, ' ') + 1), 1), RIGHT(a.full_name, 1))) as initials
        FROM applicants a
        LEFT JOIN schedule_contract sc ON a.id = sc.applicant_id
        $contractWhereClause
        ORDER BY 
            CASE 
                WHEN sc.id IS NULL THEN 1  -- Not scheduled first
                WHEN sc.contract_date < CURDATE() THEN 2  -- Expired second
                ELSE 3  -- Scheduled last
            END,
            a.created_at DESC
        LIMIT $contractReadyOffset, $contractReadyPerPage
    ", $contractParams)->find();
} catch (\Throwable $th) {
    $contractReadyApplicants = [];
    error_log("Error fetching contract ready applicants: " . $th->getMessage());
}

$totalContractReadyPages = ceil($totalContractReady / $contractReadyPerPage);

// Get distinct departments for filter
try {
    $contractReadyDepartments = $db->query("
        SELECT DISTINCT department 
        FROM applicants 
        WHERE department IS NOT NULL AND department != ''
        ORDER BY department
    ")->find();
} catch (\Throwable $th) {
    $contractReadyDepartments = [];
    error_log("Error fetching departments: " . $th->getMessage());
}
// ============================================
// TASKS SECTION
// ============================================
try {
    $tasks = $db->query(
        "SELECT t.*, e.full_name, e.position, e.start_date, e.hired_date 
        FROM tasks t 
        JOIN employees e ON t.assigned_to = e.id
        ORDER BY 
            CASE 
                WHEN t.due_date < CURDATE() AND t.status != 'Completed' THEN 1
                WHEN t.status = 'Not Started' THEN 2
                WHEN t.status = 'Ongoing' THEN 3
                WHEN t.status = 'Completed' THEN 4
            END,
            t.due_date ASC"
    )->find();
} catch (\Throwable $th) {
    $tasks = [];
    error_log($th->getMessage());
}

try {
    $applicantTasks = $db->query(
        "SELECT 
            t.*,
            e.full_name,
            e.position,
            DATEDIFF(t.due_date, CURDATE()) as days_difference,
            CASE 
                WHEN t.status = 'Completed' THEN 'completed'
                WHEN t.due_date < CURDATE() AND t.status != 'Completed' THEN 'overdue'
                WHEN t.status = 'Ongoing' THEN 'ongoing'
                ELSE 'not_started'
            END as task_status_display,
            CASE 
                WHEN t.due_date < CURDATE() AND t.status != 'Completed' THEN 1
                WHEN t.due_date = CURDATE() AND t.status != 'Completed' THEN 2
                WHEN t.due_date > CURDATE() AND t.due_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY) AND t.status != 'Completed' THEN 3
                ELSE 4
            END as urgency
        FROM tasks t
        JOIN employees e ON t.assigned_to = e.id
        ORDER BY urgency ASC, t.due_date ASC"
    )->find();
} catch (\Throwable $th) {
    $applicantTasks = [];
    error_log($th->getMessage());
}
// ============================================
// ONBOARDING SECTION
// ============================================
// Onboarding Pagination
$obPage = isset($_GET['ob_page']) ? max(1, (int) $_GET['ob_page']) : 1;
$obPerPage = 3;
$obOffset = ($obPage - 1) * $obPerPage;


// Total onboarding employees
try {
    $totalOnboardingCount = $db->query(
        "SELECT COUNT(*) as count
        FROM employees
        WHERE onboarding_status != 'Onboarded'
        AND status = 'Probationary'
        AND role = 'employee'"
    )->fetch_one()['count'] ?? 0;

} catch (\Throwable $th) {
    $totalOnboardingCount = 0;
}


// Paginated onboarding data
try {
    $onboardingTasks = $db->query(
        "SELECT 
            e.id AS employee_id,
            e.employee_number,
            e.full_name,
            e.position,
            e.department,
            e.hired_date,
            e.start_date,
            e.onboarding_status,
            COUNT(t.id) AS total_tasks,
            SUM(CASE WHEN t.status = 'Completed' THEN 1 ELSE 0 END) AS completed_tasks
        FROM employees e
        LEFT JOIN tasks t ON e.id = t.assigned_to
        WHERE e.onboarding_status != 'Onboarded'
        AND e.status = 'Probationary'
        AND e.role = 'employee'
        GROUP BY e.id
        ORDER BY e.hired_date DESC
        LIMIT $obPerPage OFFSET $obOffset"
    )->find();

} catch (\Throwable $th) {
    $onboardingTasks = [];
}

// Total pages
$totalOnboardingPages = ceil($totalOnboardingCount / $obPerPage);

// Get unique departments for onboarding filter
try {
    $departments = $db->query(
        "SELECT DISTINCT department
        FROM employees
        WHERE department IS NOT NULL
        AND department != ''
        ORDER BY department"
    )->find();
} catch (\Throwable $th) {
    $departments = [];
    error_log($th->getMessage());
}

// ============================================
// ONBOARDING STATS SECTION
// ============================================
try {
    $totalOnboarded = $db->query(
        "SELECT COUNT(*) as count 
        FROM employees WHERE onboarding_status = 'Onboarded'"
    )->fetch_one();
} catch (\Throwable $th) {
    $totalOnboarded = ['count' => 0];
}

try {
    $totalInProgress = $db->query(
        "SELECT COUNT(*) as count FROM employees WHERE onboarding_status = 'In Progress'"
    )->fetch_one();
} catch (\Throwable $th) {
    $totalInProgress = ['count' => 0];
}

try {
    $totalPending = $db->query(
        "SELECT COUNT(*) as count 
        FROM employees WHERE onboarding_status = 'Onboarding'"
    )->fetch_one();
} catch (\Throwable $th) {
    $totalPending = ['count' => 0];
}

// ============================================
// PROBATIONARY EMPLOYEES SECTION
// ============================================
try {
    $probationaryEmployees = $db->query(
        "SELECT e.id, e.full_name, e.email, e.position, e.hired_date, e.start_date, e.status 
        FROM employees e
        WHERE e.evaluation_status = 'Pending' 
        ORDER BY e.hired_date DESC"
    )->find();
} catch (\Throwable $th) {
    $probationaryEmployees = [];
    error_log($th->getMessage());
}

// ============================================
// EMPLOYEE ACCOUNTS SECTION
// ============================================
// Fetch hired applicants WITHOUT existing employee account
try {
    $OnboardingAvailableEmployees = $db->query(
        "SELECT a.id, a.full_name, a.email, a.position, a.hired_date, a.start_date, a.department
        FROM applicants a
        WHERE a.status = 'Hired'
        AND NOT EXISTS (
            SELECT 1 
            FROM employee_accounts ea 
            WHERE ea.applicant_id = a.id
        )
        ORDER BY a.hired_date DESC"
    )->find();
} catch (\Throwable $th) {
    $OnboardingAvailableEmployees = [];
    error_log($th->getMessage());
}

// Pagination for Generated Accounts
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = 5;
$offset = ($page - 1) * $perPage;

// Total accounts
try {
    $totalAccountsCount = $db->query("SELECT COUNT(*) as count FROM employee_accounts")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalAccountsCount = 0;
    error_log($th->getMessage());
}

// Accounts for current page
try {
    $generatedAccounts = $db->query(
        "SELECT ea.*, a.full_name, a.position 
        FROM employee_accounts ea
        JOIN applicants a ON ea.applicant_id = a.id
        ORDER BY ea.generated_date DESC
        LIMIT $perPage OFFSET $offset"
    )->find();
} catch (\Throwable $th) {
    $generatedAccounts = [];
    error_log($th->getMessage());
}

// Total pages for pagination
$totalPages = ceil($totalAccountsCount / $perPage);

try {
    $totalAccounts = $db->query("SELECT COUNT(*) as count FROM employee_accounts")->fetch_one();
} catch (\Throwable $th) {
    $totalAccounts = ['count' => 0];
    error_log($th->getMessage());
}

// ============================================
// NEW HIRES SECTION (PAGINATED)
// ============================================
$nhPage = isset($_GET['nh_page']) ? max(1, (int) $_GET['nh_page']) : 1;
$nhPerPage = 10;
$nhOffset = ($nhPage - 1) * $nhPerPage;

// Total new hires
try {
    $totalNewHires = $db->query("SELECT COUNT(*) as count FROM employees")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalNewHires = 0;
}

// Paginated new hires
try {
    $paginatedNewHires = $db->query(
        "SELECT id, employee_number, full_name, position, hired_date, start_date, onboarding_status, department, resume, birth_certificate, nbi_clearance, medical_result
        FROM employees WHERE role = 'employee' ORDER BY hired_date DESC LIMIT $nhPerPage OFFSET $nhOffset"
    )->find();
} catch (\Throwable $th) {
    $paginatedNewHires = [];
}

$totalNewHirePages = ceil($totalNewHires / $nhPerPage);

// ============================================
// EMPLOYEE STATS SECTION
// ============================================
try {
    $totalHired = $db->query("SELECT COUNT(*) as count FROM employees")->fetch_one();
} catch (\Throwable $th) {
    $totalHired = ['count' => 0];
}

// ============================================
// PERFORMANCE EVALUATIONS SECTION
// ============================================
try {
    $recentEvaluations = $db->query("
    SELECT 
        pe.id as evaluation_id,
        pe.overall_score,
        pe.interpretation,
        pe.review_period_end,
        pe.created_at,
        e.id as employee_id,
        e.full_name,
        e.position,
        e.hired_date,
        e.start_date,
        e.status,
        pip.id as pip_id,
        pip.improvement_areas,
        pip.goal1,
        pip.goal2,
        pip.goal3,
        pip.pip_start_date,
        pip.pip_end_date,
        -- Get criteria scores
        MAX(CASE WHEN pcs.criteria_number = 1 THEN pcs.score END) as criteria_1_score,
        MAX(CASE WHEN pcs.criteria_number = 2 THEN pcs.score END) as criteria_2_score,
        MAX(CASE WHEN pcs.criteria_number = 3 THEN pcs.score END) as criteria_3_score,
        MAX(CASE WHEN pcs.criteria_number = 4 THEN pcs.score END) as criteria_4_score,
        MAX(CASE WHEN pcs.criteria_number = 5 THEN pcs.score END) as criteria_5_score,
        -- Determine if improvement needed based on overall score
        CASE 
            WHEN pe.overall_score < 3.0 THEN 'Improvement'
            ELSE 'Meet'
        END as improvement_status
    FROM performance_evaluations pe
    JOIN employees e ON pe.employee_id = e.id
    LEFT JOIN performance_improvement_plans pip 
        ON pip.evaluation_id = pe.id
    LEFT JOIN performance_criteria_scores pcs 
        ON pcs.evaluation_id = pe.id
    GROUP BY pe.id
    ORDER BY pe.created_at DESC
    LIMIT 10
")->find();
} catch (\Throwable $th) {
    $recentEvaluations = [];
    error_log($th->getMessage());
}

// ============================================
// PERFORMANCE STATS SECTION
// ============================================
$pendingCount = $db->query("
    SELECT COUNT(*) AS count
    FROM employees
    WHERE evaluation_status = 'Pending'
")->fetch_one();

// Get ready for regular count (employees with good evaluations)
$needImprovement = $db->query("
    SELECT COUNT(*) AS count
    FROM performance_evaluations
    WHERE overall_score < 3.5
")->fetch_one();

$highScoreCount = $db->query("
    SELECT COUNT(*) AS count
    FROM performance_evaluations
    WHERE overall_score > 3.5
")->fetch_one();

// Get active PIP count
$activePipCount = $db->query("
    SELECT COUNT(*) AS count
    FROM performance_improvement_plans
")->fetch_one();

// ============================================
// BENEFITS SECTION
// ============================================
try {
    $benefitProviders = $db->query(
        "SELECT id, provider_name, contact_info
        FROM benefit_providers 
        ORDER BY provider_name ASC"
    )->find();
} catch (\Throwable $th) {
    $benefitProviders = [];
    error_log("Error fetching benefit providers: " . $th->getMessage());
}


// ============================================
// LEAVE MANAGEMENT SECTION
// ============================================

// Get leave counts by type (count of requests, not days)
try {
    $vacationLeaveCount = $db->query("
        SELECT COUNT(*) as count 
        FROM leave_requests 
        WHERE leave_type = 'Annual Leave' AND status = 'Approved'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $vacationLeaveCount = 0;
    error_log("Error fetching vacation leave count: " . $th->getMessage());
}

try {
    $sickLeaveCount = $db->query("
        SELECT COUNT(*) as count 
        FROM leave_requests 
        WHERE leave_type = 'Sick Leave' AND status = 'Approved'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $sickLeaveCount = 0;
    error_log("Error fetching sick leave count: " . $th->getMessage());
}

try {
    $emergencyLeaveCount = $db->query("
        SELECT COUNT(*) as count 
        FROM leave_requests 
        WHERE leave_type = 'Personal Day' AND status = 'Approved'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $emergencyLeaveCount = 0;
    error_log("Error fetching emergency leave count: " . $th->getMessage());
}

// Get count of pending leave requests
try {
    $pendingLeaveCount = $db->query("
        SELECT COUNT(*) as count 
        FROM leave_requests 
        WHERE status = 'Pending'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $pendingLeaveCount = 0;
    error_log("Error fetching pending leave count: " . $th->getMessage());
}

// ============================================
// PENDING LEAVE REQUESTS PAGINATION
// ============================================
$pendingPage = isset($_GET['pending_page']) ? max(1, (int) $_GET['pending_page']) : 1;
$pendingPerPage = 5;
$pendingOffset = ($pendingPage - 1) * $pendingPerPage;

// Get total count of pending leave requests for pagination
try {
    $totalPendingRequests = $db->query("
        SELECT COUNT(*) as count 
        FROM leave_requests 
        WHERE status = 'Pending'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalPendingRequests = 0;
    error_log("Error fetching total pending requests: " . $th->getMessage());
}

// Get paginated pending leave requests with employee details
try {
    $pendingLeaveRequests = $db->query("
        SELECT 
            lr.*,
            e.full_name,
            e.position,
            e.department,
            DATEDIFF(lr.end_date, lr.start_date) + 1 as actual_days
        FROM leave_requests lr
        JOIN employees e ON lr.employee_id = e.id
        WHERE lr.status = 'Pending'
        ORDER BY lr.created_at DESC
        LIMIT $pendingPerPage OFFSET $pendingOffset
    ")->find();
} catch (\Throwable $th) {
    $pendingLeaveRequests = [];
    error_log("Error fetching pending leave requests: " . $th->getMessage());
}

$totalPendingPages = ceil($totalPendingRequests / $pendingPerPage);

// ============================================
// LEAVE HISTORY PAGINATION
// ============================================
$historyPage = isset($_GET['history_page']) ? max(1, (int) $_GET['history_page']) : 1;
$historyPerPage = 10;
$historyOffset = ($historyPage - 1) * $historyPerPage;

// Get total count of all leave requests for pagination
try {
    $totalLeaveHistory = $db->query("
        SELECT COUNT(*) as count 
        FROM leave_requests
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalLeaveHistory = 0;
    error_log("Error fetching total leave history: " . $th->getMessage());
}

// Get paginated leave requests for history
try {
    $paginatedLeaveHistory = $db->query("
        SELECT 
            lr.*,
            e.full_name,
            e.position,
            e.department,
            DATEDIFF(lr.end_date, lr.start_date) + 1 as actual_days
        FROM leave_requests lr
        JOIN employees e ON lr.employee_id = e.id
        ORDER BY lr.created_at DESC
        LIMIT $historyPerPage OFFSET $historyOffset
    ")->find();
} catch (\Throwable $th) {
    $paginatedLeaveHistory = [];
    error_log("Error fetching leave history: " . $th->getMessage());
}

$totalHistoryPages = ceil($totalLeaveHistory / $historyPerPage);

// Get leave statistics by month (optional)
try {
    $leaveStats = $db->query("
        SELECT 
            DATE_FORMAT(start_date, '%Y-%m') as month,
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
        FROM leave_requests
        WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(start_date, '%Y-%m')
        ORDER BY month DESC
    ")->find();
} catch (\Throwable $th) {
    $leaveStats = [];
    error_log("Error fetching leave stats: " . $th->getMessage());
}

// ============================================
// TIME AND ATTENDANCE SECTION
// ============================================

// Get current date and calculate cutoff periods
$currentDate = date('Y-m-d');
$currentMonth = date('m');
$currentYear = date('Y');
$currentDay = date('d');

// Determine current cutoff period (1st cutoff: 21st - 5th, 2nd cutoff: 6th - 20th)
if ($currentDay >= 21 || $currentDay <= 5) {
    // 1st Cutoff: 21st of previous month to 5th of current month
    if ($currentDay <= 5) {
        $cutoffStart = date('Y-m-21', strtotime('-1 month'));
        $cutoffEnd = date('Y-m-05');
    } else {
        $cutoffStart = date('Y-m-21');
        $cutoffEnd = date('Y-m-05', strtotime('+1 month'));
    }
    $cutoffType = '1st Cutoff';
} else {
    // 2nd Cutoff: 6th to 20th of current month
    $cutoffStart = date('Y-m-06');
    $cutoffEnd = date('Y-m-20');
    $cutoffType = '2nd Cutoff';
}

// Get total active employees count
try {
    $totalActiveEmployees = $db->query("
        SELECT COUNT(*) as count 
        FROM employees 
        WHERE status = 'Active' OR status = 'Probationary'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalActiveEmployees = 0;
    error_log("Error fetching active employees: " . $th->getMessage());
}

// ============================================
// CUTOFF SUMMARY STATISTICS
// ============================================

// Get total hours for current cutoff period
try {
    $cutoffSummary = $db->query("
        SELECT 
            COALESCE(SUM(regular_hours), 0) as total_regular_hours,
            COALESCE(SUM(overtime_hours), 0) as total_overtime_hours,
            COUNT(DISTINCT employee_id) as employees_with_attendance
        FROM attendance 
        WHERE date BETWEEN '$cutoffStart' AND '$cutoffEnd'
    ")->fetch_one();

    $totalRegularHours = $cutoffSummary['total_regular_hours'] ?? 0;
    $totalOvertimeHours = $cutoffSummary['total_overtime_hours'] ?? 0;
    $totalCutoffHours = $totalRegularHours + $totalOvertimeHours;
    $employeesWithAttendance = $cutoffSummary['employees_with_attendance'] ?? 0;
} catch (\Throwable $th) {
    $totalRegularHours = 0;
    $totalOvertimeHours = 0;
    $totalCutoffHours = 0;
    $employeesWithAttendance = 0;
    error_log("Error fetching cutoff summary: " . $th->getMessage());
}

// Calculate average hours per employee
$averageHoursPerEmployee = $employeesWithAttendance > 0
    ? round($totalCutoffHours / $employeesWithAttendance, 1)
    : 0;

// Calculate projected payout (assuming average hourly rate)
try {
    $avgHourlyRate = $db->query("
        SELECT COALESCE(AVG(hourly_rate), 125) as avg_rate 
        FROM employees 
        WHERE hourly_rate > 0
    ")->fetch_one()['avg_rate'] ?? 125;

    $projectedPayout = $totalCutoffHours * $avgHourlyRate;
} catch (\Throwable $th) {
    $projectedPayout = $totalCutoffHours * 125; // Default rate
    error_log("Error calculating projected payout: " . $th->getMessage());
}

// ============================================
// DEPARTMENT HOURS BREAKDOWN
// ============================================
try {
    $departmentHours = $db->query("
        SELECT 
            e.department,
            COALESCE(SUM(a.regular_hours + a.overtime_hours), 0) as total_hours,
            COUNT(DISTINCT e.id) as employee_count
        FROM employees e
        LEFT JOIN attendance a ON e.id = a.employee_id 
            AND a.date BETWEEN '$cutoffStart' AND '$cutoffEnd'
        WHERE e.department IS NOT NULL AND e.department != ''
        GROUP BY e.department
        ORDER BY total_hours DESC
    ")->find();
} catch (\Throwable $th) {
    $departmentHours = [];
    error_log("Error fetching department hours: " . $th->getMessage());
}

// ============================================
// TODAY'S ATTENDANCE SUMMARY
// ============================================
$today = date('Y-m-d');

// Present today
try {
    $presentToday = $db->query("
        SELECT COUNT(DISTINCT employee_id) as count 
        FROM attendance 
        WHERE date = '$today' AND status = 'clocked_in'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $presentToday = 0;
    error_log("Error fetching present count: " . $th->getMessage());
}

// On leave today
try {
    $onLeaveToday = $db->query("
        SELECT COUNT(DISTINCT employee_id) as count 
        FROM leave_requests 
        WHERE '$today' BETWEEN start_date AND end_date 
        AND status = 'Approved'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $onLeaveToday = 0;
    error_log("Error fetching on leave count: " . $th->getMessage());
}

// Late today
try {
    $lateToday = $db->query("
        SELECT COUNT(DISTINCT employee_id) as count 
        FROM attendance 
        WHERE date = '$today' AND late_status = 'late'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $lateToday = 0;
    error_log("Error fetching late count: " . $th->getMessage());
}

// Absent today (active employees minus those present, on leave, or late)
try {
    $totalActiveForAttendance = $db->query("
        SELECT COUNT(*) as count 
        FROM employees 
        WHERE status = 'Active' OR status = 'Probationary'
    ")->fetch_one()['count'] ?? 0;

    $absentToday = $totalActiveForAttendance - ($presentToday + $onLeaveToday + $lateToday);
    $absentToday = max(0, $absentToday); // Ensure not negative
} catch (\Throwable $th) {
    $absentToday = 0;
    error_log("Error calculating absent count: " . $th->getMessage());
}

// ============================================
// TODAY'S ATTENDANCE LIST WITH PAGINATION
// ============================================
$attendancePage = isset($_GET['attendance_page']) ? max(1, (int) $_GET['attendance_page']) : 1;
$attendancePerPage = 10;
$attendanceOffset = ($attendancePage - 1) * $attendancePerPage;

// Get total count for pagination
try {
    $totalAttendanceToday = $db->query("
        SELECT COUNT(DISTINCT employee_id) as count 
        FROM attendance 
        WHERE date = '$today'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalAttendanceToday = 0;
    error_log("Error fetching total attendance count: " . $th->getMessage());
}

// Get paginated attendance data
try {
    $todaysAttendance = $db->query("
        SELECT 
            a.*,
            e.id as employee_id,
            e.full_name,
            e.department,
            e.hourly_rate,
            s.shift_name,
            s.start_time as shift_start,
            s.end_time as shift_end,
            TIME_FORMAT(a.clock_in, '%h:%i %p') as formatted_clock_in,
            TIME_FORMAT(a.clock_out, '%h:%i %p') as formatted_clock_out,
            -- Calculate today's hours
            CASE 
                WHEN a.clock_out IS NOT NULL 
                THEN TIMESTAMPDIFF(MINUTE, a.clock_in, a.clock_out) / 60
                ELSE TIMESTAMPDIFF(MINUTE, a.clock_in, NOW()) / 60
            END as todays_hours,
            -- Get cutoff total for this employee
            (
                SELECT COALESCE(SUM(regular_hours + overtime_hours), 0)
                FROM attendance a2
                WHERE a2.employee_id = e.id
                AND a2.date BETWEEN '$cutoffStart' AND '$cutoffEnd'
            ) as cutoff_total_hours
        FROM attendance a
        JOIN employees e ON a.employee_id = e.id
        LEFT JOIN shifts s ON a.shift_id = s.id
        WHERE a.date = '$today'
        ORDER BY a.clock_in DESC
        LIMIT $attendancePerPage OFFSET $attendanceOffset
    ")->find();
} catch (\Throwable $th) {
    $todaysAttendance = [];
    error_log("Error fetching today's attendance: " . $th->getMessage());
}

$totalAttendancePages = ceil($totalAttendanceToday / $attendancePerPage);

// ============================================
// SHIFTS FOR DROPDOWN
// ============================================
try {
    $availableShifts = $db->query("
        SELECT id, shift_name, shift_code, start_time, end_time
        FROM shifts
        ORDER BY start_time ASC
    ")->find();
} catch (\Throwable $th) {
    $availableShifts = [];
    error_log("Error fetching shifts: " . $th->getMessage());
}

// ============================================
// TIMESHEET MANAGEMENT SECTION - UPDATED
// ============================================

// Get filter parameters
$timesheetFilter = isset($_GET['timesheet_filter']) ? $_GET['timesheet_filter'] : 'this_week';
$timesheetPage = isset($_GET['timesheet_page']) ? max(1, (int) $_GET['timesheet_page']) : 1;
$timesheetPerPage = 10;
$timesheetOffset = ($timesheetPage - 1) * $timesheetPerPage;

// Calculate date range based on filter
$today = date('Y-m-d');
$filterApplied = $timesheetFilter !== 'all';

if ($timesheetFilter === 'all') {
    // No filter - show all time
    $dateRangeStart = '1970-01-01';
    $dateRangeEnd = '2099-12-31';
    $filterLabel = 'All Time';
} else {
    switch ($timesheetFilter) {
        case 'this_week':
            $startOfWeek = date('Y-m-d', strtotime('monday this week'));
            $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
            $dateRangeStart = $startOfWeek;
            $dateRangeEnd = $endOfWeek;
            $filterLabel = 'This Week';
            break;
        case 'last_week':
            $startOfLastWeek = date('Y-m-d', strtotime('monday last week'));
            $endOfLastWeek = date('Y-m-d', strtotime('sunday last week'));
            $dateRangeStart = $startOfLastWeek;
            $dateRangeEnd = $endOfLastWeek;
            $filterLabel = 'Last Week';
            break;
        case 'this_month':
            $dateRangeStart = date('Y-m-01');
            $dateRangeEnd = date('Y-m-t');
            $filterLabel = 'This Month';
            break;
        default:
            $dateRangeStart = date('Y-m-d', strtotime('monday this week'));
            $dateRangeEnd = date('Y-m-d', strtotime('sunday this week'));
            $filterLabel = 'This Week';
    }
}

// Get total count for pagination
try {
    $totalTimesheets = $db->query("
        SELECT COUNT(DISTINCT e.id) as count
        FROM employees e
        WHERE e.status IN ('Active', 'Regular', 'Probationary')
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalTimesheets = 0;
    error_log("Error fetching total timesheets: " . $th->getMessage());
}

// Get timesheet data with pagination - Using attendance_summary
try {
    if ($timesheetFilter === 'all') {
        $timesheets = $db->query("
            SELECT 
                e.id as employee_id,
                e.full_name,
                e.department,
                e.position,
                e.hourly_rate,
                -- Calculate totals from attendance_summary for all time
                COALESCE((
                    SELECT SUM(total_regular_hours) 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id
                ), 0) as regular_hours,
                COALESCE((
                    SELECT SUM(total_overtime_hours) 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id
                ), 0) as overtime_hours,
                -- Get the LATEST attendance summary ID and status
                (
                    SELECT id 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id 
                    ORDER BY as2.period_end DESC 
                    LIMIT 1
                ) as summary_id,
                COALESCE((
                    SELECT status 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id 
                    ORDER BY as2.period_end DESC 
                    LIMIT 1
                ), 'Pending') as timesheet_status,
                -- Get period for the latest summary
                (
                    SELECT period_start 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id 
                    ORDER BY as2.period_end DESC 
                    LIMIT 1
                ) as summary_period_start,
                (
                    SELECT period_end 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id 
                    ORDER BY as2.period_end DESC 
                    LIMIT 1
                ) as summary_period_end,
                (
                    SELECT MAX(period_end) 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id
                ) as last_attendance_date,
                -- Include attendance records as JSON (still from attendance for detailed view)
                (
                    SELECT CONCAT(
                        '[',
                        GROUP_CONCAT(
                            JSON_OBJECT(
                                'id', a.id,
                                'date', a.date,
                                'clock_in', TIME_FORMAT(a.clock_in, '%h:%i %p'),
                                'clock_out', TIME_FORMAT(a.clock_out, '%h:%i %p'),
                                'regular_hours', a.regular_hours,
                                'overtime_hours', a.overtime_hours,
                                'late_minutes', a.late_minutes,
                                'late_status', a.late_status
                            )
                            ORDER BY a.date DESC
                            SEPARATOR ','
                        ),
                        ']'
                    )
                    FROM attendance a 
                    WHERE a.employee_id = e.id AND a.status = 'clocked_out'
                ) as attendance_records,
                -- Get attendance count from summaries
                (
                    SELECT COUNT(*) 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id
                ) as total_attendance_days
            FROM employees e
            WHERE e.status IN ('Active', 'Regular', 'Probationary')
            ORDER BY e.full_name ASC
            LIMIT $timesheetPerPage OFFSET $timesheetOffset
        ")->find();
    } else {
        // For date-filtered view, find which payroll periods overlap with the date range
        $timesheets = $db->query("
            SELECT 
                e.id as employee_id,
                e.full_name,
                e.department,
                e.position,
                e.hourly_rate,
                -- Calculate totals from attendance_summary for the period
                COALESCE((
                    SELECT SUM(total_regular_hours) 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id 
                    AND as2.period_start <= ? 
                    AND as2.period_end >= ?
                ), 0) as regular_hours,
                COALESCE((
                    SELECT SUM(total_overtime_hours) 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id 
                    AND as2.period_start <= ? 
                    AND as2.period_end >= ?
                ), 0) as overtime_hours,
                -- Get the attendance summary ID and status for the relevant period
                (
                    SELECT id 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id 
                    AND as2.period_start <= ? 
                    AND as2.period_end >= ?
                    ORDER BY as2.period_end DESC 
                    LIMIT 1
                ) as summary_id,
                COALESCE((
                    SELECT status 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id 
                    AND as2.period_start <= ? 
                    AND as2.period_end >= ?
                    ORDER BY as2.period_end DESC 
                    LIMIT 1
                ), 'Pending') as timesheet_status,
                -- Get the exact period for this summary
                (
                    SELECT period_start 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id 
                    AND as2.period_start <= ? 
                    AND as2.period_end >= ?
                    ORDER BY as2.period_end DESC 
                    LIMIT 1
                ) as summary_period_start,
                (
                    SELECT period_end 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id 
                    AND as2.period_start <= ? 
                    AND as2.period_end >= ?
                    ORDER BY as2.period_end DESC 
                    LIMIT 1
                ) as summary_period_end,
                (
                    SELECT MAX(period_end) 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id
                ) as last_attendance_date,
                -- Include attendance records for the period as JSON (still from attendance for detailed view)
                (
                    SELECT CONCAT(
                        '[',
                        GROUP_CONCAT(
                            JSON_OBJECT(
                                'id', a.id,
                                'date', a.date,
                                'clock_in', TIME_FORMAT(a.clock_in, '%h:%i %p'),
                                'clock_out', TIME_FORMAT(a.clock_out, '%h:%i %p'),
                                'regular_hours', a.regular_hours,
                                'overtime_hours', a.overtime_hours,
                                'late_minutes', a.late_minutes,
                                'late_status', a.late_status
                            )
                            ORDER BY a.date DESC
                            SEPARATOR ','
                        ),
                        ']'
                    )
                    FROM attendance a 
                    WHERE a.employee_id = e.id 
                    AND a.date BETWEEN ? AND ?
                    AND a.status = 'clocked_out'
                ) as attendance_records,
                -- Get attendance count from summaries for the period
                (
                    SELECT COUNT(*) 
                    FROM attendance_summary as2 
                    WHERE as2.employee_id = e.id 
                    AND as2.period_start <= ? 
                    AND as2.period_end >= ?
                ) as period_attendance_days
            FROM employees e
            WHERE e.status IN ('Active', 'Regular', 'Probationary')
            ORDER BY e.full_name ASC
            LIMIT ? OFFSET ?
        ", [
            // Regular hours params (2)
            $dateRangeEnd,
            $dateRangeStart,
            // Overtime hours params (2)
            $dateRangeEnd,
            $dateRangeStart,
            // Summary ID params (2)
            $dateRangeEnd,
            $dateRangeStart,
            // Status params (2)
            $dateRangeEnd,
            $dateRangeStart,
            // Period start params (2)
            $dateRangeEnd,
            $dateRangeStart,
            // Period end params (2)
            $dateRangeEnd,
            $dateRangeStart,
            // Attendance records JSON (2)
            $dateRangeStart,
            $dateRangeEnd,
            // Period attendance days params (2)
            $dateRangeEnd,
            $dateRangeStart,
            // Pagination (2)
            $timesheetPerPage,
            $timesheetOffset
        ])->find();
    }
} catch (\Throwable $th) {
    $timesheets = [];
    error_log("Error fetching timesheets: " . $th->getMessage());
}

// Calculate total pages
$totalTimesheetPages = ceil($totalTimesheets / $timesheetPerPage);

// Get summary statistics for the period - UPDATED to use attendance_summary
try {
    if ($timesheetFilter === 'all') {
        $timesheetSummary = $db->query("
            SELECT 
                COALESCE(SUM(total_regular_hours), 0) as total_regular,
                COALESCE(SUM(total_overtime_hours), 0) as total_overtime,
                COUNT(DISTINCT employee_id) as employees_with_hours,
                COUNT(DISTINCT CASE WHEN status = 'Approved' THEN employee_id END) as approved_count,
                COUNT(DISTINCT CASE WHEN status = 'Pending' THEN employee_id END) as pending_count
            FROM attendance_summary
        ")->fetch_one();
    } else {
        $timesheetSummary = $db->query("
            SELECT 
                COALESCE(SUM(total_regular_hours), 0) as total_regular,
                COALESCE(SUM(total_overtime_hours), 0) as total_overtime,
                COUNT(DISTINCT employee_id) as employees_with_hours,
                COUNT(DISTINCT CASE WHEN status = 'Approved' THEN employee_id END) as approved_count,
                COUNT(DISTINCT CASE WHEN status = 'Pending' THEN employee_id END) as pending_count
            FROM attendance_summary 
            WHERE period_start <= ? AND period_end >= ?
        ", [$dateRangeEnd, $dateRangeStart])->fetch_one();
    }

    $totalRegularPeriod = $timesheetSummary['total_regular'] ?? 0;
    $totalOvertimePeriod = $timesheetSummary['total_overtime'] ?? 0;
    $totalHoursPeriod = $totalRegularPeriod + $totalOvertimePeriod;
    $employeesWithHours = $timesheetSummary['employees_with_hours'] ?? 0;
    $approvedCount = $timesheetSummary['approved_count'] ?? 0;
    $pendingCount = $timesheetSummary['pending_count'] ?? 0;
} catch (\Throwable $th) {
    $totalRegularPeriod = 0;
    $totalOvertimePeriod = 0;
    $totalHoursPeriod = 0;
    $employeesWithHours = 0;
    $approvedCount = 0;
    $pendingCount = 0;
    error_log("Error fetching timesheet summary: " . $th->getMessage());
}

// Get pending approval count - UPDATED to use attendance_summary
try {
    if ($timesheetFilter === 'all') {
        $pendingApprovalCount = $db->query("
            SELECT COUNT(*) as count 
            FROM attendance_summary 
            WHERE status = 'Pending'
        ")->fetch_one()['count'] ?? 0;
    } else {
        $pendingApprovalCount = $db->query("
            SELECT COUNT(*) as count 
            FROM attendance_summary 
            WHERE period_start <= ? AND period_end >= ?
            AND status = 'Pending'
        ", [$dateRangeEnd, $dateRangeStart])->fetch_one()['count'] ?? 0;
    }
} catch (\Throwable $th) {
    $pendingApprovalCount = 0;
    error_log("Error fetching pending approval count: " . $th->getMessage());
}

// ============================================
// COMPETENCY MANAGEMENT SECTION
// ============================================

// Get filter parameters
$competencyPage = isset($_GET['competency_page']) ? max(1, (int) $_GET['competency_page']) : 1;
$competencyPerPage = 10;
$competencyOffset = ($competencyPage - 1) * $competencyPerPage;

// Get total assessments count for pagination
try {
    $totalAssessments = $db->query("
        SELECT COUNT(*) as count 
        FROM competency_assessments ca
        JOIN employees e ON ca.employee_id = e.id
        WHERE e.status = 'Probationary'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalAssessments = 0;
    error_log("Error fetching total assessments: " . $th->getMessage());
}

// Get recent assessments with gap analysis
try {
    $recentAssessments = $db->query("
        SELECT 
            ca.*,
            e.full_name as employee_name,
            e.position as employee_position,
            e.id as employee_id,
            e.email,
            c.name as competency_name,
            c.required_level,
            (c.required_level - ca.proficiency_level) as gap_level,
            CASE 
                WHEN (c.required_level - ca.proficiency_level) > 0 THEN 'Gap Identified'
                ELSE 'Completed'
            END as gap_status,
            CASE 
                WHEN (c.required_level - ca.proficiency_level) > 0 THEN 'bg-yellow-50 text-yellow-700 border-yellow-200'
                ELSE 'bg-green-50 text-green-700 border-green-200'
            END as gap_class,
            CASE 
                WHEN ca.status = 'Passed' THEN 'bg-green-50 text-green-700 border-green-200'
                WHEN ca.status = 'Needs Improvement' THEN 'bg-yellow-50 text-yellow-700 border-yellow-200'
                ELSE 'bg-gray-50 text-gray-700 border-gray-200'
            END as status_class
        FROM competency_assessments ca
        JOIN employees e ON ca.employee_id = e.id
        JOIN competencies c ON ca.competency_id = c.id
        ORDER BY ca.assessment_date DESC
        LIMIT $competencyPerPage OFFSET $competencyOffset
    ")->find();
} catch (\Throwable $th) {
    $recentAssessments = [];
    error_log("Error fetching recent assessments: " . $th->getMessage());
}

$totalCompetencyPages = ceil($totalAssessments / $competencyPerPage);

// Get statistics
try {
    $competencyStats = $db->query("
        SELECT 
            COUNT(CASE WHEN ca.assessment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as completed_this_month,
            COUNT(CASE WHEN (c.required_level - ca.proficiency_level) > 0 THEN 1 END) as gaps_identified,
            COUNT(CASE WHEN ca.assessment_date IS NULL AND e.status = 'Probationary' THEN 1 END) as pending_assessments
        FROM competency_assessments ca
        JOIN employees e ON ca.employee_id = e.id
        JOIN competencies c ON ca.competency_id = c.id
        WHERE e.status = 'Probationary'
    ")->fetch_one();

    // Get last month's count for comparison
    $lastMonthCount = $db->query("
        SELECT COUNT(*) as count 
        FROM competency_assessments 
        WHERE assessment_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 60 DAY) AND DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ")->fetch_one()['count'] ?? 0;

    $completedThisMonth = $competencyStats['completed_this_month'] ?? 0;
    $gapsIdentified = $competencyStats['gaps_identified'] ?? 0;
    $pendingAssessments = $competencyStats['pending_assessments'] ?? 0;

    $monthlyComparison = $completedThisMonth - $lastMonthCount;
} catch (\Throwable $th) {
    $completedThisMonth = 0;
    $gapsIdentified = 0;
    $pendingAssessments = 0;
    $monthlyComparison = 0;
    error_log("Error fetching competency stats: " . $th->getMessage());
}

try {
    $competencies = $db->query("
        SELECT id, name, description, required_level
        FROM competencies
        ORDER BY created_at DESC
    ")->find();
} catch (\Throwable $th) {
    error_log("Error fetching competencies: " . $th->getMessage());
    $competencies = [];
}

try {
    $evaluators = $db->query("
    SELECT id, full_name
    FROM employees
    WHERE role = 'evaluator'
    ORDER BY full_name
")->find();
} catch (\Throwable $th) {
    error_log("Error fetching competencies: " . $th->getMessage());
}

// Simple query - get employees with completed training, pending assessment, and competency info
try {
    $employeesForDropdown = $db->query("
        SELECT DISTINCT 
            e.id, 
            e.full_name, 
            e.position,
            e.role,
            ts.competency_id,
            c.name as competency_name
        FROM employees e
        INNER JOIN training_schedule ts ON e.id = ts.employee_id
        INNER JOIN competencies c ON ts.competency_id = c.id
        WHERE e.role = 'employee' 
        AND ts.assessment_status = 'pending'
        ORDER BY e.full_name ASC
    ")->find();
} catch (\Throwable $th) {
    $employeesForDropdown = [];
    error_log("Error fetching employees: " . $th->getMessage());
}

//TRAINING
try {
    $employeeRole = $db->query("
        SELECT id, full_name, position
        FROM employees
        WHERE role = ?
        AND onboarding_status != ?
        AND status = ?
        ORDER BY full_name
    ", [
        'employee',
        'Onboarded',
        'Probationary'
    ])->find();

} catch (\Throwable $th) {
    error_log("Error fetching employees: " . $th->getMessage());
    $employeeRole = [];
}

try {
    $trainingProviders = $db->query("
        SELECT id, name
        FROM training_providers
        ORDER BY name")->find();

} catch (\Throwable $th) {
    error_log("Error fetching employees: " . $th->getMessage());
    $trainingProviders = [];
}

// ============================================
// TRAINING MANAGEMENT SECTION
// ============================================

// Get filter parameters
$trainingFilter = isset($_GET['training_filter']) ? $_GET['training_filter'] : 'all';
$trainingPage = isset($_GET['training_page']) ? max(1, (int) $_GET['training_page']) : 1;
$trainingPerPage = 10;
$trainingOffset = ($trainingPage - 1) * $trainingPerPage;

// Base query conditions
$statusCondition = "";
if ($trainingFilter === 'scheduled') {
    $statusCondition = "AND ts.status = 'Scheduled'";
} elseif ($trainingFilter === 'completed') {
    $statusCondition = "AND ts.status = 'Completed'";
} elseif ($trainingFilter === 'pending') {
    $statusCondition = "AND ts.assessment_status = 'pending'";
}

// Get total count for pagination
try {
    $totalTrainings = $db->query("
        SELECT COUNT(*) as count 
        FROM training_schedule ts
        WHERE 1=1 $statusCondition
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalTrainings = 0;
    error_log("Error fetching total trainings: " . $th->getMessage());
}

// Get training schedule with employee and provider details
try {
    $trainingSchedule = $db->query("
        SELECT 
            ts.*,
            e.full_name as employee_name,
            e.position as employee_position,
            e.id as employee_id,
            tp.name as provider_name,
            c.name as competency_name,
            c.required_level
        FROM training_schedule ts
        LEFT JOIN employees e ON ts.employee_id = e.id
        LEFT JOIN training_providers tp ON ts.provider_id = tp.id
        LEFT JOIN competencies c ON ts.competency_id = c.id
        WHERE 1=1 $statusCondition
        ORDER BY 
            CASE 
                WHEN ts.status = 'Scheduled' THEN 1
                WHEN ts.status = 'Completed' AND ts.assessment_status = 'pending' THEN 2
                WHEN ts.status = 'Completed' AND ts.assessment_status = 'completed' THEN 3
                WHEN ts.status = 'Completed' AND ts.assessment_status = 'failed' THEN 4
                ELSE 5
            END,
            ts.start_date ASC
        LIMIT $trainingPerPage OFFSET $trainingOffset
    ")->find();
} catch (\Throwable $th) {
    $trainingSchedule = [];
    error_log("Error fetching training schedule: " . $th->getMessage());
}

$totalTrainingPages = ceil($totalTrainings / $trainingPerPage);

// Get training statistics
try {
    $trainingStats = $db->query("
        SELECT 
            COUNT(CASE WHEN status = 'Scheduled' THEN 1 END) as active_trainings,
            COUNT(CASE WHEN training_type = 'internal' THEN 1 END) as internal_count,
            COUNT(CASE WHEN training_type = 'external' THEN 1 END) as external_count,
            COUNT(CASE WHEN training_type = 'certification' THEN 1 END) as certification_count,
            COUNT(CASE WHEN assessment_status = 'pending' AND status = 'Completed' THEN 1 END) as pending_assessment
        FROM training_schedule
    ")->fetch_one();
} catch (\Throwable $th) {
    $trainingStats = [
        'active_trainings' => 0,
        'internal_count' => 0,
        'external_count' => 0,
        'certification_count' => 0,
        'pending_assessment' => 0
    ];
    error_log("Error fetching training stats: " . $th->getMessage());
}

// Get employees with competency gaps
try {
    $employeesWithGaps = $db->query("
        SELECT 
            e.id,
            e.full_name,
            e.position,
            e.department,
            c.id as competency_id,
            c.name as competency_name,
            c.required_level,
            COALESCE(ca.proficiency_level, 0) as current_level,
            (c.required_level - COALESCE(ca.proficiency_level, 0)) as gap,
            CASE 
                WHEN ca.id IS NULL THEN 'No assessment'
                WHEN ca.proficiency_level < c.required_level THEN 'Below required'
                ELSE 'Meeting requirement'
            END as gap_status,
            (
                SELECT ts.id 
                FROM training_schedule ts 
                WHERE ts.employee_id = e.id 
                AND ts.competency_id = c.id
                AND ts.status = 'Scheduled'
                LIMIT 1
            ) as has_scheduled_training
        FROM employees e
        CROSS JOIN competencies c
        LEFT JOIN competency_assessments ca ON ca.employee_id = e.id 
            AND ca.competency_id = c.id
        WHERE e.status = 'Probationary'
        AND e.onboarding_status != 'Onboarded'
        AND (
            ca.id IS NULL 
            OR ca.proficiency_level < c.required_level
        )
        ORDER BY e.full_name, c.name
    ")->find();
} catch (\Throwable $th) {
    $employeesWithGaps = [];
    error_log("Error fetching employees with gaps: " . $th->getMessage());
}

// Group gaps by employee
$groupedGaps = [];
foreach ($employeesWithGaps as $gap) {
    $empId = $gap['id'];
    if (!isset($groupedGaps[$empId])) {
        $groupedGaps[$empId] = [
            'id' => $gap['id'],
            'full_name' => $gap['full_name'],
            'position' => $gap['position'],
            'department' => $gap['department'],
            'gaps' => []
        ];
    }
    $groupedGaps[$empId]['gaps'][] = [
        'competency_name' => $gap['competency_name'],
        'required_level' => $gap['required_level'],
        'current_level' => $gap['current_level'],
        'gap' => $gap['gap'],
        'gap_status' => $gap['gap_status'],
        'has_scheduled_training' => $gap['has_scheduled_training']
    ];
}

// Get employees for dropdown (not in training schedule)
try {
    $availableEmployees = $db->query("
        SELECT e.id, e.full_name, e.position
        FROM employees e
        WHERE e.role = 'employee'
        AND e.onboarding_status != 'Onboarded'
        AND e.status = 'Probationary'
        AND e.id NOT IN (
            SELECT DISTINCT employee_id 
            FROM training_schedule 
            WHERE status IN ('Scheduled', 'Completed')
            AND assessment_status = 'pending'
        )
        ORDER BY e.full_name
    ")->find();
} catch (\Throwable $th) {
    $availableEmployees = [];
    error_log("Error fetching available employees: " . $th->getMessage());
}
// ============================================
// CORE HUMAN CAPITAL MANAGEMENT SECTION
// ============================================

// Pagination for HCM employee list
$hcmPage = isset($_GET['hcm_page']) ? max(1, (int) $_GET['hcm_page']) : 1;
$hcmPerPage = 10;
$hcmOffset = ($hcmPage - 1) * $hcmPerPage;

// Search functionality
$hcmSearchTerm = isset($_GET['hcm_search']) ? $_GET['hcm_search'] : '';

// Filter functionality
$hcmStatusFilter = isset($_GET['hcm_status']) ? $_GET['hcm_status'] : '';
$hcmDepartmentFilter = isset($_GET['hcm_department']) ? $_GET['hcm_department'] : '';
$hcmRoleFilter = isset($_GET['hcm_role']) ? $_GET['hcm_role'] : '';

// Build WHERE clause for filters
$hcmWhereConditions = [];
$hcmParams = [];

if (!empty($hcmSearchTerm)) {
    $hcmWhereConditions[] = "(e.full_name LIKE ? OR e.employee_number LIKE ? OR e.position LIKE ? OR e.department LIKE ?)";
    $searchParam = "%$hcmSearchTerm%";
    $hcmParams = array_merge($hcmParams, [$searchParam, $searchParam, $searchParam, $searchParam]);
}

if (!empty($hcmStatusFilter)) {
    $hcmWhereConditions[] = "e.status = ?";
    $hcmParams[] = $hcmStatusFilter;
}

if (!empty($hcmDepartmentFilter)) {
    $hcmWhereConditions[] = "e.department = ?";
    $hcmParams[] = $hcmDepartmentFilter;
}

if (!empty($hcmRoleFilter)) {
    $hcmWhereConditions[] = "e.role = ?";
    $hcmParams[] = $hcmRoleFilter;
}

$hcmWhereClause = !empty($hcmWhereConditions) ? "WHERE " . implode(" AND ", $hcmWhereConditions) : "";

// Get total employees count for pagination with filters
try {
    $totalHCMSql = "SELECT COUNT(*) as count FROM employees e $hcmWhereClause";
    $totalHCMEmployees = $db->query($totalHCMSql, $hcmParams)->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalHCMEmployees = 0;
    error_log("Error fetching total HCM employees: " . $th->getMessage());
}

// Get distinct departments for filter dropdown
try {
    $hcmDepartments = $db->query("
        SELECT DISTINCT department 
        FROM employees 
        WHERE department IS NOT NULL AND department != ''
        ORDER BY department
    ")->find();
} catch (\Throwable $th) {
    $hcmDepartments = [];
    error_log("Error fetching departments: " . $th->getMessage());
}

// Get distinct statuses for filter dropdown
try {
    $hcmStatuses = $db->query("
        SELECT DISTINCT status 
        FROM employees 
        WHERE status IS NOT NULL AND status != ''
        ORDER BY status
    ")->find();
} catch (\Throwable $th) {
    $hcmStatuses = [];
    error_log("Error fetching statuses: " . $th->getMessage());
}

// Get distinct roles for filter dropdown
try {
    $hcmRoles = $db->query("
        SELECT DISTINCT role 
        FROM employees 
        WHERE role IS NOT NULL AND role != ''
        ORDER BY role
    ")->find();
} catch (\Throwable $th) {
    $hcmRoles = [];
    error_log("Error fetching roles: " . $th->getMessage());
}

// Get paginated employees with all related data and filters
try {
    $hcmEmployees = $db->query("
        SELECT 
            e.*,
            a.experience as applicant_experience,
            a.education as applicant_education,
            a.skills as applicant_skills,
            a.cover_note as applicant_cover_note,
            a.created_at as applicant_created_at,
            a.resume_path as applicant_resume_path,
            ea.username,
            ea.account_status,
            ea.last_login,
            ea.session_token,
            (
                SELECT SUM(regular_hours + overtime_hours)
                FROM attendance 
                WHERE employee_id = e.id
                AND date BETWEEN '$cutoffStart' AND '$cutoffEnd'
            ) as cutoff_hours,
            (
                SELECT COUNT(*) 
                FROM training_schedule 
                WHERE employee_id = e.id AND status = 'Scheduled'
            ) as upcoming_trainings,
            (
                SELECT COUNT(*) 
                FROM leave_requests 
                WHERE employee_id = e.id AND status = 'Pending'
            ) as pending_leaves,
            (
                SELECT COUNT(*) 
                FROM tasks 
                WHERE assigned_to = e.id AND status != 'Completed'
            ) as pending_tasks
        FROM employees e
        LEFT JOIN applicants a ON e.applicant_id = a.id
        LEFT JOIN employee_accounts ea ON e.id = ea.employee_id
        $hcmWhereClause
        ORDER BY e.hired_date DESC
        LIMIT $hcmPerPage OFFSET $hcmOffset
    ", $hcmParams)->find();
} catch (\Throwable $th) {
    $hcmEmployees = [];
    error_log("Error fetching HCM employees: " . $th->getMessage());
}

// Calculate total pages
$totalHCMPages = ceil($totalHCMEmployees / $hcmPerPage);

// Get department statistics
try {
    $departmentStats = $db->query("
        SELECT 
            department,
            COUNT(*) as total,
            SUM(CASE WHEN position LIKE '%Manager%' OR position LIKE '%Supervisor%' OR position LIKE '%Chef%' THEN 1 ELSE 0 END) as managers,
            SUM(CASE WHEN status = 'Active' OR status = 'Regular' THEN 1 ELSE 0 END) as active
        FROM employees
        WHERE department IS NOT NULL AND department != ''
        GROUP BY department
        ORDER BY total DESC
    ")->find();
} catch (\Throwable $th) {
    $departmentStats = [];
    error_log("Error fetching department stats: " . $th->getMessage());
}

// Get employee statistics
try {
    $hcmStats = $db->query("
        SELECT 
            COUNT(*) as total_employees,
            SUM(CASE WHEN status = 'Active' OR status = 'Regular' THEN 1 ELSE 0 END) as active_employees,
            SUM(CASE WHEN status = 'Probationary' THEN 1 ELSE 0 END) as probationary,
            (
                SELECT COUNT(DISTINCT employee_id) 
                FROM leave_requests 
                WHERE CURDATE() BETWEEN start_date AND end_date 
                AND status = 'Approved'
            ) as on_leave
        FROM employees
    ")->fetch_one();

    $totalEmployees = $hcmStats['total_employees'] ?? 0;
    $activeEmployees = $hcmStats['active_employees'] ?? 0;
    $probationaryCount = $hcmStats['probationary'] ?? 0;
    $onLeaveCount = $hcmStats['on_leave'] ?? 0;
    $activePercentage = $totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100) : 0;
} catch (\Throwable $th) {
    $totalEmployees = 0;
    $activeEmployees = 0;
    $probationaryCount = 0;
    $onLeaveCount = 0;
    $activePercentage = 0;
    error_log("Error fetching HCM stats: " . $th->getMessage());
}

// Get initials for avatar
function getInitials($name)
{
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    return substr($initials, 0, 2);
}

// ========================attributes: ====================
// COMPENSATION PLANNING SECTION
// ============================================

// Pagination for compensation reviews
$compensationPage = isset($_GET['compensation_page']) ? max(1, (int) $_GET['compensation_page']) : 1;
$compensationPerPage = 5;
$compensationOffset = ($compensationPage - 1) * $compensationPerPage;

// Filter parameters
$compensationStatusFilter = isset($_GET['compensation_status']) ? $_GET['compensation_status'] : '';
$compensationTypeFilter = isset($_GET['compensation_type']) ? $_GET['compensation_type'] : '';
$compensationDepartmentFilter = isset($_GET['compensation_department']) ? $_GET['compensation_department'] : '';

// Build WHERE clause for filters
$compensationWhereConditions = [];
$compensationParams = [];

if (!empty($compensationStatusFilter)) {
    $compensationWhereConditions[] = "cr.status = ?";
    $compensationParams[] = $compensationStatusFilter;
}

if (!empty($compensationTypeFilter)) {
    $compensationWhereConditions[] = "cr.review_type = ?";
    $compensationParams[] = $compensationTypeFilter;
}

if (!empty($compensationDepartmentFilter)) {
    $compensationWhereConditions[] = "e.department = ?";
    $compensationParams[] = $compensationDepartmentFilter;
}

$compensationWhereClause = !empty($compensationWhereConditions) ? "WHERE " . implode(" AND ", $compensationWhereConditions) : "";

// Get total pending reviews count for stats
try {
    $pendingReviewsCount = $db->query("
        SELECT COUNT(*) as count 
        FROM compensation_reviews 
        WHERE status IN ('draft', 'pending_finance')
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $pendingReviewsCount = 0;
    error_log("Error fetching pending reviews: " . $th->getMessage());
}

// Get compensation statistics
try {
    // Average salary across all employees (convert hourly to monthly: hourly * 8 hours * 22 days)
    $avgSalary = $db->query("
        SELECT AVG(hourly_rate * 8 * 22) as avg_salary
        FROM employees 
        WHERE hourly_rate > 0 AND status IN ('Active', 'Regular', 'Probationary')
    ")->fetch_one()['avg_salary'] ?? 0;

    // Min and max salary
    $salaryRange = $db->query("
        SELECT 
            MIN(hourly_rate * 8 * 22) as min_salary,
            MAX(hourly_rate * 8 * 22) as max_salary
        FROM employees 
        WHERE hourly_rate > 0 AND status IN ('Active', 'Regular', 'Probationary')
    ")->fetch_one();

    $minSalary = $salaryRange['min_salary'] ?? 0;
    $maxSalary = $salaryRange['max_salary'] ?? 0;

    // Total compensation budget (sum of all monthly salaries)
    $totalBudget = $db->query("
        SELECT SUM(hourly_rate * 8 * 22) as total_budget
        FROM employees 
        WHERE status IN ('Active', 'Regular', 'Probationary')
    ")->fetch_one()['total_budget'] ?? 3450000;

    // Used budget (sum of approved reviews)
    $usedBudget = $db->query("
        SELECT SUM(proposed_salary) as used_budget
        FROM compensation_reviews 
        WHERE status = 'approved'
    ")->fetch_one()['used_budget'] ?? 0;

    $budgetUtilization = $totalBudget > 0 ? round(($usedBudget / $totalBudget) * 100) : 0;

} catch (\Throwable $th) {
    $avgSalary = 0;
    $minSalary = 0;
    $maxSalary = 0;
    $totalBudget = 0;
    $budgetUtilization = 0;
    error_log("Error fetching compensation stats: " . $th->getMessage());
}

// Get distinct departments for filter
try {
    $compensationDepartments = $db->query("
        SELECT DISTINCT department 
        FROM employees 
        WHERE department IS NOT NULL AND department != ''
        ORDER BY department
    ")->find();
} catch (\Throwable $th) {
    $compensationDepartments = [];
    error_log("Error fetching departments: " . $th->getMessage());
}

// Get salary bands by position
try {
    $salaryBands = $db->query("
        SELECT 
            position,
            COUNT(*) as employee_count,
            MIN(hourly_rate * 8 * 22) as min_salary,
            MAX(hourly_rate * 8 * 22) as max_salary,
            AVG(hourly_rate * 8 * 22) as avg_salary,
            (AVG(hourly_rate * 8 * 22) / (SELECT AVG(hourly_rate * 8 * 22) FROM employees WHERE hourly_rate > 0) * 100) as market_ratio
        FROM employees 
        WHERE hourly_rate > 0 AND status IN ('Active', 'Regular', 'Probationary')
        GROUP BY position
        ORDER BY avg_salary DESC
        LIMIT 8
    ")->find();
} catch (\Throwable $th) {
    $salaryBands = [];
    error_log("Error fetching salary bands: " . $th->getMessage());
}

// Get total count for pagination
try {
    $totalCompensationReviews = $db->query("
        SELECT COUNT(*) as count 
        FROM compensation_reviews cr
        JOIN employees e ON cr.employee_id = e.id
        $compensationWhereClause
    ", $compensationParams)->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalCompensationReviews = 0;
    error_log("Error fetching total reviews: " . $th->getMessage());
}

// Get paginated upcoming reviews
try {
    $upcomingReviews = $db->query("
        SELECT 
            cr.*,
            e.id as employee_id,
            e.full_name,
            e.position,
            e.department,
            e.hourly_rate,
            e.hired_date,
            (e.hourly_rate * 8 * 22) as current_monthly,
            DATEDIFF(cr.effective_date, CURDATE()) as days_until_effective,
            CONCAT(LEFT(e.full_name, 1), RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1)) as initials
        FROM compensation_reviews cr
        JOIN employees e ON cr.employee_id = e.id
        $compensationWhereClause
        ORDER BY 
            CASE 
                WHEN cr.status = 'pending_finance' THEN 1
                WHEN cr.status = 'draft' THEN 2
                WHEN cr.status = 'approved' THEN 3
                ELSE 4
            END,
            cr.effective_date ASC
        LIMIT $compensationPerPage OFFSET $compensationOffset
    ", $compensationParams)->find();
} catch (\Throwable $th) {
    $upcomingReviews = [];
    error_log("Error fetching upcoming reviews: " . $th->getMessage());
}

$totalCompensationPages = ceil($totalCompensationReviews / $compensationPerPage);

// Get recent adjustments (approved reviews)
try {
    $recentAdjustments = $db->query("
        SELECT 
            cr.*,
            e.id as employee_id,
            e.full_name,
            e.position,
            e.department,
            (e.hourly_rate * 8 * 22) as current_monthly,
            CONCAT(LEFT(e.full_name, 1), RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1)) as initials
        FROM compensation_reviews cr
        JOIN employees e ON cr.employee_id = e.id
        WHERE cr.status = 'approved'
        ORDER BY cr.finance_approved_at DESC, cr.updated_at DESC
        LIMIT 5
    ")->find();
} catch (\Throwable $th) {
    $recentAdjustments = [];
    error_log("Error fetching recent adjustments: " . $th->getMessage());
}

// Get employees for dropdown (with monthly salary calculation)
try {
    $compensationEmployees = $db->query("
        SELECT id, full_name, position, hourly_rate,
               (hourly_rate * 8 * 22) as monthly_salary
        FROM employees 
        WHERE status IN ('Active', 'Probationary', 'Regular')
        ORDER BY full_name
    ")->find();
} catch (\Throwable $th) {
    $compensationEmployees = [];
    error_log("Error fetching employees for compensation: " . $th->getMessage());
}

// Format currency function
function formatSalary($amount)
{
    return '₱' . number_format($amount, 0);
}

function formatMonthly($hourlyRate)
{
    return '₱' . number_format($hourlyRate * 8 * 22, 0);
}


// ============================================
// HR ANALYTICS DASHBOARD SECTION
// ============================================

// Date range filter
$analyticsDateRange = isset($_GET['analytics_range']) ? (int) $_GET['analytics_range'] : 30;
if ($analyticsDateRange == 0)
    $analyticsDateRange = 30;

// Calculate date ranges
$analyticsEndDate = date('Y-m-d');
$analyticsStartDate = date('Y-m-d', strtotime("-{$analyticsDateRange} days"));

// ============================================
// KPI METRICS
// ============================================

// Average Salary (from employees table)
try {
    $analyticsAvgSalary = $db->query("
        SELECT AVG(hourly_rate * 8 * 22) as avg_salary
        FROM employees 
        WHERE hourly_rate > 0 
        AND status IN ('Active', 'Probationary', 'Regular')
    ")->fetch_one()['avg_salary'] ?? 0;

    // Calculate previous year average for comparison
    $analyticsPrevAvgSalary = $db->query("
        SELECT AVG(hourly_rate * 8 * 22) as avg_salary
        FROM employees 
        WHERE hourly_rate > 0 
        AND status IN ('Active', 'Probationary', 'Regular')
        AND hired_date <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    ")->fetch_one()['avg_salary'] ?? 0;

    $analyticsSalaryIncreasePct = $analyticsPrevAvgSalary > 0
        ? round((($analyticsAvgSalary - $analyticsPrevAvgSalary) / $analyticsPrevAvgSalary) * 100, 1)
        : 0.0;
} catch (\Throwable $th) {
    $analyticsAvgSalary = 0;
    $analyticsSalaryIncreasePct = 0.0;
    error_log("Error calculating avg salary: " . $th->getMessage());
}

// Salary Increases & Promotions
try {
    // Get compensation reviews for increases
    $analyticsSalaryIncrease = $db->query("
        SELECT AVG(increase_percentage) as avg_increase
        FROM compensation_reviews 
        WHERE status = 'approved'
        AND effective_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    ")->fetch_one()['avg_increase'] ?? 0;

    // Employees who received raises/promotions
    $analyticsEmployeesPromoted = $db->query("
        SELECT COUNT(DISTINCT employee_id) as count
        FROM compensation_reviews 
        WHERE status = 'approved'
        AND effective_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    ")->fetch_one()['count'] ?? 0;

    // Round to 1 decimal
    $analyticsSalaryIncrease = round($analyticsSalaryIncrease, 1);

} catch (\Throwable $th) {
    $analyticsSalaryIncrease = 0;
    $analyticsEmployeesPromoted = 0;
    error_log("Error calculating salary increases: " . $th->getMessage());
}

// Training & Certifications Completed
try {
    // Completed trainings
    $analyticsTrainingsCompleted = $db->query("
        SELECT COUNT(*) as count
        FROM training_schedule 
        WHERE status = 'Completed'
        AND end_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    ")->fetch_one()['count'] ?? 0;

    // Certifications earned (where training_type = 'certification' and completed)
    $analyticsCertificationsEarned = $db->query("
        SELECT COUNT(*) as count
        FROM training_schedule 
        WHERE training_type = 'certification'
        AND status = 'Completed'
        AND assessment_status = 'completed'
        AND end_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    ")->fetch_one()['count'] ?? 0;

} catch (\Throwable $th) {
    $analyticsTrainingsCompleted = 0;
    $analyticsCertificationsEarned = 0;
    error_log("Error calculating training metrics: " . $th->getMessage());
}

// Performance Ratings
try {
    // Average performance rating
    $analyticsAvgRating = $db->query("
        SELECT AVG(overall_score) as avg_rating
        FROM performance_evaluations 
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    ")->fetch_one()['avg_rating'] ?? 0;

    // Total number of ratings
    $analyticsTotalRatings = $db->query("
        SELECT COUNT(*) as count
        FROM performance_evaluations 
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    ")->fetch_one()['count'] ?? 0;

    // Round to 1 decimal
    $analyticsAvgRating = round($analyticsAvgRating, 1);

} catch (\Throwable $th) {
    $analyticsAvgRating = 4.2;
    $analyticsTotalRatings = 0;
    error_log("Error calculating performance ratings: " . $th->getMessage());
}

// ============================================
// HEADCOUNT BY DEPARTMENT
// ============================================
try {
    $analyticsDeptHeadcount = $db->query("
        SELECT 
            department,
            COUNT(*) as count,
            ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM employees WHERE status NOT IN ('Resigned', 'Terminated', 'Fired')), 0) as percentage
        FROM employees 
        WHERE status NOT IN ('Resigned', 'Terminated', 'Fired')
        GROUP BY department
        ORDER BY count DESC
    ")->find();

    $analyticsTotalHeadcount = array_sum(array_column($analyticsDeptHeadcount, 'count'));

    // Colors for chart
    $analyticsDeptColors = ['#2563eb', '#16a34a', '#d97706', '#9333ea', '#dc2626', '#0891b2'];

} catch (\Throwable $th) {
    $analyticsDeptHeadcount = [
        ['department' => 'Restaurant', 'count' => 45, 'percentage' => 29],
        ['department' => 'Kitchen', 'count' => 38, 'percentage' => 24],
        ['department' => 'Housekeeping', 'count' => 42, 'percentage' => 27],
        ['department' => 'Admin', 'count' => 31, 'percentage' => 20]
    ];
    $analyticsTotalHeadcount = 156;
    $analyticsDeptColors = ['#2563eb', '#16a34a', '#d97706', '#9333ea'];
    error_log("Error fetching department headcount: " . $th->getMessage());
}

// ============================================
// HIRES VS TERMINATIONS TREND
// ============================================
try {
    // Get last 6 months
    $analyticsMonths = [];
    $analyticsHiresData = [];
    $analyticsTerminationsData = [];

    for ($i = 5; $i >= 0; $i--) {
        $monthStart = date('Y-m-d', strtotime("-{$i} months"));
        $monthEnd = date('Y-m-t', strtotime("-{$i} months"));
        $monthName = date('M', strtotime("-{$i} months"));
        $analyticsMonths[] = $monthName;

        // Hires
        $hires = $db->query("
            SELECT COUNT(*) as count 
            FROM employees 
            WHERE hired_date BETWEEN ? AND ?
        ", [$monthStart, $monthEnd])->fetch_one()['count'] ?? 0;
        $analyticsHiresData[] = $hires;

        // Terminations
        $terms = $db->query("
            SELECT COUNT(*) as count 
            FROM employees 
            WHERE status IN ('Resigned', 'Terminated', 'Fired')
            AND updated_at BETWEEN ? AND ?
        ", [$monthStart, $monthEnd])->fetch_one()['count'] ?? 0;
        $analyticsTerminationsData[] = $terms;
    }

    $analyticsTotalHires = array_sum($analyticsHiresData);
    $analyticsTotalTerminations = array_sum($analyticsTerminationsData);
    $analyticsNetGrowth = $analyticsTotalHires - $analyticsTotalTerminations;

} catch (\Throwable $th) {
    $analyticsMonths = ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];
    $analyticsHiresData = [12, 10, 11, 14, 15, 12];
    $analyticsTerminationsData = [4, 6, 7, 3, 4, 5];
    $analyticsTotalHires = 74;
    $analyticsTotalTerminations = 29;
    $analyticsNetGrowth = 45;
    error_log("Error fetching hires/terminations: " . $th->getMessage());
}

// ============================================
// DEMOGRAPHICS
// ============================================

// Gender Distribution
try {
    $analyticsGenderData = $db->query("
        SELECT 
            gender,
            COUNT(*) as count,
            ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM employees WHERE status NOT IN ('Resigned', 'Terminated', 'Fired')), 0) as percentage
        FROM employees 
        WHERE status NOT IN ('Resigned', 'Terminated', 'Fired')
        AND gender IS NOT NULL
        GROUP BY gender
    ")->find();

    $analyticsFemaleCount = 0;
    $analyticsFemalePct = 0;
    $analyticsMaleCount = 0;
    $analyticsMalePct = 0;

    foreach ($analyticsGenderData as $g) {
        if ($g['gender'] == 'female') {
            $analyticsFemaleCount = $g['count'];
            $analyticsFemalePct = $g['percentage'];
        } elseif ($g['gender'] == 'male') {
            $analyticsMaleCount = $g['count'];
            $analyticsMalePct = $g['percentage'];
        }
    }

    if ($analyticsFemaleCount + $analyticsMaleCount == 0) {
        $analyticsFemalePct = 0;
        $analyticsMalePct = 0;
        $analyticsFemaleCount = 0;
        $analyticsMaleCount = 0;
    }

} catch (\Throwable $th) {
    $analyticsFemalePct = 0;
    $analyticsMalePct = 0;
    $analyticsFemaleCount = 0;
    $analyticsMaleCount = 0;
    error_log("Error fetching gender data: " . $th->getMessage());
}

// Age Distribution
try {
    $analyticsAgeData = $db->query("
        SELECT 
            CASE 
                WHEN age BETWEEN 18 AND 30 THEN '18-30'
                WHEN age BETWEEN 31 AND 45 THEN '31-45'
                WHEN age >= 46 THEN '46+'
            END as age_group,
            COUNT(*) as count,
            ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM employees WHERE status NOT IN ('Resigned', 'Terminated', 'Fired') AND age IS NOT NULL), 0) as percentage
        FROM employees 
        WHERE status NOT IN ('Resigned', 'Terminated', 'Fired')
        AND age IS NOT NULL
        GROUP BY age_group
        ORDER BY age_group
    ")->find();

    $analyticsAge18_30 = 0;
    $analyticsAge31_45 = 0;
    $analyticsAge46 = 0;

    foreach ($analyticsAgeData as $a) {
        if ($a['age_group'] == '18-30')
            $analyticsAge18_30 = $a['percentage'];
        if ($a['age_group'] == '31-45')
            $analyticsAge31_45 = $a['percentage'];
        if ($a['age_group'] == '46+')
            $analyticsAge46 = $a['percentage'];
    }

    if ($analyticsAge18_30 + $analyticsAge31_45 + $analyticsAge46 == 0) {
        $analyticsAge18_30 = 0;
        $analyticsAge31_45 = 0;
        $analyticsAge46 = 0;
    }

} catch (\Throwable $th) {
    $analyticsAge18_30 = 0;
    $analyticsAge31_45 = 0;
    $analyticsAge46 = 0;
    error_log("Error fetching age data: " . $th->getMessage());
}

// Tenure Distribution
try {
    $analyticsTenureData = $db->query("
        SELECT 
            CASE 
                WHEN TIMESTAMPDIFF(YEAR, hired_date, CURDATE()) < 1 THEN '<1 year'
                WHEN TIMESTAMPDIFF(YEAR, hired_date, CURDATE()) BETWEEN 1 AND 3 THEN '1-3 years'
                ELSE '3+ years'
            END as tenure_group,
            COUNT(*) as count,
            ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM employees WHERE status NOT IN ('Resigned', 'Terminated', 'Fired') AND hired_date IS NOT NULL), 0) as percentage
        FROM employees 
        WHERE status NOT IN ('Resigned', 'Terminated', 'Fired')
        AND hired_date IS NOT NULL
        GROUP BY tenure_group
        ORDER BY tenure_group
    ")->find();

    $analyticsTenureLess1 = 0;
    $analyticsTenure1to3 = 0;
    $analyticsTenure3plus = 0;

    foreach ($analyticsTenureData as $t) {
        if ($t['tenure_group'] == '<1 year')
            $analyticsTenureLess1 = $t['percentage'];
        if ($t['tenure_group'] == '1-3 years')
            $analyticsTenure1to3 = $t['percentage'];
        if ($t['tenure_group'] == '3+ years')
            $analyticsTenure3plus = $t['percentage'];
    }

    if ($analyticsTenureLess1 + $analyticsTenure1to3 + $analyticsTenure3plus == 0) {
        $analyticsTenureLess1 = 0;
        $analyticsTenure1to3 = 0;
        $analyticsTenure3plus = 0;
    }

} catch (\Throwable $th) {
    $analyticsTenureLess1 = 0;
    $analyticsTenure1to3 = 0;
    $analyticsTenure3plus = 0;
    error_log("Error fetching tenure data: " . $th->getMessage());
}

// Last sync time
$analyticsLastSync = date('M j, Y h:i A');


// ============================================
// SUCCESSION PLANNING SECTION
// ============================================

// Pagination
$successionPage = isset($_GET['succession_page']) ? max(1, (int) $_GET['succession_page']) : 1;
$successionPerPage = 1;
$successionOffset = ($successionPage - 1) * $successionPerPage;

// Filter parameters
$successionDeptFilter = isset($_GET['succession_dept']) ? $_GET['succession_dept'] : '';
$successionPositionFilter = isset($_GET['succession_position']) ? $_GET['succession_position'] : '';
$successionSortFilter = isset($_GET['succession_sort']) ? $_GET['succession_sort'] : 'readiness';

// Get all employees for the dropdown - without status filter or with correct column name
$allEmployees = $db->query("
    SELECT id, employee_number, full_name 
    FROM employees 
    ORDER BY full_name ASC
")->find();

// If find() returns a single record, convert to array
if (!empty($allEmployees) && !is_array($allEmployees) || (isset($allEmployees['id']) && !isset($allEmployees[0]))) {
    $allEmployees = [$allEmployees];
}

// Pass to view
$data['allEmployees'] = $allEmployees;
// Get distinct departments for filter
try {
    $successionDepartments = $db->query("
        SELECT DISTINCT department 
        FROM employees 
        WHERE department IS NOT NULL AND department != ''
        ORDER BY department
    ")->find();
} catch (\Throwable $th) {
    $successionDepartments = [];
    error_log("Error fetching departments: " . $th->getMessage());
}

// Build WHERE clause for filters
$successionWhereConditions = [];
$successionParams = [];

if (!empty($successionDeptFilter)) {
    $successionWhereConditions[] = "e.department = ?";
    $successionParams[] = $successionDeptFilter;
}

// Base condition: employees who are ready for promotion (completed tasks, trainings, no gaps)
$successionBaseCondition = "
    AND e.status IN ('Active', 'Regular', 'Probationary')
    AND NOT EXISTS (
        SELECT 1 FROM tasks t 
        WHERE t.assigned_to = e.id 
        AND t.status != 'Completed'
    )
    AND NOT EXISTS (
        SELECT 1 FROM training_schedule ts 
        WHERE ts.employee_id = e.id 
        AND ts.status = 'Scheduled'
    )
    AND NOT EXISTS (
        SELECT 1 FROM competency_assessments ca 
        JOIN competencies c ON ca.competency_id = c.id
        WHERE ca.employee_id = e.id 
        AND ca.proficiency_level < c.required_level
    )
";

$successionWhereClause = !empty($successionWhereConditions)
    ? "WHERE " . implode(" AND ", $successionWhereConditions) . " $successionBaseCondition"
    : "WHERE 1=1 $successionBaseCondition";

// Build ORDER BY clause
switch ($successionSortFilter) {
    case 'department':
        $successionOrderBy = "ORDER BY e.department, e.full_name";
        break;
    case 'name':
        $successionOrderBy = "ORDER BY e.full_name";
        break;
    case 'date':
        $successionOrderBy = "ORDER BY e.updated_at DESC";
        break;
    case 'readiness':
    default:
        $successionOrderBy = "ORDER BY readiness_score DESC, e.full_name";
        break;
}

// Get total candidates count
try {
    $totalCandidatesSql = "
        SELECT COUNT(DISTINCT e.id) as count 
        FROM employees e
        $successionWhereClause
    ";
    $totalCandidates = $db->query($totalCandidatesSql, $successionParams)->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalCandidates = 0;
    error_log("Error fetching total candidates: " . $th->getMessage());
}

// Get succession candidates with all metrics
try {
    $successionCandidates = $db->query("
        SELECT 
            e.*,
            -- Task completion metrics
            COALESCE((
                SELECT COUNT(*) FROM tasks 
                WHERE assigned_to = e.id
            ), 0) as total_tasks,
            COALESCE((
                SELECT COUNT(*) FROM tasks 
                WHERE assigned_to = e.id AND status = 'Completed'
            ), 0) as completed_tasks,
            -- Training metrics
            COALESCE((
                SELECT COUNT(*) FROM training_schedule 
                WHERE employee_id = e.id
            ), 0) as total_trainings,
            COALESCE((
                SELECT COUNT(*) FROM training_schedule 
                WHERE employee_id = e.id AND status = 'Completed'
            ), 0) as completed_trainings,
            -- Competency metrics
            COALESCE((
                SELECT COUNT(*) FROM competencies c
                WHERE c.id IN (
                    SELECT competency_id FROM competency_assessments 
                    WHERE employee_id = e.id AND status = 'Passed'
                )
            ), 0) as passed_competencies,
            COALESCE((
                SELECT COUNT(*) FROM competencies
            ), 0) as total_competencies,
            -- Last training date
            (
                SELECT MAX(end_date) FROM training_schedule 
                WHERE employee_id = e.id AND status = 'Completed'
            ) as last_training_date,
            -- Readiness score (custom calculation)
            (
                (
                    COALESCE((
                        SELECT COUNT(*) FROM tasks 
                        WHERE assigned_to = e.id AND status = 'Completed'
                    ), 0) * 100.0 / 
                    NULLIF((
                        SELECT COUNT(*) FROM tasks 
                        WHERE assigned_to = e.id
                    ), 0)
                ) +
                (
                    COALESCE((
                        SELECT COUNT(*) FROM training_schedule 
                        WHERE employee_id = e.id AND status = 'Completed'
                    ), 0) * 100.0 / 
                    NULLIF((
                        SELECT COUNT(*) FROM training_schedule 
                        WHERE employee_id = e.id
                    ), 0)
                ) +
                (
                    COALESCE((
                        SELECT COUNT(*) FROM competency_assessments ca
                        JOIN competencies c ON ca.competency_id = c.id
                        WHERE ca.employee_id = e.id AND ca.proficiency_level >= c.required_level
                    ), 0) * 100.0 / 
                    NULLIF((SELECT COUNT(*) FROM competencies), 0)
                )
            ) / 3 as readiness_score,
            -- Gap check
            CASE 
                WHEN NOT EXISTS (
                    SELECT 1 FROM competency_assessments ca 
                    JOIN competencies c ON ca.competency_id = c.id
                    WHERE ca.employee_id = e.id AND ca.proficiency_level < c.required_level
                ) THEN true
                ELSE false
            END as no_competency_gaps
        FROM employees e
        $successionWhereClause
        $successionOrderBy
        LIMIT $successionPerPage OFFSET $successionOffset
    ", $successionParams)->find();
} catch (\Throwable $th) {
    $successionCandidates = [];
    error_log("Error fetching succession candidates: " . $th->getMessage());
}

$totalSuccessionPages = ceil($totalCandidates / $successionPerPage);

// Get department readiness stats
try {
    $deptReadiness = $db->query("
        SELECT 
            e.department,
            COUNT(DISTINCT e.id) as candidate_count
        FROM employees e
        WHERE e.status IN ('Active', 'Regular', 'Probationary')
        AND NOT EXISTS (
            SELECT 1 FROM tasks t 
            WHERE t.assigned_to = e.id AND t.status != 'Completed'
        )
        AND NOT EXISTS (
            SELECT 1 FROM training_schedule ts 
            WHERE ts.employee_id = e.id AND ts.status = 'Scheduled'
        )
        AND NOT EXISTS (
            SELECT 1 FROM competency_assessments ca 
            JOIN competencies c ON ca.competency_id = c.id
            WHERE ca.employee_id = e.id AND ca.proficiency_level < c.required_level
        )
        GROUP BY e.department
        ORDER BY candidate_count DESC
        LIMIT 3
    ")->find();
} catch (\Throwable $th) {
    $deptReadiness = [];
    error_log("Error fetching department readiness: " . $th->getMessage());
}

// Get total training completions count
try {
    $totalTrainingsCompleted = $db->query("
        SELECT COUNT(*) as count 
        FROM training_schedule 
        WHERE status = 'Completed'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalTrainingsCompleted = 0;
    error_log("Error fetching total trainings: " . $th->getMessage());
}

// Get employees with no competency gaps
try {
    $noGapEmployees = $db->query("
        SELECT COUNT(DISTINCT e.id) as count
        FROM employees e
        WHERE e.status IN ('Active', 'Regular', 'Probationary')
        AND NOT EXISTS (
            SELECT 1 FROM competency_assessments ca 
            JOIN competencies c ON ca.competency_id = c.id
            WHERE ca.employee_id = e.id AND ca.proficiency_level < c.required_level
        )
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $noGapEmployees = 0;
    error_log("Error fetching no gap employees: " . $th->getMessage());
}

// Readiness summary stats
$readyNow = 0;
$ready1to3 = 0;
$ready3to6 = 0;

foreach ($successionCandidates as $candidate) {
    $score = $candidate['readiness_score'] ?? 0;
    if ($score >= 90) {
        $readyNow++;
    } elseif ($score >= 70) {
        $ready1to3++;
    } else {
        $ready3to6++;
    }
}
// ============================================
// SUCCESSION PLANNING SECTION - UPDATED TO SHOW ALL CANDIDATES
// ============================================

// Pagination
$successionPage = isset($_GET['succession_page']) ? max(1, (int) $_GET['succession_page']) : 1;
$successionPerPage = 5; // Changed from 1 to 5 to show more per page
$successionOffset = ($successionPage - 1) * $successionPerPage;

// Filter parameters
$successionDepartmentFilter = isset($_GET['succession_dept']) ? $_GET['succession_dept'] : '';
$successionPositionFilter = isset($_GET['succession_position']) ? $_GET['succession_position'] : '';
$successionSortBy = isset($_GET['succession_sort']) ? $_GET['succession_sort'] : 'name';

// Get distinct departments for filter dropdown
try {
    $successionDepartments = $db->query("
        SELECT DISTINCT department 
        FROM employees 
        WHERE department IS NOT NULL AND department != ''
        ORDER BY department
    ")->find();
} catch (\Throwable $th) {
    $successionDepartments = [];
    error_log("Error fetching departments: " . $th->getMessage());
}

// ============================================
// SUCCESSION STATS
// ============================================

// Ready for Promotion (fully qualified candidates)
try {
    $successionReadyCount = $db->query("
        SELECT COUNT(DISTINCT e.id) as count
        FROM employees e
        WHERE e.status IN ('Active', 'Regular', 'Probationary')
        AND EXISTS (
            SELECT 1 FROM tasks t WHERE t.assigned_to = e.id
        )
        AND EXISTS (
            SELECT 1 FROM training_schedule ts WHERE ts.employee_id = e.id
        )
        AND EXISTS (
            SELECT 1 FROM competency_assessments ca WHERE ca.employee_id = e.id
        )
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $successionReadyCount = 0;
    error_log("Error counting ready candidates: " . $th->getMessage());
}

// Total Training Completed
try {
    $successionTotalTrainings = $db->query("
        SELECT COUNT(*) as count
        FROM training_schedule 
        WHERE status = 'Completed'
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $successionTotalTrainings = 0;
    error_log("Error counting trainings: " . $th->getMessage());
}

// No Competency Gaps
try {
    $noGapEmployees = $db->query("
        SELECT e.id
        FROM employees e
        WHERE e.status IN ('Active', 'Regular', 'Probationary')
        AND NOT EXISTS (
            SELECT 1
            FROM competency_assessments ca
            JOIN competencies c ON ca.competency_id = c.id
            WHERE ca.employee_id = e.id
            AND ca.proficiency_level < c.required_level
        )
        AND EXISTS (
            SELECT 1
            FROM competency_assessments ca
            WHERE ca.employee_id = e.id
        )
    ")->find();

    $successionNoGapsCount = count($noGapEmployees);
} catch (\Throwable $th) {
    $successionNoGapsCount = 0;
    error_log("Error counting no gap employees: " . $th->getMessage());
}

// ============================================
// SUCCESSION CANDIDATES - ALL EMPLOYEES (not just fully qualified)
// ============================================

// Get total candidates count for pagination (ALL employees with tasks, trainings, assessments)
try {
    $successionTotalCandidates = $db->query("
        SELECT COUNT(DISTINCT e.id) as count
        FROM employees e
        WHERE e.status IN ('Active', 'Regular', 'Probationary')
        AND EXISTS (
            SELECT 1 FROM tasks t WHERE t.assigned_to = e.id
        )
        AND EXISTS (
            SELECT 1 FROM training_schedule ts WHERE ts.employee_id = e.id
        )
        AND EXISTS (
            SELECT 1 FROM competency_assessments ca WHERE ca.employee_id = e.id
        )
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $successionTotalCandidates = 0;
    error_log("Error counting candidates: " . $th->getMessage());
}

// Get paginated candidates (ALL candidates, not just fully qualified)
try {
    $orderBy = match ($successionSortBy) {
        'department' => "e.department ASC, e.full_name ASC",
        'name' => "e.full_name ASC",
        'date' => "last_training_date DESC NULLS LAST, e.full_name ASC",
        default => "e.full_name ASC"
    };

    // Build WHERE clause for department filter
    $deptFilterSql = "";
    $deptFilterParams = [];
    if (!empty($successionDepartmentFilter)) {
        $deptFilterSql = "AND e.department = ?";
        $deptFilterParams[] = $successionDepartmentFilter;
    }

    $successionCandidates = $db->query("
        SELECT 
            e.id,
            e.full_name,
            e.position,
            e.department,
            e.hired_date,
            
            -- Task completion
            COALESCE((
                SELECT COUNT(*) FROM tasks t WHERE t.assigned_to = e.id
            ), 0) as total_tasks,
            COALESCE((
                SELECT COUNT(*) FROM tasks t 
                WHERE t.assigned_to = e.id AND t.status = 'Completed'
            ), 0) as completed_tasks,
            
            -- Training completion
            COALESCE((
                SELECT COUNT(*) FROM training_schedule ts WHERE ts.employee_id = e.id
            ), 0) as total_trainings,
            COALESCE((
                SELECT COUNT(*) FROM training_schedule ts 
                WHERE ts.employee_id = e.id AND ts.status = 'Completed'
            ), 0) as completed_trainings,
            
            -- Last training date
            (
                SELECT MAX(ts.end_date) FROM training_schedule ts 
                WHERE ts.employee_id = e.id AND ts.status = 'Completed'
            ) as last_training_date,
            
            -- Competency assessment
            COALESCE((
                SELECT COUNT(*) FROM competency_assessments ca WHERE ca.employee_id = e.id
            ), 0) as total_assessments,
            COALESCE((
                SELECT COUNT(*) FROM competency_assessments ca 
                WHERE ca.employee_id = e.id AND ca.status = 'Passed'
            ), 0) as passed_assessments,
            
            -- All tasks completed flag
            CASE 
                WHEN NOT EXISTS (
                    SELECT 1 FROM tasks t 
                    WHERE t.assigned_to = e.id AND t.status != 'Completed'
                ) AND EXISTS (
                    SELECT 1 FROM tasks t WHERE t.assigned_to = e.id
                ) THEN 1 ELSE 0 
            END as all_tasks_complete,
            
            -- All trainings completed flag
            CASE 
                WHEN NOT EXISTS (
                    SELECT 1 FROM training_schedule ts 
                    WHERE ts.employee_id = e.id AND ts.status != 'Completed'
                ) AND EXISTS (
                    SELECT 1 FROM training_schedule ts WHERE ts.employee_id = e.id
                ) THEN 1 ELSE 0 
            END as all_trainings_complete,
            
            -- No competency gaps flag
            CASE 
                WHEN NOT EXISTS (
                    SELECT 1
                    FROM competency_assessments ca
                    JOIN competencies c ON ca.competency_id = c.id
                    WHERE ca.employee_id = e.id
                    AND ca.proficiency_level < c.required_level
                ) AND EXISTS (
                    SELECT 1 FROM competency_assessments ca WHERE ca.employee_id = e.id
                ) THEN 1 ELSE 0 
            END as no_competency_gaps,
            
            -- Readiness status
            CASE 
                WHEN NOT EXISTS (
                    SELECT 1 FROM tasks t WHERE t.assigned_to = e.id AND t.status != 'Completed'
                )
                AND NOT EXISTS (
                    SELECT 1 FROM training_schedule ts WHERE ts.employee_id = e.id AND ts.status != 'Completed'
                )
                AND NOT EXISTS (
                    SELECT 1
                    FROM competency_assessments ca
                    JOIN competencies c ON ca.competency_id = c.id
                    WHERE ca.employee_id = e.id
                    AND ca.proficiency_level < c.required_level
                ) THEN 'ready_now'
                ELSE 'in_progress'
            END as readiness_status,
            
            CONCAT(LEFT(e.full_name, 1), COALESCE(RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1), RIGHT(e.full_name, 1))) as initials
            
        FROM employees e
        WHERE e.status IN ('Active', 'Regular', 'Probationary')
        AND EXISTS (
            SELECT 1 FROM tasks t WHERE t.assigned_to = e.id
        )
        AND EXISTS (
            SELECT 1 FROM training_schedule ts WHERE ts.employee_id = e.id
        )
        AND EXISTS (
            SELECT 1 FROM competency_assessments ca WHERE ca.employee_id = e.id
        )
        $deptFilterSql
        ORDER BY $orderBy
        LIMIT $successionPerPage OFFSET $successionOffset
    ", $deptFilterParams)->find();

} catch (\Throwable $th) {
    $successionCandidates = [];
    error_log("Error fetching candidates: " . $th->getMessage());
}

$successionTotalPages = ceil($successionTotalCandidates / $successionPerPage);

// ============================================
// DEPARTMENT SUMMARY
// ============================================
try {
    $successionDeptSummary = $db->query("
        SELECT 
            e.department,
            COUNT(DISTINCT CASE 
                WHEN NOT EXISTS (
                    SELECT 1 FROM tasks t 
                    WHERE t.assigned_to = e.id AND t.status != 'Completed'
                )
                AND NOT EXISTS (
                    SELECT 1 FROM training_schedule ts 
                    WHERE ts.employee_id = e.id AND ts.status != 'Completed'
                )
                AND NOT EXISTS (
                    SELECT 1
                    FROM competency_assessments ca
                    JOIN competencies c ON ca.competency_id = c.id
                    WHERE ca.employee_id = e.id
                    AND ca.proficiency_level < c.required_level
                )
                THEN e.id 
            END) as ready_candidates
        FROM employees e
        WHERE e.department IS NOT NULL AND e.department != ''
        GROUP BY e.department
        HAVING ready_candidates > 0
        ORDER BY ready_candidates DESC
        LIMIT 3
    ")->find();
} catch (\Throwable $th) {
    $successionDeptSummary = [];
    error_log("Error fetching dept summary: " . $th->getMessage());
}

// ============================================
// READINESS SUMMARY
// ============================================
// Calculate readiness counts from all candidates
$successionReadyNow = 0;
$successionReadySoon = 0;
$successionInProgress = 0;

foreach ($successionCandidates as $candidate) {
    if ($candidate['readiness_status'] == 'ready_now') {
        $successionReadyNow++;
    } else {
        $successionInProgress++;
    }
}

// Format functions
function getReadinessBadge($status)
{
    return match ($status) {
        'ready_now' => ['text' => 'Ready Now', 'class' => 'bg-green-50 text-green-700 border-green-200'],
        'in_progress' => ['text' => 'In Progress', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
        default => ['text' => 'In Progress', 'class' => 'bg-blue-50 text-blue-700 border-blue-200']
    };
}

function getReadinessIcon($status)
{
    return match ($status) {
        'ready_now' => 'fa-check-circle',
        'in_progress' => 'fa-spinner',
        default => 'fa-spinner'
    };
}

// ============================================
// CLAIMS & REIMBURSEMENT SECTION
// ============================================

// Pagination for claims
$claimsPage = isset($_GET['claims_page']) ? max(1, (int) $_GET['claims_page']) : 1;
$claimsPerPage = 10;
$claimsOffset = ($claimsPage - 1) * $claimsPerPage;

// Filter parameters
$claimsStatusFilter = isset($_GET['claims_status']) ? $_GET['claims_status'] : 'all';
$claimsDateFilter = isset($_GET['claims_date']) ? $_GET['claims_date'] : '30';
$claimsActiveTab = isset($_GET['panel']) && $_GET['panel'] == 'history' ? 'history' : 'new';

// Calculate date range based on filter
$claimsDateCondition = "";
$claimsParams = [];

if ($claimsDateFilter === '30') {
    $claimsDateCondition = "AND expense_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
} elseif ($claimsDateFilter === '90') {
    $claimsDateCondition = "AND expense_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
} elseif ($claimsDateFilter === '180') {
    $claimsDateCondition = "AND expense_date >= DATE_SUB(CURDATE(), INTERVAL 180 DAY)";
} elseif ($claimsDateFilter === '365') {
    $claimsDateCondition = "AND expense_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
}

// Status condition
$claimsStatusCondition = "";
if ($claimsStatusFilter !== 'all') {
    $claimsStatusCondition = "AND status = ?";
    $claimsParams[] = $claimsStatusFilter;
}

// ============================================
// CLAIMS STATS
// ============================================

// Pending claims count and total
try {
    $claimsPendingStats = $db->query("
        SELECT 
            COUNT(*) as count,
            COALESCE(SUM(amount), 0) as total
        FROM expense_claims 
        WHERE status = 'Pending'
    ")->fetch_one();

    $claimsPendingCount = $claimsPendingStats['count'] ?? 0;
    $claimsPendingTotal = $claimsPendingStats['total'] ?? 0;
} catch (\Throwable $th) {
    $claimsPendingCount = 8;
    $claimsPendingTotal = 24500;
    error_log("Error fetching pending claims: " . $th->getMessage());
}

// Approved claims count and total
try {
    $claimsApprovedStats = $db->query("
        SELECT 
            COUNT(*) as count,
            COALESCE(SUM(amount), 0) as total
        FROM expense_claims 
        WHERE status = 'Approved'
    ")->fetch_one();

    $claimsApprovedCount = $claimsApprovedStats['count'] ?? 0;
    $claimsApprovedTotal = $claimsApprovedStats['total'] ?? 0;
} catch (\Throwable $th) {
    $claimsApprovedCount = 15;
    $claimsApprovedTotal = 45200;
    error_log("Error fetching approved claims: " . $th->getMessage());
}

// Processed this month claims
try {
    $claimsProcessedStats = $db->query("
        SELECT 
            COUNT(*) as count,
            COALESCE(SUM(amount), 0) as total
        FROM expense_claims 
        WHERE status IN ('Approved', 'Paid')
        AND MONTH(expense_date) = MONTH(CURDATE())
        AND YEAR(expense_date) = YEAR(CURDATE())
    ")->fetch_one();

    $claimsProcessedCount = $claimsProcessedStats['count'] ?? 0;
    $claimsProcessedTotal = $claimsProcessedStats['total'] ?? 0;
} catch (\Throwable $th) {
    $claimsProcessedCount = 23;
    $claimsProcessedTotal = 67800;
    error_log("Error fetching processed claims: " . $th->getMessage());
}

// ============================================
// CLAIMS LIST
// ============================================

// Get total claims count for pagination
try {
    $claimsTotalSql = "
        SELECT COUNT(*) as count 
        FROM expense_claims 
        WHERE 1=1 
        $claimsStatusCondition
        $claimsDateCondition
    ";
    $claimsTotal = $db->query($claimsTotalSql, $claimsParams)->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $claimsTotal = 46;
    error_log("Error fetching total claims: " . $th->getMessage());
}

// Get paginated claims
try {
    $claimsList = $db->query("
        SELECT 
            c.*,
            e.full_name as employee_name,
            e.employee_number,
            e.id as employee_id,
            DATE_FORMAT(c.expense_date, '%b %e, %Y') as formatted_date,
            DATE_FORMAT(c.created_at, '%b %e, %Y') as formatted_created,
            CONCAT('CL-', YEAR(c.created_at), '-', LPAD(c.id, 4, '0')) as claim_number,
            CONCAT(LEFT(e.full_name, 1), COALESCE(RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1), RIGHT(e.full_name, 1))) as initials
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

$claimsTotalPages = ceil($claimsTotal / $claimsPerPage);

// ============================================
// CLAIMS STATS FOOTER
// ============================================

// Total pending amount from filtered list (for footer)
$claimsTotalPendingAmount = 0;
$claimsTotalApprovedAmount = 0;

foreach ($claimsList as $claim) {
    if ($claim['status'] == 'Pending') {
        $claimsTotalPendingAmount += $claim['amount'];
    } elseif ($claim['status'] == 'Approved') {
        $claimsTotalApprovedAmount += $claim['amount'];
    }
}

// Average claim amount
try {
    $claimsAverageAmount = $db->query("
        SELECT AVG(amount) as avg_amount
        FROM expense_claims 
        WHERE 1=1
        $claimsDateCondition
    ")->fetch_one()['avg_amount'] ?? 0;
} catch (\Throwable $th) {
    $claimsAverageAmount = 0;
    error_log("Error fetching average claim: " . $th->getMessage());
}

// Processing time (average days from submission to approval)
try {
    $claimsProcessingTime = $db->query("
        SELECT AVG(DATEDIFF(approved_at, created_at)) as avg_days
        FROM expense_claims 
        WHERE status IN ('Approved', 'Paid')
        AND approved_at IS NOT NULL
        $claimsDateCondition
    ")->fetch_one()['avg_days'] ?? 2.4;

    $claimsProcessingTime = round($claimsProcessingTime, 1);
} catch (\Throwable $th) {
    $claimsProcessingTime = 2.4;
    error_log("Error fetching processing time: " . $th->getMessage());
}

// Most common claim type
try {
    $claimsMostCommon = $db->query("
        SELECT category, COUNT(*) as count
        FROM expense_claims 
        GROUP BY category
        ORDER BY count DESC
        LIMIT 1
    ")->fetch_one();

    $claimsMostCommonType = $claimsMostCommon['category'] ?? 'Meal Allowance';
} catch (\Throwable $th) {
    $claimsMostCommonType = 'Meal Allowance';
    error_log("Error fetching most common type: " . $th->getMessage());
}

// This month total
try {
    $claimsMonthTotal = $db->query("
        SELECT COALESCE(SUM(amount), 0) as total
        FROM expense_claims 
        WHERE MONTH(expense_date) = MONTH(CURDATE())
        AND YEAR(expense_date) = YEAR(CURDATE())
    ")->fetch_one()['total'] ?? 67800;
} catch (\Throwable $th) {
    $claimsMonthTotal = 67800;
    error_log("Error fetching month total: " . $th->getMessage());
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function getClaimStatusClass($status)
{
    return match ($status) {
        'Pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        'Approved' => 'bg-green-50 text-green-700 border-green-200',
        'Paid' => 'bg-blue-50 text-blue-700 border-blue-200',
        'Rejected' => 'bg-red-50 text-red-700 border-red-200',
        'Processing' => 'bg-blue-50 text-blue-700 border-blue-200',
        default => 'bg-gray-50 text-gray-700 border-gray-200'
    };
}

function getClaimIcon($category)
{
    $category = strtolower($category ?? '');
    return match (true) {
        str_contains($category, 'meal') || str_contains($category, 'food') || str_contains($category, 'dinner') => 'fa-utensils',
        str_contains($category, 'transport') || str_contains($category, 'gas') => 'fa-taxi',
        str_contains($category, 'medical') || str_contains($category, 'health') => 'fa-hospital',
        str_contains($category, 'training') || str_contains($category, 'seminar') => 'fa-graduation-cap',
        str_contains($category, 'travel') || str_contains($category, 'plane') => 'fa-plane',
        str_contains($category, 'supplies') || str_contains($category, 'office') => 'fa-boxes',
        str_contains($category, 'communication') || str_contains($category, 'internet') => 'fa-wifi',
        default => 'fa-receipt'
    };
}

// ============================================
// HMO & BENEFITS ADMINISTRATION SECTION
// ============================================

// Pagination for benefits
$hmoPage = isset($_GET['hmo_page']) ? max(1, (int) $_GET['hmo_page']) : 1;
$hmoPerPage = 10; // Changed from 5 to 10
$hmoOffset = ($hmoPage - 1) * $hmoPerPage;

// Filter parameters
$hmoProviderFilter = isset($_GET['hmo_provider']) ? $_GET['hmo_provider'] : '';
$hmoStatusFilter = isset($_GET['hmo_status']) ? $_GET['hmo_status'] : '';

// ============================================
// HMO STATS
// ============================================

// Get total employees count
try {
    $hmoTotalEmployees = $db->query("
        SELECT COUNT(*) as count 
        FROM employees 
        WHERE status IN ('Active', 'Regular', 'Probationary')
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $hmoTotalEmployees = 0;
    error_log("Error fetching total employees: " . $th->getMessage());
}

// Get enrolled employees count
try {
    $hmoEnrolledCount = $db->query("
        SELECT COUNT(DISTINCT employee_id) as count
        FROM employee_benefits
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $hmoEnrolledCount = 0;
    error_log("Error fetching enrolled count: " . $th->getMessage());
}

// Calculate coverage rate
$hmoCoverageRate = $hmoTotalEmployees > 0 ? round(($hmoEnrolledCount / $hmoTotalEmployees) * 100) : 0;

// Get pending enrollment count
try {
    $hmoPendingCount = $db->query("
        SELECT COUNT(DISTINCT e.id) as count
        FROM employees e
        WHERE e.status IN ('Active', 'Regular', 'Probationary')
        AND NOT EXISTS (
            SELECT 1 FROM employee_benefits eb WHERE eb.employee_id = e.id
        )
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $hmoPendingCount = 0;
    error_log("Error fetching pending count: " . $th->getMessage());
}

// Get claims this month (from expense_claims table with medical/health category)
try {
    $hmoClaimsThisMonth = $db->query("
        SELECT 
            COUNT(*) as count,
            COALESCE(SUM(amount), 0) as total
        FROM expense_claims 
        WHERE (category LIKE '%Medical%' OR category LIKE '%Health%' OR category LIKE '%HMO%')
        AND MONTH(expense_date) = MONTH(CURDATE())
        AND YEAR(expense_date) = YEAR(CURDATE())
    ")->fetch_one();

    $hmoClaimsCount = $hmoClaimsThisMonth['count'] ?? 0;
    $hmoClaimsTotal = $hmoClaimsThisMonth['total'] ?? 0;
} catch (\Throwable $th) {
    $hmoClaimsCount = 0;
    $hmoClaimsTotal = 0;
    error_log("Error fetching claims: " . $th->getMessage());
}

// Get premium due (next payment)
try {
    $hmoPremiumDue = $db->query("
        SELECT COALESCE(SUM(monthly_premium), 0) as total
        FROM employee_benefits 
        WHERE expiry_date >= CURDATE()
        OR expiry_date IS NULL
    ")->fetch_one()['total'] ?? 0;
} catch (\Throwable $th) {
    $hmoPremiumDue = 0;
    error_log("Error fetching premium due: " . $th->getMessage());
}

// ============================================
// COVERAGE SUMMARY (Active Plans)
// ============================================
try {
    $hmoCoveragePlans = $db->query("
        SELECT 
            bp.provider_name,
            bp.id as provider_id,
            COUNT(DISTINCT eb.employee_id) as enrolled_count,
            COALESCE(SUM(eb.monthly_premium), 0) as total_premium,
            COALESCE(AVG(eb.coverage_amount), 0) as avg_coverage
        FROM benefit_providers bp
        LEFT JOIN employee_benefits eb ON bp.id = eb.provider_id
        GROUP BY bp.id, bp.provider_name
        ORDER BY enrolled_count DESC
        LIMIT 3
    ")->find();
} catch (\Throwable $th) {
    $hmoCoveragePlans = [];
    error_log("Error fetching coverage plans: " . $th->getMessage());
}

// Calculate totals
$hmoTotalMonthlyPremium = 0;
$hmoCompanyShare = 0;
$hmoEmployeeShare = 0;

foreach ($hmoCoveragePlans as $plan) {
    $hmoTotalMonthlyPremium += $plan['total_premium'];
}
$hmoCompanyShare = $hmoTotalMonthlyPremium * 0.7; // 70% company share
$hmoEmployeeShare = $hmoTotalMonthlyPremium * 0.3; // 30% employee share

// ============================================
// EXPIRING SOON (Next 30 days)
// ============================================
try {
    $hmoExpiringSoon = $db->query("
        SELECT 
            eb.*,
            e.id as employee_id,
            e.full_name,
            e.position,
            e.department,
            bp.provider_name,
            DATEDIFF(eb.expiry_date, CURDATE()) as days_until_expiry,
            CONCAT(LEFT(e.full_name, 1), COALESCE(RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1), RIGHT(e.full_name, 1))) as initials
        FROM employee_benefits eb
        JOIN employees e ON eb.employee_id = e.id
        JOIN benefit_providers bp ON eb.provider_id = bp.id
        WHERE eb.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        ORDER BY eb.expiry_date ASC
        LIMIT 3
    ")->find();
} catch (\Throwable $th) {
    $hmoExpiringSoon = [];
    error_log("Error fetching expiring soon: " . $th->getMessage());
}

// ============================================
// RECENT ENROLLMENTS
// ============================================
try {
    $hmoRecentEnrollments = $db->query("
        SELECT 
            eb.*,
            e.id as employee_id,
            e.full_name,
            e.position,
            bp.provider_name,
            DATEDIFF(CURDATE(), eb.created_at) as days_ago,
            CASE 
                WHEN eb.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 'Processed'
                ELSE 'Pending'
            END as enrollment_status,
            CONCAT(LEFT(e.full_name, 1), COALESCE(RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1), RIGHT(e.full_name, 1))) as initials
        FROM employee_benefits eb
        JOIN employees e ON eb.employee_id = e.id
        JOIN benefit_providers bp ON eb.provider_id = bp.id
        ORDER BY eb.created_at DESC
        LIMIT 3
    ")->find();
} catch (\Throwable $th) {
    $hmoRecentEnrollments = [];
    error_log("Error fetching recent enrollments: " . $th->getMessage());
}

// ============================================
// BENEFITS LIST WITH PAGINATION
// ============================================

// Build WHERE clause for filters
$hmoWhereConditions = [];
$hmoParams = [];

if (!empty($hmoProviderFilter)) {
    $hmoWhereConditions[] = "bp.id = ?";
    $hmoParams[] = $hmoProviderFilter;
}

if (!empty($hmoStatusFilter)) {
    if ($hmoStatusFilter === 'active') {
        $hmoWhereConditions[] = "(eb.expiry_date >= CURDATE() OR eb.expiry_date IS NULL)";
    } elseif ($hmoStatusFilter === 'expiring') {
        $hmoWhereConditions[] = "eb.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
    } elseif ($hmoStatusFilter === 'expired') {
        $hmoWhereConditions[] = "eb.expiry_date < CURDATE()";
    }
}

$hmoWhereClause = !empty($hmoWhereConditions) ? "WHERE " . implode(" AND ", $hmoWhereConditions) : "";

// Debug: Check if employee_benefits has data
$debugCheck = $db->query("SELECT COUNT(*) as count FROM employee_benefits")->fetch_one();
error_log("Employee benefits count: " . ($debugCheck['count'] ?? 0));

// Get total benefits count for pagination
try {
    $hmoTotalBenefits = $db->query("
        SELECT COUNT(*) as count
        FROM employee_benefits eb
        JOIN employees e ON eb.employee_id = e.id
        JOIN benefit_providers bp ON eb.provider_id = bp.id
        " . (!empty($hmoWhereConditions) ? $hmoWhereClause : "")
    ,
        $hmoParams
    )->fetch_one()['count'] ?? 0;

    error_log("HMO Total Benefits: " . $hmoTotalBenefits);
} catch (\Throwable $th) {
    $hmoTotalBenefits = 0;
    error_log("Error fetching total benefits: " . $th->getMessage());
}

// Get paginated benefits list
try {
    $hmoBenefitsList = $db->query("
        SELECT 
            eb.*,
            e.id as employee_id,
            e.full_name,
            e.position,
            e.department,
            e.employee_number,
            bp.provider_name,
            bp.contact_info,
            DATE_FORMAT(eb.effective_date, '%b %e, %Y') as formatted_effective,
            DATE_FORMAT(eb.expiry_date, '%b %e, %Y') as formatted_expiry,
            CASE 
                WHEN eb.expiry_date IS NULL THEN 'bg-green-50 text-green-700 border-green-200'
                WHEN eb.expiry_date < CURDATE() THEN 'bg-red-50 text-red-700 border-red-200'
                WHEN eb.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'bg-yellow-50 text-yellow-700 border-yellow-200'
                ELSE 'bg-green-50 text-green-700 border-green-200'
            END as status_class,
            CASE 
                WHEN eb.expiry_date IS NULL THEN 'Active'
                WHEN eb.expiry_date < CURDATE() THEN 'Expired'
                WHEN eb.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'Expiring Soon'
                ELSE 'Active'
            END as status_text,
            CONCAT(LEFT(e.full_name, 1), COALESCE(RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1), RIGHT(e.full_name, 1))) as initials
        FROM employee_benefits eb
        JOIN employees e ON eb.employee_id = e.id
        JOIN benefit_providers bp ON eb.provider_id = bp.id
        " . (!empty($hmoWhereConditions) ? $hmoWhereClause : "") . "
        ORDER BY 
            CASE 
                WHEN eb.expiry_date IS NULL THEN 1
                WHEN eb.expiry_date < CURDATE() THEN 4
                WHEN eb.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 2
                ELSE 3
            END,
            eb.expiry_date ASC,
            eb.created_at DESC
        LIMIT $hmoPerPage OFFSET $hmoOffset
    ", $hmoParams)->find();

    error_log("HMO Benefits List count: " . count($hmoBenefitsList));

} catch (\Throwable $th) {
    $hmoBenefitsList = [];
    error_log("Error fetching benefits list: " . $th->getMessage());
}

$hmoTotalPages = ceil($hmoTotalBenefits / $hmoPerPage);

// Get providers for filter dropdown
try {
    $hmoProviders = $db->query("
        SELECT id, provider_name
        FROM benefit_providers
        ORDER BY provider_name
    ")->find();
} catch (\Throwable $th) {
    $hmoProviders = [];
    error_log("Error fetching providers: " . $th->getMessage());
}

// Format currency function for HMO
function formatHmoCurrency($amount)
{
    return '₱' . number_format($amount, 0);
}
// ============================================
// PAYROLL MANAGEMENT SECTION WITH CLAIMS
// ============================================

// Get current date info
$payrollToday = date('Y-m-d');
$payrollCurrentDay = (int) date('d');
$payrollCurrentMonth = date('m');
$payrollCurrentYear = date('Y');

// Determine current payroll period
if ($payrollCurrentDay <= 5) {
    // 1st cutoff (21st of previous month - 5th of current month)
    $payrollPeriodStart = date('Y-m-21', strtotime('-1 month'));
    $payrollPeriodEnd = date('Y-m-05');
    $payrollPeriodLabel = date('M j', strtotime($payrollPeriodStart)) . ' - ' . date('M j, Y', strtotime($payrollPeriodEnd));
    $payrollPayDate = date('Y-m-10'); // Pay on 10th
    $payrollPeriodType = '1st Cutoff';
} elseif ($payrollCurrentDay <= 20) {
    // 2nd cutoff (6th - 20th of current month)
    $payrollPeriodStart = date('Y-m-06');
    $payrollPeriodEnd = date('Y-m-20');
    $payrollPeriodLabel = date('M j', strtotime($payrollPeriodStart)) . ' - ' . date('M j, Y', strtotime($payrollPeriodEnd));
    $payrollPayDate = date('Y-m-25'); // Pay on 25th
    $payrollPeriodType = '2nd Cutoff';
} else {
    // 1st cutoff of next month (21st - end of month)
    $payrollPeriodStart = date('Y-m-21');
    $payrollPeriodEnd = date('Y-m-t');
    $payrollPeriodLabel = date('M j', strtotime($payrollPeriodStart)) . ' - ' . date('M j, Y', strtotime($payrollPeriodEnd));
    $payrollPayDate = date('Y-m-05', strtotime('+1 month')); // Pay on 5th of next month
    $payrollPeriodType = '1st Cutoff (Next Month)';
}

// Pagination
$payrollPage = isset($_GET['payroll_page']) ? max(1, (int) $_GET['payroll_page']) : 1;
$payrollPerPage = 10;
$payrollOffset = ($payrollPage - 1) * $payrollPerPage;

// Filter parameters
$payrollStatusFilter = isset($_GET['payroll_status']) ? $_GET['payroll_status'] : '';
$payrollDepartmentFilter = isset($_GET['payroll_department']) ? $_GET['payroll_department'] : '';

// Get statutory deductions
try {
    $payrollStatutoryDeductions = $db->query("
        SELECT deduction_name, deduction_amount
        FROM statutory_deductions
        ORDER BY id
    ")->find();

    $payrollTotalStatutory = 0;
    foreach ($payrollStatutoryDeductions as $deduction) {
        $payrollTotalStatutory += $deduction['deduction_amount'];
    }
} catch (\Throwable $th) {
    $payrollStatutoryDeductions = [
        ['deduction_name' => 'SSS', 'deduction_amount' => 450],
        ['deduction_name' => 'PhilHealth', 'deduction_amount' => 250],
        ['deduction_name' => 'PagIBIG', 'deduction_amount' => 100]
    ];
    $payrollTotalStatutory = 800;
    error_log("Error fetching statutory deductions: " . $th->getMessage());
}

// Get distinct departments for filter
try {
    $payrollDepartments = $db->query("
        SELECT DISTINCT department 
        FROM employees 
        WHERE department IS NOT NULL AND department != ''
        ORDER BY department
    ")->find();
} catch (\Throwable $th) {
    $payrollDepartments = [];
    error_log("Error fetching departments: " . $th->getMessage());
}

// Build WHERE clause for filters
$payrollWhereConditions = [];
$payrollParams = [];

if (!empty($payrollDepartmentFilter)) {
    $payrollWhereConditions[] = "e.department = ?";
    $payrollParams[] = $payrollDepartmentFilter;
}

$payrollWhereClause = !empty($payrollWhereConditions) ? "WHERE " . implode(" AND ", $payrollWhereConditions) : "";

// ============================================
// PAYROLL WITH APPROVED ATTENDANCE SUMMARIES ONLY
// ============================================

// Get all active employees for payroll with their payroll summary status
$payrollBaseSql = "
    SELECT 
        e.id,
        e.full_name,
        e.position,
        e.department,
        e.hourly_rate,
        -- Attendance totals from APPROVED summaries
        COALESCE((
            SELECT total_regular_hours 
            FROM attendance_summary 
            WHERE employee_id = e.id 
            AND period_start = ? 
            AND period_end = ?
            AND status = 'approved'
        ), 0) as total_regular_hours,
        COALESCE((
            SELECT total_overtime_hours 
            FROM attendance_summary 
            WHERE employee_id = e.id 
            AND period_start = ? 
            AND period_end = ?
            AND status = 'approved'
        ), 0) as total_overtime_hours,
        -- Claims totals - FIXED: Using expense_date instead of approved_at
        COALESCE((
            SELECT SUM(amount) 
            FROM expense_claims 
            WHERE employee_id = e.id 
            AND status = 'Approved'
            AND expense_date BETWEEN ? AND ?
        ), 0) as total_claims_amount,
        (
            SELECT COUNT(*) 
            FROM expense_claims 
            WHERE employee_id = e.id 
            AND status = 'Approved'
            AND expense_date BETWEEN ? AND ?
        ) as claims_count,
        -- Payroll summary status (if exists)
        ps.id as payroll_summary_id,
        ps.status as payroll_status,
        ps.generated_at as payroll_processed_date,
        -- Attendance summary status for reference
        as2.status as attendance_summary_status
    FROM employees e
    LEFT JOIN payroll_summary ps ON e.id = ps.employee_id 
        AND ps.period_start = ? 
        AND ps.period_end = ?
    LEFT JOIN attendance_summary as2 ON e.id = as2.employee_id 
        AND as2.period_start = ? 
        AND as2.period_end = ?
    WHERE e.status IN ('Active', 'Regular', 'Probationary')
";

if (!empty($payrollWhereClause)) {
    $payrollBaseSql .= " AND " . substr($payrollWhereClause, 6);
}

$payrollBaseSql .= " ORDER BY e.full_name";

try {
    $payrollAllEmployees = $db->query(
        $payrollBaseSql,
        [
            // Attendance summary params (4) - for regular and overtime
            $payrollPeriodStart,
            $payrollPeriodEnd,
            $payrollPeriodStart,
            $payrollPeriodEnd,
            // Claims params (4)
            $payrollPeriodStart,
            $payrollPeriodEnd,
            $payrollPeriodStart,
            $payrollPeriodEnd,
            // Payroll summary join params (2)
            $payrollPeriodStart,
            $payrollPeriodEnd,
            // Attendance summary join params (2)
            $payrollPeriodStart,
            $payrollPeriodEnd
        ]
    )->find();
} catch (\Throwable $th) {
    $payrollAllEmployees = [];
    error_log("Error fetching employees for payroll: " . $th->getMessage());
}

// Initialize counters
$payrollTotalGross = 0;
$payrollTotalStatutoryDeductions = 0;
$payrollTotalClaims = 0;
$payrollTotalNet = 0;
$payrollProcessedCount = 0;
$payrollPendingCount = 0;
$payrollNoDataCount = 0;

// Build the $payrollEmployees array with all calculated data
$payrollEmployees = [];
foreach ($payrollAllEmployees as $emp) {
    // Calculate gross pay (regular + overtime)
    $regularPay = $emp['total_regular_hours'] * ($emp['hourly_rate'] ?: 0);
    $overtimePay = $emp['total_overtime_hours'] * ($emp['hourly_rate'] ?: 0) * 1.25;
    $grossPay = $regularPay + $overtimePay;

    // Statutory deductions are fixed per employee
    $statutoryDeductions = $payrollTotalStatutory;

    // Get approved claims for this period
    $claimsAmount = $emp['total_claims_amount'] ?? 0;

    // Net pay is gross minus statutory deductions PLUS approved claims
    $netPay = max(0, $grossPay - $statutoryDeductions + $claimsAmount);

    // Determine status: Use payroll_summary status if exists, otherwise calculate
    if (!empty($emp['payroll_status'])) {
        $status = $emp['payroll_status'];
    } else {
        // Only show as Pending if there's approved attendance OR claims
        $hasApprovedAttendance = ($emp['total_regular_hours'] > 0 || $emp['total_overtime_hours'] > 0);
        $hasClaims = ($claimsAmount > 0);
        $status = ($hasApprovedAttendance || $hasClaims) ? 'Pending' : 'No Data';
    }

    // Update counters based on status
    if ($status == 'Processed' || $status == 'Processing') {
        $payrollProcessedCount++;
    } elseif ($status == 'Pending') {
        $payrollPendingCount++;
    } elseif ($status == 'No Data') {
        $payrollNoDataCount++;
    }

    $payrollTotalGross += $grossPay;
    $payrollTotalStatutoryDeductions += $statutoryDeductions;
    $payrollTotalClaims += $claimsAmount;
    $payrollTotalNet += $netPay;

    // Add all calculated data to the employee array
    $emp['regular_pay'] = $regularPay;
    $emp['overtime_pay'] = $overtimePay;
    $emp['gross_pay'] = $grossPay;
    $emp['statutory_deductions'] = $statutoryDeductions;
    $emp['total_deductions'] = $statutoryDeductions;
    $emp['claims_amount'] = $claimsAmount;
    $emp['claims_count'] = $emp['claims_count'] ?? 0;
    $emp['net_pay'] = $netPay;
    $emp['net_pay_without_claims'] = max(0, $grossPay - $statutoryDeductions);
    $emp['status'] = $status;
    $emp['payroll_summary_id'] = $emp['payroll_summary_id'] ?? null;
    $emp['payroll_processed_date'] = $emp['payroll_processed_date'] ?? null;
    $emp['attendance_summary_status'] = $emp['attendance_summary_status'] ?? 'none';
    $emp['initials'] = getInitials($emp['full_name']);

    $payrollEmployees[] = $emp;
}

$payrollTotalEmployees = count($payrollEmployees);


// Get total count of payroll history periods
try {
    $result = $db->query("
        SELECT COUNT(DISTINCT CONCAT(period_start, period_end)) as total
        FROM payroll_summary
        WHERE status = 'Processed'
    ")->find();
    $payrollHistoryTotal = $result[0]['total'] ?? 0;
} catch (\Throwable $th) {
    $payrollHistoryTotal = 0;
    error_log("Error counting payroll history: " . $th->getMessage());
}

// Get ALL payroll history (unpaginated)
try {
    $allPayrollHistory = $db->query("
        SELECT 
            period_start,
            period_end,
            COUNT(DISTINCT employee_id) as employee_count,
            SUM(gross_pay) as total_gross,
            SUM(net_pay) as total_net,
            SUM(claims) as total_claims,
            MAX(generated_at) as last_generated,
            GROUP_CONCAT(DISTINCT status) as statuses
        FROM payroll_summary
        WHERE status = 'Processed'
        GROUP BY period_start, period_end
        ORDER BY period_start DESC, period_end DESC
    ")->find();
} catch (\Throwable $th) {
    $allPayrollHistory = [];
    error_log("Error fetching all payroll history: " . $th->getMessage());
}

// ============================================
// CALCULATE APPROVED AND PENDING COUNTS FOR PROCESS ALL BUTTON
// ============================================
// Now we can safely access all the data because $payrollEmployees is fully built

$payrollReadyForProcessing = 0; // Approved attendance + NOT processed
$payrollTotalApproved = 0;      // Total with approved attendance (including processed)
$payrollApprovedAndProcessed = 0; // Approved attendance + already processed
$payrollPendingAttendanceCount = 0; // Pending attendance approval

foreach ($payrollEmployees as $emp) {
    if ($emp['attendance_summary_status'] == 'approved') {
        $payrollTotalApproved++;

        // Check if it's already processed
        if ($emp['status'] == 'Processed' || $emp['status'] == 'Processing') {
            $payrollApprovedAndProcessed++;
        } else {
            $payrollReadyForProcessing++;
        }
    } elseif ($emp['attendance_summary_status'] == 'pending') {
        $payrollPendingAttendanceCount++;
    }
}

// For the Process All button, we use $payrollReadyForProcessing
// For display counters, we have all the data we need

// ============================================
// FILTER EMPLOYEES BASED ON STATUS
// ============================================

// Filter employees based on status if needed
if (!empty($payrollStatusFilter)) {
    $payrollFilteredEmployees = array_filter($payrollEmployees, function ($emp) use ($payrollStatusFilter) {
        return $emp['status'] == $payrollStatusFilter;
    });
} else {
    $payrollFilteredEmployees = $payrollEmployees;
}

// Apply department filter
if (!empty($payrollDepartmentFilter)) {
    $payrollFilteredEmployees = array_filter($payrollFilteredEmployees, function ($emp) use ($payrollDepartmentFilter) {
        return $emp['department'] == $payrollDepartmentFilter;
    });
}

// Re-index array
$payrollFilteredEmployees = array_values($payrollFilteredEmployees);
$payrollTotalFiltered = count($payrollFilteredEmployees);

// Paginate
$payrollPaginatedEmployees = array_slice($payrollFilteredEmployees, $payrollOffset, $payrollPerPage);
$payrollTotalPages = ceil($payrollTotalFiltered / $payrollPerPage);

// Calculate footer totals for displayed page
$payrollPageRegularHours = 0;
$payrollPageOvertimeHours = 0;
$payrollPageClaimsTotal = 0;
$payrollPageNetTotal = 0;

foreach ($payrollPaginatedEmployees as $emp) {
    $payrollPageRegularHours += $emp['total_regular_hours'];
    $payrollPageOvertimeHours += $emp['total_overtime_hours'];
    $payrollPageClaimsTotal += $emp['claims_amount'];
    $payrollPageNetTotal += $emp['net_pay'];
}
$payrollPageAverageNet = $payrollPageNetTotal > 0 ? round($payrollPageNetTotal / count($payrollPaginatedEmployees)) : 0;

// Format currency function
function formatPayrollCurrency($amount)
{
    return '₱' . number_format($amount, 2);
}

// ============================================
// PAYROLL HISTORY SECTION
// ============================================

// Get payroll history grouped by period
try {
    $payrollHistory = $db->query("
        SELECT 
            period_start,
            period_end,
            COUNT(DISTINCT employee_id) as employee_count,
            SUM(gross_pay) as total_gross,
            SUM(net_pay) as total_net,
            SUM(claims) as total_claims,
            MAX(generated_at) as last_generated,
            GROUP_CONCAT(DISTINCT status) as statuses
        FROM payroll_summary
        WHERE status = 'Processed'
        GROUP BY period_start, period_end
        ORDER BY period_start DESC, period_end DESC
        LIMIT 3
    ")->find();
} catch (\Throwable $th) {
    $payrollHistory = [];
    error_log("Error fetching payroll history: " . $th->getMessage());
}

// Handle Excel export request
if (isset($_GET['export_payroll']) && isset($_GET['period_start']) && isset($_GET['period_end'])) {
    $periodStart = $_GET['period_start'];
    $periodEnd = $_GET['period_end'];

    // Get payroll data for this period
    $exportData = $db->query("
        SELECT 
            e.employee_number,
            e.full_name,
            e.position,
            e.department,
            ps.total_regular_hours,
            ps.total_overtime_hours,
            ps.hourly_rate,
            ps.gross_pay,
            ps.claims,
            ps.total_deductions,
            ps.net_pay,
            ps.status,
            ps.generated_at
        FROM payroll_summary ps
        JOIN employees e ON ps.employee_id = e.id
        WHERE ps.period_start = ? AND ps.period_end = ?
        ORDER BY e.full_name
    ", [$periodStart, $periodEnd])->find();

    // Generate Excel file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="payroll_' . $periodStart . '_to_' . $periodEnd . '.xls"');

    echo "Payroll Period: " . date('F j, Y', strtotime($periodStart)) . " - " . date('F j, Y', strtotime($periodEnd)) . "\n";
    echo "Generated: " . date('F j, Y H:i:s') . "\n\n";

    // Headers
    echo "Employee #\tFull Name\tPosition\tDepartment\tRegular Hours\tOvertime Hours\tHourly Rate\tGross Pay\tClaims\tDeductions\tNet Pay\tStatus\tProcessed Date\n";

    // Data rows
    foreach ($exportData as $row) {
        echo implode("\t", [
            $row['employee_number'],
            $row['full_name'],
            $row['position'],
            $row['department'],
            round($row['total_regular_hours']),
            round($row['total_overtime_hours']),
            number_format($row['hourly_rate'], 2),
            number_format($row['gross_pay'], 2),
            number_format($row['claims'], 2),
            number_format($row['total_deductions'], 2),
            number_format($row['net_pay'], 2),
            $row['status'],
            date('Y-m-d H:i', strtotime($row['generated_at']))
        ]) . "\n";
    }

    // Totals
    echo "\n";
    echo "TOTALS\t\t\t\t" .
        round(array_sum(array_column($exportData, 'total_regular_hours'))) . "\t" .
        round(array_sum(array_column($exportData, 'total_overtime_hours'))) . "\t\t" .
        number_format(array_sum(array_column($exportData, 'gross_pay')), 2) . "\t" .
        number_format(array_sum(array_column($exportData, 'claims')), 2) . "\t" .
        number_format(array_sum(array_column($exportData, 'total_deductions')), 2) . "\t" .
        number_format(array_sum(array_column($exportData, 'net_pay')), 2) . "\n";

    exit();
}

// Handle export all history
if (isset($_GET['export_all_history'])) {

    // Get all processed payroll data grouped by period
    $allHistoryData = $db->query("
        SELECT 
            ps.period_start,
            ps.period_end,
            COUNT(DISTINCT ps.employee_id) as employee_count,
            SUM(ps.gross_pay) as total_gross,
            SUM(ps.net_pay) as total_net,
            SUM(ps.claims) as total_claims,
            MAX(ps.generated_at) as last_generated
        FROM payroll_summary ps
        WHERE ps.status = 'Processed'
        GROUP BY ps.period_start, ps.period_end
        ORDER BY ps.period_start DESC, ps.period_end DESC
    ")->find();

    // Generate Excel file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="payroll_complete_history_' . date('Y-m-d') . '.xls"');

    echo "Payroll Complete History\n";
    echo "Generated: " . date('F j, Y H:i:s') . "\n\n";

    // Headers
    echo "Period Start\tPeriod End\tProcessed Date\tEmployees\tGross Pay\tClaims\tNet Pay\n";

    // Data rows
    $totalGross = 0;
    $totalClaims = 0;
    $totalNet = 0;

    foreach ($allHistoryData as $row) {
        echo implode("\t", [
            date('Y-m-d', strtotime($row['period_start'])),
            date('Y-m-d', strtotime($row['period_end'])),
            date('Y-m-d', strtotime($row['last_generated'])),
            $row['employee_count'],
            number_format($row['total_gross'], 2),
            number_format($row['total_claims'], 2),
            number_format($row['total_net'], 2)
        ]) . "\n";

        $totalGross += $row['total_gross'];
        $totalClaims += $row['total_claims'];
        $totalNet += $row['total_net'];
    }

    // Grand Totals
    echo "\n";
    echo "GRAND TOTALS\t\t\t" .
        array_sum(array_column($allHistoryData, 'employee_count')) . "\t" .
        number_format($totalGross, 2) . "\t" .
        number_format($totalClaims, 2) . "\t" .
        number_format($totalNet, 2) . "\n";

    exit();
}

// REMOVE the duplicate calculation at the bottom - it's no longer needed
// The counts are now properly calculated above


// ============================================
// EMPLOYEE SCHEDULES SECTION
// ============================================

// Pagination for schedules
$schedulePage = isset($_GET['schedule_page']) ? max(1, (int) $_GET['schedule_page']) : 1;
$schedulePerPage = 10;
$scheduleOffset = ($schedulePage - 1) * $schedulePerPage;

// Filter parameters
$scheduleFilter = isset($_GET['schedule_filter']) ? $_GET['schedule_filter'] : 'upcoming';
$scheduleDepartmentFilter = isset($_GET['schedule_dept']) ? $_GET['schedule_dept'] : '';
$scheduleEmployeeFilter = isset($_GET['schedule_employee']) ? $_GET['schedule_employee'] : '';

// Build WHERE clause for filters
$scheduleWhereConditions = [];
$scheduleParams = [];

// Date filter
$today = date('Y-m-d');
if ($scheduleFilter === 'today') {
    $scheduleWhereConditions[] = "es.schedule_date = ?";
    $scheduleParams[] = $today;
} elseif ($scheduleFilter === 'upcoming') {
    $scheduleWhereConditions[] = "es.schedule_date >= ?";
    $scheduleParams[] = $today;
} elseif ($scheduleFilter === 'week') {
    $startOfWeek = date('Y-m-d', strtotime('monday this week'));
    $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
    $scheduleWhereConditions[] = "es.schedule_date BETWEEN ? AND ?";
    $scheduleParams[] = $startOfWeek;
    $scheduleParams[] = $endOfWeek;
} elseif ($scheduleFilter === 'month') {
    $startOfMonth = date('Y-m-01');
    $endOfMonth = date('Y-m-t');
    $scheduleWhereConditions[] = "es.schedule_date BETWEEN ? AND ?";
    $scheduleParams[] = $startOfMonth;
    $scheduleParams[] = $endOfMonth;
}

// Department filter
if (!empty($scheduleDepartmentFilter)) {
    $scheduleWhereConditions[] = "e.department = ?";
    $scheduleParams[] = $scheduleDepartmentFilter;
}

// Employee filter (search by name)
if (!empty($scheduleEmployeeFilter)) {
    $scheduleWhereConditions[] = "e.full_name LIKE ?";
    $scheduleParams[] = "%{$scheduleEmployeeFilter}%";
}

$scheduleWhereClause = !empty($scheduleWhereConditions)
    ? "WHERE " . implode(" AND ", $scheduleWhereConditions)
    : "WHERE 1=1";

// Get total schedules count for pagination
try {
    $totalSchedules = $db->query("
        SELECT COUNT(*) as count 
        FROM employee_schedules es
        JOIN employees e ON es.employee_id = e.id
        $scheduleWhereClause
    ", $scheduleParams)->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalSchedules = 0;
    error_log("Error fetching total schedules: " . $th->getMessage());
}

// Get paginated schedules with employee and shift details
try {
    $employeeSchedules = $db->query("
        SELECT 
            es.*,
            e.id as employee_id,
            e.full_name,
            e.employee_number,
            e.position,
            e.department,
            s.shift_name,
            s.shift_code,
            s.start_time as shift_start,
            s.end_time as shift_end,
            DATE_FORMAT(es.schedule_date, '%a, %b %e, %Y') as formatted_date,
            DATE_FORMAT(es.time_in, '%h:%i %p') as formatted_time_in,
            DATE_FORMAT(es.time_out, '%h:%i %p') as formatted_time_out,
            CASE 
                WHEN es.schedule_date < CURDATE() THEN 'Past'
                WHEN es.schedule_date = CURDATE() THEN 'Today'
                WHEN es.schedule_date <= DATE_ADD(CURDATE(), INTERVAL 2 DAY) THEN 'Upcoming'
                ELSE 'Scheduled'
            END as date_status,
            CASE 
                WHEN es.schedule_date < CURDATE() THEN 'bg-gray-100 text-gray-600'
                WHEN es.schedule_date = CURDATE() THEN 'bg-blue-50 text-blue-600'
                WHEN es.schedule_date <= DATE_ADD(CURDATE(), INTERVAL 2 DAY) THEN 'bg-amber-50 text-amber-600'
                ELSE 'bg-green-50 text-green-600'
            END as status_class,
            CONCAT(LEFT(e.full_name, 1), COALESCE(RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1), RIGHT(e.full_name, 1))) as initials
        FROM employee_schedules es
        JOIN employees e ON es.employee_id = e.id
        LEFT JOIN shifts s ON es.shift_id = s.id
        $scheduleWhereClause
        ORDER BY 
            CASE 
                WHEN es.schedule_date < CURDATE() THEN 2
                ELSE 1
            END,
            es.schedule_date ASC,
            es.time_in ASC
        LIMIT $schedulePerPage OFFSET $scheduleOffset
    ", $scheduleParams)->find();
} catch (\Throwable $th) {
    $employeeSchedules = [];
    error_log("Error fetching employee schedules: " . $th->getMessage());
}

$totalSchedulePages = ceil($totalSchedules / $schedulePerPage);

// Get schedule statistics
try {
    $scheduleStats = $db->query("
        SELECT 
            COUNT(CASE WHEN schedule_date = CURDATE() THEN 1 END) as today_count,
            COUNT(CASE WHEN schedule_date > CURDATE() AND schedule_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as this_week_count,
            COUNT(CASE WHEN schedule_date > CURDATE() THEN 1 END) as upcoming_count,
            COUNT(CASE WHEN schedule_date < CURDATE() THEN 1 END) as past_count
        FROM employee_schedules
    ")->fetch_one();

    $schedulesToday = $scheduleStats['today_count'] ?? 0;
    $schedulesThisWeek = $scheduleStats['this_week_count'] ?? 0;
    $schedulesUpcoming = $scheduleStats['upcoming_count'] ?? 0;
    $schedulesPast = $scheduleStats['past_count'] ?? 0;
} catch (\Throwable $th) {
    $schedulesToday = 0;
    $schedulesThisWeek = 0;
    $schedulesUpcoming = 0;
    $schedulesPast = 0;
    error_log("Error fetching schedule stats: " . $th->getMessage());
}

// Get distinct departments for filter dropdown
try {
    $scheduleDepartments = $db->query("
        SELECT DISTINCT e.department 
        FROM employee_schedules es
        JOIN employees e ON es.employee_id = e.id
        WHERE e.department IS NOT NULL AND e.department != ''
        ORDER BY e.department
    ")->find();
} catch (\Throwable $th) {
    $scheduleDepartments = [];
    error_log("Error fetching schedule departments: " . $th->getMessage());
}

// Get unique schedule dates with employee counts
try {
    $bundledSchedules = $db->query("
        SELECT 
            es.schedule_date,
            DATE_FORMAT(es.schedule_date, '%b %e, %Y') as formatted_date,
            DATE_FORMAT(es.schedule_date, '%W') as day_of_week,
            COUNT(DISTINCT es.employee_id) as employee_count,
            COUNT(es.id) as total_shifts,
            GROUP_CONCAT(DISTINCT s.shift_name SEPARATOR ', ') as shift_types,
            MIN(es.time_in) as earliest_time_in,
            MAX(es.time_out) as latest_time_out,
            CASE 
                WHEN es.schedule_date = CURDATE() THEN 'Today'
                WHEN es.schedule_date < CURDATE() THEN 'Past'
                ELSE 'Upcoming'
            END as status
        FROM employee_schedules es
        LEFT JOIN shifts s ON es.shift_id = s.id
        GROUP BY es.schedule_date
        ORDER BY es.schedule_date DESC
        LIMIT 10
    ")->find();
} catch (\Throwable $th) {
    $bundledSchedules = [];
    error_log("Error fetching bundled schedules: " . $th->getMessage());
}

// Get total unique dates count
try {
    $totalScheduleDates = $db->query("
        SELECT COUNT(DISTINCT schedule_date) as count 
        FROM employee_schedules
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalScheduleDates = 0;
    error_log("Error counting schedule dates: " . $th->getMessage());
}

// ============================================
// SHIFT SWAP REQUESTS SECTION
// ============================================

// Get shift swap requests
try {
    $shiftSwapRequests = $db->query("
        SELECT 
            ssr.*,
            requester.id as requester_id,
            requester.full_name as requester_name,
            requester.shift_id as requester_shift_id,
            requester.employee_number as requester_emp_no,
            swapper.id as swapper_id,
            swapper.full_name as swapper_name,
            swapper.shift_id as swapper_shift_id,
            rshift.shift_name as requester_shift_name,
            rshift.start_time as requester_shift_start,
            rshift.end_time as requester_shift_end,
            wshift.shift_name as swapper_shift_name,
            wshift.start_time as swapper_shift_start,
            wshift.end_time as swapper_shift_end,
            DATE_FORMAT(ssr.swap_date, '%a, %b %e') as formatted_swap_date,
            DATE_FORMAT(ssr.created_at, '%b %e, %Y') as formatted_created,
            CASE 
                WHEN ssr.status = 'Pending' THEN 'bg-yellow-50 text-yellow-600 border-yellow-200'
                WHEN ssr.status = 'Approved' THEN 'bg-green-50 text-green-600 border-green-200'
                WHEN ssr.status = 'Rejected' THEN 'bg-red-50 text-red-600 border-red-200'
                WHEN ssr.status = 'Cancelled' THEN 'bg-gray-100 text-gray-600 border-gray-200'
                ELSE 'bg-gray-50 text-gray-600 border-gray-200'
            END as status_class,
            CONCAT(LEFT(requester.full_name, 1), COALESCE(RIGHT(LEFT(requester.full_name, INSTR(requester.full_name, ' ') + 1), 1), RIGHT(requester.full_name, 1))) as requester_initials,
            CONCAT(LEFT(swapper.full_name, 1), COALESCE(RIGHT(LEFT(swapper.full_name, INSTR(swapper.full_name, ' ') + 1), 1), RIGHT(swapper.full_name, 1))) as swapper_initials
        FROM shift_swap_requests ssr
        JOIN employees requester ON ssr.requester_employee_id = requester.id
        JOIN employees swapper ON ssr.swap_with_employee_id = swapper.id
        LEFT JOIN shifts rshift ON ssr.requester_shift_id = rshift.id
        LEFT JOIN shifts wshift ON ssr.swap_with_shift_id = wshift.id
        ORDER BY 
            CASE ssr.status
                WHEN 'Pending' THEN 1
                WHEN 'Approved' THEN 2
                WHEN 'Rejected' THEN 3
                ELSE 4
            END,
            ssr.created_at DESC
    ")->find();
} catch (\Throwable $th) {
    $shiftSwapRequests = [];
    error_log("Error fetching shift swap requests: " . $th->getMessage());
}

// Get pending count
$shiftSwapPendingCount = 0;
foreach ($shiftSwapRequests as $request) {
    if ($request['status'] == 'Pending') {
        $shiftSwapPendingCount++;
    }
}

// Format shift time range
function formatShiftTimeRange($start, $end)
{
    return date('g:i A', strtotime($start)) . ' - ' . date('g:i A', strtotime($end));
}

//social recognition
// ============================================
// MENTOR ASSIGNMENT SECTION
// ============================================

// Get all potential mentees (probationary employees, new hires)
try {
    $mentorMentees = $db->query("
        SELECT 
            e.id,
            e.full_name,
            e.position,
            e.department,
            e.hired_date,
            e.status,
            TIMESTAMPDIFF(MONTH, e.hired_date, CURDATE()) as months_employed,
            CONCAT(e.full_name, ' - ', e.position, ' (', e.status, ')') as display_name
        FROM employees e
        WHERE e.status IN ('Probationary', 'Onboarding')
        AND e.role != 'mentor'
        AND e.id NOT IN (
            SELECT mentee_employee_id FROM mentor_assignments WHERE status = 'Active'
        )
        ORDER BY e.full_name
    ")->find();
} catch (\Throwable $th) {
    $mentorMentees = [];
    error_log("Error fetching mentees: " . $th->getMessage());
}

// Get all potential mentors (employees with mentor role or experienced staff)
try {
    $mentorMentors = $db->query("
        SELECT 
            e.id,
            e.full_name,
            e.position,
            e.department,
            e.hired_date,
            TIMESTAMPDIFF(YEAR, e.hired_date, CURDATE()) as years_experience,
            CONCAT(e.full_name, ' - ', e.position, ' (', TIMESTAMPDIFF(YEAR, e.hired_date, CURDATE()), ' yrs)') as display_name
        FROM employees e
        WHERE e.role IN ('mentor', 'evaluator')
        AND e.status IN ('Active', 'Regular')
        ORDER BY e.full_name
    ")->find();
} catch (\Throwable $th) {
    $mentorMentors = [];
    error_log("Error fetching mentors: " . $th->getMessage());
}

// Program duration options
$mentorDurations = [
    '3 months' => '3 months',
    '6 months' => '6 months',
    '12 months' => '12 months'
];

// Get all active employees for recognition dropdown
try {
    $recognitionEmployees = $db->query("
        SELECT 
            e.id,
            e.full_name,
            e.position,
            e.department,
            e.hired_date,
            TIMESTAMPDIFF(MONTH, e.hired_date, CURDATE()) as months_employed,
            -- Check if they're eligible for Employee of the Month (at least 3 months employed)
            CASE 
                WHEN TIMESTAMPDIFF(MONTH, e.hired_date, CURDATE()) >= 3 THEN 1
                ELSE 0
            END as eligible_for_eom,
            -- Check perfect attendance in last 30 days
            CASE 
                WHEN NOT EXISTS (
                    SELECT 1 FROM attendance 
                    WHERE employee_id = e.id 
                    AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    AND (late_minutes > 0 OR early_departure_minutes > 0)
                ) AND EXISTS (
                    SELECT 1 FROM attendance 
                    WHERE employee_id = e.id 
                    AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ) THEN 1
                ELSE 0
            END as perfect_attendance,
            -- Count completed trainings in last 3 months
            (
                SELECT COUNT(*) FROM training_schedule 
                WHERE employee_id = e.id 
                AND status = 'Completed'
                AND end_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
            ) as recent_trainings,
            CONCAT(e.full_name, ' - ', e.position, ' (', e.department, ')') as display_name,
            CONCAT(e.full_name, ' - ', e.position, ' (', 
                CASE 
                    WHEN TIMESTAMPDIFF(MONTH, e.hired_date, CURDATE()) >= 3 THEN 'Eligible for EOM'
                    ELSE 'New Hire'
                END,
                CASE 
                    WHEN NOT EXISTS (
                        SELECT 1 FROM attendance 
                        WHERE employee_id = e.id 
                        AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        AND (late_minutes > 0 OR early_departure_minutes > 0)
                    ) AND EXISTS (
                        SELECT 1 FROM attendance 
                        WHERE employee_id = e.id 
                        AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    ) THEN ' • Perfect Attendance'
                    ELSE ''
                END,
                ')') as dropdown_display
        FROM employees e
        WHERE e.status IN ('Active', 'Regular', 'Probationary')
        ORDER BY e.full_name
    ")->find();
} catch (\Throwable $th) {
    $recognitionEmployees = [];
    error_log("Error fetching employees for recognition: " . $th->getMessage());
}

// Recognition types with descriptions
$recognitionTypes = [
    'Employee of the Month' => [
        'value' => 'Employee of the Month',
        'description' => 'Outstanding performance and contribution',
        'icon' => 'fa-crown',
        'color' => 'amber'
    ],
    'Rising Star' => [
        'value' => 'Rising Star',
        'description' => 'Exceptional growth and potential',
        'icon' => 'fa-star',
        'color' => 'blue'
    ],
    'Perfect Attendance' => [
        'value' => 'Perfect Attendance',
        'description' => 'No absences or lates in the last 30 days',
        'icon' => 'fa-calendar-check',
        'color' => 'green'
    ],
    'Innovation Award' => [
        'value' => 'Innovation Award',
        'description' => 'Creative ideas and improvements',
        'icon' => 'fa-lightbulb',
        'color' => 'purple'
    ],
    'Team Player' => [
        'value' => 'Team Player',
        'description' => 'Excellent collaboration and support',
        'icon' => 'fa-users',
        'color' => 'indigo'
    ]
];

// Get recent recognitions
try {
    $recentRecognitions = $db->query("
        SELECT 
            r.*,
            e.full_name,
            e.position,
            e.department,
            recognizer.full_name as recognizer_name,
            DATE_FORMAT(r.recognition_date, '%b %e, %Y') as formatted_date,
            CONCAT(LEFT(e.full_name, 1), COALESCE(RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1), RIGHT(e.full_name, 1))) as initials
        FROM employee_recognitions r
        JOIN employees e ON r.employee_id = e.id
        LEFT JOIN employees recognizer ON r.recognized_by = recognizer.id
        ORDER BY r.recognition_date DESC
        LIMIT 5
    ")->find();
} catch (\Throwable $th) {
    $recentRecognitions = [];
    error_log("Error fetching recent recognitions: " . $th->getMessage());
}

// Get recognition stats
try {
    $recognitionStats = $db->query("
        SELECT 
            COUNT(*) as total_recognitions,
            COUNT(DISTINCT employee_id) as unique_employees,
            MAX(recognition_date) as last_recognition_date,
            COUNT(CASE WHEN recognition_type = 'Employee of the Month' THEN 1 END) as eom_count,
            COUNT(CASE WHEN recognition_type = 'Perfect Attendance' THEN 1 END) as attendance_count
        FROM employee_recognitions
        WHERE recognition_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    ")->fetch_one();
} catch (\Throwable $th) {
    $recognitionStats = [
        'total_recognitions' => 0,
        'unique_employees' => 0,
        'last_recognition_date' => null,
        'eom_count' => 0,
        'attendance_count' => 0
    ];
    error_log("Error fetching recognition stats: " . $th->getMessage());
}

// ============================================
// SOCIAL RECOGNITION SECTION
// ============================================

// Filter parameters
$recognitionFilter = isset($_GET['recognition_filter']) ? $_GET['recognition_filter'] : 'all';
$recognitionSearch = isset($_GET['recognition_search']) ? $_GET['recognition_search'] : '';
$recognitionSort = isset($_GET['recognition_sort']) ? $_GET['recognition_sort'] : 'recent';

// Get active mentor assignments
try {
    $mentorAssignmentsQuery = "
        SELECT 
            ma.*,
            mentor.id as mentor_id,
            mentor.full_name as mentor_name,
            mentor.position as mentor_position,
            mentor.department,
            mentor.hired_date,
            TIMESTAMPDIFF(YEAR, mentor.hired_date, CURDATE()) as mentor_years_exp,
            CONCAT(LEFT(mentor.full_name, 1), COALESCE(RIGHT(LEFT(mentor.full_name, INSTR(mentor.full_name, ' ') + 1), 1), RIGHT(mentor.full_name, 1))) as mentor_initials,
            COUNT(DISTINCT ma2.id) as mentee_count,
            GROUP_CONCAT(DISTINCT CONCAT(LEFT(mentee.full_name, 1), COALESCE(RIGHT(LEFT(mentee.full_name, INSTR(mentee.full_name, ' ') + 1), 1), RIGHT(mentee.full_name, 1))) SEPARATOR '') as mentee_initials,
            COUNT(DISTINCT mentee.id) as total_mentees,
            MIN(ma.created_at) as earliest_assignment,
            MAX(ma.created_at) as latest_assignment
        FROM mentor_assignments ma
        JOIN employees mentor ON ma.mentor_employee_id = mentor.id
        LEFT JOIN mentor_assignments ma2 ON ma.mentor_employee_id = ma2.mentor_employee_id AND ma2.status = 'Active'
        LEFT JOIN employees mentee ON ma2.mentee_employee_id = mentee.id
        WHERE ma.status = 'Active'
    ";

    $mentorAssignmentsParams = [];

    // Apply search filter
    if (!empty($recognitionSearch)) {
        $mentorAssignmentsQuery .= " AND (mentor.full_name LIKE ? OR mentor.position LIKE ?)";
        $searchTerm = "%$recognitionSearch%";
        $mentorAssignmentsParams[] = $searchTerm;
        $mentorAssignmentsParams[] = $searchTerm;
    }

    $mentorAssignmentsQuery .= " GROUP BY mentor.id";

    // Apply sorting
    switch ($recognitionSort) {
        case 'name':
            $mentorAssignmentsQuery .= " ORDER BY mentor.full_name ASC";
            break;
        case 'mentees':
            $mentorAssignmentsQuery .= " ORDER BY total_mentees DESC";
            break;
        case 'recent':
        default:
            $mentorAssignmentsQuery .= " ORDER BY latest_assignment DESC";
            break;
    }

    $mentorAssignments = $db->query($mentorAssignmentsQuery, $mentorAssignmentsParams)->find();

    // Count active mentors
    $activeMentorsCount = count($mentorAssignments);

    // Count total mentees
    $totalMentees = 0;
    foreach ($mentorAssignments as $mentor) {
        $totalMentees += $mentor['total_mentees'];
    }

} catch (\Throwable $th) {
    $mentorAssignments = [];
    $activeMentorsCount = 0;
    $totalMentees = 0;
    error_log("Error fetching mentor assignments: " . $th->getMessage());
}

// Get available mentors (not currently mentoring or with capacity)
try {
    $availableMentors = $db->query("
        SELECT 
            e.id,
            e.full_name,
            e.position,
            e.department,
            e.hired_date,
            TIMESTAMPDIFF(YEAR, e.hired_date, CURDATE()) as years_exp,
            CONCAT(LEFT(e.full_name, 1), COALESCE(RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1), RIGHT(e.full_name, 1))) as initials,
            COUNT(ma.id) as current_mentees
        FROM employees e
        LEFT JOIN mentor_assignments ma ON e.id = ma.mentor_employee_id AND ma.status = 'Active'
        WHERE e.role IN ('mentor', 'evaluator', 'admin')
        AND e.status IN ('Active', 'Regular')
        GROUP BY e.id
        HAVING current_mentees < 5 -- Assuming max 5 mentees per mentor
        ORDER BY years_exp DESC, e.full_name
        LIMIT 5
    ")->find();
} catch (\Throwable $th) {
    $availableMentors = [];
    error_log("Error fetching available mentors: " . $th->getMessage());
}

// Get top performers based on mentor ratings and attendance
try {
    $topPerformersQuery = "
        SELECT 
            e.id,
            e.full_name,
            e.position,
            e.department,
            CONCAT(LEFT(e.full_name, 1), COALESCE(RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1), RIGHT(e.full_name, 1))) as initials,
            -- Average rating from mentor ratings
            COALESCE((
                SELECT AVG(rating) FROM mentor_ratings 
                WHERE mentee_employee_id = e.id
            ), 0) as avg_rating,
            -- Count of ratings received
            COALESCE((
                SELECT COUNT(*) FROM mentor_ratings 
                WHERE mentee_employee_id = e.id
            ), 0) as rating_count,
            -- Latest comment
            (
                SELECT comment FROM mentor_ratings 
                WHERE mentee_employee_id = e.id 
                ORDER BY created_at DESC LIMIT 1
            ) as latest_comment,
            -- Latest rating date
            (
                SELECT DATE_FORMAT(rating_date, '%b %e, %Y') FROM mentor_ratings 
                WHERE mentee_employee_id = e.id 
                ORDER BY created_at DESC LIMIT 1
            ) as latest_rating_date,
            -- Mentor who gave the latest rating
            (
                SELECT CONCAT(mentor.full_name, ' (', mentor.position, ')') 
                FROM mentor_ratings mr
                JOIN employees mentor ON mr.mentor_employee_id = mentor.id
                WHERE mr.mentee_employee_id = e.id 
                ORDER BY mr.created_at DESC LIMIT 1
            ) as latest_mentor,
            -- Perfect attendance flag (no lates in last 30 days)
            CASE 
                WHEN NOT EXISTS (
                    SELECT 1 FROM attendance 
                    WHERE employee_id = e.id 
                    AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    AND (late_minutes > 0 OR early_departure_minutes > 0)
                ) AND EXISTS (
                    SELECT 1 FROM attendance 
                    WHERE employee_id = e.id 
                    AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ) THEN 1
                ELSE 0
            END as perfect_attendance
        FROM employees e
        WHERE e.status IN ('Active', 'Regular', 'Probationary')
        AND EXISTS (
            SELECT 1 FROM mentor_ratings WHERE mentee_employee_id = e.id
        )
    ";

    // Apply filter
    if ($recognitionFilter !== 'all') {
        switch ($recognitionFilter) {
            case 'highly_rated':
                $topPerformersQuery .= " AND COALESCE((SELECT AVG(rating) FROM mentor_ratings WHERE mentee_employee_id = e.id), 0) >= 4.5";
                break;
            case 'attendance':
                $topPerformersQuery .= " AND NOT EXISTS (SELECT 1 FROM attendance WHERE employee_id = e.id AND late_minutes > 0)";
                break;
            case 'recently_rated':
                $topPerformersQuery .= " AND EXISTS (SELECT 1 FROM mentor_ratings WHERE mentee_employee_id = e.id AND rating_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY))";
                break;
        }
    }

    $topPerformersQuery .= " ORDER BY 
        CASE 
            WHEN perfect_attendance = 1 THEN 1
            ELSE 2
        END,
        avg_rating DESC,
        rating_count DESC
        LIMIT 5";

    $topPerformers = $db->query($topPerformersQuery)->find();

    // Add rank and achievement description
    $rank = 1;
    foreach ($topPerformers as &$performer) {
        $performer['rank'] = $rank;

        if ($performer['perfect_attendance']) {
            $performer['achievement'] = 'Perfect Attendance';
            $performer['badge_color'] = 'green';
            $performer['badge_text'] = '🌟 No lates';
        } elseif ($performer['avg_rating'] >= 4.5) {
            $performer['achievement'] = 'Highly Rated';
            $performer['badge_color'] = 'amber';
            $performer['badge_text'] = number_format($performer['avg_rating'], 1) . ' ★';
        } elseif ($performer['avg_rating'] >= 4.0) {
            $performer['achievement'] = 'Well Rated';
            $performer['badge_color'] = 'blue';
            $performer['badge_text'] = number_format($performer['avg_rating'], 1) . ' ★';
        } else {
            $performer['achievement'] = 'Rising Star';
            $performer['badge_color'] = 'purple';
            $performer['badge_text'] = $performer['rating_count'] . ' ratings';
        }

        $rank++;
    }

} catch (\Throwable $th) {
    $topPerformers = [];
    error_log("Error fetching top performers: " . $th->getMessage());
}
function getEmployeeCompleteness($employee)
{
    $requiredDocuments = [
        'resume' => 'Resume',
        'nbi_clearance' => 'NBI Clearance',
        'medical_result' => 'Medical Result',
        'birth_certificate' => 'Birth Certificate',
    ];

    $missingItems = [];
    $missingCount = 0;

    // Check documents with better null/empty checking
    foreach ($requiredDocuments as $doc => $label) {
        // Check if the key exists and is not empty (not null, not empty string)
        $value = $employee[$doc] ?? null;

        // Debug: Log what we're finding
        error_log("Checking $doc for employee {$employee['id']}: " . ($value ?: 'EMPTY'));

        if ($value === null || $value === '' || trim($value) === '') {
            $missingItems[] = $label;
            $missingCount++;
        }
    }

    return [
        'is_complete' => ($missingCount === 0),
        'missing_count' => $missingCount,
        'missing_items' => $missingItems,
        'status' => ($missingCount === 0) ? 'Complete' : 'Incomplete',
        'status_class' => ($missingCount === 0)
            ? 'bg-green-100 text-green-800 border-green-200'
            : 'bg-red-100 text-red-800 border-red-200',
        'icon' => ($missingCount === 0) ? 'fa-check-circle' : 'fa-exclamation-circle',
        'icon_color' => ($missingCount === 0) ? 'text-green-500' : 'text-red-500',
        'message' => ($missingCount === 0)
            ? 'All requirements complete'
            : $missingCount . ' requirement' . ($missingCount > 1 ? 's' : '') . ' missing'
    ];
}

// ============================================
// JOB REQUISITIONS SECTION
// ============================================

// Pagination for requisitions
$requisitionPage = isset($_GET['requisition_page']) ? max(1, (int) $_GET['requisition_page']) : 1;
$requisitionPerPage = 5;
$requisitionOffset = ($requisitionPage - 1) * $requisitionPerPage;

// Filter parameters
$requisitionDeptFilter = isset($_GET['requisition_dept']) ? $_GET['requisition_dept'] : '';
$requisitionPriorityFilter = isset($_GET['requisition_priority']) ? $_GET['requisition_priority'] : '';
$requisitionStatusFilter = isset($_GET['requisition_status']) ? $_GET['requisition_status'] : '';
$requisitionSearch = isset($_GET['requisition_search']) ? $_GET['requisition_search'] : '';

// Build WHERE clause for filters
$requisitionWhereConditions = [];
$requisitionParams = [];

if (!empty($requisitionSearch)) {
    $requisitionWhereConditions[] = "(job_title LIKE ? OR department LIKE ? OR requested_by LIKE ?)";
    $searchParam = "%$requisitionSearch%";
    $requisitionParams[] = $searchParam;
    $requisitionParams[] = $searchParam;
    $requisitionParams[] = $searchParam;
}

if (!empty($requisitionDeptFilter)) {
    $requisitionWhereConditions[] = "department = ?";
    $requisitionParams[] = $requisitionDeptFilter;
}

if (!empty($requisitionPriorityFilter)) {
    $requisitionWhereConditions[] = "priority = ?";
    $requisitionParams[] = $requisitionPriorityFilter;
}

if (!empty($requisitionStatusFilter)) {
    $requisitionWhereConditions[] = "status = ?";
    $requisitionParams[] = $requisitionStatusFilter;
}

$requisitionWhereClause = !empty($requisitionWhereConditions)
    ? "WHERE " . implode(" AND ", $requisitionWhereConditions)
    : "";

// Get distinct departments for filter dropdown
try {
    $requisitionDepartments = $db->query("
        SELECT DISTINCT department 
        FROM job_requisitions 
        WHERE department IS NOT NULL AND department != ''
        ORDER BY department
    ")->find();
} catch (\Throwable $th) {
    $requisitionDepartments = [];
    error_log("Error fetching requisition departments: " . $th->getMessage());
}

// ============================================
// REQUISITION STATS
// ============================================

// Get total counts for stats
try {
    $requisitionStats = $db->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'declined' THEN 1 ELSE 0 END) as declined
        FROM job_requisitions
    ")->fetch_one();

    $requisitionTotal = $requisitionStats['total'] ?? 0;
    $requisitionPending = $requisitionStats['pending'] ?? 0;
    $requisitionApproved = $requisitionStats['approved'] ?? 0;
    $requisitionDeclined = $requisitionStats['declined'] ?? 0;
} catch (\Throwable $th) {
    $requisitionTotal = 0;
    $requisitionPending = 0;
    $requisitionApproved = 0;
    $requisitionDeclined = 0;
    error_log("Error fetching requisition stats: " . $th->getMessage());
}

// Get total count for pagination
try {
    $requisitionTotalCount = $db->query("
        SELECT COUNT(*) as count 
        FROM job_requisitions
        $requisitionWhereClause
    ", $requisitionParams)->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $requisitionTotalCount = 0;
    error_log("Error counting requisitions: " . $th->getMessage());
}

// Get paginated requisitions
try {
    $requisitions = $db->query("
        SELECT 
            *,
            DATE_FORMAT(needed_by, '%M %e, %Y') as formatted_needed_by,
            DATE_FORMAT(created_at, '%M %e, %Y') as formatted_created,
            CASE 
                WHEN priority = 'high' THEN 'bg-red-50 text-red-700 border-red-200'
                WHEN priority = 'medium' THEN 'bg-yellow-50 text-yellow-700 border-yellow-200'
                WHEN priority = 'low' THEN 'bg-green-50 text-green-700 border-green-200'
                ELSE 'bg-gray-50 text-gray-700 border-gray-200'
            END as priority_class,
            CASE 
                WHEN status = 'pending' THEN 'bg-yellow-50 text-yellow-700 border-yellow-200'
                WHEN status = 'approved' THEN 'bg-green-50 text-green-700 border-green-200'
                WHEN status = 'declined' THEN 'bg-red-50 text-red-700 border-red-200'
                ELSE 'bg-gray-50 text-gray-700 border-gray-200'
            END as status_class
        FROM job_requisitions
        $requisitionWhereClause
        ORDER BY 
            CASE priority
                WHEN 'high' THEN 1
                WHEN 'medium' THEN 2
                WHEN 'low' THEN 3
            END,
            needed_by ASC,
            created_at DESC
        LIMIT $requisitionPerPage OFFSET $requisitionOffset
    ", $requisitionParams)->find();
} catch (\Throwable $th) {
    $requisitions = [];
    error_log("Error fetching requisitions: " . $th->getMessage());
}

$requisitionTotalPages = ceil($requisitionTotalCount / $requisitionPerPage);

// Get icon based on department
function getRequisitionIcon($department)
{
    $dept = strtolower($department ?? '');
    if (strpos($dept, 'kitchen') !== false || strpos($dept, 'cook') !== false || strpos($dept, 'chef') !== false) {
        return ['icon' => 'fa-utensils', 'color' => 'orange'];
    } elseif (strpos($dept, 'housekeeping') !== false || strpos($dept, 'clean') !== false) {
        return ['icon' => 'fa-broom', 'color' => 'blue'];
    } elseif (strpos($dept, 'front') !== false || strpos($dept, 'desk') !== false) {
        return ['icon' => 'fa-concierge-bell', 'color' => 'purple'];
    } elseif (strpos($dept, 'maintenance') !== false) {
        return ['icon' => 'fa-tools', 'color' => 'gray'];
    } elseif (strpos($dept, 'sales') !== false || strpos($dept, 'marketing') !== false) {
        return ['icon' => 'fa-chart-line', 'color' => 'green'];
    } elseif (strpos($dept, 'restaurant') !== false) {
        return ['icon' => 'fa-utensils', 'color' => 'red'];
    } else {
        return ['icon' => 'fa-briefcase', 'color' => 'blue'];
    }
}

// ============================================
// INTERVENTION ASSIGNMENTS
// ============================================

// Pagination for interventions
$interventionPage = isset($_GET['intervention_page']) ? max(1, (int) $_GET['intervention_page']) : 1;
$interventionPerPage = 10;
$interventionOffset = ($interventionPage - 1) * $interventionPerPage;

// Get total interventions count for pagination
try {
    $totalInterventions = $db->query("
        SELECT COUNT(*) as count 
        FROM intervention_assignments
    ")->fetch_one()['count'] ?? 0;
} catch (\Throwable $th) {
    $totalInterventions = 0;
    error_log("Error fetching total interventions: " . $th->getMessage());
}

// Get interventions with employee details
try {
    $interventionAssignments = $db->query("
        SELECT 
            ia.*,
            e.id as employee_id,
            e.full_name,
            e.position,
            e.department,
            e.employee_number,
            DATE_FORMAT(ia.assigned_date, '%b %e, %Y') as formatted_assigned,
            DATE_FORMAT(ia.due_date, '%b %e, %Y') as formatted_due,
            DATE_FORMAT(ia.completion_date, '%b %e, %Y') as formatted_completion,
            DATEDIFF(ia.due_date, CURDATE()) as days_remaining,
            CASE 
                WHEN ia.status = 'completed' THEN 'bg-green-50 text-green-700 border-green-200'
                WHEN ia.status = 'in_progress' THEN 'bg-blue-50 text-blue-700 border-blue-200'
                WHEN ia.status = 'pending' AND ia.due_date < CURDATE() THEN 'bg-red-50 text-red-700 border-red-200'
                WHEN ia.status = 'pending' THEN 'bg-yellow-50 text-yellow-700 border-yellow-200'
                WHEN ia.status = 'cancelled' THEN 'bg-gray-50 text-gray-700 border-gray-200'
                ELSE 'bg-gray-50 text-gray-700 border-gray-200'
            END as status_class,
            CONCAT(LEFT(e.full_name, 1), COALESCE(RIGHT(LEFT(e.full_name, INSTR(e.full_name, ' ') + 1), 1), RIGHT(e.full_name, 1))) as initials
        FROM intervention_assignments ia
        JOIN employees e ON ia.employee_id = e.id
        ORDER BY 
            CASE ia.status
                WHEN 'pending' THEN 1
                WHEN 'in_progress' THEN 2
                WHEN 'completed' THEN 3
                WHEN 'cancelled' THEN 4
            END,
            ia.due_date ASC,
            ia.assigned_date DESC
        LIMIT $interventionPerPage OFFSET $interventionOffset
    ")->find();
} catch (\Throwable $th) {
    $interventionAssignments = [];
    error_log("Error fetching interventions: " . $th->getMessage());
}

$totalInterventionPages = ceil($totalInterventions / $interventionPerPage);


view_path('main', 'index', [
    // Job Postings
    'jobPostings' => $jobPostings,

    // recognition
    'mentorMentees' => $mentorMentees,
    'mentorMentors' => $mentorMentors,
    'mentorDurations' => $mentorDurations,
    'recognitionEmployees' => $recognitionEmployees,
    'recognitionTypes' => $recognitionTypes,
    'recentRecognitions' => $recentRecognitions,
    'recognitionStats' => $recognitionStats,
    'recognitionFilter' => $recognitionFilter,
    'recognitionSearch' => $recognitionSearch,
    'recognitionSort' => $recognitionSort,
    'mentorAssignments' => $mentorAssignments,
    'activeMentorsCount' => $activeMentorsCount,
    'totalMentees' => $totalMentees,
    'availableMentors' => $availableMentors,
    'topPerformers' => $topPerformers,

    // REQUISITION VARIABLES
    'requisitionPage' => $requisitionPage,
    'requisitionPerPage' => $requisitionPerPage,
    'requisitionTotalPages' => $requisitionTotalPages,
    'requisitionTotalCount' => $requisitionTotalCount,
    'requisitionTotal' => $requisitionTotal,
    'requisitionPending' => $requisitionPending,
    'requisitionApproved' => $requisitionApproved,
    'requisitionDeclined' => $requisitionDeclined,
    'requisitionDeptFilter' => $requisitionDeptFilter,
    'requisitionPriorityFilter' => $requisitionPriorityFilter,
    'requisitionStatusFilter' => $requisitionStatusFilter,
    'requisitionSearch' => $requisitionSearch,
    'requisitionDepartments' => $requisitionDepartments,
    'requisitions' => $requisitions,

    // Applicants
    'applicants' => $allApplicants,
    'recentApplicants' => $recentApplicants,

    'interventionAssignments' => $interventionAssignments,
    'interventionPage' => $interventionPage,
    'totalInterventionPages' => $totalInterventionPages,

    // Employees
    'hiredEmployees' => $hiredEmployees,
    'employeesForBenefits' => $employeesForBenefits,
    // CONTRACT READY VARIABLES
    'contractReadyPage' => $contractReadyPage,
    'contractReadyPerPage' => $contractReadyPerPage,
    'totalContractReadyPages' => $totalContractReadyPages,
    'totalContractReady' => $totalContractReady,
    'contractReadyApplicants' => $contractReadyApplicants,
    'contractReadySearch' => $contractReadySearch,
    'contractReadyDept' => $contractReadyDept,
    'contractReadyDepartments' => $contractReadyDepartments,

    // Tasks
    'tasks' => $tasks,
    'applicantTasks' => $applicantTasks,


    // Onboarding
    'onboardingTasks' => $onboardingTasks,
    'obPage' => $obPage,
    'totalOnboardingPages' => $totalOnboardingPages,
    'departments' => $departments,
    'OnboardingAvailableEmployees' => $OnboardingAvailableEmployees,

    // Probationary Employees
    'probationaryEmployees' => $probationaryEmployees,

    // Employee Accounts
    'availableEmployees' => $availableEmployees,
    'generatedAccounts' => $generatedAccounts,
    'totalPages' => $totalPages,

    // New Hires
    'paginatedNewHires' => $paginatedNewHires,
    'nhPage' => $nhPage,
    'totalNewHirePages' => $totalNewHirePages,

    // Performance
    'recentEvaluations' => $recentEvaluations,
    'pendingCount' => $pendingCount,
    'needImprovement' => $needImprovement,
    'activePipCount' => $activePipCount,
    'highScoreCount' => $highScoreCount,

    // Benefits
    'benefitProviders' => $benefitProviders,

    //leave
    'vacationLeaveCount' => $vacationLeaveCount,
    'sickLeaveCount' => $sickLeaveCount,
    'emergencyLeaveCount' => $emergencyLeaveCount,
    'pendingLeaveCount' => $pendingLeaveCount,
    'pendingLeaveRequests' => $pendingLeaveRequests,
    'pendingPage' => $pendingPage,
    'totalPendingPages' => $totalPendingPages,
    'paginatedLeaveHistory' => $paginatedLeaveHistory,
    'historyPage' => $historyPage,
    'totalHistoryPages' => $totalHistoryPages,
    'leaveStats' => $leaveStats,

    //timesheet
    'timesheets' => $timesheets,
    'timesheetPage' => $timesheetPage,
    'totalTimesheetPages' => $totalTimesheetPages,
    'timesheetFilter' => $timesheetFilter,
    'filterApplied' => $filterApplied,
    'filterLabel' => $filterLabel,
    'dateRangeStart' => $dateRangeStart,
    'dateRangeEnd' => $dateRangeEnd,
    'totalRegularPeriod' => $totalRegularPeriod,
    'totalOvertimePeriod' => $totalOvertimePeriod,
    'totalHoursPeriod' => $totalHoursPeriod,
    'employeesWithHours' => $employeesWithHours,
    'pendingApprovalCount' => $pendingApprovalCount,
    'totalTimesheets' => $totalTimesheets,
    'currentDay' => $currentDay,


    'cutoffStart' => $cutoffStart,
    'cutoffEnd' => $cutoffEnd,
    'cutoffType' => $cutoffType,
    'totalCutoffHours' => $totalCutoffHours,
    'totalRegularHours' => $totalRegularHours,
    'totalOvertimeHours' => $totalOvertimeHours,
    'averageHoursPerEmployee' => $averageHoursPerEmployee,
    'totalActiveEmployees' => $totalActiveEmployees,
    'projectedPayout' => $projectedPayout,
    'departmentHours' => $departmentHours,
    'presentToday' => $presentToday,
    'onLeaveToday' => $onLeaveToday,
    'lateToday' => $lateToday,
    'absentToday' => $absentToday,
    'todaysAttendance' => $todaysAttendance,
    'attendancePage' => $attendancePage,
    'totalAttendancePages' => $totalAttendancePages,
    'availableShifts' => $availableShifts,
    'employeesWithAttendance' => $employeesWithAttendance,

    //competency
    'competencies' => $competencies,
    'evaluators' => $evaluators,
    'recentAssessments' => $recentAssessments,
    'competencyPage' => $competencyPage,
    'totalCompetencyPages' => $totalCompetencyPages,
    'completedThisMonth' => $completedThisMonth,
    'gapsIdentified' => $gapsIdentified,
    'pendingAssessments' => $pendingAssessments,
    'monthlyComparison' => $monthlyComparison,
    'employeesForDropdown' => $employeesForDropdown,

    //trainoing
    'employeeRole' => $employeeRole,
    'trainingProviders' => $trainingProviders,
    'trainingSchedule' => $trainingSchedule,
    'trainingPage' => $trainingPage,
    'totalTrainingPages' => $totalTrainingPages,
    'trainingFilter' => $trainingFilter,
    'trainingStats' => $trainingStats,
    'employeesWithGaps' => $groupedGaps,


    //hcm
    'hcmEmployees' => $hcmEmployees,
    'hcmPage' => $hcmPage,
    'totalHCMPages' => $totalHCMPages,
    'totalHCMEmployees' => $totalHCMEmployees,
    'hcmStats' => $hcmStats,
    'departmentStats' => $departmentStats,
    'totalEmployees' => $totalEmployees,
    'activeEmployees' => $activeEmployees,
    'probationaryCount' => $probationaryCount,
    'onLeaveCount' => $onLeaveCount,
    'activePercentage' => $activePercentage,
    'hcmSearchTerm' => $hcmSearchTerm,
    'hcmStatusFilter' => $hcmStatusFilter,
    'hcmDepartmentFilter' => $hcmDepartmentFilter,
    'hcmRoleFilter' => $hcmRoleFilter,
    'hcmDepartments' => $hcmDepartments,
    'hcmStatuses' => $hcmStatuses,
    'hcmRoles' => $hcmRoles,
    'hcmPerPage' => $hcmPerPage,

    //compensation
    'compensationPage' => $compensationPage,
    'totalCompensationPages' => $totalCompensationPages,
    'totalCompensationReviews' => $totalCompensationReviews,
    'compensationStatusFilter' => $compensationStatusFilter,
    'compensationTypeFilter' => $compensationTypeFilter,
    'compensationDepartmentFilter' => $compensationDepartmentFilter,
    'compensationDepartments' => $compensationDepartments,
    'pendingReviewsCount' => $pendingReviewsCount,
    'avgSalary' => $avgSalary,
    'minSalary' => $minSalary,
    'maxSalary' => $maxSalary,
    'totalBudget' => $totalBudget,
    'budgetUtilization' => $budgetUtilization,
    'salaryBands' => $salaryBands,
    'upcomingReviews' => $upcomingReviews,
    'recentAdjustments' => $recentAdjustments,
    'compensationEmployees' => $compensationEmployees,
    'usedBudget' => $usedBudget,

    //analytics
    'analyticsDateRange' => $analyticsDateRange,
    'analyticsDeptHeadcount' => $analyticsDeptHeadcount,
    'analyticsTotalHeadcount' => $analyticsTotalHeadcount,
    'analyticsDeptColors' => $analyticsDeptColors,
    'analyticsMonths' => $analyticsMonths,
    'analyticsHiresData' => $analyticsHiresData,
    'analyticsTerminationsData' => $analyticsTerminationsData,
    'analyticsTotalHires' => $analyticsTotalHires,
    'analyticsTotalTerminations' => $analyticsTotalTerminations,
    'analyticsNetGrowth' => $analyticsNetGrowth,
    'analyticsFemalePct' => $analyticsFemalePct,
    'analyticsMalePct' => $analyticsMalePct,
    'analyticsFemaleCount' => $analyticsFemaleCount,
    'analyticsMaleCount' => $analyticsMaleCount,
    'analyticsAge18_30' => $analyticsAge18_30,
    'analyticsAge31_45' => $analyticsAge31_45,
    'analyticsAge46' => $analyticsAge46,
    'analyticsTenureLess1' => $analyticsTenureLess1,
    'analyticsTenure1to3' => $analyticsTenure1to3,
    'analyticsTenure3plus' => $analyticsTenure3plus,
    'analyticsLastSync' => $analyticsLastSync,
    'analyticsAvgSalary' => $analyticsAvgSalary,
    'analyticsSalaryIncreasePct' => $analyticsSalaryIncreasePct,
    'analyticsSalaryIncrease' => $analyticsSalaryIncrease,
    'analyticsEmployeesPromoted' => $analyticsEmployeesPromoted,
    'analyticsTrainingsCompleted' => $analyticsTrainingsCompleted,
    'analyticsCertificationsEarned' => $analyticsCertificationsEarned,
    'analyticsAvgRating' => $analyticsAvgRating,
    'analyticsTotalRatings' => $analyticsTotalRatings,

    // Stats
    'stats' => [
        'totalAccounts' => $totalAccounts['count'] ?? 0,
        'totalHired' => $totalHired['count'] ?? 0,
        'onboarded' => $totalOnboarded['count'] ?? 0,
        'inProgress' => $totalInProgress['count'] ?? 0,
        'pending' => $totalPending['count'] ?? 0,
    ],

    'successionPage' => $successionPage,
    'successionPerPage' => $successionPerPage,
    'successionTotalPages' => $successionTotalPages,
    'successionTotalCandidates' => $successionTotalCandidates,
    'successionDepartmentFilter' => $successionDepartmentFilter,
    'successionPositionFilter' => $successionPositionFilter,
    'successionSortBy' => $successionSortBy,
    'successionDepartments' => $successionDepartments,
    'successionReadyCount' => $successionReadyCount,
    'successionTotalTrainings' => $successionTotalTrainings,
    'successionNoGapsCount' => $successionNoGapsCount,
    'successionCandidates' => $successionCandidates,
    'successionDeptSummary' => $successionDeptSummary,
    'successionReadyNow' => $successionReadyNow,
    'successionReadySoon' => $successionReadySoon,
    'successionInProgress' => $successionInProgress,

    //claims
    'claimsPage' => $claimsPage,
    'claimsPerPage' => $claimsPerPage,
    'claimsTotalPages' => $claimsTotalPages,
    'claimsTotal' => $claimsTotal,
    'claimsStatusFilter' => $claimsStatusFilter,
    'claimsDateFilter' => $claimsDateFilter,
    'claimsActiveTab' => $claimsActiveTab,
    'claimsList' => $claimsList,
    'claimsPendingCount' => $claimsPendingCount,
    'claimsPendingTotal' => $claimsPendingTotal,
    'claimsApprovedCount' => $claimsApprovedCount,
    'claimsApprovedTotal' => $claimsApprovedTotal,
    'claimsProcessedCount' => $claimsProcessedCount,
    'claimsProcessedTotal' => $claimsProcessedTotal,
    'claimsTotalPendingAmount' => $claimsTotalPendingAmount,
    'claimsTotalApprovedAmount' => $claimsTotalApprovedAmount,
    'claimsAverageAmount' => $claimsAverageAmount,
    'claimsProcessingTime' => $claimsProcessingTime,
    'claimsMostCommonType' => $claimsMostCommonType,
    'claimsMonthTotal' => $claimsMonthTotal,

    // HMO & BENEFITS VARIABLES
    'hmoPage' => $hmoPage,
    'hmoPerPage' => $hmoPerPage,
    'hmoTotalPages' => $hmoTotalPages,
    'hmoTotalBenefits' => $hmoTotalBenefits,
    'hmoProviderFilter' => $hmoProviderFilter,
    'hmoStatusFilter' => $hmoStatusFilter,
    'hmoProviders' => $hmoProviders,
    'hmoTotalEmployees' => $hmoTotalEmployees,
    'hmoEnrolledCount' => $hmoEnrolledCount,
    'hmoCoverageRate' => $hmoCoverageRate,
    'hmoPendingCount' => $hmoPendingCount,
    'hmoClaimsCount' => $hmoClaimsCount,
    'hmoClaimsTotal' => $hmoClaimsTotal,
    'hmoPremiumDue' => $hmoPremiumDue,
    'hmoCoveragePlans' => $hmoCoveragePlans,
    'hmoTotalMonthlyPremium' => $hmoTotalMonthlyPremium,
    'hmoCompanyShare' => $hmoCompanyShare,
    'hmoEmployeeShare' => $hmoEmployeeShare,
    'hmoExpiringSoon' => $hmoExpiringSoon,
    'hmoRecentEnrollments' => $hmoRecentEnrollments,
    'hmoBenefitsList' => $hmoBenefitsList,

    // PAYROLL VARIABLES
    'payrollPage' => $payrollPage,
    'payrollPerPage' => $payrollPerPage,
    'payrollTotalPages' => $payrollTotalPages,
    'payrollTotalEmployees' => $payrollTotalEmployees,
    'payrollTotalFiltered' => $payrollTotalFiltered,
    'payrollStatusFilter' => $payrollStatusFilter,
    'payrollDepartmentFilter' => $payrollDepartmentFilter,
    'payrollDepartments' => $payrollDepartments,
    'payrollPeriodStart' => $payrollPeriodStart,
    'payrollPeriodEnd' => $payrollPeriodEnd,
    'payrollPeriodLabel' => $payrollPeriodLabel,
    'payrollPayDate' => $payrollPayDate,
    'payrollPeriodType' => $payrollPeriodType,
    'payrollTotalGross' => $payrollTotalGross,
    'payrollTotalNet' => $payrollTotalNet,
    'payrollProcessedCount' => $payrollProcessedCount,
    'payrollPendingCount' => $payrollPendingCount,
    'payrollStatutoryDeductions' => $payrollStatutoryDeductions,
    'payrollTotalStatutory' => $payrollTotalStatutory,
    'payrollEmployees' => $payrollPaginatedEmployees,
    'payrollPageRegularHours' => $payrollPageRegularHours,
    'payrollPageOvertimeHours' => $payrollPageOvertimeHours,
    'payrollPageAverageNet' => $payrollPageAverageNet,
    'payrollPageClaimsTotal' => $payrollPageClaimsTotal,
    'payrollHistory' => $payrollHistory,
    'payrollTotalClaims' => $payrollTotalClaims,
    // NEW HISTORY VARIABLES
    'allPayrollHistory' => $allPayrollHistory, // All history for view all page
    'payrollHistoryTotal' => $payrollHistoryTotal,
    'allEmployees' => $allEmployees,


    // schedule
    'schedulePage' => $schedulePage,
    'schedulePerPage' => $schedulePerPage,
    'totalSchedulePages' => $totalSchedulePages,
    'totalSchedules' => $totalSchedules,
    'scheduleFilter' => $scheduleFilter,
    'scheduleDepartmentFilter' => $scheduleDepartmentFilter,
    'scheduleEmployeeFilter' => $scheduleEmployeeFilter,
    'scheduleDepartments' => $scheduleDepartments,
    'employeeSchedules' => $employeeSchedules,
    'schedulesToday' => $schedulesToday,
    'schedulesThisWeek' => $schedulesThisWeek,
    'schedulesUpcoming' => $schedulesUpcoming,
    'schedulesPast' => $schedulesPast,
    'bundledSchedules' => $bundledSchedules,
    'totalScheduleDates' => $totalScheduleDates,

    'shiftSwapRequests' => $shiftSwapRequests,
    'shiftSwapPendingCount' => $shiftSwapPendingCount,
]);