<?php
/**
 * Test Access Script
 * Bu script tools klasöründeki dosyaların erişilebilirliğini test eder
 */

echo "=== Tools Klasörü Erişim Testi ===\n";
echo "Test zamanı: " . date('Y-m-d H:i:s') . "\n\n";

// Test edilecek dosyalar
$testFiles = [
    'log_cleaner.php',
    'log_monitor.php',
    'admin_panel_checker.php',
    'csrf_test.php',
    'ftp_uploader.php'
];

echo "1. Dosya Varlığı Kontrolü:\n";
foreach ($testFiles as $file) {
    $filePath = __DIR__ . '/' . $file;
    if (file_exists($filePath)) {
        echo "   ✅ $file - Mevcut\n";
    } else {
        echo "   ❌ $file - Bulunamadı\n";
    }
}

echo "\n2. PHP Syntax Kontrolü:\n";
foreach ($testFiles as $file) {
    $filePath = __DIR__ . '/' . $file;
    if (file_exists($filePath)) {
        $output = shell_exec("php -l \"$filePath\" 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "   ✅ $file - Syntax OK\n";
        } else {
            echo "   ❌ $file - Syntax Error\n";
            echo "      " . trim($output) . "\n";
        }
    }
}

echo "\n3. Web Erişim Testi:\n";
$baseUrl = 'https://nextcodegroup.ostwind.az';
foreach ($testFiles as $file) {
    $url = $baseUrl . '/tools/' . $file;
    echo "   Test ediliyor: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_NOBODY, true); // Sadece header'ları al
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "   ✅ $file - HTTP 200 OK\n";
    } else if ($httpCode == 500) {
        echo "   ❌ $file - HTTP 500 Error\n";
    } else if ($httpCode == 404) {
        echo "   ❌ $file - HTTP 404 Not Found\n";
    } else {
        echo "   ⚠️ $file - HTTP $httpCode\n";
    }
}

echo "\n=== Test Tamamlandı ===\n";
?>
