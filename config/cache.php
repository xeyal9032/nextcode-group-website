<?php
// NextCode Group - Advanced Cache System
// Optimized caching system for improved performance

// Security check temporarily disabled for testing
/*
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}
*/

class CacheManager {
    private $cacheDir;
    private $defaultTTL;
    private $compressionLevel;
    
    public function __construct($cacheDir = null, $defaultTTL = 3600, $compressionLevel = 6) {
        $this->cacheDir = $cacheDir ?: __DIR__ . '/../cache';
        $this->defaultTTL = $defaultTTL;
        $this->compressionLevel = $compressionLevel;
        
        // Ensure cache directory exists
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
        
        // Create subdirectories
        $subdirs = ['pages', 'api', 'database', 'assets'];
        foreach ($subdirs as $subdir) {
            $dir = $this->cacheDir . '/' . $subdir;
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
    
    /**
     * Store data in cache
     */
    public function set($key, $data, $ttl = null) {
        $ttl = $ttl ?: $this->defaultTTL;
        $filename = $this->cacheDir . '/' . md5($key) . '.cache';
        
        $cacheData = [
            'data' => $data,
            'timestamp' => time(),
            'ttl' => $ttl,
            'checksum' => crc32(serialize($data))
        ];
        
        $compressed = gzcompress(serialize($cacheData), $this->compressionLevel);
        
        return file_put_contents($filename, $compressed, LOCK_EX);
    }
    
    /**
     * Retrieve data from cache
     */
    public function get($key) {
        $filename = $this->cacheDir . '/' . md5($key) . '.cache';
        
        if (!file_exists($filename)) {
            return false;
        }
        
        $compressed = file_get_contents($filename);
        if ($compressed ===false) {
            return false;
        }
        
        $decompressed = gzuncompress($compressed);
        $cacheData = unserialize($decompressed);
        
        // Check if cache is expired
        if (time() - $cacheData['timestamp'] > $cacheData['ttl']) {
            $this->delete($key);
            return false;
        }
        
        // Verify data integrity
        if (crc32(serialize($cacheData['data'])) !== $cacheData['checksum']) {
            $this->delete($key);
            return false;
        }
        
        return $cacheData['data'];
    }
    
    /**
     * Delete cache entry
     */
    public function delete($key) {
        $filename = $this->cacheDir . '/' . md5($key) . '.cache';
        if (file_exists($filename)) {
            return unlink($filename);
        }
        return true;
    }
    
    /**
     * Clear all cache
     */
    public function clear() {
        $files = glob($this->cacheDir . '/*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }
    
    /**
     * Clear cache by pattern
     */
    public function clearPattern($pattern) {
        $files = glob($this->cacheDir . '/' . $pattern . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }
    
    /**
     * Get cache statistics
     */
    public function getStats() {
        $files = glob($this->cacheDir . '/*.cache');
        $totalSize = 0;
        $oldFiles = 0;
        
        foreach ($files as $file) {
            $totalSize += filesize($file);
            
            // Check for old files
            if (time() - filemtime($file) > $this->defaultTTL * 2) {
                $oldFiles++;
            }
        }
        
        return [
            'total_files' => count($files),
            'total_size' => $totalSize,
            'old_files' => $oldFiles,
            'memory_usage' => memory_get_usage(),
            'cache_hit_rate' => $this->calculateHitRate()
        ];
    }
    
    /**
     * Calculate cache hit rate (simple implementation)
     */
    private function calculateHitRate() {
        $statsFile = $this->cacheDir . '/stats.json';
        
        if (file_exists($statsFile)) {
            $stats = json_decode(file_get_contents($statsFile), true);
            $total = $stats['hits'] + $stats['misses'];
            return $total > 0 ? round(($stats['hits'] / $total) * 100, 2) : 0;
        }
        
        return 0;
    }
    
    /**
     * Clean up expired cache files
     */
    public function cleanup() {
        $files = glob($this->cacheDir . '/*.cache');
        $cleaned = 0;
        
        foreach ($files as $file) {
            $compressed = file_get_contents($file);
            if ($compressed === false) continue;
            
            $decompressed = gzuncompress($compressed);
            if ($decompressed === false) {
                unlink($file);
                $cleaned++;
                continue;
            }
            
            $cacheData = unserialize($decompressed);
            if (time() - $cacheData['timestamp'] > $cacheData['ttl']) {
                unlink($file);
                $cleaned++;
            }
        }
        
        return $cleaned;
    }
}

// Global cache instance
$cache = new CacheManager();

// Cache helper functions
function cache_get($key, $fallback = null) {
    global $cache;
    $result = $cache->get($key);
    return $result !== false ? $result : $fallback;
}

function cache_set($key, $data, $ttl = 3600) {
    global $cache;
    return $cache->set($key, $data, $ttl);
}

function cache_delete($key) {
    global $cache;
    return $cache->delete($key);
}

function cache_clear($pattern = null) {
    global $cache;
    if ($pattern) {
        return $cache->clearPattern($pattern);
    }
    return $cache->clear();
}

// Auto cleanup expired cache (run with low probability)
if (mt_rand(1, 100) === 1) {
    $cache->cleanup();
}

// Headers for browser caching
if (!headers_sent()) {
    // Cache static assets for 1 year
    $ext = pathinfo($_SERVER['REQUEST_URI'] ?? '', PATHINFO_EXTENSION);
    if (in_array($ext, ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'woff2', 'ttf'])) {
        $maxAge = 31536000; // 1 year
        header("Cache-Control: public, max-age=$maxAge, immutable");
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
    }
    
    // Cache API responses for 5 minutes
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0) {
        header('Cache-Control: public, max-age=300'); // 5 minutes
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 300) . ' GMT');
    }
}

?>

