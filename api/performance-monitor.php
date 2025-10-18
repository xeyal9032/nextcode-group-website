<?php
/**
 * Performance Monitoring API Endpoint
 * Real-time performance metrics ve alerts'i alır ve saklar
 * Enhanced with modern security and performance features
 */

// Strict security headers
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Enhanced CORS with security
$allowedOrigins = [
    'https://nextcode.az',
    'https://www.nextcode.az',
    'http://localhost:3000', // Development only
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: null');
}

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Input validation
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST', 'OPTIONS'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

// Error reporting for production
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once '../config/database.php';

class PerformanceMonitorAPI {
    private $pdo;
    private $logFile;
    private $cache;
    private $rateLimiter;
    
    public function __construct() {
        try {
            // Use database config from config file instead of hardcoded values
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 30,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
            
            $this->logFile = __DIR__ . '/../logs/performance-monitor.log';
            
            // Log dizinini oluştur
            if (!is_dir(dirname($this->logFile))) {
                mkdir(dirname($this->logFile), 0755, true);
            }
            
            $this->initializeCache();
            $this->initializeRateLimiter();
            
        } catch (PDOException $e) {
            $this->logError('Database connection failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Database connection failed']);
            exit();
        }
    }
    
    /**
     * Initialize simple file-based cache
     */
    private function initializeCache() {
        $this->cache = new class {
            private $cacheDir;
            
            public function __construct() {
                $this->cacheDir = __DIR__ . '/../cache/performance/';
                if (!is_dir($this->cacheDir)) {
                    mkdir($this->cacheDir, 0755, true);
                }
            }
            
            public function get($key, $ttl = 300) {
                $file = $this->cacheDir . md5($key) . '.cache';
                if (file_exists($file) && (time() - filemtime($file)) < $ttl) {
                    return unserialize(file_get_contents($file));
                }
                return null;
            }
            
            public function set($key, $data) {
                $file = $this->cacheDir . md5($key) . '.cache';
                file_put_contents($file, serialize($data), LOCK_EX);
            }
        };
    }
    
    /**
     * Initialize rate limiter
     */
    private function initializeRateLimiter() {
        $this->rateLimiter = new class {
            private $limits = [
                'GET' => ['requests' => 200, 'window' => 3600], // 200 per hour
                'POST' => ['requests' => 100, 'window' => 3600]  // 100 per hour
            ];
            
            public function checkLimit($method, $clientIP) {
                $key = $method . '_' . $clientIP;
                $file = __DIR__ . '/../cache/rate_limit/' . md5($key) . '.limit';
                
                if (!is_dir(dirname($file))) {
                    mkdir(dirname($file), 0755, true);
                }
                
                $limit = $this->limits[$method] ?? $this->limits['GET'];
                $now = time();
                
                if (file_exists($file)) {
                    $data = json_decode(file_get_contents($file), true);
                    if (($now - $data['window_start']) < $limit['window']) {
                        if ($data['requests'] >= $limit['requests']) {
                            return false;
                        }
                        $data['requests']++;
                    } else {
                        $data = ['requests' => 1, 'window_start' => $now];
                    }
                } else {
                    $data = ['requests' => 1, 'window_start' => $now];
                }
                
                file_put_contents($file, json_encode($data), LOCK_EX);
                return true;
            }
        };
    }
    
    /**
     * Get client IP address securely
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Performance metrics'i al ve sakla with enhanced security
     */
    public function handleRequest() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $clientIP = $this->getClientIP();
            
            // Rate limiting check
            if (!$this->rateLimiter->checkLimit($method, $clientIP)) {
                http_response_code(429);
                echo json_encode(['success' => false, 'error' => 'Rate limit exceeded']);
                return;
            }
            
            // Request size validation for POST
            if ($method === 'POST') {
                $contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;
                if ($contentLength > 1048576) { // 1MB limit
                    http_response_code(413);
                    echo json_encode(['success' => false, 'error' => 'Request too large']);
                    return;
                }
            }
            
            switch ($method) {
                case 'POST':
                    $this->handlePostRequest();
                    break;
                    
                case 'GET':
                    $this->handleGetRequest();
                    break;
                    
                default:
                    http_response_code(405);
                    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
                    break;
            }
        } catch (Exception $e) {
            $this->logError('Request handling error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Internal server error']);
        }
    }
    
    /**
     * POST request - Performance metrics'i al with enhanced validation
     */
    private function handlePostRequest() {
        try {
            $rawInput = file_get_contents('php://input');
            
            if (empty($rawInput)) {
                throw new Exception('Empty request body');
            }
            
            $input = json_decode($rawInput, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON input: ' . json_last_error_msg());
            }
            
            // Validate input structure
            if (!$this->validatePerformanceData($input)) {
                throw new Exception('Invalid performance data structure');
            }
            
            // Sanitize input data
            $input = $this->sanitizeInputData($input);
            
            // Performance metrics'i sakla
            $this->savePerformanceMetrics($input);
            
            // Alerts'i kontrol et
            $this->processAlerts($input);
            
            // Response
            echo json_encode([
                'success' => true,
                'message' => 'Performance metrics saved',
                'timestamp' => date('c'),
                'metrics_count' => is_array($input) ? count($input) : 1
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            $this->logError('POST request error: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => date('c')
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Validate performance data structure
     */
    private function validatePerformanceData($data) {
        if (!is_array($data)) {
            return false;
        }
        
        // Check if it's a single metric or array of metrics
        if (isset($data['metric_name'])) {
            // Single metric
            return isset($data['value']) && is_numeric($data['value']);
        }
        
        // Array of metrics
        foreach ($data as $metric) {
            if (!is_array($metric) || !isset($metric['metric_name']) || !isset($metric['value']) || !is_numeric($metric['value'])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Sanitize input data recursively
     */
    private function sanitizeInputData($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInputData'], $data);
        }
        
        if (is_string($data)) {
            return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }
    
    /**
     * GET request - Performance metrics'i getir
     */
    private function handleGetRequest() {
        try {
            $type = $_GET['type'] ?? 'summary';
            $limit = (int)($_GET['limit'] ?? 100);
            
            switch ($type) {
                case 'summary':
                    $data = $this->getPerformanceSummary();
                    break;
                    
                case 'metrics':
                    $data = $this->getRecentMetrics($limit);
                    break;
                    
                case 'alerts':
                    $data = $this->getRecentAlerts($limit);
                    break;
                    
                case 'trends':
                    $data = $this->getPerformanceTrends();
                    break;
                    
                default:
                    throw new Exception('Invalid type parameter');
            }
            
            echo json_encode([
                'success' => true,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            $this->logError('GET request error: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Performance metrics'i veritabanına kaydet
     */
    private function savePerformanceMetrics($data) {
        try {
            // Performance metrics tablosunu oluştur
            $this->createPerformanceTables();
            
            $stmt = $this->pdo->prepare("
                INSERT INTO performance_metrics (
                    timestamp, url, lcp, fid, cls, fcp, ttfb,
                    memory_used, memory_total, memory_limit,
                    navigation_dns, navigation_tcp, navigation_dom, navigation_load,
                    user_connection_type, user_connection_downlink, user_connection_rtt,
                    viewport_width, viewport_height, device_pixel_ratio,
                    interactions_clicks, interactions_scrolls, interactions_keypresses,
                    session_duration, user_agent, ip_address
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?
                )
            ");
            
            $stmt->execute([
                $data['timestamp'] ?? date('Y-m-d H:i:s'),
                $data['url'] ?? '',
                $data['metrics']['lcp'] ?? null,
                $data['metrics']['fid'] ?? null,
                $data['metrics']['cls'] ?? null,
                $data['metrics']['fcp'] ?? null,
                $data['metrics']['ttfb'] ?? null,
                $data['metrics']['realTime']['performance']['memory']['used'] ?? null,
                $data['metrics']['realTime']['performance']['memory']['total'] ?? null,
                $data['metrics']['realTime']['performance']['memory']['limit'] ?? null,
                $data['metrics']['realTime']['performance']['navigation']['dns'] ?? null,
                $data['metrics']['realTime']['performance']['navigation']['tcp'] ?? null,
                $data['metrics']['realTime']['performance']['navigation']['dom'] ?? null,
                $data['metrics']['realTime']['performance']['navigation']['load'] ?? null,
                $data['metrics']['realTime']['user']['connection']['effectiveType'] ?? null,
                $data['metrics']['realTime']['user']['connection']['downlink'] ?? null,
                $data['metrics']['realTime']['user']['connection']['rtt'] ?? null,
                $data['metrics']['realTime']['user']['viewport']['width'] ?? null,
                $data['metrics']['realTime']['user']['viewport']['height'] ?? null,
                $data['metrics']['realTime']['user']['viewport']['devicePixelRatio'] ?? null,
                $data['metrics']['realTime']['interactions']['clicks'] ?? 0,
                $data['metrics']['realTime']['interactions']['scrolls'] ?? 0,
                $data['metrics']['realTime']['interactions']['keypresses'] ?? 0,
                $data['metrics']['session']['duration'] ?? 0,
                $data['metrics']['realTime']['user']['device']['userAgent'] ?? '',
                $this->getClientIP()
            ]);
            
            $this->log("Performance metrics saved: " . $data['timestamp']);
            
        } catch (Exception $e) {
            $this->logError('Failed to save performance metrics: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Alerts'i işle
     */
    private function processAlerts($data) {
        if (empty($data['alerts'])) {
            return;
        }
        
        try {
            // Alerts tablosunu oluştur
            $this->createAlertsTable();
            
            $stmt = $this->pdo->prepare("
                INSERT INTO performance_alerts (
                    timestamp, metric, level, value, threshold, message,
                    url, user_agent, ip_address, resolved
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
            ");
            
            foreach ($data['alerts'] as $alert) {
                $stmt->execute([
                    $alert['timestamp'] ?? date('Y-m-d H:i:s'),
                    $alert['metric'],
                    $alert['level'],
                    $alert['value'],
                    $alert['threshold'],
                    $alert['message'],
                    $alert['url'] ?? '',
                    $alert['userAgent'] ?? '',
                    $this->getClientIP()
                ]);
            }
            
            $this->log("Alerts processed: " . count($data['alerts']));
            
        } catch (Exception $e) {
            $this->logError('Failed to process alerts: ' . $e->getMessage());
        }
    }
    
    /**
     * Performance summary getir
     */
    private function getPerformanceSummary() {
        try {
            // Son 24 saat
            $stmt = $this->pdo->query("
                SELECT 
                    COUNT(*) as total_requests,
                    AVG(lcp) as avg_lcp,
                    AVG(fid) as avg_fid,
                    AVG(cls) as avg_cls,
                    AVG(fcp) as avg_fcp,
                    AVG(ttfb) as avg_ttfb,
                    AVG(memory_used) as avg_memory_used,
                    COUNT(CASE WHEN lcp > 2500 THEN 1 END) as slow_lcp_count,
                    COUNT(CASE WHEN fid > 100 THEN 1 END) as slow_fid_count,
                    COUNT(CASE WHEN cls > 0.1 THEN 1 END) as high_cls_count
                FROM performance_metrics 
                WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ");
            
            $summary = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Alerts summary
            $stmt = $this->pdo->query("
                SELECT 
                    level,
                    COUNT(*) as count
                FROM performance_alerts 
                WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                GROUP BY level
            ");
            
            $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'summary' => $summary,
                'alerts' => $alerts,
                'period' => '24 hours'
            ];
            
        } catch (Exception $e) {
            $this->logError('Failed to get performance summary: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Son metrics'leri getir
     */
    private function getRecentMetrics($limit) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM performance_metrics 
                ORDER BY timestamp DESC 
                LIMIT ?
            ");
            
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $this->logError('Failed to get recent metrics: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Son alerts'leri getir
     */
    private function getRecentAlerts($limit) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM performance_alerts 
                ORDER BY timestamp DESC 
                LIMIT ?
            ");
            
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $this->logError('Failed to get recent alerts: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Performance trends getir
     */
    private function getPerformanceTrends() {
        try {
            // Son 7 gün için günlük ortalamalar
            $stmt = $this->pdo->query("
                SELECT 
                    DATE(timestamp) as date,
                    AVG(lcp) as avg_lcp,
                    AVG(fid) as avg_fid,
                    AVG(cls) as avg_cls,
                    AVG(fcp) as avg_fcp,
                    AVG(ttfb) as avg_ttfb,
                    COUNT(*) as request_count
                FROM performance_metrics 
                WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(timestamp)
                ORDER BY date DESC
            ");
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $this->logError('Failed to get performance trends: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Performance metrics tablosunu oluştur
     */
    private function createPerformanceTables() {
        try {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS performance_metrics (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    timestamp DATETIME NOT NULL,
                    url TEXT,
                    lcp DECIMAL(10,2),
                    fid DECIMAL(10,2),
                    cls DECIMAL(10,4),
                    fcp DECIMAL(10,2),
                    ttfb DECIMAL(10,2),
                    memory_used INT,
                    memory_total INT,
                    memory_limit INT,
                    navigation_dns INT,
                    navigation_tcp INT,
                    navigation_dom INT,
                    navigation_load INT,
                    user_connection_type VARCHAR(50),
                    user_connection_downlink DECIMAL(5,2),
                    user_connection_rtt INT,
                    viewport_width INT,
                    viewport_height INT,
                    device_pixel_ratio DECIMAL(3,2),
                    interactions_clicks INT DEFAULT 0,
                    interactions_scrolls INT DEFAULT 0,
                    interactions_keypresses INT DEFAULT 0,
                    session_duration INT DEFAULT 0,
                    user_agent TEXT,
                    ip_address VARCHAR(45),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_timestamp (timestamp),
                    INDEX idx_url (url(255)),
                    INDEX idx_metrics (lcp, fid, cls, fcp, ttfb)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            
        } catch (Exception $e) {
            $this->logError('Failed to create performance_metrics table: ' . $e->getMessage());
        }
    }
    
    /**
     * Alerts tablosunu oluştur
     */
    private function createAlertsTable() {
        try {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS performance_alerts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    timestamp DATETIME NOT NULL,
                    metric VARCHAR(50) NOT NULL,
                    level ENUM('INFO', 'WARNING', 'CRITICAL') NOT NULL,
                    value DECIMAL(10,4),
                    threshold DECIMAL(10,4),
                    message TEXT,
                    url TEXT,
                    user_agent TEXT,
                    ip_address VARCHAR(45),
                    resolved BOOLEAN DEFAULT FALSE,
                    resolved_at DATETIME,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_timestamp (timestamp),
                    INDEX idx_metric (metric),
                    INDEX idx_level (level),
                    INDEX idx_resolved (resolved)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            
        } catch (Exception $e) {
            $this->logError('Failed to create performance_alerts table: ' . $e->getMessage());
        }
    }
    
    
    /**
     * Log mesajı yaz
     */
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Error log mesajı yaz
     */
    private function logError($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] ERROR: $message" . PHP_EOL;
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

// API instance'ını oluştur ve request'i işle
$api = new PerformanceMonitorAPI();
$api->handleRequest();
?>
