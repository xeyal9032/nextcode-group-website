<?php
/**
 * NextCode Group - Database Creation Test Script
 * Veritabanı oluşturma ve tablo yapısını test etmek için
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantı bilgileri
$host = 'gtorg.mysql.tools';
$port = 3306;
$dbname = 'gtorg_nextcode';
$username = 'gtorg_nextcode';
$password = ';849#dVEyg';

echo "<h1>NextCode Group - Veritabanı Test Scripti</h1>";
echo "<hr>";

try {
    // Veritabanı bağlantısını test et
    echo "<h2>1. Veritabanı Bağlantı Testi</h2>";
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "✅ Veritabanı bağlantısı başarılı<br>";
    
    // Veritabanını oluştur
    echo "<h2>2. Veritabanı Oluşturma</h2>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Veritabanı '$dbname' oluşturuldu/kontrol edildi<br>";
    
    // Veritabanını seç
    $pdo->exec("USE `$dbname`");
    echo "✅ Veritabanı seçildi<br>";
    
    // SQL dosyasını oku ve çalıştır
    echo "<h2>3. Tablo Yapısı Oluşturma</h2>";
    $sqlFile = __DIR__ . '/complete_database_structure.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL dosyası bulunamadı: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // SQL komutlarını ayır ve çalıştır
    $statements = explode(';', $sql);
    $tableCount = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        // CREATE TABLE komutlarını say
        if (stripos($statement, 'CREATE TABLE') !== false) {
            $tableCount++;
        }
        
        try {
            $pdo->exec($statement);
        } catch (PDOException $e) {
            // Hata varsa göster ama devam et
            echo "⚠️ SQL Hatası: " . $e->getMessage() . "<br>";
        }
    }
    
    echo "✅ $tableCount tablo oluşturuldu/kontrol edildi<br>";
    
    // Tabloları listele
    echo "<h2>4. Oluşturulan Tablolar</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>✅ $table</li>";
    }
    echo "</ul>";
    
    // Örnek veri ekle
    echo "<h2>5. Örnek Veri Ekleme</h2>";
    $sampleDataFile = __DIR__ . '/insert_sample_data.sql';
    
    if (file_exists($sampleDataFile)) {
        $sampleSql = file_get_contents($sampleDataFile);
        $sampleStatements = explode(';', $sampleSql);
        
        $insertCount = 0;
        foreach ($sampleStatements as $statement) {
            $statement = trim($statement);
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }
            
            if (stripos($statement, 'INSERT') !== false) {
                $insertCount++;
            }
            
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                echo "⚠️ Veri Ekleme Hatası: " . $e->getMessage() . "<br>";
            }
        }
        
        echo "✅ $insertCount veri ekleme komutu çalıştırıldı<br>";
    } else {
        echo "⚠️ Örnek veri dosyası bulunamadı<br>";
    }
    
    // Veri sayılarını kontrol et
    echo "<h2>6. Veri Kontrolü</h2>";
    $tableChecks = [
        'site_settings' => 'Site Ayarları',
        'site_statistics' => 'Site İstatistikleri',
        'services' => 'Hizmetler',
        'portfolio_categories' => 'Portfolio Kategorileri',
        'portfolio_projects' => 'Portfolio Projeleri',
        'blog_categories' => 'Blog Kategorileri',
        'blog_posts' => 'Blog Yazıları',
        'pricing_packages' => 'Fiyatlandırma Paketleri'
    ];
    
    foreach ($tableChecks as $table => $description) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $stmt->fetch()['count'];
            echo "✅ $description: $count kayıt<br>";
        } catch (PDOException $e) {
            echo "❌ $description: Tablo bulunamadı veya hata<br>";
        }
    }
    
    // Bağlantıyı kapat
    $pdo = null;
    
    echo "<hr>";
    echo "<h2>✅ Test Tamamlandı!</h2>";
    echo "<p>Veritabanı başarıyla oluşturuldu ve test edildi.</p>";
    echo "<p><strong>Sonraki Adımlar:</strong></p>";
    echo "<ul>";
    echo "<li>phpMyAdmin'de veritabanını kontrol edin</li>";
    echo "<li>Web sitesini test edin</li>";
    echo "<li>Gerekirse ek veri ekleyin</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Veritabanı Hatası</h2>";
    echo "<p>Hata: " . $e->getMessage() . "</p>";
    echo "<p><strong>Kontrol Edilecekler:</strong></p>";
    echo "<ul>";
    echo "<li>Veritabanı bağlantı bilgileri</li>";
    echo "<li>MySQL sunucu durumu</li>";
    echo "<li>Kullanıcı izinleri</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<h2>❌ Genel Hata</h2>";
    echo "<p>Hata: " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}
h1, h2 {
    color: #333;
}
ul {
    list-style-type: none;
    padding-left: 0;
}
li {
    margin: 5px 0;
    padding: 5px;
    border-radius: 3px;
}
hr {
    border: none;
    border-top: 2px solid #ddd;
    margin: 20px 0;
}
</style>
