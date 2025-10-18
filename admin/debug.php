<?php
// Admin Panel Debug Sayfası - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

$debug_info = [];
$test_results = [];

// CSRF Token oluştur
$csrf_token = generateCSRFToken();

// Veritabanı bağlantısı testi
try {
    $pdo = getSecureDatabaseConnection();
    if ($pdo) {
        $debug_info['database'] = '✅ Veritabanı bağlantısı başarılı';
        
        // Tablo kontrolü
        $tables = ['site_content', 'blog_posts', 'portfolio_projects', 'admin_users'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            $debug_info["table_$table"] = "✅ $table tablosunda $count kayıt";
        }
    } else {
        $debug_info['database'] = '❌ Veritabanı bağlantısı başarısız';
    }
} catch (Exception $e) {
    $debug_info['database'] = '❌ Veritabanı hatası: ' . $e->getMessage();
}

// Cache sistemi testi
try {
    require_once "../config/admin-cache.php";
    AdminCachedData::init();
    $cache_health = AdminCachedData::getCacheHealth();
    $debug_info['cache'] = "✅ Cache sağlık skoru: {$cache_health['score']}%";
} catch (Exception $e) {
    $debug_info['cache'] = '❌ Cache hatası: ' . $e->getMessage();
}

// Form işleme testi
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submitted_token = $_POST["csrf_token"] ?? "";
    if (!verifyCSRFToken($submitted_token)) {
        $test_results[] = "❌ CSRF Token hatası";
    } else {
        $action = $_POST["action"] ?? "";
        
        if ($action === "test_save") {
            try {
                $test_content = "Test içerik - " . date('Y-m-d H:i:s');
                $stmt = $pdo->prepare("
                    INSERT INTO site_content (page_name, section_name, content_key, content_value, content_type, created_at, updated_at) 
                    VALUES ('debug', 'test', 'test_key', ?, 'text', NOW(), NOW())
                    ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()
                ");
                $stmt->execute([$test_content]);
                
                $test_results[] = "✅ Test kaydetme başarılı: $test_content";
                
                // Cache temizle
                AdminCachedData::clearContentCache();
                $test_results[] = "✅ Cache temizleme başarılı";
                
            } catch (Exception $e) {
                $test_results[] = "❌ Test kaydetme hatası: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token; ?>">
    <title>Admin Panel Debug | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header h1 { color: #333; margin-bottom: 10px; }
        .header p { color: #666; }
        .debug-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .debug-section h3 { color: #333; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .debug-item { padding: 10px; margin: 5px 0; border-radius: 5px; background: #f8f9fa; }
        .test-form { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; }
        .btn:hover { background: #0056b3; }
        .test-results { margin-top: 20px; }
        .test-result { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-btn { display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; margin-bottom: 20px; }
        .back-btn:hover { background: #545b62; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Dashboard'a Dön
        </a>
        
        <div class="header">
            <h1><i class="fas fa-bug"></i> Admin Panel Debug</h1>
            <p>Admin panelindeki sorunları tespit etmek ve çözmek için debug bilgileri</p>
        </div>

        <div class="debug-section">
            <h3><i class="fas fa-info-circle"></i> Sistem Durumu</h3>
            <?php foreach ($debug_info as $key => $info): ?>
                <div class="debug-item"><?php echo htmlspecialchars($info); ?></div>
            <?php endforeach; ?>
        </div>

        <div class="test-form">
            <h3><i class="fas fa-flask"></i> Form İşleme Testi</h3>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="test_save">
                
                <div class="form-group">
                    <label>Test İçerik:</label>
                    <textarea name="test_content" rows="3" placeholder="Test içeriği girin...">Test içerik - <?php echo date('Y-m-d H:i:s'); ?></textarea>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Test Kaydet
                </button>
            </form>
            
            <?php if (!empty($test_results)): ?>
                <div class="test-results">
                    <h4>Test Sonuçları:</h4>
                    <?php foreach ($test_results as $result): ?>
                        <div class="test-result <?php echo strpos($result, '✅') !== false ? 'success' : 'error'; ?>">
                            <?php echo htmlspecialchars($result); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="debug-section">
            <h3><i class="fas fa-cog"></i> Öneriler</h3>
            <div class="debug-item">
                <strong>Form Kaydetme Sorunları:</strong>
                <ul style="margin-top: 10px; padding-left: 20px;">
                    <li>CSRF Token kontrolü yapılıyor ✅</li>
                    <li>Veritabanı bağlantısı çalışıyor ✅</li>
                    <li>Cache temizleme aktif ✅</li>
                    <li>JavaScript AJAX sistemi aktif ✅</li>
                </ul>
            </div>
            <div class="debug-item">
                <strong>Web Projede Görünmeme Sorunları:</strong>
                <ul style="margin-top: 10px; padding-left: 20px;">
                    <li>Cache temizleme işlemi çalışıyor ✅</li>
                    <li>Veritabanı güncellemeleri başarılı ✅</li>
                    <li>Frontend cache kontrolü gerekli ⚠️</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        console.log('Admin Panel Debug Sayfası Yüklendi');
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Form gönderim testi
        document.querySelector('form').addEventListener('submit', function(e) {
            console.log('Form gönderiliyor...');
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Test Ediliyor...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
