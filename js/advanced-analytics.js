// Advanced Analytics System with Google Analytics 4
// Enhanced tracking, custom events ve performance monitoring

class AdvancedAnalytics {
    constructor() {
        this.ga4Id = 'G-8FYSTD1FVH'; // Google Analytics 4 Measurement ID
        this.isInitialized = false;
        this.userProperties = {};
        this.customEvents = [];
        this.ecommerceData = {};
        this.performanceMetrics = {};
        
        this.init();
    }
    
    init() {
        console.log('📊 Advanced Analytics başlatılıyor...');
        
        // Google Analytics 4 script yükle
        this.loadGA4Script();
        
        // User properties topla
        this.collectUserProperties();
        
        // Performance metrics topla
        this.collectPerformanceMetrics();
        
        // Event listeners kur
        this.setupEventListeners();
        
        // E-commerce tracking
        this.setupEcommerceTracking();
        
        // Enhanced conversions
        this.setupEnhancedConversions();
        
        this.isInitialized = true;
        console.log('✅ Advanced Analytics aktif');
    }
    
    // Google Analytics 4 script yükle
    loadGA4Script() {
        if (typeof gtag === 'undefined') {
            const script = document.createElement('script');
            script.async = true;
            script.src = `https://www.googletagmanager.com/gtag/js?id=${this.ga4Id}`;
            document.head.appendChild(script);
            
            script.onload = () => {
                this.initializeGA4();
            };
        } else {
            this.initializeGA4();
        }
    }
    
    // GA4 initialize
    initializeGA4() {
        window.dataLayer = window.dataLayer || [];
        
        function gtag() {
            dataLayer.push(arguments);
        }
        
        gtag('js', new Date());
        gtag('config', this.ga4Id, {
            page_title: document.title,
            page_location: window.location.href,
            send_page_view: true,
            custom_map: {
                'custom_parameter_1': 'user_type',
                'custom_parameter_2': 'subscription_level'
            }
        });
        
        window.gtag = gtag;
        
        // User properties gönder
        this.sendUserProperties();
    }
    
    // User properties topla
    collectUserProperties() {
        this.userProperties = {
            user_id: this.getUserId(),
            user_type: this.getUserType(),
            subscription_level: this.getSubscriptionLevel(),
            device_category: this.getDeviceCategory(),
            browser: this.getBrowser(),
            os: this.getOS(),
            screen_resolution: this.getScreenResolution(),
            connection_speed: this.getConnectionSpeed(),
            language: navigator.language,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            referrer: document.referrer,
            utm_source: this.getUTMParameter('utm_source'),
            utm_medium: this.getUTMParameter('utm_medium'),
            utm_campaign: this.getUTMParameter('utm_campaign'),
            utm_term: this.getUTMParameter('utm_term'),
            utm_content: this.getUTMParameter('utm_content')
        };
    }
    
    // User ID al
    getUserId() {
        return localStorage.getItem('nextcode_user_id') || 
               sessionStorage.getItem('nextcode_user_id') || 
               this.generateUserId();
    }
    
    // User ID oluştur
    generateUserId() {
        const userId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('nextcode_user_id', userId);
        return userId;
    }
    
    // User type belirle
    getUserType() {
        if (localStorage.getItem('nextcode_admin')) return 'admin';
        if (localStorage.getItem('nextcode_premium')) return 'premium';
        if (localStorage.getItem('nextcode_registered')) return 'registered';
        return 'guest';
    }
    
    // Subscription level belirle
    getSubscriptionLevel() {
        if (localStorage.getItem('nextcode_enterprise')) return 'enterprise';
        if (localStorage.getItem('nextcode_professional')) return 'professional';
        if (localStorage.getItem('nextcode_basic')) return 'basic';
        return 'free';
    }
    
    // Device category belirle
    getDeviceCategory() {
        const userAgent = navigator.userAgent;
        if (/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(userAgent)) {
            return 'mobile';
        } else if (/iPad|Android/i.test(userAgent)) {
            return 'tablet';
        }
        return 'desktop';
    }
    
    // Browser bilgisi al
    getBrowser() {
        const userAgent = navigator.userAgent;
        if (userAgent.includes('Chrome')) return 'Chrome';
        if (userAgent.includes('Firefox')) return 'Firefox';
        if (userAgent.includes('Safari')) return 'Safari';
        if (userAgent.includes('Edge')) return 'Edge';
        if (userAgent.includes('Opera')) return 'Opera';
        return 'Other';
    }
    
    // OS bilgisi al
    getOS() {
        const userAgent = navigator.userAgent;
        if (userAgent.includes('Windows')) return 'Windows';
        if (userAgent.includes('Mac')) return 'macOS';
        if (userAgent.includes('Linux')) return 'Linux';
        if (userAgent.includes('Android')) return 'Android';
        if (userAgent.includes('iOS')) return 'iOS';
        return 'Other';
    }
    
    // Screen resolution al
    getScreenResolution() {
        return `${screen.width}x${screen.height}`;
    }
    
    // Connection speed al
    getConnectionSpeed() {
        if (navigator.connection) {
            return navigator.connection.effectiveType || 'unknown';
        }
        return 'unknown';
    }
    
    // UTM parameter al
    getUTMParameter(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param) || '';
    }
    
    // User properties gönder
    sendUserProperties() {
        if (window.gtag) {
            Object.entries(this.userProperties).forEach(([key, value]) => {
                if (value) {
                    gtag('set', 'user_property', {
                        [key]: value
                    });
                }
            });
        }
    }
    
    // Performance metrics topla
    collectPerformanceMetrics() {
        if (window.performance) {
            const timing = performance.timing;
            const navigation = performance.navigation;
            
            this.performanceMetrics = {
                page_load_time: timing.loadEventEnd - timing.navigationStart,
                dom_content_loaded: timing.domContentLoadedEventEnd - timing.navigationStart,
                first_contentful_paint: this.getFCP(),
                largest_contentful_paint: this.getLCP(),
                first_input_delay: this.getFID(),
                cumulative_layout_shift: this.getCLS(),
                time_to_first_byte: timing.responseStart - timing.navigationStart,
                dns_lookup_time: timing.domainLookupEnd - timing.domainLookupStart,
                tcp_connection_time: timing.connectEnd - timing.connectStart,
                server_response_time: timing.responseEnd - timing.responseStart,
                dom_processing_time: timing.domComplete - timing.domLoading,
                resource_count: performance.getEntriesByType('resource').length,
                resource_size: this.getTotalResourceSize()
            };
        }
    }
    
    // FCP al
    getFCP() {
        const paintEntries = performance.getEntriesByType('paint');
        const fcpEntry = paintEntries.find(entry => entry.name === 'first-contentful-paint');
        return fcpEntry ? fcpEntry.startTime : null;
    }
    
    // LCP al
    getLCP() {
        if (window.coreWebVitals && window.coreWebVitals.vitals) {
            return window.coreWebVitals.vitals.lcp;
        }
        return null;
    }
    
    // FID al
    getFID() {
        if (window.coreWebVitals && window.coreWebVitals.vitals) {
            return window.coreWebVitals.vitals.fid;
        }
        return null;
    }
    
    // CLS al
    getCLS() {
        if (window.coreWebVitals && window.coreWebVitals.vitals) {
            return window.coreWebVitals.vitals.cls;
        }
        return null;
    }
    
    // Total resource size hesapla
    getTotalResourceSize() {
        const resources = performance.getEntriesByType('resource');
        return resources.reduce((total, resource) => total + (resource.transferSize || 0), 0);
    }
    
    // Event listeners kur
    setupEventListeners() {
        // Page view tracking
        this.trackPageView();
        
        // Click tracking
        document.addEventListener('click', (e) => {
            this.trackClick(e);
        });
        
        // Form submission tracking
        document.addEventListener('submit', (e) => {
            this.trackFormSubmission(e);
        });
        
        // Scroll tracking
        this.trackScroll();
        
        // Video tracking
        this.trackVideo();
        
        // Error tracking
        this.trackErrors();
    }
    
    // Page view track
    trackPageView() {
        if (window.gtag) {
            gtag('event', 'page_view', {
                page_title: document.title,
                page_location: window.location.href,
                page_referrer: document.referrer,
                custom_parameter_1: this.userProperties.user_type,
                custom_parameter_2: this.userProperties.subscription_level
            });
        }
    }
    
    // Click track
    trackClick(e) {
        const target = e.target;
        const tagName = target.tagName.toLowerCase();
        
        if (tagName === 'a' || tagName === 'button' || target.closest('a') || target.closest('button')) {
            const element = target.closest('a') || target.closest('button') || target;
            
            this.trackEvent('click', {
                element_type: tagName,
                element_text: element.textContent?.trim().substring(0, 100) || '',
                element_href: element.href || '',
                element_class: element.className || '',
                element_id: element.id || '',
                page_location: window.location.href
            });
        }
    }
    
    // Form submission track
    trackFormSubmission(e) {
        const form = e.target;
        const formData = new FormData(form);
        
        this.trackEvent('form_submit', {
            form_id: form.id || '',
            form_action: form.action || '',
            form_method: form.method || '',
            form_fields: Array.from(formData.keys()).join(','),
            page_location: window.location.href
        });
    }
    
    // Scroll track
    trackScroll() {
        let scrollTimeout;
        let lastScrollDepth = 0;
        
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            
            scrollTimeout = setTimeout(() => {
                const scrollDepth = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
                
                if (scrollDepth >= 25 && lastScrollDepth < 25) {
                    this.trackEvent('scroll', { scroll_depth: '25%' });
                } else if (scrollDepth >= 50 && lastScrollDepth < 50) {
                    this.trackEvent('scroll', { scroll_depth: '50%' });
                } else if (scrollDepth >= 75 && lastScrollDepth < 75) {
                    this.trackEvent('scroll', { scroll_depth: '75%' });
                } else if (scrollDepth >= 90 && lastScrollDepth < 90) {
                    this.trackEvent('scroll', { scroll_depth: '90%' });
                }
                
                lastScrollDepth = scrollDepth;
            }, 150);
        });
    }
    
    // Video track
    trackVideo() {
        const videos = document.querySelectorAll('video');
        videos.forEach(video => {
            video.addEventListener('play', () => {
                this.trackEvent('video_play', {
                    video_title: video.title || '',
                    video_src: video.src || '',
                    video_duration: video.duration || 0
                });
            });
            
            video.addEventListener('pause', () => {
                this.trackEvent('video_pause', {
                    video_title: video.title || '',
                    video_src: video.src || '',
                    video_current_time: video.currentTime || 0
                });
            });
            
            video.addEventListener('ended', () => {
                this.trackEvent('video_complete', {
                    video_title: video.title || '',
                    video_src: video.src || '',
                    video_duration: video.duration || 0
                });
            });
        });
    }
    
    // Error track
    trackErrors() {
        window.addEventListener('error', (e) => {
            this.trackEvent('error', {
                error_message: e.message,
                error_filename: e.filename,
                error_lineno: e.lineno,
                error_colno: e.colno,
                error_stack: e.error?.stack || ''
            });
        });
        
        window.addEventListener('unhandledrejection', (e) => {
            this.trackEvent('error', {
                error_type: 'unhandled_rejection',
                error_reason: e.reason?.toString() || '',
                error_stack: e.reason?.stack || ''
            });
        });
    }
    
    // E-commerce tracking kur
    setupEcommerceTracking() {
        // Product view
        this.trackProductView = (product) => {
            this.trackEvent('view_item', {
                currency: 'USD',
                value: product.price,
                items: [{
                    item_id: product.id,
                    item_name: product.name,
                    item_category: product.category,
                    price: product.price,
                    quantity: 1
                }]
            });
        };
        
        // Add to cart
        this.trackAddToCart = (product, quantity = 1) => {
            this.trackEvent('add_to_cart', {
                currency: 'USD',
                value: product.price * quantity,
                items: [{
                    item_id: product.id,
                    item_name: product.name,
                    item_category: product.category,
                    price: product.price,
                    quantity: quantity
                }]
            });
        };
        
        // Purchase
        this.trackPurchase = (transaction) => {
            this.trackEvent('purchase', {
                transaction_id: transaction.id,
                value: transaction.total,
                tax: transaction.tax,
                shipping: transaction.shipping,
                currency: transaction.currency || 'USD',
                items: transaction.items
            });
        };
    }
    
    // Enhanced conversions kur
    setupEnhancedConversions() {
        // User engagement tracking
        this.trackUserEngagement = () => {
            const engagementTime = Math.round(performance.now() / 1000);
            
            this.trackEvent('user_engagement', {
                engagement_time_msec: engagementTime * 1000,
                session_id: this.getSessionId()
            });
        };
        
        // Custom event tracking
        this.trackCustomEvent = (eventName, parameters = {}) => {
            this.trackEvent(eventName, {
                ...parameters,
                custom_event: true,
                timestamp: new Date().toISOString()
            });
        };
    }
    
    // Session ID al
    getSessionId() {
        return sessionStorage.getItem('nextcode_session_id') || this.generateSessionId();
    }
    
    // Session ID oluştur
    generateSessionId() {
        const sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        sessionStorage.setItem('nextcode_session_id', sessionId);
        return sessionId;
    }
    
    // Event track
    trackEvent(eventName, parameters = {}) {
        if (window.gtag) {
            gtag('event', eventName, {
                ...parameters,
                user_id: this.userProperties.user_id,
                user_type: this.userProperties.user_type,
                device_category: this.userProperties.device_category,
                page_location: window.location.href,
                timestamp: new Date().toISOString()
            });
            
            // Custom events array'e ekle
            this.customEvents.push({
                name: eventName,
                parameters: parameters,
                timestamp: new Date().toISOString()
            });
        }
    }
    
    // Performance metrics gönder
    sendPerformanceMetrics() {
        if (window.gtag && this.performanceMetrics) {
            gtag('event', 'performance_metrics', {
                ...this.performanceMetrics,
                user_id: this.userProperties.user_id,
                device_category: this.userProperties.device_category
            });
        }
    }
    
    // Analytics raporu al
    getAnalyticsReport() {
        return {
            userProperties: this.userProperties,
            performanceMetrics: this.performanceMetrics,
            customEvents: this.customEvents,
            ecommerceData: this.ecommerceData,
            timestamp: new Date().toISOString()
        };
    }
    
    // Cleanup
    destroy() {
        // Performance metrics gönder
        this.sendPerformanceMetrics();
        
        // User engagement track
        this.trackUserEngagement();
        
        console.log('🗑️ Advanced Analytics temizlendi');
    }
}

// Global instance
window.advancedAnalytics = new AdvancedAnalytics();
