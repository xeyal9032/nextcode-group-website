-- NextCode Group - Sample Data Insertion
-- Tüm tablolar için örnek veri ekleme scripti

USE `gtorg_nextcode`;

-- ==============================================
-- SITE SETTINGS VERİLERİ
-- ==============================================

INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_title', 'NextCode Group - Rəqəmsal Marketinq Agentliyi', 'text', 'Site başlığı'),
('site_description', 'NextCode Group - Azərbaycanda rəqəmsal marketinq sahəsində peşəkar xidmətlər. SEO, sosial media, brendinq və reklam kampaniyaları ilə biznesinizi inkişaf etdirin.', 'textarea', 'Site açıqlaması'),
('site_keywords', 'rəqəmsal marketinq, SEO, sosial media, brendinq, reklam, Azərbaycan', 'text', 'Site açar sözləri'),
('company_name', 'NextCode Group', 'text', 'Şirkət adı'),
('company_email', 'info@nextcodegroup.com', 'text', 'Şirkət email adresi'),
('company_phone', '+994 XX XXX XX XX', 'text', 'Şirkət telefon nömrəsi'),
('company_address', 'Bakı şəhəri, Nəsimi rayonu, 28 May küçəsi 15', 'text', 'Şirkət ünvanı'),
('social_facebook', 'https://facebook.com/nextcodegroup', 'text', 'Facebook səhifəsi'),
('social_instagram', 'https://instagram.com/nextcodegroup', 'text', 'Instagram səhifəsi'),
('social_linkedin', 'https://linkedin.com/company/nextcodegroup', 'text', 'LinkedIn səhifəsi'),
('social_twitter', 'https://twitter.com/nextcodegroup', 'text', 'Twitter səhifəsi'),
('contact_working_hours', 'Bazar ertəsi - Cümə: 09:00-18:00', 'text', 'İş saatları'),
('google_analytics', '', 'text', 'Google Analytics ID'),
('meta_title', 'NextCode Group - Rəqəmsal Marketinq Agentliyi', 'text', 'Meta başlıq'),
('meta_description', 'Professional digital marketing services', 'text', 'Meta açıqlaması');

-- ==============================================
-- SITE STATISTICS VERİLERİ
-- ==============================================

INSERT IGNORE INTO `site_statistics` (`stat_key`, `stat_value`, `stat_type`, `description`) VALUES
('total_projects', '150', 'counter', 'Toplam proje sayısı'),
('happy_clients', '200', 'counter', 'Məmnun müştəri sayısı'),
('years_experience', '5', 'counter', 'İş təcrübəsi (il)'),
('team_members', '10', 'counter', 'Komanda üzvü sayısı'),
('total_visitors', '0', 'counter', 'Toplam ziyaretçi sayısı'),
('page_views', '0', 'counter', 'Toplam sayfa görüntüleme'),
('bounce_rate', '0', 'percentage', 'Çıkış oranı'),
('avg_session_duration', '0', 'time', 'Ortalama oturum süresi'),
('conversion_rate', '0', 'percentage', 'Dönüşüm oranı'),
('site_status', 'active', 'status', 'Site durumu'),
('maintenance_mode', 'off', 'boolean', 'Bakım modu');

-- ==============================================
-- SITE CONTENT VERİLERİ
-- ==============================================

INSERT IGNORE INTO `site_content` (`content_key`, `content_value`, `content_type`, `page_section`, `description`) VALUES
-- Hero Section
('hero_title', 'NextCode Group ilə Rəqəmsal Dönüşümünüzü Başlayın', 'text', 'hero', 'Ana səhifə hero başlığı'),
('hero_subtitle', 'Müasir web həlləri, mobil tətbiqlər və rəqəmsal marketinq xidmətləri ilə biznesinizi böyüdün', 'text', 'hero', 'Ana səhifə hero alt başlığı'),
('hero_button_text', 'Dərhal Başlayın', 'text', 'hero', 'Hero bölümü düymə mətni'),
('hero_button_link', '/contact.php', 'link', 'hero', 'Hero bölümü düymə linki'),

-- About Section
('about_title', 'Haqqımızda', 'text', 'about', 'Haqqımızda bölümü başlığı'),
('about_subtitle', 'Rəqəmsal dünyada fərq yaradan həllər', 'text', 'about', 'Haqqımızda alt başlığı'),
('about_description', 'NextCode Group olaraq, müasir texnologiyalardan istifadə edərək müəssisənizin rəqəmsal transformasiyasını dəstəkləyirik. Ekspert komandamızla web dizayn, mobil tətbiq inkişafı və rəqəmsal marketinq sahələrində xidmət göstəririk.', 'html', 'about', 'Haqqımızda açıqlaması'),
('about_button_text', 'Daha Çox Məlumat', 'text', 'about', 'Haqqımızda düymə mətni'),
('about_button_link', '/about.php', 'link', 'about', 'Haqqımızda düymə linki'),

-- Services Section
('services_title', 'Xidmətlərimiz', 'text', 'services', 'Xidmətlər bölümü başlığı'),
('services_subtitle', 'Hərtərəfli rəqəmsal həllər', 'text', 'services', 'Xidmətlər alt başlığı'),
('services_description', 'Web dizayndan mobil tətbiqlərə, SEO-dan sosial media idarəetməsinə qədər geniş xidmət spektrimizlə yanınızdayıq.', 'text', 'services', 'Xidmətlər açıqlaması'),

-- Portfolio Section
('portfolio_title', 'Portfoliomuz', 'text', 'portfolio', 'Portfolio bölümü başlığı'),
('portfolio_subtitle', 'Uğurlu layihələrimiz', 'text', 'portfolio', 'Portfolio alt başlığı'),
('portfolio_description', 'Müxtəlif sahələrdən müştərilərimiz üçün həyata keçirdiyimiz uğurlu layihələri araşdırın.', 'text', 'portfolio', 'Portfolio açıqlaması'),
('portfolio_button_text', 'Bütün Layihələri Gör', 'text', 'portfolio', 'Portfolio düymə mətni'),
('portfolio_button_link', '/portfolio.php', 'link', 'portfolio', 'Portfolio düymə linki'),

-- Contact Section
('contact_title', 'Əlaqə', 'text', 'contact', 'Əlaqə bölümü başlığı'),
('contact_subtitle', 'Layihələriniz üçün bizimlə əlaqəyə keçin', 'text', 'contact', 'Əlaqə alt başlığı'),
('contact_description', 'Rəqəmsal layihələriniz üçün peşəkar dəstək almaq istəyirsinizsə, dərhal bizimlə əlaqəyə keçin.', 'text', 'contact', 'Əlaqə açıqlaması'),
('contact_button_text', 'Əlaqəyə Keç', 'text', 'contact', 'Əlaqə düymə mətni'),
('contact_button_link', '/contact.php', 'link', 'contact', 'Əlaqə düymə linki'),

-- Footer Section
('footer_description', 'NextCode Group - Rəqəmsal transformasiyanızın etibarlı tərəfdaşı', 'text', 'footer', 'Footer açıqlaması'),
('footer_copyright', '© 2024 NextCode Group. Bütün hüquqlar qorunur.', 'text', 'footer', 'Müəllif hüququ mətni');

-- ==============================================
-- SERVİSLER VERİLERİ
-- ==============================================

INSERT IGNORE INTO `services` (`title`, `description`, `icon`, `sort_order`) VALUES
('SEO Optimizasiya', 'Axtarış motorlarında yüksək reytinq əldə edin və daha çox müştəri cəlb edin.', 'fas fa-search', 1),
('Sosial Media', 'Sosial şəbəkələrdə brendinizi gücləndirib auditoriya yaradın.', 'fas fa-users', 2),
('Brendinq', 'Unikal brend kimliyi yaradıb rəqiblərdən fərqlənin.', 'fas fa-bolt', 3),
('Reklam Kampaniyaları', 'Effektiv reklam strategiyaları ilə satışlarınızı artırın.', 'fas fa-chart-line', 4),
('Web Dizayn', 'Müasir və responsive veb sayt dizaynları', 'fas fa-palette', 5),
('Mobil Tətbiqlər', 'iOS və Android üçün mobil tətbiqlər', 'fas fa-mobile-alt', 6);

-- ==============================================
-- PORTFOLIO KATEGORİLERİ
-- ==============================================

INSERT IGNORE INTO `portfolio_categories` (`name`, `slug`, `description`, `icon`, `color`, `sort_order`) VALUES
('Web Development', 'web-development', 'Modern web applications and websites', 'fas fa-code', '#2563eb', 1),
('Mobile Apps', 'mobile-apps', 'iOS and Android mobile applications', 'fas fa-mobile-alt', '#059669', 2),
('E-commerce', 'e-commerce', 'Online stores and shopping platforms', 'fas fa-shopping-cart', '#dc2626', 3),
('SEO & Marketing', 'seo-marketing', 'Search engine optimization and digital marketing', 'fas fa-chart-line', '#7c3aed', 4),
('UI/UX Design', 'ui-ux-design', 'User interface and user experience design', 'fas fa-paint-brush', '#ea580c', 5),
('Corporate Websites', 'corporate-websites', 'Business and corporate web solutions', 'fas fa-building', '#0891b2', 6);

-- ==============================================
-- PORTFOLIO PROJELERİ
-- ==============================================

INSERT IGNORE INTO `portfolio_projects` (
    `title`, `slug`, `short_description`, `description`, `category_id`, 
    `featured_image`, `project_date`, `technologies`, `client_name`, 
    `key_results`, `is_featured`
) VALUES
(
    'Kosmetika Mağazası E-commerce Platform',
    'kosmetika-magazasi',
    'Modern və funksional kosmetika mağazası üçün e-commerce platforması',
    'Müştəri üçün tam funksional e-commerce platforması hazırladıq. Platform müasir dizayn, asan naviqasiya və güclü admin paneli ilə təchiz edilib.',
    3,
    '/assets/images/portfolio/kosmetika-store.jpg',
    '2024-01-15',
    '["PHP", "MySQL", "JavaScript", "Bootstrap", "PayPal API"]',
    'Beauty Store LLC',
    '["45% artım satışlarda", "60% artım konversiya nisbətində", "200+ məhsul kataloqu"]',
    1
),
(
    'Mobil Oyun Tətbiqi',
    'mobil-oyun-tetbiqi',
    'iOS və Android üçün əyləncəli puzzle oyunu',
    'Yaradıcı puzzle oyunu hazırladıq. Oyun müxtəlif səviyyələr, achievement sistemi və sosial paylaşım funksiyaları ilə təchiz edilib.',
    2,
    '/assets/images/portfolio/mobile-game.jpg',
    '2024-02-20',
    '["React Native", "Firebase", "Redux", "Admob"]',
    'GameDev Studio',
    '["50,000+ yükləmə", "4.8 reytinq App Store-da", "25% retention rate"]',
    1
),
(
    'Hüquq Firması Korporativ Saytı',
    'huquq-firmasi-sayti',
    'Peşəkar hüquq firması üçün korporativ veb sayt',
    'Hüquq firması üçün peşəkar və etibarlı görünüşlü veb sayt hazırladıq. Sayt müştəri portali, blog və əlaqə formları ilə təchiz edilib.',
    6,
    '/assets/images/portfolio/law-firm.jpg',
    '2024-03-10',
    '["WordPress", "PHP", "MySQL", "Bootstrap", "Contact Form 7"]',
    'Legal Partners',
    '["300% artım müştəri sorğularında", "Peşəkar imicin güclənməsi", "SEO optimizasiyası"]',
    0
);

-- ==============================================
-- BLOG KATEGORİLERİ
-- ==============================================

INSERT IGNORE INTO `blog_categories` (`name`, `slug`, `description`) VALUES
('SEO', 'seo', 'Axtarış motoru optimizasiyası məqalələri'),
('Veb İnkişaf', 'veb-inkisaf', 'Veb inkişaf trendləri və texnologiyaları'),
('E-ticarət', 'e-ticaret', 'E-ticarət platformaları və onlayn mağaza inkişafı'),
('Kibertəhlükəsizlik', 'kibertehlukesizlik', 'Veb təhlükəsizliyi və kibertəhlükəsizlik təcrübələri'),
('Rəqəmsal Marketinq', 'reqemsal-marketinq', 'Rəqəmsal marketinq strategiyaları və trendləri'),
('Sosial Media', 'sosial-media', 'Sosial media marketinqi və strategiyaları');

-- ==============================================
-- BLOG YAZILARI
-- ==============================================

INSERT IGNORE INTO `blog_posts` (
    `title`, `slug`, `content`, `excerpt`, `category_id`, `status`, `featured`, 
    `meta_title`, `meta_description`, `tags`, `published_at`
) VALUES
(
    '2024-cü İldə Rəqəmsal Marketinq Trendləri',
    '2024-reqemsal-marketinq-trendleri',
    '<h2>2024-cü İldə Rəqəmsal Marketinq Sahəsində Gözlənilən Əsas Dəyişikliklər</h2>
    <p>Rəqəmsal marketinq sahəsi sürətlə inkişaf edir və hər il yeni trendlər meydana çıxır. 2024-cü ildə də bu sahədə əhəmiyyətli dəyişikliklər gözləyirik...</p>
    <h3>1. AI və Maşın Öyrənməsi</h3>
    <p>Süni intellekt və maşın öyrənməsi texnologiyaları marketinq strategiyalarında daha çox istifadə olunacaq...</p>',
    '2024-cü ildə rəqəmsal marketinq sahəsində AI, video marketinq və şəxsi marketinq kimi trendlər ön plana çıxacaq.',
    1,
    'published',
    1,
    '2024 Rəqəmsal Marketinq Trendləri | NextCode Group',
    '2024-cü ildə rəqəmsal marketinq sahəsində gözlənilən əsas trendləri və dəyişiklikləri araşdırın.',
    'marketinq, trendlər, AI, 2024',
    '2024-01-15 10:00:00'
),
(
    'SEO Optimizasiyasının Əsasları',
    'seo-optimizasiyasinin-esaslari',
    '<h2>SEO Optimizasiyasının Əsas Prinsipləri</h2>
    <p>SEO (Search Engine Optimization) hər hansı veb saytın uğurlu olması üçün vacibdir...</p>
    <h3>Texniki SEO</h3>
    <p>Veb saytın texniki aspektlərinin optimizasiyası...</p>
    <h3>Məzmun SEO</h3>
    <p>Keyfiyyətli və orijinal məzmun yaradılması...</p>',
    'SEO optimizasiyasının əsas prinsiplərini və praktik məsləhətlərini öyrənin.',
    2,
    'published',
    1,
    'SEO Optimizasiyasının Əsasları | NextCode Group',
    'SEO optimizasiyasının əsas prinsipləri və praktik məsləhətləri.',
    'SEO, optimizasiya, axtarış motorları',
    '2024-01-20 14:30:00'
);

-- ==============================================
-- FİYATLANDIRMA PAKETLERİ
-- ==============================================

INSERT IGNORE INTO `pricing_packages` (`name`, `slug`, `description`, `price`, `billing_period`, `is_popular`, `sort_order`, `button_text`, `button_link`) VALUES
('Başlanğıc', 'basic', 'Kiçik bizneslər üçün ideal paket', 299.00, 'monthly', 0, 1, 'Başla', '/contact'),
('Peşəkar', 'professional', 'Böyüyən bizneslər üçün ən populyar seçim', 599.00, 'monthly', 1, 2, 'Seç', '/contact'),
('Premium', 'premium', 'Böyük şirkətlər üçün tam həll', 999.00, 'monthly', 0, 3, 'Əlaqə', '/contact'),
('Korporativ', 'enterprise', 'Fərdi həllər və tam dəstək', 1999.00, 'monthly', 0, 4, 'Danışaq', '/contact');

-- ==============================================
-- PAKET ÖZELLİKLERİ
-- ==============================================

-- Basic Package Features
INSERT IGNORE INTO `package_features` (`package_id`, `feature_name`, `feature_description`, `feature_value`, `is_included`, `sort_order`) VALUES
(1, 'SEO Analizi', 'Aylıq SEO performans hesabatı', '1 dəfə', 1, 1),
(1, 'Sosial Media', 'Facebook və Instagram idarəetməsi', '2 platform', 1, 2),
(1, 'Məzmun Yaradılması', 'Aylıq blog məqalələri', '4 məqalə', 1, 3),
(1, 'Google Ads', 'Aylıq reklam büdcəsi', '500 AZN', 1, 4),
(1, 'Dəstək', 'Email dəstəyi', 'İş saatları', 1, 5),
(1, 'Hesabat', 'Aylıq performans hesabatı', 'Əsas metrikalar', 1, 6);

-- Professional Package Features
INSERT IGNORE INTO `package_features` (`package_id`, `feature_name`, `feature_description`, `feature_value`, `is_included`, `sort_order`) VALUES
(2, 'SEO Analizi', 'Həftəlik SEO performans hesabatı', 'Həftəlik', 1, 1),
(2, 'Sosial Media', 'Bütün əsas platformların idarəetməsi', '5 platform', 1, 2),
(2, 'Məzmun Yaradılması', 'Həftəlik blog və sosial media məzmunu', '8 məqalə', 1, 3),
(2, 'Google Ads', 'Aylıq reklam büdcəsi', '1000 AZN', 1, 4),
(2, 'Email Marketinq', 'Aylıq email kampaniyaları', '4 kampaniya', 1, 5),
(2, 'Dəstək', 'Telefon və email dəstəyi', '24/7', 1, 6),
(2, 'Hesabat', 'Həftəlik detallı hesabat', 'Tam analitika', 1, 7),
(2, 'Veb Sayt', 'Landing page yaradılması', '2 səhifə', 1, 8);

-- ==============================================
-- NAVİGASYON MENÜLERİ
-- ==============================================

INSERT IGNORE INTO `navigation_items` (`title`, `url`, `sort_order`) VALUES
('Ana Səhifə', '/', 1),
('Haqqımızda', '/about.php', 2),
('Xidmətlər', '/services.php', 3),
('Portfolio', '/portfolio.php', 4),
('Blog', '/blog.php', 5),
('Qiymətlər', '/pricing.php', 6),
('Əlaqə', '/contact.php', 7);

-- ==============================================
-- SAYFA İÇERİKLERİ
-- ==============================================

INSERT IGNORE INTO `pages` (`title`, `slug`, `content`, `meta_description`, `status`) VALUES
(
    'NextCode Group - Rəqəmsal Marketinq Agentliyi', 
    'home', 
    '<h1>NextCode Group</h1><p>Azərbaycanda rəqəmsal marketinq sahəsində peşəkar xidmətlər təqdim edirik.</p>', 
    'NextCode Group - Azərbaycanda rəqəmsal marketinq sahəsində peşəkar xidmətlər. SEO, sosial media, brendinq və reklam kampaniyaları ilə biznesinizi inkişaf etdirin.', 
    'published'
),
(
    'Haqqımızda - NextCode Group',
    'about',
    '<h1>Haqqımızda</h1><p>NextCode Group olaraq, rəqəmsal transformasiya sahəsində peşəkar xidmətlər göstəririk.</p>',
    'NextCode Group haqqında ətraflı məlumat. Komandamız, missiyamız və dəyərlərimiz.',
    'published'
);

COMMIT;
