<?php
/**
 * Portfolio Database Check and Fix
 * NextCode Group - Portfolio Data Management
 */

session_start();

// Simple auth check
if (!isset($_SESSION['simple_admin_logged_in']) || !$_SESSION['simple_admin_logged_in']) {
    header('Location: simple-login.php');
    exit();
}

try {
    // Database connection
    $pdo = new PDO(
        'mysql:host=gtorg.mysql.tools;dbname=gtorg_nextcode;charset=utf8mb4',
        'gtorg_nextcode',
        ';849#dVEyg',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    echo "<h1>Portfolio Database Check</h1>";
    
    // Check if portfolio tables exist
    $tables = ['portfolio_categories', 'portfolio_projects'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<h2>✅ Table '$table' exists</h2>";
            
            // Get table structure
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->fetchAll();
            
            echo "<h3>Table Structure:</h3>";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            foreach ($columns as $col) {
                echo "<tr>";
                echo "<td>" . $col['Field'] . "</td>";
                echo "<td>" . $col['Type'] . "</td>";
                echo "<td>" . $col['Null'] . "</td>";
                echo "<td>" . $col['Key'] . "</td>";
                echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . $col['Extra'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Get record count
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            echo "<p><strong>Records:</strong> $count</p>";
            
            if ($count > 0) {
                // Show sample data
                $stmt = $pdo->query("SELECT * FROM $table LIMIT 3");
                $samples = $stmt->fetchAll();
                
                echo "<h3>Sample Data:</h3>";
                echo "<pre>" . print_r($samples, true) . "</pre>";
            }
            
        } else {
            echo "<h2>❌ Table '$table' does not exist</h2>";
        }
    }
    
    // Create missing tables if needed
    if ($_POST['create_tables'] ?? false) {
        echo "<h2>Creating Portfolio Tables...</h2>";
        
        // Create portfolio_categories table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS portfolio_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                color VARCHAR(7) DEFAULT '#2563eb',
                description TEXT,
                is_active TINYINT(1) DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Create portfolio_projects table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS portfolio_projects (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                short_description TEXT,
                full_description LONGTEXT,
                featured_image VARCHAR(500),
                category_id INT,
                category_name VARCHAR(255),
                technologies TEXT,
                project_url VARCHAR(500),
                github_url VARCHAR(500),
                client_name VARCHAR(255),
                project_date DATE,
                is_published TINYINT(1) DEFAULT 1,
                active TINYINT(1) DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES portfolio_categories(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        echo "<p>✅ Tables created successfully!</p>";
        
        // Insert sample data
        $pdo->exec("
            INSERT IGNORE INTO portfolio_categories (id, name, slug, color, description) VALUES
            (1, 'Web Tasarım', 'web-tasarim', '#2563eb', 'Modern ve responsive web tasarımları'),
            (2, 'E-Ticaret', 'e-ticaret', '#059669', 'Online satış platformları'),
            (3, 'Mobil Uygulama', 'mobil-uygulama', '#dc2626', 'iOS ve Android uygulamaları'),
            (4, 'Kurumsal', 'kurumsal', '#7c3aed', 'Kurumsal web siteleri'),
            (5, 'AI & Makine Öğrenmesi', 'ai-makine-ogrenmesi', '#ea580c', 'Yapay zeka projeleri')
        ");
        
        $pdo->exec("
            INSERT IGNORE INTO portfolio_projects (id, title, slug, short_description, category_id, category_name, technologies, featured_image) VALUES
            (1, 'Modern Kurumsal Web Sitesi', 'modern-kurumsal-web-sitesi', 'Teknoloji şirketi için modern, responsive ve SEO uyumlu kurumsal web sitesi tasarımı.', 1, 'Web Tasarım', 'HTML5,CSS3,JavaScript,PHP', '/images/portfolio/web-design-1.jpg'),
            (2, 'AI Destekli Müşteri Analiz Platformu', 'ai-destekli-musteri-analiz-platformu', 'Büyük veri analizi ve yapay zeka kullanarak müşteri davranışlarını analiz eden platform.', 5, 'AI & Makine Öğrenmesi', 'Python,TensorFlow,Apache Spark,MySQL', '/images/portfolio/ai-platform-1.jpg'),
            (3, 'E-Ticaret Platformu', 'e-ticaret-platformu', 'Giyim markası için kapsamlı e-ticaret çözümü. Ödeme entegrasyonları, stok yönetimi ile birlikte.', 2, 'E-Ticaret', 'React,Node.js,MongoDB,Stripe', '/images/portfolio/ecommerce-1.jpg'),
            (4, 'Mobil Fitness Uygulaması', 'mobil-fitness-uygulamasi', 'Kişiselleştirilmiş antrenman programları, beslenme takibi ve sosyal özellikler içeren fitness uygulaması.', 3, 'Mobil Uygulama', 'React Native,Firebase,Node.js', '/images/portfolio/mobile-app-1.jpg'),
            (5, 'SaaS CRM Sistemi', 'saas-crm-sistemi', 'Küçük ve orta ölçekli işletmeler için bulut tabanlı müşteri ilişkileri yönetim sistemi.', 4, 'Kurumsal', 'Angular,Spring Boot,PostgreSQL', '/images/portfolio/crm-system-1.jpg')
        ");
        
        echo "<p>✅ Sample data inserted successfully!</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Database Error:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Database Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #667eea; }
        h2 { color: #333; margin-top: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 10px 0; }
        .btn:hover { background: #5a67d8; }
    </style>
</head>
<body>
    <form method="POST">
        <button type="submit" name="create_tables" class="btn">Create Portfolio Tables & Sample Data</button>
    </form>
    
    <p><a href="simple-dashboard.php" style="color: #667eea;">← Back to Dashboard</a></p>
</body>
</html>
