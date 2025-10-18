<?php
// NextCode Group - Enhanced Session Security
// Advanced session management and security features

if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

class SecureSession {
    private $sessionName = 'NEXTCODE_SESS';
    private $sessionIdRegenerationInterval = 300; // 5 minutes
    private $sessionTimeout = 1800; // 30 minutes
    private $maxLoginAttempts = 5;
    private $lockoutTime = 900; // 15 minutes
    
    public function __construct($config = []) {
        // Apply configuration
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        
        // Configure session settings
        $this->configureSession();
        
        // Start secure session
        $this->startSecureSession();
    }
    
    /**
     * Configure PHP session settings for security
     */
    private function configureSession() {
        // Set session name
        session_name($this->sessionName);
        
        // Secure session cookie settings
        session_set_cookie_params([
            'lifetime' => $this->sessionTimeout,
            'path' => '/',
            'domain' => $this->getCookieDomain(),
            'secure' => $this->isHttps(),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        // Additional PHP session security settings
        ini_set('session.cookie_secure', $this->isHttps() ? 1 : 0);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.gc_maxlifetime', $this->sessionTimeout);
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 100);
        
        // Regenerate ID on each request
        ini_set('session.entropy_length', 32);
        ini_set('session.hash_bits_per_character', 5);
        ini_set('session.hash_function', 'sha256');
    }
    
    /**
     * Start secure session with additional security checks
     */
    private function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Regenerate session ID if needed
        $this->checkSessionRegeneration();
        
        // Update session activity timestamp
        $_SESSION['_last_activity'] = time();
        
        // Validate session
        $this->validateSession();
        
        // Set security headers
        $this->setSecurityHeaders();
    }
    
    /**
     * Check if session ID needs regeneration
     */
    private function checkSessionRegeneration() {
        $regenerate = false;
        
        // Always regenerate if this is a new session
        if (!isset($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
            $regenerate = true;
        }
        
        // Regenerate if interval has passed
        if (time() - $_SESSION['_last_regeneration'] > $this->sessionIdRegenerationInterval) {
            $regenerate = true;
        }
        
        // Regenerate on privilege escalation (e.g., admin login)
        if (isset($_SESSION['_privilege_change']) && $_SESSION['_privilege_change']) {
            $regenerate = true;
            unset($_SESSION['_privilege_change']);
        }
        
        if ($regenerate) {
            session_regenerate_id(true);
            $_SESSION['_last_regeneration'] = time();
        }
    }
    
    /**
     * Validate current session for security issues
     */
    private function validateSession() {
        // Check session timeout
        if (isset($_SESSION['_last_activity'])) {
            if (time() - $_SESSION['_last_activity'] > $this->sessionTimeout) {
                $this->destroySession();
                return;
            }
        }
        
        // Validate IP address (if enabled)
        if (isset($_SESSION['_ip_address'])) {
            if ($_SESSION['_ip_address'] !== $this->getClientIp()) {
                $this->destroySession();
                return;
            }
        } else {
            $_SESSION['_ip_address'] = $this->getClientIp();
        }
        
        // Validate User-Agent (if enabled)
        if (isset($_SESSION['_user_agent'])) {
            if ($_SESSION['_user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
                $this->destroySession();
                return;
            }
        } else {
            $_SESSION['_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }
        
        // Check for suspicious activity patterns
        $this->detectSuspiciousActivity();
    }
    
    /**
     * Detect suspicious session activity
     */
    private function detectSuspiciousActivity() {
        if (!isset($_SESSION['_request_count'])) {
            $_SESSION['_request_count'] = 1;
            $_SESSION['_request_start_time'] = time();
        } else {
            $_SESSION['_request_count']++;
            
            // Check for rapid requests (possible bot)
            $timeSpan = time() - $_SESSION['_request_start_time'];
            if ($timeSpan > 0) {
                $requestRate = $_SESSION['_request_count'] / $timeSpan;
                if ($requestRate > 10) { // More than 10 requests per second
                    $this->flagSuspiciousActivity('rapid_requests', [
                        'rate' => $requestRate,
                        'count' => $_SESSION['_request_count'],
                        'time_span' => $timeSpan
                    ]);
                }
            }
        }
        
        // Reset rate counting every minute
        if (time() - $_SESSION['_request_start_time'] > 60) {
            $_SESSION['_request_count'] = 1;
            $_SESSION['_request_start_time'] = time();
        }
    }
    
    /**
     * Flag suspicious activity
     */
    private function flagSuspiciousActivity($type, $details) {
        $_SESSION['_suspicious_activity'][] = [
            'type' => $type,
            'details' => $details,
            'timestamp' => time(),
            'ip' => $this->getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        
        // Log security event
        log_security_event("Suspicious activity: $type", json_encode($details));
        
        // Consider additional security measures
        if (count($_SESSION['_suspicious_activity'] ?? []) > 5) {
            $this->destroySession();
            error_log('Session terminated due to suspicious activity');
        }
        
        error_log('Session flagged for suspicious activity');
    }
    
    /**
     * Get client's real IP address
     */
    private function getClientIp() {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Get appropriate cookie domain
     */
    private function getCookieDomain() {
        $domain = $_SERVER['HTTP_HOST'] ?? '';
        if (stripos($domain, 'www.') === 0) {
            return '.' . substr($domain, 4);
        }
        return '.' . $domain;
    }
    
    /**
     * Check if HTTPS is enabled
     */
    private function isHttps() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
               $_SERVER['SERVER_PORT'] == 443 ||
               (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    }
    
    /**
     * Set additional security headers
     */
    private function setSecurityHeaders() {
        if (!headers_sent()) {
            header('X-Session-Security: enhanced');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        }
    }
    
    /**
     * Login attempt tracking and rate limiting
     */
    public function trackLoginAttempt($identifier, $success = false) {
        global $cache;
        
        $cacheKey = 'login_attempts_' . md5($identifier);
        $attempts = $cache->get($cacheKey) ?: [];
        
        $attempts[] = [
            'timestamp' => time(),
            'success' => $success,
            'ip' => $this->getClientIp()
        ];
        
        // Keep only last 24 hours of attempts
        $attempts = array_filter($attempts, function($attempt) {
            return time() - $attempt['timestamp'] < 86400;
        });
        
        $cache->set($cacheKey, $attempts, 86400);
        
        // Check if too many failed attempts
        $failedAttempts = array_filter($attempts, function($attempt) {
            return !$attempt['success'] && time() - $attempt['timestamp'] < 3600; // Last hour
        });
        
        if (count($failedAttempts) >= $this->maxLoginAttempts) {
            $this->lockAccount($identifier);
            return false;
        }
        
        return true;
    }
    
    /**
     * Lock account after too many failed attempts
     */
    private function lockAccount($identifier) {
        global $cache;
        
        $lockKey = 'account_locked_' . md5($identifier);
        $cache->set($lockKey, time() + $this->lockoutTime, $this->lockoutTime);
        
        log_security_event('Account locked', "Identifier: $identifier");
    }
    
    /**
     * Check if account is locked
     */
    public function isAccountLocked($identifier) {
        global $cache;
        
        $lockKey = 'account_locked_' . md5($identifier);
        return $cache->get($lockKey) !== false;
    }
    
    /**
     * Destroy session with cleanup
     */
    public function destroySession() {
        // Regenerate session ID one last time
        session_regenerate_id(true);
        
        // Clear session data
        $_SESSION = [];
        
        // Destroy session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
    }
    
    /**
     * End secure session
     */
    public function endSession() {
        $this->destroySession();
        
        // Clear any login attempt tracking
        $this->clearFailedAttempts($_SESSION['_user_id'] ?? 'unknown');
    }
    
    /**
     * Clear failed login attempts for user
     */
    private function clearFailedAttempts($userId) {
        global $cache;
        
        $cacheKey = 'login_attempts_' . md5($userId);
        $cache->delete($cacheKey);
    }
    
    /**
     * Set session data with automatic escaping
     */
    public function setSecure($key, $value) {
        $_SESSION[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Get session data
     */
    public function getSecure($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Remove session data
     */
    public function unsetSecure($key) {
        unset($_SESSION[$key]);
    }
    
    /**
     * Check if session has specific key
     */
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Get session information
     */
    public function getSessionInfo() {
        return [
            'session_id' => session_id(),
            'session_name' => session_name(),
            'created' => $_SESSION['_created'] ?? null,
            'last_activity' => $_SESSION['_last_activity'] ?? null,
            'ip_address' => $_SESSION['_ip_address'] ?? null,
            'request_count' => $_SESSION['_request_count'] ?? 0,
            'suspicious_activity_count' => count($_SESSION['_suspicious_activity'] ?? [])
        ];
    }
}

// Initialize secure session
$secureSession = new SecureSession();

// Helper functions
function get_session_info() {
    global $secureSession;
    return $secureSession->getSessionInfo();
}

function is_session_valid() {
    return !empty($_SESSION) && isset($_SESSION['_last_activity']);
}

function destroy_secure_session() {
    global $secureSession;
    $secureSession->destroySession();
}

function set_secure_session_data($key, $value) {
    global $secureSession;
    $secureSession->setSecure($key, $value);
}

function get_secure_session_data($key, $default = null) {
    global $secureSession;
    return $secureSession->getSecure($key, $default);
}

?>
