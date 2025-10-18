<?php
// Veritabanı Güncelleme Scripti - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

$success_message = "";
$error_message = "";
$update_log = [];

// CSRF Token oluştur
$csrf_token = generateCSRFToken();

$pdo = getSecureDatabaseConnection();

// Veritabanı güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $submitted_token = $_POST["csrf_token"] ?? "";
    if (!verifyCSRFToken($submitted_token)) {
        $error_message = "Güvenlik hatası. Lütfen sayfayı yenileyin.";
    } else {
        $action = $_POST['action'];
        
        if ($action === "update_database") {
            if ($pdo) {
                try {
                    $pdo->beginTransaction();
                    
                    // 1. Site content tablosunu kontrol et ve güncelle
                    $update_log[] = "🔍 Site content tablosu kontrol ediliyor...";
                    
                    $stmt = $pdo->query("SHOW TABLES LIKE 'site_content'");
                    if ($stmt->rowCount() == 0) {
                        $update_log[] = "❌ site_content tablosu bulunamadı!";
                    } else {
                        $update_log[] = "✅ site_content tablosu mevcut";
                        
                        // Mevcut içerik sayısını kontrol et
                        $stmt = $pdo->query("SELECT COUNT(*) FROM site_content");
                        $existing_count = $stmt->fetchColumn();
                        $update_log[] = "📊 Mevcut içerik sayısı: $existing_count";
                        
                        if ($existing_count == 0) {
                            $update_log[] = "📝 İçerikler aktarılıyor...";
                            
                            // Ana sayfa içerikleri
                            $home_contents = [
                                ['home', 'hero', 'main_title', 'Digital Dünyada Uğurunuz üçün Professional Həllər', 'text'],
                                ['home', 'hero', 'subtitle', 'Rəqəmsal Transformasiya', 'text'],
                                ['home', 'hero', 'description', 'Veb inkişaf, SEO, e-ticarət və rəqəmsal marketinq xidmətləri ilə biznesinizi növbəti səviyyəyə aparırıq.', 'text'],
                                ['home', 'hero', 'cta_text', 'İndi Başla', 'text'],
                                ['home', 'hero', 'video_text', 'Videomuzu İzləyin', 'text'],
                                
                                ['home', 'stats', 'projects_count', '500+', 'text'],
                                ['home', 'stats', 'projects_text', 'Tamamlanmış Layihə', 'text'],
                                ['home', 'stats', 'satisfaction_count', '98%', 'text'],
                                ['home', 'stats', 'satisfaction_text', 'Müştəri Məmnuniyyəti', 'text'],
                                ['home', 'stats', 'experience_count', '16+', 'text'],
                                ['home', 'stats', 'experience_text', 'İllik Təcrübə', 'text'],
                                
                                ['home', 'services', 'section_title', 'Xidmətlərimiz', 'text'],
                                ['home', 'services', 'section_subtitle', 'Biznesinizin rəqəmsal uğuru üçün lazım olan bütün xidmətləri təqdim edirik.', 'text'],
                                
                                ['home', 'services', 'seo_title', 'SEO Optimallaşdırma', 'text'],
                                ['home', 'services', 'seo_description', 'Google-da birinci səhifədə görünmək üçün peşəkar SEO xidmətləri.', 'text'],
                                ['home', 'services', 'seo_features', 'Açar söz tədqiqi, Texniki SEO, Məzmun optimallaşdırması', 'text'],
                                ['home', 'services', 'seo_result', '300% artış', 'text'],
                                
                                ['home', 'services', 'social_title', 'Sosial Media Marketinq', 'text'],
                                ['home', 'services', 'social_description', 'Instagram, Facebook, LinkedIn və digər platformalarda effektiv marketinq.', 'text'],
                                ['home', 'services', 'social_features', 'Məzmun yaradılması, Cədvəl planlaması, Analitika hesabatları', 'text'],
                                ['home', 'services', 'social_result', '500% engagement', 'text'],
                                
                                ['home', 'services', 'branding_title', 'Branding & Dizayn', 'text'],
                                ['home', 'services', 'branding_description', 'Güclü marka kimliyi və vizual dizayn həlləri.', 'text'],
                                ['home', 'services', 'branding_features', 'Loqo dizaynı, Brend kimliyı, Marketinq materialları', 'text'],
                                ['home', 'services', 'branding_result', '200% tanınırlıq', 'text'],
                                
                                ['home', 'services', 'ads_title', 'Rəqəmsal Reklam', 'text'],
                                ['home', 'services', 'ads_description', 'Google Ads, Facebook Ads və digər reklam platformalarında effektiv kampaniyalar.', 'text'],
                                ['home', 'services', 'ads_features', 'Google Ads, Facebook Ads, ROI optimallaşdırması', 'text'],
                                ['home', 'services', 'ads_result', '400% ROI', 'text'],
                                
                                ['home', 'testimonials', 'section_title', 'Müştəri Rəyləri', 'text'],
                                ['home', 'testimonials', 'section_subtitle', 'Bizə etibar edən müştərilərimizin uğur hekayələri və təcrübələri.', 'text'],
                                
                                ['home', 'testimonials', 'testimonial1_name', 'Əli Məmmədov', 'text'],
                                ['home', 'testimonials', 'testimonial1_position', 'Marketing Meneceri', 'text'],
                                ['home', 'testimonials', 'testimonial1_company', 'ABC Şirkəti', 'text'],
                                ['home', 'testimonials', 'testimonial1_text', 'NextCode Group ilə işləmək böyük zövq idi. SEO xidmətləri sayəsində saytımızın trafikini 300% artırdıq.', 'text'],
                                
                                ['home', 'testimonials', 'testimonial2_name', 'Leyla Həsənova', 'text'],
                                ['home', 'testimonials', 'testimonial2_position', 'CEO', 'text'],
                                ['home', 'testimonials', 'testimonial2_company', 'XYZ Company', 'text'],
                                ['home', 'testimonials', 'testimonial2_text', 'Peşəkar komanda və keyfiyyətli xidmət. Sosial media hesablarımızı mükəmməl idarə edirlər.', 'text'],
                                
                                ['home', 'testimonials', 'testimonial3_name', 'Rəşad Quliyev', 'text'],
                                ['home', 'testimonials', 'testimonial3_position', 'Sahibkar', 'text'],
                                ['home', 'testimonials', 'testimonial3_company', 'Startup Venture', 'text'],
                                ['home', 'testimonials', 'testimonial3_text', 'Brendinq xidmətləri üçün təşəkkür edirik. Yeni loqomuz və brend kimliyi çox uğurlu oldu.', 'text'],
                                
                                ['home', 'experience', 'title', '16 İllik Təcrübə və Güvən', 'text'],
                                ['home', 'experience', 'description', 'NextCode Group 2008-ci ildən bu yana rəqəmsal texnologiyalar sahəsində fasiləsiz xidmət göstərir.', 'text'],
                                ['home', 'experience', 'year', '2008-dən bəri', 'text'],
                                
                                ['home', 'cta', 'title', 'Biznesinizi Növbəti Səviyyəyə Çatdırmağa Hazırsınız?', 'text'],
                                ['home', 'cta', 'description', 'Pulsuz məsləhət seansı üçün bizimlə əlaqə saxlayın və rəqəmsal strategiyanızı müzakirə edək.', 'text'],
                                ['home', 'cta', 'button_text', 'İndi Başlayın', 'text'],
                                
                                ['home', 'footer', 'tagline', 'Professional solutions for your success in the digital world.', 'text'],
                                ['home', 'footer', 'email', 'info@nextcode.com', 'text'],
                                ['home', 'footer', 'phone', '+380 97 258 00 00', 'text'],
                                ['home', 'footer', 'address', 'Bakı şəhəri, Nəsimi rayonu<br>28 May küçəsi 15', 'html'],
                                ['home', 'footer', 'copyright', '© 2024 NextCode Group. All rights reserved.', 'text'],
                            ];
                            
                            // Hakkımızda sayfası içerikleri
                            $about_contents = [
                                ['about', 'hero', 'title', 'Haqqımızda', 'text'],
                                ['about', 'hero', 'subtitle', 'Azərbaycanın aparıcı rəqəmsal marketinq agentliyi olaraq biznesinizin uğuruna kömək edirik.', 'text'],
                                
                                ['about', 'who_we_are', 'title', 'Biz Kimik?', 'text'],
                                ['about', 'who_we_are', 'description', 'NextCode Group, 2008-ci ildən bəri Azərbaycanda rəqəmsal marketinq sahəsində fəaliyyət göstərən peşəkar komandadır.', 'text'],
                                
                                ['about', 'stats', 'clients_count', '500+', 'text'],
                                ['about', 'stats', 'clients_text', 'Müştəri', 'text'],
                                ['about', 'stats', 'projects_count', '1000+', 'text'],
                                ['about', 'stats', 'projects_text', 'Layihə', 'text'],
                                ['about', 'stats', 'experience_count', '15+', 'text'],
                                ['about', 'stats', 'experience_text', 'İllik Təcrübə', 'text'],
                                ['about', 'stats', 'satisfaction_count', '98%', 'text'],
                                ['about', 'stats', 'satisfaction_text', 'Məmnuniyyət', 'text'],
                                
                                ['about', 'experience', 'title', '16 İllik Təcrübə və Güvən', 'text'],
                                ['about', 'experience', 'description', 'NextCode Group 2008-ci ildə qurulduğu gündən bu yana rəqəmsal texnologiyalar sahəsində fasiləsiz xidmət göstərir.', 'text'],
                                ['about', 'experience', 'year', '2008-dən bəri', 'text'],
                                
                                ['about', 'team', 'title', 'Bizim Komanda', 'text'],
                                ['about', 'team', 'subtitle', 'Təcrübəli və yaradıcı mütəxəssislərimizlə tanış olun', 'text'],
                                
                                ['about', 'mission', 'title', 'Missiyamız', 'text'],
                                ['about', 'mission', 'description', 'Müştərilərimizin rəqəmsal dünyada güclü mövqe qazanması və biznes məqsədlərinə çatması üçün innovativ və effektiv həllər təqdim etmək.', 'text'],
                                
                                ['about', 'vision', 'title', 'Vizyonumuz', 'text'],
                                ['about', 'vision', 'description', 'Azərbaycanda rəqəmsal marketinq sahəsində lider agentlik olmaq və beynəlxalq bazarda tanınan bir brend yaratmaq.', 'text'],
                                
                                ['about', 'cta', 'title', 'Bizimlə İşləməyə Hazırsınız?', 'text'],
                                ['about', 'cta', 'description', 'Biznesinizi rəqəmsal dünyada irəli aparmaq üçün bizimlə əlaqə saxlayın', 'text'],
                                ['about', 'cta', 'contact_text', 'Əlaqə Saxlayın', 'text'],
                            ];
                            
                            // Hizmetler sayfası içerikleri
                            $services_contents = [
                                ['services', 'hero', 'title', 'Xidmətlərimiz', 'text'],
                                ['services', 'hero', 'subtitle', 'Biznesinizin rəqəmsal uğuru üçün lazım olan bütün xidmətləri təqdim edirik.', 'text'],
                                
                                ['services', 'seo', 'title', 'SEO Optimallaşdırma', 'text'],
                                ['services', 'seo', 'price', '500 AZN-dən başlayaraq', 'text'],
                                ['services', 'seo', 'description', 'Google-da birinci səhifədə görünmək üçün peşəkar SEO xidmətləri.', 'text'],
                                ['services', 'seo', 'features', 'Açar söz tədqiqatı, Texniki SEO audit, Məzmun optimizasiyası, Link building, Aylıq hesabat', 'text'],
                                ['services', 'seo', 'duration', '3-6 ay', 'text'],
                                
                                ['services', 'social_media', 'title', 'Sosial Media Marketinq', 'text'],
                                ['services', 'social_media', 'price', '300 AZN-dən başlayaraq', 'text'],
                                ['services', 'social_media', 'description', 'Instagram, Facebook, LinkedIn və digər platformalarda effektiv marketinq.', 'text'],
                                ['services', 'social_media', 'features', 'Məzmun planlaması, Post dizaynı, Cəmiyyət idarəçiliyi, Reklam kampaniyaları, Analitika və hesabat', 'text'],
                                ['services', 'social_media', 'duration', '1-3 ay', 'text'],
                                
                                ['services', 'branding', 'title', 'Branding & Dizayn', 'text'],
                                ['services', 'branding', 'price', '800 AZN-dən başlayaraq', 'text'],
                                ['services', 'branding', 'description', 'Güclü brend kimliyi yaratmaq və vizual dizayn həlləri', 'text'],
                                ['services', 'branding', 'features', 'Logo dizaynı, Brend kimliyı, Marketinq materialları', 'text'],
                                ['services', 'branding', 'duration', '2-4 ay', 'text'],
                                
                                ['services', 'digital_ads', 'title', 'Rəqəmsal Reklam', 'text'],
                                ['services', 'digital_ads', 'price', '600 AZN-dən başlayaraq', 'text'],
                                ['services', 'digital_ads', 'description', 'Google Ads, Facebook Ads və digər reklam platformalarında effektiv kampaniyalar', 'text'],
                                ['services', 'digital_ads', 'features', 'Google Ads, Facebook Ads, ROI optimallaşdırması', 'text'],
                                ['services', 'digital_ads', 'duration', '1-6 ay', 'text'],
                            ];
                            
                            // İletişim sayfası içerikleri
                            $contact_contents = [
                                ['contact', 'hero', 'title', 'Əlaqə', 'text'],
                                ['contact', 'hero', 'subtitle', 'Bizimlə əlaqə saxlayın', 'text'],
                                
                                ['contact', 'info', 'email', 'info@nextcode.com', 'text'],
                                ['contact', 'info', 'phone', '+380 97 258 00 00', 'text'],
                                ['contact', 'info', 'address', 'Bakı şəhəri, Nəsimi rayonu<br>28 May küçəsi 15', 'html'],
                                
                                ['contact', 'form', 'title', 'Bizimlə Əlaqə Saxlayın', 'text'],
                                ['contact', 'form', 'name_label', 'Ad Soyad', 'text'],
                                ['contact', 'form', 'email_label', 'E-mail', 'text'],
                                ['contact', 'form', 'phone_label', 'Telefon', 'text'],
                                ['contact', 'form', 'service_label', 'Maraqlandığınız Xidmət', 'text'],
                                ['contact', 'form', 'message_label', 'Mesaj', 'text'],
                                ['contact', 'form', 'submit_text', 'Mesaj Göndər', 'text'],
                            ];
                            
                            // Tüm içerikleri birleştir
                            $all_contents = array_merge($home_contents, $about_contents, $services_contents, $contact_contents);
                            
                            // İçerikleri veritabanına ekle
                            $stmt = $pdo->prepare("INSERT INTO site_content (page_name, section_name, content_key, content_value, content_type) VALUES (?, ?, ?, ?, ?)");
                            
                            foreach ($all_contents as $content) {
                                $stmt->execute($content);
                            }
                            
                            $update_log[] = "✅ " . count($all_contents) . " içerik başarıyla eklendi";
                            $update_log[] = "📊 Ana Sayfa: " . count($home_contents) . " içerik";
                            $update_log[] = "📊 Hakkımızda: " . count($about_contents) . " içerik";
                            $update_log[] = "📊 Hizmetler: " . count($services_contents) . " içerik";
                            $update_log[] = "📊 İletişim: " . count($contact_contents) . " içerik";
                            
                        } else {
                            $update_log[] = "ℹ️ İçerikler zaten mevcut, güncelleme yapılmadı";
                        }
                    }
                    
                    // 2. Diğer tabloları kontrol et
                    $tables_to_check = ['portfolio_projects', 'blog_posts', 'contact_messages', 'admin_users'];
                    foreach ($tables_to_check as $table) {
                        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                        if ($stmt->rowCount() > 0) {
                            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
                            $count = $stmt->fetchColumn();
                            $update_log[] = "✅ $table: $count kayıt";
                        } else {
                            $update_log[] = "❌ $table tablosu bulunamadı";
                        }
                    }
                    
                    $pdo->commit();
                    $success_message = "Veritabanı başarıyla güncellendi!";
                    logSecurityEvent('DATABASE_UPDATED', 'Database updated successfully', 'INFO');
                    
                } catch (Exception $e) {
                    $pdo->rollback();
                    $error_message = "Veritabanı güncelleme hatası: " . $e->getMessage();
                    $update_log[] = "❌ Hata: " . $e->getMessage();
                    logSecurityEvent('DATABASE_UPDATE_ERROR', $e->getMessage(), 'ERROR');
                }
            } else {
                $error_message = "Veritabanı bağlantı hatası!";
                $update_log[] = "❌ Veritabanı bağlantı hatası";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veritabanı Güncelleme | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .form-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; text-decoration: none; font-weight: bold; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-danger { background: #fee; color: #c33; border: 1px solid #fcc; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 8px; margin-bottom: 20px; transition: all 0.3s; }
        .back-btn:hover { background: #5a6268; transform: translateY(-2px); }
        .update-log { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 20px; border-left: 4px solid #667eea; }
        .update-log h3 { color: #333; margin-bottom: 15px; }
        .update-log ul { list-style: none; padding: 0; }
        .update-log li { padding: 5px 0; border-bottom: 1px solid #e9ecef; }
        .update-log li:last-child { border-bottom: none; }
        .warning-box { background: #fff3cd; padding: 20px; border-radius: 10px; border: 1px solid #ffeaa7; margin-bottom: 20px; }
        .warning-box h4 { color: #856404; margin-bottom: 10px; }
        .warning-box p { color: #856404; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Dashboard'a Dön
        </a>
        
        <div class="header">
            <h1><i class="fas fa-database"></i> Veritabanı Güncelleme</h1>
            <p>Site içeriklerini veritabanına aktarın ve güncelleyin</p>
        </div>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="warning-box">
            <h4><i class="fas fa-exclamation-triangle"></i> Önemli Bilgi</h4>
            <p>Bu işlem mevcut site içeriklerini veritabanına aktaracaktır. Eğer içerikler zaten varsa, tekrar eklenmeyecektir.</p>
        </div>
        
        <div class="form-container">
            <h2><i class="fas fa-sync-alt"></i> Veritabanını Güncelle</h2>
            <p>Mevcut site içeriklerini veritabanına aktarmak için aşağıdaki butona tıklayın.</p>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="action" value="update_database">
                
                <button type="submit" class="btn btn-primary" style="margin-top: 20px;">
                    <i class="fas fa-database"></i> Veritabanını Güncelle
                </button>
            </form>
            
            <?php if (!empty($update_log)): ?>
                <div class="update-log">
                    <h3><i class="fas fa-list"></i> Güncelleme Logları</h3>
                    <ul>
                        <?php foreach ($update_log as $log): ?>
                            <li><?php echo htmlspecialchars($log); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-container" style="margin-top: 20px;">
            <h2><i class="fas fa-info-circle"></i> Bilgi</h2>
            <p>Güncelleme işlemi şunları içerir:</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>✅ Site content tablosu kontrolü</li>
                <li>✅ Mevcut site içeriklerinin aktarılması</li>
                <li>✅ Ana sayfa, Hakkımızda, Hizmetler, İletişim sayfaları</li>
                <li>✅ Diğer tabloların kontrolü</li>
                <li>✅ Hata durumunda geri alma (rollback)</li>
            </ul>
            
            <div style="margin-top: 20px;">
                <a href="content.php" class="btn btn-secondary">
                    <i class="fas fa-edit"></i> İçerik Yönetimine Git
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Dashboard'a Dön
                </a>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            if (form) {
                form.addEventListener("submit", function() {
                    const submitBtn = form.querySelector("button[type='submit']");
                    if (submitBtn) {
                        submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Güncelleniyor...";
                        submitBtn.disabled = true;
                    }
                });
            }
        });
    </script>
</body>
</html>



