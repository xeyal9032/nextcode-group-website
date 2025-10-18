-- NextCode Group - Complete Database Structure
-- Tüm tabloları içeren kapsamlı veritabanı yapısı
-- MySQL 5.7+ uyumlu

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Veritabanı oluştur
-- Host: gtorg.mysql.tools:3306
-- Database: gtorg_nextcode
-- Username: gtorg_nextcode
-- Password: ;849#dVEyg

CREATE DATABASE IF NOT EXISTS `gtorg_nextcode` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gtorg_nextcode`;

-- ==============================================
-- TEMEL TABLOLAR
-- ==============================================

-- Site Settings Table
CREATE TABLE IF NOT EXISTS `site_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(100) NOT NULL UNIQUE,
    `setting_value` text,
    `setting_type` enum('text','textarea','number','boolean','json') DEFAULT 'text',
    `description` text,
    `active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_setting_key` (`setting_key`),
    KEY `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site Statistics Table
CREATE TABLE IF NOT EXISTS `site_statistics` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `stat_key` varchar(100) NOT NULL UNIQUE,
    `stat_value` text,
    `stat_type` varchar(50) DEFAULT 'counter',
    `description` varchar(255) DEFAULT NULL,
    `last_updated` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_stat_key` (`stat_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- Pages Table
CREATE TABLE IF NOT EXISTS `pages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL UNIQUE,
    `content` longtext,
    `meta_description` text,
    `meta_keywords` text,
    `status` enum('draft','published','archived') DEFAULT 'draft',
    `views` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_slug` (`slug`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- SERVİS VE PORTFOLIO TABLOLARI
-- ==============================================

-- Services Table
CREATE TABLE IF NOT EXISTS `services` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(200) NOT NULL,
    `description` text,
    `icon` varchar(100),
    `price` varchar(50),
    `features` text,
    `sort_order` int(11) DEFAULT 0,
    `status` enum('active','inactive') DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Categories Table
CREATE TABLE IF NOT EXISTS `portfolio_categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL UNIQUE,
    `slug` varchar(100) NOT NULL UNIQUE,
    `description` text,
    `icon` varchar(100),
    `color` varchar(7) DEFAULT '#2563eb',
    `sort_order` int(11) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_slug` (`slug`),
    KEY `idx_sort_order` (`sort_order`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Portfolio Projects Table
CREATE TABLE IF NOT EXISTS `portfolio_projects` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL UNIQUE,
    `short_description` text,
    `description` longtext NOT NULL,
    `category_id` int(11),
    `featured_image` varchar(500),
    `project_url` varchar(500),
    `github_url` varchar(500),
    `demo_url` varchar(500),
    `technologies` text,
    `client_name` varchar(255),
    `project_date` date,
    `completion_date` date,
    `project_status` enum('planning','in_progress','completed','on_hold') DEFAULT 'completed',
    `budget` decimal(10,2),
    `team_size` int(11),
    `duration_months` int(11),
    `key_results` text,
    `challenges_solved` text,
    `lessons_learned` text,
    `is_featured` tinyint(1) DEFAULT 0,
    `is_published` tinyint(1) DEFAULT 1,
    `view_count` int(11) DEFAULT 0,
    `sort_order` int(11) DEFAULT 0,
    `seo_title` varchar(255),
    `seo_description` text,
    `seo_keywords` varchar(500),
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_slug` (`slug`),
    KEY `idx_category_id` (`category_id`),
    KEY `idx_featured` (`is_featured`),
    KEY `idx_published` (`is_published`),
    KEY `idx_sort_order` (`sort_order`),
    FOREIGN KEY (`category_id`) REFERENCES `portfolio_categories`(`id`) ON DELETE SET NULL
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
    KEY `idx_is_primary` (`is_primary`),
    FOREIGN KEY (`project_id`) REFERENCES `portfolio_projects`(`id`) ON DELETE CASCADE
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
    KEY `idx_published` (`is_published`),
    FOREIGN KEY (`project_id`) REFERENCES `portfolio_projects`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- BLOG TABLOLARI
-- ==============================================

-- Blog Categories Table
CREATE TABLE IF NOT EXISTS `blog_categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL UNIQUE,
    `description` text,
    `status` enum('active','inactive') DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_slug` (`slug`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Posts Table
CREATE TABLE IF NOT EXISTS `blog_posts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(200) NOT NULL,
    `slug` varchar(200) NOT NULL UNIQUE,
    `content` longtext,
    `excerpt` text,
    `featured_image` varchar(255),
    `category_id` int(11),
    `author_id` int(11) NULL,
    `status` enum('draft','published','archived') DEFAULT 'draft',
    `featured` tinyint(1) DEFAULT 0,
    `meta_title` varchar(200),
    `meta_description` text,
    `tags` text,
    `published_at` timestamp NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_slug` (`slug`),
    KEY `idx_category_id` (`category_id`),
    KEY `idx_status` (`status`),
    KEY `idx_featured` (`featured`),
    KEY `idx_published_at` (`published_at`),
    FOREIGN KEY (`category_id`) REFERENCES `blog_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- İLETİŞİM VE FORM TABLOLARI
-- ==============================================

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `first_name` varchar(100) NOT NULL,
    `last_name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(20),
    `subject` varchar(200),
    `message` text NOT NULL,
    `status` enum('unread','read','archived') DEFAULT 'unread',
    `ip_address` varchar(45),
    `user_agent` text,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_email` (`email`),
    KEY `idx_created_at` (`created_at`)
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

-- ==============================================
-- FİYATLANDIRMA TABLOLARI
-- ==============================================

-- Pricing Packages Table
CREATE TABLE IF NOT EXISTS `pricing_packages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL UNIQUE,
    `description` text,
    `price` decimal(10,2) NOT NULL,
    `currency` varchar(3) DEFAULT 'AZN',
    `billing_period` varchar(20) DEFAULT 'monthly',
    `discount_percentage` int(11) DEFAULT 0,
    `is_popular` tinyint(1) DEFAULT 0,
    `is_featured` tinyint(1) DEFAULT 0,
    `active` tinyint(1) DEFAULT 1,
    `sort_order` int(11) DEFAULT 0,
    `button_text` varchar(50) DEFAULT 'Seç',
    `button_link` varchar(255),
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_slug` (`slug`),
    KEY `idx_active` (`active`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Package Features Table
CREATE TABLE IF NOT EXISTS `package_features` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `package_id` int(11) NOT NULL,
    `feature_name` varchar(255) NOT NULL,
    `feature_description` text,
    `feature_value` varchar(255),
    `is_included` tinyint(1) DEFAULT 1,
    `is_highlighted` tinyint(1) DEFAULT 0,
    `active` tinyint(1) DEFAULT 1,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_package_id` (`package_id`),
    KEY `idx_is_included` (`is_included`),
    KEY `idx_sort_order` (`sort_order`),
    FOREIGN KEY (`package_id`) REFERENCES `pricing_packages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- MEDYA VE NAVIGASYON TABLOLARI
-- ==============================================

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
    KEY `idx_sort_order` (`sort_order`),
    FOREIGN KEY (`parent_id`) REFERENCES `navigation_items`(`id`) ON DELETE CASCADE
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

COMMIT;
