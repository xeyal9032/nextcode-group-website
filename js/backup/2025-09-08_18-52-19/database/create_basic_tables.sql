-- NextCode Group - Basic Tables Creation
-- Production Environment Database Setup

-- Create pages table
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext,
  `meta_description` text,
  `meta_keywords` text,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create site_settings table
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `description` text,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create site_statistics table
CREATE TABLE IF NOT EXISTS `site_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_projects` int(11) DEFAULT 0,
  `happy_clients` int(11) DEFAULT 0,
  `years_experience` int(11) DEFAULT 0,
  `team_members` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default homepage
INSERT IGNORE INTO `pages` (`title`, `slug`, `content`, `meta_description`, `status`) VALUES
('NextCode Group - Rəqəmsal Marketinq Agentliyi', 'home', 
'<h1>NextCode Group</h1><p>Azərbaycanda rəqəmsal marketinq sahəsində peşəkar xidmətlər təqdim edirik.</p>', 
'NextCode Group - Azərbaycanda rəqəmsal marketinq sahəsində peşəkar xidmətlər. SEO, sosial media, brendinq və reklam kampaniyaları ilə biznesinizi inkişaf etdirin.', 
'published');

-- Insert default site statistics
INSERT IGNORE INTO `site_statistics` (`total_projects`, `happy_clients`, `years_experience`, `team_members`, `active`) VALUES
(150, 200, 5, 10, 1);

-- Insert default site settings
INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_title', 'NextCode Group', 'text', 'Site başlığı'),
('site_description', 'Rəqəmsal marketinq agentliyi', 'text', 'Site açıqlaması'),
('contact_email', 'info@nextcodegroup.com', 'text', 'Əlaqə email adresi'),
('contact_phone', '+994 XX XXX XX XX', 'text', 'Əlaqə telefon nömrəsi'),
('company_address', 'Bakı, Azərbaycan', 'text', 'Şirkət ünvanı'),
('social_facebook', 'https://facebook.com/nextcodegroup', 'text', 'Facebook səhifəsi'),
('social_instagram', 'https://instagram.com/nextcodegroup', 'text', 'Instagram səhifəsi'),
('social_linkedin', 'https://linkedin.com/company/nextcodegroup', 'text', 'LinkedIn səhifəsi');