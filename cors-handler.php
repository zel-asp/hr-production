<?php
// cors-handler.php - Railway optimized version

// Allow from any origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-API-Key");
header("Access-Control-Max-Age: 3600");

// Handle OPTIONS request immediately
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// For non-OPTIONS requests, continue normally