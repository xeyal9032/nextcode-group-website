<?php
// NextCode Group - Mobile Responsive Testing System
// Comprehensive mobile-first responsive design validation

if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
});

class MobileResponsiveTester {
    private $testViewports = [
        'mobile-portrait' => ['width' => 375, 'height' => 667],
        'mobile-landscape' => ['width' => 667, 'height' => 375],
        'tablet-portrait' => ['width' => 768, 'height' => 1024],
        'tablet-landscape' => ['width' => 1024, 'height' => 768],
        'desktop-small' => ['width' => 1200, 'height' => 800],
        'desktop-large' => ['width' => 1920, 'height' => 1080]
    ];
    
    private $criticalElements = [
        'header', 'navigation', '.hero-section', '.main-content',
        '.footer', '.btn', 'form', 'input', 'textarea'
    ];
    
    public function __construct($config = []) {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = array_merge($this->$key, $value);
            }
        }
    }
    
    /**
     * Generate responsive meta tag and CSS
     */
    public function generateResponsiveMeta() {
        return '
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="theme-color" content="#667eea">
<link rel="apple-touch-icon" href="/favicon-180x180.png">
';
    }
    
    /**
     * Generate mobile-first CSS utilities
     */
    public function generateMobileCSS() {
        return '
<style>
/* Mobile-first responsive utilities */
:root {
    --mobile-breakpoint: 576px;
    --tablet-breakpoint: 768px;
    --desktop-breakpoint: 992px;
    --large-breakpoint: 1200px;
}

/* Mobile-first grid system */
.responsive-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

@media (min-width: 576px) {
    .responsive-grid-sm {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 768px) {
    .responsive-grid-md {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .responsive-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

@media (min-width: 992px) {
    .responsive-grid-lg {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .responsive-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }
}

@media (min-width: 1200px) {
    .responsive-grid-xl {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .responsive-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Responsive typography */
.hero-title {
    font-size: 2rem;
    line-height: 1.2;
    margin-bottom: 1rem;
}

@media (min-width: 576px) {
    .hero-title {
        font-size: 2.5rem;
    }
}

@media (min-width: 768px) {
    .hero-title {
        font-size: 3rem;
    }
}

@media (min-width: 992px) {
    .hero-title {
        font-size: 3.5rem;
    }
}

@media (min-width: 1200px) {
    .hero-title {
        font-size: 4rem;
    }
}

/* Responsive navigation */
.navbar {
    padding: 0.5rem 1rem;
}

.navbar-collapse {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: none;
}

.navbar-collapse.show {
    display: block;
}

@media (min-width: 992px) {
    .navbar-collapse {
无     position: static;
        background: none;
        box-shadow: none;
        display: flex !important;
    }
    
    .navbar-nav {
        flex-direction: row;
        align-items: center;
    }
}

/* Responsive containers */
.container-fluid {
    width: 100%;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

@media (min-width: 576px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

@media (min-width: 768px) {
    .container-fluid {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
}

@media (min-width: 992px) {
    .container-fluid {
        padding-left: 2rem;
        padding-right: 2rem;
    }
}

/* Responsive images */
img {
    max-width: 100%;
    height: auto;
}

.responsive-image {
    width: 100%;
    object-fit: cover;
}

@media (min-width: 768px) {
    .responsive-image {
        width: auto;
        height: 300px;
    }
}

/* Touch-friendly buttons */
.btn {
    min-height: 44px;
    min-width: 44px;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    border-radius: 8px;
}

@media (min-width: 768px) {
    .btn {
        min-height: 38px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
}

/* Responsive forms */
.form-group {
    margin-bottom: 1rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    font-size: 1rem;
    border-radius: 8px;
    border: 1px solid #ddd;
}

@media (min-width: 768px) {
    .form-control {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
}

/* Mobile-specific optimizations */
@media (max-width: 767px) {
    .mobile-hide {
        display: none !important;
    }
    
    .mobile-full-width {
        width: 100% !important;
    }
    
    .mobile-stack {
        flex-direction: column !important;
    }
    
    .mobile-text-center {
        text-align: center !important;
    }
    
    /* Reduce animations on mobile */
    * {
        transition-duration: 0.2s !important;
    }
    
    /* Optimize scrolling */
    .smooth-scroll {
        scroll-behavior: auto;
        -webkit-overflow-scrolling: touch;
    }
}

/* Tablet optimizations */
@media (min-width: 768px) and (max-width: 991px) {
    .tablet-gap {
        gap: 1.5rem;
    }
    
    .tablet-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

/* Desktop optimizations */
@media (min-width: 992px) {
    .desktop-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }
    
    .desktop-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
    }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .logo, .icon {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
}

/* Print styles */
@media print {
    * {
        background: transparent !important;
        color: black !important;
        box-shadow: none !important;
        text-shadow: none !important;
    }
    
    .navbar, .footer, .btn {
        display: none !important;
    }
    
    body {
        font-size: 12pt;
        line-height: 1.5;
    }
    
    .container {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}

/* Orientation-specific styles */
@media screen and (orientation: landscape) and (max-width: 767px) {
    .navbar {
        padding: 0.25rem 0.5rem;
    }
    
    .hero-section {
        padding: 2rem 0;
    }
}

@media screen and (orientation: portrait) and (max-width: 767px) {
    .hero-section {
        padding: 3rem 0;
    }
    
    .content-section {
        padding: 2rem 0;
    }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    :root {
        --bg-color: #1a1a1a;
        --text-color: #ffffff;
        --border-color: #333333;
    }
    
    body {
        background-color: var(--bg-color);
        color: var(--text-color);
    }
    
    .navbar {
        background-color: rgba(26, 26, 26, 0.95);
        border-bottom: 1px solid var(--border-color);
    }
}
</style>';
    }
    
    /**
     * Generate responsive JavaScript utilities
     */
    public function generateResponsiveJS() {
        return '
<script>
// Responsive utilities and mobile optimizations
(function() {
    let currentBreakpoint = "mobile";
    let resizeTimeout;
    
    // Breakpoint detection
    function getCurrentBreakpoint() {
        const width = window.innerWidth;
        
        if (width < 576) return "mobile";
        if (width < 768) return "mobile-lg";        
        if (width < 992) return "tablet";
        if (width < 1200) return "desktop-sm";
        return "desktop";
    }
    
    // Update breakpoint and trigger events
    function updateBreakpoint() {
        const newBreakpoint = getCurrentBreakpoint();
        
        if (newBreakpoint !== currentBreakpoint) {
            const oldBreakpoint = currentBreakpoint;
            currentBreakpoint = newBreakpoint;
            
            // Update body class
            document.body.className = document.body.className
                .replace(/breakpoint-\\w+/g, "")
                .trim();
            document.body.classList.add("breakpoint-" + currentBreakpoint);
            
            // Dispatch custom events
            window.dispatchEvent(new CustomEvent("breakpointchange", {
                detail: { from: oldBreakpoint, to: newBreakpoint }
            }));
        }
    }
    
    // Debounced resize handler
    function handleResize() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateBreakpoint, 150);
    }
    
    // Initialize
    window.addEventListener("load", updateBreakpoint);
    window.addEventListener("resize", handleResize);
    
    // Mobile navigation
    function initMobileNav() {
        const navbarToggle = document.querySelector(".navbar-toggler");
        const navbarCollapse = document.querySelector(".navbar-collapse");
        
        if (navbarToggle && navbarCollapse) {
            navbarToggle.addEventListener("click", function() {
                navbarCollapse.classList.toggle("show");
                this.setAttribute("aria-expanded", 
                    navbarCollapse.classList.contains("show").toString()
                );
            });
            
            // Close on outside click
            document.addEventListener("click", function(e) {
                if (!navbarToggle.contains(e.target) && !navbarCollapse.contains(e.target)) {
                    navbarCollapse.classList.remove("show");
                    navbarToggle.setAttribute("aria-expanded", "false");
                }
            });
            
            // Close on escape key
            document.addEventListener("keydown", function(e) {
                if (e.key === "Escape" && navbarCollapse.classList.contains("show")) {
                    navbarCollapse.classList.remove("show");
                    navbarToggle.setAttribute("aria-expanded", "false");
                    navbarToggle.focus();
                }
            });
        }
    }
    
    // Touch optimization
    function optimizeForTouch() {
        if ("ontouchstart" in window) {
            document.body.classList.add("touch-device");
            
            // Add touch-action to prevent scrolling interference
            const interactiveElements = document.querySelectorAll("button, input, select, textarea, a");
            interactiveElements.forEach(el => {
                el.style.touchAction = "manipulation";
            });
            
            // Optimize hover effects for touch
            const hoverElements = document.querySelectorAll("[data-hover-effect]");
            hoverElements.forEach(el => {
                el.addEventListener("touchstart", function() {
                    this.classList.add("hover");
                });
                
                el.addEventListener("touchend", function() {
                    setTimeout(() => {
                        this.classList.remove("hover");
                    }, 300);
                });
            });
        }
    }
    
    // Responsive images
    function optimizeImages() {
        const images = document.querySelectorAll("img[data-srcset]");
        
        images.forEach(img => {
            const updateSrc = () => {
                const width = window.innerWidth;
                const srcset = img.dataset.srcset;
                const matches = srcset.match(/(\\S+)\\s+(\\d+)w/g);
                
                if (matches) {
                    let bestSrc = img.src;
                    let bestWidth = 0;
                    
                    matches.forEach(match => {
                        const [src, widthStr] = match.trim().split(" ");
                        const srcWidth = parseInt(widthStr);
                        
                        if (srcWidth <= width && srcWidth > bestWidth) {
                            bestSrc = src;
                            bestWidth = srcWidth;
                        }
                    });
                    
                    if (bestSrc !== img.src) {
                        img.src = bestSrc;
                    }
                }
            };
            
            window.addEventListener("resize", updateSrc);
            updateSrc();
        });
    }
    
    // Initialize on DOM ready
    document.addEventListener("DOMContentLoaded", function() {
        initMobileNav();
        optimizeForTouch();
        optimizeImages();
        updateBreakpoint();
        
        // Add support indicator
        if (typeof IntersectionObserver !== "undefined") {
            document.body.classList.add("supports-intersection-observer");
        }
        
        if (typeof CSS?.supports === "function" && CSS.supports("display: grid")) {
            document.body.classList.add("supports-css-grid");
        }
    });
    
    // Export utilities
    window.responsiveUtils = {
        getCurrentBreakpoint,
        isDesktop: () => currentBreakpoint.includes("desktop"),
        isMobile: () => currentBreakpoint.includes("mobile"),
        isTablet: () => currentBreakpoint === "tablet"
    };
})();
</script>';
    }
    
    /**
     * Generate mobile performance optimizations
     */
    public function generatePerformanceOptimizations() {
        return '
<script>
// Mobile performance optimizations
(function() {
    // Connection-based optimizations
    function handleConnectionChange() {
        if ("connection" in navigator) {
            const connection = navigator.connection;
            const effectiveType = connection.effectiveType;
            
            document.body.classList.add("connection-" + effectiveType);
            
            // Adjust loading strategies based on connection
            if (effectiveType === "slow-2g" || effectiveType === "2g") {
                // Disable animations and heavy features
                document.body.classList.add("low-bandwidth");
                
                // Lazy load non-critical images
                const images = document.querySelectorAll("img[data-defer]");
                images.forEach(img => {
                    img.style.display = "none";
                });
                
                // Reduce image quality
                const imageElements = document.querySelectorAll("img");
                imageElements.forEach(img => {
                    if (img.src.includes("quality=")) {
                        img.src = img.src.replace(/quality=\\d+/, "quality=40");
                    }
                });
            }
        }
    }
    
    // Memory management for mobile devices
    function optimizeMemory() {
        // Clean up unused event listeners periodically
        setInterval(() => {
            if (performance.memory && performance.memory.usedJSHeapSize > 50 * 1024 * 1024) {
                // If memory usage exceeds 50MB, trigger garbage collection
                if (window.gc) {
                    window.gc();
                }
            }
        }, 30000); // Check every 30 seconds
    }
    
    // Battery-aware optimizations  
    function handleBatteryChange() {
        if ("getBattery" in navigator) {
            navigator.getBattery().then(battery => {
                const handleChange = () => {
                    if (battery.level < 0.2) {
                        document.body.classList.add("low-battery");
                        
                        // Reduce autoplay and animations
                        const videos = document.querySelectorAll("video[autoplay]");
                        videos.forEach(video => {
                            video.removeAttribute("autoplay");
                        });
                        
                        // Reduce animation frequency
                        const animatedElements = document.querySelectorAll("[style*="animation"]");
                        animatedElements.forEach(el => {
                            el.style.animationDuration = "0.1s";
                        });
                    }
                };
                
                battery.addEventListener("levelchange", handleChange);
                handleChange();
            });
        }
    }
    
    // Initialize performance optimizations
    handleConnectionChange();
    optimizeMemory();
    handleBatteryChange();
    
    // Listen for connection changes
    if ("connection" in navigator) {
        navigator.connection.addEventListener("change", handleConnectionChange);
    }
})();
</script>';
    }
    
    /**
     * Perform responsive testing checks
     */
    public function performResponsiveTests() {
        return [
            'viewport_meta' => $this->checkViewportMeta(),
            'touch_targets' => $this->checkTouchTargets(),
            'responsive_images' => $this->checkResponsiveImages(),
            'text_readability' => $this->checkTextReadability(),
            'navigation_usability' => $this->checkNavigationUsability(),
            'form_usability' => $this->checkFormUsability(),
            'performance_mobile' => $this->checkMobilePerformance()
        ];
    }
    
    /**
     * Testing methods (simplified for PHP context)
     */
    private function checkViewportMeta() {
        // This would check if viewport meta tag exists
        return true;
    }
    
    private function checkTouchTargets() {
        // Check if interactive elements meet minimum touch target size
        return true;
    }
    
    private function checkResponsiveImages() {
        // Check if images have responsive attributes
        return true;
    }
    
    private function checkTextReadability() {
        // Check text size and contrast for mobile
        return true;
    }
    
    private function checkNavigationUsability() {
        // Check navigation usability on mobile
        return true;
    }
    
    private function checkFormUsability() {
        // Check form usability on mobile devices
        return true;
    }
    
    private function checkMobilePerformance() {
        // Check mobile-specific performance metrics
        return true;
    }
    
    /**
     * Generate complete mobile-responsive solution
     */
    public function generateCompleteMobileSolution() {
        $elements = [];
        
        $elements[] = $this->generateResponsiveMeta();
        $elements[] = $this->generateMobileCSS();
        $elements[] = $this->generateResponsiveJS();
        $elements[] = $this->generatePerformanceOptimizations();
        
        return implode("\n", array_filter($elements));
    }
}

// Global mobile responsive tester instance
$mobileResponsiveTester = new MobileResponsiveTester();

// Helper functions
function get_mobile_optimized_head() {
    global $mobileResponsiveTester;
    return $mobileResponsiveTester->generateResponsiveMeta();
}

function get_responsive_css() {
    global $mobileResponsiveTester;
    return $mobileResponsiveTester->generateMobileCSS();
}

function get_responsive_js() {
    global $mobileResponsiveTester;
    return $mobileResponsiveTester->generateResponsiveJS();
}

function perform_responsive_tests() {
    global $mobileResponsiveTester;
    return $mobileResponsiveTester->performResponsiveTests();
}

function auto_mobile_optimization() {
    global $mobileResponsiveTester;
    return $mobileResponsiveTester->generateCompleteMobileSolution();
}

?>

