<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// require_once '../admin/api/config.php'; // Admin paneli kaldırıldı
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $subject = trim($input['subject'] ?? '');
        $message = trim($input['message'] ?? '');
        
        // Validation
        if (empty($name) || empty($email) || empty($message)) {
            echo json_encode([
                'success' => false,
                'message' => 'Ad, email və mesaj sahələri mütləqdir.'
            ]);
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => 'Düzgün email ünvanı daxil edin.'
            ]);
            exit;
        }
        
        // Insert contact message
        $stmt = $conn->prepare("
            INSERT INTO contact_messages (name, email, phone, subject, message, status, created_at) 
            VALUES (?, ?, ?, ?, ?, 'new', NOW())
        ");
        
        if ($stmt->execute([$name, $email, $phone, $subject, $message])) {
            echo json_encode([
                'success' => true,
                'message' => 'Mesajınız uğurla göndərildi. Tezliklə sizinlə əlaqə saxlayacağıq.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Mesaj göndərilmədi. Xahiş edirik yenidən cəhd edin.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Yalnız POST metodu dəstəklənir.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Sistem xətası: ' . $e->getMessage()
    ]);
}
?>