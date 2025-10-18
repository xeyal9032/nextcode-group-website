<?php
/**
 * Site Content Helper Functions
 * Bu dosya site içeriklerini dinamik olarak yüklemek için kullanılır
 */

// Include API router for handling API requests
require_once __DIR__ . '/../api-router.php';

require_once __DIR__ . '/../config/database.php';

class ContentHelper {
    private $pdo;
    private static $cache = [];
    
    public function __construct() {
        try {
            $database = new Database();
            $this->pdo = $database->getConnection();
        } catch (Exception $e) {
            error_log('ContentHelper database connection error: ' . $e->getMessage());
            $this->pdo = null;
        }
    }
    
    /**
     * İçerik anahtarına göre değer getir
     */
    public function getContent($key, $default = '') {
        // Cache'den kontrol et
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        
        if (!$this->pdo) {
            return $default;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT content_value FROM site_content WHERE content_key = ? AND is_active = 1");
            $stmt->execute([$key]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $value = $result ? $result['content_value'] : $default;
            
            // Cache'e kaydet
            self::$cache[$key] = $value;
            
            return $value;
        } catch (Exception $e) {
            error_log('ContentHelper getContent error: ' . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * Sayfa bölümüne göre içerikleri getir
     */
    public function getSectionContent($section) {
        if (!$this->pdo) {
            return [];
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT content_key, content_value, content_type FROM site_content WHERE section_name = ? AND is_active = 1 ORDER BY id ASC");
            $stmt->execute([$section]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ContentHelper getSectionContent error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * HTML içeriği güvenli şekilde çıktıla
     * Allows safe HTML tags but removes dangerous attributes
     */
    public function getHtmlContent($key, $default = '') {
        $content = $this->getContent($key, $default);
        
        // Allowed HTML tags for content
        $allowed_tags = '<p><a><strong><em><ul><ol><li><br><h1><h2><h3><h4><h5><h6><img><span><div><b><i><u>';
        
        // Strip dangerous tags
        $content = strip_tags($content, $allowed_tags);
        
        // Remove dangerous attributes (onclick, onerror, onload, etc.)
        $content = preg_replace('/<([a-z][a-z0-9]*)[^>]*?(on\w+\s*=)[^>]*?(\/?)>/i', '<$1$3>', $content);
        
        // Remove javascript: protocol from links
        $content = preg_replace('/(<a[^>]+href\s*=\s*["\'])javascript:/i', '$1#', $content);
        
        return $content;
    }
    
    /**
     * Metin içeriği güvenli şekilde çıktıla
     */
    public function getTextContent($key, $default = '') {
        $content = $this->getContent($key, $default);
        return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Link içeriği güvenli şekilde çıktıla
     */
    public function getLinkContent($key, $default = '#') {
        $content = $this->getContent($key, $default);
        return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Resim URL'si güvenli şekilde çıktıla
     */
    public function getImageContent($key, $default = '') {
        $content = $this->getContent($key, $default);
        return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * JSON içeriği parse et
     */
    public function getJsonContent($key, $default = []) {
        $content = $this->getContent($key, '');
        if (empty($content)) {
            return $default;
        }
        
        $decoded = json_decode($content, true);
        return $decoded !== null ? $decoded : $default;
    }
    
    /**
     * Cache'i temizle
     */
    public static function clearCache() {
        self::$cache = [];
    }
    
    /**
     * Tüm aktif içerikleri getir
     */
    public function getAllContent() {
        if (!$this->pdo) {
            return [];
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM site_content WHERE is_active = 1 ORDER BY page_section, sort_order ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ContentHelper getAllContent error: ' . $e->getMessage());
            return [];
        }
    }
}

// Global helper fonksiyonlar
function getContent($key, $default = '') {
    try {
        static $helper = null;
        if ($helper === null) {
            $helper = new ContentHelper();
        }
        return $helper->getContent($key, $default);
    } catch (Exception $e) {
        error_log('getContent error: ' . $e->getMessage());
        return $default;
    }
}

function getTextContent($key, $default = '') {
    try {
        static $helper = null;
        if ($helper === null) {
            $helper = new ContentHelper();
        }
        return $helper->getTextContent($key, $default);
    } catch (Exception $e) {
        error_log('getTextContent error: ' . $e->getMessage());
        return $default;
    }
}

function getHtmlContent($key, $default = '') {
    try {
        static $helper = null;
        if ($helper === null) {
            $helper = new ContentHelper();
        }
        return $helper->getHtmlContent($key, $default);
    } catch (Exception $e) {
        error_log('getHtmlContent error: ' . $e->getMessage());
        return $default;
    }
}

function getLinkContent($key, $default = '#') {
    try {
        static $helper = null;
        if ($helper === null) {
            $helper = new ContentHelper();
        }
        return $helper->getLinkContent($key, $default);
    } catch (Exception $e) {
        error_log('getLinkContent error: ' . $e->getMessage());
        return $default;
    }
}

function getImageContent($key, $default = '') {
    try {
        static $helper = null;
        if ($helper === null) {
            $helper = new ContentHelper();
        }
        return $helper->getImageContent($key, $default);
    } catch (Exception $e) {
        error_log('getImageContent error: ' . $e->getMessage());
        return $default;
    }
}

function getSectionContent($section) {
    static $helper = null;
    if ($helper === null) {
        $helper = new ContentHelper();
    }
    return $helper->getSectionContent($section);
}
?>