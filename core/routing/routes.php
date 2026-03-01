<?php

$router->get('/', 'controller/ess/get/ess.php');
$router->patch('/tasks/complete', 'controller/ess/update/taskCompleted.php');
$router->patch('/tasks/start', 'controller/ess/update/taskStarted.php');

$router->get('/login', 'controller/auth/login.php');
$router->post('/employee-login', 'controller/auth/employee.php');
$router->post('/logout', 'controller/auth/logout.php');


$router->get('/main', 'controller/main/get/main.php');

$router->post('/postJob', 'controller/main/post/createJob.php');
$router->patch('/update-job', 'controller/main/update/editJob.php');
$router->delete('/delete-job', 'controller/main/destroy/deleteJob.php');

$router->delete('/deleteApplicant', 'controller/main/destroy/deleteApplicant.php');
$router->patch('/updateApplicantStatus', 'controller/main/update/updateApplicantStatus.php');

$router->get('/jobPosting', 'controller/jobs/get/jobPost.php');
$router->post('/submitApplication', 'controller/jobs/post/insertApplication.php');

$router->post('/assignTask', 'controller/main/post/assignTask.php');


$router->post('/generate-employee-account', 'controller/main/post/generate_account.php');

$router->post('/save-performance-evaluation', 'controller/main/post/performanceEvaluation.php');
$router->delete('/delete-evaluation', 'controller/main/destroy/deleteEvaluation.php');

$router->post('/make-regular-employee', 'controller/main/post/make-regular-employee.php');

$router->post('/create-performance-improvement-plan', 'controller/main/post/pip.php');
$router->patch('/update-performance-improvement-plan', 'controller/main/update/updatePip.php');

$router->post('/benefits/enroll', 'controller/main/post/enrollBenefits.php');

$router->post('/leave_request', 'controller/ess/post/leaveRequest.php');

$router->post('/attendance/handle', 'controller/ess/post/attendance.php');


