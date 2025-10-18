<?php
// Güvenli Portfolio Yönetimi - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

require_once "../config/database.php";

// Portfolio verilerini çek
$projects = [];
$total_projects = 0;

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        $stmt = $pdo->query("SELECT id, title, description, image_url, category, status, created_at, updated_at FROM portfolio_projects ORDER BY id DESC");
        $projects = $stmt->fetchAll();
        $total_projects = count($projects);
    }
} catch (Exception $e) {
    $error_message = "Veritabanı hatası: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Yönetimi | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="js/admin-ajax.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 15px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .stat-number { font-size: 2.5em; font-weight: bold; color: #667eea; margin-bottom: 10px; }
        .stat-label { color: #666; font-size: 1.1em; }
        .actions { display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap; }
        .btn { padding: 12px 25px; border: none; border-radius: 10px; text-decoration: none; font-weight: bold; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; }
        .btn-danger { background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .projects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
        .project-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .project-card:hover { transform: translateY(-5px); }
        .project-image { width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3em; }
        .project-content { padding: 20px; }
        .project-title { font-size: 1.3em; font-weight: bold; color: #333; margin-bottom: 10px; }
        .project-description { color: #666; margin-bottom: 15px; line-height: 1.6; }
        .project-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .project-category { background: #e7f3ff; color: #667eea; padding: 5px 12px; border-radius: 20px; font-size: 0.9em; }
        .project-status { padding: 5px 12px; border-radius: 20px; font-size: 0.9em; font-weight: bold; }
        .status-active { background: #d4edda; color: #28a745; }
        .status-inactive { background: #f8d7da; color: #dc3545; }
        .project-actions { display: flex; gap: 10px; }
        .btn-sm { padding: 8px 15px; font-size: 0.9em; }
        .back-btn { background: #6c757d; color: white; padding: 12px 25px; border: none; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .back-btn:hover { background: #5a6268; color: white; }
        @media (max-width: 768px) {
            .projects-grid { grid-template-columns: 1fr; }
            .actions { flex-direction: column; }
            .project-meta { flex-direction: column; gap: 10px; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Dashboard'a Dön
        </a>
        
        <div class="header">
            <h1><i class="fas fa-briefcase"></i> Portfolio Yönetimi</h1>
            <p>Portfolio projelerini görüntüleyin, düzenleyin ve yönetin.</p>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_projects; ?></div>
                <div class="stat-label">Toplam Proje</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($projects, function($p) { return isset($p['is_active']) && $p['is_active'] == 1; })); ?></div>
                <div class="stat-label">Aktif Proje</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($projects, function($p) { return isset($p['is_active']) && $p['is_active'] == 0; })); ?></div>
                <div class="stat-label">Pasif Proje</div>
            </div>
        </div>
        
        <div class="actions">
            <a href="portfolio-add.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Proje Ekle
            </a>
            <a href="../portfolio.php" target="_blank" class="btn btn-success">
                <i class="fas fa-external-link-alt"></i> Canlı Sayfayı Görüntüle
            </a>
        </div>
        
        <div class="projects-grid">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <div class="project-card" data-project-id="<?php echo $project['id'] ?? 0; ?>">
                        <div class="project-image">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title"><?php echo htmlspecialchars($project['title'] ?? '' ?? ''); ?></h3>
                            <p class="project-description"><?php echo htmlspecialchars(substr($project['description'] ?? '', 0, 100)) . '...'; ?></p>
                            <div class="project-meta">
                                <span class="project-category"><?php echo htmlspecialchars($project['category'] ?? 'Kategori Yok'); ?></span>
                                <span class="project-status <?php echo (isset($project['is_active']) && $project['is_active']) ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo (isset($project['is_active']) && $project['is_active']) ? 'Aktif' : 'Pasif'; ?>
                                </span>
                            </div>
                            <div class="project-actions">
                                <a href="portfolio-edit.php?id=<?php echo $project['id'] ?? 0; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Düzenle
                                </a>
                                <a href="../portfolio-detail.php?id=<?php echo $project['id'] ?? 0; ?>" target="_blank" class="btn btn-success btn-sm">
                                    <i class="fas fa-eye"></i> Görüntüle
                                </a>
                                <button class="btn btn-danger btn-sm" 
                                        data-ajax-action="delete_portfolio" 
                                        data-target-id="<?php echo $project['id'] ?? 0; ?>">
                                    <i class="fas fa-trash"></i> Sil
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <i class="fas fa-inbox" style="font-size: 4em; color: #ccc; margin-bottom: 20px;"></i>
                    <h3 style="color: #666; margin-bottom: 10px;">Henüz Proje Bulunmuyor</h3>
                    <p style="color: #999;">İlk projenizi eklemek için "Yeni Proje Ekle" butonunu kullanın.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>