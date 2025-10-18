<?php
// Portfolio Database Setup
$pdo = new PDO('mysql:host=gtorg.mysql.tools;dbname=gtorg_nextcode;charset=utf8mb4', 'gtorg_nextcode', ';849#dVEyg', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

echo "Creating portfolio tables...\n";

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

echo "Tables created successfully!\n";

// Insert sample categories
$pdo->exec("
    INSERT IGNORE INTO portfolio_categories (id, name, slug, color, description) VALUES
    (1, 'Web Tasarım', 'web-tasarim', '#2563eb', 'Modern ve responsive web tasarımları'),
    (2, 'E-Ticaret', 'e-ticaret', '#059669', 'Online satış platformları'),
    (3, 'Mobil Uygulama', 'mobil-uygulama', '#dc2626', 'iOS ve Android uygulamaları'),
    (4, 'Kurumsal', 'kurumsal', '#7c3aed', 'Kurumsal web siteleri'),
    (5, 'AI & Makine Öğrenmesi', 'ai-makine-ogrenmesi', '#ea580c', 'Yapay zeka projeleri')
");

echo "Categories inserted!\n";

// Insert sample projects
$pdo->exec("
    INSERT IGNORE INTO portfolio_projects (id, title, slug, short_description, category_id, technologies, featured_image) VALUES
    (1, 'Modern Kurumsal Web Sitesi', 'modern-kurumsal-web-sitesi', 'Teknoloji şirketi için modern, responsive ve SEO uyumlu kurumsal web sitesi tasarımı.', 1, 'HTML5,CSS3,JavaScript,PHP', '/images/portfolio/web-design-1.jpg'),
    (2, 'AI Destekli Müşteri Analiz Platformu', 'ai-destekli-musteri-analiz-platformu', 'Büyük veri analizi ve yapay zeka kullanarak müşteri davranışlarını analiz eden platform.', 5, 'Python,TensorFlow,Apache Spark,MySQL', '/images/portfolio/ai-platform-1.jpg'),
    (3, 'E-Ticaret Platformu', 'e-ticaret-platformu', 'Giyim markası için kapsamlı e-ticaret çözümü. Ödeme entegrasyonları, stok yönetimi ile birlikte.', 2, 'React,Node.js,MongoDB,Stripe', '/images/portfolio/ecommerce-1.jpg'),
    (4, 'Mobil Fitness Uygulaması', 'mobil-fitness-uygulamasi', 'Kişiselleştirilmiş antrenman programları, beslenme takibi ve sosyal özellikler içeren fitness uygulaması.', 3, 'React Native,Firebase,Node.js', '/images/portfolio/mobile-app-1.jpg'),
    (5, 'SaaS CRM Sistemi', 'saas-crm-sistemi', 'Küçük ve orta ölçekli işletmeler için bulut tabanlı müşteri ilişkileri yönetim sistemi.', 4, 'Angular,Spring Boot,PostgreSQL', '/images/portfolio/crm-system-1.jpg'),
    (6, 'Kripto Para Cüzdan Uygulaması', 'kripto-para-cuzdan-uygulamasi', 'Güvenli, çoklu kripto para desteği olan mobil cüzdan uygulaması. DeFi entegrasyonu ve NFT desteği.', 3, 'React Native,Solidity,Web3.js', '/images/portfolio/crypto-wallet-1.jpg'),
    (7, 'Hastane Yönetim Sistemi', 'hastane-yonetim-sistemi', 'Hastane operasyonlarını dijitalleştiren kapsamlı yönetim sistemi. Hasta takibi, randevu sistemi ve raporlama.', 4, 'Vue.js,Laravel,MySQL', '/images/portfolio/hospital-system-1.jpg'),
    (8, 'Finansal Analiz Dashboard', 'finansal-analiz-dashboard', 'Gerçek zamanlı finansal veri analizi ve görselleştirme platformu. Portföy yönetimi ve risk analizi araçları.', 5, 'React,D3.js,Python', '/images/portfolio/financial-dashboard-1.jpg'),
    (9, 'Mobil Bankacılık Uygulaması', 'mobil-bankacilik-uygulamasi', 'Modern mobil bankacılık deneyimi sunan, biyometrik kimlik doğrulama ve güvenli işlem desteği olan uygulama.', 3, 'React Native,Node.js,PostgreSQL', '/images/portfolio/banking-app-1.jpg'),
    (10, 'IoT Akıllı Ev Platformu', 'iot-akilli-ev-platformu', 'Akıllı ev cihazlarını yöneten, enerji tasarrufu sağlayan ve güvenlik özellikleri olan IoT platformu.', 5, 'React,Node.js,MQTT', '/images/portfolio/iot-platform-1.jpg')
");

echo "Projects inserted!\n";
echo "Portfolio database setup completed successfully!\n";
?>
