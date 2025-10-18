<?php
// NextCode Group - Qiymətlər Səhifəsi
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

// Dynamic content variables
$page_title = getTextContent('pricing_page_title', 'Pricing - NextCode Group');
$meta_description = getTextContent('pricing_meta_description', 'NextCode Group service prices and packages. Pricing policy for our digital marketing services.');
$current_page = 'pricing';

// Pricing packages data (dynamic from database)
try {
    if (tableExists('pricing_packages')) {
        $pricing_query = $pdo->prepare("SELECT * FROM pricing_packages WHERE is_active = 1 ORDER BY price ASC");
        $pricing_query->execute();
        $pricing_packages = $pricing_query->fetchAll();
    } else {
        $pricing_packages = [];
    }
} catch (PDOException $e) {
    error_log('Pricing query failed: ' . $e->getMessage());
    $pricing_packages = [];
}

// Service features
try {
    if (tableExists('package_features')) {
        $features_query = $pdo->prepare("SELECT * FROM package_features WHERE is_active = 1 ORDER BY sort_order ASC");
        $features_query->execute();
        $package_features = $features_query->fetchAll();
    } else {
        $package_features = [];
    }
} catch (PDOException $e) {
    error_log('Features query failed: ' . $e->getMessage());
    $package_features = [];
}
// Include header
require_once 'includes/header.php';
?>

<!-- Modern Pricing Styles -->
<link rel="stylesheet" href="css/pricing-modern.css">
<link rel="stylesheet" href="css/animations.css">

    <!-- Page Header -->
    <section class="page-header pricing-hero">
        <div class="container">
            <div class="header-content text-center">
                <div class="hero-badge">
                    <i class="fas fa-crown"></i>
                    <span>Premium Paketlər</span>
                </div>
                <h1 class="hero-title"><?php echo getTextContent('pricing_hero_title', 'Biznesiniz üçün Mükəmməl Planı Seçin'); ?></h1>
                <p class="hero-subtitle"><?php echo getTextContent('pricing_hero_subtitle', 'Rəqəmsal marketinq sahəsində uğur qazanmaq üçün ehtiyacınız olan bütün alətlər bir yerdə'); ?></p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Məmnun Müştəri</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">98%</span>
                        <span class="stat-label">Uğur Nisbəti</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Dəstək</span>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- Pricing Toggle -->
    <section class="pricing-toggle-section">
        <div class="container">
            <div class="pricing-toggle-wrapper">
                <div class="toggle-header">
                    <h3>Ödəniş Dövrünü Seçin</h3>
                    <p>İllik planla daha çox qənaət edin</p>
                </div>
                <div class="pricing-toggle">
                    <span class="toggle-label monthly-label">Aylıq</span>
                    <label class="toggle-switch">
                        <input type="checkbox" id="pricingToggle">
                        <span class="slider">
                            <span class="slider-button"></span>
                        </span>
                    </label>
                    <span class="toggle-label yearly-label">İllik 
                        <span class="discount-badge">
                            <i class="fas fa-fire"></i>
                            20% ENDİRİM
                        </span>
                    </span>
                </div>
                <div class="savings-info">
                    <i class="fas fa-info-circle"></i>
                    <span>İllik planla 2 ay pulsuz xidmət əldə edin!</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Limited Time Offer Banner -->
    <div class="limited-offer-banner">
        <div class="container">
            <div class="offer-content">
                <div class="offer-text">
                    <i class="fas fa-fire"></i>
                    <span><strong>Məhdud Müddət Təklifi:</strong> İlk 100 müştəri üçün 30% endirim!</span>
                </div>
                <div class="offer-counter">
                    <span class="counter-label">Qalan yer:</span>
                    <span class="counter-number" id="remainingSpots">23</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Value Proposition Section -->
    <section class="value-proposition-section">
        <div class="container">
            <div class="value-grid">
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Ortalama %300 Artım</h3>
                    <p>Müştərilərimiz ilk 6 ayda ortalama %300 satış artımı görür</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>48 Saat İçində Başlangıç</h3>
                    <p>Hızlı kurulum ve anında sonuç almaya başlayın</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>%100 Risk-Free Garanti</h3>
                    <p>30 gün içinde memnun kalmazsan, paranı geri al</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Ödüllü Ekip</h3>
                    <p>Google ve Facebook sertifikalı uzmanlar</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Plans -->
    <section class="pricing-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Qiymət Planları</h2>
                <p class="section-subtitle">Biznesiniz üçün ən uyğun planı seçin</p>
                <div class="social-proof-counter">
                    <div class="proof-item">
                        <i class="fas fa-users"></i>
                        <span><strong>2,847</strong> aktiv müştəri</span>
                    </div>
                    <div class="proof-item">
                        <i class="fas fa-star"></i>
                        <span><strong>4.9/5</strong> reytinq</span>
                    </div>
                    <div class="proof-item">
                        <i class="fas fa-check-circle"></i>
                        <span><strong>99.9%</strong> müştəri məmnuniyyəti</span>
                    </div>
                </div>
                <div class="testimonial-preview">
                    <div class="testimonial-item">
                        <div class="testimonial-content">
                            <p>"NextCode sayəsində satışlarımız 6 ayda %400 arttı!"</p>
                            <div class="testimonial-author">
                                <strong>Əli Məmmədov</strong>
                                <span>CEO, TechStart</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pricing-grid">
                <!-- Starter Plan -->
                <div class="pricing-card starter-plan">
                    <div class="plan-badge">Ən Populyar</div>
                    <div class="plan-header">
                        <div class="plan-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div class="plan-badge">Başlanğıc</div>
                        <h3 class="plan-title">Starter Pro</h3>
                        <p class="plan-description">Kiçik biznes və startaplar üçün mükəmməl başlanğıc paketi</p>
                    </div>
                    <div class="plan-price">
                        <div class="price-container">
                            <span class="currency">₼</span>
                            <span class="amount monthly-price" data-monthly="299" data-yearly="2390">299</span>
                            <span class="period">/ay</span>
                        </div>
                        <div class="price-note">İllik ödənişdə ₼2,390</div>
                    </div>
                    <div class="plan-features">
                        <div class="features-header">
                            <h4>Daxil olan xidmətlər:</h4>
                        </div>
                        <ul class="features-list">
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>SEO Optimizasyonu</strong>
                                    <span>5 açar söz üçün Google'da ilk sayfa</span>
                                    <div class="feature-benefit">+%150 organik trafik artışı</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Sosyal Medya Yönetimi</strong>
                                    <span>Facebook + Instagram profesyonel içerik</span>
                                    <div class="feature-benefit">Günlük 300+ etkileşim garantisi</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Detaylı Performans Raporu</strong>
                                    <span>Aylıq kapsamlı analiz ve öneriler</span>
                                    <div class="feature-benefit">ROI takibi dahil</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Öncelikli Destek</strong>
                                    <span>24 saat içinde yanıt garantisi</span>
                                    <div class="feature-benefit">Uzman danışman desteği</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Website Optimizasyonu</strong>
                                    <span>Hız ve performans iyileştirmeleri</span>
                                    <div class="feature-benefit">%40 daha hızlı yükleme</div>
                                </div>
                            </li>
                            <li class="feature-item upgrade-hint">
                                <i class="fas fa-arrow-up"></i>
                                <div class="feature-content">
                                    <strong>Reklam Kampaniyaları</strong>
                                    <span>Professional plan'da mevcut</span>
                                    <div class="feature-benefit">₼500 reklam kredisi hediye</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="plan-action">
                        <button class="plan-btn starter-btn" id="basicPlanBtn">
                            <span>İndi Başla</span>
                            <i class="fas fa-rocket"></i>
                        </button>
                        <div class="plan-guarantee">
                            <i class="fas fa-shield-alt"></i>
                            <span>30 gün pul geri qaytarılması</span>
                        </div>
                        <div class="trial-info">
                            <i class="fas fa-gift"></i>
                            <span>14 gün pulsuz sınaq</span>
                        </div>
                    </div>
                </div>

                <!-- Professional Plan -->
                <div class="pricing-card professional-plan popular">
                    <div class="popular-badge">
                        <i class="fas fa-fire"></i>
                        <span>Ən Məşhur</span>
                    </div>
                    <div class="plan-header">
                        <div class="plan-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div class="plan-badge">Peşəkar</div>
                        <h3 class="plan-title">Professional</h3>
                        <p class="plan-description">Böyüyən bizneslər və orta şirkətlər üçün ideal həll</p>
                    </div>
                    <div class="plan-price">
                        <div class="price-container">
                            <span class="currency">₼</span>
                            <span class="amount monthly-price" data-monthly="599" data-yearly="4790">599</span>
                            <span class="period">/ay</span>
                        </div>
                        <div class="price-note">İllik ödənişdə ₼4,790 (₼1,398 qənaət)</div>
                    </div>
                    <div class="plan-features">
                        <div class="features-header">
                            <h4>Daxil olan xidmətlər:</h4>
                        </div>
                        <ul class="features-list">
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>SEO Optimizasyonu</strong>
                                    <span>15 açar söz + rəqabət təhlili</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Sosyal Medya</strong>
                                    <span>4 platform + məzmun yaradılması</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Analitik Hesabat</strong>
                                    <span>Həftəlik detallı hesabat</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Telefon + Email Dəstəyi</strong>
                                    <span>Prioritet dəstək xidməti</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Google Ads</strong>
                                    <span>Kampaniya qurulması və idarəsi</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Brendinq Xidmətləri</strong>
                                    <span>Logo və korporativ kimlik</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Veb Sayt Təhlili</strong>
                                    <span>Detallı performans təhlili</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="plan-action">
                        <button class="plan-btn professional-btn" id="professionalPlanBtn">
                            <span>İndi Başla - 20% Endirim</span>
                            <i class="fas fa-fire"></i>
                        </button>
                        <div class="plan-guarantee">
                            <i class="fas fa-shield-alt"></i>
                            <span>30 gün pul geri qaytarılması</span>
                        </div>
                        <div class="urgency-timer">
                            <i class="fas fa-clock"></i>
                            <span>Endirim 24 saata qədər!</span>
                        </div>
                        <div class="trial-info">
                            <i class="fas fa-gift"></i>
                            <span>14 gün pulsuz sınaq + bonus</span>
                        </div>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="pricing-card enterprise-plan">
                    <div class="enterprise-badge">
                        <i class="fas fa-crown"></i>
                        <span>Premium</span>
                    </div>
                    <div class="plan-header">
                        <div class="plan-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="plan-badge">Korporativ</div>
                        <h3 class="plan-title">Enterprise</h3>
                        <p class="plan-description">Böyük şirkətlər və korporasiyalar üçün tam həll</p>
                    </div>
                    <div class="plan-price">
                        <div class="price-container">
                            <span class="currency">₼</span>
                            <span class="amount monthly-price" data-monthly="999" data-yearly="7990">999</span>
                            <span class="period">/ay</span>
                        </div>
                        <div class="price-note">İllik ödənişdə ₼7,990 (₼4,998 qənaət)</div>
                    </div>
                    <div class="plan-features">
                        <div class="features-header">
                            <h4>Daxil olan xidmətlər:</h4>
                        </div>
                        <ul class="features-list">
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>SEO Optimizasyonu</strong>
                                    <span>Sınırsız açar söz + AI təhlil</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Sosyal Medya</strong>
                                    <span>Bütün platformlar + avtomatlaşdırma</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Analitik Hesabat</strong>
                                    <span>Gündəlik real-time hesabat</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>24/7 VIP Dəstək</strong>
                                    <span>Şəxsi hesab meneceri</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Tam Reklam Paketi</strong>
                                    <span>Google, Facebook, LinkedIn, TikTok</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Tam Brendinq</strong>
                                    <span>Korporativ kimlik + marketinq materialları</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Veb Sayt Təhlili</strong>
                                    <span>Tam optimallaşdırma + A/B test</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="plan-action">
                        <button class="plan-btn enterprise-btn" id="enterprisePlanBtn">
                            <span>Dərhal Əlaqə Saxla</span>
                            <i class="fas fa-crown"></i>
                        </button>
                        <div class="plan-guarantee">
                            <i class="fas fa-shield-alt"></i>
                            <span>Tam zəmanət və dəstək</span>
                        </div>
                        <div class="enterprise-priority">
                            <i class="fas fa-star"></i>
                            <span>VIP müştəri dəstəyi</span>
                        </div>
                        <div class="trial-info">
                            <i class="fas fa-star"></i>
                            <span>30 gün pulsuz sınaq + VIP dəstək</span>
                        </div>
                    </div>
                </div>

                <!-- Custom Plan -->
                <div class="pricing-card custom-plan">
                    <div class="custom-badge">
                        <i class="fas fa-magic"></i>
                        <span>Fərdi Həll</span>
                    </div>
                    <div class="plan-header">
                        <div class="plan-icon">
                            <i class="fas fa-puzzle-piece"></i>
                        </div>
                        <div class="plan-badge">Fərdi</div>
                        <h3 class="plan-title">Custom</h3>
                        <p class="plan-description">Xüsusi tələblər və böyük layihələr üçün fərdi həllər</p>
                    </div>
                    <div class="plan-price">
                        <div class="custom-price-container">
                            <span class="custom-price">Fərdi Qiymət</span>
                            <span class="custom-subtitle">Layihənizə görə hesablanır</span>
                        </div>
                    </div>
                    <div class="plan-features">
                        <div class="features-header">
                            <h4>Daxil olan xidmətlər:</h4>
                        </div>
                        <ul class="features-list">
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Fərdi SEO Strategiyası</strong>
                                    <span>Tam fərdiləşdirilmiş yanaşma</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Xüsusi Sosyal Medya</strong>
                                    <span>Fərdi məzmun strategiyası</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Fərdi Hesabat</strong>
                                    <span>Xüsusi KPI və metrikalar</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>VIP Dəstək</strong>
                                    <span>Xüsusi komanda təyinatı</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Xüsusi İnteqrasiyalar</strong>
                                    <span>API və sistem inteqrasiyaları</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Tam Brendinq Paketi</strong>
                                    <span>Korporativ kimlik + dizayn</span>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <div class="feature-content">
                                    <strong>Strateji Konsultasiya</strong>
                                    <span>C-level məsləhət xidməti</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="plan-action">
                        <button class="plan-btn custom-btn">
                            <span>Əlaqə Saxlayın</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <div class="trial-info">
                            <i class="fas fa-handshake"></i>
                            <span>Pulsuz məsləhət sessiyası</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparison Table -->
    <section class="comparison-section">
        <div class="container">
            <div class="section-header">
                <h2>Paket Karşılaştırması</h2>
                <p>Bütün planların detallı müqayisəsi</p>
                <div class="comparison-actions">
                    <button class="btn btn-outline-primary" id="comparePackages">
                        <i class="fas fa-chart-bar"></i>
                        Detallı Karşılaştırma
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#calculatorModal">
                        <i class="fas fa-calculator"></i>
                        Fiyat Hesaplayıcısı
                    </button>
                </div>
            </div>
            
            <div class="comparison-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Xidmətlər</th>
                            <th class="starter-col">
                                <div class="plan-header-mini">
                                    <i class="fas fa-rocket"></i>
                                    <span>Başlanğıc</span>
                                    <div class="price-mini">₼299/ay</div>
                                </div>
                            </th>
                            <th class="professional-col popular-col">
                                <div class="plan-header-mini">
                                    <i class="fas fa-star"></i>
                                    <span>Peşəkar</span>
                                    <div class="price-mini">₼599/ay</div>
                                    <div class="popular-mini">Ən Populyar</div>
                                </div>
                            </th>
                            <th class="enterprise-col">
                                <div class="plan-header-mini">
                                    <i class="fas fa-building"></i>
                                    <span>Korporativ</span>
                                    <div class="price-mini">₼999/ay</div>
                                </div>
                            </th>
                            <th class="custom-col">
                                <div class="plan-header-mini">
                                    <i class="fas fa-crown"></i>
                                    <span>Fərdi</span>
                                    <div class="price-mini">Fərdi Qiymət</div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="feature-row">
                            <td class="feature-name">
                                <strong>SEO Açar Sözləri</strong>
                                <small>Axtarış motorları üçün optimallaşdırma</small>
                            </td>
                            <td><span class="feature-value">5</span></td>
                            <td><span class="feature-value highlight">15</span></td>
                            <td><span class="feature-value">50</span></td>
                            <td><span class="feature-value premium">Sınırsız</span></td>
                        </tr>
                        <tr class="feature-row">
                            <td class="feature-name">
                                <strong>Sosyal Medya Platformları</strong>
                                <small>Facebook, Instagram, LinkedIn və s.</small>
                            </td>
                            <td><span class="feature-value">2</span></td>
                            <td><span class="feature-value highlight">4</span></td>
                            <td><span class="feature-value">6</span></td>
                            <td><span class="feature-value premium">Hamısı</span></td>
                        </tr>
                        <tr class="feature-row">
                            <td class="feature-name">
                                <strong>Analitik Hesabatlar</strong>
                                <small>Performans və ROI təhlili</small>
                            </td>
                            <td><span class="feature-badge basic">Aylıq</span></td>
                            <td><span class="feature-badge pro">Həftəlik</span></td>
                            <td><span class="feature-badge enterprise">Gündəlik</span></td>
                            <td><span class="feature-badge custom">Real-time</span></td>
                        </tr>
                        <tr class="feature-row">
                            <td class="feature-name">
                                <strong>Reklam Kampaniyaları</strong>
                                <small>Google Ads, Facebook Ads</small>
                            </td>
                            <td><i class="fas fa-times text-red"></i></td>
                            <td><i class="fas fa-check text-green highlight-icon"></i></td>
                            <td><i class="fas fa-check text-green"></i></td>
                            <td><i class="fas fa-check text-green"></i></td>
                        </tr>
                        <tr class="feature-row">
                            <td class="feature-name">
                                <strong>Brendinq Xidmətləri</strong>
                                <small>Logo, korporativ kimlik</small>
                            </td>
                            <td><i class="fas fa-times text-red"></i></td>
                            <td><span class="feature-badge pro">Əsas</span></td>
                            <td><span class="feature-badge enterprise">Tam</span></td>
                            <td><span class="feature-badge custom">Fərdi</span></td>
                        </tr>
                        <tr class="feature-row">
                            <td class="feature-name">
                                <strong>Müştəri Dəstəyi</strong>
                                <small>Texniki və məsləhət dəstəyi</small>
                            </td>
                            <td><span class="feature-badge basic">Email</span></td>
                            <td><span class="feature-badge pro">Telefon + Email</span></td>
                            <td><span class="feature-badge enterprise">24/7</span></td>
                            <td><span class="feature-badge custom">VIP Prioritet</span></td>
                        </tr>
                        <tr class="feature-row">
                            <td class="feature-name">
                                <strong>Şəxsi Hesab Meneceri</strong>
                                <small>Xüsusi məsul şəxs</small>
                            </td>
                            <td><i class="fas fa-times text-red"></i></td>
                            <td><i class="fas fa-times text-red"></i></td>
                            <td><i class="fas fa-check text-green"></i></td>
                            <td><i class="fas fa-check text-green"></i></td>
                        </tr>
                        <tr class="feature-row">
                            <td class="feature-name">
                                <strong>API İnteqrasiyaları</strong>
                                <small>Xüsusi sistem bağlantıları</small>
                            </td>
                            <td><i class="fas fa-times text-red"></i></td>
                            <td><span class="feature-badge pro">Əsas</span></td>
                            <td><span class="feature-badge enterprise">Genişləndirilmiş</span></td>
                            <td><span class="feature-badge custom">Tam Fərdi</span></td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="comparison-footer">
                    <div class="comparison-note">
                        <i class="fas fa-info-circle"></i>
                        <span>Bütün planlar 14 günlük pulsuz sınaq dövru ilə gəlir</span>
                    </div>
                    <div class="comparison-actions-bottom">
                        <button class="btn btn-outline-secondary" onclick="window.location.href='contact.php'">
                            <i class="fas fa-phone"></i>
                            Məsləhət Alın
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#packageModal">
                            <i class="fas fa-rocket"></i>
                            İndi Başlayın
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Client Logos Section -->
    <section class="client-logos-section">
        <div class="container">
            <div class="logos-header">
                <h3>Bizə Güvənən Şirkətlər</h3>
                <p>Müxtəlif sahələrdən 500+ şirkət bizim xidmətlərimizdən istifadə edir</p>
            </div>
            <div class="logos-grid">
                <div class="logo-item">
                    <div class="logo-placeholder">
                        <i class="fas fa-building"></i>
                        <span>TechCorp</span>
                    </div>
                </div>
                <div class="logo-item">
                    <div class="logo-placeholder">
                        <i class="fas fa-store"></i>
                        <span>ShopAZ</span>
                    </div>
                </div>
                <div class="logo-item">
                    <div class="logo-placeholder">
                        <i class="fas fa-mobile-alt"></i>
                        <span>MobileFirst</span>
                    </div>
                </div>
                <div class="logo-item">
                    <div class="logo-placeholder">
                        <i class="fas fa-chart-line"></i>
                        <span>DataFlow</span>
                    </div>
                </div>
                <div class="logo-item">
                    <div class="logo-placeholder">
                        <i class="fas fa-cloud"></i>
                        <span>CloudTech</span>
                    </div>
                </div>
                <div class="logo-item">
                    <div class="logo-placeholder">
                        <i class="fas fa-shield-alt"></i>
                        <span>SecureNet</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust & Social Proof Section -->
    <section class="trust-section">
        <div class="container">
            <div class="trust-header">
                <h2>Müştərilərimizin Güvəni</h2>
                <p>Minlərlə müştəri bizə güvənir və uğur əldə edir</p>
            </div>
            
            <!-- Statistics -->
            <div class="trust-stats">
                <div class="stat-item">
                    <div class="stat-number" data-count="500">0</div>
                    <div class="stat-label">Uğurlu Layihə</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-count="98">0</div>
                    <div class="stat-label">% Müştəri Məmnuniyyəti</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-count="24">0</div>
                    <div class="stat-label">Saat Dəstək</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-count="5">0</div>
                    <div class="stat-label">İl Təcrübə</div>
                </div>
            </div>
            
            <!-- Trust Badges -->
            <div class="trust-badges">
                <div class="badge-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>SSL Təhlükəsizlik</span>
                </div>
                <div class="badge-item">
                    <i class="fas fa-award"></i>
                    <span>Sertifikatlı Şirkət</span>
                </div>
                <div class="badge-item">
                    <i class="fas fa-lock"></i>
                    <span>GDPR Uyğunluq</span>
                </div>
                <div class="badge-item">
                    <i class="fas fa-headset"></i>
                    <span>24/7 Dəstək</span>
                </div>
            </div>
            
            <!-- Customer Testimonials -->
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"NextCode komandası bizim şirkətimizin rəqəmsal transformasiyasında böyük rol oynadı. Professional yanaşma və keyfiyyətli xidmət."</p>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h4>Əli Məmmədov</h4>
                                <span>CEO, TechCorp</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"E-commerce platformamız NextCode sayəsində 300% artım göstərdi. Təşəkkür edirik!"</p>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h4>Leyla Həsənova</h4>
                                <span>Founder, ShopAZ</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"Mobil tətbiqimiz App Store-da 1 nömrəli oldu. NextCode-un texniki ekspertliyi əla idi."</p>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h4>Rəşad Quliyev</h4>
                                <span>CTO, MobileFirst</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Enterprise Features Section -->
    <section class="enterprise-section" id="enterprise">
        <div class="container">
            <div class="enterprise-header">
                <h2>Enterprise Həlləri</h2>
                <p>Böyük şirkətlər üçün xüsusi həllər və fərdi yanaşma</p>
            </div>
            <div class="enterprise-content">
                <div class="enterprise-features">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3>Fərdi Məsləhət</h3>
                        <p>Ekspertlərimizlə pulsuz məsləhət seansı təyin edin və layihənizi müzakirə edin</p>
                        <button class="consultation-btn" onclick="openConsultationModal()">Məsləhət Al</button>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <h3>Xüsusi Qiymət</h3>
                        <p>Layihənizin tələblərinə uyğun fərdi qiymət təklifi alın</p>
                        <button class="quote-btn" onclick="openQuoteModal()">Qiymət İstə</button>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Dedicated Team</h3>
                        <p>Layihəniz üçün xüsusi komanda və layihə meneceri təyin edilir</p>
                        <button class="team-btn" onclick="openTeamModal()">Komanda İstə</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Consultation Modal -->
    <div id="consultationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('consultationModal')">&times;</span>
            <h3>Pulsuz Məsləhət Seansı</h3>
            <form id="consultationForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="consult-name">Ad Soyad *</label>
                        <input type="text" id="consult-name" required>
                    </div>
                    <div class="form-group">
                        <label for="consult-email">Email *</label>
                        <input type="email" id="consult-email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="consult-phone">Telefon</label>
                        <input type="tel" id="consult-phone">
                    </div>
                    <div class="form-group">
                        <label for="consult-company">Şirkət</label>
                        <input type="text" id="consult-company">
                    </div>
                </div>
                <div class="form-group">
                    <label for="consult-project">Layihə Haqqında *</label>
                    <textarea id="consult-project" rows="4" required placeholder="Layihənizi qısaca təsvir edin..."></textarea>
                </div>
                <div class="form-group">
                    <label for="consult-date">Tərcih edilən tarix</label>
                    <input type="date" id="consult-date">
                </div>
                <button type="submit" class="submit-btn">Məsləhət Təyin Et</button>
            </form>
        </div>
    </div>

    <!-- Custom Quote Modal -->
    <div id="quoteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('quoteModal')">&times;</span>
            <h3>Xüsusi Qiymət Tələbi</h3>
            <form id="quoteForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="quote-name">Ad Soyad *</label>
                        <input type="text" id="quote-name" required>
                    </div>
                    <div class="form-group">
                        <label for="quote-email">Email *</label>
                        <input type="email" id="quote-email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="quote-services">Xidmətlər *</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" value="web-development"> Web Development</label>
                        <label><input type="checkbox" value="mobile-app"> Mobile App</label>
                        <label><input type="checkbox" value="ecommerce"> E-commerce</label>
                        <label><input type="checkbox" value="custom-software"> Custom Software</label>
                        <label><input type="checkbox" value="consulting"> Consulting</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="quote-budget">Büdcə Aralığı</label>
                    <select id="quote-budget">
                        <option value="">Seçin</option>
                        <option value="5000-10000">5,000 - 10,000 AZN</option>
                        <option value="10000-25000">10,000 - 25,000 AZN</option>
                        <option value="25000-50000">25,000 - 50,000 AZN</option>
                        <option value="50000+">50,000+ AZN</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="quote-timeline">Zaman çərçivəsi</label>
                    <select id="quote-timeline">
                        <option value="">Seçin</option>
                        <option value="1-3-months">1-3 ay</option>
                        <option value="3-6-months">3-6 ay</option>
                        <option value="6-12-months">6-12 ay</option>
                        <option value="12+months">12+ ay</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="quote-details">Layihə Təfərrüatları *</label>
                    <textarea id="quote-details" rows="5" required placeholder="Layihənizin təfərrüatlarını yazın..."></textarea>
                </div>
                <button type="submit" class="submit-btn">Qiymət Tələb Et</button>
            </form>
        </div>
    </div>

    <!-- FAQ Section -->
    <section class="pricing-faq">
        <div class="container">
            <div class="section-header">
                <h2>Tez-tez Verilən Suallar</h2>
                <p>Qiymətlər və planlar haqqında ən çox soruşulan suallar</p>
                <div class="faq-search">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="faqSearch" placeholder="Sual axtarın...">
                    </div>
                </div>
            </div>
            
            <div class="faq-categories">
                <button class="faq-category active" data-category="all">
                    <i class="fas fa-th-large"></i>
                    Hamısı
                </button>
                <button class="faq-category" data-category="pricing">
                    <i class="fas fa-dollar-sign"></i>
                    Qiymətlər
                </button>
                <button class="faq-category" data-category="features">
                    <i class="fas fa-star"></i>
                    Xüsusiyyətlər
                </button>
                <button class="faq-category" data-category="support">
                    <i class="fas fa-headset"></i>
                    Dəstək
                </button>
                <button class="faq-category" data-category="technical">
                    <i class="fas fa-cog"></i>
                    Texniki
                </button>
            </div>
            
            <div class="faq-grid">
                <div class="faq-item" data-category="pricing">
                    <div class="faq-question">
                        <div class="question-content">
                            <i class="fas fa-exchange-alt faq-icon"></i>
                            <h3>Planımı istədiyim vaxt dəyişə bilərəmmi?</h3>
                            <div class="faq-badge pricing">Qiymət</div>
                        </div>
                        <i class="fas fa-plus toggle-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Bəli, planınızı istədiyiniz vaxt yüksəldə və ya endirə bilərsiniz. Dəyişikliklər növbəti ödəniş dövrünüzdən etibarən qüvvəyə minir.</p>
                        <div class="faq-helpful">
                            <span>Bu cavab faydalı oldu?</span>
                            <button class="btn-helpful" data-helpful="yes">
                                <i class="fas fa-thumbs-up"></i>
                                Bəli
                            </button>
                            <button class="btn-helpful" data-helpful="no">
                                <i class="fas fa-thumbs-down"></i>
                                Xeyr
                            </button>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="pricing">
                    <div class="faq-question">
                        <div class="question-content">
                            <i class="fas fa-percentage faq-icon"></i>
                            <h3>İllik planlar üçün endirim varmı?</h3>
                            <div class="faq-badge pricing">Qiymət</div>
                        </div>
                        <i class="fas fa-plus toggle-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Bəli, illik ödəniş seçdiyiniz halda <strong>20% endirim</strong> əldə edirsiniz. Bu, 2 ay pulsuz xidmət deməkdir.</p>
                        <div class="discount-details">
                            <div class="discount-item">
                                <i class="fas fa-check"></i>
                                <span>Başlanğıc: ₼299 → ₼239/ay</span>
                            </div>
                            <div class="discount-item">
                                <i class="fas fa-check"></i>
                                <span>Peşəkar: ₼599 → ₼479/ay</span>
                            </div>
                            <div class="discount-item">
                                <i class="fas fa-check"></i>
                                <span>Korporativ: ₼999 → ₼799/ay</span>
                            </div>
                        </div>
                        <div class="faq-helpful">
                            <span>Bu cavab faydalı oldu?</span>
                            <button class="btn-helpful" data-helpful="yes">
                                <i class="fas fa-thumbs-up"></i>
                                Bəli
                            </button>
                            <button class="btn-helpful" data-helpful="no">
                                <i class="fas fa-thumbs-down"></i>
                                Xeyr
                            </button>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="pricing">
                    <div class="faq-question">
                        <div class="question-content">
                            <i class="fas fa-credit-card faq-icon"></i>
                            <h3>Ödəniş üsulları hansılardır?</h3>
                            <div class="faq-badge pricing">Qiymət</div>
                        </div>
                        <i class="fas fa-plus toggle-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Müxtəlif ödəniş üsulları ilə rahat ödəniş edə bilərsiniz:</p>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <i class="fab fa-cc-visa"></i>
                                <span>Kredit/Debit Kartlar</span>
                            </div>
                            <div class="payment-method">
                                <i class="fas fa-university"></i>
                                <span>Bank Köçürməsi</span>
                            </div>
                            <div class="payment-method">
                                <i class="fab fa-paypal"></i>
                                <span>PayPal</span>
                            </div>
                            <div class="payment-method">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Mobil Ödəniş</span>
                            </div>
                        </div>
                        <div class="security-note">
                            <i class="fas fa-shield-alt"></i>
                            <span>Bütün ödənişlər 256-bit SSL şifrələməsi ilə qorunur</span>
                        </div>
                        <div class="faq-helpful">
                            <span>Bu cavab faydalı oldu?</span>
                            <button class="btn-helpful" data-helpful="yes">
                                <i class="fas fa-thumbs-up"></i>
                                Bəli
                            </button>
                            <button class="btn-helpful" data-helpful="no">
                                <i class="fas fa-thumbs-down"></i>
                                Xeyr
                            </button>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="features">
                    <div class="faq-question">
                        <div class="question-content">
                            <i class="fas fa-gift faq-icon"></i>
                            <h3>Pulsuz sınaq dövrü varmı?</h3>
                            <div class="faq-badge features">Xüsusiyyət</div>
                        </div>
                        <i class="fas fa-plus toggle-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Bəli, bütün planlar üçün <strong>14 günlük pulsuz sınaq dövrü</strong> təklif edirik. Bu müddətdə bütün xidmətləri sınaya bilərsiniz.</p>
                        <div class="trial-features">
                            <div class="trial-feature">
                                <i class="fas fa-check"></i>
                                <span>Kredit kartı tələb olunmur</span>
                            </div>
                            <div class="trial-feature">
                                <i class="fas fa-check"></i>
                                <span>Tam funksionallıq</span>
                            </div>
                            <div class="trial-feature">
                                <i class="fas fa-check"></i>
                                <span>İstənilən vaxt ləğv edin</span>
                            </div>
                        </div>
                        <div class="faq-helpful">
                            <span>Bu cavab faydalı oldu?</span>
                            <button class="btn-helpful" data-helpful="yes">
                                <i class="fas fa-thumbs-up"></i>
                                Bəli
                            </button>
                            <button class="btn-helpful" data-helpful="no">
                                <i class="fas fa-thumbs-down"></i>
                                Xeyr
                            </button>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="features">
                    <div class="faq-question">
                        <div class="question-content">
                            <i class="fas fa-times-circle faq-icon"></i>
                            <h3>Müqaviləni ləğv edə bilərəmmi?</h3>
                            <div class="faq-badge features">Xüsusiyyət</div>
                        </div>
                        <i class="fas fa-plus toggle-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p><strong>Minimum müqavilə müddəti yoxdur!</strong> İstədiyiniz vaxt müqaviləni ləğv edə bilərsiniz.</p>
                        <div class="cancellation-info">
                            <div class="info-item">
                                <i class="fas fa-calendar-times"></i>
                                <div>
                                    <strong>Ləğv Prosesi</strong>
                                    <small>Hesab panelindən bir kliklə ləğv edin</small>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-money-bill-wave"></i>
                                <div>
                                    <strong>Geri Ödəmə</strong>
                                    <small>Cari dövrün sonuna qədər xidmət davam edir</small>
                                </div>
                            </div>
                        </div>
                        <div class="faq-helpful">
                            <span>Bu cavab faydalı oldu?</span>
                            <button class="btn-helpful" data-helpful="yes">
                                <i class="fas fa-thumbs-up"></i>
                                Bəli
                            </button>
                            <button class="btn-helpful" data-helpful="no">
                                <i class="fas fa-thumbs-down"></i>
                                Xeyr
                            </button>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="support">
                    <div class="faq-question">
                        <div class="question-content">
                            <i class="fas fa-magic faq-icon"></i>
                            <h3>Fərdi plan necə işləyir?</h3>
                            <div class="faq-badge support">Dəstək</div>
                        </div>
                        <i class="fas fa-plus toggle-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Fərdi plan sizin xüsusi tələblərinizə görə hazırlanır. Prosesimiz aşağıdakı kimi işləyir:</p>
                        <div class="custom-process">
                            <div class="process-step">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <strong>Pulsuz Məsləhət</strong>
                                    <small>Tələblərinizi müzakirə edirik</small>
                                </div>
                            </div>
                            <div class="process-step">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <strong>Fərdi Təklif</strong>
                                    <small>Xüsusi paket və qiymət hazırlanır</small>
                                </div>
                            </div>
                            <div class="process-step">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <strong>İcra</strong>
                                    <small>Xüsusi komanda təyin edilir</small>
                                </div>
                            </div>
                        </div>
                        <div class="faq-helpful">
                            <span>Bu cavab faydalı oldu?</span>
                            <button class="btn-helpful" data-helpful="yes">
                                <i class="fas fa-thumbs-up"></i>
                                Bəli
                            </button>
                            <button class="btn-helpful" data-helpful="no">
                                <i class="fas fa-thumbs-down"></i>
                                Xeyr
                            </button>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="technical">
                    <div class="faq-question">
                        <div class="question-content">
                            <i class="fas fa-chart-line faq-icon"></i>
                            <h3>Hesabatlar nə qədər detallıdır?</h3>
                            <div class="faq-badge technical">Texniki</div>
                        </div>
                        <i class="fas fa-plus toggle-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Hesabatlarımız hərtərəfli analitik məlumatlar təqdim edir:</p>
                        <div class="report-features">
                            <div class="report-feature">
                                <i class="fas fa-search"></i>
                                <div>
                                    <strong>SEO Performansı</strong>
                                    <small>Açar söz reytinqləri, trafik artımı</small>
                                </div>
                            </div>
                            <div class="report-feature">
                                <i class="fas fa-share-alt"></i>
                                <div>
                                    <strong>Sosial Medya</strong>
                                    <small>Engagement, reach, follower artımı</small>
                                </div>
                            </div>
                            <div class="report-feature">
                                <i class="fas fa-bullseye"></i>
                                <div>
                                    <strong>Reklam Kampaniyaları</strong>
                                    <small>ROI, CTR, konversiya dərəcələri</small>
                                </div>
                            </div>
                        </div>
                        <div class="faq-helpful">
                            <span>Bu cavab faydalı oldu?</span>
                            <button class="btn-helpful" data-helpful="yes">
                                <i class="fas fa-thumbs-up"></i>
                                Bəli
                            </button>
                            <button class="btn-helpful" data-helpful="no">
                                <i class="fas fa-thumbs-down"></i>
                                Xeyr
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="faq-footer">
                <div class="faq-contact">
                    <h4>Sualınızın cavabını tapa bilmədiniz?</h4>
                    <p>Bizim komandamız sizə kömək etməyə hazırdır</p>
                    <div class="contact-options">
                        <a href="contact.php" class="btn btn-primary">
                            <i class="fas fa-envelope"></i>
                            Bizimlə Əlaqə
                        </a>
                        <a href="tel:+994501234567" class="btn btn-outline-primary">
                            <i class="fas fa-phone"></i>
                            Zəng Edin
                        </a>
                        <button class="btn btn-outline-secondary" id="liveChatBtn">
                            <i class="fas fa-comments"></i>
                            Canlı Söhbət
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="pricing-cta">
        <div class="container">
            <div class="cta-content">
                <h2>Hələ də qərar verə bilmirsiniz?</h2>
                <p>Bizim mütəxəssislərimizlə danışın və sizə ən uyğun planı seçin</p>
                <div class="cta-buttons">
                    <a href="contact.php" class="btn-primary">
                        <i class="fas fa-phone"></i>
                        Pulsuz Məsləhət
                    </a>
                    <a href="#" class="btn-secondary" id="startTrial">
                        <i class="fas fa-play"></i>
                        14 Günlük Sınaq
                    </a>
                </div>
            </div>
        </div>
    </section>



    <!-- Package Selection Modal -->
    <div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="packageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="packageModalLabel">Paket Seçimi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="package-details" id="packageDetails">
                        <!-- Package details will be populated by JavaScript -->
                    </div>
                    <form id="packageForm">
                        <div class="mb-3">
                            <label for="customerName" class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" id="customerName" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerEmail" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="customerEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerPhone" class="form-label">Telefon</label>
                            <input type="tel" class="form-control" id="customerPhone" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerMessage" class="form-label">Mesaj (İsteğe bağlı)</label>
                            <textarea class="form-control" id="customerMessage" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" id="submitPackage">Paketi Seç</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Comparison Modal -->
    <div class="modal fade" id="comparisonModal" tabindex="-1" aria-labelledby="comparisonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="comparisonModalLabel">Paket Karşılaştırması</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="comparison-grid">
                        <div class="comparison-item">
                            <h6>Başlanğıc Paketi</h6>
                            <ul>
                                <li>5 SEO Açar Sözü</li>
                                <li>2 Sosyal Medya Platformu</li>
                                <li>Aylıq Hesabat</li>
                                <li>Email Dəstək</li>
                            </ul>
                            <div class="price">₼299/ay</div>
                        </div>
                        <div class="comparison-item featured">
                            <h6>Peşəkar Paketi</h6>
                            <ul>
                                <li>15 SEO Açar Sözü</li>
                                <li>4 Sosyal Medya Platformu</li>
                                <li>Həftəlik Hesabat</li>
                                <li>Google Ads</li>
                                <li>Brendinq Xidmətləri</li>
                                <li>Telefon + Email Dəstək</li>
                            </ul>
                            <div class="price">₼599/ay</div>
                        </div>
                        <div class="comparison-item">
                            <h6>Korporativ Paketi</h6>
                            <ul>
                                <li>Sınırsız SEO Açar Sözü</li>
                                <li>Bütün Sosyal Medya Platformları</li>
                                <li>Gündəlik Hesabat</li>
                                <li>Tam Reklam Paketi</li>
                                <li>Tam Brendinq</li>
                                <li>24/7 VIP Dəstək</li>
                                <li>Şəxsi Hesab Meneceri</li>
                            </ul>
                            <div class="price">₼999/ay</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='contact.php'">İletişime Geç</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Package Calculator Modal -->
    <div class="modal fade" id="calculatorModal" tabindex="-1" aria-labelledby="calculatorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="calculatorModalLabel">Özel Paket Hesaplayıcısı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="calculatorForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">SEO Xidmətləri</label>
                                    <select class="form-select" id="seoService">
                                        <option value="0">Yok</option>
                                        <option value="200">Temel SEO (₼200)</option>
                                        <option value="400">Gelişmiş SEO (₼400)</option>
                                        <option value="600">Premium SEO (₼600)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sosyal Medya</label>
                                    <select class="form-select" id="socialMedia">
                                        <option value="0">Yok</option>
                                        <option value="150">2 Platform (₼150)</option>
                                        <option value="300">4 Platform (₼300)</option>
                                        <option value="500">Tüm Platformlar (₼500)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Reklam Yönetimi</label>
                                    <select class="form-select" id="advertising">
                                        <option value="0">Yok</option>
                                        <option value="250">Google Ads (₼250)</option>
                                        <option value="200">Facebook Ads (₼200)</option>
                                        <option value="400">Tüm Platformlar (₼400)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Brendinq</label>
                                    <select class="form-select" id="branding">
                                        <option value="0">Yok</option>
                                        <option value="300">Logo Tasarım (₼300)</option>
                                        <option value="500">Kurumsal Kimlik (₼500)</option>
                                        <option value="800">Tam Branding (₼800)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Web Tasarım</label>
                                    <select class="form-select" id="webDesign">
                                        <option value="0">Yok</option>
                                        <option value="600">Temel Web Sitesi (₼600)</option>
                                        <option value="1200">E-ticaret Sitesi (₼1200)</option>
                                        <option value="2000">Kurumsal Portal (₼2000)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Analitik & Raporlama</label>
                                    <select class="form-select" id="analytics">
                                        <option value="0">Yok</option>
                                        <option value="100">Aylık Rapor (₼100)</option>
                                        <option value="200">Haftalık Rapor (₼200)</option>
                                        <option value="300">Günlük Rapor (₼300)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="calculator-result">
                            <h5>Toplam Aylık Maliyet: <span id="totalCost">₼0</span></h5>
                            <p class="text-muted">Yıllık ödeme ile %20 indirim kazanın!</p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" id="requestQuote">Teklif İste</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/pricing.js"></script>
    <script src="js/pricing-modern.js"></script>

<?php
// Include footer
require_once 'includes/footer.php';
?>