<?php
/**
 * Advanced Cache Manager
 * Multi-layer cache stratejisi
 */

class CacheManager
{
    private $layers = [];
    private $stats = [
        'hits' => 0,
        'misses' => 0,
        'writes' => 0
    ];
    
    const MEMORY_CACHE = 'memory';
    const REDIS_CACHE = 'redis';
    const FILE_CACHE = 'file';
    
    // Cache TTL presets
    const TTL_MINUTE = 60;
    const TTL_HOUR = 3600;
    const TTL_DAY = 86400;
    const TTL_WEEK = 604800;
    const TTL_MONTH = 2592000;
    
    // Cache tags
    private $tags = [];
    
    public function __construct()
    {
        // Layer 1: Memory cache (en hızlı)
        $this->layers[self::MEMORY_CACHE] = new MemoryCache();
        
        // Layer 2: Redis cache (hızlı ve persistent)
        if (class_exists('Redis') && extension_loaded('redis')) {
            require_once __DIR__ . '/redis-cache.php';
            $this->layers[self::REDIS_CACHE] = cache();
        }
        
        // Layer 3: File cache (fallback)
        require_once __DIR__ . '/cache.php';
        $this->layers[self::FILE_CACHE] = new Cache();
    }
    
    /**
     * Get from cache with multi-layer support
     */
    public function get($key, $default = null)
    {
        // Check memory cache first
        if (isset($this->layers[self::MEMORY_CACHE])) {
            $value = $this->layers[self::MEMORY_CACHE]->get($key);
            if ($value !== null) {
                $this->stats['hits']++;
                return $value;
            }
        }
        
        // Check Redis cache
        if (isset($this->layers[self::REDIS_CACHE])) {
            $value = $this->layers[self::REDIS_CACHE]->get($key);
            if ($value !== null) {
                $this->stats['hits']++;
                // Store in memory cache for next access
                $this->layers[self::MEMORY_CACHE]->set($key, $value);
                return $value;
            }
        }
        
        // Check file cache
        if (isset($this->layers[self::FILE_CACHE])) {
            $value = $this->layers[self::FILE_CACHE]->get($key);
            if ($value !== null) {
                $this->stats['hits']++;
                // Store in upper layers
                if (isset($this->layers[self::REDIS_CACHE])) {
                    $this->layers[self::REDIS_CACHE]->set($key, $value);
                }
                $this->layers[self::MEMORY_CACHE]->set($key, $value);
                return $value;
            }
        }
        
        $this->stats['misses']++;
        return $default;
    }
    
    /**
     * Set in all cache layers
     */
    public function set($key, $value, $ttl = null)
    {
        $this->stats['writes']++;
        
        foreach ($this->layers as $layer) {
            $layer->set($key, $value, $ttl);
        }
        
        return true;
    }
    
    /**
     * Remember: Get or execute callback and cache
     */
    public function remember($key, $ttl, $callback)
    {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }
    
    /**
     * Remember forever (1 month TTL)
     */
    public function rememberForever($key, $callback)
    {
        return $this->remember($key, self::TTL_MONTH, $callback);
    }
    
    /**
     * Delete from all layers
     */
    public function delete($key)
    {
        foreach ($this->layers as $layer) {
            $layer->delete($key);
        }
        return true;
    }
    
    /**
     * Flush pattern from all layers
     */
    public function flush($pattern = '*')
    {
        foreach ($this->layers as $layer) {
            if (method_exists($layer, 'flush')) {
                $layer->flush($pattern);
            }
        }
        return true;
    }
    
    /**
     * Tag-based cache
     */
    public function tags($tags)
    {
        $this->tags = is_array($tags) ? $tags : [$tags];
        return $this;
    }
    
    /**
     * Put with tags
     */
    public function put($key, $value, $ttl = null)
    {
        $this->set($key, $value, $ttl);
        
        // Store tag associations
        foreach ($this->tags as $tag) {
            $tagKey = "tag:$tag";
            $taggedKeys = $this->get($tagKey, []);
            $taggedKeys[] = $key;
            $this->set($tagKey, array_unique($taggedKeys), self::TTL_MONTH);
        }
        
        $this->tags = [];
        return true;
    }
    
    /**
     * Flush by tag
     */
    public function flushTags($tags)
    {
        $tags = is_array($tags) ? $tags : [$tags];
        
        foreach ($tags as $tag) {
            $tagKey = "tag:$tag";
            $keys = $this->get($tagKey, []);
            
            foreach ($keys as $key) {
                $this->delete($key);
            }
            
            $this->delete($tagKey);
        }
        
        return true;
    }
    
    /**
     * Cache statistics
     */
    public function getStats()
    {
        $layerStats = [];
        
        foreach ($this->layers as $name => $layer) {
            if (method_exists($layer, 'getStats')) {
                $layerStats[$name] = $layer->getStats();
            }
        }
        
        return [
            'hits' => $this->stats['hits'],
            'misses' => $this->stats['misses'],
            'writes' => $this->stats['writes'],
            'hit_ratio' => $this->stats['hits'] > 0 
                ? round($this->stats['hits'] / ($this->stats['hits'] + $this->stats['misses']) * 100, 2) 
                : 0,
            'layers' => $layerStats
        ];
    }
    
    /**
     * Cache warming - Preload critical data
     */
    public function warm($items)
    {
        foreach ($items as $key => $callback) {
            if (!$this->get($key)) {
                $value = $callback();
                $this->set($key, $value, self::TTL_HOUR);
            }
        }
        return true;
    }
}

/**
 * Memory Cache (Layer 1)
 */
class MemoryCache
{
    private $cache = [];
    private $expires = [];
    
    public function get($key)
    {
        if (!isset($this->cache[$key])) {
            return null;
        }
        
        if (isset($this->expires[$key]) && $this->expires[$key] < time()) {
            unset($this->cache[$key], $this->expires[$key]);
            return null;
        }
        
        return $this->cache[$key];
    }
    
    public function set($key, $value, $ttl = null)
    {
        $this->cache[$key] = $value;
        if ($ttl) {
            $this->expires[$key] = time() + $ttl;
        }
        return true;
    }
    
    public function delete($key)
    {
        unset($this->cache[$key], $this->expires[$key]);
        return true;
    }
    
    public function flush($pattern = '*')
    {
        $this->cache = [];
        $this->expires = [];
        return true;
    }
    
    public function getStats()
    {
        return [
            'type' => 'memory',
            'keys' => count($this->cache),
            'size' => strlen(serialize($this->cache))
        ];
    }
}

// Global instance
$GLOBALS['cache_manager'] = new CacheManager();

/**
 * Helper functions
 */
function cache_manager()
{
    return $GLOBALS['cache_manager'];
}

function cache_get($key, $default = null)
{
    return cache_manager()->get($key, $default);
}

function cache_set($key, $value, $ttl = null)
{
    return cache_manager()->set($key, $value, $ttl);
}

function cache_remember($key, $ttl, $callback)
{
    return cache_manager()->remember($key, $ttl, $callback);
}

function cache_delete($key)
{
    return cache_manager()->delete($key);
}

function cache_flush($pattern = '*')
{
    return cache_manager()->flush($pattern);
}

function cache_stats()
{
    return cache_manager()->getStats();
}


