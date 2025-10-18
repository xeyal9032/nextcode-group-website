<?php
// Site Content Table Setup

// Include API router for handling API requests
require_once 'api-router.php';

// SQLite database file path
$db_file = __DIR__ . '/database/nextcode.db';

try {
    // Create PDO connection for SQLite
    $dsn = "sqlite:$db_file";
    $pdo = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    // Enable foreign key constraints for SQLite
    $pdo->exec('PRAGMA foreign_keys = ON');
    
    // Read and modify SQL file for SQLite compatibility
    $sql = file_get_contents('database/create_site_content.sql');
    
    // Convert MySQL syntax to SQLite
    $sql = str_replace('AUTO_INCREMENT', '', $sql);
    $sql = str_replace('ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci', '', $sql);
    $sql = str_replace('enum(', 'TEXT CHECK (', $sql);
    $sql = str_replace("enum('text','html','image','link','json')", "TEXT CHECK (content_type IN ('text','html','image','link','json'))", $sql);
    $sql = str_replace('tinyint(1)', 'INTEGER', $sql);
    $sql = str_replace('int(11)', 'INTEGER', $sql);
    $sql = str_replace('varchar(255)', 'TEXT', $sql);
    $sql = str_replace('varchar(100)', 'TEXT', $sql);
    $sql = str_replace('longtext', 'TEXT', $sql);
    $sql = str_replace('text', 'TEXT', $sql);
    $sql = str_replace('timestamp DEFAULT CURRENT_TIMESTAMP', 'DATETIME DEFAULT CURRENT_TIMESTAMP', $sql);
    $sql = str_replace('timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'DATETIME DEFAULT CURRENT_TIMESTAMP', $sql);
    
    // Execute SQL
    $pdo->exec($sql);
    
    echo "Site content table created successfully!\n";
    echo "Table structure and initial data have been set up.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>