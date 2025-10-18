<?php
// Güvenli Blog Yönetimi - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

require_once "../config/database.php";

// Blog verilerini çek
$posts = [];
$total_posts = 0;

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        $stmt = $pdo->query("SELECT bp.*, bc.name as category_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id ORDER BY bp.created_at DESC");
        $posts = $stmt->fetchAll();
        $total_posts = count($posts);
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
    <title>Blog Yönetimi | NextCode Admin</title>
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
        .btn-warning { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .posts-table { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .table-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; }
        .table-header h3 { margin: 0; font-size: 1.5em; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { background: #f8f9fa; font-weight: bold; color: #333; }
        .table tr:hover { background: #f8f9fa; }
        .post-title { font-weight: bold; color: #333; }
        .post-excerpt { color: #666; font-size: 0.9em; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .post-meta { font-size: 0.8em; color: #999; }
        .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .status-published { background: #d4edda; color: #28a745; }
        .status-draft { background: #fff3cd; color: #856404; }
        .actions-cell { display: flex; gap: 5px; }
        .btn-sm { padding: 6px 12px; font-size: 0.8em; }
        .back-btn { background: #6c757d; color: white; padding: 12px 25px; border: none; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .back-btn:hover { background: #5a6268; color: white; }
        @media (max-width: 768px) {
            .table { font-size: 0.9em; }
            .table th, .table td { padding: 10px 8px; }
            .actions { flex-direction: column; }
            .actions-cell { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Dashboard'a Dön
        </a>
        
        <div class="header">
            <h1><i class="fas fa-blog"></i> Blog Yönetimi</h1>
            <p>Blog yazılarını görüntüleyin, düzenleyin ve yönetin.</p>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_posts; ?></div>
                <div class="stat-label">Toplam Yazı</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($posts, function($p) { return $p['status'] ?? 'draft' == 'published'; })); ?></div>
                <div class="stat-label">Yayınlanmış</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($posts, function($p) { return $p['status'] ?? 'draft' == 'draft'; })); ?></div>
                <div class="stat-label">Taslak</div>
            </div>
        </div>
        
        <div class="actions">
            <a href="blog-add.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Yazı Ekle
            </a>
            <a href="../blog.php" target="_blank" class="btn btn-success">
                <i class="fas fa-external-link-alt"></i> Canlı Blog Sayfası
            </a>
        </div>
        
        <div class="posts-table">
            <div class="table-header">
                <h3><i class="fas fa-list"></i> Blog Yazıları</h3>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Başlık</th>
                        <th>Kategori</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <tr data-post-id="<?php echo $post['id'] ?? 0; ?>">
                                <td>
                                    <div class="post-title"><?php echo htmlspecialchars($post['title'] ?? '' ?? ''); ?></div>
                                    <div class="post-excerpt"><?php echo htmlspecialchars(substr($post['content'] ?? '', 0, 100)) . '...'; ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($post['category_name'] ?? 'Kategori Yok'); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $post['status'] ?? 'draft' == 'published' ? 'status-published' : 'status-draft'; ?>">
                                        <?php echo $post['status'] ?? 'draft' == 'published' ? 'Yayında' : 'Taslak'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="post-meta"><?php echo date('d.m.Y', strtotime($post['created_at'] ?? '')); ?></div>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="blog-edit.php?id=<?php echo $post['id'] ?? 0; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="../blog-post.php?slug=<?php echo $post['slug'] ?? ''; ?>" target="_blank" class="btn btn-success btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm" 
                                                data-ajax-action="delete_blog_post" 
                                                data-target-id="<?php echo $post['id'] ?? 0; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 50px; color: #666;">
                                <i class="fas fa-inbox" style="font-size: 2em; margin-bottom: 10px;"></i>
                                <br>Henüz blog yazısı bulunmuyor.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>