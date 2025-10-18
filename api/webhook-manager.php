<?php
// Webhook Sistemi - Otomatik Güncelleme
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

class WebhookManager {
    private $pdo;
    private $webhookLog = [];
    
    public function __construct() {
        try {
            $database = new Database();
            $this->pdo = $database->getConnection();
            $this->createWebhookTables();
        } catch (Exception $e) {
            error_log('Webhook database connection failed: ' . $e->getMessage());
            $this->pdo = null;
        }
    }
    
    private function createWebhookTables() {
        if (!$this->pdo) return;
        
        // Webhook log table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS webhook_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                webhook_name VARCHAR(100) NOT NULL,
                payload JSON NOT NULL,
                response_code INT,
                response_body TEXT,
                execution_time DECIMAL(10,4),
                status ENUM('success', 'failed', 'pending') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                executed_at TIMESTAMP NULL,
                error_message TEXT NULL,
                INDEX idx_webhook (webhook_name),
                INDEX idx_status (status),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
    
    public function handleWebhook($webhookName, $payload) {
        $startTime = microtime(true);
        
        try {
            // Log webhook call
            $this->logWebhook($webhookName, $payload);
            
            // Process based on webhook type
            $result = $this->processWebhook($webhookName, $payload);
            
            $executionTime = microtime(true) - $startTime;
            
            // Update log with result
            $this->updateWebhookLog($webhookName, $payload, 200, $result, $executionTime, 'success');
            
            return [
                'success' => true,
                'message' => 'Webhook processed successfully',
                'data' => $result,
                'execution_time' => $executionTime
            ];
            
        } catch (Exception $e) {
            $executionTime = microtime(true) - $startTime;
            
            // Update log with error
            $this->updateWebhookLog($webhookName, $payload, 500, null, $executionTime, 'failed', $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'execution_time' => $executionTime
            ];
        }
    }
    
    private function processWebhook($webhookName, $payload) {
        switch ($webhookName) {
            case 'contact-update':
                return $this->handleContactUpdate($payload);
                
            case 'cache-clear':
                return $this->handleCacheClear($payload);
                
            case 'settings-update':
                return $this->handleSettingsUpdate($payload);
                
            case 'content-update':
                return $this->handleContentUpdate($payload);
                
            default:
                throw new Exception("Unknown webhook: {$webhookName}");
        }
    }
    
    private function handleContactUpdate($payload) {
        $table = $payload['table'] ?? '';
        $recordId = $payload['record_id'] ?? 0;
        $action = $payload['action'] ?? '';
        
        // Clear contact-related caches
        $this->clearContactCaches();
        
        // Update contact info API cache
        $this->updateContactAPICache();
        
        // Notify connected clients
        $this->notifyClients('contact_updated', [
            'table' => $table,
            'record_id' => $recordId,
            'action' => $action
        ]);
        
        return [
            'action' => 'contact_updated',
            'caches_cleared' => true,
            'clients_notified' => true
        ];
    }
    
    private function handleCacheClear($payload) {
        $cacheKey = $payload['cache_key'] ?? '';
        $cacheType = $payload['cache_type'] ?? 'all';
        
        // Clear specific cache
        $clearedFiles = $this->clearCacheFiles($cacheKey, $cacheType);
        
        // Clear browser cache headers
        $this->setCacheHeaders();
        
        return [
            'action' => 'cache_cleared',
            'cache_key' => $cacheKey,
            'cache_type' => $cacheType,
            'files_cleared' => count($clearedFiles)
        ];
    }
    
    private function handleSettingsUpdate($payload) {
        $table = $payload['table'] ?? '';
        $recordId = $payload['record_id'] ?? 0;
        
        // Clear settings cache
        $this->clearSettingsCache();
        
        // Update theme settings
        $this->updateThemeSettings();
        
        // Notify clients
        $this->notifyClients('settings_updated', [
            'table' => $table,
            'record_id' => $recordId
        ]);
        
        return [
            'action' => 'settings_updated',
            'theme_updated' => true,
            'clients_notified' => true
        ];
    }
    
    private function handleContentUpdate($payload) {
        $table = $payload['table'] ?? '';
        $recordId = $payload['record_id'] ?? 0;
        
        // Clear content cache
        $this->clearContentCache();
        
        // Update dynamic content
        $this->updateDynamicContent();
        
        // Notify clients
        $this->notifyClients('content_updated', [
            'table' => $table,
            'record_id' => $recordId
        ]);
        
        return [
            'action' => 'content_updated',
            'dynamic_content_updated' => true,
            'clients_notified' => true
        ];
    }
    
    private function clearContactCaches() {
        $cacheDir = '../cache/';
        if (!is_dir($cacheDir)) return [];
        
        $patterns = [
            'contact_info_*.cache',
            'contact_*.cache',
            'api_contact_*.cache'
        ];
        
        $clearedFiles = [];
        foreach ($patterns as $pattern) {
            $files = glob($cacheDir . $pattern);
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $clearedFiles[] = basename($file);
                }
            }
        }
        
        return $clearedFiles;
    }
    
    private function updateContactAPICache() {
        // Force regenerate contact info API response
        $apiFile = '../api/contact-info.php';
        if (file_exists($apiFile)) {
            // Touch the file to update modification time
            touch($apiFile);
        }
    }
    
    private function clearCacheFiles($cacheKey, $cacheType) {
        $cacheDir = '../cache/';
        if (!is_dir($cacheDir)) return [];
        
        $patterns = [
            'all' => ['*.cache'],
            'page' => ["{$cacheKey}_*.cache", "page_{$cacheKey}_*.cache"],
            'api' => ["api_{$cacheKey}_*.cache", "{$cacheKey}_api_*.cache"],
            'static' => ["static_{$cacheKey}_*.cache", "{$cacheKey}_static_*.cache"]
        ];
        
        $clearedFiles = [];
        $patternList = $patterns[$cacheType] ?? $patterns['all'];
        
        foreach ($patternList as $pattern) {
            $files = glob($cacheDir . $pattern);
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $clearedFiles[] = basename($file);
                }
            }
        }
        
        return $clearedFiles;
    }
    
    private function setCacheHeaders() {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
    
    private function clearSettingsCache() {
        $cacheDir = '../cache/';
        if (!is_dir($cacheDir)) return;
        
        $patterns = [
            'site_settings_*.cache',
            'settings_*.cache',
            'theme_*.cache'
        ];
        
        foreach ($patterns as $pattern) {
            $files = glob($cacheDir . $pattern);
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
    
    private function updateThemeSettings() {
        // Update theme variables if needed
        $themeFile = '../css/theme-variables.css';
        if (file_exists($themeFile)) {
            touch($themeFile);
        }
    }
    
    private function clearContentCache() {
        $cacheDir = '../cache/';
        if (!is_dir($cacheDir)) return;
        
        $patterns = [
            'site_content_*.cache',
            'content_*.cache',
            'dynamic_*.cache'
        ];
        
        foreach ($patterns as $pattern) {
            $files = glob($cacheDir . $pattern);
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
    
    private function updateDynamicContent() {
        // Force regenerate dynamic content
        $contentFile = '../api/content.php';
        if (file_exists($contentFile)) {
            touch($contentFile);
        }
    }
    
    private function notifyClients($event, $data) {
        // Send Server-Sent Events to connected clients
        $this->sendSSE($event, $data);
        
        // Send WebSocket message if available
        $this->sendWebSocket($event, $data);
    }
    
    private function sendSSE($event, $data) {
        $sseFile = '../sse/notifications.php';
        if (file_exists($sseFile)) {
            file_put_contents($sseFile, json_encode([
                'event' => $event,
                'data' => $data,
                'timestamp' => time()
            ]));
        }
    }
    
    private function sendWebSocket($event, $data) {
        // WebSocket implementation would go here
        // For now, we'll use a simple file-based approach
        $wsFile = '../ws/messages.json';
        $wsDir = dirname($wsFile);
        
        if (!is_dir($wsDir)) {
            mkdir($wsDir, 0755, true);
        }
        
        $message = [
            'event' => $event,
            'data' => $data,
            'timestamp' => time()
        ];
        
        file_put_contents($wsFile, json_encode($message));
    }
    
    private function logWebhook($webhookName, $payload) {
        if (!$this->pdo) return;
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO webhook_log (webhook_name, payload) 
                VALUES (?, ?)
            ");
            
            $stmt->execute([
                $webhookName,
                json_encode($payload)
            ]);
            
            $this->webhookLog[] = [
                'webhook' => $webhookName,
                'payload' => $payload,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
        } catch (Exception $e) {
            error_log('Webhook log error: ' . $e->getMessage());
        }
    }
    
    private function updateWebhookLog($webhookName, $payload, $responseCode, $responseBody, $executionTime, $status, $errorMessage = null) {
        if (!$this->pdo) return;
        
        try {
            $stmt = $this->pdo->prepare("
                UPDATE webhook_log 
                SET response_code = ?, response_body = ?, execution_time = ?, 
                    status = ?, executed_at = NOW(), error_message = ?
                WHERE webhook_name = ? AND payload = ? AND executed_at IS NULL
                ORDER BY created_at DESC LIMIT 1
            ");
            
            $stmt->execute([
                $responseCode,
                $responseBody ? json_encode($responseBody) : null,
                $executionTime,
                $status,
                $errorMessage,
                $webhookName,
                json_encode($payload)
            ]);
            
        } catch (Exception $e) {
            error_log('Webhook log update error: ' . $e->getMessage());
        }
    }
    
    public function getWebhookLog($limit = 50) {
        if (!$this->pdo) return null;
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM webhook_log 
                ORDER BY created_at DESC 
                LIMIT ?
            ");
            
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log('Get webhook log error: ' . $e->getMessage());
            return null;
        }
    }
}

// Handle webhook requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        exit;
    }
    
    $webhookName = $input['webhook'] ?? '';
    $payload = $input['payload'] ?? [];
    
    if (empty($webhookName)) {
        http_response_code(400);
        echo json_encode(['error' => 'Webhook name required']);
        exit;
    }
    
    $webhookManager = new WebhookManager();
    $result = $webhookManager->handleWebhook($webhookName, $payload);
    
    http_response_code($result['success'] ? 200 : 500);
    echo json_encode($result);
    
} else {
    // GET request - return webhook log
    $webhookManager = new WebhookManager();
    $log = $webhookManager->getWebhookLog();
    
    echo json_encode([
        'success' => true,
        'data' => $log,
        'message' => 'Webhook Manager'
    ]);
}
?>
