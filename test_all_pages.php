<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Test Raporu</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .test-section { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px; }
        .test-link { display: inline-block; margin: 5px; padding: 8px 12px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        .test-link:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🧪 SAYFA TEST RAPORU</h1>
    
    <div class="test-section">
        <h2>📊 VERİTABANI DURUMU</h2>
        <?php
        define('SECURE_ACCESS', true);
        require_once 'config/database.php';
        
        if (!$pdo) {
            echo '<p class="error">❌ Veritabanı bağlantısı kurulamadı!</p>';
        } else {
            echo '<p class="success">✅ Veritabanı bağlantısı başarılı</p>';
            
            try {
                // Portfolio projeleri
                $portfolio_count = $pdo->query("SELECT COUNT(*) FROM portfolio_projects")->fetchColumn();
                echo "<p>✅ Portfolio projeleri: $portfolio_count adet</p>";
                
                // Blog yazıları
                $blog_count = $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
                echo "<p>✅ Blog yazıları: $blog_count adet</p>";
                
                // Site ayarları
                $settings_count = $pdo->query("SELECT COUNT(*) FROM site_settings")->fetchColumn();
                echo "<p>✅ Site ayarları: $settings_count adet</p>";
                
            } catch (PDOException $e) {
                echo '<p class="error">❌ Veritabanı hatası: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>🔗 ANA SAYFALAR</h2>
        <p>Bu linkleri yeni sekmede açarak test edin:</p>
        
        <a href="https://nextcode.az/" class="test-link" target="_blank">🏠 Ana Sayfa</a>
        <a href="https://nextcode.az/about.php" class="test-link" target="_blank">👥 Hakkımızda</a>
        <a href="https://nextcode.az/services.php" class="test-link" target="_blank">🛠️ Hizmetler</a>
        <a href="https://nextcode.az/portfolio.php" class="test-link" target="_blank">💼 Portfolio</a>
        <a href="https://nextcode.az/blog.php" class="test-link" target="_blank">📝 Blog</a>
        <a href="https://nextcode.az/contact.php" class="test-link" target="_blank">📞 İletişim</a>
        <a href="https://nextcode.az/pricing.php" class="test-link" target="_blank">💰 Fiyatlandırma</a>
        <a href="https://nextcode.az/faq.php" class="test-link" target="_blank">❓ SSS</a>
    </div>
    
    <div class="test-section">
        <h2>🎯 PORTFOLIO DETAYLARI</h2>
        <p>Portfolio proje detay sayfalarını test edin:</p>
        
        <a href="https://nextcode.az/portfolio-detail.php?id=1" class="test-link" target="_blank">🤖 AI Analiz Platformu</a>
        <a href="https://nextcode.az/portfolio-detail.php?id=2" class="test-link" target="_blank">🛒 E-Ticaret Platformu</a>
        <a href="https://nextcode.az/portfolio-detail.php?id=3" class="test-link" target="_blank">₿ Kripto Cüzdan</a>
        <a href="https://nextcode.az/portfolio-detail.php?id=4" class="test-link" target="_blank">🏢 Kurumsal Web</a>
        <a href="https://nextcode.az/portfolio-detail.php?id=5" class="test-link" target="_blank">📱 Fitness App</a>
        <a href="https://nextcode.az/portfolio-detail.php?id=6" class="test-link" target="_blank">🏠 IoT Akıllı Ev</a>
        <a href="https://nextcode.az/portfolio-detail.php?id=7" class="test-link" target="_blank">💼 SaaS CRM</a>
    </div>
    
    <div class="test-section">
        <h2>📝 BLOG DETAYLARI (AZERBAYCAN DİLİNDE)</h2>
        <p>Blog yazı detay sayfalarını test edin:</p>
        
        <a href="https://nextcode.az/blog-post.php?id=1" class="test-link" target="_blank">💻 Veb İnkişaf Trendləri</a>
        <a href="https://nextcode.az/blog-post.php?id=2" class="test-link" target="_blank">📱 Mobil Tətbiqlər</a>
        <a href="https://nextcode.az/blog-post.php?id=3" class="test-link" target="_blank">📈 Rəqəmsal Marketinq</a>
        <a href="https://nextcode.az/blog-post.php?id=4" class="test-link" target="_blank">🤖 Süni İntellekt</a>
        <a href="https://nextcode.az/blog-post.php?id=5" class="test-link" target="_blank">🚀 Rəqəmsal Sahibkarlıq</a>
    </div>
    
    <div class="test-section">
        <h2>🖼️ GÖRSEL TESTLERİ</h2>
        <p>Portfolio ve blog görsellerini test edin:</p>
        
        <h3>Portfolio Görselleri:</h3>
        <a href="https://nextcode.az/images/portfolio/neural-visualizer.svg" class="test-link" target="_blank">🤖 AI Görsel</a>
        <a href="https://nextcode.az/images/portfolio/marketplace.svg" class="test-link" target="_blank">🛒 E-Ticaret Görsel</a>
        <a href="https://nextcode.az/images/portfolio/defi-platform.svg" class="test-link" target="_blank">₿ Kripto Görsel</a>
        <a href="https://nextcode.az/images/portfolio/corporate-website.jpg" class="test-link" target="_blank">🏢 Kurumsal Görsel</a>
        <a href="https://nextcode.az/images/portfolio/mobile-app.jpg" class="test-link" target="_blank">📱 Mobil Görsel</a>
        <a href="https://nextcode.az/images/portfolio/smart-city.svg" class="test-link" target="_blank">🏠 IoT Görsel</a>
        <a href="https://nextcode.az/images/portfolio/project-management.svg" class="test-link" target="_blank">💼 CRM Görsel</a>
        
        <h3>Blog Görselleri:</h3>
        <a href="https://nextcode.az/images/blog/veb-inkisaf-trendleri.jpg" class="test-link" target="_blank">💻 Veb İnkişaf</a>
        <a href="https://nextcode.az/images/blog/mobil-tetbiq-inkisafi.jpg" class="test-link" target="_blank">📱 Mobil Tətbiqlər</a>
        <a href="https://nextcode.az/images/blog/reqemsal-marketinq.jpg" class="test-link" target="_blank">📈 Rəqəmsal Marketinq</a>
        <a href="https://nextcode.az/images/blog/suni-intellekt.jpg" class="test-link" target="_blank">🤖 Süni İntellekt</a>
        <a href="https://nextcode.az/images/blog/reqemsal-sahibkarlig.jpg" class="test-link" target="_blank">🚀 Rəqəmsal Sahibkarlıq</a>
    </div>
    
    <div class="test-section">
        <h2>🔧 API ENDPOINT'LERİ</h2>
        <p>API endpoint'lerini test edin:</p>
        
        <a href="https://nextcode.az/api/portfolio.php" class="test-link" target="_blank">📊 Portfolio API</a>
        <a href="https://nextcode.az/api/blog.php" class="test-link" target="_blank">📝 Blog API</a>
        <a href="https://nextcode.az/api/services.php" class="test-link" target="_blank">🛠️ Services API</a>
        <a href="https://nextcode.az/api/settings.php" class="test-link" target="_blank">⚙️ Settings API</a>
    </div>
    
    <div class="test-section">
        <h2>👨‍💼 ADMIN PANELİ</h2>
        <p>Admin paneli sayfalarını test edin (giriş gerekebilir):</p>
        
        <a href="https://nextcode.az/admin/" class="test-link" target="_blank">🏠 Admin Dashboard</a>
        <a href="https://nextcode.az/admin/login.php" class="test-link" target="_blank">🔐 Admin Login</a>
    </div>
    
    <div class="test-section">
        <h2>📋 TEST NOTLARI</h2>
        <ul>
            <li><strong>Görsel Testleri:</strong> Tüm portfolio görsellerinin düzgün yüklenip yüklenmediğini kontrol edin</li>
            <li><strong>Responsive Test:</strong> Sayfaları farklı ekran boyutlarında test edin</li>
            <li><strong>Hız Testi:</strong> Sayfa yükleme hızlarını kontrol edin</li>
            <li><strong>SEO Test:</strong> Meta tag'lerin doğru göründüğünü kontrol edin</li>
            <li><strong>Form Testleri:</strong> İletişim formlarının çalıştığını kontrol edin</li>
            <li><strong>API Testleri:</strong> API endpoint'lerinin JSON döndürdüğünü kontrol edin</li>
        </ul>
    </div>
    
    <div class="test-section">
        <h2>🚨 BİLİNEN SORUNLAR VE ÇÖZÜMLER</h2>
        <ul>
            <li><strong>Görsel Sorunları:</strong> ✅ Çözüldü - Tüm görseller relatif yollara çevrildi</li>
            <li><strong>Redirect Sorunları:</strong> ✅ Çözüldü - .htaccess basitleştirildi</li>
            <li><strong>Veritabanı Sorunları:</strong> ✅ Çözüldü - Tüm veriler güncellendi</li>
            <li><strong>Kategori Sorunları:</strong> ✅ Çözüldü - Kategoriler yeniden düzenlendi</li>
        </ul>
    </div>
    
    <script>
        // Sayfa yükleme hızlarını test et
        window.addEventListener('load', function() {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log('Sayfa yükleme süresi: ' + loadTime + 'ms');
        });
        
        // Test linklerini izle
        document.querySelectorAll('.test-link').forEach(link => {
            link.addEventListener('click', function() {
                console.log('Test edilen sayfa: ' + this.textContent);
            });
        });
    </script>
</body>
</html>
