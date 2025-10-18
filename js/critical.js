// Critical JavaScript - Above the fold functionality
// NextCode Group - Critical JS

(function() {
    'use strict';
    
    // Critical performance monitoring
    const performanceMonitor = {
        init() {
            this.startTime = performance.now();
            this.observeNavigation();
            this.observePaint();
        },
        
        observeNavigation() {
            if ('PerformanceObserver' in window) {
                try {
                    const observer = new PerformanceObserver((list) => {
                        list.getEntries().forEach((entry) => {
                            if (entry.entryType === 'navigation') {
                                console.log('🚀 Navigation timing:', entry);
                            }
                        });
                    });
                    observer.observe({ entryTypes: ['navigation'] });
                } catch (e) {
                    console.warn('Navigation timing observer failed:', e);
                }
            }
        },
        
        observePaint() {
            if ('PerformanceObserver' in window) {
                try {
                    const observer = new PerformanceObserver((list) => {
                        list.getEntries().forEach((entry) => {
                            if (entry.entryType === 'paint') {
                                console.log(`🎨 ${entry.name}:`, entry.startTime.toFixed(2) + 'ms');
                            }
                        });
                    });
                    observer.observe({ entryTypes: ['paint'] });
                } catch (e) {
                    console.warn('Paint timing observer failed:', e);
                }
            }
        }
    };
    
    // Critical error handling
    const errorHandler = {
        init() {
            window.addEventListener('error', this.handleError.bind(this));
            window.addEventListener('unhandledrejection', this.handlePromiseRejection.bind(this));
        },
        
        handleError(event) {
            console.error('🚨 Global Error:', {
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                error: event.error
            });
            
            // Send to analytics if available
            if (window.gtag) {
                window.gtag('event', 'exception', {
                    description: event.message,
                    fatal: false
                });
            }
        },
        
        handlePromiseRejection(event) {
            console.error('🚨 Unhandled Promise Rejection:', event.reason);
            
            if (window.gtag) {
                window.gtag('event', 'exception', {
                    description: 'Unhandled Promise Rejection: ' + event.reason,
                    fatal: false
                });
            }
        }
    };
    
    // Critical theme initialization - Simplified
    const themeManager = {
        init() {
            this.loadTheme();
            // Don't setup toggle here - let theme.js handle it
        },
        
        loadTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Update icon immediately
            this.updateThemeIcon(savedTheme);
        },
        
        updateThemeIcon(theme) {
            const themeIcon = document.getElementById('themeIcon');
            if (themeIcon) {
                if (theme === 'dark') {
                    themeIcon.className = 'fas fa-sun';
                } else {
                    themeIcon.className = 'fas fa-moon';
                }
            }
        },
        
        enableTransitions() {
            document.documentElement.style.transition = 'all 0.3s ease';
        }
    };
    
    // Critical lazy loading
    const lazyLoader = {
        init() {
            if ('IntersectionObserver' in window) {
                this.setupIntersectionObserver();
            } else {
                this.setupScrollBasedLoading();
            }
        },
        
        setupIntersectionObserver() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadElement(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });
            
            // Observe lazy images
            document.querySelectorAll('img[data-src]').forEach(img => {
                observer.observe(img);
            });
        },
        
        loadElement(element) {
            if (element.tagName === 'IMG' && element.dataset.src) {
                element.src = element.dataset.src;
                element.removeAttribute('data-src');
                element.classList.add('loaded');
            }
        },
        
        setupScrollBasedLoading() {
            let scrollTimeout;
            window.addEventListener('scroll', () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    this.loadVisibleElements();
                }, 100);
            });
        },
        
        loadVisibleElements() {
            document.querySelectorAll('img[data-src]').forEach(img => {
                if (this.isElementInViewport(img)) {
                    this.loadElement(img);
                }
            });
        },
        
        isElementInViewport(el) {
            const rect = el.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }
    };
    
    // Critical service worker registration
    const serviceWorkerManager = {
        init() {
            if ('serviceWorker' in navigator) {
                this.registerServiceWorker();
            }
        },
        
        async registerServiceWorker() {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('✅ Service Worker registered:', registration);
                
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            console.log('🔄 Service Worker updated');
                        }
                    });
                });
            } catch (error) {
                console.warn('Service Worker registration failed:', error);
            }
        }
    };
    
    // Initialize critical functionality
    function initCritical() {
        console.log('🚀 Critical JS Initializing...');
        
        performanceMonitor.init();
        errorHandler.init();
        themeManager.init();
        lazyLoader.init();
        serviceWorkerManager.init();
        
        // Make themeManager globally available
        window.themeManager = themeManager;
        
        console.log('✅ Critical JS Initialized');
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCritical);
    } else {
        initCritical();
    }
    
    // Export for use in other scripts
    window.CriticalJS = {
        performanceMonitor,
        errorHandler,
        themeManager,
        lazyLoader,
        serviceWorkerManager
    };
    
})();