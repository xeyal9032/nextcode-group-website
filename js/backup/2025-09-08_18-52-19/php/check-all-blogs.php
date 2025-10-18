<?php
// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("SELECT id, title, content, excerpt FROM blog_posts");
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($posts as $post) {
        echo "ID: " . $post['id'] . " - Title: " . $post['title'] . "\n";
        echo "Excerpt: " . $post['excerpt'] . "\n";
        echo "Content: " . substr($post['content'], 0, 100) . "...\n";
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>