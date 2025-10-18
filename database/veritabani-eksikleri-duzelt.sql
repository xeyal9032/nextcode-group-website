-- ============================================
-- NextCode Group - Veritabanı Eksiklerini Düzelt
-- Mevcut Veritabanı ile Kod Uyumluluğu
-- ============================================

-- Tarih: 12 Ekim 2025
-- MySQL Version: 5.7.44
-- Database: gtorg_nextcode

-- ⚠️ ÖNEMLİ: Çalıştırmadan önce YEDEK alin!
-- mysqldump -u gtorg_nextcode -p gtorg_nextcode > backup_$(date +%Y%m%d_%H%M%S).sql

USE gtorg_nextcode;

-- ============================================
-- MIGRATION 1: admin_users Tablosu
-- ============================================

-- 1.1. remember_token sütunu ekle (Remember Me özelliği için GEREKLI)
ALTER TABLE admin_users 
ADD COLUMN remember_token VARCHAR(255) NULL AFTER password_hash;

-- 1.2. remember_token index ekle (performans için)
ALTER TABLE admin_users 
ADD INDEX idx_remember_token (remember_token);

-- KONTROL:
-- DESCRIBE admin_users;
-- Beklenen: remember_token VARCHAR(255) NULL

-- ============================================
-- MIGRATION 2: contact_messages Tablosu
-- ============================================

-- 2.1. status ENUM değerlerini güncelle
-- Mevcut: 'new','read','replied'
-- Gerekli: 'unread','read','archived'

-- Önce 'new' değerlerini 'unread' olarak güncelle
UPDATE contact_messages 
SET status = 'unread' 
WHERE status = 'new';

-- 'replied' değerlerini 'archived' olarak güncelle
UPDATE contact_messages 
SET status = 'archived' 
WHERE status = 'replied';

-- Enum tipini güncelle (MySQL 5.7 için 2 adımda)
ALTER TABLE contact_messages 
MODIFY COLUMN status ENUM('new','read','replied','unread','archived') DEFAULT 'unread';

-- Artık 'new' ve 'replied' değerleri kullanılmadığı için kaldır
ALTER TABLE contact_messages 
MODIFY COLUMN status ENUM('unread','read','archived') DEFAULT 'unread';

-- 2.2. is_read sütununu kaldır (artık status kullanılıyor)
-- Önce verileri status'a migrate et (eğer is_read kullanılıyorsa)
UPDATE contact_messages 
SET status = 'read' 
WHERE is_read = 1 AND status = 'unread';

-- Sütunu kaldır
ALTER TABLE contact_messages 
DROP COLUMN is_read;

-- 2.3. Index'leri kontrol et ve ekle
ALTER TABLE contact_messages 
DROP INDEX IF EXISTS idx_read;

ALTER TABLE contact_messages 
ADD INDEX IF NOT EXISTS idx_status (status);

-- KONTROL:
-- DESCRIBE contact_messages;
-- SELECT status, COUNT(*) FROM contact_messages GROUP BY status;

-- ============================================
-- MIGRATION 3: services Tablosu
-- ============================================

-- 3.1. status sütunu ekle (eğer yoksa)
-- Mevcut: is_active TINYINT(1)
-- Kod bazen: status ENUM('active','inactive')

-- is_active'i kullanmaya devam et (uyumlu)
-- Ama status alanı da olabilir, kontrol et:

-- Eğer status kolonu yoksa ve is_active varsa, bir şey yapma (kod zaten uyumlu)
-- SHOW COLUMNS FROM services LIKE 'status';

-- ============================================
-- MIGRATION 4: site_images Tablosu
-- ============================================

-- 4.1. image_description sütunu ekle (eğer yoksa)
ALTER TABLE site_images 
ADD COLUMN IF NOT EXISTS image_description TEXT AFTER image_title;

-- 4.2. image_type sütunu kontrol et (zaten var)
-- ALTER TABLE site_images 
-- ADD COLUMN IF NOT EXISTS image_type ENUM('image','icon','logo','background','banner') DEFAULT 'image';

-- KONTROL:
-- DESCRIBE site_images;

-- ============================================
-- MIGRATION 5: portfolio_projects Tablosu
-- ============================================

-- 5.1. is_featured ve is_published sütunları ekle (eğer yoksa)
-- Mevcut tabloda: featured (featured yerine is_featured olmalı)

-- featured sütununu is_featured olarak değiştir
ALTER TABLE portfolio_projects 
CHANGE COLUMN featured is_featured TINYINT(1) DEFAULT 0;

-- is_published sütunu ekle
ALTER TABLE portfolio_projects 
ADD COLUMN IF NOT EXISTS is_published TINYINT(1) DEFAULT 1 AFTER is_featured;

-- Index ekle
ALTER TABLE portfolio_projects 
ADD INDEX IF NOT EXISTS idx_featured (is_featured);

ALTER TABLE portfolio_projects 
ADD INDEX IF NOT EXISTS idx_published (is_published);

-- KONTROL:
-- DESCRIBE portfolio_projects;

-- ============================================
-- MIGRATION 6: Index Optimizasyonları
-- ============================================

-- 6.1. contact_messages için composite index
ALTER TABLE contact_messages 
ADD INDEX IF NOT EXISTS idx_email_status (email, status);

ALTER TABLE contact_messages 
ADD INDEX IF NOT EXISTS idx_created_status (created_at, status);

-- 6.2. blog_posts için composite index
ALTER TABLE blog_posts 
ADD INDEX IF NOT EXISTS idx_category_status (category_id, status);

ALTER TABLE blog_posts 
ADD INDEX IF NOT EXISTS idx_status_published (status, published_at);

-- 6.3. portfolio_projects için composite index
ALTER TABLE portfolio_projects 
ADD INDEX IF NOT EXISTS idx_category_published (category, is_published);

ALTER TABLE portfolio_projects 
ADD INDEX IF NOT EXISTS idx_featured_published (is_featured, is_published);

-- 6.4. site_content için composite index
ALTER TABLE site_content 
ADD INDEX IF NOT EXISTS idx_page_active (page_name, is_active);

ALTER TABLE site_content 
ADD INDEX IF NOT EXISTS idx_section_active (section_name, is_active);

-- ============================================
-- MIGRATION 7: Data Cleanup (Opsiyonel)
-- ============================================

-- 7.1. portfolio_projects'te slug yoksa oluştur
UPDATE portfolio_projects 
SET slug = LOWER(REPLACE(REPLACE(title, ' ', '-'), 'ə', 'e'))
WHERE slug IS NULL OR slug = '';

-- 7.2. site_content'te boş değerleri temizle
-- DELETE FROM site_content WHERE content_value = 'İçerik eklenmeli' OR content_value = '';

-- ============================================
-- KONTROL SORĞULARI
-- ============================================

-- Tüm değişiklikleri kontrol et:

SELECT 'admin_users' as tablo, 
       COUNT(*) as sutun_sayisi 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'gtorg_nextcode' 
AND TABLE_NAME = 'admin_users';

SELECT 'contact_messages' as tablo,
       COUNT(*) as sutun_sayisi
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'gtorg_nextcode' 
AND TABLE_NAME = 'contact_messages';

-- Eksik sütunları bul:
SELECT 
    'admin_users' as tablo,
    'remember_token' as sutun,
    CASE 
        WHEN COUNT(*) > 0 THEN '✅ VAR' 
        ELSE '❌ YOK' 
    END as durum
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'gtorg_nextcode' 
AND TABLE_NAME = 'admin_users'
AND COLUMN_NAME = 'remember_token'

UNION ALL

SELECT 
    'site_images' as tablo,
    'image_description' as sutun,
    CASE 
        WHEN COUNT(*) > 0 THEN '✅ VAR' 
        ELSE '❌ YOK' 
    END as durum
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'gtorg_nextcode' 
AND TABLE_NAME = 'site_images'
AND COLUMN_NAME = 'image_description'

UNION ALL

SELECT 
    'portfolio_projects' as tablo,
    'is_featured' as sutun,
    CASE 
        WHEN COUNT(*) > 0 THEN '✅ VAR' 
        ELSE '❌ YOK' 
    END as durum
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'gtorg_nextcode' 
AND TABLE_NAME = 'portfolio_projects'
AND COLUMN_NAME = 'is_featured';

-- Index'leri kontrol et:
SHOW INDEX FROM admin_users WHERE Key_name = 'idx_remember_token';
SHOW INDEX FROM contact_messages WHERE Key_name = 'idx_status';
SHOW INDEX FROM portfolio_projects WHERE Key_name = 'idx_featured';

-- ============================================
-- SONUÇ KONTROLÜ
-- ============================================

-- Tüm migrations başarılı mı kontrol et:
SELECT 
    TABLE_NAME as tablo,
    COLUMN_NAME as sutun,
    COLUMN_TYPE as tip,
    IS_NULLABLE as nullable,
    COLUMN_DEFAULT as varsayilan
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'gtorg_nextcode'
AND (
    (TABLE_NAME = 'admin_users' AND COLUMN_NAME = 'remember_token')
    OR (TABLE_NAME = 'site_images' AND COLUMN_NAME = 'image_description')
    OR (TABLE_NAME = 'portfolio_projects' AND COLUMN_NAME = 'is_featured')
    OR (TABLE_NAME = 'portfolio_projects' AND COLUMN_NAME = 'is_published')
)
ORDER BY TABLE_NAME, COLUMN_NAME;

-- ============================================
-- NOT: 
-- - MySQL 5.7.44 versiyonunuzda IF NOT EXISTS çalışmayabilir
-- - Hata alırsanız IF NOT EXISTS kısmını kaldırın
-- - Her sorguyu tek tek çalıştırın
-- - "Duplicate column" hatası normal (sütun zaten varsa)
-- ============================================

