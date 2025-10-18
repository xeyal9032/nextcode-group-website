/**
 * Font Loader - NextCode Group
 * Optimized font loading with fallback handling
 */

class FontLoader {
    constructor() {
        this.fontsLoaded = false;
        this.fallbackTimeout = 2000; // 2 seconds
        this.init();
    }

    init() {
        this.addFontLoadingClass();
        this.loadFonts();
        this.setupFallback();
    }

    addFontLoadingClass() {
        document.documentElement.classList.add('font-loading');
        
        // Add font loading styles
        const style = document.createElement('style');
        style.textContent = `
            .font-loading * {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif !important;
            }
            
            .font-loading h1, .font-loading h2, .font-loading h3, 
            .font-loading h4, .font-loading h5, .font-loading h6 {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif !important;
                font-weight: 600 !important;
            }
        `;
        document.head.appendChild(style);
    }

    loadFonts() {
        // Check if Inter font is available first
        if (document.fonts && document.fonts.check) {
            // Check if Inter font is loaded
            if (document.fonts.check('16px Inter')) {
                this.onFontsLoaded();
                return;
            }
        }

        // Immediate font loading with optimized weights
        if (document.fonts && document.fonts.load) {
            Promise.all([
                document.fonts.load('400 16px Inter'),
                document.fonts.load('600 16px Inter'),
                document.fonts.load('700 16px Inter')
            ]).then(() => {
                this.onFontsLoaded();
            }).catch((error) => {
                // Font loading failed, using fallback silently
                // Try to load fonts individually
                this.loadFontsIndividually();
            });
        } else {
            // Fallback for older browsers
            setTimeout(() => {
                this.onFontsLoaded();
            }, 500);
        }

        // Check if fonts are already loaded
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(() => {
                this.onFontsLoaded();
            });
        }
    }

    loadFontsIndividually() {
        if (document.fonts && document.fonts.load) {
            const fontPromises = [];
            
            // Try loading each font individually
            ['400 16px Inter', '600 16px Inter', '700 16px Inter'].forEach(font => {
                fontPromises.push(
                    document.fonts.load(font).catch(err => {
                        // Failed to load font, continue silently
                        return null; // Continue even if one font fails
                    })
                );
            });

            Promise.all(fontPromises).finally(() => {
                this.onFontsLoaded();
            });
        } else {
            this.onFontsLoaded();
        }
    }

    setupFallback() {
        // Ensure fonts load within reasonable time
        setTimeout(() => {
            if (!this.fontsLoaded) {
                this.onFontsLoaded();
            }
        }, this.fallbackTimeout);
    }

    onFontsLoaded() {
        if (this.fontsLoaded) return;
        
        this.fontsLoaded = true;
        document.documentElement.classList.remove('font-loading');
        document.documentElement.classList.add('font-loaded');
        
        // Add smooth transition
        const style = document.createElement('style');
        style.textContent = `
            .font-loaded * {
                transition: font-family 0.3s ease !important;
            }
        `;
        document.head.appendChild(style);
    }

    // Check if specific font is available
    isFontAvailable(fontName) {
        if (document.fonts && document.fonts.check) {
            return document.fonts.check(`16px "${fontName}"`);
        }
        return false;
    }

    // Get available fonts
    getAvailableFonts() {
        if (document.fonts && document.fonts.values) {
            return Array.from(document.fonts.values()).map(font => font.family);
        }
        return [];
    }
}

// Initialize font loader when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.fontLoader = new FontLoader();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FontLoader;
}
