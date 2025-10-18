<?php
/**
 * Cache Temizleme Scripti
 * Bu dosyayı tarayıcıda açın: https://nextcode.az/clear-cache.php
 * Sonra dosyayı silin (güvenlik için)
 */

// Tüm cache dosyalarını temizle
function clearAllCaches() {
    $results = [];
    
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
    
    // 3. Session temizle
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_destroy();
    $results[] = '✅ Session temizlendi';
    
    // 4. Config cache temizle
    $configCache = __DIR__ . '/cache/config_cache.php';
    if (file_exists($configCache)) {
        @unlink($configCache);
        $results[] = '✅ Config cache temizlendi';
    }
    
    return $results;
}

// Ana sayfa
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Temizleme</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .result {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .result-item {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            background: white;
            font-size: 14px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
        }
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-danger {
            background: #dc3545;
            margin-top: 10px;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            color: #856404;
            font-size: 14px;
        }
        .success {
            background: #d4edda;
            border: 1px solid #28a745;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧹 Cache Temizleme</h1>
        <p class="subtitle">NextCode Group - Cache Yönetimi</p>
        
        <?php if (isset($_GET['clear'])): ?>
            <?php $results = clearAllCaches(); ?>
            <div class="success">
                <strong>✅ Cache başarıyla temizlendi!</strong>
            </div>
            <div class="result">
                <?php foreach ($results as $result): ?>
                    <div class="result-item"><?php echo $result; ?></div>
                <?php endforeach; ?>
            </div>
            <a href="/" class="btn">🏠 Ana Sayfaya Dön</a>
            <a href="?delete=1" class="btn btn-danger">🗑️ Bu Dosyayı Sil</a>
        <?php elseif (isset($_GET['delete'])): ?>
            <?php 
            if (@unlink(__FILE__)) {
                echo '<div class="success">✅ clear-cache.php dosyası silindi!</div>';
                echo '<a href="/" class="btn">🏠 Ana Sayfaya Dön</a>';
            } else {
                echo '<div class="warning">⚠️ Dosya silinemedi. Manuel olarak silin.</div>';
                echo '<a href="/" class="btn">🏠 Ana Sayfaya Dön</a>';
            }
            ?>
        <?php else: ?>
            <div class="warning">
                <strong>⚠️ Uyarı:</strong> Bu işlem tüm cache dosyalarını temizleyecek. 
                Devam etmek istiyor musunuz?
            </div>
            <a href="?clear=1" class="btn">🧹 Cache'i Temizle</a>
            <a href="/" class="btn btn-danger">❌ İptal Et</a>
        <?php endif; ?>
    </div>
</body>
</html>


