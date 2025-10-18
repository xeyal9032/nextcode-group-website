<?php
/**
 * NextCode Group - Uptime Monitoring System
 * Web sitesinin durumunu izler ve raporlar
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/error_handler.php';

class UptimeMonitor {
    private $pdo;
    private $logFile;
    private $checkInterval;
    private $alertEmail;
    
    public function __construct() {
        try {
            $database = new Database();
            $this->pdo = $database->getConnection();
            $this->logFile = __DIR__ . '/../logs/uptime-monitor.log';
            $this->checkInterval = 300; // 5 dakika
            $this->alertEmail = 'admin@nextcodegroup.com';
            
            if (!$this->pdo) {
                throw new Exception('Database connection failed');
            }
        } catch (Exception $e) {
            error_log('UptimeMonitor initialization failed: ' . $e->getMessage());
            $this->pdo = null;
        }
    }
    
    /**
     * Monitoring tablosunu oluştur
     */
    public function createMonitoringTable() {
        if (!$this->pdo) {
            return false;
        }
        
        try {
            $sql = "CREATE TABLE IF NOT EXISTS uptime_monitoring (
                id INT AUTO_INCREMENT PRIMARY KEY,
                check_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                site_url VARCHAR(500) NOT NULL,
                response_time INT DEFAULT 0,
                status_code INT DEFAULT 0,
                is_online TINYINT(1) DEFAULT 1,
                error_message TEXT,
                server_info TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_check_time (check_time),
                INDEX idx_site_url (site_url),
                INDEX idx_is_online (is_online)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->pdo->exec($sql);
            return true;
        } catch (Exception $e) {
            error_log('Failed to create monitoring table: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Site durumunu kontrol et
     */
    public function checkSiteStatus($url = null) {
        if (!$url) {
            $url = $this->getSiteUrl();
        }
        
        $startTime = microtime(true);
        $result = [
            'url' => $url,
            'check_time' => date('Y-m-d H:i:s'),
            'response_time' => 0,
            'status_code' => 0,
            'is_online' => false,
            'error_message' => null,
            'server_info' => null
        ];
        
        try {
            // cURL ile site kontrolü
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'NextCode Uptime Monitor 1.0',
                CURLOPT_HEADER => true,
                CURLOPT_NOBODY => true
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $responseTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
            $error = curl_error($ch);
            curl_close($ch);
            
            $endTime = microtime(true);
            $totalTime = round(($endTime - $startTime) * 1000); // ms
            
            $result['response_time'] = $totalTime;
            $result['status_code'] = $httpCode;
            $result['is_online'] = ($httpCode >= 200 && $httpCode < 400) && empty($error);
            
            if ($error) {
                $result['error_message'] = $error;
            }
            
            // Server bilgilerini al
            $result['server_info'] = json_encode([
                'http_code' => $httpCode,
                'total_time' => $responseTime,
                'content_type' => curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?? 'unknown'
            ]);
            
        } catch (Exception $e) {
            $result['error_message'] = $e->getMessage();
            $result['is_online'] = false;
        }
        
        // Sonucu veritabanına kaydet
        $this->saveCheckResult($result);
        
        return $result;
    }
    
    /**
     * Site URL'sini al
     */
    private function getSiteUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host;
    }
    
    /**
     * Kontrol sonucunu veritabanına kaydet
     */
    private function saveCheckResult($result) {
        if (!$this->pdo) {
            return false;
        }
        
        try {
            $sql = "INSERT INTO uptime_monitoring 
                    (site_url, response_time, status_code, is_online, error_message, server_info) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $result['url'],
                $result['response_time'],
                $result['status_code'],
                $result['is_online'] ? 1 : 0,
                $result['error_message'],
                $result['server_info']
            ]);
        } catch (Exception $e) {
            error_log('Failed to save check result: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Son 24 saatlik istatistikleri al
     */
    public function getLast24HoursStats() {
        if (!$this->pdo) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        try {
            $sql = "SELECT 
                        COUNT(*) as total_checks,
                        SUM(is_online) as online_checks,
                        AVG(response_time) as avg_response_time,
                        MAX(response_time) as max_response_time,
                        MIN(response_time) as min_response_time,
                        COUNT(CASE WHEN is_online = 0 THEN 1 END) as offline_checks
                    FROM uptime_monitoring 
                    WHERE check_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            
            $stmt = $this->pdo->query($sql);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Uptime yüzdesini hesapla
            $uptimePercentage = $stats['total_checks'] > 0 
                ? round(($stats['online_checks'] / $stats['total_checks']) * 100, 2)
                : 0;
            
            $stats['uptime_percentage'] = $uptimePercentage;
            
            return ['success' => true, 'stats' => $stats];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Son downtime olaylarını al
     */
    public function getRecentDowntime() {
        if (!$this->pdo) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        try {
            $sql = "SELECT * FROM uptime_monitoring 
                    WHERE is_online = 0 
                    ORDER BY check_time DESC 
                    LIMIT 10";
            
            $stmt = $this->pdo->query($sql);
            $downtime = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'downtime' => $downtime];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Email uyarısı gönder
     */
    public function sendAlert($message, $subject = 'Site Uptime Alert') {
        // Basit email gönderimi (production'da daha gelişmiş olabilir)
        $headers = "From: noreply@nextcodegroup.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $htmlMessage = "
        <html>
        <body>
            <h2>NextCode Group - Uptime Alert</h2>
            <p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>
            <p><strong>Message:</strong> {$message}</p>
            <hr>
            <p><small>This is an automated message from NextCode Group monitoring system.</small></p>
        </body>
        </html>
        ";
        
        return mail($this->alertEmail, $subject, $htmlMessage, $headers);
    }
    
    /**
     * Eski kayıtları temizle
     */
    public function cleanupOldRecords($days = 30) {
        if (!$this->pdo) {
            return false;
        }
        
        try {
            $sql = "DELETE FROM uptime_monitoring 
                    WHERE check_time < DATE_SUB(NOW(), INTERVAL ? DAY)";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$days]);
            
            return $result;
        } catch (Exception $e) {
            error_log('Failed to cleanup old records: ' . $e->getMessage());
            return false;
        }
    }
}

// CLI veya web erişimi için
if (php_sapi_name() === 'cli' || isset($_GET['check'])) {
    $monitor = new UptimeMonitor();
    
    // Monitoring tablosunu oluştur
    $monitor->createMonitoringTable();
    
    // Site durumunu kontrol et
    echo "=== Uptime Monitoring Check ===\n";
    $result = $monitor->checkSiteStatus();
    
    echo "URL: {$result['url']}\n";
    echo "Status: " . ($result['is_online'] ? 'ONLINE' : 'OFFLINE') . "\n";
    echo "Response Time: {$result['response_time']}ms\n";
    echo "HTTP Code: {$result['status_code']}\n";
    
    if ($result['error_message']) {
        echo "Error: {$result['error_message']}\n";
    }
    
    echo "\n=== 24 Hours Statistics ===\n";
    $stats = $monitor->getLast24HoursStats();
    if ($stats['success']) {
        $s = $stats['stats'];
        echo "Total Checks: {$s['total_checks']}\n";
        echo "Online Checks: {$s['online_checks']}\n";
        echo "Offline Checks: {$s['offline_checks']}\n";
        echo "Uptime: {$s['uptime_percentage']}%\n";
        echo "Avg Response Time: " . round($s['avg_response_time'], 2) . "ms\n";
    }
    
    echo "\n=== Monitoring Complete ===\n";
}
?>
