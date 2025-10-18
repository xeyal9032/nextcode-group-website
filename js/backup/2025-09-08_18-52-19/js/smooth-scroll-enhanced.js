// Enhanced Smooth Scrolling System
// NextCode Group - Advanced Smooth Scrolling with Performance Optimization

class EnhancedSmoothScroll {
    constructor() {
        this.isScrolling = false;
        this.scrollTarget = null;
        this.scrollStartTime = 0;
        this.scrollDuration = 800;
        this.easingFunction = 'easeInOutCubic';
        this.performanceMode = 'auto';
        this.isInitialized = false;
        
        // Performance monitoring
        this.scrollEvents = [];
        this.lastScrollTime = 0;
        
        this.init();
    }
    
    init() {
        console.log('🚀 Enhanced Smooth Scroll başlatılıyor...');
        
        // Performance mode detection
        this.detectPerformanceMode();
        
        // Setup smooth scrolling
        this.setupSmoothScrolling();
        
        // Setup performance monitoring
        this.setupPerformanceMonitoring();
        
        this.isInitialized = true;
        console.log('✅ Enhanced Smooth Scroll aktif');
    }
    
    // Performance mode detection
    detectPerformanceMode() {
        if ('connection' in navigator) {
            const connection = navigator.connection;
            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                this.performanceMode = 'low';
                this.scrollDuration = 1200; // Slower for low-end devices
            } else if (connection.effectiveType === '3g') {
                this.performanceMode = 'medium';
                this.scrollDuration = 1000;
            } else {
                this.performanceMode = 'high';
                this.scrollDuration = 800;
            }
        }
        
        // Check device capabilities
        if ('hardwareConcurrency' in navigator && navigator.hardwareConcurrency < 4) {
            this.performanceMode = 'low';
            this.scrollDuration = 1200;
        }
        
        console.log('📱 Performance Mode:', this.performanceMode);
    }
    
    // Setup smooth scrolling
    setupSmoothScrolling() {
        // Intercept all internal links
        document.addEventListener('click', (e) => {
            const target = e.target.closest('a[href^="#"]');
            if (target && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                this.smoothScrollTo(target.getAttribute('href'));
            }
        });
        
        // Add smooth scroll to programmatic navigation
        window.smoothScrollTo = (target) => this.smoothScrollTo(target);
        
        // Keyboard navigation support
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Home') {
                e.preventDefault();
                this.smoothScrollTo('#top');
            } else if (e.key === 'End') {
                e.preventDefault();
                this.smoothScrollTo('#bottom');
            }
        });
    }
    
    // Main smooth scroll function
    smoothScrollTo(target) {
        if (this.isScrolling) return;
        
        const targetElement = this.getTargetElement(target);
        if (!targetElement) return;
        
        this.isScrolling = true;
        this.scrollTarget = targetElement;
        this.scrollStartTime = performance.now();
        
        // Add scrolling class
        document.body.classList.add('smooth-scrolling');
        
        // Start smooth scroll animation
        this.animateScroll();
    }
    
    // Get target element
    getTargetElement(target) {
        if (target === '#top') {
            return document.documentElement;
        } else if (target === '#bottom') {
            return document.body;
        } else if (target.startsWith('#')) {
            return document.querySelector(target);
        }
        return null;
    }
    
    // Animate scroll
    animateScroll() {
        const currentTime = performance.now();
        const elapsed = currentTime - this.scrollStartTime;
        const progress = Math.min(elapsed / this.scrollDuration, 1);
        
        // Apply easing
        const easedProgress = this.applyEasing(progress);
        
        // Calculate scroll position
        const startPosition = window.pageYOffset;
        const targetPosition = this.getTargetPosition();
        const distance = targetPosition - startPosition;
        const currentPosition = startPosition + (distance * easedProgress);
        
        // Apply scroll
        window.scrollTo(0, currentPosition);
        
        // Continue animation or finish
        if (progress < 1) {
            requestAnimationFrame(() => this.animateScroll());
        } else {
            this.finishScroll();
        }
    }
    
    // Get target scroll position
    getTargetPosition() {
        if (this.scrollTarget === document.documentElement) {
            return 0;
        } else if (this.scrollTarget === document.body) {
            return document.body.scrollHeight - window.innerHeight;
        } else {
            const rect = this.scrollTarget.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            return scrollTop + rect.top - 80; // Account for fixed header
        }
    }
    
    // Apply easing function
    applyEasing(progress) {
        switch (this.easingFunction) {
            case 'easeInOutCubic':
                return progress < 0.5 
                    ? 4 * progress * progress * progress 
                    : 1 - Math.pow(-2 * progress + 2, 3) / 2;
            case 'easeOutQuart':
                return 1 - Math.pow(1 - progress, 4);
            case 'easeInOutQuart':
                return progress < 0.5 
                    ? 8 * progress * progress * progress * progress 
                    : 1 - Math.pow(-2 * progress + 2, 4) / 2;
            default:
                return progress;
        }
    }
    
    // Finish scroll
    finishScroll() {
        this.isScrolling = false;
        this.scrollTarget = null;
        
        // Remove scrolling class
        document.body.classList.remove('smooth-scrolling');
        
        // Dispatch scroll complete event
        window.dispatchEvent(new CustomEvent('smoothScrollComplete'));
        
        // Performance tracking
        this.trackScrollPerformance();
    }
    
    // Setup performance monitoring
    setupPerformanceMonitoring() {
        let scrollTimeout;
        
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            
            const currentTime = performance.now();
            const timeSinceLastScroll = currentTime - this.lastScrollTime;
            
            if (timeSinceLastScroll > 16) { // 60fps threshold
                this.scrollEvents.push({
                    timestamp: currentTime,
                    performance: timeSinceLastScroll
                });
            }
            
            this.lastScrollTime = currentTime;
            
            // Clean up old events
            if (this.scrollEvents.length > 100) {
                this.scrollEvents = this.scrollEvents.slice(-50);
            }
            
            scrollTimeout = setTimeout(() => {
                this.analyzeScrollPerformance();
            }, 1000);
        });
    }
    
    // Track scroll performance
    trackScrollPerformance() {
        const scrollTime = performance.now() - this.scrollStartTime;
        
        // Send to analytics if available
        if (window.gtag) {
            window.gtag('event', 'smooth_scroll', {
                event_category: 'navigation',
                event_label: this.performanceMode,
                value: Math.round(scrollTime)
            });
        }
        
        console.log('📊 Smooth Scroll Performance:', {
            duration: scrollTime.toFixed(2) + 'ms',
            mode: this.performanceMode
        });
    }
    
    // Analyze scroll performance
    analyzeScrollPerformance() {
        if (this.scrollEvents.length < 10) return;
        
        const avgPerformance = this.scrollEvents.reduce((sum, event) => sum + event.performance, 0) / this.scrollEvents.length;
        
        // Adjust performance mode if needed
        if (avgPerformance > 20 && this.performanceMode !== 'low') {
            this.performanceMode = 'low';
            this.scrollDuration = 1200;
            console.log('⚠️ Performance mode downgraded to low');
        } else if (avgPerformance < 10 && this.performanceMode === 'low') {
            this.performanceMode = 'high';
            this.scrollDuration = 800;
            console.log('✅ Performance mode upgraded to high');
        }
    }
    
    // Public methods
    setEasing(easing) {
        this.easingFunction = easing;
    }
    
    setDuration(duration) {
        this.scrollDuration = duration;
    }
    
    // Destroy
    destroy() {
        this.isScrolling = false;
        this.scrollTarget = null;
        document.body.classList.remove('smooth-scrolling');
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    window.enhancedSmoothScroll = new EnhancedSmoothScroll();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedSmoothScroll;
}
