<?php
// Admin Panel Kurulum Scripti - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

$setup_complete = false;
$error_message = "";
$success_message = "";

// Kurulum durumu kontrolü
if (file_exists('../.admin_setup_complete')) {
    $setup_complete = true;
}

// CSRF Token oluştur
$csrf_token = generateCSRFToken();

// Form işleme
if ($_SERVER["REQUEST_METHOD"] === "POST" && !$setup_complete) {
    $submitted_token = $_POST["csrf_token"] ?? "";
    if (!verifyCSRFToken($submitted_token)) {
        $error_message = "Güvenlik hatası. Lütfen sayfayı yenileyin.";
    } else {
        try {
            require_once "../config/database.php";
            $db = new Database();
            $pdo = $db->getConnection();
            
            if (!$pdo) {
                throw new Exception("Veritabanı bağlantısı kurulamadı.");
            }
            
            // Admin tablolarını oluştur
            $sql_file = '../database/create/admin_tables.sql';
            if (file_exists($sql_file)) {
                $sql = file_get_contents($sql_file);
                
                // SQL dosyasını çalıştır
                $statements = explode(';', $sql);
                foreach ($statements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        $pdo->exec($statement);
                    }
                }
            }
            
            // Kurulum tamamlandı dosyası oluştur
            file_put_contents('../.admin_setup_complete', date('Y-m-d H:i:s'));
            
            $success_message = "Admin paneli başarıyla kuruldu! Artık admin paneline giriş yapabilirsiniz.";
            $setup_complete = true;
            
            logSecurityEvent('ADMIN_SETUP_COMPLETED', 'Admin panel setup completed successfully', 'INFO');
            
        } catch (Exception $e) {
            $error_message = "Kurulum sırasında hata oluştu: " . $e->getMessage();
            logSecurityEvent('ADMIN_SETUP_ERROR', $e->getMessage(), 'ERROR');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Kurulum | NextCode</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .setup-container { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); text-align: center; }
        .setup-header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .setup-header p { color: #666; font-size: 1.2em; margin-bottom: 30px; }
        .alert { padding: 20px; border-radius: 10px; margin-bottom: 30px; font-weight: 500; }
        .alert-danger { background: #fee; color: #c33; border: 1px solid #fcc; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .btn { padding: 15px 30px; border: none; border-radius: 10px; text-decoration: none; font-weight: bold; transition: all 0.3s; display: inline-flex; align-items: center; gap: 10px; cursor: pointer; margin: 10px; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .setup-info { background: #f8f9fa; padding: 30px; border-radius: 15px; margin-bottom: 30px; text-align: left; }
        .setup-info h3 { color: #333; margin-bottom: 15px; }
        .setup-info ul { margin-left: 20px; color: #666; }
        .setup-info li { margin-bottom: 8px; }
        .credentials { background: #e7f3ff; padding: 20px; border-radius: 10px; margin-top: 20px; border-left: 4px solid #667eea; }
        .credentials h4 { color: #333; margin-bottom: 10px; }
        .credentials p { color: #666; margin-bottom: 5px; }
        @media (max-width: 768px) { 
            .setup-container { padding: 30px 20px; }
            .setup-header h1 { font-size: 2em; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="setup-container">
            <div class="setup-header">
                <h1><i class="fas fa-cogs"></i> Admin Panel Kurulum</h1>
                <p>NextCode Group Admin Panelini kurmak için aşağıdaki adımları takip edin</p>
            </div>
            
            <?php if ($setup_complete): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
                
                <div class="credentials">
                    <h4><i class="fas fa-key"></i> Varsayılan Giriş Bilgileri</h4>
                    <p><strong>Kullanıcı Adı:</strong> admin</p>
                    <p><strong>Şifre:</strong> admin123</p>
                    <p><strong>E-posta:</strong> admin@nextcode.az</p>
                    <p style="color: #dc3545; font-weight: bold; margin-top: 10px;">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Güvenlik için ilk girişten sonra şifrenizi değiştirin!
                    </p>
                </div>
                
                <a href="login.php" class="btn btn-success">
                    <i class="fas fa-sign-in-alt"></i> Admin Paneline Giriş Yap
                </a>
                
            <?php else: ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <div class="setup-info">
                    <h3><i class="fas fa-info-circle"></i> Kurulum İşlemi</h3>
                    <p>Bu kurulum işlemi şunları yapacaktır:</p>
                    <ul>
                        <li>Admin kullanıcı tablosunu oluşturacak</li>
                        <li>Blog kategorileri ve yazıları tablolarını kuracak</li>
                        <li>Portfolio projeleri tablosunu oluşturacak</li>
                        <li>İletişim mesajları tablosunu kuracak</li>
                        <li>Site ayarları tablosunu oluşturacak</li>
                        <li>Audit log tablosunu kuracak</li>
                        <li>Varsayılan admin kullanıcısını ekleyecek</li>
                        <li>Örnek verileri yükleyecek</li>
                    </ul>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-rocket"></i> Kurulumu Başlat
                    </button>
                </form>
                
                <div class="credentials">
                    <h4><i class="fas fa-info-circle"></i> Kurulum Sonrası</h4>
                    <p>Kurulum tamamlandıktan sonra varsayılan admin kullanıcısı ile giriş yapabilirsiniz.</p>
                    <p>Güvenlik için ilk girişten sonra şifrenizi mutlaka değiştirin.</p>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e1e5e9;">
                <a href="../index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Ana Sayfaya Dön
                </a>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const submitBtn = form ? form.querySelector("button[type='submit']") : null;
            
            if (submitBtn) {
                form.addEventListener("submit", function() {
                    submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Kurulum Yapılıyor...";
                    submitBtn.disabled = true;
                });
            }
        });
    </script>
</body>
</html>