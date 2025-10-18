/**
 * Performance Optimizer - NextCode Group
 * Comprehensive performance optimization system
 */

class PerformanceOptimizer {
    constructor() {
        this.config = {
            // Image optimization
            imageQuality: 85,
            webpEnabled: true,
            lazyLoadThreshold: '50px',
            
            // Resource optimization
            preloadCritical: true,
            deferNonCritical: true,
            
            // Memory management
            memoryThreshold: 80, // percentage
            cleanupInterval: 300000, // 5 minutes
            
            // Network optimization
            enableCompression: true,
            cacheStrategy: 'stale-while-revalidate',
            
            // Performance monitoring
            metricsEnabled: true,
            alertThresholds: {
                lcp: 2500, // ms
                fid: 100,  // ms
                cls: 0.1   // score
            }
        };
        
        this.metrics = {
            pageLoad: null,
            resources: [],
            memory: null,
            network: null,
            vitals: {}
        };
        
        this.observers = new Map();
        this.cache = new Map();
        this.isInitialized = false;
        
        this.init();
    }
    
    async init() {
        console.log('🚀 Performance Optimizer başlatılıyor...');
        
        try {
            // Core optimizations
            await this.initCoreOptimizations();
            
            // Image optimizations
            await this.initImageOptimizations();
            
            // Resource optimizations
            await this.initResourceOptimizations();
            
            // Memory management
            await this.initMemoryManagement();
            
            // Performance monitoring
            await this.initPerformanceMonitoring();
            
            // Network optimizations
            await this.initNetworkOptimizations();
            
            this.isInitialized = true;
            console.log('✅ Performance Optimizer aktif');
            
        } catch (error) {
            console.error('❌ Performance Optimizer başlatılamadı:', error);
        }
    }
    
    // Core Performance Optimizations
    async initCoreOptimizations() {
        // Critical resource hints
        this.addResourceHints();
        
        // Optimize event listeners
        this.optimizeEventListeners();
        
        // Setup viewport optimizations
        this.setupViewportOptimizations();
        
        // Initialize Web Workers for heavy tasks
        this.initWebWorkers();
    }
    
    addResourceHints() {
        const head = document.head;
        
        // DNS prefetch for external domains
        const externalDomains = [
            'fonts.googleapis.com',
            'fonts.gstatic.com',
            'cdnjs.cloudflare.com'
        ];
        
        externalDomains.forEach(domain => {
            const link = document.createElement('link');
            link.rel = 'dns-prefetch';
            link.href = `//${domain}`;
            head.appendChild(link);
        });
        
        // Preconnect to critical origins
        const criticalOrigins = [
            'https://fonts.googleapis.com',
            'https://api.nextcode.az'
        ];
        
        criticalOrigins.forEach(origin => {
            const link = document.createElement('link');
            link.rel = 'preconnect';
            link.href = origin;
            link.crossOrigin = 'anonymous';
            head.appendChild(link);
        });
    }
    
    optimizeEventListeners() {
        // Passive event listeners for better scroll performance
        const passiveEvents = ['scroll', 'wheel', 'touchstart', 'touchmove'];
        
        passiveEvents.forEach(eventType => {
            const originalAddEventListener = EventTarget.prototype.addEventListener;
            EventTarget.prototype.addEventListener = function(type, listener, options) {
                if (passiveEvents.includes(type) && typeof options !== 'object') {
                    options = { passive: true };
                } else if (typeof options === 'object' && options.passive === undefined) {
                    options.passive = true;
                }
                return originalAddEventListener.call(this, type, listener, options);
            };
        });
    }
    
    setupViewportOptimizations() {
        // Optimize viewport meta tag
        let viewport = document.querySelector('meta[name="viewport"]');
        if (!viewport) {
            viewport = document.createElement('meta');
            viewport.name = 'viewport';
            document.head.appendChild(viewport);
        }
        
        viewport.content = 'width=device-width, initial-scale=1, viewport-fit=cover';
        
        // Add color-scheme meta for better rendering
        const colorScheme = document.createElement('meta');
        colorScheme.name = 'color-scheme';
        colorScheme.content = 'light dark';
        document.head.appendChild(colorScheme);
    }
    
    initWebWorkers() {
        if ('Worker' in window) {
            // Create worker for heavy computations
            const workerCode = `
                self.onmessage = function(e) {
                    const { type, data } = e.data;
                    
                    switch(type) {
                        case 'processImages':
                            // Image processing logic
                            self.postMessage({ type: 'imagesProcessed', result: data });
                            break;
                        case 'calculateMetrics':
                            // Performance calculations
                            self.postMessage({ type: 'metricsCalculated', result: data });
                            break;
                    }
                };
            `;
            
            const blob = new Blob([workerCode], { type: 'application/javascript' });
            this.worker = new Worker(URL.createObjectURL(blob));
            
            this.worker.onmessage = (e) => {
                this.handleWorkerMessage(e.data);
            };
        }
    }
    
    // Image Optimizations
    async initImageOptimizations() {
        // Setup advanced lazy loading
        this.setupAdvancedLazyLoading();
        
        // Implement responsive images
        this.implementResponsiveImages();
        
        // Setup image format optimization
        this.setupImageFormatOptimization();
        
        // Preload critical images
        this.preloadCriticalImages();
    }
    
    implementResponsiveImages() {
        const images = document.querySelectorAll('img[data-sizes]');
        images.forEach(img => {
            if (img.dataset.sizes) {
                img.sizes = img.dataset.sizes;
            }
        });
    }

    setupImageFormatOptimization() {
        // WebP support detection and implementation
        if (this.supportsWebP()) {
            const images = document.querySelectorAll('img[data-webp]');
            images.forEach(img => {
                if (img.dataset.webp) {
                    img.src = img.dataset.webp;
                }
            });
        }
    }

    preloadCriticalImages() {
        const criticalImages = document.querySelectorAll('img[data-critical]');
        criticalImages.forEach(img => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = img.src || img.dataset.src;
            document.head.appendChild(link);
        });
    }

    setupAdvancedLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadImage(entry.target);
                        imageObserver.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: this.config.lazyLoadThreshold,
                threshold: 0.01
            });
            
            this.observers.set('images', imageObserver);
            
            // Observe existing lazy images
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }
    
    async loadImage(img) {
        const src = img.dataset.src;
        if (!src) return;
        
        try {
            // Create optimized image
            const optimizedSrc = await this.getOptimizedImageSrc(src);
            
            // Load with decode API for better performance
            const tempImg = new Image();
            tempImg.src = optimizedSrc;
            
            await tempImg.decode();
            
            // Apply to actual image
            img.src = optimizedSrc;
            img.removeAttribute('data-src');
            img.classList.add('loaded');
            
        } catch (error) {
            console.warn('Image loading failed:', src, error);
            // Fallback to original
            img.src = src;
            img.removeAttribute('data-src');
        }
    }
    
    async getOptimizedImageSrc(src) {
        // Check cache first
        if (this.cache.has(src)) {
            return this.cache.get(src);
        }
        
        let optimizedSrc = src;
        
        // WebP conversion if supported
        if (this.config.webpEnabled && this.supportsWebP()) {
            optimizedSrc = src.replace(/\.(jpg|jpeg|png)$/i, '.webp');
            
            // Verify WebP version exists
            if (!(await this.imageExists(optimizedSrc))) {
                optimizedSrc = src;
            }
        }
        
        // Cache the result
        this.cache.set(src, optimizedSrc);
        
        return optimizedSrc;
    }
    
    supportsWebP() {
        const canvas = document.createElement('canvas');
        canvas.width = 1;
        canvas.height = 1;
        return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    }
    
    async imageExists(src) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => resolve(true);
            img.onerror = () => resolve(false);
            img.src = src;
        });
    }
    
    // Resource Optimizations
    async initResourceOptimizations() {
        // Setup resource loading priorities
        this.setupResourcePriorities();
        
        // Implement code splitting
        this.implementCodeSplitting();
        
        // Setup service worker caching
        this.setupServiceWorkerCaching();
    }

    implementCodeSplitting() {
        // Dynamic import support for modern browsers
        if ('import' in document.createElement('script')) {
            // Mark modules for lazy loading
            document.querySelectorAll('script[data-lazy]').forEach(script => {
                const src = script.src;
                script.remove();
                
                // Load on interaction or viewport entry
                const loadModule = () => {
                    import(src).catch(err => console.warn('Module loading failed:', err));
                };
                
                // Load on first user interaction
                ['click', 'scroll', 'keydown'].forEach(event => {
                    document.addEventListener(event, loadModule, { once: true });
                });
            });
        }
    }

    setupServiceWorkerCaching() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('✅ Service Worker registered:', registration);
                })
                .catch(error => {
                    console.warn('❌ Service Worker registration failed:', error);
                });
        }
    }

    setupResourcePriorities() {
        // Critical CSS inline
        const criticalCSS = document.querySelector('style[data-critical]');
        if (criticalCSS) {
            criticalCSS.setAttribute('data-priority', 'high');
        }
        
        // Defer non-critical JavaScript
        document.querySelectorAll('script[src]:not([data-critical])').forEach(script => {
            if (!script.defer && !script.async) {
                script.defer = true;
            }
        });
        
        // Preload critical resources
        const criticalResources = [
            { href: '/css/critical.css', as: 'style' },
            { href: '/js/critical.js', as: 'script' },
            { href: '/css/webfonts/fa-solid-900.woff2', as: 'font', type: 'font/woff2', crossorigin: 'anonymous' }
        ];
        
        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            Object.assign(link, resource);
            document.head.appendChild(link);
        });
    }
    
    // Memory Management
    async initMemoryManagement() {
        // Setup memory monitoring
        this.setupMemoryMonitoring();
        
        // Implement garbage collection hints
        this.implementGCHints();
        
        // Setup memory cleanup
        this.setupMemoryCleanup();
    }
    
    setupMemoryMonitoring() {
        if ('memory' in performance) {
            setInterval(() => {
                const memory = performance.memory;
                const usedPercent = (memory.usedJSHeapSize / memory.jsHeapSizeLimit) * 100;
                
                this.metrics.memory = {
                    used: memory.usedJSHeapSize,
                    total: memory.totalJSHeapSize,
                    limit: memory.jsHeapSizeLimit,
                    percentage: usedPercent
                };
                
                if (usedPercent > this.config.memoryThreshold) {
                    this.triggerMemoryCleanup();
                }
            }, 30000); // Check every 30 seconds
        }
    }
    
    triggerMemoryCleanup() {
        console.log('🧹 Memory cleanup triggered');
        
        // Clear caches
        this.cache.clear();
        
        // Remove unused observers
        this.cleanupObservers();
        
        // Force garbage collection if available
        if (window.gc) {
            window.gc();
        }
    }
    
    // Performance Monitoring
    async initPerformanceMonitoring() {
        // Setup Core Web Vitals monitoring
        this.setupCoreWebVitals();
        
        // Setup resource timing monitoring
        this.setupResourceTiming();
        
        // Setup long task monitoring
        this.setupLongTaskMonitoring();
    }
    
    setupCoreWebVitals() {
        if ('PerformanceObserver' in window) {
            // LCP (Largest Contentful Paint)
            new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1];
                this.metrics.vitals.lcp = lastEntry.startTime;
                
                if (lastEntry.startTime > this.config.alertThresholds.lcp) {
                    console.warn('⚠️ Poor LCP:', lastEntry.startTime + 'ms');
                }
            }).observe({ entryTypes: ['largest-contentful-paint'] });
            
            // FID (First Input Delay)
            new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    this.metrics.vitals.fid = entry.processingStart - entry.startTime;
                    
                    if (entry.processingStart - entry.startTime > this.config.alertThresholds.fid) {
                        console.warn('⚠️ Poor FID:', entry.processingStart - entry.startTime + 'ms');
                    }
                });
            }).observe({ entryTypes: ['first-input'] });
            
            // CLS (Cumulative Layout Shift)
            let clsValue = 0;
            new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    if (!entry.hadRecentInput) {
                        clsValue += entry.value;
                    }
                });
                
                this.metrics.vitals.cls = clsValue;
                
                if (clsValue > this.config.alertThresholds.cls) {
                    console.warn('⚠️ Poor CLS:', clsValue);
                }
            }).observe({ entryTypes: ['layout-shift'] });
        }
    }
    
    // Network Optimizations
    async initNetworkOptimizations() {
        // Setup connection monitoring
        this.setupConnectionMonitoring();
        
        // Implement adaptive loading
        this.implementAdaptiveLoading();
        
        // Setup offline handling
        this.setupOfflineHandling();
    }
    
    setupConnectionMonitoring() {
        if ('connection' in navigator) {
            const connection = navigator.connection;
            
            this.metrics.network = {
                effectiveType: connection.effectiveType,
                downlink: connection.downlink,
                rtt: connection.rtt,
                saveData: connection.saveData
            };
            
            connection.addEventListener('change', () => {
                this.metrics.network = {
                    effectiveType: connection.effectiveType,
                    downlink: connection.downlink,
                    rtt: connection.rtt,
                    saveData: connection.saveData
                };
                
                this.adaptToNetworkConditions();
            });
        }
    }
    
    adaptToNetworkConditions() {
        const { effectiveType, saveData } = this.metrics.network;
        
        if (effectiveType === 'slow-2g' || effectiveType === '2g' || saveData) {
            // Reduce quality for slow connections
            this.config.imageQuality = 60;
            this.config.lazyLoadThreshold = '200px';
            
            // Disable non-essential features
            this.disableNonEssentialFeatures();
        } else {
            // Restore normal quality
            this.config.imageQuality = 85;
            this.config.lazyLoadThreshold = '50px';
        }
    }
    
    // Utility Methods
    getMetrics() {
        return {
            ...this.metrics,
            timestamp: new Date().toISOString(),
            config: this.config
        };
    }
    
    async generateReport() {
        const metrics = this.getMetrics();
        
        const report = {
            summary: {
                score: this.calculatePerformanceScore(),
                recommendations: this.generateRecommendations()
            },
            details: metrics,
            timestamp: new Date().toISOString()
        };
        
        console.log('📊 Performance Report:', report);
        return report;
    }
    
    calculatePerformanceScore() {
        let score = 100;
        
        // Deduct points based on metrics
        if (this.metrics.vitals.lcp > 2500) score -= 20;
        if (this.metrics.vitals.fid > 100) score -= 15;
        if (this.metrics.vitals.cls > 0.1) score -= 15;
        if (this.metrics.memory?.percentage > 80) score -= 10;
        
        return Math.max(0, score);
    }
    
    generateRecommendations() {
        const recommendations = [];
        
        if (this.metrics.vitals.lcp > 2500) {
            recommendations.push('Optimize Largest Contentful Paint by preloading critical images');
        }
        
        if (this.metrics.vitals.fid > 100) {
            recommendations.push('Reduce First Input Delay by optimizing JavaScript execution');
        }
        
        if (this.metrics.vitals.cls > 0.1) {
            recommendations.push('Improve Cumulative Layout Shift by setting image dimensions');
        }
        
        if (this.metrics.memory?.percentage > 80) {
            recommendations.push('Optimize memory usage by implementing better caching strategies');
        }
        
        return recommendations;
    }
    
    // Cleanup
    destroy() {
        // Clear all observers
        this.observers.forEach(observer => observer.disconnect());
        this.observers.clear();
        
        // Clear caches
        this.cache.clear();
        
        // Terminate worker
        if (this.worker) {
            this.worker.terminate();
        }
        
        console.log('🗑️ Performance Optimizer cleaned up');
    }

    implementGCHints() {
        // Garbage collection hints for better memory management
        if ('gc' in window && typeof window.gc === 'function') {
            // Schedule periodic garbage collection hints
            setInterval(() => {
                if (performance.memory && performance.memory.usedJSHeapSize > 50 * 1024 * 1024) {
                    window.gc();
                }
            }, 30000);
        }

        // Memory pressure detection
        if ('memory' in performance) {
            const checkMemoryPressure = () => {
                const memory = performance.memory;
                const usageRatio = memory.usedJSHeapSize / memory.totalJSHeapSize;
                
                if (usageRatio > 0.9) {
                    // High memory usage - trigger cleanup
                    this.cleanupUnusedResources();
                }
            };

            setInterval(checkMemoryPressure, 10000);
        }
    }

    setupResourceTiming() {
        // Monitor resource loading performance
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                list.getEntries().forEach((entry) => {
                    if (entry.duration > 1000) {
                        console.warn(`Slow resource: ${entry.name} took ${entry.duration}ms`);
                    }
                });
            });
            observer.observe({ entryTypes: ['resource'] });
        }

        // Track navigation timing
        window.addEventListener('load', () => {
            setTimeout(() => {
                const navigation = performance.getEntriesByType('navigation')[0];
                if (navigation) {
                    this.metrics.navigationTiming = {
                        domContentLoaded: navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart,
                        loadComplete: navigation.loadEventEnd - navigation.loadEventStart,
                        totalTime: navigation.loadEventEnd - navigation.fetchStart
                    };
                }
            }, 0);
        });
    }

    setupLongTaskMonitoring() {
        // Monitor long tasks that block the main thread
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                list.getEntries().forEach((entry) => {
                    console.warn(`Long task detected: ${entry.duration}ms at ${entry.startTime}`);
                    
                    // Track long tasks in metrics
                    if (!this.metrics.longTasks) {
                        this.metrics.longTasks = [];
                    }
                    this.metrics.longTasks.push({
                        duration: entry.duration,
                        startTime: entry.startTime,
                        attribution: entry.attribution
                    });
                });
            });
            
            try {
                observer.observe({ entryTypes: ['longtask'] });
            } catch (e) {
                console.log('Long task monitoring not supported');
            }
        }
    }

    implementAdaptiveLoading() {
        // Adaptive loading based on network conditions
        if ('connection' in navigator) {
            const connection = navigator.connection;
            
            // Adjust loading strategy based on connection type
            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                // Disable non-critical features for slow connections
                this.disableNonCriticalFeatures();
            } else if (connection.effectiveType === '3g') {
                // Reduce image quality for 3G
                this.adaptImageQuality('medium');
            } else {
                // Full quality for 4G and above
                this.adaptImageQuality('high');
            }
            
            // Listen for connection changes
            connection.addEventListener('change', () => {
                this.adaptToConnection();
            });
        }
        
        // Adaptive loading based on device capabilities
        if ('deviceMemory' in navigator) {
            const memory = navigator.deviceMemory;
            if (memory < 2) {
                // Low memory device - reduce features
                this.optimizeForLowMemory();
            }
        }
    }

    disableNonCriticalFeatures() {
        // Disable animations for slow connections
        document.body.classList.add('reduce-motion');
        
        // Disable auto-play videos
        const videos = document.querySelectorAll('video[autoplay]');
        videos.forEach(video => {
            video.removeAttribute('autoplay');
        });
    }

    adaptImageQuality(quality) {
        const images = document.querySelectorAll('img[data-adaptive]');
        images.forEach(img => {
            const src = img.dataset.src || img.src;
            if (quality === 'medium') {
                img.src = src.replace(/\.(jpg|jpeg|png)$/i, '_medium.$1');
            } else if (quality === 'high') {
                img.src = src;
            }
        });
    }

    optimizeForLowMemory() {
        // Reduce cache size
        if ('caches' in window) {
            caches.keys().then(names => {
                names.forEach(name => {
                    if (name.includes('images')) {
                        caches.delete(name);
                    }
                });
            });
        }
        
        // Limit concurrent image loading
        this.maxConcurrentImages = 2;
    }

    adaptToConnection() {
        const connection = navigator.connection;
        if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
            this.disableNonCriticalFeatures();
        } else {
            document.body.classList.remove('reduce-motion');
        }
    }

    setupOfflineHandling() {
        // Handle offline/online events
        window.addEventListener('online', () => {
            console.log('Connection restored');
            document.body.classList.remove('offline-mode');
            this.syncOfflineData();
        });

        window.addEventListener('offline', () => {
            console.log('Connection lost');
            document.body.classList.add('offline-mode');
            this.enableOfflineMode();
        });

        // Check initial connection status
        if (!navigator.onLine) {
            this.enableOfflineMode();
        }
    }

    enableOfflineMode() {
        // Cache current page for offline viewing
        if ('caches' in window) {
            caches.open('offline-cache').then(cache => {
                cache.add(window.location.pathname);
            });
        }

        // Show offline notification
        this.showOfflineNotification();
    }

    syncOfflineData() {
        // Sync any offline data when connection is restored
        const offlineData = localStorage.getItem('offline-data');
        if (offlineData) {
            try {
                const data = JSON.parse(offlineData);
                // Process offline data
                this.processOfflineData(data);
                localStorage.removeItem('offline-data');
            } catch (e) {
                console.error('Error syncing offline data:', e);
            }
        }
    }

    showOfflineNotification() {
        const notification = document.createElement('div');
        notification.className = 'offline-notification';
        notification.textContent = 'You are currently offline. Some features may be limited.';
        notification.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #f39c12;
            color: white;
            padding: 10px;
            text-align: center;
            z-index: 9999;
        `;
        document.body.appendChild(notification);

        // Remove notification when online
        window.addEventListener('online', () => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, { once: true });
    }

    processOfflineData(data) {
        // Process any data that was collected while offline
        console.log('Processing offline data:', data);
    }

    setupMemoryCleanup() {
        // Setup automatic memory cleanup
        setInterval(() => {
            this.cleanupUnusedResources();
        }, 60000); // Every minute

        // Setup cleanup on page visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.cleanupUnusedResources();
            }
        });

        // Setup cleanup before page unload
        window.addEventListener('beforeunload', () => {
            this.cleanupUnusedResources();
        });
    }

    cleanupUnusedResources() {
        // Remove unused event listeners
        const unusedElements = document.querySelectorAll('[data-cleanup="true"]');
        unusedElements.forEach(element => {
            element.remove();
        });

        // Clear cached data older than 5 minutes
        const now = Date.now();
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('cache_')) {
                const item = JSON.parse(localStorage.getItem(key) || '{}');
                if (item.timestamp && now - item.timestamp > 300000) {
                    localStorage.removeItem(key);
                }
            }
        });
    }
}

// Global instance
window.performanceOptimizer = new PerformanceOptimizer();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PerformanceOptimizer;
}