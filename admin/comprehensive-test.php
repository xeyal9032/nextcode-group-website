<?php
/**
 * Kapsamlı Admin Panel Test Sayfası
 * NextCode Group - Tüm Özellikler Test
 */

if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

session_start();

// Admin giriş kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$test_results = [];
$overall_status = "success";

// Test fonksiyonu
function runTest($test_name, $test_function) {
    global $test_results, $overall_status;
    
    try {
        $result = $test_function();
        $test_results[] = [
            'name' => $test_name,
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Başarılı' : 'Başarısız'
        ];
        
        if (!$result) {
            $overall_status = "error";
        }
    } catch (Exception $e) {
        $test_results[] = [
            'name' => $test_name,
            'status' => 'error',
            'message' => 'Hata: ' . $e->getMessage()
        ];
        $overall_status = "error";
    }
}

// Test 1: Database Bağlantısı
runTest("Database Bağlantısı", function() {
    try {
        require_once '../config/database.php';
        $db = new Database();
        $pdo = $db->getConnection();
        return $pdo !== false;
    } catch (Exception $e) {
        return false;
    }
});

// Test 2: Admin Cache Sistemi
runTest("Admin Cache Sistemi", function() {
    try {
        require_once '../config/admin-cache.php';
        AdminCachedData::init();
        return true;
    } catch (Exception $e) {
        return false;
    }
});

// Test 3: Dashboard İstatistikleri
runTest("Dashboard İstatistikleri", function() {
    try {
        require_once '../config/admin-cache.php';
        $stats = AdminCachedData::getDashboardStats();
        return is_array($stats) && count($stats) > 0;
    } catch (Exception $e) {
        return false;
    }
});

// Test 4: Blog Kategorileri
runTest("Blog Kategorileri", function() {
    try {
        require_once '../config/admin-cache.php';
        $categories = AdminCachedData::getBlogCategories();
        return is_array($categories);
    } catch (Exception $e) {
        return false;
    }
});

// Test 5: Site İçerikleri
runTest("Site İçerikleri", function() {
    try {
        require_once '../config/admin-cache.php';
        $content = AdminCachedData::getSiteContent('home');
        return is_array($content);
    } catch (Exception $e) {
        return false;
    }
});

// Test 6: Dosya İzinleri
runTest("Dosya İzinleri", function() {
    $required_files = [
        'index.php',
        'login.php',
        'includes/security.php',
        '../config/database.php',
        '../config/admin-cache.php'
    ];
    
    foreach ($required_files as $file) {
        if (!file_exists($file) || !is_readable($file)) {
            return false;
        }
    }
    return true;
});

// Test 7: Cache Klasörleri
runTest("Cache Klasörleri", function() {
    $cache_dirs = [
        '../cache/',
        '../cache/admin/',
        '../cache/pages/',
        '../cache/api/',
        '../cache/assets/'
    ];
    
    foreach ($cache_dirs as $dir) {
        if (!is_dir($dir)) {
            return false;
        }
    }
    return true;
});

// Test 8: Session Güvenliği
runTest("Session Güvenliği", function() {
    return isset($_SESSION['admin_logged_in']) && 
           isset($_SESSION['admin_user_id']) && 
           isset($_SESSION['admin_username']);
});

// Test 9: PHP Extensions
runTest("PHP Extensions", function() {
    $required_extensions = ['pdo', 'pdo_mysql', 'json', 'curl'];
    
    foreach ($required_extensions as $ext) {
        if (!extension_loaded($ext)) {
            return false;
        }
    }
    return true;
});

// Test 10: Memory Limit
runTest("Memory Limit", function() {
    $memory_limit = ini_get('memory_limit');
    $memory_bytes = parseSize($memory_limit);
    return $memory_bytes >= 128 * 1024 * 1024; // 128MB
});

function parseSize($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

// Test 11: Upload Dizinleri
runTest("Upload Dizinleri", function() {
    $upload_dirs = [
        '../uploads/',
        '../uploads/images/',
        '../uploads/documents/',
        '../uploads/temp/'
    ];
    
    foreach ($upload_dirs as $dir) {
        if (!is_dir($dir)) {
            return false;
        }
    }
    return true;
});

// Test 12: .htaccess
runTest(".htaccess Dosyası", function() {
    return file_exists('../.htaccess') && is_readable('../.htaccess');
});

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kapsamlı Admin Panel Test | NextCode</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .test-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #ddd;
        }
        .test-card.success {
            border-left-color: #28a745;
        }
        .test-card.error {
            border-left-color: #dc3545;
        }
        .test-card h3 {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        .test-card .icon {
            margin-right: 10px;
            font-size: 1.3em;
        }
        .test-card.success .icon {
            color: #28a745;
        }
        .test-card.error .icon {
            color: #dc3545;
        }
        .overall-status {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .overall-status.success {
            border: 2px solid #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        .overall-status.error {
            border: 2px solid #dc3545;
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        }
        .status-icon {
            font-size: 4em;
            margin-bottom: 15px;
        }
        .status-icon.success {
            color: #28a745;
        }
        .status-icon.error {
            color: #dc3545;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-card i {
            font-size: 3em;
            color: #667eea;
            margin-bottom: 15px;
        }
        .feature-card h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .feature-card p {
            color: #666;
            margin-bottom: 20px;
        }
        .feature-card a {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .feature-card a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .stat-card .number {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .stat-card .label {
            color: #666;
            font-size: 1.1em;
        }
        .refresh-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 1.1em;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 20px auto;
            display: block;
        }
        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-flask"></i> Kapsamlı Admin Panel Test</h1>
            <p>NextCode Group - Tüm Özellikler ve Sistem Kontrolü</p>
        </div>
        
        <div class="content">
            <!-- Genel Durum -->
            <div class="overall-status <?php echo $overall_status; ?>">
                <div class="status-icon <?php echo $overall_status; ?>">
                    <i class="fas <?php echo $overall_status === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                </div>
                <h2>Sistem Durumu: <?php echo $overall_status === 'success' ? 'BAŞARILI' : 'SORUN VAR'; ?></h2>
                <p><?php echo $overall_status === 'success' ? 'Tüm testler başarıyla geçti!' : 'Bazı testlerde sorun tespit edildi.'; ?></p>
            </div>
            
            <!-- Test Sonuçları -->
            <h2><i class="fas fa-list-check"></i> Test Sonuçları</h2>
            <div class="test-grid">
                <?php foreach ($test_results as $test): ?>
                <div class="test-card <?php echo $test['status']; ?>">
                    <h3>
                        <i class="fas <?php echo $test['status'] === 'success' ? 'fa-check-circle' : 'fa-times-circle'; ?> icon"></i>
                        <?php echo htmlspecialchars($test['name']); ?>
                    </h3>
                    <p><?php echo htmlspecialchars($test['message']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- İstatistikler -->
            <h2><i class="fas fa-chart-bar"></i> Sistem İstatistikleri</h2>
            <div class="stats-grid">
                <?php
                try {
                    require_once '../config/admin-cache.php';
                    $stats = AdminCachedData::getDashboardStats();
                    
                    if ($stats) {
                        echo "<div class='stat-card'>";
                        echo "<div class='number'>" . ($stats['total_projects'] ?? 0) . "</div>";
                        echo "<div class='label'>Portfolio Projeleri</div>";
                        echo "</div>";
                        
                        echo "<div class='stat-card'>";
                        echo "<div class='number'>" . ($stats['total_posts'] ?? 0) . "</div>";
                        echo "<div class='label'>Blog Yazıları</div>";
                        echo "</div>";
                        
                        echo "<div class='stat-card'>";
                        echo "<div class='number'>" . ($stats['total_messages'] ?? 0) . "</div>";
                        echo "<div class='label'>Gelen Mesajlar</div>";
                        echo "</div>";
                        
                        echo "<div class='stat-card'>";
                        echo "<div class='number'>" . ($stats['total_users'] ?? 0) . "</div>";
                        echo "<div class='label'>Admin Kullanıcıları</div>";
                        echo "</div>";
                    }
                } catch (Exception $e) {
                    echo "<div class='stat-card'>";
                    echo "<div class='number'>-</div>";
                    echo "<div class='label'>İstatistikler Yüklenemedi</div>";
                    echo "</div>";
                }
                ?>
            </div>
            
            <!-- Admin Panel Özellikleri -->
            <h2><i class="fas fa-cogs"></i> Admin Panel Özellikleri</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fas fa-tachometer-alt"></i>
                    <h3>Dashboard</h3>
                    <p>Genel istatistikler ve hızlı erişim menüleri</p>
                    <a href="index.php" target="_blank">Dashboard'a Git</a>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-briefcase"></i>
                    <h3>Portfolio Yönetimi</h3>
                    <p>Projeler, kategoriler ve görseller</p>
                    <a href="portfolio.php" target="_blank">Portfolio'yu Yönet</a>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-blog"></i>
                    <h3>Blog Yönetimi</h3>
                    <p>Yazılar, kategoriler ve yorumlar</p>
                    <a href="blog.php" target="_blank">Blog'u Yönet</a>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Mesaj Yönetimi</h3>
                    <p>Gelen mesajlar ve iletişim formları</p>
                    <a href="messages.php" target="_blank">Mesajları Görüntüle</a>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-edit"></i>
                    <h3>İçerik Yönetimi</h3>
                    <p>Sayfa içerikleri ve düzenlemeler</p>
                    <a href="content.php" target="_blank">İçerikleri Düzenle</a>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-images"></i>
                    <h3>Medya Yönetimi</h3>
                    <p>Resimler, dosyalar ve medya kütüphanesi</p>
                    <a href="media.php" target="_blank">Medyayı Yönet</a>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-users"></i>
                    <h3>Kullanıcı Yönetimi</h3>
                    <p>Admin kullanıcıları ve roller</p>
                    <a href="user-roles.php" target="_blank">Kullanıcıları Yönet</a>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Güvenlik</h3>
                    <p>Güvenlik ayarları ve audit logları</p>
                    <a href="audit-logs.php" target="_blank">Güvenlik Logları</a>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-database"></i>
                    <h3>Cache Yönetimi</h3>
                    <p>Sistem cache'ini temizleme ve yönetim</p>
                    <a href="cache-management.php" target="_blank">Cache'i Yönet</a>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-user-circle"></i>
                    <h3>Profil Yönetimi</h3>
                    <p>Kişisel bilgiler ve şifre değişikliği</p>
                    <a href="profile.php" target="_blank">Profili Düzenle</a>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-search"></i>
                    <h3>İçerik Tarayıcı</h3>
                    <p>Web sitesi içeriklerini tarama ve analiz</p>
                    <a href="web-content-scanner.php" target="_blank">İçerikleri Tara</a>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-sign-out-alt"></i>
                    <h3>Çıkış</h3>
                    <p>Güvenli çıkış işlemi</p>
                    <a href="logout.php">Çıkış Yap</a>
                </div>
            </div>
            
            <button class="refresh-btn" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i> Testleri Yenile
            </button>
            
            <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <h3><i class="fas fa-info-circle"></i> Test Bilgileri</h3>
                <p><strong>Test Zamanı:</strong> <?php echo date('d.m.Y H:i:s'); ?></p>
                <p><strong>Kullanıcı:</strong> <?php echo $_SESSION['admin_username'] ?? 'Bilinmiyor'; ?></p>
                <p><strong>PHP Sürümü:</strong> <?php echo PHP_VERSION; ?></p>
                <p><strong>Sunucu:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor'; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
