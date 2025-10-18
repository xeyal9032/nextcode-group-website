<?php
// Header include file for all pages
// Ensure this file is included after setting $page_title, $meta_description, and $current_page variables

// Define secure access constant
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Include API router for handling API requests
require_once __DIR__ . '/../api-router.php';

// Include page functions for navigation
require_once __DIR__ . '/page_functions.php';

// Default values if not set
if (!isset($page_title)) {
    $page_title = 'NextCode Group - Digital Marketing Agency';
}
if (!isset($meta_description)) {
    $meta_description = 'NextCode Group - Azərbaycanda peşəkar rəqəmsal marketinq xidmətləri. SEO, sosial media, brendinq və reklam kampaniyaları ilə biznesinizi inkişaf etdirin.';
}
if (!isset($current_page)) {
    $current_page = 'home';
}
if (!isset($meta_keywords)) {
    $meta_keywords = 'rəqəmsal marketinq, SEO, sosial media, brendinq, reklam kampaniyaları, veb dizayn, Google Ads, Facebook Ads, Instagram marketinq, Azərbaycan, Bakı, NextCode';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-K6RHVXZ4');</script>
    <!-- End Google Tag Manager -->
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- Cache Control -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($og_title ?? $page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($og_description ?? $meta_description); ?>">
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:site_name" content="NextCode Group">
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image ?? 'https://nextcodegroup.ostwind.az/images/og-image.jpg'); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="az_AZ">
    
    <!-- Twitter Cards Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@nextcode">
    <meta name="twitter:creator" content="@nextcode">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta name="twitter:image" content="https://nextcode.com/images/twitter-card.jpg">
    
    <!-- Additional SEO Meta Tags -->
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="author" content="NextCode Group">
    <meta name="copyright" content="NextCode Group">
    <link rel="canonical" href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    
    <!-- Google Site Verification -->
    <meta name="google-site-verification" content="google6b45980adf7adfc5" />
    <meta name="google-site-verification" content="406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw" />
    
    <!-- Preload critical resources -->
    <link rel="preload" href="css/critical.css" as="style">
    <link rel="preload" href="js/critical.js" as="script">
    <link rel="preload" href="https://fonts.gstatic.com/s/inter/v12/UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuLyfAZ9hiJ-Ek-_EeA.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://fonts.gstatic.com/s/inter/v12/UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuOKfAZ9hiA.woff2" as="font" type="font/woff2" crossorigin>
    
    <!-- Critical CSS -->
    <link rel="stylesheet" href="css/critical.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/inter-font.css">
    <link rel="stylesheet" href="css/theme-variables.css">
    <link rel="stylesheet" href="css/theme-support.css">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/modern-styles.css">
    <link rel="stylesheet" href="css/readability-enhancements.css">
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <?php if ($current_page === 'portfolio-detail'): ?>
    <link rel="stylesheet" href="css/portfolio-detail.css?v=<?php echo time(); ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="css/cookie-consent.css">
    <link rel="stylesheet" href="css/critical-enhanced.css">
    <link rel="stylesheet" href="css/modern-animations.css">
    <link rel="stylesheet" href="css/responsive-enhanced.css">
    
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-8FYSTD1FVH"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        // NextCode.az GA4 Configuration
        gtag('config', 'G-8FYSTD1FVH', {
            'send_page_view': true,
            'linker': {
                'domains': ['nextcode.az', 'www.nextcode.az', 'nextcodegroup.ostwind.az'],
                'accept_incoming': true
            },
            'cookie_flags': 'SameSite=None;Secure',
            'anonymize_ip': true,
            'allow_google_signals': true,
            'cookie_domain': 'nextcode.az',
            'page_title': '<?php echo htmlspecialchars($page_title ?? "NextCode"); ?>',
            'page_location': '<?php echo "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>',
            'page_path': '<?php echo $_SERVER['REQUEST_URI']; ?>'
        });
        
        // Enhanced Measurement - Event Tracking
        
        // Scroll Depth Tracking
        let maxScroll = 0;
        window.addEventListener('scroll', function() {
            const scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
            
            if (scrollPercent > maxScroll && scrollPercent % 25 === 0) {
                maxScroll = scrollPercent;
                gtag('event', 'scroll_depth', {
                    'percent': scrollPercent,
                    'page': '<?php echo $current_page ?? "unknown"; ?>'
                });
            }
        });
        
        // Outbound Link Tracking
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.hostname !== window.location.hostname && link.href) {
                gtag('event', 'click', {
                    'event_category': 'outbound',
                    'event_label': link.href,
                    'transport_type': 'beacon'
                });
            }
        });
        
        // Form Submission Tracking
        document.addEventListener('submit', function(e) {
            const form = e.target;
            gtag('event', 'form_submit', {
                'event_category': 'engagement',
                'event_label': form.id || form.action || 'unknown',
                'page': '<?php echo $current_page ?? "unknown"; ?>'
            });
        });
        
        // Button Click Tracking
        document.addEventListener('click', function(e) {
            const button = e.target.closest('button, .btn');
            if (button) {
                gtag('event', 'button_click', {
                    'event_category': 'engagement',
                    'event_label': button.textContent.trim() || button.id || 'unknown',
                    'button_id': button.id || 'no-id'
                });
            }
        });
        
        // Video Play Tracking
        document.addEventListener('play', function(e) {
            if (e.target.tagName === 'VIDEO') {
                gtag('event', 'video_start', {
                    'event_category': 'engagement',
                    'event_label': e.target.currentSrc || e.target.src || 'unknown',
                    'video_title': e.target.title || 'no-title'
                });
            }
        }, true);
        
        // File Download Tracking
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && link.href.match(/\.(pdf|zip|doc|docx|xls|xlsx|ppt|pptx)$/i)) {
                gtag('event', 'file_download', {
                    'event_category': 'engagement',
                    'event_label': link.href,
                    'file_name': link.href.split('/').pop(),
                    'file_extension': link.href.split('.').pop()
                });
            }
        });
        
        // Error Tracking
        window.addEventListener('error', function(e) {
            gtag('event', 'exception', {
                'description': e.message,
                'fatal': false,
                'page': '<?php echo $current_page ?? "unknown"; ?>'
            });
        });
        
        console.log('✅ Google Analytics 4 initialized - ID: G-8FYSTD1FVH');
    </script>
    
    <!-- Structured Data for SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "NextCode Group",
        "url": "https://nextcode.com",
        "logo": "https://nextcode.com/favicon.svg",
        "description": "Azərbaycanda peşəkar rəqəmsal marketinq xidmətləri. SEO, sosial media, brendinq və reklam kampaniyaları ilə biznesinizi inkişaf etdirin.",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Bakı",
            "addressCountry": "AZ"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+90-555-123-4567",
            "contactType": "customer service",
            "email": "info@nextcode.com"
        },
        "sameAs": [
            "https://facebook.com/nextcode",
            "https://twitter.com/nextcode",
            "https://instagram.com/nextcode",
            "https://linkedin.com/company/nextcode"
        ],
        "serviceArea": {
            "@type": "Country",
            "name": "Azerbaijan"
        },
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Digital Marketing Services",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "SEO Optimization"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Social Media Marketing"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Branding"
                    }
                }
            ]
        }
    }
    </script>
</head>
<body class="<?php echo isset($current_page) ? $current_page . '-page' : 'home-page'; ?>">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K6RHVXZ4"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
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
                    <?php echo generateDynamicNavigation($current_page); ?>
                    
                    <!-- Theme and Design Controls -->
                    <li class="nav-item d-flex align-items-center ms-2">
                        <div class="header-controls d-flex align-items-center gap-2">
                            <button id="themeToggle" class="btn btn-outline-secondary btn-sm" title="Tema Değiştir" type="button">
                                <i class="fas fa-moon" id="themeIcon"></i>
                            </button>
                            
                            <button class="btn btn-sm design-toggle-btn" type="button" id="designToggle" title="Tasarım Değiştir">
                                <div class="toggle-switch">
                                    <div class="toggle-slider">
                                        <i class="fas fa-home toggle-icon" id="designIcon"></i>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </li>
                    
                    <!-- Contact Button -->
                    <li class="nav-item ms-2">
                        <a class="btn btn-primary btn-sm px-3" href="contact.php">
                            <i class="fas fa-phone me-1"></i>
                            Contact
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Console Guard - Disable console.log in production (MUST BE FIRST) -->
    <script src="js/console-guard.js"></script>
    
    <!-- Font Loader -->
    <script src="js/font-loader.js"></script>
    
    <!-- Critical JavaScript -->
    <script src="js/critical.js"></script>
    <script src="js/theme.js"></script>
    
    <!-- Image Performance Optimization -->
    <script>
        // Görsel yükleme performansını izle
        document.addEventListener('DOMContentLoaded', function() {
            // Portfolio görselleri için özel optimizasyon
            const portfolioImages = document.querySelectorAll('.modern-portfolio-image img');
            portfolioImages.forEach(img => {
                img.loading = 'lazy';
                img.decoding = 'async';
            });
            
            // Performance monitoring
            if ('PerformanceObserver' in window) {
                const observer = new PerformanceObserver((list) => {
                    list.getEntries().forEach((entry) => {
                        if (entry.entryType === 'resource' && entry.name.includes('images')) {
                            console.log(`📊 Görsel yükleme: ${entry.duration.toFixed(2)}ms - ${entry.name.split('/').pop()}`);
                        }
                    });
                });
                
                observer.observe({ entryTypes: ['resource'] });
            }
        });
    </script>