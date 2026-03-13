<?php
// AUTHENTICATION ROUTES
$router->get('/login', 'controller/auth/login.php');
$router->post('/employee-login', 'controller/auth/employee.php');
$router->post('/hr-login', 'controller/auth/hrLogin.php');
$router->post('/logout', 'controller/auth/logout.php');

// ============ NEW API ROUTES ============
// Employees API
$router->get('/api/employees', 'api/employees.php');

// Applicants API
$router->get('/api/applicants', 'api/applicants.php');
$router->get('/api/test', 'api/test.php');

// Compensation API
$router->get('/api/compensation', 'api/compensation.php');
$router->post('/api/compensation', 'api/compensation.php');
$router->put('/api/compensation', 'api/compensation.php');
$router->delete('/api/compensation', 'api/compensation.php');

// Job Requisition API
$router->get('/api/job-requisitions', 'api/job-requisition.php');
$router->post('/api/job-requisitions', 'api/job-requisition.php');
$router->put('/api/job-requisitions', 'api/job-requisition.php');
$router->delete('/api/job-requisitions', 'api/job-requisition.php');
$router->patch('/api/job-requisitions', 'api/job-requisition.php');

// Claims API
$router->get('/api/claims', 'api/claims.php');
$router->post('/api/claims', 'api/claims.php');

// Payroll API
$router->get('/api/payroll', 'api/payroll.php');
$router->post('/api/payroll', 'api/payroll.php');

// ============ END NEW API ROUTES ============

// MAIN DASHBOARD & JOB MANAGEMENT
$router->get('/', 'controller/ess/get/ess.php');
$router->get('/main', 'controller/main/get/main.php');
$router->patch('/profile-update', 'controller/ess/update/updateProfile.php');
$router->post('/generate-payslip-pdf', 'controller/ess/post/generate-payslip-pdf.php');

//requisition
$router->patch('/update-requisition-status', 'controller/main/update/updateRequisition.php');

$router->post('/postJob', 'controller/main/post/createJob.php');
$router->patch('/update-job', 'controller/main/update/editJob.php');
$router->delete('/delete-job', 'controller/main/destroy/deleteJob.php');

$router->get('/jobPosting', 'controller/jobs/get/jobPost.php');
$router->post('/submitApplication', 'controller/jobs/post/insertApplication.php');

// APPLICANT MANAGEMENT
$router->delete('/deleteApplicant', 'controller/main/destroy/deleteApplicant.php');
$router->patch('/updateApplicantStatus', 'controller/main/update/updateApplicantStatus.php');
$router->patch('/hire-applicant', 'controller/main/update/hireFromContract.php');
$router->delete('/cancel-contract', 'controller/main/destroy/deleteContract.php');
$router->post('/schedule-contract', 'controller/main/post/scheduleContract.php');


// TASK MANAGEMENT
$router->patch('/tasks/complete', 'controller/ess/update/taskCompleted.php');
$router->patch('/tasks/start', 'controller/ess/update/taskStarted.php');
$router->post('/assignTask', 'controller/main/post/assignTask.php');
$router->delete('/delete-task', 'controller/main/destroy/deleteTask.php');

// EMPLOYEE MANAGEMENT
$router->post('/generate-employee-account', 'controller/main/post/generate_account.php');
$router->post('/make-regular-employee', 'controller/main/post/make-regular-employee.php');

// PERFORMANCE MANAGEMENT
$router->post('/save-performance-evaluation', 'controller/main/post/performanceEvaluation.php');
$router->delete('/delete-evaluation', 'controller/main/destroy/deleteEvaluation.php');
$router->post('/create-performance-improvement-plan', 'controller/main/post/pip.php');
$router->patch('/update-performance-improvement-plan', 'controller/main/update/updatePip.php');

// BENEFITS & LEAVE
$router->post('/benefits/enroll', 'controller/main/post/enrollBenefits.php');
$router->post('/leave_request', 'controller/ess/post/leaveRequest.php');
$router->delete('/remove-request', 'controller/ess/delete/leaveDelete.php');
$router->get('/export/employees-csv', 'controller/main/get/excelEmployees.php');

// ATTENDANCE
$router->post('/attendance/handle', 'controller/ess/post/attendance.php');
$router->post('/addProvider', 'controller/main/post/addProvider.php');
$router->post('/addNoteFromTraining', 'controller/main/post/trainingNote.php');

//LEAVE
$router->patch('/leave/deny', 'controller/main/update/leave-deny.php');
$router->patch('/leave/approve', 'controller/main/update/leave-approved.php');

//COMPETENCY
$router->post('/competency-assessment', 'controller/main/post/competency.php');

//TRAINING
$router->post('/addTraining', 'controller/main/post/addTraining.php');

//COMPENSATION
$router->post('/add-compensation', 'controller/main/post/addCompensation.php');

//CORE
$router->patch('/update-employee', 'controller/main/update/updateEmployeeInfo.php');

//Claims
$router->post('/submit-claims', 'controller/ess/post/submitClaims.php');

//schecdule
$router->post('/upload-attendance', 'controller/main/post/schedule.php');
$router->post('/request-swap-schedule', 'controller/ess/post/swapRequest.php');
$router->get('/download-upload', 'controller/main/get/download-upload.php');
$router->get('/download-schedule', 'controller/main/get/download-schedule.php');
$router->patch('/approve-swap-request', 'controller/main/update/approveSwap.php');
$router->patch('/reject-swap-request', 'controller/main/update/rejectSwap.php');

//social recognition
$router->post('/assign-mentor', 'controller/main/post/assignMentor.php');
$router->post('/give-recognition', 'controller/main/post/giveRecognition.php');
$router->post('/mentor-rating', 'controller/ess/post/mentorRating.php');

//Onboarding
$router->post('/upload-document', 'controller/ess/post/uploadDocuments.php');
$router->post('/send-requirement-notification', 'controller/main/post/requirementsNotif.php');

//payroll
$router->post('/payroll-summary', 'controller/main/post/savePayroll.php');
$router->post('/process-all-payroll', 'controller/main/post/saveAllPayroll.php');

$router->patch('/approve-timesheet', 'controller/main/update/approveTimesheet.php');
$router->patch('/updateClaimStatus', 'controller/main/update/updateClaimStatus.php');


$router->post('/assign-interventions', 'controller/main/post/assignIntervention.php');
$router->patch('/update-intervention', 'controller/main/update/updateIntervention.php');

$router->get('/generate-payslip', 'controller/ess/get/generate-payslip.php');