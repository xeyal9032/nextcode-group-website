<?php
/**
 * Check Server Configuration
 * Bu dosya sunucu yapılandırmasını ve gerekli modülleri kontrol eder
 */

// Include API router for handling API requests
require_once 'api-router.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Configuration Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .check { margin: 10px 0; padding: 10px; border: 1px solid #ddd; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .warning { background-color: #fff3cd; border-color: #ffeaa7; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .status { font-weight: bold; }
        .details { margin-top: 10px; font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>
    <h1>Server Configuration Check</h1>
    
    <div id="results"></div>
    
    <script>
        function addCheck(title, status, details, type = 'success') {
            const resultDiv = document.createElement('div');
            resultDiv.className = `check ${type}`;
            
            const statusIcon = type === 'success' ? '✓' : type === 'warning' ? '⚠' : '✗';
            
            resultDiv.innerHTML = `
                <div class="status">${title}: ${statusIcon}</div>
                <div class="details">${details}</div>
            `;
            
            document.getElementById('results').appendChild(resultDiv);
        }
        
        // Check PHP version
        const phpVersion = '<?php echo PHP_VERSION; ?>';
        const phpVersionNum = parseFloat(phpVersion);
        
        if (phpVersionNum >= 7.4) {
            addCheck('PHP Version', 'OK', `PHP ${phpVersion} - Supported`);
        } else {
            addCheck('PHP Version', 'Warning', `PHP ${phpVersion} - Consider upgrading to 7.4+`, 'warning');
        }
        
        // Check server software
        const serverSoftware = '<?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>';
        addCheck('Server Software', 'Info', serverSoftware);
        
        // Check if .htaccess is readable
        const htaccessExists = <?php echo file_exists('.htaccess') ? 'true' : 'false'; ?>;
        if (htaccessExists) {
            addCheck('.htaccess File', 'OK', 'File exists and readable');
        } else {
            addCheck('.htaccess File', 'Error', 'File not found or not readable', 'error');
        }
        
        // Check if mod_rewrite is available (indirect check)
        const modRewriteAvailable = <?php echo function_exists('apache_get_modules') ? 'true' : 'false'; ?>;
        if (modRewriteAvailable) {
            const modules = <?php echo function_exists('apache_get_modules') ? json_encode(apache_get_modules()) : '[]'; ?>;
            const hasRewrite = modules.includes('mod_rewrite');
            
            if (hasRewrite) {
                addCheck('mod_rewrite Module', 'OK', 'Apache mod_rewrite is available');
            } else {
                addCheck('mod_rewrite Module', 'Warning', 'mod_rewrite not found in Apache modules', 'warning');
            }
        } else {
            addCheck('mod_rewrite Module', 'Info', 'Cannot check Apache modules (not running on Apache)');
        }
        
        // Check API directory
        const apiDirExists = <?php echo is_dir('api') ? 'true' : 'false'; ?>;
        if (apiDirExists) {
            addCheck('API Directory', 'OK', 'API directory exists');
            
            // Check key API files
            const apiFiles = ['settings.php', 'services.php', 'portfolio.php', 'blog.php'];
            apiFiles.forEach(file => {
                const fileExists = <?php echo file_exists('api/settings.php') && file_exists('api/services.php') && file_exists('api/portfolio.php') && file_exists('api/blog.php') ? 'true' : 'false'; ?>;
                if (fileExists) {
                    addCheck(`API File: ${file}`, 'OK', 'File exists');
                } else {
                    addCheck(`API File: ${file}`, 'Error', 'File not found', 'error');
                }
            });
        } else {
            addCheck('API Directory', 'Error', 'API directory not found', 'error');
        }
        
        // Check if we can write to .htaccess
        const htaccessWritable = <?php echo is_writable('.htaccess') ? 'true' : 'false'; ?>;
        if (htaccessWritable) {
            addCheck('.htaccess Writable', 'OK', 'File is writable');
        } else {
            addCheck('.htaccess Writable', 'Warning', 'File is not writable - may need to set permissions', 'warning');
        }
        
        // Check current working directory
        const cwd = '<?php echo getcwd(); ?>';
        addCheck('Working Directory', 'Info', cwd);
        
        // Check if running on localhost or production
        const host = '<?php echo $_SERVER['HTTP_HOST'] ?? 'Unknown'; ?>';
        if (host.includes('localhost') || host.includes('127.0.0.1')) {
            addCheck('Environment', 'Info', 'Running on localhost/development');
        } else {
            addCheck('Environment', 'Info', 'Running on production server');
        }
    </script>
</body>
</html>
