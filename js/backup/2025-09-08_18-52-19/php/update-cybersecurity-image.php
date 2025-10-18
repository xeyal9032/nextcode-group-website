<?php
// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';

$db = new Database();
$pdo = $db->getConnection();

$stmt = $pdo->prepare('UPDATE blog_posts SET featured_image = ? WHERE id = ?');
$result = $stmt->execute(['assets/images/blog/cybersecurity.jpg', 5]);

if ($result) {
    echo "Updated post 5 with cybersecurity image successfully\n";
} else {
    echo "Failed to update post 5\n";
}
?>