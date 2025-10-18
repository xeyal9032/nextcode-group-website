<?php
// Test file for theme toggle functionality

// Define secure access constant
define('SECURE_ACCESS', true);

// Set page variables
$page_title = 'Tema Değiştirme Testi - NextCode Group';
$meta_description = 'Tema değiştirme sisteminin test edilmesi için oluşturulmuş test sayfası';
$current_page = 'test';

require_once 'includes/header.php';
?>

<section class="modern-section modern-section--light">
    <div class="modern-container">
        <div class="modern-text-center modern-m-8">
            <h1 class="modern-heading modern-heading--h1 modern-m-6">
                Tema Değiştirme Testi
            </h1>
            <p class="modern-text modern-text--lead modern-m-6">
                Bu sayfa tema değiştirme butonunun düzgün çalışıp çalışmadığını test etmek için oluşturulmuştur.
            </p>
        </div>
        
        <div class="modern-grid modern-grid--2">
            <div class="modern-card">
                <div class="modern-card__body">
                    <h2 class="modern-heading modern-heading--h3">Tema Butonu Testi</h2>
                    <p class="modern-text">
                        Yukarıdaki navigasyon çubuğundaki tema değiştirme butonuna tıklayın. 
                        Sayfa renklerinin değişip değişmediğini kontrol edin.
                    </p>
                    <div class="modern-text modern-text--small modern-text--muted">
                        Buton ID: themeToggle<br>
                        İkon ID: themeIcon<br>
                        Mevcut Tema: <span id="currentTheme">light</span>
                    </div>
                </div>
            </div>
            
            <div class="modern-card">
                <div class="modern-card__body">
                    <h2 class="modern-heading modern-heading--h3">Renk Değişimi Testi</h2>
                    <p class="modern-text">
                        Tema değiştiğinde aşağıdaki elementlerin renklerinin değişip değişmediğini kontrol edin:
                    </p>
                    <ul class="modern-text modern-text--small">
                        <li>Arka plan rengi</li>
                        <li>Metin renkleri</li>
                        <li>Kart arka planları</li>
                        <li>Buton renkleri</li>
                        <li>Border renkleri</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="modern-grid modern-grid--3">
            <div class="modern-card modern-card--service">
                <div class="modern-card__body modern-text-center">
                    <div class="modern-badge modern-badge--primary modern-m-4">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3 class="modern-heading modern-heading--h4">Açık Tema</h3>
                    <p class="modern-text">
                        Beyaz arka plan ve koyu metin renkleri
                    </p>
                </div>
            </div>
            
            <div class="modern-card modern-card--service">
                <div class="modern-card__body modern-text-center">
                    <div class="modern-badge modern-badge--secondary modern-m-4">
                        <i class="fas fa-moon"></i>
                    </div>
                    <h3 class="modern-heading modern-heading--h4">Koyu Tema</h3>
                    <p class="modern-text">
                        Koyu arka plan ve açık metin renkleri
                    </p>
                </div>
            </div>
            
            <div class="modern-card modern-card--service">
                <div class="modern-card__body modern-text-center">
                    <div class="modern-badge modern-badge--accent modern-m-4">
                        <i class="fas fa-toggle-on"></i>
                    </div>
                    <h3 class="modern-heading modern-heading--h4">Test Durumu</h3>
                    <p class="modern-text" id="testStatus">
                        Test başlatılıyor...
                    </p>
                </div>
            </div>
        </div>
        
        <div class="modern-card modern-m-8">
            <div class="modern-card__body">
                <h2 class="modern-heading modern-heading--h3">JavaScript Konsol Çıktısı</h2>
                <p class="modern-text">
                    Tarayıcınızın geliştirici araçlarını açın (F12) ve Console sekmesini kontrol edin. 
                    Tema değiştirme butonuna tıkladığınızda aşağıdaki mesajları görmelisiniz:
                </p>
                <div class="modern-text modern-text--small modern-text--muted">
                    <code>
                        ✅ Theme toggle event listener added<br>
                        ✅ Theme icon updated to: [theme]<br>
                        ✅ Theme icon animated to: [theme]
                    </code>
                </div>
            </div>
        </div>
        
        <div class="modern-text-center modern-m-8">
            <button id="themeToggle" class="modern-btn modern-btn--primary modern-m-4">
                <i class="fas fa-moon" id="themeIcon"></i>
                Tema Değiştir
            </button>
            <a href="index.php" class="modern-btn modern-btn--outline modern-m-4">
                <i class="fas fa-home me-2"></i>
                Ana Sayfaya Dön
            </a>
        </div>
    </div>
</section>

<script>
// Test script for theme functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('🧪 Theme test page loaded');
    
    // Update current theme display
    function updateCurrentTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const themeDisplay = document.getElementById('currentTheme');
        const testStatus = document.getElementById('testStatus');
        
        if (themeDisplay) {
            themeDisplay.textContent = currentTheme;
        }
        
        if (testStatus) {
            testStatus.innerHTML = `Mevcut tema: <strong>${currentTheme}</strong>`;
        }
        
        console.log('🎨 Current theme:', currentTheme);
    }
    
    // Listen for theme changes
    window.addEventListener('themeChanged', function(event) {
        console.log('🎨 Theme changed event:', event.detail);
        updateCurrentTheme();
    });
    
    // Initial theme update
    updateCurrentTheme();
    
    // Check if theme elements exist
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    
    console.log('🔍 Theme toggle button found:', !!themeToggle);
    console.log('🔍 Theme icon found:', !!themeIcon);
    
    if (!themeToggle) {
        console.error('❌ Theme toggle button not found!');
    }
    
    if (!themeIcon) {
        console.error('❌ Theme icon not found!');
    }
    
    // Test theme toggle functionality
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            console.log('🖱️ Theme toggle clicked');
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
