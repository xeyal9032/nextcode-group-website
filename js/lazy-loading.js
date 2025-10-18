/**
 * NextCode Group Web Projesi - Lazy Loading Sistemi
 * Görsellerin geç yüklenmesi için optimize edilmiş sistem
 */

class LazyLoader {
    constructor(options = {}) {
        this.options = {
            root: null,
            rootMargin: '50px',
            threshold: 0.1,
            loadingClass: 'lazy-loading',
            loadedClass: 'lazy-loaded',
            errorClass: 'lazy-error',
            placeholder: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAwIiBoZWlnaHQ9IjYwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIyNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkxvYWRpbmcuLi48L3RleHQ+PC9zdmc+',
            ...options
        };
        
        this.observer = null;
        this.images = [];
        this.loadedCount = 0;
        this.totalCount = 0;
        
        this.init();
    }
    
    init() {
        this.createObserver();
        this.scanImages();
        this.bindEvents();
        
        console.log('🖼️ LazyLoader başlatıldı');
    }
    
    createObserver() {
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver(
                this.handleIntersection.bind(this),
                {
                    root: this.options.root,
                    rootMargin: this.options.rootMargin,
                    threshold: this.options.threshold
                }
            );
        } else {
            console.warn('⚠️ IntersectionObserver desteklenmiyor, fallback kullanılıyor');
            this.fallbackLoad();
        }
    }
    
    scanImages() {
        const images = document.querySelectorAll('img[data-src], img[data-lazy]');
        this.totalCount = images.length;
        
        images.forEach(img => {
            this.images.push(img);
            this.setupImage(img);
            
            if (this.observer) {
                this.observer.observe(img);
            }
        });
        
        console.log(`📊 ${this.totalCount} görsel tespit edildi`);
    }
    
    setupImage(img) {
        // Loading state
        img.classList.add(this.options.loadingClass);
        
        // Placeholder image
        if (!img.src || img.src === '') {
            img.src = this.options.placeholder;
        }
        
        // Alt text ekle
        if (!img.alt) {
            img.alt = 'Görsel yükleniyor...';
        }
        
        // Loading indicator
        this.addLoadingIndicator(img);
    }
    
    addLoadingIndicator(img) {
        const wrapper = document.createElement('div');
        wrapper.className = 'lazy-wrapper';
        wrapper.style.position = 'relative';
        wrapper.style.display = 'inline-block';
        
        img.parentNode.insertBefore(wrapper, img);
        wrapper.appendChild(img);
        
        // Loading spinner
        const spinner = document.createElement('div');
        spinner.className = 'lazy-spinner';
        spinner.innerHTML = `
            <div class="spinner">
                <div class="spinner-inner"></div>
            </div>
        `;
        wrapper.appendChild(spinner);
        
        // CSS stilleri
        this.addStyles();
    }
    
    addStyles() {
        if (document.getElementById('lazy-loader-styles')) return;
        
        const style = document.createElement('style');
        style.id = 'lazy-loader-styles';
        style.textContent = `
            .lazy-wrapper {
                position: relative;
                display: inline-block;
                overflow: hidden;
            }
            
            .lazy-loading {
                opacity: 0.7;
                transition: opacity 0.3s ease;
            }
            
            .lazy-loaded {
                opacity: 1;
                transition: opacity 0.3s ease;
            }
            
            .lazy-error {
                opacity: 0.5;
                filter: grayscale(100%);
            }
            
            .lazy-spinner {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 10;
            }
            
            .spinner {
                width: 40px;
                height: 40px;
                border: 3px solid #f3f3f3;
                border-top: 3px solid #3498db;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            
            .spinner-inner {
                width: 20px;
                height: 20px;
                border: 2px solid #f3f3f3;
                border-top: 2px solid #e74c3c;
                border-radius: 50%;
                animation: spin 0.8s linear infinite reverse;
                margin: 8px auto;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            .lazy-loaded .lazy-spinner {
                opacity: 0;
                transition: opacity 0.3s ease;
            }
        `;
        
        document.head.appendChild(style);
    }
    
    handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                this.loadImage(entry.target);
                this.observer.unobserve(entry.target);
            }
        });
    }
    
    async loadImage(img) {
        try {
            const src = img.dataset.src || img.dataset.lazy;
            if (!src) return;
            
            // Preload image
            const imageLoader = new Image();
            
            // Promise ile yükleme
            await new Promise((resolve, reject) => {
                imageLoader.onload = resolve;
                imageLoader.onerror = reject;
                imageLoader.src = src;
            });
            
            // Image yüklendi
            img.src = src;
            img.classList.remove(this.options.loadingClass);
            img.classList.add(this.options.loadedClass);
            
            // Spinner'ı kaldır
            const spinner = img.parentNode.querySelector('.lazy-spinner');
            if (spinner) {
                spinner.style.opacity = '0';
                setTimeout(() => spinner.remove(), 300);
            }
            
            // Alt text güncelle
            img.alt = img.dataset.alt || img.alt;
            
            this.loadedCount++;
            this.updateProgress();
            
            // Custom event
            this.dispatchEvent('imageLoaded', {
                image: img,
                loaded: this.loadedCount,
                total: this.totalCount
            });
            
        } catch (error) {
            console.error('❌ Görsel yükleme hatası:', error);
            this.handleImageError(img);
        }
    }
    
    handleImageError(img) {
        img.classList.remove(this.options.loadingClass);
        img.classList.add(this.options.errorClass);
        
        // Error placeholder
        img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAwIiBoZWlnaHQ9IjYwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIyNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkdyw7x2ZWwgecO2a2xlbWVkaTwvdGV4dD48L3N2Zz4=';
        img.alt = 'Görsel yüklenemedi';
        
        // Spinner'ı kaldır
        const spinner = img.parentNode.querySelector('.lazy-spinner');
        if (spinner) {
            spinner.remove();
        }
        
        this.dispatchEvent('imageError', { image: img });
    }
    
    updateProgress() {
        const progress = Math.round((this.loadedCount / this.totalCount) * 100);
        
        this.dispatchEvent('progress', {
            loaded: this.loadedCount,
            total: this.totalCount,
            progress: progress
        });
        
        if (this.loadedCount === this.totalCount) {
            this.dispatchEvent('allLoaded', {
                total: this.totalCount
            });
        }
    }
    
    fallbackLoad() {
        // IntersectionObserver desteklenmiyorsa tüm görselleri yükle
        this.images.forEach(img => {
            setTimeout(() => this.loadImage(img), 100);
        });
    }
    
    bindEvents() {
        // Custom event listener'lar
        document.addEventListener('lazyLoader:imageLoaded', (e) => {
            console.log(`✅ Görsel yüklendi: ${e.detail.loaded}/${e.detail.total}`);
        });
        
        document.addEventListener('lazyLoader:allLoaded', (e) => {
            console.log(`🎉 Tüm görseller yüklendi: ${e.detail.total} görsel`);
        });
        
        document.addEventListener('lazyLoader:imageError', (e) => {
            console.error('❌ Görsel yükleme hatası:', e.detail.image);
        });
    }
    
    dispatchEvent(eventName, detail) {
        const event = new CustomEvent(`lazyLoader:${eventName}`, {
            detail: detail
        });
        document.dispatchEvent(event);
    }
    
    // Public methods
    refresh() {
        this.images = [];
        this.loadedCount = 0;
        this.totalCount = 0;
        
        if (this.observer) {
            this.observer.disconnect();
        }
        
        this.scanImages();
    }
    
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
        
        this.images = [];
        this.loadedCount = 0;
        this.totalCount = 0;
        
        console.log('🗑️ LazyLoader temizlendi');
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', function() {
    // LazyLoader'ı başlat
    window.lazyLoader = new LazyLoader({
        rootMargin: '100px',
        threshold: 0.1
    });
    
    // Performance monitoring
    if ('PerformanceObserver' in window) {
        const observer = new PerformanceObserver((list) => {
            list.getEntries().forEach((entry) => {
                if (entry.entryType === 'resource' && entry.name.includes('images')) {
                    console.log(`📊 Görsel yükleme süresi: ${entry.duration.toFixed(2)}ms - ${entry.name}`);
                }
            });
        });
        
        observer.observe({ entryTypes: ['resource'] });
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LazyLoader;
}
