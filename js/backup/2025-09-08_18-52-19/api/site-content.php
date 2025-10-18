<?php
// Site içeriği API endpoint - Merkezi içerik yönetimi
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';

try {
    $pdo = new PDO(
        'mysql:host=gtorg.mysql.tools;dbname=gtorg_nextcode;charset=utf8mb4',
        'gtorg_nextcode',
        ';849#dVEyg'
    );
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // İçerik getir
            $section = $_GET['section'] ?? null;
            $key = $_GET['key'] ?? null;
            
            if ($key) {
                // Belirli bir anahtar için içerik getir
                $stmt = $pdo->prepare("SELECT * FROM site_content WHERE content_key = ? AND is_active = 1");
                $stmt->execute([$key]);
                $content = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($content) {
                    echo json_encode([
                        'success' => true,
                        'data' => $content
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode([
                        'success' => false,
                        'error' => 'İçerik bulunamadı'
                    ], JSON_UNESCAPED_UNICODE);
                }
            } elseif ($section) {
                // Belirli bir bölüm için tüm içerikleri getir
                $stmt = $pdo->prepare("SELECT content_key, content_value, content_type FROM site_content WHERE page_section = ? AND is_active = 1");
                $stmt->execute([$section]);
                $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $section_data = [];
                foreach ($contents as $content) {
                    $section_data[$content['content_key']] = $content['content_value'];
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $section_data
                ], JSON_UNESCAPED_UNICODE);
            } else {
                // Tüm aktif içerikleri getir
                $stmt = $pdo->query("SELECT content_key, content_value, content_type, page_section, updated_at, is_active FROM site_content WHERE is_active = 1 ORDER BY page_section, content_key");
                $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $contents
                ], JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'POST':
            // Yeni içerik ekle (sadece admin için)
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['content_key']) || !isset($input['content_value'])) {
                throw new Exception('Gerekli alanlar eksik');
            }
            
            $stmt = $pdo->prepare("INSERT INTO site_content (content_key, content_value, content_type, page_section) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = CURRENT_TIMESTAMP");
            $stmt->execute([
                $input['content_key'],
                $input['content_value'],
                $input['content_type'] ?? 'text',
                $input['page_section'] ?? 'general'
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'İçerik başarıyla eklendi/güncellendi'
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'PUT':
            // İçerik güncelle (sadece admin için)
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['content_key'])) {
                throw new Exception('Content key gerekli');
            }
            
            $stmt = $pdo->prepare("UPDATE site_content SET content_value = ?, content_type = ?, page_section = ?, updated_at = CURRENT_TIMESTAMP WHERE content_key = ?");
            $stmt->execute([
                $input['content_value'] ?? '',
                $input['content_type'] ?? 'text',
                $input['page_section'] ?? 'general',
                $input['content_key']
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'İçerik başarıyla güncellendi'
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'DELETE':
            // İçerik sil (sadece admin için)
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['content_key'])) {
                throw new Exception('Content key gerekli');
            }
            
            $stmt = $pdo->prepare("DELETE FROM site_content WHERE content_key = ?");
            $stmt->execute([$input['content_key']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'İçerik başarıyla silindi'
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'error' => 'Method not allowed'
            ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Veritabanı hatası: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
