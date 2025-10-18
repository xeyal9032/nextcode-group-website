<?php
// Otomatik Senkronizasyon API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

class AutoSyncManager {
    private $pdo;
    private $syncLog = [];
    
    public function __construct() {
        try {
            $database = new Database();
            $this->pdo = $database->getConnection();
            $this->createSyncTables();
        } catch (Exception $e) {
            error_log('AutoSync database connection failed: ' . $e->getMessage());
            $this->pdo = null;
        }
    }
    
    private function createSyncTables() {
        if (!$this->pdo) return;
        
        // Sync log table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS sync_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                table_name VARCHAR(100) NOT NULL,
                record_id INT NOT NULL,
                action VARCHAR(20) NOT NULL,
                old_data JSON,
                new_data JSON,
                sync_status ENUM('pending', 'synced', 'failed') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                synced_at TIMESTAMP NULL,
                error_message TEXT NULL,
                INDEX idx_table_record (table_name, record_id),
                INDEX idx_status (sync_status),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Cache invalidation table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS cache_invalidation (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cache_key VARCHAR(255) NOT NULL,
                cache_type ENUM('page', 'api', 'static') NOT NULL,
                invalidated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                reason VARCHAR(255),
                INDEX idx_key (cache_key),
                INDEX idx_type (cache_type),
                INDEX idx_invalidated (invalidated_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
    
    public function logChange($tableName, $recordId, $action, $oldData = null, $newData = null) {
        if (!$this->pdo) return false;
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO sync_log (table_name, record_id, action, old_data, new_data) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $tableName,
                $recordId,
                $action,
                $oldData ? json_encode($oldData) : null,
                $newData ? json_encode($newData) : null
            ]);
            
            $this->syncLog[] = [
                'table' => $tableName,
                'record' => $recordId,
                'action' => $action,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            // Trigger immediate sync for critical tables
            if (in_array($tableName, ['contact_info', 'site_settings', 'site_content'])) {
                $this->processSyncQueue();
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Sync log error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function invalidateCache($cacheKey, $cacheType = 'page', $reason = 'Data updated') {
        if (!$this->pdo) return false;
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO cache_invalidation (cache_key, cache_type, reason) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$cacheKey, $cacheType, $reason]);
            
            // Clear specific cache files
            $this->clearCacheFiles($cacheKey, $cacheType);
            
            return true;
        } catch (Exception $e) {
            error_log('Cache invalidation error: ' . $e->getMessage());
            return false;
        }
    }
    
    private function clearCacheFiles($cacheKey, $cacheType) {
        $cacheDir = '../cache/';
        if (!is_dir($cacheDir)) return;
        
        $patterns = [
            'page' => ["{$cacheKey}_*.cache", "page_{$cacheKey}_*.cache"],
            'api' => ["api_{$cacheKey}_*.cache", "{$cacheKey}_api_*.cache"],
            'static' => ["static_{$cacheKey}_*.cache", "{$cacheKey}_static_*.cache"]
        ];
        
        if (isset($patterns[$cacheType])) {
            foreach ($patterns[$cacheType] as $pattern) {
                $files = glob($cacheDir . $pattern);
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }
    
    public function processSyncQueue() {
        if (!$this->pdo) return false;
        
        try {
            // Get pending sync records
            $stmt = $this->pdo->query("
                SELECT * FROM sync_log 
                WHERE sync_status = 'pending' 
                ORDER BY created_at ASC 
                LIMIT 10
            ");
            
            $pendingRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($pendingRecords as $record) {
                $this->syncRecord($record);
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Sync queue processing error: ' . $e->getMessage());
            return false;
        }
    }
    
    private function syncRecord($record) {
        try {
            // Update sync status to processing
            $stmt = $this->pdo->prepare("
                UPDATE sync_log 
                SET sync_status = 'synced', synced_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$record['id']]);
            
            // Invalidate related caches
            $this->invalidateCache($record['table_name'], 'api', 'Auto sync');
            
            // Trigger webhooks if configured
            $this->triggerWebhooks($record);
            
            return true;
        } catch (Exception $e) {
            // Mark as failed
            $stmt = $this->pdo->prepare("
                UPDATE sync_log 
                SET sync_status = 'failed', error_message = ? 
                WHERE id = ?
            ");
            $stmt->execute([$e->getMessage(), $record['id']]);
            
            error_log('Sync record error: ' . $e->getMessage());
            return false;
        }
    }
    
    private function triggerWebhooks($record) {
        // Webhook URLs configuration
        $webhooks = [
            'contact_info' => [
                'https://nextcodegroup.ostwind.az/api/webhook/contact-update.php',
                'https://nextcodegroup.ostwind.az/api/webhook/cache-clear.php'
            ],
            'site_settings' => [
                'https://nextcodegroup.ostwind.az/api/webhook/settings-update.php'
            ]
        ];
        
        if (isset($webhooks[$record['table_name']])) {
            foreach ($webhooks[$record['table_name']] as $webhookUrl) {
                $this->callWebhook($webhookUrl, $record);
            }
        }
    }
    
    private function callWebhook($url, $data) {
        $postData = json_encode([
            'table' => $data['table_name'],
            'record_id' => $data['record_id'],
            'action' => $data['action'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => $postData,
                'timeout' => 5
            ]
        ]);
        
        try {
            $result = file_get_contents($url, false, $context);
            return $result !== false;
        } catch (Exception $e) {
            error_log('Webhook call failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getSyncStatus() {
        if (!$this->pdo) return null;
        
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    sync_status,
                    COUNT(*) as count,
                    MAX(created_at) as last_sync
                FROM sync_log 
                GROUP BY sync_status
            ");
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Sync status error: ' . $e->getMessage());
            return null;
        }
    }
}

// Handle API requests
$syncManager = new AutoSyncManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        exit;
    }
    
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'log_change':
            $result = $syncManager->logChange(
                $input['table_name'] ?? '',
                $input['record_id'] ?? 0,
                $input['action'] ?? '',
                $input['old_data'] ?? null,
                $input['new_data'] ?? null
            );
            echo json_encode(['success' => $result]);
            break;
            
        case 'invalidate_cache':
            $result = $syncManager->invalidateCache(
                $input['cache_key'] ?? '',
                $input['cache_type'] ?? 'page',
                $input['reason'] ?? 'Manual invalidation'
            );
            echo json_encode(['success' => $result]);
            break;
            
        case 'process_queue':
            $result = $syncManager->processSyncQueue();
            echo json_encode(['success' => $result]);
            break;
            
        case 'get_status':
            $status = $syncManager->getSyncStatus();
            echo json_encode(['success' => true, 'data' => $status]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action']);
    }
} else {
    // GET request - return sync status
    $status = $syncManager->getSyncStatus();
    echo json_encode([
        'success' => true,
        'data' => $status,
        'message' => 'Auto Sync API is running'
    ]);
}
?>
