-- ============================================
-- NextCode Group - Veritabanı Güncelleme SQL
-- Manuel Migration Scripti
-- ============================================

-- KULLANIM:
-- 1. phpMyAdmin'de gtorg_nextcode veritabanını seçin
-- 2. SQL sekmesine gidin
-- 3. Bu dosyanın içeriğini yapıştırın
-- 4. "Devam Et" butonuna tıklayın

-- ⚠️ ÖNEMLİ: Çalıştırmadan önce veritabanı yedeği alın!

-- ============================================
-- MIGRATION 1: contact_messages Tablosu
-- ============================================

-- 1.1. Mevcut 'name' sütununu 'first_name' olarak yeniden adlandır (eğer varsa)
-- Not: Eğer hata verirse, bu satırı atlayın (sütun zaten yoktur)
-- ALTER TABLE contact_messages CHANGE name first_name VARCHAR(255) NOT NULL;

-- 1.2. first_name sütununu ekle (eğer yoksa)
ALTER TABLE contact_messages 
ADD COLUMN IF NOT EXISTS first_name VARCHAR(255) NOT NULL AFTER id;

-- 1.3. last_name sütununu ekle (eğer yoksa)
ALTER TABLE contact_messages 
ADD COLUMN IF NOT EXISTS last_name VARCHAR(255) NOT NULL AFTER first_name;

-- 1.4. status sütununu ekle (eğer yoksa)
ALTER TABLE contact_messages 
ADD COLUMN IF NOT EXISTS status ENUM('unread', 'read', 'archived') DEFAULT 'unread' AFTER message;

-- 1.5. ip_address sütununu ekle (eğer yoksa)
ALTER TABLE contact_messages 
ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45) AFTER status;

-- 1.6. user_agent sütununu ekle (eğer yoksa)
ALTER TABLE contact_messages 
ADD COLUMN IF NOT EXISTS user_agent TEXT AFTER ip_address;

-- 1.7. Index'leri ekle
ALTER TABLE contact_messages 
ADD INDEX IF NOT EXISTS idx_status (status);

ALTER TABLE contact_messages 
ADD INDEX IF NOT EXISTS idx_created_at (created_at);

ALTER TABLE contact_messages 
ADD INDEX IF NOT EXISTS idx_email (email);

-- ============================================
-- MIGRATION 2: admin_users Tablosu
-- ============================================

-- 2.1. remember_token sütununu ekle (eğer yoksa)
ALTER TABLE admin_users 
ADD COLUMN IF NOT EXISTS remember_token VARCHAR(255) NULL AFTER password_hash;

-- 2.2. remember_token index'i ekle
ALTER TABLE admin_users 
ADD INDEX IF NOT EXISTS idx_remember_token (remember_token);

-- 2.3. login_attempts sütununu ekle (eğer yoksa)
ALTER TABLE admin_users 
ADD COLUMN IF NOT EXISTS login_attempts INT DEFAULT 0 AFTER last_login;

-- 2.4. locked_until sütununu ekle (eğer yoksa)
ALTER TABLE admin_users 
ADD COLUMN IF NOT EXISTS locked_until TIMESTAMP NULL AFTER login_attempts;

-- 2.5. ip_address sütununu ekle (eğer yoksa)
ALTER TABLE admin_users 
ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45) AFTER locked_until;

-- 2.6. user_agent sütununu ekle (eğer yoksa)
ALTER TABLE admin_users 
ADD COLUMN IF NOT EXISTS user_agent TEXT AFTER ip_address;

-- ============================================
-- BONUS: Performans İyileştirmeleri (Opsiyonel)
-- ============================================

-- Blog posts için composite index
ALTER TABLE blog_posts 
ADD INDEX IF NOT EXISTS idx_category_status (category_id, status);

-- Portfolio projects için composite index
ALTER TABLE portfolio_projects 
ADD INDEX IF NOT EXISTS idx_category_published (category, is_published);

-- Site content için performans index
ALTER TABLE site_content 
ADD INDEX IF NOT EXISTS idx_page_active (page_name, is_active);

-- ============================================
-- KONTROL SORĞULARI
-- ============================================

-- Değişiklikleri kontrol edin:

-- contact_messages kontrol
DESCRIBE contact_messages;

-- admin_users kontrol
DESCRIBE admin_users;

-- Son eklenen mesajı kontrol et
SELECT * FROM contact_messages 
ORDER BY created_at DESC 
LIMIT 1;

-- Admin kullanıcısını kontrol et
SELECT 
    username, 
    remember_token, 
    login_attempts, 
    locked_until,
    last_login 
FROM admin_users 
WHERE username = 'admin';

-- ============================================
-- NOT: 
-- - MySQL 5.7+'da IF NOT EXISTS kullanabilirsiniz
-- - Daha eski versiyonlarda hata alırsanız, 
--   IF NOT EXISTS kısmını kaldırın ve tek tek çalıştırın
-- ============================================

