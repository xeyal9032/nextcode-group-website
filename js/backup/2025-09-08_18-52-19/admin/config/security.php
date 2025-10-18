<?php
/**
 * Admin Panel Security Configuration
 * NextCode Group - Advanced Security System
 */

if (!defined('ADMIN_ACCESS')) {
    die('Direct access forbidden');
}

class AdminSecurity {
    private static $instance = null;
    private $config;
    
    private function __construct() {
        $this->config = [
            'session_timeout' => 3600, // 1 hour
            'max_login_attempts' => 5,
            'lockout_duration' => 900, // 15 minutes
            'csrf_token_lifetime' => 3600,
            'password_min_length' => 12,
            'require_2fa' => false,
            'allowed_file_types' => ['php', 'html', 'css', 'js', 'json', 'txt', 'md', 'sql'],
            'max_file_size' => 10 * 1024 * 1024, // 10MB
            'admin_ip_whitelist' => [], // Empty = allow all
        ];
        
        $this->initSecurity();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function initSecurity() {
        // Set security headers
        $this->setSecurityHeaders();
        
        // Configure session
        $this->configureSession();
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    private function setSecurityHeaders() {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        
        // Content Security Policy for admin panel
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self';";
        
        header("Content-Security-Policy: $csp");
    }
    
    private function configureSession() {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', 1);
        ini_set('session.gc_maxlifetime', $this->config['session_timeout']);
        session_name('ADMIN_SESSION_' . substr(md5(__FILE__), 0, 8));
    }
    
    public function checkAuth() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity']) > $this->config['session_timeout']) {
            $this->logout();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['last_regeneration']) || 
            (time() - $_SESSION['last_regeneration']) > 300) { // 5 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
        
        return true;
    }
    
    public function login($username, $password, $remember = false) {
        global $pdo;
        
        if (!$pdo) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        try {
            // Check rate limiting
            if (!$this->checkRateLimit($username)) {
                return ['success' => false, 'message' => 'Too many login attempts. Please try again later.'];
            }
            
            // Get user
            $stmt = $pdo->prepare("
                SELECT id, username, password_hash, full_name, role, is_active, 
                       login_attempts, locked_until 
                FROM admin_users 
                WHERE username = ? AND is_active = 1
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $this->logFailedAttempt($username);
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
            // Check if account is locked
            if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                return ['success' => false, 'message' => 'Account is temporarily locked'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                $this->logFailedAttempt($username, $user['id']);
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
            // Successful login
            $this->createSession($user);
            $this->resetLoginAttempts($user['id']);
            $this->logActivity($user['id'], 'login', 'Admin panel login');
            
            return ['success' => true, 'user' => $user];
            
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed'];
        }
    }
    
    private function createSession($user) {
        session_regenerate_id(true);
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_full_name'] = $user['full_name'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['last_regeneration'] = time();
        $_SESSION['csrf_token'] = $this->generateCSRFToken();
        
        // Store session in database
        $this->storeSession($user['id']);
    }
    
    private function storeSession($user_id) {
        global $pdo;
        
        if (!$pdo) return;
        
        try {
            $session_id = session_id();
            $expires_at = date('Y-m-d H:i:s', time() + $this->config['session_timeout']);
            
            $stmt = $pdo->prepare("
                INSERT INTO admin_sessions (id, user_id, ip_address, user_agent, expires_at)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                last_activity = CURRENT_TIMESTAMP,
                expires_at = ?
            ");
            
            $stmt->execute([
                $session_id,
                $user_id,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $expires_at,
                $expires_at
            ]);
        } catch (PDOException $e) {
            error_log('Session storage error: ' . $e->getMessage());
        }
    }
    
    public function logout() {
        global $pdo;
        
        // Log activity
        if (isset($_SESSION['admin_user_id'])) {
            $this->logActivity($_SESSION['admin_user_id'], 'logout', 'Admin panel logout');
            
            // Remove session from database
            if ($pdo) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM admin_sessions WHERE id = ?");
                    $stmt->execute([session_id()]);
                } catch (PDOException $e) {
                    error_log('Session deletion error: ' . $e->getMessage());
                }
            }
        }
        
        // Clear session
        session_unset();
        session_destroy();
        
        // Start new session
        session_start();
        session_regenerate_id(true);
    }
    
    public function generateCSRFToken() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        return $token;
    }
    
    public function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        // Check token lifetime
        if ((time() - $_SESSION['csrf_token_time']) > $this->config['csrf_token_lifetime']) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    private function checkRateLimit($username) {
        $cache_file = sys_get_temp_dir() . '/admin_rate_limit_' . md5($username . $_SERVER['REMOTE_ADDR']);
        
        if (file_exists($cache_file)) {
            $data = json_decode(file_get_contents($cache_file), true);
            if ($data && $data['time'] > time() - $this->config['lockout_duration']) {
                if ($data['attempts'] >= $this->config['max_login_attempts']) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    private function logFailedAttempt($username, $user_id = null) {
        global $pdo;
        
        // Update rate limiting
        $cache_file = sys_get_temp_dir() . '/admin_rate_limit_' . md5($username . $_SERVER['REMOTE_ADDR']);
        $data = ['attempts' => 1, 'time' => time()];
        
        if (file_exists($cache_file)) {
            $existing = json_decode(file_get_contents($cache_file), true);
            if ($existing && $existing['time'] > time() - $this->config['lockout_duration']) {
                $data['attempts'] = $existing['attempts'] + 1;
            }
        }
        
        file_put_contents($cache_file, json_encode($data));
        
        // Update database if user exists
        if ($user_id && $pdo) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE admin_users 
                    SET login_attempts = login_attempts + 1,
                        locked_until = CASE 
                            WHEN login_attempts + 1 >= ? THEN DATE_ADD(NOW(), INTERVAL ? SECOND)
                            ELSE locked_until 
                        END
                    WHERE id = ?
                ");
                $stmt->execute([$this->config['max_login_attempts'], $this->config['lockout_duration'], $user_id]);
            } catch (PDOException $e) {
                error_log('Failed attempt logging error: ' . $e->getMessage());
            }
        }
        
        // Log security event
        $this->logActivity(null, 'failed_login', "Failed login attempt for: $username");
    }
    
    private function resetLoginAttempts($user_id) {
        global $pdo;
        
        if (!$pdo) return;
        
        try {
            $stmt = $pdo->prepare("
                UPDATE admin_users 
                SET login_attempts = 0, locked_until = NULL, last_login = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            error_log('Reset login attempts error: ' . $e->getMessage());
        }
    }
    
    public function logActivity($user_id, $action, $details = '', $resource = '') {
        global $pdo;
        
        if (!$pdo) return;
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO admin_activity_log (user_id, action, resource, details, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $user_id,
                $action,
                $resource,
                json_encode(['message' => $details, 'timestamp' => time()]),
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (PDOException $e) {
            error_log('Activity logging error: ' . $e->getMessage());
        }
    }
    
    public function hasPermission($action, $resource = '') {
        if (!isset($_SESSION['admin_role'])) {
            return false;
        }
        
        $role = $_SESSION['admin_role'];
        
        // Super admin has all permissions
        if ($role === 'super_admin') {
            return true;
        }
        
        // Define role permissions
        $permissions = [
            'admin' => [
                'file_read', 'file_write', 'file_delete', 'file_upload',
                'database_read', 'database_write', 'settings_read', 'settings_write'
            ],
            'editor' => [
                'file_read', 'file_write', 'database_read'
            ]
        ];
        
        return isset($permissions[$role]) && in_array($action, $permissions[$role]);
    }
    
    public function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    public function validateFileUpload($file) {
        $errors = [];
        
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error';
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > $this->config['max_file_size']) {
            $errors[] = 'File too large';
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->config['allowed_file_types'])) {
            $errors[] = 'File type not allowed';
        }
        
        return $errors;
    }
    
    public function cleanupExpiredSessions() {
        global $pdo;
        
        if (!$pdo) return;
        
        try {
            $stmt = $pdo->prepare("DELETE FROM admin_sessions WHERE expires_at < NOW()");
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Session cleanup error: ' . $e->getMessage());
        }
    }
}

// Initialize security
$adminSecurity = AdminSecurity::getInstance();
?>
