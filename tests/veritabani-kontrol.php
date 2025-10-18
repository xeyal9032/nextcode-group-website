<?php
/**
 * NextCode Group - Veritabanı Uyumluluk Kontrol Scripti
 * 
 * Bu script veritabanı ve kod arasındaki uyumluluğu kontrol eder
 * 
 * KULLANIM:
 * 1. Bu dosyayı FTP'ye yükle (root klasöre)
 * 2. Browser'da aç: https://nextcode.az/veritabani-kontrol.php
 * 3. Sonuçları incele
 * 4. Test bittikten sonra dosyayı SİL (güvenlik için)
 */

// Güvenlik: Sadece localhost'tan veya belirli IP'den erişim
$allowed_ips = ['127.0.0.1', '::1'];
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', $allowed_ips)) {
    // Production'da yorumu kaldırarak erişimi kısıtlayabilirsiniz
    // die('Erişim reddedildi. Bu script sadece localhost\'tan çalıştırılabilir.');
}

// Database bağlantısı
define('SECURE_ACCESS', true);
require_once 'config/database.php';

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veritabanı Uyumluluk Kontrolü - NextCode</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .test-section {
            margin-bottom: 30px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .test-header {
            background: #f7fafc;
            padding: 15px 20px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .test-header h2 {
            font-size: 1.3rem;
            color: #2d3748;
        }
        
        .badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }
        
        .badge-success {
            background: #48bb78;
            color: white;
        }
        
        .badge-warning {
            background: #ed8936;
            color: white;
        }
        
        .badge-danger {
            background: #f56565;
            color: white;
        }
        
        .test-body {
            padding: 20px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table th {
            background: #f7fafc;
            font-weight: 600;
            color: #4a5568;
        }
        
        .table tr:hover {
            background: #f7fafc;
        }
        
        .icon {
            font-size: 1.2rem;
            margin-right: 5px;
        }
        
        .icon-success { color: #48bb78; }
        .icon-warning { color: #ed8936; }
        .icon-danger { color: #f56565; }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: #fff5f5;
            border-left: 4px solid #f56565;
            color: #c53030;
        }
        
        .alert-warning {
            background: #fffaf0;
            border-left: 4px solid #ed8936;
            color: #c05621;
        }
        
        .alert-success {
            background: #f0fff4;
            border-left: 4px solid #48bb78;
            color: #2f855a;
        }
        
        .sql-code {
            background: #2d3748;
            color: #68d391;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
            margin-top: 10px;
        }
        
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .summary-card h3 {
            font-size: 2rem;
            margin-bottom: 5px;
        }
        
        .summary-card p {
            color: #718096;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗄️ Veritabanı Uyumluluk Kontrolü</h1>
            <p>NextCode Group - Otomatik Kontrol Scripti</p>
        </div>
        
        <div class="content">
            <?php
            if (!$pdo) {
                echo '<div class="alert alert-danger">
                    <strong>❌ Veritabanı Bağlantısı Başarısız!</strong><br>
                    Lütfen config/database.php dosyasını kontrol edin.
                </div>';
                exit;
            }
            
            // Test sonuçları
            $results = [];
            $total_tests = 0;
            $passed_tests = 0;
            $failed_tests = 0;
            $warnings = 0;
            
            // TEST 1: contact_messages tablosu
            echo '<div class="test-section">';
            echo '<div class="test-header">';
            echo '<h2>TEST 1: contact_messages Tablosu</h2>';
            
            try {
                $columns = $pdo->query("SHOW COLUMNS FROM contact_messages")->fetchAll(PDO::FETCH_COLUMN);
                
                $has_first_name = in_array('first_name', $columns);
                $has_last_name = in_array('last_name', $columns);
                $has_name = in_array('name', $columns);
                $has_status = in_array('status', $columns);
                $has_ip_address = in_array('ip_address', $columns);
                $has_user_agent = in_array('user_agent', $columns);
                
                $test_passed = $has_first_name && $has_last_name && $has_status;
                
                if ($test_passed) {
                    echo '<span class="badge badge-success">✅ BAŞARILI</span>';
                    $passed_tests++;
                } else {
                    echo '<span class="badge badge-danger">❌ BAŞARISIZ</span>';
                    $failed_tests++;
                }
                $total_tests++;
                
                echo '</div>';
                echo '<div class="test-body">';
                
                echo '<table class="table">';
                echo '<tr><th>Sütun</th><th>Durum</th><th>Açıklama</th></tr>';
                echo '<tr><td>first_name</td><td>' . ($has_first_name ? '<span class="icon icon-success">✅</span> Var' : '<span class="icon icon-danger">❌</span> Yok') . '</td><td>İsim sütunu (gerekli)</td></tr>';
                echo '<tr><td>last_name</td><td>' . ($has_last_name ? '<span class="icon icon-success">✅</span> Var' : '<span class="icon icon-danger">❌</span> Yok') . '</td><td>Soyisim sütunu (gerekli)</td></tr>';
                echo '<tr><td>status</td><td>' . ($has_status ? '<span class="icon icon-success">✅</span> Var' : '<span class="icon icon-warning">⚠️</span> Yok') . '</td><td>Mesaj durumu</td></tr>';
                echo '<tr><td>ip_address</td><td>' . ($has_ip_address ? '<span class="icon icon-success">✅</span> Var' : '<span class="icon icon-warning">⚠️</span> Yok') . '</td><td>IP adresi</td></tr>';
                echo '<tr><td>user_agent</td><td>' . ($has_user_agent ? '<span class="icon icon-success">✅</span> Var' : '<span class="icon icon-warning">⚠️</span> Yok') . '</td><td>Browser bilgisi</td></tr>';
                echo '</table>';
                
                if (!$test_passed) {
                    echo '<div class="alert alert-danger" style="margin-top: 15px;">';
                    echo '<strong>🔧 Düzeltme Gerekli!</strong><br>';
                    echo 'Aşağıdaki SQL sorgusunu phpMyAdmin\'de çalıştırın:';
                    echo '<div class="sql-code">';
                    if ($has_name && !$has_first_name) {
                        echo 'ALTER TABLE contact_messages CHANGE name first_name VARCHAR(255) NOT NULL;<br>';
                        echo 'ALTER TABLE contact_messages ADD COLUMN last_name VARCHAR(255) NOT NULL AFTER first_name;';
                    } else {
                        echo 'ALTER TABLE contact_messages ADD COLUMN first_name VARCHAR(255) NOT NULL AFTER id;<br>';
                        echo 'ALTER TABLE contact_messages ADD COLUMN last_name VARCHAR(255) NOT NULL AFTER first_name;';
                    }
                    if (!$has_status) {
                        echo '<br>ALTER TABLE contact_messages ADD COLUMN status ENUM(\'unread\', \'read\', \'archived\') DEFAULT \'unread\' AFTER message;';
                    }
                    echo '</div>';
                    echo '</div>';
                }
                
                echo '</div>';
            } catch (PDOException $e) {
                echo '<span class="badge badge-danger">❌ HATA</span>';
                echo '</div>';
                echo '<div class="test-body">';
                echo '<div class="alert alert-danger">Tablo bulunamadı: ' . $e->getMessage() . '</div>';
                echo '</div>';
                $failed_tests++;
                $total_tests++;
            }
            echo '</div>';
            
            // TEST 2: admin_users tablosu
            echo '<div class="test-section">';
            echo '<div class="test-header">';
            echo '<h2>TEST 2: admin_users Tablosu</h2>';
            
            try {
                $columns = $pdo->query("SHOW COLUMNS FROM admin_users")->fetchAll(PDO::FETCH_COLUMN);
                
                $has_remember_token = in_array('remember_token', $columns);
                $has_login_attempts = in_array('login_attempts', $columns);
                $has_locked_until = in_array('locked_until', $columns);
                
                if ($has_remember_token && $has_login_attempts && $has_locked_until) {
                    echo '<span class="badge badge-success">✅ BAŞARILI</span>';
                    $passed_tests++;
                } elseif ($has_login_attempts && $has_locked_until) {
                    echo '<span class="badge badge-warning">⚠️ UYARI</span>';
                    $warnings++;
                } else {
                    echo '<span class="badge badge-danger">❌ BAŞARISIZ</span>';
                    $failed_tests++;
                }
                $total_tests++;
                
                echo '</div>';
                echo '<div class="test-body">';
                
                echo '<table class="table">';
                echo '<tr><th>Sütun</th><th>Durum</th><th>Açıklama</th></tr>';
                echo '<tr><td>remember_token</td><td>' . ($has_remember_token ? '<span class="icon icon-success">✅</span> Var' : '<span class="icon icon-danger">❌</span> Yok') . '</td><td>Beni hatırla token (gerekli)</td></tr>';
                echo '<tr><td>login_attempts</td><td>' . ($has_login_attempts ? '<span class="icon icon-success">✅</span> Var' : '<span class="icon icon-warning">⚠️</span> Yok') . '</td><td>Giriş denemeleri</td></tr>';
                echo '<tr><td>locked_until</td><td>' . ($has_locked_until ? '<span class="icon icon-success">✅</span> Var' : '<span class="icon icon-warning">⚠️</span> Yok') . '</td><td>Hesap kilidi</td></tr>';
                echo '</table>';
                
                if (!$has_remember_token) {
                    echo '<div class="alert alert-warning" style="margin-top: 15px;">';
                    echo '<strong>🔧 Düzeltme Gerekli!</strong><br>';
                    echo 'Aşağıdaki SQL sorgusunu phpMyAdmin\'de çalıştırın:';
                    echo '<div class="sql-code">';
                    echo 'ALTER TABLE admin_users ADD COLUMN remember_token VARCHAR(255) NULL AFTER password_hash;<br>';
                    echo 'ALTER TABLE admin_users ADD INDEX idx_remember_token (remember_token);';
                    echo '</div>';
                    echo '</div>';
                }
                
                echo '</div>';
            } catch (PDOException $e) {
                echo '<span class="badge badge-danger">❌ HATA</span>';
                echo '</div>';
                echo '<div class="test-body">';
                echo '<div class="alert alert-danger">Tablo bulunamadı: ' . $e->getMessage() . '</div>';
                echo '</div>';
                $failed_tests++;
                $total_tests++;
            }
            echo '</div>';
            
            // TEST 3: Diğer önemli tablolar
            $tables_to_check = [
                'blog_posts' => 'Blog yazıları',
                'portfolio_projects' => 'Portfolyo projeleri',
                'site_content' => 'Site içerikleri',
                'services' => 'Hizmetler',
                'faq' => 'SSS',
                'pricing_packages' => 'Fiyatlandırma paketleri'
            ];
            
            echo '<div class="test-section">';
            echo '<div class="test-header">';
            echo '<h2>TEST 3: Diğer Tablolar</h2>';
            echo '</div>';
            echo '<div class="test-body">';
            echo '<table class="table">';
            echo '<tr><th>Tablo</th><th>Açıklama</th><th>Durum</th></tr>';
            
            $all_tables_exist = true;
            foreach ($tables_to_check as $table => $description) {
                try {
                    $pdo->query("SELECT 1 FROM $table LIMIT 1");
                    echo '<tr><td>' . $table . '</td><td>' . $description . '</td><td><span class="icon icon-success">✅</span> Var</td></tr>';
                } catch (PDOException $e) {
                    echo '<tr><td>' . $table . '</td><td>' . $description . '</td><td><span class="icon icon-danger">❌</span> Yok</td></tr>';
                    $all_tables_exist = false;
                }
            }
            
            echo '</table>';
            
            if ($all_tables_exist) {
                $passed_tests++;
            } else {
                $failed_tests++;
            }
            $total_tests++;
            
            echo '</div>';
            echo '</div>';
            
            // ÖZET
            $success_rate = ($passed_tests / $total_tests) * 100;
            ?>
            
            <div class="summary">
                <div class="summary-card">
                    <h3 style="color: #4a5568;"><?php echo $total_tests; ?></h3>
                    <p>Toplam Test</p>
                </div>
                <div class="summary-card">
                    <h3 style="color: #48bb78;"><?php echo $passed_tests; ?></h3>
                    <p>Başarılı</p>
                </div>
                <div class="summary-card">
                    <h3 style="color: #f56565;"><?php echo $failed_tests; ?></h3>
                    <p>Başarısız</p>
                </div>
                <div class="summary-card">
                    <h3 style="color: #ed8936;"><?php echo $warnings; ?></h3>
                    <p>Uyarılar</p>
                </div>
                <div class="summary-card">
                    <h3 style="color: <?php echo $success_rate >= 80 ? '#48bb78' : ($success_rate >= 50 ? '#ed8936' : '#f56565'); ?>">
                        <?php echo round($success_rate); ?>%
                    </h3>
                    <p>Başarı Oranı</p>
                </div>
            </div>
            
            <?php if ($failed_tests > 0): ?>
            <div class="alert alert-danger">
                <strong>⚠️ Aksiyon Gerekli!</strong><br>
                <?php echo $failed_tests; ?> test başarısız oldu. Yukarıdaki SQL sorgularını phpMyAdmin'de çalıştırarak düzeltmeleri yapın.
            </div>
            <?php elseif ($warnings > 0): ?>
            <div class="alert alert-warning">
                <strong>✅ Çoğunlukla Başarılı!</strong><br>
                Bazı opsiyonel sütunlar eksik. Yukarıdaki önerileri uygulayabilirsiniz.
            </div>
            <?php else: ?>
            <div class="alert alert-success">
                <strong>🎉 Mükemmel!</strong><br>
                Tüm testler başarıyla geçti. Veritabanınız kodla tam uyumlu.
            </div>
            <?php endif; ?>
            
            <div class="alert alert-danger" style="margin-top: 20px;">
                <strong>🔒 GÜVENLİK UYARISI!</strong><br>
                Test tamamlandıktan sonra bu dosyayı (veritabani-kontrol.php) mutlaka silin!
            </div>
        </div>
    </div>
</body>
</html>

