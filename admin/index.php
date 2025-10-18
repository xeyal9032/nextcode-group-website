<?php
// Güvenli Admin Dashboard - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

// Veritabanı bağlantısı - Cached
require_once "../config/admin-cache.php";

try {
    $stats = AdminCachedData::getDashboardStats();
} catch (Exception $e) {
    // Hata durumunda varsayılan değerler
    $stats = array(
        "total_projects" => 0,
        "total_posts" => 0,
        "total_messages" => 0,
        "total_users" => 0,
        "unread_messages" => 0,
        "recent_posts" => 0
    );
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | NextCode</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="js/admin-ajax.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .admin-header { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); text-align: center; }
        .admin-header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .admin-header p { color: #666; font-size: 1.2em; margin-bottom: 20px; }
        .logout-btn { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 12px 25px; border: none; border-radius: 10px; text-decoration: none; font-weight: bold; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .logout-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 30px; border-radius: 20px; text-align: center; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); transition: transform 0.3s; border: 1px solid rgba(255, 255, 255, 0.2); }
        .stat-card:hover { transform: translateY(-10px); }
        .stat-number { font-size: 3em; font-weight: bold; color: #667eea; margin-bottom: 10px; }
        .stat-label { font-size: 1.2em; color: #333; font-weight: 600; }
        .quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px; }
        .action-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 30px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); transition: transform 0.3s; border: 1px solid rgba(255, 255, 255, 0.2); }
        .action-card:hover { transform: translateY(-5px); }
        .action-card h3 { color: #333; margin-bottom: 15px; font-size: 1.5em; display: flex; align-items: center; gap: 10px; }
        .action-card p { color: #666; margin-bottom: 20px; line-height: 1.6; }
        .action-links { display: flex; gap: 10px; flex-wrap: wrap; }
        .action-links a { padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 10px; font-size: 0.9em; font-weight: 600; transition: all 0.3s; }
        .action-links a:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3); }
        .footer { text-align: center; margin-top: 40px; padding: 30px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); }
        @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } .quick-actions { grid-template-columns: 1fr; } .admin-header h1 { font-size: 2em; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <p>Hoş geldiniz, <?php echo htmlspecialchars($_SESSION["admin_username"] ?? "Admin"); ?>!</p>
            <p><small>Son giriş: <?php echo date("Y-m-d H:i:s"); ?></small></p>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
            </a>
        </div>

        <div class="stats-grid">
                   <div class="stat-card">
                       <div class="stat-number" data-stat="projects"><?php echo $stats["total_projects"]; ?></div>
                       <div class="stat-label"><i class="fas fa-briefcase"></i> Portfolio Projeleri</div>
                   </div>
                   <div class="stat-card">
                       <div class="stat-number" data-stat="posts"><?php echo $stats["total_posts"]; ?></div>
                       <div class="stat-label"><i class="fas fa-blog"></i> Blog Yazıları</div>
                   </div>
                   <div class="stat-card">
                       <div class="stat-number" data-stat="messages"><?php echo $stats["total_messages"]; ?></div>
                       <div class="stat-label"><i class="fas fa-envelope"></i> Mesajlar</div>
                   </div>
                   <div class="stat-card">
                       <div class="stat-number" data-stat="users"><?php echo $stats["total_users"]; ?></div>
                       <div class="stat-label"><i class="fas fa-users"></i> Admin Kullanıcıları</div>
                   </div>
        </div>

        <div class="quick-actions">
            <div class="action-card">
                <h3><i class="fas fa-briefcase"></i> Portfolio Yönetimi</h3>
                <p>Portfolio projelerini, case studyleri ve başarı hikayelerini yönetin.</p>
                <div class="action-links">
                    <a href="portfolio.php"><i class="fas fa-list"></i> Projeleri Görüntüle</a>
                    <a href="#portfolio/add"><i class="fas fa-plus"></i> Yeni Proje</a>
                    <a href="../portfolio.php" target="_blank"><i class="fas fa-external-link-alt"></i> Canlı Sayfa</a>
                </div>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-blog"></i> Blog Yönetimi</h3>
                <p>Blog yazılarını, kategorileri ve yorumları yönetin.</p>
                <div class="action-links">
                    <a href="blog.php"><i class="fas fa-list"></i> Yazıları Görüntüle</a>
                    <a href="blog-add.php"><i class="fas fa-plus"></i> Yeni Yazı</a>
                    <a href="../blog.php" target="_blank"><i class="fas fa-external-link-alt"></i> Canlı Blog</a>
                </div>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-envelope"></i> Mesaj Yönetimi</h3>
                <p>İletişim formu mesajlarını görüntüleyin ve yanıtlayın.</p>
                <div class="action-links">
                    <a href="messages.php"><i class="fas fa-inbox"></i> Mesajları Görüntüle</a>
                    <a href="#messages/unread"><i class="fas fa-envelope"></i> Okunmamış</a>
                    <a href="../contact.php" target="_blank"><i class="fas fa-external-link-alt"></i> İletişim Sayfası</a>
                </div>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-file-alt"></i> İçerik Yönetimi</h3>
                <p>Site içeriklerini, sayfa metinlerini ve genel ayarları yönetin.</p>
                <div class="action-links">
                    <a href="content.php"><i class="fas fa-globe"></i> Site İçerik Yönetimi</a>
                    <a href="web-content-scanner.php"><i class="fas fa-search"></i> Web İçerik Tarayıcısı</a>
                    <a href="content-editor.php"><i class="fas fa-edit"></i> İçerik Düzenleyici</a>
                    <a href="#pages"><i class="fas fa-file"></i> Sayfaları Yönet</a>
                    <a href="#settings"><i class="fas fa-cog"></i> Ayarlar</a>
                </div>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-images"></i> Medya Yönetimi</h3>
                <p>Fotoğrafları, videoları ve diğer medya dosyalarını yönetin.</p>
                <div class="action-links">
                    <a href="media.php"><i class="fas fa-images"></i> Medya Kütüphanesi</a>
                    <a href="media.php#upload"><i class="fas fa-upload"></i> Dosya Yükle</a>
                    <a href="media.php#gallery"><i class="fas fa-th"></i> Galeri</a>
                </div>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-users"></i> Kullanıcı Yönetimi</h3>
                <p>Admin kullanıcılarını yönetin ve yetkilerini düzenleyin.</p>
                <div class="action-links">
                    <a href="users.php"><i class="fas fa-users"></i> Kullanıcıları Görüntüle</a>
                    <a href="user-add.php"><i class="fas fa-user-plus"></i> Yeni Kullanıcı</a>
                    <a href="user-roles.php"><i class="fas fa-user-shield"></i> Roller</a>
                </div>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-shield-alt"></i> Güvenlik & Logs</h3>
                <p>Sistem güvenliğini ve aktivite loglarını yönetin.</p>
                <div class="action-links">
                    <a href="audit-logs.php"><i class="fas fa-clipboard-list"></i> Audit Logs</a>
                    <a href="update-database.php"><i class="fas fa-database"></i> Veritabanı Güncelle</a>
                    <a href="database-status.php"><i class="fas fa-chart-bar"></i> Veritabanı Durumu</a>
                    <a href="test-404.php"><i class="fas fa-bug"></i> 404 Test</a>
                    <a href="#security"><i class="fas fa-lock"></i> Güvenlik Ayarları</a>
                    <a href="#backup"><i class="fas fa-database"></i> Yedekleme</a>
                </div>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-memory"></i> Cache Yönetimi</h3>
                <p>Cache dosyalarını yönetin ve sistem performansını optimize edin.</p>
                <div class="action-links">
                    <a href="cache-management.php"><i class="fas fa-memory"></i> Cache Yönetimi</a>
                    <a href="../clear-cache.php" target="_blank"><i class="fas fa-trash"></i> Cache Temizle</a>
                    <a href="cache-management.php#stats"><i class="fas fa-chart-pie"></i> Cache İstatistikleri</a>
                </div>
            </div>
        </div>

        <div class="footer">
            <h3><i class="fas fa-rocket"></i> NextCode Admin Panel</h3>
            <p>Modern ve kullanıcı dostu admin paneli ile web sitenizi kolayca yönetin.</p>
            <p><small>Versiyon 1.0 | Son güncelleme: <?php echo date("Y-m-d"); ?></small></p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const cards = document.querySelectorAll(".stat-card, .action-card");
            cards.forEach(card => {
                card.addEventListener("mouseenter", function() { this.style.transform = "translateY(-10px)"; });
                card.addEventListener("mouseleave", function() { this.style.transform = "translateY(0)"; });
            });
        });
    </script>
</body>
</html>