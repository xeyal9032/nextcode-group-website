<?php
// Check site_content table structure
define('SECURE_ACCESS', true);
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "<h2>Site Content Table Structure:</h2>";
    
    // Get table structure
    $stmt = $pdo->query("DESCRIBE site_content");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM site_content");
    $count = $stmt->fetch()['count'];
    echo "<p>Total records in site_content: {$count}</p>";
    
    // Show sample data
    if ($count > 0) {
        echo "<h3>Sample Data:</h3>";
        $stmt = $pdo->query("SELECT * FROM site_content LIMIT 5");
        $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1'>";
        if (!empty($samples)) {
            // Header
            echo "<tr>";
            foreach (array_keys($samples[0]) as $key) {
                echo "<th>{$key}</th>";
            }
            echo "</tr>";
            
            // Data
            foreach ($samples as $row) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Error: " . $e->getMessage() . "</h3>";
}
?>
