-- NextCode Group - Basit Veritabanı Güncelleme
-- Her komutu ayrı ayrı çalıştırın

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
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Images Table
CREATE TABLE IF NOT EXISTS `portfolio_images` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) NOT NULL,
    `image_path` varchar(500) NOT NULL,
    `image_title` varchar(255),
    `image_alt` varchar(255),
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Testimonials Table
CREATE TABLE IF NOT EXISTS `portfolio_testimonials` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11),
    `client_name` varchar(255) NOT NULL,
    `client_position` varchar(255),
    `client_company` varchar(255),
    `testimonial_text` longtext NOT NULL,
    `rating` int(11) DEFAULT 5,
    `is_featured` tinyint(1) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Newsletter Subscribers Table
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(100) NOT NULL UNIQUE,
    `status` enum('active','unsubscribed') DEFAULT 'active',
    `subscribed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- PORTFOLIO_PROJECTS TABLOSUNA SÜTUN EKLE
-- ==============================================

-- Bu komutları tek tek çalıştırın:

-- ALTER TABLE `portfolio_projects` ADD COLUMN `slug` varchar(255) UNIQUE AFTER `title`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `short_description` text AFTER `slug`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `category_id` int(11) AFTER `short_description`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `featured_image` varchar(500) AFTER `category_id`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `is_featured` tinyint(1) DEFAULT 0 AFTER `featured_image`;
-- ALTER TABLE `portfolio_projects` ADD COLUMN `is_published` tinyint(1) DEFAULT 1 AFTER `is_featured`;

-- ==============================================
-- BLOG_POSTS TABLOSUNA SÜTUN EKLE
-- ==============================================

-- Bu komutları tek tek çalıştırın:

-- ALTER TABLE `blog_posts` ADD COLUMN `featured` tinyint(1) DEFAULT 0 AFTER `status`;
-- ALTER TABLE `blog_posts` ADD COLUMN `meta_title` varchar(200) AFTER `featured`;
-- ALTER TABLE `blog_posts` ADD COLUMN `meta_description` text AFTER `meta_title`;
-- ALTER TABLE `blog_posts` ADD COLUMN `tags` text AFTER `meta_description`;
-- ALTER TABLE `blog_posts` ADD COLUMN `published_at` timestamp NULL AFTER `tags`;

-- ==============================================
-- CONTACT_MESSAGES TABLOSUNA SÜTUN EKLE
-- ==============================================

-- Bu komutları tek tek çalıştırın:

-- ALTER TABLE `contact_messages` ADD COLUMN `first_name` varchar(100) AFTER `id`;
-- ALTER TABLE `contact_messages` ADD COLUMN `last_name` varchar(100) AFTER `first_name`;
-- ALTER TABLE `contact_messages` ADD COLUMN `subject` varchar(200) AFTER `phone`;
-- ALTER TABLE `contact_messages` ADD COLUMN `status` enum('unread','read','archived') DEFAULT 'unread' AFTER `message`;

-- ==============================================
-- PRICING_PACKAGES TABLOSUNA SÜTUN EKLE
-- ==============================================

-- Bu komutları tek tek çalıştırın:

-- ALTER TABLE `pricing_packages` ADD COLUMN `slug` varchar(100) UNIQUE AFTER `name`;
-- ALTER TABLE `pricing_packages` ADD COLUMN `billing_period` varchar(20) DEFAULT 'monthly' AFTER `currency`;
-- ALTER TABLE `pricing_packages` ADD COLUMN `is_featured` tinyint(1) DEFAULT 0 AFTER `is_popular`;
-- ALTER TABLE `pricing_packages` ADD COLUMN `button_text` varchar(50) DEFAULT 'Seç' AFTER `sort_order`;
-- ALTER TABLE `pricing_packages` ADD COLUMN `button_link` varchar(255) AFTER `button_text`;

COMMIT;
