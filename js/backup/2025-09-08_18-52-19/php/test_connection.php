<?php
// NextCode Group - Database Connection Test
// Test script to verify database connection with new credentials

// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';

echo "<h2>NextCode Group - Database Connection Test</h2>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;background:#f5f5f5;}pre{background:#fff;padding:20px;border-radius:5px;border-left:4px solid #007bff;}</style>";
echo "<pre>";

echo "Testing connection to: {$db_host}\n";
echo "Database: {$db_name}\n";
echo "User: {$db_user}\n";
echo "\n";

try {
    // Test database connection
    echo "Attempting to connect...\n";
    
    $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
    $test_pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    echo "✓ Database connection successful!\n\n";
    
    // Check existing tables
    echo "=== Checking existing tables ===\n";
    $tables_query = $test_pdo->query("SHOW TABLES");
    $existing_tables = $tables_query->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($existing_tables)) {
        echo "No tables found in database.\n";
        echo "Database is empty and ready for setup.\n\n";
    } else {
        echo "Found " . count($existing_tables) . " existing tables:\n";
        foreach ($existing_tables as $table) {
            echo "  - {$table}\n";
        }
        echo "\n";
    }
    
    // Check required tables
    echo "=== Checking required tables ===\n";
    $required_tables = ['pages', 'site_settings', 'site_statistics'];
    $missing_tables = [];
    
    foreach ($required_tables as $table) {
        if (in_array($table, $existing_tables)) {
            echo "✓ Table '{$table}' exists\n";
            
            // Count records
            $count_query = $test_pdo->query("SELECT COUNT(*) FROM `{$table}`");
            $count = $count_query->fetchColumn();
            echo "  └─ Contains {$count} records\n";
        } else {
            echo "✗ Table '{$table}' missing\n";
            $missing_tables[] = $table;
        }
    }
    
    echo "\n";
    
    if (empty($missing_tables)) {
        echo "=== Database Status: READY ===\n";
        echo "All required tables exist. Website should work properly.\n";
        echo "\n<a href='index.php' style='background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Go to Website</a>";
    } else {
        echo "=== Database Status: SETUP REQUIRED ===\n";
        echo "Missing tables: " . implode(', ', $missing_tables) . "\n";
        echo "\n<a href='setup.php' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Run Database Setup</a>";
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    
    // Common error solutions
    echo "=== Possible Solutions ===\n";
    echo "1. Check if database server is running\n";
    echo "2. Verify database credentials\n";
    echo "3. Ensure database '{$dbname}' exists\n";
    echo "4. Check firewall/network settings\n";
    echo "5. Verify user permissions\n";
    
} catch (Exception $e) {
    echo "✗ Unexpected error: " . $e->getMessage() . "\n";
}

echo "</pre>";
echo "<div style='text-align:center;margin-top:30px;'>";
echo "<a href='test_connection.php' style='background:#6c757d;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin-right:10px;'>Refresh Test</a>";
echo "<a href='setup.php' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Database Setup</a>";
echo "</div>";
?>