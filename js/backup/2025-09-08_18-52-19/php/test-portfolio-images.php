<?php
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

echo "<h1>Portfolio Resim Test</h1>";
echo "<p>Server Time: " . date('Y-m-d H:i:s') . "</p>";

$portfolio_images = [
    'images/portfolio/ecommerce-project.jpg',
    'images/portfolio/corporate-website.jpg',
    'images/portfolio/mobile-app.jpg',
    'images/portfolio/ai-chatbot.jpg',
    'images/portfolio/responsive-design.jpg'
];

echo "<h2>Resim Testleri:</h2>";
foreach ($portfolio_images as $image) {
    if (file_exists($image)) {
        $size = filesize($image);
        echo "<div style='margin: 20px; display: inline-block; text-align: center;'>";
        echo "<h4>" . basename($image) . "</h4>";
        echo "<p>Boyut: " . number_format($size) . " bytes</p>";
        echo "<img src='$image?v=" . time() . "' style='width: 300px; height: 200px; object-fit: cover; border: 2px solid #ccc; border-radius: 8px;'>";
        echo "<br><a href='$image' target='_blank'>Doğrudan Aç</a>";
        echo "</div>";
    } else {
        echo "<p style='color: red;'>✗ $image (BULUNAMADI)</p>";
    }
}

echo "<h2>Portfolio Sayfasına Git:</h2>";
echo "<p><a href='portfolio.php' target='_blank'>Portfolio Sayfasını Aç</a></p>";
echo "<p><a href='portfolio.php?nocache=" . time() . "' target='_blank'>Portfolio Sayfasını Cache Olmadan Aç</a></p>";
?>
