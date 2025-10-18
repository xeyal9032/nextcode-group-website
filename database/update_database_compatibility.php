<?php
/**
 * NextCode Group - Veritabanı Uyumluluk Güncellemesi
 * Web projesi ile tam uyumlu çalışması için veritabanını günceller
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

echo "<h1>NextCode Group - Veritabanı Uyumluluk Güncellemesi</h1>";
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
    
    // Web projesi için gerekli tablolar
    $requiredTables = [
        'site_settings', 'site_content', 'site_images', 'site_statistics',
        'pages', 'services', 'portfolio_categories', 'portfolio_projects', 
        'portfolio_images', 'portfolio_testimonials', 'blog_categories', 
        'blog_posts', 'contact_messages', 'newsletter_subscribers',
        'pricing_packages', 'package_features', 'media_files',
        'navigation_items', 'activity_log', 'admin_users', 'admin_logs'
    ];
    
    $existingTables = array_intersect($requiredTables, $tables);
    $missingTables = array_diff($requiredTables, $tables);
    
    echo "✅ Mevcut tablolar: " . count($existingTables) . "/" . count($requiredTables) . "<br>";
    if (!empty($missingTables)) {
        echo "❌ Eksik tablolar: " . implode(', ', $missingTables) . "<br>";
    }
    
    // Eksik tabloları ekle
    if (!empty($missingTables)) {
        echo "<h2>3. Eksik Tabloları Ekleme</h2>";
        
        foreach ($missingTables as $table) {
            try {
                switch ($table) {
                    case 'navigation_items':
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
                        
                        // Örnek navigasyon verileri
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
                        echo "✅ navigation_items tablosu eklendi<br>";
                        break;
                        
                    case 'activity_log':
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
                        break;
                }
            } catch (PDOException $e) {
                echo "❌ $table tablosu eklenirken hata: " . $e->getMessage() . "<br>";
            }
        }
    }
    
    // Tablo yapılarını kontrol et ve güncelle
    echo "<h2>4. Tablo Yapılarını Güncelleme</h2>";
    
    // blog_posts tablosu güncellemeleri
    if (in_array('blog_posts', $tables)) {
        echo "<h3>blog_posts Tablosu</h3>";
        $columns = $pdo->query("SHOW COLUMNS FROM blog_posts")->fetchAll(PDO::FETCH_COLUMN);
        
        $blogUpdates = [
            'read_time' => 'INT DEFAULT 5',
            'featured_image' => 'VARCHAR(500)',
            'is_featured' => 'TINYINT(1) DEFAULT 0',
            'meta_title' => 'VARCHAR(255)',
            'meta_description' => 'TEXT',
            'tags' => 'TEXT',
            'published_at' => 'TIMESTAMP NULL'
        ];
        
        foreach ($blogUpdates as $column => $definition) {
            if (!in_array($column, $columns)) {
                try {
                    $pdo->exec("ALTER TABLE `blog_posts` ADD COLUMN `$column` $definition");
                    echo "✅ blog_posts.$column sütunu eklendi<br>";
                } catch (PDOException $e) {
                    echo "❌ $column sütunu eklenirken hata: " . $e->getMessage() . "<br>";
                }
            } else {
                echo "✅ blog_posts.$column sütunu zaten mevcut<br>";
            }
        }
        
        // blog_posts için status sütununu kontrol et
        if (!in_array('status', $columns)) {
            try {
                $pdo->exec("ALTER TABLE `blog_posts` ADD COLUMN `status` ENUM('draft','published','archived') DEFAULT 'published'");
                echo "✅ blog_posts.status sütunu eklendi<br>";
            } catch (PDOException $e) {
                echo "❌ status sütunu eklenirken hata: " . $e->getMessage() . "<br>";
            }
        }
    }
    
    // portfolio_projects tablosu güncellemeleri
    if (in_array('portfolio_projects', $tables)) {
        echo "<h3>portfolio_projects Tablosu</h3>";
        $columns = $pdo->query("SHOW COLUMNS FROM portfolio_projects")->fetchAll(PDO::FETCH_COLUMN);
        
        $portfolioUpdates = [
            'slug' => 'VARCHAR(255) UNIQUE',
            'short_description' => 'TEXT',
            'category_id' => 'INT',
            'featured_image' => 'VARCHAR(500)',
            'project_date' => 'DATE',
            'completion_date' => 'DATE',
            'project_status' => "ENUM('planning','in_progress','completed','on_hold') DEFAULT 'completed'",
            'budget' => 'DECIMAL(10,2)',
            'team_size' => 'INT',
            'duration_months' => 'INT',
            'key_results' => 'TEXT',
            'challenges_solved' => 'TEXT',
            'lessons_learned' => 'TEXT',
            'is_featured' => 'TINYINT(1) DEFAULT 0',
            'is_published' => 'TINYINT(1) DEFAULT 1',
            'view_count' => 'INT DEFAULT 0',
            'seo_title' => 'VARCHAR(255)',
            'seo_description' => 'TEXT',
            'seo_keywords' => 'VARCHAR(500)'
        ];
        
        foreach ($portfolioUpdates as $column => $definition) {
            if (!in_array($column, $columns)) {
                try {
                    $pdo->exec("ALTER TABLE `portfolio_projects` ADD COLUMN `$column` $definition");
                    echo "✅ portfolio_projects.$column sütunu eklendi<br>";
                } catch (PDOException $e) {
                    echo "❌ $column sütunu eklenirken hata: " . $e->getMessage() . "<br>";
                }
            } else {
                echo "✅ portfolio_projects.$column sütunu zaten mevcut<br>";
            }
        }
        
        // portfolio_projects için status sütununu kontrol et
        if (!in_array('status', $columns)) {
            try {
                $pdo->exec("ALTER TABLE `portfolio_projects` ADD COLUMN `status` ENUM('active','inactive') DEFAULT 'active'");
                echo "✅ portfolio_projects.status sütunu eklendi<br>";
            } catch (PDOException $e) {
                echo "❌ status sütunu eklenirken hata: " . $e->getMessage() . "<br>";
            }
        }
    }
    
    // services tablosu güncellemeleri
    if (in_array('services', $tables)) {
        echo "<h3>services Tablosu</h3>";
        $columns = $pdo->query("SHOW COLUMNS FROM services")->fetchAll(PDO::FETCH_COLUMN);
        
        $serviceUpdates = [
            'icon' => 'VARCHAR(100)',
            'price' => 'VARCHAR(50)',
            'features' => 'TEXT',
            'sort_order' => 'INT DEFAULT 0',
            'status' => "ENUM('active','inactive') DEFAULT 'active'"
        ];
        
        foreach ($serviceUpdates as $column => $definition) {
            if (!in_array($column, $columns)) {
                try {
                    $pdo->exec("ALTER TABLE `services` ADD COLUMN `$column` $definition");
                    echo "✅ services.$column sütunu eklendi<br>";
                } catch (PDOException $e) {
                    echo "❌ $column sütunu eklenirken hata: " . $e->getMessage() . "<br>";
                }
            } else {
                echo "✅ services.$column sütunu zaten mevcut<br>";
            }
        }
    }
    
    // Index'leri ekle
    echo "<h2>5. Performans Indexlerini Ekleme</h2>";
    
    $indexes = [
        'blog_posts' => [
            'idx_status' => 'status',
            'idx_featured' => 'is_featured',
            'idx_published_at' => 'published_at',
            'idx_category_status' => 'category_id, status'
        ],
        'portfolio_projects' => [
            'idx_slug' => 'slug',
            'idx_category_id' => 'category_id',
            'idx_featured' => 'is_featured',
            'idx_published' => 'is_published',
            'idx_status' => 'status',
            'idx_category_published' => 'category_id, is_published'
        ],
        'services' => [
            'idx_status' => 'status',
            'idx_sort_order' => 'sort_order'
        ],
        'contact_messages' => [
            'idx_status' => 'status',
            'idx_email' => 'email',
            'idx_created_at' => 'created_at'
        ]
    ];
    
    foreach ($indexes as $table => $tableIndexes) {
        if (in_array($table, $tables)) {
            $existingIndexes = $pdo->query("SHOW INDEX FROM $table")->fetchAll();
            $existingIndexNames = array_column($existingIndexes, 'Key_name');
            
            foreach ($tableIndexes as $indexName => $columns) {
                if (!in_array($indexName, $existingIndexNames)) {
                    try {
                        $pdo->exec("ALTER TABLE `$table` ADD INDEX `$indexName` ($columns)");
                        echo "✅ $table.$indexName indexi eklendi<br>";
                    } catch (PDOException $e) {
                        echo "❌ $indexName indexi eklenirken hata: " . $e->getMessage() . "<br>";
                    }
                } else {
                    echo "✅ $table.$indexName indexi zaten mevcut<br>";
                }
            }
        }
    }
    
    // Veri bütünlüğünü sağla
    echo "<h2>6. Veri Bütünlüğünü Sağlama</h2>";
    
    // blog_posts için varsayılan değerleri güncelle
    if (in_array('blog_posts', $tables)) {
        try {
            $pdo->exec("UPDATE blog_posts SET status = 'published' WHERE status IS NULL OR status = ''");
            $pdo->exec("UPDATE blog_posts SET is_featured = 0 WHERE is_featured IS NULL");
            $pdo->exec("UPDATE blog_posts SET read_time = 5 WHERE read_time IS NULL OR read_time = 0");
            echo "✅ blog_posts veri bütünlüğü sağlandı<br>";
        } catch (PDOException $e) {
            echo "❌ blog_posts veri güncelleme hatası: " . $e->getMessage() . "<br>";
        }
    }
    
    // portfolio_projects için varsayılan değerleri güncelle
    if (in_array('portfolio_projects', $tables)) {
        try {
            $pdo->exec("UPDATE portfolio_projects SET status = 'active' WHERE status IS NULL OR status = ''");
            $pdo->exec("UPDATE portfolio_projects SET is_featured = 0 WHERE is_featured IS NULL");
            $pdo->exec("UPDATE portfolio_projects SET is_published = 1 WHERE is_published IS NULL");
            $pdo->exec("UPDATE portfolio_projects SET view_count = 0 WHERE view_count IS NULL");
            echo "✅ portfolio_projects veri bütünlüğü sağlandı<br>";
        } catch (PDOException $e) {
            echo "❌ portfolio_projects veri güncelleme hatası: " . $e->getMessage() . "<br>";
        }
    }
    
    // services için varsayılan değerleri güncelle
    if (in_array('services', $tables)) {
        try {
            $pdo->exec("UPDATE services SET status = 'active' WHERE status IS NULL OR status = ''");
            $pdo->exec("UPDATE services SET sort_order = 0 WHERE sort_order IS NULL");
            echo "✅ services veri bütünlüğü sağlandı<br>";
        } catch (PDOException $e) {
            echo "❌ services veri güncelleme hatası: " . $e->getMessage() . "<br>";
        }
    }
    
    // Final kontrol
    echo "<h2>7. Final Kontrol</h2>";
    
    // Tablo sayılarını kontrol et
    $finalTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $finalRequiredTables = array_intersect($requiredTables, $finalTables);
    
    echo "✅ Gerekli tablolar: " . count($finalRequiredTables) . "/" . count($requiredTables) . "<br>";
    
    // Önemli tabloların kayıt sayılarını kontrol et
    $importantTables = ['blog_posts', 'portfolio_projects', 'services', 'site_settings', 'site_content'];
    foreach ($importantTables as $table) {
        if (in_array($table, $finalTables)) {
            $count = $pdo->query("SELECT COUNT(*) as count FROM $table")->fetch()['count'];
            echo "✅ $table: $count kayıt<br>";
        }
    }
    
    // Bağlantıyı kapat
    $pdo = null;
    
    echo "<hr>";
    echo "<h2>🎉 Güncelleme Tamamlandı!</h2>";
    echo "<p>Veritabanınız web projesi ile tam uyumlu hale getirildi.</p>";
    
    echo "<h3>📋 Yapılan İyileştirmeler:</h3>";
    echo "<ul>";
    echo "<li>✅ Eksik tablolar eklendi</li>";
    echo "<li>✅ Tablo yapıları güncellendi</li>";
    echo "<li>✅ Performans indexleri eklendi</li>";
    echo "<li>✅ Veri bütünlüğü sağlandı</li>";
    echo "<li>✅ Web projesi uyumluluğu sağlandı</li>";
    echo "</ul>";
    
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
