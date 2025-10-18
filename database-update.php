<?php
/**
 * Veritabanı Güncelleme ve Test Scripti
 * NextCode Group - Database Update
 */

define('EMERGENCY_ACCESS', true);

echo "<h2>🗄️ Veritabanı Güncelleme ve Test</h2>";

try {
    // Database bağlantısı
    require_once 'config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        echo "✅ Database bağlantısı başarılı<br><br>";
        
        // Veritabanı bilgileri
        echo "<h3>📊 Veritabanı Bilgileri:</h3>";
        $stmt = $pdo->query("SELECT VERSION() as version");
        $version = $stmt->fetch();
        echo "MySQL Sürümü: " . $version['version'] . "<br>";
        
        $stmt = $pdo->query("SELECT DATABASE() as db_name");
        $db_name = $stmt->fetch();
        echo "Veritabanı Adı: " . $db_name['db_name'] . "<br><br>";
        
        // Tabloları kontrol et
        echo "<h3>📋 Tablo Durumu:</h3>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
        echo "<tr style='background: #f5f5f5;'><th style='padding: 10px;'>Tablo Adı</th><th style='padding: 10px;'>Kayıt Sayısı</th><th style='padding: 10px;'>Durum</th></tr>";
        
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
                $count = $stmt->fetch();
                echo "<tr>";
                echo "<td style='padding: 8px;'>$table</td>";
                echo "<td style='padding: 8px;'>" . $count['count'] . "</td>";
                echo "<td style='padding: 8px;'>✅ Aktif</td>";
                echo "</tr>";
            } catch (Exception $e) {
                echo "<tr>";
                echo "<td style='padding: 8px;'>$table</td>";
                echo "<td style='padding: 8px;'>-</td>";
                echo "<td style='padding: 8px;'>❌ Hata</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        
        // Admin kullanıcıları güncelle
        echo "<h3>👥 Admin Kullanıcıları Güncelleme:</h3>";
        
        // Kilitli kullanıcıları aç
        $stmt = $pdo->prepare("UPDATE admin_users SET locked_until = NULL, login_attempts = 0 WHERE locked_until IS NOT NULL");
        $stmt->execute();
        echo "✅ Kilitli kullanıcılar açıldı<br>";
        
        // Pasif kullanıcıları aktif et (admin hariç)
        $stmt = $pdo->prepare("UPDATE admin_users SET is_active = 1 WHERE username != 'admin'");
        $stmt->execute();
        echo "✅ Pasif kullanıcılar aktif edildi<br>";
        
        // Admin kullanıcıları listele
        echo "<h4>Güncel Admin Kullanıcıları:</h4>";
        $stmt = $pdo->query("SELECT id, username, full_name, email, role, is_active, login_attempts, locked_until FROM admin_users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
        echo "<tr style='background: #f5f5f5;'>";
        echo "<th style='padding: 8px;'>ID</th>";
        echo "<th style='padding: 8px;'>Kullanıcı Adı</th>";
        echo "<th style='padding: 8px;'>Ad</th>";
        echo "<th style='padding: 8px;'>E-posta</th>";
        echo "<th style='padding: 8px;'>Rol</th>";
        echo "<th style='padding: 8px;'>Aktif</th>";
        echo "<th style='padding: 8px;'>Giriş Denemeleri</th>";
        echo "<th style='padding: 8px;'>Kilitli</th>";
        echo "</tr>";
        
        foreach ($users as $user) {
            $active_status = $user['is_active'] ? '✅ Evet' : '❌ Hayır';
            $locked_status = $user['locked_until'] ? '🔒 Evet' : '✅ Hayır';
            
            echo "<tr>";
            echo "<td style='padding: 8px;'>" . $user['id'] . "</td>";
            echo "<td style='padding: 8px;'>" . $user['username'] . "</td>";
            echo "<td style='padding: 8px;'>" . $user['full_name'] . "</td>";
            echo "<td style='padding: 8px;'>" . $user['email'] . "</td>";
            echo "<td style='padding: 8px;'>" . $user['role'] . "</td>";
            echo "<td style='padding: 8px;'>" . $active_status . "</td>";
            echo "<td style='padding: 8px;'>" . $user['login_attempts'] . "</td>";
            echo "<td style='padding: 8px;'>" . $locked_status . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Site içeriklerini kontrol et
        echo "<h3>📝 Site İçerikleri Kontrolü:</h3>";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM site_content");
        $content_count = $stmt->fetch();
        echo "Toplam içerik sayısı: " . $content_count['count'] . "<br>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM blog_posts");
        $blog_count = $stmt->fetch();
        echo "Blog yazısı sayısı: " . $blog_count['count'] . "<br>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM portfolio_projects");
        $portfolio_count = $stmt->fetch();
        echo "Portfolio projesi sayısı: " . $portfolio_count['count'] . "<br>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages");
        $message_count = $stmt->fetch();
        echo "Gelen mesaj sayısı: " . $message_count['count'] . "<br><br>";
        
        // Cache tablosunu temizle
        echo "<h3>🗑️ Cache Temizleme:</h3>";
        try {
            $stmt = $pdo->query("DELETE FROM admin_cache WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
            $deleted = $stmt->rowCount();
            echo "✅ Eski cache kayıtları temizlendi: $deleted kayıt<br>";
        } catch (Exception $e) {
            echo "⚠️ Cache tablosu bulunamadı (normal)<br>";
        }
        
        echo "<h3>✅ Veritabanı Güncelleme Tamamlandı!</h3>";
        
    } else {
        echo "❌ Database bağlantısı başarısız<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Veritabanı hatası: " . $e->getMessage() . "<br>";
}

echo "<h3>🔗 Test Linkleri:</h3>";
echo "<p><a href='index.php' target='_blank' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Ana Sayfa Test</a></p>";
echo "<p><a href='admin/login.php' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Admin Login Test</a></p>";
echo "<p><a href='admin/comprehensive-test.php' target='_blank' style='background: #6f42c1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🧪 Kapsamlı Test</a></p>";

echo "<hr>";
echo "<p><strong>Veritabanı güncelleme tamamlandı! Şimdi test linklerini deneyin.</strong></p>";
?>
