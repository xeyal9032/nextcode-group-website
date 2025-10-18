<?php
// Blog veritabanı kontrolü
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Blog Veritabanı Kontrolü</h1>";

try {
    require_once 'config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        echo "<p style='color: red;'>✗ Veritabanı bağlantısı başarısız!</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✓ Veritabanı bağlantısı başarılı</p>";
    
    // Blog tablolarını kontrol et
    echo "<h2>Blog Tabloları:</h2>";
    
    $tables = ['blog_posts', 'blog_categories'];
    foreach ($tables as $table) {
        try {
            $checkTable = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($checkTable->rowCount() > 0) {
                echo "<p style='color: green;'>✓ $table tablosu mevcut</p>";
                
                // Tablo içeriğini kontrol et
                $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                echo "<p>   - Kayıt sayısı: $count</p>";
                
                if ($table === 'blog_posts' && $count > 0) {
                    // İlk birkaç kaydı göster
                    $stmt = $pdo->query("SELECT id, title, status FROM $table LIMIT 5");
                    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo "<p>   - Örnek kayıtlar:</p>";
                    echo "<ul>";
                    foreach ($posts as $post) {
                        echo "<li>ID: {$post['id']}, Başlık: {$post['title']}, Durum: {$post['status']}</li>";
                    }
                    echo "</ul>";
                }
            } else {
                echo "<p style='color: red;'>✗ $table tablosu bulunamadı</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ $table tablosu kontrolü başarısız: " . $e->getMessage() . "</p>";
        }
    }
    
    // Blog API'sini kontrol et
    echo "<h2>Blog API Kontrolü:</h2>";
    if (file_exists('api/blog.php')) {
        echo "<p style='color: green;'>✓ Blog API dosyası mevcut</p>";
        
        // API'yi test et
        $api_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/api/blog.php';
        echo "<p>API URL: <a href='$api_url' target='_blank'>$api_url</a></p>";
    } else {
        echo "<p style='color: red;'>✗ Blog API dosyası bulunamadı</p>";
    }
    
    // Fallback verilerini kontrol et
    echo "<h2>Fallback Veriler:</h2>";
    echo "<p>Blog-post.php dosyasında fallback veriler mevcut mu?</p>";
    
    $fallback_content = file_get_contents('blog-post.php');
    if (strpos($fallback_content, 'fallback_posts') !== false) {
        echo "<p style='color: green;'>✓ Fallback veriler mevcut</p>";
        
        // Fallback verilerden örnek al
        preg_match('/fallback_posts\s*=\s*\[(.*?)\];/s', $fallback_content, $matches);
        if (isset($matches[1])) {
            echo "<p>Fallback veriler bulundu ve güncellenmiş görünüyor.</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Fallback veriler bulunamadı</p>";
    }
    
    echo "<h2>Çözüm Önerileri:</h2>";
    echo "<ol>";
    echo "<li>Eğer blog tabloları yoksa: <a href='database/create_tables.sql'>create_tables.sql</a> çalıştırın</li>";
    echo "<li>Eğer tablolar boşsa: Blog verilerini ekleyin</li>";
    echo "<li>Fallback verileri kullanmak için: Veritabanı bağlantısını geçici olarak devre dışı bırakın</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Hata: " . $e->getMessage() . "</p>";
}
?>


