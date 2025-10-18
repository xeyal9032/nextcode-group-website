-- ============================================
-- NextCode Web Projesi - Test SQL Sorguları
-- ============================================

-- TEST 1: Contact Messages Tablosu Kontrolü
-- ============================================

-- 1.1. Tablo yapısını kontrol et
DESCRIBE contact_messages;
-- Beklenen sütunlar: id, first_name, last_name, email, phone, subject, message, status, ip_address, user_agent, created_at, updated_at

-- 1.2. Son gönderilen mesajı kontrol et
SELECT 
    id,
    first_name,
    last_name,
    email,
    phone,
    subject,
    LEFT(message, 50) as message_preview,
    status,
    ip_address,
    created_at
FROM contact_messages
ORDER BY created_at DESC
LIMIT 1;

-- 1.3. Test mesajını bul (email ile)
SELECT * FROM contact_messages
WHERE email = 'test@nextcode.az'
ORDER BY created_at DESC
LIMIT 1;

-- 1.4. Bugünkü tüm mesajları listele
SELECT 
    first_name,
    last_name,
    email,
    subject,
    status,
    created_at
FROM contact_messages
WHERE DATE(created_at) = CURDATE()
ORDER BY created_at DESC;

-- 1.5. first_name ve last_name split kontrolü
SELECT 
    first_name,
    last_name,
    CONCAT(first_name, ' ', last_name) as full_name,
    email
FROM contact_messages
ORDER BY created_at DESC
LIMIT 5;

-- ============================================
-- TEST 2: Admin Users - Remember Token
-- ============================================

-- 2.1. Admin users tablosu yapısı
DESCRIBE admin_users;
-- remember_token sütunu var mı kontrol et

-- 2.2. Admin kullanıcısının remember_token'ını kontrol et
SELECT 
    id,
    username,
    email,
    remember_token,
    last_login,
    login_attempts,
    locked_until,
    is_active
FROM admin_users
WHERE username = 'admin';

-- 2.3. Remember token'ı olan kullanıcılar
SELECT 
    username,
    LENGTH(remember_token) as token_length,
    last_login
FROM admin_users
WHERE remember_token IS NOT NULL;

-- ============================================
-- TEST 3: Site Content - XSS Kontrolü
-- ============================================

-- 3.1. Tüm site içeriklerini listele
SELECT 
    id,
    page_name,
    section_name,
    content_key,
    LEFT(content_value, 100) as content_preview,
    content_type,
    is_active
FROM site_content
WHERE is_active = 1
ORDER BY page_name, section_name;

-- 3.2. Tehlikeli karakterler içeren içerikleri bul
SELECT 
    id,
    page_name,
    content_key,
    content_value
FROM site_content
WHERE content_value LIKE '%<script%'
   OR content_value LIKE '%javascript:%'
   OR content_value LIKE '%onerror=%'
   OR content_value LIKE '%onclick=%';

-- 3.3. Test için XSS içerik ekle (SADECE TEST İÇİN!)
-- INSERT INTO site_content (page_name, section_name, content_key, content_value, content_type, is_active)
-- VALUES ('test', 'test', 'xss_test', '<script>alert("XSS")</script>', 'html', 1);

-- 3.4. Test içeriğini sil
-- DELETE FROM site_content WHERE content_key = 'xss_test';

-- ============================================
-- TEST 4: Genel İstatistikler
-- ============================================

-- 4.1. Toplam mesaj sayısı
SELECT COUNT(*) as total_messages FROM contact_messages;

-- 4.2. Durumlara göre mesaj dağılımı
SELECT 
    status,
    COUNT(*) as count
FROM contact_messages
GROUP BY status;

-- 4.3. Son 7 günde gelen mesajlar
SELECT 
    DATE(created_at) as date,
    COUNT(*) as message_count
FROM contact_messages
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created_at)
ORDER BY date DESC;

-- 4.4. En çok mesaj gönderen email'ler
SELECT 
    email,
    COUNT(*) as message_count,
    MAX(created_at) as last_message
FROM contact_messages
GROUP BY email
ORDER BY message_count DESC
LIMIT 10;

-- ============================================
-- TEST 5: Acil Durum - Tablo Düzeltmeleri
-- ============================================

-- 5.1. Eğer first_name/last_name yoksa ve name varsa:
-- ALTER TABLE contact_messages CHANGE name first_name VARCHAR(255) NOT NULL;
-- ALTER TABLE contact_messages ADD COLUMN last_name VARCHAR(255) NOT NULL AFTER first_name;

-- 5.2. Eğer remember_token yoksa:
-- ALTER TABLE admin_users ADD COLUMN remember_token VARCHAR(255) NULL AFTER password_hash;
-- ALTER TABLE admin_users ADD INDEX idx_remember_token (remember_token);

-- 5.3. Eğer status sütunu yoksa (eski is_read yerine):
-- ALTER TABLE contact_messages ADD COLUMN status ENUM('unread', 'read', 'archived') DEFAULT 'unread' AFTER message;

-- ============================================
-- TEST 6: Veritabanı Temizliği (Opsiyonel)
-- ============================================

-- 6.1. Test mesajlarını sil (DİKKATLİ!)
-- DELETE FROM contact_messages WHERE email LIKE '%test%' OR email LIKE '%@example.com';

-- 6.2. 30 günden eski okunmuş mesajları sil (DİKKATLİ!)
-- DELETE FROM contact_messages 
-- WHERE status = 'read' 
-- AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- 6.3. Arşivlenmiş mesajları sil (DİKKATLİ!)
-- DELETE FROM contact_messages WHERE status = 'archived';

-- ============================================
-- TEST 7: Performans Kontrolleri
-- ============================================

-- 7.1. Index'leri kontrol et
SHOW INDEX FROM contact_messages;

-- 7.2. Tablo boyutunu kontrol et
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "Size (MB)",
    table_rows
FROM information_schema.TABLES
WHERE table_schema = 'gtorg_nextcode'
AND table_name = 'contact_messages';

-- 7.3. Yavaş sorguları test et
EXPLAIN SELECT * FROM contact_messages 
WHERE email = 'test@nextcode.az' 
ORDER BY created_at DESC;

-- ============================================
-- HIZLI TEST SORGUSU (Tümünü Kontrol Et)
-- ============================================

SELECT 
    'Contact Messages' as test_name,
    (SELECT COUNT(*) FROM contact_messages) as total_count,
    (SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = CURDATE()) as today_count,
    (SELECT COUNT(*) FROM contact_messages WHERE first_name IS NULL) as missing_first_name

UNION ALL

SELECT 
    'Admin Users',
    (SELECT COUNT(*) FROM admin_users) as total_count,
    (SELECT COUNT(*) FROM admin_users WHERE is_active = 1) as active_count,
    (SELECT COUNT(*) FROM admin_users WHERE remember_token IS NOT NULL) as has_token

UNION ALL

SELECT 
    'Site Content',
    (SELECT COUNT(*) FROM site_content) as total_count,
    (SELECT COUNT(*) FROM site_content WHERE is_active = 1) as active_count,
    (SELECT COUNT(*) FROM site_content WHERE content_value LIKE '%<script%') as dangerous_content;

-- ============================================
-- NOT: Bu dosyayı phpMyAdmin'de veya MySQL komut satırında çalıştırabilirsiniz
-- Dikkatli olun: DELETE sorguları yorumlanmıştır (--), kullanmadan önce yorumu kaldırın
-- ============================================

