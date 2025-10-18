<?php
// Cache temizleme dosyası
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Cache Temizleme</h1>";

// Browser cache headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Dosya cache'ini temizle
$cache_dirs = [
    'cache/',
    'tmp/',
    'logs/',
    'assets/cache/'
];

foreach ($cache_dirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                echo "Silindi: $file<br>";
            }
        }
    }
}

// Portfolio resimlerini kontrol et
$portfolio_images = [
    'images/portfolio/ecommerce-project.jpg',
    'images/portfolio/corporate-website.jpg',
    'images/portfolio/mobile-app.jpg',
    'images/portfolio/ai-chatbot.jpg',
    'images/portfolio/responsive-design.jpg'
];

echo "<h2>Portfolio Resimleri:</h2>";
foreach ($portfolio_images as $image) {
    if (file_exists($image)) {
        $size = filesize($image);
        echo "✓ $image ($size bytes)<br>";
    } else {
        echo "✗ $image (BULUNAMADI)<br>";
    }
}

echo "<h2>Cache Temizlendi!</h2>";
echo "<p>Sayfayı yenileyin ve Ctrl+F5 ile hard refresh yapın.</p>";
echo "<p><a href='portfolio.php'>Portfolio Sayfasına Git</a></p>";
?>
