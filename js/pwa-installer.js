/**
 * PWA Installer
 * Handles Progressive Web App installation
 */

class PWAInstaller {
    constructor() {
        this.deferredPrompt = null;
        this.installButton = null;
        this.initialized = false;
        
        this.init();
    }
    
    init() {
        if (this.initialized) return;
        
        // Register service worker
        this.registerServiceWorker();
        
        // Setup install prompt
        this.setupInstallPrompt();
        
        // Check if already installed
        this.checkInstallStatus();
        
        // Monitor connection status
        this.monitorConnectionStatus();
        
        this.initialized = true;
    }
    
    /**
     * Register service worker
     */
    async registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            console.log('Service Worker not supported');
            return;
        }
        
        try {
            const registration = await navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            });
            
            console.log('Service Worker registered:', registration.scope);
            
            // Check for updates
            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;
                
                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        // New service worker available
                        this.showUpdateNotification();
                    }
                });
            });
            
            // Auto-update service worker
            setInterval(() => {
                registration.update();
            }, 60 * 60 * 1000); // Check every hour
            
        } catch (error) {
            console.error('Service Worker registration failed:', error);
        }
    }
    
    /**
     * Setup install prompt
     */
    setupInstallPrompt() {
        // Listen for beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing
            e.preventDefault();
            
            // Save the event
            this.deferredPrompt = e;
            
            // Show install button
            this.showInstallButton();
        });
        
        // Listen for app installed event
        window.addEventListener('appinstalled', () => {
            console.log('PWA installed');
            this.hideInstallButton();
            this.showInstallSuccessMessage();
            
            // Track installation
            this.trackInstallation();
        });
    }
    
    /**
     * Show install button
     */
    showInstallButton() {
        // Check if button already exists
        if (document.getElementById('pwa-install-btn')) return;
        
        // Create install button
        const button = document.createElement('button');
        button.id = 'pwa-install-btn';
        button.className = 'pwa-install-button';
        button.innerHTML = `
            <i class="fas fa-download"></i>
            <span>Uygulamayı Yükle</span>
        `;
        
        // Add click handler
        button.addEventListener('click', () => this.promptInstall());
        
        // Add to page
        document.body.appendChild(button);
        this.installButton = button;
        
        // Add styles
        this.injectStyles();
    }
    
    /**
     * Hide install button
     */
    hideInstallButton() {
        if (this.installButton) {
            this.installButton.remove();
            this.installButton = null;
        }
    }
    
    /**
     * Prompt user to install
     */
    async promptInstall() {
        if (!this.deferredPrompt) return;
        
        // Show the install prompt
        this.deferredPrompt.prompt();
        
        // Wait for user response
        const { outcome } = await this.deferredPrompt.userChoice;
        
        console.log(`User response: ${outcome}`);
        
        // Clear the deferred prompt
        this.deferredPrompt = null;
        
        if (outcome === 'accepted') {
            this.hideInstallButton();
        }
    }
    
    /**
     * Check if app is already installed
     */
    checkInstallStatus() {
        // Check if running as installed PWA
        if (window.matchMedia('(display-mode: standalone)').matches || 
            window.navigator.standalone === true) {
            console.log('PWA is installed');
            this.hideInstallButton();
            return true;
        }
        
        return false;
    }
    
    /**
     * Monitor online/offline status
     */
    monitorConnectionStatus() {
        const showConnectionStatus = (online) => {
            const statusEl = document.getElementById('connection-status') || this.createConnectionStatusEl();
            
            statusEl.className = `connection-status ${online ? 'online' : 'offline'}`;
            statusEl.innerHTML = online 
                ? '<i class="fas fa-wifi"></i> Online' 
                : '<i class="fas fa-wifi-slash"></i> Offline';
            
            if (!online) {
                statusEl.classList.add('show');
            } else {
                setTimeout(() => {
                    statusEl.classList.remove('show');
                }, 3000);
            }
        };
        
        // Initial status
        showConnectionStatus(navigator.onLine);
        
        // Listen for status changes
        window.addEventListener('online', () => showConnectionStatus(true));
        window.addEventListener('offline', () => showConnectionStatus(false));
    }
    
    /**
     * Create connection status element
     */
    createConnectionStatusEl() {
        const el = document.createElement('div');
        el.id = 'connection-status';
        el.className = 'connection-status';
        document.body.appendChild(el);
        return el;
    }
    
    /**
     * Show update notification
     */
    showUpdateNotification() {
        const notification = document.createElement('div');
        notification.className = 'pwa-update-notification';
        notification.innerHTML = `
            <div class="pwa-update-content">
                <i class="fas fa-sync-alt"></i>
                <span>Yeni versiyon mevcut!</span>
                <button onclick="window.location.reload()">Güncelle</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
    }
    
    /**
     * Show install success message
     */
    showInstallSuccessMessage() {
        const message = document.createElement('div');
        message.className = 'pwa-success-message';
        message.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>Uygulama başarıyla yüklendi!</span>
        `;
        
        document.body.appendChild(message);
        
        setTimeout(() => {
            message.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            message.classList.remove('show');
            setTimeout(() => message.remove(), 300);
        }, 3000);
    }
    
    /**
     * Track installation
     */
    trackInstallation() {
        // Send analytics event
        if (typeof gtag !== 'undefined') {
            gtag('event', 'pwa_install', {
                event_category: 'PWA',
                event_label: 'App Installed'
            });
        }
        
        // Log to console
        console.log('PWA installation tracked');
    }
    
    /**
     * Inject CSS styles
     */
    injectStyles() {
        if (document.getElementById('pwa-styles')) return;
        
        const style = document.createElement('style');
        style.id = 'pwa-styles';
        style.textContent = `
            .pwa-install-button {
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                border: none;
                padding: 1rem 1.5rem;
                border-radius: 50px;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                transition: all 0.3s;
                z-index: 9999;
                animation: slideInUp 0.5s ease;
            }
            
            .pwa-install-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
            }
            
            .connection-status {
                position: fixed;
                top: -50px;
                left: 50%;
                transform: translateX(-50%);
                background: white;
                padding: 0.75rem 1.5rem;
                border-radius: 50px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                display: flex;
                align-items: center;
                gap: 0.5rem;
                transition: top 0.3s;
                z-index: 10000;
            }
            
            .connection-status.show {
                top: 1rem;
            }
            
            .connection-status.online {
                color: #10b981;
            }
            
            .connection-status.offline {
                color: #ef4444;
            }
            
            .pwa-update-notification {
                position: fixed;
                top: -100px;
                left: 50%;
                transform: translateX(-50%);
                background: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 8px 30px rgba(0,0,0,0.15);
                z-index: 10001;
                transition: top 0.3s;
            }
            
            .pwa-update-notification.show {
                top: 1rem;
            }
            
            .pwa-update-content {
                display: flex;
                align-items: center;
                gap: 1rem;
            }
            
            .pwa-update-content button {
                background: #667eea;
                color: white;
                border: none;
                padding: 0.5rem 1rem;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 600;
            }
            
            .pwa-success-message {
                position: fixed;
                bottom: -100px;
                left: 50%;
                transform: translateX(-50%);
                background: #10b981;
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                z-index: 10002;
                transition: bottom 0.3s;
            }
            
            .pwa-success-message.show {
                bottom: 2rem;
            }
            
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(50px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @media (max-width: 768px) {
                .pwa-install-button {
                    bottom: 1rem;
                    right: 1rem;
                    padding: 0.75rem 1.25rem;
                    font-size: 0.9rem;
                }
            }
        `;
        
        document.head.appendChild(style);
    }
}

// Initialize PWA Installer
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new PWAInstaller();
    });
} else {
    new PWAInstaller();
}

// Export for manual initialization
window.PWAInstaller = PWAInstaller;


