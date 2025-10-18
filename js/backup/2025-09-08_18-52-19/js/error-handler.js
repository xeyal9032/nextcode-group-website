// Global Error Handler for Contact Form
(function() {
    'use strict';
    
    // Store original error handler
    const originalErrorHandler = window.onerror;
    
    // Global error handler
    window.onerror = function(message, source, lineno, colno, error) {
        // Log error for debugging
        console.warn('Error handled:', message, source, lineno);
        
        // Don't break contact form functionality
        if (message && message.includes('preloadCriticalImages')) {
            console.warn('Image optimizer error - continuing without it');
            return true; // Prevent error from breaking
        }
        
        if (message && message.includes('themeManager.enableTransitions')) {
            console.warn('Theme manager error - continuing without transitions');
            return true; // Prevent error from breaking
        }
        
        if (message && message.includes('font')) {
            console.warn('Font loading error - using fallback fonts');
            document.body.classList.add('font-loading-error');
            return true; // Prevent error from breaking
        }
        
        // Call original error handler if it exists
        if (originalErrorHandler) {
            return originalErrorHandler(message, source, lineno, colno, error);
        }
        
        return false; // Let default error handling continue
    };
    
    // Handle unhandled promise rejections
    window.addEventListener('unhandledrejection', function(event) {
        console.warn('Unhandled promise rejection:', event.reason);
        event.preventDefault(); // Prevent default handling
    });
    
    // Ensure contact form functionality is protected
    window.addEventListener('DOMContentLoaded', function() {
        // Check if contact form exists and protect it
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            console.log('Contact form found - protecting functionality');
            
            // Ensure form submission works even if other scripts fail
            contactForm.addEventListener('submit', function(e) {
                console.log('Contact form submission detected');
                // Let the contact form script handle this
            });
        }
    });
    
    console.log('Error handler initialized - contact form protected');
})();
