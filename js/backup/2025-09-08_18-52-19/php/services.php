<?php
// NextCode Group - Xidmətlər Səhifəsi
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

// Ensure $pdo variable is defined
if (!isset($pdo)) {
    $pdo = null;
}

// Dynamic content variables
$page_title = getTextContent('services_page_title', 'Services - NextCode Group');
$meta_description = getTextContent('services_meta_description', 'NextCode Group digital marketing services - SEO, social media, branding and advertising campaigns.');
$current_page = 'services';

// Services data (dynamic from database)
try {
    // Check if database connection is available and use tableExists function
    if (!$pdo || !tableExists('services')) {
        $services_list = [];
    } else {
        $services_query = $pdo->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY order_index ASC");
        $services_query->execute();
        $services_list = $services_query->fetchAll();
    }
} catch (PDOException $e) {
    error_log('Services query failed: ' . $e->getMessage());
    $services_list = [];
}

// Service packages/pricing
try {
    // Check if database connection is available and use tableExists function
    if (!$pdo || !tableExists('service_packages')) {
        $service_packages = [];
    } else {
        $packages_query = $pdo->prepare("SELECT * FROM service_packages WHERE is_active = 1 ORDER BY price ASC");
        $packages_query->execute();
        $service_packages = $packages_query->fetchAll();
    }
} catch (PDOException $e) {
    error_log('Packages query failed: ' . $e->getMessage());
    $service_packages = [];
}

// Always ensure we have services data by adding fallback
if (empty($services_list)) {
    $services_list = [
        [
            'id' => 1,
            'title' => 'SEO Optimizasiyası',
            'description' => 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün SEO strategiyaları və texnikaları.',
            'icon' => 'fas fa-search',
            'features' => ['Axtarış analizi', 'Məzmun optimizasiyası', 'Texniki SEO', 'Backlink strategiyası'],
            'price' => '500',
            'duration' => '3-6 ay'
        ],
        [
            'id' => 2,
            'title' => 'Sosial Media Marketinqi',
            'description' => 'Sosial media platformalarında brendinizi gücləndirmək üçün effektiv marketinq strategiyaları.',
            'icon' => 'fas fa-share-alt',
            'features' => ['Platforma idarəetməsi', 'Məzmun yaradıcılığı', 'Auditoriya analizi', 'Reklam kampaniyaları'],
            'price' => '400',
            'duration' => 'Aylıq'
        ],
        [
            'id' => 3,
            'title' => 'Web Dizayn & Development',
            'description' => 'Müasir və responsive web saytlar, e-ticarət həlləri və web tətbiqlər.',
            'icon' => 'fas fa-code',
            'features' => ['Responsive dizayn', 'SEO optimizasiyası', 'Sürət optimizasiyası', 'Təhlükəsizlik'],
            'price' => '800',
            'duration' => '4-8 həftə'
        ],
        [
            'id' => 4,
            'title' => 'Brendinq & Logo Dizaynı',
            'description' => 'Brendinizi fərqləndirən unikal logo və vizual kimlik dizaynı.',
            'icon' => 'fas fa-palette',
            'features' => ['Logo dizaynı', 'Vizual kimlik', 'Brend qaydaları', 'Marketinq materialları'],
            'price' => '300',
            'duration' => '2-4 həftə'
        ],
        [
            'id' => 5,
            'title' => 'PPC Reklam Kampaniyaları',
            'description' => 'Google Ads və sosial media platformalarında hədəflənmiş reklam kampaniyaları.',
            'icon' => 'fas fa-ad',
            'features' => ['Kampaniya planlaması', 'Hədəf auditoriya', 'A/B testləri', 'ROI izləmə'],
            'price' => '600',
            'duration' => 'Aylıq'
        ],
        [
            'id' => 6,
            'title' => 'Content Marketinqi',
            'description' => 'SEO üçün keyfiyyətli məzmun yaradıcılığı və content strategiyası.',
            'icon' => 'fas fa-pen-fancy',
            'features' => ['Məzmun planlaması', 'Blog yazıları', 'İnfografikalar', 'Video məzmun'],
            'price' => '350',
            'duration' => 'Aylıq'
        ]
    ];
}

// Include header
require_once 'includes/header.php';
?>

    <!-- Page Header -->
    <section class="modern-section modern-section--hero">
        <div class="modern-container">
            <div class="modern-text-center">
                <div class="modern-badge modern-badge--outline modern-m-4">
                    <i class="fas fa-cogs me-2"></i>Xidmətlərimiz
                </div>
                <h1 class="modern-heading modern-heading--xl"><?php echo getTextContent('services_header_title', 'Our Services'); ?></h1>
                <p class="modern-text modern-text--lg"><?php echo getTextContent('services_header_subtitle', 'Biznesinizi rəqəmsal dünyada irəli aparmaq üçün geniş xidmət spektri'); ?></p>
            </div>
        </div>
    </section>

    <!-- Services Overview -->
    <section class="modern-section">
        <div class="modern-container">
            <div class="modern-text-center modern-m-8">
                <h2 class="modern-heading modern-heading--lg"><?php echo getTextContent('services_overview_title', 'Nə Təklif Edirik?'); ?></h2>
                <p class="modern-text modern-text--md"><?php echo getTextContent('services_overview_description', 'Müasir rəqəmsal marketinq həlləri ilə biznesinizi yeni zirvələrə çatdırırıq. Hər bir xidmətimiz müştərilərimizin unikal ehtiyaclarına uyğun olaraq fərdiləşdirilir.'); ?></p>
            </div>
        </div>
    </section>

    <!-- Main Services -->
    <section class="modern-section modern-section--light">
        <div class="modern-container">
            <div class="modern-grid modern-grid--3-cols modern-grid--gap-lg" id="services-grid">
                <!-- SEO Service -->
                <div class="modern-card modern-card--service modern-animate--fadeInUp modern-animate--delay-1">
                    <div class="modern-card__header">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">SEO Optimizasiya</h3>
                        <div class="modern-badge modern-badge--primary">300₼-dən başlayaraq</div>
                    </div>
                    <div class="modern-card__body">
                        <p class="modern-text modern-m-4">Axtarış sistemlərində yüksək reytinq əldə etmək üçün peşəkar SEO xidmətləri</p>
                        <div class="modern-m-6">
                            <div class="modern-flex modern-flex-col modern-gap-3">
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Açar söz tədqiqatı</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Texniki SEO audit</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Məzmun optimizasiyası</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Link building</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Aylıq hesabat</span>
                                </div>
                            </div>
                        </div>
                        <div class="modern-flex modern-items-center modern-gap-3 modern-m-4">
                            <i class="fas fa-clock"></i>
                            <span class="modern-text">3-6 ay</span>
                        </div>
                        <button class="modern-btn modern-btn--primary modern-btn--full" data-service="seo">Ətraflı</button>
                    </div>
                </div>

                <!-- Social Media Service -->
                <div class="modern-card modern-card--service modern-animate--fadeInUp modern-animate--delay-2">
                    <div class="modern-card__header">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Sosial Media İdarəçiliyi</h3>
                        <div class="modern-badge modern-badge--primary">250₼-dən başlayaraq</div>
                    </div>
                    <div class="modern-card__body">
                        <p class="modern-text modern-m-4">Sosial media platformalarında güclü varlıq yaratmaq və idarə etmək</p>
                        <div class="modern-m-6">
                            <div class="modern-flex modern-flex-col modern-gap-3">
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Məzmun planlaması</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Post dizaynı</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Cəmiyyət idarəçiliyi</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Reklam kampaniyaları</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Analitika və hesabat</span>
                                </div>
                            </div>
                        </div>
                        <div class="modern-flex modern-items-center modern-gap-3 modern-m-4">
                            <i class="fas fa-clock"></i>
                            <span class="modern-text">Davamlı</span>
                        </div>
                        <button class="modern-btn modern-btn--primary modern-btn--full" data-service="social-media">Ətraflı</button>
                    </div>
                </div>

                <!-- Branding Service -->
                <div class="modern-card modern-card--service modern-animate--fadeInUp modern-animate--delay-3">
                    <div class="modern-card__header">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Brendinq və Dizayn</h3>
                        <div class="modern-badge modern-badge--primary">500₼-dən başlayaraq</div>
                    </div>
                    <div class="modern-card__body">
                        <p class="modern-text modern-m-4">Güclü brend kimliyi yaratmaq və vizual dizayn həlləri</p>
                        <div class="modern-m-6">
                            <div class="modern-flex modern-flex-col modern-gap-3">
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Logo dizaynı</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Brend kimliyi</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Marketinq materialları</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Brend strategiyası</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Dizayn sistemi</span>
                                </div>
                            </div>
                        </div>
                        <div class="modern-flex modern-items-center modern-gap-3 modern-m-4">
                            <i class="fas fa-clock"></i>
                            <span class="modern-text">2-4 həftə</span>
                        </div>
                        <button class="modern-btn modern-btn--primary modern-btn--full" data-service="branding">Ətraflı</button>
                    </div>
                </div>

                <!-- Advertising Service -->
                <div class="modern-card modern-card--service modern-animate--fadeInUp modern-animate--delay-4">
                    <div class="modern-card__header">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Reklam Kampaniyaları</h3>
                        <div class="modern-badge modern-badge--primary">400₼-dən başlayaraq</div>
                    </div>
                    <div class="modern-card__body">
                        <p class="modern-text modern-m-4">Google Ads, Facebook Ads və digər platformalarda effektiv reklam</p>
                        <div class="modern-m-6">
                            <div class="modern-flex modern-flex-col modern-gap-3">
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Kampaniya strategiyası</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Hədəf auditoriya</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Reklam yaradılması</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">A/B testləri</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">ROI optimizasiyası</span>
                                </div>
                            </div>
                        </div>
                        <div class="modern-flex modern-items-center modern-gap-3 modern-m-4">
                            <i class="fas fa-clock"></i>
                            <span class="modern-text">1-3 ay</span>
                        </div>
                        <button class="modern-btn modern-btn--primary modern-btn--full" data-service="advertising">Ətraflı</button>
                    </div>
                </div>

                <!-- Web Development Service -->
                <div class="modern-card modern-card--service modern-animate--fadeInUp modern-animate--delay-5">
                    <div class="modern-card__header">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-code"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Veb Sayt Hazırlanması</h3>
                        <div class="modern-badge modern-badge--primary">800₼-dən başlayaraq</div>
                    </div>
                    <div class="modern-card__body">
                        <p class="modern-text modern-m-4">Müasir və funksional veb saytların hazırlanması</p>
                        <div class="modern-m-6">
                            <div class="modern-flex modern-flex-col modern-gap-3">
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Responsive dizayn</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">CMS inteqrasiyası</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">E-commerce həlləri</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">SEO optimizasiya</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Texniki dəstək</span>
                                </div>
                            </div>
                        </div>
                        <div class="modern-flex modern-items-center modern-gap-3 modern-m-4">
                            <i class="fas fa-clock"></i>
                            <span class="modern-text">2-8 həftə</span>
                        </div>
                        <button class="modern-btn modern-btn--primary modern-btn--full" data-service="web-development">Ətraflı</button>
                    </div>
                </div>

                <!-- Email Marketing Service -->
                <div class="modern-card modern-card--service modern-animate--fadeInUp modern-animate--delay-6">
                    <div class="modern-card__header">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Email Marketinq</h3>
                        <div class="modern-badge modern-badge--primary">200₼-dən başlayaraq</div>
                    </div>
                    <div class="modern-card__body">
                        <p class="modern-text modern-m-4">Effektiv email kampaniyaları və müştəri əlaqələri</p>
                        <div class="modern-m-6">
                            <div class="modern-flex modern-flex-col modern-gap-3">
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Email dizaynı</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Avtomatlaşdırma</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Seqmentasiya</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">A/B testləri</span>
                                </div>
                                <div class="modern-flex modern-items-center modern-gap-3">
                                    <i class="fas fa-check-circle modern-text--success"></i>
                                    <span class="modern-text">Performans analizi</span>
                                </div>
                            </div>
                        </div>
                        <div class="modern-flex modern-items-center modern-gap-3 modern-m-4">
                            <i class="fas fa-clock"></i>
                            <span class="modern-text">Davamlı</span>
                        </div>
                        <button class="modern-btn modern-btn--primary modern-btn--full" data-service="email-marketing">Ətraflı</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="modern-section">
        <div class="modern-container">
            <div class="modern-text-center modern-m-8">
                <h2 class="modern-heading modern-heading--lg">İş Prosesimiz</h2>
            </div>
            <div class="modern-grid modern-grid--4-cols modern-grid--gap-lg">
                <div class="modern-card modern-card--process modern-animate--fadeInUp modern-animate--delay-1">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-badge modern-badge--primary modern-m-4">01</div>
                        <h3 class="modern-heading modern-heading--md">Analiz və Planlaşdırma</h3>
                        <p class="modern-text">Biznesinizi və hədəflərinizi dərindən analiz edirik</p>
                    </div>
                </div>
                <div class="modern-card modern-card--process modern-animate--fadeInUp modern-animate--delay-2">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-badge modern-badge--primary modern-m-4">02</div>
                        <h3 class="modern-heading modern-heading--md">Strategiya Hazırlanması</h3>
                        <p class="modern-text">Fərdi strategiya və yol xəritəsi yaradırıq</p>
                    </div>
                </div>
                <div class="modern-card modern-card--process modern-animate--fadeInUp modern-animate--delay-3">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-badge modern-badge--primary modern-m-4">03</div>
                        <h3 class="modern-heading modern-heading--md">İcra və Tətbiq</h3>
                        <p class="modern-text">Planı həyata keçirir və nəticələri izləyirik</p>
                    </div>
                </div>
                <div class="modern-card modern-card--process modern-animate--fadeInUp modern-animate--delay-4">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-badge modern-badge--primary modern-m-4">04</div>
                        <h3 class="modern-heading modern-heading--md">Optimizasiya</h3>
                        <p class="modern-text">Nəticələri analiz edib davamlı təkmilləşdiririk</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="modern-section modern-section--light">
        <div class="modern-container">
            <div class="modern-text-center modern-m-8">
                <h2 class="modern-heading modern-heading--lg">Niyə Bizi Seçməlisiniz?</h2>
            </div>
            <div class="modern-grid modern-grid--4-cols modern-grid--gap-md">
                <div class="modern-card modern-card--feature modern-animate--fadeInUp modern-animate--delay-1">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-award"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Təcrübəli Komanda</h3>
                        <p class="modern-text">5+ il təcrübəyə malik mütəxəssislər komandası</p>
                    </div>
                </div>
                <div class="modern-card modern-card--feature modern-animate--fadeInUp modern-animate--delay-2">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Sübut Edilmiş Nəticələr</h3>
                        <p class="modern-text">250+ müştəri və 500+ uğurlu layihə</p>
                    </div>
                </div>
                <div class="modern-card modern-card--feature modern-animate--fadeInUp modern-animate--delay-3">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Vaxtında Çatdırılma</h3>
                        <p class="modern-text">Bütün layihələri müəyyən vaxtda tamamlayırıq</p>
                    </div>
                </div>
                <div class="modern-card modern-card--feature modern-animate--fadeInUp modern-animate--delay-4">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">24/7 Dəstək</h3>
                        <p class="modern-text">Həftənin 7 günü, günün 24 saatı dəstək</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="modern-section modern-section--dark">
        <div class="modern-container">
            <div class="modern-text-center">
                <h2 class="modern-heading modern-heading--lg">Layihənizi Müzakirə Edək</h2>
                <p class="modern-text modern-text--md modern-m-6">Biznesiniz üçün ən uyğun həlləri birlikdə tapaq</p>
                <div class="modern-flex modern-justify-center modern-gap-4">
                    <a href="contact.php" class="modern-btn modern-btn--primary">Pulsuz Məsləhət</a>
                    <a href="portfolio.php" class="modern-btn modern-btn--outline">Portfolio</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Detail Modal -->
    <div class="modal fade" id="serviceDetailModal" tabindex="-1" aria-labelledby="serviceDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceDetailModalLabel">Xidmət Təfərrüatları</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="service-modal-content">
                        <div class="service-modal-header">
                            <div class="service-modal-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="service-modal-info">
                                <h3 id="modal-service-title">SEO Optimizasiya</h3>
                                <div class="service-modal-price" id="modal-service-price">300₼-dən başlayaraq</div>
                            </div>
                        </div>
                        <div class="service-modal-description">
                            <p id="modal-service-description">Axtarış sistemlərində yüksək reytinq əldə etmək üçün peşəkar SEO xidmətləri</p>
                        </div>
                        <div class="service-modal-features">
                            <h4>Xidmət Daxilində:</h4>
                            <ul id="modal-service-features">
                                <li><i class="fas fa-check"></i> Açar söz tədqiqatı</li>
                                <li><i class="fas fa-check"></i> Texniki SEO audit</li>
                                <li><i class="fas fa-check"></i> Məzmun optimizasiyası</li>
                                <li><i class="fas fa-check"></i> Link building</li>
                                <li><i class="fas fa-check"></i> Aylıq hesabat</li>
                            </ul>
                        </div>
                        <div class="service-modal-details">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <i class="fas fa-clock"></i>
                                        <span>Müddət: <span id="modal-service-duration">3-6 ay</span></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <i class="fas fa-users"></i>
                                        <span>Komanda: 2-3 mütəxəssis</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="service-modal-benefits">
                            <h4>Gözlənilən Nəticələr:</h4>
                            <div id="modal-service-benefits">
                                <div class="benefit-item">
                                    <i class="fas fa-arrow-up text-success"></i>
                                    <span>Axtarış nəticələrində yüksək mövqe</span>
                                </div>
                                <div class="benefit-item">
                                    <i class="fas fa-chart-line text-success"></i>
                                    <span>Orqanik trafik artımı</span>
                                </div>
                                <div class="benefit-item">
                                    <i class="fas fa-eye text-success"></i>
                                    <span>Brend görünürlüyünün artması</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <a href="contact.php" class="btn btn-primary">İndi Sifariş Et</a>
                </div>
            </div>
        </div>
    </div>

    <script src="js/services.js"></script>
    <script>
        // Animasyon sistemi
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.3,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, observerOptions);
            
            // Tüm fade-up bölümlerini gözlemle
            const sections = document.querySelectorAll('.fade-up');
            sections.forEach(section => {
                observer.observe(section);
            });
        });
    </script>

<?php
// Include footer
require_once 'includes/footer.php';
?>