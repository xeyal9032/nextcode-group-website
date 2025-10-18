<?php
// Test sayfası - Blog değişikliklerini kontrol et
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

echo "<h1>Blog Değişiklikleri Test Sayfası</h1>";
echo "<p>Son güncelleme: " . date('Y-m-d H:i:s') . "</p>";

// Blog-post.php dosyasından veri al
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 2;

// Fallback data'yı include et
$fallback_posts = [
    1 => [
        'id' => 1,
        'title' => 'SEO Optimizasiyası: Axtarış Nəticələrində Yüksəlmək',
        'featured_image' => 'images/blog/seo-optimization.jpg',
        'read_time' => '12',
        'author' => 'NextCode SEO Team'
    ],
    2 => [
        'id' => 2,
        'title' => 'Modern Web Development Trends 2024',
        'featured_image' => 'images/blog/web-development-trends.jpg',
        'read_time' => '15',
        'author' => 'NextCode Development Team'
    ],
    3 => [
        'id' => 3,
        'title' => 'E-commerce Development: Online Mağaza Yaratmaq',
        'featured_image' => 'images/blog/ecommerce-development.jpg',
        'read_time' => '18',
        'author' => 'NextCode E-commerce Team'
    ],
    4 => [
        'id' => 4,
        'title' => 'Cybersecurity: Web Təhlükəsizliyi və Best Practices',
        'featured_image' => 'images/blog/cybersecurity-guide.jpg',
        'read_time' => '20',
        'author' => 'NextCode Security Team'
    ]
];

$current_post = $fallback_posts[$post_id] ?? $fallback_posts[2];

echo "<h2>Test Edilen Makale:</h2>";
echo "<p><strong>Başlık:</strong> " . htmlspecialchars($current_post['title']) . "</p>";
echo "<p><strong>Fotoğraf:</strong> " . htmlspecialchars($current_post['featured_image']) . "</p>";
echo "<p><strong>Okuma Süresi:</strong> " . htmlspecialchars($current_post['read_time']) . " dakika</p>";
echo "<p><strong>Yazar:</strong> " . htmlspecialchars($current_post['author']) . "</p>";

echo "<h2>Fotoğraf Kontrolü:</h2>";
if (file_exists($current_post['featured_image'])) {
    echo "<p style='color: green;'>✓ Fotoğraf mevcut: " . $current_post['featured_image'] . "</p>";
    echo "<img src='" . $current_post['featured_image'] . "' alt='Test' style='max-width: 300px;'>";
} else {
    echo "<p style='color: red;'>✗ Fotoğraf bulunamadı: " . $current_post['featured_image'] . "</p>";
}

echo "<h2>Tüm Blog Makaleleri:</h2>";
foreach ($fallback_posts as $id => $post) {
    echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
    echo "<h3><a href='test-blog.php?id=$id'>" . htmlspecialchars($post['title']) . "</a></h3>";
    echo "<p>Fotoğraf: " . htmlspecialchars($post['featured_image']) . "</p>";
    echo "<p>Okuma Süresi: " . htmlspecialchars($post['read_time']) . " dakika</p>";
    echo "<p>Yazar: " . htmlspecialchars($post['author']) . "</p>";
    echo "</div>";
}

echo "<h2>Cache Temizleme:</h2>";
echo "<p><a href='clear-cache.php'>Cache Temizle</a></p>";
echo "<p><a href='blog.php'>Blog Sayfasına Git</a></p>";
echo "<p><a href='blog-post.php?id=2'>Blog Post Sayfasına Git</a></p>";
?>


