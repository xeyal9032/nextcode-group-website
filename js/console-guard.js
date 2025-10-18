/**
 * Console Guard - Production Console Disabler
 * Disables console.log, console.error, console.warn in production
 * 
 * @author NextCode Group
 * @version 1.0
 */

(function() {
    'use strict';
    
    // Check if we're in production (not localhost or development)
    const isProduction = window.location.hostname !== 'localhost' 
                      && window.location.hostname !== '127.0.0.1'
                      && !window.location.hostname.includes('localhost')
                      && !window.location.hostname.includes('dev.')
                      && !window.location.hostname.includes('test.')
                      && !window.location.hostname.includes('staging.');
    
    // Only disable console in production
    if (isProduction) {
        // Store original console methods (in case we need them)
        const originalConsole = {
            log: console.log,
            error: console.error,
            warn: console.warn,
            info: console.info,
            debug: console.debug
        };
        
        // Disable console methods
        console.log = function() {};
        console.error = function() {};
        console.warn = function() {};
        console.info = function() {};
        console.debug = function() {};
        
        // Keep console.table and console.group for debugging if needed
        // But you can disable these too if you want
        
        // Optional: Log that console is disabled (only once)
        if (typeof originalConsole.log === 'function') {
            originalConsole.log('%c🔒 Console logs disabled in production', 'color: #f56565; font-weight: bold;');
        }
        
        // Make originalConsole available globally for emergency debugging
        // Access via: window.__console__.log('debug message')
        window.__console__ = originalConsole;
    } else {
        // Development mode - console is active
        console.log('%c✅ Console logs enabled (Development mode)', 'color: #48bb78; font-weight: bold;');
    }
})();


