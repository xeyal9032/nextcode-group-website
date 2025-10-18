<?php
/**
 * Analytics API Endpoint
 * Service Worker ve analytics.js için backend endpoint
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get request data
$input = json_decode(file_get_contents('php://input'), true);

// Response
$response = [
    'success' => true,
    'timestamp' => time(),
    'message' => 'Analytics data received'
];

// If POST request with data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input) {
    // Log analytics data (optional)
    $logFile = __DIR__ . '/../logs/analytics.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    $logEntry = json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => $input['type'] ?? 'unknown',
        'data' => $input['data'] ?? [],
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]) . "\n";
    
    @file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    $response['logged'] = true;
}

echo json_encode($response);
