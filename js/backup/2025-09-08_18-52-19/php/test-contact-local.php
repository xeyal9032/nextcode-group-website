<?php
// Local test version of contact form handler
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

// Simulate successful database insert
error_log('Simulating database insert for: ' . $name . ' (' . $email . ')');

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

// Log the WhatsApp message for debugging
error_log('WhatsApp message prepared: ' . $whatsapp_message);
error_log('WhatsApp URL: ' . $whatsapp_url);

echo json_encode([
    'success' => true, 
    'message' => 'Mesajınız uğurla göndərildi! WhatsApp-da cavab göndəriləcək.',
    'whatsapp_url' => $whatsapp_url,
    'redirect' => true,
    'debug_info' => [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'company' => $company,
        'service' => $service,
        'budget' => $budget,
        'message_length' => strlen($message)
    ]
]);
?>
