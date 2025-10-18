<?php
/**
 * Admin Cache Otomatik Temizleme Scripti
 * Bu script cron job olarak çalıştırılabilir
 * Örnek cron job: 0,5,10,15,20,25,30,35,40,45,50,55 * * * * /usr/bin/php /path/to/admin-cache-cleaner.php
 */

// Sadece CLI'den çalıştırılabilir
if (php_sapi_name() !== 'cli') {
    die('Bu script sadece komut satırından çalıştırılabilir.');
}

// Proje kök dizinini ayarla
$project_root = dirname(__DIR__);
chdir($project_root);

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Cache yönetimi sınıfını dahil et
require_once 'config/admin-cache.php';

echo "[" . date('Y-m-d H:i:s') . "] Admin Cache Otomatik Temizleme Başlatıldı\n";

try {
    // Cache sistemini başlat
    AdminCachedData::init();
    
    // Süresi dolmuş cache dosyalarını temizle
    $cleaned = AdminCachedData::autoCleanExpiredCache();
    
    if ($cleaned > 0) {
        echo "[" . date('Y-m-d H:i:s') . "] ✅ {$cleaned} süresi dolmuş cache dosyası temizlendi\n";
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] ℹ️ Temizlenecek süresi dolmuş cache dosyası bulunamadı\n";
    }
    
    // Cache sağlık durumunu kontrol et
    $health = AdminCachedData::getCacheHealth();
    echo "[" . date('Y-m-d H:i:s') . "] 📊 Cache sağlık skoru: {$health['score']}% ({$health['status']})\n";
    
    // Eğer sağlık durumu kötüyse uyarı ver
    if ($health['status'] === 'poor' || $health['status'] === 'fair') {
        echo "[" . date('Y-m-d H:i:s') . "] ⚠️ Cache sağlık durumu düşük. Öneriler:\n";
        foreach ($health['recommendation'] as $recommendation) {
            echo "[" . date('Y-m-d H:i:s') . "] - {$recommendation}\n";
        }
    }
    
    // Cache istatistiklerini al (production uyumlu)
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
    
    echo "[" . date('Y-m-d H:i:s') . "] 📈 Cache istatistikleri:\n";
    echo "[" . date('Y-m-d H:i:s') . "] - Toplam dosya: {$total_files}\n";
    echo "[" . date('Y-m-d H:i:s') . "] - Geçerli dosya: {$valid_files}\n";
    echo "[" . date('Y-m-d H:i:s') . "] - Süresi dolmuş: {$expired_files}\n";
    echo "[" . date('Y-m-d H:i:s') . "] - Toplam boyut: " . number_format($total_size / 1024, 1) . " KB\n";
    
    echo "[" . date('Y-m-d H:i:s') . "] ✅ Admin Cache Otomatik Temizleme Tamamlandı\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ❌ Hata: " . $e->getMessage() . "\n";
    exit(1);
}

exit(0);
