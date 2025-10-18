<?php
// Email System Test for NextCode Group
// Test email functionality

// Define secure access constant
define('SECURE_ACCESS', true);

// Include required files
require_once 'config/email.php';
require_once 'includes/EmailSender.php';

// Test email configuration
echo "<h1>📧 Email System Test - NextCode Group</h1>";

// Test 1: Configuration Check
echo "<h2>1. Konfigürasyon Kontrolü</h2>";
$emailSender = new EmailSender();
$configTest = $emailSender->testEmailConfig();

if ($configTest['success']) {
    echo "✅ <strong>Konfigürasyon Geçerli:</strong> " . $configTest['message'] . "<br>";
} else {
    echo "❌ <strong>Konfigürasyon Hatası:</strong> " . $configTest['message'] . "<br>";
}

// Test 2: Email Configuration Details
echo "<h2>2. Email Konfigürasyon Detayları</h2>";
$config = getEmailConfig();
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Ayarlar</th><th>Değerler</th></tr>";
echo "<tr><td>SMTP Host</td><td>" . $config['smtp_host'] . "</td></tr>";
echo "<tr><td>SMTP Port</td><td>" . $config['smtp_port'] . "</td></tr>";
echo "<tr><td>SMTP Username</td><td>" . $config['smtp_username'] . "</td></tr>";
echo "<tr><td>SMTP Password</td><td>" . (empty($config['smtp_password']) ? '❌ Boş' : '✅ Ayarlanmış') . "</td></tr>";
echo "<tr><td>From Email</td><td>" . $config['from_email'] . "</td></tr>";
echo "<tr><td>Contact Email</td><td>" . $config['contact_email'] . "</td></tr>";
echo "<tr><td>Email Enabled</td><td>" . ($config['enabled'] ? '✅ Aktif' : '❌ Pasif') . "</td></tr>";
echo "</table>";

// Test 3: Test Email Sending (if form submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
    echo "<h2>3. Test Email Gönderimi</h2>";
    
    $testData = [
        'name' => $_POST['test_name'] ?? 'Test Kullanıcı',
        'email' => $_POST['test_email'] ?? 'test@example.com',
        'phone' => $_POST['test_phone'] ?? '+994501234567',
        'company' => $_POST['test_company'] ?? 'Test Şirkəti',
        'service' => $_POST['test_service'] ?? 'SEO Optimizasyonu',
        'budget' => $_POST['test_budget'] ?? '1000-2500 AZN',
        'subject' => 'Test Mesajı - Email Sistemi',
        'message' => 'Bu bir test mesajıdır. Email sistemi düzgün çalışıyor mu kontrol etmek için gönderilmiştir.'
    ];
    
    $result = $emailSender->sendContactEmail($testData);
    
    if ($result) {
        echo "✅ <strong>Test Email Başarıyla Gönderildi!</strong><br>";
        echo "📧 Email adresine kontrol edin: " . $testData['email'] . "<br>";
    } else {
        echo "❌ <strong>Test Email Gönderilemedi:</strong> " . $emailSender->getLastError() . "<br>";
    }
}

// Test Form
echo "<h2>3. Test Email Gönder</h2>";
echo "<form method='POST' style='background: #f5f5f5; padding: 20px; border-radius: 10px;'>";
echo "<table>";
echo "<tr><td>Ad:</td><td><input type='text' name='test_name' value='Test Kullanıcı' required></td></tr>";
echo "<tr><td>Email:</td><td><input type='email' name='test_email' value='xeyalcemilli9032@gmail.com' required></td></tr>";
echo "<tr><td>Telefon:</td><td><input type='text' name='test_phone' value='+994501234567'></td></tr>";
echo "<tr><td>Şirkət:</td><td><input type='text' name='test_company' value='Test Şirkəti'></td></tr>";
echo "<tr><td>Xidmət:</td><td><input type='text' name='test_service' value='SEO Optimizasyonu'></td></tr>";
echo "<tr><td>Büdcə:</td><td><input type='text' name='test_budget' value='1000-2500 AZN'></td></tr>";
echo "<tr><td colspan='2'><button type='submit' style='background: #1e40af; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Test Email Gönder</button></td></tr>";
echo "</table>";
echo "</form>";

// Test 4: System Information
echo "<h2>4. Sistem Bilgileri</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Özellik</th><th>Durum</th></tr>";
echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>Mail Function</td><td>" . (function_exists('mail') ? '✅ Mevcut' : '❌ Yok') . "</td></tr>";
echo "<tr><td>OpenSSL</td><td>" . (extension_loaded('openssl') ? '✅ Yüklü' : '❌ Yok') . "</td></tr>";
echo "<tr><td>cURL</td><td>" . (extension_loaded('curl') ? '✅ Yüklü' : '❌ Yok') . "</td></tr>";
echo "<tr><td>Server</td><td>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor') . "</td></tr>";
echo "</table>";

// Test 5: Email Log Check
echo "<h2>5. Email Log Kontrolü</h2>";
$logFile = __DIR__ . '/logs/email.log';
if (file_exists($logFile)) {
    echo "✅ <strong>Email Log Dosyası Mevcut:</strong> " . $logFile . "<br>";
    echo "📁 <strong>Dosya Boyutu:</strong> " . number_format(filesize($logFile)) . " bytes<br>";
    
    $logContent = file_get_contents($logFile);
    $logLines = explode("\n", $logContent);
    $recentLines = array_slice($logLines, -5); // Son 5 satır
    
    echo "<strong>Son Log Kayıtları:</strong><br>";
    echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
    foreach ($recentLines as $line) {
        if (!empty(trim($line))) {
            echo htmlspecialchars($line) . "\n";
        }
    }
    echo "</pre>";
} else {
    echo "⚠️ <strong>Email Log Dosyası Bulunamadı:</strong> " . $logFile . "<br>";
}

// Instructions
echo "<h2>📋 Kurulum Talimatları</h2>";
echo "<div style='background: #e0f2fe; padding: 15px; border-radius: 10px; border-left: 4px solid #0288d1;'>";
echo "<h3>Gmail SMTP Ayarları:</h3>";
echo "<ol>";
echo "<li>Gmail hesabınızda <strong>2-Factor Authentication</strong> aktifleştirin</li>";
echo "<li><strong>App Password</strong> oluşturun (Google Account > Security > App passwords)</li>";
echo "<li><code>config/email.php</code> dosyasında <strong>SMTP_PASSWORD</strong> değerini App Password ile değiştirin</li>";
echo "<li>Test email göndererek sistemi kontrol edin</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Test Tarihi:</strong> " . date('d.m.Y H:i:s') . "</p>";
?>
