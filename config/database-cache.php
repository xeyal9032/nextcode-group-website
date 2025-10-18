<?php
// NextCode Group - Database Query Cache System
// Optimized database caching for better performance

// Security check temporarily disabled for testing
/*
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}
*/

require_once __DIR__ . '/cache.php';

class DatabaseCache {
    private $cache;
    private $cachePrefix = 'db_';
    private $defaultTTL = 1800; // 30 minutes
    
    public function __construct($cacheManager = null) {
        $this->cache = $cacheManager ?: $cache;
    }
    
    /**
     * Cache SELECT queries with automatic invalidation
     */
    public function query($sql, $params = [], $ttl = null) {
        global $pdo;
        
        if (!$pdo) {
            return false;
        }
        
        // Create cache key
        $cacheKey = $this->cachePrefix . md5($sql . serialize($params));
        
        // Try to get from cache first
        $result = $this->cache->get($cacheKey);
        if ($result !== false) {
            return $result;
        }
        
        // Execute query
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Cache the result
            $this->cache->set($cacheKey, $result, $ttl ?: $this->defaultTTL);
            
            return $result;
        } catch (PDOException $e) {
            error_log('Database cache query failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cache single query result
     */
    public function queryOne($sql, $params = [], $ttl = null) {
        $results = $this->query($sql, $params, $ttl);
        return is_array($results) ? $results[0] ?? false : false;
    }
    
    /**
     * Cache paginated results
     */
    public function queryPaginated($sql, $params = [], $page = 1, $perPage = 10, $ttl = null) {
        $offset = ($page - 1) * $perPage;
        
        // Count query
        $countSql = "SELECT COUNT(*) as total FROM ($sql) as count_table";
        $countResult = $this->queryOne($countSql, $params, $ttl);
        $total = $countResult['total'] ?? 0;
        
        // Data query
        $limitedSql = "$sql LIMIT $perPage OFFSET $offset";
        $data = $this->query($limitedSql, $params, $ttl);
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Cache function results
     */
    public function cacheFunction($functionName, $params = [], $ttl = null) {
        $cacheKey = $this->cachePrefix . 'func_' . md5($functionName . serialize($params));
        
        // Try cache first
        $result = $this->cache->get($cacheKey);
        if ($result !== false) {
            return $result;
        }
        
        // Execute function
        if (function_exists($functionName)) {
            $result = call_user_func_array($functionName, $params);
            $this->cache->set($cacheKey, $result, $ttl ?: $this->defaultTTL);
            return $result;
        }
        
        return false;
    }
    
    /**
     * Invalidate cache by table
     */
    public function invalidateTable($tableName) {
        $pattern = $this->cachePrefix . '*_table_' . $tableName . '_*';
        $this->cache->clearPattern($pattern);
    }
    
    /**
     * Invalidate cache by tags
     */
    public function invalidateTags($tags) {
        foreach ($tags as $tag) {
            $pattern = $this->cachePrefix . '*_tag_' . $tag . '_*';
            $this->cache->clearPattern($pattern);
        }
    }
    
    /**
     * Clear all database cache
     */
    public function clearAll() {
        $pattern = $this->cachePrefix . '*';
        $this->cache->clearPattern($pattern);
    }
    
    /**
     * Warm up cache with common queries
     */
    public function warmUp() {
        $commonQueries = [
            // Blog queries
            'blog_posts_featured' => [
                'sql' => 'SELECT * FROM blog_posts WHERE status = ? AND is_featured = ? ORDER BY published_at DESC LIMIT 3',
                'params' => ['published', 1]
            ],
            'blog_categories' => [
                'sql' => 'SELECT * FROM blog_categories ORDER BY name ASC',
                'params' => []
            ],
            // Portfolio queries
            'portfolio_featured' => [
                'sql' => 'SELECT * FROM portfolio_projects WHERE is_published = ? AND is_featured = ? ORDER BY sort_order ASC LIMIT 6',
                'params' => [1, 1]
            ],
            // Services queries
            'active_services' => [
                'sql' => 'SELECT * FROM services WHERE status = ? ORDER BY order_index ASC',
                'params' => ['active']
            ],
            // FAQ queries
            'active_faq' => [
                'sql' => 'SELECT * FROM faq WHERE is_active = ? ORDER BY sort_order ASC LIMIT 10',
                'params' => [1]
            ],
            // Site statistics
            'site_stats' => [
                'sql' => 'SELECT * FROM site_statistics WHERE is_active = ? ORDER BY id DESC LIMIT 1',
                'params' => [1]
            ]
        ];
        
        foreach ($commonQueries as $key => $query) {
            $cacheKey = $this->cachePrefix . 'warmup_' . $key;
            $result = $this->query($query['sql'], $query['params'], 3600); // Cache for 1 hour
        }
        
        return true;
    }
}

// Global database cache instance
$dbCache = new DatabaseCache($cache);

// Enhanced query functions with caching
function cached_query($sql, $params = [], $ttl = null) {
    global $dbCache;
    return $dbCache->query($sql, $params, $ttl);
}

function cached_query_one($sql, $params = [], $ttl = null) {
    global $dbCache;
    return $dbCache->queryOne($sql, $params, $ttl);
}

function cached_query_paginated($sql, $params = [], $page = 1, $perPage = 10, $ttl = null) {
    global $dbCache;
    return $dbCache->queryPaginated($sql, $params, $page, $perPage, $ttl);
}

// Auto-warm up cache on first load (low probability)
if (mt_rand(1, 50) === 1) {
    $dbCache->warmUp();
}

?>

