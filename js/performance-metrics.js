// Performance Metrics and Reporting System
class PerformanceMetrics {
    constructor() {
        this.metrics = {
            navigation: {},
            resources: [],
            vitals: {},
            custom: {},
            errors: []
        };
        
        this.observers = {};
        this.reportingInterval = null;
        this.init();
    }

    init() {
        this.measureNavigationTiming();
        this.measureResourceTiming();
        this.measureCoreWebVitals();
        this.setupPerformanceObserver();
        this.trackCustomMetrics();
        this.setupErrorTracking();
        this.startPeriodicReporting();
    }

    // Measure Navigation Timing
    measureNavigationTiming() {
        if (!window.performance || !window.performance.timing) {
            console.warn('Navigation Timing API not supported');
            return;
        }

        const timing = window.performance.timing;
        const navigation = window.performance.navigation;
        
        this.metrics.navigation = {
            // DNS lookup time
            dns_lookup: timing.domainLookupEnd - timing.domainLookupStart,
            
            // TCP connection time
            tcp_connection: timing.connectEnd - timing.connectStart,
            
            // SSL handshake time
            ssl_handshake: timing.secureConnectionStart > 0 ? 
                timing.connectEnd - timing.secureConnectionStart : 0,
            
            // Request time
            request_time: timing.responseStart - timing.requestStart,
            
            // Response time
            response_time: timing.responseEnd - timing.responseStart,
            
            // DOM processing time
            dom_processing: timing.domComplete - timing.domLoading,
            
            // DOM content loaded
            dom_content_loaded: timing.domContentLoadedEventEnd - timing.navigationStart,
            
            // Page load time
            page_load_time: timing.loadEventEnd - timing.navigationStart,
            
            // Time to first byte
            ttfb: timing.responseStart - timing.navigationStart,
            
            // Navigation type
            navigation_type: this.getNavigationType(navigation.type),
            
            // Redirect count
            redirect_count: navigation.redirectCount,
            
            // Redirect time
            redirect_time: timing.redirectEnd - timing.redirectStart,
            
            // Timestamp
            timestamp: new Date().toISOString()
        };
    }

    // Get navigation type description
    getNavigationType(type) {
        const types = {
            0: 'navigate',
            1: 'reload',
            2: 'back_forward',
            255: 'reserved'
        };
        return types[type] || 'unknown';
    }

    // Measure Resource Timing
    measureResourceTiming() {
        if (!window.performance || !window.performance.getEntriesByType) {
            console.warn('Resource Timing API not supported');
            return;
        }

        const resources = window.performance.getEntriesByType('resource');
        
        this.metrics.resources = resources.map(resource => ({
            name: resource.name,
            type: this.getResourceType(resource.name),
            size: resource.transferSize || 0,
            duration: resource.duration,
            start_time: resource.startTime,
            dns_lookup: resource.domainLookupEnd - resource.domainLookupStart,
            tcp_connection: resource.connectEnd - resource.connectStart,
            ssl_handshake: resource.secureConnectionStart > 0 ? 
                resource.connectEnd - resource.secureConnectionStart : 0,
            request_time: resource.responseStart - resource.requestStart,
            response_time: resource.responseEnd - resource.responseStart,
            cache_hit: resource.transferSize === 0 && resource.decodedBodySize > 0
        }));
    }

    // Determine resource type
    getResourceType(url) {
        const extension = url.split('.').pop().toLowerCase().split('?')[0];
        
        const types = {
            'css': 'stylesheet',
            'js': 'script',
            'jpg': 'image',
            'jpeg': 'image',
            'png': 'image',
            'gif': 'image',
            'svg': 'image',
            'webp': 'image',
            'woff': 'font',
            'woff2': 'font',
            'ttf': 'font',
            'eot': 'font',
            'mp4': 'video',
            'webm': 'video',
            'mp3': 'audio',
            'wav': 'audio'
        };
        
        return types[extension] || 'other';
    }

    // Measure Core Web Vitals
    measureCoreWebVitals() {
        // Largest Contentful Paint (LCP)
        this.measureLCP();
        
        // First Input Delay (FID)
        this.measureFID();
        
        // Cumulative Layout Shift (CLS)
        this.measureCLS();
        
        // First Contentful Paint (FCP)
        this.measureFCP();
        
        // Time to Interactive (TTI)
        this.measureTTI();
    }

    // Measure Largest Contentful Paint
    measureLCP() {
        if (!window.PerformanceObserver) return;
        
        try {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1];
                
                this.metrics.vitals.lcp = {
                    value: lastEntry.startTime,
                    rating: this.rateLCP(lastEntry.startTime),
                    timestamp: new Date().toISOString()
                };
                
                this.sendMetric('lcp', this.metrics.vitals.lcp);
            });
            
            observer.observe({ entryTypes: ['largest-contentful-paint'] });
            this.observers.lcp = observer;
        } catch (error) {
            console.warn('LCP measurement failed:', error);
        }
    }

    // Rate LCP performance
    rateLCP(value) {
        if (value <= 2500) return 'good';
        if (value <= 4000) return 'needs-improvement';
        return 'poor';
    }

    // Measure First Input Delay
    measureFID() {
        if (!window.PerformanceObserver) return;
        
        try {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    this.metrics.vitals.fid = {
                        value: entry.processingStart - entry.startTime,
                        rating: this.rateFID(entry.processingStart - entry.startTime),
                        timestamp: new Date().toISOString()
                    };
                    
                    this.sendMetric('fid', this.metrics.vitals.fid);
                });
            });
            
            observer.observe({ entryTypes: ['first-input'] });
            this.observers.fid = observer;
        } catch (error) {
            console.warn('FID measurement failed:', error);
        }
    }

    // Rate FID performance
    rateFID(value) {
        if (value <= 100) return 'good';
        if (value <= 300) return 'needs-improvement';
        return 'poor';
    }

    // Measure Cumulative Layout Shift
    measureCLS() {
        if (!window.PerformanceObserver) return;
        
        let clsValue = 0;
        let sessionValue = 0;
        let sessionEntries = [];
        
        try {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                
                entries.forEach(entry => {
                    if (!entry.hadRecentInput) {
                        const firstSessionEntry = sessionEntries[0];
                        const lastSessionEntry = sessionEntries[sessionEntries.length - 1];
                        
                        if (sessionValue &&
                            entry.startTime - lastSessionEntry.startTime < 1000 &&
                            entry.startTime - firstSessionEntry.startTime < 5000) {
                            sessionValue += entry.value;
                            sessionEntries.push(entry);
                        } else {
                            sessionValue = entry.value;
                            sessionEntries = [entry];
                        }
                        
                        if (sessionValue > clsValue) {
                            clsValue = sessionValue;
                            
                            this.metrics.vitals.cls = {
                                value: clsValue,
                                rating: this.rateCLS(clsValue),
                                timestamp: new Date().toISOString()
                            };
                        }
                    }
                });
            });
            
            observer.observe({ entryTypes: ['layout-shift'] });
            this.observers.cls = observer;
        } catch (error) {
            console.warn('CLS measurement failed:', error);
        }
    }

    // Rate CLS performance
    rateCLS(value) {
        if (value <= 0.1) return 'good';
        if (value <= 0.25) return 'needs-improvement';
        return 'poor';
    }

    // Measure First Contentful Paint
    measureFCP() {
        if (!window.PerformanceObserver) return;
        
        try {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    if (entry.name === 'first-contentful-paint') {
                        this.metrics.vitals.fcp = {
                            value: entry.startTime,
                            rating: this.rateFCP(entry.startTime),
                            timestamp: new Date().toISOString()
                        };
                        
                        this.sendMetric('fcp', this.metrics.vitals.fcp);
                    }
                });
            });
            
            observer.observe({ entryTypes: ['paint'] });
            this.observers.fcp = observer;
        } catch (error) {
            console.warn('FCP measurement failed:', error);
        }
    }

    // Rate FCP performance
    rateFCP(value) {
        if (value <= 1800) return 'good';
        if (value <= 3000) return 'needs-improvement';
        return 'poor';
    }

    // Measure Time to Interactive (simplified)
    measureTTI() {
        // This is a simplified TTI measurement
        // In production, you might want to use a more sophisticated library
        
        window.addEventListener('load', () => {
            setTimeout(() => {
                const tti = performance.now();
                
                this.metrics.vitals.tti = {
                    value: tti,
                    rating: this.rateTTI(tti),
                    timestamp: new Date().toISOString()
                };
                
                this.sendMetric('tti', this.metrics.vitals.tti);
            }, 0);
        });
    }

    // Rate TTI performance
    rateTTI(value) {
        if (value <= 3800) return 'good';
        if (value <= 7300) return 'needs-improvement';
        return 'poor';
    }

    // Setup Performance Observer for additional metrics
    setupPerformanceObserver() {
        if (!window.PerformanceObserver) return;
        
        try {
            // Long tasks observer
            const longTaskObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    this.trackLongTask(entry);
                });
            });
            
            longTaskObserver.observe({ entryTypes: ['longtask'] });
            this.observers.longTask = longTaskObserver;
        } catch (error) {
            console.warn('Long task observer failed:', error);
        }
    }

    // Track long tasks
    trackLongTask(entry) {
        const longTaskData = {
            duration: entry.duration,
            start_time: entry.startTime,
            attribution: entry.attribution || [],
            timestamp: new Date().toISOString()
        };
        
        this.sendMetric('long_task', longTaskData);
    }

    // Track custom metrics
    trackCustomMetrics() {
        // Memory usage (if available)
        if (window.performance && window.performance.memory) {
            this.metrics.custom.memory = {
                used: window.performance.memory.usedJSHeapSize,
                total: window.performance.memory.totalJSHeapSize,
                limit: window.performance.memory.jsHeapSizeLimit,
                timestamp: new Date().toISOString()
            };
        }
        
        // Connection information
        if (navigator.connection) {
            this.metrics.custom.connection = {
                effective_type: navigator.connection.effectiveType,
                downlink: navigator.connection.downlink,
                rtt: navigator.connection.rtt,
                save_data: navigator.connection.saveData,
                timestamp: new Date().toISOString()
            };
        }
        
        // Device information
        this.metrics.custom.device = {
            user_agent: navigator.userAgent,
            platform: navigator.platform,
            language: navigator.language,
            screen_resolution: `${screen.width}x${screen.height}`,
            viewport_size: `${window.innerWidth}x${window.innerHeight}`,
            pixel_ratio: window.devicePixelRatio || 1,
            timestamp: new Date().toISOString()
        };
    }

    // Setup error tracking
    setupErrorTracking() {
        // JavaScript errors
        window.addEventListener('error', (event) => {
            this.trackError({
                type: 'javascript',
                message: event.message,
                filename: event.filename,
                line: event.lineno,
                column: event.colno,
                stack: event.error ? event.error.stack : null,
                timestamp: new Date().toISOString()
            });
        });
        
        // Unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            this.trackError({
                type: 'promise_rejection',
                message: event.reason ? event.reason.toString() : 'Unknown promise rejection',
                stack: event.reason && event.reason.stack ? event.reason.stack : null,
                timestamp: new Date().toISOString()
            });
        });
        
        // Resource loading errors
        window.addEventListener('error', (event) => {
            if (event.target !== window) {
                this.trackError({
                    type: 'resource',
                    message: `Failed to load resource: ${event.target.src || event.target.href}`,
                    element: event.target.tagName,
                    source: event.target.src || event.target.href,
                    timestamp: new Date().toISOString()
                });
            }
        }, true);
    }

    // Track error
    trackError(errorData) {
        this.metrics.errors.push(errorData);
        
        // Keep only last 50 errors
        if (this.metrics.errors.length > 50) {
            this.metrics.errors = this.metrics.errors.slice(-50);
        }
        
        this.sendMetric('error', errorData);
    }

    // Start periodic reporting
    startPeriodicReporting() {
        // Send metrics every 30 seconds
        this.reportingInterval = setInterval(() => {
            this.sendPerformanceReport();
        }, 30000);
        
        // Send final report on page unload
        window.addEventListener('beforeunload', () => {
            this.sendPerformanceReport(true);
        });
    }

    // Send individual metric
    sendMetric(type, data) {
        if (!this.isAnalyticsEnabled()) return;
        
        const metricData = {
            metric_type: type,
            data: data,
            page: window.location.pathname,
            timestamp: new Date().toISOString(),
            session_id: this.getSessionId()
        };
        
        this.sendToAnalytics('performance_metric', metricData);
    }

    // Send complete performance report
    sendPerformanceReport(isFinal = false) {
        if (!this.isAnalyticsEnabled()) return;
        
        // Update custom metrics before sending
        this.trackCustomMetrics();
        
        const report = {
            navigation: this.metrics.navigation,
            resources: this.summarizeResources(),
            vitals: this.metrics.vitals,
            custom: this.metrics.custom,
            errors: this.metrics.errors,
            is_final: isFinal,
            page: window.location.pathname,
            timestamp: new Date().toISOString(),
            session_id: this.getSessionId()
        };
        
        this.sendToAnalytics('performance_report', report);
    }

    // Summarize resource metrics
    summarizeResources() {
        const resources = this.metrics.resources;
        
        const summary = {
            total_count: resources.length,
            total_size: resources.reduce((sum, r) => sum + r.size, 0),
            total_duration: resources.reduce((sum, r) => sum + r.duration, 0),
            by_type: {}
        };
        
        // Group by resource type
        resources.forEach(resource => {
            if (!summary.by_type[resource.type]) {
                summary.by_type[resource.type] = {
                    count: 0,
                    size: 0,
                    duration: 0
                };
            }
            
            summary.by_type[resource.type].count++;
            summary.by_type[resource.type].size += resource.size;
            summary.by_type[resource.type].duration += resource.duration;
        });
        
        return summary;
    }

    // Get session ID
    getSessionId() {
        let sessionId = sessionStorage.getItem('analytics_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('analytics_session_id', sessionId);
        }
        return sessionId;
    }

    // Check if analytics is enabled
    isAnalyticsEnabled() {
        const consent = localStorage.getItem('cookie_consent');
        if (consent) {
            try {
                const consentData = JSON.parse(consent);
                return consentData.analytics === true;
            } catch (error) {
                return false;
            }
        }
        return false;
    }

    // Send data to analytics endpoint
    sendToAnalytics(eventType, data) {
        try {
            fetch('/api/analytics', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    event_type: eventType,
                    data: data,
                    timestamp: new Date().toISOString()
                })
            }).catch(error => {
                console.error('Performance analytics request failed:', error);
            });
        } catch (error) {
            console.error('Error sending performance data:', error);
        }
    }

    // Get performance summary for display
    getPerformanceSummary() {
        return {
            navigation: this.metrics.navigation,
            vitals: this.metrics.vitals,
            resources: this.summarizeResources(),
            errors: this.metrics.errors.length,
            memory: this.metrics.custom.memory,
            connection: this.metrics.custom.connection
        };
    }

    // Generate performance score
    generatePerformanceScore() {
        let score = 100;
        const vitals = this.metrics.vitals;
        
        // Deduct points based on Core Web Vitals
        if (vitals.lcp) {
            if (vitals.lcp.rating === 'poor') score -= 30;
            else if (vitals.lcp.rating === 'needs-improvement') score -= 15;
        }
        
        if (vitals.fid) {
            if (vitals.fid.rating === 'poor') score -= 25;
            else if (vitals.fid.rating === 'needs-improvement') score -= 10;
        }
        
        if (vitals.cls) {
            if (vitals.cls.rating === 'poor') score -= 25;
            else if (vitals.cls.rating === 'needs-improvement') score -= 10;
        }
        
        if (vitals.fcp) {
            if (vitals.fcp.rating === 'poor') score -= 20;
            else if (vitals.fcp.rating === 'needs-improvement') score -= 10;
        }
        
        // Deduct points for errors
        score -= Math.min(this.metrics.errors.length * 2, 20);
        
        return Math.max(0, Math.round(score));
    }

    // Cleanup
    destroy() {
        // Clear interval
        if (this.reportingInterval) {
            clearInterval(this.reportingInterval);
        }
        
        // Disconnect observers
        Object.values(this.observers).forEach(observer => {
            if (observer && observer.disconnect) {
                observer.disconnect();
            }
        });
    }
}

// Initialize performance metrics
document.addEventListener('DOMContentLoaded', () => {
    window.performanceMetrics = new PerformanceMetrics();
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.performanceMetrics) {
        window.performanceMetrics.destroy();
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PerformanceMetrics;
}