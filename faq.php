<?php
// NextCode Group - FAQ Səhifəsi
// Production Environment Configuration

// Define secure access constant
define('SECURE_ACCESS', true);

// Include API router for handling API requests
require_once 'api-router.php';

// Database bağlantısını include et
require_once 'config/database.php';

// Security configuration include et
require_once 'config/security.php';

// Error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Include page functions
require_once 'includes/page_functions.php';
require_once 'includes/content_helper.php';

// Dynamic content variables
$page_title = getTextContent('faq_page_title', 'FAQ - NextCode Group');
$meta_description = getTextContent('faq_meta_description', 'Frequently asked questions and answers about NextCode Group. Information about our digital marketing services.');
$current_page = 'faq';

// FAQ data (dynamic from database)
try {
    if (tableExists('faq')) {
        $faq_query = $pdo->prepare("SELECT * FROM faq WHERE is_active = 1 ORDER BY sort_order ASC");
        $faq_query->execute();
        $faq_items = $faq_query->fetchAll();
    } else {
        $faq_items = [];
    }
} catch (PDOException $e) {
    error_log('FAQ query failed: ' . $e->getMessage());
    $faq_items = [];
}

// FAQ categories
try {
    if (tableExists('faq_categories')) {
        $categories_query = $pdo->prepare("SELECT * FROM faq_categories WHERE is_active = 1 ORDER BY name ASC");
        $categories_query->execute();
        $faq_categories = $categories_query->fetchAll();
    } else {
        $faq_categories = [];
    }
} catch (PDOException $e) {
    error_log('FAQ categories query failed: ' . $e->getMessage());
    $faq_categories = [];
}
// Include header
require_once 'includes/header.php';
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4"><?php echo getTextContent('faq_header_title', 'Tez-tez Verilən Suallar'); ?></h1>
                    <p class="lead mb-0"><?php echo getTextContent('faq_header_subtitle', 'Ən çox soruşulan suallar və cavablar'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                    Xidmətləriniz hansı sahələri əhatə edir?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Biz SEO optimizasyonu, sosial media marketinqi, brendinq, reklam kampaniyaları və veb sayt hazırlanması sahələrində xidmət göstəririk.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                    Nəticələri nə vaxt görə bilərəm?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    SEO nəticələri 3-6 ay, sosial media və reklam kampaniyaları isə 1-2 həftə ərzində görünməyə başlayır.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                    Qiymətlər necə müəyyən edilir?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Qiymətlər layihənin mürəkkəbliyi, xidmət növü və müddətinə görə müəyyən edilir. Pulsuz məsləhət üçün bizimlə əlaqə saxlayın.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                    Müqavilə müddəti nə qədərdir?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Minimum müqavilə müddəti 3 aydır. Uzunmüddətli müqavilələr üçün endirim təklif edirik.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5">
                                    Hesabat və analitika təqdim edirsinizmi?
                                </button>
                            </h2>
                            <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Bəli, aylıq detallı hesabatlar və real-time analitika təqdim edirik. Bütün nəticələr şəffaf şəkildə paylaşılır.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
// Include footer
require_once 'includes/footer.php';
?>