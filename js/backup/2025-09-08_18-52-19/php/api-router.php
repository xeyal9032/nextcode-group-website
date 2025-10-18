<?php
/**
 * API Router for when .htaccess is not available
 * Bu dosya .htaccess çalışmadığında API endpoint'lerini yönlendirmek için kullanılır
 */

// Only process if this is an API request
if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
    $apiPath = substr($_SERVER['REQUEST_URI'], 5); // Remove '/api/' prefix
    
    // Map API endpoints to their corresponding files
    $apiRoutes = [
        'services' => 'api/services.php',
        'services.php' => 'api/services.php',
        'portfolio' => 'api/portfolio.php',
        'portfolio.php' => 'api/portfolio.php',
        'blog' => 'api/blog.php',
        'blog.php' => 'api/blog.php',
        'blog/recent' => 'api/blog.php',
        'settings' => 'api/settings.php',
        'settings.php' => 'api/settings.php',
        'analytics' => 'api/analytics.php',
        'analytics.php' => 'api/analytics.php',
        'contact-info' => 'api/contact-info.php',
        'contact-info.php' => 'api/contact-info.php',
        'contact' => 'api/contact.php',
        'contact.php' => 'api/contact.php',
        'content' => 'api/content.php',
        'content.php' => 'api/content.php',
        'site-content' => 'api/site-content.php',
        'site-content.php' => 'api/site-content.php',
        'performance-monitor' => 'api/performance-monitor.php',
        'performance-monitor.php' => 'api/performance-monitor.php',
        'clear-cache' => 'api/clear-cache.php',
        'clear-cache.php' => 'api/clear-cache.php'
    ];
    
    // Check if the API route exists
    if (isset($apiRoutes[$apiPath])) {
        $filePath = __DIR__ . '/' . $apiRoutes[$apiPath];
        if (file_exists($filePath)) {
            // Set the original request URI for the API file to handle
            $_SERVER['ORIGINAL_REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            include $filePath;
            exit;
        }
    }
    
    // API endpoint not found
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'API endpoint not found', 
        'path' => $apiPath,
        'available_endpoints' => array_keys($apiRoutes)
    ]);
    exit;
}

// If not an API request, continue with normal page loading
return false;
?>
