<?php
/**
 * Admin Panel - Session Check API
 * NextCode Group - AJAX Session Validation
 */

define('ADMIN_ACCESS', true);

// Include configurations
require_once '../config/database.php';
require_once '../config/security.php';

// Set JSON header
header('Content-Type: application/json');

// Check if request is AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(400);
    echo json_encode(['valid' => false, 'error' => 'Invalid request']);
    exit();
}

try {
    // Check session validity
    $isValid = $adminSecurity->checkAuth();
    
    $response = [
        'valid' => $isValid,
        'timestamp' => time()
    ];
    
    if ($isValid) {
        $response['user'] = [
            'id' => $_SESSION['admin_user_id'] ?? null,
            'username' => $_SESSION['admin_username'] ?? null,
            'role' => $_SESSION['admin_role'] ?? null
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'valid' => false, 
        'error' => 'Session check failed'
    ]);
}
?>
