<?php
/**
 * Google Analytics 4 Configuration
 * GA4 tracking and cross-domain setup
 */

// Check if secure access is defined
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class GoogleAnalytics {
    private $measurementId;
    private $apiSecret;
    private $domains;
    
    public function __construct() {
        // GA4 Measurement ID
        $this->measurementId = $_ENV['GA4_MEASUREMENT_ID'] ?? 'G-8FYSTD1FVH';
        $this->apiSecret = $_ENV['GA4_API_SECRET'] ?? '';
        
        // Cross-domain tracking domains
        $this->domains = [
            'nextcode.az',
            'www.nextcode.az',
            'nextcodegroup.ostwind.az' // Legacy domain
        ];
    }
    
    /**
     * Get GA4 tracking code
     */
    public function getTrackingCode() {
        ob_start();
        ?>
        <!-- Google Analytics 4 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $this->measurementId; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', '<?php echo $this->measurementId; ?>', {
                'send_page_view': true,
                'linker': {
                    'domains': <?php echo json_encode($this->domains); ?>,
                    'accept_incoming': true
                },
                'cookie_flags': 'SameSite=None;Secure',
                'anonymize_ip': true,
                'allow_google_signals': true,
                'allow_ad_personalization_signals': false
            });
            
            // Enhanced measurement
            gtag('config', '<?php echo $this->measurementId; ?>', {
                'page_title': document.title,
                'page_location': window.location.href,
                'page_path': window.location.pathname
            });
            
            // Track scrolls
            let scrollDepth = 0;
            window.addEventListener('scroll', function() {
                const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                const scrolled = Math.round((winScroll / height) * 100);
                
                if (scrolled > scrollDepth && scrolled % 25 === 0) {
                    scrollDepth = scrolled;
                    gtag('event', 'scroll', {
                        'percent_scrolled': scrolled
                    });
                }
            });
            
            // Track outbound links
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.hostname !== window.location.hostname) {
                    gtag('event', 'click', {
                        'event_category': 'outbound',
                        'event_label': link.href,
                        'transport_type': 'beacon'
                    });
                }
            });
            
            // Track file downloads
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href.match(/\.(pdf|zip|doc|docx|xls|xlsx|ppt|pptx)$/i)) {
                    gtag('event', 'file_download', {
                        'file_name': link.href.split('/').pop(),
                        'file_extension': link.href.split('.').pop(),
                        'link_url': link.href
                    });
                }
            });
            
            // Track form submissions
            document.addEventListener('submit', function(e) {
                const form = e.target;
                gtag('event', 'form_submit', {
                    'form_id': form.id || 'unknown',
                    'form_name': form.name || 'unknown',
                    'form_destination': form.action || window.location.href
                });
            });
            
            // Track video plays
            document.addEventListener('play', function(e) {
                if (e.target.tagName === 'VIDEO') {
                    gtag('event', 'video_start', {
                        'video_url': e.target.currentSrc || e.target.src,
                        'video_title': e.target.title || 'unknown'
                    });
                }
            }, true);
            
            // Track search
            if (window.location.search.includes('q=') || window.location.search.includes('search=')) {
                const urlParams = new URLSearchParams(window.location.search);
                const searchTerm = urlParams.get('q') || urlParams.get('search');
                
                if (searchTerm) {
                    gtag('event', 'search', {
                        'search_term': searchTerm
                    });
                }
            }
            
            // Track errors
            window.addEventListener('error', function(e) {
                gtag('event', 'exception', {
                    'description': e.message,
                    'fatal': false
                });
            });
            
            console.log('✅ Google Analytics 4 initialized');
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get Google Tag Manager code
     */
    public function getGTMCode($containerId = 'GTM-XXXXXXX') {
        ob_start();
        ?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?php echo $containerId; ?>');</script>
        <!-- End Google Tag Manager -->
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get GTM noscript code
     */
    public function getGTMNoscript($containerId = 'GTM-XXXXXXX') {
        ob_start();
        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $containerId; ?>"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
        return ob_get_clean();
    }
    
    /**
     * Track event via Measurement Protocol
     */
    public function trackEvent($clientId, $eventName, $params = []) {
        if (empty($this->apiSecret)) {
            return false;
        }
        
        $data = [
            'client_id' => $clientId,
            'events' => [
                [
                    'name' => $eventName,
                    'params' => $params
                ]
            ]
        ];
        
        $url = "https://www.google-analytics.com/mp/collect?measurement_id={$this->measurementId}&api_secret={$this->apiSecret}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 204;
    }
    
    /**
     * Track page view
     */
    public function trackPageView($clientId, $pageUrl, $pageTitle = '') {
        return $this->trackEvent($clientId, 'page_view', [
            'page_location' => $pageUrl,
            'page_title' => $pageTitle
        ]);
    }
    
    /**
     * Track conversion
     */
    public function trackConversion($clientId, $conversionName, $value = 0, $currency = 'USD') {
        return $this->trackEvent($clientId, $conversionName, [
            'value' => $value,
            'currency' => $currency
        ]);
    }
}

// Global instance
$GLOBALS['ga'] = new GoogleAnalytics();

/**
 * Helper function
 */
function ga() {
    return $GLOBALS['ga'];
}

