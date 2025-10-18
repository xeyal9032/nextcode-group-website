<?php
/**
 * Admin Panel Test Script
 * NextCode Group - System Test
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin Panel Test</h1>";

// Test 1: Database connection
echo "<h2>1. Database Connection Test</h2>";
try {
    require_once 'config/database.php';
    if ($pdo) {
        echo "✅ Database connection successful<br>";
        
        // Test query
        $stmt = $pdo->query("SELECT VERSION() as version");
        $result = $stmt->fetch();
        echo "✅ MySQL Version: " . $result['version'] . "<br>";
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 2: Security configuration
echo "<h2>2. Security Configuration Test</h2>";
try {
    define('ADMIN_ACCESS', true);
    require_once 'config/security.php';
    echo "✅ Security configuration loaded<br>";
    
    $adminSecurity = AdminSecurity::getInstance();
    echo "✅ Security instance created<br>";
    
    // Test CSRF token generation
    session_start();
    $token = $adminSecurity->generateCSRFToken();
    if ($token) {
        echo "✅ CSRF token generated: " . substr($token, 0, 16) . "...<br>";
    }
} catch (Exception $e) {
    echo "❌ Security error: " . $e->getMessage() . "<br>";
}

// Test 3: File permissions
echo "<h2>3. File Permissions Test</h2>";
$directories = ['css', 'js', 'includes', 'pages', 'config'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_readable($dir) && is_writable($dir)) {
            echo "✅ Directory '$dir' - OK<br>";
        } else {
            echo "⚠️ Directory '$dir' - Permission issues<br>";
        }
    } else {
        echo "❌ Directory '$dir' - Not found<br>";
    }
}

// Test 4: Required PHP extensions
echo "<h2>4. PHP Extensions Test</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'openssl'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ Extension '$ext' - Available<br>";
    } else {
        echo "❌ Extension '$ext' - Missing<br>";
    }
}

// Test 5: Admin table creation
echo "<h2>5. Admin Tables Test</h2>";
try {
    if ($pdo) {
        // Check if admin tables exist
        $tables = ['admin_users', 'admin_sessions', 'admin_activity_log', 'admin_files', 'site_settings'];
        
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            
            if ($stmt->fetch()) {
                echo "✅ Table '$table' exists<br>";
            } else {
                echo "⚠️ Table '$table' missing - Creating...<br>";
                
                // Try to create tables
                $sql_file = '../database/create_admin_tables.sql';
                if (file_exists($sql_file)) {
                    $sql = file_get_contents($sql_file);
                    $pdo->exec($sql);
                    echo "✅ Admin tables created<br>";
                    break;
                }
            }
        }
        
        // Check if default admin user exists
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admin_users WHERE role = 'super_admin'");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "✅ Super admin user exists<br>";
        } else {
            echo "⚠️ Creating default super admin user...<br>";
            
            $password_hash = password_hash('Admin123!@#NextCode', PASSWORD_ARGON2ID);
            $stmt = $pdo->prepare("
                INSERT INTO admin_users (username, email, password_hash, full_name, role, is_active) 
                VALUES (?, ?, ?, ?, 'super_admin', 1)
            ");
            
            if ($stmt->execute(['admin', 'admin@nextcodegroup.com', $password_hash, 'Super Administrator'])) {
                echo "✅ Default super admin created<br>";
                echo "📋 Login credentials:<br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;Username: admin<br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;Password: Admin123!@#NextCode<br>";
            } else {
                echo "❌ Failed to create default admin<br>";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Table creation error: " . $e->getMessage() . "<br>";
}

// Test 6: File access test
echo "<h2>6. File Access Test</h2>";
$test_files = [
    'index.php' => 'Main admin page',
    'login.php' => 'Login page',
    'css/admin.css' => 'Admin styles',
    'js/admin.js' => 'Admin scripts',
    'pages/file-manager.php' => 'File manager',
    'pages/code-editor.php' => 'Code editor',
    'pages/database.php' => 'Database manager',
    'pages/settings.php' => 'Settings panel'
];

foreach ($test_files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description ($file) - OK<br>";
    } else {
        echo "❌ $description ($file) - Missing<br>";
    }
}

// Test summary
echo "<h2>Test Summary</h2>";
echo "<p>✅ = Success | ⚠️ = Warning | ❌ = Error</p>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Go to <a href='login.php'>login.php</a> to test the login system</li>";
echo "<li>Use the credentials shown above to login</li>";
echo "<li>Test all admin panel features</li>";
echo "<li>Delete this test file after testing</li>";
echo "</ol>";

echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo "h1 { color: #667eea; }";
echo "h2 { color: #333; margin-top: 30px; }";
echo "✅ { color: green; }";
echo "⚠️ { color: orange; }";
echo "❌ { color: red; }";
echo "</style>";
?>
