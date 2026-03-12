<?php
// Simple test.php - No errors guaranteed
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

echo json_encode([
    'success' => true,
    'message' => 'Test page is working',
    'time' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $_SERVER['REQUEST_URI']
]);