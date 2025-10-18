<?php
// Comprehensive Theme Test - Tüm Sayfalarda Gece Gündüz Modu Kontrolü
header('Content-Type: application/json; charset=utf-8');

// Test results array
$testResults = [
    'theme_system' => [],
    'css_files' => [],
    'js_files' => [],
    'pages' => [],
    'components' => [],
    'overall_status' => 'success'
];

// Test CSS files
function testCSSFiles() {
    global $testResults;
    
    $cssFiles = [
        'css/theme.css' => 'Main theme CSS',
        'css/theme-variables.css' => 'Theme variables CSS',
        'css/theme-support.css' => 'Theme support CSS'
    ];
    
    foreach ($cssFiles as $file => $description) {
        if (file_exists($file)) {
            $size = filesize($file);
            $testResults['css_files'][] = [
                'file' => $file,
                'description' => $description,
                'status' => 'success',
                'size' => $size,
                'message' => 'File exists and accessible'
            ];
        } else {
            $testResults['css_files'][] = [
                'file' => $file,
                'description' => $description,
                'status' => 'error',
                'size' => 0,
                'message' => 'File not found'
            ];
            $testResults['overall_status'] = 'error';
        }
    }
}

// Test JavaScript files
function testJSFiles() {
    global $testResults;
    
    $jsFiles = [
        'js/theme.js' => 'Main theme JavaScript',
        'js/critical.js' => 'Critical JavaScript'
    ];
    
    foreach ($jsFiles as $file => $description) {
        if (file_exists($file)) {
            $size = filesize($file);
            $testResults['js_files'][] = [
                'file' => $file,
                'description' => $description,
                'status' => 'success',
                'size' => $size,
                'message' => 'File exists and accessible'
            ];
        } else {
            $testResults['js_files'][] = [
                'file' => $file,
                'description' => $description,
                'status' => 'error',
                'size' => 0,
                'message' => 'File not found'
            ];
            $testResults['overall_status'] = 'error';
        }
    }
}

// Test pages
function testPages() {
    global $testResults;
    
    $pages = [
        'index.php' => 'Home Page',
        'about.php' => 'About Page',
        'services.php' => 'Services Page',
        'portfolio.php' => 'Portfolio Page',
        'blog.php' => 'Blog Page',
        'pricing.php' => 'Pricing Page',
        'faq.php' => 'FAQ Page',
        'contact.php' => 'Contact Page'
    ];
    
    foreach ($pages as $file => $description) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            
            // Check for theme-related content
            $hasThemeToggle = strpos($content, 'themeToggle') !== false;
            $hasThemeCSS = strpos($content, 'theme.css') !== false || strpos($content, 'theme-variables.css') !== false;
            $hasThemeJS = strpos($content, 'theme.js') !== false;
            $hasDataTheme = strpos($content, 'data-theme') !== false;
            
            $status = 'success';
            $message = 'Page has theme support';
            
            if (!$hasThemeToggle && !$hasDataTheme) {
                $status = 'warning';
                $message = 'Page may not have theme toggle button';
            }
            
            if (!$hasThemeCSS) {
                $status = 'warning';
                $message = 'Page may not have theme CSS files';
            }
            
            if (!$hasThemeJS) {
                $status = 'warning';
                $message = 'Page may not have theme JavaScript';
            }
            
            $testResults['pages'][] = [
                'file' => $file,
                'description' => $description,
                'status' => $status,
                'has_theme_toggle' => $hasThemeToggle,
                'has_theme_css' => $hasThemeCSS,
                'has_theme_js' => $hasThemeJS,
                'has_data_theme' => $hasDataTheme,
                'message' => $message
            ];
        } else {
            $testResults['pages'][] = [
                'file' => $file,
                'description' => $description,
                'status' => 'error',
                'has_theme_toggle' => false,
                'has_theme_css' => false,
                'has_theme_js' => false,
                'has_data_theme' => false,
                'message' => 'Page not found'
            ];
            $testResults['overall_status'] = 'error';
        }
    }
}

// Test includes
function testIncludes() {
    global $testResults;
    
    $includes = [
        'includes/header.php' => 'Header Include',
        'includes/footer.php' => 'Footer Include'
    ];
    
    foreach ($includes as $file => $description) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            
            $hasThemeToggle = strpos($content, 'themeToggle') !== false;
            $hasThemeCSS = strpos($content, 'theme.css') !== false || strpos($content, 'theme-variables.css') !== false;
            $hasThemeJS = strpos($content, 'theme.js') !== false;
            
            $status = 'success';
            $message = 'Include has theme support';
            
            if (!$hasThemeToggle) {
                $status = 'warning';
                $message = 'Include may not have theme toggle button';
            }
            
            if (!$hasThemeCSS) {
                $status = 'warning';
                $message = 'Include may not have theme CSS files';
            }
            
            if (!$hasThemeJS) {
                $status = 'warning';
                $message = 'Include may not have theme JavaScript';
            }
            
            $testResults['components'][] = [
                'file' => $file,
                'description' => $description,
                'status' => $status,
                'has_theme_toggle' => $hasThemeToggle,
                'has_theme_css' => $hasThemeCSS,
                'has_theme_js' => $hasThemeJS,
                'message' => $message
            ];
        } else {
            $testResults['components'][] = [
                'file' => $file,
                'description' => $description,
                'status' => 'error',
                'has_theme_toggle' => false,
                'has_theme_css' => false,
                'has_theme_js' => false,
                'message' => 'Include not found'
            ];
            $testResults['overall_status'] = 'error';
        }
    }
}

// Test theme system functionality
function testThemeSystem() {
    global $testResults;
    
    // Check if theme.js exists and has required functions
    if (file_exists('js/theme.js')) {
        $content = file_get_contents('js/theme.js');
        
        $hasThemeManager = strpos($content, 'class ThemeManager') !== false;
        $hasSetTheme = strpos($content, 'setTheme') !== false;
        $hasThemeToggle = strpos($content, 'themeToggle') !== false;
        $hasLocalStorage = strpos($content, 'localStorage') !== false;
        
        $status = 'success';
        $message = 'Theme system is properly implemented';
        
        if (!$hasThemeManager) {
            $status = 'error';
            $message = 'ThemeManager class not found';
        }
        
        if (!$hasSetTheme) {
            $status = 'warning';
            $message = 'setTheme function not found';
        }
        
        if (!$hasThemeToggle) {
            $status = 'warning';
            $message = 'Theme toggle functionality not found';
        }
        
        if (!$hasLocalStorage) {
            $status = 'warning';
            $message = 'LocalStorage support not found';
        }
        
        $testResults['theme_system'][] = [
            'component' => 'ThemeManager Class',
            'status' => $hasThemeManager ? 'success' : 'error',
            'message' => $hasThemeManager ? 'ThemeManager class exists' : 'ThemeManager class not found'
        ];
        
        $testResults['theme_system'][] = [
            'component' => 'setTheme Function',
            'status' => $hasSetTheme ? 'success' : 'warning',
            'message' => $hasSetTheme ? 'setTheme function exists' : 'setTheme function not found'
        ];
        
        $testResults['theme_system'][] = [
            'component' => 'Theme Toggle',
            'status' => $hasThemeToggle ? 'success' : 'warning',
            'message' => $hasThemeToggle ? 'Theme toggle functionality exists' : 'Theme toggle functionality not found'
        ];
        
        $testResults['theme_system'][] = [
            'component' => 'LocalStorage Support',
            'status' => $hasLocalStorage ? 'success' : 'warning',
            'message' => $hasLocalStorage ? 'LocalStorage support exists' : 'LocalStorage support not found'
        ];
    } else {
        $testResults['theme_system'][] = [
            'component' => 'Theme JavaScript',
            'status' => 'error',
            'message' => 'theme.js file not found'
        ];
        $testResults['overall_status'] = 'error';
    }
}

// Run all tests
testCSSFiles();
testJSFiles();
testPages();
testIncludes();
testThemeSystem();

// Add summary
$testResults['summary'] = [
    'total_tests' => count($testResults['css_files']) + count($testResults['js_files']) + count($testResults['pages']) + count($testResults['components']) + count($testResults['theme_system']),
    'success_count' => 0,
    'warning_count' => 0,
    'error_count' => 0,
    'overall_status' => $testResults['overall_status']
];

// Count results
foreach ($testResults['css_files'] as $result) {
    if ($result['status'] === 'success') $testResults['summary']['success_count']++;
    elseif ($result['status'] === 'warning') $testResults['summary']['warning_count']++;
    else $testResults['summary']['error_count']++;
}

foreach ($testResults['js_files'] as $result) {
    if ($result['status'] === 'success') $testResults['summary']['success_count']++;
    elseif ($result['status'] === 'warning') $testResults['summary']['warning_count']++;
    else $testResults['summary']['error_count']++;
}

foreach ($testResults['pages'] as $result) {
    if ($result['status'] === 'success') $testResults['summary']['success_count']++;
    elseif ($result['status'] === 'warning') $testResults['summary']['warning_count']++;
    else $testResults['summary']['error_count']++;
}

foreach ($testResults['components'] as $result) {
    if ($result['status'] === 'success') $testResults['summary']['success_count']++;
    elseif ($result['status'] === 'warning') $testResults['summary']['warning_count']++;
    else $testResults['summary']['error_count']++;
}

foreach ($testResults['theme_system'] as $result) {
    if ($result['status'] === 'success') $testResults['summary']['success_count']++;
    elseif ($result['status'] === 'warning') $testResults['summary']['warning_count']++;
    else $testResults['summary']['error_count']++;
}

// Output results
echo json_encode($testResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
