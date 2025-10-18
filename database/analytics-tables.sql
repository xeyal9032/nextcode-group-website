-- NextCode Group Analytics Database Tables
-- Google Analytics 4 ve Custom Analytics için gerekli tablolar

-- Analytics Events Tablosu
CREATE TABLE IF NOT EXISTS `analytics_events` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `event_type` varchar(100) NOT NULL COMMENT 'Event türü (page_view, click, scroll, etc.)',
    `user_id` varchar(255) NOT NULL COMMENT 'Unique user identifier',
    `session_id` varchar(255) DEFAULT NULL COMMENT 'Session identifier',
    `page_location` text DEFAULT NULL COMMENT 'Page URL',
    `page_title` varchar(500) DEFAULT NULL COMMENT 'Page title',
    `event_data` json DEFAULT NULL COMMENT 'Event-specific data',
    `user_agent` text DEFAULT NULL COMMENT 'User agent string',
    `ip_address` varchar(45) DEFAULT NULL COMMENT 'Client IP address',
    `referrer` text DEFAULT NULL COMMENT 'Referrer URL',
    `utm_source` varchar(100) DEFAULT NULL COMMENT 'UTM source parameter',
    `utm_medium` varchar(100) DEFAULT NULL COMMENT 'UTM medium parameter',
    `utm_campaign` varchar(100) DEFAULT NULL COMMENT 'UTM campaign parameter',
    `utm_term` varchar(100) DEFAULT NULL COMMENT 'UTM term parameter',
    `utm_content` varchar(100) DEFAULT NULL COMMENT 'UTM content parameter',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_event_type` (`event_type`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_session_id` (`session_id`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_page_location` (`page_location`(255)),
    KEY `idx_utm_source` (`utm_source`),
    KEY `idx_utm_campaign` (`utm_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Analytics events tracking table';

-- Analytics User Properties Tablosu
CREATE TABLE IF NOT EXISTS `analytics_user_properties` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` varchar(255) NOT NULL COMMENT 'Unique user identifier',
    `property_name` varchar(100) NOT NULL COMMENT 'Property name (user_type, subscription_level, etc.)',
    `property_value` text NOT NULL COMMENT 'Property value',
    `property_category` varchar(50) DEFAULT NULL COMMENT 'Property category (user, device, browser, etc.)',
    `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Property active status',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_property` (`user_id`, `property_name`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_property_name` (`property_name`),
    KEY `idx_property_category` (`property_category`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User properties and attributes table';

-- Analytics Performance Tablosu
CREATE TABLE IF NOT EXISTS `analytics_performance` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` varchar(255) NOT NULL COMMENT 'Unique user identifier',
    `session_id` varchar(255) DEFAULT NULL COMMENT 'Session identifier',
    `metric_name` varchar(100) NOT NULL COMMENT 'Performance metric name (LCP, FID, CLS, etc.)',
    `metric_value` decimal(10,4) NOT NULL COMMENT 'Metric value',
    `metric_unit` varchar(20) DEFAULT NULL COMMENT 'Metric unit (ms, %, etc.)',
    `page_location` text DEFAULT NULL COMMENT 'Page URL where metric was measured',
    `page_title` varchar(500) DEFAULT NULL COMMENT 'Page title',
    `user_agent` text DEFAULT NULL COMMENT 'User agent string',
    `device_category` varchar(20) DEFAULT NULL COMMENT 'Device category (desktop, mobile, tablet)',
    `connection_speed` varchar(20) DEFAULT NULL COMMENT 'Connection speed (4g, 3g, 2g, slow-2g)',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_session_id` (`session_id`),
    KEY `idx_metric_name` (`metric_name`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_device_category` (`device_category`),
    KEY `idx_connection_speed` (`connection_speed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Performance metrics tracking table';

-- Analytics User Engagement Tablosu
CREATE TABLE IF NOT EXISTS `analytics_user_engagement` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` varchar(255) NOT NULL COMMENT 'Unique user identifier',
    `session_id` varchar(255) NOT NULL COMMENT 'Session identifier',
    `page_location` text NOT NULL COMMENT 'Page URL',
    `page_title` varchar(500) DEFAULT NULL COMMENT 'Page title',
    `engagement_time` int(11) NOT NULL DEFAULT 0 COMMENT 'Engagement time in seconds',
    `scroll_depth` tinyint(3) unsigned DEFAULT 0 COMMENT 'Scroll depth percentage (0-100)',
    `interaction_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Number of interactions (clicks, form submissions, etc.)',
    `form_interactions` int(11) NOT NULL DEFAULT 0 COMMENT 'Number of form interactions',
    `video_interactions` int(11) NOT NULL DEFAULT 0 COMMENT 'Number of video interactions',
    `user_agent` text DEFAULT NULL COMMENT 'User agent string',
    `device_category` varchar(20) DEFAULT NULL COMMENT 'Device category',
    `browser` varchar(50) DEFAULT NULL COMMENT 'Browser name',
    `os` varchar(50) DEFAULT NULL COMMENT 'Operating system',
    `screen_resolution` varchar(20) DEFAULT NULL COMMENT 'Screen resolution (e.g., 1920x1080)',
    `language` varchar(10) DEFAULT NULL COMMENT 'User language preference',
    `timezone` varchar(50) DEFAULT NULL COMMENT 'User timezone',
    `session_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Session start time',
    `session_end` timestamp NULL DEFAULT NULL COMMENT 'Session end time',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_session` (`session_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_session_id` (`session_id`),
    KEY `idx_page_location` (`page_location`(255)),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_device_category` (`device_category`),
    KEY `idx_browser` (`browser`),
    KEY `idx_os` (`os`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User engagement and session tracking table';

-- Analytics E-commerce Tablosu
CREATE TABLE IF NOT EXISTS `analytics_ecommerce` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `event_type` varchar(50) NOT NULL COMMENT 'E-commerce event type (view_item, add_to_cart, purchase, etc.)',
    `user_id` varchar(255) NOT NULL COMMENT 'Unique user identifier',
    `session_id` varchar(255) DEFAULT NULL COMMENT 'Session identifier',
    `page_location` text DEFAULT NULL COMMENT 'Page URL',
    `page_title` varchar(500) DEFAULT NULL COMMENT 'Page title',
    `item_id` varchar(255) DEFAULT NULL COMMENT 'Product/item ID',
    `item_name` varchar(500) DEFAULT NULL COMMENT 'Product/item name',
    `item_category` varchar(100) DEFAULT NULL COMMENT 'Product category',
    `item_brand` varchar(100) DEFAULT NULL COMMENT 'Product brand',
    `item_variant` varchar(100) DEFAULT NULL COMMENT 'Product variant',
    `price` decimal(10,2) DEFAULT NULL COMMENT 'Product price',
    `quantity` int(11) NOT NULL DEFAULT 1 COMMENT 'Product quantity',
    `value` decimal(10,2) DEFAULT NULL COMMENT 'Total transaction value',
    `currency` varchar(3) NOT NULL DEFAULT 'USD' COMMENT 'Currency code',
    `transaction_id` varchar(255) DEFAULT NULL COMMENT 'Transaction ID',
    `affiliation` varchar(100) DEFAULT NULL COMMENT 'Affiliation/store name',
    `tax` decimal(10,2) DEFAULT NULL COMMENT 'Tax amount',
    `shipping` decimal(10,2) DEFAULT NULL COMMENT 'Shipping cost',
    `coupon` varchar(100) DEFAULT NULL COMMENT 'Coupon code',
    `event_data` json DEFAULT NULL COMMENT 'Additional event data',
    `user_agent` text DEFAULT NULL COMMENT 'User agent string',
    `ip_address` varchar(45) DEFAULT NULL COMMENT 'Client IP address',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_event_type` (`event_type`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_session_id` (`session_id`),
    KEY `idx_item_id` (`item_id`),
    KEY `idx_transaction_id` (`transaction_id`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_item_category` (`item_category`),
    KEY `idx_currency` (`currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='E-commerce events and transactions table';

-- Analytics Goals Tablosu
CREATE TABLE IF NOT EXISTS `analytics_goals` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `goal_name` varchar(100) NOT NULL COMMENT 'Goal name',
    `goal_type` varchar(50) NOT NULL COMMENT 'Goal type (page_view, event, duration, pages_per_session)',
    `goal_value` varchar(255) DEFAULT NULL COMMENT 'Goal value (URL, event name, time, etc.)',
    `goal_description` text DEFAULT NULL COMMENT 'Goal description',
    `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Goal active status',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_goal_name` (`goal_name`),
    KEY `idx_goal_type` (`goal_type`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Analytics goals and conversions table';

-- Analytics Goal Conversions Tablosu
CREATE TABLE IF NOT EXISTS `analytics_goal_conversions` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `goal_id` bigint(20) unsigned NOT NULL COMMENT 'Goal ID reference',
    `user_id` varchar(255) NOT NULL COMMENT 'Unique user identifier',
    `session_id` varchar(255) DEFAULT NULL COMMENT 'Session identifier',
    `conversion_value` decimal(10,2) DEFAULT NULL COMMENT 'Conversion value',
    `page_location` text DEFAULT NULL COMMENT 'Page URL where conversion occurred',
    `page_title` varchar(500) DEFAULT NULL COMMENT 'Page title',
    `conversion_time` int(11) DEFAULT NULL COMMENT 'Time to conversion in seconds',
    `funnel_position` tinyint(3) unsigned DEFAULT NULL COMMENT 'Position in conversion funnel',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_goal_id` (`goal_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_session_id` (`session_id`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_goal_conversions_goal` FOREIGN KEY (`goal_id`) REFERENCES `analytics_goals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Goal conversions tracking table';

-- Analytics Custom Dimensions Tablosu
CREATE TABLE IF NOT EXISTS `analytics_custom_dimensions` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `dimension_name` varchar(100) NOT NULL COMMENT 'Custom dimension name',
    `dimension_key` varchar(100) NOT NULL COMMENT 'GA4 custom dimension key (custom_parameter_X)',
    `dimension_scope` varchar(20) NOT NULL DEFAULT 'user' COMMENT 'Dimension scope (user, event, item)',
    `dimension_description` text DEFAULT NULL COMMENT 'Dimension description',
    `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Dimension active status',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_dimension_key` (`dimension_key`),
    UNIQUE KEY `uk_dimension_name` (`dimension_name`),
    KEY `idx_dimension_scope` (`dimension_scope`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Custom dimensions configuration table';

-- Analytics Custom Metrics Tablosu
CREATE TABLE IF NOT EXISTS `analytics_custom_metrics` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `metric_name` varchar(100) NOT NULL COMMENT 'Custom metric name',
    `metric_key` varchar(100) NOT NULL COMMENT 'GA4 custom metric key (custom_metric_X)',
    `metric_type` varchar(20) NOT NULL DEFAULT 'integer' COMMENT 'Metric type (integer, float, time, currency)',
    `metric_unit` varchar(20) DEFAULT NULL COMMENT 'Metric unit (ms, %, $, etc.)',
    `metric_description` text DEFAULT NULL COMMENT 'Metric description',
    `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Metric active status',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_metric_key` (`metric_key`),
    UNIQUE KEY `uk_metric_name` (`metric_name`),
    KEY `idx_metric_type` (`metric_type`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Custom metrics configuration table';

-- Analytics Reports Cache Tablosu
CREATE TABLE IF NOT EXISTS `analytics_reports_cache` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `report_type` varchar(100) NOT NULL COMMENT 'Report type (overview, performance, user_behavior, ecommerce)',
    `report_period` varchar(20) NOT NULL COMMENT 'Report period (7d, 30d, 90d, 1y)',
    `report_data` json NOT NULL COMMENT 'Cached report data',
    `cache_expires` timestamp NOT NULL COMMENT 'Cache expiration time',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_report_cache` (`report_type`, `report_period`),
    KEY `idx_report_type` (`report_type`),
    KEY `idx_report_period` (`report_period`),
    KEY `idx_cache_expires` (`cache_expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Analytics reports cache table';

-- Analytics Alerts Tablosu
CREATE TABLE IF NOT EXISTS `analytics_alerts` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `alert_type` varchar(50) NOT NULL COMMENT 'Alert type (performance, conversion, error, etc.)',
    `alert_severity` enum('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium' COMMENT 'Alert severity level',
    `alert_title` varchar(200) NOT NULL COMMENT 'Alert title',
    `alert_message` text NOT NULL COMMENT 'Alert message',
    `alert_data` json DEFAULT NULL COMMENT 'Additional alert data',
    `is_resolved` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Alert resolved status',
    `resolved_at` timestamp NULL DEFAULT NULL COMMENT 'Alert resolution time',
    `resolved_by` varchar(255) DEFAULT NULL COMMENT 'User who resolved the alert',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_alert_type` (`alert_type`),
    KEY `idx_alert_severity` (`alert_severity`),
    KEY `idx_is_resolved` (`is_resolved`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Analytics alerts and notifications table';

-- Default Custom Dimensions
INSERT IGNORE INTO `analytics_custom_dimensions` (`dimension_name`, `dimension_key`, `dimension_scope`, `dimension_description`) VALUES
('User Type', 'custom_parameter_1', 'user', 'User type (admin, premium, registered, guest)'),
('Subscription Level', 'custom_parameter_2', 'user', 'User subscription level (enterprise, professional, basic, free)'),
('Device Category', 'custom_parameter_3', 'user', 'Device category (desktop, mobile, tablet)'),
('Browser Type', 'custom_parameter_4', 'user', 'Browser type (Chrome, Firefox, Safari, Edge, Opera)'),
('OS Type', 'custom_parameter_5', 'user', 'Operating system type (Windows, macOS, Linux, Android, iOS)');

-- Default Custom Metrics
INSERT IGNORE INTO `analytics_custom_metrics` (`metric_name`, `metric_key`, `metric_type`, `metric_unit`, `metric_description`) VALUES
('Page Load Time', 'custom_metric_1', 'time', 'ms', 'Page load time in milliseconds'),
('User Engagement Time', 'custom_metric_2', 'time', 's', 'User engagement time in seconds'),
('Scroll Depth', 'custom_metric_3', 'integer', '%', 'Page scroll depth percentage'),
('Form Completion Rate', 'custom_metric_4', 'float', '%', 'Form completion rate percentage');

-- Default Goals
INSERT IGNORE INTO `analytics_goals` (`goal_name`, `goal_type`, `goal_value`, `goal_description`) VALUES
('Contact Form Submission', 'event', 'form_submit', 'User submits contact form'),
('Portfolio View', 'page_view', '/portfolio.php', 'User views portfolio page'),
('Service Page Visit', 'page_view', '/services.php', 'User visits services page'),
('Blog Read', 'page_view', '/blog.php', 'User reads blog content'),
('Phone Call Click', 'event', 'click', 'User clicks phone number'),
('Email Click', 'event', 'click', 'User clicks email address');

-- Indexes for better performance
CREATE INDEX `idx_analytics_events_composite` ON `analytics_events` (`event_type`, `created_at`, `user_id`);
CREATE INDEX `idx_analytics_performance_composite` ON `analytics_performance` (`metric_name`, `created_at`, `user_id`);
CREATE INDEX `idx_analytics_ecommerce_composite` ON `analytics_ecommerce` (`event_type`, `created_at`, `user_id`);
CREATE INDEX `idx_analytics_user_engagement_composite` ON `analytics_user_engagement` (`user_id`, `session_start`, `device_category`);

-- Partitioning for large tables (optional - for very high traffic sites)
-- ALTER TABLE `analytics_events` PARTITION BY RANGE (YEAR(created_at)) (
--     PARTITION p2023 VALUES LESS THAN (2024),
--     PARTITION p2024 VALUES LESS THAN (2025),
--     PARTITION p2025 VALUES LESS THAN (2026),
--     PARTITION p_future VALUES LESS THAN MAXVALUE
-- );

-- Views for common analytics queries
CREATE OR REPLACE VIEW `v_analytics_daily_summary` AS
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_events,
    COUNT(DISTINCT user_id) as unique_users,
    COUNT(DISTINCT session_id) as sessions,
    COUNT(CASE WHEN event_type = 'page_view' THEN 1 END) as page_views,
    COUNT(CASE WHEN event_type = 'click' THEN 1 END) as clicks,
    COUNT(CASE WHEN event_type = 'form_submit' THEN 1 END) as form_submissions
FROM analytics_events 
GROUP BY DATE(created_at)
ORDER BY date DESC;

CREATE OR REPLACE VIEW `v_analytics_user_journey` AS
SELECT 
    user_id,
    session_id,
    GROUP_CONCAT(event_type ORDER BY created_at SEPARATOR ' > ') as event_sequence,
    COUNT(*) as event_count,
    MIN(created_at) as session_start,
    MAX(created_at) as session_end,
    TIMESTAMPDIFF(SECOND, MIN(created_at), MAX(created_at)) as session_duration
FROM analytics_events 
GROUP BY user_id, session_id
HAVING event_count > 1
ORDER BY session_start DESC;

-- Stored procedures for common analytics operations
DELIMITER //

CREATE PROCEDURE `sp_cleanup_old_analytics_data`(IN days_to_keep INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Clean up old events
    DELETE FROM analytics_events 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL days_to_keep DAY);
    
    -- Clean up old performance data
    DELETE FROM analytics_performance 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL days_to_keep DAY);
    
    -- Clean up old ecommerce data
    DELETE FROM analytics_ecommerce 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL days_to_keep DAY);
    
    -- Clean up old user engagement data
    DELETE FROM analytics_user_engagement 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL days_to_keep DAY);
    
    -- Clean up old reports cache
    DELETE FROM analytics_reports_cache 
    WHERE cache_expires < NOW();
    
    COMMIT;
    
    SELECT ROW_COUNT() as deleted_rows;
END //

CREATE PROCEDURE `sp_generate_analytics_report`(IN report_type VARCHAR(100), IN period VARCHAR(20))
BEGIN
    DECLARE report_data JSON;
    DECLARE cache_key VARCHAR(200);
    
    -- Check cache first
    SET cache_key = CONCAT(report_type, '_', period);
    
    SELECT report_data INTO report_data
    FROM analytics_reports_cache 
    WHERE report_type = report_type 
    AND report_period = period 
    AND cache_expires > NOW();
    
    IF report_data IS NOT NULL THEN
        SELECT report_data as cached_report;
    ELSE
        -- Generate fresh report based on type
        CASE report_type
            WHEN 'overview' THEN
                CALL sp_generate_overview_report(period);
            WHEN 'performance' THEN
                CALL sp_generate_performance_report(period);
            WHEN 'user_behavior' THEN
                CALL sp_generate_user_behavior_report(period);
            WHEN 'ecommerce' THEN
                CALL sp_generate_ecommerce_report(period);
            ELSE
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid report type';
        END CASE;
    END IF;
END //

DELIMITER ;

-- Comments and documentation
-- Bu tablolar Google Analytics 4 ve custom analytics için gerekli tüm veriyi saklar
-- Performance için uygun indexler eklenmiştir
-- Partitioning opsiyonel olarak eklenebilir (yüksek trafik için)
-- Views ve stored procedures ile yaygın analytics sorguları optimize edilmiştir
