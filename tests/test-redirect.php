<?php
/**
 * Redirect Test - NextCode Group
 * SSL yönlendirme döngüsü testi
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirect Test - NextCode Group</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-result { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <h1>🔄 Redirect Test Sonuçları</h1>
    
    <div class="test-result info">
        <h3>📊 Test Bilgileri</h3>
        <p><strong>Protocol:</strong> <?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS' : 'HTTP'; ?></p>
        <p><strong>Host:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'Bilinmiyor'; ?></p>
        <p><strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'Bilinmiyor'; ?></p>
        <p><strong>Server Name:</strong> <?php echo $_SERVER['SERVER_NAME'] ?? 'Bilinmiyor'; ?></p>
    </div>
    
    <div class="test-result success">
        <h3>✅ SSL Durumu</h3>
        <p><?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS aktif - SSL çalışıyor' : 'HTTP - SSL geçici olarak devre dışı'; ?></p>
    </div>
    
    <div class="test-result info">
        <h3>🔧 Yapılan Düzeltmeler</h3>
        <ul>
            <li>✅ Localhost ve 127.0.0.1 için SSL yönlendirme devre dışı</li>
            <li>✅ SSL header'ları sadece HTTPS'de aktif</li>
            <li>✅ Yönlendirme döngüsü önlendi</li>
        </ul>
    </div>
    
    <div class="test-result success">
        <h3>🎯 Test Sonucu</h3>
        <p>Bu sayfa yüklenebiliyorsa redirect döngüsü düzeltilmiştir!</p>
    </div>
    
    <div class="test-result info">
        <h3>📝 Sonraki Adımlar</h3>
        <ol>
            <li>Ana sayfayı test edin: <a href="/">Ana Sayfa</a></li>
            <li>Diğer sayfaları test edin</li>
            <li>SSL sertifikası kurulumu için hosting sağlayıcınızla iletişime geçin</li>
        </ol>
    </div>
    
</body>
</html>
