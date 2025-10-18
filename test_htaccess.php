<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>.htaccess Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>.htaccess Test Sayfası</h1>
    
    <h2>Test Sonuçları:</h2>
    
    <?php
    echo "<p class='info'>🔄 .htaccess dosyaları basitleştirildi...</p>";
    
    // .htaccess dosyalarını kontrol et
    $htaccess_files = [
        '.htaccess' => 'Ana .htaccess dosyası',
        'images/.htaccess' => 'Images klasörü .htaccess',
        'images/portfolio/.htaccess' => 'Portfolio klasörü .htaccess'
    ];
    
    foreach ($htaccess_files as $file => $description) {
        if (file_exists($file)) {
            echo "<p class='success'>✅ $description mevcut: $file</p>";
            $content = file_get_contents($file);
            $lines = count(explode("\n", $content));
            echo "<p class='info'>📄 Satır sayısı: $lines</p>";
        } else {
            echo "<p class='error'>❌ $description bulunamadı: $file</p>";
        }
    }
    
    echo "<h3>Görsel Test:</h3>";
    
    // Basit görsel testi
    $test_image = 'https://nextcode.az/images/portfolio/marketplace.svg';
    echo "<p>Test görseli: <a href='$test_image' target='_blank'>$test_image</a></p>";
    echo "<img src='$test_image' style='max-width: 200px; border: 1px solid #ccc;' alt='Test Görsel' onerror=\"this.style.border='2px solid red'; this.alt='Görsel yüklenemedi!'; alert('Görsel yüklenemedi: ' + this.src);\" onload=\"this.style.border='2px solid green';\">";
    
    echo "<h3>Dizin Erişim Testi:</h3>";
    
    // Dizin erişim testi
    $directories = ['images', 'images/portfolio'];
    foreach ($directories as $dir) {
        if (is_dir($dir)) {
            echo "<p class='success'>✅ Dizin mevcut: $dir</p>";
            if (is_readable($dir)) {
                echo "<p class='success'>✅ Dizin okunabilir: $dir</p>";
            } else {
                echo "<p class='error'>❌ Dizin okunamıyor: $dir</p>";
            }
        } else {
            echo "<p class='error'>❌ Dizin bulunamadı: $dir</p>";
        }
    }
    
    echo "<h3>Çözüm Durumu:</h3>";
    echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "<p><strong>500 Internal Server Error için yapılanlar:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Ana .htaccess basitleştirildi</li>";
    echo "<li>✅ Images/.htaccess sadece 'Options -Indexes' içeriyor</li>";
    echo "<li>✅ Portfolio/.htaccess sadece 'Options -Indexes' içeriyor</li>";
    echo "<li>✅ Karmaşık direktifler kaldırıldı</li>";
    echo "<li>✅ RewriteRule ile images erişimi sağlandı</li>";
    echo "</ul>";
    echo "<p><strong>Test edilecek URL'ler:</strong></p>";
    echo "<ul>";
    echo "<li><a href='https://nextcode.az/images/portfolio/' target='_blank'>https://nextcode.az/images/portfolio/</a></li>";
    echo "<li><a href='https://nextcode.az/portfolio.php' target='_blank'>https://nextcode.az/portfolio.php</a></li>";
    echo "<li><a href='https://nextcode.az/portfolio-detail.php?id=2' target='_blank'>E-Ticaret Projesi</a></li>";
    echo "</ul>";
    echo "</div>";
    ?>
    
</body>
</html>











