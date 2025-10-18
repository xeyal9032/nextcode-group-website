<?php
// Frontend Cache Temizleme API - NextCode Group
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Sadece admin panelinden erişilebilir
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], '/admin/') === false) {
    http_response_code(403);
    die('Access denied');
}

// JSON header
header('Content-Type: application/json');

// Cache temizleme işlemleri
$results = [];

try {
    // 1. PHP OPcache temizle
    if (function_exists('opcache_reset')) {
        opcache_reset();
        $results[] = '✅ PHP OPcache temizlendi';
    } else {
        $results[] = '⚠️ OPcache mevcut değil';
    }
    
    // 2. Cache dizinini temizle
    $cacheDir = __DIR__ . '/cache';
    if (is_dir($cacheDir)) {
        $cleared = 0;
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'cache') {
                if (@unlink($file->getRealPath())) {
                    $cleared++;
                }
            }
        }
        $results[] = "✅ {$cleared} cache dosyası silindi";
    }
    
    // 3. Admin cache temizle
    require_once 'config/admin-cache.php';
    AdminCachedData::init();
    AdminCachedData::$cache->clearAll();
    $results[] = '✅ Admin cache temizlendi';
    
    // 4. Veritabanı cache temizle
    if (file_exists('cache/database')) {
        $dbCacheFiles = glob('cache/database/*.cache');
        foreach ($dbCacheFiles as $file) {
            @unlink($file);
        }
        $results[] = '✅ Veritabanı cache temizlendi';
    }
    
    // 5. API cache temizle
    if (file_exists('cache/api')) {
        $apiCacheFiles = glob('cache/api/*.cache');
        foreach ($apiCacheFiles as $file) {
            @unlink($file);
        }
        $results[] = '✅ API cache temizlendi';
    }
    
    // 6. Sayfa cache temizle
    if (file_exists('cache/pages')) {
        $pageCacheFiles = glob('cache/pages/*.cache');
        foreach ($pageCacheFiles as $file) {
            @unlink($file);
        }
        $results[] = '✅ Sayfa cache temizlendi';
    }
    
    // 7. Asset cache temizle
    if (file_exists('cache/assets')) {
        $assetCacheFiles = glob('cache/assets/*.cache');
        foreach ($assetCacheFiles as $file) {
            @unlink($file);
        }
        $results[] = '✅ Asset cache temizlendi';
    }
    
    // 8. Migration cache temizle
    if (file_exists('cache/migration_cache.json')) {
        @unlink('cache/migration_cache.json');
        $results[] = '✅ Migration cache temizlendi';
    }
    
    // Başarılı yanıt
    echo json_encode([
        'success' => true,
        'message' => 'Tüm cache dosyaları başarıyla temizlendi',
        'results' => $results,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    // Hata yanıtı
    echo json_encode([
        'success' => false,
        'message' => 'Cache temizleme hatası: ' . $e->getMessage(),
        'results' => $results,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
