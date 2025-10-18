<?php
// İletişim bilgileri API endpoint
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $pdo = new PDO(
        'mysql:host=gtorg.mysql.tools;dbname=gtorg_nextcode;charset=utf8mb4',
        'gtorg_nextcode',
        ';849#dVEyg'
    );
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // İletişim bilgilerini çek
    $stmt = $pdo->query("SELECT * FROM contact_info LIMIT 1");
    $contact_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($contact_info) {
        echo json_encode([
            'success' => true,
            'data' => [
                'company_name' => $contact_info['company_name'] ?? 'NextCode Group',
                'address' => $contact_info['address'] ?? 'Xocalı prospekti 11, Block A, 3-cü mərtəbə, Bakı 1008, Azərbaycan',
                'phone' => $contact_info['phone'] ?? '+380972580000',
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
            'address' => 'Xocalı prospekti 11, Block A, 3-cü mərtəbə, Bakı 1008, Azərbaycan',
            'phone' => '+380972580000',
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
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Veritabanı hatası: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
