<?php


// GLOBAL CORS HANDLER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-API-Key, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Enable error reporting temporarily (remove after fixing)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check if this is an API request
if (strpos($uri, '/api/') === 0) {

    $parts = explode('/', trim($uri, '/'));

    if (isset($parts[1])) {
        $endpoint = $parts[1];
        $apiFile = __DIR__ . '/api/' . $endpoint . '.php';

        if (file_exists($apiFile)) {
            require $apiFile;
            exit;
        }
    }
}

// Only start session for non-API requests
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// BASE_PATH for InfinityFree
const BASE_PATH = __DIR__ . DIRECTORY_SEPARATOR;
require BASE_PATH . 'core/function/function.php';

// FIXED AUTOLOADER
spl_autoload_register(function ($class) {
    // Remove the Core namespace
    $class = str_replace('Core\\', '', $class);
    
    // Build correct file path
    $classFile = base_path("core/Class/{$class}.php");
    
    if (file_exists($classFile)) {
        require $classFile;
    } else {
        // Try alternative paths
        $alternativePaths = [
            base_path("core/class/{$class}.php"),
            base_path("Core/Class/{$class}.php"),
            base_path("core/classes/{$class}.php")
        ];
        
        $found = false;
        foreach ($alternativePaths as $path) {
            if (file_exists($path)) {
                require $path;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            die("Error: Cannot find class file for: {$class}");
        }
    }
});

// Check if Router class exists
if (!class_exists('Core\Router')) {
    // Try without namespace
    if (class_exists('Router')) {
        // Create a class alias
        class_alias('Router', 'Core\Router');
    } else {
        die("Error: Router class not found. Check core/Class/Router.php");
    }
}

$router = new Core\Router();
$routes = require base_path('core/routing/routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_POST['__method'] ?? $_SERVER['REQUEST_METHOD'];
$router->routes($uri, $method);