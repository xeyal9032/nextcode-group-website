<?php
/**
 * NextCode Group - Contact API Endpoint
 * İletişim formu için REST API endpoint'i
 * 
 * @author NextCode Group
 * @version 1.0
 * @since 2024
 * 
 * @endpoint POST /api/contact.php
 * @description İletişim formu verilerini alır ve işler
 */

// API başlıkları
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Error handler include et
require_once __DIR__ . '/../includes/error_handler.php';

// Database connection
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        handleApiError('Database connection failed', 500);
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            handleApiError('Invalid JSON input', 400);
        }
        
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $subject = trim($input['subject'] ?? '');
        $message = trim($input['message'] ?? '');
        
        // Split name into first_name and last_name
        $name_parts = explode(' ', $name, 2);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
        
        // Validation with detailed error messages
        $errors = [];
        
        if (empty($name)) {
            $errors[] = handleValidationError('name', 'Ad sahəsi mütləqdir');
        }
        
        if (empty($email)) {
            $errors[] = handleValidationError('email', 'Email sahəsi mütləqdir');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = handleValidationError('email', 'Düzgün email ünvanı daxil edin');
        }
        
        if (empty($message)) {
            $errors[] = handleValidationError('message', 'Mesaj sahəsi mütləqdir');
        }
        
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ]);
            exit;
        }
        
        // Insert contact message with error handling
        $stmt = $conn->prepare("
            INSERT INTO contact_messages (first_name, last_name, email, phone, subject, message, status, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 'unread', ?, ?, NOW())
        ");
        
        if (!$stmt) {
            handleDatabaseError($conn, "INSERT INTO contact_messages");
            handleApiError('Database prepare failed', 500);
        }
        
        $result = $stmt->execute([
            $first_name,
            $last_name,
            $email, 
            $phone, 
            $subject, 
            $message,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
        
        if ($result) {
            // Email gönderimini dene
            require_once __DIR__ . '/../includes/EmailSender.php';
            $emailSender = new EmailSender();
            
            $emailData = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message
            ];
            
            $emailSent = $emailSender->sendContactEmail($emailData);
            $emailMessage = '';
            
            if ($emailSent) {
                $emailMessage = ' Email göndərildi.';
            } else {
                error_log('API Email sending failed: ' . $emailSender->getLastError());
                $emailMessage = ' Email göndərilmədi, ancaq mesaj qeydə alındı.';
            }
            
            logSecurityEvent('Contact form submitted', "Name: $name, Email: $email, Email Sent: " . ($emailSent ? 'Yes' : 'No'));
            echo json_encode([
                'success' => true,
                'message' => 'Mesajınız uğurla göndərildi. Tezliklə sizinlə əlaqə saxlayacağıq.' . $emailMessage,
                'email_sent' => $emailSent
            ]);
        } else {
            handleDatabaseError($conn, "INSERT INTO contact_messages");
            handleApiError('Failed to save message', 500);
        }
    } else {
        handleApiError('Method not allowed', 405);
    }
} catch (Exception $e) {
    logError('Contact API Exception: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    handleApiError('Internal server error', 500);
}
?>