<?php
// Görsel erişim testi
define('SECURE_ACCESS', true);
require_once 'config/database.php';

echo "<h1>Görsel Erişim Testi</h1>";

if (!$pdo) {
    die('Veritabanı bağlantısı kurulamadı!');
}

try {
    echo "<h2>Portfolio Projeleri ve Görselleri</h2>";
    
    $stmt = $pdo->query("SELECT id, title, image_url FROM portfolio_projects ORDER BY id");
    $projects = $stmt->fetchAll();
    
    foreach ($projects as $project) {
        echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
        echo "<h3>ID {$project['id']}: " . htmlspecialchars($project['title']) . "</h3>";
        
        // Dosya varlığı kontrolü
        $local_path = str_replace('https://nextcode.az/', __DIR__ . '/', $project['image_url']);
        if (file_exists($local_path)) {
            echo "<p style='color: green;'>✅ Dosya mevcut: " . basename($local_path) . " (" . filesize($local_path) . " bytes)</p>";
        } else {
            echo "<p style='color: red;'>❌ Dosya bulunamadı: " . basename($local_path) . "</p>";
        }
        
        // Web erişim testi
        echo "<p><strong>Web URL:</strong> <a href='" . htmlspecialchars($project['image_url']) . "' target='_blank'>" . htmlspecialchars($project['image_url']) . "</a></p>";
        
        // Görsel gösterimi
        echo "<div style='margin: 10px 0;'>";
        echo "<img src='" . htmlspecialchars($project['image_url']) . "' style='max-width: 200px; border: 1px solid #ccc;' alt='" . htmlspecialchars($project['title']) . "' onerror=\"this.style.border='2px solid red'; this.alt='Görsel yüklenemedi!'; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkfDtnJzZWwgecO2a2xlbmVtZWRpPC90ZXh0Pjwvc3ZnPg==';\">";
        echo "</div>";
        
        echo "</div>";
    }
    
    echo "<h2>Dizin İzinleri Testi</h2>";
    
    $directories = [
        'images',
        'images/portfolio'
    ];
    
    foreach ($directories as $dir) {
        if (is_dir($dir)) {
            echo "<p style='color: green;'>✅ Dizin mevcut: $dir</p>";
            
            // İzin kontrolü
            if (is_readable($dir)) {
                echo "<p style='color: green;'>✅ Dizin okunabilir: $dir</p>";
            } else {
                echo "<p style='color: red;'>❌ Dizin okunamıyor: $dir</p>";
            }
            
            // .htaccess dosyası kontrolü
            $htaccess_path = $dir . '/.htaccess';
            if (file_exists($htaccess_path)) {
                echo "<p style='color: green;'>✅ .htaccess mevcut: $htaccess_path</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ .htaccess bulunamadı: $htaccess_path</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ Dizin bulunamadı: $dir</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Veritabanı hatası: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>











