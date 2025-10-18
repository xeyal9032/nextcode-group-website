<?php
// İletişim bilgileri API endpoint
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Error reporting
error_reporting(0);
ini_set('display_errors', 0);

require_once '../config/database.php';

try {
    // Check if $pdo is available
    if (!isset($pdo) || !$pdo) {
        throw new Exception('Database connection not available');
    }
    
    // Check if contact_info table exists
    $table_check = $pdo->query("SHOW TABLES LIKE 'contact_info'")->fetch();
    
    if (!$table_check) {
        // Table doesn't exist, return default data
        $contact_info = null;
    } else {
        // İletişim bilgilerini çek
        $stmt = $pdo->query("SELECT * FROM contact_info LIMIT 1");
        $contact_info = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if ($contact_info) {
        echo json_encode([
            'success' => true,
            'data' => [
                'company_name' => $contact_info['company_name'] ?? 'NextCode Group',
                'address' => $contact_info['address'] ?? 'Bakı şəhəri, Nəsimi rayonu, 28 May küçəsi 15',
                'phone' => $contact_info['phone'] ?? '+380 97 258 00 00',
                'email' => $contact_info['email'] ?? 'xeyalcemilli9032@gmail.com',
                'website' => $contact_info['website'] ?? 'https://nextcodegroup.ostwind.az',
                'working_hours' => $contact_info['working_hours'] ?? 'Bazar ertəsi - Cümə: 09:00-18:00',
                'facebook' => $contact_info['facebook'] ?? '',
                'instagram' => $contact_info['instagram'] ?? '',
                'linkedin' => $contact_info['linkedin'] ?? '',
                'twitter' => $contact_info['twitter'] ?? '',
                'whatsapp' => $contact_info['whatsapp'] ?? '',
                'telegram' => $contact_info['telegram'] ?? ''
            ]
        ], JSON_UNESCAPED_UNICODE);
    } else {
        // Varsayılan bilgiler
        echo json_encode([
            'success' => true,
            'data' => [
                'company_name' => 'NextCode Group',
                'address' => 'Bakı şəhəri, Nəsimi rayonu, 28 May küçəsi 15',
                'phone' => '+380 97 258 00 00',
                'email' => 'xeyalcemilli9032@gmail.com',
                'website' => 'https://nextcodegroup.ostwind.az',
                'working_hours' => 'Bazar ertəsi - Cümə: 09:00-18:00',
                'facebook' => '',
                'instagram' => '',
                'linkedin' => '',
                'twitter' => '',
                'whatsapp' => '',
                'telegram' => ''
            ]
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server hatası: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
