<?php
// NextCode Group - Home Page
// Production Environment Configuration

// Define secure access constant
define('SECURE_ACCESS', true);

// Include API router for handling API requests
require_once 'api-router.php';

// Database bağlantısını include et
require_once 'config/database.php';

// Security configuration include et
require_once 'config/security.php';

// Page functions include et
require_once 'includes/page_functions.php';

// Content helper include et
require_once 'includes/content_helper.php';

// Session başlat
session_start();

// Error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Get homepage content from database (with error handling)
$homepage = false;
try {
    $homepage = getPageBySlug('home');
} catch (Exception $e) {
    error_log('Error loading homepage: ' . $e->getMessage());
}

// Dynamic content variables with fallbacks
$page_title = $homepage ? $homepage['title'] : 'NextCode Group - Digital Marketing Agency';
$meta_description = $homepage ? $homepage['meta_description'] : 'NextCode Group - Professional digital marketing services in Azerbaijan. SEO, social media, branding and advertising campaigns to grow your business.';
$current_page = 'home';

// Increment page views (with error handling)
if ($homepage) {
    try {
        incrementPageViews($homepage['id']);
    } catch (Exception $e) {
        error_log('Error incrementing page views: ' . $e->getMessage());
    }
}

// Site statistics (dynamic data from database with error handling)
$site_stats = false;
try {
    // Check if table exists first
    if (tableExists('site_statistics')) {
        $stats_query = $pdo->prepare("SELECT * FROM site_statistics WHERE is_active = 1 ORDER BY id DESC LIMIT 1");
        $stats_query->execute();
        $site_stats = $stats_query->fetch();
    }
} catch (Exception $e) {
    error_log('Error loading site statistics: ' . $e->getMessage());
}

// Default values if no data in database or error occurred
if (!$site_stats) {
    $site_stats = [
            'projects_completed' => '500+',
            'client_satisfaction' => '98%',
            'years_experience' => '16+'
        ];
}
// Include header
require_once 'includes/header.php';
?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="modern-section modern-section--hero main-content">
        <div class="modern-container">
            <div class="modern-grid modern-grid--2">
                <div class="modern-animate--fadeInLeft">
                    <div class="modern-badge modern-badge--primary modern-m-4">
                        <i class="fas fa-rocket me-2"></i>
                        Rəqəmsal Transformasiya
                    </div>
                    
                    <h1 class="modern-heading modern-heading--h1 modern-m-6">
                        <?php echo getTextContent('hero_title', 'Biznesinizi Gələcəyə Hazırlayın'); ?>
                    </h1>
                    
                    <p class="modern-text modern-text--lead modern-m-6">
                        <?php echo getTextContent('hero_subtitle', 'NextCode Group ilə rəqəmsal dünyada liderlik edin. AI-powered həllər və innovativ strategiyalarla biznesinizi növbəti səviyyəyə çatdırın.'); ?>
                    </p>
                    
                    <div class="modern-flex modern-flex-wrap modern-m-8">
                        <a href="<?php echo getLinkContent('hero_button_link', 'contact.php'); ?>" class="modern-btn modern-btn--primary modern-btn--lg modern-m-4" id="heroConsultationBtn">
                            <i class="fas fa-arrow-right me-2"></i>
                            <?php echo getTextContent('hero_button_text', 'Pulsuz Məsləhət'); ?>
                        </a>
                        <a href="<?php echo getLinkContent('hero_video_link', 'portfolio.php'); ?>" class="modern-btn modern-btn--outline modern-btn--lg modern-m-4" id="heroPortfolioBtn">
                            <i class="fas fa-play me-2"></i>
                            <?php echo getTextContent('hero_video_text', 'Demo İzlə'); ?>
                        </a>
                    </div>
                    
                    <div class="modern-grid modern-grid--3 modern-m-8">
                        <div class="modern-card modern-card--glass modern-text-center">
                            <div class="modern-card__body">
                                <div class="modern-heading modern-heading--h3 modern-text-light">
                                    <?php echo isset($site_stats['projects_completed']) ? $site_stats['projects_completed'] : '500+'; ?>
                                </div>
                                <div class="modern-text modern-text-light">
                                    <?php echo getTextContent('stat_projects_label', 'Uğurlu Layihə'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="modern-card modern-card--glass modern-text-center">
                            <div class="modern-card__body">
                                <div class="modern-heading modern-heading--h3 modern-text-light">
                                    <?php echo isset($site_stats['client_satisfaction']) ? $site_stats['client_satisfaction'] : '98%'; ?>
                                </div>
                                <div class="modern-text modern-text-light">
                                    <?php echo getTextContent('stat_satisfaction_label', 'Müştəri Məmnuniyyəti'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="modern-card modern-card--glass modern-text-center">
                            <div class="modern-card__body">
                                <div class="modern-heading modern-heading--h3 modern-text-light">
                                    <?php echo isset($site_stats['years_experience']) ? $site_stats['years_experience'] : '16+'; ?>
                                </div>
                                <div class="modern-text modern-text-light">
                                    <?php echo getTextContent('stat_experience_label', 'İl Təcrübə'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
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
    </section>

    <!-- Services Section -->
    <section class="modern-section modern-section--light">
        <div class="modern-container">
            <div class="modern-text-center modern-m-8">
                <h2 class="modern-heading modern-heading--h2 modern-m-6">
                    <?php echo getTextContent('services_title', 'Our Services'); ?>
                </h2>
                <p class="modern-text modern-text--lead modern-m-6">
                    <?php echo getTextContent('services_description', 'Biznesinizin rəqəmsal transformasiyası üçün lazım olan bütün xidmətləri bir yerdə təklif edirik.'); ?>
                </p>
            </div>
            
            <div class="modern-grid modern-grid--3">
                <div class="modern-card modern-animate--fadeInUp modern-animate--delay-1">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-badge modern-badge--primary modern-m-4">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--h4 modern-m-4">SEO Optimizasiya</h3>
                        <p class="modern-text modern-m-6">
                            Axtarış motorlarında yüksək reytinq əldə edin və daha çox müştəri cəlb edin. Google-da ilk səhifədə görünmək üçün peşəkar SEO strategiyaları.
                        </p>
                        <div class="modern-flex modern-flex-wrap modern-justify-center modern-m-6">
                            <span class="modern-badge modern-badge--outline modern-m-2">Açar söz tədqiqi</span>
                            <span class="modern-badge modern-badge--outline modern-m-2">Texniki SEO</span>
                            <span class="modern-badge modern-badge--outline modern-m-2">Məzmun optimallaşdırması</span>
                        </div>
                        <div class="modern-text modern-text--small modern-text-muted modern-m-4">
                            <i class="fas fa-chart-line me-2"></i> Ortalama 300% trafik artışı
                        </div>
                    </div>
                </div>
                
                <div class="modern-card modern-animate--fadeInUp modern-animate--delay-2">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-badge modern-badge--secondary modern-m-4">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--h4 modern-m-4">Sosial Media İdarəetməsi</h3>
                        <p class="modern-text modern-m-6">
                            Sosial şəbəkələrdə brendinizi gücləndirib auditoriya yaradın. Facebook, Instagram, LinkedIn və digər platformlarda peşəkar idarəetmə.
                        </p>
                        <div class="modern-flex modern-flex-wrap modern-justify-center modern-m-6">
                            <span class="modern-badge modern-badge--outline modern-m-2">Məzmun yaradılması</span>
                            <span class="modern-badge modern-badge--outline modern-m-2">Cədvəl planlaması</span>
                            <span class="modern-badge modern-badge--outline modern-m-2">Analitika hesabatları</span>
                        </div>
                        <div class="modern-text modern-text--small modern-text-muted modern-m-4">
                            <i class="fas fa-heart me-2"></i> 85% engagement artışı
                        </div>
                    </div>
                </div>
                
                <div class="modern-card modern-animate--fadeInUp modern-animate--delay-3">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-badge modern-badge--accent modern-m-4">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--h4 modern-m-4">Brendinq və Dizayn</h3>
                        <p class="modern-text modern-m-6">
                            Unikal brend kimliyi yaradıb rəqiblərdən fərqlənin. Loqo dizaynından tam brend strategiyasına qədər bütün xidmətlər.
                        </p>
                        <div class="modern-flex modern-flex-wrap modern-justify-center modern-m-6">
                            <span class="modern-badge modern-badge--outline modern-m-2">Loqo dizaynı</span>
                            <span class="modern-badge modern-badge--outline modern-m-2">Brend kimliyı</span>
                            <span class="modern-badge modern-badge--outline modern-m-2">Marketinq materialları</span>
                        </div>
                        <div class="modern-text modern-text--small modern-text-muted modern-m-4">
                            <i class="fas fa-award me-2"></i> 50+ uğurlu brend layihəsi
                        </div>
                    </div>
                </div>
                <div class="modern-card modern-animate--fadeInUp modern-animate--delay-4">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-badge modern-badge--primary modern-m-4">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--h4 modern-m-4">Rəqəmsal Reklam Kampaniyaları</h3>
                        <p class="modern-text modern-m-6">
                            Google Ads, Facebook Ads və digər platformlarda effektiv reklam strategiyaları ilə satışlarınızı artırın və ROI-nizi maksimallaşdırın.
                        </p>
                        <div class="modern-flex modern-flex-wrap modern-justify-center modern-m-6">
                            <span class="modern-badge modern-badge--outline modern-m-2">Google Ads</span>
                            <span class="modern-badge modern-badge--outline modern-m-2">Facebook Ads</span>
                            <span class="modern-badge modern-badge--outline modern-m-2">ROI optimallaşdırması</span>
                        </div>
                        <div class="modern-text modern-text--small modern-text-muted modern-m-4">
                            <i class="fas fa-dollar-sign me-2"></i> Ortalama 450% ROI artışı
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="modern-section modern-section--light">
        <div class="modern-container">
            <div class="modern-text-center modern-m-8">
                <div class="modern-badge modern-badge--outline modern-m-4">
                    <i class="fas fa-quote-left me-2"></i>Müştəri Rəyləri
                </div>
                <h2 class="modern-heading modern-heading--lg">Müştəri Rəyləri</h2>
                <p class="modern-text modern-text--md">
                    Bizə etibar edən müştərilərimizin uğur hekayələri və təcrübələri. Hər bir layihədə keyfiyyət və peşəkarlığımızla fərqlənirik.
                </p>
            </div>
            <div class="modern-grid modern-grid--3-cols modern-grid--gap-lg">
                <div class="modern-card modern-card--testimonial modern-animate--fadeInUp modern-animate--delay-1">
                    <div class="modern-card__body">
                        <div class="modern-flex modern-items-center modern-gap-4 modern-m-6">
                            <div class="modern-card__image modern-card__image--avatar">
                                <img src="https://trae-api-us.mchost.guru/api/ide/v1/text_to_image?prompt=professional%20azerbaijani%20businessman%20headshot%20portrait%20smiling%20confident%20modern%20office%20background&image_size=square" alt="Əli Məmmədov" class="modern-card__image" loading="lazy">
                            </div>
                            <div class="modern-flex modern-flex-col">
                                <h4 class="modern-heading modern-heading--sm">Əli Məmmədov</h4>
                                <p class="modern-text modern-text--sm">Marketing Meneceri</p>
                                <p class="modern-text modern-text--sm modern-text--muted">ABC Şirkəti</p>
                            </div>
                        </div>
                        <div class="modern-flex modern-gap-1 modern-m-4">
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="fas fa-star modern-text--warning"></i>
                        </div>
                        <blockquote class="modern-text modern-text--md modern-m-4">
                            "NextCode Group ilə işləmək böyük zövq idi. SEO xidmətləri sayəsində saytımızın trafikini 300% artırdıq."
                        </blockquote>
                    </div>
                </div>
                <div class="modern-card modern-card--testimonial modern-animate--fadeInUp modern-animate--delay-2">
                    <div class="modern-card__body">
                        <div class="modern-flex modern-items-center modern-gap-4 modern-m-6">
                            <div class="modern-card__image modern-card__image--avatar">
                                <img src="https://trae-api-us.mchost.guru/api/ide/v1/text_to_image?prompt=professional%20azerbaijani%20businesswoman%20headshot%20portrait%20smiling%20confident%20modern%20office%20background&image_size=square" alt="Leyla Həsənova" class="modern-card__image" loading="lazy">
                            </div>
                            <div class="modern-flex modern-flex-col">
                                <h4 class="modern-heading modern-heading--sm">Leyla Həsənova</h4>
                                <p class="modern-text modern-text--sm">CEO</p>
                                <p class="modern-text modern-text--sm modern-text--muted">XYZ Company</p>
                            </div>
                        </div>
                        <div class="modern-flex modern-gap-1 modern-m-4">
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="fas fa-star modern-text--warning"></i>
                        </div>
                        <blockquote class="modern-text modern-text--md modern-m-4">
                            "Peşəkar komanda və keyfiyyətli xidmət. Sosial media hesablarımızı mükəmməl idarə edirlər."
                        </blockquote>
                    </div>
                </div>
                <div class="modern-card modern-card--testimonial modern-animate--fadeInUp modern-animate--delay-3">
                    <div class="modern-card__body">
                        <div class="modern-flex modern-items-center modern-gap-4 modern-m-6">
                            <div class="modern-card__image modern-card__image--avatar">
                                <img src="https://trae-api-us.mchost.guru/api/ide/v1/text_to_image?prompt=young%20azerbaijani%20entrepreneur%20headshot%20portrait%20creative%20startup%20office%20background&image_size=square" alt="Rəşad Quliyev" class="modern-card__image" loading="lazy">
                            </div>
                            <div class="modern-flex modern-flex-col">
                                <h4 class="modern-heading modern-heading--sm">Rəşad Quliyev</h4>
                                <p class="modern-text modern-text--sm">Sahibkar</p>
                                <p class="modern-text modern-text--sm modern-text--muted">Startup Venture</p>
                            </div>
                        </div>
                        <div class="modern-flex modern-gap-1 modern-m-4">
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="fas fa-star modern-text--warning"></i>
                            <i class="far fa-star modern-text--muted"></i>
                        </div>
                        <blockquote class="modern-text modern-text--md modern-m-4">
                            "Brendinq xidmətləri üçün təşəkkür edirik. Yeni loqomuz və brend kimliyi çox uğurlu oldu."
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Heritage Section -->
    <section class="company-heritage">
        <div class="container-custom">
            <div class="heritage-banner">
                <div class="heritage-info">
                    <div class="heritage-badge">
                        <i class="fas fa-award"></i>
                        <span>2008-dən bəri</span>
                    </div>
                    <h3 class="heritage-title">16 İllik Təcrübə və Güvən</h3>
                    <p class="heritage-text">
                        NextCode Group 2008-ci ildən bu yana rəqəmsal texnologiyalar sahəsində 
                        fasiləsiz xidmət göstərir və müştərilərinə etibarlı həllər təqdim edir.
                    </p>
                    <div class="heritage-achievements">
                        <div class="achievement-item">
                            <i class="fas fa-trophy"></i>
                            <span>Google Partner Sertifikatı</span>
                        </div>
                        <div class="achievement-item">
                            <i class="fas fa-medal"></i>
                            <span>Facebook Marketing Partner</span>
                        </div>
                        <div class="achievement-item">
                            <i class="fas fa-star"></i>
                            <span>98% Müştəri Məmnuniyyəti</span>
                        </div>
                    </div>
                </div>
                <div class="heritage-stats">
                    <div class="heritage-stat">
                        <div class="stat-number">16+</div>
                        <div class="stat-label">İl Təcrübə</div>
                        <div class="stat-description">Rəqəmsal marketinq sahəsində</div>
                    </div>
                    <div class="heritage-stat">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Uğurlu Layihə</div>
                        <div class="stat-description">Müxtəlif sektorlarda</div>
                    </div>
                    <div class="heritage-stat">
                        <div class="stat-number">250+</div>
                        <div class="stat-label">Məmnun Müştəri</div>
                        <div class="stat-description">Azərbaycan və dünyada</div>
                    </div>
                    <div class="heritage-stat">
                        <div class="stat-number">15M+</div>
                        <div class="stat-label">Reklam Büdcəsi</div>
                        <div class="stat-description">İdarə edilən</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories Section -->
    <section class="success-stories">
        <div class="container-custom">
            <div class="section-header">
                <h2 class="section-title">Uğur Hekayələrimiz</h2>
                <p class="section-description">
                    Müştərilərimizin biznesinə əlavə etdiyimiz dəyər və əldə etdikləri nəticələr
                </p>
            </div>
            <div class="success-grid">
                <div class="success-card">
                    <div class="success-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h4 class="success-title">E-ticarət Platformu</h4>
                    <p class="success-description">
                        Yerli geyim brendinin onlayn satış platformunu yaratdıq və 6 ay ərzində satışları 400% artırdıq.
                    </p>
                    <div class="success-metrics">
                        <div class="metric">
                            <span class="metric-value">400%</span>
                            <span class="metric-label">Satış Artışı</span>
                        </div>
                        <div class="metric">
                            <span class="metric-value">2.5x</span>
                            <span class="metric-label">ROI</span>
                        </div>
                    </div>
                </div>
                <div class="success-card">
                    <div class="success-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4 class="success-title">SEO Kampaniyası</h4>
                    <p class="success-description">
                        Turizm şirkətinin Google-da görünürlüyünü artıraraq organik trafikini 8 ay ərzində 350% artırdıq.
                    </p>
                    <div class="success-metrics">
                        <div class="metric">
                            <span class="metric-value">350%</span>
                            <span class="metric-label">Trafik Artışı</span>
                        </div>
                        <div class="metric">
                            <span class="metric-value">#1</span>
                            <span class="metric-label">Google Reytinqi</span>
                        </div>
                    </div>
                </div>
                <div class="success-card">
                    <div class="success-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="success-title">Sosial Media Kampaniyası</h4>
                    <p class="success-description">
                        Restoran zəncirinin sosial media hesablarını idarə edərək follower sayını 10x artırdıq.
                    </p>
                    <div class="success-metrics">
                        <div class="metric">
                            <span class="metric-value">10x</span>
                            <span class="metric-label">Follower Artışı</span>
                        </div>
                        <div class="metric">
                            <span class="metric-value">85%</span>
                            <span class="metric-label">Engagement</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container-custom">
            <div class="cta-content">
                <h2 class="cta-title">
                    Biznesinizi Növbəti Səviyyəyə Çatdırmağa Hazırsınız?
                </h2>
                <p class="cta-description">
                    Pulsuz məsləhət seansı üçün bizimlə əlaqə saxlayın və rəqəmsal strategiyanızı müzakirə edək.
                </p>
                <div class="cta-buttons">
                    <a href="contact.php" class="btn btn-modern btn-white" id="ctaStartBtn">
                        İndi Başlayın
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="pricing.php" class="btn btn-modern btn-outline-white" id="ctaPricingBtn">
                        View Pricing
                    </a>
                </div>
            </div>
        </div>
    </section>

<?php
// Include footer
require_once 'includes/footer.php';
?>