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

// Görsel yönetimi fonksiyonları
function getImageUrl($image_key, $default_url = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT image_url FROM site_images WHERE image_key = ? AND is_active = 1");
        $stmt->execute([$image_key]);
        $result = $stmt->fetch();
        
        return $result ? $result['image_url'] : $default_url;
    } catch (Exception $e) {
        error_log('Error getting image URL: ' . $e->getMessage());
        return $default_url;
    }
}

function getImageAlt($image_key, $default_alt = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT image_alt FROM site_images WHERE image_key = ? AND is_active = 1");
        $stmt->execute([$image_key]);
        $result = $stmt->fetch();
        
        return $result ? $result['image_alt'] : $default_alt;
    } catch (Exception $e) {
        error_log('Error getting image alt: ' . $e->getMessage());
        return $default_alt;
    }
}

function getImageTitle($image_key, $default_title = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT image_title FROM site_images WHERE image_key = ? AND is_active = 1");
        $stmt->execute([$image_key]);
        $result = $stmt->fetch();
        
        return $result ? $result['image_title'] : $default_title;
    } catch (Exception $e) {
        error_log('Error getting image title: ' . $e->getMessage());
        return $default_title;
    }
}

function getImageTag($image_key, $default_url = '', $default_alt = '', $attributes = []) {
    $url = getImageUrl($image_key, $default_url);
    $alt = getImageAlt($image_key, $default_alt);
    $title = getImageTitle($image_key, $alt);
    
    if (empty($url)) {
        return '';
    }
    
    $attr_string = '';
    foreach ($attributes as $key => $value) {
        $attr_string .= " $key=\"$value\"";
    }
    
    return "<img src=\"$url\" alt=\"$alt\" title=\"$title\"$attr_string>";
}

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
                        <h3 class="modern-heading modern-heading--md"><?php echo getTextContent('services_seo_title', 'SEO Optimizasiya'); ?></h3>
                        <div class="modern-badge modern-badge--primary"><?php echo getTextContent('services_seo_price', '300₼-dən başlayaraq'); ?></div>
                    </div>
                    <div class="modern-card__body">
                        <p class="modern-text modern-m-4"><?php echo getTextContent('services_seo_description', 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün peşəkar SEO xidmətləri'); ?></p>
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
                            <span class="modern-text"><?php echo getTextContent('services_seo_duration', '3-6 ay'); ?></span>
                        </div>
                        <button class="modern-btn modern-btn--primary modern-btn--full service-detail-btn" data-service="seo">Ətraflı</button>
                    </div>
                </div>

                <!-- Social Media Service -->
                <div class="modern-card modern-card--service modern-animate--fadeInUp modern-animate--delay-2">
                    <div class="modern-card__header">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md"><?php echo getTextContent('services_social_title', 'Sosial Media İdarəçiliyi'); ?></h3>
                        <div class="modern-badge modern-badge--primary"><?php echo getTextContent('services_social_price', '250₼-dən başlayaraq'); ?></div>
                    </div>
                    <div class="modern-card__body">
                        <p class="modern-text modern-m-4"><?php echo getTextContent('services_social_description', 'Sosial media platformalarında güclü varlıq yaratmaq və idarə etmək'); ?></p>
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
                            <span class="modern-text"><?php echo getTextContent('services_social_duration', 'Davamlı'); ?></span>
                        </div>
                        <button class="modern-btn modern-btn--primary modern-btn--full service-detail-btn" data-service="social-media">Ətraflı</button>
                    </div>
                </div>

                <!-- Branding Service -->
                <div class="modern-card modern-card--service modern-animate--fadeInUp modern-animate--delay-3">
                    <div class="modern-card__header">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md"><?php echo getTextContent('services_branding_title', 'Brendinq və Dizayn'); ?></h3>
                        <div class="modern-badge modern-badge--primary"><?php echo getTextContent('services_branding_price', '500₼-dən başlayaraq'); ?></div>
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
                        <button class="modern-btn modern-btn--primary modern-btn--full service-detail-btn" data-service="branding">Ətraflı</button>
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
                        <button class="modern-btn modern-btn--primary modern-btn--full service-detail-btn" data-service="advertising">Ətraflı</button>
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
                        <button class="modern-btn modern-btn--primary modern-btn--full service-detail-btn" data-service="web-development">Ətraflı</button>
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
                        <button class="modern-btn modern-btn--primary modern-btn--full service-detail-btn" data-service="email-marketing">Ətraflı</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="modern-section">
        <div class="modern-container">
            <div class="modern-text-center modern-m-8">
                <h2 class="modern-heading modern-heading--lg"><?php echo getTextContent('services_process_title', 'İş Prosesimiz'); ?></h2>
            </div>
            <div class="modern-grid modern-grid--4-cols modern-grid--gap-lg">
                <div class="modern-card modern-card--process modern-animate--fadeInUp modern-animate--delay-1">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-badge modern-badge--primary modern-m-4">01</div>
                        <h3 class="modern-heading modern-heading--md"><?php echo getTextContent('services_process_step1_title', 'Analiz və Planlaşdırma'); ?></h3>
                        <p class="modern-text"><?php echo getTextContent('services_process_step1_desc', 'Biznesinizi və hədəflərinizi dərindən analiz edirik'); ?></p>
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
                <h2 class="modern-heading modern-heading--lg"><?php echo getTextContent('services_cta_title', 'Layihənizi Müzakirə Edək'); ?></h2>
                <p class="modern-text modern-text--md modern-m-6"><?php echo getTextContent('services_cta_subtitle', 'Biznesiniz üçün ən uyğun həlləri birlikdə tapaq'); ?></p>
                <div class="modern-flex modern-justify-center modern-gap-4">
                    <a href="contact.php" class="modern-btn modern-btn--primary"><?php echo getTextContent('services_cta_consultation_text', 'Pulsuz Məsləhət'); ?></a>
                    <a href="portfolio.php" class="modern-btn modern-btn--outline"><?php echo getTextContent('services_cta_portfolio_text', 'Portfolio'); ?></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Detail Modal -->
    <div class="modal fade" id="serviceDetailModal" tabindex="-1" aria-labelledby="serviceDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <div class="modal-body">
                    <div class="service-modal-content">
                        <div class="service-modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="service-modal-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="service-modal-info">
                                <h3 id="modal-service-title">SEO Optimizasiya</h3>
                                <div class="service-modal-price" id="modal-service-price">300₼-dən başlayaraq</div>
                            </div>
                        </div>
                        
                        <div class="service-modal-body">
                            <div class="service-modal-description">
                                <p id="modal-service-description">Axtarış sistemlərində yüksək reytinq əldə etmək üçün peşəkar SEO xidmətləri</p>
                            </div>
                            
                            <div class="service-modal-section service-modal-features">
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
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Müddət: <span id="modal-service-duration">3-6 ay</span></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span>Komanda: 2-3 mütəxəssis</span>
                                </div>
                            </div>
                            
                            <div class="service-modal-section service-modal-benefits">
                                <h4>Gözlənilən Nəticələr:</h4>
                                <div id="modal-service-benefits">
                                    <div class="benefit-item">
                                        <i class="fas fa-arrow-up"></i>
                                        <span>Axtarış nəticələrində yüksək mövqe</span>
                                    </div>
                                    <div class="benefit-item">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Orqanik trafik artımı</span>
                                    </div>
                                    <div class="benefit-item">
                                        <i class="fas fa-eye"></i>
                                        <span>Brend görünürlüyünün artması</span>
                                    </div>
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

    <style>
        /* Modern Service Modal Styles */
        .service-modal-content {
            padding: 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 20px;
            overflow: hidden;
        }
        
        .service-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .service-modal-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%) translateY(-100%) rotate(30deg); }
            50% { transform: translateX(100%) translateY(100%) rotate(30deg); }
        }
        
        .service-modal-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 32px;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 2;
        }
        
        .service-modal-info h3 {
            margin: 0 0 10px 0;
            color: white;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            z-index: 2;
        }
        
        .service-modal-price {
            font-size: 20px;
            color: rgba(255,255,255,0.9);
            font-weight: 600;
            background: rgba(255,255,255,0.1);
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-block;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 2;
        }
        
        .service-modal-body {
            padding: 40px 30px;
        }
        
        .service-modal-description {
            margin-bottom: 30px;
            font-size: 17px;
            line-height: 1.7;
            color: #4a5568;
            text-align: center;
            font-weight: 400;
        }
        
        .service-modal-section {
            margin-bottom: 35px;
        }
        
        .service-modal-section h4 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 700;
            position: relative;
            padding-left: 15px;
        }
        
        .service-modal-section h4::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        
        .service-modal-features ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .service-modal-features li {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border-radius: 12px;
            color: #4a5568;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .service-modal-features li:hover {
            transform: translateX(5px);
            border-left-color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);
        }
        
        .service-modal-features li i {
            color: #48bb78;
            margin-right: 15px;
            font-size: 16px;
            width: 20px;
            text-align: center;
        }
        
        .service-modal-details {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            color: white;
        }
        
        .detail-item i {
            margin-right: 12px;
            font-size: 18px;
            width: 24px;
            text-align: center;
            opacity: 0.9;
        }
        
        .detail-item span {
            font-weight: 500;
        }
        
        .service-modal-benefits {
            background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #9ae6b4;
        }
        
        .service-modal-benefits h4 {
            color: #22543d;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 700;
        }
        
        .benefit-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            color: #22543d;
            font-weight: 500;
        }
        
        .benefit-item i {
            margin-right: 15px;
            font-size: 18px;
            width: 24px;
            text-align: center;
            color: #38a169;
        }
        
        /* Modal Animation */
        .modal.fade .modal-dialog {
            transform: scale(0.8) translateY(-50px);
            transition: all 0.3s ease;
        }
        
        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
        }
        
        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        
        .modal-header {
            border: none;
            padding: 0;
        }
        
        .modal-body {
            padding: 0;
        }
        
        .modal-footer {
            border: none;
            padding: 25px 30px;
            background: #f8f9fa;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .modal-footer .btn {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .modal-footer .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .modal-footer .btn-secondary {
            background: #e2e8f0;
            border: none;
            color: #4a5568;
        }
        
        .modal-footer .btn-secondary:hover {
            background: #cbd5e0;
            transform: translateY(-2px);
        }
        
        /* Close Button */
        .btn-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            z-index: 10;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .btn-close:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .service-modal-header {
                padding: 25px 20px;
            }
            
            .service-modal-body {
                padding: 30px 20px;
            }
            
            .service-modal-features ul {
                grid-template-columns: 1fr;
            }
            
            .service-modal-details {
                grid-template-columns: 1fr;
            }
            
            .modal-footer {
                flex-direction: column;
                padding: 20px;
            }
        }
        
        /* Modern Button Styles */
        .modern-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 14px;
        }
        
        .modern-btn--primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .modern-btn--primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .modern-btn--full {
            width: 100%;
        }
        
        /* Service Card Enhancements */
        .modern-card--service {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .modern-card--service:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .modern-card__header {
            padding: 25px 25px 15px;
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .modern-card__icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 24px;
        }
        
        .modern-card__body {
            padding: 20px 25px 25px;
        }
        
        .modern-badge--primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
    
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