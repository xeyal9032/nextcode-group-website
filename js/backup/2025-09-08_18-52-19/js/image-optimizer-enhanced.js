// Enhanced Image Optimizer System
// NextCode Group - Advanced Image Optimization
// Bu dosya mevcut image-optimizer.js'i tamamlar, override etmez

class EnhancedImageOptimizer {
    constructor() {
        this.webpSupported = false;
        this.avifSupported = false;
        this.optimizedImages = new Set();
        this.lazyLoadObserver = null;
        this.preloadObserver = null;
        this.isInitialized = false;
        
        // Performance metrics
        this.loadingStartTime = 0;
        this.totalImages = 0;
        this.loadedImages = 0;
        
        this.init();
    }
    
    init() {
        console.log('🚀 Enhanced Image Optimizer başlatılıyor...');
        
        // Modern format support kontrol et
        this.checkModernFormatSupport();
        
        // Advanced lazy loading kur
        this.setupAdvancedLazyLoading();
        
        // Preload critical images
        this.preloadCriticalImages();
        
        // Performance monitoring
        this.setupPerformanceMonitoring();
        
        this.isInitialized = true;
        console.log('✅ Enhanced Image Optimizer aktif');
    }
    
    // Modern format support kontrol et
    async checkModernFormatSupport() {
        try {
            // WebP support
            const webpCanvas = document.createElement('canvas');
            webpCanvas.width = 1;
            webpCanvas.height = 1;
            const webpCtx = webpCanvas.getContext('2d');
            webpCtx.fillStyle = 'red';
            webpCtx.fillRect(0, 0, 1, 1);
            const webpDataURL = webpCanvas.toDataURL('image/webp');
            this.webpSupported = webpDataURL.indexOf('data:image/webp') === 0;
            
            // AVIF support (experimental)
            const avifCanvas = document.createElement('canvas');
            avifCanvas.width = 1;
            avifCanvas.height = 1;
            const avifCtx = avifCanvas.getContext('2d');
            avifCtx.fillStyle = 'red';
            avifCtx.fillRect(0, 0, 1, 1);
            try {
                const avifDataURL = avifCanvas.toDataURL('image/avif');
                this.avifSupported = avifDataURL.indexOf('data:image/avif') === 0;
            } catch (e) {
                this.avifSupported = false;
            }
            
            console.log('🌐 Format Support:', {
                'WebP': this.webpSupported ? '✅' : '❌',
                'AVIF': this.avifSupported ? '✅' : '❌'
            });
            
        } catch (error) {
            console.warn('Format support kontrol hatası:', error);
            this.webpSupported = false;
            this.avifSupported = false;
        }
    }
    
    // Advanced lazy loading kur
    setupAdvancedLazyLoading() {
        if ('IntersectionObserver' in window) {
            this.setupIntersectionObserver();
            this.setupPreloadObserver();
        } else {
            this.setupScrollBasedLazyLoading();
        }
    }
    
    // Intersection Observer kur
    setupIntersectionObserver() {
        this.lazyLoadObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    this.lazyLoadObserver.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '100px 0px', // Daha erken yükleme
            threshold: 0.01
        });
        
        // Lazy load için resimleri observe et
        this.observeLazyImages();
    }
    
    // Preload observer kur
    setupPreloadObserver() {
        this.preloadObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.preloadImage(entry.target);
                    this.preloadObserver.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '200px 0px', // Daha erken preload
            threshold: 0.01
        });
        
        // Preload için resimleri observe et
        this.observePreloadImages();
    }
    
    // Lazy load için resimleri observe et
    observeLazyImages() {
        const lazyImages = document.querySelectorAll('img[data-src], img[data-srcset]');
        lazyImages.forEach(img => {
            this.lazyLoadObserver.observe(img);
        });
    }
    
    // Preload için resimleri observe et
    observePreloadImages() {
        const preloadImages = document.querySelectorAll('img[data-preload]');
        preloadImages.forEach(img => {
            this.preloadObserver.observe(img);
        });
    }
    
    // Resim yükle
    loadImage(img) {
        if (this.optimizedImages.has(img)) return;
        
        const startTime = performance.now();
        
        // Modern format seç
        const bestFormat = this.selectBestFormat(img);
        
        // Resim yükle
        if (bestFormat && bestFormat !== img.src) {
            img.src = bestFormat;
        }
        
        // Lazy loading attributes'ları temizle
        img.removeAttribute('data-src');
        img.removeAttribute('data-srcset');
        img.classList.remove('lazy');
        img.classList.add('loaded');
        
        // Performance tracking
        const loadTime = performance.now() - startTime;
        this.trackImageLoad(img, loadTime);
        
        this.optimizedImages.add(img);
        this.loadedImages++;
    }
    
    // Preload resim
    preloadImage(img) {
        const bestFormat = this.selectBestFormat(img);
        if (bestFormat && bestFormat !== img.src) {
            const preloadLink = document.createElement('link');
            preloadLink.rel = 'preload';
            preloadLink.as = 'image';
            preloadLink.href = bestFormat;
            document.head.appendChild(preloadLink);
        }
    }
    
    // En iyi format seç
    selectBestFormat(img) {
        const originalSrc = img.dataset.src || img.src;
        if (!originalSrc) return null;
        
        // AVIF support varsa AVIF kullan
        if (this.avifSupported && this.hasAVIFVersion(originalSrc)) {
            return this.getAVIFVersion(originalSrc);
        }
        
        // WebP support varsa WebP kullan
        if (this.webpSupported && this.hasWebPVersion(originalSrc)) {
            return this.getWebPVersion(originalSrc);
        }
        
        return originalSrc;
    }
    
    // AVIF versiyonu var mı kontrol et
    hasAVIFVersion(src) {
        return src.includes('.jpg') || src.includes('.jpeg') || src.includes('.png');
    }
    
    // AVIF versiyonu al
    getAVIFVersion(src) {
        return src.replace(/\.(jpg|jpeg|png)$/i, '.avif');
    }
    
    // WebP versiyonu var mı kontrol et
    hasWebPVersion(src) {
        return src.includes('.jpg') || src.includes('.jpeg') || src.includes('.png');
    }
    
    // WebP versiyonu al
    getWebPVersion(src) {
        return src.replace(/\.(jpg|jpeg|png)$/i, '.webp');
    }
    
    // Performance monitoring
    setupPerformanceMonitoring() {
        this.loadingStartTime = performance.now();
        this.totalImages = document.querySelectorAll('img').length;
        
        // Performance metrics gönder
        window.addEventListener('load', () => {
            this.sendPerformanceMetrics();
        });
    }
    
    // Resim yükleme performansını takip et
    trackImageLoad(img, loadTime) {
        // Core Web Vitals için LCP tracking
        if (img.classList.contains('hero-image') || img.classList.contains('critical-image')) {
            this.trackLCP(img, loadTime);
        }
    }
    
    // LCP tracking
    trackLCP(img, loadTime) {
        if ('PerformanceObserver' in window) {
            try {
                const observer = new PerformanceObserver((list) => {
                    list.getEntries().forEach((entry) => {
                        if (entry.entryType === 'largest-contentful-paint') {
                            console.log('🎯 LCP:', entry.startTime.toFixed(2), 'ms');
                        }
                    });
                });
                observer.observe({ entryTypes: ['largest-contentful-paint'] });
            } catch (e) {
                console.warn('LCP tracking hatası:', e);
            }
        }
    }
    
    // Performance metrics gönder
    sendPerformanceMetrics() {
        const totalTime = performance.now() - this.loadingStartTime;
        const metrics = {
            totalImages: this.totalImages,
            loadedImages: this.loadedImages,
            totalLoadTime: totalTime.toFixed(2),
            averageLoadTime: (totalTime / this.loadedImages).toFixed(2),
            webpSupported: this.webpSupported,
            avifSupported: this.avifSupported
        };
        
        console.log('📊 Image Performance Metrics:', metrics);
        
        // Analytics API'ye gönder
        this.sendToAnalytics(metrics);
    }
    
    // Analytics'e gönder
    sendToAnalytics(metrics) {
        if (window.gtag) {
            window.gtag('event', 'image_optimization', {
                event_category: 'performance',
                event_label: 'image_loading',
                value: Math.round(metrics.averageLoadTime),
                custom_parameters: metrics
            });
        }
    }
    
    // Scroll-based lazy loading (fallback)
    setupScrollBasedLazyLoading() {
        let scrollTimeout;
        
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                this.loadVisibleImages();
            }, 100);
        });
    }
    
    // Görünür resimleri yükle
    loadVisibleImages() {
        const lazyImages = document.querySelectorAll('img[data-src]');
        
        lazyImages.forEach(img => {
            if (this.isElementInViewport(img)) {
                this.loadImage(img);
            }
        });
    }
    
    // Element viewport'ta mı kontrol et
    isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    // Destroy
    destroy() {
        if (this.lazyLoadObserver) {
            this.lazyLoadObserver.disconnect();
        }
        if (this.preloadObserver) {
            this.preloadObserver.disconnect();
        }
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    window.enhancedImageOptimizer = new EnhancedImageOptimizer();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedImageOptimizer;
}
