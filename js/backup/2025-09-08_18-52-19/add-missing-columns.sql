-- Add only missing columns to contact_messages table
-- This script will only add columns that don't already exist

-- Add company column (if missing)
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

-- Add service column (if missing)
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

-- Add budget column (if missing)
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

-- Add is_read column (if missing)
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

-- Add ip_address column (if missing)
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

-- Add user_agent column (if missing)
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

-- Add updated_at column (if missing)
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

-- Show final table structure
DESCRIBE contact_messages;

-- Show success message
SELECT 'Contact messages table structure updated successfully!' as message;
