-- Modern Portfolio Database Structure
-- Bu dosya modern portfolio sistemi için gerekli tabloları oluşturur

-- Portfolio Projects Table (Ana proje tablosu)
CREATE TABLE IF NOT EXISTS `portfolio_projects` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL UNIQUE,
    `short_description` text,
    `description` longtext NOT NULL,
    `category` varchar(100) NOT NULL,
    `featured_image` varchar(500),
    `project_url` varchar(500),
    `github_url` varchar(500),
    `technologies` text,
    `client_name` varchar(255),
    `completion_date` date,
    `duration` varchar(100),
    `team_size` int(11),
    `challenges` longtext,
    `solutions` longtext,
    `results` longtext,
    `features` longtext,
    `status` enum('active','inactive','draft') DEFAULT 'active',
    `sort_order` int(11) DEFAULT 0,
    `views` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_category` (`category`),
    KEY `idx_status` (`status`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Images Table (Proje görselleri)
CREATE TABLE IF NOT EXISTS `portfolio_images` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) NOT NULL,
    `image_url` varchar(500) NOT NULL,
    `alt_text` varchar(255),
    `caption` text,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_sort_order` (`sort_order`),
    FOREIGN KEY (`project_id`) REFERENCES `portfolio_projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Categories Table (Kategori yönetimi)
CREATE TABLE IF NOT EXISTS `portfolio_categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL UNIQUE,
    `slug` varchar(100) NOT NULL UNIQUE,
    `description` text,
    `icon` varchar(100),
    `color` varchar(7) DEFAULT '#007bff',
    `sort_order` int(11) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_sort_order` (`sort_order`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Technologies Table (Teknoloji yönetimi)
CREATE TABLE IF NOT EXISTS `portfolio_technologies` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL UNIQUE,
    `slug` varchar(100) NOT NULL UNIQUE,
    `icon` varchar(100),
    `color` varchar(7) DEFAULT '#6c757d',
    `description` text,
    `website_url` varchar(255),
    `sort_order` int(11) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_sort_order` (`sort_order`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Project Technologies (Çoktan çoğa ilişki)
CREATE TABLE IF NOT EXISTS `portfolio_project_technologies` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) NOT NULL,
    `technology_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_project_tech` (`project_id`, `technology_id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_technology_id` (`technology_id`),
    FOREIGN KEY (`project_id`) REFERENCES `portfolio_projects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`technology_id`) REFERENCES `portfolio_technologies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Tags Table (Etiket sistemi)
CREATE TABLE IF NOT EXISTS `portfolio_tags` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL UNIQUE,
    `slug` varchar(100) NOT NULL UNIQUE,
    `color` varchar(7) DEFAULT '#6c757d',
    `usage_count` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_usage_count` (`usage_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Project Tags (Çoktan çoğa ilişki)
CREATE TABLE IF NOT EXISTS `portfolio_project_tags` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) NOT NULL,
    `tag_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_project_tag` (`project_id`, `tag_id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_tag_id` (`tag_id`),
    FOREIGN KEY (`project_id`) REFERENCES `portfolio_projects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tag_id`) REFERENCES `portfolio_tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Analytics (Proje istatistikleri)
CREATE TABLE IF NOT EXISTS `portfolio_analytics` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) NOT NULL,
    `visitor_ip` varchar(45),
    `user_agent` text,
    `referrer` varchar(500),
    `view_date` date NOT NULL,
    `view_time` time NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_view_date` (`view_date`),
    KEY `idx_visitor_ip` (`visitor_ip`),
    FOREIGN KEY (`project_id`) REFERENCES `portfolio_projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data Insert
-- Kategoriler
INSERT IGNORE INTO `portfolio_categories` (`name`, `slug`, `description`, `icon`, `color`, `sort_order`) VALUES
('Web Tasarım', 'web-tasarim', 'Modern ve responsive web site tasarımları', 'fas fa-palette', '#e74c3c', 1),
('E-Ticaret', 'e-ticaret', 'Online mağaza ve e-ticaret çözümleri', 'fas fa-shopping-cart', '#f39c12', 2),
('Mobil Uygulama', 'mobil-uygulama', 'iOS ve Android mobil uygulamalar', 'fas fa-mobile-alt', '#9b59b6', 3),
('Kurumsal', 'kurumsal', 'Kurumsal web siteleri ve portallar', 'fas fa-building', '#3498db', 4),
('Blog & CMS', 'blog-cms', 'Blog siteleri ve içerik yönetim sistemleri', 'fas fa-edit', '#2ecc71', 5);

-- Teknolojiler
INSERT IGNORE INTO `portfolio_technologies` (`name`, `slug`, `icon`, `color`, `description`, `sort_order`) VALUES
('HTML5', 'html5', 'fab fa-html5', '#e34f26', 'Modern web standartları', 1),
('CSS3', 'css3', 'fab fa-css3-alt', '#1572b6', 'Gelişmiş stil ve animasyonlar', 2),
('JavaScript', 'javascript', 'fab fa-js-square', '#f7df1e', 'İnteraktif web uygulamaları', 3),
('PHP', 'php', 'fab fa-php', '#777bb4', 'Sunucu tarafı programlama', 4),
('MySQL', 'mysql', 'fas fa-database', '#4479a1', 'Veritabanı yönetimi', 5),
('Bootstrap', 'bootstrap', 'fab fa-bootstrap', '#7952b3', 'Responsive framework', 6),
('jQuery', 'jquery', 'fas fa-code', '#0769ad', 'JavaScript kütüphanesi', 7),
('React', 'react', 'fab fa-react', '#61dafb', 'Modern frontend framework', 8),
('Node.js', 'nodejs', 'fab fa-node-js', '#339933', 'JavaScript runtime', 9),
('WordPress', 'wordpress', 'fab fa-wordpress', '#21759b', 'İçerik yönetim sistemi', 10);

-- Örnek Proje
INSERT IGNORE INTO `portfolio_projects` (
    `title`, 
    `slug`, 
    `short_description`, 
    `description`, 
    `category`, 
    `featured_image`, 
    `technologies`, 
    `client_name`, 
    `completion_date`, 
    `duration`, 
    `team_size`, 
    `challenges`, 
    `solutions`, 
    `results`, 
    `features`, 
    `status`
) VALUES (
    'Modern E-Ticaret Sitesi',
    'modern-e-ticaret-sitesi',
    'Kullanıcı dostu arayüz ve güçlü yönetim paneli ile modern e-ticaret çözümü',
    'Bu proje, modern e-ticaret ihtiyaçlarını karşılamak için geliştirilmiş kapsamlı bir online mağaza çözümüdür. Responsive tasarım, güvenli ödeme sistemi ve kullanıcı dostu yönetim paneli ile donatılmıştır.',
    'E-Ticaret',
    'images/portfolio/ecommerce-project.jpg',
    'HTML5, CSS3, JavaScript, PHP, MySQL, Bootstrap',
    'ABC Teknoloji',
    '2024-01-15',
    '6 hafta',
    3,
    'Yüksek performans gereksinimleri ve karmaşık ürün katalog yapısı',
    'Optimize edilmiş veritabanı sorguları ve önbellekleme sistemi',
    '%40 daha hızlı sayfa yükleme ve %25 artış satışlarda',
    'Responsive tasarım\nGüvenli ödeme sistemi\nÜrün filtreleme\nSipariş takibi\nCanlı destek\nSEO optimizasyonu',
    'active'
);

-- Örnek etiketler
INSERT IGNORE INTO `portfolio_tags` (`name`, `slug`, `color`) VALUES
('Responsive', 'responsive', '#28a745'),
('E-Commerce', 'e-commerce', '#dc3545'),
('Modern', 'modern', '#007bff'),
('Fast', 'fast', '#ffc107'),
('Secure', 'secure', '#6f42c1');

COMMIT;