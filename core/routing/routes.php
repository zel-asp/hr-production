<?php

$router->get('/', 'controller/ess/test.php');

$router->get('/login', 'controller/auth/login.php');

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