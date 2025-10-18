<?php
// NextCode Group - Database Configuration
// MySQL Configuration for Production

// Error reporting settings for development
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

class Database {
    private $host = 'gtorg.mysql.tools';
    private $db_name = 'gtorg_nextcode';
    private $username = 'gtorg_nextcode';
    private $password = ';849#dVEyg';
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 10,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                ]
            );
            
            // Test bağlantı
            $this->conn->query('SELECT 1');
            
            return $this->conn;
        } catch(PDOException $exception) {
            error_log('Database connection failed: ' . $exception->getMessage());
            error_log('Database host: ' . $this->host);
            error_log('Database name: ' . $this->db_name);
            error_log('Database user: ' . $this->username);
            return null;
        }
    }
}

// Create database instance
try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (Exception $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    $pdo = null;
}

if ($pdo) {
    try {
        // Create blog_categories table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS blog_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                slug VARCHAR(255) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create blog_posts table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS blog_posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                content LONGTEXT,
                excerpt TEXT,
                featured_image VARCHAR(500),
                category_id INT,
                author_id INT,
                status ENUM("draft", "published", "archived") DEFAULT "draft",
                is_featured TINYINT(1) DEFAULT 0,
                meta_title VARCHAR(255),
                meta_description TEXT,
                tags TEXT,
                published_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
                INDEX idx_slug (slug),
                INDEX idx_status (status),
                INDEX idx_featured (is_featured),
                INDEX idx_published (published_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create site_settings table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS site_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(255) UNIQUE NOT NULL,
                setting_value TEXT,
                setting_type ENUM("text", "textarea", "number", "boolean", "url", "email", "color") DEFAULT "text",
                description TEXT,
                is_public TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_key (setting_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create portfolio_projects table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS portfolio_projects (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                category VARCHAR(100),
                image_url VARCHAR(500),
                project_url VARCHAR(500),
                github_url VARCHAR(500),
                technologies TEXT,
                status ENUM("completed", "in_progress", "planned") DEFAULT "completed",
                featured TINYINT(1) DEFAULT 0,
                sort_order INT DEFAULT 0,
                views INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_category (category),
                INDEX idx_status (status),
                INDEX idx_featured (featured),
                INDEX idx_sort (sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create portfolio_categories table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS portfolio_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                slug VARCHAR(255) NOT NULL UNIQUE,
                description TEXT,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug),
                INDEX idx_sort (sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create services table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS services (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                icon VARCHAR(100),
                is_active TINYINT(1) DEFAULT 1,
                order_index INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_title (title),
                INDEX idx_active (is_active),
                INDEX idx_order (order_index)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create faq table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS faq (
                id INT AUTO_INCREMENT PRIMARY KEY,
                question VARCHAR(500) NOT NULL,
                answer TEXT NOT NULL,
                category VARCHAR(100) DEFAULT "general",
                is_active TINYINT(1) DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_category (category),
                INDEX idx_active (is_active),
                INDEX idx_sort (sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create pricing_packages table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS pricing_packages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                currency VARCHAR(3) DEFAULT "USD",
                features TEXT,
                is_popular TINYINT(1) DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_active (is_active),
                INDEX idx_popular (is_popular),
                INDEX idx_sort (sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create about_content table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS about_content (
                id INT AUTO_INCREMENT PRIMARY KEY,
                section_name VARCHAR(100) NOT NULL,
                title VARCHAR(255),
                content TEXT,
                image_url VARCHAR(500),
                is_active TINYINT(1) DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_section (section_name),
                INDEX idx_active (is_active),
                INDEX idx_sort (sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create contact_info table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS contact_info (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_name VARCHAR(255),
                email VARCHAR(255),
                phone VARCHAR(50),
                address TEXT,
                working_hours VARCHAR(255),
                social_facebook VARCHAR(500),
                social_twitter VARCHAR(500),
                social_instagram VARCHAR(500),
                social_linkedin VARCHAR(500),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create contact_messages table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS contact_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                company VARCHAR(255),
                service VARCHAR(100),
                budget VARCHAR(100),
                subject VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                status ENUM("new", "read", "replied") DEFAULT "new",
                is_read TINYINT(1) DEFAULT 0,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_read (is_read),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Add missing columns if they don't exist (MySQL 8.0+ supports IF NOT EXISTS)
        try {
            // Check if columns exist before adding them
            $columns = $pdo->query("SHOW COLUMNS FROM contact_messages")->fetchAll(PDO::FETCH_COLUMN);
            
            if (!in_array('phone', $columns)) {
                $pdo->exec('ALTER TABLE contact_messages ADD COLUMN phone VARCHAR(50) AFTER email');
            }
            if (!in_array('company', $columns)) {
                $pdo->exec('ALTER TABLE contact_messages ADD COLUMN company VARCHAR(255) AFTER phone');
            }
            if (!in_array('service', $columns)) {
                $pdo->exec('ALTER TABLE contact_messages ADD COLUMN service VARCHAR(100) AFTER company');
            }
            if (!in_array('budget', $columns)) {
                $pdo->exec('ALTER TABLE contact_messages ADD COLUMN budget VARCHAR(100) AFTER service');
            }
            if (!in_array('is_read', $columns)) {
                $pdo->exec('ALTER TABLE contact_messages ADD COLUMN is_read TINYINT(1) DEFAULT 0 AFTER message');
            }
            if (!in_array('ip_address', $columns)) {
                $pdo->exec('ALTER TABLE contact_messages ADD COLUMN ip_address VARCHAR(45) AFTER is_read');
            }
            if (!in_array('user_agent', $columns)) {
                $pdo->exec('ALTER TABLE contact_messages ADD COLUMN user_agent TEXT AFTER ip_address');
            }
            if (!in_array('updated_at', $columns)) {
                $pdo->exec('ALTER TABLE contact_messages ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at');
            }
        } catch (PDOException $e) {
            error_log('Contact messages table update failed: ' . $e->getMessage());
        }
        
        // Create admin_users table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS admin_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                full_name VARCHAR(255),
                role ENUM("admin", "editor", "viewer") DEFAULT "admin",
                is_active TINYINT(1) DEFAULT 1,
                last_login TIMESTAMP NULL,
                login_attempts INT DEFAULT 0,
                locked_until TIMESTAMP NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_username (username),
                INDEX idx_email (email),
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Add missing columns to admin_users table if they don't exist
        try {
            $columns = $pdo->query("SHOW COLUMNS FROM admin_users")->fetchAll(PDO::FETCH_COLUMN);
            
            if (!in_array('login_attempts', $columns)) {
                $pdo->exec('ALTER TABLE admin_users ADD COLUMN login_attempts INT DEFAULT 0 AFTER last_login');
            }
            if (!in_array('locked_until', $columns)) {
                $pdo->exec('ALTER TABLE admin_users ADD COLUMN locked_until TIMESTAMP NULL AFTER login_attempts');
            }
            if (!in_array('ip_address', $columns)) {
                $pdo->exec('ALTER TABLE admin_users ADD COLUMN ip_address VARCHAR(45) AFTER locked_until');
            }
            if (!in_array('user_agent', $columns)) {
                $pdo->exec('ALTER TABLE admin_users ADD COLUMN user_agent TEXT AFTER ip_address');
            }
        } catch (PDOException $e) {
            error_log('Admin users table update failed: ' . $e->getMessage());
        }
        
        // Create site_content table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS site_content (
                id INT AUTO_INCREMENT PRIMARY KEY,
                page_name VARCHAR(100) NOT NULL,
                section_name VARCHAR(100) NOT NULL,
                content_key VARCHAR(100) NOT NULL,
                content_value TEXT,
                content_type ENUM("text", "html", "image", "json") DEFAULT "text",
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_content (page_name, section_name, content_key),
                INDEX idx_page (page_name),
                INDEX idx_section (section_name),
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create pages table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS pages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                content LONGTEXT,
                meta_title VARCHAR(255),
                meta_description TEXT,
                meta_keywords TEXT,
                status ENUM("draft", "published", "archived") DEFAULT "draft",
                views INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug),
                INDEX idx_status (status),
                INDEX idx_views (views)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create site_statistics table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS site_statistics (
                id INT AUTO_INCREMENT PRIMARY KEY,
                projects_completed VARCHAR(50) DEFAULT "500+",
                client_satisfaction VARCHAR(10) DEFAULT "98%",
                years_experience VARCHAR(10) DEFAULT "16+",
                total_clients INT DEFAULT 0,
                total_projects INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create team_members table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS team_members (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                position VARCHAR(255) NOT NULL,
                bio TEXT,
                image_url VARCHAR(500),
                email VARCHAR(255),
                linkedin_url VARCHAR(500),
                twitter_url VARCHAR(500),
                github_url VARCHAR(500),
                is_active TINYINT(1) DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_active (is_active),
                INDEX idx_sort (sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create company_stats table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS company_stats (
                id INT AUTO_INCREMENT PRIMARY KEY,
                founded_year VARCHAR(10) DEFAULT "2008",
                team_size VARCHAR(20) DEFAULT "25+",
                projects_completed VARCHAR(20) DEFAULT "500+",
                countries_served VARCHAR(20) DEFAULT "15+",
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create service_packages table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS service_packages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                currency VARCHAR(3) DEFAULT "USD",
                features TEXT,
                is_active TINYINT(1) DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_active (is_active),
                INDEX idx_sort (sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create faq_categories table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS faq_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                is_active TINYINT(1) DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_active (is_active),
                INDEX idx_sort (sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Create package_features table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS package_features (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                is_active TINYINT(1) DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_active (is_active),
                INDEX idx_sort (sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Insert default site settings if empty
        $settings_count = $pdo->query('SELECT COUNT(*) FROM site_settings')->fetchColumn();
        if ($settings_count == 0) {
            $pdo->exec("INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
                ('site_name', 'NextCode Group', 'text', 'Web sitesinin ana başlığı'),
                ('site_description', 'Modern web çözümleri ve dijital pazarlama hizmetleri', 'text', 'Web sitesinin meta açıklaması'),
                ('site_keywords', 'web tasarım, dijital pazarlama, SEO, sosyal medya', 'text', 'SEO anahtar kelimeleri'),
                ('contact_email', 'info@nextcodegroup.com', 'email', 'İletişim e-posta adresi'),
                ('contact_phone', '+90 555 123 45 67', 'text', 'İletişim telefon numarası'),
                ('contact_address', 'İstanbul, Türkiye', 'text', 'Şirket adresi'),
                ('social_facebook', 'https://facebook.com/nextcodegroup', 'url', 'Facebook sayfası'),
                ('social_instagram', 'https://instagram.com/nextcodegroup', 'url', 'Instagram sayfası'),
                ('social_linkedin', 'https://linkedin.com/company/nextcodegroup', 'url', 'LinkedIn sayfası'),
                ('analytics_code', '', 'text', 'Google Analytics kodu'),
                ('theme_color', '#667eea', 'color', 'Ana tema rengi'),
                ('logo_url', '/assets/images/logo.png', 'url', 'Site logosu'),
                ('favicon_url', '/assets/images/favicon.ico', 'url', 'Favicon'),
                ('maintenance_mode', '0', 'boolean', 'Bakım modu')
            ");
        }
        
        // Insert default blog categories if empty
        $categories_count = $pdo->query('SELECT COUNT(*) FROM blog_categories')->fetchColumn();
        if ($categories_count == 0) {
            $pdo->exec("INSERT INTO blog_categories (name, slug, description) VALUES
                ('Web Development', 'web-development', 'Modern web applications and websites'),
                ('Mobile Apps', 'mobile-apps', 'iOS and Android mobile applications'),
                ('Digital Marketing', 'digital-marketing', 'SEO, social media and online marketing strategies'),
                ('Technology', 'technology', 'Latest technology trends and innovations'),
                ('Business', 'business', 'Business strategies and entrepreneurship')
            ");
        }
        
        // Insert default blog posts if empty
        $posts_count = $pdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn();
        if ($posts_count == 0) {
            $pdo->exec("INSERT INTO blog_posts (title, slug, content, excerpt, category_id, status, is_featured, published_at) VALUES
                ('Modern Web Development Trends 2024', 'modern-web-development-trends-2024', '<h2>Modern Web Development Trends</h2><p>Discover the latest trends in web development for 2024. From AI integration to progressive web apps, learn what technologies are shaping the future of web development.</p><p>Key trends include:</p><ul><li>AI-powered development tools</li><li>Serverless architecture</li><li>Progressive Web Apps (PWAs)</li><li>WebAssembly integration</li></ul>', 'Explore the cutting-edge technologies shaping web development in 2024', 1, 'published', 1, NOW()),
                ('Building Responsive Mobile Applications', 'building-responsive-mobile-applications', '<h2>Mobile App Development Guide</h2><p>Learn how to build responsive mobile applications that work seamlessly across all devices. This comprehensive guide covers best practices, frameworks, and optimization techniques.</p><p>Topics covered:</p><ul><li>Responsive design principles</li><li>Cross-platform frameworks</li><li>Performance optimization</li><li>User experience design</li></ul>', 'A comprehensive guide to building responsive mobile applications', 2, 'published', 0, NOW()),
                ('Digital Marketing Strategies for Small Business', 'digital-marketing-strategies-small-business', '<h2>Digital Marketing for Small Businesses</h2><p>Effective digital marketing strategies that small businesses can implement to grow their online presence and increase revenue.</p><p>Strategies include:</p><ul><li>Search Engine Optimization (SEO)</li><li>Social Media Marketing</li><li>Content Marketing</li><li>Email Marketing</li><li>Pay-Per-Click Advertising</li></ul>', 'Proven digital marketing strategies to grow your small business', 3, 'published', 0, NOW()),
                ('The Future of Artificial Intelligence', 'future-of-artificial-intelligence', '<h2>AI Revolution</h2><p>Artificial Intelligence is transforming industries and reshaping the way we work and live. Explore the latest developments in AI technology and its potential impact on various sectors.</p><p>Key areas:</p><ul><li>Machine Learning advancements</li><li>Natural Language Processing</li><li>Computer Vision</li><li>Autonomous systems</li></ul>', 'Exploring the transformative power of artificial intelligence', 4, 'published', 1, NOW()),
                ('Entrepreneurship in the Digital Age', 'entrepreneurship-digital-age', '<h2>Digital Entrepreneurship</h2><p>Starting a business in today\'s digital landscape requires new strategies and approaches. Learn how to leverage technology and digital platforms to build successful ventures.</p><p>Essential topics:</p><ul><li>Digital business models</li><li>Online marketing strategies</li><li>E-commerce platforms</li><li>Remote team management</li></ul>', 'Guide to building successful businesses in the digital era', 5, 'published', 0, NOW())
            ");
        }
        
        // Insert default services if empty
        $services_count = $pdo->query('SELECT COUNT(*) FROM services')->fetchColumn();
        if ($services_count == 0) {
            $pdo->exec("INSERT INTO services (title, description, icon, is_active) VALUES
                ('Web Tasarım', 'Modern ve responsive web tasarım hizmetleri', 'fas fa-laptop-code', 1),
                ('Mobil Uygulama', 'iOS ve Android mobil uygulama geliştirme', 'fas fa-mobile-alt', 1),
                ('SEO Optimizasyonu', 'Arama motoru optimizasyonu ve dijital pazarlama', 'fas fa-search', 1),
                ('Sosyal Medya', 'Sosyal medya yönetimi ve pazarlama stratejileri', 'fas fa-share-alt', 1)
            ");
        }
        
        // Insert default FAQ if empty
        $faq_count = $pdo->query('SELECT COUNT(*) FROM faq')->fetchColumn();
        if ($faq_count == 0) {
            $pdo->exec("INSERT INTO faq (question, answer, category, is_active) VALUES
                ('Web sitesi ne kadar sürede hazır olur?', 'Proje kapsamına göre 1-4 hafta arasında tamamlanır.', 'general', 1),
                ('Mobil uygulama geliştirme süreci nasıl?', 'iOS ve Android platformları için ayrı ayrı geliştirme yapılır.', 'services', 1),
                ('SEO çalışması ne kadar sürer?', 'İlk sonuçlar 3-6 ay içinde görülmeye başlar.', 'marketing', 1)
            ");
        }
        
        // Insert default pricing packages if empty
        $pricing_count = $pdo->query('SELECT COUNT(*) FROM pricing_packages')->fetchColumn();
        if ($pricing_count == 0) {
            $pdo->exec("INSERT INTO pricing_packages (name, description, price, currency, features, is_popular) VALUES
                ('Başlangıç', 'Küçük işletmeler için temel paket', 999.00, 'USD', '[\"5 Sayfa\", \"Responsive Tasarım\", \"SEO Temelleri\", \"1 Ay Destek\"]', 0),
                ('Profesyonel', 'Orta ölçekli işletmeler için', 1999.00, 'USD', '[\"10 Sayfa\", \"E-ticaret Entegrasyonu\", \"Gelişmiş SEO\", \"3 Ay Destek\"]', 1),
                ('Kurumsal', 'Büyük işletmeler için özel çözümler', 3999.00, 'USD', '[\"Sınırsız Sayfa\", \"Özel Tasarım\", \"Tam SEO Paketi\", \"1 Yıl Destek\"]', 0)
            ");
        }
        
        // Insert default pages if empty
        $pages_count = $pdo->query('SELECT COUNT(*) FROM pages')->fetchColumn();
        if ($pages_count == 0) {
            $pdo->exec("INSERT INTO pages (title, slug, content, meta_description, status) VALUES
                ('NextCode Group - Dijital Pazarlama Ajansı', 'home', '<h1>NextCode Group</h1><p>Modern web çözümleri ve dijital pazarlama hizmetleri sunuyoruz.</p>', 'NextCode Group - Modern web çözümleri ve dijital pazarlama hizmetleri', 'published'),
                ('Hakkımızda', 'about', '<h1>Hakkımızda</h1><p>NextCode Group olarak dijital dünyada başarılı olmanız için çalışıyoruz.</p>', 'NextCode Group hakkında detaylı bilgi', 'published'),
                ('Hizmetlerimiz', 'services', '<h1>Hizmetlerimiz</h1><p>Web tasarım, mobil uygulama, SEO ve sosyal medya hizmetleri.</p>', 'NextCode Group hizmetleri', 'published'),
                ('Portfolio', 'portfolio', '<h1>Portfolio</h1><p>Tamamladığımız projelerden örnekler.</p>', 'NextCode Group portfolio projeleri', 'published'),
                ('Blog', 'blog', '<h1>Blog</h1><p>Dijital dünyadan güncel haberler ve makaleler.</p>', 'NextCode Group blog yazıları', 'published'),
                ('Fiyatlandırma', 'pricing', '<h1>Fiyatlandırma</h1><p>Hizmet paketlerimiz ve fiyatlarımız.</p>', 'NextCode Group fiyatlandırma paketleri', 'published'),
                ('İletişim', 'contact', '<h1>İletişim</h1><p>Bizimle iletişime geçin.</p>', 'NextCode Group iletişim bilgileri', 'published'),
                ('SSS', 'faq', '<h1>Sık Sorulan Sorular</h1><p>En çok sorulan sorular ve cevapları.</p>', 'NextCode Group SSS', 'published')
            ");
        }
        
        // Insert default site statistics if empty
        $stats_count = $pdo->query('SELECT COUNT(*) FROM site_statistics')->fetchColumn();
        if ($stats_count == 0) {
            $pdo->exec("INSERT INTO site_statistics (projects_completed, client_satisfaction, years_experience, total_clients, total_projects, is_active) VALUES
                ('500+', '98%', '16+', 250, 500, 1)
            ");
        }
        
        // Insert default team members if empty
        $team_count = $pdo->query('SELECT COUNT(*) FROM team_members')->fetchColumn();
        if ($team_count == 0) {
            $pdo->exec("INSERT INTO team_members (name, position, bio, is_active, sort_order) VALUES
                ('Əli Məmmədov', 'CEO & Founder', '16 ildən artıq təcrübəyə malik rəqəmsal marketinq mütəxəssisi', 1, 1),
                ('Leyla Həsənova', 'Marketing Director', 'SEO və sosial media strategiyaları sahəsində peşəkar', 1, 2),
                ('Rəşad Quliyev', 'Lead Developer', 'Full-stack development və AI inteqrasiyası mütəxəssisi', 1, 3)
            ");
        }
        
        // Insert default company stats if empty
        $company_stats_count = $pdo->query('SELECT COUNT(*) FROM company_stats')->fetchColumn();
        if ($company_stats_count == 0) {
            $pdo->exec("INSERT INTO company_stats (founded_year, team_size, projects_completed, countries_served, is_active) VALUES
                ('2008', '25+', '500+', '15+', 1)
            ");
        }
        
        // Insert default admin user if empty
        $admin_count = $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
        if ($admin_count == 0) {
            $default_password_hash = password_hash('Admin123!@#', PASSWORD_DEFAULT);
            $pdo->exec("INSERT INTO admin_users (username, email, password_hash, full_name, role, is_active) VALUES
                ('admin', 'admin@nextcodegroup.com', '$default_password_hash', 'Admin User', 'admin', 1)
            ");
        }
        
    } catch (PDOException $e) {
        error_log('Database setup failed: ' . $e->getMessage());
    }
}
?>