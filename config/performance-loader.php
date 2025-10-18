<?php
// NextCode Group - Performance Loader System
// Advanced page loading optimization and resource management

if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

class PerformanceLoader {
    private $enableCriticalCss = true;
    private $enableResourceHints = true;
    private $enableLazyLoading = true;
    private $enableCompression = true;
    private $enablePrefetch = true;
    private $cacheEnabled = true;
    
    public function __construct($config = []) {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * Generate optimized page head for performance
     */
    public function generateOptimizedHead($pageData = []) {
        $head = [];
        
        // Meta tags for performance
        $head[] = '<meta charset="utf-8">';
        $head[] = '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $head[] = '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
        
        // Resource hints
        if ($this->enableResourceHints) {
            $head[] = $this->getResourceHints($pageData);
        }
        
        // Critical CSS
        if ($this->enableCriticalCss) {
            $head[] = $this->getCriticalCss($pageData);
        }
        
        // Preloaded resources
        $head[] = $this->getPreloadedResources($pageData);
        
        // DNS prefetch for external resources
        $head[] = $this->getDnsPrefetch();
        
        return implode("\n", array_filter($head));
    }
    
    /**
     * Generate resource hints for performance
     */
    private function getResourceHints($pageData) {
        $hints = [];
        
        // Preconnect to external domains
        $externalDomains = [
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com',
            'https://www.google-analytics.com',
            'https://cdnjs.cloudflare.com'
        ];
        
        foreach ($externalDomains as $domain) {
            $hints[] = '<link rel="preconnect" href="' . $domain . '" crossorigin>';
        }
        
        // Dns-prefetch for additional domains
        $dnsDomains = [
            '//cdn.jsdelivr.net',
            '//maxcdn.bootstrapcdn.com',
            '//unpkg.com'
        ];
        
        foreach ($dnsDomains as $domain) {
            $hints[] = '<link rel="dns-prefetch" href="' . $domain . '">';
        }
        
        return implode("\n", $hints);
    }
    
    /**
     * Load critical CSS inline
     */
    private function getCriticalCss($pageData) {
        $criticalCssFile = $this->getCriticalCssFile($pageData);
        
        if ($criticalCssFile && file_exists($criticalCssFile)) {
            $css = file_get_contents($criticalCssFile);
            
            // Minify CSS for inline use
            $css = $this->minifyCss($css);
            
            return '<style>' . $css . '</style>';
        }
        
        // Fallback critical CSS
        return $this->getFallbackCriticalCss();
    }
    
    /**
     * Get critical CSS file based on page
     */
    private function getCriticalCssFile($pageData) {
        $pageType = $pageData['page_type'] ?? 'home';
        
        $criticalFiles = [
            'home' => 'css/critical.css',
            'portfolio' => 'css/critical-portfolio.css',
            'blog' => 'css/critical-blog.css',
            'about' => 'css/critical-about.css',
            'contact' => 'css/critical-contact.css'
        ];
        
        return $criticalFiles[$pageType] ?? $criticalFiles['home'];
    }
    
    /**
     * Get fallback critical CSS
     */
    private function getFallbackCriticalCss() {
        return '
<style>
/* Critical CSS Fallback */
html{font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif}
body{margin:0;line-height:1.6;color:#333;background:#fff}
.navbar{position:fixed;top:0;width:100%;background:#fff;border-bottom:1px solid #eee;z-index:1000}
.container{max-width:1200px;margin:0 auto;padding:0 20px}
.hero-section{padding:100px 0;text-align:center;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)}
h1{font-size:2.5rem;margin-bottom:1rem;color:#fff}
.btn{display:inline-block;padding:12px 24px;background:#007bff;color:#fff;text-decoration:none;border-radius:4px}
.btn:hover{background:#0056b3}
@media (max-width:768px){
  h1{font-size:2rem}
  .container{padding:0 15px}
}
</style>';
    }
    
    /**
     * Get preloaded resources
     */
    private function getPreloadedResources($pageData) {
        $preloads = [];
        
        // Font preloading
        $preloads[] = '<link rel="preload" href="/fonts/inter-font.woff2" as="font" type="font/woff2" crossorigin>';
        
        // Critical images
        $criticalImages = $this->getCriticalImages($pageData);
        foreach ($criticalImages as $image) {
            $preloads[] = '<link rel="preload" href="' . $image . '" as="image">';
        }
        
        // JavaScript for non-blocking loading
        $preloads[] = '<link rel="preload" href="/js/main.js" as="script">';
        
        return implode("\n", $preloads);
    }
    
    /**
     * Get critical images for preloading
     */
    private function getCriticalImages($pageData) {
        $pageType = $pageData['page_type'] ?? 'home';
        
        $images = [
            'home' => ['/images/hero-bg.jpg'],
            'portfolio' => ['/images/portfolio/hero.jpg'],
            'about' => ['/images/about-hero.jpg'],
            'contact' => ['/images/contact-bg.jpg']
        ];
        
        return $images[$pageType] ?? ['/images/hero-bg.jpg'];
    }
    
    /**
     * Generate DNS prefetch links
     */
    private function getDnsPrefetch() {
        $domains = [
            '//cdn.jsdelivr.net',
            '//www.google-analytics.com',
            '//cdnjs.cloudflare.com'
        ];
        
        $prefetch = [];
        foreach ($domains as $domain) {
            $prefetch[] = '<link rel="dns-prefetch" href="' . $domain . '">';
        }
        
        return implode("\n", $prefetch);
    }
    
    /**
     * Generate optimized scripts loading
     */
    public function generateOptimizedScripts($scripts = []) {
        if (!$scripts) {
            $scripts = ['js/main.js', 'js/modern-interactions.js'];
        }
        
        $output = [];
        
        foreach ($scripts as $script) {
            if ($this->enableLazyLoading) {
                $output[] = '<script src="' . $script . '" defer></script>';
            } else {
                $output[] = '<script src="' . $script . '"></script>';
            }
        }
        
        return implode("\n", $output);
    }
    
    /**
     * Generate optimized CSS loading
     */
    public function generateOptimizedStyles($styles = []) {
        if (!$styles) {
            $styles = ['css/styles.css', 'css/modern-styles.css'];
        }
        
        $output = [];
        
        foreach ($styles as $style) {
            // Use media loading for non-critical styles
            $media = $this->isCriticalStyle($style) ? '' : 'media="print" onload="this.media=\'all\'"';
            
            $output[] = '<link rel="stylesheet" href="' . $style . '" ' . $media . '>';
        }
        
        // Add noscript fallback
        $output[] = $this->getNoscriptFallback($styles);
        
        return implode("\n", $output);
    }
    
    /**
     * Check if style is critical
     */
    private function isCriticalStyle($style) {
        $criticalStyles = ['style.css', 'critical.css', 'modern-styles.css'];
        
        foreach ($criticalStyles as $critical) {
            if (strpos($style, $critical) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate noscript fallback
     */
    private function getNoscriptFallback($styles) {
        $noscript = '<noscript>';
        
        foreach ($styles as $style) {
            $noscript .= '<link rel="stylesheet" href="' . $style . '">';
        }
        
        $noscript .= '</noscript>';
        
        return $noscript;
    }
    
    /**
     * Generate lazy loading implementation
     */
    public function generateLazyLoadingScript() {
        if (!$this->enableLazyLoading) {
            return '';
        }
        
        return '
<script>
// Enhanced lazy loading with Intersection Observer
(function() {
    const lazyImages = document.querySelectorAll(\'img[data-src]\');
    
    if (\'IntersectionObserver\' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // Add fade-in animation
                    img.style.opacity = \'0\';
                    img.style.transition = \'opacity 0.3s ease\';
                    
                    img.src = img.dataset.src;
                    img.classList.remove(\'lazy\');
                    
                    img.onload = () => {
                        img.style.opacity = \'1\';
                    };
                    
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: \'50px 0px\',
            threshold: 0.01
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
        
    } else {
        // Fallback for older browsers
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.classList.remove(\'lazy\');
        });
    }
})();

// Preload images on hover
document.addEventListener(\'mouseover\', function(e) {
    const link = e.target.closest(\'a\');
    if (link && link.href && link.getAttribute(\'data-preload\')) {
        const img = new Image();
        img.src = link.getAttribute(\'data-preload\');
    }
});
</script>';
    }
    
    /**
     * Minify CSS for inline use
     */
    private function minifyCss($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove unnecessary whitespace
        $css = preg_replace('/\s+/' , ' ', $css);
        $css = preg_replace('/;\s}/', '}', $css);
        $css = preg_replace('/\(\s/', '(', $css);
        $css = preg_replace('/\)\s/', ')', $css);
        
        // Remove units from zero values
        $css = preg_replace('/(:| )0(em|ex|ch|rem|vw|vh|vm|cm|mm|in|pt|pc|px|%)/', '${1}0', $css);
        
        return trim($css);
    }
    
    /**
     * Generate performance monitoring script
     */
    public function generatePerformanceMonitor() {
        return '
<script>
// Performance monitoring and optimization
(function() {
    const performanceData = {};
    
    // Capture performance metrics
    window.addEventListener(\'load\', function() {
        setTimeout(function() {
            if (\'performance\' in window) {
                const perf = performance.getEntriesByType(\'navigation\')[0];
                const paint = performance.getEntriesByType(\'paint\');
                
                performanceData.loadTime = perf.loadEventEnd - perf.loadEventStart;
                performanceData.domContentLoaded = perf.domContentLoadedEventEnd - perf.fetchStart;
                performanceData.firstPaint = paint.find(entry => entry.name === \'first-paint\')?.startTime || 0;
                performanceData.firstContentfulPaint = paint.find(entry => entry.name === \'first-contentful-paint\')?.startTime || 0;
                
                // Send performance data to analytics (if configured)
                if (typeof gtag !== \'undefined\') {
                    gtag(\'event\', \'web_vitals\', {
                        custom_map: {
                            metric_page_load_time: Math.round(performanceData.loadTime),
                            metric_dom_content_loaded: Math.round(performanceData.domContentLoaded),
                            metric_first_paint: Math.round(performanceData.firstPaint),
                            metric_first_contentful_paint: Math.round(performanceData.firstContentfulPaint)
                        }
                    });
                }
                
                // Console performance summary
                console.log(\'Performance Metrics:\', {
                    \'Load Time\': Math.round(performanceData.loadTime) + \'ms\',
                    \'DOM Ready\': Math.round(performanceData.domContentLoaded) + \'ms\',
                    \'First Paint\': Math.round(performanceData.firstPaint) + \'ms\',
                    \'First Contentful Paint\': Math.round(performanceData.firstContentfulPaint) + \'ms\'
                });
            }
        }, 0);
    });
    
    // Resource loading optimization
    document.addEventListener(\'DOMContentLoaded\', function() {
        // Preload critical resources
        const criticalImages = document.querySelectorAll(\'[data-preload]\');
        criticalImages.forEach(img => {
            const imgEl = new Image();
            imgEl.src = img.dataset.preload;
        });
        
        // Optimize font loading
        if (\'fonts\' in document) {
            document.fonts.ready.then(() => {
                document.documentElement.classList.add(\'fonts-loaded\');
            });
        }
    });
})();
</script>';
    }
    
    /**
     * Generate Service Worker registration
     */
    public function generateServiceWorkerRegistration() {
        return '
<script>
// Service Worker registration for caching
if (\'serviceWorker\' in navigator) {
    window.addEventListener(\'load\', function() {
        navigator.serviceWorker.register(\'/sw.js\')
            .then(function(registration) {
                console.log(\'ServiceWorker registration successful\');
            })
            .catch(function(err) {
                console.log(\'ServiceWorker registration failed\');
            });
    });
}
</script>';
    }
    
    /**
     * Generate complete optimized head
     */
    public function generateCompleteOptimizedHead($pageData = []) {
        $head = [];
        
        // Basic meta tags
        $head[] = $this->generateOptimizedHead($pageData);
        
        // Stylesheets
        $head[] = $this->generateOptimizedStyles($pageData['styles'] ?? []);
        
        // Performance monitoring
        $head[] = $this->generatePerformanceMonitor();
        
        // Service Worker
        $head[] = $this->generateServiceWorkerRegistration();
        
        // Lazy loading
        $head[] = $this->generateLazyLoadingScript();
        
        return implode("\n", array_filter($head));
    }
}

// Global performance loader instance
$performanceLoader = new PerformanceLoader();

// Helper functions
function get_optimized_head($pageData = []) {
    global $performanceLoader;
    return $performanceLoader->generateCompleteOptimizedHead($pageData);
}

function get_performance_scripts($scripts = []) {
    global $performanceLoader;
    return $performanceLoader->generateOptimizedScripts($scripts);
}

function generate_lazy_loading() {
    global $performanceLoader;
    return $performanceLoader->generateLazyLoadingScript();
}

// Auto-generate optimized head for current page
function auto_performance_head() {
    global $performanceLoader;
    
    $pageData = [
        'page_type' => basename($_SERVER['PHP_SELF'], '.php'),
        'styles' => ['css/style.css', 'css/responsive.css'],
        'scripts' => ['js/main.js']
    ];
    
    return $performanceLoader->generateCompleteOptimizedHead($pageData);
}

?>
