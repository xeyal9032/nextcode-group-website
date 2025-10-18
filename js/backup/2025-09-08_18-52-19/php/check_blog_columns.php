<?php
// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->query('PRAGMA table_info(blog_posts)');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Blog Posts Table Columns:\n";
    echo "========================\n";
    
    foreach($columns as $col) {
        echo $col['name'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>