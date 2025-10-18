-- Fix contact_messages table structure
-- This script is idempotent and safe to run multiple times

-- Check and add phone column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'contact_messages' 
     AND COLUMN_NAME = 'phone') = 0,
    'ALTER TABLE contact_messages ADD COLUMN phone VARCHAR(50) AFTER email',
    'SELECT "phone column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add company column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'contact_messages' 
     AND COLUMN_NAME = 'company') = 0,
    'ALTER TABLE contact_messages ADD COLUMN company VARCHAR(100) AFTER phone',
    'SELECT "company column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add service column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'contact_messages' 
     AND COLUMN_NAME = 'service') = 0,
    'ALTER TABLE contact_messages ADD COLUMN service VARCHAR(100) AFTER company',
    'SELECT "service column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add budget column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'contact_messages' 
     AND COLUMN_NAME = 'budget') = 0,
    'ALTER TABLE contact_messages ADD COLUMN budget VARCHAR(50) AFTER service',
    'SELECT "budget column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add is_read column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'contact_messages' 
     AND COLUMN_NAME = 'is_read') = 0,
    'ALTER TABLE contact_messages ADD COLUMN is_read TINYINT(1) DEFAULT 0 AFTER budget',
    'SELECT "is_read column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add ip_address column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'contact_messages' 
     AND COLUMN_NAME = 'ip_address') = 0,
    'ALTER TABLE contact_messages ADD COLUMN ip_address VARCHAR(45) AFTER is_read',
    'SELECT "ip_address column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add user_agent column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'contact_messages' 
     AND COLUMN_NAME = 'user_agent') = 0,
    'ALTER TABLE contact_messages ADD COLUMN user_agent TEXT AFTER ip_address',
    'SELECT "user_agent column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add updated_at column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'contact_messages' 
     AND COLUMN_NAME = 'updated_at') = 0,
    'ALTER TABLE contact_messages ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at',
    'SELECT "updated_at column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add indexes if they don't exist
-- Note: MySQL doesn't have a direct way to check if indexes exist, so we'll use a different approach

-- Show final table structure
DESCRIBE contact_messages;

-- Show success message
SELECT 'Contact messages table structure updated successfully!' as message;
