<?php
/**
 * Environment Variables Helper
 * Bu sınıf .env dosyasından değişkenleri okur ve güvenli şekilde sağlar
 */

class EnvHelper {
    private static $env = [];
    private static $loaded = false;
    
    /**
     * Environment dosyasını yükle
     */
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }
        
        $envFile = $path ?: __DIR__ . '/../.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                // Yorum satırlarını atla
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                // KEY=VALUE formatını parse et
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Tırnak işaretlerini kaldır
                    if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                        (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                        $value = substr($value, 1, -1);
                    }
                    
                    self::$env[$key] = $value;
                }
            }
        }
        
        self::$loaded = true;
    }
    
    /**
     * Environment değişkenini al
     */
    public static function get($key, $default = null) {
        self::load();
        
        // Önce environment değişkenlerini kontrol et
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        // Sonra .env dosyasından kontrol et
        return isset(self::$env[$key]) ? self::$env[$key] : $default;
    }
    
    /**
     * Database bağlantı bilgilerini al
     */
    public static function getDatabaseConfig() {
        return [
            'host' => self::get('DB_HOST', 'localhost'),
            'name' => self::get('DB_NAME', 'nextcode'),
            'user' => self::get('DB_USER', 'root'),
            'pass' => self::get('DB_PASS', ''),
            'charset' => 'utf8mb4'
        ];
    }
    
    /**
     * Güvenlik ayarlarını al
     */
    public static function getSecurityConfig() {
        return [
            'app_env' => self::get('APP_ENV', 'production'),
            'app_debug' => self::get('APP_DEBUG', 'false') === 'true',
            'app_key' => self::get('APP_KEY', ''),
            'session_lifetime' => (int)self::get('SESSION_LIFETIME', 120),
            'session_secure' => self::get('SESSION_SECURE', 'true') === 'true',
            'session_httponly' => self::get('SESSION_HTTPONLY', 'true') === 'true',
            'session_samesite' => self::get('SESSION_SAMESITE', 'Strict')
        ];
    }
    
    /**
     * Cache ayarlarını al
     */
    public static function getCacheConfig() {
        return [
            'driver' => self::get('CACHE_DRIVER', 'file'),
            'ttl' => (int)self::get('CACHE_TTL', 3600)
        ];
    }
    
    /**
     * Mail ayarlarını al
     */
    public static function getMailConfig() {
        return [
            'host' => self::get('MAIL_HOST', 'localhost'),
            'port' => (int)self::get('MAIL_PORT', 587),
            'username' => self::get('MAIL_USERNAME', ''),
            'password' => self::get('MAIL_PASSWORD', ''),
            'encryption' => self::get('MAIL_ENCRYPTION', 'tls')
        ];
    }
    
    /**
     * API ayarlarını al
     */
    public static function getApiConfig() {
        return [
            'rate_limit' => (int)self::get('API_RATE_LIMIT', 100),
            'rate_window' => (int)self::get('API_RATE_WINDOW', 60)
        ];
    }
    
    /**
     * CDN ayarlarını al
     */
    public static function getCdnConfig() {
        return [
            'url' => self::get('CDN_URL', ''),
            'enabled' => self::get('CDN_ENABLED', 'false') === 'true'
        ];
    }
    
    /**
     * Analytics ayarlarını al
     */
    public static function getAnalyticsConfig() {
        return [
            'google_analytics_id' => self::get('GOOGLE_ANALYTICS_ID', ''),
            'google_tag_manager_id' => self::get('GOOGLE_TAG_MANAGER_ID', '')
        ];
    }
    
    /**
     * Sosyal medya ayarlarını al
     */
    public static function getSocialConfig() {
        return [
            'facebook_app_id' => self::get('FACEBOOK_APP_ID', ''),
            'instagram_client_id' => self::get('INSTAGRAM_CLIENT_ID', ''),
            'linkedin_client_id' => self::get('LINKEDIN_CLIENT_ID', '')
        ];
    }
    
    /**
     * Güvenlik header ayarlarını al
     */
    public static function getSecurityHeadersConfig() {
        return [
            'enabled' => self::get('SECURITY_HEADERS_ENABLED', 'true') === 'true',
            'csp_enabled' => self::get('CSP_ENABLED', 'true') === 'true',
            'hsts_enabled' => self::get('HSTS_ENABLED', 'true') === 'true'
        ];
    }
    
    /**
     * Performans ayarlarını al
     */
    public static function getPerformanceConfig() {
        return [
            'asset_version' => self::get('ASSET_VERSION', '1.0.0'),
            'compression_enabled' => self::get('COMPRESSION_ENABLED', 'true') === 'true',
            'minification_enabled' => self::get('MINIFICATION_ENABLED', 'true') === 'true'
        ];
    }
    
    /**
     * Tüm environment değişkenlerini al
     */
    public static function all() {
        self::load();
        return self::$env;
    }
    
    /**
     * Environment değişkenini ayarla
     */
    public static function set($key, $value) {
        self::load();
        self::$env[$key] = $value;
        putenv("$key=$value");
    }
    
    /**
     * Environment değişkeninin varlığını kontrol et
     */
    public static function has($key) {
        self::load();
        return isset(self::$env[$key]) || getenv($key) !== false;
    }
}
?>


