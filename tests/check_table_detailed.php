<?php
// Check site_content table structure - detailed
define('SECURE_ACCESS', true);
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "<h2>Site Content Table Structure:</h2>";
    
    // Get table structure
    $stmt = $pdo->query("SHOW COLUMNS FROM site_content");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    foreach ($columns as $column) {
        echo "Column: {$column['Field']} | Type: {$column['Type']} | Null: {$column['Null']} | Key: {$column['Key']} | Default: {$column['Default']} | Extra: {$column['Extra']}\n";
    }
    echo "</pre>";
    
    // Check if table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM site_content");
    $count = $stmt->fetch()['count'];
    echo "<p>Total records in site_content: {$count}</p>";
    
    // Show sample data
    if ($count > 0) {
        echo "<h3>Sample Data:</h3>";
        $stmt = $pdo->query("SELECT * FROM site_content LIMIT 3");
        $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<pre>";
        foreach ($samples as $row) {
            print_r($row);
        }
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Error: " . $e->getMessage() . "</h3>";
}
?>
