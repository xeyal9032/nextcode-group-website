<?php
// Site İçerik Kurulum Scripti - NextCode Group
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
    
    // Ana sayfa içerikleri
    $home_contents = [
        // Hero Section
        ['home', 'hero', 'main_title', 'NextCode Group', 'text'],
        ['home', 'hero', 'subtitle', 'Dijital Pazarlama ve Teknoloji Çözümleri', 'text'],
        ['home', 'hero', 'description', 'Modern web tasarım, SEO optimizasyonu ve dijital pazarlama hizmetleri ile işinizi bir üst seviyeye taşıyoruz.', 'text'],
        ['home', 'hero', 'cta_text', 'Projenizi Başlatın', 'text'],
        ['home', 'hero', 'cta_link', '#contact', 'text'],
        
        // Services Section
        ['home', 'services', 'section_title', 'Hizmetlerimiz', 'text'],
        ['home', 'services', 'section_subtitle', 'Dijital dünyada başarılı olmak için ihtiyacınız olan tüm hizmetleri sunuyoruz', 'text'],
        
        // About Section
        ['home', 'about', 'section_title', 'Hakkımızda', 'text'],
        ['home', 'about', 'description', 'NextCode Group olarak, 2020 yılından bu yana dijital dünyada müşterilerimize en kaliteli hizmetleri sunmaya devam ediyoruz.', 'html'],
        
        // Testimonials Section
        ['home', 'testimonials', 'section_title', 'Müşteri Yorumları', 'text'],
        ['home', 'testimonials', 'section_subtitle', 'Başarılı projelerimizden memnun kalan müşterilerimizin görüşleri', 'text'],
        
        // CTA Section
        ['home', 'cta', 'title', 'Projenizi Hayata Geçirelim', 'text'],
        ['home', 'cta', 'description', 'Uzman ekibimizle birlikte dijital dünyada fark yaratın. Hemen iletişime geçin!', 'text'],
        ['home', 'cta', 'button_text', 'İletişime Geçin', 'text'],
        ['home', 'cta', 'button_link', '/contact', 'text'],
    ];
    
    // Hakkımızda sayfası içerikleri
    $about_contents = [
        ['about', 'hero', 'title', 'Hakkımızda', 'text'],
        ['about', 'hero', 'subtitle', 'NextCode Group\'un hikayesi', 'text'],
        
        ['about', 'story', 'title', 'Hikayemiz', 'text'],
        ['about', 'story', 'content', 'NextCode Group, 2020 yılında teknoloji ve dijital pazarlama alanında uzman bir ekip tarafından kurulmuştur. Müşterilerimizin dijital dünyada başarılı olmaları için en kaliteli hizmetleri sunmayı hedefliyoruz.', 'html'],
        
        ['about', 'mission', 'title', 'Misyonumuz', 'text'],
        ['about', 'mission', 'content', 'İşletmelerin dijital dönüşümlerinde rehberlik etmek ve onları dijital dünyada öne çıkarmak için çalışıyoruz.', 'html'],
        
        ['about', 'vision', 'title', 'Vizyonumuz', 'text'],
        ['about', 'vision', 'content', 'Türkiye\'nin önde gelen dijital pazarlama ve teknoloji çözümleri şirketi olmak.', 'html'],
    ];
    
    // Hizmetler sayfası içerikleri
    $services_contents = [
        ['services', 'hero', 'title', 'Hizmetlerimiz', 'text'],
        ['services', 'hero', 'subtitle', 'Dijital dünyada ihtiyacınız olan tüm hizmetler', 'text'],
        
        ['services', 'web_design', 'title', 'Web Tasarım', 'text'],
        ['services', 'web_design', 'description', 'Modern, responsive ve kullanıcı dostu web siteleri tasarlıyoruz.', 'html'],
        
        ['services', 'seo', 'title', 'SEO Optimizasyonu', 'text'],
        ['services', 'seo', 'description', 'Arama motorlarında üst sıralarda yer almanızı sağlıyoruz.', 'html'],
        
        ['services', 'digital_marketing', 'title', 'Dijital Pazarlama', 'text'],
        ['services', 'digital_marketing', 'description', 'Sosyal medya ve online reklamcılık ile markanızı güçlendiriyoruz.', 'html'],
    ];
    
    // İletişim sayfası içerikleri
    $contact_contents = [
        ['contact', 'hero', 'title', 'İletişim', 'text'],
        ['contact', 'hero', 'subtitle', 'Bizimle iletişime geçin', 'text'],
        
        ['contact', 'info', 'phone', '+90 (212) 123 45 67', 'text'],
        ['contact', 'info', 'email', 'info@nextcode.az', 'text'],
        ['contact', 'info', 'address', 'İstanbul, Türkiye', 'text'],
        
        ['contact', 'form', 'title', 'Mesaj Gönderin', 'text'],
        ['contact', 'form', 'subtitle', 'Projeleriniz hakkında bizimle konuşun', 'text'],
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
    echo "<p style='color: #155724;'>Site içerikleri başarıyla kuruldu!</p>";
    echo "<p style='color: #155724;'>Toplam " . count($all_contents) . " içerik eklendi/güncellendi.</p>";
    echo "<a href='content.php' style='color: #155724; text-decoration: underline;'>İçerik Yönetimine Git</a>";
    echo "</div>";
    
} catch (Exception $e) {
    $pdo->rollback();
    echo "<div style='background: #fee; padding: 20px; border-radius: 8px; margin: 20px; border: 1px solid #fcc;'>";
    echo "<h3 style='color: #c33; margin-bottom: 10px;'>❌ Hata!</h3>";
    echo "<p style='color: #c33;'>Hata: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>



