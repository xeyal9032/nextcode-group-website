<?php
/**
 * CSRF Token Test Script
 * Bu script CSRF korumasının gerçekten çalışıp çalışmadığını test eder
 */

class CSRFTest {
    private $baseUrl;
    private $sessionCookie;
    
    public function __construct($baseUrl) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }
    
    /**
     * CSRF korumasını test et
     */
    public function testCSRFProtection() {
        echo "=== CSRF Koruması Test ===\n";
        echo "Test URL: {$this->baseUrl}\n";
        echo "Başlangıç zamanı: " . date('Y-m-d H:i:s') . "\n\n";
        
        // 1. Login sayfasını al ve session cookie'sini yakala
        echo "1. Login sayfasından CSRF token alınıyor...\n";
        $loginPage = $this->getLoginPage();
        
        if (!$loginPage['success']) {
            echo "❌ Login sayfası alınamadı: " . $loginPage['error'] . "\n";
            return false;
        }
        
        // 2. CSRF token'ı çıkar
        $csrfToken = $this->extractCSRFToken($loginPage['content']);
        
        if (!$csrfToken) {
            echo "❌ CSRF token bulunamadı!\n";
            return false;
        }
        
        echo "✅ CSRF token bulundu: " . substr($csrfToken, 0, 16) . "...\n";
        
        // 3. Geçerli token ile login denemesi
        echo "2. Geçerli CSRF token ile login testi...\n";
        $validLogin = $this->testLoginWithToken($csrfToken);
        
        if ($validLogin['success']) {
            echo "✅ Geçerli CSRF token ile login başarılı\n";
        } else {
            echo "⚠️ Geçerli CSRF token ile login başarısız: " . $validLogin['error'] . "\n";
        }
        
        // 4. Geçersiz token ile login denemesi
        echo "3. Geçersiz CSRF token ile login testi...\n";
        $invalidLogin = $this->testLoginWithToken('invalid_token_12345');
        
        if (!$invalidLogin['success'] && strpos($invalidLogin['error'], 'token') !== false) {
            echo "✅ Geçersiz CSRF token reddedildi (Güvenlik çalışıyor!)\n";
            $this->addResult('csrf_protection', 'success', 'CSRF koruması aktif ve çalışıyor');
        } else {
            echo "❌ Geçersiz CSRF token kabul edildi (Güvenlik açığı!)\n";
            $this->addResult('csrf_protection', 'error', 'CSRF koruması çalışmıyor');
        }
        
        // 5. Token olmadan login denemesi
        echo "4. Token olmadan login testi...\n";
        $noTokenLogin = $this->testLoginWithoutToken();
        
        if (!$noTokenLogin['success']) {
            echo "✅ Token olmadan login reddedildi (Güvenlik çalışıyor!)\n";
            $this->addResult('csrf_protection', 'success', 'Token gereksinimi aktif');
        } else {
            echo "❌ Token olmadan login kabul edildi (Güvenlik açığı!)\n";
            $this->addResult('csrf_protection', 'error', 'Token gereksinimi çalışmıyor');
        }
        
        return true;
    }
    
    /**
     * Login sayfasını al
     */
    private function getLoginPage() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/admin/login.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/csrf_test_cookies.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, 'CSRF Test Bot');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                'success' => false,
                'error' => curl_error($ch)
            ];
        }
        
        curl_close($ch);
        
        $headers = substr($response, 0, $headerSize);
        $content = substr($response, $headerSize);
        
        // Session cookie'sini yakala
        if (preg_match('/Set-Cookie: ([^;]+)/', $headers, $matches)) {
            $this->sessionCookie = $matches[1];
        }
        
        return [
            'success' => true,
            'content' => $content,
            'headers' => $headers,
            'status_code' => $httpCode
        ];
    }
    
    /**
     * CSRF token'ı HTML'den çıkar
     */
    private function extractCSRFToken($html) {
        // Debug: HTML içeriğini kontrol et
        echo "    HTML içeriği kontrol ediliyor...\n";
        echo "    HTML uzunluğu: " . strlen($html) . " karakter\n";
        
        // CSRF ile ilgili tüm içerikleri ara
        if (strpos($html, 'csrf_token') !== false) {
            echo "    ✅ 'csrf_token' metni bulundu\n";
        } else {
            echo "    ❌ 'csrf_token' metni bulunamadı\n";
        }
        
        if (strpos($html, 'csrf') !== false) {
            echo "    ✅ 'csrf' metni bulundu\n";
        } else {
            echo "    ❌ 'csrf' metni bulunamadı\n";
        }
        
        if (strpos($html, 'generate_csrf_token') !== false) {
            echo "    ✅ 'generate_csrf_token' fonksiyonu bulundu\n";
        } else {
            echo "    ❌ 'generate_csrf_token' fonksiyonu bulunamadı\n";
        }
        
        // Token'ı çıkarmaya çalış
        if (preg_match('/name="csrf_token"\s+value="([^"]+)"/', $html, $matches)) {
            echo "    ✅ CSRF token başarıyla çıkarıldı\n";
            return $matches[1];
        }
        
        // Alternatif pattern'ları dene
        if (preg_match('/value="([a-f0-9]{64})"/', $html, $matches)) {
            echo "    ✅ 64 karakterlik hex token bulundu\n";
            return $matches[1];
        }
        
        echo "    ❌ Hiçbir CSRF token pattern'i eşleşmedi\n";
        return false;
    }
    
    /**
     * Token ile login testi
     */
    private function testLoginWithToken($token) {
        $ch = curl_init();
        
        $postData = http_build_query([
            'username' => 'admin',
            'password' => 'Admin123!@#',
            'csrf_token' => $token
        ]);
        
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/admin/login.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/csrf_test_cookies.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, 'CSRF Test Bot');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                'success' => false,
                'error' => curl_error($ch)
            ];
        }
        
        curl_close($ch);
        
        $headers = substr($response, 0, $headerSize);
        $content = substr($response, $headerSize);
        
        // Location header'ı kontrol et (başarılı login redirect eder)
        $isRedirect = strpos($headers, 'Location: dashboard.php') !== false;
        
        return [
            'success' => $isRedirect,
            'content' => $content,
            'headers' => $headers,
            'status_code' => $httpCode,
            'error' => $isRedirect ? '' : 'Login başarısız'
        ];
    }
    
    /**
     * Token olmadan login testi
     */
    private function testLoginWithoutToken() {
        $ch = curl_init();
        
        $postData = http_build_query([
            'username' => 'admin',
            'password' => 'Admin123!@#'
            // csrf_token yok
        ]);
        
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/admin/login.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/csrf_test_cookies.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, 'CSRF Test Bot');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                'success' => false,
                'error' => curl_error($ch)
            ];
        }
        
        curl_close($ch);
        
        $headers = substr($response, 0, $headerSize);
        $content = substr($response, $headerSize);
        
        // Token hatası mesajını kontrol et
        $hasTokenError = strpos($content, 'token') !== false || 
                        strpos($content, 'güvenlik') !== false ||
                        strpos($content, 'security') !== false;
        
        return [
            'success' => !$hasTokenError,
            'content' => $content,
            'headers' => $headers,
            'status_code' => $httpCode,
            'error' => $hasTokenError ? 'Token hatası tespit edildi' : 'Token kontrolü yapılmadı'
        ];
    }
    
    /**
     * Test sonucu ekle
     */
    private function addResult($test, $status, $message) {
        echo "[$test] $status: $message\n";
    }
    
    /**
     * Cookie dosyasını temizle
     */
    public function cleanup() {
        $cookieFile = sys_get_temp_dir() . '/csrf_test_cookies.txt';
        if (file_exists($cookieFile)) {
            unlink($cookieFile);
        }
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $csrfTest = new CSRFTest('http://nextcodegroup.ostwind.az');
    
    try {
        $csrfTest->testCSRFProtection();
        echo "\n=== CSRF Test Tamamlandı ===\n";
    } catch (Exception $e) {
        echo "Hata: " . $e->getMessage() . "\n";
    } finally {
        $csrfTest->cleanup();
    }
}

// Web arayüzü
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $csrfTest = new CSRFTest('http://nextcodegroup.ostwind.az');
    
    switch ($_GET['action']) {
        case 'test':
            ob_start();
            try {
                $csrfTest->testCSRFProtection();
                $output = ob_get_clean();
                echo json_encode([
                    'success' => true,
                    'output' => $output
                ]);
            } catch (Exception $e) {
                ob_end_clean();
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            } finally {
                $csrfTest->cleanup();
            }
            break;
            
        default:
            echo json_encode(['error' => 'Geçersiz işlem']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSRF Test - NextCode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1><i class="fas fa-shield-alt"></i> CSRF Koruması Test</h1>
        
        <div class="alert alert-info">
            <h5>Test Edilecek:</h5>
            <p><strong>URL:</strong> http://nextcodegroup.ostwind.az/admin/login.php</p>
            <p><strong>Test:</strong> CSRF token korumasının gerçek işlevselliği</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>CSRF Test Senaryoları</h5>
            </div>
            <div class="card-body">
                <ul>
                    <li>✅ Geçerli CSRF token ile login</li>
                    <li>❌ Geçersiz CSRF token ile login</li>
                    <li>❌ Token olmadan login</li>
                </ul>
            </div>
        </div>
        
        <div class="mt-4">
            <button id="runCSRFTest" class="btn btn-primary btn-lg">
                <i class="fas fa-shield-alt"></i> CSRF Korumasını Test Et
            </button>
        </div>
        
        <div id="csrfResults" class="mt-4" style="display: none;">
            <!-- Test sonuçları buraya yüklenecek -->
        </div>
    </div>

    <script>
        document.getElementById('runCSRFTest').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Test Ediliyor...';
            
            fetch('csrf_test.php?action=test')
                .then(response => response.json())
                .then(data => {
                    showCSRFResults(data);
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-shield-alt"></i> CSRF Korumasını Test Et';
                })
                .catch(error => {
                    console.error('Hata:', error);
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-shield-alt"></i> CSRF Korumasını Test Et';
                });
        });
        
        function showCSRFResults(data) {
            const resultsDiv = document.getElementById('csrfResults');
            resultsDiv.style.display = 'block';
            
            if (data.success) {
                resultsDiv.innerHTML = `
                    <div class="card">
                        <div class="card-header">
                            <h5>CSRF Test Sonuçları</h5>
                        </div>
                        <div class="card-body">
                            <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px;">${data.output}</pre>
                        </div>
                    </div>
                `;
            } else {
                resultsDiv.innerHTML = '<div class="alert alert-danger">Test sırasında hata oluştu: ' + data.error + '</div>';
            }
        }
    </script>
</body>
</html>
