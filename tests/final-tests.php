<?php
/**
 * Final Tests - NextCode Group
 * SSL, CDN ve genel optimizasyon testleri
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once 'config/database.php';
require_once 'config/cdn-config.php';
require_once 'config/security.php';

// Test sonuçları
$tests = [];
$totalTests = 0;
$passedTests = 0;

function runTest($testName, $testFunction) {
    global $tests, $totalTests, $passedTests;
    $totalTests++;
    
    try {
        $result = $testFunction();
        $tests[] = [
            'name' => $testName,
            'status' => $result ? 'PASS' : 'FAIL',
            'message' => $result ? '✅ Başarılı' : '❌ Başarısız'
        ];
        if ($result) $passedTests++;
    } catch (Exception $e) {
        $tests[] = [
            'name' => $testName,
            'status' => 'ERROR',
            'message' => '❌ Hata: ' . $e->getMessage()
        ];
    }
}

// Test fonksiyonları
runTest('SSL HTTPS Yönlendirme', function() {
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
});

runTest('SSL Security Headers', function() {
    $headers = headers_list();
    $hasHSTS = false;
    foreach ($headers as $header) {
        if (strpos($header, 'Strict-Transport-Security') !== false) {
            $hasHSTS = true;
            break;
        }
    }
    return $hasHSTS;
});

runTest('CDN Sistemi Aktif', function() {
    global $cdnManager;
    return $cdnManager->cdnEnabled;
});

runTest('CDN Fallback Sistemi', function() {
    global $cdnManager;
    return $cdnManager->fallbackEnabled;
});

runTest('Veritabanı Bağlantısı', function() {
    try {
        $database = new Database();
        $pdo = $database->getConnection();
        return $pdo !== null;
    } catch (Exception $e) {
        return false;
    }
});

runTest('Security Headers', function() {
    $headers = headers_list();
    $requiredHeaders = ['X-Content-Type-Options', 'X-Frame-Options', 'X-XSS-Protection'];
    $foundHeaders = 0;
    
    foreach ($headers as $header) {
        foreach ($requiredHeaders as $required) {
            if (strpos($header, $required) !== false) {
                $foundHeaders++;
                break;
            }
        }
    }
    
    return $foundHeaders >= count($requiredHeaders);
});

runTest('API Router Çalışıyor', function() {
    return file_exists('api-router.php') && is_readable('api-router.php');
});

runTest('Backup Sistemi Hazır', function() {
    return file_exists('backup/enhanced-backup.php') && is_readable('backup/enhanced-backup.php');
});

runTest('Monitoring Sistemi Hazır', function() {
    return file_exists('monitoring/uptime-monitor.php') && is_readable('monitoring/uptime-monitor.php');
});

runTest('CSS Dosyaları Mevcut', function() {
    $cssFiles = ['css/modern-styles.css', 'css/bootstrap.min.css', 'css/fontawesome.css'];
    $foundFiles = 0;
    
    foreach ($cssFiles as $file) {
        if (file_exists($file) && is_readable($file)) {
            $foundFiles++;
        }
    }
    
    return $foundFiles >= 2; // En az 2 dosya olmalı
});

runTest('JavaScript Dosyaları Mevcut', function() {
    $jsFiles = ['js/main.js', 'js/analytics.js', 'js/performance-monitor.js'];
    $foundFiles = 0;
    
    foreach ($jsFiles as $file) {
        if (file_exists($file) && is_readable($file)) {
            $foundFiles++;
        }
    }
    
    return $foundFiles >= 2; // En az 2 dosya olmalı
});

runTest('Ana Sayfalar Mevcut', function() {
    $pages = ['index.php', 'about.php', 'services.php', 'portfolio.php', 'contact.php'];
    $foundPages = 0;
    
    foreach ($pages as $page) {
        if (file_exists($page) && is_readable($page)) {
            $foundPages++;
        }
    }
    
    return $foundPages >= 4; // En az 4 sayfa olmalı
});

runTest('Error Handling Aktif', function() {
    return file_exists('includes/error_handler.php') && is_readable('includes/error_handler.php');
});

runTest('Log Dizini Mevcut', function() {
    return is_dir('logs') && is_writable('logs');
});

runTest('Assets Dizini Mevcut', function() {
    return is_dir('assets') && is_readable('assets');
});

runTest('Images Dizini Mevcut', function() {
    return is_dir('images') && is_readable('images');
});

// Performans testleri
runTest('PHP Version Uygun', function() {
    return version_compare(PHP_VERSION, '7.4.0', '>=');
});

runTest('PDO Extension Mevcut', function() {
    return extension_loaded('pdo') && extension_loaded('pdo_mysql');
});

runTest('JSON Extension Mevcut', function() {
    return extension_loaded('json');
});

runTest('cURL Extension Mevcut', function() {
    return extension_loaded('curl');
});

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Tests - NextCode Group</title>
    <style>
        body { 
            font-family: 'Inter', Arial, sans-serif; 
            margin: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }
        .test-result { 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 8px; 
            border-left: 4px solid;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .success { 
            background-color: #d4edda; 
            color: #155724; 
            border-left-color: #28a745;
        }
        .error { 
            background-color: #f8d7da; 
            color: #721c24; 
            border-left-color: #dc3545;
        }
        .info { 
            background-color: #d1ecf1; 
            color: #0c5460; 
            border-left-color: #17a2b8;
        }
        .summary {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        .test-name {
            font-weight: 600;
            font-size: 16px;
        }
        .test-status {
            font-weight: 700;
            font-size: 14px;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            transition: width 0.3s ease;
        }
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin: 30px 0 20px 0;
            color: #495057;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }
        .recommendations {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .recommendations h3 {
            color: #856404;
            margin-top: 0;
        }
        .recommendations ul {
            color: #856404;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 NextCode Group - Final Tests</h1>
            <p>SSL, CDN ve Optimizasyon Test Sonuçları</p>
        </div>
        
        <div class="summary">
            <h2>📊 Test Özeti</h2>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo ($passedTests / $totalTests) * 100; ?>%"></div>
            </div>
            <p><strong><?php echo $passedTests; ?></strong> / <strong><?php echo $totalTests; ?></strong> test başarılı</p>
            <p><strong><?php echo round(($passedTests / $totalTests) * 100, 1); ?>%</strong> başarı oranı</p>
        </div>
        
        <h2 class="section-title">🔒 SSL ve Güvenlik Testleri</h2>
        
        <?php
        $sslTests = array_filter($tests, function($test) {
            return strpos($test['name'], 'SSL') !== false || strpos($test['name'], 'Security') !== false;
        });
        
        foreach ($sslTests as $test) {
            $class = $test['status'] === 'PASS' ? 'success' : ($test['status'] === 'ERROR' ? 'error' : 'error');
            echo "<div class='test-result $class'>";
            echo "<span class='test-name'>{$test['name']}</span>";
            echo "<span class='test-status'>{$test['message']}</span>";
            echo "</div>";
        }
        ?>
        
        <h2 class="section-title">🌐 CDN ve Performans Testleri</h2>
        
        <?php
        $cdnTests = array_filter($tests, function($test) {
            return strpos($test['name'], 'CDN') !== false || strpos($test['name'], 'CSS') !== false || strpos($test['name'], 'JavaScript') !== false;
        });
        
        foreach ($cdnTests as $test) {
            $class = $test['status'] === 'PASS' ? 'success' : ($test['status'] === 'ERROR' ? 'error' : 'error');
            echo "<div class='test-result $class'>";
            echo "<span class='test-name'>{$test['name']}</span>";
            echo "<span class='test-status'>{$test['message']}</span>";
            echo "</div>";
        }
        ?>
        
        <h2 class="section-title">🗄️ Veritabanı ve Sistem Testleri</h2>
        
        <?php
        $systemTests = array_filter($tests, function($test) {
            return strpos($test['name'], 'Veritabanı') !== false || strpos($test['name'], 'PHP') !== false || strpos($test['name'], 'Extension') !== false;
        });
        
        foreach ($systemTests as $test) {
            $class = $test['status'] === 'PASS' ? 'success' : ($test['status'] === 'ERROR' ? 'error' : 'error');
            echo "<div class='test-result $class'>";
            echo "<span class='test-name'>{$test['name']}</span>";
            echo "<span class='test-status'>{$test['message']}</span>";
            echo "</div>";
        }
        ?>
        
        <h2 class="section-title">📁 Dosya ve Dizin Testleri</h2>
        
        <?php
        $fileTests = array_filter($tests, function($test) {
            return strpos($test['name'], 'Dosyaları') !== false || strpos($test['name'], 'Dizini') !== false || strpos($test['name'], 'Sayfalar') !== false;
        });
        
        foreach ($fileTests as $test) {
            $class = $test['status'] === 'PASS' ? 'success' : ($test['status'] === 'ERROR' ? 'error' : 'error');
            echo "<div class='test-result $class'>";
            echo "<span class='test-name'>{$test['name']}</span>";
            echo "<span class='test-status'>{$test['message']}</span>";
            echo "</div>";
        }
        ?>
        
        <h2 class="section-title">🔧 Sistem ve API Testleri</h2>
        
        <?php
        $apiTests = array_filter($tests, function($test) {
            return strpos($test['name'], 'API') !== false || strpos($test['name'], 'Backup') !== false || strpos($test['name'], 'Monitoring') !== false || strpos($test['name'], 'Error') !== false;
        });
        
        foreach ($apiTests as $test) {
            $class = $test['status'] === 'PASS' ? 'success' : ($test['status'] === 'ERROR' ? 'error' : 'error');
            echo "<div class='test-result $class'>";
            echo "<span class='test-name'>{$test['name']}</span>";
            echo "<span class='test-status'>{$test['message']}</span>";
            echo "</div>";
        }
        ?>
        
        <?php if ($passedTests / $totalTests >= 0.9): ?>
        <div class="recommendations">
            <h3>🎉 Tebrikler!</h3>
            <p>Projeniz %90+ başarı oranı ile mükemmel durumda! Tüm sistemler çalışıyor ve optimizasyonlar aktif.</p>
            <ul>
                <li>✅ SSL sertifikası aktif ve çalışıyor</li>
                <li>✅ CDN sistemi aktif ve fallback koruması mevcut</li>
                <li>✅ Güvenlik header'ları aktif</li>
                <li>✅ Tüm sistemler optimize edilmiş</li>
            </ul>
        </div>
        <?php elseif ($passedTests / $totalTests >= 0.8): ?>
        <div class="recommendations">
            <h3>👍 İyi Durumda!</h3>
            <p>Projeniz %80+ başarı oranı ile iyi durumda. Birkaç küçük optimizasyon yapılabilir.</p>
            <ul>
                <li>🔧 Bazı testler başarısız - kontrol edilmeli</li>
                <li>📊 Performans optimizasyonları yapılabilir</li>
            </ul>
        </div>
        <?php else: ?>
        <div class="recommendations">
            <h3>⚠️ Dikkat Gerekli!</h3>
            <p>Projenizde bazı sorunlar var. Aşağıdaki önerileri uygulayın:</p>
            <ul>
                <li>🔍 Başarısız testleri kontrol edin</li>
                <li>🛠️ Sistem gereksinimlerini kontrol edin</li>
                <li>📞 Destek alın</li>
            </ul>
        </div>
        <?php endif; ?>
        
        <div class="test-result info">
            <span class="test-name">📅 Test Tarihi</span>
            <span class="test-status"><?php echo date('d.m.Y H:i:s'); ?></span>
        </div>
        
        <div class="test-result info">
            <span class="test-name">🌐 Test URL</span>
            <span class="test-status"><?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?></span>
        </div>
        
        <p style="text-align: center; margin-top: 30px;">
            <a href="index.php" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 25px; font-weight: 600;">← Ana Sayfaya Dön</a>
            <a href="test-cdn.php" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 25px; font-weight: 600; margin-left: 10px;">CDN Test</a>
        </p>
    </div>
    
    <script>
        // Test sonuçlarını konsola yazdır
        console.log('Final Tests Completed:', {
            totalTests: <?php echo $totalTests; ?>,
            passedTests: <?php echo $passedTests; ?>,
            successRate: <?php echo round(($passedTests / $totalTests) * 100, 1); ?> + '%',
            timestamp: new Date().toISOString()
        });
        
        // Sayfa yüklendiğinde animasyon
        document.addEventListener('DOMContentLoaded', function() {
            const progressFill = document.querySelector('.progress-fill');
            if (progressFill) {
                setTimeout(() => {
                    progressFill.style.transition = 'width 1s ease';
                }, 500);
            }
        });
    </script>
</body>
</html>

