<?php
/**
 * Acil Redirect Sorunu Çözümü
 * NextCode Group - ERR_TOO_MANY_REDIRECTS Fix
 */

define('EMERGENCY_ACCESS', true);

echo "<h2>🚨 Acil Redirect Sorunu Çözümü</h2>";

// Cache temizleme
$cache_dirs = [
    'cache/',
    'cache/admin/',
    'cache/api/',
    'cache/assets/',
    'cache/database/',
    'cache/pages/',
    'temp/',
    'logs/'
];

echo "<h3>1. Cache Temizleme</h3>";
foreach ($cache_dirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                if (unlink($file)) {
                    $count++;
                }
            }
        }
        echo "✅ $dir - $count dosya temizlendi<br>";
    }
}

// Session temizleme
echo "<h3>2. Session Temizleme</h3>";
session_start();
session_destroy();
session_regenerate_id(true);
echo "✅ Session temizlendi<br>";

// .htaccess yedekleme ve basitleştirme
echo "<h3>3. .htaccess Düzeltme</h3>";
if (file_exists('.htaccess')) {
    copy('.htaccess', '.htaccess.backup-' . date('Y-m-d-H-i-s'));
    echo "✅ .htaccess yedeklendi<br>";
    
    // Basit .htaccess oluştur
    $simple_htaccess = '# Emergency .htaccess - No redirects
RewriteEngine On

# Allow direct access to files
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^ - [L]

# Allow direct access to directories
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Allow assets
RewriteRule ^(images|css|js|assets|webfonts|uploads|cache|temp)/.*$ - [L]

# Error pages
ErrorDocument 404 /404.php
ErrorDocument 500 /500.php

# Security
Options -Indexes
<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|inc|bak)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
';
    
    file_put_contents('.htaccess', $simple_htaccess);
    echo "✅ Basit .htaccess oluşturuldu<br>";
}

// Database bağlantı testi
echo "<h3>4. Database Bağlantı Testi</h3>";
try {
    define('EMERGENCY_ACCESS', true);
    require_once 'config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        echo "✅ Database bağlantısı başarılı<br>";
        
        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
        $result = $stmt->fetch();
        echo "✅ Admin kullanıcı sayısı: " . $result['count'] . "<br>";
        
    } else {
        echo "❌ Database bağlantısı başarısız<br>";
    }
} catch (Exception $e) {
    echo "❌ Database hatası: " . $e->getMessage() . "<br>";
}

echo "<h3>5. Test Linkleri</h3>";
echo "<p><a href='index.php' target='_blank' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Ana Sayfa Test</a></p>";
echo "<p><a href='admin/login.php' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Admin Login Test</a></p>";

echo "<h3>6. Sonraki Adımlar</h3>";
echo "<ol>";
echo "<li>Ana sayfa linkini test edin</li>";
echo "<li>Admin login linkini test edin</li>";
echo "<li>Eğer çalışıyorsa, .htaccess'i geri yükleyin</li>";
echo "<li>Bu dosyayı silin</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>✅ Acil çözüm tamamlandı!</strong> Şimdi test linklerini deneyin.</p>";
?>
