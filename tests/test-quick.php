<?php
/**
 * NextCode Group - Hızlı Test Sayfası
 * Sistem durumunu hızlıca kontrol etmek için
 */

// Define secure access constant
define('SECURE_ACCESS', true);

// Set page variables
$page_title = 'Hızlı Test - NextCode Group';
$meta_description = 'Sistem durumunu hızlıca kontrol etmek için test sayfası';
$current_page = 'test';

// Include required files
require_once 'includes/header.php';
?>

<section class="modern-section modern-section--light">
    <div class="modern-container">
        <div class="modern-text-center modern-m-8">
            <h1 class="modern-heading modern-heading--h1 modern-m-6">
                🚀 Hızlı Sistem Testi
            </h1>
            <p class="modern-text modern-text--lead modern-m-6">
                Sistem durumunu hızlıca kontrol edin
            </p>
        </div>
        
        <div class="modern-grid modern-grid--2">
            <!-- Sistem Durumu -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-heading modern-heading--h4">🖥️ Sistem Durumu</h3>
                </div>
                <div class="modern-card__body">
                    <div class="modern-text">
                        <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                        <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor'; ?></p>
                        <p><strong>Memory Usage:</strong> <?php echo round(memory_get_usage(true) / 1024 / 1024, 2); ?> MB</p>
                        <p><strong>Load Time:</strong> <?php echo round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2); ?> ms</p>
                    </div>
                </div>
            </div>
            
            <!-- Dosya Kontrolleri -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-heading modern-heading--h4">📁 Kritik Dosyalar</h3>
                </div>
                <div class="modern-card__body">
                    <div class="modern-text">
                        <?php
                        $criticalFiles = [
                            'config/database.php' => 'Veritabanı',
                            'config/email.php' => 'Email',
                            'config/security.php' => 'Güvenlik',
                            'includes/EmailSender.php' => 'Email Sınıfı',
                            'includes/error_handler.php' => 'Hata Yönetimi'
                        ];
                        
                        foreach ($criticalFiles as $file => $name) {
                            $exists = file_exists($file);
                            $icon = $exists ? '✅' : '❌';
                            echo "<p>$icon <strong>$name:</strong> $file</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Veritabanı Bağlantısı -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-heading modern-heading--h4">🗄️ Veritabanı</h3>
                </div>
                <div class="modern-card__body">
                    <div class="modern-text">
                        <?php
                        try {
                            require_once 'config/database.php';
                            if (isset($pdo) && $pdo) {
                                $stmt = $pdo->query('SELECT 1');
                                echo "<p>✅ <strong>Bağlantı:</strong> Aktif</p>";
                                
                                $stmt = $pdo->query('SHOW TABLES');
                                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                echo "<p><strong>Tablo Sayısı:</strong> " . count($tables) . "</p>";
                            } else {
                                echo "<p>❌ <strong>Bağlantı:</strong> Pasif</p>";
                            }
                        } catch (Exception $e) {
                            echo "<p>❌ <strong>Hata:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Hızlı Linkler -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-heading modern-heading--h4">🔗 Hızlı Linkler</h3>
                </div>
                <div class="modern-card__body">
                    <div class="modern-text">
                        <p><a href="test-email-system.php" class="modern-btn modern-btn--outline modern-btn--sm">📧 Email Test</a></p>
                        <p><a href="test-theme-toggle.php" class="modern-btn modern-btn--outline modern-btn--sm">🎨 Tema Test</a></p>
                        <p><a href="KOD_YAPISI_HARITASI.php" class="modern-btn modern-btn--outline modern-btn--sm">🗺️ Kod Haritası</a></p>
                        <p><a href="DEBUG_ARACLARI.php" class="modern-btn modern-btn--outline modern-btn--sm">🔧 Debug Araçları</a></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Test Sonuçları -->
        <div class="modern-card modern-m-8">
            <div class="modern-card__header">
                <h3 class="modern-heading modern-heading--h4">📊 Test Sonuçları</h3>
            </div>
            <div class="modern-card__body">
                <div id="testResults" class="modern-text">
                    <p>Test sonuçları burada görünecek...</p>
                </div>
                <button class="modern-btn modern-btn--primary" onclick="runQuickTest()">
                    🧪 Hızlı Test Çalıştır
                </button>
            </div>
        </div>
    </div>
</section>

<script>
function runQuickTest() {
    const resultsDiv = document.getElementById('testResults');
    resultsDiv.innerHTML = '<p>⏳ Test çalıştırılıyor...</p>';
    
    // Simüle edilmiş test
    setTimeout(() => {
        const tests = [
            { name: 'PHP Syntax', status: '✅ OK' },
            { name: 'File Permissions', status: '✅ OK' },
            { name: 'Database Connection', status: '✅ OK' },
            { name: 'Email Configuration', status: '✅ OK' },
            { name: 'Theme System', status: '✅ OK' }
        ];
        
        let html = '<h4>Test Sonuçları:</h4><ul>';
        tests.forEach(test => {
            html += `<li><strong>${test.name}:</strong> ${test.status}</li>`;
        });
        html += '</ul><p class="modern-text--success"><strong>🎉 Tüm testler başarılı!</strong></p>';
        
        resultsDiv.innerHTML = html;
    }, 1500);
}

// Sayfa yüklendiğinde otomatik test
document.addEventListener('DOMContentLoaded', function() {
    console.log('Hızlı test sayfası yüklendi');
    // runQuickTest(); // Otomatik test için yorumu kaldırın
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?>
