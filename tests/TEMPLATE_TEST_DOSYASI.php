<?php
/**
 * NextCode Group - Test Dosyası Template
 * Bu template yeni test dosyaları oluştururken kullanılabilir
 * 
 * @author NextCode Group
 * @version 1.0
 * @since 2024
 */

// Define secure access constant
define('SECURE_ACCESS', true);

// Set page variables
$page_title = 'Test Sayfası - NextCode Group';
$meta_description = 'Test sayfası açıklaması';
$current_page = 'test';

// Include required files
require_once 'includes/header.php';

// Additional includes if needed
// require_once 'config/database.php';
// require_once 'includes/EmailSender.php';
?>

<section class="modern-section modern-section--light">
    <div class="modern-container">
        <div class="modern-text-center modern-m-8">
            <h1 class="modern-heading modern-heading--h1 modern-m-6">
                Test Sayfası
            </h1>
            <p class="modern-text modern-text--lead modern-m-6">
                Bu bir test sayfasıdır. Gerekli değişiklikleri yapın.
            </p>
        </div>
        
        <div class="modern-card">
            <div class="modern-card__body">
                <h2 class="modern-heading modern-heading--h3 modern-m-6">Test İçeriği</h2>
                
                <!-- Test içeriğinizi buraya ekleyin -->
                <div class="modern-text">
                    <p>Test içeriği buraya yazılacak...</p>
                </div>
                
                <!-- Test butonları -->
                <div class="modern-flex modern-justify-center modern-m-6">
                    <button class="modern-btn modern-btn--primary" onclick="testFunction()">
                        Test Et
                    </button>
                </div>
                
                <!-- Test sonuçları -->
                <div id="testResults" class="modern-m-6" style="display: none;">
                    <div class="modern-alert modern-alert--info">
                        <h4>Test Sonuçları</h4>
                        <p id="testOutput"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function testFunction() {
    const resultsDiv = document.getElementById('testResults');
    const outputDiv = document.getElementById('testOutput');
    
    // Test işlemi buraya yazılacak
    const testResult = {
        success: true,
        message: 'Test başarıyla tamamlandı!',
        timestamp: new Date().toLocaleString()
    };
    
    // Sonuçları göster
    outputDiv.innerHTML = `
        <strong>Durum:</strong> ${testResult.success ? '✅ Başarılı' : '❌ Başarısız'}<br>
        <strong>Mesaj:</strong> ${testResult.message}<br>
        <strong>Tarih:</strong> ${testResult.timestamp}
    `;
    
    resultsDiv.style.display = 'block';
}

// Sayfa yüklendiğinde otomatik test
document.addEventListener('DOMContentLoaded', function() {
    console.log('Test sayfası yüklendi:', '<?php echo $page_title; ?>');
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?>
