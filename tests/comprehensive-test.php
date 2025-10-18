<?php
// NextCode Group - Comprehensive Test Suite
// Bu dosya bütün saytın funksionallığını və performansını test edir

define('SECURE_ACCESS', true);

// Test konfigurasiyası
$test_config = [
    'show_details' => true,
    'show_performance' => true,
    'show_errors' => true,
    'auto_refresh' => false,
    'refresh_interval' => 30 // saniye
];

// Test sonuçları
$test_results = [
    'database' => [],
    'pages' => [],
    'api' => [],
    'images' => [],
    'performance' => [],
    'errors' => [],
    'overall' => 'pending'
];

// Include gerekli dosyalar
require_once 'config/database.php';
require_once 'includes/page_functions.php';
require_once 'includes/content_helper.php';

// Test başlangıç zamanı
$test_start_time = microtime(true);

?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextCode Group - Comprehensive Test Suite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/all.min.css" rel="stylesheet">
    <style>
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .test-section {
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        .test-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
        }
        .test-content {
            padding: 20px;
        }
        .test-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        .test-item:last-child {
            border-bottom: none;
        }
        .test-name {
            font-weight: 500;
        }
        .test-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-success {
            background: #d4edda;
            color: #155724;
        }
        .status-error {
            background: #f8d7da;
            color: #721c24;
        }
        .status-warning {
            background: #fff3cd;
            color: #856404;
        }
        .status-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        .performance-metric {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 10px 0;
        }
        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }
        .refresh-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .auto-refresh {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 1000;
        }
        .error-details {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 5px;
            font-family: monospace;
            font-size: 0.85rem;
        }
        .image-test {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .image-item {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
        }
        .image-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
        .image-info {
            padding: 10px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-vial me-2"></i>NextCode Group - Comprehensive Test Suite</h1>
            <div>
                <button class="btn btn-primary refresh-btn" onclick="runTests()">
                    <i class="fas fa-sync-alt me-1"></i>Test Et
                </button>
                <button class="btn btn-outline-secondary auto-refresh" onclick="toggleAutoRefresh()">
                    <i class="fas fa-clock me-1"></i>Auto Refresh
                </button>
            </div>
        </div>

        <?php
        // Test fonksiyonları
        function testDatabase() {
            global $pdo, $test_results;
            
            $tests = [
                'Database Connection' => function() use ($pdo) {
                    return $pdo ? 'Connected' : 'Failed';
                },
                'Tables Check' => function() use ($pdo) {
                    if (!$pdo) return 'No connection';
                    $tables = ['pages', 'blog_posts', 'portfolio_projects', 'contact_messages', 'site_settings'];
                    $existing = [];
                    foreach ($tables as $table) {
                        try {
                            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                            if ($stmt->rowCount() > 0) {
                                $existing[] = $table;
                            }
                        } catch (Exception $e) {
                            // Table doesn't exist
                        }
                    }
                    return count($existing) . '/' . count($tables) . ' tables exist';
                },
                'Data Integrity' => function() use ($pdo) {
                    if (!$pdo) return 'No connection';
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM pages WHERE status = 'published'");
                        $result = $stmt->fetch();
                        return $result['count'] . ' published pages';
                    } catch (Exception $e) {
                        return 'Error: ' . $e->getMessage();
                    }
                }
            ];
            
            foreach ($tests as $name => $test) {
                try {
                    $result = $test();
                    $status = strpos($result, 'Error') !== false ? 'error' : 'success';
                    $test_results['database'][] = [
                        'name' => $name,
                        'result' => $result,
                        'status' => $status
                    ];
                } catch (Exception $e) {
                    $test_results['database'][] = [
                        'name' => $name,
                        'result' => 'Exception: ' . $e->getMessage(),
                        'status' => 'error'
                    ];
                }
            }
        }

        function testPages() {
            global $test_results;
            
            $pages = [
                'index.php' => 'Ana Səhifə',
                'about.php' => 'Haqqımızda',
                'services.php' => 'Xidmətlər',
                'portfolio.php' => 'Portfolio',
                'blog.php' => 'Blog',
                'contact.php' => 'Əlaqə',
                'pricing.php' => 'Qiymətləndirmə',
                'faq.php' => 'SSS'
            ];
            
            foreach ($pages as $file => $name) {
                $test_results['pages'][] = [
                    'name' => $name,
                    'file' => $file,
                    'exists' => file_exists($file),
                    'readable' => file_exists($file) ? is_readable($file) : false,
                    'size' => file_exists($file) ? filesize($file) : 0
                ];
            }
        }

        function testAPI() {
            global $test_results;
            
            $api_endpoints = [
                'api/blog.php' => 'Blog API',
                'api/portfolio.php' => 'Portfolio API',
                'api/contact-info.php' => 'Contact Info API',
                'api/services.php' => 'Services API',
                'api/settings.php' => 'Settings API'
            ];
            
            foreach ($api_endpoints as $endpoint => $name) {
                $test_results['api'][] = [
                    'name' => $name,
                    'endpoint' => $endpoint,
                    'exists' => file_exists($endpoint),
                    'readable' => file_exists($endpoint) ? is_readable($endpoint) : false
                ];
            }
        }

        function testImages() {
            global $test_results;
            
            $image_dirs = [
                'images/' => 'Ana görsel klasörü',
                'images/blog/' => 'Blog görselleri',
                'images/portfolio/' => 'Portfolio görselleri',
                'images/testimonials/' => 'Testimonial görselleri'
            ];
            
            foreach ($image_dirs as $dir => $name) {
                if (is_dir($dir)) {
                    $files = glob($dir . '*.{jpg,jpeg,png,gif,svg,webp}', GLOB_BRACE);
                    $total_size = 0;
                    foreach ($files as $file) {
                        $total_size += filesize($file);
                    }
                    
                    $test_results['images'][] = [
                        'name' => $name,
                        'directory' => $dir,
                        'file_count' => count($files),
                        'total_size' => $total_size,
                        'files' => array_slice($files, 0, 5) // İlk 5 dosya
                    ];
                } else {
                    $test_results['images'][] = [
                        'name' => $name,
                        'directory' => $dir,
                        'file_count' => 0,
                        'total_size' => 0,
                        'files' => []
                    ];
                }
            }
        }

        function testPerformance() {
            global $test_results, $test_start_time;
            
            $memory_usage = memory_get_usage(true);
            $peak_memory = memory_get_peak_usage(true);
            $execution_time = microtime(true) - $test_start_time;
            
            $test_results['performance'] = [
                'memory_usage' => $memory_usage,
                'peak_memory' => $peak_memory,
                'execution_time' => $execution_time,
                'php_version' => PHP_VERSION,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'
            ];
        }

        function testErrors() {
            global $test_results;
            
            $error_logs = [
                'logs/error.log' => 'Error Log',
                'logs/performance-monitor.log' => 'Performance Log',
                'logs/enhanced-backup.log' => 'Backup Log'
            ];
            
            foreach ($error_logs as $file => $name) {
                if (file_exists($file)) {
                    $size = filesize($file);
                    $modified = date('Y-m-d H:i:s', filemtime($file));
                    $test_results['errors'][] = [
                        'name' => $name,
                        'file' => $file,
                        'size' => $size,
                        'modified' => $modified,
                        'has_errors' => $size > 0
                    ];
                } else {
                    $test_results['errors'][] = [
                        'name' => $name,
                        'file' => $file,
                        'size' => 0,
                        'modified' => 'Never',
                        'has_errors' => false
                    ];
                }
            }
        }

        // Testleri çalıştır
        testDatabase();
        testPages();
        testAPI();
        testImages();
        testPerformance();
        testErrors();

        // Genel durum hesapla
        $total_tests = 0;
        $passed_tests = 0;
        
        foreach ($test_results['database'] as $test) {
            $total_tests++;
            if ($test['status'] === 'success') $passed_tests++;
        }
        
        foreach ($test_results['pages'] as $test) {
            $total_tests++;
            if ($test['exists'] && $test['readable']) $passed_tests++;
        }
        
        $test_results['overall'] = [
            'total' => $total_tests,
            'passed' => $passed_tests,
            'percentage' => $total_tests > 0 ? round(($passed_tests / $total_tests) * 100, 2) : 0
        ];
        ?>

        <!-- Database Test Results -->
        <div class="test-section">
            <div class="test-header">
                <i class="fas fa-database me-2"></i>Database Test Results
            </div>
            <div class="test-content">
                <?php foreach ($test_results['database'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?></span>
                    <span class="test-status status-<?php echo $test['status']; ?>">
                        <?php echo htmlspecialchars($test['result']); ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pages Test Results -->
        <div class="test-section">
            <div class="test-header">
                <i class="fas fa-file-alt me-2"></i>Pages Test Results
            </div>
            <div class="test-content">
                <?php foreach ($test_results['pages'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?> (<?php echo $test['file']; ?>)</span>
                    <span class="test-status status-<?php echo ($test['exists'] && $test['readable']) ? 'success' : 'error'; ?>">
                        <?php 
                        if ($test['exists'] && $test['readable']) {
                            echo 'OK (' . round($test['size'] / 1024, 2) . ' KB)';
                        } else {
                            echo 'Missing or not readable';
                        }
                        ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- API Test Results -->
        <div class="test-section">
            <div class="test-header">
                <i class="fas fa-code me-2"></i>API Test Results
            </div>
            <div class="test-content">
                <?php foreach ($test_results['api'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?></span>
                    <span class="test-status status-<?php echo ($test['exists'] && $test['readable']) ? 'success' : 'error'; ?>">
                        <?php echo ($test['exists'] && $test['readable']) ? 'Available' : 'Not Found'; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Images Test Results -->
        <div class="test-section">
            <div class="test-header">
                <i class="fas fa-images me-2"></i>Images Test Results
            </div>
            <div class="test-content">
                <?php foreach ($test_results['images'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?></span>
                    <span class="test-status status-<?php echo $test['file_count'] > 0 ? 'success' : 'warning'; ?>">
                        <?php echo $test['file_count']; ?> files (<?php echo round($test['total_size'] / 1024, 2); ?> KB)
                    </span>
                </div>
                <?php if (!empty($test['files'])): ?>
                <div class="image-test">
                    <?php foreach ($test['files'] as $file): ?>
                    <div class="image-item">
                        <img src="<?php echo htmlspecialchars($file); ?>" alt="Test Image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlIE5vdCBGb3VuZDwvdGV4dD48L3N2Zz4='">
                        <div class="image-info">
                            <strong><?php echo basename($file); ?></strong><br>
                            <?php echo round(filesize($file) / 1024, 2); ?> KB
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Performance Test Results -->
        <div class="test-section">
            <div class="test-header">
                <i class="fas fa-tachometer-alt me-2"></i>Performance Test Results
            </div>
            <div class="test-content">
                <div class="performance-metric">
                    <div class="metric-value"><?php echo round($test_results['performance']['execution_time'] * 1000, 2); ?>ms</div>
                    <div>Execution Time</div>
                </div>
                <div class="performance-metric">
                    <div class="metric-value"><?php echo round($test_results['performance']['memory_usage'] / 1024 / 1024, 2); ?>MB</div>
                    <div>Memory Usage</div>
                </div>
                <div class="performance-metric">
                    <div class="metric-value"><?php echo round($test_results['performance']['peak_memory'] / 1024 / 1024, 2); ?>MB</div>
                    <div>Peak Memory</div>
                </div>
                <div class="performance-metric">
                    <div class="metric-value"><?php echo $test_results['performance']['php_version']; ?></div>
                    <div>PHP Version</div>
                </div>
            </div>
        </div>

        <!-- Error Logs Test Results -->
        <div class="test-section">
            <div class="test-header">
                <i class="fas fa-exclamation-triangle me-2"></i>Error Logs Test Results
            </div>
            <div class="test-content">
                <?php foreach ($test_results['errors'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?></span>
                    <span class="test-status status-<?php echo $test['has_errors'] ? 'warning' : 'success'; ?>">
                        <?php 
                        if ($test['size'] > 0) {
                            echo round($test['size'] / 1024, 2) . ' KB - ' . $test['modified'];
                        } else {
                            echo 'No errors';
                        }
                        ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Overall Test Results -->
        <div class="test-section">
            <div class="test-header">
                <i class="fas fa-chart-line me-2"></i>Overall Test Results
            </div>
            <div class="test-content">
                <div class="performance-metric">
                    <div class="metric-value"><?php echo $test_results['overall']['percentage']; ?>%</div>
                    <div>Success Rate</div>
                </div>
                <div class="performance-metric">
                    <div class="metric-value"><?php echo $test_results['overall']['passed']; ?>/<?php echo $test_results['overall']['total']; ?></div>
                    <div>Tests Passed</div>
                </div>
                <div class="performance-metric">
                    <div class="metric-value"><?php echo date('Y-m-d H:i:s'); ?></div>
                    <div>Last Test</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="test-section">
            <div class="test-header">
                <i class="fas fa-tools me-2"></i>Quick Actions
            </div>
            <div class="test-content">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="index.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-home me-1"></i>Ana Səhifə
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="blog.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-blog me-1"></i>Blog
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="portfolio.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-briefcase me-1"></i>Portfolio
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="contact.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-envelope me-1"></i>Əlaqə
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let autoRefreshInterval = null;
        let autoRefreshEnabled = false;

        function runTests() {
            location.reload();
        }

        function toggleAutoRefresh() {
            if (autoRefreshEnabled) {
                clearInterval(autoRefreshInterval);
                autoRefreshEnabled = false;
                document.querySelector('.auto-refresh').innerHTML = '<i class="fas fa-clock me-1"></i>Auto Refresh';
                document.querySelector('.auto-refresh').classList.remove('btn-success');
                document.querySelector('.auto-refresh').classList.add('btn-outline-secondary');
            } else {
                autoRefreshInterval = setInterval(runTests, <?php echo $test_config['refresh_interval'] * 1000; ?>);
                autoRefreshEnabled = true;
                document.querySelector('.auto-refresh').innerHTML = '<i class="fas fa-stop me-1"></i>Stop Auto';
                document.querySelector('.auto-refresh').classList.remove('btn-outline-secondary');
                document.querySelector('.auto-refresh').classList.add('btn-success');
            }
        }

        // Sayfa yüklendiğinde otomatik test çalıştır
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🧪 Comprehensive Test Suite Loaded');
            console.log('📊 Test Results:', <?php echo json_encode($test_results); ?>);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                runTests();
            }
            if (e.ctrlKey && e.key === 'a') {
                e.preventDefault();
                toggleAutoRefresh();
            }
        });
    </script>
</body>
</html>
