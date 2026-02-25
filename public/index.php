<?php

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'core/function/function.php';

spl_autoload_register(function ($class) {
    $class = str_replace("Core" . DIRECTORY_SEPARATOR, "", $class);
    require base_path("core/Class/{$class}.php");
});

$router = new Core\Router();

$routes = require base_path('core/routing/routes.php');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$method = $_POST['__method'] ?? $_SERVER['REQUEST_METHOD'];

$router->routes($uri, $method);