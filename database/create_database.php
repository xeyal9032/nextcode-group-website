<?php
// Direct Database Creation Script
// This script will create the necessary tables for the NextCode admin panel

// Include API router for handling API requests
require_once __DIR__ . '/../api-router.php';

$host = 'gtorg.mysql.tools';
$dbname = 'gtorg_nextcode';
$username = 'gtorg_nextcode';
$password = ';849#dVEyg';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully!\n";
    
    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/create_tables.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "Executing " . count($statements) . " SQL statements...\n";
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $pdo->exec($statement);
            
            // Extract table name for better feedback
            if (preg_match('/CREATE TABLE.*?`?([a-zA-Z_]+)`?/i', $statement, $matches)) {
                echo "✓ Created table: {$matches[1]}\n";
            } elseif (preg_match('/INSERT.*?INTO.*?`?([a-zA-Z_]+)`?/i', $statement, $matches)) {
                echo "✓ Inserted data into: {$matches[1]}\n";
            } else {
                echo "✓ Executed statement\n";
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false) {
                if (preg_match('/CREATE TABLE.*?`?([a-zA-Z_]+)`?/i', $statement, $matches)) {
                    echo "⚠ Table already exists: {$matches[1]}\n";
                }
            } elseif (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "⚠ Duplicate entry (skipped)\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Verify tables were created
    echo "\nVerifying Tables:\n";
    $stmt = $pdo->query("SHOW TABLES");
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
    
    foreach ($expectedTables as $table) {
        if (in_array($table, $tables)) {
            echo "✓ {$table}\n";
        } else {
            echo "✗ {$table} (missing)\n";
        }
    }
    
    // Test admin user
    echo "\nTesting Admin User:\n";
    $stmt = $pdo->prepare("SELECT username, email FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "✓ Admin user found: {$admin['username']} ({$admin['email']})\n";
        echo "Default Login: Username: admin, Password: password\n";
    } else {
        echo "✗ Admin user not found\n";
    }
    
    // Test services
    echo "\nTesting Services:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM services");
    $serviceCount = $stmt->fetch()['count'];
    echo "✓ Found {$serviceCount} services\n";
    
    // Test site settings
    echo "\nTesting Site Settings:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM site_settings");
    $settingsCount = $stmt->fetch()['count'];
    echo "✓ Found {$settingsCount} site settings\n";
    
    echo "\n✅ Database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Database setup failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
}
?>