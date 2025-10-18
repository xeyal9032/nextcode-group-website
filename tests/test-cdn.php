<?php
/**
 * CDN Test Dosyası - NextCode Group
 * CDN aktifleştirme sonrası test için
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once 'config/cdn-config.php';

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CDN Test - NextCode Group</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-result { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <h1>🚀 CDN Test Sonuçları</h1>
    
    <div class="test-result info">
        <strong>CDN Durumu:</strong> <?php echo $cdnManager->cdnEnabled ? '✅ Aktif' : '❌ Pasif'; ?>
    </div>
    
    <div class="test-result info">
        <strong>Fallback Sistemi:</strong> <?php echo $cdnManager->fallbackEnabled ? '✅ Aktif' : '❌ Pasif'; ?>
    </div>
    
    <h2>📊 CDN URL Testleri</h2>
    
    <?php
    // Bootstrap CSS testi
    $bootstrapUrl = $cdnManager->getCssUrl('bootstrap');
    $bootstrapAvailable = $cdnManager->isCdnAvailable($bootstrapUrl);
    ?>
    <div class="test-result <?php echo $bootstrapAvailable ? 'success' : 'error'; ?>">
        <strong>Bootstrap CSS:</strong> 
        <?php echo $bootstrapAvailable ? '✅ CDN Çalışıyor' : '❌ CDN Çalışmıyor - Local dosya kullanılacak'; ?>
        <br><small>URL: <?php echo $bootstrapUrl; ?></small>
    </div>
    
    <?php
    // FontAwesome CSS testi
    $fontawesomeUrl = $cdnManager->getCssUrl('fontawesome');
    $fontawesomeAvailable = $cdnManager->isCdnAvailable($fontawesomeUrl);
    ?>
    <div class="test-result <?php echo $fontawesomeAvailable ? 'success' : 'error'; ?>">
        <strong>FontAwesome CSS:</strong> 
        <?php echo $fontawesomeAvailable ? '✅ CDN Çalışıyor' : '❌ CDN Çalışmıyor - Local dosya kullanılacak'; ?>
        <br><small>URL: <?php echo $fontawesomeUrl; ?></small>
    </div>
    
    <?php
    // Google Fonts testi
    $googleFontsUrl = $cdnManager->getCssUrl('google_fonts');
    $googleFontsAvailable = $cdnManager->isCdnAvailable($googleFontsUrl);
    ?>
    <div class="test-result <?php echo $googleFontsAvailable ? 'success' : 'error'; ?>">
        <strong>Google Fonts:</strong> 
        <?php echo $googleFontsAvailable ? '✅ CDN Çalışıyor' : '❌ CDN Çalışmıyor - Local dosya kullanılacak'; ?>
        <br><small>URL: <?php echo $googleFontsUrl; ?></small>
    </div>
    
    <h2>🔧 CDN HTML Tag Örnekleri</h2>
    
    <div class="test-result info">
        <strong>Bootstrap CSS Tag:</strong><br>
        <code><?php echo htmlspecialchars($cdnManager->generateCssTag('bootstrap')); ?></code>
    </div>
    
    <div class="test-result info">
        <strong>FontAwesome CSS Tag:</strong><br>
        <code><?php echo htmlspecialchars($cdnManager->generateCssTag('fontawesome')); ?></code>
    </div>
    
    <h2>📈 Performans Bilgileri</h2>
    
    <div class="test-result success">
        <strong>✅ CDN Avantajları:</strong><br>
        • Daha hızlı yükleme<br>
        • Bandwidth tasarrufu<br>
        • Global erişim<br>
        • Otomatik fallback sistemi
    </div>
    
    <div class="test-result info">
        <strong>🔄 Fallback Sistemi:</strong><br>
        CDN çalışmazsa otomatik olarak local dosyalar kullanılır.<br>
        Bu sayede site her zaman çalışır durumda kalır.
    </div>
    
    <p><a href="index.php">← Ana Sayfaya Dön</a></p>
    
    <script>
        // CDN test sonuçlarını konsola yazdır
        console.log('CDN Test Tamamlandı:', {
            cdnEnabled: <?php echo $cdnManager->cdnEnabled ? 'true' : 'false'; ?>,
            fallbackEnabled: <?php echo $cdnManager->fallbackEnabled ? 'true' : 'false'; ?>,
            bootstrapAvailable: <?php echo $bootstrapAvailable ? 'true' : 'false'; ?>,
            fontawesomeAvailable: <?php echo $fontawesomeAvailable ? 'true' : 'false'; ?>,
            googleFontsAvailable: <?php echo $googleFontsAvailable ? 'true' : 'false'; ?>
        });
    </script>
</body>
</html>

