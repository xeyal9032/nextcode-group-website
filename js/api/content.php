<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $page = $_GET['page'] ?? 'home';
        
        $stmt = $conn->prepare("SELECT * FROM content_pages WHERE page_slug = ? AND status = 'published'");
        $stmt->execute([$page]);
        $content = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($content) {
            echo json_encode([
                'success' => true,
                'data' => $content
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Content not found'
            ]);
        }
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>