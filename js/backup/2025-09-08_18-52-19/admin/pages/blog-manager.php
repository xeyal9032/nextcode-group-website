<?php
/**
 * Blog Manager
 * NextCode Group - Blog Management System
 */

session_start();

// Simple auth check
if (!isset($_SESSION['simple_admin_logged_in']) || !$_SESSION['simple_admin_logged_in']) {
    header('Location: simple-login.php');
    exit();
}

$current_user = [
    'username' => $_SESSION['admin_username'] ?? 'admin',
    'role' => $_SESSION['admin_role'] ?? 'super_admin'
];

$message = '';
$error = '';

try {
    $pdo = new PDO(
        'mysql:host=gtorg.mysql.tools;dbname=gtorg_nextcode;charset=utf8mb4',
        'gtorg_nextcode',
        ';849#dVEyg',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Handle form submissions
    if ($_POST) {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add_post':
                $title = $_POST['title'] ?? '';
                $content = $_POST['content'] ?? '';
                $excerpt = $_POST['excerpt'] ?? '';
                $category_id = $_POST['category_id'] ?? 1;
                $status = $_POST['status'] ?? 'draft';
                
                if ($title && $content) {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO blog_posts (title, slug, content, excerpt, category_id, status, published_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;
                    
                    if ($stmt->execute([$title, $slug, $content, $excerpt, $category_id, $status, $published_at])) {
                        $message = 'Blog yazısı başarıyla eklendi!';
                    } else {
                        $error = 'Blog yazısı eklenirken hata oluştu.';
                    }
                } else {
                    $error = 'Başlık ve içerik alanları zorunludur.';
                }
                break;
                
            case 'update_post':
                $id = $_POST['id'] ?? 0;
                $title = $_POST['title'] ?? '';
                $content = $_POST['content'] ?? '';
                $excerpt = $_POST['excerpt'] ?? '';
                $status = $_POST['status'] ?? 'draft';
                
                if ($id && $title && $content) {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
                    
                    $stmt = $pdo->prepare("
                        UPDATE blog_posts 
                        SET title = ?, slug = ?, content = ?, excerpt = ?, status = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    
                    if ($stmt->execute([$title, $slug, $content, $excerpt, $status, $id])) {
                        $message = 'Blog yazısı başarıyla güncellendi!';
                    } else {
                        $error = 'Blog yazısı güncellenirken hata oluştu.';
                    }
                } else {
                    $error = 'Geçersiz veri.';
                }
                break;
                
            case 'delete_post':
                $id = $_POST['id'] ?? 0;
                
                if ($id) {
                    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
                    if ($stmt->execute([$id])) {
                        $message = 'Blog yazısı başarıyla silindi!';
                    } else {
                        $error = 'Blog yazısı silinirken hata oluştu.';
                    }
                }
                break;
        }
    }
    
    // Get blog posts
    $stmt = $pdo->query("
        SELECT bp.*, bc.name as category_name 
        FROM blog_posts bp 
        LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
        ORDER BY bp.created_at DESC
    ");
    $blog_posts = $stmt->fetchAll();
    
    // Get categories
    $stmt = $pdo->query("SELECT * FROM blog_categories ORDER BY name");
    $categories = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = 'Veritabanı hatası: ' . $e->getMessage();
    $blog_posts = [];
    $categories = [];
}

// Get action and ID from URL
$action = $_GET['action'] ?? 'list';
$edit_id = $_GET['id'] ?? 0;
$edit_post = null;

if ($edit_id && $action === 'edit') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_post = $stmt->fetch();
    } catch (Exception $e) {
        $error = 'Düzenlenecek yazı bulunamadı.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Yönetimi - NextCode Group</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #334155;
        }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 20px;
            font-weight: 700;
        }
        
        .logo i {
            margin-right: 12px;
            font-size: 24px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-section h3 {
            padding: 0 20px 10px;
            font-size: 12px;
            text-transform: uppercase;
            opacity: 0.7;
            letter-spacing: 1px;
        }
        
        .menu-section ul {
            list-style: none;
            margin-bottom: 30px;
        }
        
        .menu-section li {
            margin: 2px 0;
        }
        
        .menu-section a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .menu-section a:hover,
        .menu-section li.active a {
            background: rgba(255,255,255,0.1);
            color: white;
            padding-left: 30px;
        }
        
        .menu-section a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 20px;
        }
        
        .header {
            background: white;
            border-radius: 12px;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #1e293b;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group textarea {
            min-height: 200px;
            resize: vertical;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
        }
        
        .table tr:hover {
            background: #f8fafc;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-published {
            background: #d4edda;
            color: #155724;
        }
        
        .status-draft {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-archived {
            background: #f8d7da;
            color: #721c24;
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .actions .btn {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .table {
                font-size: 14px;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-blog"></i>
                    <span>Blog Yönetimi</span>
                </div>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-section">
                    <h3>Ana Menu</h3>
                    <ul>
                        <li>
                            <a href="web-admin.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="pages/blog-manager.php">
                                <i class="fas fa-blog"></i>
                                <span>Blog Yönetimi</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/portfolio-manager.php">
                                <i class="fas fa-briefcase"></i>
                                <span>Portfolio</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/contact-manager.php">
                                <i class="fas fa-envelope"></i>
                                <span>İletişim</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="menu-section">
                    <h3>Blog İşlemleri</h3>
                    <ul>
                        <li>
                            <a href="pages/blog-manager.php?action=add">
                                <i class="fas fa-plus"></i>
                                <span>Yeni Yazı</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/blog-manager.php">
                                <i class="fas fa-list"></i>
                                <span>Tüm Yazılar</span>
                            </a>
                        </li>
                        <li>
                            <a href="../blog.php" target="_blank">
                                <i class="fas fa-external-link-alt"></i>
                                <span>Blog Sayfası</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Blog Yönetimi</h1>
                <a href="pages/blog-manager.php?action=add" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Yeni Blog Yazısı
                </a>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($action === 'add' || $action === 'edit'): ?>
                <!-- Add/Edit Form -->
                <div class="card">
                    <h2><?php echo $action === 'edit' ? 'Blog Yazısını Düzenle' : 'Yeni Blog Yazısı'; ?></h2>
                    
                    <form method="POST" action="pages/blog-manager.php">
                        <input type="hidden" name="action" value="<?php echo $action === 'edit' ? 'update_post' : 'add_post'; ?>">
                        <?php if ($edit_post): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_post['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="title">Başlık *</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit_post['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="excerpt">Özet</label>
                            <textarea id="excerpt" name="excerpt" rows="3"><?php echo htmlspecialchars($edit_post['excerpt'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">İçerik *</label>
                            <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($edit_post['content'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Kategori</label>
                            <select id="category_id" name="category_id">
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                        <?php echo ($edit_post['category_id'] ?? 1) == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Durum</label>
                            <select id="status" name="status">
                                <option value="draft" <?php echo ($edit_post['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Taslak</option>
                                <option value="published" <?php echo ($edit_post['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Yayınlandı</option>
                                <option value="archived" <?php echo ($edit_post['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Arşivlendi</option>
                            </select>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i>
                                <?php echo $action === 'edit' ? 'Güncelle' : 'Kaydet'; ?>
                            </button>
                            <a href="pages/blog-manager.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                İptal
                            </a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Blog Posts List -->
                <div class="card">
                    <h2>Blog Yazıları</h2>
                    
                    <?php if (empty($blog_posts)): ?>
                        <p>Henüz blog yazısı bulunmuyor.</p>
                    <?php else: ?>
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
                                <?php foreach ($blog_posts as $post): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                            <?php if ($post['excerpt']): ?>
                                                <br><small style="color: #6b7280;"><?php echo htmlspecialchars(substr($post['excerpt'], 0, 100)) . '...'; ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($post['category_name'] ?? 'Kategori Yok'); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $post['status']; ?>">
                                                <?php 
                                                $status_labels = [
                                                    'draft' => 'Taslak',
                                                    'published' => 'Yayınlandı',
                                                    'archived' => 'Arşivlendi'
                                                ];
                                                echo $status_labels[$post['status']] ?? $post['status'];
                                                ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?></td>
                                        <td>
                                            <div class="actions">
                                                <a href="pages/blog-manager.php?action=edit&id=<?php echo $post['id']; ?>" class="btn btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="../blog-detail.php?slug=<?php echo $post['slug']; ?>" target="_blank" class="btn btn-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Bu yazıyı silmek istediğinizden emin misiniz?')">
                                                    <input type="hidden" name="action" value="delete_post">
                                                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
