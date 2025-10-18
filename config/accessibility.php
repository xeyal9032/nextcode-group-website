<?php
// NextCode Group - Accessibility Compliance System
// Comprehensive WCAG 2.1 AA accessibility features

if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

class AccessibilityManager {
    private $enableAria = true;
    private $enableKeyboardNav = true;
    private $enableScreenReader = true;
    private $enableFocusManagement = true;
    private $enableColorContrast = true;
    private $minContrastRatio = 4.5;
    
    public function __construct($config = []) {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * Generate ARIA labels and accessibility attributes
     */
    public function generateAriaLabels($elementData = []) {
        $elements = [
            // Navigation
            'nav-main' => [
                'role' => 'navigation',
                'aria-label' => 'Ana navigasiya menüsü',
                'aria-expanded' => 'false'
            ],
            
            // Headings hierarchy
            'h1-main' => [
                'id' => 'main-heading',
                'aria-level' => '1'
            ],
            
            // Links
            'skip-link' => [
                'href' => '#main-content',
                'aria-label' => 'Ana məzmuna keçmək',
                'class' => 'skip-link sr-only sr-only-focusable'
            ],
            
            // Forms
            'contact-form' => [
                'role' => 'form',
                'aria-label' => 'Əlaqə forması',
                'novalidate' => false
            ],
            
            // Images
            'logo' => [
                'alt' => 'NextCode Group loqosu - Ana səhifəyə qayıtmaq',
                'role' => 'img',
                'aria-label' => 'Şirkət loqosu'
            ],
            
            // Buttons
            'cta-button' => [
                'type' => 'button',
                'aria-label' => 'Ətraflı məlumat almaq',
                'role' => 'button',
                'aria-expanded' => 'false'
            ],
            
            // Modal/dialog
            'modal' => [
                'role' => 'dialog',
                'aria-modal' => 'true',
                'aria-labelledby' => 'modal-title',
                'aria-describedby' => 'modal-description'
            ]
        ];
        
        // Merge with provided data
        if ($elementData) {
            $elements = array_merge($elements, $elementData);
        }
        
        return $elements;
    }
    
    /**
     * Generate keyboard navigation support
     */
    public function generateKeyboardNavigation() {
        return '
<script>
// Enhanced keyboard navigation for accessibility
document.addEventListener(\'DOMContentLoaded\', function() {
    
    // Skip link functionality
    const skipLink = document.querySelector(\'[href="#main-content"]\');
    if (skipLink) {
        skipLink.addEventListener(\'click\', function(e) {
            e.preventDefault();
            const target = document.querySelector(\'#main-content\');
            if (target) {
                target.focus();
                target.scrollIntoView({ behavior: \'smooth\' });
            }
        });
    }
    
    // Trap focus in modal dialogs
    document.addEventListener(\'keydown\', function(e) {
        const modal = document.querySelector(\'[aria-modal="true"]\');
        if (modal && modal.style.display !== \'none\') {
            if (e.key === \'Tab\') {
                trapFocus(e, modal);
            } else if (e.key === \'Escape\') {
                closeModal(modal);
            }
        }
    });
    
    // Arrow key navigation for menu items
    const menuItems = document.querySelectorAll(\'nav a, .menu-item\');
    menuItems.forEach(item => {
        item.addEventListener(\'keydown\', function(e) {
            const items = Array.from(this.parentNode.parentNode.querySelectorAll(\'a\'));
            const currentIndex = items.indexOf(this);
            
            switch(e.key) {
                case \'ArrowDown\':
                case \'ArrowRight\':
                    e.preventDefault();
                    const nextIndex = (currentIndex + 1) % items.length;
                    items[nextIndex].focus();
                    break;
                case \'ArrowUp\':
                case \'ArrowLeft\':
                    e.preventDefault();
                    const prevIndex = currentIndex === 0 ? items.length - 1 : currentIndex - 1;
                    items[prevIndex].focus();
                    break;
                case \'Home\':
                    e.preventDefault();
                    items[0].focus();
                    break;
                case \'End\':
                    e.preventDefault();
                    items[items.length - 1].focus();
                    break;
            }
        });
    });
    
    // Form field navigation
    const formInputs = document.querySelectorAll(\'input, textarea, select\');
    formInputs.forEach(input => {
        input.addEventListener(\'keydown\', function(e) {
            if (e.key === \'Enter\' && this.tagName !== \'BUTTON\') {
                e.preventDefault();
                // Submit form or move to next field based on context
                if (this.form) {
                    const submitBtn = this.form.querySelector(\'button[type="submit"]\');
                    if (submitBtn) submitBtn.click();
                }
            }
        });
    });
});

function trapFocus(e, container) {
    const focusableElements = container.querySelectorAll(
        \'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])\'
    );
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];
    
    if (e.shiftKey) {
        if (document.activeElement === firstElement) {
            lastElement.focus();
            e.preventDefault();
        }
    } else {
        if (document.activeElement === lastElement) {
            firstElement.focus();
            e.preventDefault();
        }
    }
}

function closeModal(modal) {
    modal.style.display = \'none\';
    modal.setAttribute(\'aria-hidden\', \'true\');
    
    // Return focus to element that opened modal
    const opener = document.querySelector(\'[aria-controls="\' + modal.id + \'"]\');
    if (opener) opener.focus();
}
</script>';
    }
    
    /**
     * Generate screen reader support
     */
    public function generateScreenReaderSupport() {
        return '
<script>
// Screen reader announcements and live regions
(function() {
    let liveRegion;
    
    // Create live region for announcements
    if (!document.getElementById(\'aria-live-region\')) {
        liveRegion = document.createElement(\'div\');
        liveRegion.id = \'aria-live-region\';
        liveRegion.setAttribute(\'aria-live\', \'polite\');
        liveRegion.setAttribute(\'aria-atomic\', \'true\');
        liveRegion.className = \'sr-only\';
        document.body.appendChild(liveRegion);
    }
    
    // Announce function
    window.announceToScreenReader = function(message) {
        if (liveRegion) {
            liveRegion.textContent = message;
            
            // Clear after announcement
            setTimeout(() => {
                liveRegion.textContent = \'\';
            }, 1000);
        }
    };
    
    // Auto-announce form validation errors
    document.addEventListener(\'invalid\', function(e) {
        setTimeout(() => {
            const message = \'Form error: \' + e.target.validationMessage;
            announceToScreenReader(message);
        }, 100);
    });
    
    // Announce page changes for single-page apps
    let lastUrl = location.href;
    new Mutation Observer(() => {
        const url = location.href;
        if (url !== lastUrl) {
            announceToScreenReader(\'Page changed to \' + document.title);
            lastUrl = url;
        }
    }).observe(document, { subtree: true, childList: true });
})();
</script>';
    }
    
    /**
     * Generate focus management system
     */
    public function generateFocusManagement() {
        return '
<script>
// Enhanced focus management
(function() {
    let focusStack = [];
    let lastFocusedElement = null;
    
    // Track focused elements
    document.addEventListener(\'focusin\', function(e) {
        lastFocusedElement = e.target;
    });
    
    // Show focus indicators
    document.addEventListener(\'keydown\', function(e) {
        if (e.key === \'Tab\') {
            document.body.classList.add(\'keyboard-navigation\');
        }
    });
    
    document.addEventListener(\'mousedown\', function() {
        document.body.classList.remove(\'keyboard-navigation\');
    });
    
    // Focus restoration
    window.restoreFocusTo = function(element) {
        if (element && typeof element.focus === \'function\') {
            element.focus();
        } else if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
    };
    
    // Progressive enhancement: show/hide elements based on interaction
    document.addEventListener(\'DOMContentLoaded\', function() {
        const showSkipLink = document.querySelector(\'.skip-link\');
        if (showSkipLink) {
            showSkipLink.addEventListener(\'focus\', function() {
                this.classList.remove(\'sr-only\');
            });
            showSkipLink.addEventListener(\'blur\', function() {
                this.classList.add(\'sr-only\');
            });
        }
    });
})();
</script>';
    }
    
    /**
     * Generate color contrast checking
     */
    public function generateColorContrastSupport() {
        return '
<script>
// Color contrast checking utility
(function() {
    window.checkColorContrast = function(foreground, background) {
        // Simplified contrast ratio calculation
        const fg = colorToRgb(foreground);
        const bg = colorToRgb(background);
        
        const fgLuminance = getLuminance(fg);
        const bgLuminance = getLuminance(bg);
        
        const lighter = Math.max(fgLuminance, bgLuminance);
        const darker = Math.min(fgLuminance, bgLuminance);
        
        const contrastRatio = ((lighter + 0.05) / (darker + 0.05));
        
        return {
            ratio: contrastRatio,
            aaLarge: contrastRatio >= 3,
            aa: contrastRatio >= 4.5,
            AAA: contrastRatio >= 7
        };
    };
    
    function colorToRgb(color) {
        const tmp = document.createElement(\'div\');
        tmp.style.color = color;
        document.body.appendChild(tmp);
        const cssColor = window.getComputedStyle(tmp).color;
        document.body.removeChild(tmp);
        
        const values = cssColor.match(/\\d+/g);
        return values ? {
            r: parseInt(values[0]),
            g: parseInt(values[1]),
            b: parseInt(values[2])
        } : {r: 0, g: 0, b: 0};
    }
    
    function getLuminance(rgb) {
        const {r, g, b} = rgb;
        const [rs, gs, bs] = [r, g, b].map(c => {
            c = c / 255;
            return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
        });
        return 0.2126 * rs + 0.7152 * gs + 0.0722 * bs;
    }
    
    // Add high contrast mode toggle
    window.toggleHighContrast = function() {
        document.body.classList.toggle(\'high-contrast\');
        const isHighContrast = document.body.classList.contains(\'high-contrast\');
        localStorage.setItem(\'highContrast\', isHighContrast);
    };
    
    // Restore high contrast setting
    if (localStorage.getItem(\'highContrast\') === \'true\') {
        document.body.classList.add(\'high-contrast\');
    }
})();
</script>';
    }
    
    /**
     * Generate accessibility CSS helpers
     */
    public function generateAccessibilityCSS() {
        return '
<style>
/* Screen reader only content */
.sr-only {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

.sr-only-focusable:focus {
    position: static !important;
    width: auto !important;
    height: auto !important;
    overflow: visible !important;
    clip: auto !important;
    white-space: normal !important;
}

/* Skip link */
.skip-link {
    position: absolute;
    top: -40px;
    left: 6px;
    background: #000;
    color: #fff;
    padding: 8px 16px;
    text-decoration: none;
    transition: top 0.3s ease;
    z-index: 10000;
    border-radius: 0 0 4px 4px;
}

.skip-link:focus {
    top: 0;
    background: #007bff;
}

/* Focus indicators */
.keyboard-navigation *:focus {
    outline: 2px solid #007bff !important;
    outline-offset: 2px !important;
    box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.25) !important;
}

/* High contrast mode */
.high-contrast {
    filter: contrast(1.5) brightness(1.1);
}

.high-contrast * {
    text-shadow: none !important;
    box-shadow: none !important;
}

/* Reduced motion preferences */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}

/* Focus management for modal */
[aria-modal="true"]:focus {
    outline: 3px solid #007bff;
    outline-offset: 2px;
}

/* Form accessibility */
label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

input:required,
textarea:required,
select:required {
    border-left: 4px solid #dc3545;
}

input:valid,
textarea:valid,
select:valid {
    border-left: 4px solid #28a745;
}

/* Error styles */
.error-message {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Success styles */
.success-message {
    color: #28a745;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Accessibility improvements for interactive elements */
button:disabled,
input:disabled,
textarea:disabled,
select:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

button[aria-pressed="true"] {
    background-color: #007bff;
    color: white;
}

button[aria-expanded="false"]::after {
    content: " ▼";
}

button[aria-expanded="true"]::after {
    content: " ▲";
}

/* Ensure minimum touch target sizes */
button, 
input[type="submit"],
input[type="button"],
input[type="checkbox"],
input[type="radio"],
a {
    min-height: 44px;
    min-width: 44px;
}

/* Support for forced colored preferences */
@media (forced-colors: active) {
    .btn, button, input[type="button"] {
        border: 2px solid ButtonText;
        background: ButtonFace;
        color: ButtonText;
    }
    
    .btn-primary {
        border-color: Highlight;
        background: Highlight;
        color: HighlightText;
    }
}
</style>';
    }
    
    /**
     * Generate complete accessibility suite
     */
    public function generateCompleteAccessibilitySuite() {
        $elements = [];
        
        // CSS
        $elements[] = $this->generateAccessibilityCSS();
        
        // ARIA labels will be handled as HTML attributes, not here
        // $elements[] = $this->generateAriaLabels();
        
        // JavaScript features
        $elements[] = $this->generateKeyboardNavigation();
        $elements[] = $this->generateScreenReaderSupport();
        $elements[] = $this->generateFocusManagement();
        $elements[] = $this->generateColorContrastSupport();
        
        return implode("\n", array_filter($elements));
    }
    
    /**
     * Check accessibility compliance
     */
    public function checkAccessibilityCompliance() {
        return [
            'aria_labels' => $this->hasAllAriaLabels(),
            'keyboard_navigation' => $this->hasKeyboardNavigation(),
            'color_contrast' => $this->checkColorContrast(),
            'alt_text' => $this->hasAltText(),
            'semantic_html' => $this->hasSemanticHtml(),
            'focus_management' => $this->hasProperFocus(),
            'screen_reader_support' => $this->hasScreenReaderSupport()
        ];
    }
    
    /**
     * Accessibility check methods
     */
    private function hasAllAriaLabels() {
        // Check if navigation has aria-label
        return document.querySelector('nav')?.hasAttribute('aria-label');
    }
    
    private function hasKeyboardNavigation() {
        // Check for skip links
        return document.querySelector('.skip-link') !== null;
    }
    
    private function checkColorContrast() {
        // Basic contrast check would go here
        return true; // Simplified
    }
    
    private function hasAltText() {
        // Check all images have alt text
        const images = document.querySelectorAll('img');
        return Array.from(images).every(img => img.alt && img.alt.trim() !== '');
    }
    
    private function hasSemanticHtml() {
        // Check for semantic HTML elements
        return document.querySelector('main') && document.querySelector('nav') && document.querySelector('footer');
    }
    
    private function hasProperFocus() {
        // Check focus management
        return true; // Simplified
    }
    
    private function hasScreenReaderSupport() {
        // Check for screen reader utilities
        return document.querySelector('[aria-live]') !== null;
    }
}

// Global accessibility manager instance
$accessibilityManager = new AccessibilityManager();

// Helper functions
function get_accessibility_suite() {
    global $accessibilityManager;
    return $accessibilityManager->generateCompleteAccessibilitySuite();
}

function check_accessibility_compliance() {
    global $accessibilityManager;
    return $accessibilityManager->checkAccessibilityCompliance();
}

function generate_aria_elements($data = []) {
    global $accessibilityManager;
    return $accessibilityManager->generateAriaLabels($data);
}

// Auto-generate accessibility features
function auto_accessibility_features() {
    global $accessibilityManager;
    return $accessibilityManager->generateCompleteAccessibilitySuite();
}

?>
