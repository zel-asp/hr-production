<?php
function getDB() {
    // Try multiple possible paths for config - WITHOUT using base_path()
    $possiblePaths = [
        __DIR__ . '/../config/config.php',           // api/../config/config.php
        __DIR__ . '/../../config/config.php',        // Go up two levels
        dirname(__DIR__) . '/config/config.php',     // Same as first option
        $_SERVER['DOCUMENT_ROOT'] . '/config/config.php'
    ];
    
    $configFile = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $configFile = $path;
            break;
        }
    }
    
    if (!$configFile) {
        sendError('Configuration file not found. Tried: ' . implode(', ', $possiblePaths), 500);
    }
    
    $config = require $configFile;
    
    // Make sure Database class is loaded - also without base_path()
    $databasePaths = [
        __DIR__ . '/../core/Class/Database.php',
        __DIR__ . '/../../core/Class/Database.php',
        dirname(__DIR__) . '/core/Class/Database.php',
        $_SERVER['DOCUMENT_ROOT'] . '/core/Class/Database.php'
    ];
    
    $dbClassFile = null;
    foreach ($databasePaths as $path) {
        if (file_exists($path)) {
            $dbClassFile = $path;
            break;
        }
    }
    
    if (!$dbClassFile) {
        sendError('Database class not found', 500);
    }
    
    require_once $dbClassFile;
    
    return new Core\Database($config['database']);
}

function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

function sendError($message, $statusCode = 400) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

function getRequestData() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    return $data ?? [];
}

function notifyEmployee($employeeId, $title, $message) {
    $db = getDB();
    
    // Create notifications table if it doesn't exist
    $db->query("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            is_read TINYINT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (employee_id)
        )
    ");
    
    $sql = "INSERT INTO notifications (employee_id, title, message, created_at) 
            VALUES (?, ?, ?, NOW())";
    return $db->query($sql, [$employeeId, $title, $message]);
}