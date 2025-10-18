<?php
// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Check if blog_posts table exists
    $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='blog_posts'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "blog_posts table does not exist!\n";
        exit;
    }
    
    echo "blog_posts table exists\n";
    
    // Check total posts
    $stmt = $conn->query('SELECT COUNT(*) as count FROM blog_posts');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total blog posts: " . $result['count'] . "\n";
    
    // Check published posts
    $stmt = $conn->query('SELECT COUNT(*) as count FROM blog_posts WHERE status = "published"');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Published blog posts: " . $result['count'] . "\n";
    
    // Show first few posts
    $stmt = $conn->query('SELECT id, title, status, created_at FROM blog_posts LIMIT 5');
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nFirst 5 posts:\n";
    foreach ($posts as $post) {
        echo "ID: {$post['id']}, Title: {$post['title']}, Status: {$post['status']}, Created: {$post['created_at']}\n";
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>