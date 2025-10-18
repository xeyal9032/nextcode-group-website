<?php
/**
 * Font düzeltmelerini test et
 */

echo "Font düzeltmeleri test ediliyor...\n";

$baseUrl = 'https://nextcodegroup.ostwind.az';

echo "\n1. Font CSS Dosyası Testi:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/css/inter-font.css');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 && $response) {
    echo "   ✅ Font CSS dosyası yüklendi (HTTP $httpCode)\n";
    
    // Google Fonts import kontrolü
    if (strpos($response, '@import url') !== false) {
        echo "   ✅ Google Fonts import eklendi\n";
    } else {
        echo "   ❌ Google Fonts import bulunamadı\n";
    }
    
    // Font-face tanımları kontrolü
    $fontFaceCount = substr_count($response, '@font-face');
    echo "   ✅ $fontFaceCount font-face tanımı bulundu\n";
    
} else {
    echo "   ❌ Font CSS dosyası yüklenemedi (HTTP $httpCode)\n";
}

echo "\n2. Font Loader JS Testi:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/js/font-loader.js');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 && $response) {
    echo "   ✅ Font Loader JS dosyası yüklendi (HTTP $httpCode)\n";
    
    // Hata yönetimi kontrolü
    if (strpos($response, 'loadFontsIndividually') !== false) {
        echo "   ✅ Bireysel font yükleme fonksiyonu eklendi\n";
    } else {
        echo "   ❌ Bireysel font yükleme fonksiyonu bulunamadı\n";
    }
    
    // Font kontrolü
    if (strpos($response, 'document.fonts.check') !== false) {
        echo "   ✅ Font kontrolü eklendi\n";
    } else {
        echo "   ❌ Font kontrolü bulunamadı\n";
    }
    
    // Hata yakalama
    if (strpos($response, '.catch((error)') !== false) {
        echo "   ✅ Gelişmiş hata yakalama eklendi\n";
    } else {
        echo "   ❌ Gelişmiş hata yakalama bulunamadı\n";
    }
    
} else {
    echo "   ❌ Font Loader JS dosyası yüklenemedi (HTTP $httpCode)\n";
}

echo "\n3. About.php Font Testi:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/about.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 && $response) {
    echo "   ✅ About.php sayfası yüklendi (HTTP $httpCode)\n";
    
    // Font CSS include kontrolü
    if (strpos($response, 'inter-font.css') !== false) {
        echo "   ✅ Inter font CSS include edildi\n";
    } else {
        echo "   ❌ Inter font CSS include edilmemiş\n";
    }
    
    // Font Loader JS include kontrolü
    if (strpos($response, 'font-loader.js') !== false) {
        echo "   ✅ Font Loader JS include edildi\n";
    } else {
        echo "   ❌ Font Loader JS include edilmemiş\n";
    }
    
} else {
    echo "   ❌ About.php sayfası yüklenemedi (HTTP $httpCode)\n";
}

echo "\n=== Test Tamamlandı ===\n";
?>














































