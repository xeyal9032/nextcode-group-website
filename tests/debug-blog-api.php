<?php
// Blog API'sini debug et
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

echo "<h1>Blog API Debug</h1>";

// 1. Blog API dosyasının varlığını kontrol et
echo "<h2>1. API Dosya Kontrolü:</h2>";
$api_file = 'api/blog.php';
if (file_exists($api_file)) {
    echo "<p style='color: green;'>✓ Blog API dosyası mevcut: $api_file</p>";
} else {
    echo "<p style='color: red;'>✗ Blog API dosyası bulunamadı: $api_file</p>";
}

// 2. Veritabanı bağlantısını test et
echo "<h2>2. Veritabanı Bağlantı Testi:</h2>";
try {
    require_once 'config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        echo "<p style='color: green;'>✓ Veritabanı bağlantısı başarılı</p>";
        
        // Blog posts tablosunu kontrol et
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM blog_posts");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>📊 Toplam blog post sayısı: " . $result['count'] . "</p>";
        
        // ID 2'yi kontrol et
        $stmt = $pdo->prepare("SELECT id, title, author, read_time, featured_image FROM blog_posts WHERE id = 2");
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($post) {
            echo "<p style='color: green;'>✓ ID 2 blog post bulundu:</p>";
            echo "<ul>";
            echo "<li><strong>Başlık:</strong> " . htmlspecialchars($post['title']) . "</li>";
            echo "<li><strong>Yazar:</strong> " . htmlspecialchars($post['author'] ?? 'Yok') . "</li>";
            echo "<li><strong>Okuma Süresi:</strong> " . htmlspecialchars($post['read_time'] ?? 'Yok') . " dakika</li>";
            echo "<li><strong>Fotoğraf:</strong> " . htmlspecialchars($post['featured_image'] ?? 'Yok') . "</li>";
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>✗ ID 2 blog post bulunamadı</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Veritabanı bağlantısı başarısız</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Veritabanı hatası: " . $e->getMessage() . "</p>";
}

// 3. Blog API'sini test et
echo "<h2>3. Blog API Testi:</h2>";
if (file_exists($api_file)) {
    try {
        // API'yi include et ve test et
        ob_start();
        include $api_file;
        $api_output = ob_get_clean();
        
        if (!empty($api_output)) {
            echo "<p style='color: green;'>✓ Blog API çalışıyor</p>";
            echo "<details><summary>API Çıktısı:</summary><pre>" . htmlspecialchars($api_output) . "</pre></details>";
        } else {
            echo "<p style='color: orange;'>⚠ Blog API çıktı vermiyor</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Blog API hatası: " . $e->getMessage() . "</p>";
    }
}

// 4. Blog sayfasındaki fallback verilerini kontrol et
echo "<h2>4. Fallback Veriler Kontrolü:</h2>";
$blog_file = 'blog.php';
if (file_exists($blog_file)) {
    $content = file_get_contents($blog_file);
    
    // Fallback posts array'ini bul
    if (preg_match('/\$fallback_posts\s*=\s*\[(.*?)\];/s', $content, $matches)) {
        echo "<p style='color: green;'>✓ Fallback posts array bulundu</p>";
        
        // ID 2'yi fallback'te ara
        if (strpos($matches[1], "'id' => 2") !== false) {
            echo "<p style='color: green;'>✓ ID 2 fallback verilerinde mevcut</p>";
        } else {
            echo "<p style='color: red;'>✗ ID 2 fallback verilerinde bulunamadı</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Fallback posts array bulunamadı</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Blog.php dosyası bulunamadı</p>";
}

echo "<h2>🔧 Önerilen Çözümler:</h2>";
echo "<ol>";
echo "<li><strong>ID 2'yi test edin:</strong> <a href='blog-post.php?id=2' target='_blank'>blog-post.php?id=2</a></li>";
echo "<li><strong>Cache temizleyin:</strong> Tarayıcıda Ctrl+F5 yapın</li>";
echo "<li><strong>Blog API'sini kontrol edin:</strong> <a href='api/blog.php' target='_blank'>api/blog.php</a></li>";
echo "<li><strong>Veritabanı loglarını kontrol edin</strong></li>";
echo "</ol>";
?>


