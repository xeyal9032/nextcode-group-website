// Core Web Vitals Implementation
// NextCode Group Performance Monitoring

class CoreWebVitals {
    constructor() {
        this.vitals = {};
        this.observers = [];
        this.isInitialized = false;

        this.init();
    }

    init() {
        console.log('📊 Core Web Vitals başlatılıyor...');

        // Performance Observer API desteği kontrol et
        if ('PerformanceObserver' in window) {
            this.setupLCPObserver();
            this.setupFIDObserver();
            this.setupCLSObserver();
            this.setupFCPObserver();
            this.measureTTFB();

            this.isInitialized = true;
            console.log('✅ Core Web Vitals aktif');
        } else {
            console.warn('⚠️ PerformanceObserver desteklenmiyor');
        }
    }

    // Largest Contentful Paint (LCP)
    setupLCPObserver() {
        try {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1];

                this.vitals.lcp = lastEntry.startTime;

                // LCP optimizasyonu
                this.optimizeLCP(lastEntry);

                console.log('🎯 LCP:', this.vitals.lcp + 'ms');
            });

            observer.observe({ entryTypes: ['largest-contentful-paint'] });
            this.observers.push(observer);

        } catch (error) {
            console.error('LCP Observer hatası:', error);
        }
    }

    // First Input Delay (FID)
    setupFIDObserver() {
        try {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    this.vitals.fid = entry.processingStart - entry.startTime;

                    // FID optimizasyonu
                    this.optimizeFID(entry);

                    console.log('⚡ FID:', this.vitals.fid + 'ms');
                });
            });

            observer.observe({ entryTypes: ['first-input'] });
            this.observers.push(observer);

        } catch (error) {
            console.error('FID Observer hatası:', error);
        }
    }

    // Cumulative Layout Shift (CLS)
    setupCLSObserver() {
        try {
            let clsValue = 0;
            let clsEntries = [];

            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    if (!entry.hadRecentInput) {
                        clsValue += entry.value;
                        clsEntries.push(entry);
                    }
                });

                this.vitals.cls = clsValue;

                // CLS optimizasyonu
                this.optimizeCLS(clsEntries);

                console.log('📐 CLS:', this.vitals.cls);
            });

            observer.observe({ entryTypes: ['layout-shift'] });
            this.observers.push(observer);

        } catch (error) {
            console.error('CLS Observer hatası:', error);
        }
    }

    // First Contentful Paint (FCP)
    setupFCPObserver() {
        try {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const fcpEntry = entries[0];

                this.vitals.fcp = fcpEntry.startTime;

                // FCP optimizasyonu
                this.optimizeFCP(fcpEntry);

                console.log('🎨 FCP:', this.vitals.fcp + 'ms');
            });

            observer.observe({ entryTypes: ['paint'] });
            this.observers.push(observer);

        } catch (error) {
            console.error('FCP Observer hatası:', error);
        }
    }

    // Time to First Byte (TTFB)
    measureTTFB() {
        if (window.performance && window.performance.timing) {
            const timing = performance.timing;
            this.vitals.ttfb = timing.responseStart - timing.navigationStart;

            // TTFB optimizasyonu
            this.optimizeTTFB();

            console.log('🌐 TTFB:', this.vitals.ttfb + 'ms');
        }
    }

    // LCP Optimizasyonu
    optimizeLCP(entry) {
        if (entry.startTime > 2500) {
            console.warn('⚠️ LCP yavaş:', entry.startTime + 'ms');

            // LCP elementini optimize et
            if (entry.element) {
                this.optimizeLCPElement(entry.element);
            }
        }
    }

    // FID Optimizasyonu
    optimizeFID(entry) {
        if (entry.processingStart - entry.startTime > 100) {
            console.warn('⚠️ FID yavaş:', entry.processingStart - entry.startTime + 'ms');
        }
    }

    // CLS Optimizasyonu
    optimizeCLS(entries) {
        if (this.vitals.cls > 0.1) {
            console.warn('⚠️ CLS yüksek:', this.vitals.cls);
        }
    }

    // FCP Optimizasyonu
    optimizeFCP(entry) {
        if (entry.startTime > 1800) {
            console.warn('⚠️ FCP yavaş:', entry.startTime + 'ms');
        }
    }

    // TTFB Optimizasyonu
    optimizeTTFB() {
        if (this.vitals.ttfb > 600) {
            console.warn('⚠️ TTFB yavaş:', this.vitals.ttfb + 'ms');
        }
    }

    // LCP Element Optimizasyonu
    optimizeLCPElement(element) {
        if (element.tagName === 'IMG') {
            // Image optimizasyonu
            if (!element.loading) {
                element.loading = 'eager';
            }
            if (!element.decoding) {
                element.decoding = 'async';
            }
        }
    }

    // Performance Monitoring Başlat
    startPerformanceMonitoring() {
        if (!this.isInitialized) return;

        // Memory usage monitoring
        this.monitorMemoryUsage();

        // Performance thresholds kontrol
        this.checkPerformanceThresholds();
    }

    // Memory Usage Monitoring
    monitorMemoryUsage() {
        if ('memory' in performance) {
            const memory = performance.memory;
            console.log('💾 Memory:', {
                used: Math.round(memory.usedJSHeapSize / 1048576) + 'MB',
                total: Math.round(memory.totalJSHeapSize / 1048576) + 'MB',
                limit: Math.round(memory.jsHeapSizeLimit / 1048576) + 'MB'
            });
        }
    }

    // Performance Thresholds Kontrol
    checkPerformanceThresholds() {
        const thresholds = {
            lcp: { good: 2500, needs_improvement: 4000 },
            fid: { good: 100, needs_improvement: 300 },
            cls: { good: 0.1, needs_improvement: 0.25 },
            fcp: { good: 1800, needs_improvement: 3000 },
            ttfb: { good: 600, needs_improvement: 1800 }
        };

        Object.entries(thresholds).forEach(([metric, threshold]) => {
            if (this.vitals[metric] !== undefined) {
                const value = this.vitals[metric];
                let status = 'good';

                if (value > threshold.needs_improvement) {
                    status = 'poor';
                } else if (value > threshold.good) {
                    status = 'needs_improvement';
                }

                console.log(`📊 ${metric.toUpperCase()}: ${value}ms (${status})`);
            }
        });
    }

    // Performance Raporu Al
    getPerformanceReport() {
        return {
            vitals: this.vitals,
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent,
            url: window.location.href
        };
    }

    // Metrics API'ye Gönder
    sendMetricsToAPI() {
        if (!this.isInitialized) return;

        const report = this.getPerformanceReport();

        // API'ye gönder
        fetch('/api/performance-monitor.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(report)
        }).catch(error => {
            console.error('Metrics gönderme hatası:', error);
        });
    }

    // Finalize Metrics
    finalizeMetrics() {
        if (!this.isInitialized) return;

        // Final performance metrics
        this.measureTTFB();

        // API'ye gönder
        this.sendMetricsToAPI();

        console.log('📊 Final Performance Metrics:', this.vitals);
    }

    // Cleanup
    destroy() {
        this.observers.forEach(observer => {
            try {
                observer.disconnect();
            } catch (error) {
                console.error('Observer disconnect hatası:', error);
            }
        });

        this.observers = [];
        this.isInitialized = false;

        console.log('🗑️ Core Web Vitals temizlendi');
    }
}

// Global instance
window.coreWebVitals = new CoreWebVitals();

// Page unload'ta metrics gönder
window.addEventListener('beforeunload', () => {
    if (window.coreWebVitals) {
        window.coreWebVitals.finalizeMetrics();
    }
});

// Page visibility change'de monitoring başlat
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible' && window.coreWebVitals) {
        window.coreWebVitals.startPerformanceMonitoring();
    }
});
