<?php
// Header include file for all pages
// Ensure this file is included after setting $page_title, $meta_description, and $current_page variables

// Include API router for handling API requests
require_once __DIR__ . '/../api-router.php';

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/theme-variables.css">
    <link rel="stylesheet" href="css/theme-support.css">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/modern-styles.css">
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <?php if ($current_page === 'portfolio-detail'): ?>
    <link rel="stylesheet" href="css/portfolio-detail.css?v=<?php echo time(); ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="css/cookie-consent.css">
    <link rel="stylesheet" href="css/critical-enhanced.css">
    <link rel="stylesheet" href="css/modern-animations.css">
    <link rel="stylesheet" href="css/responsive-enhanced.css">
    
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
                    <li class="nav-item ms-2">
                        <button id="themeToggle" class="btn btn-outline-secondary btn-sm me-2" title="Theme Toggle">
                            <i class="fas fa-moon" id="themeIcon"></i>
                        </button>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm px-3" href="contact.php">
                            <i class="fas fa-phone me-1"></i>
                            Contact
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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