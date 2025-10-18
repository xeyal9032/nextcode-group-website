<?php
// Gelişmiş Güvenlik Headers - NextCode Group

if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

/**
 * Gelişmiş güvenlik headers'larını ayarla
 */
function setSecurityHeaders() {
    // X-Content-Type-Options
    header('X-Content-Type-Options: nosniff');
    
    // X-Frame-Options
    header('X-Frame-Options: DENY');
    
    // X-XSS-Protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Permissions Policy
    header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=(), vibrate=(), fullscreen=(self), sync-xhr=()');
    
    // X-Permitted-Cross-Domain-Policies
    header('X-Permitted-Cross-Domain-Policies: none');
    
    // Strict-Transport-Security (sadece HTTPS'te)
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
    
    // Content Security Policy
    $csp = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdnjs.cloudflare.com cdn.quilljs.com",
        "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com cdn.quilljs.com fonts.googleapis.com",
        "font-src 'self' cdnjs.cloudflare.com fonts.gstatic.com",
        "img-src 'self' data: https:",
        "media-src 'self'",
        "object-src 'none'",
        "child-src 'none'",
        "frame-ancestors 'none'",
        "form-action 'self'",
        "base-uri 'self'",
        "manifest-src 'self'",
        "worker-src 'self'",
        "connect-src 'self'"
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp));
    
    // Cross-Origin Embedder Policy
    header('Cross-Origin-Embedder-Policy: require-corp');
    
    // Cross-Origin Opener Policy
    header('Cross-Origin-Opener-Policy: same-origin');
    
    // Cross-Origin Resource Policy
    header('Cross-Origin-Resource-Policy: same-origin');
}

/**
 * Cache kontrolü için headers
 */
function setCacheHeaders($public = false, $max_age = 3600) {
    if ($public) {
        header('Cache-Control: public, max-age=' . $max_age);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . ' GMT');
    } else {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}

/**
 * Admin paneli için özel güvenlik headers
 */
function setAdminSecurityHeaders() {
    // Admin paneli için daha sıkı güvenlik
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    
    // Admin paneli için cache'i devre dışı bırak
    setCacheHeaders(false);
    
    // Admin paneli için özel CSP
    $admin_csp = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdnjs.cloudflare.com cdn.quilljs.com",
        "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com cdn.quilljs.com",
        "font-src 'self' cdnjs.cloudflare.com",
        "img-src 'self' data: https:",
        "connect-src 'self'",
        "form-action 'self'",
        "base-uri 'self'",
        "object-src 'none'",
        "frame-ancestors 'none'"
    ];
    header('Content-Security-Policy: ' . implode('; ', $admin_csp));
}

/**
 * Dosya upload için güvenlik headers
 */
function setUploadSecurityHeaders() {
    // Upload sayfaları için özel güvenlik
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    
    // Upload için daha sıkı CSP
    $upload_csp = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline'",
        "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com",
        "font-src 'self' cdnjs.cloudflare.com",
        "img-src 'self' data:",
        "form-action 'self'",
        "object-src 'none'",
        "frame-ancestors 'none'"
    ];
    header('Content-Security-Policy: ' . implode('; ', $upload_csp));
}

// Otomatik olarak güvenlik headers'larını ayarla
if (!headers_sent()) {
    try {
        setSecurityHeaders();
    } catch (Exception $e) {
        // Headers hatası durumunda sessizce devam et
        error_log("Security headers error: " . $e->getMessage());
    }
}
?>
