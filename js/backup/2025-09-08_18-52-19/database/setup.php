<?php
// Database Setup Script
// This script will create the necessary tables for the NextCode admin panel

// Include API router for handling API requests
require_once __DIR__ . '/../api-router.php';

// require_once '../admin/api/config.php'; // Admin paneli kaldırıldı
require_once '../config/database.php';

try {
    $db = $pdo;
    
    echo "<h2>NextCode Database Setup</h2>";
    echo "<p>Connecting to database...</p>";
    
    // Read SQL file
    $sqlFile = __DIR__ . '/create_tables.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception('SQL file not found: ' . $sqlFile);
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "<p>Executing " . count($statements) . " SQL statements...</p>";
    echo "<ul>";
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $db->exec($statement);
            
            // Extract table name for better feedback
            if (preg_match('/CREATE TABLE.*?`?([a-zA-Z_]+)`?/i', $statement, $matches)) {
                echo "<li style='color: green;'>✓ Created table: {$matches[1]}</li>";
            } elseif (preg_match('/INSERT.*?INTO.*?`?([a-zA-Z_]+)`?/i', $statement, $matches)) {
                echo "<li style='color: blue;'>✓ Inserted data into: {$matches[1]}</li>";
            } else {
                echo "<li style='color: gray;'>✓ Executed statement</li>";
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false) {
                if (preg_match('/CREATE TABLE.*?`?([a-zA-Z_]+)`?/i', $statement, $matches)) {
                    echo "<li style='color: orange;'>⚠ Table already exists: {$matches[1]}</li>";
                }
            } elseif (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "<li style='color: orange;'>⚠ Duplicate entry (skipped)</li>";
            } else {
                echo "<li style='color: red;'>✗ Error: " . $e->getMessage() . "</li>";
            }
        }
    }
    
    echo "</ul>";
    
    // Verify tables were created
    echo "<h3>Verifying Tables</h3>";
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $expectedTables = [
        'admin_users',
        'services',
        'portfolio_projects',
        'blog_categories',
        'blog_posts',
        'contact_messages',
        'newsletter_subscribers',
        'media_files',
        'navigation_items',
        'site_settings',
        'content_pages',
        'activity_log'
    ];
    
    echo "<ul>";
    foreach ($expectedTables as $table) {
        if (in_array($table, $tables)) {
            echo "<li style='color: green;'>✓ {$table}</li>";
        } else {
            echo "<li style='color: red;'>✗ {$table} (missing)</li>";
        }
    }
    echo "</ul>";
    
    // Test admin user
    echo "<h3>Testing Admin User</h3>";
    $stmt = $db->prepare("SELECT username, email FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p style='color: green;'>✓ Admin user found: {$admin['username']} ({$admin['email']})</p>";
        echo "<p><strong>Default Login:</strong><br>Username: admin<br>Password: password</p>";
    } else {
        echo "<p style='color: red;'>✗ Admin user not found</p>";
    }
    
    // Test services
    echo "<h3>Testing Services</h3>";
    $stmt = $db->query("SELECT COUNT(*) as count FROM services");
    $serviceCount = $stmt->fetch()['count'];
    echo "<p style='color: green;'>✓ Found {$serviceCount} services</p>";
    
    // Test site settings
    echo "<h3>Testing Site Settings</h3>";
    $stmt = $db->query("SELECT COUNT(*) as count FROM site_settings");
    $settingsCount = $stmt->fetch()['count'];
    echo "<p style='color: green;'>✓ Found {$settingsCount} site settings</p>";
    
    echo "<h3 style='color: green;'>✅ Database setup completed successfully!</h3>";
    echo "<p><a href='../admin/index.html'>Go to Admin Panel</a></p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Database setup failed!</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in admin/api/config.php</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>NextCode Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h2, h3 { color: #333; }
        ul { list-style: none; padding: 0; }
        li { padding: 5px 0; }
    </style>
</head>
<body>
</body>
</html>