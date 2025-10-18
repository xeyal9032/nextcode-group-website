<?php
// Blog API Test - Resim verilerini kontrol et

// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';

echo "<h2>Blog API Test</h2>";

// 1. API'yi test et
echo "<h3>1. Blog API Test (/api/blog.php)</h3>";
$api_url = 'http://localhost:8000/api/blog.php';
$response = file_get_contents($api_url);
$data = json_decode($response, true);

if ($data && $data['success']) {
    echo "<p style='color: green;'>✓ API başarılı</p>";
    echo "<p>Toplam blog yazısı: " . count($data['data']) . "</p>";
    
    echo "<h4>Blog yazıları ve resimleri:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Başlık</th><th>Resim Yolu</th><th>Dosya Var mı?</th></tr>";
    
    foreach ($data['data'] as $post) {
        $image_path = $post['featured_image'] ?? 'Yok';
        $file_exists = 'N/A';
        
        if ($image_path && $image_path !== 'Yok') {
            $full_path = __DIR__ . '/' . $image_path;
            $file_exists = file_exists($full_path) ? '✓ Var' : '✗ Yok';
        }
        
        echo "<tr>";
        echo "<td>" . $post['id'] . "</td>";
        echo "<td>" . htmlspecialchars($post['title']) . "</td>";
        echo "<td>" . htmlspecialchars($image_path) . "</td>";
        echo "<td>" . $file_exists . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>✗ API hatası</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}

// 2. Resim klasörünü kontrol et
echo "<h3>2. Resim Klasörü Kontrolü</h3>";
$image_dir = __DIR__ . '/assets/images/blog/';

if (is_dir($image_dir)) {
    echo "<p style='color: green;'>✓ assets/images/blog/ klasörü mevcut</p>";
    
    $files = scandir($image_dir);
    $image_files = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
    });
    
    if (count($image_files) > 0) {
        echo "<p>Bulunan resim dosyaları (" . count($image_files) . " adet):</p>";
        echo "<ul>";
        foreach ($image_files as $file) {
            $file_size = filesize($image_dir . $file);
            echo "<li>" . $file . " (" . round($file_size/1024, 2) . " KB)</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠ Klasörde resim dosyası bulunamadı</p>";
    }
} else {
    echo "<p style='color: red;'>✗ assets/images/blog/ klasörü bulunamadı</p>";
}

// 3. Veritabanından direkt kontrol
echo "<h3>3. Veritabanı Direkt Kontrolü</h3>";
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->query("SELECT id, title, featured_image FROM blog_posts WHERE status = 'published' ORDER BY id");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p style='color: green;'>✓ Veritabanı bağlantısı başarılı</p>";
    echo "<p>Veritabanındaki blog yazıları:</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Başlık</th><th>featured_image</th></tr>";
    
    foreach ($posts as $post) {
        echo "<tr>";
        echo "<td>" . $post['id'] . "</td>";
        echo "<td>" . htmlspecialchars($post['title']) . "</td>";
        echo "<td>" . htmlspecialchars($post['featured_image'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Veritabanı hatası: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Test tamamlandı.</strong> Yukarıdaki sonuçları kontrol ederek sorunun kaynağını belirleyebilirsiniz.</p>";
?>