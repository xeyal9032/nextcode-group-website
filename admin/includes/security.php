<?php
// Admin Panel - Güvenlik Fonksiyonları
// NextCode Group - Production Security

if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

// Güvenlik headers'larını dahil et
require_once __DIR__ . '/security-headers.php';

// Audit logger'ı dahil et

/**
 * Güvenli şifre hash'leme
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Şifre doğrulama
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * CSRF Token oluşturma
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF Token doğrulama
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Input sanitization
 */
function sanitizeInput($data, $type = 'text') {
    if (is_array($data)) {
        return array_map(function($item) use ($type) {
            return sanitizeInput($item, $type);
        }, $data);
    }
    
    $data = trim($data);
    
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($data, FILTER_SANITIZE_URL);
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'html':
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        case 'text':
        default:
            return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Input validation
 */
function validateInput($data, $type, $required = true) {
    if ($required && empty($data)) {
        return false;
    }
    
    if (empty($data) && !$required) {
        return true;
    }
    
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_VALIDATE_EMAIL) !== false;
        case 'url':
            return filter_var($data, FILTER_VALIDATE_URL) !== false;
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT) !== false;
        case 'float':
            return filter_var($data, FILTER_VALIDATE_FLOAT) !== false;
        case 'alphanumeric':
            return preg_match('/^[a-zA-Z0-9]+$/', $data);
        case 'username':
            return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $data);
        case 'password':
            return strlen($data) >= 8;
        case 'text':
        default:
            return strlen($data) > 0;
    }
}

/**
 * Rate limiting
 */
function checkRateLimit($identifier, $max_attempts = 5, $time_window = 300) {
    $cache_file = sys_get_temp_dir() . '/rate_limit_' . md5($identifier);
    
    if (file_exists($cache_file)) {
        $data = json_decode(file_get_contents($cache_file), true);
        if ($data && $data['time'] > time() - $time_window) {
            if ($data['attempts'] >= $max_attempts) {
                return false;
            }
            $data['attempts']++;
        } else {
            $data = ['attempts' => 1, 'time' => time()];
        }
    } else {
        $data = ['attempts' => 1, 'time' => time()];
    }
    
    file_put_contents($cache_file, json_encode($data));
    return true;
}

/**
 * Session güvenliği
 */
function secureSession() {
    // Session güvenlik ayarları
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    // Session regeneration
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 dakika
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

/**
 * Güvenli redirect
 */
function secureRedirect($url, $status_code = 302) {
    try {
        // URL validation
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            header("Location: $url", true, $status_code);
            exit;
        } elseif (strpos($url, '/') === 0) {
            // Relative URL
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $url = $protocol . '://' . $host . $url;
            header("Location: $url", true, $status_code);
            exit;
        } else {
            // Simple relative URL
            header("Location: $url");
            exit;
        }
    } catch (Exception $e) {
        // Redirect hatası durumunda basit redirect
        header("Location: $url");
        exit;
    }
}

/**
 * Güvenlik logları
 */
function logSecurityEvent($event, $details = '', $level = 'INFO') {
    try {
        $log_entry = date('Y-m-d H:i:s') . ' [' . $level . '] ' . $event;
        if ($details) {
            $log_entry .= ' - ' . $details;
        }
        $log_entry .= ' - IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $log_entry .= ' - User Agent: ' . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
        $log_entry .= PHP_EOL;
        
        $log_file = __DIR__ . '/../../logs/admin_security.log';
        $log_dir = dirname($log_file);
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    } catch (Exception $e) {
        // Log yazma hatası durumunda sessizce devam et
        error_log("Security log error: " . $e->getMessage());
    }
}

/**
 * Admin yetki kontrolü
 */
function checkAdminPermission($required_role = 'admin') {
    try {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            logSecurityEvent('UNAUTHORIZED_ACCESS', 'Admin panel without login', 'WARNING');
            secureRedirect('login.php');
            exit;
        }
        
        if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] !== $required_role) {
            logSecurityEvent('INSUFFICIENT_PERMISSIONS', 'Role: ' . ($_SESSION['admin_role'] ?? 'none'), 'WARNING');
            return false;
        }
        
        return true;
    } catch (Exception $e) {
        // Permission check hatası durumunda basit kontrol
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header("Location: login.php");
            exit;
        }
        return true;
    }
}

/**
 * Güvenli veritabanı bağlantısı
 */
function getSecureDatabaseConnection() {
    try {
        require_once __DIR__ . '/../../config/database.php';
        $db = new Database();
        $pdo = $db->getConnection();
        
        if (!$pdo) {
            logSecurityEvent('DATABASE_CONNECTION_FAILED', 'PDO connection failed', 'ERROR');
            return null;
        }
        
        return $pdo;
    } catch (Exception $e) {
        logSecurityEvent('DATABASE_ERROR', $e->getMessage(), 'ERROR');
        return null;
    }
}

// Audit Logging Fonksiyonu
function logAuditEvent($category, $action, $details, $level = 'INFO', $user_id = null) {
    global $pdo;
    
    if (!$pdo) {
        return false;
    }
    
    // Kullanıcı ID'sini al
    if ($user_id === null) {
        $user_id = $_SESSION['admin_user_id'] ?? null;
    }
    
    // IP adresini al
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    // Audit log tablosunu kontrol et ve oluştur
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'admin_audit_logs'");
        if ($stmt->rowCount() == 0) {
            $create_table = "
                CREATE TABLE admin_audit_logs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT,
                    category VARCHAR(50) NOT NULL,
                    action VARCHAR(100) NOT NULL,
                    details TEXT,
                    level VARCHAR(20) DEFAULT 'INFO',
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_user_id (user_id),
                    INDEX idx_category (category),
                    INDEX idx_action (action),
                    INDEX idx_level (level),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            $pdo->exec($create_table);
        }
    } catch (Exception $e) {
        error_log("Audit log table creation failed: " . $e->getMessage());
        return false;
    }
    
    // Audit log kaydını ekle
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_audit_logs 
            (user_id, category, action, details, level, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $user_id,
            $category,
            $action,
            $details,
            $level,
            $ip_address,
            $user_agent
        ]);
        
        // Log dosyasına da yaz
        $log_entry = date('Y-m-d H:i:s') . " | $level | $category | $action | $details | IP: $ip_address | User: $user_id\n";
        file_put_contents('logs/admin_audit.log', $log_entry, FILE_APPEND | LOCK_EX);
        
        return true;
    } catch (Exception $e) {
        error_log("Audit logging failed: " . $e->getMessage());
        return false;
    }
}

// Session güvenliğini başlat (gelişmiş güvenli ayarlar ile)
// Admin paneli için ayrı session namespace kullan
if (session_status() === PHP_SESSION_ACTIVE) {
    // Session zaten aktif, admin paneli için özel kontroller
    if (!isset($_SESSION['admin_session_started'])) {
        $_SESSION['admin_session_started'] = true;
        $_SESSION['session_created'] = time();
    }
} else {
    // Session başlat
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1); // Sadece HTTPS üzerinde çalışır
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', 3600); // 1 saat
    ini_set('session.cookie_lifetime', 3600); // 1 saat
    
    session_start();
    $_SESSION['admin_session_started'] = true;
}

// Admin paneli güvenlik kontrolleri
if (isset($_SESSION['admin_session_started'])) {
    secureSession();
    
    // Session ID yenileme
    if (!isset($_SESSION['session_created'])) {
        $_SESSION['session_created'] = time();
    }
    
    // Session timeout kontrolü (30 dakika inaktivite) - sadece admin paneli için
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        // Admin paneli session'ını temizle
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['admin_user_id']);
        logSecurityEvent('SESSION_TIMEOUT', 'Admin session expired due to inactivity', 'INFO');
        secureRedirect('login.php?timeout=1');
    }
    
    $_SESSION['last_activity'] = time();
    
    // Session ID düzenli yenileme (güvenlik için)
    if (isset($_SESSION['session_created']) && (time() - $_SESSION['session_created'] > 1800)) {
        session_regenerate_id(true);
        $_SESSION['session_created'] = time();
        logSecurityEvent('SESSION_REGENERATED', 'Admin session ID regenerated for security', 'INFO');
    }
}

// Admin paneli için özel güvenlik headers'larını ayarla
if (!headers_sent()) {
    setAdminSecurityHeaders();
}
?>
