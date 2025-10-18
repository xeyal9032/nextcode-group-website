<?php
/**
 * NextCode Group - Cron Job için Otomatik Migration
 * 
 * Bu script cron job ile günlük çalıştırılır
 * 
 * CRON AYARI:
 * 0 3 * * * /usr/bin/php /path/to/nextcode/cron/auto-migration.php
 * (Her gün saat 03:00'da çalışır)
 * 
 * Manuel Çalıştırma:
 * php cron/auto-migration.php
 */

// CLI'den çalıştırıldığını kontrol et
if (php_sapi_name() !== 'cli') {
    die('Bu script sadece command line\'dan çalıştırılabilir.');
}

// Secure access tanımla
define('SECURE_ACCESS', true);

// Başlangıç zamanı
$start_time = microtime(true);

echo "====================================\n";
echo "NextCode Auto-Migration\n";
echo "====================================\n";
echo "Başlangıç: " . date('Y-m-d H:i:s') . "\n\n";

// Database bağlantısı
require_once __DIR__ . '/../config/database.php';

if (!$pdo) {
    echo "❌ HATA: Veritabanı bağlantısı başarısız!\n";
    exit(1);
}

echo "✅ Veritabanı bağlantısı başarılı\n\n";

// Auto-migration'ı çalıştır
require_once __DIR__ . '/../config/auto-migration.php';

$migration = new AutoMigration($pdo);
$success = $migration->forceRun(); // Cache'i atla, her zaman çalıştır

echo "Migration çalıştırılıyor...\n\n";

// Sonuçları göster
$results = $migration->getAppliedMigrations();
$errors = $migration->getErrors();

if (count($results) > 0) {
    echo "✅ BAŞARILI İŞLEMLER (" . count($results) . "):\n";
    echo "------------------------------------\n";
    foreach ($results as $result) {
        echo "  ✓ " . $result . "\n";
    }
    echo "\n";
} else {
    echo "ℹ️  Hiçbir değişiklik yapılmadı (Veritabanı güncel)\n\n";
}

if (count($errors) > 0) {
    echo "❌ HATALAR (" . count($errors) . "):\n";
    echo "------------------------------------\n";
    foreach ($errors as $error) {
        echo "  ✗ " . $error . "\n";
    }
    echo "\n";
}

// Bitiş zamanı
$end_time = microtime(true);
$duration = round($end_time - $start_time, 2);

echo "====================================\n";
echo "Bitiş: " . date('Y-m-d H:i:s') . "\n";
echo "Süre: {$duration} saniye\n";
echo "====================================\n";

// Exit code
exit(count($errors) > 0 ? 1 : 0);
?>


