<?php
// Analytics API Endpoint
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

class AnalyticsAPI {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Create analytics tables if they don't exist
        $this->createTables();
    }
    
    private function createTables() {
        // Page views table
        $pageViewsQuery = "
            CREATE TABLE IF NOT EXISTS analytics_page_views (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(255) NOT NULL,
                page_url VARCHAR(500) NOT NULL,
                page_title VARCHAR(255),
                referrer VARCHAR(500),
                user_agent TEXT,
                ip_address VARCHAR(45),
                country VARCHAR(100),
                city VARCHAR(100),
                device_type VARCHAR(50),
                browser VARCHAR(100),
                os VARCHAR(100),
                screen_resolution VARCHAR(20),
                viewport_size VARCHAR(20),
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_session (session_id),
                INDEX idx_page (page_url),
                INDEX idx_timestamp (timestamp)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        
        // User interactions table
        $interactionsQuery = "
            CREATE TABLE IF NOT EXISTS analytics_interactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(255) NOT NULL,
                page_url VARCHAR(500) NOT NULL,
                event_type VARCHAR(100) NOT NULL,
                element_type VARCHAR(100),
                element_id VARCHAR(255),
                element_class VARCHAR(255),
                element_text TEXT,
                coordinates JSON,
                scroll_depth INT,
                time_on_page INT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_session (session_id),
                INDEX idx_event (event_type),
                INDEX idx_timestamp (timestamp)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        
        // Performance metrics table
        $performanceQuery = "
            CREATE TABLE IF NOT EXISTS analytics_performance (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(255) NOT NULL,
                page_url VARCHAR(500) NOT NULL,
                metric_type VARCHAR(100) NOT NULL,
                metric_data JSON,
                is_final BOOLEAN DEFAULT FALSE,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_session (session_id),
                INDEX idx_metric (metric_type),
                INDEX idx_timestamp (timestamp)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        
        // Traffic sources table
        $trafficQuery = "
            CREATE TABLE IF NOT EXISTS analytics_traffic_sources (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(255) NOT NULL,
                source VARCHAR(255),
                medium VARCHAR(255),
                campaign VARCHAR(255),
                term VARCHAR(255),
                content VARCHAR(255),
                referrer VARCHAR(500),
                landing_page VARCHAR(500),
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_session (session_id),
                INDEX idx_source (source),
                INDEX idx_timestamp (timestamp)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        
        // Conversions table
        $conversionsQuery = "
            CREATE TABLE IF NOT EXISTS analytics_conversions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(255) NOT NULL,
                conversion_type VARCHAR(100) NOT NULL,
                conversion_value VARCHAR(255),
                page_url VARCHAR(500),
                form_data JSON,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_session (session_id),
                INDEX idx_type (conversion_type),
                INDEX idx_timestamp (timestamp)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        
        try {
            $this->db->exec($pageViewsQuery);
            $this->db->exec($interactionsQuery);
            $this->db->exec($performanceQuery);
            $this->db->exec($trafficQuery);
            $this->db->exec($conversionsQuery);
        } catch (PDOException $e) {
            error_log('Analytics table creation failed: ' . $e->getMessage());
        }
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'POST':
                return $this->handlePost();
            case 'GET':
                return $this->handleGet();
            default:
                return $this->sendResponse(['error' => 'Method not allowed'], 405);
        }
    }
    
    private function handlePost() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['event_type'])) {
            return $this->sendResponse(['error' => 'Invalid request data'], 400);
        }
        
        $eventType = $input['event_type'];
        $data = $input['data'] ?? [];
        
        switch ($eventType) {
            case 'page_view':
                return $this->recordPageView($data);
            case 'user_interaction':
                return $this->recordInteraction($data);
            case 'performance_metric':
            case 'performance_report':
                return $this->recordPerformance($eventType, $data);
            case 'traffic_source':
                return $this->recordTrafficSource($data);
            case 'conversion':
                return $this->recordConversion($data);
            default:
                return $this->sendResponse(['error' => 'Unknown event type'], 400);
        }
    }
    
    private function handleGet() {
        $action = $_GET['action'] ?? 'dashboard';
        $period = $_GET['period'] ?? '7d';
        
        switch ($action) {
            case 'dashboard':
                return $this->getDashboardData($period);
            case 'traffic':
                return $this->getTrafficData($period);
            case 'performance':
                return $this->getPerformanceData($period);
            case 'conversions':
                return $this->getConversionsData($period);
            case 'pages':
                return $this->getPopularPages($period);
            case 'realtime':
                return $this->getRealtimeData();
            default:
                return $this->sendResponse(['error' => 'Unknown action'], 400);
        }
    }
    
    private function recordPageView($data) {
        $query = "
            INSERT INTO analytics_page_views 
            (session_id, page_url, page_title, referrer, user_agent, ip_address, 
             device_type, browser, os, screen_resolution, viewport_size) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['session_id'] ?? '',
                $data['page'] ?? '',
                $data['title'] ?? '',
                $data['referrer'] ?? '',
                $data['user_agent'] ?? '',
                $this->getClientIP(),
                $data['device_type'] ?? '',
                $data['browser'] ?? '',
                $data['os'] ?? '',
                $data['screen_resolution'] ?? '',
                $data['viewport_size'] ?? ''
            ]);
            
            return $this->sendResponse(['success' => true]);
        } catch (PDOException $e) {
            error_log('Page view recording failed: ' . $e->getMessage());
            return $this->sendResponse(['error' => 'Database error'], 500);
        }
    }
    
    private function recordInteraction($data) {
        $query = "
            INSERT INTO analytics_interactions 
            (session_id, page_url, event_type, element_type, element_id, 
             element_class, element_text, coordinates, scroll_depth, time_on_page) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['session_id'] ?? '',
                $data['page'] ?? '',
                $data['event_type'] ?? '',
                $data['element_type'] ?? '',
                $data['element_id'] ?? '',
                $data['element_class'] ?? '',
                $data['element_text'] ?? '',
                json_encode($data['coordinates'] ?? null),
                $data['scroll_depth'] ?? 0,
                $data['time_on_page'] ?? 0
            ]);
            
            return $this->sendResponse(['success' => true]);
        } catch (PDOException $e) {
            error_log('Interaction recording failed: ' . $e->getMessage());
            return $this->sendResponse(['error' => 'Database error'], 500);
        }
    }
    
    private function recordPerformance($eventType, $data) {
        $query = "
            INSERT INTO analytics_performance 
            (session_id, page_url, metric_type, metric_data, is_final) 
            VALUES (?, ?, ?, ?, ?)
        ";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['session_id'] ?? '',
                $data['page'] ?? '',
                $eventType,
                json_encode($data),
                $data['is_final'] ?? false
            ]);
            
            return $this->sendResponse(['success' => true]);
        } catch (PDOException $e) {
            error_log('Performance recording failed: ' . $e->getMessage());
            return $this->sendResponse(['error' => 'Database error'], 500);
        }
    }
    
    private function recordTrafficSource($data) {
        $query = "
            INSERT INTO analytics_traffic_sources 
            (session_id, source, medium, campaign, term, content, referrer, landing_page) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['session_id'] ?? '',
                $data['source'] ?? '',
                $data['medium'] ?? '',
                $data['campaign'] ?? '',
                $data['term'] ?? '',
                $data['content'] ?? '',
                $data['referrer'] ?? '',
                $data['landing_page'] ?? ''
            ]);
            
            return $this->sendResponse(['success' => true]);
        } catch (PDOException $e) {
            error_log('Traffic source recording failed: ' . $e->getMessage());
            return $this->sendResponse(['error' => 'Database error'], 500);
        }
    }
    
    private function recordConversion($data) {
        $query = "
            INSERT INTO analytics_conversions 
            (session_id, conversion_type, conversion_value, page_url, form_data) 
            VALUES (?, ?, ?, ?, ?)
        ";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['session_id'] ?? '',
                $data['conversion_type'] ?? '',
                $data['conversion_value'] ?? '',
                $data['page'] ?? '',
                json_encode($data['form_data'] ?? null)
            ]);
            
            return $this->sendResponse(['success' => true]);
        } catch (PDOException $e) {
            error_log('Conversion recording failed: ' . $e->getMessage());
            return $this->sendResponse(['error' => 'Database error'], 500);
        }
    }
    
    private function getDashboardData($period) {
        $dateCondition = $this->getDateCondition($period);
        
        // Get basic metrics
        $metrics = [
            'page_views' => $this->getPageViewsCount($dateCondition),
            'unique_visitors' => $this->getUniqueVisitorsCount($dateCondition),
            'bounce_rate' => $this->getBounceRate($dateCondition),
            'avg_session_duration' => $this->getAvgSessionDuration($dateCondition),
            'conversions' => $this->getConversionsCount($dateCondition)
        ];
        
        // Get traffic sources
        $trafficSources = $this->getTrafficSources($dateCondition);
        
        // Get popular pages
        $popularPages = $this->getPopularPages($period, 10);
        
        // Get daily stats for chart
        $dailyStats = $this->getDailyStats($period);
        
        return $this->sendResponse([
            'metrics' => $metrics,
            'traffic_sources' => $trafficSources,
            'popular_pages' => $popularPages,
            'daily_stats' => $dailyStats
        ]);
    }
    
    private function getDateCondition($period) {
        switch ($period) {
            case '1d':
                return "timestamp >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
            case '7d':
                return "timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case '30d':
                return "timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            case '90d':
                return "timestamp >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
            default:
                return "timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        }
    }
    
    private function getPageViewsCount($dateCondition) {
        $query = "SELECT COUNT(*) as count FROM analytics_page_views WHERE $dateCondition";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    }
    
    private function getUniqueVisitorsCount($dateCondition) {
        $query = "SELECT COUNT(DISTINCT session_id) as count FROM analytics_page_views WHERE $dateCondition";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    }
    
    private function getBounceRate($dateCondition) {
        $totalSessions = "SELECT COUNT(DISTINCT session_id) FROM analytics_page_views WHERE $dateCondition";
        $bounceSessions = "
            SELECT COUNT(DISTINCT session_id) 
            FROM analytics_page_views 
            WHERE $dateCondition 
            AND session_id IN (
                SELECT session_id 
                FROM analytics_page_views 
                WHERE $dateCondition 
                GROUP BY session_id 
                HAVING COUNT(*) = 1
            )
        ";
        
        $totalStmt = $this->db->query($totalSessions);
        $bounceStmt = $this->db->query($bounceSessions);
        
        $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['COUNT(DISTINCT session_id)'] ?? 0;
        $bounce = $bounceStmt->fetch(PDO::FETCH_ASSOC)['COUNT(DISTINCT session_id)'] ?? 0;
        
        return $total > 0 ? round(($bounce / $total) * 100, 2) : 0;
    }
    
    private function getAvgSessionDuration($dateCondition) {
        $query = "
            SELECT AVG(duration) as avg_duration
            FROM (
                SELECT session_id, 
                       TIMESTAMPDIFF(SECOND, MIN(timestamp), MAX(timestamp)) as duration
                FROM analytics_page_views 
                WHERE $dateCondition
                GROUP BY session_id
                HAVING COUNT(*) > 1
            ) as session_durations
        ";
        
        $stmt = $this->db->query($query);
        return round($stmt->fetch(PDO::FETCH_ASSOC)['avg_duration'] ?? 0);
    }
    
    private function getConversionsCount($dateCondition) {
        $query = "SELECT COUNT(*) as count FROM analytics_conversions WHERE $dateCondition";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    }
    
    private function getTrafficSources($dateCondition) {
        $query = "
            SELECT source, COUNT(*) as count
            FROM analytics_traffic_sources 
            WHERE $dateCondition
            GROUP BY source
            ORDER BY count DESC
            LIMIT 10
        ";
        
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getPopularPages($period, $limit = 10) {
        $dateCondition = $this->getDateCondition($period);
        
        $query = "
            SELECT page_url, page_title, COUNT(*) as views
            FROM analytics_page_views 
            WHERE $dateCondition
            GROUP BY page_url, page_title
            ORDER BY views DESC
            LIMIT $limit
        ";
        
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getDailyStats($period) {
        $dateCondition = $this->getDateCondition($period);
        
        $query = "
            SELECT DATE(timestamp) as date,
                   COUNT(*) as page_views,
                   COUNT(DISTINCT session_id) as unique_visitors
            FROM analytics_page_views 
            WHERE $dateCondition
            GROUP BY DATE(timestamp)
            ORDER BY date ASC
        ";
        
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getRealtimeData() {
        // Get data from last 30 minutes
        $query = "
            SELECT COUNT(*) as active_users
            FROM analytics_page_views 
            WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
        ";
        
        $stmt = $this->db->query($query);
        $activeUsers = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'] ?? 0;
        
        // Get recent page views
        $recentQuery = "
            SELECT page_url, timestamp
            FROM analytics_page_views 
            WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
            ORDER BY timestamp DESC
            LIMIT 10
        ";
        
        $recentStmt = $this->db->query($recentQuery);
        $recentViews = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $this->sendResponse([
            'active_users' => $activeUsers,
            'recent_views' => $recentViews,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    private function getClientIP() {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
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
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    private function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }
}

// Initialize and handle request
try {
    $api = new AnalyticsAPI();
    $api->handleRequest();
} catch (Exception $e) {
    error_log('Analytics API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>