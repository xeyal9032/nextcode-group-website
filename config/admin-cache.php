<?php
// Admin Panel Caching System - NextCode Group
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class AdminCache {
    private $cache_dir;
    private $cache_time;
    
    public function __construct($cache_time = 300) { // 5 minutes default
        $this->cache_dir = __DIR__ . '/../cache/admin/';
        $this->cache_time = $cache_time;
        
        // Create cache directory if it doesn't exist
        if (!file_exists($this->cache_dir)) {
            mkdir($this->cache_dir, 0755, true);
        }
    }
    
    /**
     * Get cached data
     */
    public function get($key) {
        $cache_file = $this->cache_dir . md5($key) . '.cache';
        
        if (file_exists($cache_file)) {
            $cache_data = file_get_contents($cache_file);
            $cache = unserialize($cache_data);
            
            // Check if cache is still valid
            if (time() - $cache['timestamp'] < $this->cache_time) {
                return $cache['data'];
            } else {
                // Cache expired, remove file
                unlink($cache_file);
            }
        }
        
        return false;
    }
    
    /**
     * Set cache data
     */
    public function set($key, $data) {
        $cache_file = $this->cache_dir . md5($key) . '.cache';
        
        $cache = [
            'timestamp' => time(),
            'data' => $data
        ];
        
        file_put_contents($cache_file, serialize($cache));
    }
    
    /**
     * Clear specific cache
     */
    public function clear($key) {
        $cache_file = $this->cache_dir . md5($key) . '.cache';
        
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
    }
    
    /**
     * Clear all cache
     */
    public function clearAll() {
        $files = glob($this->cache_dir . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }
    
    /**
     * Get cache statistics
     */
    public function getStats() {
        $files = glob($this->cache_dir . '*.cache');
        $total_size = 0;
        $expired_count = 0;
        $valid_count = 0;
        
        foreach ($files as $file) {
            $total_size += filesize($file);
            
            $cache_data = file_get_contents($file);
            $cache = unserialize($cache_data);
            
            if (time() - $cache['timestamp'] < $this->cache_time) {
                $valid_count++;
            } else {
                $expired_count++;
            }
        }
        
        return [
            'total_files' => count($files),
            'valid_files' => $valid_count,
            'expired_files' => $expired_count,
            'total_size' => $total_size,
            'cache_time' => $this->cache_time
        ];
    }
}

// Cached functions for admin panel
class AdminCachedData {
    private static $cache;
    
    public static function init() {
        if (!self::$cache) {
            self::$cache = new AdminCache();
        }
    }
    
    /**
     * Get cached dashboard stats
     */
    public static function getDashboardStats() {
        self::init();
        
        $cache_key = 'dashboard_stats';
        $cached_data = self::$cache->get($cache_key);
        
        if ($cached_data === false) {
            try {
                require_once __DIR__ . '/database.php';
                $db = new Database();
                $pdo = $db->getConnection();
                
                if ($pdo) {
                    $stats = [
                        'total_projects' => $pdo->query("SELECT COUNT(*) FROM portfolio_projects")->fetchColumn(),
                        'total_posts' => $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn(),
                        'total_messages' => $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn(),
                        'total_users' => $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn(),
                        'unread_messages' => $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'")->fetchColumn(),
                        'recent_posts' => $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn()
                    ];
                    
                    self::$cache->set($cache_key, $stats);
                    return $stats;
                }
            } catch (Exception $e) {
                error_log("Dashboard stats error: " . $e->getMessage());
            }
            
            return [
                'total_projects' => 0,
                'total_posts' => 0,
                'total_messages' => 0,
                'total_users' => 0,
                'unread_messages' => 0,
                'recent_posts' => 0
            ];
        }
        
        return $cached_data;
    }
    
    /**
     * Get cached blog categories
     */
    public static function getBlogCategories() {
        self::init();
        
        $cache_key = 'blog_categories';
        $cached_data = self::$cache->get($cache_key);
        
        if ($cached_data === false) {
            try {
                require_once __DIR__ . '/database.php';
                $db = new Database();
                $pdo = $db->getConnection();
                
                if ($pdo) {
                    $stmt = $pdo->query("SELECT id, name, description FROM blog_categories ORDER BY name");
                    $categories = $stmt->fetchAll();
                    
                    self::$cache->set($cache_key, $categories);
                    return $categories;
                }
            } catch (Exception $e) {
                error_log("Blog categories error: " . $e->getMessage());
            }
            
            return [];
        }
        
        return $cached_data;
    }
    
    /**
     * Get cached site content
     */
    public static function getSiteContent($page_name = null) {
        self::init();
        
        $cache_key = 'site_content_' . ($page_name ?: 'all');
        $cached_data = self::$cache->get($cache_key);
        
        if ($cached_data === false) {
            try {
                require_once __DIR__ . '/database.php';
                $db = new Database();
                $pdo = $db->getConnection();
                
                if ($pdo) {
                    if ($page_name) {
                        $stmt = $pdo->prepare("SELECT id, page_name, section_name, content_key, content_value, content_type, created_at, updated_at FROM site_content WHERE page_name = ? ORDER BY section_name, content_key");
                        $stmt->execute([$page_name]);
                    } else {
                        $stmt = $pdo->query("SELECT id, page_name, section_name, content_key, content_value, content_type, created_at, updated_at FROM site_content ORDER BY page_name, section_name, content_key");
                    }
                    
                    $content = $stmt->fetchAll();
                    
                    self::$cache->set($cache_key, $content);
                    return $content;
                }
            } catch (Exception $e) {
                error_log("Site content error: " . $e->getMessage());
            }
            
            return [];
        }
        
        return $cached_data;
    }
    
    /**
     * Clear cache when content is updated
     */
    public static function clearContentCache() {
        self::init();
        
        self::$cache->clear('dashboard_stats');
        self::$cache->clear('blog_categories');
        self::$cache->clear('site_content_all');
        
        // Clear page-specific content cache
        $pages = ['home', 'about', 'services', 'portfolio', 'blog', 'contact', 'pricing', 'faq'];
        foreach ($pages as $page) {
            self::$cache->clear('site_content_' . $page);
        }
        
        // Log cache clear action
        error_log("Admin cache cleared: Content cache");
    }
    
    /**
     * Clear cache when blog content is updated
     */
    public static function clearBlogCache() {
        self::init();
        
        self::$cache->clear('dashboard_stats');
        self::$cache->clear('blog_categories');
        
        // Log cache clear action
        error_log("Admin cache cleared: Blog cache");
    }
    
    /**
     * Clear cache when portfolio is updated
     */
    public static function clearPortfolioCache() {
        self::init();
        
        self::$cache->clear('dashboard_stats');
        
        // Log cache clear action
        error_log("Admin cache cleared: Portfolio cache");
    }
    
    /**
     * Clear cache when messages are updated
     */
    public static function clearMessageCache() {
        self::init();
        
        self::$cache->clear('dashboard_stats');
        
        // Log cache clear action
        error_log("Admin cache cleared: Message cache");
    }
    
    /**
     * Clear all cache
     */
    public static function clearAllCache() {
        self::init();
        
        try {
            // Clear all cache files in admin directory
            $cache_dir = __DIR__ . '/../cache/admin/';
            if (is_dir($cache_dir)) {
                $files = glob($cache_dir . '*.cache');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
            
            // Clear all cache files in main cache directory
            $main_cache_dir = __DIR__ . '/../cache/';
            $cache_types = ['pages', 'api', 'assets', 'database'];
            foreach ($cache_types as $type) {
                $type_dir = $main_cache_dir . $type . '/';
                if (is_dir($type_dir)) {
                    $files = glob($type_dir . '*');
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                }
            }
            
            // Log cache clear action
            error_log("Admin cache cleared: All cache");
            
            return true;
        } catch (Exception $e) {
            error_log("Cache clear error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Auto-clean expired cache files
     */
    public static function autoCleanExpiredCache() {
        self::init();
        
        $cache_dir = __DIR__ . '/../cache/admin/';
        $files = glob($cache_dir . '*.cache');
        $cleaned = 0;
        
        foreach ($files as $file) {
            $cache_data = file_get_contents($file);
            $cache = unserialize($cache_data);
            
            if (time() - $cache['timestamp'] >= self::$cache->cache_time) {
                if (unlink($file)) {
                    $cleaned++;
                }
            }
        }
        
        if ($cleaned > 0) {
            error_log("Admin cache auto-clean: {$cleaned} expired files removed");
        }
        
        return $cleaned;
    }
    
    /**
     * Get cache statistics
     */
    public static function getCacheStats() {
        self::init();
        
        $cache_dir = __DIR__ . '/../cache/admin/';
        $files = glob($cache_dir . '*.cache');
        $total_files = count($files);
        $valid_files = 0;
        $expired_files = 0;
        $total_size = 0;
        
        foreach ($files as $file) {
            $total_size += filesize($file);
            
            $cache_data = file_get_contents($file);
            $cache = unserialize($cache_data);
            
            if (time() - $cache['timestamp'] < 300) { // 5 minutes cache time
                $valid_files++;
            } else {
                $expired_files++;
            }
        }
        
        return [
            'total_files' => $total_files,
            'valid_files' => $valid_files,
            'expired_files' => $expired_files,
            'total_size' => $total_size,
            'cache_time' => 300
        ];
    }
    
    /**
     * Get cache health status
     */
    public static function getCacheHealth() {
        self::init();
        
        $cache_dir = __DIR__ . '/../cache/admin/';
        $files = glob($cache_dir . '*.cache');
        $total_files = count($files);
        $valid_files = 0;
        $expired_files = 0;
        $total_size = 0;
        
        foreach ($files as $file) {
            $total_size += filesize($file);
            
            $cache_data = file_get_contents($file);
            $cache = unserialize($cache_data);
            
            if (time() - $cache['timestamp'] < 300) { // 5 minutes cache time
                $valid_files++;
            } else {
                $expired_files++;
            }
        }
        
        $health_score = 0;
        if ($total_files > 0) {
            $health_score = ($valid_files / $total_files) * 100;
        }
        
        $status = 'excellent';
        if ($health_score < 80) $status = 'good';
        if ($health_score < 60) $status = 'fair';
        if ($health_score < 40) $status = 'poor';
        
        $stats = [
            'total_files' => $total_files,
            'valid_files' => $valid_files,
            'expired_files' => $expired_files,
            'total_size' => $total_size
        ];
        
        return [
            'score' => round($health_score, 1),
            'status' => $status,
            'recommendation' => self::getCacheRecommendation($stats)
        ];
    }
    
    /**
     * Get cache optimization recommendations
     */
    private static function getCacheRecommendation($stats) {
        $recommendations = [];
        
        if ($stats['expired_files'] > $stats['valid_files']) {
            $recommendations[] = 'Çok fazla süresi dolmuş cache dosyası var. Otomatik temizleme önerilir.';
        }
        
        if ($stats['total_size'] > 1024 * 1024) { // 1MB
            $recommendations[] = 'Cache boyutu çok büyük. Cache süresini azaltmayı düşünün.';
        }
        
        if ($stats['total_files'] > 100) {
            $recommendations[] = 'Çok fazla cache dosyası var. Cache stratejinizi gözden geçirin.';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Cache sistemi optimal durumda.';
        }
        
        return $recommendations;
    }
}

// Auto-clear cache on content updates
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add_content':
        case 'update_content':
        case 'delete_content':
            AdminCachedData::clearContentCache();
            break;
        case 'add_blog_post':
        case 'update_blog_post':
        case 'delete_blog_post':
            AdminCachedData::clearBlogCache();
            break;
        case 'add_portfolio':
        case 'update_portfolio':
        case 'delete_portfolio':
            AdminCachedData::clearPortfolioCache();
            break;
        case 'update_message_status':
        case 'delete_message':
            AdminCachedData::clearMessageCache();
            break;
    }
}
?>


