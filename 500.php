<?php
// NextCode Group - 500 Error Page
// Production Environment Configuration

// Define secure access constant
define('SECURE_ACCESS', true);

// Include API router for handling API requests
require_once 'api-router.php';

// Database bağlantısını include et
require_once 'config/database.php';

// Security configuration include et
require_once 'config/security.php';

// Session başlat
session_start();

// Set 500 header
http_response_code(500);

// Page variables
$page_title = "Server Xətası - NextCode Group";
$meta_description = "Daxili server xətası baş verdi. Zəhmət olmasa bir az sonra yenidən cəhd edin.";
$current_page = '500';

// Include header
require_once 'includes/header.php';
?>

    <!-- 500 Error Section -->
    <section class="modern-section modern-section--hero main-content">
        <div class="modern-container">
            <div class="modern-text-center">
                <div class="modern-badge modern-badge--outline modern-m-4">
                    <i class="fas fa-server me-2"></i>Server Xətası
                </div>
                <h1 class="modern-heading modern-heading--xl modern-m-6">500</h1>
                <h2 class="modern-heading modern-heading--lg modern-m-4">Server Xətası</h2>
                <p class="modern-text modern-text--md modern-m-6">
                    Üzr istəyirik, server tərəfində texniki problem yaranıb.
                </p>
                
                <div class="modern-card modern-card--glass modern-m-6">
                    <div class="modern-card__body">
                        <h4 class="modern-heading modern-heading--md modern-m-4">
                            <i class="fas fa-info-circle me-2"></i>Mümkün həll yolları:
                        </h4>
                        <ul class="modern-list modern-list--unstyled modern-m-4">
                            <li class="modern-list__item">
                                <i class="fas fa-check-circle modern-text--success me-2"></i>
                                Bir neçə dəqiqə gözləyib yenidən cəhd edin
                            </li>
                            <li class="modern-list__item">
                                <i class="fas fa-check-circle modern-text--success me-2"></i>
                                Problemin davam etməsi halında administrator ilə əlaqə saxlayın
                            </li>
                            <li class="modern-list__item">
                                <i class="fas fa-check-circle modern-text--success me-2"></i>
                                Əgər ilk dəfə sayta daxil olursunuzsa, verilənlər bazası quraşdırılmamış ola bilər
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="modern-flex modern-justify-center modern-gap-4 modern-m-8">
                    <a href="setup.php" class="modern-btn modern-btn--warning">
                        <i class="fas fa-cog me-2"></i>
                        Database Quraşdırması
                    </a>
                    <a href="index.php" class="modern-btn modern-btn--primary">
                        <i class="fas fa-home me-2"></i>
                        Ana Səhifə
                    </a>
                    <button onclick="history.back()" class="modern-btn modern-btn--outline">
                        <i class="fas fa-arrow-left me-2"></i>
                        Geri Qayıt
                    </button>
                </div>
            </div>
        </div>
    </section>

<?php
// Include footer
require_once 'includes/footer.php';
?>