<?php
// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->query("SELECT id, title, featured_image FROM blog_posts ORDER BY id");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Blog Posts and Images:\n";
    echo "======================\n";
    
    foreach ($posts as $post) {
        echo "ID: {$post['id']}\n";
        echo "Title: {$post['title']}\n";
        echo "Featured Image: " . ($post['featured_image'] ?: 'NULL') . "\n";
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>