/**
 * Analytics ve İstatistik Takip Sistemi
 * Sayfa görüntülenmeleri, kullanıcı etkileşimleri ve performans metrikleri
 */

class Analytics {
    constructor() {
        this.sessionId = this.generateSessionId();
        this.startTime = Date.now();
        this.events = [];
        this.pageViews = 0;
        this.init();
    }

    init() {
        this.trackPageView();
        this.setupEventListeners();
        this.startSessionTracking();
    }

    generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    trackPageView() {
        this.pageViews++;
        const pageData = {
            url: window.location.href,
            title: document.title,
            timestamp: Date.now(),
            referrer: document.referrer,
            userAgent: navigator.userAgent,
            sessionId: this.sessionId
        };

        this.logEvent('page_view', pageData);
        this.sendToServer('page_view', pageData);
    }

    trackEvent(eventName, eventData = {}) {
        const event = {
            name: eventName,
            data: eventData,
            timestamp: Date.now(),
            sessionId: this.sessionId,
            url: window.location.href
        };

        this.logEvent(eventName, event);
        this.sendToServer('event', event);
    }

    setupEventListeners() {
        // Click tracking
        document.addEventListener('click', (e) => {
            const target = e.target;
            if (target.tagName === 'A') {
                this.trackEvent('link_click', {
                    href: target.href,
                    text: target.textContent.trim()
                });
            }
            if (target.tagName === 'BUTTON') {
                this.trackEvent('button_click', {
                    text: target.textContent.trim(),
                    id: target.id,
                    className: target.className
                });
            }
        });

        // Form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target;
            this.trackEvent('form_submit', {
                formId: form.id,
                action: form.action,
                method: form.method
            });
        });

        // Scroll tracking
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                const scrollPercent = Math.round(
                    (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100
                );
                this.trackEvent('scroll', { percent: scrollPercent });
            }, 250);
        });
    }

    startSessionTracking() {
        // Track session duration every 30 seconds
        setInterval(() => {
            const sessionDuration = Date.now() - this.startTime;
            this.trackEvent('session_ping', {
                duration: sessionDuration,
                pageViews: this.pageViews
            });
        }, 30000);

        // Track when user leaves
        window.addEventListener('beforeunload', () => {
            const sessionDuration = Date.now() - this.startTime;
            this.trackEvent('session_end', {
                duration: sessionDuration,
                pageViews: this.pageViews
            });
        });
    }

    logEvent(eventName, eventData) {
        this.events.push({ name: eventName, data: eventData });
        console.log(`📊 Analytics: ${eventName}`, eventData);
    }

    async sendToServer(type, data) {
        try {
            // Sunucuya veri gönderme (isteğe bağlı)
            if (window.location.hostname !== 'localhost') {
                await fetch('/api/analytics', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ type, data })
                });
            }
        } catch (error) {
            console.warn('Analytics veri gönderimi başarısız:', error);
        }
    }

    getSessionData() {
        return {
            sessionId: this.sessionId,
            startTime: this.startTime,
            duration: Date.now() - this.startTime,
            pageViews: this.pageViews,
            events: this.events
        };
    }
}

// Global analytics instance
window.analytics = new Analytics();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Analytics;
}