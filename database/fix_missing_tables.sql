-- NextCode Group - Eksik Tabloları Ekleme
-- Tespit edilen eksik tabloları güvenli şekilde ekler

USE `gtorg_nextcode`;

-- ==============================================
-- EKSİK TABLOLARI EKLE
-- ==============================================

-- Navigation Items Table (Eğer yoksa)
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
    KEY `idx_sort_order` (`sort_order`),
    FOREIGN KEY (`parent_id`) REFERENCES `navigation_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Log Table (Eğer yoksa)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- EKSİK SÜTUNLARI EKLE
-- ==============================================

-- blog_posts tablosuna featured sütunu ekle (eğer yoksa)
ALTER TABLE `blog_posts` 
ADD COLUMN IF NOT EXISTS `featured` tinyint(1) DEFAULT 0 AFTER `status`;

-- blog_posts tablosuna eksik sütunları ekle
ALTER TABLE `blog_posts` 
ADD COLUMN IF NOT EXISTS `meta_title` varchar(200) AFTER `featured`;

ALTER TABLE `blog_posts` 
ADD COLUMN IF NOT EXISTS `meta_description` text AFTER `meta_title`;

ALTER TABLE `blog_posts` 
ADD COLUMN IF NOT EXISTS `tags` text AFTER `meta_description`;

ALTER TABLE `blog_posts` 
ADD COLUMN IF NOT EXISTS `published_at` timestamp NULL AFTER `tags`;

-- ==============================================
-- EKSİK INDEXLERİ EKLE
-- ==============================================

-- blog_posts tablosuna indexler ekle
ALTER TABLE `blog_posts` 
ADD INDEX IF NOT EXISTS `idx_featured` (`featured`);

ALTER TABLE `blog_posts` 
ADD INDEX IF NOT EXISTS `idx_published_at` (`published_at`);

-- portfolio_projects tablosuna eksik indexler ekle
ALTER TABLE `portfolio_projects` 
ADD INDEX IF NOT EXISTS `idx_slug` (`slug`);

ALTER TABLE `portfolio_projects` 
ADD INDEX IF NOT EXISTS `idx_category_id` (`category_id`);

ALTER TABLE `portfolio_projects` 
ADD INDEX IF NOT EXISTS `idx_published` (`is_published`);

-- ==============================================
-- ÖRNEK VERİ EKLE
-- ==============================================

-- Navigation items için örnek veri ekle
INSERT IGNORE INTO `navigation_items` (`title`, `url`, `sort_order`, `status`) VALUES
('Ana Sayfa', '/', 1, 'active'),
('Hakkımızda', '/about.php', 2, 'active'),
('Hizmetlerimiz', '/services.php', 3, 'active'),
('Portfolio', '/portfolio.php', 4, 'active'),
('Blog', '/blog.php', 5, 'active'),
('Fiyatlandırma', '/pricing.php', 6, 'active'),
('İletişim', '/contact.php', 7, 'active');

-- ==============================================
-- KONTROL SORGULARI
-- ==============================================

-- Eklenen tabloları kontrol et
SELECT 
    TABLE_NAME as 'Tablo Adı',
    TABLE_ROWS as 'Kayıt Sayısı',
    CREATE_TIME as 'Oluşturulma Tarihi'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'gtorg_nextcode' 
AND TABLE_NAME IN ('navigation_items', 'activity_log')
ORDER BY TABLE_NAME;

-- Eklenen sütunları kontrol et
SELECT 
    TABLE_NAME as 'Tablo',
    COLUMN_NAME as 'Sütun',
    COLUMN_TYPE as 'Tip',
    IS_NULLABLE as 'Null Olabilir',
    COLUMN_DEFAULT as 'Varsayılan Değer'
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'gtorg_nextcode'
AND TABLE_NAME = 'blog_posts'
AND COLUMN_NAME IN ('featured', 'meta_title', 'meta_description', 'tags', 'published_at')
ORDER BY ORDINAL_POSITION;

-- Eklenen indexleri kontrol et
SELECT 
    TABLE_NAME as 'Tablo',
    INDEX_NAME as 'Index Adı',
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) as 'Sütunlar'
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'gtorg_nextcode'
AND (
    (TABLE_NAME = 'blog_posts' AND INDEX_NAME IN ('idx_featured', 'idx_published_at'))
    OR (TABLE_NAME = 'portfolio_projects' AND INDEX_NAME IN ('idx_slug', 'idx_category_id', 'idx_published'))
)
GROUP BY TABLE_NAME, INDEX_NAME
ORDER BY TABLE_NAME, INDEX_NAME;

COMMIT;
