<?php
// Font Fix Test - Canlı Site Kontrolü
define('SECURE_ACCESS', true);

// Set page variables
$page_title = 'Font Fix Test - NextCode Group';
$meta_description = 'Font rendering ve typography iyileştirmelerinin test edilmesi';
$current_page = 'font-test';

require_once 'includes/header.php';
?>

<section class="modern-section modern-section--light">
    <div class="modern-container">
        <div class="modern-text-center modern-m-8">
            <h1 class="modern-heading modern-heading--h1 modern-m-6">Font Rendering Test</h1>
            <p class="modern-text modern-text--lead modern-m-6">Inter font yükleme ve rendering kalitesini test edin.</p>
        </div>

        <div class="modern-card modern-card--elevated modern-p-6">
            <h2 class="modern-heading modern-heading--h2 modern-mb-4">Typography Hierarchy Test</h2>
            
            <h1>Ana Başlık (H1) - Inter Font Test</h1>
            <h2>Alt Başlık (H2) - Font Weight Test</h2>
            <h3>Orta Başlık (H3) - Line Height Test</h3>
            <h4>Küçük Başlık (H4) - Letter Spacing Test</h4>
            
            <p>Bu paragraf Inter font ile yazılmıştır. Font rendering kalitesi, line height, letter spacing ve genel typography hierarchy'yi test etmek için kullanılır. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            
            <p class="modern-text--lead">Bu lead paragraf daha büyük font size ile yazılmıştır. Font loading durumunu ve rendering kalitesini kontrol edebilirsiniz.</p>
            
            <p class="modern-text--small">Bu küçük metin font size testi için kullanılır. Inter font'un tüm weight'leri test edilmelidir.</p>
            
            <div class="modern-mt-6">
                <h3>Font Weights Test</h3>
                <p style="font-weight: 300;">Font Weight 300 - Light</p>
                <p style="font-weight: 400;">Font Weight 400 - Regular</p>
                <p style="font-weight: 500;">Font Weight 500 - Medium</p>
                <p style="font-weight: 600;">Font Weight 600 - Semi Bold</p>
                <p style="font-weight: 700;">Font Weight 700 - Bold</p>
                <p style="font-weight: 800;">Font Weight 800 - Extra Bold</p>
            </div>
            
            <div class="modern-mt-6">
                <h3>Font Loading Status</h3>
                <div id="font-status" class="modern-text">
                    <p>Font yükleme durumu kontrol ediliyor...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Font loading status check
document.addEventListener('DOMContentLoaded', function() {
    const fontStatus = document.getElementById('font-status');
    
    function checkFontStatus() {
        if (document.fonts && document.fonts.check) {
            const interLoaded = document.fonts.check('16px Inter');
            const status = interLoaded ? 
                '<p style="color: green;">✅ Inter font başarıyla yüklendi</p>' :
                '<p style="color: orange;">⚠️ Inter font henüz yüklenmedi, fallback kullanılıyor</p>';
            fontStatus.innerHTML = status;
        } else {
            fontStatus.innerHTML = '<p style="color: blue;">ℹ️ Font API desteklenmiyor, manuel kontrol gerekli</p>';
        }
    }
    
    // Check immediately
    checkFontStatus();
    
    // Check after font loading
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(() => {
            checkFontStatus();
        });
    }
    
    // Check after 3 seconds
    setTimeout(checkFontStatus, 3000);
});
</script>

<?php require_once 'includes/footer.php'; ?>
