<?php
// NextCode Group - CDN Integration System
// Advanced CDN management for static assets and performance optimization

if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

class CDNManager {
    private $cdnEnabled = false;
    private $cdnDomain = '';
    private $assetsDomain = '';
    private $fallbackEnabled = true;
    private $versioning = true;
    private $cacheVersion = '1.0';
    
    public function __construct($config = []) {
        // Load configuration from environment or defaults
        $this->cdnEnabled = $this->getConfig('CDN_ENABLED', false);
        $this->cdnDomain = $this->getConfig('CDN_DOMAIN', 'https://cdn.nextcodegroup.com');
        $this->assetsDomain = $this->getConfig('ASSETS_DOMAIN', $_SERVER['HTTP_HOST'] ?? 'localhost');
        $this->fallbackEnabled = $this->getConfig('CDN_FALLBACK', true);
        $this->cacheVersion = $this->getConfig('CACHE_VERSION', '1.0');
        
        // Apply custom config
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        
        // Set version hash for cache busting
        if ($this->versioning) {
            $this->cacheVersion = md5(filemtime(__FILE__) . date('Y-m-d'));
        }
    }
    
    /**
     * Get configuration value with fallback
     */
    private function getConfig($key, $default = null) {
        // Try environment variable first
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
        
        // Try configuration file
        if ($value === null && file_exists(__DIR__ . '/cdn-config.php')) {
            $config = include __DIR__ . '/cdn-config.php';
            $value = $config[$key] ?? null;
        }
        
        return $value !== null ? $value : $default;
    }
    
    /**
     * Generate CDN URL for asset
     */
    public function getAssetUrl($path, $options = []) {
        // Clean path
        $path = ltrim($path, '/');
        
        // Apply versioning
        if ($this->versioning) {
            $path = $this->addVersionToPath($path);
        }
        
        // Build CDN URL
        $cdnUrl = $this->buildCdnUrl($path);
        
        // Add query parameters if specified
        if (!empty($options['params'])) {
            $cdnUrl .= '?' . http_build_query($options['params']);
        }
        
        // Wrap in fallback if enabled
        if ($this->fallbackEnabled) {
            return $this->wrapWithFallback($cdnUrl, $path);
        }
        
        return $cdnUrl;
    }
    
    /**
     * Build CDN URL from path
     */
    private function buildCdnUrl($path) {
        if ($this->cdnEnabled) {
            return rtrim($this->cdnDomain, '/') . '/' . $path;
        }
        
        return '//' . rtrim($this->assetsDomain, '/') . '/' . $path;
    }
    
    /**
     * Add version to asset path for cache busting
     */
    private function addVersionToPath($path) {
        $pathInfo = pathinfo($path);
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        $basePath = $pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '';
        $filename = $pathInfo['filename'];
        
        return $basePath . $filename . '.v' . substr($this->cacheVersion, 0, 8) . $extension;
    }
    
    /**
     * Wrap CDN URL with fallback
     */
    private function wrapWithFallback($cdnUrl, $originalPath) {
        $fallbackUrl = '//' . rtrim($this->assetsDomain, '/') . '/' . $originalPath;
        
        // Create JavaScript fallback wrapper
        $fallbackScript = "
            (function() {
                var img = new Image();
                img.onerror = function() {
                    var all = document.querySelectorAll('src=\"$cdnUrl\"');
                    for (var i = 0; i < all.length; i++) {
                        all[i].src = '$fallbackUrl';
                    }
                };
                img.src = '$cdnUrl';
            })();
        ";
        
        // For PHP context, return HTML with fallback
        return "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    $fallbackScript
                });
            </script>
            <img src=\"$cdnUrl\" onerror=\"this.src='$fallbackUrl'\" style=\"display:none;\">
        ";
    }
    
    /**
     * Get prefetch HTML for critical assets
     */
    public function getPrefetchHtml($assets = []) {
        if (!$this->cdnEnabled) {
            return '';
        }
        
        $prefetchHtml = '';
        foreach ($assets as $asset) {
            $url = $this->getAssetUrl($asset['path']);
            $type = $asset['type'] ?? 'style';
            
            switch ($type) {
                case 'style':
                    $prefetchHtml .= "\n<link rel=\"prefetch\" href=\"$url\" as=\"style\">";
                    break;
                case 'script':
                    $prefetchHtml .= "\n<link rel=\"prefetch\" href=\"$url\" as=\"script\">";
                    break;
                case 'image':
                    $prefetchHtml .= "\n<link rel=\"prefetch\" href=\"$url\" as=\"image\">";
                    break;
                case 'font':
                    $prefetchHtml .= "\n<link rel=\"prefetch\" href=\"$url\" as=\"font\" crossorigin>";
                    break;
            }
        }
        
        return $prefetchHtml;
    }
    
    /**
     * Get preload HTML for critical assets
     */
    public function getPreloadHtml($assets = []) {
        $preloadHtml = '';
        
        foreach ($assets as $asset) {
            $url = $this->getAssetUrl($asset['path']);
            $type = $asset['type'] ?? 'style';
            $media = isset($asset['media']) ? ' media="' . $asset['media'] . '"' : '';
            
            switch ($type) {
                case 'style':
                    $preloadHtml .= "\n<link rel=\"preload\" href=\"$url\" as=\"style\"{$media}>";
                    break;
                case 'script':
                    $preloadHtml .= "\n<link rel=\"preload\" href=\"$url\" as=\"script\"{$media}>";
                    break;
                case 'image':
                    $preloadHtml .= "\n<link rel=\"preload\" href=\"$url\" as=\"image\"{$media}>";
                    break;
                case 'font':
                    $preloadHtml .= "\n<link rel=\"preload\" href=\"$url\" as=\"font\" type=\"font/woff2\" crossorigin{$media}>";
                    break;
            }
        }
        
        return $preloadHtml;
    }
    
    /**
     * Optimize image URLs with WebP support
     */
    public function getOptimizedImageUrl($originalPath, $options = []) {
        $width = $options['width'] ?? null;
        $height = $options['height'] ?? null;
        $quality = $options['quality'] ?? 85;
        $format = $options['format'] ?? 'auto';
        
        // Check WebP support
        $supportsWebP = $this->supportsWebP();
        
        $path = ltrim($originalPath, '/');
        
        // Add optimization parameters
        $params = [];
        if ($width) $params['w'] = $width;
        if ($height) $params['h'] = $height;
        if ($quality !== 85) $params['q'] = $quality;
        if ($format !== 'auto') $params['f'] = $format;
        if ($supportsWebP && $format === 'auto') $params['f'] = 'webp';
        
        // Build optimized URL
        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }
        
        return $this->getAssetUrl($path);
    }
    
    /**
     * Check if browser supports WebP
     */
    private function supportsWebP() {
        return isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false;
    }
    
    /**
     * Generate responsive image HTML
     */
    public function getResponsiveImage($originalPath, $sizes = [], $alt = '', $class = '') {
        $srcset = [];
        
        foreach ($sizes as $size) {
            $width = $size['width'] ?? null;
            $descriptor = $width ? $width . 'w' : ($size['descriptor'] ?? '1x');
            
            $options = $size;
            unset($options['descriptor'], $options['width']);
            $options['width'] = $width;
            
            $url = $this->getOptimizedImageUrl($originalPath, $options);
            $srcset[] = $url . ' ' . $descriptor;
        }
        
        $srcsetString = implode(', ', $srcset);
        $fallbackUrl = $this->getAssetUrl($originalPath);
        
        return "<img src=\"$fallbackUrl\" srcset=\"$srcsetString\" alt=\"$alt\" class=\"$class\" loading=\"lazy\">";
    }
    
    /**
     * Generate critical CSS preloader
     */
    public function getCriticalCssLoader($cssFile, $criticalCssFile = '') {
        if (!$criticalCssFile) {
            $criticalCssFile = $cssFile;
        }
        
        $cssUrl = $this->getAssetUrl($cssFile);
        $criticalUrl = $this->getAssetUrl($criticalCssFile, ['params' => ['critical' => '1']]);
        
        return "
            <link rel=\"preload\" href=\"$cssUrl\" as=\"style\" onload=\"this.onload=null;this.rel='stylesheet'\">
            <noscript><link rel=\"stylesheet\" href=\"$cssUrl\"></noscript>
            
            <style id=\"critical-css\">
                /* Critical CSS will be loaded here */
            </style>
            
            <script>
                // Load critical CSS immediately
                fetch('$criticalUrl')
                    .then(response => response.text())
                    .then(css => {
                        document.getElementById('critical-css').textContent = css;
                    })
                    .catch(error => {
                        console.warn('Failed to load critical CSS:', error);
                    });
                    
                // Load full CSS
                (function() {
                    var link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = '$cssUrl';
                    document.head.appendChild(link);
                })();
            </script>
        ";
    }
    
    /**
     * Generate lazy loading script for images
     */
    public function enableLazyLoading() {
        return "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if ('IntersectionObserver' in window) {
                        const imageObserver = new IntersectionObserver((entries, sender) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    const img = entry.target;
                                    img.src = img.dataset.src;
                                    img.classList.remove('lazy');
                                    imageObserver.unobserve(img);
                                }
                            });
                        });
                        
                        document.querySelectorAll('.lazy').forEach(img => {
                            imageObserver.observe(img);
                        });
                    } else {
                        // Fallback for older browsers
                        document.querySelectorAll('.lazy').forEach(img => {
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                        });
                    }
                });
            </script>
        ";
    }
    
    /**
     * Get performance optimization headers
     */
    public function getOptimizationHeaders() {
        $headers = [];
        
        if ($this->cdnEnabled) {
            // Cache static assets for 1 year
            $headers['Cache-Control'] = 'public, max-age=31536000, immutable';
            $headers['Expires'] = gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT';
            
            // Compression support
            $headers['Vary'] = 'Accept-Encoding';
            
            // Security headers
            $headers['X-Content-Type-Options'] = 'nosniff';
            $headers['X-Frame-Options'] = 'DENY';
        }
        
        return $headers;
    }
    
    /**
     * Calculate bundle hash for cache busting
     */
    public function calculateBundleHash($files = []) {
        $content = '';
        foreach ($files as $file) {
            if (file_exists($file)) {
                $content .= file_get_contents($file);
            }
        }
        
        return substr(md5($content), 0, 8);
    }
    
    /**
     * Upload assets to CDN (placeholder for actual CDN API)
     */
    public function uploadToCdn($localPath, $remotePath = null) {
        // This would integrate with actual CDN service (AWS CloudFront, Cloudflare, etc.)
        error_log("CDN upload: $localPath -> " . (!$remotePath ? basename($localPath) : $remotePath));
        
        return true; // Placeholder
    }
    
    /**
     * Purge CDN cache for specific assets
     */
    public function purgeCache($patterns = []) {
        // This would integrate with actual CDN purge API
        error_log("CDN purge requested for: " . implode(', ', $patterns));
        
        return true; // Placeholder
    }
    
    /**
     * Get CDN status and health
     */
    public function getCdnStatus() {
        return [
            'enabled' => $this->cdnEnabled,
            'domain' => $this->cdnDomain,
            'assets_domain' => $this->assetsDomain,
            'fallback_enabled' => $this->fallbackEnabled,
            'versioning_enabled' => $this->versioning,
            'cache_version' => $this->cacheVersion,
            'status' => $this->testCdnConnectivity()
        ];
    }
    
    /**
     * Test CDN connectivity
     */
    private function testCdnConnectivity() {
        if (!$this->cdnEnabled) {
            return 'disabled';
        }
        
        $testUrl = $this->cdnDomain . '/test-connection.txt';
        
        $context = stream_context_create([
            'http' => [
                'method' => 'HEAD',
                'timeout' => 5,
                'user_agent' => 'NextCode-CDN-Test/1.0'
            ]
        ]);
        
        $response = @file_get_contents($testUrl, false, $context);
        
        return $response !== false ? 'connected' : 'error';
    }
}

// Initialize CDN manager
$cdnManager = new CDNManager();

// Helper functions for easy access
function get_cdn_asset_url($path, $options = []) {
    global $cdnManager;
    return $cdnManager->getAssetUrl($path, $options);
}

function get_optimized_image_url($path, $options = []) {
    global $cdnManager;
    return $cdnManager->getOptimizedImageUrl($path, $options);
}

function get_responsive_image($path, $sizes, $alt = '', $class = '') {
    global $cdnManager;
    return $cdnManager->getResponsiveImage($path, $sizes, $alt, $class);
}

function get_critical_css_loader($cssFile, $criticalCssFile = '') {
    global $cdnManager;
    return $cdnManager->getCriticalCssLoader($cssFile, $criticalCssFile);
}

function enable_lazy_loading() {
    global $cdnManager;
    return $cdnManager->enableLazyLoading();
}

function get_cdn_status() {
    global $cdnManager;
    return $cdnManager->getCdnStatus();
}

// Set optimization headers
if (!headers_sent()) {
    $headers = $cdnManager->getOptimizationHeaders();
    foreach ($headers as $name => $value) {
        header("$name: $value");
    }
}

?>

