<?php
// Mevcut Site İçeriklerini Admin Paneline Aktarma - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

$pdo = getSecureDatabaseConnection();

if (!$pdo) {
    die("Veritabanı bağlantı hatası!");
}

try {
    $pdo->beginTransaction();
    
    // Ana sayfa içerikleri (https://nextcode.az/)
    $home_contents = [
        // Hero Section
        ['home', 'hero', 'main_title', 'Digital Dünyada Uğurunuz üçün Professional Həllər', 'text'],
        ['home', 'hero', 'subtitle', 'Rəqəmsal Transformasiya', 'text'],
        ['home', 'hero', 'description', 'Veb inkişaf, SEO, e-ticarət və rəqəmsal marketinq xidmətləri ilə biznesinizi növbəti səviyyəyə aparırıq.', 'text'],
        ['home', 'hero', 'cta_text', 'İndi Başla', 'text'],
        ['home', 'hero', 'video_text', 'Videomuzu İzləyin', 'text'],
        
        // Stats Section
        ['home', 'stats', 'projects_count', '500+', 'text'],
        ['home', 'stats', 'projects_text', 'Tamamlanmış Layihə', 'text'],
        ['home', 'stats', 'satisfaction_count', '98%', 'text'],
        ['home', 'stats', 'satisfaction_text', 'Müştəri Məmnuniyyəti', 'text'],
        ['home', 'stats', 'experience_count', '16+', 'text'],
        ['home', 'stats', 'experience_text', 'İllik Təcrübə', 'text'],
        
        // Digital Success Section
        ['home', 'digital_success', 'title', 'Rəqəmsal Uğur', 'text'],
        ['home', 'digital_success', 'description', 'AI və data analitikası ilə biznesinizi gücləndirin', 'text'],
        
        // Services Section
        ['home', 'services', 'section_title', 'Xidmətlərimiz', 'text'],
        ['home', 'services', 'section_subtitle', 'Biznesinizin rəqəmsal uğuru üçün lazım olan bütün xidmətləri təqdim edirik.', 'text'],
        
        // SEO Service
        ['home', 'services', 'seo_title', 'SEO Optimallaşdırma', 'text'],
        ['home', 'services', 'seo_description', 'Google-da birinci səhifədə görünmək üçün peşəkar SEO xidmətləri.', 'text'],
        ['home', 'services', 'seo_features', 'Açar söz tədqiqi, Texniki SEO, Məzmun optimallaşdırması', 'text'],
        ['home', 'services', 'seo_result', '300% artış', 'text'],
        
        // Social Media Service
        ['home', 'services', 'social_title', 'Sosial Media Marketinq', 'text'],
        ['home', 'services', 'social_description', 'Instagram, Facebook, LinkedIn və digər platformalarda effektiv marketinq.', 'text'],
        ['home', 'services', 'social_features', 'Məzmun yaradılması, Cədvəl planlaması, Analitika hesabatları', 'text'],
        ['home', 'services', 'social_result', '500% engagement', 'text'],
        
        // Branding Service
        ['home', 'services', 'branding_title', 'Branding & Dizayn', 'text'],
        ['home', 'services', 'branding_description', 'Güclü marka kimliyi və vizual dizayn həlləri.', 'text'],
        ['home', 'services', 'branding_features', 'Loqo dizaynı, Brend kimliyı, Marketinq materialları', 'text'],
        ['home', 'services', 'branding_result', '200% tanınırlıq', 'text'],
        
        // Digital Ads Service
        ['home', 'services', 'ads_title', 'Rəqəmsal Reklam', 'text'],
        ['home', 'services', 'ads_description', 'Google Ads, Facebook Ads və digər reklam platformalarında effektiv kampaniyalar.', 'text'],
        ['home', 'services', 'ads_features', 'Google Ads, Facebook Ads, ROI optimallaşdırması', 'text'],
        ['home', 'services', 'ads_result', '400% ROI', 'text'],
        
        // Testimonials Section
        ['home', 'testimonials', 'section_title', 'Müştəri Rəyləri', 'text'],
        ['home', 'testimonials', 'section_subtitle', 'Bizə etibar edən müştərilərimizin uğur hekayələri və təcrübələri. Hər bir layihədə keyfiyyət və peşəkarlığımızla fərqlənirik.', 'text'],
        
        // Testimonial 1
        ['home', 'testimonials', 'testimonial1_name', 'Əli Məmmədov', 'text'],
        ['home', 'testimonials', 'testimonial1_position', 'Marketing Meneceri', 'text'],
        ['home', 'testimonials', 'testimonial1_company', 'ABC Şirkəti', 'text'],
        ['home', 'testimonials', 'testimonial1_text', 'NextCode Group ilə işləmək böyük zövq idi. SEO xidmətləri sayəsində saytımızın trafikini 300% artırdıq.', 'text'],
        
        // Testimonial 2
        ['home', 'testimonials', 'testimonial2_name', 'Leyla Həsənova', 'text'],
        ['home', 'testimonials', 'testimonial2_position', 'CEO', 'text'],
        ['home', 'testimonials', 'testimonial2_company', 'XYZ Company', 'text'],
        ['home', 'testimonials', 'testimonial2_text', 'Peşəkar komanda və keyfiyyətli xidmət. Sosial media hesablarımızı mükəmməl idarə edirlər.', 'text'],
        
        // Testimonial 3
        ['home', 'testimonials', 'testimonial3_name', 'Rəşad Quliyev', 'text'],
        ['home', 'testimonials', 'testimonial3_position', 'Sahibkar', 'text'],
        ['home', 'testimonials', 'testimonial3_company', 'Startup Venture', 'text'],
        ['home', 'testimonials', 'testimonial3_text', 'Brendinq xidmətləri üçün təşəkkür edirik. Yeni loqomuz və brend kimliyi çox uğurlu oldu.', 'text'],
        
        // Experience Section
        ['home', 'experience', 'title', '16 İllik Təcrübə və Güvən', 'text'],
        ['home', 'experience', 'description', 'NextCode Group 2008-ci ildən bu yana rəqəmsal texnologiyalar sahəsində fasiləsiz xidmət göstərir və müştərilərinə etibarlı həllər təqdim edir.', 'text'],
        ['home', 'experience', 'year', '2008-dən bəri', 'text'],
        
        // Certifications
        ['home', 'certifications', 'google_partner', 'Google Partner Sertifikatı', 'text'],
        ['home', 'certifications', 'facebook_partner', 'Facebook Marketing Partner', 'text'],
        ['home', 'certifications', 'satisfaction', '98% Müştəri Məmnuniyyəti', 'text'],
        ['home', 'certifications', 'experience_years', '16+ İl Təcrübə', 'text'],
        ['home', 'certifications', 'projects', '500+ Uğurlu Layihə', 'text'],
        ['home', 'certifications', 'clients', '250+ Məmnun Müştəri', 'text'],
        ['home', 'certifications', 'budget', '15M+ Reklam Büdcəsi', 'text'],
        
        // Success Stories Section
        ['home', 'success_stories', 'section_title', 'Uğur Hekayələrimiz', 'text'],
        ['home', 'success_stories', 'section_subtitle', 'Müştərilərimizin biznesinə əlavə etdiyimiz dəyər və əldə etdikləri nəticələr', 'text'],
        
        // Success Story 1
        ['home', 'success_stories', 'story1_title', 'E-ticarət Platformu', 'text'],
        ['home', 'success_stories', 'story1_description', 'Yerli geyim brendinin onlayn satış platformunu yaratdıq və 6 ay ərzində satışları 400% artırdıq.', 'text'],
        ['home', 'success_stories', 'story1_result', '400% Satış Artışı', 'text'],
        ['home', 'success_stories', 'story1_roi', '2.5x ROI', 'text'],
        
        // Success Story 2
        ['home', 'success_stories', 'story2_title', 'SEO Kampaniyası', 'text'],
        ['home', 'success_stories', 'story2_description', 'Turizm şirkətinin Google-da görünürlüyünü artıraraq organik trafikini 8 ay ərzində 350% artırdıq.', 'text'],
        ['home', 'success_stories', 'story2_result', '350% Trafik Artışı', 'text'],
        ['home', 'success_stories', 'story2_ranking', '#1 Google Reytinqi', 'text'],
        
        // Success Story 3
        ['home', 'success_stories', 'story3_title', 'Sosial Media Kampaniyası', 'text'],
        ['home', 'success_stories', 'story3_description', 'Restoran zəncirinin sosial media hesablarını idarə edərək follower sayını 10x artırdıq.', 'text'],
        ['home', 'success_stories', 'story3_result', '10x Follower Artışı', 'text'],
        ['home', 'success_stories', 'story3_engagement', '85% Engagement', 'text'],
        
        // CTA Section
        ['home', 'cta', 'title', 'Biznesinizi Növbəti Səviyyəyə Çatdırmağa Hazırsınız?', 'text'],
        ['home', 'cta', 'description', 'Pulsuz məsləhət seansı üçün bizimlə əlaqə saxlayın və rəqəmsal strategiyanızı müzakirə edək.', 'text'],
        ['home', 'cta', 'button_text', 'İndi Başlayın', 'text'],
        ['home', 'cta', 'pricing_text', 'View Pricing', 'text'],
        
        // Footer
        ['home', 'footer', 'tagline', 'Professional solutions for your success in the digital world.', 'text'],
        ['home', 'footer', 'email', 'info@nextcode.com', 'text'],
        ['home', 'footer', 'phone', '+380 97 258 00 00', 'text'],
        ['home', 'footer', 'address', 'Bakı şəhəri, Nəsimi rayonu<br>28 May küçəsi 15', 'html'],
        ['home', 'footer', 'copyright', '© 2024 NextCode Group. All rights reserved.', 'text'],
        ['home', 'footer', 'newsletter_title', 'Newsletter', 'text'],
        ['home', 'footer', 'newsletter_subtitle', 'Subscribe to stay updated with the latest news and special offers in digital marketing.', 'text'],
        ['home', 'footer', 'newsletter_placeholder', 'Your email address', 'text'],
        ['home', 'footer', 'privacy_text', 'I accept the privacy policy', 'text'],
    ];
    
    // Hakkımızda sayfası içerikleri (https://nextcode.az/about.php)
    $about_contents = [
        // Hero Section
        ['about', 'hero', 'title', 'Haqqımızda', 'text'],
        ['about', 'hero', 'subtitle', 'Azərbaycanın aparıcı rəqəmsal marketinq agentliyi olaraq biznesinizin uğuruna kömək edirik.', 'text'],
        
        // Who We Are Section
        ['about', 'who_we_are', 'title', 'Biz Kimik?', 'text'],
        ['about', 'who_we_are', 'description', 'NextCode Group, 2008-ci ildən bəri Azərbaycanda rəqəmsal marketinq sahəsində fəaliyyət göstərən peşəkar komandadır.', 'text'],
        
        // Stats Section
        ['about', 'stats', 'clients_count', '500+', 'text'],
        ['about', 'stats', 'clients_text', 'Müştəri', 'text'],
        ['about', 'stats', 'projects_count', '1000+', 'text'],
        ['about', 'stats', 'projects_text', 'Layihə', 'text'],
        ['about', 'stats', 'experience_count', '15+', 'text'],
        ['about', 'stats', 'experience_text', 'İllik Təcrübə', 'text'],
        ['about', 'stats', 'satisfaction_count', '98%', 'text'],
        ['about', 'stats', 'satisfaction_text', 'Məmnuniyyət', 'text'],
        
        // Experience Section
        ['about', 'experience', 'title', '16 İllik Təcrübə və Güvən', 'text'],
        ['about', 'experience', 'description', 'NextCode Group 2008-ci ildə qurulduğu gündən bu yana rəqəmsal texnologiyalar sahəsində fasiləsiz xidmət göstərir. 16 il ərzində yüzlərlə müştəri ilə işləyərək, onların rəqəmsal transformasiya yolculuğunda etibarlı tərəfdaş olmuşuq.', 'text'],
        ['about', 'experience', 'year', '2008-dən bəri', 'text'],
        
        // Experience Points
        ['about', 'experience', 'point1', '2008-ci ildən bəri fasiləsiz xidmət', 'text'],
        ['about', 'experience', 'point2', 'Sektorda qazanılmış güvən və nüfuz', 'text'],
        ['about', 'experience', 'point3', 'Texnoloji yeniliklərlə həmişə bir addım önündə', 'text'],
        ['about', 'experience', 'point4', 'Müştəri məmnuniyyətində sabit yüksək göstəricilər', 'text'],
        
        // Timeline
        ['about', 'timeline', '2008_title', 'Şirkətin Qurulması', 'text'],
        ['about', 'timeline', '2008_description', 'NextCode Group-un təməli qoyuldu', 'text'],
        ['about', 'timeline', '2012_title', 'İlk 100 Müştəri', 'text'],
        ['about', 'timeline', '2012_description', 'Müştəri bazamız 100-ü keçdi', 'text'],
        ['about', 'timeline', '2018_title', 'Beynəlxalq Genişlənmə', 'text'],
        ['about', 'timeline', '2018_description', 'Regional bazara çıxış', 'text'],
        ['about', 'timeline', '2024_title', '16 İllik Təcrübə', 'text'],
        ['about', 'timeline', '2024_description', 'Sektorda lider mövqe', 'text'],
        
        // Team Section
        ['about', 'team', 'title', 'Bizim Komanda', 'text'],
        ['about', 'team', 'subtitle', 'Təcrübəli və yaradıcı mütəxəssislərimizlə tanış olun', 'text'],
        
        // Team Member 1
        ['about', 'team', 'member1_name', 'Əli Məmmədov', 'text'],
        ['about', 'team', 'member1_position', 'CEO & Founder', 'text'],
        ['about', 'team', 'member1_description', '10 illik təcrübəyə malik rəqəmsal marketinq mütəxəssisi', 'text'],
        
        // Team Member 2
        ['about', 'team', 'member2_name', 'Leyla Həsənova', 'text'],
        ['about', 'team', 'member2_position', 'Marketing Director', 'text'],
        ['about', 'team', 'member2_description', 'Yaradıcı kampaniyalar və strategiyalar üzrə mütəxəssis', 'text'],
        
        // Team Member 3
        ['about', 'team', 'member3_name', 'Rəşad Quliyev', 'text'],
        ['about', 'team', 'member3_position', 'Lead Developer', 'text'],
        ['about', 'team', 'member3_description', 'Full-stack developer və texniki həllərin memarı', 'text'],
        
        // Team Member 4
        ['about', 'team', 'member4_name', 'Səbinə Əliyeva', 'text'],
        ['about', 'team', 'member4_position', 'Creative Director', 'text'],
        ['about', 'team', 'member4_description', 'UX/UI dizayn və brendinq üzrə mütəxəssis', 'text'],
        
        // Mission & Vision
        ['about', 'mission', 'title', 'Missiyamız', 'text'],
        ['about', 'mission', 'description', 'Müştərilərimizin rəqəmsal dünyada güclü mövqe qazanması və biznes məqsədlərinə çatması üçün innovativ və effektiv həllər təqdim etmək.', 'text'],
        
        ['about', 'vision', 'title', 'Vizyonumuz', 'text'],
        ['about', 'vision', 'description', 'Azərbaycanda rəqəmsal marketinq sahəsində lider agentlik olmaq və beynəlxalq bazarda tanınan bir brend yaratmaq.', 'text'],
        
        // Values
        ['about', 'values', 'title', 'Bizim Dəyərlərimiz', 'text'],
        ['about', 'values', 'innovation_title', 'İnnovasiya', 'text'],
        ['about', 'values', 'innovation_description', 'Həmişə yeni texnologiyalar və yaradıcı yanaşmalar axtarırıq', 'text'],
        ['about', 'values', 'trust_title', 'Güvən', 'text'],
        ['about', 'values', 'trust_description', 'Müştərilərimizlə uzunmüddətli və etibarlı əlaqələr qururuq', 'text'],
        ['about', 'values', 'quality_title', 'Keyfiyyət', 'text'],
        ['about', 'values', 'quality_description', 'Hər bir layihədə ən yüksək keyfiyyət standartlarını tətbiq edirik', 'text'],
        ['about', 'values', 'teamwork_title', 'Komanda İşi', 'text'],
        ['about', 'values', 'teamwork_description', 'Güclü komanda ruhu ilə ən yaxşı nəticələrə çatırıq', 'text'],
        
        // CTA Section
        ['about', 'cta', 'title', 'Bizimlə İşləməyə Hazırsınız?', 'text'],
        ['about', 'cta', 'description', 'Biznesinizi rəqəmsal dünyada irəli aparmaq üçün bizimlə əlaqə saxlayın', 'text'],
        ['about', 'cta', 'contact_text', 'Əlaqə Saxlayın', 'text'],
        ['about', 'cta', 'portfolio_text', 'Portfolio', 'text'],
    ];
    
    // Hizmetler sayfası içerikleri (https://nextcode.az/services.php)
    $services_contents = [
        // Hero Section
        ['services', 'hero', 'title', 'Xidmətlərimiz', 'text'],
        ['services', 'hero', 'subtitle', 'Biznesinizin rəqəmsal uğuru üçün lazım olan bütün xidmətləri təqdim edirik.', 'text'],
        
        // Overview Section
        ['services', 'overview', 'title', 'Xidmətlərimizə Ümumi Baxış', 'text'],
        ['services', 'overview', 'description', 'Biznesinizin rəqəmsal uğuru üçün lazım olan bütün xidmətləri təqdim edirik.', 'text'],
        
        // SEO Service
        ['services', 'seo', 'title', 'SEO Optimallaşdırma', 'text'],
        ['services', 'seo', 'price', '500 AZN-dən başlayaraq', 'text'],
        ['services', 'seo', 'description', 'Google-da birinci səhifədə görünmək üçün peşəkar SEO xidmətləri.', 'text'],
        ['services', 'seo', 'features', 'Açar söz tədqiqatı, Texniki SEO audit, Məzmun optimizasiyası, Link building, Aylıq hesabat', 'text'],
        ['services', 'seo', 'duration', '3-6 ay', 'text'],
        
        // Social Media Service
        ['services', 'social_media', 'title', 'Sosial Media Marketinq', 'text'],
        ['services', 'social_media', 'price', '300 AZN-dən başlayaraq', 'text'],
        ['services', 'social_media', 'description', 'Instagram, Facebook, LinkedIn və digər platformalarda effektiv marketinq.', 'text'],
        ['services', 'social_media', 'features', 'Məzmun planlaması, Post dizaynı, Cəmiyyət idarəçiliyi, Reklam kampaniyaları, Analitika və hesabat', 'text'],
        ['services', 'social_media', 'duration', '1-3 ay', 'text'],
        
        // Branding Service
        ['services', 'branding', 'title', 'Branding & Dizayn', 'text'],
        ['services', 'branding', 'price', '800 AZN-dən başlayaraq', 'text'],
        ['services', 'branding', 'description', 'Güclü brend kimliyi yaratmaq və vizual dizayn həlləri', 'text'],
        ['services', 'branding', 'features', 'Logo dizaynı, Brend kimliyı, Marketinq materialları', 'text'],
        ['services', 'branding', 'duration', '2-4 ay', 'text'],
        
        // Digital Ads Service
        ['services', 'digital_ads', 'title', 'Rəqəmsal Reklam', 'text'],
        ['services', 'digital_ads', 'price', '600 AZN-dən başlayaraq', 'text'],
        ['services', 'digital_ads', 'description', 'Google Ads, Facebook Ads və digər reklam platformalarında effektiv kampaniyalar', 'text'],
        ['services', 'digital_ads', 'features', 'Google Ads, Facebook Ads, ROI optimallaşdırması', 'text'],
        ['services', 'digital_ads', 'duration', '1-6 ay', 'text'],
        
        // Web Development Service
        ['services', 'web_development', 'title', 'Veb İnkişaf', 'text'],
        ['services', 'web_development', 'price', '1000 AZN-dən başlayaraq', 'text'],
        ['services', 'web_development', 'description', 'Modern, responsive və sürətli web saytları yaradırıq', 'text'],
        ['services', 'web_development', 'features', 'Responsive dizayn, CMS inteqrasiyası, SEO optimizasiyası, Hosting və dəstək', 'text'],
        ['services', 'web_development', 'duration', '4-8 həftə', 'text'],
        
        // E-commerce Service
        ['services', 'ecommerce', 'title', 'E-ticarət Həlləri', 'text'],
        ['services', 'ecommerce', 'price', '1500 AZN-dən başlayaraq', 'text'],
        ['services', 'ecommerce', 'description', 'Onlayn satış platformaları və e-ticarət həlləri', 'text'],
        ['services', 'ecommerce', 'features', 'Onlayn mağaza, Ödəniş sistemi, İnventar idarəçiliyi, Sifariş idarəçiliyi', 'text'],
        ['services', 'ecommerce', 'duration', '6-12 həftə', 'text'],
    ];
    
    // İletişim sayfası içerikleri (https://nextcode.az/contact.php)
    $contact_contents = [
        // Hero Section
        ['contact', 'hero', 'title', 'Əlaqə', 'text'],
        ['contact', 'hero', 'subtitle', 'Bizimlə əlaqə saxlayın', 'text'],
        
        // Contact Info
        ['contact', 'info', 'email', 'info@nextcode.com', 'text'],
        ['contact', 'info', 'phone', '+380 97 258 00 00', 'text'],
        ['contact', 'info', 'address', 'Bakı şəhəri, Nəsimi rayonu<br>28 May küçəsi 15', 'html'],
        
        // Form Section
        ['contact', 'form', 'title', 'Bizimlə Əlaqə Saxlayın', 'text'],
        ['contact', 'form', 'name_label', 'Ad Soyad', 'text'],
        ['contact', 'form', 'email_label', 'E-mail', 'text'],
        ['contact', 'form', 'phone_label', 'Telefon', 'text'],
        ['contact', 'form', 'service_label', 'Maraqlandığınız Xidmət', 'text'],
        ['contact', 'form', 'message_label', 'Mesaj', 'text'],
        ['contact', 'form', 'submit_text', 'Mesaj Göndər', 'text'],
        
        // Service Options
        ['contact', 'services', 'seo', 'SEO Optimizasiya', 'text'],
        ['contact', 'services', 'social_media', 'Sosial Media', 'text'],
        ['contact', 'services', 'branding', 'Brendinq', 'text'],
        ['contact', 'services', 'advertising', 'Reklam Kampaniyaları', 'text'],
        ['contact', 'services', 'web_design', 'Veb Dizayn', 'text'],
        ['contact', 'services', 'other', 'Digər', 'text'],
    ];
    
    // Tüm içerikleri birleştir
    $all_contents = array_merge($home_contents, $about_contents, $services_contents, $contact_contents);
    
    // İçerikleri veritabanına ekle
    $stmt = $pdo->prepare("INSERT INTO site_content (page_name, section_name, content_key, content_value, content_type) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()");
    
    foreach ($all_contents as $content) {
        $stmt->execute($content);
    }
    
    $pdo->commit();
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px; border: 1px solid #c3e6cb;'>";
    echo "<h3 style='color: #155724; margin-bottom: 10px;'>✅ Başarılı!</h3>";
    echo "<p style='color: #155724;'>Mevcut site içerikleri başarıyla admin paneline aktarıldı!</p>";
    echo "<p style='color: #155724;'>Toplam " . count($all_contents) . " içerik eklendi/güncellendi.</p>";
    echo "<p style='color: #155724; margin-top: 10px;'><strong>İçerikler:</strong></p>";
    echo "<ul style='color: #155724; margin-left: 20px;'>";
    echo "<li>Ana Sayfa: " . count($home_contents) . " içerik</li>";
    echo "<li>Hakkımızda: " . count($about_contents) . " içerik</li>";
    echo "<li>Hizmetler: " . count($services_contents) . " içerik</li>";
    echo "<li>İletişim: " . count($contact_contents) . " içerik</li>";
    echo "</ul>";
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='content.php' style='background: #155724; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>İçerik Yönetimine Git</a>";
    echo "<a href='index.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Admin Dashboard</a>";
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    $pdo->rollback();
    echo "<div style='background: #fee; padding: 20px; border-radius: 8px; margin: 20px; border: 1px solid #fcc;'>";
    echo "<h3 style='color: #c33; margin-bottom: 10px;'>❌ Hata!</h3>";
    echo "<p style='color: #c33;'>Hata: " . $e->getMessage() . "</p>";
    echo "<p style='color: #c33;'>Lütfen tekrar deneyin veya sistem yöneticisine başvurun.</p>";
    echo "</div>";
}
?>



