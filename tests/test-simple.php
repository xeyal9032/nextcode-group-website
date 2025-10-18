<?php
// Basit test sayfası - Yönlendirme sorunları testi
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basit Test - NextCode Group</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5; 
            padding: 20px;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .success { 
            color: #28a745; 
            font-size: 18px; 
            font-weight: bold; 
            margin: 20px 0;
        }
        .info { 
            color: #17a2b8; 
            margin: 10px 0; 
            padding: 10px;
            background: #e7f3ff;
            border-radius: 5px;
        }
        .error { 
            color: #dc3545; 
            margin: 10px 0; 
            padding: 10px;
            background: #f8d7da;
            border-radius: 5px;
        }
        .test-link {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .test-link:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Basit Test Sayfası</h1>
        
        <div class="success">
            ✅ Bu sayfa yüklenebiliyorsa temel sorunlar düzeltilmiştir!
        </div>
        
        <div class="info">
            <h3>📊 Sistem Bilgileri</h3>
            <p><strong>Protocol:</strong> <?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS' : 'HTTP'; ?></p>
            <p><strong>Host:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'Bilinmiyor'; ?></p>
            <p><strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'Bilinmiyor'; ?></p>
            <p><strong>Server Name:</strong> <?php echo $_SERVER['SERVER_NAME'] ?? 'Bilinmiyor'; ?></p>
            <p><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
        </div>
        
        <div class="info">
            <h3>🔗 Test Linkleri</h3>
            <p>Bu linkleri test edin:</p>
            <a href="index.php" class="test-link">Ana Sayfa</a>
            <a href="about.php" class="test-link">Hakkımızda</a>
            <a href="services.php" class="test-link">Hizmetler</a>
            <a href="portfolio.php" class="test-link">Portfolio</a>
            <a href="contact.php" class="test-link">İletişim</a>
        </div>
        
        <div class="info">
            <h3>📝 Yapılan Düzeltmeler</h3>
            <ul>
                <li>✅ Clean URLs kuralları güvenli hale getirildi</li>
                <li>✅ Ana sayfa dosyaları için özel kurallar eklendi</li>
                <li>✅ Yönlendirme döngüsü önlendi</li>
                <li>✅ SSL yönlendirme geçici olarak kapalı</li>
            </ul>
        </div>
        
        <div class="info">
            <h3>🎯 Test Sonucu</h3>
            <p>Bu sayfa yüklenebiliyorsa ve linkler çalışıyorsa, site normal şekilde çalışıyor demektir.</p>
        </div>
        
        <div class="info">
            <h3>⚠️ Önemli Notlar</h3>
            <ul>
                <li>SSL yönlendirme geçici olarak kapalı</li>
                <li>Site HTTP üzerinden çalışıyor</li>
                <li>Güvenlik önlemleri aktif</li>
                <li>Hosting sağlayıcısı SSL kurduğunda aktifleştirilecek</li>
            </ul>
        </div>
    </div>
</body>
</html>
