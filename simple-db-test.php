<?php
/**
 * Basit Database Test
 */

echo "<h2>🔍 Basit Database Test</h2>";

echo "<h3>1. Dosya Kontrolü:</h3>";
if (file_exists('config/database.php')) {
    echo "✅ config/database.php dosyası mevcut<br>";
} else {
    echo "❌ config/database.php dosyası bulunamadı<br>";
}

echo "<h3>2. Database Sınıfı Testi:</h3>";
try {
    require_once 'config/database.php';
    echo "✅ Database sınıfı yüklendi<br>";
    
    $db = new Database();
    echo "✅ Database nesnesi oluşturuldu<br>";
    
    $pdo = $db->getConnection();
    if ($pdo) {
        echo "✅ PDO bağlantısı başarılı<br>";
        
        // Basit test query
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "✅ Test query başarılı: " . $result['test'] . "<br>";
        
        // Admin kullanıcı sayısı
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
        $count = $stmt->fetch();
        echo "✅ Admin kullanıcı sayısı: " . $count['count'] . "<br>";
        
    } else {
        echo "❌ PDO bağlantısı başarısız<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "<br>";
    echo "Hata dosyası: " . $e->getFile() . " Satır: " . $e->getLine() . "<br>";
}

echo "<h3>3. PHP Bilgileri:</h3>";
echo "PHP Sürümü: " . PHP_VERSION . "<br>";
echo "PDO Mevcut: " . (extension_loaded('pdo') ? 'Evet' : 'Hayır') . "<br>";
echo "PDO MySQL Mevcut: " . (extension_loaded('pdo_mysql') ? 'Evet' : 'Hayır') . "<br>";

echo "<h3>4. Test Linkleri:</h3>";
echo "<p><a href='index.php' target='_blank'>🏠 Ana Sayfa</a></p>";
echo "<p><a href='admin/login.php' target='_blank'>🔐 Admin Login</a></p>";

echo "<hr>";
echo "<p><strong>Bu basit test tamamlandı.</strong></p>";
?>
