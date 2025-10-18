// Cookie Consent System
class CookieConsent {
    constructor() {
        this.cookieName = 'cookie_consent';
        this.cookieExpiry = 365; // days
        this.init();
    }

    init() {
        // Check if consent already given
        if (!this.getCookie(this.cookieName)) {
            this.showConsentBanner();
        } else {
            // Load analytics if consent given
            const consent = JSON.parse(this.getCookie(this.cookieName));
            if (consent.analytics) {
                this.loadAnalytics();
            }
        }
        
        this.bindEvents();
    }

    showConsentBanner() {
        const banner = document.createElement('div');
        banner.className = 'cookie-consent';
        banner.innerHTML = `
            <div class="cookie-consent-content">
                <div class="cookie-consent-text">
                    <h4><i class="fas fa-cookie-bite"></i> Çerez Bildirimi</h4>
                    <p>Web sitemizde deneyiminizi geliştirmek için çerezler kullanıyoruz. Sitemizi kullanmaya devam ederek çerez kullanımımızı kabul etmiş olursunuz. 
                    <a href="#" onclick="cookieConsent.showSettings()">Çerez Ayarları</a></p>
                </div>
                <div class="cookie-consent-actions">
                    <button class="cookie-btn cookie-btn-settings" onclick="cookieConsent.showSettings()">
                        <i class="fas fa-cog"></i> Ayarlar
                    </button>
                    <button class="cookie-btn cookie-btn-decline" onclick="cookieConsent.declineAll()">
                        <i class="fas fa-times"></i> Reddet
                    </button>
                    <button class="cookie-btn cookie-btn-accept" onclick="cookieConsent.acceptAll()">
                        <i class="fas fa-check"></i> Kabul Et
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(banner);
        
        // Show banner with animation
        setTimeout(() => {
            banner.classList.add('show');
        }, 100);
    }

    showSettings() {
        const modal = document.createElement('div');
        modal.className = 'cookie-settings-modal';
        modal.innerHTML = `
            <div class="cookie-settings-content">
                <div class="cookie-settings-header">
                    <h3><i class="fas fa-cookie-bite"></i> Çerez Ayarları</h3>
                    <button class="cookie-settings-close" onclick="cookieConsent.closeSettings()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="cookie-category">
                    <div class="cookie-category-header">
                        <h4><i class="fas fa-shield-alt"></i> Zorunlu Çerezler</h4>
                        <label class="cookie-toggle">
                            <input type="checkbox" checked disabled>
                            <span class="cookie-toggle-slider"></span>
                        </label>
                    </div>
                    <p>Bu çerezler web sitemizin temel işlevlerini sağlamak için gereklidir ve devre dışı bırakılamaz.</p>
                </div>
                
                <div class="cookie-category">
                    <div class="cookie-category-header">
                        <h4><i class="fas fa-chart-line"></i> Analitik Çerezler</h4>
                        <label class="cookie-toggle">
                            <input type="checkbox" id="analytics-toggle">
                            <span class="cookie-toggle-slider"></span>
                        </label>
                    </div>
                    <p>Bu çerezler web sitemizin nasıl kullanıldığını anlamamıza yardımcı olur ve deneyiminizi geliştirmemizi sağlar.</p>
                </div>
                
                <div class="cookie-category">
                    <div class="cookie-category-header">
                        <h4><i class="fas fa-bullseye"></i> Pazarlama Çerezler</h4>
                        <label class="cookie-toggle">
                            <input type="checkbox" id="marketing-toggle">
                            <span class="cookie-toggle-slider"></span>
                        </label>
                    </div>
                    <p>Bu çerezler size daha alakalı reklamlar göstermek için kullanılır.</p>
                </div>
                
                <div class="cookie-settings-actions">
                    <button class="cookie-btn cookie-btn-decline" onclick="cookieConsent.saveSettings()">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                    <button class="cookie-btn cookie-btn-accept" onclick="cookieConsent.acceptAllFromSettings()">
                        <i class="fas fa-check"></i> Tümünü Kabul Et
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        setTimeout(() => {
            modal.classList.add('show');
        }, 100);
    }

    closeSettings() {
        const modal = document.querySelector('.cookie-settings-modal');
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.remove();
            }, 300);
        }
    }

    acceptAll() {
        const consent = {
            necessary: true,
            analytics: true,
            marketing: true,
            timestamp: new Date().toISOString()
        };
        
        this.setCookie(this.cookieName, JSON.stringify(consent), this.cookieExpiry);
        this.hideBanner();
        this.loadAnalytics();
        this.trackEvent('cookie_consent', 'accept_all');
    }

    declineAll() {
        const consent = {
            necessary: true,
            analytics: false,
            marketing: false,
            timestamp: new Date().toISOString()
        };
        
        this.setCookie(this.cookieName, JSON.stringify(consent), this.cookieExpiry);
        this.hideBanner();
        this.trackEvent('cookie_consent', 'decline_all');
    }

    saveSettings() {
        const analyticsToggle = document.getElementById('analytics-toggle');
        const marketingToggle = document.getElementById('marketing-toggle');
        
        const consent = {
            necessary: true,
            analytics: analyticsToggle ? analyticsToggle.checked : false,
            marketing: marketingToggle ? marketingToggle.checked : false,
            timestamp: new Date().toISOString()
        };
        
        this.setCookie(this.cookieName, JSON.stringify(consent), this.cookieExpiry);
        this.closeSettings();
        this.hideBanner();
        
        if (consent.analytics) {
            this.loadAnalytics();
        }
        
        this.trackEvent('cookie_consent', 'custom_settings');
    }

    acceptAllFromSettings() {
        const analyticsToggle = document.getElementById('analytics-toggle');
        const marketingToggle = document.getElementById('marketing-toggle');
        
        if (analyticsToggle) analyticsToggle.checked = true;
        if (marketingToggle) marketingToggle.checked = true;
        
        this.saveSettings();
    }

    hideBanner() {
        const banner = document.querySelector('.cookie-consent');
        if (banner) {
            banner.classList.remove('show');
            setTimeout(() => {
                banner.remove();
            }, 300);
        }
    }

    loadAnalytics() {
        // Google Analytics disabled for now
        // Uncomment and replace YOUR_GA_MEASUREMENT_ID when implementing
        /*
        if (typeof gtag === 'undefined') {
            const script = document.createElement('script');
            script.async = true;
            script.src = 'https://www.googletagmanager.com/gtag/js?id=YOUR_GA_MEASUREMENT_ID';
            document.head.appendChild(script);
            
            script.onload = () => {
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', 'YOUR_GA_MEASUREMENT_ID');
                window.gtag = gtag;
            };
        }
        */
    }

    trackEvent(action, category, label = null, value = null) {
        // Track events if analytics consent given
        const consent = this.getCookie(this.cookieName);
        if (consent) {
            const consentData = JSON.parse(consent);
            if (consentData.analytics && typeof gtag !== 'undefined') {
                const eventData = {
                    event_category: category,
                    event_label: label,
                    value: value
                };
                gtag('event', action, eventData);
            }
        }
    }

    bindEvents() {
        // Track page views
        this.trackEvent('page_view', 'engagement');
        
        // Track form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.tagName === 'FORM') {
                this.trackEvent('form_submit', 'engagement', e.target.id || 'unknown_form');
            }
        });
        
        // Track button clicks
        document.addEventListener('click', (e) => {
            if (e.target.tagName === 'BUTTON' || e.target.classList.contains('btn')) {
                this.trackEvent('button_click', 'engagement', e.target.textContent.trim());
            }
        });
        
        // Track external links
        document.addEventListener('click', (e) => {
            if (e.target.tagName === 'A' && e.target.hostname !== window.location.hostname) {
                this.trackEvent('external_link', 'engagement', e.target.href);
            }
        });
    }

    // Utility functions
    setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
    }

    getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Public methods for external use
    getConsent() {
        const consent = this.getCookie(this.cookieName);
        return consent ? JSON.parse(consent) : null;
    }

    hasAnalyticsConsent() {
        const consent = this.getConsent();
        return consent && consent.analytics;
    }

    hasMarketingConsent() {
        const consent = this.getConsent();
        return consent && consent.marketing;
    }

    resetConsent() {
        document.cookie = `${this.cookieName}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
        location.reload();
    }
}

// Initialize cookie consent system
let cookieConsent;
document.addEventListener('DOMContentLoaded', () => {
    cookieConsent = new CookieConsent();
});

// Export for external use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CookieConsent;
}