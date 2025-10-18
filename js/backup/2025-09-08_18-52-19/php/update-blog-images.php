<?php
// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Update blog posts with featured images
    $updates = [
        1 => 'assets/images/blog/web-development-2024.jpg',
        2 => 'assets/images/blog/mobile-apps.jpg', 
        3 => 'assets/images/blog/digital-marketing.jpg',
        4 => 'assets/images/blog/artificial-intelligence.jpg',
        5 => 'assets/images/blog/entrepreneurship.jpg'
    ];
    
    foreach ($updates as $id => $image_path) {
        $stmt = $pdo->prepare("UPDATE blog_posts SET featured_image = ? WHERE id = ?");
        $stmt->execute([$image_path, $id]);
        echo "Updated post ID $id with image: $image_path\n";
    }
    
    echo "\nAll blog post images updated successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>