<?php
// Test file for readability improvements

// Define secure access constant
define('SECURE_ACCESS', true);

// Set page variables
$page_title = 'Okunabilirlik Testi - NextCode Group';
$meta_description = 'Metin okunabilirlik iyileştirmelerinin test edilmesi için oluşturulmuş test sayfası';
$current_page = 'test';

require_once 'includes/header.php';
?>

<section class="modern-section modern-section--light">
    <div class="modern-container">
        <div class="modern-text-center modern-m-8">
            <h1 class="modern-heading modern-heading--h1 modern-m-6">
                Yazı Okunabilirlik Testi
            </h1>
            <p class="modern-text modern-text--lead modern-m-6">
                Bu sayfa yazı okunabilirliği iyileştirmelerini test etmek için oluşturulmuştur.
            </p>
        </div>
        
        <div class="modern-grid modern-grid--2">
            <div class="modern-card">
                <div class="modern-card__body">
                    <h2 class="modern-heading modern-heading--h3">Başlık Testi</h2>
                    <p class="modern-text">
                        Bu paragraf yazı okunabilirliğini test etmek için yazılmıştır. 
                        Font boyutu, satır yüksekliği ve harf aralığı optimizasyonlarını 
                        kontrol edebilirsiniz. İnter font düzgün yükleniyor mu ve 
                        metinler net görünüyor mu?
                    </p>
                    <p class="modern-text modern-text--small">
                        Küçük metin testi: Bu metin daha küçük boyutta yazılmıştır 
                        ancak yine de okunabilir olmalıdır.
                    </p>
                </div>
            </div>
            
            <div class="modern-card">
                <div class="modern-card__body">
                    <h2 class="modern-heading modern-heading--h3">Renk Kontrast Testi</h2>
                    <p class="modern-text">
                        Bu bölümde renk kontrastını test edebilirsiniz. Metin arka plan 
                        ile yeterli kontrasta sahip mi? Hem açık hem koyu tema için 
                        test edin.
                    </p>
                    <div class="modern-text modern-text--muted">
                        Muted text: Bu metin daha soluk renkte görünmelidir.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modern-grid modern-grid--3">
            <div class="modern-card modern-card--service">
                <div class="modern-card__body modern-text-center">
                    <div class="modern-badge modern-badge--primary modern-m-4">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="modern-heading modern-heading--h4">Mobil Test</h3>
                    <p class="modern-text">
                        Mobil cihazlarda yazılar yeterince büyük mü? 
                        Satır yüksekliği uygun mu?
                    </p>
                </div>
            </div>
            
            <div class="modern-card modern-card--service">
                <div class="modern-card__body modern-text-center">
                    <div class="modern-badge modern-badge--secondary modern-m-4">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <h3 class="modern-heading modern-heading--h4">Desktop Test</h3>
                    <p class="modern-text">
                        Desktop ekranlarda yazılar net ve okunabilir mi? 
                        Font rendering kalitesi nasıl?
                    </p>
                </div>
            </div>
            
            <div class="modern-card modern-card--service">
                <div class="modern-card__body modern-text-center">
                    <div class="modern-badge modern-badge--accent modern-m-4">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="modern-heading modern-heading--h4">Accessibility Test</h3>
                    <p class="modern-text">
                        Accessibility standartlarına uygun mu? 
                        Yüksek kontrast modunda nasıl görünüyor?
                    </p>
                </div>
            </div>
        </div>
        
        <div class="modern-card modern-m-8">
            <div class="modern-card__body">
                <h2 class="modern-heading modern-heading--h3">Uzun Metin Testi</h2>
                <p class="modern-text">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </p>
                <p class="modern-text">
                    Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.
                </p>
                <blockquote class="modern-quote">
                    Bu bir alıntı metnidir. Font rendering ve satır yüksekliği 
                    burada da doğru çalışıyor mu?
                </blockquote>
            </div>
        </div>
        
        <div class="modern-text-center modern-m-8">
            <button id="themeToggle" class="modern-btn modern-btn--primary modern-m-4">
                <i class="fas fa-moon me-2"></i>
                Tema Değiştir
            </button>
            <a href="index.php" class="modern-btn modern-btn--outline modern-m-4">
                <i class="fas fa-home me-2"></i>
                Ana Sayfaya Dön
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
