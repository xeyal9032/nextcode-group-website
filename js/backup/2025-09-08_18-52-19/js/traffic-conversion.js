// Traffic Sources and Conversion Tracking System
class TrafficConversionAnalytics {
    constructor() {
        this.conversionGoals = {
            'contact_form': { name: 'Əlaqə Formu', value: 50 },
            'newsletter_signup': { name: 'Newsletter Abunəliyi', value: 10 },
            'service_inquiry': { name: 'Xidmət Sorğusu', value: 100 },
            'download': { name: 'Fayl Yükləməsi', value: 5 },
            'phone_call': { name: 'Telefon Zəngi', value: 75 },
            'quote_request': { name: 'Qiymət Sorğusu', value: 150 }
        };
        
        this.trafficSources = {
            'organic': 'Organik Axtarış',
            'direct': 'Birbaşa Trafik',
            'social': 'Sosial Media',
            'referral': 'Referral',
            'email': 'Email Marketing',
            'paid': 'Ödənişli Reklam',
            'other': 'Digər'
        };
        
        this.init();
    }

    init() {
        this.detectTrafficSource();
        this.setupConversionTracking();
        this.trackPageViews();
        this.setupFormTracking();
        this.setupClickTracking();
        this.setupScrollTracking();
        this.setupTimeTracking();
    }

    // Detect and store traffic source
    detectTrafficSource() {
        const urlParams = new URLSearchParams(window.location.search);
        const referrer = document.referrer;
        const currentUrl = window.location.href;
        
        let source = 'direct';
        let medium = 'none';
        let campaign = '';
        let content = '';
        let term = '';

        // Check UTM parameters first
        if (urlParams.get('utm_source')) {
            source = urlParams.get('utm_source');
            medium = urlParams.get('utm_medium') || 'unknown';
            campaign = urlParams.get('utm_campaign') || '';
            content = urlParams.get('utm_content') || '';
            term = urlParams.get('utm_term') || '';
        }
        // Check for Google Ads parameters
        else if (urlParams.get('gclid')) {
            source = 'google';
            medium = 'cpc';
            campaign = 'google_ads';
        }
        // Check for Facebook parameters
        else if (urlParams.get('fbclid')) {
            source = 'facebook';
            medium = 'social';
            campaign = 'facebook_ads';
        }
        // Analyze referrer
        else if (referrer) {
            const referrerDomain = new URL(referrer).hostname;
            
            if (referrerDomain.includes('google.')) {
                source = 'google';
                medium = 'organic';
            } else if (referrerDomain.includes('bing.') || referrerDomain.includes('yahoo.')) {
                source = referrerDomain.split('.')[0];
                medium = 'organic';
            } else if (referrerDomain.includes('facebook.') || referrerDomain.includes('fb.')) {
                source = 'facebook';
                medium = 'social';
            } else if (referrerDomain.includes('instagram.')) {
                source = 'instagram';
                medium = 'social';
            } else if (referrerDomain.includes('linkedin.')) {
                source = 'linkedin';
                medium = 'social';
            } else if (referrerDomain.includes('twitter.') || referrerDomain.includes('t.co')) {
                source = 'twitter';
                medium = 'social';
            } else if (referrerDomain.includes('youtube.')) {
                source = 'youtube';
                medium = 'social';
            } else {
                source = referrerDomain;
                medium = 'referral';
            }
        }

        // Store traffic source data
        const trafficData = {
            source,
            medium,
            campaign,
            content,
            term,
            referrer,
            landing_page: window.location.pathname,
            timestamp: new Date().toISOString(),
            session_id: this.getSessionId()
        };

        this.storeTrafficData(trafficData);
        this.sendTrafficData(trafficData);
    }

    // Generate or get session ID
    getSessionId() {
        let sessionId = sessionStorage.getItem('analytics_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('analytics_session_id', sessionId);
        }
        return sessionId;
    }

    // Store traffic data locally
    storeTrafficData(data) {
        try {
            const existingData = JSON.parse(localStorage.getItem('traffic_sources') || '[]');
            existingData.push(data);
            
            // Keep only last 100 entries
            if (existingData.length > 100) {
                existingData.splice(0, existingData.length - 100);
            }
            
            localStorage.setItem('traffic_sources', JSON.stringify(existingData));
        } catch (error) {
            console.error('Error storing traffic data:', error);
        }
    }

    // Send traffic data to analytics
    sendTrafficData(data) {
        // Send to Google Analytics if available
        if (typeof gtag !== 'undefined') {
            gtag('event', 'traffic_source', {
                source: data.source,
                medium: data.medium,
                campaign: data.campaign,
                content: data.content,
                term: data.term
            });
        }

        // Send to custom analytics endpoint
        this.sendToAnalytics('traffic_source', data);
    }

    // Setup conversion tracking
    setupConversionTracking() {
        // Track form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.tagName === 'FORM') {
                this.trackConversion(this.getFormConversionType(form), {
                    form_id: form.id || 'unknown',
                    form_action: form.action || window.location.href,
                    form_method: form.method || 'GET'
                });
            }
        });

        // Track button clicks for conversions
        document.addEventListener('click', (e) => {
            const element = e.target.closest('button, a');
            if (element) {
                const conversionType = this.getClickConversionType(element);
                if (conversionType) {
                    this.trackConversion(conversionType, {
                        element_text: element.textContent.trim(),
                        element_class: element.className,
                        element_id: element.id || 'unknown'
                    });
                }
            }
        });
    }

    // Determine form conversion type
    getFormConversionType(form) {
        const formId = form.id.toLowerCase();
        const formClass = form.className.toLowerCase();
        const action = form.action.toLowerCase();

        if (formId.includes('contact') || formClass.includes('contact') || action.includes('contact')) {
            return 'contact_form';
        } else if (formId.includes('newsletter') || formClass.includes('newsletter')) {
            return 'newsletter_signup';
        } else if (formId.includes('quote') || formClass.includes('quote')) {
            return 'quote_request';
        } else if (formId.includes('service') || formClass.includes('service')) {
            return 'service_inquiry';
        }
        
        return 'contact_form'; // Default
    }

    // Determine click conversion type
    getClickConversionType(element) {
        const text = element.textContent.toLowerCase();
        const className = element.className.toLowerCase();
        const href = element.href ? element.href.toLowerCase() : '';

        if (text.includes('yüklə') || text.includes('download') || href.includes('download')) {
            return 'download';
        } else if (text.includes('zəng') || text.includes('telefon') || href.includes('tel:')) {
            return 'phone_call';
        } else if (text.includes('qiymət') || text.includes('quote')) {
            return 'quote_request';
        } else if (className.includes('cta') && (text.includes('əlaqə') || text.includes('contact'))) {
            return 'contact_form';
        }
        
        return null;
    }

    // Track conversion event
    trackConversion(type, additionalData = {}) {
        if (!this.conversionGoals[type]) {
            console.warn('Unknown conversion type:', type);
            return;
        }

        const conversionData = {
            type,
            name: this.conversionGoals[type].name,
            value: this.conversionGoals[type].value,
            page: window.location.pathname,
            timestamp: new Date().toISOString(),
            session_id: this.getSessionId(),
            traffic_source: this.getCurrentTrafficSource(),
            ...additionalData
        };

        this.storeConversionData(conversionData);
        this.sendConversionData(conversionData);
    }

    // Get current traffic source from session
    getCurrentTrafficSource() {
        try {
            const trafficData = JSON.parse(localStorage.getItem('traffic_sources') || '[]');
            const sessionId = this.getSessionId();
            const currentSession = trafficData.find(data => data.session_id === sessionId);
            return currentSession || { source: 'unknown', medium: 'unknown' };
        } catch (error) {
            return { source: 'unknown', medium: 'unknown' };
        }
    }

    // Store conversion data
    storeConversionData(data) {
        try {
            const existingData = JSON.parse(localStorage.getItem('conversions') || '[]');
            existingData.push(data);
            
            // Keep only last 50 conversions
            if (existingData.length > 50) {
                existingData.splice(0, existingData.length - 50);
            }
            
            localStorage.setItem('conversions', JSON.stringify(existingData));
        } catch (error) {
            console.error('Error storing conversion data:', error);
        }
    }

    // Send conversion data to analytics
    sendConversionData(data) {
        // Send to Google Analytics if available
        if (typeof gtag !== 'undefined') {
            gtag('event', 'conversion', {
                event_category: 'Conversion',
                event_label: data.name,
                value: data.value,
                currency: 'AZN'
            });
        }

        // Send to custom analytics endpoint
        this.sendToAnalytics('conversion', data);
    }

    // Track page views with source attribution
    trackPageViews() {
        const pageData = {
            page: window.location.pathname,
            title: document.title,
            timestamp: new Date().toISOString(),
            session_id: this.getSessionId(),
            traffic_source: this.getCurrentTrafficSource(),
            referrer: document.referrer,
            user_agent: navigator.userAgent
        };

        this.sendToAnalytics('page_view', pageData);
    }

    // Setup form tracking
    setupFormTracking() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            // Track form starts
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    this.trackFormStart(form);
                }, { once: true });
            });

            // Track form abandonment
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.trackFormProgress(form, input);
                });
            });
        });
    }

    // Track form start
    trackFormStart(form) {
        const formData = {
            form_id: form.id || 'unknown',
            form_name: form.name || 'unknown',
            page: window.location.pathname,
            timestamp: new Date().toISOString(),
            session_id: this.getSessionId(),
            traffic_source: this.getCurrentTrafficSource()
        };

        this.sendToAnalytics('form_start', formData);
    }

    // Track form progress
    trackFormProgress(form, field) {
        const formData = {
            form_id: form.id || 'unknown',
            field_name: field.name || field.id || 'unknown',
            field_type: field.type,
            field_filled: field.value.length > 0,
            page: window.location.pathname,
            timestamp: new Date().toISOString(),
            session_id: this.getSessionId()
        };

        this.sendToAnalytics('form_progress', formData);
    }

    // Setup click tracking
    setupClickTracking() {
        document.addEventListener('click', (e) => {
            const element = e.target.closest('a, button');
            if (element) {
                const clickData = {
                    element_type: element.tagName.toLowerCase(),
                    element_text: element.textContent.trim().substring(0, 100),
                    element_class: element.className,
                    element_id: element.id || 'unknown',
                    href: element.href || '',
                    page: window.location.pathname,
                    timestamp: new Date().toISOString(),
                    session_id: this.getSessionId(),
                    traffic_source: this.getCurrentTrafficSource()
                };

                this.sendToAnalytics('click', clickData);
            }
        });
    }

    // Setup scroll tracking
    setupScrollTracking() {
        let maxScroll = 0;
        let scrollMilestones = [25, 50, 75, 90, 100];
        let trackedMilestones = new Set();

        window.addEventListener('scroll', () => {
            const scrollPercent = Math.round(
                (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100
            );

            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;
            }

            scrollMilestones.forEach(milestone => {
                if (scrollPercent >= milestone && !trackedMilestones.has(milestone)) {
                    trackedMilestones.add(milestone);
                    
                    const scrollData = {
                        scroll_depth: milestone,
                        page: window.location.pathname,
                        timestamp: new Date().toISOString(),
                        session_id: this.getSessionId(),
                        traffic_source: this.getCurrentTrafficSource()
                    };

                    this.sendToAnalytics('scroll', scrollData);
                }
            });
        });

        // Track final scroll depth on page unload
        window.addEventListener('beforeunload', () => {
            if (maxScroll > 0) {
                const scrollData = {
                    final_scroll_depth: maxScroll,
                    page: window.location.pathname,
                    timestamp: new Date().toISOString(),
                    session_id: this.getSessionId()
                };

                this.sendToAnalytics('final_scroll', scrollData);
            }
        });
    }

    // Setup time tracking
    setupTimeTracking() {
        const startTime = Date.now();
        let isActive = true;
        let totalActiveTime = 0;
        let lastActiveTime = startTime;

        // Track when user becomes inactive
        const trackInactivity = () => {
            if (isActive) {
                totalActiveTime += Date.now() - lastActiveTime;
                isActive = false;
            }
        };

        // Track when user becomes active
        const trackActivity = () => {
            if (!isActive) {
                lastActiveTime = Date.now();
                isActive = true;
            }
        };

        // Listen for activity events
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, trackActivity, { passive: true });
        });

        // Listen for inactivity events
        ['blur', 'visibilitychange'].forEach(event => {
            window.addEventListener(event, () => {
                if (document.hidden || !document.hasFocus()) {
                    trackInactivity();
                }
            });
        });

        // Send time data on page unload
        window.addEventListener('beforeunload', () => {
            if (isActive) {
                totalActiveTime += Date.now() - lastActiveTime;
            }

            const timeData = {
                total_time: Date.now() - startTime,
                active_time: totalActiveTime,
                page: window.location.pathname,
                timestamp: new Date().toISOString(),
                session_id: this.getSessionId(),
                traffic_source: this.getCurrentTrafficSource()
            };

            this.sendToAnalytics('time_on_page', timeData);
        });
    }

    // Send data to analytics endpoint
    sendToAnalytics(eventType, data) {
        // Check if analytics is enabled via cookie consent
        if (!this.isAnalyticsEnabled()) {
            return;
        }

        try {
            // Send to custom analytics endpoint
            fetch('/api/analytics', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    event_type: eventType,
                    data: data,
                    timestamp: new Date().toISOString(),
                    user_agent: navigator.userAgent,
                    screen_resolution: `${screen.width}x${screen.height}`,
                    viewport_size: `${window.innerWidth}x${window.innerHeight}`,
                    language: navigator.language,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
                })
            }).catch(error => {
                console.error('Analytics request failed:', error);
            });
        } catch (error) {
            console.error('Error sending analytics data:', error);
        }
    }

    // Check if analytics is enabled
    isAnalyticsEnabled() {
        // Check cookie consent
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

    // Get analytics summary
    getAnalyticsSummary() {
        try {
            const trafficSources = JSON.parse(localStorage.getItem('traffic_sources') || '[]');
            const conversions = JSON.parse(localStorage.getItem('conversions') || '[]');
            
            return {
                traffic_sources: trafficSources,
                conversions: conversions,
                total_sessions: new Set(trafficSources.map(t => t.session_id)).size,
                total_conversions: conversions.length,
                conversion_rate: trafficSources.length > 0 ? (conversions.length / trafficSources.length * 100).toFixed(2) : 0
            };
        } catch (error) {
            console.error('Error getting analytics summary:', error);
            return null;
        }
    }

    // Clear analytics data
    clearAnalyticsData() {
        localStorage.removeItem('traffic_sources');
        localStorage.removeItem('conversions');
        sessionStorage.removeItem('analytics_session_id');
    }
}

// Initialize traffic and conversion tracking
document.addEventListener('DOMContentLoaded', () => {
    window.trafficConversionAnalytics = new TrafficConversionAnalytics();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TrafficConversionAnalytics;
}