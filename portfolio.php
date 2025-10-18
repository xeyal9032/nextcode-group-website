<?php
define('SECURE_ACCESS', true);

// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';
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

// Sayfa bilgileri
$page_title = getTextContent('portfolio_page_title', 'Portfolio - Müasir Veb Həlləri');
$meta_description = getTextContent('portfolio_meta_description', 'Peşəkar veb dizayn və inkişaf layihələrimizi kəşf edin. Müasir, responsiv və istifadəçi dostu veb saytlar.');
$meta_keywords = getTextContent('portfolio_meta_keywords', 'portfolio, veb dizayn, veb inkişaf, mobil tətbiqlər, e-ticarət, AI layihələri, NextCode, Azərbaycan, Bakı');
$current_page = 'portfolio';

// Portfolio verilerini yükle
$categories = [];
$projects = [];

try {
    // Veritabanı bağlantısını kontrol et
    if (isset($pdo) && $pdo) {
        // Categories tablosunu kontrol et
        if (tableExists('portfolio_categories')) {
            $cat_query = $pdo->query("SELECT * FROM portfolio_categories WHERE is_active = 1 ORDER BY sort_order ASC");
            $categories = $cat_query->fetchAll();
        }
        
        // Projects tablosunu kontrol et
        if (tableExists('portfolio_projects')) {
            $proj_query = $pdo->query("SELECT * FROM portfolio_projects WHERE is_published = 1 AND active = 1 ORDER BY sort_order ASC");
            $projects = $proj_query->fetchAll();
        }
    }
} catch (Exception $e) {
    error_log('Portfolio data loading error: ' . $e->getMessage());
}

// Eğer veritabanından veri yüklenemezse fallback verileri kullan
if (empty($categories)) {
    $categories = [
        ['id' => 1, 'name' => 'Web Tasarım', 'slug' => 'web-tasarim', 'color' => '#2563eb'],
        ['id' => 2, 'name' => 'E-Ticaret', 'slug' => 'e-ticaret', 'color' => '#059669'],
        ['id' => 3, 'name' => 'Mobil Uygulama', 'slug' => 'mobil-uygulama', 'color' => '#dc2626'],
        ['id' => 4, 'name' => 'Kurumsal', 'slug' => 'kurumsal', 'color' => '#7c3aed'],
        ['id' => 5, 'name' => 'AI & Makine Öğrenmesi', 'slug' => 'ai-makine-ogrenmesi', 'color' => '#ea580c']
    ];
}

// Veritabanından portfolio projelerini çek
if ($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT 
                p.id, 
                p.title, 
                p.description as short_description,
                p.category as category_name,
                p.image_url as featured_image,
                p.project_url as demo_url,
                p.technologies,
                p.status,
                p.is_featured,
                pc.id as category_id
            FROM portfolio_projects p
            LEFT JOIN portfolio_categories pc ON p.category = pc.name
            WHERE p.is_published = 1
            ORDER BY p.sort_order ASC
        ");
        $db_projects = $stmt->fetchAll();
        
        if (!empty($db_projects)) {
            $projects = $db_projects;
        }
    } catch (PDOException $e) {
        error_log('Portfolio projects fetch error: ' . $e->getMessage());
    }
}

if (empty($projects)) {
    $projects = [
        [
            'id' => 1,
            'title' => 'AI Destekli Müşteri Analiz Platformu',
            'category_name' => 'AI & Makine Öğrenmesi',
            'category_id' => 5,
            'short_description' => 'Büyük veri analizi ve yapay zeka kullanarak müşteri davranışlarını analiz eden platform',
            'description' => 'Modern AI teknolojileri kullanarak müşteri verilerini analiz eden, gerçek zamanlı insights sunan ve tahminleme yapabilen kapsamlı analiz platformu.',
            'featured_image' => 'images/portfolio/ai-chatbot.jpg',
            'demo_url' => 'https://demo.example.com/ai-analysis',
            'technologies' => '["Python", "TensorFlow", "Apache Spark", "React", "PostgreSQL"]'
        ],
        [
            'id' => 2,
            'title' => 'Modern E-Ticaret Platformu',
            'category_name' => 'E-Ticaret',
            'category_id' => 2,
            'short_description' => 'Modern ve kullanıcı dostu e-ticaret platformu',
            'description' => 'Bu proje, modern e-ticaret ihtiyaçlarını karşılayan kapsamlı bir online satış platformudur. Responsive tasarım, güvenli ödeme sistemi ve gelişmiş yönetim paneli içerir.',
            'featured_image' => 'images/portfolio/ecommerce-project.jpg',
            'demo_url' => 'https://demo.example.com/ecommerce',
            'technologies' => '["PHP", "MySQL", "JavaScript", "Bootstrap", "Stripe API"]'
        ],
        [
            'id' => 3,
            'title' => 'Kripto Para Cüzdan Uygulaması',
            'category_name' => 'AI & Makine Öğrenmesi',
            'category_id' => 5,
            'short_description' => 'Güvenli, çoklu kripto para desteği olan mobil cüzdan uygulaması',
            'description' => 'DeFi entegrasyonu ve NFT desteği olan, güvenli blockchain teknolojisi kullanan modern kripto para cüzdan uygulaması.',
            'featured_image' => 'images/portfolio/crypto-tracker.svg',
            'demo_url' => 'https://demo.example.com/crypto-wallet',
            'technologies' => '["React Native", "Solidity", "Web3.js", "Node.js", "MongoDB"]'
        ],
        [
            'id' => 4,
            'title' => 'Kurumsal Web Sitesi',
            'category_name' => 'Kurumsal',
            'category_id' => 4,
            'short_description' => 'Profesyonel kurumsal web sitesi tasarımı',
            'description' => 'Şirketin kurumsal kimliğini yansıtan modern ve profesyonel web sitesi. SEO optimizasyonu, hızlı yükleme ve mobil uyumluluk özellikleri ile donatılmıştır.',
            'featured_image' => 'images/portfolio/corporate-website.jpg',
            'demo_url' => 'https://demo.example.com/corporate',
            'technologies' => '["HTML5", "CSS3", "JavaScript", "WordPress", "SEO"]'
        ],
        [
            'id' => 5,
            'title' => 'Mobil Fitness Uygulaması',
            'category_name' => 'Mobil Uygulama',
            'category_id' => 3,
            'short_description' => 'Kişiselleştirilmiş antrenman programları ve beslenme takibi',
            'description' => 'Modern fitness teknolojileri kullanan, kişiselleştirilmiş antrenman programları, beslenme takibi ve sosyal özellikler içeren kapsamlı fitness uygulaması.',
            'featured_image' => 'images/portfolio/mobile-app.jpg',
            'demo_url' => 'https://demo.example.com/fitness-app',
            'technologies' => '["React Native", "Firebase", "Node.js", "MongoDB", "Redux"]'
        ],
        [
            'id' => 6,
            'title' => 'IoT Akıllı Ev Platformu',
            'category_name' => 'AI & Makine Öğrenmesi',
            'category_id' => 5,
            'short_description' => 'Akıllı ev cihazlarını yöneten IoT platformu',
            'description' => 'Akıllı ev cihazlarını tek platformdan yöneten, enerji tasarrufu sağlayan ve güvenlik özellikleri olan modern IoT platformu.',
            'featured_image' => 'images/portfolio/smart-city.svg',
            'demo_url' => 'https://demo.example.com/iot-smart-home',
            'technologies' => '["React", "Node.js", "MQTT", "Python", "PostgreSQL"]'
        ],
        [
            'id' => 7,
            'title' => 'Modern SaaS CRM Sistemi',
            'category_name' => 'Web Tasarım',
            'category_id' => 1,
            'short_description' => 'Küçük ve orta ölçekli işletmeler için bulut tabanlı CRM',
            'description' => 'Modern SaaS mimarisi ile geliştirilmiş, bulut tabanlı müşteri ilişkileri yönetim sistemi. Gerçek zamanlı veri senkronizasyonu ve gelişmiş raporlama özellikleri.',
            'featured_image' => 'images/portfolio/responsive-design.jpg',
            'demo_url' => 'https://demo.example.com/saas-crm',
            'technologies' => '["Angular", "Spring Boot", "PostgreSQL", "Redis", "Docker"]'
        ]
    ];
}

require_once 'includes/header.php';
?>

<style>
/* Modern Portfolio Sayfası Stilleri */
.portfolio-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    position: relative;
    overflow-x: hidden;
    transition: background 0.3s ease;
}

/* Modern Portfolio Grid */
.modern-portfolio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    padding: 2rem 0;
}

/* Modern Portfolio Card */
.modern-portfolio-card {
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    cursor: pointer;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.modern-portfolio-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
    border-color: rgba(255, 255, 255, 0.4);
}

.modern-portfolio-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.modern-portfolio-card:hover::before {
    opacity: 1;
}

/* Modern Portfolio Image */
.modern-portfolio-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.modern-portfolio-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    filter: brightness(0.9) contrast(1.1);
}

.modern-portfolio-card:hover .modern-portfolio-image img {
    transform: scale(1.1) rotate(2deg);
    filter: brightness(1.1) contrast(1.2);
}

/* Modern Portfolio Overlay */
.modern-portfolio-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(5px);
}

.modern-portfolio-card:hover .modern-portfolio-overlay {
    opacity: 1;
}

/* Modern Portfolio Actions */
.modern-portfolio-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.modern-btn-action {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 12px 20px;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modern-btn-action:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Modern Portfolio Content */
.modern-portfolio-content {
    padding: 1.5rem;
    position: relative;
}

.modern-portfolio-category {
    color: #00d4ff;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.75rem;
    display: inline-block;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
}

.modern-portfolio-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 0.75rem;
    line-height: 1.3;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
}

.modern-portfolio-description {
    color: #e0e0e0;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1.25rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
}

/* Modern Tech Tags */
.modern-tech-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.modern-tech-tag {
    background: rgba(0, 212, 255, 0.2);
    backdrop-filter: blur(10px);
    color: #00d4ff;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    border: 1px solid rgba(0, 212, 255, 0.3);
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
}

.modern-tech-tag:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
}

/* Loading Animation */
.modern-loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
}

.modern-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top: 4px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .modern-portfolio-grid {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .modern-portfolio-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1rem 0;
    }
    
    .modern-portfolio-card {
        border-radius: 15px;
    }
    
    .modern-portfolio-image {
        height: 200px;
    }
    
    .modern-portfolio-content {
        padding: 1rem;
    }
    
    .modern-portfolio-title {
        font-size: 1.2rem;
    }
    
    .modern-portfolio-description {
        font-size: 0.9rem;
    }
    
    .modern-portfolio-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .modern-btn-action {
        width: 100%;
        padding: 10px 16px;
        font-size: 0.85rem;
    }
}

@media (max-width: 480px) {
    .modern-portfolio-grid {
        padding: 0.5rem 0;
    }
    
    .modern-portfolio-image {
        height: 180px;
    }
    
    .modern-portfolio-content {
        padding: 0.75rem;
    }
    
    .modern-portfolio-title {
        font-size: 1.1rem;
    }
    
    .modern-tech-tags {
        gap: 0.25rem;
    }
    
    .modern-tech-tag {
        font-size: 0.7rem;
        padding: 4px 8px;
    }
}

.portfolio-page::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="var(--text-primary)" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
    pointer-events: none;
}

.portfolio-container {
    position: relative;
    z-index: 1;
    padding: 80px 0;
}

.portfolio-header {
    text-align: center;
    margin-bottom: 80px;
    position: relative;
}

.portfolio-header::before {
    content: '';
    position: absolute;
    top: -50px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: var(--gradient-primary);
    border-radius: 2px;
    animation: slideInDown 1s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

.portfolio-header h1 {
    font-size: 4rem;
    font-weight: 800;
    color: var(--text-primary);
    text-shadow: 0 4px 8px var(--shadow-color);
    margin-bottom: 30px;
    text-shadow: 0 4px 8px rgba(0,0,0,0.3);
    animation: fadeInUp 1s ease-out 0.3s both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.portfolio-header p {
    font-size: 1.3rem;
    color: var(--text-secondary);
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
    text-shadow: 0 2px 4px var(--shadow-color);
    animation: fadeInUp 1s ease-out 0.6s both;
}

.portfolio-filters {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 60px;
    animation: fadeInUp 1s ease-out 0.9s both;
}

.filter-btn {
    background: var(--bg-secondary);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    padding: 12px 24px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.filter-btn:hover::before {
    left: 100%;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--bg-primary);
    border-color: var(--border-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow-color);
}

.filter-btn.active {
    background: var(--gradient-primary);
    border-color: transparent;
    box-shadow: var(--shadow-color);
}

.portfolio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 40px;
    margin-top: 40px;
    animation: fadeInUp 1s ease-out 1.2s both;
}

.portfolio-item {
    background: var(--bg-secondary);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    cursor: pointer;
}

.portfolio-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.portfolio-item:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--shadow-heavy);
}

.portfolio-item:hover::before {
    opacity: 1;
}

.portfolio-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.portfolio-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    filter: brightness(0.9) contrast(1.1);
}

.portfolio-item:hover .portfolio-image img {
    transform: scale(1.1) rotate(2deg);
    filter: brightness(1.1) contrast(1.2);
}

.portfolio-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
}

.portfolio-item:hover .portfolio-overlay {
    opacity: 1;
}

.portfolio-actions {
    display: flex;
    gap: 15px;
    align-items: center;
}

.btn-action {
    background: var(--bg-secondary);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    padding: 12px 20px;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: auto;
    height: auto;
}

.btn-action:hover {
    background: var(--bg-primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-color);
    color: var(--text-primary);
}

.portfolio-content {
    padding: 25px;
    position: relative;
}

.portfolio-category {
    color: var(--accent-color);
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 12px;
    display: inline-block;
}

.portfolio-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 12px;
    line-height: 1.3;
    text-shadow: 0 2px 4px var(--shadow-color);
}

.portfolio-description {
    color: var(--text-secondary);
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 20px;
    text-shadow: 0 1px 2px var(--shadow-color);
}

.portfolio-tech {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tech-tag {
    background: var(--bg-secondary);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: var(--text-primary);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.tech-tag:hover {
    background: var(--bg-primary);
    transform: translateY(-1px);
}

.hidden {
    display: none;
}

/* Responsive Tasarım ve Mobil Optimizasyon */
@media (max-width: 1200px) {
    .portfolio-grid {
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
    }
    
    .portfolio-header h1 {
        font-size: 3.5rem;
    }
}

@media (max-width: 768px) {
    .portfolio-container {
        padding: 60px 20px;
    }
    
    .portfolio-header {
        margin-bottom: 60px;
    }
    
    .portfolio-header h1 {
        font-size: 2.8rem;
        margin-bottom: 20px;
    }
    
    .portfolio-header p {
        font-size: 1.1rem;
        padding: 0 10px;
    }
    
    .portfolio-filters {
        margin-bottom: 40px;
        gap: 10px;
    }
    
    .filter-btn {
        padding: 10px 18px;
        font-size: 0.85rem;
    }
    
    .portfolio-grid {
        grid-template-columns: 1fr;
        gap: 25px;
        margin-top: 30px;
    }
    
    .portfolio-item {
        margin: 0 10px;
    }
    
    .portfolio-image {
        height: 220px;
    }
    
    .portfolio-content {
        padding: 20px;
    }
    
    .portfolio-title {
        font-size: 1.2rem;
    }
    
    .portfolio-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-action {
        width: 100%;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .portfolio-header h1 {
        font-size: 2.2rem;
    }
    
    .portfolio-header p {
        font-size: 1rem;
    }
    
    .filter-btn {
        padding: 8px 14px;
        font-size: 0.8rem;
    }
    
    .portfolio-image {
        height: 200px;
    }
    
    .portfolio-content {
        padding: 15px;
    }
    
    .tech-tag {
        font-size: 0.7rem;
        padding: 4px 8px;
    }
}

/* Gelişmiş Animasyonlar */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

.portfolio-item {
    animation: float 6s ease-in-out infinite;
}

.portfolio-item:nth-child(2n) {
    animation-delay: -2s;
}

.portfolio-item:nth-child(3n) {
    animation-delay: -4s;
}

.filter-btn.active {
    animation: pulse 2s ease-in-out infinite;
}

/* Scroll Animasyonları */
.portfolio-item {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.8s ease-out forwards, float 6s ease-in-out infinite;
}

.portfolio-item:nth-child(1) { animation-delay: 0.1s, 0s; }
.portfolio-item:nth-child(2) { animation-delay: 0.2s, -2s; }
.portfolio-item:nth-child(3) { animation-delay: 0.3s, -4s; }
.portfolio-item:nth-child(4) { animation-delay: 0.4s, -1s; }
.portfolio-item:nth-child(5) { animation-delay: 0.5s, -3s; }
.portfolio-item:nth-child(6) { animation-delay: 0.6s, -5s; }

/* Loading Animasyonu */
.portfolio-loading {
    display: none;
    text-align: center;
    padding: 40px;
    color: var(--text-primary);
    font-size: 1.2rem;
}

.portfolio-loading.active {
    display: block;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid var(--border-color);
    border-top: 4px solid var(--text-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

    <!-- Page Header -->
    <section class="modern-section modern-section--hero main-content">
        <div class="modern-container">
            <div class="modern-text-center">
                <div class="modern-badge modern-badge--outline modern-m-4">
                    <i class="fas fa-briefcase me-2"></i>Portfolio
                </div>
                <h1 class="modern-heading modern-heading--xl"><?php echo getTextContent('portfolio_header_title', 'Portfolio'); ?></h1>
                <p class="modern-text modern-text--lg">
                    <?php echo getTextContent('portfolio_header_subtitle', 'Yaradıcı layihələrimizi kəşf edin və rəqəmsal dünyada yaratdığımız təsirli həlləri görün'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Portfolio Content -->
    <section class="modern-section">
        <div class="modern-container">

        <!-- Portfolio Filters -->
        <div class="modern-flex modern-flex-wrap modern-justify-center modern-m-8">
            <button class="modern-btn modern-btn--ghost modern-btn--sm modern-m-2 active" data-filter="all"><?php echo getTextContent('portfolio_category_all', 'Hamısı'); ?></button>
            <?php foreach ($categories as $category): ?>
                <button class="modern-btn modern-btn--ghost modern-btn--sm modern-m-2" data-filter="<?php echo strtolower(str_replace(' ', '-', $category['name'])); ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Portfolio Loading -->
        <div class="modern-text-center" id="portfolioLoading" style="display: none;">
            <div class="modern-m-4">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
            </div>
            <p class="modern-text"><?php echo getTextContent('portfolio_loading_text', 'Layihələr yüklənir...'); ?></p>
        </div>

        <!-- Modern Portfolio Grid -->
        <div class="modern-portfolio-grid" id="portfolioGrid">
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $index => $project): ?>
                        <div class="modern-portfolio-card modern-animate--fadeInUp modern-animate--delay-<?php echo ($index % 5) + 1; ?>" data-category="<?php echo strtolower(str_replace(' ', '-', $project['category_name'] ?? 'genel')); ?>">
                            <div class="modern-portfolio-image">
                                <img src="<?php echo htmlspecialchars($project['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($project['title']); ?>" loading="lazy"
                                     onerror="this.src='/images/placeholder.svg'">
                                <div class="modern-portfolio-overlay">
                                    <div class="modern-portfolio-actions">
                                        <a href="portfolio-detail.php?id=<?php echo $project['id']; ?>" class="modern-btn-action">
                                            <i class="fas fa-eye me-2"></i> <?php echo getTextContent('portfolio_button_details', 'Təfərrüatları Gör'); ?>
                                        </a>
                                        <?php if (!empty($project['demo_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($project['demo_url']); ?>" class="modern-btn-action" target="_blank">
                                                <i class="fas fa-external-link-alt me-2"></i> <?php echo getTextContent('portfolio_button_demo', 'Demo'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modern-portfolio-content">
                                <div class="modern-portfolio-category">
                                    <?php echo htmlspecialchars($project['category_name'] ?? 'Genel'); ?>
                                </div>
                                
                                <h3 class="modern-portfolio-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                                <p class="modern-portfolio-description"><?php echo htmlspecialchars(substr($project['short_description'] ?? '', 0, 120)) . '...'; ?></p>
                                
                                <div class="modern-tech-tags">
                                    <?php 
                                    $technologies = [];
                                    if (!empty($project['technologies'])) {
                                        // JSON formatında ise decode et
                                        if (is_string($project['technologies']) && $project['technologies'][0] === '[') {
                                            $technologies = json_decode($project['technologies'], true) ?: [];
                                        } else {
                                            // Comma-separated string ise explode et
                                            $technologies = explode(',', $project['technologies']);
                                        }
                                    }
                                    
                                    foreach (array_slice($technologies, 0, 3) as $tech): 
                                        if (trim($tech)): 
                                    ?>
                                        <span class="modern-tech-tag"><?php echo htmlspecialchars(trim($tech)); ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    if (count($technologies) > 3): 
                                    ?>
                                        <span class="modern-tech-tag">+<?php echo count($technologies) - 3; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="modern-card modern-text-center">
                        <div class="modern-card__body">
                            <div class="modern-alert modern-alert--info">
                                <h4 class="modern-heading modern-heading--h4"><?php echo getTextContent('portfolio_no_projects_title', 'Henüz layihə yoxdur'); ?></h4>
                                <p class="modern-text"><?php echo getTextContent('portfolio_no_projects_desc', 'Tezliklə möhtəşəm layihələr əlavə olunacaq!'); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Portfolio Loading State -->
                <div class="portfolio-loading" id="portfolioLoading" style="display: none;">
                    <div class="loading-spinner"></div>
                    <p>Layihələr yüklənir...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><?php echo getTextContent('portfolio_modal_title', 'Layihə Təfərrüatları'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Yüklənir...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Modern Portfolio JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');
    const portfolioGrid = document.getElementById('portfolioGrid');
    const portfolioLoading = document.getElementById('portfolioLoading');

    // Intersection Observer for scroll animations
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

    // Observe all portfolio items
    portfolioItems.forEach(item => {
        observer.observe(item);
    });

    // Enhanced filtering with animations
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Show loading
            portfolioLoading.classList.add('active');
            portfolioGrid.style.opacity = '0.5';

            // Update active button with animation
            filterBtns.forEach(b => {
                b.classList.remove('active');
                b.style.transform = 'scale(1)';
            });
            
            this.classList.add('active');
            this.style.transform = 'scale(1.05)';
            
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);

            const filter = this.getAttribute('data-filter');

            // Animate out items
            portfolioItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.transform = 'translateY(20px)';
                    item.style.opacity = '0';
                }, index * 50);
            });

            // Filter and animate in items
            setTimeout(() => {
                let visibleIndex = 0;
                portfolioItems.forEach(item => {
                    const shouldShow = filter === 'all' || item.getAttribute('data-category') === filter;
                    
                    if (shouldShow) {
                        item.style.display = 'block';
                        setTimeout(() => {
                            item.style.transform = 'translateY(0)';
                            item.style.opacity = '1';
                        }, visibleIndex * 100);
                        visibleIndex++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Hide loading
                setTimeout(() => {
                    portfolioLoading.classList.remove('active');
                    portfolioGrid.style.opacity = '1';
                }, 300);
            }, 500);
        });
    });

    // Modern Portfolio Card Animations
    window.initPortfolioAnimations = function() {
        const cards = document.querySelectorAll('.modern-portfolio-card');
        
        // Intersection Observer for scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            observer.observe(card);
        });
    };

    // Modern Hover Effects
    window.initModernHoverEffects = function() {
        const cards = document.querySelectorAll('.modern-portfolio-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
                this.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.25)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.1)';
            });
        });
    };

    // Modern Image Loading
    window.initModernImageLoading = function() {
        const images = document.querySelectorAll('.modern-portfolio-image img');
        
        images.forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
                this.style.transform = 'scale(1)';
            });
            
            img.addEventListener('error', function() {
                this.src = 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=600&fit=crop&crop=center';
            });
            
            // Initial state
            img.style.opacity = '0';
            img.style.transform = 'scale(1.1)';
            img.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        });
    };

    // Enhanced hover effects
    portfolioItems.forEach(item => {
        const image = item.querySelector('img');
        const overlay = item.querySelector('.portfolio-overlay');
        
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            if (image) {
                image.style.transform = 'scale(1.1) rotate(2deg)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            if (image) {
                image.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    // Smooth scrolling for internal links
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

    // Parallax effect for background
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.portfolio-page');
        if (parallax) {
            const speed = scrolled * 0.5;
            parallax.style.backgroundPosition = `center ${speed}px`;
        }
    });

    // Performance optimization: Lazy loading for images
    const images = document.querySelectorAll('img[loading="lazy"]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => {
        imageObserver.observe(img);
    });

    // Add loading animation on page load
    setTimeout(() => {
        portfolioItems.forEach((item, index) => {
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }, 300);

    // Touch support for mobile devices
    let touchStartY = 0;
    let touchEndY = 0;

    document.addEventListener('touchstart', e => {
        touchStartY = e.changedTouches[0].screenY;
    });

    document.addEventListener('touchend', e => {
        touchEndY = e.changedTouches[0].screenY;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartY - touchEndY;
        
        if (Math.abs(diff) > swipeThreshold) {
            // Add swipe animations or actions here if needed
        }
    }
});

// Modal Function
function openModal(projectId) {
    const modal = new bootstrap.Modal(document.getElementById('projectModal'));
    const modalBody = document.getElementById('modalBody');
    const modalTitle = document.getElementById('modalTitle');
    
    // Show loading
    modalBody.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden"><?php echo getTextContent('portfolio_modal_loading', 'Yükleniyor...'); ?></span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch project details
    fetch(`api/portfolio.php?action=get_project&id=${projectId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const project = data.project;
                modalTitle.textContent = project.title;
                
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <img src="${project.image_url}" class="img-fluid rounded" alt="${project.title}" onerror="this.src='/images/placeholder.svg'">
                        </div>
                        <div class="col-md-6">
                            <h5>Proje Hakkında</h5>
                            <p>${project.description}</p>
                            
                            <h6>Kategori</h6>
                            <span class="badge bg-primary">${project.category}</span>
                            
                            <h6 class="mt-3">Teknolojiler</h6>
                            <div class="d-flex flex-wrap gap-2">
                                ${project.technologies.split(',').map(tech => 
                                    `<span class="badge bg-secondary">${tech.trim()}</span>`
                                ).join('')}
                            </div>
                            
                            ${project.project_url ? `
                                <div class="mt-3">
                                    <a href="${project.project_url}" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt"></i> Projeyi Görüntüle
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger">Proje detayları yüklenemedi.</div>';
            }
        })
        .catch(error => {
            modalBody.innerHTML = '<div class="alert alert-danger">Bir hata oluştu.</div>';
        });
}

// Utility function for smooth animations
function animateElement(element, animation, duration = 300) {
    return new Promise(resolve => {
        element.style.animation = `${animation} ${duration}ms ease-out`;
        setTimeout(() => {
            element.style.animation = '';
            resolve();
        }, duration);
    });
}

// Error handling for images
document.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG') {
        e.target.src = '/images/placeholder.svg';
        e.target.alt = 'Görsel yüklenemedi';
        e.target.classList.add('image-error');
    }
}, true);

// Portfolio API error handling
function handlePortfolioError(error, context = '') {
    console.error(`Portfolio ${context} hatası:`, error);
    
    const errorMessage = document.createElement('div');
    errorMessage.className = 'alert alert-danger mt-3';
    errorMessage.innerHTML = `
        <h5>Hata Oluştu</h5>
        <p>Portfolio yüklenirken bir hata oluştu. Lütfen sayfayı yenileyin.</p>
        <small>Hata: ${error.message || 'Bilinmeyen hata'}</small>
    `;
    
    const portfolioGrid = document.getElementById('portfolioGrid');
    if (portfolioGrid) {
        portfolioGrid.appendChild(errorMessage);
    }
}

// Portfolio verilerini API'den yükle
function loadPortfolioFromAPI() {
    const portfolioLoading = document.getElementById('portfolioLoading');
    const portfolioGrid = document.getElementById('portfolioGrid');
    
    if (portfolioLoading) portfolioLoading.style.display = 'block';
    
    fetch('api/portfolio.php?action=get_projects')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.projects && data.projects.length > 0) {
                // API'den gelen projeleri işle ve göster
                console.log('API\'den projeler yüklendi:', data.projects);
                // Burada dinamik olarak portfolio grid'i güncellenebilir
            } else {
                console.log('API\'den proje bulunamadı veya hata var');
            }
        })
        .catch(error => {
            handlePortfolioError(error, 'API yükleme');
        })
        .finally(() => {
            if (portfolioLoading) portfolioLoading.style.display = 'none';
        });
}

// Modern Portfolio filtreleme
function filterPortfolio(category) {
    const cards = document.querySelectorAll('.modern-portfolio-card[data-category]');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    // Loading göster
    if (loadingSpinner) loadingSpinner.style.display = 'flex';
    
    setTimeout(() => {
        cards.forEach((card, index) => {
            if (category === 'all' || card.dataset.category === category) {
                card.style.display = 'block';
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('modern-animate--fadeInUp');
            } else {
                card.style.display = 'none';
                card.classList.remove('modern-animate--fadeInUp');
            }
        });
        
        // Loading gizle
        if (loadingSpinner) loadingSpinner.style.display = 'none';
    }, 300);
}

// Initialize modern portfolio features
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modern portfolio features with error handling
    try {
        if (typeof window.initPortfolioAnimations === 'function') {
            window.initPortfolioAnimations();
        }
    } catch (e) {
        console.warn('initPortfolioAnimations failed:', e);
    }
    
    try {
        if (typeof window.initModernHoverEffects === 'function') {
            window.initModernHoverEffects();
        }
    } catch (e) {
        console.warn('initModernHoverEffects failed:', e);
    }
    
    try {
        if (typeof window.initModernImageLoading === 'function') {
            window.initModernImageLoading();
        }
    } catch (e) {
        console.warn('initModernImageLoading failed:', e);
    }
    
    // Initialize existing features with error handling
    if (typeof initAnimations === 'function') initAnimations();
    if (typeof initPortfolioFiltering === 'function') initPortfolioFiltering();
    if (typeof initSmoothScrolling === 'function') initSmoothScrolling();
    if (typeof initLazyLoading === 'function') initLazyLoading();
    if (typeof initPerformanceMonitoring === 'function') initPerformanceMonitoring();
});
</script>

<?php require_once 'includes/footer.php'; ?>