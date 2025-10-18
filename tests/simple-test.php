<?php
// Basit test sayfası - Redirect döngüsü testi
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basit Test - NextCode Group</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-size: 18px; font-weight: bold; }
        .info { color: #17a2b8; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 Basit Test Sayfası</h1>
        
        <div class="success">
            ✅ Bu sayfa yüklenebiliyorsa redirect döngüsü düzeltilmiştir!
        </div>
        
        <div class="info">
            <p><strong>Protocol:</strong> <?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS' : 'HTTP'; ?></p>
            <p><strong>Host:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'Bilinmiyor'; ?></p>
            <p><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
        
        <h2>🔗 Test Linkleri</h2>
        <ul>
            <li><a href="index.php">Ana Sayfa</a></li>
            <li><a href="about.php">Hakkımızda</a></li>
            <li><a href="services.php">Hizmetler</a></li>
            <li><a href="portfolio.php">Portfolio</a></li>
            <li><a href="contact.php">İletişim</a></li>
        </ul>
        
        <h2>📝 Durum</h2>
        <p>SSL yönlendirme geçici olarak devre dışı bırakıldı. Site normal şekilde çalışmalı.</p>
    </div>
</body>
</html>
