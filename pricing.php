<?php
// NextCode Group - Pricing Page
// Modern, Clean and Professional Design

// Define secure access constant
define('SECURE_ACCESS', true);

// Include API router for handling API requests
require_once 'api-router.php';

// Database connection
require_once 'config/database.php';

// Security configuration
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
$page_title = getTextContent('pricing_page_title', 'Qiymət Planları - NextCode Group');
$meta_description = getTextContent('pricing_meta_description', 'Peşəkar rəqəmsal marketinq xidmətləri qiymətləri. Biznesinizin böyüməsi üçün mükəmməl planı seçin.');
$current_page = 'pricing';

// Include header
require_once 'includes/header.php';
?>

<!-- Modern Pricing Page Styles -->
<link rel="stylesheet" href="css/pricing-alternative.css">
<style>
/* Modern Pricing Page Styles - Alternative Theme Compatible */
.pricing-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    position: relative;
    overflow-x: hidden;
}

/* Alternative theme override */
body.alternative-theme .pricing-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%) !important;
    background-size: 400% 400% !important;
    animation: gradientShift 15s ease infinite !important;
}

body.alternative-theme .pricing-page::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%) !important;
    pointer-events: none !important;
}

body.alternative-theme .pricing-page::after {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background-image: 
        radial-gradient(2px 2px at 20px 30px, rgba(255, 255, 255, 0.3), transparent),
        radial-gradient(2px 2px at 40px 70px, rgba(255, 255, 255, 0.2), transparent),
        radial-gradient(1px 1px at 90px 40px, rgba(255, 255, 255, 0.4), transparent),
        radial-gradient(1px 1px at 130px 80px, rgba(255, 255, 255, 0.3), transparent) !important;
    background-repeat: repeat !important;
    background-size: 200px 200px !important;
    animation: sparkle 20s linear infinite !important;
    pointer-events: none !important;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes sparkle {
    0% { transform: translateY(0px); }
    100% { transform: translateY(-200px); }
}

/* Hero Section */
.pricing-hero {
    padding: 120px 0 80px;
    text-align: center;
    position: relative;
    z-index: 1;
}

/* Alternative theme hero styles */
body.alternative-theme .pricing-hero {
    padding: 120px 0 80px !important;
    text-align: center !important;
    position: relative !important;
    z-index: 1 !important;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 0.75rem 2rem;
    border-radius: 50px;
    color: #ffffff;
    font-weight: 600;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
}

.hero-badge:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
}

.hero-badge i {
    color: #ffd700;
    font-size: 1.2rem;
}

/* Alternative theme hero badge */
body.alternative-theme .hero-badge {
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(20px) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
}

body.alternative-theme .hero-badge i {
    color: #ffd700 !important;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff 0%, #4ecdc4 50%, #ffd700 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 1.3rem;
    color: rgba(255, 255, 255, 0.9);
    max-width: 700px;
    margin: 0 auto 3rem;
    line-height: 1.6;
}

/* Alternative theme hero text */
body.alternative-theme .hero-title {
    background: linear-gradient(135deg, #ffffff 0%, #4ecdc4 50%, #ffd700 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

body.alternative-theme .hero-subtitle {
    color: rgba(255, 255, 255, 0.9) !important;
}

.hero-stats {
    display: flex;
    justify-content: center;
    gap: 4rem;
    flex-wrap: wrap;
    margin-top: 3rem;
}

.stat-item {
    text-align: center;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #4ecdc4, #ffd700);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
    font-weight: 500;
}

/* Alternative theme stats */
body.alternative-theme .stat-item {
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(20px) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
}

body.alternative-theme .stat-number {
    background: linear-gradient(135deg, #4ecdc4, #ffd700) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

body.alternative-theme .stat-label {
    color: rgba(255, 255, 255, 0.8) !important;
}


/* Pricing Cards */
.pricing-section {
    padding: 80px 0;
    position: relative;
    z-index: 1;
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.pricing-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(30px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 25px;
    padding: 2.5rem;
    position: relative;
    transition: all 0.4s ease;
    overflow: hidden;
}

.pricing-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.pricing-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 30px 80px rgba(102, 126, 234, 0.4);
    border-color: rgba(255, 215, 0, 0.5);
}

.pricing-card:hover::before {
    opacity: 1;
}

.pricing-card.popular {
    border: 2px solid #ffd700;
    box-shadow: 0 20px 60px rgba(255, 215, 0, 0.3);
    transform: scale(1.05);
}

.pricing-card.popular::before {
    opacity: 1;
    background: linear-gradient(90deg, #ffd700, #ff6b6b, #4ecdc4);
}

/* Alternative theme pricing cards */
body.alternative-theme .pricing-card {
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(30px) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
}

body.alternative-theme .pricing-card.popular {
    border: 2px solid #ffd700 !important;
    box-shadow: 0 20px 60px rgba(255, 215, 0, 0.3) !important;
}

body.alternative-theme .popular-badge {
    background: linear-gradient(135deg, #ff6b6b, #ffd700) !important;
    color: #ffffff !important;
}

.popular-badge {
    position: absolute;
    top: -15px;
    right: 30px;
    background: linear-gradient(135deg, #ff6b6b, #ffd700);
    color: #ffffff;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
}

.plan-header {
    text-align: center;
    margin-bottom: 2rem;
}

.plan-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, #4ecdc4, #ffd700);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(78, 205, 196, 0.3);
}

.plan-icon i {
    font-size: 2.5rem;
    color: #ffffff;
}

.plan-badge {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 1rem;
}

.plan-title {
    color: #ffffff;
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.plan-description {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
    line-height: 1.6;
}

.plan-price {
    text-align: center;
    margin-bottom: 2.5rem;
}

.price-container {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.currency {
    color: #ffffff;
    font-size: 1.8rem;
    font-weight: 600;
}

.amount {
    color: #ffffff;
    font-size: 3.5rem;
    font-weight: 800;
}

/* Alternative theme plan elements */
body.alternative-theme .plan-badge {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
}

body.alternative-theme .plan-title {
    color: #ffffff !important;
}

body.alternative-theme .plan-description {
    color: rgba(255, 255, 255, 0.8) !important;
}

body.alternative-theme .currency {
    color: #ffffff !important;
}

body.alternative-theme .amount {
    color: #ffffff !important;
}

body.alternative-theme .period {
    color: rgba(255, 255, 255, 0.8) !important;
}

body.alternative-theme .price-note {
    color: rgba(255, 255, 255, 0.7) !important;
}

.period {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.3rem;
    font-weight: 500;
}

.price-note {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

.plan-features {
    margin-bottom: 2.5rem;
}

.features-header h4 {
    color: #ffffff;
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-align: center;
}

.features-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    transition: all 0.3s ease;
}

.feature-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(10px);
}

.feature-item i {
    color: #4ecdc4;
    font-size: 1.3rem;
    flex-shrink: 0;
    margin-top: 0.2rem;
}

.feature-content {
    flex: 1;
}

.feature-content strong {
    color: #ffffff;
    display: block;
    margin-bottom: 0.3rem;
    font-weight: 600;
}

.feature-content span {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.95rem;
    line-height: 1.5;
}

.feature-benefit {
    color: #ffd700;
    font-size: 0.85rem;
    margin-top: 0.3rem;
    font-weight: 500;
}

/* Alternative theme features */
body.alternative-theme .features-header h4 {
    color: #ffffff !important;
}

body.alternative-theme .feature-item {
    background: rgba(255, 255, 255, 0.05) !important;
    color: #ffffff !important;
}

body.alternative-theme .feature-item:hover {
    background: rgba(255, 255, 255, 0.1) !important;
}

body.alternative-theme .feature-item i {
    color: #4ecdc4 !important;
}

body.alternative-theme .feature-content strong {
    color: #ffffff !important;
}

body.alternative-theme .feature-content span {
    color: rgba(255, 255, 255, 0.7) !important;
}

body.alternative-theme .feature-benefit {
    color: #ffd700 !important;
}

.plan-action {
    text-align: center;
}

.plan-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    border: none;
    padding: 1.2rem 2.5rem;
    border-radius: 30px;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    width: 100%;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.plan-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.plan-btn.popular {
    background: linear-gradient(135deg, #ff6b6b 0%, #ffd700 100%);
    box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
}

.plan-btn.popular:hover {
    background: linear-gradient(135deg, #ffd700 0%, #ff6b6b 100%);
    box-shadow: 0 15px 40px rgba(255, 107, 107, 0.5);
}

.plan-guarantee {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.plan-guarantee i {
    color: #4ecdc4;
}

.trial-info {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.trial-info i {
    color: #ffd700;
}

/* Alternative theme buttons and guarantees */
body.alternative-theme .plan-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3) !important;
}

body.alternative-theme .plan-btn.popular {
    background: linear-gradient(135deg, #ff6b6b 0%, #ffd700 100%) !important;
    box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3) !important;
}

body.alternative-theme .plan-guarantee {
    color: rgba(255, 255, 255, 0.8) !important;
}

body.alternative-theme .plan-guarantee i {
    color: #4ecdc4 !important;
}

body.alternative-theme .trial-info {
    color: rgba(255, 255, 255, 0.7) !important;
}

body.alternative-theme .trial-info i {
    color: #ffd700 !important;
}

/* CTA Section */
.cta-section {
    padding: 80px 0;
    text-align: center;
    position: relative;
    z-index: 1;
}

.cta-content {
    max-width: 800px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(30px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 30px;
    padding: 4rem;
}

.cta-title {
    color: #ffffff;
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #ffffff 0%, #4ecdc4 50%, #ffd700 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.cta-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.2rem;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.cta-buttons {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.cta-btn {
    padding: 1.2rem 2.5rem;
    border-radius: 30px;
    font-weight: 700;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    border: none;
}

.cta-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.cta-btn.primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.cta-btn.secondary {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.cta-btn.secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: #4ecdc4;
    transform: translateY(-3px);
}

/* Alternative theme CTA */
body.alternative-theme .cta-content {
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(30px) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
}

body.alternative-theme .cta-title {
    background: linear-gradient(135deg, #ffffff 0%, #4ecdc4 50%, #ffd700 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

body.alternative-theme .cta-subtitle {
    color: rgba(255, 255, 255, 0.8) !important;
}

body.alternative-theme .cta-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3) !important;
}

body.alternative-theme .cta-btn.secondary {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
    border: 2px solid rgba(255, 255, 255, 0.3) !important;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .pricing-grid {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }
}

@media (max-width: 992px) {
    .hero-title {
        font-size: 3rem;
    }
    
    .hero-stats {
        gap: 2rem;
    }
    
    .pricing-grid {
        grid-template-columns: 1fr;
        max-width: 500px;
    }
    
    .pricing-card.popular {
        transform: none;
    }
}

@media (max-width: 768px) {
    .pricing-hero {
        padding: 80px 0 60px;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }
    
    .stat-item {
        width: 200px;
    }
    
    
    .pricing-section {
        padding: 60px 0;
    }
    
    .pricing-card {
        padding: 2rem;
    }
    
    .cta-content {
        padding: 3rem 2rem;
    }
    
    .cta-title {
        font-size: 2rem;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-btn {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .amount {
        font-size: 3rem;
    }
    
    .pricing-card {
        padding: 1.5rem;
    }
    
    .plan-icon {
        width: 60px;
        height: 60px;
    }
    
    .plan-icon i {
        font-size: 2rem;
    }
}
</style>

<!-- Pricing Page Content -->
<div class="pricing-page">
    <!-- Hero Section -->
    <section class="pricing-hero">
        <div class="container">
                <div class="hero-badge">
                    <i class="fas fa-crown"></i>
                <span><?php echo getTextContent('pricing_hero_badge', 'Premium Qiymət Planları'); ?></span>
                </div>
            <h1 class="hero-title"><?php echo getTextContent('pricing_hero_title', 'Mükəmməl Planınızı Seçin'); ?></h1>
            <p class="hero-subtitle">
                <?php echo getTextContent('pricing_hero_subtitle', 'Biznesinizin potensialını peşəkar rəqəmsal marketinq xidmətlərimizlə açın. Uğur yolculuğunuza bugün çevik qiymət seçimlərimizlə başlayın.'); ?>
            </p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                    <span class="stat-label"><?php echo getTextContent('pricing_stat_clients', 'Məmnun Müştəri'); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">98%</span>
                    <span class="stat-label"><?php echo getTextContent('pricing_stat_success', 'Uğur Nisbəti'); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                    <span class="stat-label"><?php echo getTextContent('pricing_stat_support', 'Dəstək'); ?></span>
                    </div>
                </div>
            </div>
    </section>


    <!-- Pricing Cards -->
    <section class="pricing-section">
        <div class="container">
            <div class="pricing-grid">
                <!-- Starter Plan -->
                <div class="pricing-card">
                    <div class="plan-header">
                        <div class="plan-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div class="plan-badge"><?php echo getTextContent('pricing_basic_badge', 'Başlanğıc'); ?></div>
                        <h3 class="plan-title"><?php echo getTextContent('pricing_basic_title', 'Basic Pro'); ?></h3>
                        <p class="plan-description">
                            <?php echo getTextContent('pricing_basic_description', 'Kiçik bizneslər və startaplar üçün onlayn mövcudluqlarını qurmaq istəyənlər üçün mükəmməl.'); ?>
                        </p>
                    </div>
                    <div class="plan-price">
                        <div class="price-container">
                            <span class="currency">₼</span>
                            <span class="amount" data-monthly="<?php echo getTextContent('pricing_basic_price_monthly', '299'); ?>" data-yearly="<?php echo getTextContent('pricing_basic_price_yearly', '239'); ?>"><?php echo getTextContent('pricing_basic_price_monthly', '299'); ?></span>
                            <span class="period"><?php echo getTextContent('pricing_period_monthly', '/ay'); ?></span>
                        </div>
                        <div class="price-note"><?php echo getTextContent('pricing_basic_price_note', 'İllik ödəniş: ₼2,390 (₼598 qənaət)'); ?></div>
                    </div>
                    <div class="plan-features">
                        <div class="features-header">
                            <h4><?php echo getTextContent('pricing_features_header', 'Daxil olan xidmətlər:'); ?></h4>
                        </div>
                        <ul class="features-list">
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>SEO Optimizasiya</strong>
                                    <span>Google reytinqi üçün 5 açar söz</span>
                                    <div class="feature-benefit">+150% orqanik trafik artımı</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Sosial Medya İdarəetməsi</strong>
                                    <span>Facebook + Instagram məzmun yaradılması</span>
                                    <div class="feature-benefit">Günlük 300+ etkileşim zəmanəti</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Performans Hesabatları</strong>
                                    <span>Aylıq detallı analitika və görüşlər</span>
                                    <div class="feature-benefit">ROI izləməsi daxildir</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Prioritet Dəstək</strong>
                                    <span>24 saat cavab zəmanəti</span>
                                    <div class="feature-benefit">Ekspert məsləhət dəstəyi</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Veb Sayt Optimizasiyası</strong>
                                    <span>Sürət və performans təkmilləşdirmələri</span>
                                    <div class="feature-benefit">40% daha sürətli yükləmə</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="plan-action">
                        <button class="plan-btn" onclick="selectPlan('basic')">
                            <span><?php echo getTextContent('pricing_basic_button', 'Başla'); ?></span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <div class="plan-guarantee">
                            <i class="fas fa-shield-alt"></i>
                            <span><?php echo getTextContent('pricing_basic_guarantee', '30 günlük pul geri qaytarılması zəmanəti'); ?></span>
                        </div>
                        <div class="trial-info">
                            <i class="fas fa-gift"></i>
                            <span><?php echo getTextContent('pricing_basic_trial', '14 günlük pulsuz sınaq'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Professional Plan -->
                <div class="pricing-card popular">
                    <div class="popular-badge">
                        <i class="fas fa-fire"></i>
                        <span><?php echo getTextContent('pricing_professional_popular', 'Ən Populyar'); ?></span>
                    </div>
                    <div class="plan-header">
                        <div class="plan-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="plan-badge"><?php echo getTextContent('pricing_professional_badge', 'Peşəkar'); ?></div>
                        <h3 class="plan-title"><?php echo getTextContent('pricing_professional_title', 'Professional'); ?></h3>
                        <p class="plan-description">
                            <?php echo getTextContent('pricing_professional_description', 'Böyüyən bizneslər və orta ölçülü şirkətlər üçün hərtərəfli rəqəmsal marketinq axtaranlar üçün ideal.'); ?>
                        </p>
                    </div>
                    <div class="plan-price">
                        <div class="price-container">
                            <span class="currency">₼</span>
                            <span class="amount" data-monthly="<?php echo getTextContent('pricing_professional_price_monthly', '599'); ?>" data-yearly="<?php echo getTextContent('pricing_professional_price_yearly', '479'); ?>"><?php echo getTextContent('pricing_professional_price_monthly', '599'); ?></span>
                            <span class="period"><?php echo getTextContent('pricing_period_monthly', '/ay'); ?></span>
                        </div>
                        <div class="price-note"><?php echo getTextContent('pricing_professional_price_note', 'İllik ödəniş: ₼4,790 (₼1,198 qənaət)'); ?></div>
                    </div>
                    <div class="plan-features">
                        <div class="features-header">
                            <h4><?php echo getTextContent('pricing_features_header', 'Daxil olan xidmətlər:'); ?></h4>
                        </div>
                        <ul class="features-list">
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Qabaqcıl SEO</strong>
                                    <span>15 açar söz + rəqib analizi</span>
                                    <div class="feature-benefit">+300% orqanik trafik artımı</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Çox Platformlu Sosial Medya</strong>
                                    <span>4 platform + məzmun yaradılması</span>
                                    <div class="feature-benefit">Günlük 500+ etkileşim</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Google Ads İdarəetməsi</strong>
                                    <span>Kampaniya qurulumu və optimizasiya</span>
                                    <div class="feature-benefit">₼500 reklam krediti daxildir</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Brend Xidmətləri</strong>
                                    <span>Logo və korporativ kimlik dizaynı</span>
                                    <div class="feature-benefit">Peşəkar brend qaydaları</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Həftəlik Hesabatlar</strong>
                                    <span>Detallı performans analitikası</span>
                                    <div class="feature-benefit">Real vaxt dashboard girişi</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Telefon + Email Dəstək</strong>
                                    <span>Prioritet müştəri xidməti</span>
                                    <div class="feature-benefit">Dedicated hesab meneceri</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="plan-action">
                        <button class="plan-btn popular" onclick="selectPlan('professional')">
                            <span><?php echo getTextContent('pricing_professional_button', 'İndi Başla - 20% Endirim'); ?></span>
                            <i class="fas fa-fire"></i>
                        </button>
                        <div class="plan-guarantee">
                            <i class="fas fa-shield-alt"></i>
                            <span><?php echo getTextContent('pricing_professional_guarantee', '30 günlük pul geri qaytarılması zəmanəti'); ?></span>
                        </div>
                        <div class="trial-info">
                            <i class="fas fa-gift"></i>
                            <span><?php echo getTextContent('pricing_professional_trial', '14 günlük pulsuz sınaq + bonus xüsusiyyətlər'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="pricing-card">
                    <div class="plan-header">
                        <div class="plan-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="plan-badge"><?php echo getTextContent('pricing_enterprise_badge', 'Enterprise'); ?></div>
                        <h3 class="plan-title"><?php echo getTextContent('pricing_enterprise_title', 'Enterprise'); ?></h3>
                        <p class="plan-description">
                            <?php echo getTextContent('pricing_enterprise_description', 'Böyük şirkətlər və korporasiyalar üçün tam miqyaslı rəqəmsal marketinq tələb edənlər üçün tam həll.'); ?>
                        </p>
                    </div>
                    <div class="plan-price">
                        <div class="price-container">
                            <span class="currency">₼</span>
                            <span class="amount" data-monthly="<?php echo getTextContent('pricing_enterprise_price_monthly', '999'); ?>" data-yearly="<?php echo getTextContent('pricing_enterprise_price_yearly', '799'); ?>"><?php echo getTextContent('pricing_enterprise_price_monthly', '999'); ?></span>
                            <span class="period"><?php echo getTextContent('pricing_period_monthly', '/ay'); ?></span>
                        </div>
                        <div class="price-note"><?php echo getTextContent('pricing_enterprise_price_note', 'İllik ödəniş: ₼7,990 (₼1,998 qənaət)'); ?></div>
                    </div>
                    <div class="plan-features">
                        <div class="features-header">
                            <h4><?php echo getTextContent('pricing_features_header', 'Daxil olan xidmətlər:'); ?></h4>
                        </div>
                        <ul class="features-list">
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Limitsiz SEO</strong>
                                    <span>Limitsiz açar söz + AI analizi</span>
                                    <div class="feature-benefit">+500% orqanik trafik artımı</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Bütün Sosial Medya Platformları</strong>
                                    <span>Tam platform idarəetməsi + avtomatlaşdırma</span>
                                    <div class="feature-benefit">Günlük 1000+ etkileşim</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Tam Reklam İdarəetməsi</strong>
                                    <span>Google, Facebook, LinkedIn, TikTok</span>
                                    <div class="feature-benefit">₼1000 reklam krediti daxildir</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Tam Brend Paketi</strong>
                                    <span>Korporativ kimlik + marketinq materialları</span>
                                    <div class="feature-benefit">Tam brend qaydaları</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>24/7 VIP Dəstək</strong>
                                    <span>Dedicated hesab meneceri</span>
                                    <div class="feature-benefit">Prioritet cavab vaxtı</div>
                                </div>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check"></i>
                                <div class="feature-content">
                                    <strong>Günlük Hesabatlar</strong>
                                    <span>Real vaxt performans analitikası</span>
                                    <div class="feature-benefit">Xüsusi dashboard girişi</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="plan-action">
                        <button class="plan-btn" onclick="selectPlan('enterprise')">
                            <span><?php echo getTextContent('pricing_enterprise_button', 'Satışla Əlaqə'); ?></span>
                            <i class="fas fa-phone"></i>
                        </button>
                        <div class="plan-guarantee">
                            <i class="fas fa-shield-alt"></i>
                            <span><?php echo getTextContent('pricing_enterprise_guarantee', 'Tam zəmanət və dəstək'); ?></span>
                        </div>
                        <div class="trial-info">
                            <i class="fas fa-star"></i>
                            <span><?php echo getTextContent('pricing_enterprise_trial', '30 günlük pulsuz sınaq + VIP dəstək'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title"><?php echo getTextContent('pricing_cta_title', 'Biznesinizi Dəyişdirməyə Hazırsınız?'); ?></h2>
                <p class="cta-subtitle">
                    <?php echo getTextContent('pricing_cta_subtitle', 'Minlərlə uğurlu biznesin NextCode Group-a rəqəmsal marketinq ehtiyacları üçün etibar etdiyi qoşulun. Uğur yolculuğunuza bugün başlayın!'); ?>
                </p>
                <div class="cta-buttons">
                    <a href="contact.php" class="cta-btn primary">
                            <i class="fas fa-phone"></i>
                        <span><?php echo getTextContent('pricing_cta_button_1', 'Pulsuz Məsləhət Al'); ?></span>
                    </a>
                    <a href="#" class="cta-btn secondary" onclick="startTrial()">
                        <i class="fas fa-play"></i>
                        <span><?php echo getTextContent('pricing_cta_button_2', '14 Günlük Sınaq Başlat'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </section>
            </div>

<!-- Pricing JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if alternative theme is active
    const checkAlternativeTheme = () => {
        const isAlternativeTheme = document.body.classList.contains('alternative-theme') || 
                                  localStorage.getItem('design') === 'alternative';
        
        if (isAlternativeTheme) {
            document.body.classList.add('alternative-theme');
        }
    };
    
    // Check theme on load
    checkAlternativeTheme();
    
    // Listen for theme changes
    document.addEventListener('designChanged', checkAlternativeTheme);
    
    
    // Plan selection functionality
    window.selectPlan = function(planType) {
        // Store selected plan in localStorage
        localStorage.setItem('selectedPlan', planType);
        
        // Redirect to contact page with plan info
        window.location.href = `contact.php?plan=${planType}`;
    };
    
    // Trial start functionality
    window.startTrial = function() {
        // Store trial info in localStorage
        localStorage.setItem('trialStarted', 'true');
        localStorage.setItem('trialDate', new Date().toISOString());
        
        // Show success message
        alert('Trial started successfully! You will be redirected to our contact page.');
        
        // Redirect to contact page
        window.location.href = 'contact.php?trial=true';
    };
    
    // Add smooth scrolling for better UX
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add intersection observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe pricing cards for animation
    document.querySelectorAll('.pricing-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?>
