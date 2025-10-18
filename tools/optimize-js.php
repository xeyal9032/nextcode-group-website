<?php
// JavaScript Optimizer ve Lazy Loading Generator
// Bu script JavaScript dosyalarını optimize eder ve lazy loading ekler

class JavaScriptOptimizer {
    private $jsDir = 'js/';
    private $outputDir = 'js/optimized/';
    
    public function __construct() {
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    public function minifyJS($inputFile, $outputFile = null) {
        $js = file_get_contents($inputFile);
        
        // JavaScript minification
        $js = $this->minify($js);
        
        if (!$outputFile) {
            $outputFile = str_replace('.js', '.min.js', $inputFile);
        }
        
        file_put_contents($outputFile, $js);
        
        $originalSize = filesize($inputFile);
        $minifiedSize = filesize($outputFile);
        $savings = round((($originalSize - $minifiedSize) / $originalSize) * 100, 2);
        
        echo "✓ Minified: " . basename($inputFile) . " ({$originalSize} bytes → {$minifiedSize} bytes, {$savings}% savings)\n";
        
        return $outputFile;
    }
    
    private function minify($js) {
        // Remove single-line comments
        $js = preg_replace('~//[^\r\n]*~', '', $js);
        
        // Remove multi-line comments
        $js = preg_replace('~/\*.*?\*/~s', '', $js);
        
        // Remove unnecessary whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        $js = preg_replace('/;\s*/', ';', $js);
        $js = preg_replace('/{\s*/', '{', $js);
        $js = preg_replace('/\s*}/', '}', $js);
        $js = preg_replace('/,\s*/', ',', $js);
        $js = preg_replace('/\s*=\s*/', '=', $js);
        $js = preg_replace('/\s*\+\s*/', '+', $js);
        $js = preg_replace('/\s*-\s*/', '-', $js);
        $js = preg_replace('/\s*\*\s*/', '*', $js);
        $js = preg_replace('/\s*\/\s*/', '/', $js);
        
        // Remove leading and trailing whitespace
        $js = trim($js);
        
        return $js;
    }
    
    public function generateLazyLoading() {
        $lazyLoadingJS = '
// Lazy Loading Implementation
class LazyLoader {
    constructor() {
        this.imageObserver = null;
        this.init();
    }
    
    init() {
        if ("IntersectionObserver" in window) {
            this.imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        this.loadImage(img);
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: "50px 0px",
                threshold: 0.01
            });
            
            this.observeImages();
        } else {
            // Fallback for older browsers
            this.loadAllImages();
        }
    }
    
    observeImages() {
        const lazyImages = document.querySelectorAll("img[data-src]");
        lazyImages.forEach(img => this.imageObserver.observe(img));
    }
    
    loadImage(img) {
        const src = img.getAttribute("data-src");
        if (src) {
            img.src = src;
            img.removeAttribute("data-src");
            img.classList.add("loaded");
        }
    }
    
    loadAllImages() {
        const lazyImages = document.querySelectorAll("img[data-src]");
        lazyImages.forEach(img => this.loadImage(img));
    }
}

// Initialize lazy loading when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    new LazyLoader();
});

// Performance monitoring
class PerformanceMonitor {
    constructor() {
        this.metrics = {};
        this.init();
    }
    
    init() {
        if ("PerformanceObserver" in window) {
            const observer = new PerformanceObserver((list) => {
                list.getEntries().forEach((entry) => {
                    this.metrics[entry.name] = entry.duration;
                });
            });
            
            observer.observe({ entryTypes: ["navigation", "paint", "measure"] });
        }
    }
    
    getMetrics() {
        return this.metrics;
    }
}

// Initialize performance monitoring
const perfMonitor = new PerformanceMonitor();
';
        
        file_put_contents($this->outputDir . 'lazy-loading.min.js', $this->minify($lazyLoadingJS));
        echo "✓ Generated lazy-loading.min.js\n";
    }
    
    public function generateBundleOptimizer() {
        $bundleOptimizer = '
// Bundle Optimizer - Load scripts only when needed
class BundleOptimizer {
    constructor() {
        this.loadedScripts = new Set();
        this.loadingPromises = new Map();
    }
    
    async loadScript(src, condition = null) {
        if (this.loadedScripts.has(src)) {
            return Promise.resolve();
        }
        
        if (this.loadingPromises.has(src)) {
            return this.loadingPromises.get(src);
        }
        
        const promise = new Promise((resolve, reject) => {
            if (condition && !condition()) {
                resolve();
                return;
            }
            
            const script = document.createElement("script");
            script.src = src;
            script.async = true;
            script.onload = () => {
                this.loadedScripts.add(src);
                resolve();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
        
        this.loadingPromises.set(src, promise);
        return promise;
    }
    
    async loadConditionalScripts() {
        // Load theme.js only if theme toggle exists
        if (document.getElementById("themeToggle")) {
            await this.loadScript("js/theme.min.js");
        }
        
        // Load portfolio.js only on portfolio pages
        if (document.querySelector(".portfolio-grid")) {
            await this.loadScript("js/portfolio.min.js");
        }
        
        // Load blog.js only on blog pages
        if (document.querySelector(".blog-post")) {
            await this.loadScript("js/blog.min.js");
        }
    }
}

// Initialize bundle optimizer
document.addEventListener("DOMContentLoaded", () => {
    const bundleOptimizer = new BundleOptimizer();
    bundleOptimizer.loadConditionalScripts();
});
';
        
        file_put_contents($this->outputDir . 'bundle-optimizer.min.js', $this->minify($bundleOptimizer));
        echo "✓ Generated bundle-optimizer.min.js\n";
    }
    
    public function optimizeAllJS() {
        echo "🚀 Starting JavaScript Optimization...\n\n";
        
        // Lazy loading ve bundle optimizer oluştur
        $this->generateLazyLoading();
        $this->generateBundleOptimizer();
        
        // Ana JS dosyalarını minify et
        $mainJSFiles = [
            'main.js',
            'theme.js',
            'portfolio.js',
            'blog.js',
            'services.js'
        ];
        
        foreach ($mainJSFiles as $file) {
            $filePath = $this->jsDir . $file;
            if (file_exists($filePath)) {
                $this->minifyJS($filePath, $this->outputDir . str_replace('.js', '.min.js', $file));
            }
        }
        
        echo "\n✅ JavaScript Optimization completed!\n";
    }
}

// Script'i çalıştır
$optimizer = new JavaScriptOptimizer();
$optimizer->optimizeAllJS();
?>
