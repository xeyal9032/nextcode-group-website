<?php
/**
 * Google Analytics 4 Configuration
 * NextCode Group Analytics Settings
 */

// Include API router for handling API requests
require_once __DIR__ . '/../api-router.php';

// Google Analytics 4 Measurement ID
// Bu ID'yi Google Analytics hesabınızdan alabilirsiniz
define('GA4_MEASUREMENT_ID', 'G-8FYSTD1FVH');

// Google Analytics 4 API Key (Enhanced Measurement için)
define('GA4_API_KEY', 'your_api_key_here');

// Google Analytics 4 Property ID
define('GA4_PROPERTY_ID', '123456789');

// Analytics Settings
$analyticsConfig = [
    // Basic Settings
    'measurement_id' => GA4_MEASUREMENT_ID,
    'property_id' => GA4_PROPERTY_ID,
    'api_key' => GA4_API_KEY,
    
    // Tracking Settings
    'enable_page_view_tracking' => true,
    'enable_user_property_tracking' => true,
    'enable_custom_event_tracking' => true,
    'enable_ecommerce_tracking' => true,
    'enable_enhanced_conversions' => true,
    'enable_performance_tracking' => true,
    
    // Privacy Settings
    'respect_do_not_track' => true,
    'anonymize_ip' => true,
    'disable_personalized_ads' => false,
    'cookie_consent_required' => true,
    
    // Custom Dimensions
    'custom_dimensions' => [
        'user_type' => 'custom_parameter_1',
        'subscription_level' => 'custom_parameter_2',
        'device_category' => 'custom_parameter_3',
        'browser_type' => 'custom_parameter_4',
        'os_type' => 'custom_parameter_5'
    ],
    
    // Custom Metrics
    'custom_metrics' => [
        'page_load_time' => 'custom_metric_1',
        'user_engagement_time' => 'custom_metric_2',
        'scroll_depth' => 'custom_metric_3',
        'form_completion_rate' => 'custom_metric_4'
    ],
    
    // Event Tracking
    'tracked_events' => [
        'page_view',
        'click',
        'scroll',
        'form_submit',
        'video_play',
        'video_pause',
        'video_complete',
        'error',
        'user_engagement',
        'performance_metrics',
        'view_item',
        'add_to_cart',
        'purchase'
    ],
    
    // E-commerce Settings
    'ecommerce' => [
        'currency' => 'USD',
        'tax_rate' => 0.08,
        'shipping_cost' => 5.99,
        'enable_product_impressions' => true,
        'enable_product_clicks' => true,
        'enable_add_to_cart' => true,
        'enable_purchase' => true
    ],
    
    // Performance Thresholds
    'performance_thresholds' => [
        'lcp_good' => 2500,
        'lcp_needs_improvement' => 4000,
        'fid_good' => 100,
        'fid_needs_improvement' => 300,
        'cls_good' => 0.1,
        'cls_needs_improvement' => 0.25,
        'fcp_good' => 1800,
        'fcp_needs_improvement' => 3000
    ],
    
    // Debug Settings
    'debug_mode' => false,
    'log_events' => true,
    'console_logging' => false,
    
    // Data Retention
    'data_retention_days' => 26,
    'user_data_deletion_enabled' => true,
    
    // Integration Settings
    'integrations' => [
        'google_ads' => false,
        'google_merchant_center' => false,
        'google_search_console' => false,
        'facebook_pixel' => false,
        'hotjar' => false
    ]
];

// Analytics Helper Functions
class AnalyticsHelper {
    
    /**
     * Analytics config'i döndür
     */
    public static function getConfig() {
        global $analyticsConfig;
        return $analyticsConfig;
    }
    
    /**
     * Measurement ID döndür
     */
    public static function getMeasurementId() {
        return GA4_MEASUREMENT_ID;
    }
    
    /**
     * Custom dimension key döndür
     */
    public static function getCustomDimensionKey($name) {
        global $analyticsConfig;
        return $analyticsConfig['custom_dimensions'][$name] ?? null;
    }
    
    /**
     * Custom metric key döndür
     */
    public static function getCustomMetricKey($name) {
        global $analyticsConfig;
        return $analyticsConfig['custom_metrics'][$name] ?? null;
    }
    
    /**
     * Event tracking aktif mi kontrol et
     */
    public static function isEventTrackingEnabled($eventName) {
        global $analyticsConfig;
        return in_array($eventName, $analyticsConfig['tracked_events']);
    }
    
    /**
     * E-commerce tracking aktif mi kontrol et
     */
    public static function isEcommerceEnabled() {
        global $analyticsConfig;
        return $analyticsConfig['enable_ecommerce_tracking'];
    }
    
    /**
     * Performance tracking aktif mi kontrol et
     */
    public static function isPerformanceTrackingEnabled() {
        global $analyticsConfig;
        return $analyticsConfig['enable_performance_tracking'];
    }
    
    /**
     * Debug mode aktif mi kontrol et
     */
    public static function isDebugMode() {
        global $analyticsConfig;
        return $analyticsConfig['debug_mode'];
    }
    
    /**
     * Analytics script tag'ini döndür
     */
    public static function getAnalyticsScript() {
        $measurementId = self::getMeasurementId();
        
        if (empty($measurementId) || $measurementId === 'G-XXXXXXXXXX') {
            return '<!-- Google Analytics 4: Measurement ID ayarlanmamış -->';
        }
        
        return "
        <!-- Google Analytics 4 -->
        <script async src=\"https://www.googletagmanager.com/gtag/js?id={$measurementId}\"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{$measurementId}', {
                page_title: document.title,
                page_location: window.location.href,
                send_page_view: true,
                custom_map: {
                    'custom_parameter_1': 'user_type',
                    'custom_parameter_2': 'subscription_level',
                    'custom_parameter_3': 'device_category',
                    'custom_parameter_4': 'browser_type',
                    'custom_parameter_5': 'os_type'
                }
            });
        </script>";
    }
    
    /**
     * Enhanced E-commerce script tag'ini döndür
     */
    public static function getEcommerceScript() {
        if (!self::isEcommerceEnabled()) {
            return '';
        }
        
        return "
        <!-- Enhanced E-commerce -->
        <script>
            // E-commerce tracking functions
            window.trackProductView = function(product) {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'view_item', {
                        currency: 'USD',
                        value: product.price,
                        items: [{
                            item_id: product.id,
                            item_name: product.name,
                            item_category: product.category,
                            price: product.price,
                            quantity: 1
                        }]
                    });
                }
            };
            
            window.trackAddToCart = function(product, quantity = 1) {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'add_to_cart', {
                        currency: 'USD',
                        value: product.price * quantity,
                        items: [{
                            item_id: product.id,
                            item_name: product.name,
                            item_category: product.category,
                            price: product.price,
                            quantity: quantity
                        }]
                    });
                }
            };
            
            window.trackPurchase = function(transaction) {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'purchase', {
                        transaction_id: transaction.id,
                        value: transaction.total,
                        tax: transaction.tax,
                        shipping: transaction.shipping,
                        currency: transaction.currency || 'USD',
                        items: transaction.items
                    });
                }
            };
        </script>";
    }
    
    /**
     * Performance tracking script tag'ini döndür
     */
    public static function getPerformanceScript() {
        if (!self::isPerformanceTrackingEnabled()) {
            return '';
        }
        
        return "
        <!-- Performance Tracking -->
        <script>
            // Performance tracking functions
            window.trackPerformanceMetrics = function() {
                if (typeof gtag !== 'undefined' && window.performance) {
                    const timing = performance.timing;
                    const navigation = performance.navigation;
                    
                    gtag('event', 'performance_metrics', {
                        page_load_time: timing.loadEventEnd - timing.navigationStart,
                        dom_content_loaded: timing.domContentLoadedEventEnd - timing.navigationStart,
                        time_to_first_byte: timing.responseStart - timing.navigationStart,
                        dns_lookup_time: timing.domainLookupEnd - timing.domainLookupStart,
                        tcp_connection_time: timing.connectEnd - timing.connectStart,
                        server_response_time: timing.responseEnd - timing.responseStart,
                        dom_processing_time: timing.domComplete - timing.domLoading,
                        resource_count: performance.getEntriesByType('resource').length
                    });
                }
            };
            
            // Page load sonrası performance metrics gönder
            if (document.readyState === 'complete') {
                trackPerformanceMetrics();
            } else {
                window.addEventListener('load', trackPerformanceMetrics);
            }
        </script>";
    }
    
    /**
     * Privacy consent script tag'ini döndür
     */
    public static function getPrivacyScript() {
        global $analyticsConfig;
        
        if (!$analyticsConfig['cookie_consent_required']) {
            return '';
        }
        
        return "
        <!-- Privacy Consent -->
        <script>
            // Cookie consent management
            window.analyticsConsent = {
                granted: false,
                required: true,
                
                grant: function() {
                    this.granted = true;
                    localStorage.setItem('analytics_consent', 'granted');
                    
                    // Enable analytics
                    if (typeof gtag !== 'undefined') {
                        gtag('consent', 'update', {
                            'analytics_storage': 'granted'
                        });
                    }
                    
                    // Show success message
                    this.showMessage('Analytics consent granted', 'success');
                },
                
                deny: function() {
                    this.granted = false;
                    localStorage.setItem('analytics_consent', 'denied');
                    
                    // Disable analytics
                    if (typeof gtag !== 'undefined') {
                        gtag('consent', 'update', {
                            'analytics_storage': 'denied'
                        });
                    }
                    
                    // Show message
                    this.showMessage('Analytics consent denied', 'info');
                },
                
                check: function() {
                    const consent = localStorage.getItem('analytics_consent');
                    this.granted = consent === 'granted';
                    return this.granted;
                },
                
                showMessage: function(message, type) {
                    // Simple message display
                    console.log('Analytics Consent:', message);
                }
            };
            
            // Check existing consent
            analyticsConsent.check();
            
            // Initialize consent
            if (typeof gtag !== 'undefined') {
                gtag('consent', 'default', {
                    'analytics_storage': analyticsConsent.granted ? 'granted' : 'denied'
                });
            }
        </script>";
    }
    
    /**
     * Tüm analytics script'lerini döndür
     */
    public static function getAllScripts() {
        return self::getAnalyticsScript() . 
               self::getEcommerceScript() . 
               self::getPerformanceScript() . 
               self::getPrivacyScript();
    }
}

// Analytics config'i export et
if (isset($exportConfig) && $exportConfig) {
    return $analyticsConfig;
}
?>
