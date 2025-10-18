-- NextCode Group - Akıllı Veritabanı Güncelleme
-- Sadece eksik sütunları ekler, mevcut olanları atlar

USE `gtorg_nextcode`;

-- ==============================================
-- EKSİK TABLOLARI EKLE
-- ==============================================

-- Site Content Table
CREATE TABLE IF NOT EXISTS `site_content` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `content_key` varchar(100) NOT NULL UNIQUE,
    `content_value` text,
    `content_type` enum('text','html','image','link','json') DEFAULT 'text',
    `page_section` varchar(100) DEFAULT 'general',
    `description` text,
    `is_active` tinyint(1) DEFAULT 1,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_content_key` (`content_key`),
    KEY `idx_page_section` (`page_section`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Images Table
CREATE TABLE IF NOT EXISTS `portfolio_images` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) NOT NULL,
    `image_path` varchar(500) NOT NULL,
    `image_title` varchar(255),
    `image_alt` varchar(255),
    `image_description` text,
    `image_type` enum('thumbnail','gallery','before','after','mockup','screenshot') DEFAULT 'gallery',
    `sort_order` int(11) DEFAULT 0,
    `is_primary` tinyint(1) DEFAULT 0,
    `file_size` int(11),
    `image_width` int(11),
    `image_height` int(11),
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_image_type` (`image_type`),
    KEY `idx_is_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Testimonials Table
CREATE TABLE IF NOT EXISTS `portfolio_testimonials` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11),
    `client_name` varchar(255) NOT NULL,
    `client_position` varchar(255),
    `client_company` varchar(255),
    `client_email` varchar(255),
    `client_photo` varchar(500),
    `testimonial_text` longtext NOT NULL,
    `rating` int(11) CHECK (`rating` >= 1 AND `rating` <= 5),
    `is_featured` tinyint(1) DEFAULT 0,
    `is_published` tinyint(1) DEFAULT 1,
    `testimonial_date` date,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_featured` (`is_featured`),
    KEY `idx_published` (`is_published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Newsletter Subscribers Table
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(100) NOT NULL UNIQUE,
    `status` enum('active','unsubscribed') DEFAULT 'active',
    `subscribed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `unsubscribed_at` timestamp NULL,
    `ip_address` varchar(45),
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_email` (`email`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Media Files Table
CREATE TABLE IF NOT EXISTS `media_files` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `filename` varchar(255) NOT NULL,
    `original_name` varchar(255) NOT NULL,
    `file_path` varchar(500),
    `file_size` int(11),
    `mime_type` varchar(100),
    `alt_text` varchar(255),
    `uploaded_by` int(11) NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_filename` (`filename`),
    KEY `idx_mime_type` (`mime_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Navigation Items Table
CREATE TABLE IF NOT EXISTS `navigation_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(100) NOT NULL,
    `url` varchar(255) NOT NULL,
    `parent_id` int(11) NULL,
    `sort_order` int(11) DEFAULT 0,
    `status` enum('active','inactive') DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_parent_id` (`parent_id`),
    KEY `idx_status` (`status`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Log Table
CREATE TABLE IF NOT EXISTS `activity_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NULL,
    `action` varchar(100) NOT NULL,
    `description` text,
    `ip_address` varchar(45),
    `user_agent` text,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_action` (`action`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- SÜTUN EKLEME (HATA TOLERANSLI)
-- ==============================================

-- portfolio_projects tablosu için sütun ekleme
-- Bu komutları tek tek çalıştırın veya PHP scripti kullanın

-- ALTER TABLE `portfolio_projects` ADD COLUMN `slug` varchar(255) UNIQUE AFTER `title`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `short_description` text AFTER `slug`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `category_id` int(11) AFTER `short_description`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `featured_image` varchar(500) AFTER `category_id`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `project_date` date AFTER `featured_image`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `completion_date` date AFTER `project_date`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `project_status` enum('planning','in_progress','completed','on_hold') DEFAULT 'completed' AFTER `completion_date`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `budget` decimal(10,2) AFTER `project_status`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `team_size` int(11) AFTER `budget`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `duration_months` int(11) AFTER `team_size`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `key_results` text AFTER `duration_months`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `challenges_solved` text AFTER `key_results`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `lessons_learned` text AFTER `challenges_solved`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `is_featured` tinyint(1) DEFAULT 0 AFTER `lessons_learned`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `is_published` tinyint(1) DEFAULT 1 AFTER `is_featured`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `view_count` int(11) DEFAULT 0 AFTER `is_published`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `seo_title` varchar(255) AFTER `view_count`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `seo_description` text AFTER `seo_title`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `seo_keywords` varchar(500) AFTER `seo_description`;

-- blog_posts tablosu için sütun ekleme
-- ALTER TABLE `blog_posts` ADD COLUMN `featured` tinyint(1) DEFAULT 0 AFTER `status`;
-- ALTER TABLE `blog_posts` ADD COLUMN `meta_title` varchar(200) AFTER `featured`;
-- ALTER TABLE `blog_posts` ADD COLUMN `meta_description` text AFTER `meta_title`;
-- ALTER TABLE `blog_posts` ADD COLUMN `tags` text AFTER `meta_description`;
-- ALTER TABLE `blog_posts` ADD COLUMN `published_at` timestamp NULL AFTER `tags`;

-- contact_messages tablosu için sütun ekleme
-- ALTER TABLE `contact_messages` ADD COLUMN `first_name` varchar(100) AFTER `id`;
-- ALTER TABLE `contact_messages` ADD COLUMN `last_name` varchar(100) AFTER `first_name`;
-- ALTER TABLE `contact_messages` ADD COLUMN `subject` varchar(200) AFTER `phone`;
-- ALTER TABLE `contact_messages` ADD COLUMN `status` enum('unread','read','archived') DEFAULT 'unread' AFTER `message`;

-- pricing_packages tablosu için sütun ekleme
-- ALTER TABLE `pricing_packages` ADD COLUMN `slug` varchar(100) UNIQUE AFTER `name`;
-- ALTER TABLE `pricing_packages` ADD COLUMN `billing_period` varchar(20) DEFAULT 'monthly' AFTER `currency`;
-- ALTER TABLE `pricing_packages` ADD COLUMN `discount_percentage` int(11) DEFAULT 0 AFTER `billing_period`;
-- ALTER TABLE `pricing_packages` ADD COLUMN `is_featured` tinyint(1) DEFAULT 0 AFTER `is_popular`;
-- ALTER TABLE `pricing_packages` ADD COLUMN `button_text` varchar(50) DEFAULT 'Seç' AFTER `sort_order`;
-- ALTER TABLE `pricing_packages` ADD COLUMN `button_link` varchar(255) AFTER `button_text`;

-- ==============================================
-- INDEX EKLEME (HATA TOLERANSLI)
-- ==============================================

-- portfolio_projects indexleri
-- ALTER TABLE `portfolio_projects` ADD INDEX `idx_slug` (`slug`);
-- ALTER TABLE `portfolio_projects` ADD INDEX `idx_category_id` (`category_id`);
-- ALTER TABLE `portfolio_projects` ADD INDEX `idx_featured` (`is_featured`);
-- ALTER TABLE `portfolio_projects` ADD INDEX `idx_published` (`is_published`);

-- blog_posts indexleri
-- ALTER TABLE `blog_posts` ADD INDEX `idx_featured` (`featured`);
-- ALTER TABLE `blog_posts` ADD INDEX `idx_published_at` (`published_at`);

-- contact_messages indexleri
-- ALTER TABLE `contact_messages` ADD INDEX `idx_status` (`status`);
-- ALTER TABLE `contact_messages` ADD INDEX `idx_created_at` (`created_at`);

-- pricing_packages indexleri
-- ALTER TABLE `pricing_packages` ADD INDEX `idx_slug` (`slug`);

COMMIT;
