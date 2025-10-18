<?php
/**
 * NextCode Group - Database Structure Checker
 * Mevcut veritabanı yapısını kontrol eder ve eksik tabloları/sütunları raporlar
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

echo "<h1>NextCode Group - Veritabanı Yapı Kontrolü</h1>";
echo "<hr>";

try {
    // Veritabanı bağlantısını test et
    echo "<h2>1. Veritabanı Bağlantı Testi</h2>";
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "✅ Veritabanı bağlantısı başarılı<br>";
    
    // Mevcut tabloları listele
    echo "<h2>2. Mevcut Tablolar</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Sıra</th><th>Tablo Adı</th><th>Kayıt Sayısı</th><th>Durum</th></tr>";
    
    $requiredTables = [
        'site_settings',
        'site_statistics', 
        'site_content',
        'pages',
        'services',
        'portfolio_categories',
        'portfolio_projects',
        'portfolio_images',
        'portfolio_testimonials',
        'blog_categories',
        'blog_posts',
        'contact_messages',
        'newsletter_subscribers',
        'pricing_packages',
        'package_features',
        'media_files',
        'navigation_items',
        'activity_log'
    ];
    
    $existingTables = [];
    foreach ($tables as $index => $table) {
        try {
            $countStmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $countStmt->fetch()['count'];
            $existingTables[] = $table;
            
            $status = in_array($table, $requiredTables) ? '✅ Gerekli' : '⚠️ Ekstra';
            echo "<tr><td>" . ($index + 1) . "</td><td>$table</td><td>$count</td><td>$status</td></tr>";
        } catch (PDOException $e) {
            echo "<tr><td>" . ($index + 1) . "</td><td>$table</td><td>Hata</td><td>❌ Sorunlu</td></tr>";
        }
    }
    echo "</table>";
    
    // Eksik tabloları kontrol et
    echo "<h2>3. Eksik Tablolar</h2>";
    $missingTables = array_diff($requiredTables, $existingTables);
    
    if (empty($missingTables)) {
        echo "✅ Tüm gerekli tablolar mevcut<br>";
    } else {
        echo "❌ Eksik tablolar:<br>";
        echo "<ul>";
        foreach ($missingTables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    }
    
    // Mevcut tabloların yapısını kontrol et
    echo "<h2>4. Tablo Yapı Kontrolü</h2>";
    
    // portfolio_projects tablosu kontrolü
    if (in_array('portfolio_projects', $existingTables)) {
        echo "<h3>portfolio_projects Tablosu</h3>";
        $columns = $pdo->query("SHOW COLUMNS FROM portfolio_projects")->fetchAll(PDO::FETCH_COLUMN);
        
        $requiredColumns = ['slug', 'short_description', 'category_id', 'featured_image', 'is_featured', 'is_published'];
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "✅ Tüm gerekli sütunlar mevcut<br>";
        } else {
            echo "❌ Eksik sütunlar:<br>";
            echo "<ul>";
            foreach ($missingColumns as $column) {
                echo "<li>$column</li>";
            }
            echo "</ul>";
        }
    }
    
    // blog_posts tablosu kontrolü
    if (in_array('blog_posts', $existingTables)) {
        echo "<h3>blog_posts Tablosu</h3>";
        $columns = $pdo->query("SHOW COLUMNS FROM blog_posts")->fetchAll(PDO::FETCH_COLUMN);
        
        $requiredColumns = ['featured', 'meta_title', 'meta_description', 'tags', 'published_at'];
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "✅ Tüm gerekli sütunlar mevcut<br>";
        } else {
            echo "❌ Eksik sütunlar:<br>";
            echo "<ul>";
            foreach ($missingColumns as $column) {
                echo "<li>$column</li>";
            }
            echo "</ul>";
        }
    }
    
    // contact_messages tablosu kontrolü
    if (in_array('contact_messages', $existingTables)) {
        echo "<h3>contact_messages Tablosu</h3>";
        $columns = $pdo->query("SHOW COLUMNS FROM contact_messages")->fetchAll(PDO::FETCH_COLUMN);
        
        $requiredColumns = ['first_name', 'last_name', 'subject', 'status'];
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "✅ Tüm gerekli sütunlar mevcut<br>";
        } else {
            echo "❌ Eksik sütunlar:<br>";
            echo "<ul>";
            foreach ($missingColumns as $column) {
                echo "<li>$column</li>";
            }
            echo "</ul>";
        }
    }
    
    // Index kontrolü
    echo "<h2>5. Index Kontrolü</h2>";
    if (in_array('portfolio_projects', $existingTables)) {
        $indexes = $pdo->query("SHOW INDEX FROM portfolio_projects")->fetchAll();
        $indexNames = array_column($indexes, 'Key_name');
        
        $requiredIndexes = ['idx_slug', 'idx_category_id', 'idx_featured', 'idx_published'];
        $missingIndexes = array_diff($requiredIndexes, $indexNames);
        
        if (empty($missingIndexes)) {
            echo "✅ portfolio_projects tablosu indexleri tamam<br>";
        } else {
            echo "❌ Eksik indexler:<br>";
            echo "<ul>";
            foreach ($missingIndexes as $index) {
                echo "<li>$index</li>";
            }
            echo "</ul>";
        }
    }
    
    // Veri kontrolü
    echo "<h2>6. Örnek Veri Kontrolü</h2>";
    $dataChecks = [
        'site_settings' => 'Site ayarları',
        'blog_categories' => 'Blog kategorileri',
        'services' => 'Hizmetler',
        'portfolio_categories' => 'Portfolio kategorileri',
        'pricing_packages' => 'Fiyatlandırma paketleri'
    ];
    
    foreach ($dataChecks as $table => $description) {
        if (in_array($table, $existingTables)) {
            try {
                $countStmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
                $count = $countStmt->fetch()['count'];
                
                if ($count > 0) {
                    echo "✅ $description: $count kayıt<br>";
                } else {
                    echo "⚠️ $description: Veri yok<br>";
                }
            } catch (PDOException $e) {
                echo "❌ $description: Kontrol edilemedi<br>";
            }
        }
    }
    
    // Bağlantıyı kapat
    $pdo = null;
    
    echo "<hr>";
    echo "<h2>📋 Öneriler</h2>";
    echo "<ul>";
    echo "<li>Eksik tablolar için <code>database/update_existing_database.sql</code> dosyasını çalıştırın</li>";
    echo "<li>Yeni veritabanı kurulumu için <code>database/complete_database_structure.sql</code> dosyasını kullanın</li>";
    echo "<li>Örnek veri eklemek için <code>database/insert_sample_data.sql</code> dosyasını çalıştırın</li>";
    echo "<li>Test için <code>database/test_database_creation.php</code> dosyasını kullanın</li>";
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
h1, h2, h3 {
    color: #333;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0;
}
th, td {
    text-align: left;
    padding: 8px;
}
th {
    background-color: #f2f2f2;
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
code {
    background-color: #f4f4f4;
    padding: 2px 4px;
    border-radius: 3px;
    font-family: monospace;
}
</style>


