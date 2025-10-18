<?php
/**
 * NextCode Group - Database Compatibility Checker (CLI)
 * Terminal'den veritabanı uyumluluğunu kontrol eder
 */

define('SECURE_ACCESS', true);

// ANSI Colors for terminal output
class Colors {
    public static $GREEN = "\033[32m";
    public static $RED = "\033[31m";
    public static $YELLOW = "\033[33m";
    public static $BLUE = "\033[34m";
    public static $RESET = "\033[0m";
    public static $BOLD = "\033[1m";
}

echo Colors::$BOLD . Colors::$BLUE . "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   NextCode Group - Veritabanı Uyumluluk Kontrolü          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo Colors::$RESET . "\n";

// Database bağlantısı
require_once __DIR__ . '/config/database.php';

if (!$pdo) {
    echo Colors::$RED . "❌ Veritabanı bağlantısı BAŞARISIZ!\n" . Colors::$RESET;
    echo "Lütfen config/database.php dosyasını kontrol edin.\n\n";
    exit(1);
}

echo Colors::$GREEN . "✅ Veritabanı bağlantısı başarılı\n" . Colors::$RESET;
echo "Database: gtorg_nextcode\n";
echo "Host: gtorg.mysql.tools\n\n";

// Test sonuçları
$total_checks = 0;
$passed_checks = 0;
$failed_checks = 0;
$warnings = 0;

// Kontrol edilecek tablo ve sütunlar
$checks = [
    'admin_users' => [
        'required' => ['id', 'username', 'email', 'password_hash', 'login_attempts', 'locked_until'],
        'optional' => ['remember_token', 'ip_address', 'user_agent']
    ],
    'contact_messages' => [
        'required' => ['id', 'first_name', 'last_name', 'email', 'status'],
        'optional' => ['phone', 'subject', 'message', 'ip_address', 'user_agent']
    ],
    'blog_posts' => [
        'required' => ['id', 'title', 'slug', 'content', 'status'],
        'optional' => ['category_id', 'featured_image', 'meta_title', 'meta_description']
    ],
    'portfolio_projects' => [
        'required' => ['id', 'title', 'description', 'category'],
        'optional' => ['is_featured', 'is_published', 'image_url', 'technologies']
    ],
    'services' => [
        'required' => ['id', 'title', 'description'],
        'optional' => ['icon', 'is_active', 'order_index']
    ],
    'site_content' => [
        'required' => ['id', 'page_name', 'section_name', 'content_key', 'content_value'],
        'optional' => ['content_type', 'is_active']
    ],
    'site_images' => [
        'required' => ['id', 'image_key', 'image_url'],
        'optional' => ['image_alt', 'image_title', 'image_description']
    ]
];

echo Colors::$BOLD . "TABLO KONTROLÜ\n" . Colors::$RESET;
echo str_repeat("=", 60) . "\n\n";

foreach ($checks as $table => $columns) {
    $total_checks++;
    
    echo Colors::$BOLD . "📋 {$table}\n" . Colors::$RESET;
    
    try {
        // Tablo var mı kontrol et
        $pdo->query("SELECT 1 FROM `{$table}` LIMIT 1");
        echo Colors::$GREEN . "   ✓ Tablo mevcut\n" . Colors::$RESET;
        
        // Sütunları al
        $existing_columns = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_COLUMN);
        
        // Required sütunları kontrol et
        $missing_required = [];
        foreach ($columns['required'] as $col) {
            if (in_array($col, $existing_columns)) {
                echo "   ✓ {$col} " . Colors::$GREEN . "[VAR]" . Colors::$RESET . "\n";
            } else {
                echo "   ✗ {$col} " . Colors::$RED . "[YOK - GEREKLİ!]" . Colors::$RESET . "\n";
                $missing_required[] = $col;
                $failed_checks++;
            }
        }
        
        // Optional sütunları kontrol et
        $missing_optional = [];
        foreach ($columns['optional'] as $col) {
            if (in_array($col, $existing_columns)) {
                echo "   ✓ {$col} " . Colors::$GREEN . "[VAR]" . Colors::$RESET . "\n";
            } else {
                echo "   ⚠ {$col} " . Colors::$YELLOW . "[YOK - Opsiyonel]" . Colors::$RESET . "\n";
                $missing_optional[] = $col;
                $warnings++;
            }
        }
        
        // Tablo durumu
        if (count($missing_required) === 0) {
            echo Colors::$GREEN . "   ✅ Tablo tam uyumlu\n" . Colors::$RESET;
            $passed_checks++;
        } else {
            echo Colors::$RED . "   ❌ " . count($missing_required) . " gerekli sütun eksik\n" . Colors::$RESET;
        }
        
    } catch (PDOException $e) {
        echo Colors::$RED . "   ❌ Tablo bulunamadı: " . $e->getMessage() . "\n" . Colors::$RESET;
        $failed_checks++;
    }
    
    echo "\n";
}

// Index kontrolü
echo Colors::$BOLD . "\nINDEX KONTROLÜ\n" . Colors::$RESET;
echo str_repeat("=", 60) . "\n\n";

$index_checks = [
    'contact_messages' => ['idx_status', 'idx_email', 'idx_created_at'],
    'admin_users' => ['idx_remember_token', 'idx_username', 'idx_email'],
    'blog_posts' => ['idx_slug', 'idx_status'],
    'portfolio_projects' => ['idx_category', 'idx_is_featured']
];

foreach ($index_checks as $table => $indexes) {
    echo Colors::$BOLD . "📊 {$table}\n" . Colors::$RESET;
    
    try {
        $existing_indexes = $pdo->query("SHOW INDEX FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
        $index_names = array_column($existing_indexes, 'Key_name');
        
        foreach ($indexes as $index) {
            if (in_array($index, $index_names)) {
                echo "   ✓ {$index} " . Colors::$GREEN . "[VAR]" . Colors::$RESET . "\n";
            } else {
                echo "   ⚠ {$index} " . Colors::$YELLOW . "[YOK - Performans için önerilen]" . Colors::$RESET . "\n";
            }
        }
    } catch (PDOException $e) {
        echo Colors::$RED . "   ❌ Index kontrol hatası\n" . Colors::$RESET;
    }
    
    echo "\n";
}

// Özet
echo Colors::$BOLD . "\n" . str_repeat("=", 60) . "\n";
echo "ÖZET\n";
echo str_repeat("=", 60) . "\n" . Colors::$RESET;

$success_rate = $total_checks > 0 ? round(($passed_checks / $total_checks) * 100) : 0;

echo "\nToplam Kontrol: {$total_checks}\n";
echo Colors::$GREEN . "Başarılı: {$passed_checks}\n" . Colors::$RESET;
echo Colors::$RED . "Başarısız: {$failed_checks}\n" . Colors::$RESET;
echo Colors::$YELLOW . "Uyarılar: {$warnings}\n" . Colors::$RESET;
echo "\nBaşarı Oranı: ";

if ($success_rate >= 80) {
    echo Colors::$GREEN . "{$success_rate}% ✅\n" . Colors::$RESET;
} elseif ($success_rate >= 50) {
    echo Colors::$YELLOW . "{$success_rate}% ⚠️\n" . Colors::$RESET;
} else {
    echo Colors::$RED . "{$success_rate}% ❌\n" . Colors::$RESET;
}

// Öneriler
if ($failed_checks > 0 || $warnings > 5) {
    echo "\n" . Colors::$BOLD . Colors::$YELLOW . "ÖNERILER:\n" . Colors::$RESET;
    echo str_repeat("-", 60) . "\n";
    echo "1. Otomatik migration çalıştırın:\n";
    echo "   php cron/auto-migration.php\n\n";
    echo "2. Veya admin panelden:\n";
    echo "   https://nextcode.az/admin/database-migration.php\n\n";
    echo "3. Veya manuel SQL:\n";
    echo "   veritabani-eksikleri-GUVENLI.sql dosyasını phpMyAdmin'de çalıştırın\n";
}

echo "\n" . Colors::$BOLD . "Tarih: " . date('Y-m-d H:i:s') . "\n" . Colors::$RESET;
echo "\n";

exit($failed_checks > 0 ? 1 : 0);
?>

