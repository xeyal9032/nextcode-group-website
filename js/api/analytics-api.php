<?php
/**
 * Google Analytics 4 API Endpoint
 * NextCode Group Analytics Data API
 * Enhanced with modern security and performance features
 */

// Strict security headers
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'');

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

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400'); // 24 hours

// Rate limiting
session_start();
if (!isset($_SESSION['api_requests'])) {
    $_SESSION['api_requests'] = [];
}

$currentTime = time();
$_SESSION['api_requests'] = array_filter($_SESSION['api_requests'], function($timestamp) use ($currentTime) {
    return ($currentTime - $timestamp) < 3600; // 1 hour window
});

if (count($_SESSION['api_requests']) > 1000) { // 1000 requests per hour
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Rate limit exceeded']);
    exit();
}

$_SESSION['api_requests'][] = $currentTime;

// OPTIONS request için CORS preflight
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

require_once '../config/database.php';
require_once '../config/analytics-config.php';

// Error reporting for production
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

class AnalyticsAPI {
    private $pdo;
    private $config;
    private $cache;
    private $rateLimiter;
    
    public function __construct() {
        try {
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
        } catch (PDOException $e) {
            $this->logError('Database connection failed: ' . $e->getMessage());
            $this->sendError('Database connection failed', 500);
            exit();
        }
        
        $this->config = $this->getAnalyticsConfig();
        $this->initializeCache();
        $this->initializeRateLimiter();
    }
    
    /**
     * Initialize simple file-based cache
     */
    private function initializeCache() {
        $this->cache = new class {
            private $cacheDir;
            
            public function __construct() {
                $this->cacheDir = __DIR__ . '/../cache/analytics/';
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
                'GET' => ['requests' => 100, 'window' => 3600], // 100 per hour
                'POST' => ['requests' => 50, 'window' => 3600]   // 50 per hour
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
     * Get analytics configuration with caching
     */
    private function getAnalyticsConfig() {
        $cached = $this->cache->get('analytics_config', 3600); // 1 hour cache
        if ($cached !== null) {
            return $cached;
        }
        
        $config = AnalyticsHelper::getConfig();
        $this->cache->set('analytics_config', $config);
        return $config;
    }
    
    /**
     * Ana request handler with enhanced security
     */
    public function handleRequest() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $endpoint = $this->sanitizeInput($_GET['endpoint'] ?? '');
            $clientIP = $this->getClientIP();
            
            // Rate limiting check
            if (!$this->rateLimiter->checkLimit($method, $clientIP)) {
                $this->sendError('Rate limit exceeded', 429);
                return;
            }
            
            // Input validation
            if (!$this->validateEndpoint($endpoint)) {
                $this->sendError('Invalid endpoint', 400);
                return;
            }
            
            // Request size validation
            if ($method === 'POST') {
                $contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;
                if ($contentLength > 1048576) { // 1MB limit
                    $this->sendError('Request too large', 413);
                    return;
                }
            }
            
            switch ($method) {
                case 'GET':
                    $this->handleGetRequest($endpoint);
                    break;
                case 'POST':
                    $this->handlePostRequest($endpoint);
                    break;
                default:
                    $this->sendError('Method not allowed', 405);
            }
        } catch (Exception $e) {
            $this->logError('API Error: ' . $e->getMessage());
            $this->sendError('Internal server error', 500);
        }
    }
    
    /**
     * Validate endpoint parameter
     */
    private function validateEndpoint($endpoint) {
        $allowedEndpoints = [
            'config', 'events', 'performance', 'users', 'ecommerce', 'reports',
            'event', 'user_property'
        ];
        return in_array($endpoint, $allowedEndpoints);
    }
    
    /**
     * Sanitize input data
     */
    private function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * GET request handler
     */
    private function handleGetRequest($endpoint) {
        switch ($endpoint) {
            case 'config':
                $this->getAnalyticsConfigResponse();
                break;
            case 'events':
                $this->getAnalyticsEvents();
                break;
            case 'performance':
                $this->getPerformanceData();
                break;
            case 'users':
                $this->getUserAnalytics();
                break;
            case 'ecommerce':
                $this->getEcommerceData();
                break;
            case 'reports':
                $this->getAnalyticsReports();
                break;
            default:
                $this->sendError('Invalid endpoint', 400);
        }
    }
    
    /**
     * POST request handler with enhanced validation
     */
    private function handlePostRequest($endpoint) {
        $rawInput = file_get_contents('php://input');
        
        if (empty($rawInput)) {
            $this->sendError('Empty request body', 400);
            return;
        }
        
        $input = json_decode($rawInput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendError('Invalid JSON input: ' . json_last_error_msg(), 400);
            return;
        }
        
        // Validate input structure
        if (!$this->validateInputData($input, $endpoint)) {
            $this->sendError('Invalid input data structure', 400);
            return;
        }
        
        // Sanitize input data
        $input = $this->sanitizeInputData($input);
        
        switch ($endpoint) {
            case 'event':
                $this->trackCustomEvent($input);
                break;
            case 'user_property':
                $this->updateUserProperty($input);
                break;
            case 'performance':
                $this->savePerformanceData($input);
                break;
            case 'ecommerce':
                $this->trackEcommerceEvent($input);
                break;
            default:
                $this->sendError('Invalid endpoint', 400);
        }
    }
    
    /**
     * Validate input data structure
     */
    private function validateInputData($input, $endpoint) {
        if (!is_array($input)) {
            return false;
        }
        
        switch ($endpoint) {
            case 'event':
                return isset($input['event_type']) && isset($input['user_id']);
            case 'user_property':
                return isset($input['user_id']) && isset($input['property_name']);
            case 'performance':
                return isset($input['metric_name']) && isset($input['value']);
            case 'ecommerce':
                return isset($input['event_type']) && isset($input['user_id']);
            default:
                return false;
        }
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
     * Analytics config response döndür
     */
    private function getAnalyticsConfigResponse() {
        $config = [
            'measurement_id' => $this->config['measurement_id'],
            'property_id' => $this->config['property_id'],
            'tracking_enabled' => [
                'page_view' => $this->config['enable_page_view_tracking'],
                'user_properties' => $this->config['enable_user_property_tracking'],
                'custom_events' => $this->config['enable_custom_event_tracking'],
                'ecommerce' => $this->config['enable_ecommerce_tracking'],
                'performance' => $this->config['enable_performance_tracking']
            ],
            'custom_dimensions' => $this->config['custom_dimensions'],
            'custom_metrics' => $this->config['custom_metrics'],
            'performance_thresholds' => $this->config['performance_thresholds']
        ];
        
        $this->sendResponse($config);
    }
    
    /**
     * Analytics events döndür
     */
    private function getAnalyticsEvents() {
        $limit = min((int)($_GET['limit'] ?? 100), 1000);
        $offset = (int)($_GET['offset'] ?? 0);
        $eventType = $_GET['type'] ?? '';
        
        try {
            $sql = "SELECT * FROM analytics_events WHERE 1=1";
            $params = [];
            
            if ($eventType) {
                $sql .= " AND event_type = ?";
                $params[] = $eventType;
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $events = $stmt->fetchAll();
            
            $this->sendResponse([
                'events' => $events,
                'total' => count($events),
                'limit' => $limit,
                'offset' => $offset
            ]);
        } catch (PDOException $e) {
            $this->logError('Failed to fetch events: ' . $e->getMessage());
            $this->sendError('Failed to fetch events', 500);
        }
    }
    
    /**
     * Performance data döndür
     */
    private function getPerformanceData() {
        $limit = min((int)($_GET['limit'] ?? 100), 1000);
        $offset = (int)($_GET['offset'] ?? 0);
        $metric = $_GET['metric'] ?? '';
        
        try {
            $sql = "SELECT * FROM analytics_performance WHERE 1=1";
            $params = [];
            
            if ($metric) {
                $sql .= " AND metric_name = ?";
                $params[] = $metric;
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll();
            
            $this->sendResponse([
                'performance_data' => $data,
                'total' => count($data),
                'limit' => $limit,
                'offset' => $offset
            ]);
        } catch (PDOException $e) {
            $this->logError('Failed to fetch performance data: ' . $e->getMessage());
            $this->sendError('Failed to fetch performance data', 500);
        }
    }
    
    /**
     * User analytics döndür
     */
    private function getUserAnalytics() {
        try {
            // User properties summary
            $stmt = $this->pdo->query("
                SELECT 
                    user_type,
                    subscription_level,
                    device_category,
                    browser,
                    os,
                    COUNT(*) as count
                FROM analytics_user_properties 
                GROUP BY user_type, subscription_level, device_category, browser, os
                ORDER BY count DESC
            ");
            $userSummary = $stmt->fetchAll();
            
            // User engagement metrics
            $stmt = $this->pdo->query("
                SELECT 
                    AVG(engagement_time) as avg_engagement,
                    MAX(engagement_time) as max_engagement,
                    COUNT(DISTINCT user_id) as unique_users,
                    COUNT(*) as total_sessions
                FROM analytics_user_engagement
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $engagementMetrics = $stmt->fetch();
            
            $this->sendResponse([
                'user_summary' => $userSummary,
                'engagement_metrics' => $engagementMetrics
            ]);
        } catch (PDOException $e) {
            $this->logError('Failed to fetch user analytics: ' . $e->getMessage());
            $this->sendError('Failed to fetch user analytics', 500);
        }
    }
    
    /**
     * E-commerce data döndür
     */
    private function getEcommerceData() {
        try {
            // Sales summary
            $stmt = $this->pdo->query("
                SELECT 
                    DATE(created_at) as date,
                    SUM(value) as total_sales,
                    COUNT(*) as transactions,
                    AVG(value) as avg_order_value
                FROM analytics_ecommerce 
                WHERE event_type = 'purchase'
                AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date DESC
            ");
            $salesSummary = $stmt->fetchAll();
            
            // Product performance
            $stmt = $this->pdo->query("
                SELECT 
                    item_name,
                    item_category,
                    COUNT(*) as views,
                    SUM(CASE WHEN event_type = 'add_to_cart' THEN 1 ELSE 0 END) as add_to_cart,
                    SUM(CASE WHEN event_type = 'purchase' THEN 1 ELSE 0 END) as purchases
                FROM analytics_ecommerce 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY item_name, item_category
                ORDER BY views DESC
                LIMIT 20
            ");
            $productPerformance = $stmt->fetchAll();
            
            $this->sendResponse([
                'sales_summary' => $salesSummary,
                'product_performance' => $productPerformance
            ]);
        } catch (PDOException $e) {
            $this->logError('Failed to fetch ecommerce data: ' . $e->getMessage());
            $this->sendError('Failed to fetch ecommerce data', 500);
        }
    }
    
    /**
     * Analytics reports döndür
     */
    private function getAnalyticsReports() {
        $reportType = $_GET['type'] ?? 'overview';
        $period = $_GET['period'] ?? '30d';
        
        try {
            switch ($reportType) {
                case 'overview':
                    $report = $this->generateOverviewReport($period);
                    break;
                case 'performance':
                    $report = $this->generatePerformanceReport($period);
                    break;
                case 'user_behavior':
                    $report = $this->generateUserBehaviorReport($period);
                    break;
                case 'ecommerce':
                    $report = $this->generateEcommerceReport($period);
                    break;
                default:
                    $this->sendError('Invalid report type', 400);
                    return;
            }
            
            $this->sendResponse($report);
        } catch (Exception $e) {
            $this->logError('Failed to generate report: ' . $e->getMessage());
            $this->sendError('Failed to generate report', 500);
        }
    }
    
    /**
     * Overview report oluştur
     */
    private function generateOverviewReport($period) {
        $days = $this->getDaysFromPeriod($period);
        
        // Page views
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count FROM analytics_events 
            WHERE event_type = 'page_view' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$days]);
        $pageViews = $stmt->fetch()['count'];
        
        // Unique users
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT user_id) as count FROM analytics_events 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$days]);
        $uniqueUsers = $stmt->fetch()['count'];
        
        // Sessions
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT session_id) as count FROM analytics_events 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$days]);
        $sessions = $stmt->fetch()['count'];
        
        // Bounce rate
        $stmt = $this->pdo->prepare("
            SELECT 
                (COUNT(CASE WHEN event_count = 1 THEN 1 END) * 100.0 / COUNT(*)) as bounce_rate
            FROM (
                SELECT session_id, COUNT(*) as event_count 
                FROM analytics_events 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY session_id
            ) session_events
        ");
        $stmt->execute([$days]);
        $bounceRate = round($stmt->fetch()['bounce_rate'], 2);
        
        return [
            'period' => $period,
            'page_views' => $pageViews,
            'unique_users' => $uniqueUsers,
            'sessions' => $sessions,
            'bounce_rate' => $bounceRate,
            'avg_session_duration' => $this->getAverageSessionDuration($days)
        ];
    }
    
    /**
     * Performance report oluştur
     */
    private function generatePerformanceReport($period) {
        $days = $this->getDaysFromPeriod($period);
        
        $stmt = $this->pdo->prepare("
            SELECT 
                metric_name,
                AVG(metric_value) as avg_value,
                MIN(metric_value) as min_value,
                MAX(metric_value) as max_value,
                COUNT(*) as samples
            FROM analytics_performance 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY metric_name
        ");
        $stmt->execute([$days]);
        $metrics = $stmt->fetchAll();
        
        return [
            'period' => $period,
            'metrics' => $metrics,
            'thresholds' => $this->config['performance_thresholds']
        ];
    }
    
    /**
     * User behavior report oluştur
     */
    private function generateUserBehaviorReport($period) {
        $days = $this->getDaysFromPeriod($period);
        
        // Top pages
        $stmt = $this->pdo->prepare("
            SELECT 
                page_location,
                COUNT(*) as views,
                COUNT(DISTINCT user_id) as unique_users
            FROM analytics_events 
            WHERE event_type = 'page_view' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY page_location 
            ORDER BY views DESC 
            LIMIT 10
        ");
        $stmt->execute([$days]);
        $topPages = $stmt->fetchAll();
        
        // User flow
        $stmt = $this->pdo->prepare("
            SELECT 
                event_type,
                COUNT(*) as count
            FROM analytics_events 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY event_type 
            ORDER BY count DESC
        ");
        $stmt->execute([$days]);
        $userFlow = $stmt->fetchAll();
        
        return [
            'period' => $period,
            'top_pages' => $topPages,
            'user_flow' => $userFlow
        ];
    }
    
    /**
     * E-commerce report oluştur
     */
    private function generateEcommerceReport($period) {
        $days = $this->getDaysFromPeriod($period);
        
        // Revenue
        $stmt = $this->pdo->prepare("
            SELECT 
                SUM(value) as total_revenue,
                COUNT(*) as transactions,
                AVG(value) as avg_order_value
            FROM analytics_ecommerce 
            WHERE event_type = 'purchase' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$days]);
        $revenue = $stmt->fetch();
        
        // Conversion funnel
        $stmt = $this->pdo->prepare("
            SELECT 
                event_type,
                COUNT(*) as count
            FROM analytics_ecommerce 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            AND event_type IN ('view_item', 'add_to_cart', 'purchase')
            GROUP BY event_type
        ");
        $stmt->execute([$days]);
        $funnel = $stmt->fetchAll();
        
        return [
            'period' => $period,
            'revenue' => $revenue,
            'conversion_funnel' => $funnel
        ];
    }
    
    /**
     * Period'den gün sayısını al
     */
    private function getDaysFromPeriod($period) {
        switch ($period) {
            case '7d': return 7;
            case '30d': return 30;
            case '90d': return 90;
            case '1y': return 365;
            default: return 30;
        }
    }
    
    /**
     * Average session duration hesapla
     */
    private function getAverageSessionDuration($days) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT AVG(engagement_time) as avg_duration
                FROM analytics_user_engagement 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            $stmt->execute([$days]);
            $result = $stmt->fetch();
            return round($result['avg_duration'] ?? 0, 2);
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Custom event track
     */
    private function trackCustomEvent($data) {
        $required = ['event_type', 'user_id'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                $this->sendError("Missing required field: {$field}", 400);
            }
        }
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO analytics_events (
                    event_type, user_id, session_id, page_location, 
                    event_data, user_agent, ip_address, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $data['event_type'],
                $data['user_id'],
                $data['session_id'] ?? null,
                $data['page_location'] ?? null,
                json_encode($data['event_data'] ?? []),
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $this->getClientIP()
            ]);
            
            $this->sendResponse(['success' => true, 'message' => 'Event tracked successfully']);
        } catch (PDOException $e) {
            $this->logError('Failed to track event: ' . $e->getMessage());
            $this->sendError('Failed to track event', 500);
        }
    }
    
    /**
     * User property güncelle
     */
    private function updateUserProperty($data) {
        $required = ['user_id', 'property_name', 'property_value'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                $this->sendError("Missing required field: {$field}", 400);
            }
        }
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO analytics_user_properties (
                    user_id, property_name, property_value, updated_at
                ) VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    property_value = VALUES(property_value),
                    updated_at = NOW()
            ");
            
            $stmt->execute([
                $data['user_id'],
                $data['property_name'],
                $data['property_value']
            ]);
            
            $this->sendResponse(['success' => true, 'message' => 'User property updated']);
        } catch (PDOException $e) {
            $this->logError('Failed to update user property: ' . $e->getMessage());
            $this->sendError('Failed to update user property', 500);
        }
    }
    
    /**
     * Performance data kaydet
     */
    private function savePerformanceData($data) {
        $required = ['user_id', 'metric_name', 'metric_value'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                $this->sendError("Missing required field: {$field}", 400);
            }
        }
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO analytics_performance (
                    user_id, metric_name, metric_value, page_location, 
                    user_agent, created_at
                ) VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $data['user_id'],
                $data['metric_name'],
                $data['metric_value'],
                $data['page_location'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
            
            $this->sendResponse(['success' => true, 'message' => 'Performance data saved']);
        } catch (PDOException $e) {
            $this->logError('Failed to save performance data: ' . $e->getMessage());
            $this->sendError('Failed to save performance data', 500);
        }
    }
    
    /**
     * E-commerce event track
     */
    private function trackEcommerceEvent($data) {
        $required = ['event_type', 'user_id'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                $this->sendError("Missing required field: {$field}", 400);
            }
        }
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO analytics_ecommerce (
                    event_type, user_id, session_id, page_location,
                    item_id, item_name, item_category, price, quantity,
                    value, currency, transaction_id, event_data, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $data['event_type'],
                $data['user_id'],
                $data['session_id'] ?? null,
                $data['page_location'] ?? null,
                $data['item_id'] ?? null,
                $data['item_name'] ?? null,
                $data['item_category'] ?? null,
                $data['price'] ?? null,
                $data['quantity'] ?? 1,
                $data['value'] ?? null,
                $data['currency'] ?? 'USD',
                $data['transaction_id'] ?? null,
                json_encode($data['event_data'] ?? [])
            ]);
            
            $this->sendResponse(['success' => true, 'message' => 'E-commerce event tracked']);
        } catch (PDOException $e) {
            $this->logError('Failed to track e-commerce event: ' . $e->getMessage());
            $this->sendError('Failed to track e-commerce event', 500);
        }
    }
    
    /**
     * Client IP al
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
     * Response gönder
     */
    private function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode([
            'success' => true,
            'data' => $data,
            'timestamp' => date('c')
        ]);
    }
    
    /**
     * Error gönder
     */
    private function sendError($message, $statusCode = 400) {
        http_response_code($statusCode);
        echo json_encode([
            'success' => false,
            'error' => $message,
            'timestamp' => date('c')
        ]);
    }
    
    /**
     * Error log
     */
    private function logError($message) {
        error_log("[Analytics API] " . $message);
    }
}

// API instance oluştur ve request'i handle et
$api = new AnalyticsAPI();
$api->handleRequest();
?>
