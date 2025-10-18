<?php
define('SECURE_ACCESS', true);
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "Site Content Table Columns:\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM site_content");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
    echo "\nSample data:\n";
    $stmt = $pdo->query("SELECT * FROM site_content LIMIT 2");
    $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($samples as $row) {
        foreach ($row as $key => $value) {
            echo "{$key}: {$value}\n";
        }
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
