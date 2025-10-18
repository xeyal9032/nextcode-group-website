<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Debug-Info: Settings API v1.0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Log function for debugging
if (!function_exists('logDebug')) {
    function logDebug($message) {
        error_log(date('[Y-m-d H:i:s] ') . "Settings API: " . $message);
    }
}

logDebug("API request started");

try {
    // Check if database config file exists
    $configPath = __DIR__ . '/../config/database.php';
    if (!file_exists($configPath)) {
        throw new Exception("Database config file not found at: " . $configPath);
    }
    
    logDebug("Including database config");
    require_once $configPath;
    
    // Check if Database class exists
    if (!class_exists('Database')) {
        throw new Exception("Database class not found");
    }
    
    logDebug("Creating database instance");
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        throw new Exception("Failed to get database connection");
    }
    
    logDebug("Database connection successful");
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Check if site_settings table exists
        $tableCheck = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='site_settings'");
        $tableExists = $tableCheck ? $tableCheck->fetchColumn() : false;
        if (!$tableExists) {
            throw new Exception("site_settings table does not exist");
        }
        
        logDebug("site_settings table exists");
        
        // Get site settings
        $stmt = $conn->prepare("SELECT * FROM site_settings ORDER BY setting_key");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . implode(', ', $conn->errorInfo()));
        }
        
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Failed to execute statement: " . implode(', ', $stmt->errorInfo()));
        }
        
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        logDebug("Found " . count($settings) . " settings");
        
        // Convert to key-value pairs
        $settingsData = [];
        foreach ($settings as $setting) {
            $settingsData[$setting['setting_key']] = $setting['setting_value'];
        }
        
        logDebug("Settings data prepared successfully");
        
        echo json_encode([
            'success' => true,
            'data' => $settingsData,
            'debug' => [
                'timestamp' => date('Y-m-d H:i:s'),
                'settings_count' => count($settings),
                'database_file' => $db->getDatabasePath() ?? 'unknown'
            ]
        ]);
    } else {
        throw new Exception("Method not allowed: " . $_SERVER['REQUEST_METHOD']);
    }
    
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    $errorFile = $e->getFile();
    $errorLine = $e->getLine();
    
    logDebug("Error occurred: " . $errorMessage . " in " . $errorFile . " on line " . $errorLine);
    
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $errorMessage,
        'debug' => [
            'error_file' => $errorFile,
            'error_line' => $errorLine,
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION,
            'config_path' => $configPath ?? 'unknown'
        ]
    ]);
}
?>