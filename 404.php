<?php
// NextCode Group - 404 Error Page
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

// Set 404 header
http_response_code(404);

// Page variables
$page_title = "Səhifə Tapılmadı - NextCode Group";
$meta_description = "Axtardığınız səhifə tapılmadı. NextCode Group-un əsas səhifəsinə qayıdın.";
$current_page = '404';

// Include header
require_once 'includes/header.php';
?>

    <!-- 404 Error Section -->
    <section class="modern-section modern-section--hero main-content">
        <div class="modern-container">
            <div class="modern-text-center">
                <div class="modern-badge modern-badge--outline modern-m-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>Xəta
                </div>
                <h1 class="modern-heading modern-heading--xl modern-m-6">404</h1>
                <h2 class="modern-heading modern-heading--lg modern-m-4">Səhifə Tapılmadı</h2>
                <p class="modern-text modern-text--md modern-m-6">
                    Üzr istəyirik, axtardığınız səhifə mövcud deyil və ya köçürülüb.
                </p>
                <div class="modern-flex modern-justify-center modern-gap-4 modern-m-8">
                    <a href="index.php" class="modern-btn modern-btn--primary">
                        <i class="fas fa-home me-2"></i>
                        Ana Səhifə
                    </a>
                    <a href="contact.php" class="modern-btn modern-btn--outline">
                        <i class="fas fa-envelope me-2"></i>
                        Əlaqə
                    </a>
                </div>
            </div>
        </div>
    </section>

<?php
// Include footer
require_once 'includes/footer.php';
?>