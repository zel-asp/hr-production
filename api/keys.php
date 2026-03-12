<?php
// Include CORS handler
require_once __DIR__ . '/../cors-handler.php';

// Fallback for servers without getallheaders()
if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

function getApiKey()
{
    // Try multiple ways to get the API key
    // 1. Check URL parameter first (easiest for testing)
    if (isset($_GET['api_key'])) {
        return $_GET['api_key'];
    }

    // 2. Check X-API-Key URL parameter
    if (isset($_GET['X-API-Key'])) {
        return $_GET['X-API-Key'];
    }

    // 3. Check headers
    $headers = getallheaders();

    // Try different header variations
    $headerVariations = [
        'X-API-Key',
        'X-Api-Key',
        'X-API-KEY',
        'API-Key',
        'Api-Key'
    ];

    foreach ($headerVariations as $variation) {
        if (isset($headers[$variation])) {
            return $headers[$variation];
        }
    }

    // 4. Check Authorization header as Bearer token
    if (isset($headers['Authorization'])) {
        $auth = $headers['Authorization'];
        if (strpos($auth, 'Bearer ') === 0) {
            return substr($auth, 7);
        }
    }

    return null;
}

function validateApiKey()
{
    $apiKey = getApiKey();

    if (!$apiKey) {
        sendError('API key is required. Provide via ?api_key= or X-API-Key header', 401);
    }

    $validKeys = [
        // Existing keys
        'hr_system_2026_secure_key_12345' => ['name' => 'HR System', 'permissions' => 'all'],
        'finance_system_2026_key_67890' => ['name' => 'Finance System', 'permissions' => 'write'],
        'employee_portal_key_11111' => ['name' => 'Employee Portal', 'permissions' => 'read'],

        // New Core Systems key
        'core_system_2026_key_54321' => ['name' => 'Core Systems', 'permissions' => 'write'],

        // New Logistics key
        'logistic_system_2026_key_98765' => ['name' => 'Logistics', 'permissions' => 'write']
    ];

    if (!isset($validKeys[$apiKey])) {
        sendError('Invalid API key: ' . $apiKey, 401);
    }

    return $validKeys[$apiKey];
}

function canWrite($apiInfo)
{
    return $apiInfo['permissions'] === 'all' || $apiInfo['permissions'] === 'write';
}

function canRead($apiInfo)
{
    return true;
}