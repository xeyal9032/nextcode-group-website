<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Önce services tablosunun varlığını kontrol et
        $tableExists = false;
        try {
            $checkTable = $conn->query("SHOW TABLES LIKE 'services'");
            $tableExists = $checkTable->rowCount() > 0;
        } catch (Exception $e) {
            $tableExists = false;
        }
        
        if ($tableExists) {
            // Tablo varsa verileri çek
            $stmt = $conn->prepare("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC");
            $stmt->execute();
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Tablo yoksa fallback data kullan
            $services = [
                [
                    'id' => 1,
                    'title' => 'SEO Optimizasiya',
                    'description' => 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün peşəkar SEO xidmətləri',
                    'icon' => 'fas fa-search',
                    'price' => '300₼-dən başlayaraq',
                    'duration' => '3-6 ay',
                    'status' => 'active'
                ],
                [
                    'id' => 2,
                    'title' => 'Sosial Media İdarəçiliyi',
                    'description' => 'Sosial media platformalarında güclü varlıq yaratmaq və idarə etmək',
                    'icon' => 'fas fa-share-alt',
                    'price' => '250₼-dən başlayaraq',
                    'duration' => 'Davamlı',
                    'status' => 'active'
                ],
                [
                    'id' => 3,
                    'title' => 'Brendinq və Dizayn',
                    'description' => 'Güclü brend kimliyi yaratmaq və vizual dizayn həlləri',
                    'icon' => 'fas fa-palette',
                    'price' => '500₼-dən başlayaraq',
                    'duration' => '2-4 həftə',
                    'status' => 'active'
                ],
                [
                    'id' => 4,
                    'title' => 'Reklam Kampaniyaları',
                    'description' => 'Google Ads, Facebook Ads və digər platformalarda effektiv reklam kampaniyaları',
                    'icon' => 'fas fa-bullhorn',
                    'price' => '400₼-dən başlayaraq',
                    'duration' => '1-3 ay',
                    'status' => 'active'
                ]
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $services
        ]);
    }
} catch (Exception $e) {
    // Hata durumunda fallback data döndür
    $fallbackServices = [
        [
            'id' => 1,
            'title' => 'SEO Optimizasiya',
            'description' => 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün peşəkar SEO xidmətləri',
            'icon' => 'fas fa-search',
            'price' => '300₼-dən başlayaraq',
            'duration' => '3-6 ay',
            'status' => 'active'
        ],
        [
            'id' => 2,
            'title' => 'Sosial Media İdarəçiliyi',
            'description' => 'Sosial media platformalarında güclü varlıq yaratmaq və idarə etmək',
            'icon' => 'fas fa-share-alt',
            'price' => '250₼-dən başlayaraq',
            'duration' => 'Davamlı',
            'status' => 'active'
        ],
        [
            'id' => 3,
            'title' => 'Brendinq və Dizayn',
            'description' => 'Güclü brend kimliyi yaratmaq və vizual dizayn həlləri',
            'icon' => 'fas fa-palette',
            'price' => '500₼-dən başlayaraq',
            'duration' => '2-4 həftə',
            'status' => 'active'
        ],
        [
            'id' => 4,
            'title' => 'Reklam Kampaniyaları',
            'description' => 'Google Ads, Facebook Ads və digər platformalarda effektiv reklam kampaniyaları',
            'icon' => 'fas fa-bullhorn',
            'price' => '400₼-dən başlayaraq',
            'duration' => '1-3 ay',
            'status' => 'active'
        ]
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $fallbackServices,
        'message' => 'Using fallback data due to database connection issue'
    ]);
}
?>