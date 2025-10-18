<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

try {
    $cleared_files = 0;
    $cleared_dirs = [];
    
    // Cache dizinlerini temizle
    $cache_dirs = [
        '../cache',
        '../tmp', 
        '../logs',
        '../uploads/temp'
    ];
    
    foreach ($cache_dirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.htaccess') {
                    if (unlink($file)) {
                        $cleared_files++;
                    }
                }
            }
            $cleared_dirs[] = $dir;
        }
    }
    
    // OPcache temizle (eğer mevcutsa)
    if (function_exists('opcache_reset')) {
        opcache_reset();
        $cleared_dirs[] = 'OPcache';
    }
    
    // Browser cache için header'lar
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Başarılı yanıt
    echo json_encode([
        'success' => true,
        'message' => 'Cache başarıyla temizlendi',
        'cleared_files' => $cleared_files,
        'cleared_dirs' => $cleared_dirs,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Cache temizleme hatası: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
