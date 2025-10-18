<?php
/**
 * Redis Cache Manager
 * Redis ile performans odaklı cache yönetimi
 */

class RedisCache
{
    private $redis;
    private $prefix = 'nextcode_';
    private $defaultTTL = 3600; // 1 saat
    private $isConnected = false;
    
    public function __construct($host = '127.0.0.1', $port = 6379, $password = null)
    {
        try {
            if (!extension_loaded('redis')) {
                error_log('Redis extension not loaded, falling back to file cache');
                return;
            }
            
            $this->redis = new Redis();
            $connected = $this->redis->connect($host, $port, 2.5); // 2.5 second timeout
            
            if ($connected && $password) {
                $this->redis->auth($password);
            }
            
            if ($connected) {
                $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
                $this->redis->setOption(Redis::OPT_PREFIX, $this->prefix);
                $this->isConnected = true;
                error_log('Redis cache connected successfully');
            }
        } catch (Exception $e) {
            error_log('Redis connection failed: ' . $e->getMessage());
            $this->isConnected = false;
        }
    }
    
    /**
     * Cache'e veri kaydet
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$this->isConnected) {
            return $this->setFileCache($key, $value, $ttl);
        }
        
        try {
            $ttl = $ttl ?? $this->defaultTTL;
            return $this->redis->setex($key, $ttl, $value);
        } catch (Exception $e) {
            error_log('Redis set error: ' . $e->getMessage());
            return $this->setFileCache($key, $value, $ttl);
        }
    }
    
    /**
     * Cache'den veri al
     */
    public function get($key)
    {
        if (!$this->isConnected) {
            return $this->getFileCache($key);
        }
        
        try {
            $value = $this->redis->get($key);
            return $value !== false ? $value : null;
        } catch (Exception $e) {
            error_log('Redis get error: ' . $e->getMessage());
            return $this->getFileCache($key);
        }
    }
    
    /**
     * Cache'den veri sil
     */
    public function delete($key)
    {
        if (!$this->isConnected) {
            return $this->deleteFileCache($key);
        }
        
        try {
            return $this->redis->del($key) > 0;
        } catch (Exception $e) {
            error_log('Redis delete error: ' . $e->getMessage());
            return $this->deleteFileCache($key);
        }
    }
    
    /**
     * Pattern ile cache temizle
     */
    public function flush($pattern = '*')
    {
        if (!$this->isConnected) {
            return $this->flushFileCache($pattern);
        }
        
        try {
            $keys = $this->redis->keys($pattern);
            if (!empty($keys)) {
                return $this->redis->del($keys) > 0;
            }
            return true;
        } catch (Exception $e) {
            error_log('Redis flush error: ' . $e->getMessage());
            return $this->flushFileCache($pattern);
        }
    }
    
    /**
     * Cache'e varsa al, yoksa callback çalıştır ve kaydet
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
     * Increment cache value
     */
    public function increment($key, $value = 1)
    {
        if (!$this->isConnected) {
            return false;
        }
        
        try {
            return $this->redis->incrBy($key, $value);
        } catch (Exception $e) {
            error_log('Redis increment error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Decrement cache value
     */
    public function decrement($key, $value = 1)
    {
        if (!$this->isConnected) {
            return false;
        }
        
        try {
            return $this->redis->decrBy($key, $value);
        } catch (Exception $e) {
            error_log('Redis decrement error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if key exists
     */
    public function exists($key)
    {
        if (!$this->isConnected) {
            return $this->fileExists($key);
        }
        
        try {
            return $this->redis->exists($key) > 0;
        } catch (Exception $e) {
            error_log('Redis exists error: ' . $e->getMessage());
            return $this->fileExists($key);
        }
    }
    
    /**
     * Get cache statistics
     */
    public function getStats()
    {
        if (!$this->isConnected) {
            return ['connected' => false, 'type' => 'file'];
        }
        
        try {
            $info = $this->redis->info();
            return [
                'connected' => true,
                'type' => 'redis',
                'version' => $info['redis_version'] ?? 'unknown',
                'used_memory' => $info['used_memory_human'] ?? 'unknown',
                'uptime' => $info['uptime_in_days'] ?? 'unknown',
                'keys' => $this->redis->dbSize()
            ];
        } catch (Exception $e) {
            return ['connected' => false, 'error' => $e->getMessage()];
        }
    }
    
    // Fallback: File-based cache methods
    
    private function getCacheDir()
    {
        $dir = __DIR__ . '/../cache/redis-fallback';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }
    
    private function getCacheFile($key)
    {
        return $this->getCacheDir() . '/' . md5($this->prefix . $key) . '.cache';
    }
    
    private function setFileCache($key, $value, $ttl)
    {
        $file = $this->getCacheFile($key);
        $data = [
            'value' => $value,
            'expires' => time() + ($ttl ?? $this->defaultTTL)
        ];
        return file_put_contents($file, serialize($data)) !== false;
    }
    
    private function getFileCache($key)
    {
        $file = $this->getCacheFile($key);
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($file));
        
        if ($data['expires'] < time()) {
            unlink($file);
            return null;
        }
        
        return $data['value'];
    }
    
    private function deleteFileCache($key)
    {
        $file = $this->getCacheFile($key);
        return file_exists($file) ? unlink($file) : true;
    }
    
    private function flushFileCache($pattern)
    {
        $dir = $this->getCacheDir();
        $files = glob($dir . '/*.cache');
        
        foreach ($files as $file) {
            unlink($file);
        }
        
        return true;
    }
    
    private function fileExists($key)
    {
        return file_exists($this->getCacheFile($key));
    }
    
    /**
     * Close Redis connection
     */
    public function __destruct()
    {
        if ($this->isConnected && $this->redis) {
            try {
                $this->redis->close();
            } catch (Exception $e) {
                // Suppress errors on shutdown
            }
        }
    }
}

// Global cache instance
$GLOBALS['redis_cache'] = new RedisCache(
    $_ENV['REDIS_HOST'] ?? '127.0.0.1',
    $_ENV['REDIS_PORT'] ?? 6379,
    $_ENV['REDIS_PASSWORD'] ?? null
);

/**
 * Helper function to access cache
 */
function cache()
{
    return $GLOBALS['redis_cache'];
}


