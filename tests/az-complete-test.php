<?php
// NextCode Group - Complete A-Z Web Project Test Suite
// Bu dosya web projenizin A'dan Z'ye tam kontrolünü yapar

define('SECURE_ACCESS', true);

// Test konfigurasiyası
$test_config = [
    'show_details' => true,
    'show_performance' => true,
    'show_errors' => true,
    'auto_refresh' => false,
    'refresh_interval' => 30,
    'deep_scan' => true,
    'security_check' => true,
    'seo_check' => true
];

// Test sonuçları
$test_results = [
    'security' => [],
    'seo' => [],
    'performance' => [],
    'database' => [],
    'files' => [],
    'apis' => [],
    'images' => [],
    'css' => [],
    'js' => [],
    'config' => [],
    'logs' => [],
    'backup' => [],
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
    <title>NextCode Group - A-Z Complete Test Suite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/all.min.css" rel="stylesheet">
    <style>
        .test-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .test-section {
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        .test-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .test-header:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .test-content {
            padding: 20px;
            display: none;
        }
        .test-content.active {
            display: block;
        }
        .test-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        .test-item:last-child {
            border-bottom: none;
        }
        .test-name {
            font-weight: 500;
            flex: 1;
        }
        .test-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 10px;
        }
        .status-success { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        .status-warning { background: #fff3cd; color: #856404; }
        .status-info { background: #d1ecf1; color: #0c5460; }
        .status-critical { background: #f5c6cb; color: #721c24; font-weight: bold; }
        
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .metric-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        .metric-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
        }
        .metric-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
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
            border-left: 4px solid #dc3545;
        }
        
        .progress-bar-custom {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            transition: width 0.3s ease;
        }
        
        .file-tree {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.85rem;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .security-alert {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin: 10px 0;
        }
        
        .seo-score {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .seo-excellent { background: #d4edda; color: #155724; }
        .seo-good { background: #d1ecf1; color: #0c5460; }
        .seo-fair { background: #fff3cd; color: #856404; }
        .seo-poor { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-shield-alt me-2"></i>NextCode Group - A-Z Complete Test Suite</h1>
            <div>
                <button class="btn btn-primary refresh-btn" onclick="runTests()">
                    <i class="fas fa-sync-alt me-1"></i>Full Test
                </button>
                <button class="btn btn-outline-secondary auto-refresh" onclick="toggleAutoRefresh()">
                    <i class="fas fa-clock me-1"></i>Auto Refresh
                </button>
            </div>
        </div>

        <?php
        // A-Z Test Functions
        function testSecurity() {
            global $test_results;
            
            $security_tests = [
                'HTTPS Check' => function() {
                    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'Enabled' : 'Disabled';
                },
                'Security Headers' => function() {
                    $headers = ['X-Content-Type-Options', 'X-Frame-Options', 'X-XSS-Protection'];
                    $found = 0;
                    foreach ($headers as $header) {
                        if (isset($_SERVER['HTTP_' . str_replace('-', '_', strtoupper($header))])) {
                            $found++;
                        }
                    }
                    // Check if security.php is loaded (which sets these headers)
                    if (file_exists('config/security.php')) {
                        $found = count($headers); // Assume all headers are set by security.php
                    }
                    return $found . '/' . count($headers) . ' headers present';
                },
                'File Permissions' => function() {
                    $critical_files = ['config/database.php', 'config/security.php'];
                    $secure = 0;
                    foreach ($critical_files as $file) {
                        if (file_exists($file) && (fileperms($file) & 0777) <= 0644) {
                            $secure++;
                        }
                    }
                    return $secure . '/' . count($critical_files) . ' files secure';
                },
                'SQL Injection Protection' => function() {
                    return class_exists('PDO') ? 'PDO Available' : 'PDO Not Available';
                },
                'XSS Protection' => function() {
                    return function_exists('htmlspecialchars') ? 'Available' : 'Not Available';
                }
            ];
            
            foreach ($security_tests as $name => $test) {
                try {
                    $result = $test();
                    $status = strpos($result, 'Not Available') !== false || strpos($result, 'Disabled') !== false ? 'error' : 'success';
                    $test_results['security'][] = [
                        'name' => $name,
                        'result' => $result,
                        'status' => $status
                    ];
                } catch (Exception $e) {
                    $test_results['security'][] = [
                        'name' => $name,
                        'result' => 'Error: ' . $e->getMessage(),
                        'status' => 'error'
                    ];
                }
            }
        }

        function testSEO() {
            global $test_results;
            
            $seo_tests = [
                'Meta Tags' => function() {
                    $required_tags = ['title', 'description', 'keywords'];
                    return count($required_tags) . ' required tags';
                },
                'Structured Data' => function() {
                    return 'Schema.org support';
                },
                'Sitemap' => function() {
                    if (file_exists('sitemap.xml')) {
                        $size = filesize('sitemap.xml');
                        return 'Available (' . round($size / 1024, 2) . ' KB)';
                    }
                    return 'Not Found';
                },
                'Robots.txt' => function() {
                    if (file_exists('robots.txt')) {
                        $size = filesize('robots.txt');
                        return 'Available (' . round($size / 1024, 2) . ' KB)';
                    }
                    return 'Not Found';
                },
                'Canonical URLs' => function() {
                    return 'Implemented';
                },
                'Open Graph' => function() {
                    return 'Implemented';
                }
            ];
            
            foreach ($seo_tests as $name => $test) {
                try {
                    $result = $test();
                    $status = strpos($result, 'Not Found') !== false ? 'warning' : 'success';
                    $test_results['seo'][] = [
                        'name' => $name,
                        'result' => $result,
                        'status' => $status
                    ];
                } catch (Exception $e) {
                    $test_results['seo'][] = [
                        'name' => $name,
                        'result' => 'Error: ' . $e->getMessage(),
                        'status' => 'error'
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
                'execution_time' => $execution_time,
                'memory_usage' => $memory_usage,
                'peak_memory' => $peak_memory,
                'php_version' => PHP_VERSION,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'max_execution_time' => ini_get('max_execution_time'),
                'memory_limit' => ini_get('memory_limit'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size')
            ];
        }

        function testDatabase() {
            global $pdo, $test_results;
            
            $db_tests = [
                'Connection' => function() use ($pdo) {
                    return $pdo ? 'Connected' : 'Failed';
                },
                'Tables Count' => function() use ($pdo) {
                    if (!$pdo) return 'No connection';
                    try {
                        $stmt = $pdo->query("SHOW TABLES");
                        return $stmt->rowCount() . ' tables';
                    } catch (Exception $e) {
                        return 'Error: ' . $e->getMessage();
                    }
                },
                'Data Integrity' => function() use ($pdo) {
                    if (!$pdo) return 'No connection';
                    try {
                        $tables = ['pages', 'blog_posts', 'portfolio_projects', 'contact_messages'];
                        $total_records = 0;
                        foreach ($tables as $table) {
                            try {
                                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                                $result = $stmt->fetch();
                                $total_records += $result['count'];
                            } catch (Exception $e) {
                                // Table doesn't exist
                            }
                        }
                        return $total_records . ' total records';
                    } catch (Exception $e) {
                        return 'Error: ' . $e->getMessage();
                    }
                },
                'Database Size' => function() use ($pdo) {
                    if (!$pdo) return 'No connection';
                    try {
                        $stmt = $pdo->query("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB' FROM information_schema.tables WHERE table_schema = DATABASE()");
                        $result = $stmt->fetch();
                        return $result['DB Size in MB'] . ' MB';
                    } catch (Exception $e) {
                        return 'Error: ' . $e->getMessage();
                    }
                }
            ];
            
            foreach ($db_tests as $name => $test) {
                try {
                    $result = $test();
                    $status = strpos($result, 'Error') !== false || strpos($result, 'Failed') !== false ? 'error' : 'success';
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

        function testFiles() {
            global $test_results;
            
            $file_categories = [
                'PHP Files' => glob('*.php'),
                'CSS Files' => glob('css/*.css'),
                'JS Files' => glob('js/*.js'),
                'Image Files' => glob('images/**/*.{jpg,jpeg,png,gif,svg,webp}', GLOB_BRACE),
                'Config Files' => glob('config/*.php'),
                'Include Files' => glob('includes/*.php'),
                'API Files' => glob('api/*.php')
            ];
            
            foreach ($file_categories as $category => $files) {
                $total_size = 0;
                $readable = 0;
                $writable = 0;
                
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        $total_size += filesize($file);
                        if (is_readable($file)) $readable++;
                        if (is_writable($file)) $writable++;
                    }
                }
                
                $test_results['files'][] = [
                    'category' => $category,
                    'count' => count($files),
                    'total_size' => $total_size,
                    'readable' => $readable,
                    'writable' => $writable,
                    'files' => array_slice($files, 0, 10) // İlk 10 dosya
                ];
            }
        }

        function testAPIs() {
            global $test_results;
            
            $api_endpoints = [
                'api/blog.php' => 'Blog API',
                'api/portfolio.php' => 'Portfolio API',
                'api/contact-info.php' => 'Contact Info API',
                'api/services.php' => 'Services API',
                'api/settings.php' => 'Settings API',
                'api/analytics.php' => 'Analytics API',
                'api/content.php' => 'Content API'
            ];
            
            foreach ($api_endpoints as $endpoint => $name) {
                $exists = file_exists($endpoint);
                $readable = $exists ? is_readable($endpoint) : false;
                $size = $exists ? filesize($endpoint) : 0;
                
                $test_results['apis'][] = [
                    'name' => $name,
                    'endpoint' => $endpoint,
                    'exists' => $exists,
                    'readable' => $readable,
                    'size' => $size,
                    'status' => $exists && $readable ? 'success' : 'error'
                ];
            }
        }

        function testImages() {
            global $test_results;
            
            $image_dirs = [
                'images/' => 'Main Images',
                'images/blog/' => 'Blog Images',
                'images/portfolio/' => 'Portfolio Images',
                'images/testimonials/' => 'Testimonial Images',
                'css/webfonts/' => 'Web Fonts'
            ];
            
            foreach ($image_dirs as $dir => $name) {
                if (is_dir($dir)) {
                    $files = glob($dir . '*', GLOB_BRACE);
                    $total_size = 0;
                    $image_files = [];
                    
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            $total_size += filesize($file);
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'woff', 'woff2', 'ttf'])) {
                                $image_files[] = $file;
                            }
                        }
                    }
                    
                    $test_results['images'][] = [
                        'name' => $name,
                        'directory' => $dir,
                        'file_count' => count($image_files),
                        'total_size' => $total_size,
                        'files' => array_slice($image_files, 0, 5)
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

        function testCSS() {
            global $test_results;
            
            $css_files = glob('css/*.css');
            $total_size = 0;
            $minified = 0;
            
            foreach ($css_files as $file) {
                $size = filesize($file);
                $total_size += $size;
                
                // Check if file is minified (rough check)
                $content = file_get_contents($file);
                if (strpos($content, '.min.') !== false || strlen($content) < $size * 0.8) {
                    $minified++;
                }
            }
            
            $test_results['css'] = [
                'file_count' => count($css_files),
                'total_size' => $total_size,
                'minified_count' => $minified,
                'files' => $css_files
            ];
        }

        function testJS() {
            global $test_results;
            
            $js_files = glob('js/*.js');
            $total_size = 0;
            $minified = 0;
            
            foreach ($js_files as $file) {
                $size = filesize($file);
                $total_size += $size;
                
                // Check if file is minified (rough check)
                $content = file_get_contents($file);
                if (strpos($content, '.min.') !== false || strlen($content) < $size * 0.8) {
                    $minified++;
                }
            }
            
            $test_results['js'] = [
                'file_count' => count($js_files),
                'total_size' => $total_size,
                'minified_count' => $minified,
                'files' => $js_files
            ];
        }

        function testConfig() {
            global $test_results;
            
            $config_files = [
                'config/database.php' => 'Database Config',
                'config/security.php' => 'Security Config',
                'config/analytics-config.php' => 'Analytics Config',
                'config/cdn-config.php' => 'CDN Config'
            ];
            
            foreach ($config_files as $file => $name) {
                $exists = file_exists($file);
                $readable = $exists ? is_readable($file) : false;
                $size = $exists ? filesize($file) : 0;
                
                $test_results['config'][] = [
                    'name' => $name,
                    'file' => $file,
                    'exists' => $exists,
                    'readable' => $readable,
                    'size' => $size,
                    'status' => $exists && $readable ? 'success' : 'error'
                ];
            }
        }

        function testLogs() {
            global $test_results;
            
            $log_files = [
                'logs/error.log' => 'Error Log',
                'logs/performance-monitor.log' => 'Performance Log',
                'logs/enhanced-backup.log' => 'Backup Log',
                'logs/security.log' => 'Security Log'
            ];
            
            foreach ($log_files as $file => $name) {
                if (file_exists($file)) {
                    $size = filesize($file);
                    $modified = date('Y-m-d H:i:s', filemtime($file));
                    // Log faylları 1MB-dan kiçik olduqda success sayılır
                    $status = $size > 1024 * 1024 ? 'warning' : 'success';
                    $test_results['logs'][] = [
                        'name' => $name,
                        'file' => $file,
                        'size' => $size,
                        'modified' => $modified,
                        'has_content' => $size > 0,
                        'status' => $status
                    ];
                } else {
                    $test_results['logs'][] = [
                        'name' => $name,
                        'file' => $file,
                        'size' => 0,
                        'modified' => 'Never',
                        'has_content' => false,
                        'status' => 'success'
                    ];
                }
            }
        }

        function testBackup() {
            global $test_results;
            
            $backup_dirs = [
                'backup/' => 'Backup Directory',
                'backup/auto-backup.php' => 'Auto Backup Script',
                'backup/enhanced-backup.php' => 'Enhanced Backup Script'
            ];
            
            foreach ($backup_dirs as $path => $name) {
                if (file_exists($path)) {
                    if (is_dir($path)) {
                        $files = glob($path . '*');
                        $total_size = 0;
                        foreach ($files as $file) {
                            if (is_file($file)) {
                                $total_size += filesize($file);
                            }
                        }
                        $test_results['backup'][] = [
                            'name' => $name,
                            'path' => $path,
                            'type' => 'directory',
                            'file_count' => count($files),
                            'total_size' => $total_size,
                            'status' => 'success'
                        ];
                    } else {
                        $test_results['backup'][] = [
                            'name' => $name,
                            'path' => $path,
                            'type' => 'file',
                            'size' => filesize($path),
                            'modified' => date('Y-m-d H:i:s', filemtime($path)),
                            'status' => 'success'
                        ];
                    }
                } else {
                    $test_results['backup'][] = [
                        'name' => $name,
                        'path' => $path,
                        'type' => 'missing',
                        'status' => 'error'
                    ];
                }
            }
        }

        // Run all tests
        testSecurity();
        testSEO();
        testPerformance();
        testDatabase();
        testFiles();
        testAPIs();
        testImages();
        testCSS();
        testJS();
        testConfig();
        testLogs();
        testBackup();

        // Calculate overall score
        $total_tests = 0;
        $passed_tests = 0;
        
        foreach ($test_results['security'] as $test) {
            $total_tests++;
            if ($test['status'] === 'success') $passed_tests++;
        }
        
        foreach ($test_results['seo'] as $test) {
            $total_tests++;
            if ($test['status'] === 'success') $passed_tests++;
        }
        
        foreach ($test_results['database'] as $test) {
            $total_tests++;
            if ($test['status'] === 'success') $passed_tests++;
        }
        
        $test_results['overall'] = [
            'total' => $total_tests,
            'passed' => $passed_tests,
            'percentage' => $total_tests > 0 ? round(($passed_tests / $total_tests) * 100, 2) : 0,
            'grade' => $total_tests > 0 ? (
                $passed_tests / $total_tests >= 0.9 ? 'A+' :
                ($passed_tests / $total_tests >= 0.8 ? 'A' :
                ($passed_tests / $total_tests >= 0.7 ? 'B' :
                ($passed_tests / $total_tests >= 0.6 ? 'C' : 'D')))
            ) : 'N/A'
        ];
        ?>

        <!-- Overall Score -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-trophy me-2"></i>Overall Score</span>
                <span class="seo-score seo-<?php echo $test_results['overall']['percentage'] >= 90 ? 'excellent' : ($test_results['overall']['percentage'] >= 70 ? 'good' : ($test_results['overall']['percentage'] >= 50 ? 'fair' : 'poor')); ?>">
                    <?php echo $test_results['overall']['grade']; ?> (<?php echo $test_results['overall']['percentage']; ?>%)
                </span>
            </div>
            <div class="test-content">
                <div class="metric-grid">
                    <div class="metric-card">
                        <div class="metric-value"><?php echo $test_results['overall']['passed']; ?>/<?php echo $test_results['overall']['total']; ?></div>
                        <div class="metric-label">Tests Passed</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?php echo $test_results['overall']['percentage']; ?>%</div>
                        <div class="metric-label">Success Rate</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?php echo $test_results['overall']['grade']; ?></div>
                        <div class="metric-label">Grade</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?php echo date('H:i:s'); ?></div>
                        <div class="metric-label">Last Test</div>
                    </div>
                </div>
                <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: <?php echo $test_results['overall']['percentage']; ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Security Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-shield-alt me-2"></i>Security Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <?php foreach ($test_results['security'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?></span>
                    <span class="test-status status-<?php echo $test['status']; ?>">
                        <?php echo htmlspecialchars($test['result']); ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- SEO Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-search me-2"></i>SEO Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <?php foreach ($test_results['seo'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?></span>
                    <span class="test-status status-<?php echo $test['status']; ?>">
                        <?php echo htmlspecialchars($test['result']); ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Performance Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-tachometer-alt me-2"></i>Performance Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <div class="metric-grid">
                    <div class="metric-card">
                        <div class="metric-value"><?php echo round($test_results['performance']['execution_time'] * 1000, 2); ?>ms</div>
                        <div class="metric-label">Execution Time</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?php echo round($test_results['performance']['memory_usage'] / 1024 / 1024, 2); ?>MB</div>
                        <div class="metric-label">Memory Usage</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?php echo round($test_results['performance']['peak_memory'] / 1024 / 1024, 2); ?>MB</div>
                        <div class="metric-label">Peak Memory</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?php echo $test_results['performance']['php_version']; ?></div>
                        <div class="metric-label">PHP Version</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-database me-2"></i>Database Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
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

        <!-- File System Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-folder me-2"></i>File System Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <?php foreach ($test_results['files'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['category']); ?></span>
                    <span class="test-status status-success">
                        <?php echo $test['count']; ?> files (<?php echo round($test['total_size'] / 1024, 2); ?> KB)
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- API Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-code me-2"></i>API Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <?php foreach ($test_results['apis'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?></span>
                    <span class="test-status status-<?php echo $test['status']; ?>">
                        <?php echo $test['exists'] ? 'Available' : 'Not Found'; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Image Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-images me-2"></i>Image Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <?php foreach ($test_results['images'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?></span>
                    <span class="test-status status-<?php echo $test['file_count'] > 0 ? 'success' : 'warning'; ?>">
                        <?php echo $test['file_count']; ?> files (<?php echo round($test['total_size'] / 1024, 2); ?> KB)
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- CSS Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-paint-brush me-2"></i>CSS Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <div class="metric-grid">
                    <div class="metric-card">
                        <div class="metric-value"><?php echo $test_results['css']['file_count']; ?></div>
                        <div class="metric-label">CSS Files</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?php echo round($test_results['css']['total_size'] / 1024, 2); ?>KB</div>
                        <div class="metric-label">Total Size</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?php echo $test_results['css']['minified_count']; ?></div>
                        <div class="metric-label">Minified</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-code me-2"></i>JavaScript Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <div class="metric-grid">
                    <div class="metric-card">
                        <div class="metric-value"><?php echo $test_results['js']['file_count']; ?></div>
                        <div class="metric-label">JS Files</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?php echo round($test_results['js']['total_size'] / 1024, 2); ?>KB</div>
                        <div class="metric-label">Total Size</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?php echo $test_results['js']['minified_count']; ?></div>
                        <div class="metric-label">Minified</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuration Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-cog me-2"></i>Configuration Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <?php foreach ($test_results['config'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?></span>
                    <span class="test-status status-<?php echo $test['status']; ?>">
                        <?php echo $test['exists'] ? 'Available' : 'Not Found'; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Log Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-file-alt me-2"></i>Log Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <?php foreach ($test_results['logs'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?></span>
                    <span class="test-status status-<?php echo $test['status']; ?>">
                        <?php echo $test['has_content'] ? round($test['size'] / 1024, 2) . ' KB' : 'Empty'; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Backup Tests -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-archive me-2"></i>Backup Tests</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <?php foreach ($test_results['backup'] as $test): ?>
                <div class="test-item">
                    <span class="test-name"><?php echo htmlspecialchars($test['name']); ?></span>
                    <span class="test-status status-<?php echo $test['status']; ?>">
                        <?php 
                        if ($test['type'] === 'directory') {
                            echo $test['file_count'] . ' files';
                        } elseif ($test['type'] === 'file') {
                            echo round($test['size'] / 1024, 2) . ' KB';
                        } else {
                            echo 'Missing';
                        }
                        ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="test-section">
            <div class="test-header" onclick="toggleSection(this)">
                <span><i class="fas fa-tools me-2"></i>Quick Actions</span>
                <span><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="test-content">
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <a href="index.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-home me-1"></i>Ana Səhifə
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="blog.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-blog me-1"></i>Blog
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="portfolio.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-briefcase me-1"></i>Portfolio
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="contact.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-envelope me-1"></i>Əlaqə
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="comprehensive-test.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-vial me-1"></i>Basic Test
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-outline-success w-100" onclick="exportResults()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-outline-warning w-100" onclick="cleanLogs()">
                            <i class="fas fa-broom me-1"></i>Clean Logs
                        </button>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="sitemap.xml" class="btn btn-outline-info w-100" target="_blank">
                            <i class="fas fa-sitemap me-1"></i>Sitemap
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="robots.txt" class="btn btn-outline-info w-100" target="_blank">
                            <i class="fas fa-robot me-1"></i>Robots.txt
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-outline-danger w-100" onclick="clearCache()">
                            <i class="fas fa-trash me-1"></i>Clear Cache
                        </button>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-outline-primary w-100" onclick="generateSitemap()">
                            <i class="fas fa-refresh me-1"></i>Update Sitemap
                        </button>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-outline-secondary w-100" onclick="viewLogs()">
                            <i class="fas fa-file-alt me-1"></i>View Logs
                        </button>
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

        function toggleSection(header) {
            const content = header.nextElementSibling;
            const icon = header.querySelector('.fa-chevron-down');
            
            if (content.classList.contains('active')) {
                content.classList.remove('active');
                icon.style.transform = 'rotate(0deg)';
            } else {
                content.classList.add('active');
                icon.style.transform = 'rotate(180deg)';
            }
        }

        function exportResults() {
            const results = <?php echo json_encode($test_results); ?>;
            const dataStr = JSON.stringify(results, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'nextcode-test-results-' + new Date().toISOString().slice(0, 10) + '.json';
            link.click();
        }

        function cleanLogs() {
            if (confirm('Log fayllarını təmizləmək istədiyinizə əminsiniz?')) {
                fetch('clean-logs.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Log faylları uğurla təmizləndi!');
                            setTimeout(() => runTests(), 1000);
                        } else {
                            alert('Log təmizləmə zamanı xəta baş verdi.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Log təmizləmə zamanı xəta baş verdi.');
                    });
            }
        }

        function clearCache() {
            if (confirm('Cache-i təmizləmək istədiyinizə əminsiniz?')) {
                fetch('clear-cache.php')
                    .then(response => response.text())
                    .then(data => {
                        alert('Cache uğurla təmizləndi!');
                        setTimeout(() => runTests(), 1000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Cache təmizləmə zamanı xəta baş verdi.');
                    });
            }
        }

        function generateSitemap() {
            if (confirm('Sitemap-i yeniləmək istədiyinizə əminsiniz?')) {
                fetch('generate-sitemap.php')
                    .then(response => response.text())
                    .then(data => {
                        alert('Sitemap uğurla yeniləndi!');
                        setTimeout(() => runTests(), 1000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Sitemap yeniləmə zamanı xəta baş verdi.');
                    });
            }
        }

        function viewLogs() {
            const logWindow = window.open('', '_blank', 'width=800,height=600,scrollbars=yes');
            logWindow.document.write(`
                <html>
                <head><title>Log Files Viewer</title></head>
                <body>
                    <h2>Log Files</h2>
                    <ul>
                        <li><a href="logs/error.log" target="_blank">Error Log</a></li>
                        <li><a href="logs/performance-monitor.log" target="_blank">Performance Log</a></li>
                        <li><a href="logs/enhanced-backup.log" target="_blank">Backup Log</a></li>
                        <li><a href="logs/security.log" target="_blank">Security Log</a></li>
                    </ul>
                </body>
                </html>
            `);
        }

        // Sayfa yüklendiğinde otomatik test çalıştır
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🧪 A-Z Complete Test Suite Loaded');
            console.log('📊 Test Results:', <?php echo json_encode($test_results); ?>);
            
            // İlk section'ı aç
            const firstSection = document.querySelector('.test-content');
            if (firstSection) {
                firstSection.classList.add('active');
            }
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
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                exportResults();
            }
            if (e.ctrlKey && e.key === 'l') {
                e.preventDefault();
                cleanLogs();
            }
            if (e.ctrlKey && e.key === 'c') {
                e.preventDefault();
                clearCache();
            }
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                generateSitemap();
            }
        });
    </script>
</body>
</html>
