// ========================================
// VIDEO MODAL FUNCTIONS
// ========================================

// Video modal açma fonksiyonu
function openVideoModal() {
    const modal = document.getElementById('videoModal');
    const videoPlayer = document.getElementById('videoPlayer');
    
    if (modal && videoPlayer) {
        modal.style.display = 'block';
        
        // Video'yu otomatik başlat
        videoPlayer.play().catch(function(error) {
            console.log('Video otomatik başlatılamadı:', error);
        });
        
        // Body scroll'u engelle
        document.body.style.overflow = 'hidden';
        
        // Analytics event
        if (typeof gtag !== 'undefined') {
            gtag('event', 'video_play', {
                'event_category': 'engagement',
                'event_label': 'hero_video'
            });
        }
    }
}

// Video modal kapatma fonksiyonu
function closeVideoModal() {
    const modal = document.getElementById('videoModal');
    const videoPlayer = document.getElementById('videoPlayer');
    
    if (modal && videoPlayer) {
        modal.style.display = 'none';
        
        // Video'yu durdur ve başa sar
        videoPlayer.pause();
        videoPlayer.currentTime = 0;
        
        // Body scroll'u geri aç
        document.body.style.overflow = 'auto';
        
        // Analytics event
        if (typeof gtag !== 'undefined') {
            gtag('event', 'video_close', {
                'event_category': 'engagement',
                'event_label': 'hero_video'
            });
        }
    }
}

// ESC tuşu ile modal kapatma
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeVideoModal();
    }
});

// Modal dışına tıklayınca kapatma
document.addEventListener('click', function(event) {
    const modal = document.getElementById('videoModal');
    if (event.target === modal) {
        closeVideoModal();
    }
});

// ========================================
// MODERN ANA SAYFA JAVASCRIPT DOSYASI - ENHANCED VERSION
// ========================================

// Modern Performance Monitor with Web APIs
class PerformanceMonitor {
    constructor() {
        this.startTime = performance.now();
        this.metrics = new Map();
        this.observer = null;
        this.initPerformanceObserver();
    }
    
    mark(name) {
        if ('mark' in performance) {
            performance.mark(name);
        }
        this.metrics.set(name, performance.now() - this.startTime);
    }
    
    measure(name, startMark, endMark) {
        try {
            if ('measure' in performance && performance.getEntriesByName(startMark).length) {
                performance.measure(name, startMark, endMark);
                const measure = performance.getEntriesByName(name, 'measure')[0];
                console.log(`${name}: ${measure.duration.toFixed(2)}ms`);
                return measure.duration;
            }
        } catch (error) {
            console.warn('Performance measurement failed:', error);
        }
        
        // Fallback
        const start = this.metrics.get(startMark) || 0;
        const end = this.metrics.get(endMark) || performance.now() - this.startTime;
        const duration = end - start;
        this.metrics.set(name, duration);
        return duration;
    }
    
    initPerformanceObserver() {
        if ('PerformanceObserver' in window) {
            try {
                this.observer = new PerformanceObserver((list) => {
                    list.getEntries().forEach((entry) => {
                        if (entry.entryType === 'navigation') {
                            console.log('Navigation timing:', entry);
                        } else if (entry.entryType === 'paint') {
                            console.log(`${entry.name}: ${entry.startTime.toFixed(2)}ms`);
                        }
                    });
                });
                
                this.observer.observe({ entryTypes: ['navigation', 'paint', 'measure'] });
            } catch (error) {
                console.warn('PerformanceObserver not supported:', error);
            }
        }
    }
    
    getReport() {
        return Object.fromEntries(this.metrics);
    }
    
    disconnect() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
}

const performanceMonitor = new PerformanceMonitor();

// Modern Feature Detection with Enhanced Capabilities
class FeatureDetector {
    constructor() {
        this.cache = new Map();
        this.init();
    }
    
    init() {
        // Core feature detection
        this.intersectionObserver = 'IntersectionObserver' in window;
        this.resizeObserver = 'ResizeObserver' in window;
        this.mutationObserver = 'MutationObserver' in window;
        this.performanceObserver = 'PerformanceObserver' in window;
        
        // CSS feature detection
        this.modernCSS = {
            grid: CSS.supports('display', 'grid'),
            flexbox: CSS.supports('display', 'flex'),
            customProperties: CSS.supports('--custom', 'property'),
            backdropFilter: CSS.supports('backdrop-filter', 'blur(10px)'),
            containerQueries: CSS.supports('container-type', 'inline-size'),
            aspectRatio: CSS.supports('aspect-ratio', '1/1'),
            gap: CSS.supports('gap', '1rem')
        };
        
        // Device and interaction detection
        this.touchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        this.pointerEvents = 'PointerEvent' in window;
        this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        this.highContrast = window.matchMedia('(prefers-contrast: high)').matches;
        
        // Network and storage
        this.serviceWorker = 'serviceWorker' in navigator;
        this.localStorage = this.testLocalStorage();
        this.sessionStorage = this.testSessionStorage();
        this.indexedDB = 'indexedDB' in window;
        
        // Modern APIs
        this.webGL = this.testWebGL();
        this.webAssembly = 'WebAssembly' in window;
        this.webWorkers = 'Worker' in window;
        this.geolocation = 'geolocation' in navigator;
        this.notifications = 'Notification' in window;
        
        // Initialize async detections
        this.detectImageFormats();
        this.detectNetworkInfo();
    }
    
    async detectImageFormats() {
        const formats = ['webp', 'avif', 'jxl'];
        const results = {};
        
        for (const format of formats) {
            try {
                results[format] = await this.testImageFormat(format);
            } catch (error) {
                results[format] = false;
            }
        }
        
        this.imageFormats = results;
        return results;
    }
    
    async testImageFormat(format) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => resolve(img.width === 1 && img.height === 1);
            img.onerror = () => resolve(false);
            
            const testImages = {
                webp: 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA',
                avif: 'data:image/avif;base64,AAAAIGZ0eXBhdmlmAAAAAGF2aWZtaWYxbWlhZk1BMUIAAADybWV0YQAAAAAAAAAoaGRscgAAAAAAAAAAcGljdAAAAAAAAAAAAAAAAGxpYmF2aWYAAAAADnBpdG0AAAAAAAEAAAAeaWxvYwAAAABEAAABAAEAAAABAAABGgAAAB0AAAAoaWluZgAAAAAAAQAAABppbmZlAgAAAAABAABhdjAxQ29sb3IAAAAAamlwcnAAAABLaXBjbwAAABRpc3BlAAAAAAAAAAIAAAACAAAAEHBpeGkAAAAAAwgICAAAAAxhdjFDgQ0MAAAAABNjb2xybmNseAACAAIAAYAAAAAXaXBtYQAAAAAAAAABAAEEAQKDBAAAACVtZGF0EgAKCBgABogQEAwgMg8f8D///8WfhwB8+ErK42A='
            };
            
            img.src = testImages[format] || '';
            
            // Timeout after 1 second
            setTimeout(() => resolve(false), 1000);
        });
    }
    
    detectNetworkInfo() {
        if ('connection' in navigator) {
            this.networkInfo = {
                effectiveType: navigator.connection.effectiveType,
                downlink: navigator.connection.downlink,
                rtt: navigator.connection.rtt,
                saveData: navigator.connection.saveData
            };
        }
    }
    
    testLocalStorage() {
        try {
            const test = '__test__';
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch (e) {
            return false;
        }
    }
    
    testSessionStorage() {
        try {
            const test = '__test__';
            sessionStorage.setItem(test, test);
            sessionStorage.removeItem(test);
            return true;
        } catch (e) {
            return false;
        }
    }
    
    testWebGL() {
        try {
            const canvas = document.createElement('canvas');
            return !!(canvas.getContext('webgl') || canvas.getContext('experimental-webgl'));
        } catch (e) {
            return false;
        }
    }
    
    supports(feature) {
        if (this.cache.has(feature)) {
            return this.cache.get(feature);
        }
        
        const result = this[feature] !== undefined ? this[feature] : false;
        this.cache.set(feature, result);
        return result;
    }
    
    getReport() {
        return {
            intersectionObserver: this.intersectionObserver,
            resizeObserver: this.resizeObserver,
            modernCSS: this.modernCSS,
            touchDevice: this.touchDevice,
            reducedMotion: this.reducedMotion,
            darkMode: this.darkMode,
            imageFormats: this.imageFormats,
            networkInfo: this.networkInfo,
            serviceWorker: this.serviceWorker,
            localStorage: this.localStorage,
            webGL: this.webGL
        };
    }
}

const featureSupport = new FeatureDetector();

// Modern Application Initialization
class AppInitializer {
    constructor() {
        this.initialized = false;
        this.initPromise = null;
    }
    
    async init() {
        if (this.initialized) return;
        if (this.initPromise) return this.initPromise;
        
        this.initPromise = this._performInit();
        await this.initPromise;
        this.initialized = true;
    }
    
    async _performInit() {
        performanceMonitor.mark('appInitStart');
        
        try {
            // Phase 1: Critical features
            await this.initCriticalFeatures();
            
            // Phase 2: UI enhancements
            await this.initUIEnhancements();
            
            // Phase 3: Progressive features
            await this.initProgressiveFeatures();
            
            // Phase 4: Background features
            this.initBackgroundFeatures();
            
            performanceMonitor.mark('appInitEnd');
            performanceMonitor.measure('App Initialization', 'appInitStart', 'appInitEnd');
            
            this.logInitializationReport();
            
        } catch (error) {
            console.error('App initialization failed:', error);
            this.handleInitializationError(error);
        }
    }
    
    async initCriticalFeatures() {
        const criticalTasks = [
            () => featureSupport.detectImageFormats(),
            () => this.initErrorHandling(),
            () => this.initAccessibilityFeatures(),
            () => this.initModernAnimations()
        ];
        
        await Promise.all(criticalTasks.map(task => this.safeExecute(task)));
    }
    
    async initUIEnhancements() {
        const uiTasks = [
            () => this.initSmoothScrolling(),
            () => this.initScrollAnimations(),
            () => this.initHeaderScrollEffect(),
            () => this.initModernInteractions(),
            () => this.initContactForm()
        ];
        
        await Promise.all(uiTasks.map(task => this.safeExecute(task)));
    }
    
    async initProgressiveFeatures() {
        const progressiveTasks = [
            () => this.initPortfolioEffects(),
            () => this.initParallaxEffects(),
            () => this.initEnhancedScrollAnimations(),
            () => this.initModernScrollEffects(),
            () => this.initProgressiveEnhancement()
        ];
        
        await Promise.all(progressiveTasks.map(task => this.safeExecute(task)));
    }
    
    initBackgroundFeatures() {
        // Non-blocking background tasks
        requestIdleCallback(() => {
            this.safeExecute(() => this.initPerformanceOptimizations());
            this.safeExecute(() => this.initServiceWorker());
        }, { timeout: 5000 });
    }
    
    async safeExecute(fn) {
        try {
            return await fn();
        } catch (error) {
            console.warn(`Task failed: ${fn.name}`, error);
            return null;
        }
    }
    
    initErrorHandling() {
        initErrorHandling();
    }
    
    initAccessibilityFeatures() {
        initAccessibilityFeatures();
    }
    
    initModernAnimations() {
        initModernAnimations();
    }
    
    initSmoothScrolling() {
        initSmoothScrolling();
    }
    
    initScrollAnimations() {
        initScrollAnimations();
    }
    
    initHeaderScrollEffect() {
        initHeaderScrollEffect();
    }
    
    initModernInteractions() {
        initModernInteractions();
    }
    
    initContactForm() {
        initContactForm();
    }
    
    initPortfolioEffects() {
        initPortfolioEffects();
    }
    
    initParallaxEffects() {
        initParallaxEffects();
    }
    
    initEnhancedScrollAnimations() {
        initEnhancedScrollAnimations();
    }
    
    initModernScrollEffects() {
        initModernScrollEffects();
    }
    
    initProgressiveEnhancement() {
        initProgressiveEnhancement();
    }
    
    initPerformanceOptimizations() {
        initPerformanceOptimizations();
    }
    
    initServiceWorker() {
        initServiceWorker();
    }
    
    handleInitializationError(error) {
        // Fallback to basic functionality
        document.body.classList.add('basic-mode');
        console.error('Falling back to basic mode due to initialization error:', error);
    }
    
    logInitializationReport() {
        console.group('🚀 NextCode App Initialized');
        console.log('Performance Report:', performanceMonitor.getReport());
        console.log('Feature Support:', featureSupport.getReport());
        console.log('Initialization completed successfully');
        console.groupEnd();
    }
}

const appInitializer = new AppInitializer();

// DOM Content Loaded Event
document.addEventListener('DOMContentLoaded', () => {
    appInitializer.init();
});

// Modern Animasyonlar
function initModernAnimations() {
    // Fade in up animation for elements
    const animateElements = document.querySelectorAll('.service-card, .portfolio-item, .blog-card');
    
    animateElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'all 0.6s ease';
        
        setTimeout(() => {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Modern UI Etkileşimleri
function initModernInteractions() {
    // Button hover effects
    const buttons = document.querySelectorAll('.btn-primary');
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.4)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.3)';
        });
    });
    
    // Card hover effects
    const cards = document.querySelectorAll('.service-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 20px 60px rgba(0, 0, 0, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 10px 40px rgba(0, 0, 0, 0.1)';
        });
    });
}

// Parallax Efektleri
function initParallaxEffects() {
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.hero-section');
        
        parallaxElements.forEach(element => {
            const speed = 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
}

// Mobile Menu Fonksiyonları
function initMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Menu linklerine tıklandığında menüyü kapat
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }
}

// Smooth Scrolling
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            // Check if href is valid and not just '#'
            if (href && href !== '#' && href.length > 1) {
                const target = document.querySelector(href);
                
                if (target) {
                    const header = document.querySelector('#header');
                    const headerHeight = header ? header.offsetHeight : 0;
                    const targetPosition = target.offsetTop - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
}

// Enhanced Scroll Animasyonları
function initScrollAnimations() {
    if (!featureSupport.intersectionObserver) {
        // Fallback for older browsers
        addScrollAnimationsToElements();
        return;
    }
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const delay = element.dataset.delay || 0;
                
                setTimeout(() => {
                    element.classList.add('animate-in');
                    
                    // Add stagger effect for multiple elements
                    if (element.classList.contains('stagger')) {
                        const siblings = element.parentElement.querySelectorAll('.stagger');
                        siblings.forEach((sibling, index) => {
                            setTimeout(() => {
                                sibling.classList.add('animate-in');
                            }, index * 100);
                        });
                    }
                }, delay);
                
                observer.unobserve(element);
            }
        });
    }, observerOptions);
    
    // Observe elements with animation classes
    const animatedElements = document.querySelectorAll('.fade-up, .fade-left, .fade-right, .service-card');
    animatedElements.forEach(element => {
        observer.observe(element);
    });
}

// Add scroll animations to elements without Intersection Observer
function addScrollAnimationsToElements() {
    const elements = document.querySelectorAll('.fade-up, .fade-left, .fade-right');
    
    function checkScroll() {
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add('animate-in');
            }
        });
    }
    
    window.addEventListener('scroll', checkScroll);
    checkScroll(); // Initial check
}

// Modern Scroll Effects
function initModernScrollEffects() {
    let ticking = false;
    
    function updateScrollEffects() {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        // Parallax effect for hero section
        const heroSection = document.querySelector('.hero-section');
        if (heroSection) {
            heroSection.style.transform = `translate3d(0, ${rate}px, 0)`;
        }
        

        
        ticking = false;
    }
    
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateScrollEffects);
            ticking = true;
        }
    }
    
    window.addEventListener('scroll', requestTick);
}

// Initialize enhanced scroll animations
function initEnhancedScrollAnimations() {
    // Add animation classes to elements
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach((card, index) => {
        card.classList.add('fade-up');
        card.dataset.delay = index * 100;
    });
    

    
    // Initialize scroll animations
    initScrollAnimations();
    initModernScrollEffects();
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, observerOptions);
    
    // Animasyon yapılacak elementleri seç
    const animateElements = document.querySelectorAll(
        '.service-card, .portfolio-item, .blog-card, .stat-item, .about-content, .contact-content'
    );
    
    animateElements.forEach(el => {
        el.classList.add('scroll-animate');
        observer.observe(el);
    });
}

// Header Scroll Effect
function initHeaderScrollEffect() {
    const header = document.querySelector('#header');
    
    // Header elementi varsa scroll efektini uygula
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.98)';
                header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.15)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
            }
        });
    }
}

// Portfolio Hover Effects
function initPortfolioEffects() {
    const portfolioItems = document.querySelectorAll('.portfolio-item');
    
    portfolioItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
}

// İletişim Formu
function initContactForm() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Form verilerini al
            const formData = new FormData(this);
            const data = {
                firstName: formData.get('firstName'),
                lastName: formData.get('lastName'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                company: formData.get('company'),
                service: formData.get('service'),
                budget: formData.get('budget'),
                message: formData.get('message'),
                privacy: formData.get('privacy'),
                newsletter: formData.get('newsletter')
            };
            
            // Form validasyonu
            if (validateContactForm(data)) {
                // Form gönderimi simülasyonu
                submitContactForm(data);
            }
        });
    }
}

// Form Validasyonu
function validateContactForm(data) {
    let isValid = true;
    const errors = [];
    
    // Ad kontrolü
    if (!data.firstName || data.firstName.trim().length < 2) {
        errors.push('Lütfən düzgün ad daxil edin.');
        isValid = false;
    }
    
    // Soyad kontrolü
    if (!data.lastName || data.lastName.trim().length < 2) {
        errors.push('Lütfən düzgün soyad daxil edin.');
        isValid = false;
    }
    
    // E-posta kontrolü
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!data.email || !emailRegex.test(data.email)) {
        errors.push('Lütfən düzgün e-poçt ünvanı daxil edin.');
        isValid = false;
    }
    
    // Mesaj kontrolü
    if (!data.message || data.message.trim().length < 10) {
        errors.push('Lütfən ən azı 10 simvol uzunluğunda mesaj daxil edin.');
        isValid = false;
    }
    
    // Məxfilik siyasəti kontrolü
    if (!data.privacy) {
        errors.push('Məxfilik siyasətini qəbul etməlisiniz.');
        isValid = false;
    }
    
    // Hataları göster
    if (!isValid) {
        showAlert(errors.join('\n'), 'error');
    }
    
    return isValid;
}

// Form Gönderimi
function submitContactForm(data) {
    // Loading durumu göster
    const submitBtn = document.querySelector('#contactForm button[type="submit"]');
    
    if (submitBtn) {
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Göndərilir...';
        submitBtn.disabled = true;
        
        // Simülasyon için setTimeout kullan
        setTimeout(() => {
            // Başarı mesajı göster
            showAlert('Mesajınız uğurla göndərildi! Ən qısa zamanda sizinlə əlaqə saxlayacağıq.', 'success');
            
            // Formu temizle
            const contactForm = document.getElementById('contactForm');
            if (contactForm) {
                contactForm.reset();
            }
            
            // Butonu eski haline getir
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            
            // Gerçek uygulamada burada API çağrısı yapılacak
            console.log('Form Data:', data);
        }, 2000);
    }
}

// Alert Mesajları
function showAlert(message, type = 'info') {
    // Mevcut alert varsa kaldır
    const existingAlert = document.querySelector('.alert-message');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Yeni alert oluştur
    const alert = document.createElement('div');
    alert.className = `alert-message alert-${type}`;
    alert.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        max-width: 400px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        animation: slideInRight 0.3s ease-out;
    `;
    
    // Tip göre renk ayarla
    switch(type) {
        case 'success':
            alert.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
            break;
        case 'error':
            alert.style.background = 'linear-gradient(135deg, #dc3545, #e83e8c)';
            break;
        case 'warning':
            alert.style.background = 'linear-gradient(135deg, #ffc107, #fd7e14)';
            break;
        default:
            alert.style.background = 'linear-gradient(135deg, #17a2b8, #6f42c1)';
    }
    
    alert.textContent = message;
    document.body.appendChild(alert);
    
    // 5 saniye sonra kaldır
    setTimeout(() => {
        if (alert.parentNode) {
            alert.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => alert.remove(), 300);
        }
    }, 5000);
}

// Sayfa yüklenme animasyonu
window.addEventListener('load', function() {
    document.body.classList.add('loaded');
    
    // Hero bölümü animasyonu
    const heroContent = document.querySelector('.hero-content');
    if (heroContent) {
        heroContent.classList.add('fade-in-up');
    }
});

// Scroll to top butonu
function initScrollToTop() {
    // Scroll to top butonu oluştur
    const scrollBtn = document.createElement('button');
    scrollBtn.innerHTML = '↑';
    scrollBtn.className = 'scroll-to-top';
    scrollBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        border: none;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    `;
    
    document.body.appendChild(scrollBtn);
    
    // Scroll olayını dinle
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            scrollBtn.style.opacity = '1';
            scrollBtn.style.visibility = 'visible';
        } else {
            scrollBtn.style.opacity = '0';
            scrollBtn.style.visibility = 'hidden';
        }
    });
    
    // Butona tıklandığında yukarı scroll
    scrollBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Scroll to top butonunu başlat
initScrollToTop();

// CSS animasyonları için style ekle
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .scroll-to-top:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3) !important;
    }
`;
document.head.appendChild(style);

// Modern Utilities Class with ES6+ features
class ModernUtils {
    constructor() {
        this.cache = new Map();
        this.observers = new Map();
        this.timers = new Map();
        this.abortControllers = new Map();
    }

    // Enhanced debounce with cancellation support
    debounce(func, wait, options = {}) {
        const { immediate = false, maxWait = null } = options;
        let timeoutId, maxTimeoutId, lastCallTime, lastInvokeTime = 0;
        
        const invokeFunc = (time) => {
            const args = this.lastArgs;
            const thisArg = this.lastThis;
            
            this.lastArgs = this.lastThis = undefined;
            lastInvokeTime = time;
            return func.apply(thisArg, args);
        };
        
        const leadingEdge = (time) => {
            lastInvokeTime = time;
            timeoutId = setTimeout(timerExpired, wait);
            return immediate ? invokeFunc(time) : this.result;
        };
        
        const remainingWait = (time) => {
            const timeSinceLastCall = time - lastCallTime;
            const timeSinceLastInvoke = time - lastInvokeTime;
            const timeWaiting = wait - timeSinceLastCall;
            
            return maxWait !== null
                ? Math.min(timeWaiting, maxWait - timeSinceLastInvoke)
                : timeWaiting;
        };
        
        const shouldInvoke = (time) => {
            const timeSinceLastCall = time - lastCallTime;
            const timeSinceLastInvoke = time - lastInvokeTime;
            
            return (lastCallTime === undefined || (timeSinceLastCall >= wait) ||
                    (timeSinceLastCall < 0) || (maxWait !== null && timeSinceLastInvoke >= maxWait));
        };
        
        const timerExpired = () => {
            const time = Date.now();
            if (shouldInvoke(time)) {
                return trailingEdge(time);
            }
            timeoutId = setTimeout(timerExpired, remainingWait(time));
        };
        
        const trailingEdge = (time) => {
            timeoutId = undefined;
            if (this.lastArgs) {
                return invokeFunc(time);
            }
            this.lastArgs = this.lastThis = undefined;
            return this.result;
        };
        
        const cancel = () => {
            if (timeoutId !== undefined) {
                clearTimeout(timeoutId);
            }
            if (maxTimeoutId !== undefined) {
                clearTimeout(maxTimeoutId);
            }
            lastInvokeTime = 0;
            this.lastArgs = this.lastThis = timeoutId = maxTimeoutId = undefined;
        };
        
        const flush = () => {
            return timeoutId === undefined ? this.result : trailingEdge(Date.now());
        };
        
        const debounced = (...args) => {
            const time = Date.now();
            const isInvoking = shouldInvoke(time);
            
            this.lastArgs = args;
            this.lastThis = this;
            lastCallTime = time;
            
            if (isInvoking) {
                if (timeoutId === undefined) {
                    return leadingEdge(lastCallTime);
                }
                if (maxWait !== null) {
                    timeoutId = setTimeout(timerExpired, wait);
                    return invokeFunc(lastCallTime);
                }
            }
            if (timeoutId === undefined) {
                timeoutId = setTimeout(timerExpired, wait);
            }
            return this.result;
        };
        
        debounced.cancel = cancel;
        debounced.flush = flush;
        return debounced;
    }
    
    // Enhanced throttle with leading/trailing options
    throttle(func, limit, options = {}) {
        const { leading = true, trailing = true } = options;
        return this.debounce(func, limit, {
            maxWait: limit,
            immediate: leading
        });
    }
    
    // Advanced viewport detection with partial visibility
    isElementInViewport(el, options = {}) {
        const {
            threshold = 0,
            partial = false,
            container = null
        } = options;
        
        if (!el || !el.getBoundingClientRect) return false;
        
        const rect = el.getBoundingClientRect();
        const containerRect = container ? container.getBoundingClientRect() : {
            top: 0,
            left: 0,
            bottom: window.innerHeight,
            right: window.innerWidth
        };
        
        if (partial) {
            return (
                rect.bottom > containerRect.top + threshold &&
                rect.top < containerRect.bottom - threshold &&
                rect.right > containerRect.left + threshold &&
                rect.left < containerRect.right - threshold
            );
        }
        
        return (
            rect.top >= containerRect.top + threshold &&
            rect.left >= containerRect.left + threshold &&
            rect.bottom <= containerRect.bottom - threshold &&
            rect.right <= containerRect.right - threshold
        );
    }
    
    // Enhanced Intersection Observer with caching
    createIntersectionObserver(callback, options = {}) {
        const cacheKey = JSON.stringify(options);
        
        if (this.observers.has(cacheKey)) {
            return this.observers.get(cacheKey);
        }
        
        let observer;
        
        if (featureSupport.supports('intersectionObserver')) {
            observer = new IntersectionObserver(callback, {
                threshold: options.threshold || 0.1,
                rootMargin: options.rootMargin || '0px',
                root: options.root || null,
                ...options
            });
        } else {
            // Enhanced fallback with scroll-based detection
            const elements = new Set();
            let isObserving = false;
            
            const checkVisibility = this.throttle(() => {
                elements.forEach(element => {
                    const isVisible = this.isElementInViewport(element, {
                        threshold: options.threshold || 0.1,
                        partial: true
                    });
                    
                    callback([{
                        target: element,
                        isIntersecting: isVisible,
                        intersectionRatio: isVisible ? 1 : 0
                    }]);
                });
            }, 100);
            
            observer = {
                observe: (element) => {
                    elements.add(element);
                    if (!isObserving) {
                        window.addEventListener('scroll', checkVisibility, { passive: true });
                        window.addEventListener('resize', checkVisibility, { passive: true });
                        isObserving = true;
                    }
                    // Initial check
                    checkVisibility();
                },
                unobserve: (element) => {
                    elements.delete(element);
                    if (elements.size === 0 && isObserving) {
                        window.removeEventListener('scroll', checkVisibility);
                        window.removeEventListener('resize', checkVisibility);
                        isObserving = false;
                    }
                },
                disconnect: () => {
                    elements.clear();
                    if (isObserving) {
                        window.removeEventListener('scroll', checkVisibility);
                        window.removeEventListener('resize', checkVisibility);
                        isObserving = false;
                    }
                }
            };
        }
        
        this.observers.set(cacheKey, observer);
        return observer;
    }
    
    // Enhanced error handling with retry logic
    async handleError(error, context = 'Unknown', options = {}) {
        const {
            retry = false,
            maxRetries = 3,
            retryDelay = 1000,
            silent = false
        } = options;
        
        if (!silent) {
            console.error(`Error in ${context}:`, error);
        }
        
        // Enhanced analytics reporting
        if (window.gtag) {
            gtag('event', 'exception', {
                description: `${context}: ${error.message}`,
                fatal: false,
                custom_map: {
                    error_context: context,
                    error_stack: error.stack?.substring(0, 500)
                }
            });
        }
        
        // User notification for critical errors
        if (context.includes('critical') && !silent) {
            showNotification('Bir hata oluştu. Lütfen sayfayı yenileyin.', 'error');
        }
        
        // Retry logic
        if (retry && error.retryCount < maxRetries) {
            error.retryCount = (error.retryCount || 0) + 1;
            await this.delay(retryDelay * error.retryCount);
            throw error; // Re-throw for retry handling
        }
        
        return error;
    }
    
    // Enhanced timeout utility with abort controller
    async withTimeout(promise, timeoutMs = 5000, options = {}) {
        const { signal, onTimeout } = options;
        
        const controller = new AbortController();
        const timeoutId = setTimeout(() => {
            controller.abort();
            if (onTimeout) onTimeout();
        }, timeoutMs);
        
        try {
            const result = await Promise.race([
                promise,
                new Promise((_, reject) => {
                    controller.signal.addEventListener('abort', () => {
                        reject(new Error(`Operation timed out after ${timeoutMs}ms`));
                    });
                    
                    if (signal) {
                        signal.addEventListener('abort', () => {
                            reject(new Error('Operation was aborted'));
                        });
                    }
                })
            ]);
            
            clearTimeout(timeoutId);
            return result;
        } catch (error) {
            clearTimeout(timeoutId);
            throw error;
        }
    }
    
    // Advanced image loading with progressive enhancement
    async loadOptimizedImage(src, options = {}) {
        const {
            alt = '',
            className = '',
            sizes = '',
            loading = 'lazy',
            decode = true,
            placeholder = null,
            onProgress = null
        } = options;
        
        try {
            const img = new Image();
            
            // Set basic attributes
            img.alt = alt;
            img.className = className;
            img.loading = loading;
            if (sizes) img.sizes = sizes;
            
            // Try modern formats with feature detection
            const optimizedSrc = await this.getOptimizedImageSrc(src);
            
            // Create loading promise
            const loadPromise = new Promise((resolve, reject) => {
                img.onload = () => {
                    if (decode && img.decode) {
                        img.decode().then(() => resolve(img)).catch(() => resolve(img));
                    } else {
                        resolve(img);
                    }
                };
                
                img.onerror = () => {
                    // Fallback to original format
                    if (optimizedSrc !== src) {
                        img.src = src;
                    } else {
                        reject(new Error(`Failed to load image: ${src}`));
                    }
                };
                
                // Progress tracking for larger images
                if (onProgress && 'fetch' in window) {
                    this.trackImageProgress(optimizedSrc, onProgress);
                }
            });
            
            img.src = optimizedSrc;
            
            return await this.withTimeout(loadPromise, 10000, {
                onTimeout: () => console.warn(`Image loading timed out: ${src}`)
            });
            
        } catch (error) {
            await this.handleError(error, 'loadOptimizedImage', { silent: true });
            throw error;
        }
    }
    
    // Get optimized image source based on browser support
    async getOptimizedImageSrc(src) {
        const formats = ['avif', 'webp'];
        
        for (const format of formats) {
            if (featureSupport.supports(format)) {
                const optimizedSrc = src.replace(/\.(jpe?g|png)$/i, `.${format}`);
                
                // Check if optimized version exists
                if (await this.imageExists(optimizedSrc)) {
                    return optimizedSrc;
                }
            }
        }
        
        return src;
    }
    
    // Check if image exists
    async imageExists(src) {
        try {
            const response = await fetch(src, { method: 'HEAD' });
            return response.ok;
        } catch {
            return false;
        }
    }
    
    // Track image loading progress
    async trackImageProgress(src, onProgress) {
        try {
            const response = await fetch(src);
            const reader = response.body?.getReader();
            
            if (!reader) return;
            
            const contentLength = +response.headers.get('Content-Length');
            let receivedLength = 0;
            
            while (true) {
                const { done, value } = await reader.read();
                
                if (done) break;
                
                receivedLength += value.length;
                
                if (contentLength) {
                    const progress = (receivedLength / contentLength) * 100;
                    onProgress(Math.round(progress));
                }
            }
        } catch (error) {
            console.warn('Failed to track image progress:', error);
        }
    }
    
    // Utility delay function
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
    
    // Cache management
    setCache(key, value, ttl = 300000) { // 5 minutes default TTL
        this.cache.set(key, {
            value,
            expires: Date.now() + ttl
        });
    }
    
    getCache(key) {
        const item = this.cache.get(key);
        if (!item) return null;
        
        if (Date.now() > item.expires) {
            this.cache.delete(key);
            return null;
        }
        
        return item.value;
    }
    
    clearCache() {
        this.cache.clear();
    }
    
    // Cleanup method
    destroy() {
        this.observers.forEach(observer => observer.disconnect());
        this.observers.clear();
        this.timers.forEach(timer => clearTimeout(timer));
        this.timers.clear();
        this.abortControllers.forEach(controller => controller.abort());
        this.abortControllers.clear();
        this.clearCache();
    }
}

// Create global instance
const utils = new ModernUtils();

// Performance Optimization Functions
function initPerformanceOptimizations() {
    performanceMonitor.mark('performanceOptStart');
    
    // Optimize scroll events
    const optimizedScrollHandler = utils.throttle(() => {
        // Handle scroll-based animations and effects
        handleScrollEffects();
    }, 16); // ~60fps
    
    window.addEventListener('scroll', optimizedScrollHandler, { passive: true });
    
    // Optimize resize events
    const optimizedResizeHandler = utils.debounce(() => {
        // Handle responsive adjustments
        handleResponsiveAdjustments();
    }, 250);
    
    window.addEventListener('resize', optimizedResizeHandler);
    
    // Preload critical resources
    preloadCriticalResources();
    
    // Initialize lazy loading
    initLazyLoading();
    
    performanceMonitor.mark('performanceOptEnd');
}

function handleScrollEffects() {
    // Consolidated scroll effects to reduce reflow/repaint
    const scrollY = window.pageYOffset;
    
    // Header effect
    const header = document.querySelector('#header');
    if (header) {
        if (scrollY > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.98)';
            header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.15)';
        } else {
            header.style.background = 'rgba(255, 255, 255, 0.95)';
            header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
        }
    }
    
    // Parallax effects
    if (!featureSupport.reducedMotion) {
        const parallaxElements = document.querySelectorAll('.hero-section');
        parallaxElements.forEach(element => {
            const speed = 0.5;
            element.style.transform = `translateY(${scrollY * speed}px)`;
        });
    }
}

function handleResponsiveAdjustments() {
    // Handle responsive layout adjustments
    const viewport = {
        width: window.innerWidth,
        height: window.innerHeight
    };
    
    // Adjust mobile menu if needed
    if (viewport.width > 768) {
        const navMenu = document.querySelector('.nav-menu');
        const hamburger = document.querySelector('.hamburger');
        if (navMenu && hamburger) {
            navMenu.classList.remove('active');
            hamburger.classList.remove('active');
        }
    }
}

function preloadCriticalResources() {
    // Preload critical CSS and fonts
    const criticalResources = [
        { href: 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', as: 'style' },
        { href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', as: 'style' }
    ];
    
    criticalResources.forEach(resource => {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.href = resource.href;
        link.as = resource.as;
        link.crossOrigin = 'anonymous';
        document.head.appendChild(link);
    });
}

function initLazyLoading() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    if (featureSupport.intersectionObserver) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for older browsers
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.classList.remove('lazy');
        });
    }
}

// Modern Accessibility Manager Class
class AccessibilityManager {
    constructor() {
        this.liveRegion = null;
        this.focusTrap = new Map();
        this.keyboardListeners = new Set();
        this.isInitialized = false;
    }
    
    async init() {
        if (this.isInitialized) return;
        
        performanceMonitor.mark('accessibilityStart');
        
        try {
            await Promise.all([
                this.initKeyboardNavigation(),
                this.initFocusManagement(),
                this.initScreenReaderSupport(),
                this.initHighContrastSupport(),
                this.initReducedMotionSupport(),
                this.initARIAEnhancements()
            ]);
            
            this.isInitialized = true;
            performanceMonitor.mark('accessibilityEnd');
        } catch (error) {
            utils.handleError(error, 'AccessibilityManager.init');
        }
    }
    
    async initKeyboardNavigation() {
        // Setup keyboard event handlers
        this.setupKeyboardHandlers();
        
        // Initialize roving tabindex for complex widgets
        this.initRovingTabindex();
    }
    

    
    setupKeyboardHandlers() {
        const keydownHandler = (e) => {
            switch (e.key) {
                case 'Tab':
                    this.handleTabNavigation(e);
                    break;
                case 'Escape':
                    this.handleEscapeKey(e);
                    break;
                case 'Enter':
                case ' ':
                    this.handleActivation(e);
                    break;
                case 'ArrowUp':
                case 'ArrowDown':
                case 'ArrowLeft':
                case 'ArrowRight':
                    this.handleArrowNavigation(e);
                    break;
            }
        };
        
        const mousedownHandler = () => {
            document.body.classList.remove('keyboard-navigation');
        };
        
        document.addEventListener('keydown', keydownHandler);
        document.addEventListener('mousedown', mousedownHandler);
        
        this.keyboardListeners.add(() => {
            document.removeEventListener('keydown', keydownHandler);
            document.removeEventListener('mousedown', mousedownHandler);
        });
    }
    
    handleTabNavigation(e) {
        document.body.classList.add('keyboard-navigation');
        
        // Handle focus trapping in modals
        const activeModal = document.querySelector('.modal.show');
        if (activeModal && this.focusTrap.has(activeModal)) {
            this.trapFocus(e, activeModal);
        }
    }
    
    handleEscapeKey(e) {
        // Close modals, dropdowns, etc.
        const activeModal = document.querySelector('.modal.show');
        if (activeModal) {
            const modal = window.bootstrap?.Modal?.getInstance(activeModal);
            if (modal) {
                modal.hide();
                e.preventDefault();
            }
        }
        
        // Close dropdowns
        const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
        openDropdowns.forEach(dropdown => {
            const toggle = dropdown.previousElementSibling;
            if (toggle) toggle.click();
        });
    }
    
    handleActivation(e) {
        const target = e.target;
        
        // Handle custom interactive elements
        if (target.hasAttribute('role') && 
            ['button', 'tab', 'menuitem'].includes(target.getAttribute('role'))) {
            if (e.key === ' ') e.preventDefault();
            target.click();
        }
    }
    
    handleArrowNavigation(e) {
        const target = e.target;
        const role = target.getAttribute('role');
        
        if (['tablist', 'menu', 'menubar', 'listbox'].includes(role)) {
            this.navigateWithArrows(e, target);
        }
    }
    
    navigateWithArrows(e, container) {
        const items = container.querySelectorAll('[role="tab"], [role="menuitem"], [role="option"]');
        const currentIndex = Array.from(items).indexOf(e.target);
        let nextIndex;
        
        switch (e.key) {
            case 'ArrowUp':
            case 'ArrowLeft':
                nextIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
                break;
            case 'ArrowDown':
            case 'ArrowRight':
                nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
                break;
            default:
                return;
        }
        
        e.preventDefault();
        items[nextIndex].focus();
    }
    
    initRovingTabindex() {
        const widgets = document.querySelectorAll('[role="tablist"], [role="menu"], [role="menubar"]');
        
        widgets.forEach(widget => {
            const items = widget.querySelectorAll('[role="tab"], [role="menuitem"]');
            
            items.forEach((item, index) => {
                item.tabIndex = index === 0 ? 0 : -1;
                
                item.addEventListener('focus', () => {
                    items.forEach(otherItem => otherItem.tabIndex = -1);
                    item.tabIndex = 0;
                });
            });
        });
    }
    
    async initFocusManagement() {
        // Enhanced modal focus management
        const modals = document.querySelectorAll('.modal');
        
        modals.forEach(modal => {
            this.setupModalFocusTrap(modal);
        });
        
        // Setup focus indicators
        this.setupFocusIndicators();
    }
    
    setupModalFocusTrap(modal) {
        const focusableSelector = [
            'button:not([disabled])',
            '[href]:not([disabled])',
            'input:not([disabled])',
            'select:not([disabled])',
            'textarea:not([disabled])',
            '[tabindex]:not([tabindex="-1"]):not([disabled])'
        ].join(', ');
        
        modal.addEventListener('shown.bs.modal', () => {
            const focusableElements = modal.querySelectorAll(focusableSelector);
            
            if (focusableElements.length > 0) {
                this.focusTrap.set(modal, {
                    firstFocusable: focusableElements[0],
                    lastFocusable: focusableElements[focusableElements.length - 1],
                    previousFocus: document.activeElement
                });
                
                focusableElements[0].focus();
            }
        });
        
        modal.addEventListener('hidden.bs.modal', () => {
            const trapData = this.focusTrap.get(modal);
            if (trapData && trapData.previousFocus) {
                trapData.previousFocus.focus();
            }
            this.focusTrap.delete(modal);
        });
    }
    
    trapFocus(e, modal) {
        const trapData = this.focusTrap.get(modal);
        if (!trapData) return;
        
        if (e.shiftKey) {
            if (document.activeElement === trapData.firstFocusable) {
                e.preventDefault();
                trapData.lastFocusable.focus();
            }
        } else {
            if (document.activeElement === trapData.lastFocusable) {
                e.preventDefault();
                trapData.firstFocusable.focus();
            }
        }
    }
    
    setupFocusIndicators() {
        const style = document.createElement('style');
        style.textContent = `
            .keyboard-navigation *:focus {
                outline: 2px solid var(--focus-color, #007bff) !important;
                outline-offset: 2px !important;
            }
            
            .keyboard-navigation button:focus,
            .keyboard-navigation [role="button"]:focus {
                box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25) !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    async initScreenReaderSupport() {
        // Create live region for announcements
        this.createLiveRegion();
        
        // Enhance existing elements with ARIA
        this.enhanceWithARIA();
        
        // Setup dynamic content announcements
        this.setupDynamicAnnouncements();
    }
    
    createLiveRegion() {
        if (this.liveRegion) return;
        
        this.liveRegion = document.createElement('div');
        this.liveRegion.setAttribute('aria-live', 'polite');
        this.liveRegion.setAttribute('aria-atomic', 'true');
        this.liveRegion.className = 'sr-only';
        this.liveRegion.style.cssText = `
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        `;
        
        document.body.appendChild(this.liveRegion);
    }
    
    enhanceWithARIA() {
        // Add ARIA labels to buttons without text
        const buttons = document.querySelectorAll('button:not([aria-label]):not([aria-labelledby])');
        buttons.forEach(button => {
            if (!button.textContent.trim()) {
                const icon = button.querySelector('i[class*="fa-"]');
                if (icon) {
                    const iconClass = Array.from(icon.classList).find(cls => cls.startsWith('fa-'));
                    const label = this.getIconLabel(iconClass) || 'Düğme';
                    button.setAttribute('aria-label', label);
                }
            }
        });
        
        // Enhance form controls
        this.enhanceFormControls();
        
        // Add landmarks
        this.addLandmarks();
    }
    
    getIconLabel(iconClass) {
        const iconLabels = {
            'fa-home': 'Ana sayfa',
            'fa-menu': 'Menü',
            'fa-close': 'Kapat',
            'fa-search': 'Ara',
            'fa-user': 'Kullanıcı',
            'fa-phone': 'Telefon',
            'fa-email': 'E-posta',
            'fa-location': 'Konum'
        };
        
        return iconLabels[iconClass];
    }
    
    enhanceFormControls() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                if (!input.hasAttribute('aria-label') && !input.hasAttribute('aria-labelledby')) {
                    const label = form.querySelector(`label[for="${input.id}"]`);
                    if (label) {
                        input.setAttribute('aria-labelledby', label.id || this.generateId('label'));
                    }
                }
                
                // Add required indicator
                if (input.hasAttribute('required')) {
                    input.setAttribute('aria-required', 'true');
                }
            });
        });
    }
    
    addLandmarks() {
        // Add main landmark if not present
        if (!document.querySelector('main, [role="main"]')) {
            const mainContent = document.querySelector('#main, .main-content, .content');
            if (mainContent) {
                mainContent.setAttribute('role', 'main');
            }
        }
        
        // Add navigation landmarks
        const navs = document.querySelectorAll('nav:not([role]), .navbar:not([role])');
        navs.forEach(nav => nav.setAttribute('role', 'navigation'));
    }
    
    setupDynamicAnnouncements() {
        // Announce form submission results
        const originalShowAlert = window.showAlert;
        if (originalShowAlert) {
            window.showAlert = (message, type) => {
                originalShowAlert(message, type);
                this.announce(message);
            };
        }
    }
    
    announce(message, priority = 'polite') {
        if (!this.liveRegion) return;
        
        this.liveRegion.setAttribute('aria-live', priority);
        this.liveRegion.textContent = message;
        
        // Clear after announcement
        setTimeout(() => {
            this.liveRegion.textContent = '';
        }, 1000);
    }
    
    async initHighContrastSupport() {
        // Detect high contrast mode
        const isHighContrast = this.detectHighContrast();
        
        if (isHighContrast) {
            document.body.classList.add('high-contrast');
            this.enhanceForHighContrast();
        }
        
        // Listen for changes
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-contrast: high)');
            mediaQuery.addListener((e) => {
                if (e.matches) {
                    document.body.classList.add('high-contrast');
                    this.enhanceForHighContrast();
                } else {
                    document.body.classList.remove('high-contrast');
                }
            });
        }
    }
    
    detectHighContrast() {
        // Multiple detection methods
        if (window.matchMedia && window.matchMedia('(prefers-contrast: high)').matches) {
            return true;
        }
        
        // Fallback detection
        const testElement = document.createElement('div');
        testElement.style.cssText = `
            position: absolute;
            top: -9999px;
            background-color: canvas;
            color: canvastext;
        `;
        document.body.appendChild(testElement);
        
        const computedStyle = window.getComputedStyle(testElement);
        const isHighContrast = computedStyle.backgroundColor === computedStyle.color;
        
        document.body.removeChild(testElement);
        return isHighContrast;
    }
    
    enhanceForHighContrast() {
        const style = document.createElement('style');
        style.textContent = `
            .high-contrast {
                --focus-color: highlight;
                --text-color: canvastext;
                --bg-color: canvas;
                --border-color: canvastext;
            }
            
            .high-contrast * {
                border-color: var(--border-color) !important;
            }
            
            .high-contrast button,
            .high-contrast .btn {
                border: 2px solid var(--border-color) !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    async initReducedMotionSupport() {
        const prefersReducedMotion = window.matchMedia && 
            window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        if (prefersReducedMotion) {
            document.body.classList.add('reduced-motion');
            this.disableAnimations();
        }
        
        // Listen for changes
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
            mediaQuery.addListener((e) => {
                if (e.matches) {
                    document.body.classList.add('reduced-motion');
                    this.disableAnimations();
                } else {
                    document.body.classList.remove('reduced-motion');
                }
            });
        }
    }
    
    disableAnimations() {
        const style = document.createElement('style');
        style.textContent = `
            .reduced-motion *,
            .reduced-motion *::before,
            .reduced-motion *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    async initARIAEnhancements() {
        // Add ARIA expanded states to collapsible elements
        const collapsibles = document.querySelectorAll('[data-bs-toggle="collapse"]');
        collapsibles.forEach(trigger => {
            const target = document.querySelector(trigger.getAttribute('data-bs-target'));
            if (target) {
                trigger.setAttribute('aria-expanded', target.classList.contains('show'));
                trigger.setAttribute('aria-controls', target.id || this.generateId('collapse'));
            }
        });
        
        // Enhance tabs
        this.enhanceTabs();
        
        // Enhance carousels
        this.enhanceCarousels();
    }
    
    enhanceTabs() {
        const tabLists = document.querySelectorAll('.nav-tabs');
        
        tabLists.forEach(tabList => {
            tabList.setAttribute('role', 'tablist');
            
            const tabs = tabList.querySelectorAll('.nav-link');
            tabs.forEach((tab, index) => {
                tab.setAttribute('role', 'tab');
                tab.setAttribute('aria-selected', tab.classList.contains('active'));
                
                const targetId = tab.getAttribute('data-bs-target') || tab.getAttribute('href');
                if (targetId) {
                    const panel = document.querySelector(targetId);
                    if (panel) {
                        panel.setAttribute('role', 'tabpanel');
                        panel.setAttribute('aria-labelledby', tab.id || this.generateId('tab'));
                    }
                }
            });
        });
    }
    
    enhanceCarousels() {
        const carousels = document.querySelectorAll('.carousel');
        
        carousels.forEach(carousel => {
            carousel.setAttribute('role', 'region');
            carousel.setAttribute('aria-label', 'Carousel');
            
            const slides = carousel.querySelectorAll('.carousel-item');
            slides.forEach((slide, index) => {
                slide.setAttribute('role', 'group');
                slide.setAttribute('aria-label', `Slide ${index + 1} of ${slides.length}`);
            });
        });
    }
    
    generateId(prefix = 'element') {
        return `${prefix}-${Math.random().toString(36).substr(2, 9)}`;
    }
    
    destroy() {
        // Clean up event listeners
        this.keyboardListeners.forEach(cleanup => cleanup());
        this.keyboardListeners.clear();
        
        // Remove created elements
        if (this.liveRegion) {
            this.liveRegion.remove();
            this.liveRegion = null;
        }
        
        // Clear focus traps
        this.focusTrap.clear();
        
        this.isInitialized = false;
    }
}

// Create global instance
const accessibilityManager = new AccessibilityManager();

// Legacy function for backward compatibility
function initAccessibilityFeatures() {
    return accessibilityManager.init();
}

function initKeyboardNavigation() {
    return accessibilityManager.initKeyboardNavigation();
}

function initFocusManagement() {
    return accessibilityManager.initFocusManagement();
}

function initScreenReaderSupport() {
    return accessibilityManager.initScreenReaderSupport();
    liveRegion.setAttribute('aria-atomic', 'true');
    liveRegion.className = 'sr-only';
    return accessibilityManager.initScreenReaderSupport();
}

function initHighContrastSupport() {
    return accessibilityManager.initHighContrastSupport();
}

function initReducedMotionSupport() {
    return accessibilityManager.initReducedMotionSupport();
}

// Modern Progressive Enhancement Manager
class ProgressiveEnhancementManager {
    constructor() {
        this.validationRules = new Map();
        this.enhancedForms = new Set();
        this.enhancedImages = new Set();
        this.isInitialized = false;
    }
    
    async init() {
        if (this.isInitialized) return;
        
        performanceMonitor.mark('progressiveStart');
        
        try {
            await Promise.all([
                this.enhanceFormValidation(),
                this.enhanceImageHandling(),
                this.enhanceNavigation(),
                this.enhanceInteractivity()
            ]);
            
            this.isInitialized = true;
            performanceMonitor.mark('progressiveEnd');
        } catch (error) {
            utils.handleError(error, 'ProgressiveEnhancementManager.init');
        }
    }
    
    async enhanceFormValidation() {
        const forms = document.querySelectorAll('form');
        
        for (const form of forms) {
            if (this.enhancedForms.has(form)) continue;
            
            await this.setupFormEnhancements(form);
            this.enhancedForms.add(form);
        }
    }
    
    async setupFormEnhancements(form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        // Setup validation rules
        this.setupValidationRules();
        
        inputs.forEach(input => {
            this.enhanceInput(input);
        });
        
        // Add form-level enhancements
        this.addFormSubmissionEnhancements(form);
    }
    
    setupValidationRules() {
        this.validationRules.set('required', {
            test: (value) => value.trim().length > 0,
            message: 'Bu alan zorunludur.'
        });
        
        this.validationRules.set('email', {
            test: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
            message: 'Geçerli bir e-posta adresi girin.'
        });
        
        this.validationRules.set('phone', {
            test: (value) => /^[\+]?[0-9\s\-\(\)]{10,}$/.test(value),
            message: 'Geçerli bir telefon numarası girin.'
        });
        
        this.validationRules.set('url', {
            test: (value) => {
                try {
                    new URL(value);
                    return true;
                } catch {
                    return false;
                }
            },
            message: 'Geçerli bir URL girin.'
        });
        
        this.validationRules.set('minLength', {
            test: (value, min) => value.length >= min,
            message: (min) => `En az ${min} karakter olmalıdır.`
        });
        
        this.validationRules.set('maxLength', {
            test: (value, max) => value.length <= max,
            message: (max) => `En fazla ${max} karakter olmalıdır.`
        });
    }
    
    enhanceInput(input) {
        // Real-time validation with debouncing
        const debouncedValidation = utils.debounce(() => {
            if (input.classList.contains('is-invalid') || input.value.trim()) {
                this.validateField(input);
            }
        }, 300);
        
        input.addEventListener('blur', () => {
            this.validateField(input);
        });
        
        input.addEventListener('input', debouncedValidation);
        
        // Add accessibility enhancements
        this.addInputAccessibility(input);
        
        // Add visual enhancements
        this.addInputVisualEnhancements(input);
    }
    
    addInputAccessibility(input) {
        // Add ARIA attributes
        if (!input.hasAttribute('aria-describedby')) {
            const helpText = input.parentNode.querySelector('.form-text, .help-text');
            if (helpText) {
                const helpId = helpText.id || `help-${Math.random().toString(36).substr(2, 9)}`;
                helpText.id = helpId;
                input.setAttribute('aria-describedby', helpId);
            }
        }
        
        // Add required indicator
        if (input.hasAttribute('required')) {
            input.setAttribute('aria-required', 'true');
        }
    }
    
    addInputVisualEnhancements(input) {
        // Add focus indicators
        input.addEventListener('focus', () => {
            input.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', () => {
            input.parentNode.classList.remove('focused');
        });
        
        // Add character counter for text inputs
        if (input.hasAttribute('maxlength') && ['text', 'textarea'].includes(input.type)) {
            this.addCharacterCounter(input);
        }
    }
    
    addCharacterCounter(input) {
        const maxLength = parseInt(input.getAttribute('maxlength'));
        const counter = document.createElement('small');
        counter.className = 'character-counter text-muted';
        
        const updateCounter = () => {
            const remaining = maxLength - input.value.length;
            counter.textContent = `${remaining} karakter kaldı`;
            counter.classList.toggle('text-warning', remaining < 20);
            counter.classList.toggle('text-danger', remaining < 5);
        };
        
        input.addEventListener('input', updateCounter);
        updateCounter();
        
        input.parentNode.appendChild(counter);
    }
    
    validateField(field) {
        const value = field.value.trim();
        const validationResults = [];
        
        // Required validation
        if (field.hasAttribute('required')) {
            const rule = this.validationRules.get('required');
            if (!rule.test(value)) {
                validationResults.push(rule.message);
            }
        }
        
        // Type-specific validation
        if (value) {
            switch (field.type) {
                case 'email':
                    const emailRule = this.validationRules.get('email');
                    if (!emailRule.test(value)) {
                        validationResults.push(emailRule.message);
                    }
                    break;
                case 'tel':
                    const phoneRule = this.validationRules.get('phone');
                    if (!phoneRule.test(value)) {
                        validationResults.push(phoneRule.message);
                    }
                    break;
                case 'url':
                    const urlRule = this.validationRules.get('url');
                    if (!urlRule.test(value)) {
                        validationResults.push(urlRule.message);
                    }
                    break;
            }
            
            // Length validation
            if (field.hasAttribute('minlength')) {
                const min = parseInt(field.getAttribute('minlength'));
                const rule = this.validationRules.get('minLength');
                if (!rule.test(value, min)) {
                    validationResults.push(rule.message(min));
                }
            }
        }
        
        // Custom validation
        const customValidation = field.getAttribute('data-validation');
        if (customValidation && this.validationRules.has(customValidation)) {
            const rule = this.validationRules.get(customValidation);
            if (!rule.test(value)) {
                validationResults.push(rule.message);
            }
        }
        
        // Update field state
        const isValid = validationResults.length === 0;
        this.updateFieldState(field, isValid, validationResults[0] || '');
        
        return isValid;
    }
    
    updateFieldState(field, isValid, message) {
        // Update classes
        field.classList.toggle('is-valid', isValid && field.value.trim());
        field.classList.toggle('is-invalid', !isValid);
        
        // Update ARIA attributes
        field.setAttribute('aria-invalid', !isValid);
        
        // Update feedback message
        this.updateFeedbackMessage(field, message, isValid);
    }
    
    updateFeedbackMessage(field, message, isValid) {
        let feedback = field.parentNode.querySelector('.invalid-feedback, .valid-feedback');
        
        if (!feedback) {
            feedback = document.createElement('div');
            field.parentNode.appendChild(feedback);
        }
        
        feedback.className = isValid ? 'valid-feedback' : 'invalid-feedback';
        feedback.textContent = message;
        
        // Update ARIA describedby
        const feedbackId = feedback.id || `feedback-${Math.random().toString(36).substr(2, 9)}`;
        feedback.id = feedbackId;
        
        const describedBy = field.getAttribute('aria-describedby') || '';
        if (!describedBy.includes(feedbackId)) {
            field.setAttribute('aria-describedby', `${describedBy} ${feedbackId}`.trim());
        }
    }
    
    addFormSubmissionEnhancements(form) {
        form.addEventListener('submit', (e) => {
            const isValid = this.validateForm(form);
            if (!isValid) {
                e.preventDefault();
                this.focusFirstInvalidField(form);
            }
        });
    }
    
    validateForm(form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    focusFirstInvalidField(form) {
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) {
            firstInvalid.focus();
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    async enhanceImageHandling() {
        const images = document.querySelectorAll('img');
        
        for (const img of images) {
            if (this.enhancedImages.has(img)) continue;
            
            await this.enhanceImage(img);
            this.enhancedImages.add(img);
        }
    }
    
    async enhanceImage(img) {
        // Add loading states
        this.addImageLoadingStates(img);
        
        // Add lazy loading if not already present
        if (!img.hasAttribute('loading')) {
            img.setAttribute('loading', 'lazy');
        }
        
        // Add error handling
        this.addImageErrorHandling(img);
        
        // Add accessibility enhancements
        this.addImageAccessibility(img);
    }
    
    addImageLoadingStates(img) {
        if (!img.complete) {
            img.style.backgroundColor = 'var(--loading-bg, #f8f9fa)';
            img.style.minHeight = '200px';
            img.classList.add('loading');
        }
        
        img.addEventListener('load', () => {
            img.style.backgroundColor = 'transparent';
            img.style.minHeight = 'auto';
            img.classList.remove('loading');
            img.classList.add('loaded');
        });
    }
    
    addImageErrorHandling(img) {
        img.addEventListener('error', () => {
            img.style.backgroundColor = 'var(--error-bg, #e9ecef)';
            img.classList.add('error');
            
            if (!img.alt) {
                img.alt = 'Resim yüklenemedi';
            }
            
            // Try to load a fallback image
            const fallback = img.getAttribute('data-fallback');
            if (fallback && img.src !== fallback) {
                img.src = fallback;
            }
        });
    }
    
    addImageAccessibility(img) {
        // Ensure alt text is present
        if (!img.hasAttribute('alt')) {
            img.setAttribute('alt', '');
        }
        
        // Add role for decorative images
        if (img.alt === '') {
            img.setAttribute('role', 'presentation');
        }
    }
    
    async enhanceNavigation() {
        // Add active states to navigation
        this.updateActiveNavigation();
        
        // Add keyboard navigation enhancements
        this.enhanceKeyboardNavigation();
        
        // Add mobile navigation enhancements
        this.enhanceMobileNavigation();
    }
    
    updateActiveNavigation() {
        const navLinks = document.querySelectorAll('.nav-link, .navbar-nav .nav-link');
        const currentPath = window.location.pathname;
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPath || (href !== '/' && currentPath.startsWith(href))) {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
            } else {
                link.classList.remove('active');
                link.removeAttribute('aria-current');
            }
        });
    }
    
    enhanceKeyboardNavigation() {
        const navItems = document.querySelectorAll('.navbar-nav .nav-item');
        
        navItems.forEach((item, index) => {
            const link = item.querySelector('.nav-link');
            if (link) {
                link.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                        e.preventDefault();
                        const nextItem = navItems[index + 1] || navItems[0];
                        nextItem.querySelector('.nav-link')?.focus();
                    } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                        e.preventDefault();
                        const prevItem = navItems[index - 1] || navItems[navItems.length - 1];
                        prevItem.querySelector('.nav-link')?.focus();
                    }
                });
            }
        });
    }
    
    enhanceMobileNavigation() {
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        if (navbarToggler && navbarCollapse) {
            // Add ARIA attributes
            navbarToggler.setAttribute('aria-expanded', 'false');
            navbarToggler.setAttribute('aria-controls', navbarCollapse.id || 'navbarNav');
            
            // Update ARIA state on toggle
            navbarToggler.addEventListener('click', () => {
                const isExpanded = navbarCollapse.classList.contains('show');
                navbarToggler.setAttribute('aria-expanded', !isExpanded);
            });
        }
    }
    
    async enhanceInteractivity() {
        // Add hover effects
        this.addHoverEffects();
        
        // Add click feedback
        this.addClickFeedback();
        
        // Add smooth transitions
        this.addSmoothTransitions();
    }
    
    addHoverEffects() {
        const interactiveElements = document.querySelectorAll(
            'button, .btn, .card, .nav-link, [role="button"]'
        );
        
        interactiveElements.forEach(element => {
            element.addEventListener('mouseenter', () => {
                element.classList.add('hovered');
            });
            
            element.addEventListener('mouseleave', () => {
                element.classList.remove('hovered');
            });
        });
    }
    
    addClickFeedback() {
        const clickableElements = document.querySelectorAll(
            'button, .btn, [role="button"], .clickable'
        );
        
        clickableElements.forEach(element => {
            element.addEventListener('mousedown', () => {
                element.classList.add('pressed');
            });
            
            element.addEventListener('mouseup', () => {
                element.classList.remove('pressed');
            });
            
            element.addEventListener('mouseleave', () => {
                element.classList.remove('pressed');
            });
        });
    }
    
    addSmoothTransitions() {
        const style = document.createElement('style');
        style.textContent = `
            .focused {
                transform: scale(1.02);
                transition: transform 0.2s ease;
            }
            
            .hovered {
                transform: translateY(-2px);
                transition: transform 0.2s ease;
            }
            
            .pressed {
                transform: scale(0.98);
                transition: transform 0.1s ease;
            }
            
            .loading {
                opacity: 0.7;
                transition: opacity 0.3s ease;
            }
            
            .loaded {
                opacity: 1;
                transition: opacity 0.3s ease;
            }
            
            .character-counter {
                transition: color 0.2s ease;
            }
        `;
        document.head.appendChild(style);
    }
    
    addCustomValidationRule(name, rule) {
        this.validationRules.set(name, rule);
    }
    
    destroy() {
        this.validationRules.clear();
        this.enhancedForms.clear();
        this.enhancedImages.clear();
        this.isInitialized = false;
    }
}

// Create global instance
const progressiveEnhancementManager = new ProgressiveEnhancementManager();

// Legacy functions for backward compatibility
function initProgressiveEnhancement() {
    return progressiveEnhancementManager.init();
}

function enhanceFormValidation() {
    return progressiveEnhancementManager.enhanceFormValidation();
}

function validateField(field) {
    return progressiveEnhancementManager.validateField(field);
}

function enhanceImageHandling() {
    return progressiveEnhancementManager.enhanceImageHandling();
}

function enhanceNavigation() {
    return progressiveEnhancementManager.enhanceNavigation();
}

// Modern Error Handling and Service Worker Manager
class ErrorHandlingManager {
    constructor() {
        this.errorQueue = [];
        this.isOnline = navigator.onLine;
        this.errorHandlers = new Map();
        this.retryAttempts = new Map();
        this.maxRetries = 3;
        this.isInitialized = false;
    }
    
    async init() {
        if (this.isInitialized) return;
        
        try {
            await Promise.all([
                this.initGlobalErrorHandling(),
                this.initNetworkMonitoring(),
                this.initServiceWorker(),
                this.initPerformanceMonitoring()
            ]);
            
            this.isInitialized = true;
            console.log('ErrorHandlingManager initialized successfully');
        } catch (error) {
            console.error('Failed to initialize ErrorHandlingManager:', error);
        }
    }
    
    async initGlobalErrorHandling() {
        // Enhanced global error handler
        window.addEventListener('error', (e) => {
            this.handleGlobalError({
                type: 'javascript',
                error: e.error,
                message: e.message,
                filename: e.filename,
                lineno: e.lineno,
                colno: e.colno,
                stack: e.error?.stack,
                timestamp: Date.now(),
                userAgent: navigator.userAgent,
                url: window.location.href
            });
        });
        
        // Enhanced unhandled promise rejection handler
        window.addEventListener('unhandledrejection', (e) => {
            this.handlePromiseRejection({
                type: 'promise',
                reason: e.reason,
                promise: e.promise,
                timestamp: Date.now(),
                url: window.location.href,
                stack: e.reason?.stack
            });
            e.preventDefault();
        });
        
        // Resource loading errors
        window.addEventListener('error', (e) => {
            if (e.target !== window) {
                this.handleResourceError({
                    type: 'resource',
                    element: e.target.tagName,
                    source: e.target.src || e.target.href,
                    timestamp: Date.now()
                });
            }
        }, true);
    }
    
    async handleGlobalError(errorInfo) {
        console.error('Global Error:', errorInfo);
        
        // Add to error queue for potential retry
        this.errorQueue.push(errorInfo);
        
        // Notify user if critical
        if (this.isCriticalError(errorInfo)) {
            this.showErrorNotification(
                'Bir hata oluştu. Sayfa yeniden yüklenecek.',
                'error'
            );
            
            // Auto-reload for critical errors after delay
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        }
        
        // Send to analytics if available
        this.sendErrorToAnalytics(errorInfo);
    }
    
    async handlePromiseRejection(rejectionInfo) {
        console.error('Unhandled Promise Rejection:', rejectionInfo);
        
        // Try to recover from common promise rejections
        if (this.canRecover(rejectionInfo)) {
            await this.attemptRecovery(rejectionInfo);
        } else {
            this.showErrorNotification(
                'Bir işlem tamamlanamadı. Lütfen tekrar deneyin.',
                'warning'
            );
        }
        
        this.sendErrorToAnalytics(rejectionInfo);
    }
    
    async handleResourceError(resourceInfo) {
        console.warn('Resource Loading Error:', resourceInfo);
        
        // Attempt to reload critical resources
        if (this.isCriticalResource(resourceInfo)) {
            await this.retryResourceLoad(resourceInfo);
        }
    }
    
    isCriticalError(errorInfo) {
        const criticalPatterns = [
            /Cannot read property/,
            /is not defined/,
            /Network Error/,
            /ChunkLoadError/
        ];
        
        return criticalPatterns.some(pattern => 
            pattern.test(errorInfo.message || '')
        );
    }
    
    isCriticalResource(resourceInfo) {
        const criticalExtensions = ['.css', '.js'];
        return criticalExtensions.some(ext => 
            resourceInfo.source?.includes(ext)
        );
    }
    
    canRecover(rejectionInfo) {
        const recoverablePatterns = [
            /fetch/i,
            /network/i,
            /timeout/i
        ];
        
        return recoverablePatterns.some(pattern => 
            pattern.test(rejectionInfo.reason?.message || '')
        );
    }
    
    async attemptRecovery(rejectionInfo) {
        const errorKey = this.getErrorKey(rejectionInfo);
        const attempts = this.retryAttempts.get(errorKey) || 0;
        
        if (attempts < this.maxRetries) {
            this.retryAttempts.set(errorKey, attempts + 1);
            
            // Wait before retry with exponential backoff
            const delay = Math.pow(2, attempts) * 1000;
            await new Promise(resolve => setTimeout(resolve, delay));
            
            console.log(`Attempting recovery for ${errorKey}, attempt ${attempts + 1}`);
            return true;
        }
        
        return false;
    }
    
    async retryResourceLoad(resourceInfo) {
        const errorKey = resourceInfo.source;
        const attempts = this.retryAttempts.get(errorKey) || 0;
        
        if (attempts < this.maxRetries) {
            this.retryAttempts.set(errorKey, attempts + 1);
            
            setTimeout(() => {
                if (resourceInfo.element === 'SCRIPT') {
                    const script = document.createElement('script');
                    script.src = resourceInfo.source;
                    document.head.appendChild(script);
                } else if (resourceInfo.element === 'LINK') {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = resourceInfo.source;
                    document.head.appendChild(link);
                }
            }, 1000 * (attempts + 1));
        }
    }
    
    getErrorKey(errorInfo) {
        return `${errorInfo.type}-${errorInfo.reason?.message || errorInfo.message || 'unknown'}`;
    }
    
    async initNetworkMonitoring() {
        // Enhanced network status monitoring
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.handleNetworkStatusChange(true);
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.handleNetworkStatusChange(false);
        });
        
        // Monitor connection quality
        if ('connection' in navigator) {
            navigator.connection.addEventListener('change', () => {
                this.handleConnectionChange();
            });
        }
    }
    
    handleNetworkStatusChange(isOnline) {
        if (isOnline) {
            this.showErrorNotification(
                'İnternet bağlantısı yeniden kuruldu.',
                'success'
            );
            
            // Retry failed requests
            this.retryFailedRequests();
        } else {
            this.showErrorNotification(
                'İnternet bağlantısı kesildi. Bazı özellikler çalışmayabilir.',
                'warning'
            );
        }
    }
    
    handleConnectionChange() {
        if ('connection' in navigator) {
            const connection = navigator.connection;
            const isSlowConnection = connection.effectiveType === 'slow-2g' || 
                                   connection.effectiveType === '2g';
            
            if (isSlowConnection) {
                this.showErrorNotification(
                    'Yavaş internet bağlantısı tespit edildi.',
                    'info'
                );
            }
        }
    }
    
    async retryFailedRequests() {
        // Implement retry logic for failed requests
        console.log('Retrying failed requests...');
    }
    
    async initServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            console.log('Service Worker not supported');
            return;
        }
        
        try {
            // Register service worker
            const registration = await navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            });
            
            console.log('Service Worker registered:', registration);
            
            // Handle service worker updates
            registration.addEventListener('updatefound', () => {
                this.handleServiceWorkerUpdate(registration);
            });
            
            // Listen for service worker messages
            navigator.serviceWorker.addEventListener('message', (event) => {
                this.handleServiceWorkerMessage(event);
            });
            
            // Check for existing service worker
            if (registration.active) {
                console.log('Service Worker is active');
            }
            
        } catch (error) {
            console.error('Service Worker registration failed:', error);
            this.handleServiceWorkerError(error);
        }
    }
    
    handleServiceWorkerUpdate(registration) {
        const newWorker = registration.installing;
        
        if (newWorker) {
            newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                    // New service worker is available
                    this.showUpdateNotification();
                }
            });
        }
    }
    
    handleServiceWorkerMessage(event) {
        const { type, payload } = event.data;
        
        switch (type) {
            case 'CACHE_UPDATED':
                console.log('Cache updated:', payload);
                break;
            case 'OFFLINE_FALLBACK':
                this.showErrorNotification(
                    'Çevrimdışı modda çalışıyorsunuz.',
                    'info'
                );
                break;
            default:
                console.log('Service Worker message:', event.data);
        }
    }
    
    handleServiceWorkerError(error) {
        console.error('Service Worker error:', error);
        
        // Fallback for when service worker fails
        this.initFallbackCaching();
    }
    
    showUpdateNotification() {
        const notification = this.showErrorNotification(
            'Yeni bir sürüm mevcut. Güncellemek için tıklayın.',
            'info',
            0 // Don't auto-hide
        );
        
        notification.style.cursor = 'pointer';
        notification.addEventListener('click', () => {
            window.location.reload();
        });
    }
    
    async initPerformanceMonitoring() {
        // Monitor long tasks
        if ('PerformanceObserver' in window) {
            try {
                const observer = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (entry.duration > 50) { // Long task threshold
                            console.warn('Long task detected:', entry);
                        }
                    }
                });
                
                observer.observe({ entryTypes: ['longtask'] });
            } catch (error) {
                console.log('Long task monitoring not available:', error);
            }
        }
        
        // Monitor memory usage
        if ('memory' in performance) {
            setInterval(() => {
                const memory = performance.memory;
                const usedPercent = (memory.usedJSHeapSize / memory.jsHeapSizeLimit) * 100;
                
                if (usedPercent > 90) {
                    console.warn('High memory usage detected:', usedPercent + '%');
                }
            }, 30000); // Check every 30 seconds
        }
    }
    
    initFallbackCaching() {
        // Simple fallback caching mechanism
        console.log('Initializing fallback caching...');
    }
    
    showErrorNotification(message, type = 'error', duration = 5000) {
        // Use existing notification system
        if (typeof showNotification === 'function') {
            return showNotification(message, type, duration);
        } else {
            // Fallback notification
            console.log(`${type.toUpperCase()}: ${message}`);
            alert(message);
        }
    }
    
    sendErrorToAnalytics(errorInfo) {
        // Send error to analytics service if available
        if (typeof gtag === 'function') {
            gtag('event', 'exception', {
                description: errorInfo.message || errorInfo.reason?.message,
                fatal: this.isCriticalError(errorInfo)
            });
        }
    }
    
    addErrorHandler(type, handler) {
        if (!this.errorHandlers.has(type)) {
            this.errorHandlers.set(type, []);
        }
        this.errorHandlers.get(type).push(handler);
    }
    
    removeErrorHandler(type, handler) {
        if (this.errorHandlers.has(type)) {
            const handlers = this.errorHandlers.get(type);
            const index = handlers.indexOf(handler);
            if (index > -1) {
                handlers.splice(index, 1);
            }
        }
    }
    
    getErrorReport() {
        return {
            errorQueue: this.errorQueue,
            retryAttempts: Object.fromEntries(this.retryAttempts),
            isOnline: this.isOnline,
            timestamp: Date.now()
        };
    }
    
    clearErrorQueue() {
        this.errorQueue = [];
        this.retryAttempts.clear();
    }
    
    destroy() {
        this.errorHandlers.clear();
        this.clearErrorQueue();
        this.isInitialized = false;
    }
}

// Create global instance
const errorHandlingManager = new ErrorHandlingManager();

// Legacy functions for backward compatibility
function initErrorHandling() {
    return errorHandlingManager.init();
}

function initServiceWorker() {
    return errorHandlingManager.initServiceWorker();
}

// Package Modal Functions
function openPackageModal(packageName, packagePrice, packageFeatures) {
    const modal = document.getElementById('packageModal');
    const modalLabel = document.getElementById('packageModalLabel');
    const packageDetails = document.getElementById('packageDetails');
    
    if (modal && modalLabel && packageDetails) {
        modalLabel.textContent = packageName;
        
        let featuresHtml = '';
        if (packageFeatures && Array.isArray(packageFeatures)) {
            featuresHtml = '<ul class="list-unstyled">';
            packageFeatures.forEach(feature => {
                featuresHtml += `<li><i class="fas fa-check text-success me-2"></i>${feature}</li>`;
            });
            featuresHtml += '</ul>';
        }
        
        packageDetails.innerHTML = `
            <div class="package-info">
                <h5>${packageName}</h5>
                <div class="price mb-3">
                    <span class="h4 text-primary">${packagePrice}</span>
                </div>
                ${featuresHtml}
            </div>
        `;
        
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }
}

// Pricing Toggle Function
function togglePricing(isYearly = false) {
    const monthlyPrices = document.querySelectorAll('.monthly-price');
    const yearlyPrices = document.querySelectorAll('.yearly-price');
    
    if (isYearly) {
        monthlyPrices.forEach(price => price.style.display = 'none');
        yearlyPrices.forEach(price => price.style.display = 'block');
    } else {
        monthlyPrices.forEach(price => price.style.display = 'block');
        yearlyPrices.forEach(price => price.style.display = 'none');
    }
}

// Notification Function
function showNotification(message, type = 'info', duration = 5000) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.toast-notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `toast-notification toast-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10001;
        max-width: 400px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        animation: slideInRight 0.3s ease-out;
        cursor: pointer;
    `;
    
    // Set background color based on type
    switch(type) {
        case 'success':
            notification.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
            break;
        case 'error':
            notification.style.background = 'linear-gradient(135deg, #dc3545, #e83e8c)';
            break;
        case 'warning':
            notification.style.background = 'linear-gradient(135deg, #ffc107, #fd7e14)';
            break;
        default:
            notification.style.background = 'linear-gradient(135deg, #17a2b8, #6f42c1)';
    }
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <span class="me-2">${getNotificationIcon(type)}</span>
            <span>${message}</span>
            <button type="button" class="btn-close btn-close-white ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after duration
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        }
    }, duration);
    
    // Remove on click
    notification.addEventListener('click', () => {
        notification.remove();
    });
}

// Get notification icon based on type
function getNotificationIcon(type) {
    switch(type) {
        case 'success':
            return '<i class="fas fa-check-circle"></i>';
        case 'error':
            return '<i class="fas fa-exclamation-circle"></i>';
        case 'warning':
            return '<i class="fas fa-exclamation-triangle"></i>';
        default:
            return '<i class="fas fa-info-circle"></i>';
    }
}

// Google Maps Functions
function openGoogleMaps() {
    const address = 'Xocalı prospekti 11, Block A, 3-cü mərtəbə, Bakı 1008, Azərbaycan';
    const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
    window.open(url, '_blank');
}

function getDirections() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const destination = 'Xocalı prospekti 11, Block A, 3-cü mərtəbə, Bakı 1008, Azərbaycan';
            const url = `https://www.google.com/maps/dir/${lat},${lng}/${encodeURIComponent(destination)}`;
            window.open(url, '_blank');
        }, function() {
            showNotification('Konum bilgisi alınamadı. Lütfen tarayıcı ayarlarınızı kontrol edin.', 'error');
        });
    } else {
        showNotification('Tarayıcınız konum hizmetlerini desteklemiyor.', 'error');
    }
}

// Scroll-triggered Animations
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.fade-up, .fade-left, .fade-right, .scale-up');
    
    if (animatedElements.length === 0) return;
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
}

// Add scroll animations to existing elements
function addScrollAnimationsToElements() {
    // Add animations to service cards
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach((card, index) => {
        card.classList.add('fade-up');
        card.style.transitionDelay = `${index * 0.1}s`;
    });
    

    
    // Testimonial cards removed - section completely removed
    
    // Add animations to stats
    const statItems = document.querySelectorAll('.stat-item');
    statItems.forEach((item, index) => {
        item.classList.add(index % 2 === 0 ? 'fade-left' : 'fade-right');
        item.style.transitionDelay = `${index * 0.1}s`;
    });
}

// Global olarak erişilebilir hale getir
window.NextCodeUtils = utils;
window.validateContactForm = validateContactForm;
window.submitContactForm = submitContactForm;
window.showAlert = showAlert;
window.openPackageModal = openPackageModal;
window.togglePricing = togglePricing;
window.showNotification = showNotification;
window.openGoogleMaps = openGoogleMaps;
window.getDirections = getDirections;
window.initScrollAnimations = initScrollAnimations;
window.addScrollAnimationsToElements = addScrollAnimationsToElements;