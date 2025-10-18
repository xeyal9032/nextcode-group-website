-- NextCode Group - Portfolio Projects Table Creation
-- Bu dosya portfolio_projects tablosunu oluşturur

-- Tablo yoksa oluştur
CREATE TABLE IF NOT EXISTS `portfolio_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(500) DEFAULT NULL,
  `description` text,
  `category_name` varchar(100) DEFAULT 'Web Development',
  `duration` int(11) DEFAULT 6,
  `team_size` int(11) DEFAULT 3,
  `budget` varchar(50) DEFAULT '25K',
  `completion_rate` int(11) DEFAULT 100,
  `objective` text,
  `challenges` text,
  `solution` text,
  `results` text,
  `image` varchar(500) DEFAULT NULL,
  `image_1` varchar(500) DEFAULT NULL,
  `image_2` varchar(500) DEFAULT NULL,
  `image_3` varchar(500) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`active`),
  KEY `idx_category` (`category_name`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tablo yapısını kontrol et
DESCRIBE portfolio_projects;
