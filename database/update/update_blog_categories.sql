-- Blog Kategorilerini Azerbaycan Türkçesine Güncelleme
-- Bu script mevcut kategorileri günceller

USE `gtorg_nextcode`;

-- Mevcut kategorileri güncelle
UPDATE `blog_categories` SET 
    `name` = 'Veb İnkişaf', 
    `slug` = 'veb-inkisaf',
    `description` = 'Veb inkişaf trendləri və texnologiyaları'
WHERE `name` = 'Web Development' OR `slug` = 'web-development';

UPDATE `blog_categories` SET 
    `name` = 'E-ticarət', 
    `slug` = 'e-ticaret',
    `description` = 'E-ticarət platformaları və onlayn mağaza inkişafı'
WHERE `name` = 'E-commerce' OR `slug` = 'ecommerce';

UPDATE `blog_categories` SET 
    `name` = 'Kibertəhlükəsizlik', 
    `slug` = 'kibertehlukesizlik',
    `description` = 'Veb təhlükəsizliyi və kibertəhlükəsizlik təcrübələri'
WHERE `name` = 'Cybersecurity' OR `slug` = 'cybersecurity';

-- Eğer kategoriler yoksa ekle
INSERT IGNORE INTO `blog_categories` (`name`, `slug`, `description`) VALUES
('SEO', 'seo', 'Axtarış motoru optimizasiyası məqalələri'),
('Veb İnkişaf', 'veb-inkisaf', 'Veb inkişaf trendləri və texnologiyaları'),
('E-ticarət', 'e-ticaret', 'E-ticarət platformaları və onlayn mağaza inkişafı'),
('Kibertəhlükəsizlik', 'kibertehlukesizlik', 'Veb təhlükəsizliyi və kibertəhlükəsizlik təcrübələri');

-- Blog yazılarının kategorilerini güncelle
UPDATE `blog_posts` SET 
    `title` = 'Müasir Web İnkişaf Trendləri 2024',
    `slug` = 'muasir-web-inkisaf-trendleri-2024',
    `excerpt` = '2024-cü ildə veb inkişaf sahəsindəki ən vacib trendlər: süni intellekt inteqrasiyası, serversiz arxitektura, proqressiv veb tətbiqlər və VebAssembli.'
WHERE `title` = 'Modern Web Development Trends 2024' OR `slug` = 'modern-web-development-trends';

UPDATE `blog_posts` SET 
    `title` = 'E-ticarət İnkişafı: Online Mağaza Yaratmaq',
    `slug` = 'e-ticaret-inkisafi-online-magaza-yaratmaq',
    `excerpt` = 'E-ticarət platforması yaratmaq üçün hərtərəfli təlimat: platform seçimi, ödəniş inteqrasiyası, mobil optimizasiya və performans.'
WHERE `title` = 'E-commerce Development: Online Mağaza Yaratmaq' OR `slug` = 'ecommerce-development-guide';

UPDATE `blog_posts` SET 
    `title` = 'Kibertəhlükəsizlik: Web Təhlükəsizliyi və Ən Yaxşı Təcrübələr',
    `slug` = 'kibertehlukesizlik-web-tehlukesizliyi-ve-en-yaxsi-tecrubeler',
    `excerpt` = 'Veb təhlükəsizliyinin hərtərəfli təlimatı: təhlükələr, OWASP Top 10, təhlükəsizlik tətbiqi və ən yaxşı təcrübələr.'
WHERE `title` = 'Cybersecurity: Web Təhlükəsizliyi və Best Practices' OR `slug` = 'cybersecurity-web-security';

-- Kategori ID'lerini güncelle
UPDATE `blog_posts` SET `category_id` = (
    SELECT `id` FROM `blog_categories` WHERE `name` = 'Veb İnkişaf' LIMIT 1
) WHERE `title` = 'Müasir Web İnkişaf Trendləri 2024';

UPDATE `blog_posts` SET `category_id` = (
    SELECT `id` FROM `blog_categories` WHERE `name` = 'E-ticarət' LIMIT 1
) WHERE `title` = 'E-ticarət İnkişafı: Online Mağaza Yaratmaq';

UPDATE `blog_posts` SET `category_id` = (
    SELECT `id` FROM `blog_categories` WHERE `name` = 'Kibertəhlükəsizlik' LIMIT 1
) WHERE `title` = 'Kibertəhlükəsizlik: Web Təhlükəsizliyi və Ən Yaxşı Təcrübələr';

-- Güncellenen kayıtları göster
SELECT 'Güncellenen Blog Kategorileri:' as 'Sonuç';
SELECT * FROM `blog_categories` ORDER BY `id`;

SELECT 'Güncellenen Blog Yazıları:' as 'Sonuç';
SELECT `id`, `title`, `slug`, `category_id` FROM `blog_posts` ORDER BY `id`;

