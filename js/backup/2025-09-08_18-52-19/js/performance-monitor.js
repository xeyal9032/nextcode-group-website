// Performance Monitor System
// NextCode Group Real-time Performance Monitoring

class PerformanceMonitor {
    constructor() {
        this.metrics = {};
        this.alerts = [];
        this.isMonitoring = false;
        this.monitoringInterval = null;
        this.alertThresholds = {
            memory: 80, // %80 memory usage
            cpu: 70,    // %70 CPU usage
            response: 2000, // 2s response time
            errors: 5   // 5 error per minute
        };
        
        this.init();
    }
    
    init() {
        console.log('📊 Performance Monitor başlatılıyor...');
        
        // Performance monitoring başlat
        this.startMonitoring();
        
        // Error monitoring
        this.setupErrorMonitoring();
        
        // Resource monitoring
        this.setupResourceMonitoring();
        
        // Network monitoring
        this.setupNetworkMonitoring();
        
        console.log('✅ Performance Monitor aktif');
    }
    
    // Performance monitoring başlat
    startMonitoring() {
        if (this.isMonitoring) return;
        
        this.isMonitoring = true;
        
        // Her 10 saniyede bir metrics topla
        this.monitoringInterval = setInterval(() => {
            this.collectMetrics();
            this.checkAlerts();
            this.sendMetricsToAPI();
        }, 10000);
        
        console.log('🔄 Performance monitoring başlatıldı');
    }
    
    // Performance monitoring durdur
    stopMonitoring() {
        if (this.monitoringInterval) {
            clearInterval(this.monitoringInterval);
            this.monitoringInterval = null;
        }
        
        this.isMonitoring = false;
        console.log('⏹️ Performance monitoring durduruldu');
    }
    
    // Performance mark ekle
    mark(name) {
        if (window.performance && window.performance.mark) {
            window.performance.mark(name);
            console.log(`📊 Performance mark: ${name}`);
        }
    }
    
    // Performance measure ekle
    measure(name, startMark, endMark) {
        if (window.performance && window.performance.measure) {
            try {
                window.performance.measure(name, startMark, endMark);
                console.log(`📊 Performance measure: ${name}`);
            } catch (error) {
                console.warn(`⚠️ Performance measure failed: ${name}`, error);
            }
        }
    }
    
    // Metrics topla
    collectMetrics() {
        const timestamp = new Date().toISOString();
        
        // Memory metrics
        if ('memory' in performance) {
            const memory = performance.memory;
            this.metrics.memory = {
                used: Math.round(memory.usedJSHeapSize / 1048576),
                total: Math.round(memory.totalJSHeapSize / 1048576),
                limit: Math.round(memory.jsHeapSizeLimit / 1048576),
                percentage: Math.round((memory.usedJSHeapSize / memory.jsHeapSizeLimit) * 100),
                timestamp
            };
        }
        
        // CPU metrics (approximate)
        this.metrics.cpu = {
            load: this.getCPULoad(),
            timestamp
        };
        
        // Page performance metrics
        if (window.performance && window.performance.timing) {
            const timing = performance.timing;
            this.metrics.pagePerformance = {
                loadTime: timing.loadEventEnd - timing.navigationStart,
                domReady: timing.domContentLoadedEventEnd - timing.navigationStart,
                firstPaint: this.getFirstPaint(),
                timestamp
            };
        }
        
        // User interaction metrics
        this.metrics.userInteraction = {
            clicks: this.getClickCount(),
            scrolls: this.getScrollCount(),
            keypresses: this.getKeypressCount(),
            timestamp
        };
        
        // Network metrics
        this.metrics.network = {
            connection: this.getNetworkInfo(),
            resources: this.getResourceMetrics(),
            timestamp
        };
    }
    
    // CPU load hesapla (approximate)
    getCPULoad() {
        const start = performance.now();
        
        // Simple CPU test
        let result = 0;
        for (let i = 0; i < 1000000; i++) {
            result += Math.random();
        }
        
        const end = performance.now();
        const duration = end - start;
        
        // Normalize to percentage (lower is better)
        return Math.round((1000 - duration) / 10);
    }
    
    // First paint al
    getFirstPaint() {
        const paintEntries = performance.getEntriesByType('paint');
        const fcpEntry = paintEntries.find(entry => entry.name === 'first-contentful-paint');
        return fcpEntry ? fcpEntry.startTime : null;
    }
    
    // Click count al
    getClickCount() {
        return this.metrics.userInteraction?.clicks || 0;
    }
    
    // Scroll count al
    getScrollCount() {
        return this.metrics.userInteraction?.scrolls || 0;
    }
    
    // Keypress count al
    getKeypressCount() {
        return this.metrics.userInteraction?.keypresses || 0;
    }
    
    // Network info al
    getNetworkInfo() {
        if (navigator.connection) {
            return {
                effectiveType: navigator.connection.effectiveType,
                downlink: navigator.connection.downlink,
                rtt: navigator.connection.rtt,
                saveData: navigator.connection.saveData
            };
        }
        return null;
    }
    
    // Resource metrics al
    getResourceMetrics() {
        const resources = performance.getEntriesByType('resource');
        
        return {
            count: resources.length,
            totalSize: resources.reduce((total, resource) => total + (resource.transferSize || 0), 0),
            slowResources: resources.filter(resource => resource.duration > 1000).length,
            failedResources: resources.filter(resource => resource.initiatorType === 'xmlhttprequest' && resource.responseEnd === 0).length
        };
    }
    
    // Error monitoring kur
    setupErrorMonitoring() {
        let errorCount = 0;
        let lastErrorTime = Date.now();
        
        // JavaScript errors
        window.addEventListener('error', (event) => {
            errorCount++;
            this.recordError('javascript', event.message, event.filename, event.lineno);
        });
        
        // Promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            errorCount++;
            this.recordError('promise', event.reason?.toString(), 'promise', 0);
        });
        
        // Resource errors
        window.addEventListener('error', (event) => {
            if (event.target !== window) {
                errorCount++;
                this.recordError('resource', `Failed to load ${event.target.src || event.target.href}`, 'resource', 0);
            }
        }, true);
        
        // Error count reset (her dakika)
        setInterval(() => {
            const now = Date.now();
            if (now - lastErrorTime > 60000) {
                errorCount = 0;
                lastErrorTime = now;
            }
        }, 60000);
        
        this.metrics.errors = { count: errorCount, timestamp: new Date().toISOString() };
    }
    
    // Resource monitoring kur
    setupResourceMonitoring() {
        // Resource loading
        const observer = new PerformanceObserver((list) => {
            list.getEntries().forEach(entry => {
                if (entry.entryType === 'resource') {
                    this.analyzeResource(entry);
                }
            });
        });
        
        try {
            observer.observe({ entryTypes: ['resource'] });
        } catch (error) {
            console.warn('Resource observer kurulamadı:', error);
        }
    }
    
    // Network monitoring kur
    setupNetworkMonitoring() {
        // Network status changes
        if ('onLine' in navigator) {
            window.addEventListener('online', () => {
                this.recordNetworkEvent('online');
            });
            
            window.addEventListener('offline', () => {
                this.recordNetworkEvent('offline');
            });
        }
        
        // Network quality monitoring
        if ('connection' in navigator) {
            navigator.connection.addEventListener('change', () => {
                this.recordNetworkEvent('connection_change', this.getNetworkInfo());
            });
        }
    }
    
    // Error kaydet
    recordError(type, message, source, line) {
        const error = {
            type,
            message: message ? message.substring(0, 200) : 'Unknown error',
            source: source || 'unknown',
            line: line || 0,
            timestamp: new Date().toISOString(),
            url: window.location.href,
            userAgent: navigator.userAgent
        };
        
        this.alerts.push(error);
        
        // Console'da detaylı göster
        console.error('🚨 Performance Error:', {
            type: error.type,
            message: error.message,
            source: error.source,
            line: error.line,
            timestamp: error.timestamp
        });
        
        // API'ye gönder
        this.sendErrorToAPI(error);
    }
    
    // Network event kaydet
    recordNetworkEvent(type, data = null) {
        const event = {
            type,
            data,
            timestamp: new Date().toISOString()
        };
        
        console.log('🌐 Network Event:', event);
    }
    
    // Resource analiz et
    analyzeResource(entry) {
        if (entry.duration > 1000) {
            console.warn('⚠️ Slow resource:', entry.name, entry.duration + 'ms');
        }
        
        if (entry.transferSize > 1024 * 1024) {
            console.warn('⚠️ Large resource:', entry.name, Math.round(entry.transferSize / 1024) + 'KB');
        }
    }
    
    // Alerts kontrol et
    checkAlerts() {
        const alerts = [];
        
        // Memory alert
        if (this.metrics.memory && this.metrics.memory.percentage > this.alertThresholds.memory) {
            alerts.push({
                type: 'memory',
                message: `Memory usage yüksek: ${this.metrics.memory.percentage}%`,
                severity: 'warning',
                timestamp: new Date().toISOString()
            });
        }
        
        // CPU alert
        if (this.metrics.cpu && this.metrics.cpu.load > this.alertThresholds.cpu) {
            alerts.push({
                type: 'cpu',
                message: `CPU load yüksek: ${this.metrics.cpu.load}%`,
                severity: 'warning',
                timestamp: new Date().toISOString()
            });
        }
        
        // Error alert
        if (this.metrics.errors && this.metrics.errors.count > this.alertThresholds.errors) {
            alerts.push({
                type: 'errors',
                message: `Çok fazla hata: ${this.metrics.errors.count} hata/dakika`,
                severity: 'error',
                timestamp: new Date().toISOString()
            });
        }
        
        // Alerts'i göster
        alerts.forEach(alert => {
            this.showAlert(alert);
        });
        
        // Alerts'i API'ye gönder
        if (alerts.length > 0) {
            this.sendAlertsToAPI(alerts);
        }
    }
    
    // Alert göster
    showAlert(alert) {
        // Console'da göster
        const icon = alert.severity === 'error' ? '🚨' : '⚠️';
        console.log(`${icon} ${alert.type.toUpperCase()}: ${alert.message}`);
        
        // UI'da göster (opsiyonel)
        this.showUINotification(alert);
    }
    
    // UI notification göster
    showUINotification(alert) {
        // Simple notification
        const notification = document.createElement('div');
        notification.className = `alert alert-${alert.severity === 'error' ? 'danger' : 'warning'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        
        notification.innerHTML = `
            <strong>${alert.type.toUpperCase()}</strong><br>
            ${alert.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // 5 saniye sonra kaldır
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Metrics API'ye gönder
    sendMetricsToAPI() {
        const data = {
            metrics: this.metrics,
            timestamp: new Date().toISOString(),
            url: window.location.href,
            userAgent: navigator.userAgent
        };
        
        fetch('/api/performance-monitor.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        }).catch(error => {
            console.error('Metrics gönderme hatası:', error);
        });
    }
    
    // Error API'ye gönder
    sendErrorToAPI(error) {
        fetch('/api/performance-monitor.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'error',
                data: error
            })
        }).catch(err => {
            console.error('Error gönderme hatası:', err);
        });
    }
    
    // Alerts API'ye gönder
    sendAlertsToAPI(alerts) {
        fetch('/api/performance-monitor.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'alerts',
                data: alerts
            })
        }).catch(error => {
            console.error('Alerts gönderme hatası:', error);
        });
    }
    
    // Performance raporu al
    getPerformanceReport() {
        return {
            metrics: this.metrics,
            alerts: this.alerts,
            summary: {
                memoryUsage: this.metrics.memory?.percentage || 0,
                cpuLoad: this.metrics.cpu?.load || 0,
                errorCount: this.metrics.errors?.count || 0,
                pageLoadTime: this.metrics.pagePerformance?.loadTime || 0
            },
            timestamp: new Date().toISOString()
        };
    }
    
    // Cleanup
    destroy() {
        this.stopMonitoring();
        console.log('🗑️ Performance Monitor temizlendi');
    }
}

// Global instance
window.performanceMonitor = new PerformanceMonitor();

// Page unload'ta cleanup
window.addEventListener('beforeunload', () => {
    if (window.performanceMonitor) {
        window.performanceMonitor.destroy();
    }
});
