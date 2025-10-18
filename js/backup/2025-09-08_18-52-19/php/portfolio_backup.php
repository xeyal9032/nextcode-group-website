<?php
// NextCode Group - Portfolio Səhifəsi
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
ini_set('display_errors', 0); // Production için kapalı
ini_set('log_errors', 1);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Dynamic content variables
$page_title = 'Portfolio - NextCode Group';
$meta_description = 'NextCode Group portfolio - tamamladığımız layihələr və müştəri işlərimiz.';
$current_page = 'portfolio';

// Portfolio projects data (dynamic from database)
try {
    $portfolio_query = $pdo->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM portfolio_projects p 
        LEFT JOIN portfolio_categories c ON p.category_id = c.id 
        WHERE p.is_published = 1 
        ORDER BY p.sort_order ASC, p.created_at DESC
    ");
    $portfolio_query->execute();
    $portfolio_projects = $portfolio_query->fetchAll();
    
} catch (PDOException $e) {
    error_log('Portfolio query failed: ' . $e->getMessage());
    $portfolio_projects = [];
}

// Portfolio categories
try {
    $categories_query = $pdo->prepare("SELECT * FROM portfolio_categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC");
    $categories_query->execute();
    $portfolio_categories = $categories_query->fetchAll();
    
} catch (PDOException $e) {
    error_log('Categories query failed: ' . $e->getMessage());
    $portfolio_categories = [];
}

// Filter by category if specified
$selected_category = $_GET['category'] ?? 'all';
if ($selected_category !== 'all' && !empty($portfolio_projects)) {
    $portfolio_projects = array_filter($portfolio_projects, function($project) use ($selected_category) {
        return $project['category_id'] == $selected_category;
    });
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Modern Web & Mobil Uygulama Projeleri | NextCode</title>
    <meta name="description" content="NextCode'un gerçekleştirdiği modern web siteleri, mobil uygulamalar, AI destekli projeler ve kurumsal çözümler. Müşterilerimize sunduğumuz kaliteli yazılım projelerini keşfedin.">
    <meta name="keywords" content="web tasarım, mobil uygulama, e-ticaret, kurumsal web sitesi, AI projeler, yazılım geliştirme">
    <meta property="og:title" content="Portfolio - Modern Web & Mobil Uygulama Projeleri | NextCode">
    <meta property="og:description" content="NextCode'un gerçekleştirdiği modern web siteleri, mobil uygulamalar ve AI destekli projeler.">
    <meta property="og:type" content="website">
    <meta name="robots" content="index, follow">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
            <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Fonts CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/cookie-consent.css">
    <link rel="stylesheet" href="assets/css/portfolio.css">
    <link rel="stylesheet" href="css/portfolio-detail.css">
    <style>
        /* Portfolio Hover Effects - En basit */
        .portfolio-card {
            cursor: pointer;
        }
        
        .portfolio-overlay .btn {
            margin: 5px;
        }
        
        /* Project Image Placeholder Styles - Inline olarak kullanılıyor */
        
        /* Dark Theme Styles */
        .dark-theme {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
        }
        
        .dark-theme .card {
            background-color: #2d2d2d !important;
            border-color: #404040 !important;
        }
        
        .dark-theme .card-title {
            color: #ffffff !important;
        }
        
        .dark-theme .card-text {
            color: #cccccc !important;
        }
        
        .dark-theme .badge {
            background-color: #667eea !important;
        }
        
        .dark-theme .btn-outline-primary {
            color: #667eea !important;
            border-color: #667eea !important;
        }
        
        .dark-theme .btn-outline-primary:hover {
            background-color: #667eea !important;
            color: #ffffff !important;
        }
        
        /* Modern Hero Section */
        .modern-hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        
        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        .hero-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 2px, transparent 2px),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 2px, transparent 2px),
                radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 100px 100px, 150px 150px, 80px 80px;
            animation: float 20s ease-in-out infinite;
        }
        
        .hero-gradient {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 12px 24px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            animation: fadeInUp 1s ease-out 0.2s both;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #fff 0%, #f093fb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-description {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease-out 0.4s both;
        }
        
        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-bottom: 2.5rem;
            animation: fadeInUp 1s ease-out 0.6s both;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            font-weight: 500;
        }
        
        .hero-actions {
            display: flex;
            gap: 1rem;
            animation: fadeInUp 1s ease-out 0.8s both;
        }
        
        .btn-modern {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 32px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .btn-modern.primary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .btn-modern.primary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 255, 255, 0.2);
        }
        
        .btn-modern.secondary {
            background: transparent;
            color: white;
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        .btn-modern.secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
        }
        
        .hero-visual {
            position: relative;
            z-index: 2;
        }
        

        
        /* Animations */
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
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        /* Modern Portfolio Styles */
        .portfolio-filters {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: #666;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .modern-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .modern-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
        }
        
        .portfolio-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(118, 75, 162, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 100;
            visibility: hidden;
        }
        
        .portfolio-card:hover .portfolio-overlay {
            opacity: 1 !important;
            visibility: visible !important;
            display: flex !important;
        }
        
        /* Ek güvenlik için */
        .portfolio-overlay {
            pointer-events: auto;
            cursor: pointer;
        }
        
        .portfolio-overlay .btn {
            pointer-events: auto;
            z-index: 101;
            margin: 5px;
            cursor: pointer;
        }
        
        /* Hover durumunda overlay'i zorla göster */
        /* Hover overlay kaldırıldı - sadece card body'deki butonlar kullanılıyor */
        
        .overlay-content {
            display: flex;
            gap: 1rem;
        }
        
        .btn-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .btn-icon:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
            color: white;
        }
        
        .project-category {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .project-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        
        .project-description {
            color: #718096;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .tech-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .tech-tag {
            background: #f7fafc;
            color: #4a5568;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid #e2e8f0;
        }
        
        .filter-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(102, 126, 234, 0.3);
            color: #667eea;
            padding: 14px 28px;
            margin: 8px;
            border-radius: 30px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-weight: 600;
            font-size: 0.95rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);
            cursor: pointer;
        }
        
        .filter-btn:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            border-color: transparent;
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            border-color: transparent;
        }
        
        /* Modern Portfolio Grid Layout */
        .portfolio-items {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            padding: 40px 0;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .portfolio-grid-item {
            width: 100%;
        }
        
        .portfolio-item {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 350px;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
        }
        
        .portfolio-item:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 50px rgba(102, 126, 234, 0.25);
        }
        
        .portfolio-image {
            position: relative;
            overflow: hidden;
            height: 100%;
            border-radius: 20px;
        }
        
        .portfolio-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.6s ease;
            filter: brightness(0.9);
        }
        
        .portfolio-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(102, 126, 234, 0.95) 0%, 
                rgba(118, 75, 162, 0.95) 50%,
                rgba(255, 107, 107, 0.95) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            backdrop-filter: blur(10px);
        }
        
        .portfolio-card:hover .portfolio-overlay {
            opacity: 1;
        }
        
        .portfolio-card:hover .portfolio-image img {
            transform: scale(1.15);
            filter: brightness(1.1);
        }
        
        .portfolio-content {
            text-align: center;
            color: white;
            padding: 35px 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .portfolio-content h4 {
            margin-bottom: 15px;
            font-size: 2rem;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        
        .client-name {
            margin-bottom: 20px;
            opacity: 0.95;
            font-size: 1.2rem;
            font-weight: 500;
            text-shadow: 0 1px 5px rgba(0, 0, 0, 0.2);
        }
        
        .portfolio-tech {
            margin-bottom: 28px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            max-width: 100%;
        }
        
        .tech-tag {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            padding: 10px 18px;
            border-radius: 25px;
            font-size: 0.95rem;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(15px);
            transition: all 0.3s ease;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            white-space: nowrap;
        }
        
        .tech-tag:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateY(-2px);
        }
        
        .portfolio-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .portfolio-actions .btn {
            padding: 14px 28px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 120px;
        }
        
        .portfolio-actions .btn-light {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        .portfolio-actions .btn-light:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 255, 255, 0.2);
        }
        
        .portfolio-actions .btn-primary {
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
            border-color: rgba(255, 255, 255, 0.9);
        }
        
        .portfolio-actions .btn-primary:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 255, 255, 0.3);
        }
        
        .portfolio-search {
            padding: 30px 0;
        }
        
        .search-wrapper {
            position: relative;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 1.1rem;
            z-index: 2;
        }
        
        .search-input {
            width: 100%;
            padding: 18px 20px 18px 55px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            font-size: 1rem;
            font-weight: 500;
            color: #333;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.1);
        }
        
        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 6px 30px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }
        
        .search-input::placeholder {
            color: #999;
            font-weight: 400;
        }
        
        .testimonials {
            background: #f8f9fa;
            padding: 80px 0;
        }
        
        .testimonial-item {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin: 20px 0;
        }
        
        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        .testimonial-author {
            font-weight: bold;
            color: #667eea;
        }
        
        .load-more-section {
            padding: 60px 0 40px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 18px 40px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #5a6fd8 0%, #6a5acd 100%);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .portfolio-items {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 15px;
            }
            
            .portfolio-item {
                height: 280px;
            }
            
            .portfolio-content {
                padding: 25px 20px;
            }
            
            .portfolio-content h4 {
                font-size: 1.6rem;
            }
            
            .filter-btn {
                padding: 12px 20px;
                margin: 5px;
                font-size: 0.9rem;
            }
            
            .search-input {
                padding: 16px 18px 16px 50px;
            }
        }
        
        @media (max-width: 480px) {
            .portfolio-content h4 {
                font-size: 1.4rem;
            }
            
            .portfolio-actions {
                flex-direction: column;
                gap: 8px;
            }
            
            .portfolio-actions .btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            .tech-tag {
                font-size: 0.8rem;
                padding: 6px 12px;
            }
        }
            transform: translateY(-2px);
        }
        
        /* Portfolio Statistics Cards */
        .stat-card {
            background: white;
            padding: 2rem 1rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1rem;
            color: #718096;
            font-weight: 600;
        }
        
        /* Navbar CSS Fixes */
        .navbar-nav {
            align-items: center;
            gap: 0.5rem;
        }
        
        .navbar-nav .nav-item {
            display: flex;
            align-items: center;
        }
        
        /* Theme toggle ve contact arasında geniş boşluk */
        #themeToggle { margin-right: 1rem; }
        .navbar-nav .btn.btn-primary { margin-left: 0.25rem; }
        
        /* Mobile responsive fixes */
        @media (max-width: 991.98px) {
            .navbar-nav {
                text-align: center;
                padding-top: 1rem;
                width: 100%;
            }
            
            .navbar-nav .nav-item {
                margin-bottom: 0.5rem;
                justify-content: center;
                width: 100%;
            }
            
            .navbar-nav .btn {
                width: auto;
                margin: 0.25rem 0;
            }
        }
        
        /* Prevent button overlap */
        .navbar-nav .nav-item:last-child {
            margin-left: 0;
        }
        
        .navbar-collapse {
            flex-grow: 1;
        }
        

        


    </style>

    

    </script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.php">
                <i class="fas fa-code text-primary me-2"></i>
                <span class="text-gradient">NextCode</span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo ($current_page == 'index') ? 'active' : ''; ?>" href="/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo ($current_page == 'about') ? 'active' : ''; ?>" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo ($current_page == 'services') ? 'active' : ''; ?>" href="services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo ($current_page == 'portfolio') ? 'active' : ''; ?>" href="portfolio.php">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo ($current_page == 'blog') ? 'active' : ''; ?>" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo ($current_page == 'pricing') ? 'active' : ''; ?>" href="pricing.php">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo ($current_page == 'faq') ? 'active' : ''; ?>" href="faq.php">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo ($current_page == 'contact') ? 'active' : ''; ?>" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item ms-2">
                        <button id="themeToggle" class="btn btn-outline-secondary btn-sm me-4" title="Tema Dəyiş">
                            <i class="fas fa-moon" id="themeIcon"></i>
                        </button>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm px-3 ms-1" href="contact.php">
                            <i class="fas fa-phone me-1"></i>
                            Contact
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modern Hero Section -->
    <section class="modern-hero">
        <div class="hero-background">
            <div class="hero-particles"></div>
            <div class="hero-gradient"></div>
        </div>
        <div class="container">
            <div class="row justify-content-center min-vh-100">
                <div class="col-lg-8 text-center">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="fas fa-star"></i>
                            <span>Premium Portfolio</span>
                        </div>
                        <h1 class="hero-title">
                            Yaradıcı <span class="gradient-text">Həllər</span><br>
                            Rəqəmsal <span class="gradient-text">Gələcək</span>
                        </h1>
                        <p class="hero-description">
                            Müştərilərimizin xəyallarını gerçəkləşdirən, innovativ və müasir 
                            texnologiyalarla hazırlanmış layihələrimizi kəşf edin.
                        </p>
                        <div class="hero-stats">
                            <div class="stat-item">
                                <div class="stat-number"><?php echo count($portfolio_projects); ?>+</div>
                                <div class="stat-label">Uğurlu Layihə</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo count($portfolio_categories); ?>+</div>
                                <div class="stat-label">Xidmət Kategoriyası</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">24/7</div>
                                <div class="stat-label">Dəstək Xidməti</div>
                            </div>
                        </div>
                        <div class="hero-actions">
                            <a href="#portfolio" class="btn-modern primary">
                                <span>Layihələri Kəşf Et</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                            <a href="contact.php" class="btn-modern secondary">
                                <span>Əlaqə Saxla</span>
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern Portfolio Section -->
    <section id="portfolio" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-4 fw-bold mb-3">
                        <span class="gradient-text">Portfolio</span>
                    </h2>
                    <p class="lead text-muted">Ən son layihələrimiz və uğur hekayələrimiz</p>
                </div>
            </div>
            

            
            <!-- Portfolio Filter Buttons -->
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <div class="portfolio-filters">
                        <button class="filter-btn active" data-filter="all">
                            <i class="fas fa-th"></i>
                            Hamısı
                        </button>
                        <?php foreach ($portfolio_categories as $category): ?>
                            <button class="filter-btn" data-filter="<?php echo strtolower(str_replace(' ', '-', $category['name'])); ?>">
                                <i class="<?php echo $category['icon'] ?: 'fas fa-folder'; ?>"></i>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Portfolio Search -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="portfolio-search">
                        <div class="search-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" id="portfolioSearch" placeholder="Proje adı, teknoloji veya müşteri adı ile arayın...">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Portfolio Grid - Bootstrap Grid Kullanarak -->
            <div class="row" id="portfolioGrid">
                <?php if (!empty($portfolio_projects)): ?>
                    <?php foreach ($portfolio_projects as $project): ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4 portfolio-card" data-category="<?php echo strtolower(str_replace(' ', '-', $project['category_name'] ?? 'web')); ?>" style="position: relative;">
                            <div class="card h-100 shadow-sm">
                                <div class="portfolio-image" style="height: 200px; overflow: hidden; position: relative;">
                                    <!-- Proje Fotoğrafı - Güzel Placeholder -->
                                    <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; position: relative; overflow: hidden;">
                                        <!-- Arka plan deseni -->
                                        <div style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 20px 20px;"></div>
                                        
                                        <!-- Ana içerik -->
                                        <div style="text-align: center; z-index: 2; position: relative;">
                                            <div style="font-size: 48px; margin-bottom: 15px; opacity: 0.9;">💻</div>
                                            <div style="font-size: 16px; font-weight: bold; margin-bottom: 8px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);"><?php echo htmlspecialchars($project['title']); ?></div>
                                            <div style="font-size: 12px; opacity: 0.9; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"><?php echo htmlspecialchars($project['category_name'] ?? 'Web Development'); ?></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Hover Overlay - Tamamen Kaldırıldı -->
                                </div>
                                <div class="card-body">
                                    <div class="project-category mb-2">
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($project['category_name'] ?? 'Web Development'); ?></span>
                                    </div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars(substr($project['short_description'] ?: $project['description'], 0, 100)) . '...'; ?></p>
                                    <div class="tech-stack mb-3">
                                        <?php 
                                        $technologies = [];
                                        if (isset($project['technologies'])) {
                                            if (is_string($project['technologies'])) {
                                                $technologies = json_decode($project['technologies'], true) ?: explode(',', $project['technologies']);
                                            } elseif (is_array($project['technologies'])) {
                                                $technologies = $project['technologies'];
                                            }
                                        }
                                        if (!empty($technologies)) {
                                            foreach (array_slice($technologies, 0, 3) as $tech) {
                                                echo '<span class="badge bg-secondary me-1">' . htmlspecialchars(trim($tech)) . '</span>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="portfolio-actions">
                                        <a href="portfolio-detail.php?id=<?php echo $project['id']; ?>" class="btn btn-primary btn-lg" style="width: 100%; margin-bottom: 10px; padding: 12px 20px; font-weight: bold; font-size: 16px;">
                                            <i class="fas fa-eye me-2"></i>
                                            Detallar
                                        </a>
                                        <?php if ($project['project_url']): ?>
                                            <a href="<?php echo htmlspecialchars($project['project_url']); ?>" class="btn btn-outline-primary btn-sm" target="_blank" style="width: 100%;">
                                                <i class="fas fa-external-link-alt me-1"></i>
                                                Demo
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback projects if database is empty -->
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <p class="mb-0">Portfolio projeleri yükleniyor...</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Load More Button -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <button id="loadMoreBtn" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        Daha Çox Göstər
                    </button>
                </div>
            </div>
            
            <!-- Portfolio Statistics -->
            <section class="portfolio-stats mt-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-rocket text-primary"></i>
                                </div>
                                <div class="stat-number"><?php echo count($portfolio_projects); ?>+</div>
                                <div class="stat-label">Tamamlanan Layihə</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-layer-group text-success"></i>
                                </div>
                                <div class="stat-number"><?php echo count($portfolio_categories); ?>+</div>
                                <div class="stat-label">Xidmət Kategoriyası</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                                <div class="stat-number">5.0</div>
                                <div class="stat-label">Müştəri Rəyinqi</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-clock text-info"></i>
                                </div>
                                <div class="stat-number">24/7</div>
                                <div class="stat-label">Dəstək Xidməti</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>





    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold mb-3">Layihənizi Həyata Keçirməyə Hazırsınız?</h2>
                    <p class="lead mb-0">Bizimlə əlaqə saxlayın və xəyallarınızı gerçəkləşdirək. Pulsuz məsləhət alın!</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <a href="contact.php" class="btn btn-light btn-lg px-4 py-3">
                        <i class="fas fa-rocket me-2"></i>
                        Layihəyə Başla
                    </a>
                </div>
            </div>
        </div>
    </section>








    <!-- Footer -->
    <footer class="footer">
        <div class="container-custom">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-brand">
                        <i class="fas fa-code"></i>
                        <span>NextCode</span>
                    </div>
                    <p class="footer-description">
                        Rəqəmsal dünyada uğurunuz üçün peşəkar həllər təklif edirik.
                    </p>
                    <div class="footer-social">
                                                    <a href="https://www.facebook.com/OstWind.LLC/?locale=ru_RU" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.instagram.com/nextcodegroup/" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Services</h4>
                    <ul class="footer-links">
                        <li><a href="services.php">SEO Optimizasiya</a></li>
                        <li><a href="services.php">Sosial Media</a></li>
                        <li><a href="services.php">Brendinq</a></li>
                        <li><a href="services.php">Reklam Kampaniyaları</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Şirkət</h4>
                    <ul class="footer-links">
                        <li><a href="about.php">About</a></li>
                        <li><a href="portfolio.php">Portföy</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Contact</h4>
                    <div class="footer-contact">
                        <p><i class="fas fa-envelope"></i> <span data-email>xeyalcemilli9032@gmail.com</span></p>
                        <p><i class="fas fa-phone"></i> <span data-phone>+380972580000</span></p>
                        <p><i class="fas fa-map-marker-alt"></i> <span data-address>Xocalı prospekti 11, Block A, 3-cü mərtəbə, Bakı 1008, Azərbaycan</span></p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 NextCode Group. Bütün hüquqlar qorunur.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
            <script src="js/theme.js"></script>
        <script src="js/api-integration.js"></script>
        <script src="js/main.js"></script>
        <script src="js/cookie-consent.js"></script>
        <script src="js/advanced-analytics.js"></script>
        <script src="js/traffic-conversion.js"></script>
        <script src="js/performance-metrics.js"></script>
        <script src="js/portfolio-detail.js"></script>
    
    <script>
        // Portfolio sayfası için özel JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Portfolio sayfası yüklendi');
            
            // Portfolio filtreleme sistemi
            const filterButtons = document.querySelectorAll('.filter-btn');
            const portfolioItems = document.querySelectorAll('.portfolio-card');
            
            console.log('Portfolio kartları bulundu:', portfolioItems.length);
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    
                    // Aktif buton stilini güncelle
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Portfolio öğelerini filtrele
                    portfolioItems.forEach(item => {
                        if (filter === 'all' || item.getAttribute('data-category') === filter) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
            
            // Theme toggle butonunun durumunu kontrol et
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            
            if (themeToggle && themeIcon) {
                // Mevcut temayı kontrol et
                const currentTheme = localStorage.getItem('theme') || 'light';
                updateThemeIcon(currentTheme);
                
                // Theme toggle click event
                themeToggle.addEventListener('click', function() {
                    const currentTheme = localStorage.getItem('theme') || 'light';
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    
                    // Theme'i değiştir
                    if (window.themeManager) {
                        window.themeManager.setTheme(newTheme);
                    } else {
                        // Fallback theme değiştirme
                        document.body.setAttribute('data-theme', newTheme);
                        localStorage.setItem('theme', newTheme);
                        updateThemeIcon(newTheme);
                    }
                });
            }
            
            // Theme icon'unu güncelle
            function updateThemeIcon(theme) {
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.className = 'fas fa-sun';
                        themeIcon.style.color = '#f39c12';
                    } else {
                        themeIcon.className = 'fas fa-moon';
                        themeIcon.style.color = '#667eea';
                    }
                }
            }
            
            // Portfolio filtreleme için smooth scroll
            const portfolioLinks = document.querySelectorAll('a[href^="#"]');
            portfolioLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Hover overlay kaldırıldı - sadece card body'deki butonlar kullanılıyor
            
            // Portfolio overlay butonları artık normal HTML link'ler ile çalışıyor
            
            // Portfolio arama sistemi
            const searchInput = document.getElementById('portfolioSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    portfolioItems.forEach(item => {
                        const title = item.querySelector('.card-title').textContent.toLowerCase();
                        const description = item.querySelector('.card-text').textContent.toLowerCase();
                        const category = item.querySelector('.badge').textContent.toLowerCase();
                        
                        if (title.includes(searchTerm) || description.includes(searchTerm) || category.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    // Arama yapıldığında filtre butonlarını sıfırla
                    if (searchTerm !== '') {
                        filterButtons.forEach(btn => btn.classList.remove('active'));
                        document.querySelector('[data-filter="all"]').classList.add('active');
                    }
                });
            }
            
            // Load More butonu
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    // Tüm projeleri göster
                    portfolioItems.forEach(item => {
                        item.style.display = 'block';
                    });
                    
                    // Tüm filtreleri sıfırla
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    document.querySelector('[data-filter="all"]').classList.add('active');
                    
                    // Arama kutusunu temizle
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    
                    // Butonu gizle
                    this.style.display = 'none';
                });
            }
        });
        
        // Portfolio butonları artık normal HTML link'ler ile çalışıyor
    </script>
  </body>
  </html>