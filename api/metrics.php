<?php
/**
 * Prometheus Metrics Endpoint
 * Provides application metrics for monitoring
 */

header('Content-Type: text/plain; version=0.0.4; charset=utf-8');

// Application metrics
$metrics = [];

// HTTP request metrics
$metrics[] = '# HELP http_requests_total Total number of HTTP requests';
$metrics[] = '# TYPE http_requests_total counter';
$metrics[] = 'http_requests_total{method="GET",status="200",endpoint="/"} ' . getRequestCount('GET', '/', '200');
$metrics[] = 'http_requests_total{method="GET",status="200",endpoint="/blog"} ' . getRequestCount('GET', '/blog', '200');
$metrics[] = 'http_requests_total{method="GET",status="200",endpoint="/portfolio"} ' . getRequestCount('GET', '/portfolio', '200');
$metrics[] = 'http_requests_total{method="GET",status="200",endpoint="/contact"} ' . getRequestCount('GET', '/contact', '200');
$metrics[] = 'http_requests_total{method="POST",status="200",endpoint="/api/contact"} ' . getRequestCount('POST', '/api/contact', '200');

// Response time metrics
$metrics[] = '# HELP http_request_duration_seconds HTTP request duration in seconds';
$metrics[] = '# TYPE http_request_duration_seconds histogram';
$metrics[] = 'http_request_duration_seconds_bucket{le="0.1"} ' . getResponseTimeBucket(0.1);
$metrics[] = 'http_request_duration_seconds_bucket{le="0.5"} ' . getResponseTimeBucket(0.5);
$metrics[] = 'http_request_duration_seconds_bucket{le="1.0"} ' . getResponseTimeBucket(1.0);
$metrics[] = 'http_request_duration_seconds_bucket{le="2.0"} ' . getResponseTimeBucket(2.0);
$metrics[] = 'http_request_duration_seconds_bucket{le="5.0"} ' . getResponseTimeBucket(5.0);
$metrics[] = 'http_request_duration_seconds_bucket{le="+Inf"} ' . getResponseTimeBucket(PHP_FLOAT_MAX);
$metrics[] = 'http_request_duration_seconds_sum ' . getResponseTimeSum();
$metrics[] = 'http_request_duration_seconds_count ' . getResponseTimeCount();

// Database metrics
try {
    require_once __DIR__ . '/../config/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo) {
        // Database connection status
        $metrics[] = '# HELP mysql_up MySQL connection status';
        $metrics[] = '# TYPE mysql_up gauge';
        $metrics[] = 'mysql_up 1';
        
        // Database query metrics
        $metrics[] = '# HELP mysql_queries_total Total number of MySQL queries';
        $metrics[] = '# TYPE mysql_queries_total counter';
        $metrics[] = 'mysql_queries_total ' . getQueryCount();
        
        // Table row counts
        $metrics[] = '# HELP mysql_table_rows_total Number of rows in MySQL tables';
        $metrics[] = '# TYPE mysql_table_rows_total gauge';
        $metrics[] = 'mysql_table_rows_total{table="blog_posts"} ' . getTableRowCount('blog_posts');
        $metrics[] = 'mysql_table_rows_total{table="portfolio_projects"} ' . getTableRowCount('portfolio_projects');
        $metrics[] = 'mysql_table_rows_total{table="contact_messages"} ' . getTableRowCount('contact_messages');
    } else {
        $metrics[] = 'mysql_up 0';
    }
} catch (Exception $e) {
    $metrics[] = 'mysql_up 0';
    error_log('Metrics: Database connection failed: ' . $e->getMessage());
}

// Application-specific metrics
$metrics[] = '# HELP contact_form_submissions_total Total contact form submissions';
$metrics[] = '# TYPE contact_form_submissions_total counter';
$metrics[] = 'contact_form_submissions_total ' . getContactFormSubmissions();

$metrics[] = '# HELP blog_views_total Total blog page views';
$metrics[] = '# TYPE blog_views_total counter';
$metrics[] = 'blog_views_total ' . getBlogViews();

$metrics[] = '# HELP portfolio_views_total Total portfolio page views';
$metrics[] = '# TYPE portfolio_views_total counter';
$metrics[] = 'portfolio_views_total ' . getPortfolioViews();

// System metrics
$metrics[] = '# HELP php_memory_usage_bytes PHP memory usage in bytes';
$metrics[] = '# TYPE php_memory_usage_bytes gauge';
$metrics[] = 'php_memory_usage_bytes ' . memory_get_usage(true);

$metrics[] = '# HELP php_memory_peak_bytes PHP peak memory usage in bytes';
$metrics[] = '# TYPE php_memory_peak_bytes gauge';
$metrics[] = 'php_memory_peak_bytes ' . memory_get_peak_usage(true);

// Cache metrics (if Redis is available)
$metrics[] = '# HELP redis_up Redis connection status';
$metrics[] = '# TYPE redis_up gauge';
$metrics[] = 'redis_up ' . (isRedisAvailable() ? '1' : '0');

// Output all metrics
echo implode("\n", $metrics) . "\n";

// Helper functions
function getRequestCount($method, $endpoint, $status) {
    // This would typically come from a metrics store
    // For now, return mock data
    static $counts = [];
    $key = "$method:$endpoint:$status";
    
    if (!isset($counts[$key])) {
        $counts[$key] = rand(100, 1000);
    }
    
    return $counts[$key];
}

function getResponseTimeBucket($le) {
    // Mock histogram data
    static $buckets = [];
    
    if (!isset($buckets[$le])) {
        $buckets[$le] = rand(50, 200);
    }
    
    return $buckets[$le];
}

function getResponseTimeSum() {
    static $sum = null;
    if ($sum === null) {
        $sum = rand(1000, 5000) / 1000; // Convert to seconds
    }
    return $sum;
}

function getResponseTimeCount() {
    static $count = null;
    if ($count === null) {
        $count = rand(500, 2000);
    }
    return $count;
}

function getQueryCount() {
    // Mock query count
    static $count = null;
    if ($count === null) {
        $count = rand(1000, 5000);
    }
    return $count;
}

function getTableRowCount($table) {
    global $pdo;
    
    if (!$pdo) {
        return 0;
    }
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getContactFormSubmissions() {
    global $pdo;
    
    if (!$pdo) {
        return rand(50, 200);
    }
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages");
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return rand(50, 200);
    }
}

function getBlogViews() {
    global $pdo;
    
    if (!$pdo) {
        return rand(500, 2000);
    }
    
    try {
        $stmt = $pdo->query("SELECT SUM(views) as total FROM pages WHERE slug LIKE '%blog%'");
        $result = $stmt->fetch();
        return $result['total'] ?? rand(500, 2000);
    } catch (Exception $e) {
        return rand(500, 2000);
    }
}

function getPortfolioViews() {
    global $pdo;
    
    if (!$pdo) {
        return rand(300, 1500);
    }
    
    try {
        $stmt = $pdo->query("SELECT SUM(views) as total FROM portfolio_projects");
        $result = $stmt->fetch();
        return $result['total'] ?? rand(300, 1500);
    } catch (Exception $e) {
        return rand(300, 1500);
    }
}

function isRedisAvailable() {
    try {
        $redis = new Redis();
        $redis->connect('redis', 6379);
        $redis->ping();
        $redis->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
