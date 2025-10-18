/**
 * Modern Design Theme Switcher
 * Professional theme switching system with smooth transitions
 */

class DesignSwitcher {
    constructor() {
        this.currentDesign = this.getStoredDesign() || 'default';
        this.isLoading = false;
        this.designs = {
            'default': {
                name: 'Varsayılan',
                fullName: 'Varsayılan Tasarım',
                cssFiles: [],
                icon: 'fas fa-home',
                description: 'Mevcut tasarım'
            },
            'alternative': {
                name: 'Alternatif',
                fullName: 'Alternatif Tasarım',
                cssFiles: ['css/alternative-theme.css', 'css/pricing-alternative.css'],
                icon: 'fas fa-palette',
                description: 'Farklı tasarım'
            }
        };
        
        this.init();
    }

    init() {
        this.loadDesignSwitcherCSS();
        this.loadDesign(this.currentDesign);
        this.bindEvents();
        this.updateButtonState();
        
        // Force night mode for alternative theme on init
        if (this.currentDesign === 'alternative') {
            // Completely disable theme.js functionality
            if (window.themeManager) {
                window.themeManager.setTheme = function() {}; // Disable theme switching
            }
            
            // Force dark mode and prevent theme changes
            document.documentElement.setAttribute('data-theme', 'dark');
            document.documentElement.style.setProperty('--bs-body-bg', '#0d1117');
            document.documentElement.style.setProperty('--bs-body-color', '#f0f6fc');
            document.body.style.backgroundColor = '#0d1117';
            document.body.style.color = '#f0f6fc';
            
            // Disable theme toggle button
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                themeToggle.style.display = 'none';
            }
            
            localStorage.setItem('theme', 'dark');
            localStorage.setItem('force-dark-mode', 'true');
        }
    }

    loadDesignSwitcherCSS() {
        if (!document.querySelector('link[href="css/design-switcher.css"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'css/design-switcher.css';
            document.head.appendChild(link);
        }
    }

    bindEvents() {
        // Toggle button click
        const toggleBtn = document.getElementById('designToggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (!this.isLoading) {
                    this.toggleDesign();
                }
            });
        }

        // Legacy dropdown option clicks (for compatibility)
        document.addEventListener('click', (e) => {
            if (e.target.closest('.design-option')) {
                e.preventDefault();
                const designKey = e.target.closest('.design-option').dataset.design;
                if (designKey && !this.isLoading) {
                    this.switchDesign(designKey);
                }
            }
        });

        // Keyboard shortcut (Ctrl + D)
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'd') {
                e.preventDefault();
                this.toggleDesign();
            }
        });
    }

    toggleDesign() {
        // Only allow switching to alternative theme (night mode only)
        if (this.currentDesign === 'default') {
            this.switchDesign('alternative');
        } else {
            this.switchDesign('default');
        }
    }

    async switchDesign(designKey) {
        if (!this.designs[designKey] || this.isLoading) {
            return;
        }

        this.isLoading = true;
        this.showLoadingState();
        this.showTransitionOverlay();

        try {
            // Add loading class to body
            document.body.classList.add('theme-loading', 'theme-changing');

            // Remove current design CSS files
            this.removeDesignCSS();

            // Load new design with smooth transition
            await this.loadDesign(designKey);
            
            // Update current design
            this.currentDesign = designKey;
            
            // Force night mode for alternative theme
            if (designKey === 'alternative') {
                // Completely disable theme.js functionality
                if (window.themeManager) {
                    window.themeManager.setTheme = function() {}; // Disable theme switching
                }
                
                // Force dark mode and prevent theme changes
                document.documentElement.setAttribute('data-theme', 'dark');
                document.documentElement.style.setProperty('--bs-body-bg', '#0d1117');
                document.documentElement.style.setProperty('--bs-body-color', '#f0f6fc');
                document.body.style.backgroundColor = '#0d1117';
                document.body.style.color = '#f0f6fc';
                
                // Disable theme toggle button
                const themeToggle = document.getElementById('themeToggle');
                if (themeToggle) {
                    themeToggle.style.display = 'none';
                }
                
                localStorage.setItem('theme', 'dark');
                localStorage.setItem('force-dark-mode', 'true');
            } else {
                // Re-enable theme.js for default theme
                if (window.themeManager) {
                    // Restore theme manager functionality
                    window.themeManager.setTheme = window.themeManager.setTheme || function(theme) {
                        document.documentElement.setAttribute('data-theme', theme);
                        localStorage.setItem('theme', theme);
                    };
                }
                
                // Re-enable theme toggle button
                const themeToggle = document.getElementById('themeToggle');
                if (themeToggle) {
                    themeToggle.style.display = 'block';
                }
                
                // Remove forced dark mode
                document.documentElement.style.removeProperty('--bs-body-bg');
                document.documentElement.style.removeProperty('--bs-body-color');
                document.body.style.removeProperty('background-color');
                document.body.style.removeProperty('color');
                
                localStorage.removeItem('force-dark-mode');
                
                const savedTheme = localStorage.getItem('theme') || 'light';
                if (window.themeManager) {
                    window.themeManager.setTheme(savedTheme);
                }
            }
            
            // Store preference
            this.storeDesign(designKey);
            
            // Update UI
            this.updateButtonState();
            this.updateDropdownState();
            
            // Show notification
            this.showNotification(`Tasarım değiştirildi: ${this.designs[designKey].fullName}`);
            
            // Trigger custom event
            this.triggerDesignChangeEvent(designKey);

            // Wait for transition to complete
            await new Promise(resolve => setTimeout(resolve, 600));

        } catch (error) {
            console.error('Error switching design:', error);
            this.showNotification('Tasarım değiştirilirken hata oluştu', 'error');
        } finally {
            this.isLoading = false;
            this.hideLoadingState();
            this.hideTransitionOverlay();
            document.body.classList.remove('theme-loading', 'theme-changing');
        }
    }

    async loadDesign(designKey) {
        const design = this.designs[designKey];
        if (!design) return Promise.resolve();

        const loadPromises = design.cssFiles.map(cssFile => this.loadCSS(cssFile));
        await Promise.all(loadPromises);

        // Add design class to body
        document.body.className = document.body.className.replace(/design-\w+/g, '');
        if (designKey !== 'default') {
            document.body.classList.add(`design-${designKey}`);
        }

        // Wait for styles to apply
        await new Promise(resolve => setTimeout(resolve, 100));
    }

    loadCSS(cssFile) {
        return new Promise((resolve, reject) => {
            // Check if CSS file is already loaded
            const existingLink = document.querySelector(`link[href="${cssFile}"]`);
            if (existingLink) {
                resolve();
                return;
            }

            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = cssFile;
            link.id = `design-css-${cssFile.replace(/[^a-zA-Z0-9]/g, '-')}`;
            
            // Add loading event
            link.onload = () => {
                console.log(`Design CSS loaded: ${cssFile}`);
                resolve();
            };
            
            link.onerror = () => {
                console.error(`Failed to load design CSS: ${cssFile}`);
                reject(new Error(`Failed to load ${cssFile}`));
            };

            document.head.appendChild(link);
        });
    }

    removeDesignCSS() {
        const designCSSLinks = document.querySelectorAll('link[id^="design-css-"]');
        designCSSLinks.forEach(link => {
            link.remove();
        });
    }

    updateButtonState() {
        const icon = document.getElementById('designIcon');
        const toggleBtn = document.getElementById('designToggle');
        
        if (icon && toggleBtn) {
            const design = this.designs[this.currentDesign];
            if (design) {
                icon.className = design.icon;
                toggleBtn.title = `Tasarım Değiştir (${design.fullName})`;
                
                // Update toggle button state
                if (this.currentDesign === 'alternative') {
                    toggleBtn.classList.add('alternative');
                } else {
                    toggleBtn.classList.remove('alternative');
                }
            }
        }
        
        // Legacy dropdown support
        const name = document.getElementById('designName');
        const dropdownBtn = document.getElementById('designDropdown');
        if (name && dropdownBtn) {
            const design = this.designs[this.currentDesign];
            if (design) {
                name.textContent = design.name;
                dropdownBtn.title = `Tasarım Seç (${design.fullName})`;
            }
        }
    }

    updateDropdownState() {
        // Remove active class from all options
        document.querySelectorAll('.design-option').forEach(option => {
            option.classList.remove('active');
        });

        // Add active class to current design
        const currentOption = document.querySelector(`[data-design="${this.currentDesign}"]`);
        if (currentOption) {
            currentOption.classList.add('active');
        }
    }

    showLoadingState() {
        const button = document.getElementById('designDropdown');
        if (button) {
            button.classList.add('design-loading');
            button.disabled = true;
        }
    }

    hideLoadingState() {
        const button = document.getElementById('designDropdown');
        if (button) {
            button.classList.remove('design-loading');
            button.disabled = false;
        }
    }

    showTransitionOverlay() {
        let overlay = document.querySelector('.theme-transition-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'theme-transition-overlay';
            document.body.appendChild(overlay);
        }
        overlay.classList.add('active');
    }

    hideTransitionOverlay() {
        const overlay = document.querySelector('.theme-transition-overlay');
        if (overlay) {
            overlay.classList.remove('active');
            setTimeout(() => {
                if (overlay.parentNode) {
                    overlay.remove();
                }
            }, 300);
        }
    }

    showNotification(message, type = 'success') {
        // Remove existing notification
        const existingNotification = document.querySelector('.design-notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Create notification
        const notification = document.createElement('div');
        notification.className = 'design-notification';
        
        const icon = type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
        const bgColor = type === 'error' ? 'linear-gradient(135deg, #ef4444, #dc2626)' : 'linear-gradient(135deg, #10b981, #059669)';
        
        notification.innerHTML = `
            <div class="notification-content">
                <i class="${icon} me-2"></i>
                ${message}
            </div>
        `;

        notification.style.background = bgColor;
        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 3000);
    }


    storeDesign(designKey) {
        try {
            localStorage.setItem('nextcode_design_theme', designKey);
        } catch (e) {
            console.warn('Could not store design preference:', e);
        }
    }

    getStoredDesign() {
        try {
            return localStorage.getItem('nextcode_design_theme');
        } catch (e) {
            console.warn('Could not retrieve design preference:', e);
            return null;
        }
    }

    triggerDesignChangeEvent(designKey) {
        const event = new CustomEvent('designChanged', {
            detail: {
                design: designKey,
                designInfo: this.designs[designKey]
            }
        });
        document.dispatchEvent(event);
    }

    // Public methods
    getCurrentDesign() {
        return this.currentDesign;
    }

    getAvailableDesigns() {
        return Object.keys(this.designs).map(key => ({
            key,
            ...this.designs[key]
        }));
    }

    setDesign(designKey) {
        if (this.designs[designKey]) {
            this.switchDesign(designKey);
        }
    }
}

// Initialize design switcher when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.designSwitcher = new DesignSwitcher();
    
    // Override theme.js functionality when alternative theme is active
    const checkThemeOverride = () => {
        if (window.designSwitcher && window.designSwitcher.getCurrentDesign() === 'alternative') {
            // Completely disable theme.js
            if (window.themeManager) {
                window.themeManager.setTheme = function() {};
                window.themeManager.setupThemeToggle = function() {};
            }
            
            // Force dark mode
            document.documentElement.setAttribute('data-theme', 'dark');
            document.documentElement.style.setProperty('--bs-body-bg', '#0d1117');
            document.documentElement.style.setProperty('--bs-body-color', '#f0f6fc');
            document.body.style.backgroundColor = '#0d1117';
            document.body.style.color = '#f0f6fc';
            
            // Hide theme toggle button
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                themeToggle.style.display = 'none';
            }
        }
    };
    
    // Check on load
    setTimeout(checkThemeOverride, 100);
    
    // Check periodically
    setInterval(checkThemeOverride, 1000);
    
    // Listen for design change events
    document.addEventListener('designChanged', function(e) {
        console.log('Design changed to:', e.detail.design, e.detail.designInfo.name);
        
        // Force theme override when alternative theme is selected
        if (e.detail.design === 'alternative') {
            setTimeout(checkThemeOverride, 100);
        }
        
        // You can add custom logic here for design-specific functionality
        switch(e.detail.design) {
            case 'alternative':
                // Alternative theme specific logic - force dark mode
                break;
            default:
                // Default design logic
                break;
        }
    });
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DesignSwitcher;
}
