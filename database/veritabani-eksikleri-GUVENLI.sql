-- ============================================
-- NextCode Group - Veritabanı Eksiklerini Düzelt
-- MySQL 5.7.44 Uyumlu Versiyon
-- ============================================

-- ⚠️ KULLANIM:
-- 1. phpMyAdmin'de gtorg_nextcode veritabanını seçin
-- 2. Her sorguyu TEK TEK çalıştırın (toplu çalıştırmayın)
-- 3. "Duplicate column" hatası normal (sütun varsa)
-- 4. Diğer sorgulara devam edin

USE gtorg_nextcode;

-- ============================================
-- STEP 1: admin_users - remember_token Ekle
-- ============================================

-- Kontrol et (phpMyAdmin'de çalıştır):
DESCRIBE admin_users;
-- remember_token var mı bak

-- Eğer YOK ise bu sorguyu çalıştır:
ALTER TABLE admin_users 
ADD COLUMN remember_token VARCHAR(255) NULL AFTER password_hash;

-- Index ekle:
ALTER TABLE admin_users 
ADD INDEX idx_remember_token (remember_token);

-- ✅ Tamamlandı: admin_users.remember_token

-- ============================================
-- STEP 2: contact_messages - Status Düzelt
-- ============================================

-- 2.1. Mevcut durumu kontrol et:
SELECT DISTINCT status FROM contact_messages;
-- Mevcut değerler: 'new', 'read', 'replied'
-- Gerekli değerler: 'unread', 'read', 'archived'

-- 2.2. Verileri güncelle:
UPDATE contact_messages 
SET status = 'unread' 
WHERE status = 'new';

UPDATE contact_messages 
SET status = 'archived' 
WHERE status = 'replied';

-- 2.3. Enum'u geçici olarak genişlet:
ALTER TABLE contact_messages 
MODIFY COLUMN status ENUM('new','read','replied','unread','archived') DEFAULT 'unread';

-- 2.4. Eski değerleri kaldır:
ALTER TABLE contact_messages 
MODIFY COLUMN status ENUM('unread','read','archived') DEFAULT 'unread';

-- 2.5. is_read sütununu kaldır (artık kullanılmıyor):
-- Önce verileri migrate et:
UPDATE contact_messages 
SET status = 'read' 
WHERE is_read = 1 AND status = 'unread';

-- Sütunu kaldır:
ALTER TABLE contact_messages 
DROP COLUMN is_read;

-- Index'i kaldır (eğer varsa):
ALTER TABLE contact_messages 
DROP INDEX idx_read;

-- Yeni index ekle:
ALTER TABLE contact_messages 
ADD INDEX idx_status (status);

-- ✅ Tamamlandı: contact_messages.status güncelendi

-- ============================================
-- STEP 3: site_images - image_description Ekle
-- ============================================

-- Kontrol et:
DESCRIBE site_images;
-- image_description var mı bak

-- Eğer YOK ise ekle:
ALTER TABLE site_images 
ADD COLUMN image_description TEXT AFTER image_title;

-- ✅ Tamamlandı: site_images.image_description

-- ============================================
-- STEP 4: portfolio_projects - Sütun İsimleri Düzelt
-- ============================================

-- 4.1. featured → is_featured değiştir:
ALTER TABLE portfolio_projects 
CHANGE COLUMN featured is_featured TINYINT(1) DEFAULT 0;

-- 4.2. is_published ekle (eğer yoksa):
-- Kontrol et:
DESCRIBE portfolio_projects;
-- is_published var mı bak

-- Ekle:
ALTER TABLE portfolio_projects 
ADD COLUMN is_published TINYINT(1) DEFAULT 1 AFTER is_featured;

-- Index'ler ekle:
ALTER TABLE portfolio_projects 
ADD INDEX idx_is_featured (is_featured);

ALTER TABLE portfolio_projects 
ADD INDEX idx_is_published (is_published);

-- ✅ Tamamlandı: portfolio_projects güncelendi

-- ============================================
-- STEP 5: services Tablosu - status Sütunu
-- ============================================

-- Kontrol et:
DESCRIBE services;
-- Mevcut: is_active TINYINT(1)
-- Kod: Bazen status ENUM kullanıyor

-- ⚠️ NOT: Kod zaten is_active kullanıyor, değişiklik gerekmez
-- Ama uyumluluk için status kolonu ekleyebiliriz:

ALTER TABLE services 
ADD COLUMN status ENUM('active','inactive') DEFAULT 'active' AFTER is_active;

-- is_active'e göre status'u set et:
UPDATE services 
SET status = IF(is_active = 1, 'active', 'inactive');

-- ✅ Tamamlandı: services.status

-- ============================================
-- STEP 6: Performans İyileştirme Index'leri
-- ============================================

-- 6.1. contact_messages composite index'ler:
ALTER TABLE contact_messages 
ADD INDEX idx_email_status (email, status);

ALTER TABLE contact_messages 
ADD INDEX idx_created_status (created_at, status);

-- 6.2. blog_posts composite index'ler:
ALTER TABLE blog_posts 
ADD INDEX idx_category_status (category_id, status);

ALTER TABLE blog_posts 
ADD INDEX idx_status_published (status, published_at);

-- 6.3. portfolio_projects composite index'ler:
ALTER TABLE portfolio_projects 
ADD INDEX idx_category_published (category, is_published);

ALTER TABLE portfolio_projects 
ADD INDEX idx_featured_published (is_featured, is_published);

-- 6.4. site_content composite index'ler:
ALTER TABLE site_content 
ADD INDEX idx_page_active (page_name, is_active);

ALTER TABLE site_content 
ADD INDEX idx_section_active (section_name, is_active);

-- ✅ Tamamlandı: Performans index'leri

-- ============================================
-- FINAL KONTROL
-- ============================================

-- Tüm değişiklikleri kontrol et:

-- 1. admin_users
SELECT 
    'admin_users' as tablo,
    COLUMN_NAME as sutun,
    COLUMN_TYPE as tip
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'gtorg_nextcode'
AND TABLE_NAME = 'admin_users'
AND COLUMN_NAME IN ('remember_token', 'login_attempts', 'locked_until')
ORDER BY ORDINAL_POSITION;

-- 2. contact_messages
SELECT 
    'contact_messages' as tablo,
    COLUMN_NAME as sutun,
    COLUMN_TYPE as tip
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'gtorg_nextcode'
AND TABLE_NAME = 'contact_messages'
AND COLUMN_NAME IN ('first_name', 'last_name', 'status', 'ip_address', 'user_agent')
ORDER BY ORDINAL_POSITION;

-- 3. site_images
SELECT 
    'site_images' as tablo,
    COLUMN_NAME as sutun,
    COLUMN_TYPE as tip
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'gtorg_nextcode'
AND TABLE_NAME = 'site_images'
AND COLUMN_NAME IN ('image_key', 'image_url', 'image_alt', 'image_title', 'image_description')
ORDER BY ORDINAL_POSITION;

-- 4. portfolio_projects
SELECT 
    'portfolio_projects' as tablo,
    COLUMN_NAME as sutun,
    COLUMN_TYPE as tip
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'gtorg_nextcode'
AND TABLE_NAME = 'portfolio_projects'
AND COLUMN_NAME IN ('is_featured', 'is_published', 'status')
ORDER BY ORDINAL_POSITION;

-- 5. Index'leri kontrol et:
SELECT 
    TABLE_NAME as tablo,
    INDEX_NAME as index_adi,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) as sutunlar
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'gtorg_nextcode'
AND (
    (TABLE_NAME = 'admin_users' AND INDEX_NAME = 'idx_remember_token')
    OR (TABLE_NAME = 'contact_messages' AND INDEX_NAME IN ('idx_status', 'idx_email_status'))
    OR (TABLE_NAME = 'portfolio_projects' AND INDEX_NAME IN ('idx_is_featured', 'idx_is_published'))
)
GROUP BY TABLE_NAME, INDEX_NAME
ORDER BY TABLE_NAME, INDEX_NAME;

-- ============================================
-- ÖZET
-- ============================================

-- Yapılan değişiklikler:
-- ✅ admin_users: remember_token eklendi
-- ✅ contact_messages: status güncellendi, is_read kaldırıldı
-- ✅ site_images: image_description eklendi
-- ✅ portfolio_projects: is_featured, is_published düzeltildi
-- ✅ services: status eklendi
-- ✅ 12+ performans index'i eklendi

-- ============================================
-- YEDEK ALMA (Önemli!)
-- ============================================

-- Migration öncesi yedek almayı unutmayın:
-- mysqldump -u gtorg_nextcode -p gtorg_nextcode > backup_before_migration.sql

-- ============================================
-- GERİ ALMA (Rollback)
-- ============================================

-- Eğer bir şeyler yanlış giderse:

-- admin_users:
-- ALTER TABLE admin_users DROP COLUMN remember_token;
-- ALTER TABLE admin_users DROP INDEX idx_remember_token;

-- contact_messages:
-- ALTER TABLE contact_messages ADD COLUMN is_read TINYINT(1) DEFAULT 0;
-- ALTER TABLE contact_messages MODIFY COLUMN status ENUM('new','read','replied') DEFAULT 'new';

-- site_images:
-- ALTER TABLE site_images DROP COLUMN image_description;

-- portfolio_projects:
-- ALTER TABLE portfolio_projects CHANGE COLUMN is_featured featured TINYINT(1) DEFAULT 0;
-- ALTER TABLE portfolio_projects DROP COLUMN is_published;

-- ============================================

