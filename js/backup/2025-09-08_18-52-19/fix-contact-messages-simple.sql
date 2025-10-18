-- Simple fix for contact_messages table structure
-- This script works with older MySQL versions

-- Try to add each column individually (will fail silently if column exists)

-- Add phone column
ALTER TABLE `contact_messages` ADD COLUMN `phone` VARCHAR(50) AFTER `email`;

-- Add company column  
ALTER TABLE `contact_messages` ADD COLUMN `company` VARCHAR(100) AFTER `phone`;

-- Add service column
ALTER TABLE `contact_messages` ADD COLUMN `service` VARCHAR(100) AFTER `company`;

-- Add budget column
ALTER TABLE `contact_messages` ADD COLUMN `budget` VARCHAR(50) AFTER `service`;

-- Add is_read column
ALTER TABLE `contact_messages` ADD COLUMN `is_read` TINYINT(1) DEFAULT 0 AFTER `budget`;

-- Add ip_address column
ALTER TABLE `contact_messages` ADD COLUMN `ip_address` VARCHAR(45) AFTER `is_read`;

-- Add user_agent column
ALTER TABLE `contact_messages` ADD COLUMN `user_agent` TEXT AFTER `ip_address`;

-- Add updated_at column
ALTER TABLE `contact_messages` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Show the final table structure
DESCRIBE `contact_messages`;
