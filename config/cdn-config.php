<?php
// NextCode Group - CDN Configuration
// CDN settings and environment variables

return [
    // CDN Settings
    'CDN_ENABLED' => filter_var(env('CDN_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'CDN_DOMAIN' => env('CDN_DOMAIN', 'https://cdn.nextcodegroup.com'),
    'ASSETS_DOMAIN' => env('ASSETS_DOMAIN', $_SERVER['HTTP_HOST'] ?? 'localhost'),
    'CDN_FALLBACK' => filter_var(env('CDN_FALLBACK', true), FILTER_VALIDATE_BOOLEAN),
    'CACHE_VERSION' => env('CACHE_VERSION', '1.0'),
    
    // Asset optimization settings
    'ENABLE_WEBP' => filter_var(env('ENABLE_WEBP', true), FILTER_VALIDATE_BOOLEAN),
    'ENABLE_LAZY_LOADING' => filter_var(env('ENABLE_LAZY_LOADING', true), FILTER_VALIDATE_BOOLEAN),
    'ENABLE_PRELOAD' => filter_var(env('ENABLE_PRELOAD', true), FILTER_VALIDATE_BOOLEAN),
    'ENABLE_PREFETCH' => filter_var(env('ENABLE_PREFETCH', false), FILTER_VALIDATE_BOOLEAN),
    
    // Image optimization settings
    'DEFAULT_IMAGE_QUALITY' => (int)env('DEFAULT_IMAGE_QUALITY', 85),
    'RESPONSIVE_IMAGE_SIZES' => [
        ['width' => 320, 'descriptor' => '320w'],
        ['width' => 768, 'descriptor' => '768w'],
        ['width' => 1024, 'descriptor' => '1024w'],
        ['width' => 1440, 'descriptor' => '1440w'],
        ['width' => 1920, 'descriptor' => '1920w']
    ],
    
    // Critical assets for preload/prefetch
    'CRITICAL_ASSETS' => [
        ['path' => 'css/critical.css', 'type' => 'style'],
        ['path' => 'css/styles.css', 'type' => 'style'],
        ['path' => 'js/main.js', 'type' => 'script'],
        ['path' => 'fonts/inter-font.woff2', 'type' => 'font']
    ],
    
    // Performance settings
    'MAX_CACHE_DURATION' => (int)env('MAX_CACHE_DURATION', 31536000), // 1 year
    'DEFAULT_CACHE_DURATION' => (int)env('DEFAULT_CACHE_DURATION', 86400), // 1 day
    
    // Browser support
    'MODERN_BROWSER_SUPPORT' => true,
    'LEGACY_BROWSER_FALLBACK' => true,
    
    // Development settings
    'ENABLE_COMPRESSION' => filter_var(env('ENABLE_COMPRESSION', true), FILTER_VALIDATE_BOOLEAN),
    'ENABLE_MINIFICATION' => filter_var(env('ENABLE_MINIFICATION', true), FILTER_VALIDATE_BOOLEAN),
];

// Helper function to read environment variables
function env($key, $default = null) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    
    if ($value === null) {
        return $default;
    }
    
    // Cast boolean values
    if (in_array(strtolower($value), ['true', '1', 'yes', 'on'])) {
        return true;
    }
    
    if (in_array(strtolower($value), ['false', '0', 'no', 'off'])) {
        return false;
    }
    
    return $value;
}
?>