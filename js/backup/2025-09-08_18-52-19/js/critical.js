/**
 * Critical JavaScript - Essential functionality that must load immediately
 * This file contains only the most critical scripts needed for initial page render
 */

// Critical DOM utilities
const CriticalJS = {
    // Fast DOM ready function
    ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    },

    // Essential viewport detection
    isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    },

    // Critical image loading
    loadCriticalImages() {
        const criticalImages = document.querySelectorAll('img[data-critical]');
        criticalImages.forEach(img => {
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            }
        });
    },

    // Essential navigation functionality
    initNavigation() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;

        // Mobile menu toggle
        const toggleButton = navbar.querySelector('.navbar-toggler');
        const navbarCollapse = navbar.querySelector('.navbar-collapse');
        
        if (toggleButton && navbarCollapse) {
            toggleButton.addEventListener('click', () => {
                navbarCollapse.classList.toggle('show');
            });
        }

        // Scroll behavior for navbar
        let lastScrollY = window.scrollY;
        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;
            
            if (currentScrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            
            lastScrollY = currentScrollY;
        }, { passive: true });
    },

    // Critical form validation
    initCriticalForms() {
        const forms = document.querySelectorAll('form[data-critical]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    },

    // Performance monitoring for critical metrics
    initCriticalMetrics() {
        // Measure First Contentful Paint
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (entry.name === 'first-contentful-paint') {
                        console.log('🎨 First Contentful Paint:', entry.startTime + 'ms');
                    }
                }
            });
            observer.observe({ entryTypes: ['paint'] });
        }

        // Measure Largest Contentful Paint
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1];
                console.log('🖼️ Largest Contentful Paint:', lastEntry.startTime + 'ms');
            });
            observer.observe({ entryTypes: ['largest-contentful-paint'] });
        }
    },

    // Initialize all critical functionality
    init() {
        this.loadCriticalImages();
        this.initNavigation();
        this.initCriticalForms();
        this.initCriticalMetrics();
        
        console.log('✅ Critical JS initialized');
    }
};

// Auto-initialize when DOM is ready
CriticalJS.ready(() => {
    CriticalJS.init();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CriticalJS;
}

// Global access
window.CriticalJS = CriticalJS;