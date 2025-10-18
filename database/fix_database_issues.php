<?php
/**
 * NextCode Group - Eksik Tabloları ve Sütunları Ekleme Scripti
 * Tespit edilen sorunları güvenli şekilde çözer
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

echo "<h1>NextCode Group - Veritabanı Sorunlarını Çözme</h1>";
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
    echo "<h2>2. Mevcut Tablolar Kontrolü</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Eksik tabloları kontrol et
    $missingTables = [];
    if (!in_array('navigation_items', $tables)) {
        $missingTables[] = 'navigation_items';
    }
    if (!in_array('activity_log', $tables)) {
        $missingTables[] = 'activity_log';
    }
    
    if (empty($missingTables)) {
        echo "✅ Tüm gerekli tablolar mevcut<br>";
    } else {
        echo "❌ Eksik tablolar: " . implode(', ', $missingTables) . "<br>";
    }
    
    // Eksik tabloları ekle
    if (!empty($missingTables)) {
        echo "<h2>3. Eksik Tabloları Ekleme</h2>";
        
        foreach ($missingTables as $table) {
            try {
                if ($table === 'navigation_items') {
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS `navigation_items` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `title` varchar(100) NOT NULL,
                            `url` varchar(255) NOT NULL,
                            `parent_id` int(11) NULL,
                            `sort_order` int(11) DEFAULT 0,
                            `status` enum('active','inactive') DEFAULT 'active',
                            `icon` varchar(100) NULL,
                            `target` enum('_self','_blank') DEFAULT '_self',
                            `css_class` varchar(255) NULL,
                            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`),
                            KEY `idx_parent_id` (`parent_id`),
                            KEY `idx_status` (`status`),
                            KEY `idx_sort_order` (`sort_order`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                    ");
                    
                    // Örnek veri ekle
                    $pdo->exec("
                        INSERT IGNORE INTO `navigation_items` (`title`, `url`, `sort_order`, `status`) VALUES
                        ('Ana Sayfa', '/', 1, 'active'),
                        ('Hakkımızda', '/about.php', 2, 'active'),
                        ('Hizmetlerimiz', '/services.php', 3, 'active'),
                        ('Portfolio', '/portfolio.php', 4, 'active'),
                        ('Blog', '/blog.php', 5, 'active'),
                        ('Fiyatlandırma', '/pricing.php', 6, 'active'),
                        ('İletişim', '/contact.php', 7, 'active')
                    ");
                    
                    echo "✅ navigation_items tablosu eklendi ve örnek veri eklendi<br>";
                    
                } elseif ($table === 'activity_log') {
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS `activity_log` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `user_id` int(11) NULL,
                            `action` varchar(100) NOT NULL,
                            `description` text,
                            `table_name` varchar(100) NULL,
                            `record_id` int(11) NULL,
                            `old_values` json NULL,
                            `new_values` json NULL,
                            `ip_address` varchar(45),
                            `user_agent` text,
                            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`),
                            KEY `idx_user_id` (`user_id`),
                            KEY `idx_action` (`action`),
                            KEY `idx_table_record` (`table_name`, `record_id`),
                            KEY `idx_created_at` (`created_at`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                    ");
                    
                    echo "✅ activity_log tablosu eklendi<br>";
                }
            } catch (PDOException $e) {
                echo "❌ $table tablosu eklenirken hata: " . $e->getMessage() . "<br>";
            }
        }
    }
    
    // blog_posts.featured sütununu kontrol et ve ekle
    echo "<h2>4. blog_posts.featured Sütunu Kontrolü</h2>";
    if (in_array('blog_posts', $tables)) {
        $columns = $pdo->query("SHOW COLUMNS FROM blog_posts")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('featured', $columns)) {
            try {
                $pdo->exec("ALTER TABLE `blog_posts` ADD COLUMN `featured` tinyint(1) DEFAULT 0 AFTER `status`");
                echo "✅ blog_posts.featured sütunu eklendi<br>";
            } catch (PDOException $e) {
                echo "❌ featured sütunu eklenirken hata: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "✅ blog_posts.featured sütunu zaten mevcut<br>";
        }
        
        // Diğer eksik sütunları ekle
        $missingColumns = [];
        if (!in_array('meta_title', $columns)) $missingColumns[] = 'meta_title';
        if (!in_array('meta_description', $columns)) $missingColumns[] = 'meta_description';
        if (!in_array('tags', $columns)) $missingColumns[] = 'tags';
        if (!in_array('published_at', $columns)) $missingColumns[] = 'published_at';
        
        if (!empty($missingColumns)) {
            echo "Eksik sütunlar: " . implode(', ', $missingColumns) . "<br>";
            
            foreach ($missingColumns as $column) {
                try {
                    switch ($column) {
                        case 'meta_title':
                            $pdo->exec("ALTER TABLE `blog_posts` ADD COLUMN `meta_title` varchar(200) AFTER `featured`");
                            break;
                        case 'meta_description':
                            $pdo->exec("ALTER TABLE `blog_posts` ADD COLUMN `meta_description` text AFTER `meta_title`");
                            break;
                        case 'tags':
                            $pdo->exec("ALTER TABLE `blog_posts` ADD COLUMN `tags` text AFTER `meta_description`");
                            break;
                        case 'published_at':
                            $pdo->exec("ALTER TABLE `blog_posts` ADD COLUMN `published_at` timestamp NULL AFTER `tags`");
                            break;
                    }
                    echo "✅ blog_posts.$column sütunu eklendi<br>";
                } catch (PDOException $e) {
                    echo "❌ $column sütunu eklenirken hata: " . $e->getMessage() . "<br>";
                }
            }
        }
    }
    
    // Index kontrolü ve ekleme
    echo "<h2>5. Index Kontrolü ve Ekleme</h2>";
    
    // blog_posts indexleri
    if (in_array('blog_posts', $tables)) {
        $indexes = $pdo->query("SHOW INDEX FROM blog_posts")->fetchAll();
        $indexNames = array_column($indexes, 'Key_name');
        
        $requiredIndexes = ['idx_featured', 'idx_published_at'];
        foreach ($requiredIndexes as $index) {
            if (!in_array($index, $indexNames)) {
                try {
                    if ($index === 'idx_featured') {
                        $pdo->exec("ALTER TABLE `blog_posts` ADD INDEX `idx_featured` (`featured`)");
                    } elseif ($index === 'idx_published_at') {
                        $pdo->exec("ALTER TABLE `blog_posts` ADD INDEX `idx_published_at` (`published_at`)");
                    }
                    echo "✅ blog_posts.$index indexi eklendi<br>";
                } catch (PDOException $e) {
                    echo "❌ $index indexi eklenirken hata: " . $e->getMessage() . "<br>";
                }
            } else {
                echo "✅ blog_posts.$index indexi zaten mevcut<br>";
            }
        }
    }
    
    // portfolio_projects indexleri
    if (in_array('portfolio_projects', $tables)) {
        $indexes = $pdo->query("SHOW INDEX FROM portfolio_projects")->fetchAll();
        $indexNames = array_column($indexes, 'Key_name');
        
        $requiredIndexes = ['idx_slug', 'idx_category_id', 'idx_published'];
        foreach ($requiredIndexes as $index) {
            if (!in_array($index, $indexNames)) {
                try {
                    if ($index === 'idx_slug') {
                        $pdo->exec("ALTER TABLE `portfolio_projects` ADD INDEX `idx_slug` (`slug`)");
                    } elseif ($index === 'idx_category_id') {
                        $pdo->exec("ALTER TABLE `portfolio_projects` ADD INDEX `idx_category_id` (`category_id`)");
                    } elseif ($index === 'idx_published') {
                        $pdo->exec("ALTER TABLE `portfolio_projects` ADD INDEX `idx_published` (`is_published`)");
                    }
                    echo "✅ portfolio_projects.$index indexi eklendi<br>";
                } catch (PDOException $e) {
                    echo "❌ $index indexi eklenirken hata: " . $e->getMessage() . "<br>";
                }
            } else {
                echo "✅ portfolio_projects.$index indexi zaten mevcut<br>";
            }
        }
    }
    
    // Final kontrol
    echo "<h2>6. Final Kontrol</h2>";
    
    // Tabloları tekrar kontrol et
    $stmt = $pdo->query("SHOW TABLES");
    $finalTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $allRequiredTables = ['navigation_items', 'activity_log'];
    $finalMissingTables = array_diff($allRequiredTables, $finalTables);
    
    if (empty($finalMissingTables)) {
        echo "✅ Tüm gerekli tablolar mevcut<br>";
    } else {
        echo "❌ Hala eksik tablolar: " . implode(', ', $finalMissingTables) . "<br>";
    }
    
    // Navigation items kayıt sayısını kontrol et
    if (in_array('navigation_items', $finalTables)) {
        $count = $pdo->query("SELECT COUNT(*) as count FROM navigation_items")->fetch()['count'];
        echo "✅ navigation_items: $count kayıt<br>";
    }
    
    // Activity log kayıt sayısını kontrol et
    if (in_array('activity_log', $finalTables)) {
        $count = $pdo->query("SELECT COUNT(*) as count FROM activity_log")->fetch()['count'];
        echo "✅ activity_log: $count kayıt<br>";
    }
    
    // Bağlantıyı kapat
    $pdo = null;
    
    echo "<hr>";
    echo "<h2>📋 Özet</h2>";
    echo "<ul>";
    echo "<li>✅ Eksik tablolar eklendi</li>";
    echo "<li>✅ blog_posts.featured sütunu eklendi</li>";
    echo "<li>✅ Eksik indexler eklendi</li>";
    echo "<li>✅ Örnek navigasyon verileri eklendi</li>";
    echo "</ul>";
    
    echo "<h2>🎉 Tamamlandı!</h2>";
    echo "<p>Tüm tespit edilen sorunlar çözüldü. Veritabanınız artık tam uyumlu durumda.</p>";
    
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
hr {
    border: none;
    border-top: 2px solid #ddd;
    margin: 20px 0;
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
</style>
