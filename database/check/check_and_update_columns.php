<?php
/**
 * NextCode Group - Sütun Kontrol ve Güncelleme Scripti
 * Mevcut sütunları kontrol eder ve sadece eksik olanları ekler
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

echo "<h1>NextCode Group - Sütun Kontrol ve Güncelleme</h1>";
echo "<hr>";

try {
    // Veritabanı bağlantısı
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ Veritabanı bağlantısı başarılı<br>";
    
    // Kontrol edilecek tablolar ve sütunlar
    $tableUpdates = [
        'portfolio_projects' => [
            'slug' => 'varchar(255) UNIQUE AFTER `title`',
            'short_description' => 'text AFTER `slug`',
            'category_id' => 'int(11) AFTER `short_description`',
            'featured_image' => 'varchar(500) AFTER `category_id`',
            'project_date' => 'date AFTER `featured_image`',
            'completion_date' => 'date AFTER `project_date`',
            'project_status' => "enum('planning','in_progress','completed','on_hold') DEFAULT 'completed' AFTER `completion_date`",
            'budget' => 'decimal(10,2) AFTER `project_status`',
            'team_size' => 'int(11) AFTER `budget`',
            'duration_months' => 'int(11) AFTER `team_size`',
            'key_results' => 'text AFTER `duration_months`',
            'challenges_solved' => 'text AFTER `key_results`',
            'lessons_learned' => 'text AFTER `challenges_solved`',
            'is_featured' => 'tinyint(1) DEFAULT 0 AFTER `lessons_learned`',
            'is_published' => 'tinyint(1) DEFAULT 1 AFTER `is_featured`',
            'view_count' => 'int(11) DEFAULT 0 AFTER `is_published`',
            'seo_title' => 'varchar(255) AFTER `view_count`',
            'seo_description' => 'text AFTER `seo_title`',
            'seo_keywords' => 'varchar(500) AFTER `seo_description`'
        ],
        'blog_posts' => [
            'featured' => 'tinyint(1) DEFAULT 0 AFTER `status`',
            'meta_title' => 'varchar(200) AFTER `featured`',
            'meta_description' => 'text AFTER `meta_title`',
            'tags' => 'text AFTER `meta_description`',
            'published_at' => 'timestamp NULL AFTER `tags`'
        ],
        'contact_messages' => [
            'first_name' => 'varchar(100) AFTER `id`',
            'last_name' => 'varchar(100) AFTER `first_name`',
            'subject' => 'varchar(200) AFTER `phone`',
            'status' => "enum('unread','read','archived') DEFAULT 'unread' AFTER `message`"
        ],
        'pricing_packages' => [
            'slug' => 'varchar(100) UNIQUE AFTER `name`',
            'billing_period' => "varchar(20) DEFAULT 'monthly' AFTER `currency`",
            'discount_percentage' => 'int(11) DEFAULT 0 AFTER `billing_period`',
            'is_featured' => 'tinyint(1) DEFAULT 0 AFTER `is_popular`',
            'button_text' => "varchar(50) DEFAULT 'Seç' AFTER `sort_order`",
            'button_link' => 'varchar(255) AFTER `button_text`'
        ]
    ];
    
    // Kontrol edilecek indexler
    $indexUpdates = [
        'portfolio_projects' => [
            'idx_slug' => '(`slug`)',
            'idx_category_id' => '(`category_id`)',
            'idx_featured' => '(`is_featured`)',
            'idx_published' => '(`is_published`)'
        ],
        'blog_posts' => [
            'idx_featured' => '(`featured`)',
            'idx_published_at' => '(`published_at`)'
        ],
        'contact_messages' => [
            'idx_status' => '(`status`)',
            'idx_created_at' => '(`created_at`)'
        ],
        'pricing_packages' => [
            'idx_slug' => '(`slug`)'
        ]
    ];
    
    $totalAdded = 0;
    $totalSkipped = 0;
    
    foreach ($tableUpdates as $table => $columns) {
        echo "<h2>$table Tablosu</h2>";
        
        // Tablo var mı kontrol et
        try {
            $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
        } catch (PDOException $e) {
            echo "❌ Tablo bulunamadı: $table<br>";
            continue;
        }
        
        // Mevcut sütunları al
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
        $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>Eksik Sütunlar:</h3>";
        $addedColumns = [];
        
        foreach ($columns as $column => $definition) {
            if (!in_array($column, $existingColumns)) {
                try {
                    $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
                    $pdo->exec($sql);
                    echo "✅ Eklendi: $column<br>";
                    $addedColumns[] = $column;
                    $totalAdded++;
                } catch (PDOException $e) {
                    echo "❌ Hata ($column): " . $e->getMessage() . "<br>";
                }
            } else {
                echo "⏭️ Zaten mevcut: $column<br>";
                $totalSkipped++;
            }
        }
        
        // Indexleri kontrol et ve ekle
        if (isset($indexUpdates[$table]) && !empty($addedColumns)) {
            echo "<h3>Indexler:</h3>";
            
            // Mevcut indexleri al
            $stmt = $pdo->query("SHOW INDEX FROM `$table`");
            $existingIndexes = array_column($stmt->fetchAll(), 'Key_name');
            
            foreach ($indexUpdates[$table] as $indexName => $indexDefinition) {
                if (!in_array($indexName, $existingIndexes)) {
                    try {
                        $sql = "ALTER TABLE `$table` ADD INDEX `$indexName` $indexDefinition";
                        $pdo->exec($sql);
                        echo "✅ Index eklendi: $indexName<br>";
                    } catch (PDOException $e) {
                        echo "❌ Index hatası ($indexName): " . $e->getMessage() . "<br>";
                    }
                } else {
                    echo "⏭️ Index zaten mevcut: $indexName<br>";
                }
            }
        }
        
        echo "<hr>";
    }
    
    // Özet
    echo "<h2>📊 Özet</h2>";
    echo "<ul>";
    echo "<li>✅ Eklenen sütun: $totalAdded</li>";
    echo "<li>⏭️ Zaten mevcut: $totalSkipped</li>";
    echo "</ul>";
    
    // Güncellenen SQL dosyası oluştur
    echo "<h2>📝 Güncellenmiş SQL Dosyası</h2>";
    $sqlContent = "-- NextCode Group - Güncellenmiş Veritabanı Yapısı\n";
    $sqlContent .= "-- Bu dosya mevcut sütunları kontrol ederek sadece eksik olanları ekler\n\n";
    $sqlContent .= "USE `gtorg_nextcode`;\n\n";
    
    foreach ($tableUpdates as $table => $columns) {
        $sqlContent .= "-- $table tablosu için eksik sütunlar\n";
        foreach ($columns as $column => $definition) {
            $sqlContent .= "-- ALTER TABLE `$table` ADD COLUMN `$column` $definition;\n";
        }
        $sqlContent .= "\n";
    }
    
    file_put_contents(__DIR__ . '/generated_update.sql', $sqlContent);
    echo "✅ Güncellenmiş SQL dosyası oluşturuldu: <code>database/generated_update.sql</code><br>";
    
    echo "<h2>✅ İşlem Tamamlandı!</h2>";
    echo "<p>Veritabanı yapısı başarıyla güncellendi.</p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Veritabanı Hatası</h2>";
    echo "<p>Hata: " . $e->getMessage() . "</p>";
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


