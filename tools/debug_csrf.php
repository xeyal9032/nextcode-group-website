<?php
/**
 * CSRF Debug Script
 * Bu script CSRF token'ın neden çalışmadığını debug eder
 */

define('SECURE_ACCESS', true);

echo "=== CSRF Debug Script ===\n";
echo "Başlangıç zamanı: " . date('Y-m-d H:i:s') . "\n\n";

// 1. Session durumunu kontrol et
echo "1. Session Durumu:\n";
echo "   Session Status: " . session_status() . " (1=disabled, 2=active, 0=none)\n";
echo "   Session ID: " . (session_id() ?: 'Yok') . "\n";
echo "   Session Name: " . session_name() . "\n\n";

// 2. Session'ı başlat
echo "2. Session Başlatılıyor:\n";
session_start();
echo "   Session Status: " . session_status() . "\n";
echo "   Session ID: " . session_id() . "\n\n";

// 3. Security dosyasını yükle
echo "3. Security Dosyası Yükleniyor:\n";
try {
    $securityPath = __DIR__ . '/../config/security.php';
    echo "   Security dosya yolu: $securityPath\n";
    echo "   Dosya var mı: " . (file_exists($securityPath) ? 'Evet' : 'Hayır') . "\n";
    
    if (file_exists($securityPath)) {
        require_once $securityPath;
        echo "   ✅ Security.php başarıyla yüklendi\n";
    } else {
        echo "   ❌ Security.php dosyası bulunamadı\n";
    }
} catch (Exception $e) {
    echo "   ❌ Security.php yükleme hatası: " . $e->getMessage() . "\n";
}

// 4. CSRF fonksiyonlarını kontrol et
echo "\n4. CSRF Fonksiyonları:\n";
if (function_exists('generate_csrf_token')) {
    echo "   ✅ generate_csrf_token() fonksiyonu mevcut\n";
} else {
    echo "   ❌ generate_csrf_token() fonksiyonu bulunamadı\n";
}

if (function_exists('verify_csrf_token')) {
    echo "   ✅ verify_csrf_token() fonksiyonu mevcut\n";
} else {
    echo "   ❌ verify_csrf_token() fonksiyonu bulunamadı\n";
}

// 5. CSRF token oluştur
echo "\n5. CSRF Token Oluşturma:\n";
try {
    $token = generate_csrf_token();
    echo "   ✅ CSRF token oluşturuldu: " . substr($token, 0, 16) . "...\n";
    echo "   Token uzunluğu: " . strlen($token) . " karakter\n";
    echo "   Session'da token: " . (isset($_SESSION['csrf_token']) ? 'Var' : 'Yok') . "\n";
} catch (Exception $e) {
    echo "   ❌ CSRF token oluşturma hatası: " . $e->getMessage() . "\n";
}

// 6. Token doğrulama
echo "\n6. CSRF Token Doğrulama:\n";
if (isset($token)) {
    $isValid = verify_csrf_token($token);
    echo "   Token doğrulama: " . ($isValid ? '✅ Geçerli' : '❌ Geçersiz') . "\n";
    
    $isInvalid = verify_csrf_token('invalid_token');
    echo "   Geçersiz token testi: " . ($isInvalid ? '❌ Yanlış kabul etti' : '✅ Doğru reddetti') . "\n";
}

// 7. Session verilerini göster
echo "\n7. Session Verileri:\n";
if (!empty($_SESSION)) {
    foreach ($_SESSION as $key => $value) {
        if (is_string($value)) {
            echo "   $key: " . substr($value, 0, 20) . (strlen($value) > 20 ? '...' : '') . "\n";
        } else {
            echo "   $key: " . gettype($value) . "\n";
        }
    }
} else {
    echo "   Session boş\n";
}

echo "\n=== Debug Tamamlandı ===\n";
?>
