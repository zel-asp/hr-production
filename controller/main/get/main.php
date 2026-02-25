<?php

use Core\Database;

$config = require base_path('config/config.php');
$db = new Database($config['database']);

try {
    $jobPostings = $db->query(
        "SELECT id, position, department, location, shift, salary, created_at 
        FROM job_postings ORDER BY created_at DESC"
    )->find();
} catch (\Throwable $th) {
    $jobPostings = [];
    error_log($th->getMessage());
}

try {
    $allApplicants = $db->query(
        "SELECT id, full_name, email, phone, position, experience, education, skills, resume_path, cover_note, status, hired_date, start_date, created_at 
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

try {
    $hiredApplicants = $db->query(
        "SELECT id, full_name, position, hired_date, start_date 
        FROM applicants 
        WHERE status = 'Hired' 
        ORDER BY created_at DESC"
    )->find();
} catch (\Throwable $th) {
    $hiredApplicants = [];
    error_log($th->getMessage());
}

// Get tasks with applicant details
try {
    $tasks = $db->query(
        "SELECT t.*, a.full_name, a.position, a.start_date, a.hired_date 
        FROM tasks t 
        JOIN applicants a ON t.assigned_to = a.id 
        WHERE a.status = 'Hired' 
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
    $onboardingTasks = $db->query(
        "SELECT 
            a.id AS applicant_id,
            a.full_name,
            a.position,
            a.department,
            a.hired_date,
            a.start_date,
            'Active' as status,
            COUNT(t.id) AS total_tasks,
            SUM(CASE WHEN t.status = 'Completed' THEN 1 ELSE 0 END) AS completed_tasks
        FROM applicants a
        LEFT JOIN tasks t ON a.id = t.assigned_to
        WHERE a.status = 'Hired'
        GROUP BY a.id
        ORDER BY a.start_date DESC"
    )->find();
} catch (\Throwable $th) {
    $onboardingTasks = [];
    error_log($th->getMessage());
}
// Get tasks with status for each applicant
try {
    $applicantTasks = $db->query(
        "SELECT 
            t.*,
            a.full_name,
            a.position,
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
        JOIN applicants a ON t.assigned_to = a.id
        WHERE a.status = 'Hired'
        ORDER BY urgency ASC, t.due_date ASC"
    )->find();
} catch (\Throwable $th) {
    $applicantTasks = [];
    error_log($th->getMessage());
}

// Get unique departments for onboarding filter
try {
    $departments = $db->query(
        "SELECT DISTINCT COALESCE(a.department, jp.department, '') as department
        FROM applicants a
        LEFT JOIN job_postings jp ON a.position = jp.position
        WHERE a.status = 'Hired'
        AND COALESCE(a.department, jp.department, '') != ''
        UNION
        SELECT 'Kitchen'
        UNION
        SELECT 'Service'
        UNION
        SELECT 'Management'
        UNION
        SELECT 'HR'
        ORDER BY department"
    )->find();
} catch (\Throwable $th) {
    // Fallback departments if query fails
    $departments = [
        ['department' => 'Kitchen'],
        ['department' => 'Service'],
        ['department' => 'Management'],
        ['department' => 'HR']
    ];
    error_log($th->getMessage());
}


try {
    $staffMembers = $db->query(
        "SELECT DISTINCT assigned_staff 
        FROM tasks 
        WHERE assigned_staff IS NOT NULL AND assigned_staff != ''
        UNION
        SELECT 'Sarah Reyes' as assigned_staff
        UNION
        SELECT 'Mike Dela Cruz'
        UNION
        SELECT 'Lisa Martinez'
        ORDER BY assigned_staff"
    )->find();
} catch (\Throwable $th) {
    $staffMembers = [
        ['assigned_staff' => 'Sarah Reyes'],
        ['assigned_staff' => 'Mike Dela Cruz'],
        ['assigned_staff' => 'Lisa Martinez']
    ];
    error_log($th->getMessage());
}

// Fetch hired applicants (probationary employees) for the dropdown and table
try {
    $probationaryEmployees = $db->query(
        "SELECT e.id, e.full_name, e.email, e.position, e.hired_date, e.start_date 
    FROM employees e
    WHERE e.status = 'Probationary' 
    ORDER BY e.hired_date DESC"
    )->find();
} catch (\Throwable $th) {
    $probationaryEmployees = [];
    error_log($th->getMessage());
}

// Fetch hired applicants WITHOUT existing employee account
try {
    $availableEmployees = $db->query(
        "SELECT a.id, a.full_name, a.email, a.position, a.hired_date, a.start_date
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
    $availableEmployees = [];
    error_log($th->getMessage());
}

// Pagination for Generated Accounts
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = 10;
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
    // Total accounts generated
    $totalAccounts = $db->query("SELECT COUNT(*) as count FROM employee_accounts")->fetch_one();
} catch (\Throwable $th) {
    $totalAccounts = ['count' => 0];
    error_log($th->getMessage());
}

try {
    $totalHired = $db->query("SELECT COUNT(*) as count FROM employees WHERE status = 'Active'")->fetch_one();
} catch (\Throwable $th) {
    $totalHired = ['count' => 0];
}

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

$nhPage = isset($_GET['nh_page']) ? max(1, (int) $_GET['nh_page']) : 1;
$nhPerPage = 5;
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
        "SELECT id, full_name, position, hired_date, start_date, onboarding_status, department
        FROM employees ORDER BY hired_date DESC LIMIT $nhPerPage OFFSET $nhOffset"
    )->find();
} catch (\Throwable $th) {
    $paginatedNewHires = [];
}

// Fetch recent evaluations with employee details
try {
    $recentEvaluations = $db->query(
        "SELECT 
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
            DATEDIFF(pe.review_period_end, CURDATE()) as days_since_review
        FROM performance_evaluations pe
        JOIN employees e ON pe.employee_id = e.id
        ORDER BY pe.created_at DESC
        LIMIT 10"
    )->find();
} catch (\Throwable $th) {
    $recentEvaluations = [];
    error_log($th->getMessage());
}

// Total pages
$totalNewHirePages = ceil($totalNewHires / $nhPerPage);

view_path('main', 'index', [
    'jobPostings' => $jobPostings,
    'applicants' => $allApplicants,
    'recentApplicants' => $recentApplicants,
    'hiredApplicants' => $hiredApplicants,
    'tasks' => $tasks,
    'onboardingTasks' => $onboardingTasks,
    'applicantTasks' => $applicantTasks,
    'staffMembers' => $staffMembers,
    'departments' => $departments,
    'probationaryEmployees' => $probationaryEmployees,
    'availableEmployees' => $availableEmployees,
    'generatedAccounts' => $generatedAccounts,
    'totalPages' => $totalPages,
    'paginatedNewHires' => $paginatedNewHires,
    'nhPage' => $nhPage,
    'totalNewHirePages' => $totalNewHirePages,
    'recentEvaluations' => $recentEvaluations,
    'stats' => [
        'totalAccounts' => $totalAccounts['count'] ?? 0,
        'totalHired' => $totalHired['count'] ?? 0,
        'onboarded' => $totalOnboarded['count'] ?? 0,
        'inProgress' => $totalInProgress['count'] ?? 0,
        'pending' => $totalPending['count'] ?? 0,
    ]
]);