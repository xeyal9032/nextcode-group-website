<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Görsel Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-image { max-width: 300px; margin: 10px; border: 2px solid #ccc; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Görsel Test Sayfası</h1>
    
    <?php
    define('SECURE_ACCESS', true);
    require_once 'config/database.php';
    
    if ($pdo) {
        try {
            echo "<h2>Tüm Projelerin Yeni Fotoğrafları</h2>";
            
            $stmt = $pdo->query("SELECT id, title, image_url FROM portfolio_projects ORDER BY id");
            $projects = $stmt->fetchAll();
            
            foreach ($projects as $project) {
                echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
                echo "<h3>ID {$project['id']}: " . htmlspecialchars($project['title']) . "</h3>";
                echo "<p><strong>Fotoğraf URL:</strong> " . htmlspecialchars($project['image_url']) . "</p>";
                
                // Görsel testi
                echo "<div style='margin: 10px 0;'>";
                echo "<img src='" . htmlspecialchars($project['image_url']) . "' class='test-image' alt='" . htmlspecialchars($project['title']) . "' onerror=\"this.style.border='2px solid red'; this.alt='Görsel yüklenemedi!';\">";
                echo "</div>";
                
                // Dosya kontrolü
                $file_path = str_replace('https://nextcode.az/', __DIR__ . '/', $project['image_url']);
                if (file_exists($file_path)) {
                    echo "<p class='success'>✅ Dosya mevcut: " . basename($file_path) . " (" . filesize($file_path) . " bytes)</p>";
                } else {
                    echo "<p class='error'>❌ Dosya bulunamadı: " . basename($file_path) . "</p>";
                }
                
                echo "</div>";
            }
            
        } catch (PDOException $e) {
            echo "<p class='error'>Veritabanı hatası: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p class='error'>Veritabanı bağlantısı kurulamadı!</p>";
    }
    ?>
    
    <h3>Dizin ve İzin Kontrolü:</h3>
    <?php
    // Dizin kontrolü
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
    
    // .htaccess kontrolü
    $htaccess_files = ['.htaccess', 'images/.htaccess', 'images/portfolio/.htaccess'];
    foreach ($htaccess_files as $file) {
        if (file_exists($file)) {
            echo "<p class='success'>✅ .htaccess mevcut: $file</p>";
        } else {
            echo "<p class='error'>❌ .htaccess bulunamadı: $file</p>";
        }
    }
    ?>
    
    <h3>Çözüm Önerileri:</h3>
    <div style="background: #f0f8ff; padding: 15px; border-radius: 8px; margin: 10px 0;">
        <p><strong>403 Forbidden hatası için:</strong></p>
        <ul>
            <li>✅ .htaccess dosyaları oluşturuldu</li>
            <li>✅ Dizin izinleri ayarlandı</li>
            <li>✅ CORS başlıkları eklendi</li>
            <li>🔧 Web sunucu yeniden başlatılmalı</li>
            <li>🔧 Dizin izinleri 755 olmalı</li>
        </ul>
    </div>
    
</body>
</html>
