-- Site Statistics Table
-- Bu tablo web sitesi istatistiklerini saklar

CREATE TABLE IF NOT EXISTS `site_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stat_key` varchar(100) NOT NULL UNIQUE,
  `stat_value` text,
  `stat_type` varchar(50) DEFAULT 'counter',
  `description` varchar(255) DEFAULT NULL,
  `last_updated` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX idx_stat_key (`stat_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Temel istatistik verilerini ekle
INSERT IGNORE INTO `site_statistics` (`stat_key`, `stat_value`, `stat_type`, `description`) VALUES
('total_visitors', '0', 'counter', 'Toplam ziyaretçi sayısı'),
('total_projects', '0', 'counter', 'Toplam proje sayısı'),
('total_clients', '0', 'counter', 'Toplam müşteri sayısı'),
('page_views', '0', 'counter', 'Toplam sayfa görüntüleme'),
('bounce_rate', '0', 'percentage', 'Çıkış oranı'),
('avg_session_duration', '0', 'time', 'Ortalama oturum süresi'),
('conversion_rate', '0', 'percentage', 'Dönüşüm oranı'),
('last_backup', '', 'datetime', 'Son yedekleme tarihi'),
('site_status', 'active', 'status', 'Site durumu'),
('maintenance_mode', 'off', 'boolean', 'Bakım modu');