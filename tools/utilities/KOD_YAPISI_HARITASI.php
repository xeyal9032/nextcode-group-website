<?php
// NextCode Group - Kod Yapısı Haritası ve Analiz Aracı
// Bu dosya projenizin kod yapısını görselleştirir ve analiz eder

// Define secure access constant
define('SECURE_ACCESS', true);

// Include required files (optional - database connection)
try {
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
    }
} catch (Exception $e) {
    // Database connection failed, continue without it
    error_log('Database connection failed in KOD_YAPISI_HARITASI.php: ' . $e->getMessage());
}

// HTML başlığı
echo "<!DOCTYPE html>
<html lang='az'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Kod Yapısı Haritası - NextCode Group</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #e5e7eb; border-radius: 8px; }
        .file-tree { background: #f9fafb; padding: 15px; border-radius: 5px; font-family: monospace; }
        .file-item { margin: 5px 0; padding: 5px; }
        .file-php { color: #7c3aed; font-weight: bold; }
        .file-css { color: #059669; }
        .file-js { color: #dc2626; }
        .file-config { color: #ea580c; }
        .connection { background: #dbeafe; padding: 10px; margin: 10px 0; border-left: 4px solid #3b82f6; border-radius: 4px; }
        .error { background: #fef2f2; color: #dc2626; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .success { background: #f0fdf4; color: #059669; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: #f8fafc; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #e2e8f0; }
        .stat-number { font-size: 2em; font-weight: bold; color: #1e40af; }
        .stat-label { color: #64748b; margin-top: 5px; }
        .dependency-tree { background: #fefce8; padding: 15px; border-radius: 5px; border-left: 4px solid #eab308; }
        .search-box { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 5px; margin: 10px 0; }
        .highlight { background: yellow; padding: 2px 4px; border-radius: 3px; }
    </style>
</head>
<body>";

echo "<div class='container'>";

// Başlık
echo "<div class='header'>
    <h1>🏗️ Kod Yapısı Haritası</h1>
    <p>NextCode Group Web Projesi - Dosya Bağlantıları ve İlişkiler</p>
    <p><strong>Tarih:</strong> " . date('d.m.Y H:i:s') . "</p>
</div>";

// Arama kutusu
echo "<div class='section'>
    <h3>🔍 Dosya Arama</h3>
    <input type='text' class='search-box' id='searchInput' placeholder='Dosya adı veya içerik arayın...' onkeyup='searchFiles()'>
</div>";

// İstatistikler
echo "<div class='section'>
    <h3>📊 Proje İstatistikleri</h3>
    <div class='stats'>";

// Dosya sayılarını hesapla
$phpFiles = count(glob('*.php')) + count(glob('api/*.php')) + count(glob('includes/*.php')) + count(glob('config/*.php'));
$cssFiles = count(glob('css/*.css'));
$jsFiles = count(glob('js/*.js'));
$totalFiles = $phpFiles + $cssFiles + $jsFiles;

echo "<div class='stat-card'>
    <div class='stat-number'>$totalFiles</div>
    <div class='stat-label'>Toplam Dosya</div>
</div>";

echo "<div class='stat-card'>
    <div class='stat-number'>$phpFiles</div>
    <div class='stat-label'>PHP Dosyaları</div>
</div>";

echo "<div class='stat-card'>
    <div class='stat-number'>$cssFiles</div>
    <div class='stat-label'>CSS Dosyaları</div>
</div>";

echo "<div class='stat-card'>
    <div class='stat-number'>$jsFiles</div>
    <div class='stat-label'>JavaScript Dosyaları</div>
</div>";

echo "</div></div>";

// Ana dosya yapısı
echo "<div class='section'>
    <h3>📁 Ana Dosya Yapısı</h3>
    <div class='file-tree'>";

function displayFileTree($dir, $prefix = '') {
    $files = glob($dir . '/*');
    if (!$files) {
        return; // Dosya bulunamazsa çık
    }
    
    foreach ($files as $file) {
        if (is_dir($file)) {
            $name = basename($file);
            echo "<div class='file-item'>$prefix📁 $name/</div>";
            displayFileTree($file, $prefix . '&nbsp;&nbsp;');
        } else {
            $name = basename($file);
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $class = match($ext) {
                'php' => 'file-php',
                'css' => 'file-css',
                'js' => 'file-js',
                default => 'file-config'
            };
            echo "<div class='file-item $class'>$prefix📄 $name</div>";
        }
    }
}

displayFileTree('.');

echo "</div></div>";

// Kritik dosya bağlantıları
echo "<div class='section'>
    <h3>🔗 Kritik Dosya Bağlantıları</h3>";

$criticalConnections = [
    'Ana Sayfa' => [
        'index.php' => ['includes/header.php', 'includes/footer.php', 'css/styles.css', 'js/critical.js']
    ],
    'İletişim Sistemi' => [
        'contact.php' => ['contact-handler.php', 'api/contact.php', 'includes/EmailSender.php', 'config/email.php'],
        'contact-handler.php' => ['config/database.php', 'includes/EmailSender.php'],
        'api/contact.php' => ['config/database.php', 'includes/error_handler.php']
    ],
    'Tema Sistemi' => [
        'css/theme-variables.css' => ['js/theme.js', 'css/styles.css'],
        'js/theme.js' => ['css/readability-enhancements.css']
    ],
    'Veritabanı' => [
        'config/database.php' => ['Tüm PHP dosyaları']
    ],
    'Güvenlik' => [
        'config/security.php' => ['Tüm form işlemleri'],
        'includes/error_handler.php' => ['Tüm API dosyaları']
    ]
];

foreach ($criticalConnections as $category => $connections) {
    echo "<h4>$category</h4>";
    foreach ($connections as $file => $dependencies) {
        echo "<div class='connection'>
            <strong>$file</strong> → " . implode(', ', $dependencies) . "
        </div>";
    }
}

echo "</div>";

// Dosya bağımlılıkları analizi
echo "<div class='section'>
    <h3>🌐 Dosya Bağımlılıkları</h3>
    <div class='dependency-tree'>";

function analyzeDependencies($file) {
    if (!file_exists($file)) return [];
    
    $content = file_get_contents($file);
    $dependencies = [];
    
    // require/include dosyalarını bul
    preg_match_all('/(?:require|include)(?:_once)?\s+[\'"]([^\'"]+)[\'"]/', $content, $matches);
    if (!empty($matches[1])) {
        $dependencies = array_merge($dependencies, $matches[1]);
    }
    
    // CSS dosyalarını bul
    preg_match_all('/href\s*=\s*[\'"]([^\'"]*\.css[^\'"]*)[\'"]/', $content, $matches);
    if (!empty($matches[1])) {
        $dependencies = array_merge($dependencies, $matches[1]);
    }
    
    // JavaScript dosyalarını bul
    preg_match_all('/src\s*=\s*[\'"]([^\'"]*\.js[^\'"]*)[\'"]/', $content, $matches);
    if (!empty($matches[1])) {
        $dependencies = array_merge($dependencies, $matches[1]);
    }
    
    return array_unique($dependencies);
}

$mainFiles = ['index.php', 'contact.php', 'about.php', 'services.php', 'portfolio.php'];
foreach ($mainFiles as $file) {
    if (file_exists($file)) {
        $deps = analyzeDependencies($file);
        echo "<h4>$file</h4>";
        if (!empty($deps)) {
            echo "<ul>";
            foreach ($deps as $dep) {
                echo "<li>$dep</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Bağımlılık bulunamadı</p>";
        }
    }
}

echo "</div></div>";

// Sistem durumu kontrolü
echo "<div class='section'>
    <h3>🔍 Sistem Durumu Kontrolü</h3>";

// Kritik dosyaları kontrol et
$criticalFiles = [
    'config/database.php' => 'Veritabanı Konfigürasyonu',
    'config/email.php' => 'Email Konfigürasyonu',
    'config/security.php' => 'Güvenlik Konfigürasyonu',
    'includes/EmailSender.php' => 'Email Gönderim Sınıfı',
    'includes/error_handler.php' => 'Hata Yönetimi',
    'api/contact.php' => 'İletişim API',
    'contact-handler.php' => 'İletişim İşleyicisi'
];

foreach ($criticalFiles as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='success'>✅ $description ($file) - Mevcut</div>";
    } else {
        echo "<div class='error'>❌ $description ($file) - Bulunamadı</div>";
    }
}

// Veritabanı bağlantısını test et
try {
    if (isset($pdo) && $pdo) {
        $pdo->query('SELECT 1');
        echo "<div class='success'>✅ Veritabanı Bağlantısı - Aktif</div>";
    } else {
        echo "<div class='error'>❌ Veritabanı Bağlantısı - Pasif</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Veritabanı Bağlantısı - Hata: " . $e->getMessage() . "</div>";
}

echo "</div>";

// Hızlı erişim linkleri
echo "<div class='section'>
    <h3>🚀 Hızlı Erişim Linkleri</h3>
    <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px;'>";

$quickLinks = [
    'Ana Sayfa' => 'index.php',
    'İletişim' => 'contact.php',
    'Email Test' => 'test-email-system.php',
    'Tema Test' => 'test-theme-toggle.php',
    'Okunabilirlik Test' => 'test-readability.php',
    'Kapsamlı Test' => 'comprehensive-test.php'
];

foreach ($quickLinks as $name => $file) {
    if (file_exists($file)) {
        echo "<a href='$file' style='display: block; padding: 10px; background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 5px; text-decoration: none; color: #0369a1; text-align: center;'>$name</a>";
    }
}

echo "</div></div>";

// JavaScript arama fonksiyonu
echo "<script>
function searchFiles() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const fileItems = document.querySelectorAll('.file-item');
    
    fileItems.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(filter)) {
            item.style.display = 'block';
            if (filter) {
                item.innerHTML = item.innerHTML.replace(new RegExp(filter, 'gi'), '<span class=\"highlight\">$&</span>');
            }
        } else {
            item.style.display = 'none';
        }
    });
}
</script>";

echo "</div></body></html>";
?>
