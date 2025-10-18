<?php
/**
 * NextCode Group - Araçlar Merkezi
 * 
 * Bu sayfa, projedeki tüm yardımcı araçları kategorilere göre organize eder
 * ve kolay erişim sağlar.
 */

// Güvenlik kontrolü
if (!defined('TOOLS_ACCESS')) {
    define('TOOLS_ACCESS', true);
}

// Sayfa başlığı
$pageTitle = "Araçlar Merkezi - NextCode Group";
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
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .tool-item {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .tool-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            background: #fff;
        }
        .tool-item h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.2em;
        }
        .tool-item p {
            margin: 0;
            color: #666;
            font-size: 0.9em;
            line-height: 1.4;
        }
        .tool-item .type {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: bold;
            margin-top: 10px;
        }
        .type.setup {
            background: #d4edda;
            color: #155724;
        }
        .type.update {
            background: #fff3cd;
            color: #856404;
        }
        .type.check {
            background: #d1ecf1;
            color: #0c5460;
        }
        .type.fix {
            background: #f8d7da;
            color: #721c24;
        }
        .type.utility {
            background: #e2e3e5;
            color: #383d41;
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
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .warning h4 {
            margin: 0 0 10px 0;
            color: #856404;
        }
        .warning p {
            margin: 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛠️ Araçlar Merkezi</h1>
            <p>NextCode Group Web Projesi - Tüm Yardımcı Araçlar</p>
        </div>
        
        <div class="content">
            <div class="warning">
                <h4>⚠️ Önemli Uyarı</h4>
                <p>Bu araçlar sistem dosyalarını değiştirebilir. Kullanmadan önce mutlaka backup alın!</p>
            </div>

            <div class="stats">
                <h3>📊 Araç İstatistikleri</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">10+</div>
                        <div class="stat-label">Araç</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">5</div>
                        <div class="stat-label">Kategori</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Güvenli</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo date('d.m.Y'); ?></div>
                        <div class="stat-label">Son Güncelleme</div>
                    </div>
                </div>
            </div>

            <!-- Setup Araçları -->
            <div class="category">
                <div class="category-header">
                    <h2>🔧 Setup Araçları</h2>
                    <p>Kurulum ve ilk yapılandırma araçları</p>
                </div>
                <div class="tools-grid">
                    <a href="setup/setup.php" class="tool-item">
                        <h3>Ana Kurulum</h3>
                        <p>Projenin ana kurulum scripti - veritabanı, tablolar ve temel ayarlar</p>
                        <span class="type setup">Setup</span>
                    </a>
                    <a href="setup/setup_site_content.php" class="tool-item">
                        <h3>Site İçerik Kurulumu</h3>
                        <p>Site içeriklerinin kurulumu ve temel verilerin eklenmesi</p>
                        <span class="type setup">Setup</span>
                    </a>
                </div>
            </div>

            <!-- Update Araçları -->
            <div class="category">
                <div class="category-header">
                    <h2>🔄 Update Araçları</h2>
                    <p>Güncelleme ve veri senkronizasyon araçları</p>
                </div>
                <div class="tools-grid">
                    <a href="update/update-blog-db.php" class="tool-item">
                        <h3>Blog Veritabanı Güncellemesi</h3>
                        <p>Blog veritabanı yapısını günceller ve eksik verileri ekler</p>
                        <span class="type update">Update</span>
                    </a>
                    <a href="update/update-blog-db-2.php" class="tool-item">
                        <h3>Blog Veritabanı Güncellemesi v2</h3>
                        <p>Blog veritabanı için gelişmiş güncelleme scripti</p>
                        <span class="type update">Update</span>
                    </a>
                    <a href="update/update-live-blog.php" class="tool-item">
                        <h3>Canlı Blog Güncellemesi</h3>
                        <p>Canlı blog verilerini günceller ve senkronize eder</p>
                        <span class="type update">Update</span>
                    </a>
                </div>
            </div>

            <!-- Check Araçları -->
            <div class="category">
                <div class="category-header">
                    <h2>✅ Check Araçları</h2>
                    <p>Sistem kontrolü ve doğrulama araçları</p>
                </div>
                <div class="tools-grid">
                    <a href="check/check-blog-db.php" class="tool-item">
                        <h3>Blog Veritabanı Kontrolü</h3>
                        <p>Blog veritabanı yapısını kontrol eder ve sorunları tespit eder</p>
                        <span class="type check">Check</span>
                    </a>
                </div>
            </div>

            <!-- Fix Araçları -->
            <div class="category">
                <div class="category-header">
                    <h2>🛠️ Fix Araçları</h2>
                    <p>Hata düzeltme ve onarım araçları</p>
                </div>
                <div class="tools-grid">
                    <a href="fix/fix-blog-schema.php" class="tool-item">
                        <h3>Blog Şeması Düzeltme</h3>
                        <p>Blog veritabanı şemasındaki hataları düzeltir</p>
                        <span class="type fix">Fix</span>
                    </a>
                </div>
            </div>

            <!-- Utility Araçları -->
            <div class="category">
                <div class="category-header">
                    <h2>🔧 Utility Araçları</h2>
                    <p>Genel yardımcı araçlar ve sistem yönetimi</p>
                </div>
                <div class="tools-grid">
                    <a href="utilities/clear-cache.php" class="tool-item">
                        <h3>Cache Temizleme</h3>
                        <p>Sistem cache'ini temizler ve performansı artırır</p>
                        <span class="type utility">Utility</span>
                    </a>
                    <a href="utilities/generate-sitemap.php" class="tool-item">
                        <h3>Site Haritası Oluşturma</h3>
                        <p>SEO için site haritası (sitemap.xml) oluşturur</p>
                        <span class="type utility">Utility</span>
                    </a>
                    <a href="utilities/force-refresh.php" class="tool-item">
                        <h3>Zorla Yenileme</h3>
                        <p>Sistem dosyalarını zorla yeniler ve günceller</p>
                        <span class="type utility">Utility</span>
                    </a>
                    <a href="utilities/KOD_YAPISI_HARITASI.php" class="tool-item">
                        <h3>Kod Yapısı Haritası</h3>
                        <p>Proje kod yapısını görsel olarak gösterir</p>
                        <span class="type utility">Utility</span>
                    </a>
                    <a href="utilities/test_output.html" class="tool-item">
                        <h3>Test Çıktısı</h3>
                        <p>Test sonuçlarının HTML çıktısı</p>
                        <span class="type utility">Utility</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>NextCode Group</strong> - Araçlar Merkezi</p>
            <p>Son Güncelleme: <?php echo date('d.m.Y H:i:s'); ?> | Toplam Araç: 10+ | Durum: Aktif ✅</p>
        </div>
    </div>
</body>
</html>
