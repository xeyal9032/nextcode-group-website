<?php
/**
 * Enhanced CSRF Protection
 * Güçlendirilmiş CSRF token sistemi
 */

class EnhancedCSRFProtection {
    private $tokenName = 'csrf_token';
    private $tokenLength = 32;
    private $sessionKey = 'csrf_tokens';
    private $maxTokens = 10;
    private $tokenLifetime = 3600; // 1 saat
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Token storage'ı başlat
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }
        
        // Eski tokenları temizle
        $this->cleanExpiredTokens();
    }
    
    /**
     * Yeni CSRF token oluştur
     */
    public function generateToken($purpose = 'default') {
        $token = bin2hex(random_bytes($this->tokenLength));
        $timestamp = time();
        
        $tokenData = [
            'token' => $token,
            'purpose' => $purpose,
            'created_at' => $timestamp,
            'expires_at' => $timestamp + $this->tokenLifetime,
            'used' => false,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        
        // Token'ı session'a kaydet
        $_SESSION[$this->sessionKey][$token] = $tokenData;
        
        // Maksimum token sayısını kontrol et
        $this->enforceMaxTokens();
        
        return $token;
    }
    
    /**
     * CSRF token'ı doğrula
     */
    public function validateToken($token, $purpose = 'default') {
        if (empty($token)) {
            return false;
        }
        
        // Token session'da var mı?
        if (!isset($_SESSION[$this->sessionKey][$token])) {
            return false;
        }
        
        $tokenData = $_SESSION[$this->sessionKey][$token];
        
        // Token süresi dolmuş mu?
        if ($tokenData['expires_at'] < time()) {
            unset($_SESSION[$this->sessionKey][$token]);
            return false;
        }
        
        // Token daha önce kullanılmış mı?
        if ($tokenData['used']) {
            return false;
        }
        
        // Purpose eşleşiyor mu?
        if ($tokenData['purpose'] !== $purpose) {
            return false;
        }
        
        // IP adresi kontrolü (opsiyonel)
        $currentIP = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!empty($tokenData['ip']) && $tokenData['ip'] !== $currentIP) {
            // IP değişmişse token'ı geçersiz kıl
            unset($_SESSION[$this->sessionKey][$token]);
            return false;
        }
        
        // Token'ı kullanıldı olarak işaretle
        $_SESSION[$this->sessionKey][$token]['used'] = true;
        
        return true;
    }
    
    /**
     * Token'ı kullanıldı olarak işaretle
     */
    public function markTokenAsUsed($token) {
        if (isset($_SESSION[$this->sessionKey][$token])) {
            $_SESSION[$this->sessionKey][$token]['used'] = true;
        }
    }
    
    /**
     * Token'ı sil
     */
    public function revokeToken($token) {
        if (isset($_SESSION[$this->sessionKey][$token])) {
            unset($_SESSION[$this->sessionKey][$token]);
        }
    }
    
    /**
     * Tüm tokenları temizle
     */
    public function clearAllTokens() {
        $_SESSION[$this->sessionKey] = [];
    }
    
    /**
     * Süresi dolmuş tokenları temizle
     */
    private function cleanExpiredTokens() {
        $currentTime = time();
        
        foreach ($_SESSION[$this->sessionKey] as $token => $tokenData) {
            if ($tokenData['expires_at'] < $currentTime) {
                unset($_SESSION[$this->sessionKey][$token]);
            }
        }
    }
    
    /**
     * Maksimum token sayısını kontrol et
     */
    private function enforceMaxTokens() {
        if (count($_SESSION[$this->sessionKey]) > $this->maxTokens) {
            // En eski tokenları sil
            $tokens = $_SESSION[$this->sessionKey];
            uasort($tokens, function($a, $b) {
                return $a['created_at'] - $b['created_at'];
            });
            
            $tokensToRemove = count($tokens) - $this->maxTokens;
            $removed = 0;
            
            foreach ($tokens as $token => $tokenData) {
                if ($removed >= $tokensToRemove) {
                    break;
                }
                unset($_SESSION[$this->sessionKey][$token]);
                $removed++;
            }
        }
    }
    
    /**
     * Hidden input field oluştur
     */
    public function generateHiddenInput($purpose = 'default') {
        $token = $this->generateToken($purpose);
        return '<input type="hidden" name="' . $this->tokenName . '" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Meta tag oluştur
     */
    public function generateMetaTag($purpose = 'default') {
        $token = $this->generateToken($purpose);
        return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * JavaScript için token al
     */
    public function getTokenForJS($purpose = 'default') {
        $token = $this->generateToken($purpose);
        return $token;
    }
    
    /**
     * AJAX istekleri için header oluştur
     */
    public function generateAjaxHeader($purpose = 'default') {
        $token = $this->generateToken($purpose);
        return 'X-CSRF-Token: ' . $token;
    }
    
    /**
     * Form için CSRF koruması ekle
     */
    public function protectForm($formHTML, $purpose = 'default') {
        $tokenInput = $this->generateHiddenInput($purpose);
        
        // Form tag'ini bul ve token input'unu ekle
        if (preg_match('/<form[^>]*>/i', $formHTML, $matches)) {
            $formTag = $matches[0];
            $protectedForm = str_replace($formTag, $formTag . $tokenInput, $formHTML);
            return $protectedForm;
        }
        
        return $formHTML . $tokenInput;
    }
    
    /**
     * POST isteğini kontrol et
     */
    public function validatePostRequest($purpose = 'default') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return true; // POST değilse kontrol etme
        }
        
        $token = $_POST[$this->tokenName] ?? '';
        return $this->validateToken($token, $purpose);
    }
    
    /**
     * AJAX isteğini kontrol et
     */
    public function validateAjaxRequest($purpose = 'default') {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            return true; // AJAX değilse kontrol etme
        }
        
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return $this->validateToken($token, $purpose);
    }
    
    /**
     * CSRF koruması için middleware
     */
    public function middleware($purpose = 'default') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validatePostRequest($purpose)) {
                http_response_code(403);
                die('CSRF token validation failed');
            }
        }
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            if (!$this->validateAjaxRequest($purpose)) {
                http_response_code(403);
                die('CSRF token validation failed');
            }
        }
    }
    
    /**
     * Token istatistikleri
     */
    public function getTokenStats() {
        $tokens = $_SESSION[$this->sessionKey] ?? [];
        $stats = [
            'total' => count($tokens),
            'used' => 0,
            'unused' => 0,
            'expired' => 0
        ];
        
        $currentTime = time();
        
        foreach ($tokens as $tokenData) {
            if ($tokenData['expires_at'] < $currentTime) {
                $stats['expired']++;
            } elseif ($tokenData['used']) {
                $stats['used']++;
            } else {
                $stats['unused']++;
            }
        }
        
        return $stats;
    }
    
    /**
     * CSRF koruması için JavaScript kodu
     */
    public function generateJavaScript() {
        return '
        // CSRF Token Management
        class CSRFManager {
            constructor() {
                this.token = this.getTokenFromMeta();
                this.setupAjaxProtection();
            }
            
            getTokenFromMeta() {
                const meta = document.querySelector(\'meta[name="csrf-token"]\');
                return meta ? meta.getAttribute(\'content\') : null;
            }
            
            setupAjaxProtection() {
                // XMLHttpRequest için interceptor
                const originalOpen = XMLHttpRequest.prototype.open;
                const originalSend = XMLHttpRequest.prototype.send;
                
                XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
                    this._method = method;
                    this._url = url;
                    return originalOpen.apply(this, arguments);
                };
                
                XMLHttpRequest.prototype.send = function(data) {
                    if (this._method && [\'POST\', \'PUT\', \'DELETE\', \'PATCH\'].includes(this._method.toUpperCase())) {
                        this.setRequestHeader(\'X-CSRF-Token\', CSRFManager.token);
                    }
                    return originalSend.apply(this, arguments);
                };
                
                // Fetch API için interceptor
                const originalFetch = window.fetch;
                window.fetch = function(url, options = {}) {
                    if (options.method && [\'POST\', \'PUT\', \'DELETE\', \'PATCH\'].includes(options.method.toUpperCase())) {
                        options.headers = options.headers || {};
                        options.headers[\'X-CSRF-Token\'] = CSRFManager.token;
                    }
                    return originalFetch(url, options);
                };
            }
            
            refreshToken() {
                fetch(\'/api/csrf-refresh\', {
                    method: \'POST\',
                    headers: {
                        \'X-CSRF-Token\': this.token,
                        \'Content-Type\': \'application/json\'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.token) {
                        this.token = data.token;
                        const meta = document.querySelector(\'meta[name="csrf-token"]\');
                        if (meta) {
                            meta.setAttribute(\'content\', data.token);
                        }
                    }
                })
                .catch(error => {
                    console.error(\'CSRF token refresh failed:\', error);
                });
            }
        }
        
        // CSRF Manager\'ı başlat
        const csrfManager = new CSRFManager();
        ';
    }
}

// Kullanım örneği
if (php_sapi_name() === 'cli') {
    $csrf = new EnhancedCSRFProtection();
    
    echo "Enhanced CSRF Protection Created!\n";
    
    // Token oluştur
    $token = $csrf->generateToken('form');
    echo "Generated Token: " . substr($token, 0, 10) . "...\n";
    
    // Token doğrula
    $valid = $csrf->validateToken($token, 'form');
    echo "Token Valid: " . ($valid ? 'Yes' : 'No') . "\n";
    
    // İstatistikler
    $stats = $csrf->getTokenStats();
    echo "Token Stats: " . json_encode($stats) . "\n";
}
?>


