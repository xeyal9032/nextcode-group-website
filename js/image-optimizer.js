// Image Optimizer System
// NextCode Group WebP Support and Image Optimization

class ImageOptimizer {
    constructor() {
        this.webpSupported = false;
        this.optimizedImages = new Set();
        this.lazyLoadObserver = null;
        this.isInitialized = false;
        
        this.init();
    }
    
    init() {
        console.log('🖼️ Image Optimizer başlatılıyor...');
        
        // WebP support kontrol et
        this.checkWebPSupport();
        
        // Lazy loading kur
        this.setupLazyLoading();
        
        // Mevcut resimleri optimize et
        this.optimizeExistingImages();
        
        // Intersection Observer kur
        this.setupIntersectionObserver();
        
        this.isInitialized = true;
        console.log('✅ Image Optimizer aktif');
    }
    
    // WebP support kontrol et
    async checkWebPSupport() {
        try {
            const canvas = document.createElement('canvas');
            canvas.width = 1;
            canvas.height = 1;
            
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = 'red';
            ctx.fillRect(0, 0, 1, 1);
            
            const webpDataURL = canvas.toDataURL('image/webp');
            this.webpSupported = webpDataURL.indexOf('data:image/webp') === 0;
            
            console.log('🌐 WebP Support:', this.webpSupported ? '✅ Destekleniyor' : '❌ Desteklenmiyor');
            
        } catch (error) {
            console.warn('WebP support kontrol hatası:', error);
            this.webpSupported = false;
        }
    }
    
    // Lazy loading kur
    setupLazyLoading() {
        // Intersection Observer API desteği kontrol et
        if ('IntersectionObserver' in window) {
            this.setupIntersectionObserver();
        } else {
            // Fallback: scroll event
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
            rootMargin: '50px 0px',
            threshold: 0.01
        });
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
    isElementInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    // Resim yükle
    loadImage(img) {
        if (img.dataset.src) {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
            img.classList.remove('lazy');
            img.classList.add('loaded');
            
            // WebP conversion
            if (this.webpSupported) {
                this.convertToWebP(img);
            }
        }
    }
    
    // Mevcut resimleri optimize et
    optimizeExistingImages() {
        const images = document.querySelectorAll('img:not([data-optimized])');
        
        images.forEach(img => {
            this.optimizeImage(img);
        });
    }
    
    // Resim optimize et
    optimizeImage(img) {
        if (this.optimizedImages.has(img)) return;
        
        // Lazy loading ekle
        this.addLazyLoading(img);
        
        // Responsive images ekle
        this.addResponsiveImages(img);
        
        // WebP conversion
        if (this.webpSupported) {
            this.convertToWebP(img);
        }
        
        // Optimized flag ekle
        img.setAttribute('data-optimized', 'true');
        this.optimizedImages.add(img);
    }
    
    // WebP'ye çevir
    convertToWebP(img) {
        const originalSrc = img.src;
        
        // WebP URL oluştur
        const webpSrc = this.getWebPVersion(originalSrc);
        
        if (webpSrc && webpSrc !== originalSrc) {
            // WebP support kontrol et ve yükle
            this.loadWebPImage(img, webpSrc, originalSrc);
        }
    }
    
    // WebP version URL al
    getWebPVersion(src) {
        if (!src || src.startsWith('data:')) return null;
        
        // URL'de zaten WebP var mı kontrol et
        if (src.includes('.webp')) return src;
        
        // WebP extension ekle
        const lastDotIndex = src.lastIndexOf('.');
        if (lastDotIndex > 0) {
            const baseUrl = src.substring(0, lastDotIndex);
            const extension = src.substring(lastDotIndex);
            
            // WebP version oluştur
            return `${baseUrl}.webp`;
        }
        
        return null;
    }
    
    // WebP resim yükle
    loadWebPImage(img, webpSrc, fallbackSrc) {
        const webpImg = new Image();
        
        webpImg.onload = () => {
            // WebP yüklendi, src'i değiştir
            img.src = webpSrc;
            console.log('🔄 WebP yüklendi:', webpSrc);
        };
        
        webpImg.onerror = () => {
            // WebP yüklenemedi, fallback kullan
            console.log('⚠️ WebP yüklenemedi, fallback kullanılıyor:', fallbackSrc);
        };
        
        webpImg.src = webpSrc;
    }
    
    // Lazy loading ekle
    addLazyLoading(img) {
        if (img.classList.contains('lazy')) return;
        
        // Lazy class ekle
        img.classList.add('lazy');
        
        // Data-src'e taşı
        if (img.src && !img.dataset.src) {
            img.dataset.src = img.src;
            img.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'; // 1x1 transparent GIF
        }
        
        // Observer'a ekle
        if (this.lazyLoadObserver) {
            this.lazyLoadObserver.observe(img);
        }
    }
    
    // Responsive images ekle
    addResponsiveImages(img) {
        if (img.srcset) return; // Zaten responsive
        
        const originalSrc = img.src || img.dataset.src;
        if (!originalSrc) return;
        
        // Responsive sizes
        const sizes = this.getResponsiveSizes();
        
        // Srcset oluştur
        const srcset = this.getResponsiveSrcset(originalSrc, sizes);
        
        if (srcset) {
            img.srcset = srcset;
            img.sizes = sizes;
        }
    }
    
    // Responsive sizes al
    getResponsiveSizes() {
        return '(max-width: 576px) 100vw, (max-width: 768px) 50vw, (max-width: 992px) 33vw, 25vw';
    }
    
    // Responsive srcset oluştur
    getResponsiveSrcset(originalSrc, sizes) {
        if (!originalSrc || originalSrc.startsWith('data:')) return null;
        
        const srcset = [];
        const widths = [320, 640, 960, 1280, 1920];
        
        widths.forEach(width => {
            const resizedUrl = this.getResizedImageUrl(originalSrc, width);
            if (resizedUrl) {
                srcset.push(`${resizedUrl} ${width}w`);
            }
        });
        
        return srcset.join(', ');
    }
    
    // Resized image URL al
    getResizedImageUrl(originalSrc, width) {
        // Bu fonksiyon server-side image resizing için kullanılabilir
        // Şimdilik original URL'i döndür
        return originalSrc;
    }
    
    // Client-side image compression
    compressImage(file, quality = 0.8, maxWidth = 1920) {
        return new Promise((resolve) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();
            
            img.onload = () => {
                // Aspect ratio koru
                const ratio = Math.min(maxWidth / img.width, maxWidth / img.height);
                const newWidth = img.width * ratio;
                const newHeight = img.height * ratio;
                
                canvas.width = newWidth;
                canvas.height = newHeight;
                
                // Resmi çiz
                ctx.drawImage(img, 0, 0, newWidth, newHeight);
                
                // Compress
                canvas.toBlob(resolve, 'image/jpeg', quality);
            };
            
            img.src = URL.createObjectURL(file);
        });
    }
    
    // Resim kaynaklarını güncelle
    updateImageSources() {
        const images = document.querySelectorAll('img[data-src]');
        
        images.forEach(img => {
            if (this.isElementInViewport(img)) {
                this.loadImage(img);
            }
        });
    }
    
    // Yeni resim ekle
    addImage(img) {
        if (img && !this.optimizedImages.has(img)) {
            this.optimizeImage(img);
        }
    }
    
    // Optimization stats al
    getOptimizationStats() {
        return {
            totalImages: this.optimizedImages.size,
            webpSupported: this.webpSupported,
            lazyLoaded: document.querySelectorAll('img.lazy').length,
            responsive: document.querySelectorAll('img[srcset]').length,
            timestamp: new Date().toISOString()
        };
    }
    
    // Cleanup
    destroy() {
        if (this.lazyLoadObserver) {
            this.lazyLoadObserver.disconnect();
        }
        
        this.optimizedImages.clear();
        this.isInitialized = false;
        
        console.log('🗑️ Image Optimizer temizlendi');
    }
}

// Global instance
window.imageOptimizer = new ImageOptimizer();

// Mutation Observer for dynamically added images
const imageObserver = new MutationObserver((mutations) => {
    mutations.forEach(mutation => {
        mutation.addedNodes.forEach(node => {
            if (node.nodeType === Node.ELEMENT_NODE) {
                // Yeni eklenen resimleri bul
                const images = node.querySelectorAll ? node.querySelectorAll('img') : [];
                if (node.tagName === 'IMG') {
                    images.push(node);
                }
                
                images.forEach(img => {
                    if (window.imageOptimizer) {
                        window.imageOptimizer.addImage(img);
                    }
                });
            }
        });
    });
});

// DOM değişikliklerini izle
imageObserver.observe(document.body, {
    childList: true,
    subtree: true
});

// Page unload'ta cleanup
window.addEventListener('beforeunload', () => {
    if (window.imageOptimizer) {
        window.imageOptimizer.destroy();
    }
    
    if (imageObserver) {
        imageObserver.disconnect();
    }
});
