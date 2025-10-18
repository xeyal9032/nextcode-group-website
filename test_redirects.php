<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirect Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .test-link { display: block; margin: 10px 0; padding: 10px; border: 1px solid #ccc; text-decoration: none; }
        .test-link:hover { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Redirect Test Sayfası</h1>
    
    <h2>Test Linkleri:</h2>
    
    <a href="https://nextcode.az/images/portfolio/" class="test-link" target="_blank">
        🔗 https://nextcode.az/images/portfolio/ (Klasör)
    </a>
    
    <a href="https://nextcode.az/images/portfolio/marketplace.svg" class="test-link" target="_blank">
        🔗 https://nextcode.az/images/portfolio/marketplace.svg (SVG)
    </a>
    
    <a href="https://nextcode.az/images/portfolio/smart-city.svg" class="test-link" target="_blank">
        🔗 https://nextcode.az/images/portfolio/smart-city.svg (IoT Projesi)
    </a>
    
    <a href="https://nextcode.az/portfolio.php" class="test-link" target="_blank">
        🔗 https://nextcode.az/portfolio.php (Portfolio Sayfası)
    </a>
    
    <a href="https://nextcode.az/portfolio-detail.php?id=2" class="test-link" target="_blank">
        🔗 https://nextcode.az/portfolio-detail.php?id=2 (E-Ticaret Projesi)
    </a>
    
    <h2>.htaccess Durumu:</h2>
    
    <?php
    echo "<p class='info'>🔄 Minimal .htaccess aktif - HTTPS yönlendirmesi devre dışı</p>";
    
    if (file_exists('.htaccess')) {
        echo "<p class='success'>✅ .htaccess dosyası mevcut</p>";
        $content = file_get_contents('.htaccess');
        $lines = count(explode("\n", $content));
        echo "<p class='info'>📄 Satır sayısı: $lines</p>";
        
        if (strpos($content, 'RewriteRule.*https') !== false) {
            echo "<p class='warning'>⚠️ HTTPS yönlendirmesi aktif - bu redirect döngüsüne sebep olabilir</p>";
        } else {
            echo "<p class='success'>✅ HTTPS yönlendirmesi devre dışı</p>";
        }
        
        if (strpos($content, 'RewriteRule ^images/') !== false) {
            echo "<p class='success'>✅ Images klasörü erişimi aktif</p>";
        } else {
            echo "<p class='error'>❌ Images klasörü erişimi bulunamadı</p>";
        }
    } else {
        echo "<p class='error'>❌ .htaccess dosyası bulunamadı</p>";
    }
    
    echo "<h3>Sunucu Bilgileri:</h3>";
    echo "<p><strong>HTTPS:</strong> " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'Aktif' : 'Pasif') . "</p>";
    echo "<p><strong>Port:</strong> " . $_SERVER['SERVER_PORT'] . "</p>";
    echo "<p><strong>X-Forwarded-Proto:</strong> " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'Yok') . "</p>";
    echo "<p><strong>X-Forwarded-Ssl:</strong> " . ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? 'Yok') . "</p>";
    
    echo "<h3>Çözüm Durumu:</h3>";
    echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "<p><strong>ERR_TOO_MANY_REDIRECTS için yapılanlar:</strong></p>";
    echo "<ul>";
    echo "<li>✅ HTTPS yönlendirmesi geçici olarak devre dışı bırakıldı</li>";
    echo "<li>✅ Images klasörü için özel erişim kuralı eklendi</li>";
    echo "<li>✅ Minimal .htaccess kullanılıyor</li>";
    echo "<li>✅ Yedek dosyalar oluşturuldu (.htaccess.with-redirects)</li>";
    echo "</ul>";
    echo "<p><strong>Test Sırası:</strong></p>";
    echo "<ol>";
    echo "<li>Önce yukarıdaki linkleri test edin</li>";
    echo "<li>Eğer çalışıyorsa, HTTPS yönlendirmesini yeniden aktif edebiliriz</li>";
    echo "<li>Çalışmıyorsa, daha da basit bir .htaccess deneyebiliriz</li>";
    echo "</ol>";
    echo "</div>";
    ?>
    
</body>
</html>
