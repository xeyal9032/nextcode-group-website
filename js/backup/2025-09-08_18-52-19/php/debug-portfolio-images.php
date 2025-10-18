<?php
header('Content-Type: text/html; charset=utf-8');
echo "<h1>Portfolio Resim Debug</h1>";

// Portfolio resimlerini kontrol et
$portfolio_images = [
    'images/portfolio/ecommerce-project.jpg',
    'images/portfolio/corporate-website.jpg',
    'images/portfolio/mobile-app.jpg',
    'images/portfolio/ai-chatbot.jpg',
    'images/portfolio/responsive-design.jpg'
];

echo "<h2>Resim Kontrolü:</h2>";
foreach ($portfolio_images as $image) {
    if (file_exists($image)) {
        $size = filesize($image);
        $modified = date('Y-m-d H:i:s', filemtime($image));
        echo "✓ $image<br>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;Boyut: $size bytes<br>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;Son değişiklik: $modified<br>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;URL: <a href='$image' target='_blank'>$image</a><br><br>";
    } else {
        echo "✗ $image (BULUNAMADI)<br><br>";
    }
}

// Portfolio.php dosyasındaki resim yollarını kontrol et
echo "<h2>Portfolio.php Dosya Kontrolü:</h2>";
$portfolio_content = file_get_contents('portfolio.php');
$image_pattern = '/images\/portfolio\/[^"\']+\.jpg/';
preg_match_all($image_pattern, $portfolio_content, $matches);

if (!empty($matches[0])) {
    echo "Bulunan resim yolları:<br>";
    foreach (array_unique($matches[0]) as $match) {
        echo "• $match<br>";
    }
} else {
    echo "Portfolio.php dosyasında resim yolu bulunamadı!<br>";
}

// Cache kontrolü
echo "<h2>Cache Kontrolü:</h2>";
echo "Server Time: " . date('Y-m-d H:i:s') . "<br>";
echo "Cache Control: " . (isset($_SERVER['HTTP_CACHE_CONTROL']) ? $_SERVER['HTTP_CACHE_CONTROL'] : 'Not set') . "<br>";

// Test resimleri
echo "<h2>Test Resimleri:</h2>";
foreach ($portfolio_images as $image) {
    if (file_exists($image)) {
        echo "<div style='margin: 20px; display: inline-block;'>";
        echo "<h4>" . basename($image) . "</h4>";
        echo "<img src='$image?v=" . time() . "' style='width: 200px; height: 150px; object-fit: cover; border: 1px solid #ccc;'>";
        echo "</div>";
    }
}

echo "<h2>Çözüm Önerileri:</h2>";
echo "<ol>";
echo "<li>Tarayıcıda Ctrl+F5 ile hard refresh yapın</li>";
echo "<li>Tarayıcı cache'ini temizleyin</li>";
echo "<li>Farklı bir tarayıcıda test edin</li>";
echo "<li>Incognito/Private modda test edin</li>";
echo "<li>Resim URL'lerini doğrudan tarayıcıda açın</li>";
echo "</ol>";

echo "<p><a href='portfolio.php'>Portfolio Sayfasına Git</a> | <a href='clear-cache.php'>Cache Temizle</a></p>";
?>
