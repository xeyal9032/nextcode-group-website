-- Check current contact_messages table structure
-- This will show us which columns already exist

-- Show all columns in the table
SHOW COLUMNS FROM `contact_messages`;

-- Show table structure in detail
DESCRIBE `contact_messages`;

-- Check if specific columns exist
SELECT 
    'phone' as column_name,
    CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'contact_messages' 
    AND COLUMN_NAME = 'phone'

UNION ALL

SELECT 
    'company' as column_name,
    CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'contact_messages' 
    AND COLUMN_NAME = 'company'

UNION ALL

SELECT 
    'service' as column_name,
    CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'contact_messages' 
    AND COLUMN_NAME = 'service'

UNION ALL

SELECT 
    'budget' as column_name,
    CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'contact_messages' 
    AND COLUMN_NAME = 'budget'

UNION ALL

SELECT 
    'is_read' as column_name,
    CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'contact_messages' 
    AND COLUMN_NAME = 'is_read'

UNION ALL

SELECT 
    'ip_address' as column_name,
    CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'contact_messages' 
    AND COLUMN_NAME = 'ip_address'

UNION ALL

SELECT 
    'user_agent' as column_name,
    CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'contact_messages' 
    AND COLUMN_NAME = 'user_agent'

UNION ALL

SELECT 
    'updated_at' as column_name,
    CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'contact_messages' 
    AND COLUMN_NAME = 'updated_at';
