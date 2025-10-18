<?php
/**
 * Admin Panel - Dashboard Statistics API
 * NextCode Group - Real-time Dashboard Data
 */

define('ADMIN_ACCESS', true);

// Include configurations
require_once '../config/database.php';
require_once '../config/security.php';

// Check authentication
if (!$adminSecurity->checkAuth()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Set JSON header
header('Content-Type: application/json');

try {
    $stats = [];
    
    if ($pdo) {
        // File count
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_files");
        $result = $stmt->fetch();
        $stats[] = [
            'label' => 'Total Files',
            'value' => number_format($result['total'] ?? 0),
            'icon' => 'file-alt'
        ];
        
        // Recent activity count (last 24 hours)
        $stmt = $pdo->query("
            SELECT COUNT(*) as total 
            FROM admin_activity_log 
            WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $result = $stmt->fetch();
        $stats[] = [
            'label' => 'Recent Activity',
            'value' => number_format($result['total'] ?? 0),
            'icon' => 'activity'
        ];
        
        // Active sessions
        $stmt = $pdo->query("
            SELECT COUNT(*) as total 
            FROM admin_sessions 
            WHERE expires_at > NOW()
        ");
        $result = $stmt->fetch();
        $stats[] = [
            'label' => 'Active Sessions',
            'value' => number_format($result['total'] ?? 0),
            'icon' => 'users'
        ];
        
        // Database size
        $stmt = $pdo->query("
            SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
        ");
        $result = $stmt->fetch();
        $stats[] = [
            'label' => 'Database Size',
            'value' => ($result['size_mb'] ?? 0) . ' MB',
            'icon' => 'server'
        ];
    } else {
        // Fallback stats when database is not available
        $stats = [
            ['label' => 'Total Files', 'value' => '0', 'icon' => 'file-alt'],
            ['label' => 'Recent Activity', 'value' => '0', 'icon' => 'activity'],
            ['label' => 'Active Sessions', 'value' => '1', 'icon' => 'users'],
            ['label' => 'System Status', 'value' => 'Online', 'icon' => 'server']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load dashboard stats'
    ]);
}
?>
