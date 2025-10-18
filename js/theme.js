// Enhanced Theme Management System with Smooth Animations

// Prevent duplicate declaration
if (typeof themeConfig === 'undefined') {
// Theme configuration with extended options
const themeConfig = {
    themes: {
        light: {
            name: 'Açık Tema',
            icon: '☀️',
            colors: {
                primary: '#3498db',
                primaryHover: '#2980b9',
                secondary: '#2ecc71',
                secondaryHover: '#27ae60',
                background: '#ffffff',
                backgroundSecondary: '#f8f9fa',
                surface: '#ffffff',
                surfaceElevated: '#ffffff',
                text: '#2c3e50',
                textSecondary: '#7f8c8d',
                textMuted: '#95a5a6',
                border: '#e9ecef',
                borderLight: '#f1f3f4',
                shadow: 'rgba(0, 0, 0, 0.1)',
                shadowHeavy: 'rgba(0, 0, 0, 0.15)',
                success: '#27ae60',
                warning: '#f39c12',
                error: '#e74c3c',
                info: '#3498db'
            },
            gradients: {
                primary: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                secondary: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                hero: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
            }
        },
        dark: {
            name: 'Koyu Tema',
            icon: '🌙',
            colors: {
                primary: '#3498db',
                primaryHover: '#5dade2',
                secondary: '#2ecc71',
                secondaryHover: '#58d68d',
                background: '#0d1117',
                backgroundSecondary: '#161b22',
                surface: '#21262d',
                surfaceElevated: '#30363d',
                text: '#f0f6fc',
                textSecondary: '#8b949e',
                textMuted: '#6e7681',
                border: '#30363d',
                borderLight: '#21262d',
                shadow: 'rgba(0, 0, 0, 0.3)',
                shadowHeavy: 'rgba(0, 0, 0, 0.5)',
                success: '#238636',
                warning: '#d29922',
                error: '#da3633',
                info: '#1f6feb'
            },
            gradients: {
                primary: 'linear-gradient(135deg, #434343 0%, #000000 100%)',
                secondary: 'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)',
                hero: 'linear-gradient(135deg, #232526 0%, #414345 100%)'
            }
        },
        auto: {
            name: 'Sistem Teması',
            icon: '🔄',
            followSystem: true
        },
        highContrast: {
            name: 'Yüksek Kontrast',
            icon: '⚫',
            colors: {
                primary: '#0000ff',
                primaryHover: '#0000cc',
                secondary: '#008000',
                secondaryHover: '#006600',
                background: '#ffffff',
                backgroundSecondary: '#f0f0f0',
                surface: '#ffffff',
                surfaceElevated: '#ffffff',
                text: '#000000',
                textSecondary: '#000000',
                textMuted: '#333333',
                border: '#000000',
                borderLight: '#666666',
                shadow: 'rgba(0, 0, 0, 0.8)',
                shadowHeavy: 'rgba(0, 0, 0, 1)',
                success: '#008000',
                warning: '#ff8c00',
                error: '#ff0000',
                info: '#0000ff'
            },
            gradients: {
                primary: 'linear-gradient(135deg, #000000 0%, #333333 100%)',
                secondary: 'linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%)',
                hero: 'linear-gradient(135deg, #000000 0%, #333333 100%)'
            }
        }
    },
    defaultTheme: 'auto',
    storageKey: 'preferred-theme',
    transitionDuration: 300,
    enableAnimations: true,
    enableSystemSync: true
};

class ThemeManager {
    constructor() {
        this.mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        this.contrastQuery = window.matchMedia('(prefers-contrast: high)');
        this.motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        
        this.observers = [];
        this.transitionTimeout = null;
        this.isTransitioning = false;
        
        // Initialize theme immediately
        this.currentTheme = this.getSavedTheme() || 'light';
        this.systemTheme = this.getSystemTheme();
        
        this.init();
    }
    
    init() {
        this.detectSystemPreferences();
        
        // Apply saved theme immediately on page load
        const savedTheme = this.getSavedTheme();
        if (savedTheme) {
            this.setTheme(savedTheme);
        } else {
            this.setTheme('light');
        }
        
        this.setupEnhancedThemeToggle();
        this.listenForSystemChanges();
        this.setupAccessibilityFeatures();
        this.initializeThemeAnimations();
        this.dispatchThemeEvent();
        
        // Performance monitoring
        if (window.performanceMonitor) {
            window.performanceMonitor.mark('themeManagerInit');
        }
    }
    
    detectSystemPreferences() {
        // Detect high contrast preference
        if (this.contrastQuery.matches && !this.getSavedTheme()) {
            this.currentTheme = 'highContrast';
        }
        
        // Detect reduced motion preference
        if (this.motionQuery.matches) {
            themeConfig.enableAnimations = false;
        }
    }

    getSavedTheme() {
        return localStorage.getItem('theme') || localStorage.getItem(themeConfig.storageKey);
    }

    getSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    setupEnhancedThemeToggle() {
        // Setup theme toggle button
        this.setupThemeToggle();
        
        // Listen for system theme changes
        this.setupSystemThemeListener();
        
        // Update theme icon based on current theme
        this.updateThemeIcon();
    }

    setupAccessibilityFeatures() {
        // Add accessibility enhancements
    }

    initializeThemeAnimations() {
        // Initialize theme transition animations
    }

    listenForSystemChanges() {
        // Enhanced system change listening
    }

    applyTheme(theme) {
        // Apply the specified theme
        this.setTheme(theme);
    }

    dispatchThemeEvent() {
        // Dispatch theme change event
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: this.currentTheme } }));
    }

    setupThemeToggle() {
        const themeToggle = document.getElementById('themeToggle');
        
        if (themeToggle) {
            // Remove any existing event listeners
            themeToggle.removeEventListener('click', this.handleThemeToggle);
            
            // Add new event listener
            this.handleThemeToggle = (e) => {
                e.preventDefault();
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                this.setTheme(newTheme);
                
                // Add click animation
                themeToggle.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    themeToggle.style.transform = 'scale(1)';
                }, 150);
            };
            
            themeToggle.addEventListener('click', this.handleThemeToggle);
            console.log('✅ Theme toggle event listener added');
        } else {
            console.warn('⚠️ Theme toggle button not found');
        }
    }

    setTheme(theme) {
        const html = document.documentElement;
        const previousTheme = this.currentTheme;
        
        // Enable smooth transitions
        this.enableSmoothTransitions();
        
        // Add loading state
        html.classList.add('theme-transitioning');
        
        // Set theme attribute with delay for smooth transition
        setTimeout(() => {
            html.setAttribute('data-theme', theme);
            
            // Update current theme
            this.currentTheme = theme;
            
            // Update icon with animation
            this.updateThemeIconWithAnimation();
            
            // Save to localStorage
            localStorage.setItem('theme', theme);
            
            // Dispatch theme change event
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme, previousTheme } }));
            
            // Remove loading state after transition
            setTimeout(() => {
                html.classList.remove('theme-transitioning');
            }, 300);
        }, 50);
    }
    
    updateThemeIcon() {
        const themeIcon = document.getElementById('themeIcon');
        if (themeIcon) {
            const currentTheme = document.documentElement.getAttribute('data-theme') || this.currentTheme;
            if (currentTheme === 'dark') {
                themeIcon.className = 'fas fa-sun';
                themeIcon.style.color = '#f39c12';
            } else {
                themeIcon.className = 'fas fa-moon';
                themeIcon.style.color = '#667eea';
            }
            console.log('✅ Theme icon updated to:', currentTheme);
        } else {
            console.warn('⚠️ Theme icon element not found');
        }
    }

    setupSystemThemeListener() {
        // Listen for system theme changes
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            
            mediaQuery.addEventListener('change', (e) => {
                // Only auto-switch if user hasn't manually set a preference
                if (!localStorage.getItem('theme')) {
                    this.setTheme(e.matches ? 'dark' : 'light');
                }
            });
        }
    }

    getCurrentTheme() {
        return document.documentElement.getAttribute('data-theme') || 'light';
    }

    // Enhanced smooth theme transition
    enableSmoothTransitions() {
        const css = document.createElement('style');
        css.textContent = `
            .theme-transitioning * {
                transition: background-color 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                           color 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                           border-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                           box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            
            .theme-transitioning {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            
            .theme-toggle-animation {
                animation: themeTogglePulse 0.3s ease-in-out;
            }
            
            @keyframes themeTogglePulse {
                0% { transform: scale(1); }
                50% { transform: scale(0.9); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(css);
        
        // Remove after transition
        setTimeout(() => {
            if (css.parentNode) {
                document.head.removeChild(css);
            }
        }, 500);
    }
    
    // Update theme icon with smooth animation
    updateThemeIconWithAnimation() {
        const themeIcon = document.getElementById('themeIcon');
        if (themeIcon) {
            // Add animation class
            themeIcon.classList.add('theme-toggle-animation');
            
            const currentTheme = this.currentTheme;
            if (currentTheme === 'dark') {
                themeIcon.className = 'fas fa-sun theme-toggle-animation';
                themeIcon.style.color = '#f39c12';
            } else {
                themeIcon.className = 'fas fa-moon theme-toggle-animation';
                themeIcon.style.color = '#667eea';
            }
            
            // Remove animation class after animation
            setTimeout(() => {
                themeIcon.classList.remove('theme-toggle-animation');
            }, 300);
            
            console.log('✅ Theme icon animated to:', currentTheme);
        } else {
            console.warn('⚠️ Theme icon element not found for animation');
        }
    }
}

// Enhanced animations for theme-aware components
class AnimationManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupScrollAnimations();
        this.setupHoverEffects();
        this.setupParallaxEffects();
    }

    setupScrollAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe elements with animation classes
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    }

    setupHoverEffects() {
        // Enhanced hover effects for cards
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-8px) scale(1.02)';
                card.style.boxShadow = '0 20px 40px var(--shadow-color)';
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
                card.style.boxShadow = '0 4px 15px var(--shadow-color)';
            });
        });
    }

    setupParallaxEffects() {
        // Subtle parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroSection = document.querySelector('.hero-section');
            
            if (heroSection) {
                const rate = scrolled * -0.5;
                heroSection.style.transform = `translateY(${rate}px)`;
            }
        });
    }
}

// Performance optimized scroll handler
class PerformanceManager {
    constructor() {
        this.ticking = false;
        this.init();
    }

    init() {
        this.setupOptimizedScrollHandler();
        this.setupLazyLoading();
    }

    setupOptimizedScrollHandler() {
        const handleScroll = () => {
            if (!this.ticking) {
                requestAnimationFrame(() => {
                    this.updateScrollEffects();
                    this.ticking = false;
                });
                this.ticking = true;
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
    }

    updateScrollEffects() {
        const scrolled = window.pageYOffset;
        
        // Update navbar background opacity
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            const opacity = Math.min(scrolled / 100, 1);
            navbar.style.backgroundColor = `rgba(255, 255, 255, ${0.95 * opacity})`;
        }
    }

    setupLazyLoading() {
        // Lazy load images
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

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize theme management
    window.themeManager = new ThemeManager();
    
    // Initialize animations
    window.animationManager = new AnimationManager();
    
    // Initialize performance optimizations
    window.performanceManager = new PerformanceManager();
    
    // Add smooth transitions after page load
    setTimeout(() => {
        if (window.themeManager && typeof window.themeManager.enableTransitions === 'function') {
            window.themeManager.enableTransitions();
        }
    }, 100);
});
} // End of themeConfig check

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ThemeManager, AnimationManager, PerformanceManager };
}