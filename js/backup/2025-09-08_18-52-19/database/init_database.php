<?php
// NextCode Group - Database Initialization Script
// Production Environment Setup

// Include API router for handling API requests
require_once __DIR__ . '/../api-router.php';

// Include database configuration
require_once __DIR__ . '/../config/database.php';

try {
    // Read SQL file
    $sql_file = __DIR__ . '/create_basic_tables.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception('SQL file not found: ' . $sql_file);
    }
    
    $sql_content = file_get_contents($sql_file);
    
    if ($sql_content === false) {
        throw new Exception('Failed to read SQL file');
    }
    
    // Split SQL statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql_content)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
        }
    );
    
    echo "<h2>NextCode Group - Database Initialization</h2>\n";
    echo "<pre>\n";
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (trim($statement)) {
            try {
                $pdo->exec($statement);
                echo "✓ Executed: " . substr(trim($statement), 0, 50) . "...\n";
            } catch (PDOException $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
                echo "Statement: " . substr(trim($statement), 0, 100) . "...\n";
            }
        }
    }
    
    echo "\n=== Database Initialization Complete ===\n";
    
    // Verify tables
    echo "\n=== Verifying Tables ===\n";
    $tables = ['pages', 'site_settings', 'site_statistics'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
            $count = $stmt->fetchColumn();
            echo "✓ Table '$table' exists with $count records\n";
        } catch (PDOException $e) {
            echo "✗ Table '$table' error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== Setup Complete ===\n";
    echo "Database has been successfully initialized.\n";
    echo "You can now access the website.\n";
    echo "</pre>\n";
    
} catch (Exception $e) {
    echo "<h2>Database Initialization Error</h2>\n";
    echo "<pre>\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "</pre>\n";
    
    // Log the error
    error_log('Database initialization error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Initialization - NextCode Group</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        pre {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
            overflow-x: auto;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-top: 30px;">
        <a href="../index.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Go to Homepage</a>
    </div>
</body>
</html>