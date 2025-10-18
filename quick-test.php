<?php
/**
 * Hızlı Test Sayfası - Database Erişimi
 */

define('EMERGENCY_ACCESS', true);

echo "<h2>🔍 Hızlı Database Test</h2>";

// Debug bilgileri
echo "<h3>Debug Bilgileri:</h3>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Yok') . "<br>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'Yok') . "<br>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Yok') . "<br>";

// Database test
echo "<h3>Database Test:</h3>";
try {
    // Geçici olarak güvenlik kontrolünü bypass et
    $old_htaccess = file_get_contents('.htaccess');
    
    // Database bağlantısını test et
    require_once 'config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        echo "✅ Database bağlantısı başarılı<br>";
        
        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
        $result = $stmt->fetch();
        echo "✅ Admin kullanıcı sayısı: " . $result['count'] . "<br>";
        
        // Admin kullanıcıları listele
        $stmt = $pdo->query("SELECT username, full_name, is_active FROM admin_users");
        $users = $stmt->fetchAll();
        
        echo "<h4>Admin Kullanıcıları:</h4>";
        echo "<ul>";
        foreach ($users as $user) {
            $status = $user['is_active'] ? '✅ Aktif' : '❌ Pasif';
            echo "<li>" . $user['username'] . " - " . $user['full_name'] . " - " . $status . "</li>";
        }
        echo "</ul>";
        
    } else {
        echo "❌ Database bağlantısı başarısız<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database hatası: " . $e->getMessage() . "<br>";
}

echo "<h3>Test Linkleri:</h3>";
echo "<p><a href='index.php' target='_blank'>🏠 Ana Sayfa</a></p>";
echo "<p><a href='admin/login.php' target='_blank'>🔐 Admin Login</a></p>";

echo "<hr>";
echo "<p><strong>Bu sayfa test amaçlıdır. Test tamamlandıktan sonra silinmelidir.</strong></p>";
?>
