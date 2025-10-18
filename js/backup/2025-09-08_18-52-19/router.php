<?php
/**
 * Simple Router for PHP Built-in Server
 * Handles API routing for development
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Handle API routes
if (strpos($uri, '/api/') === 0) {
    $apiPath = substr($uri, 5); // Remove '/api/' prefix
    
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
        'contact-info.php' => 'api/contact-info.php'
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
    echo json_encode(['error' => 'API endpoint not found', 'path' => $apiPath]);
    exit;
}

// Handle static files (CSS, JS, images, etc.)
if (file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    // Set appropriate content type for static files
    $extension = pathinfo($uri, PATHINFO_EXTENSION);
    switch ($extension) {
        case 'css':
            header('Content-Type: text/css');
            break;
        case 'js':
            header('Content-Type: application/javascript');
            break;
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'jpg':
        case 'jpeg':
            header('Content-Type: image/jpeg');
            break;
        case 'gif':
            header('Content-Type: image/gif');
            break;
        case 'svg':
            header('Content-Type: image/svg+xml');
            break;
        case 'woff':
        case 'woff2':
            header('Content-Type: font/woff');
            break;
        case 'ttf':
            header('Content-Type: font/ttf');
            break;
    }
    return false; // Let PHP built-in server handle static files
}

// Handle all other requests - serve index.php
if ($uri === '/' || !file_exists(__DIR__ . $uri)) {
    include __DIR__ . '/index.php';
    exit;
}

// Let PHP built-in server handle the request
return false;
?>