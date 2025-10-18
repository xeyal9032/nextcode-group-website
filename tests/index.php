<?php
/**
 * NextCode Group - Test Dosyaları Merkezi
 * 
 * Bu sayfa, projedeki tüm test dosyalarını kategorilere göre organize eder
 * ve kolay erişim sağlar.
 */

// Güvenlik kontrolü
if (!defined('TEST_ACCESS')) {
    define('TEST_ACCESS', true);
}

// Sayfa başlığı
$pageTitle = "Test Dosyaları Merkezi - NextCode Group";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1em;
        }
        .content {
            padding: 40px;
        }
        .category {
            margin-bottom: 40px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }
        .category-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        .category-header h2 {
            margin: 0;
            color: #333;
            font-size: 1.5em;
        }
        .category-header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .test-item {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .test-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            background: #fff;
        }
        .test-item h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.2em;
        }
        .test-item p {
            margin: 0;
            color: #666;
            font-size: 0.9em;
            line-height: 1.4;
        }
        .test-item .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: bold;
            margin-top: 10px;
        }
        .status.ready {
            background: #d4edda;
            color: #155724;
        }
        .status.testing {
            background: #fff3cd;
            color: #856404;
        }
        .status.debug {
            background: #f8d7da;
            color: #721c24;
        }
        .stats {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .stats h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #666;
            font-size: 0.9em;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Test Dosyaları Merkezi</h1>
            <p>NextCode Group Web Projesi - Tüm Test ve Debug Araçları</p>
        </div>
        
        <div class="content">
            <div class="stats">
                <h3>📊 Test İstatistikleri</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">25+</div>
                        <div class="stat-label">Test Dosyası</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">6</div>
                        <div class="stat-label">Kategori</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Kapsam</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo date('d.m.Y'); ?></div>
                        <div class="stat-label">Son Güncelleme</div>
                    </div>
                </div>
            </div>

            <!-- Sistem Testleri -->
            <div class="category">
                <div class="category-header">
                    <h2>🔧 Sistem Testleri</h2>
                    <p>Genel sistem ve performans testleri</p>
                </div>
                <div class="test-grid">
                    <a href="comprehensive-test.php" class="test-item">
                        <h3>Kapsamlı Sistem Testi</h3>
                        <p>Tüm sistem bileşenlerini test eder - API, veritabanı, dosyalar</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="final-tests.php" class="test-item">
                        <h3>Final Test Sistemi</h3>
                        <p>Son kontroller ve optimizasyon testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="az-complete-test.php" class="test-item">
                        <h3>Azərbaycan Testi</h3>
                        <p>Azerbaycan dili desteği ve yerelleştirme testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="simple-test.php" class="test-item">
                        <h3>Basit Test</h3>
                        <p>Temel sistem kontrolleri ve hızlı test</p>
                        <span class="status ready">Hazır</span>
                    </a>
                </div>
            </div>

            <!-- API Testleri -->
            <div class="category">
                <div class="category-header">
                    <h2>📡 API Testleri</h2>
                    <p>API endpoint'leri ve servis testleri</p>
                </div>
                <div class="test-grid">
                    <a href="test-blog.php" class="test-item">
                        <h3>Blog API Testi</h3>
                        <p>Blog sistemi API'lerini test eder</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="test-cdn.php" class="test-item">
                        <h3>CDN Testi</h3>
                        <p>CDN bağlantıları ve performans testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="test-email-system.php" class="test-item">
                        <h3>Email Sistemi Testi</h3>
                        <p>Email gönderim sistemi ve SMTP testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="test-redirect.php" class="test-item">
                        <h3>Yönlendirme Testi</h3>
                        <p>URL yönlendirmeleri ve routing testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                </div>
            </div>

            <!-- Tema ve UI Testleri -->
            <div class="category">
                <div class="category-header">
                    <h2>🎨 Tema ve UI Testleri</h2>
                    <p>Kullanıcı arayüzü ve tema testleri</p>
                </div>
                <div class="test-grid">
                    <a href="test-theme-toggle.php" class="test-item">
                        <h3>Tema Değiştirme Testi</h3>
                        <p>Light/Dark mode geçiş testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="theme-test.php" class="test-item">
                        <h3>Tema Sistemi Testi</h3>
                        <p>Genel tema sistemi ve CSS testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="theme-comprehensive-test.php" class="test-item">
                        <h3>Kapsamlı Tema Testi</h3>
                        <p>Detaylı tema ve stil testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="final-theme-test.php" class="test-item">
                        <h3>Final Tema Testi</h3>
                        <p>Son tema kontrolleri ve optimizasyonlar</p>
                        <span class="status ready">Hazır</span>
                    </a>
                </div>
            </div>

            <!-- Okunabilirlik Testleri -->
            <div class="category">
                <div class="category-header">
                    <h2>📖 Okunabilirlik Testleri</h2>
                    <p>Font, metin ve okunabilirlik testleri</p>
                </div>
                <div class="test-grid">
                    <a href="test-readability.php" class="test-item">
                        <h3>Okunabilirlik Testi</h3>
                        <p>Font yükleme ve metin okunabilirlik testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="text-visibility-test.php" class="test-item">
                        <h3>Metin Görünürlük Testi</h3>
                        <p>Metin görünürlüğü ve kontrast testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="text-visibility-comprehensive-test.php" class="test-item">
                        <h3>Kapsamlı Metin Testi</h3>
                        <p>Detaylı metin ve font analizi</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="final-text-visibility-test.php" class="test-item">
                        <h3>Final Metin Testi</h3>
                        <p>Son metin kontrolleri ve optimizasyonlar</p>
                        <span class="status ready">Hazır</span>
                    </a>
                </div>
            </div>

            <!-- Debug Araçları -->
            <div class="category">
                <div class="category-header">
                    <h2>🐛 Debug Araçları</h2>
                    <p>Hata ayıklama ve sistem analizi araçları</p>
                </div>
                <div class="test-grid">
                    <a href="DEBUG_ARACLARI.php" class="test-item">
                        <h3>Debug Araçları</h3>
                        <p>Kapsamlı debug ve sistem analiz araçları</p>
                        <span class="status debug">Debug</span>
                    </a>
                    <a href="debug-blog-api.php" class="test-item">
                        <h3>Blog API Debug</h3>
                        <p>Blog API'leri için özel debug araçları</p>
                        <span class="status debug">Debug</span>
                    </a>
                </div>
            </div>

            <!-- Özel Testler -->
            <div class="category">
                <div class="category-header">
                    <h2>🔍 Özel Testler</h2>
                    <p>Spesifik özellikler için özel testler</p>
                </div>
                <div class="test-grid">
                    <a href="test-font-fix.php" class="test-item">
                        <h3>Font Düzeltme Testi</h3>
                        <p>Font yükleme sorunları ve düzeltme testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="test-glass-card.php" class="test-item">
                        <h3>Glass Card Testi</h3>
                        <p>Glassmorphism efektleri ve kart testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="test-quick.php" class="test-item">
                        <h3>Hızlı Test</h3>
                        <p>Hızlı sistem kontrolleri ve temel testler</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="test-simple.php" class="test-item">
                        <h3>Basit Test</h3>
                        <p>Temel fonksiyonlar ve basit kontroller</p>
                        <span class="status ready">Hazır</span>
                    </a>
                    <a href="TEMPLATE_TEST_DOSYASI.php" class="test-item">
                        <h3>Template Test Dosyası</h3>
                        <p>Template sistemi ve şablon testleri</p>
                        <span class="status ready">Hazır</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>NextCode Group</strong> - Test Dosyaları Merkezi</p>
            <p>Son Güncelleme: <?php echo date('d.m.Y H:i:s'); ?> | Toplam Test: 25+ | Durum: Aktif ✅</p>
        </div>
    </div>
</body>
</html>
