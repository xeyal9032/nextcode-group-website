<?php
// Veritabanı Durumu Kontrol - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

$pdo = getSecureDatabaseConnection();
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veritabanı Durumu | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .status-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .status-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #e9ecef; }
        .status-item:last-child { border-bottom: none; }
        .status-label { font-weight: 600; color: #333; }
        .status-value { font-weight: 500; }
        .status-success { color: #28a745; }
        .status-warning { color: #ffc107; }
        .status-danger { color: #dc3545; }
        .status-info { color: #17a2b8; }
        .back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 8px; margin-bottom: 20px; transition: all 0.3s; }
        .back-btn:hover { background: #5a6268; transform: translateY(-2px); }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; text-decoration: none; font-weight: bold; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; margin-right: 10px; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-danger { background: #fee; color: #c33; border: 1px solid #fcc; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Dashboard'a Dön
        </a>
        
        <div class="header">
            <h1><i class="fas fa-database"></i> Veritabanı Durumu</h1>
            <p>Veritabanı bağlantısı ve tablo durumları</p>
        </div>
        
        <div class="status-container">
            <?php if ($pdo): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Veritabanı bağlantısı başarılı!
                </div>
                
                <h2><i class="fas fa-table"></i> Tablo Durumları</h2>
                
                <?php
                $tables = [
                    'site_content' => 'Site İçerikleri',
                    'portfolio_projects' => 'Portfolio Projeleri',
                    'blog_posts' => 'Blog Yazıları',
                    'contact_messages' => 'İletişim Mesajları',
                    'admin_users' => 'Admin Kullanıcıları',
                    'blog_categories' => 'Blog Kategorileri',
                    'site_settings' => 'Site Ayarları',
                    'site_images' => 'Site Görselleri'
                ];
                
                foreach ($tables as $table => $name):
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
                        $count = $stmt->fetchColumn();
                        $status_class = $count > 0 ? 'status-success' : 'status-warning';
                        $status_icon = $count > 0 ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
                    } catch (Exception $e) {
                        $count = 'Tablo bulunamadı';
                        $status_class = 'status-danger';
                        $status_icon = 'fas fa-times-circle';
                    }
                ?>
                    <div class="status-item">
                        <div class="status-label">
                            <i class="fas fa-table"></i> <?php echo htmlspecialchars($name); ?>
                        </div>
                        <div class="status-value <?php echo $status_class; ?>">
                            <i class="<?php echo $status_icon; ?>"></i> <?php echo htmlspecialchars($count); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e9ecef;">
                    <h3><i class="fas fa-info-circle"></i> Özet</h3>
                    
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) FROM site_content");
                        $content_count = $stmt->fetchColumn();
                        
                        if ($content_count == 0) {
                            echo '<div class="alert alert-danger">';
                            echo '<i class="fas fa-exclamation-triangle"></i> Site içerikleri henüz aktarılmamış!';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-success">';
                            echo '<i class="fas fa-check-circle"></i> Site içerikleri mevcut (' . $content_count . ' kayıt)';
                            echo '</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">';
                        echo '<i class="fas fa-times-circle"></i> Site content tablosu bulunamadı!';
                        echo '</div>';
                    }
                    ?>
                </div>
                
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> Veritabanı bağlantı hatası!
                </div>
                
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #dc3545;">
                    <h3><i class="fas fa-exclamation-triangle"></i> Çözüm Önerileri</h3>
                    <ul style="margin-left: 20px; margin-top: 10px;">
                        <li>XAMPP Control Panel'i açın</li>
                        <li>Apache ve MySQL servislerini başlatın</li>
                        <li>MySQL'in yeşil olduğundan emin olun</li>
                        <li>phpMyAdmin'e gidin: http://localhost/phpmyadmin</li>
                        <li>'nextcode_group' veritabanının var olduğunu kontrol edin</li>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 30px;">
                <a href="update-database.php" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i> Veritabanını Güncelle
                </a>
                <a href="content.php" class="btn btn-secondary">
                    <i class="fas fa-edit"></i> İçerik Yönetimi
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>



