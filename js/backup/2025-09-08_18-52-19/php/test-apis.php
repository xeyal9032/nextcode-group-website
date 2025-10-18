<?php
/**
 * API Test Script
 * NextCode Group - API Endpoint Tester
 */

header('Content-Type: text/html; charset=utf-8');

// Test configuration
$baseUrl = 'http://localhost';
$apiEndpoints = [
    'about' => [
        'url' => '/api/about.php',
        'methods' => ['GET'],
        'params' => [
            '' => 'Tüm veriler',
            '?action=team' => 'Takım üyeleri',
            '?action=stats' => 'Şirket istatistikleri',
            '?action=timeline' => 'Şirket zaman çizelgesi'
        ]
    ],
    'pricing' => [
        'url' => '/api/pricing.php',
        'methods' => ['GET'],
        'params' => [
            '' => 'Tüm veriler',
            '?action=packages' => 'Fiyat paketleri',
            '?action=testimonials' => 'Müşteri yorumları',
            '?action=featured' => 'Öne çıkan paketler'
        ]
    ],
    'faq' => [
        'url' => '/api/faq.php',
        'methods' => ['GET'],
        'params' => [
            '' => 'Tüm veriler',
            '?action=categories' => 'FAQ kategorileri',
            '?action=items' => 'FAQ öğeleri',
            '?action=featured' => 'Öne çıkan FAQ\'lar',
            '?action=search&q=sual' => 'Arama testi'
        ]
    ],
    'blog' => [
        'url' => '/api/blog.php',
        'methods' => ['GET'],
        'params' => [
            '' => 'Tüm blog yazıları',
            '?limit=5' => '5 yazı ile sınırlı',
            '?featured=1' => 'Öne çıkan yazılar'
        ]
    ],
    'services' => [
        'url' => '/api/services.php',
        'methods' => ['GET'],
        'params' => [
            '' => 'Tüm hizmetler'
        ]
    ],
    'portfolio' => [
        'url' => '/api/portfolio.php',
        'methods' => ['GET'],
        'params' => [
            '?action=get_projects' => 'Tüm projeler',
            '?action=get_categories' => 'Proje kategorileri',
            '?action=get_technologies' => 'Teknolojiler'
        ]
    ],
    'contact' => [
        'url' => '/api/contact.php',
        'methods' => ['POST'],
        'test_data' => [
            'name' => 'Test Kullanıcı',
            'email' => 'test@example.com',
            'phone' => '+994501234567',
            'subject' => 'Test Mesajı',
            'message' => 'Bu bir test mesajıdır.'
        ]
    ],
    'settings' => [
        'url' => '/api/settings.php',
        'methods' => ['GET'],
        'params' => [
            '' => 'Site ayarları'
        ]
    ]
];

function testAPI($endpoint, $config) {
    global $baseUrl;
    
    echo "<div class='api-test'>";
    echo "<h3>🔍 {$endpoint} API Testi</h3>";
    
    foreach ($config['methods'] as $method) {
        if ($method === 'GET' && isset($config['params'])) {
            foreach ($config['params'] as $param => $description) {
                $url = $baseUrl . $config['url'] . $param;
                echo "<div class='test-case'>";
                echo "<h4>GET: {$description}</h4>";
                echo "<p><strong>URL:</strong> <code>{$url}</code></p>";
                
                $result = testGetRequest($url);
                displayResult($result);
                echo "</div>";
            }
        } elseif ($method === 'POST' && isset($config['test_data'])) {
            $url = $baseUrl . $config['url'];
            echo "<div class='test-case'>";
            echo "<h4>POST: Test verisi gönderimi</h4>";
            echo "<p><strong>URL:</strong> <code>{$url}</code></p>";
            
            $result = testPostRequest($url, $config['test_data']);
            displayResult($result);
            echo "</div>";
        }
    }
    
    echo "</div>";
}

function testGetRequest($url) {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'header' => "Accept: application/json\r\n"
        ]
    ]);
    
    $startTime = microtime(true);
    $response = @file_get_contents($url, false, $context);
    $endTime = microtime(true);
    
    $responseTime = round(($endTime - $startTime) * 1000, 2);
    
    if ($response === false) {
        return [
            'success' => false,
            'error' => 'İstek başarısız oldu',
            'response_time' => $responseTime
        ];
    }
    
    $data = json_decode($response, true);
    
    return [
        'success' => true,
        'data' => $data,
        'response_time' => $responseTime,
        'raw_response' => $response
    ];
}

function testPostRequest($url, $postData) {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'timeout' => 10,
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode($postData)
        ]
    ]);
    
    $startTime = microtime(true);
    $response = @file_get_contents($url, false, $context);
    $endTime = microtime(true);
    
    $responseTime = round(($endTime - $startTime) * 1000, 2);
    
    if ($response === false) {
        return [
            'success' => false,
            'error' => 'İstek başarısız oldu',
            'response_time' => $responseTime
        ];
    }
    
    $data = json_decode($response, true);
    
    return [
        'success' => true,
        'data' => $data,
        'response_time' => $responseTime,
        'raw_response' => $response,
        'post_data' => $postData
    ];
}

function displayResult($result) {
    if (!$result['success']) {
        echo "<div class='result error'>";
        echo "<p><strong>❌ Hata:</strong> {$result['error']}</p>";
        echo "<p><strong>⏱️ Yanıt Süresi:</strong> {$result['response_time']} ms</p>";
        echo "</div>";
        return;
    }
    
    $data = $result['data'];
    $isValidJson = json_last_error() === JSON_ERROR_NONE;
    
    echo "<div class='result success'>";
    echo "<p><strong>✅ Durum:</strong> Başarılı</p>";
    echo "<p><strong>⏱️ Yanıt Süresi:</strong> {$result['response_time']} ms</p>";
    
    if ($isValidJson && is_array($data)) {
        echo "<p><strong>📊 JSON Geçerli:</strong> Evet</p>";
        
        if (isset($data['success'])) {
            $status = $data['success'] ? '✅ Başarılı' : '❌ Başarısız';
            echo "<p><strong>🎯 API Durumu:</strong> {$status}</p>";
        }
        
        if (isset($data['data']) && is_array($data['data'])) {
            $count = count($data['data']);
            echo "<p><strong>📈 Veri Sayısı:</strong> {$count}</p>";
        }
        
        if (isset($data['message'])) {
            echo "<p><strong>💬 Mesaj:</strong> {$data['message']}</p>";
        }
        
        echo "<details>";
        echo "<summary>📋 JSON Yanıtı Görüntüle</summary>";
        echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre>";
        echo "</details>";
    } else {
        echo "<p><strong>⚠️ JSON Geçerli:</strong> Hayır</p>";
        echo "<details>";
        echo "<summary>📄 Ham Yanıt Görüntüle</summary>";
        echo "<pre>" . htmlspecialchars($result['raw_response']) . "</pre>";
        echo "</details>";
    }
    
    if (isset($result['post_data'])) {
        echo "<details>";
        echo "<summary>📤 Gönderilen Veri</summary>";
        echo "<pre>" . htmlspecialchars(json_encode($result['post_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre>";
        echo "</details>";
    }
    
    echo "</div>";
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextCode API Test Sonuçları</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }
        .api-test {
            margin-bottom: 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .api-test h3 {
            background: #007bff;
            color: white;
            margin: 0;
            padding: 15px 20px;
            font-size: 1.2em;
        }
        .test-case {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        .test-case:last-child {
            border-bottom: none;
        }
        .test-case h4 {
            color: #555;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .result {
            margin-top: 15px;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .result.success {
            background-color: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .result.error {
            background-color: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 0.85em;
            line-height: 1.4;
        }
        details {
            margin-top: 10px;
        }
        summary {
            cursor: pointer;
            font-weight: bold;
            padding: 5px 0;
            color: #007bff;
        }
        summary:hover {
            color: #0056b3;
        }
        .stats {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .stats h2 {
            margin-top: 0;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 NextCode Group API Test Sonuçları</h1>
        
        <div class="stats">
            <h2>📊 Test İstatistikleri</h2>
            <p><strong>Test Zamanı:</strong> <?php echo date('d.m.Y H:i:s'); ?></p>
            <p><strong>Test Edilen API Sayısı:</strong> <?php echo count($apiEndpoints); ?></p>
            <p><strong>Sunucu:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor'; ?></p>
            <p><strong>PHP Sürümü:</strong> <?php echo PHP_VERSION; ?></p>
        </div>
        
        <?php
        foreach ($apiEndpoints as $endpoint => $config) {
            testAPI($endpoint, $config);
        }
        ?>
        
        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>NextCode Group</strong> - API Test Scripti</p>
            <p>Tüm API endpoint'leri test edildi. Hata durumunda lütfen geliştirici ekibi ile iletişime geçin.</p>
        </div>
    </div>
</body>
</html>