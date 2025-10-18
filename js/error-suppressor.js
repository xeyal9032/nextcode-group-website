/**
 * Error Suppressor
 * Font ve resource hatalarını sessizce yönetir
 */

(function() {
    'use strict';
    
    // Font loading hatalarını yakala
    const originalFontFace = window.FontFace;
    
    if (originalFontFace) {
        window.FontFace = function(...args) {
            const fontFace = new originalFontFace(...args);
            
            // Load error'ı yakalayalım
            fontFace.load().catch(err => {
                // Sessizce fallback font kullan
                console.log('Font yükleme hatası (fallback kullanılıyor):', args[0]);
            });
            
            return fontFace;
        };
        
        // Prototype'ı koru
        window.FontFace.prototype = originalFontFace.prototype;
    }
    
    // Service Worker fetch hatalarını filtrele
    const originalConsoleError = console.error;
    const originalConsoleWarn = console.warn;
    
    console.error = function(...args) {
        const message = args.join(' ');
        
        // Bu hataları gösterme (zararsız)
        const suppressPatterns = [
            'Failed to decode downloaded font',
            'OTS parsing error',
            'Font loading failed',
            'Service Worker: Fetch failed',
            'The resource was preloaded'
        ];
        
        const shouldSuppress = suppressPatterns.some(pattern => 
            message.includes(pattern)
        );
        
        if (!shouldSuppress) {
            originalConsoleError.apply(console, args);
        }
    };
    
    console.warn = function(...args) {
        const message = args.join(' ');
        
        // Bu warning'leri gösterme
        const suppressPatterns = [
            'was preloaded using link preload',
            'Font loading failed, using fallback',
            'Failed to load font'
        ];
        
        const shouldSuppress = suppressPatterns.some(pattern => 
            message.includes(pattern)
        );
        
        if (!shouldSuppress) {
            originalConsoleWarn.apply(console, args);
        }
    };
    
    console.log('✅ Error suppressor aktif - Console temiz');
})();


