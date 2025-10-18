<?php
/**
 * Google Analytics Test Page
 * Analytics kodunun doğru çalışıp çalışmadığını test eder
 */

define('SECURE_ACCESS', true);
session_start();

$page_title = 'Google Analytics Test - NextCode';
$current_page = 'analytics-test';
$meta_description = 'Google Analytics test sayfası';

require_once 'includes/header.php';
?>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 2rem;
    }
    
    .test-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .test-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    }
    
    .test-card h2 {
        color: #667eea;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .status-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-error {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .status-warning {
        background: #fef3c7;
        color: #92400e;
    }
    
    .code-block {
        background: #1f2937;
        color: #f3f4f6;
        padding: 1rem;
        border-radius: 8px;
        overflow-x: auto;
        margin: 1rem 0;
        font-family: 'Courier New', monospace;
    }
    
    .test-button {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        margin: 0.5rem;
        transition: transform 0.3s;
    }
    
    .test-button:hover {
        transform: translateY(-2px);
    }
    
    .result-box {
        background: #f3f4f6;
        border-left: 4px solid #667eea;
        padding: 1rem;
        border-radius: 4px;
        margin-top: 1rem;
    }
    
    .checklist {
        list-style: none;
        padding: 0;
    }
    
    .checklist li {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: #f8f9fa;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .checklist li.pass::before {
        content: "✓";
        color: #10b981;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    .checklist li.fail::before {
        content: "✗";
        color: #ef4444;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    .checklist li.pending::before {
        content: "○";
        color: #f59e0b;
        font-weight: bold;
        font-size: 1.2rem;
    }
</style>

<div class="test-container">
    <div class="test-card">
        <h1 style="text-align: center; color: #667eea;">
            <i class="fas fa-chart-line"></i> Google Analytics Test
        </h1>
        <p style="text-align: center; color: #6b7280; margin-bottom: 2rem;">
            Google Analytics kodunun doğru çalışıp çalışmadığını kontrol edin
        </p>
    </div>

    <!-- Test 1: Google Analytics Loaded -->
    <div class="test-card">
        <h2><i class="fas fa-code"></i> 1. Analytics Kodu Yüklendi mi?</h2>
        
        <div id="test1-result" class="result-box">
            <strong>Kontrol ediliyor...</strong>
        </div>
        
        <script>
            setTimeout(() => {
                const result = document.getElementById('test1-result');
                
                if (typeof gtag === 'function') {
                    result.innerHTML = '<span class="status-badge status-success">✓ BAŞARILI</span><br><br>Google Analytics kodu başarıyla yüklendi ve gtag function tanımlı.';
                } else {
                    result.innerHTML = '<span class="status-badge status-error">✗ BAŞARISIZ</span><br><br>gtag function bulunamadı. Analytics kodu header\'a eklenmemiş olabilir.';
                }
            }, 1000);
        </script>
    </div>

    <!-- Test 2: DataLayer Check -->
    <div class="test-card">
        <h2><i class="fas fa-database"></i> 2. DataLayer Çalışıyor mu?</h2>
        
        <div id="test2-result" class="result-box">
            <strong>Kontrol ediliyor...</strong>
        </div>
        
        <script>
            setTimeout(() => {
                const result = document.getElementById('test2-result');
                
                if (window.dataLayer && Array.isArray(window.dataLayer)) {
                    const eventCount = window.dataLayer.length;
                    result.innerHTML = `
                        <span class="status-badge status-success">✓ BAŞARILI</span><br><br>
                        DataLayer aktif ve ${eventCount} event içeriyor.<br>
                        <div class="code-block" style="max-height: 200px; overflow-y: auto;">
                            ${JSON.stringify(window.dataLayer, null, 2)}
                        </div>
                    `;
                } else {
                    result.innerHTML = '<span class="status-badge status-error">✗ BAŞARISIZ</span><br><br>DataLayer bulunamadı.';
                }
            }, 1500);
        </script>
    </div>

    <!-- Test 3: Network Request -->
    <div class="test-card">
        <h2><i class="fas fa-network-wired"></i> 3. Analytics Request Gönderiliyor mu?</h2>
        
        <div class="result-box">
            <strong>Test için:</strong>
            <ol style="margin-left: 1.5rem; margin-top: 1rem;">
                <li>Chrome DevTools açın (F12)</li>
                <li>Network tab'a gidin</li>
                <li>Filter: "google-analytics" veya "collect"</li>
                <li>Sayfa yenilendiğinde request görmelisiniz</li>
            </ol>
        </div>
        
        <button class="test-button" onclick="testAnalyticsRequest()">
            <i class="fas fa-sync"></i> Test Event Gönder
        </button>
        
        <div id="test3-result" style="margin-top: 1rem;"></div>
    </div>

    <!-- Test 4: Event Tracking -->
    <div class="test-card">
        <h2><i class="fas fa-mouse-pointer"></i> 4. Event Tracking Test</h2>
        
        <p>Aşağıdaki butonlara tıklayarak event tracking'i test edin:</p>
        
        <button class="test-button" id="test-button-1" onclick="testButtonClick()">
            <i class="fas fa-click"></i> Test Button Click
        </button>
        
        <button class="test-button" onclick="testCustomEvent()">
            <i class="fas fa-star"></i> Test Custom Event
        </button>
        
        <a href="https://google.com" target="_blank" class="test-button" style="display: inline-block; text-decoration: none;">
            <i class="fas fa-external-link-alt"></i> Test Outbound Link
        </a>
        
        <div id="test4-result" class="result-box" style="display: none; margin-top: 1rem;"></div>
    </div>

    <!-- Test 5: Real-Time Reports -->
    <div class="test-card">
        <h2><i class="fas fa-clock"></i> 5. Real-Time Reports</h2>
        
        <div class="result-box">
            <strong>Google Analytics Real-Time kontrol:</strong>
            <ol style="margin-left: 1.5rem; margin-top: 1rem;">
                <li>Google Analytics'e gidin</li>
                <li>Reports > Realtime</li>
                <li>Bu sayfayı açık tutun</li>
                <li>1-2 dakika içinde real-time'da görünmelisiniz</li>
            </ol>
            
            <a href="https://analytics.google.com/analytics/web/#/realtime" target="_blank" class="test-button">
                <i class="fas fa-external-link-alt"></i> Analytics Real-Time Aç
            </a>
        </div>
    </div>

    <!-- Test 6: Checklist -->
    <div class="test-card">
        <h2><i class="fas fa-list-check"></i> 6. Kontrol Listesi</h2>
        
        <ul class="checklist" id="analytics-checklist">
            <li class="pending">Google Analytics kodu header'da mevcut</li>
            <li class="pending">Measurement ID doğru (G-XXXXXXXXXX)</li>
            <li class="pending">gtag function çalışıyor</li>
            <li class="pending">DataLayer aktif</li>
            <li class="pending">Network request gidiyor</li>
            <li class="pending">Real-time'da görünüyor</li>
            <li class="pending">Event tracking çalışıyor</li>
            <li class="pending">Cross-domain tracking aktif</li>
        </ul>
        
        <button class="test-button" onclick="runFullTest()">
            <i class="fas fa-play"></i> Tam Test Çalıştır
        </button>
    </div>

    <!-- Debugging Console -->
    <div class="test-card">
        <h2><i class="fas fa-terminal"></i> Debug Console</h2>
        
        <div id="debug-console" class="code-block" style="max-height: 300px; overflow-y: auto; font-size: 0.85rem;">
            <div style="color: #10b981;">✓ Debug console hazır...</div>
        </div>
        
        <button class="test-button" onclick="clearDebugConsole()">
            <i class="fas fa-trash"></i> Temizle
        </button>
    </div>

    <!-- Configuration Info -->
    <div class="test-card">
        <h2><i class="fas fa-info-circle"></i> Yapılandırma Bilgileri</h2>
        
        <div class="result-box">
            <strong>Mevcut Sayfa:</strong> <?php echo $current_page ?? 'unknown'; ?><br>
            <strong>Page Title:</strong> <?php echo $page_title ?? 'unknown'; ?><br>
            <strong>Current URL:</strong> <?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?><br>
            <strong>Referrer:</strong> <span id="referrer-info">-</span><br>
            <strong>User Agent:</strong> <span id="ua-info">-</span>
        </div>
        
        <script>
            document.getElementById('referrer-info').textContent = document.referrer || 'Direct';
            document.getElementById('ua-info').textContent = navigator.userAgent;
        </script>
    </div>

    <!-- Quick Fixes -->
    <div class="test-card">
        <h2><i class="fas fa-wrench"></i> Hızlı Çözümler</h2>
        
        <div class="result-box">
            <h4 style="color: #667eea; margin-bottom: 1rem;">Analytics Çalışmıyorsa:</h4>
            
            <strong>1. Measurement ID Güncelle:</strong>
            <p><code>includes/header.php</code> dosyasında <code>G-XXXXXXXXXX</code> yerine gerçek GA4 ID'nizi yazın.</p>
            
            <strong>2. GA4 Property Oluştur:</strong>
            <p>Google Analytics > Admin > Create Property</p>
            
            <strong>3. Measurement ID Al:</strong>
            <p>Admin > Data Streams > Web > Measurement ID</p>
            
            <strong>4. Test Et:</strong>
            <p>Analytics > Reports > Realtime (1-2 dakika içinde görünmeli)</p>
            
            <strong>5. Debug Mode:</strong>
            <div class="code-block">
// Console'a yapıştır:<br>
gtag('config', 'G-XXXXXXXXXX', {'debug_mode': true});
            </div>
        </div>
    </div>
</div>

<script>
    // Debug log helper
    function debugLog(message, type = 'info') {
        const console = document.getElementById('debug-console');
        const timestamp = new Date().toLocaleTimeString();
        const colors = {
            info: '#3b82f6',
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b'
        };
        
        const entry = document.createElement('div');
        entry.style.color = colors[type];
        entry.textContent = `[${timestamp}] ${message}`;
        console.appendChild(entry);
        console.scrollTop = console.scrollHeight;
    }
    
    function clearDebugConsole() {
        document.getElementById('debug-console').innerHTML = '<div style="color: #10b981;">✓ Debug console temizlendi...</div>';
    }
    
    // Test Analytics Request
    function testAnalyticsRequest() {
        debugLog('Test event gönderiliyor...', 'info');
        
        if (typeof gtag === 'function') {
            gtag('event', 'test_event', {
                'event_category': 'test',
                'event_label': 'Analytics Test Button',
                'value': 1
            });
            
            debugLog('✓ Test event başarıyla gönderildi!', 'success');
            
            const result = document.getElementById('test3-result');
            result.innerHTML = '<span class="status-badge status-success">✓ Event gönderildi!</span><br><br>Chrome DevTools > Network tab\'da "collect" veya "google-analytics" filtresi ile request\'i görebilirsiniz.';
        } else {
            debugLog('✗ gtag function bulunamadı!', 'error');
            
            const result = document.getElementById('test3-result');
            result.innerHTML = '<span class="status-badge status-error">✗ gtag tanımlı değil</span><br><br>Analytics kodu düzgün yüklenmemiş.';
        }
    }
    
    // Test Button Click Event
    function testButtonClick() {
        debugLog('Button click event triggered', 'info');
        
        if (typeof gtag === 'function') {
            gtag('event', 'button_test', {
                'event_category': 'engagement',
                'event_label': 'Test Button',
                'button_id': 'test-button-1'
            });
            debugLog('✓ Button click event gönderildi', 'success');
        }
        
        const result = document.getElementById('test4-result');
        result.style.display = 'block';
        result.innerHTML = '<span class="status-badge status-success">✓ Button click event gönderildi!</span>';
    }
    
    // Test Custom Event
    function testCustomEvent() {
        debugLog('Custom event sending...', 'info');
        
        if (typeof gtag === 'function') {
            gtag('event', 'custom_test_event', {
                'event_category': 'testing',
                'event_label': 'Custom Event Test',
                'custom_parameter': 'test_value',
                'timestamp': new Date().toISOString()
            });
            debugLog('✓ Custom event başarıyla gönderildi', 'success');
        }
    }
    
    // Run Full Test
    function runFullTest() {
        debugLog('=== TAM TEST BAŞLATILDI ===', 'info');
        
        const checklist = document.getElementById('analytics-checklist');
        const items = checklist.querySelectorAll('li');
        
        // Test 1: Analytics kod mevcut
        setTimeout(() => {
            if (document.querySelector('script[src*="googletagmanager"]')) {
                items[0].className = 'pass';
                debugLog('✓ Analytics script tag bulundu', 'success');
            } else {
                items[0].className = 'fail';
                debugLog('✗ Analytics script tag bulunamadı', 'error');
            }
        }, 100);
        
        // Test 2: Measurement ID
        setTimeout(() => {
            const scripts = document.querySelectorAll('script');
            let hasValidId = false;
            
            scripts.forEach(script => {
                if (script.textContent.includes('G-') && !script.textContent.includes('G-XXXXXXXXXX')) {
                    hasValidId = true;
                }
            });
            
            if (hasValidId) {
                items[1].className = 'pass';
                debugLog('✓ Measurement ID düzgün yapılandırılmış', 'success');
            } else {
                items[1].className = 'fail';
                debugLog('✗ Measurement ID placeholder (G-XXXXXXXXXX) değiştirilmemiş', 'error');
            }
        }, 200);
        
        // Test 3: gtag function
        setTimeout(() => {
            if (typeof gtag === 'function') {
                items[2].className = 'pass';
                debugLog('✓ gtag function çalışıyor', 'success');
            } else {
                items[2].className = 'fail';
                debugLog('✗ gtag function tanımlı değil', 'error');
            }
        }, 300);
        
        // Test 4: DataLayer
        setTimeout(() => {
            if (window.dataLayer && window.dataLayer.length > 0) {
                items[3].className = 'pass';
                debugLog(`✓ DataLayer aktif (${window.dataLayer.length} events)`, 'success');
            } else {
                items[3].className = 'fail';
                debugLog('✗ DataLayer boş veya tanımlı değil', 'error');
            }
        }, 400);
        
        // Test 5-8: Pending (manuel kontrol gerekli)
        setTimeout(() => {
            debugLog('Network request test için Chrome DevTools kullanın', 'warning');
            debugLog('Real-time test için Google Analytics\'e gidin', 'warning');
            debugLog('=== TEST TAMAMLANDI ===', 'info');
        }, 500);
    }
    
    // Page load event
    debugLog('Sayfa yüklendi', 'success');
    debugLog(`Current page: <?php echo $current_page ?? "unknown"; ?>`, 'info');
    
    // Auto-run basic test
    setTimeout(() => {
        debugLog('Otomatik test başlatılıyor...', 'info');
        
        if (typeof gtag === 'function') {
            debugLog('✓ gtag function bulundu', 'success');
            
            // Send test page view
            gtag('event', 'page_view', {
                'page_title': 'Analytics Test Page',
                'page_location': window.location.href,
                'page_path': '/analytics-test.php'
            });
            
            debugLog('✓ Test page_view event gönderildi', 'success');
        } else {
            debugLog('✗ gtag function bulunamadı - Analytics kodu yok', 'error');
        }
    }, 2000);
</script>

<?php require_once 'includes/footer.php'; ?>


