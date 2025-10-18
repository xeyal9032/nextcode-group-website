<?php
/**
 * About.php header düzeltmesini test et
 */

echo "About.php header düzeltmesi test ediliyor...\n";

$baseUrl = 'https://nextcodegroup.ostwind.az';

echo "\n1. About.php Sayfası Testi:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/about.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 && $response) {
    echo "   ✅ Sayfa başarıyla yüklendi (HTTP $httpCode)\n";
    
    // Duplicate navigation kontrolü
    $navCount = substr_count($response, '<nav class="navbar');
    if ($navCount == 1) {
        echo "   ✅ Tek navigation var (doğru)\n";
    } else {
        echo "   ❌ $navCount navigation bulundu (duplicate var!)\n";
    }
    
    // Theme toggle button kontrolü
    $themeToggleCount = substr_count($response, 'themeToggle');
    if ($themeToggleCount <= 1) {
        echo "   ✅ Theme toggle button doğru sayıda ($themeToggleCount)\n";
    } else {
        echo "   ❌ Çok fazla theme toggle button ($themeToggleCount)\n";
    }
    
    // Contact Us button kontrolü
    $contactButtonCount = substr_count($response, 'Contact Us');
    if ($contactButtonCount <= 2) {
        echo "   ✅ Contact Us button sayısı normal ($contactButtonCount)\n";
    } else {
        echo "   ❌ Çok fazla Contact Us button ($contactButtonCount)\n";
    }
    
    // Page header kontrolü
    if (strpos($response, 'modern-section--hero') !== false) {
        echo "   ✅ Page header section bulundu\n";
    } else {
        echo "   ❌ Page header section bulunamadı\n";
    }
    
    // About title kontrolü
    if (strpos($response, 'Haqqımızda') !== false) {
        echo "   ✅ About title (Haqqımızda) bulundu\n";
    } else {
        echo "   ❌ About title bulunamadı\n";
    }
    
} else {
    echo "   ❌ Sayfa yüklenemedi (HTTP $httpCode)\n";
}

echo "\n2. Header Include Testi:\n";
if (strpos($response, 'includes/header.php') === false) {
    echo "   ✅ Header include doğru çalışıyor\n";
} else {
    echo "   ❌ Header include hatası var\n";
}

echo "\n3. HTML Yapısı Kontrolü:\n";
// HTML yapısını kontrol et
$dom = new DOMDocument();
@$dom->loadHTML($response);
$xpath = new DOMXPath($dom);

// Navigation sayısını kontrol et
$navs = $xpath->query('//nav[@class="navbar"]');
echo "   Navigation sayısı: " . $navs->length . "\n";

// Button sayısını kontrol et
$buttons = $xpath->query('//button[@id="themeToggle"]');
echo "   Theme toggle button sayısı: " . $buttons->length . "\n";

// Contact Us link sayısını kontrol et
$contactLinks = $xpath->query('//a[contains(text(), "Contact Us")]');
echo "   Contact Us link sayısı: " . $contactLinks->length . "\n";

echo "\n=== Test Tamamlandı ===\n";
?>














































