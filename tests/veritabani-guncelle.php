<?php
/**
 * NextCode Group - Veritabanı Güncelleme (Migration) Scripti
 * 
 * Bu script veritabanını otomatik olarak günceller
 * 
 * ⚠️ ÖNEMLİ: Çalıştırmadan önce veritabanı yedeği alın!
 * 
 * KULLANIM:
 * 1. Bu dosyayı FTP'ye yükle (root klasöre)
 * 2. Browser'da aç: https://nextcode.az/veritabani-guncelle.php
 * 3. "Güncellemeyi Başlat" butonuna tıkla
 * 4. Tamamlandıktan sonra bu dosyayı SİL!
 */

// Güvenlik
define('SECURE_ACCESS', true);

// POST ile güncelleme yapılacak (yanlışlıkla çalışmasın)
$confirmation_required = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_update'])) {
    $confirmation_required = false;
}

// Database bağlantısı
require_once 'config/database.php';

// Sonuçlar array'i
$results = [];
$errors = [];
$warnings = [];
$success_count = 0;
$error_count = 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veritabanı Güncelleme - NextCode</title>
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
            max-width: 900px;
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
        
        .content {
            padding: 30px;
        }
        
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
        
        .alert-info {
            background: #ebf8ff;
            border-left: 4px solid #4299e1;
            color: #2c5282;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .btn-danger {
            background: #f56565;
            color: white;
        }
        
        .result-box {
            background: #f7fafc;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .result-box h3 {
            margin-bottom: 15px;
            color: #2d3748;
        }
        
        .result-item {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        
        .result-success {
            background: #f0fff4;
            color: #2f855a;
        }
        
        .result-error {
            background: #fff5f5;
            color: #c53030;
        }
        
        .result-info {
            background: #ebf8ff;
            color: #2c5282;
        }
        
        .checklist {
            list-style: none;
            padding: 0;
        }
        
        .checklist li {
            padding: 10px;
            margin-bottom: 10px;
            background: #f7fafc;
            border-left: 4px solid #4299e1;
            border-radius: 5px;
        }
        
        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .summary-card h3 {
            font-size: 2.5rem;
            margin-bottom: 5px;
        }
        
        .summary-card p {
            color: #718096;
            font-size: 0.9rem;
        }
        
        .code {
            background: #2d3748;
            color: #68d391;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            overflow-x: auto;
            margin: 10px 0;
        }
        
        form {
            text-align: center;
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 Veritabanı Güncelleme</h1>
            <p>NextCode Group - Otomatik Migration</p>
        </div>
        
        <div class="content">
            <?php if (!$pdo): ?>
                <div class="alert alert-danger">
                    <strong>❌ Veritabanı Bağlantısı Başarısız!</strong><br>
                    Lütfen config/database.php dosyasını kontrol edin.
                </div>
            <?php elseif ($confirmation_required): ?>
                <!-- Onay Formu -->
                <div class="alert alert-warning">
                    <strong>⚠️ DİKKAT!</strong><br>
                    Bu işlem veritabanınızda değişiklik yapacaktır. Devam etmeden önce:
                </div>
                
                <ul class="checklist">
                    <li>✅ Veritabanı yedeği aldınız mı?</li>
                    <li>✅ Production ortamında mısınız?</li>
                    <li>✅ Bu işlemi yapmak istediğinizden emin misiniz?</li>
                </ul>
                
                <div class="alert alert-info">
                    <strong>📋 Yapılacak İşlemler:</strong><br>
                    1. contact_messages tablosu kontrol edilecek<br>
                    2. admin_users tablosu kontrol edilecek<br>
                    3. Eksik sütunlar eklenecek<br>
                    4. Index'ler oluşturulacak<br>
                    5. Tablo yapıları düzeltilecek
                </div>
                
                <form method="POST">
                    <button type="submit" name="confirm_update" class="btn btn-primary">
                        ✅ Evet, Güncellemeyi Başlat
                    </button>
                </form>
                
                <div class="alert alert-danger" style="margin-top: 20px;">
                    <strong>🔒 GÜVENLİK UYARISI!</strong><br>
                    Güncelleme tamamlandıktan sonra bu dosyayı (veritabani-guncelle.php) mutlaka silin!
                </div>
                
            <?php else: ?>
                <!-- Güncelleme İşlemi -->
                <div class="alert alert-info">
                    <strong>🔄 Güncelleme Başlatıldı...</strong>
                </div>
                
                <?php
                // MIGRATION 1: contact_messages tablosu
                try {
                    $columns = $pdo->query("SHOW COLUMNS FROM contact_messages")->fetchAll(PDO::FETCH_COLUMN);
                    
                    $has_first_name = in_array('first_name', $columns);
                    $has_last_name = in_array('last_name', $columns);
                    $has_name = in_array('name', $columns);
                    $has_status = in_array('status', $columns);
                    $has_ip_address = in_array('ip_address', $columns);
                    $has_user_agent = in_array('user_agent', $columns);
                    
                    // first_name ve last_name ekle
                    if (!$has_first_name && $has_name) {
                        $pdo->exec("ALTER TABLE contact_messages CHANGE name first_name VARCHAR(255) NOT NULL");
                        $results[] = "✅ contact_messages: 'name' sütunu 'first_name' olarak yeniden adlandırıldı";
                        $success_count++;
                    } elseif (!$has_first_name) {
                        $pdo->exec("ALTER TABLE contact_messages ADD COLUMN first_name VARCHAR(255) NOT NULL AFTER id");
                        $results[] = "✅ contact_messages: 'first_name' sütunu eklendi";
                        $success_count++;
                    } else {
                        $warnings[] = "ℹ️ contact_messages: 'first_name' sütunu zaten mevcut";
                    }
                    
                    if (!$has_last_name) {
                        $pdo->exec("ALTER TABLE contact_messages ADD COLUMN last_name VARCHAR(255) NOT NULL AFTER first_name");
                        $results[] = "✅ contact_messages: 'last_name' sütunu eklendi";
                        $success_count++;
                    } else {
                        $warnings[] = "ℹ️ contact_messages: 'last_name' sütunu zaten mevcut";
                    }
                    
                    // status sütunu ekle
                    if (!$has_status) {
                        $pdo->exec("ALTER TABLE contact_messages ADD COLUMN status ENUM('unread', 'read', 'archived') DEFAULT 'unread' AFTER message");
                        $results[] = "✅ contact_messages: 'status' sütunu eklendi";
                        $success_count++;
                    } else {
                        $warnings[] = "ℹ️ contact_messages: 'status' sütunu zaten mevcut";
                    }
                    
                    // ip_address sütunu ekle
                    if (!$has_ip_address) {
                        $pdo->exec("ALTER TABLE contact_messages ADD COLUMN ip_address VARCHAR(45) AFTER status");
                        $results[] = "✅ contact_messages: 'ip_address' sütunu eklendi";
                        $success_count++;
                    } else {
                        $warnings[] = "ℹ️ contact_messages: 'ip_address' sütunu zaten mevcut";
                    }
                    
                    // user_agent sütunu ekle
                    if (!$has_user_agent) {
                        $pdo->exec("ALTER TABLE contact_messages ADD COLUMN user_agent TEXT AFTER ip_address");
                        $results[] = "✅ contact_messages: 'user_agent' sütunu eklendi";
                        $success_count++;
                    } else {
                        $warnings[] = "ℹ️ contact_messages: 'user_agent' sütunu zaten mevcut";
                    }
                    
                    // Index'leri kontrol et ve ekle
                    $indexes = $pdo->query("SHOW INDEX FROM contact_messages")->fetchAll(PDO::FETCH_ASSOC);
                    $index_names = array_column($indexes, 'Key_name');
                    
                    if (!in_array('idx_status', $index_names)) {
                        $pdo->exec("ALTER TABLE contact_messages ADD INDEX idx_status (status)");
                        $results[] = "✅ contact_messages: 'idx_status' index'i eklendi";
                        $success_count++;
                    }
                    
                    if (!in_array('idx_created_at', $index_names)) {
                        $pdo->exec("ALTER TABLE contact_messages ADD INDEX idx_created_at (created_at)");
                        $results[] = "✅ contact_messages: 'idx_created_at' index'i eklendi";
                        $success_count++;
                    }
                    
                } catch (PDOException $e) {
                    $errors[] = "❌ contact_messages hatası: " . $e->getMessage();
                    $error_count++;
                }
                
                // MIGRATION 2: admin_users tablosu
                try {
                    $columns = $pdo->query("SHOW COLUMNS FROM admin_users")->fetchAll(PDO::FETCH_COLUMN);
                    
                    $has_remember_token = in_array('remember_token', $columns);
                    $has_login_attempts = in_array('login_attempts', $columns);
                    $has_locked_until = in_array('locked_until', $columns);
                    $has_ip_address = in_array('ip_address', $columns);
                    $has_user_agent = in_array('user_agent', $columns);
                    
                    // remember_token ekle
                    if (!$has_remember_token) {
                        $pdo->exec("ALTER TABLE admin_users ADD COLUMN remember_token VARCHAR(255) NULL AFTER password_hash");
                        $results[] = "✅ admin_users: 'remember_token' sütunu eklendi";
                        $success_count++;
                        
                        // Index ekle
                        $pdo->exec("ALTER TABLE admin_users ADD INDEX idx_remember_token (remember_token)");
                        $results[] = "✅ admin_users: 'idx_remember_token' index'i eklendi";
                        $success_count++;
                    } else {
                        $warnings[] = "ℹ️ admin_users: 'remember_token' sütunu zaten mevcut";
                    }
                    
                    // login_attempts ekle
                    if (!$has_login_attempts) {
                        $pdo->exec("ALTER TABLE admin_users ADD COLUMN login_attempts INT DEFAULT 0 AFTER last_login");
                        $results[] = "✅ admin_users: 'login_attempts' sütunu eklendi";
                        $success_count++;
                    } else {
                        $warnings[] = "ℹ️ admin_users: 'login_attempts' sütunu zaten mevcut";
                    }
                    
                    // locked_until ekle
                    if (!$has_locked_until) {
                        $pdo->exec("ALTER TABLE admin_users ADD COLUMN locked_until TIMESTAMP NULL AFTER login_attempts");
                        $results[] = "✅ admin_users: 'locked_until' sütunu eklendi";
                        $success_count++;
                    } else {
                        $warnings[] = "ℹ️ admin_users: 'locked_until' sütunu zaten mevcut";
                    }
                    
                    // ip_address ekle
                    if (!$has_ip_address) {
                        $pdo->exec("ALTER TABLE admin_users ADD COLUMN ip_address VARCHAR(45) AFTER locked_until");
                        $results[] = "✅ admin_users: 'ip_address' sütunu eklendi";
                        $success_count++;
                    } else {
                        $warnings[] = "ℹ️ admin_users: 'ip_address' sütunu zaten mevcut";
                    }
                    
                    // user_agent ekle
                    if (!$has_user_agent) {
                        $pdo->exec("ALTER TABLE admin_users ADD COLUMN user_agent TEXT AFTER ip_address");
                        $results[] = "✅ admin_users: 'user_agent' sütunu eklendi";
                        $success_count++;
                    } else {
                        $warnings[] = "ℹ️ admin_users: 'user_agent' sütunu zaten mevcut";
                    }
                    
                } catch (PDOException $e) {
                    $errors[] = "❌ admin_users hatası: " . $e->getMessage();
                    $error_count++;
                }
                ?>
                
                <!-- Sonuçlar -->
                <div class="summary">
                    <div class="summary-card">
                        <h3 style="color: #48bb78;"><?php echo $success_count; ?></h3>
                        <p>Başarılı İşlem</p>
                    </div>
                    <div class="summary-card">
                        <h3 style="color: #f56565;"><?php echo $error_count; ?></h3>
                        <p>Hata</p>
                    </div>
                    <div class="summary-card">
                        <h3 style="color: #ed8936;"><?php echo count($warnings); ?></h3>
                        <p>Uyarı</p>
                    </div>
                </div>
                
                <?php if ($error_count > 0): ?>
                    <div class="result-box">
                        <h3>❌ Hatalar (<?php echo $error_count; ?>)</h3>
                        <?php foreach ($errors as $error): ?>
                            <div class="result-item result-error"><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success_count > 0): ?>
                    <div class="result-box">
                        <h3>✅ Başarılı İşlemler (<?php echo $success_count; ?>)</h3>
                        <?php foreach ($results as $result): ?>
                            <div class="result-item result-success"><?php echo $result; ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (count($warnings) > 0): ?>
                    <div class="result-box">
                        <h3>ℹ️ Uyarılar (<?php echo count($warnings); ?>)</h3>
                        <?php foreach ($warnings as $warning): ?>
                            <div class="result-item result-info"><?php echo $warning; ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_count === 0): ?>
                    <div class="alert alert-success">
                        <strong>🎉 Güncelleme Tamamlandı!</strong><br>
                        Veritabanınız başarıyla güncellendi. Artık web siteniz kodla tam uyumlu.
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>📋 Sonraki Adımlar:</strong><br>
                        1. ✅ Veritabanı güncellendi<br>
                        2. 🧪 Web sitesini test edin (contact form, admin login)<br>
                        3. 🔒 Bu dosyayı SİLİN (veritabani-guncelle.php)<br>
                        4. 🚀 Production'da kullanmaya hazır!
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <strong>⚠️ Bazı Hatalar Oluştu!</strong><br>
                        Yukarıdaki hataları kontrol edin ve gerekirse manuel olarak düzeltin.
                    </div>
                <?php endif; ?>
                
                <div class="alert alert-danger" style="margin-top: 30px;">
                    <strong>🔒 ÇOK ÖNEMLİ!</strong><br>
                    Güncelleme tamamlandı. Şimdi bu dosyayı (veritabani-guncelle.php) FTP'den SİLİN!<br>
                    Bu dosya güvenlik riski oluşturur.
                </div>
                
                <!-- Kontrol Sorguları -->
                <div class="result-box">
                    <h3>🔍 Kontrol Sorguları (phpMyAdmin'de çalıştırın)</h3>
                    <div class="code">
-- contact_messages kontrol<br>
DESCRIBE contact_messages;<br>
SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 1;<br>
<br>
-- admin_users kontrol<br>
DESCRIBE admin_users;<br>
SELECT username, remember_token, login_attempts FROM admin_users WHERE username = 'admin';<br>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

