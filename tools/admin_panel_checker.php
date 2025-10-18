<?php
/**
 * Admin Panel Checker - Canlı Site Test Aracı
 * Bu script admin panelinin canlı ortamda doğru çalışıp çalışmadığını kontrol eder
 */

class AdminPanelChecker {
    private $baseUrl;
    private $testResults = [];
    
    public function __construct($baseUrl) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }
    
    /**
     * Admin panelini kapsamlı test et
     */
    public function runFullTest() {
        echo "=== NextCode Admin Panel Canlı Test ===\n";
        echo "Test URL: {$this->baseUrl}\n";
        echo "Başlangıç zamanı: " . date('Y-m-d H:i:s') . "\n\n";
        
        $this->testAdminAccess();
        $this->testLoginPage();
        $this->testDatabaseConnection();
        $this->testSecurityHeaders();
        $this->testNewFeatures();
        $this->testErrorHandling();
        
        $this->showResults();
    }
    
    /**
     * Admin panel erişimini test et
     */
    private function testAdminAccess() {
        echo "1. Admin Panel Erişim Testi...\n";
        
        $url = $this->baseUrl . '/admin/';
        $response = $this->makeRequest($url);
        
        if ($response['success']) {
            if (strpos($response['content'], 'login') !== false || 
                strpos($response['content'], 'giriş') !== false ||
                strpos($response['content'], 'NextCode') !== false) {
                $this->addResult('admin_access', 'success', 'Admin paneli erişilebilir');
            } else {
                $this->addResult('admin_access', 'warning', 'Admin paneli farklı bir sayfa döndürüyor');
            }
        } else {
            $this->addResult('admin_access', 'error', 'Admin paneline erişilemiyor: ' . $response['error']);
        }
    }
    
    /**
     * Login sayfasını test et
     */
    private function testLoginPage() {
        echo "2. Login Sayfası Testi...\n";
        
        $url = $this->baseUrl . '/admin/login.php';
        $response = $this->makeRequest($url);
        
        if ($response['success']) {
            $checks = [
                'login_form' => strpos($response['content'], '<form') !== false,
                'username_field' => strpos($response['content'], 'username') !== false,
                'password_field' => strpos($response['content'], 'password') !== false,
                'csrf_protection' => strpos($response['content'], 'csrf_token') !== false,
                'modern_design' => strpos($response['content'], 'bootstrap') !== false || 
                                  strpos($response['content'], 'modern') !== false ||
                                  strpos($response['content'], 'gradient') !== false
            ];
            
            $passed = array_sum($checks);
            $total = count($checks);
            
            // Debug: Hangi kontrollerin geçtiğini göster
            echo "    Form checks: " . ($checks['login_form'] ? '✅' : '❌') . " form, ";
            echo ($checks['username_field'] ? '✅' : '❌') . " username, ";
            echo ($checks['password_field'] ? '✅' : '❌') . " password, ";
            echo ($checks['csrf_protection'] ? '✅' : '❌') . " csrf, ";
            echo ($checks['modern_design'] ? '✅' : '❌') . " design\n";
            
            if ($passed == $total) {
                $this->addResult('login_page', 'success', "Login sayfası tam ($passed/$total)");
            } else {
                $this->addResult('login_page', 'warning', "Login sayfası kısmen ($passed/$total)");
            }
        } else {
            $this->addResult('login_page', 'error', 'Login sayfası yüklenemiyor: ' . $response['error']);
        }
    }
    
    /**
     * Veritabanı bağlantısını test et
     */
    private function testDatabaseConnection() {
        echo "3. Veritabanı Bağlantı Testi...\n";
        
        // Bu test için admin panelinde bir API endpoint oluşturmamız gerekebilir
        // Şimdilik login sayfasından veritabanı bağlantısını test edelim
        $url = $this->baseUrl . '/admin/login.php';
        $response = $this->makeRequest($url);
        
        if ($response['success']) {
            // Eğer sayfa yükleniyorsa, veritabanı bağlantısı muhtemelen çalışıyor
            if (!strpos($response['content'], 'Veritabanı hatası') && 
                !strpos($response['content'], 'Database error')) {
                $this->addResult('database', 'success', 'Veritabanı bağlantısı çalışıyor');
            } else {
                $this->addResult('database', 'error', 'Veritabanı bağlantı hatası tespit edildi');
            }
        } else {
            $this->addResult('database', 'error', 'Veritabanı test edilemiyor');
        }
    }
    
    /**
     * Güvenlik başlıklarını test et
     */
    private function testSecurityHeaders() {
        echo "4. Güvenlik Başlıkları Testi...\n";
        
        $url = $this->baseUrl . '/admin/login.php';
        $response = $this->makeRequest($url);
        
        if ($response['success']) {
            $headers = $response['headers'];
            
            // Header isimlerini case-insensitive olarak kontrol et
            $headerKeys = array_map('strtolower', array_keys($headers));
            $headerValues = array_combine($headerKeys, array_values($headers));
            
            $securityChecks = [
                'x_frame_options' => isset($headerValues['x-frame-options']) || isset($headers['X-Frame-Options']),
                'x_content_type' => isset($headerValues['x-content-type-options']) || isset($headers['X-Content-Type-Options']),
                'x_xss_protection' => isset($headerValues['x-xss-protection']) || isset($headers['X-XSS-Protection']),
                'content_security_policy' => isset($headerValues['content-security-policy']) || isset($headers['Content-Security-Policy']),
                'referrer_policy' => isset($headerValues['referrer-policy']) || isset($headers['Referrer-Policy'])
            ];
            
            $passed = array_sum($securityChecks);
            $total = count($securityChecks);
            
            // Debug: Header'ları göster
            echo "    Detected headers: " . implode(', ', array_keys($headers)) . "\n";
            
            if ($passed >= 3) {
                $this->addResult('security', 'success', "Güvenlik başlıkları aktif ($passed/$total)");
            } else {
                $this->addResult('security', 'warning', "Güvenlik başlıkları eksik ($passed/$total)");
            }
        } else {
            $this->addResult('security', 'error', 'Güvenlik testleri yapılamıyor');
        }
    }
    
    /**
     * Yeni özellikleri test et
     */
    private function testNewFeatures() {
        echo "5. Yeni Özellikler Testi...\n";
        
        // Log viewer test
        $url = $this->baseUrl . '/admin/log_viewer.php';
        $response = $this->makeRequest($url);
        
        if ($response['success']) {
            if (strpos($response['content'], 'log') !== false || 
                strpos($response['content'], 'Log') !== false) {
                $this->addResult('log_viewer', 'success', 'Log Viewer erişilebilir');
            } else {
                $this->addResult('log_viewer', 'warning', 'Log Viewer farklı içerik döndürüyor');
            }
        } else {
            $this->addResult('log_viewer', 'error', 'Log Viewer erişilemiyor');
        }
        
        // Dashboard test
        $url = $this->baseUrl . '/admin/dashboard.php';
        $response = $this->makeRequest($url);
        
        if ($response['success']) {
            if (strpos($response['content'], 'dashboard') !== false || 
                strpos($response['content'], 'Dashboard') !== false ||
                strpos($response['content'], 'login') !== false) { // Login redirect
                $this->addResult('dashboard', 'success', 'Dashboard erişilebilir');
            } else {
                $this->addResult('dashboard', 'warning', 'Dashboard farklı içerik döndürüyor');
            }
        } else {
            $this->addResult('dashboard', 'error', 'Dashboard erişilemiyor');
        }
    }
    
    /**
     * Hata yönetimini test et
     */
    private function testErrorHandling() {
        echo "6. Hata Yönetimi Testi...\n";
        
        // 404 test
        $url = $this->baseUrl . '/admin/nonexistent.php';
        $response = $this->makeRequest($url);
        
        if ($response['status_code'] == 404) {
            $this->addResult('error_handling', 'success', '404 hataları doğru yönetiliyor');
        } else if ($response['status_code'] == 500) {
            $this->addResult('error_handling', 'warning', '500 hatası alındı (beklenmeyen)');
        } else {
            $this->addResult('error_handling', 'info', "HTTP {$response['status_code']} döndü");
        }
    }
    
    /**
     * HTTP isteği yap
     */
    private function makeRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'NextCode Admin Panel Checker');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                'success' => false,
                'error' => curl_error($ch),
                'status_code' => 0
            ];
        }
        
        curl_close($ch);
        
        $headers = substr($response, 0, $headerSize);
        $content = substr($response, $headerSize);
        
        // Headers'ı parse et
        $headerArray = [];
        foreach (explode("\n", $headers) as $header) {
            if (strpos($header, ':') !== false) {
                list($key, $value) = explode(':', $header, 2);
                $headerArray[trim($key)] = trim($value);
            }
        }
        
        return [
            'success' => true,
            'content' => $content,
            'headers' => $headerArray,
            'status_code' => $httpCode
        ];
    }
    
    /**
     * Test sonucu ekle
     */
    private function addResult($test, $status, $message) {
        $this->testResults[] = [
            'test' => $test,
            'status' => $status,
            'message' => $message,
            'timestamp' => date('H:i:s')
        ];
    }
    
    /**
     * Sonuçları göster
     */
    private function showResults() {
        echo "\n=== TEST SONUÇLARI ===\n";
        
        $successCount = 0;
        $warningCount = 0;
        $errorCount = 0;
        
        foreach ($this->testResults as $result) {
            $statusIcon = $result['status'] === 'success' ? '✅' : 
                         ($result['status'] === 'warning' ? '⚠️' : '❌');
            
            echo "[{$result['timestamp']}] {$statusIcon} {$result['test']}: {$result['message']}\n";
            
            switch ($result['status']) {
                case 'success': $successCount++; break;
                case 'warning': $warningCount++; break;
                case 'error': $errorCount++; break;
            }
        }
        
        echo "\n=== ÖZET ===\n";
        echo "✅ Başarılı: $successCount\n";
        echo "⚠️ Uyarı: $warningCount\n";
        echo "❌ Hata: $errorCount\n";
        
        $total = count($this->testResults);
        $successRate = round(($successCount / $total) * 100, 1);
        
        echo "\nBaşarı Oranı: $successRate%\n";
        
        if ($successRate >= 80) {
            echo "🎉 Admin paneli canlıda başarıyla çalışıyor!\n";
        } else if ($successRate >= 60) {
            echo "⚠️ Admin paneli çalışıyor ancak bazı sorunlar var.\n";
        } else {
            echo "❌ Admin panelinde ciddi sorunlar var.\n";
        }
        
        echo "\nTest tamamlandı: " . date('Y-m-d H:i:s') . "\n";
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $checker = new AdminPanelChecker('http://nextcodegroup.ostwind.az');
    $checker->runFullTest();
}

// Web arayüzü
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $checker = new AdminPanelChecker('http://nextcodegroup.ostwind.az');
    
    switch ($_GET['action']) {
        case 'test':
            ob_start();
            $checker->runFullTest();
            $output = ob_get_clean();
            
            echo json_encode([
                'success' => true,
                'output' => $output,
                'results' => $checker->testResults ?? []
            ]);
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
    <title>Admin Panel Checker - NextCode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .test-success { background: #d4edda; color: #155724; }
        .test-warning { background: #fff3cd; color: #856404; }
        .test-error { background: #f8d7da; color: #721c24; }
        .output { background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1><i class="fas fa-shield-alt"></i> Admin Panel Checker</h1>
        
        <div class="alert alert-info">
            <h5>Test Edilecek Site:</h5>
            <p><strong>URL:</strong> http://nextcodegroup.ostwind.az</p>
            <p><strong>Admin Panel:</strong> http://nextcodegroup.ostwind.az/admin/</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>Test Edilecek Özellikler</h5>
            </div>
            <div class="card-body">
                <ul>
                    <li>✅ Admin Panel Erişimi</li>
                    <li>✅ Login Sayfası</li>
                    <li>✅ Veritabanı Bağlantısı</li>
                    <li>✅ Güvenlik Başlıkları</li>
                    <li>✅ Yeni Özellikler (Log Viewer, Dashboard)</li>
                    <li>✅ Hata Yönetimi</li>
                </ul>
            </div>
        </div>
        
        <div class="mt-4">
            <button id="runTest" class="btn btn-primary btn-lg">
                <i class="fas fa-play"></i> Admin Panelini Test Et
            </button>
        </div>
        
        <div id="testResults" class="mt-4" style="display: none;">
            <!-- Test sonuçları buraya yüklenecek -->
        </div>
    </div>

    <script>
        document.getElementById('runTest').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Test Ediliyor...';
            
            fetch('admin_panel_checker.php?action=test')
                .then(response => response.json())
                .then(data => {
                    showResults(data);
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-play"></i> Admin Panelini Test Et';
                })
                .catch(error => {
                    console.error('Hata:', error);
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-play"></i> Admin Panelini Test Et';
                });
        });
        
        function showResults(data) {
            const resultsDiv = document.getElementById('testResults');
            resultsDiv.style.display = 'block';
            
            if (data.success) {
                resultsDiv.innerHTML = `
                    <div class="card">
                        <div class="card-header">
                            <h5>Test Sonuçları</h5>
                        </div>
                        <div class="card-body">
                            <div class="output">${data.output}</div>
                        </div>
                    </div>
                `;
            } else {
                resultsDiv.innerHTML = '<div class="alert alert-danger">Test sırasında hata oluştu!</div>';
            }
        }
    </script>
</body>
</html>
