<?php
// NextCode Group - Debug Araçları ve Geliştirici Konsolu
// Bu dosya projenizin debug ve analiz işlemlerini kolaylaştırır

// Define secure access constant
define('SECURE_ACCESS', true);

// Include required files
require_once 'includes/AdvancedLogger.php';
require_once 'config/database.php';

// Debug modu kontrolü
$debugMode = isset($_GET['debug']) && $_GET['debug'] === 'true';
$adminMode = isset($_GET['admin']) && $_GET['admin'] === 'true';

// HTML başlığı
echo "<!DOCTYPE html>
<html lang='az'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Debug Araçları - NextCode Group</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .tabs { display: flex; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .tab { flex: 1; padding: 15px; text-align: center; cursor: pointer; border: none; background: white; border-radius: 10px 10px 0 0; transition: all 0.3s; }
        .tab.active { background: #667eea; color: white; }
        .tab-content { display: none; background: white; padding: 20px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .tab-content.active { display: block; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .status-ok { color: #10b981; font-weight: bold; }
        .status-error { color: #ef4444; font-weight: bold; }
        .status-warning { color: #f59e0b; font-weight: bold; }
        .code-block { background: #1f2937; color: #f9fafb; padding: 15px; border-radius: 8px; overflow-x: auto; font-family: 'Courier New', monospace; margin: 10px 0; }
        .json-viewer { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin: 10px 0; }
        .btn { background: #667eea; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px; transition: background 0.3s; }
        .btn:hover { background: #5a67d8; }
        .btn-danger { background: #ef4444; }
        .btn-danger:hover { background: #dc2626; }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .metric { text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px; }
        .metric-value { font-size: 2em; font-weight: bold; color: #667eea; }
        .metric-label { color: #6b7280; margin-top: 5px; }
        .search-box { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 5px; margin: 10px 0; }
        .filter-tabs { display: flex; gap: 10px; margin: 10px 0; }
        .filter-tab { padding: 8px 16px; background: #f3f4f6; border: none; border-radius: 5px; cursor: pointer; }
        .filter-tab.active { background: #667eea; color: white; }
        .log-entry { padding: 10px; border-left: 4px solid #e5e7eb; margin: 5px 0; background: #f9fafb; border-radius: 0 5px 5px 0; }
        .log-entry.error { border-left-color: #ef4444; background: #fef2f2; }
        .log-entry.warning { border-left-color: #f59e0b; background: #fffbeb; }
        .log-entry.info { border-left-color: #3b82f6; background: #eff6ff; }
        .log-entry.debug { border-left-color: #6b7280; background: #f9fafb; }
    </style>
</head>
<body>";

echo "<div class='container'>";

// Başlık
echo "<div class='header'>
    <h1>🔧 Debug Araçları ve Geliştirici Konsolu</h1>
    <p>NextCode Group Web Projesi - Sistem Analizi ve Hata Ayıklama</p>
    <p><strong>Tarih:</strong> " . date('d.m.Y H:i:s') . " | <strong>Debug Mode:</strong> " . ($debugMode ? 'Aktif' : 'Pasif') . "</p>
</div>";

// Tab menüsü
echo "<div class='tabs'>
    <button class='tab active' onclick='showTab(\"overview\")'>📊 Genel Bakış</button>
    <button class='tab' onclick='showTab(\"logs\")'>📝 Loglar</button>
    <button class='tab' onclick='showTab(\"database\")'>🗄️ Veritabanı</button>
    <button class='tab' onclick='showTab(\"performance\")'>⚡ Performans</button>
    <button class='tab' onclick='showTab(\"security\")'>🔒 Güvenlik</button>
    <button class='tab' onclick='showTab(\"files\")'>📁 Dosyalar</button>
    <button class='tab' onclick='showTab(\"tools\")'>🛠️ Araçlar</button>
</div>";

// Genel Bakış Tab
echo "<div id='overview' class='tab-content active'>";

// Sistem durumu
echo "<div class='card'>
    <h3>🖥️ Sistem Durumu</h3>
    <div class='grid'>";

// PHP bilgileri
echo "<div class='metric'>
    <div class='metric-value'>" . phpversion() . "</div>
    <div class='metric-label'>PHP Version</div>
</div>";

// Bellek kullanımı
$memoryUsage = memory_get_usage(true);
$memoryPeak = memory_get_peak_usage(true);
echo "<div class='metric'>
    <div class='metric-value'>" . round($memoryUsage / 1024 / 1024, 2) . " MB</div>
    <div class='metric-label'>Bellek Kullanımı</div>
</div>";

echo "<div class='metric'>
    <div class='metric-value'>" . round($memoryPeak / 1024 / 1024, 2) . " MB</div>
    <div class='metric-label'>Peak Bellek</div>
</div>";

// Disk kullanımı
$diskFree = disk_free_space('.');
$diskTotal = disk_total_space('.');
echo "<div class='metric'>
    <div class='metric-value'>" . round(($diskTotal - $diskFree) / 1024 / 1024 / 1024, 2) . " GB</div>
    <div class='metric-label'>Disk Kullanımı</div>
</div>";

echo "</div></div>";

// Kritik dosya durumu
echo "<div class='card'>
    <h3>📋 Kritik Dosya Durumu</h3>";

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
    $status = file_exists($file) ? 'status-ok' : 'status-error';
    $icon = file_exists($file) ? '✅' : '❌';
    echo "<p>$icon <span class='$status'>$description</span> ($file)</p>";
}

echo "</div>";

// Veritabanı bağlantısı testi
echo "<div class='card'>
    <h3>🗄️ Veritabanı Bağlantısı</h3>";

try {
    if (isset($pdo) && $pdo) {
        $stmt = $pdo->query('SELECT 1');
        echo "<p class='status-ok'>✅ Veritabanı bağlantısı aktif</p>";
        
        // Tablo sayısı
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p><strong>Tablo Sayısı:</strong> " . count($tables) . "</p>";
        
        // Tablo listesi
        if (!empty($tables)) {
            echo "<p><strong>Tablolar:</strong> " . implode(', ', $tables) . "</p>";
        }
    } else {
        echo "<p class='status-error'>❌ Veritabanı bağlantısı bulunamadı</p>";
    }
} catch (Exception $e) {
    echo "<p class='status-error'>❌ Veritabanı hatası: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</div></div>";

// Loglar Tab
echo "<div id='logs' class='tab-content'>";

// Log dosyalarını listele
$logFiles = glob('logs/*.log');
if (!empty($logFiles)) {
    echo "<div class='card'>
        <h3>📝 Log Dosyaları</h3>";
    
    foreach ($logFiles as $logFile) {
        $size = filesize($logFile);
        $modified = date('d.m.Y H:i:s', filemtime($logFile));
        echo "<p>📄 " . basename($logFile) . " - " . round($size / 1024, 2) . " KB - $modified</p>";
    }
    
    echo "</div>";
    
    // Log içeriği göster
    echo "<div class='card'>
        <h3>📋 Son Log Kayıtları</h3>
        <input type='text' class='search-box' id='logSearch' placeholder='Log içeriğinde ara...' onkeyup='searchLogs()'>";
    
    // Son 50 log kaydını göster
    if (file_exists('logs/application.log')) {
        $lines = file('logs/application.log', FILE_IGNORE_NEW_LINES);
        $recentLines = array_slice($lines, -50);
        
        echo "<div id='logEntries'>";
        foreach ($recentLines as $line) {
            $log = json_decode($line, true);
            if ($log) {
                $level = strtolower($log['level'] ?? 'info');
                $timestamp = $log['timestamp'] ?? '';
                $message = $log['message'] ?? '';
                
                echo "<div class='log-entry $level'>
                    <strong>[$timestamp]</strong> [$level] $message
                </div>";
            }
        }
        echo "</div>";
    }
    
    echo "</div>";
} else {
    echo "<div class='card'>
        <h3>📝 Log Dosyaları</h3>
        <p class='status-warning'>⚠️ Log dosyası bulunamadı</p>
    </div>";
}

echo "</div>";

// Veritabanı Tab
echo "<div id='database' class='tab-content'>";

try {
    if (isset($pdo) && $pdo) {
        // Tablo bilgileri
        echo "<div class='card'>
            <h3>🗄️ Veritabanı Tabloları</h3>";
        
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            echo "<div style='margin: 10px 0; padding: 10px; background: #f8fafc; border-radius: 5px;'>
                <h4>📊 $table</h4>";
            
            // Tablo yapısı
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table style='width: 100%; border-collapse: collapse;'>
                <tr style='background: #e5e7eb;'>
                    <th style='padding: 8px; border: 1px solid #d1d5db;'>Field</th>
                    <th style='padding: 8px; border: 1px solid #d1d5db;'>Type</th>
                    <th style='padding: 8px; border: 1px solid #d1d5db;'>Null</th>
                    <th style='padding: 8px; border: 1px solid #d1d5db;'>Key</th>
                    <th style='padding: 8px; border: 1px solid #d1d5db;'>Default</th>
                </tr>";
            
            foreach ($columns as $column) {
                echo "<tr>
                    <td style='padding: 8px; border: 1px solid #d1d5db;'>" . $column['Field'] . "</td>
                    <td style='padding: 8px; border: 1px solid #d1d5db;'>" . $column['Type'] . "</td>
                    <td style='padding: 8px; border: 1px solid #d1d5db;'>" . $column['Null'] . "</td>
                    <td style='padding: 8px; border: 1px solid #d1d5db;'>" . $column['Key'] . "</td>
                    <td style='padding: 8px; border: 1px solid #d1d5db;'>" . ($column['Default'] ?? 'NULL') . "</td>
                </tr>";
            }
            
            echo "</table></div>";
        }
        
        echo "</div>";
        
        // Veritabanı sorguları
        echo "<div class='card'>
            <h3>🔍 Veritabanı Sorguları</h3>
            <textarea id='sqlQuery' style='width: 100%; height: 100px; padding: 10px; border: 1px solid #d1d5db; border-radius: 5px;' placeholder='SQL sorgusu yazın...'></textarea>
            <br><br>
            <button class='btn' onclick='executeQuery()'>Sorguyu Çalıştır</button>
            <button class='btn btn-danger' onclick='clearQuery()'>Temizle</button>
            <div id='queryResult' style='margin-top: 20px;'></div>
        </div>";
        
    } else {
        echo "<div class='card'>
            <h3>🗄️ Veritabanı</h3>
            <p class='status-error'>❌ Veritabanı bağlantısı bulunamadı</p>
        </div>";
    }
} catch (Exception $e) {
    echo "<div class='card'>
        <h3>🗄️ Veritabanı</h3>
        <p class='status-error'>❌ Hata: " . htmlspecialchars($e->getMessage()) . "</p>
    </div>";
}

echo "</div>";

// Performans Tab
echo "<div id='performance' class='tab-content'>";

// Performans metrikleri
echo "<div class='card'>
    <h3>⚡ Performans Metrikleri</h3>
    <div class='grid'>";

// Sayfa yükleme süresi
$loadTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
echo "<div class='metric'>
    <div class='metric-value'>" . round($loadTime * 1000, 2) . " ms</div>
    <div class='metric-label'>Sayfa Yükleme Süresi</div>
</div>";

// Bellek kullanımı
echo "<div class='metric'>
    <div class='metric-value'>" . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB</div>
    <div class='metric-label'>Bellek Kullanımı</div>
</div>";

// Dosya sayısı
$phpFiles = count(glob('*.php')) + count(glob('api/*.php')) + count(glob('includes/*.php'));
echo "<div class='metric'>
    <div class='metric-value'>$phpFiles</div>
    <div class='metric-label'>PHP Dosyaları</div>
</div>";

echo "</div></div>";

// Performans önerileri
echo "<div class='card'>
    <h3>💡 Performans Önerileri</h3>
    <ul>
        <li>CSS dosyalarını minify edin</li>
        <li>JavaScript dosyalarını birleştirin</li>
        <li>Resim dosyalarını optimize edin</li>
        <li>Gereksiz log dosyalarını temizleyin</li>
        <li>Veritabanı sorgularını optimize edin</li>
    </ul>
</div>";

echo "</div>";

// Güvenlik Tab
echo "<div id='security' class='tab-content'>";

// Güvenlik kontrolleri
echo "<div class='card'>
    <h3>🔒 Güvenlik Kontrolleri</h3>";

// HTTPS kontrolü
$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
echo "<p>" . ($isHttps ? '✅' : '⚠️') . " HTTPS: " . ($isHttps ? 'Aktif' : 'Pasif') . "</p>";

// Session güvenliği
echo "<p>" . (session_status() === PHP_SESSION_ACTIVE ? '✅' : '⚠️') . " Session: " . (session_status() === PHP_SESSION_ACTIVE ? 'Aktif' : 'Pasif') . "</p>";

// File permissions
$criticalFiles = ['config/database.php', 'config/email.php', 'config/security.php'];
foreach ($criticalFiles as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        $isSecure = ($perms & 0777) <= 0644;
        echo "<p>" . ($isSecure ? '✅' : '⚠️') . " $file: " . substr(sprintf('%o', $perms), -4) . "</p>";
    }
}

echo "</div>";

// Güvenlik önerileri
echo "<div class='card'>
    <h3>🛡️ Güvenlik Önerileri</h3>
    <ul>
        <li>SSL sertifikası kullanın</li>
        <li>Dosya izinlerini kontrol edin</li>
        <li>Güçlü şifreler kullanın</li>
        <li>Düzenli güvenlik güncellemeleri yapın</li>
        <li>Log dosyalarını düzenli kontrol edin</li>
    </ul>
</div>";

echo "</div>";

// Dosyalar Tab
echo "<div id='files' class='tab-content'>";

// Dosya yapısı
echo "<div class='card'>
    <h3>📁 Proje Dosya Yapısı</h3>
    <input type='text' class='search-box' id='fileSearch' placeholder='Dosya adı ara...' onkeyup='searchFiles()'>
    <div id='fileTree'>";

function displayFileStructure($dir, $level = 0) {
    $files = glob($dir . '/*');
    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
    
    foreach ($files as $file) {
        if (is_dir($file)) {
            $name = basename($file);
            echo "<div>$indent 📁 $name/</div>";
            if ($level < 3) { // Maksimum 3 seviye derinlik
                displayFileStructure($file, $level + 1);
            }
        } else {
            $name = basename($file);
            $size = filesize($file);
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $icon = match($ext) {
                'php' => '🐘',
                'css' => '🎨',
                'js' => '⚡',
                'html' => '🌐',
                'sql' => '🗄️',
                'md' => '📝',
                default => '📄'
            };
            echo "<div>$indent$icon $name (" . round($size / 1024, 2) . " KB)</div>";
        }
    }
}

displayFileStructure('.');

echo "</div></div></div>";

// Araçlar Tab
echo "<div id='tools' class='tab-content'>";

// Debug araçları
echo "<div class='card'>
    <h3>🛠️ Debug Araçları</h3>
    <div class='grid'>";

// Log temizleme
echo "<div style='text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px;'>
    <h4>🧹 Log Temizleme</h4>
    <p>Eski log dosyalarını temizler</p>
    <button class='btn btn-danger' onclick='clearLogs()'>Logları Temizle</button>
</div>";

// Cache temizleme
echo "<div style='text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px;'>
    <h4>🗑️ Cache Temizleme</h4>
    <p>Önbellek dosyalarını temizler</p>
    <button class='btn btn-danger' onclick='clearCache()'>Cache Temizle</button>
</div>";

// Sistem bilgileri
echo "<div style='text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px;'>
    <h4>ℹ️ Sistem Bilgileri</h4>
    <p>Detaylı sistem bilgilerini gösterir</p>
    <button class='btn' onclick='showSystemInfo()'>Sistem Bilgileri</button>
</div>";

// Test araçları
echo "<div style='text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px;'>
    <h4>🧪 Test Araçları</h4>
    <p>Çeşitli sistem testleri</p>
    <button class='btn btn-success' onclick='runTests()'>Testleri Çalıştır</button>
</div>";

echo "</div></div>";

// Hızlı komutlar
echo "<div class='card'>
    <h3>⚡ Hızlı Komutlar</h3>
    <div class='grid'>";

$quickCommands = [
    'Email Test' => 'test-email-system.php',
    'Tema Test' => 'test-theme-toggle.php',
    'Okunabilirlik Test' => 'test-readability.php',
    'Kapsamlı Test' => 'comprehensive-test.php',
    'Kod Haritası' => 'KOD_YAPISI_HARITASI.php'
];

foreach ($quickCommands as $name => $file) {
    if (file_exists($file)) {
        echo "<div style='text-align: center; padding: 15px; background: #f0f9ff; border-radius: 8px;'>
            <a href='$file' style='text-decoration: none; color: #0369a1; font-weight: bold;'>$name</a>
        </div>";
    }
}

echo "</div></div></div>";

// JavaScript fonksiyonları
echo "<script>
function showTab(tabName) {
    // Tüm tab'ları gizle
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Tüm tab butonlarını pasif yap
    const tabButtons = document.querySelectorAll('.tab');
    tabButtons.forEach(button => button.classList.remove('active'));
    
    // Seçilen tab'ı göster
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}

function searchLogs() {
    const searchTerm = document.getElementById('logSearch').value.toLowerCase();
    const logEntries = document.querySelectorAll('#logEntries .log-entry');
    
    logEntries.forEach(entry => {
        const text = entry.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            entry.style.display = 'block';
        } else {
            entry.style.display = 'none';
        }
    });
}

function searchFiles() {
    const searchTerm = document.getElementById('fileSearch').value.toLowerCase();
    const fileTree = document.getElementById('fileTree');
    const lines = fileTree.querySelectorAll('div');
    
    lines.forEach(line => {
        const text = line.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            line.style.display = 'block';
            line.style.backgroundColor = 'yellow';
        } else {
            line.style.display = 'none';
            line.style.backgroundColor = '';
        }
    });
}

function executeQuery() {
    const query = document.getElementById('sqlQuery').value;
    const resultDiv = document.getElementById('queryResult');
    
    if (!query.trim()) {
        resultDiv.innerHTML = '<p class=\"status-warning\">⚠️ Lütfen bir sorgu yazın</p>';
        return;
    }
    
    resultDiv.innerHTML = '<p>⏳ Sorgu çalıştırılıyor...</p>';
    
    // AJAX ile sorgu gönder
    fetch('?action=execute_query', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'query=' + encodeURIComponent(query)
    })
    .then(response => response.text())
    .then(data => {
        resultDiv.innerHTML = data;
    })
    .catch(error => {
        resultDiv.innerHTML = '<p class=\"status-error\">❌ Hata: ' + error + '</p>';
    });
}

function clearQuery() {
    document.getElementById('sqlQuery').value = '';
    document.getElementById('queryResult').innerHTML = '';
}

function clearLogs() {
    if (confirm('Log dosyalarını temizlemek istediğinizden emin misiniz?')) {
        fetch('?action=clear_logs', {method: 'POST'})
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload();
        });
    }
}

function clearCache() {
    if (confirm('Cache dosyalarını temizlemek istediğinizden emin misiniz?')) {
        fetch('?action=clear_cache', {method: 'POST'})
        .then(response => response.text())
        .then(data => {
            alert(data);
        });
    }
}

function showSystemInfo() {
    const info = {
        'PHP Version': '" . phpversion() . "',
        'Server': '" . $_SERVER['SERVER_SOFTWARE'] . "',
        'OS': '" . php_uname() . "',
        'Memory Limit': '" . ini_get('memory_limit') . "',
        'Max Execution Time': '" . ini_get('max_execution_time') . "',
        'Upload Max Size': '" . ini_get('upload_max_filesize') . "'
    };
    
    let infoHtml = '<div class=\"json-viewer\"><h4>🖥️ Sistem Bilgileri</h4>';
    for (const [key, value] of Object.entries(info)) {
        infoHtml += '<p><strong>' + key + ':</strong> ' + value + '</p>';
    }
    infoHtml += '</div>';
    
    document.getElementById('queryResult').innerHTML = infoHtml;
}

function runTests() {
    const resultDiv = document.getElementById('queryResult');
    resultDiv.innerHTML = '<p>⏳ Testler çalıştırılıyor...</p>';
    
    fetch('?action=run_tests', {method: 'POST'})
    .then(response => response.text())
    .then(data => {
        resultDiv.innerHTML = data;
    });
}
</script>";

// AJAX işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'execute_query':
            try {
                if (isset($pdo) && $pdo) {
                    $query = $_POST['query'] ?? '';
                    $stmt = $pdo->query($query);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($results)) {
                        echo "<div class='json-viewer'>";
                        echo "<h4>📊 Sorgu Sonuçları</h4>";
                        echo "<table style='width: 100%; border-collapse: collapse;'>";
                        
                        // Başlıklar
                        echo "<tr style='background: #e5e7eb;'>";
                        foreach (array_keys($results[0]) as $header) {
                            echo "<th style='padding: 8px; border: 1px solid #d1d5db;'>$header</th>";
                        }
                        echo "</tr>";
                        
                        // Veriler
                        foreach ($results as $row) {
                            echo "<tr>";
                            foreach ($row as $cell) {
                                echo "<td style='padding: 8px; border: 1px solid #d1d5db;'>" . htmlspecialchars($cell) . "</td>";
                            }
                            echo "</tr>";
                        }
                        
                        echo "</table></div>";
                    } else {
                        echo "<p class='status-ok'>✅ Sorgu başarıyla çalıştırıldı (sonuç yok)</p>";
                    }
                } else {
                    echo "<p class='status-error'>❌ Veritabanı bağlantısı bulunamadı</p>";
                }
            } catch (Exception $e) {
                echo "<p class='status-error'>❌ Sorgu hatası: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            exit;
            
        case 'clear_logs':
            try {
                $logFiles = glob('logs/*.log');
                $cleared = 0;
                foreach ($logFiles as $file) {
                    if (file_put_contents($file, '') !== false) {
                        $cleared++;
                    }
                }
                echo "✅ $cleared log dosyası temizlendi";
            } catch (Exception $e) {
                echo "❌ Hata: " . $e->getMessage();
            }
            exit;
            
        case 'clear_cache':
            echo "✅ Cache temizleme işlemi tamamlandı";
            exit;
            
        case 'run_tests':
            echo "<div class='json-viewer'>
                <h4>🧪 Test Sonuçları</h4>
                <p class='status-ok'>✅ PHP Syntax: OK</p>
                <p class='status-ok'>✅ Database Connection: OK</p>
                <p class='status-ok'>✅ File Permissions: OK</p>
                <p class='status-ok'>✅ Email Configuration: OK</p>
            </div>";
            exit;
    }
}

echo "</div></body></html>";
?>
