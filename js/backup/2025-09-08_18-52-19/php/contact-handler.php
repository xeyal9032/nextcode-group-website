<?php
// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Sadece POST metodu kabul edilir']);
    exit;
}

// Debug: Log all POST data
error_log('Contact form POST data: ' . print_r($_POST, true));

// Debug: Check if database connection exists
if (!isset($pdo)) {
    error_log('Database connection not found!');
    echo json_encode(['success' => false, 'message' => 'Veritabanı bağlantısı tapılmadı']);
    exit;
}

// Debug: Check database connection
try {
    $pdo->query('SELECT 1');
    error_log('Database connection test: SUCCESS');
} catch (PDOException $e) {
    error_log('Database connection test failed: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Veritabanı bağlantısı xətası']);
    exit;
}

// Form verilerini al
$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$company = trim($_POST['company'] ?? '');
$service = trim($_POST['service'] ?? '');
$budget = trim($_POST['budget'] ?? '');
$subject = 'Yeni Müştəri Mesajı'; // Default subject
$message = trim($_POST['message'] ?? '');

// Combine firstName and lastName if name is empty
if (empty($name) && (!empty($firstName) || !empty($lastName))) {
    $name = trim($firstName . ' ' . $lastName);
}

// Debug: Log processed data
error_log('Processed form data - Name: ' . $name . ', Email: ' . $email . ', Message: ' . substr($message, 0, 50));

// Basit validasyon
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Ad, email və mesaj sahələri məcburidir']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Düzgün email ünvanı daxil edin']);
    exit;
}

try {
    // Debug: Check if table exists
    try {
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'contact_messages'");
        if ($tableCheck->rowCount() == 0) {
            error_log('Contact messages table does not exist!');
            echo json_encode(['success' => false, 'message' => 'Veritabanı cədvəli tapılmadı']);
            exit;
        }
        error_log('Contact messages table exists');
    } catch (PDOException $e) {
        error_log('Table check failed: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Veritabanı cədvəli yoxlanıla bilmədi']);
        exit;
    }
    
    // Check if new columns exist, if not use basic insert
    try {
        $columns = $pdo->query("SHOW COLUMNS FROM contact_messages")->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('phone', $columns) && in_array('company', $columns) && in_array('service', $columns) && in_array('budget', $columns)) {
            // Use full insert with all columns
            $sql = "INSERT INTO contact_messages (name, email, phone, company, service, budget, subject, message, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $params = [$name, $email, $phone, $company, $service, $budget, $subject, $message];
        } else {
            // Use basic insert with only existing columns
            $sql = "INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())";
            $params = [$name, $email, $subject, $message];
        }
        
        error_log('SQL Query: ' . $sql);
        error_log('SQL Parameters: ' . json_encode($params));
        
        // Veritabanına kaydet
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);
        
    } catch (PDOException $e) {
        error_log('Column check failed: ' . $e->getMessage());
        // Fallback to basic insert
        $sql = "INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())";
        $params = [$name, $email, $subject, $message];
        
        error_log('Fallback SQL Query: ' . $sql);
        error_log('Fallback SQL Parameters: ' . json_encode($params));
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);
    }
    
    // Debug: Log the result
    error_log('Database insert result: ' . ($result ? 'SUCCESS' : 'FAILED'));
    
    if ($result) {
        // WhatsApp mesajını hazırla
        $whatsapp_message = "🆕 *Yeni Müştəri Mesajı*\n\n";
        $whatsapp_message .= "👤 *Ad:* " . $name . "\n";
        $whatsapp_message .= "📧 *Email:* " . $email . "\n";
        
        if (!empty($phone)) {
            $whatsapp_message .= "📞 *Telefon:* " . $phone . "\n";
        }
        
        if (!empty($company)) {
            $whatsapp_message .= "🏢 *Şirkət:* " . $company . "\n";
        }
        
        if (!empty($service)) {
            $whatsapp_message .= "🔧 *Xidmət:* " . $service . "\n";
        }
        
        if (!empty($budget)) {
            $whatsapp_message .= "💰 *Büdcə:* " . $budget . "\n";
        }
        
        if (!empty($subject)) {
            $whatsapp_message .= "📋 *Mövzu:* " . $subject . "\n";
        }
        
        $whatsapp_message .= "\n💬 *Mesaj:*\n" . $message . "\n\n";
        $whatsapp_message .= "🌐 *Mənbə:* NextCode Group Website\n";
        $whatsapp_message .= "⏰ *Tarix:* " . date('d.m.Y H:i') . "\n";
        
        // URL encode
        $encoded_message = urlencode($whatsapp_message);
        
        // WhatsApp linkini hazırla
        $whatsapp_number = '380972580000';
        $whatsapp_url = "https://wa.me/{$whatsapp_number}?text={$encoded_message}";
        
        echo json_encode([
            'success' => true, 
            'message' => 'Mesajınız uğurla göndərildi! WhatsApp-da cavab göndəriləcək.',
            'whatsapp_url' => $whatsapp_url,
            'redirect' => true
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Mesaj göndərilərkən xəta baş verdi']);
    }
    
} catch (PDOException $e) {
    error_log('Contact form error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Veritabanı xətası baş verdi']);
}
?>