<?php
// SIMPLE PROXY - Guaranteed to work

// Always send CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-API-Key");

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Forward to API
$api_path = str_replace('/proxy.php', '', $_SERVER['REQUEST_URI']);
$api_url = "http://hr.wuaze.com" . $api_path;

// Use file_get_contents for simplicity
$context = stream_context_create([
    'http' => [
        'method' => $_SERVER['REQUEST_METHOD'],
        'header' => "Content-Type: application/json\r\n",
        'ignore_errors' => true
    ]
]);

// Add body for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $options = stream_context_get_options($context);
    $options['http']['content'] = file_get_contents('php://input');
    stream_context_set_option($context, $options);
}

$response = file_get_contents($api_url, false, $context);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to connect to API']);
} else {
    echo $response;
}