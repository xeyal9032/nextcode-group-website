<?php
/**
 * NextCode Group - Glass Card Test
 * Rəqəmsal Uğur bölümünün light/dark mode görünümünü test eder
 */

// Define secure access constant
define('SECURE_ACCESS', true);

// Set page variables
$page_title = 'Glass Card Test - NextCode Group';
$meta_description = 'Glass card görünümünün light/dark mode testleri';
$current_page = 'test';

// Include required files
require_once 'includes/header.php';
?>

<section class="modern-section modern-section--light">
    <div class="modern-container">
        <div class="modern-text-center modern-m-8">
            <h1 class="modern-heading modern-heading--h1 modern-m-6">
                🎨 Glass Card Test
            </h1>
            <p class="modern-text modern-text--lead modern-m-6">
                "Rəqəmsal Uğur" bölümünün light/dark mode görünümünü test edin
            </p>
        </div>
        
        <!-- Test Butonları -->
        <div class="modern-text-center modern-m-6">
            <button class="modern-btn modern-btn--primary modern-m-2" onclick="switchToLightMode()">
                ☀️ Light Mode
            </button>
            <button class="modern-btn modern-btn--secondary modern-m-2" onclick="switchToDarkMode()">
                🌙 Dark Mode
            </button>
            <button class="modern-btn modern-btn--outline modern-m-2" onclick="toggleTheme()">
                🔄 Toggle Theme
            </button>
        </div>
        
        <!-- Hero Section Test -->
        <div class="modern-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 4rem 0;">
            <div class="modern-container">
                <div class="modern-grid modern-grid--2">
                    <!-- Sol Taraf -->
                    <div class="modern-animate--fadeInLeft">
                        <div class="modern-text-center modern-m-8">
                            <div class="modern-badge modern-badge--primary modern-m-4">
                                <i class="fas fa-rocket"></i>
                                RƏQƏMSAL TRANSFORMASIYA
                            </div>
                            <h1 class="modern-heading modern-heading--h1 modern-text-light modern-m-6">
                                NextCode Group ilə<br>
                                Rəqəmsal<br>
                                Dönüşümünüzü<br>
                                Başlayın
                            </h1>
                            <p class="modern-text modern-text-light modern-m-6">
                                Müasir web həlləri, mobil tətbiqlər və rəqəmsal marketinq xidmətləri ilə biznesinizi böyüdün
                            </p>
                        </div>
                    </div>
                    
                    <!-- Sağ Taraf - Glass Card -->
                    <div class="modern-animate--fadeInRight modern-text-center">
                        <div class="modern-card modern-card--glass modern-p-8">
                            <div class="modern-card__body">
                                <i class="fas fa-chart-line" style="font-size: 4rem; color: rgba(255,255,255,0.8);"></i>
                                <h3 class="modern-heading modern-heading--h4 modern-text-light modern-m-4">
                                    Rəqəmsal Uğur
                                </h3>
                                <p class="modern-text modern-text-light">
                                    AI və data analitikası ilə biznesinizi gücləndirin
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Karşılaştırma Bölümü -->
        <div class="modern-section modern-section--light">
            <div class="modern-container">
                <div class="modern-text-center modern-m-8">
                    <h2 class="modern-heading modern-heading--h2">🔍 Görünüm Karşılaştırması</h2>
                    <p class="modern-text modern-text--lead">Light ve Dark modda glass card görünümü</p>
                </div>
                
                <div class="modern-grid modern-grid--2">
                    <!-- Light Mode Test -->
                    <div class="modern-card">
                        <div class="modern-card__header">
                            <h3 class="modern-heading modern-heading--h4">☀️ Light Mode</h3>
                        </div>
                        <div class="modern-card__body" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 2rem;">
                            <div class="modern-card modern-card--glass modern-p-6">
                                <div class="modern-card__body">
                                    <i class="fas fa-chart-line" style="font-size: 3rem; color: rgba(255,255,255,0.8);"></i>
                                    <h4 class="modern-heading modern-heading--h5 modern-text-light modern-m-4">
                                        Rəqəmsal Uğur
                                    </h4>
                                    <p class="modern-text modern-text-light">
                                        AI və data analitikası
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dark Mode Test -->
                    <div class="modern-card">
                        <div class="modern-card__header">
                            <h3 class="modern-heading modern-heading--h4">🌙 Dark Mode</h3>
                        </div>
                        <div class="modern-card__body" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 2rem;">
                            <div class="modern-card modern-card--glass modern-p-6" data-theme="dark">
                                <div class="modern-card__body">
                                    <i class="fas fa-chart-line" style="font-size: 3rem; color: rgba(255,255,255,0.8);"></i>
                                    <h4 class="modern-heading modern-heading--h5 modern-text-light modern-m-4">
                                        Rəqəmsal Uğur
                                    </h4>
                                    <p class="modern-text modern-text-light">
                                        AI və data analitikası
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sonuçlar -->
        <div class="modern-card modern-m-8">
            <div class="modern-card__header">
                <h3 class="modern-heading modern-heading--h4">📊 Test Sonuçları</h3>
            </div>
            <div class="modern-card__body">
                <div id="testResults" class="modern-text">
                    <p>Test sonuçları burada görünecek...</p>
                </div>
                <button class="modern-btn modern-btn--primary" onclick="runGlassCardTest()">
                    🧪 Glass Card Testini Çalıştır
                </button>
            </div>
        </div>
    </div>
</section>

<script>
function switchToLightMode() {
    document.documentElement.setAttribute('data-theme', 'light');
    localStorage.setItem('theme', 'light');
    updateTestResults('Light Mode aktifleştirildi');
}

function switchToDarkMode() {
    document.documentElement.setAttribute('data-theme', 'dark');
    localStorage.setItem('theme', 'dark');
    updateTestResults('Dark Mode aktifleştirildi');
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateTestResults(`${newTheme === 'light' ? 'Light' : 'Dark'} Mode'a geçildi`);
}

function updateTestResults(message) {
    const resultsDiv = document.getElementById('testResults');
    const timestamp = new Date().toLocaleString();
    resultsDiv.innerHTML = `
        <div class="modern-alert modern-alert--success">
            <strong>✅ ${message}</strong><br>
            <small>Tarih: ${timestamp}</small>
        </div>
    `;
}

function runGlassCardTest() {
    const resultsDiv = document.getElementById('testResults');
    resultsDiv.innerHTML = '<p>⏳ Glass card testi çalıştırılıyor...</p>';
    
    setTimeout(() => {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const glassCard = document.querySelector('.modern-card--glass');
        
        if (glassCard) {
            const computedStyle = window.getComputedStyle(glassCard);
            const backgroundColor = computedStyle.backgroundColor;
            const color = computedStyle.color;
            
            resultsDiv.innerHTML = `
                <div class="modern-alert modern-alert--info">
                    <h4>🔍 Glass Card Analizi</h4>
                    <p><strong>Mevcut Tema:</strong> ${currentTheme}</p>
                    <p><strong>Arka Plan Rengi:</strong> ${backgroundColor}</p>
                    <p><strong>Metin Rengi:</strong> ${color}</p>
                    <p><strong>Durum:</strong> ${currentTheme === 'light' ? 'Light mode için optimize edildi' : 'Dark mode için optimize edildi'}</p>
                </div>
            `;
        } else {
            resultsDiv.innerHTML = '<p class="modern-text--error">❌ Glass card bulunamadı</p>';
        }
    }, 1000);
}

// Sayfa yüklendiğinde mevcut temayı yükle
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    console.log('Glass card test sayfası yüklendi, tema:', savedTheme);
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?>
