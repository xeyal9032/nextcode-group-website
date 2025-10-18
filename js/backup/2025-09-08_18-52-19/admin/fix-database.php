<?php
/**
 * Database Fix Script
 * NextCode Group - Fix Database Issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Fix Script</h1>";

// Include database configuration
require_once '../config/database.php';

if (!$pdo) {
    die("❌ Database connection failed!");
}

echo "✅ Database connected successfully<br><br>";

try {
    // Step 1: Drop foreign key constraints to avoid issues
    echo "<h2>Step 1: Cleaning up existing tables</h2>";
    
    $tables_to_drop = ['admin_files', 'admin_activity_log', 'admin_sessions'];
    foreach ($tables_to_drop as $table) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS `$table`");
            echo "✅ Dropped table: $table<br>";
        } catch (Exception $e) {
            echo "⚠️ Could not drop $table: " . $e->getMessage() . "<br>";
        }
    }
    
    // Step 2: Fix admin_users table
    echo "<h2>Step 2: Fixing admin_users table</h2>";
    
    // Check if admin_users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_users'");
    if ($stmt->fetch()) {
        echo "✅ admin_users table exists<br>";
        
        // Check columns
        $columns = $pdo->query("SHOW COLUMNS FROM admin_users")->fetchAll(PDO::FETCH_COLUMN);
        
        // Add missing columns
        if (!in_array('login_attempts', $columns)) {
            $pdo->exec("ALTER TABLE admin_users ADD COLUMN login_attempts INT DEFAULT 0 AFTER last_login");
            echo "✅ Added login_attempts column<br>";
        }
        
        if (!in_array('locked_until', $columns)) {
            $pdo->exec("ALTER TABLE admin_users ADD COLUMN locked_until TIMESTAMP NULL AFTER login_attempts");
            echo "✅ Added locked_until column<br>";
        }
        
    } else {
        // Create admin_users table
        $pdo->exec("
            CREATE TABLE admin_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                full_name VARCHAR(255),
                role ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
                is_active TINYINT(1) DEFAULT 1,
                last_login TIMESTAMP NULL,
                login_attempts INT DEFAULT 0,
                locked_until TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_username (username),
                INDEX idx_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✅ Created admin_users table<br>";
    }
    
    // Step 3: Create other tables without foreign keys first
    echo "<h2>Step 3: Creating admin tables</h2>";
    
    // Admin sessions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_sessions (
            id VARCHAR(128) PRIMARY KEY,
            user_id INT NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NOT NULL,
            INDEX idx_user_id (user_id),
            INDEX idx_expires (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Created admin_sessions table<br>";
    
    // Admin activity log
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(100) NOT NULL,
            resource VARCHAR(255),
            details TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_action (action),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Created admin_activity_log table<br>";
    
    // Admin files table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_files (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_size INT NOT NULL,
            mime_type VARCHAR(100),
            file_type ENUM('image', 'document', 'code', 'other') DEFAULT 'other',
            uploaded_by INT,
            is_system_file TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_filename (filename),
            INDEX idx_file_type (file_type),
            INDEX idx_uploaded_by (uploaded_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Created admin_files table<br>";
    
    // Step 4: Handle admin user
    echo "<h2>Step 4: Setting up admin user</h2>";
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Create admin user
        $password_hash = password_hash('Admin123!@#NextCode', PASSWORD_ARGON2ID);
        $stmt = $pdo->prepare("
            INSERT INTO admin_users (username, email, password_hash, full_name, role, is_active) 
            VALUES (?, ?, ?, ?, 'super_admin', 1)
        ");
        
        if ($stmt->execute(['admin', 'admin@nextcodegroup.com', $password_hash, 'Super Administrator'])) {
            echo "✅ Created admin user<br>";
        } else {
            echo "❌ Failed to create admin user<br>";
        }
    } else {
        echo "✅ Admin user already exists<br>";
        
        // Update password just in case
        $password_hash = password_hash('Admin123!@#NextCode', PASSWORD_ARGON2ID);
        $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ?, role = 'super_admin' WHERE username = 'admin'");
        $stmt->execute([$password_hash]);
        echo "✅ Updated admin user password<br>";
    }
    
    // Step 5: Create site_settings if needed
    echo "<h2>Step 5: Setting up site_settings</h2>";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'site_settings'");
    if (!$stmt->fetch()) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS site_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) UNIQUE NOT NULL,
                setting_value TEXT,
                setting_type ENUM('string', 'text', 'number', 'boolean', 'json') DEFAULT 'string',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_setting_key (setting_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✅ Created site_settings table<br>";
    } else {
        echo "✅ site_settings table already exists<br>";
    }
    
    echo "<h2>✅ Database Fix Completed!</h2>";
    echo "<p><strong>Admin Login Credentials:</strong></p>";
    echo "<ul>";
    echo "<li>Username: <strong>admin</strong></li>";
    echo "<li>Password: <strong>Admin123!@#NextCode</strong></li>";
    echo "</ul>";
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo "h1 { color: #667eea; }";
echo "h2 { color: #333; margin-top: 30px; }";
echo "✅ { color: green; }";
echo "⚠️ { color: orange; }";
echo "❌ { color: red; }";
echo "</style>";
?>
