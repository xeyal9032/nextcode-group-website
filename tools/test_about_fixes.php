<?php
/**
 * About.php sayfasındaki düzeltmeleri test et
 */

echo "About.php düzeltmeleri test ediliyor...\n";

$baseUrl = 'https://nextcodegroup.ostwind.az';

// Test edilecek URL'ler
$testUrls = [
    'about.php' => $baseUrl . '/about.php',
    'contact-info-api' => $baseUrl . '/api/contact-info.php'
];

echo "\n1. Sayfa Erişim Testleri:\n";
foreach ($testUrls as $name => $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "   ✅ $name - HTTP 200 OK\n";
    } else {
        echo "   ❌ $name - HTTP $httpCode\n";
    }
}

echo "\n2. About.php İçerik Testi:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/about.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    // CSS hatalarını kontrol et
    if (strpos($response, 'readability-enhancements.css') !== false) {
        echo "   ❌ readability-enhancements.css hala include ediliyor\n";
    } else {
        echo "   ✅ readability-enhancements.css include'u kaldırıldı\n";
    }
    
    // Font hatalarını kontrol et
    if (strpos($response, 'Inter-Bold.woff2') !== false || strpos($response, 'Inter-Regular.woff2') !== false) {
        echo "   ⚠️ Font dosyaları hala referans ediliyor (normal)\n";
    } else {
        echo "   ✅ Font referansları kontrol edildi\n";
    }
    
    // Favicon kontrolü
    if (strpos($response, 'favicon.svg') !== false) {
        echo "   ✅ Favicon.svg doğru referans ediliyor\n";
    } else {
        echo "   ⚠️ Favicon referansı bulunamadı\n";
    }
}

echo "\n3. API Test:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/contact-info.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        echo "   ✅ API başarıyla çalışıyor\n";
    } else {
        echo "   ⚠️ API çalışıyor ama beklenmeyen format\n";
    }
} else {
    echo "   ❌ API hala HTTP $httpCode döndürüyor\n";
}

echo "\n=== Test Tamamlandı ===\n";
?>














































