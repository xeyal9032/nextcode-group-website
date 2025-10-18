<?php
// Kullanıcı Yönetimi - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

$success_message = "";
$error_message = "";

// Başarılı işlem mesajları
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case '1':
            $success_message = "Kullanıcı başarıyla eklendi!";
            break;
        case '2':
            $success_message = "Kullanıcı başarıyla güncellendi!";
            break;
        case '3':
            $success_message = "Kullanıcı başarıyla silindi!";
            break;
    }
}

// Kullanıcı verilerini çek
$users = [];
$total_users = 0;
$active_users = 0;
$locked_users = 0;

$pdo = getSecureDatabaseConnection();
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, username, email, full_name, role, is_active, locked_until, last_login, created_at FROM admin_users ORDER BY created_at DESC");
        $users = $stmt->fetchAll();
        $total_users = count($users);
        $active_users = count(array_filter($users, function($u) { return $u['is_active'] ?? 0 == 1; }));
        $locked_users = count(array_filter($users, function($u) { return $u['locked_until'] ?? null && strtotime($u['locked_until'] ?? null) > time(); }));
    } catch (Exception $e) {
        $error_message = "Kullanıcı verileri alınamadı.";
        logSecurityEvent('USER_LIST_ERROR', $e->getMessage(), 'ERROR');
    }
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Yönetimi | NextCode Admin</title>
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
        .btn-warning { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .users-table { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .table-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; }
        .table-header h3 { margin: 0; font-size: 1.5em; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { background: #f8f9fa; font-weight: bold; color: #333; }
        .table tr:hover { background: #f8f9fa; }
        .user-name { font-weight: bold; color: #333; }
        .user-email { color: #667eea; font-size: 0.9em; }
        .user-role { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .role-admin { background: #dc3545; color: white; }
        .role-editor { background: #ffc107; color: #333; }
        .role-viewer { background: #6c757d; color: white; }
        .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .status-active { background: #d4edda; color: #28a745; }
        .status-inactive { background: #f8d7da; color: #dc3545; }
        .status-locked { background: #fff3cd; color: #856404; }
        .actions-cell { display: flex; gap: 5px; }
        .btn-sm { padding: 6px 12px; font-size: 0.8em; }
        .back-btn { background: #6c757d; color: white; padding: 12px 25px; border: none; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .back-btn:hover { background: #5a6268; color: white; }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 500; }
        .alert-danger { background: #fee; color: #c33; border: 1px solid #fcc; }
        .alert-success { background: #efe; color: #363; border: 1px solid #cfc; }
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
            <h1><i class="fas fa-users"></i> Kullanıcı Yönetimi</h1>
            <p>Admin kullanıcılarını görüntüleyin, düzenleyin ve yönetin.</p>
        </div>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Toplam Kullanıcı</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $active_users; ?></div>
                <div class="stat-label">Aktif Kullanıcı</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $locked_users; ?></div>
                <div class="stat-label">Kilitli Kullanıcı</div>
            </div>
        </div>
        
        <div class="actions">
            <a href="user-add.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Yeni Kullanıcı Ekle
            </a>
            <a href="user-roles.php" class="btn btn-success">
                <i class="fas fa-user-shield"></i> Rol Yönetimi
            </a>
        </div>
        
        <div class="users-table">
            <div class="table-header">
                <h3><i class="fas fa-list"></i> Admin Kullanıcıları</h3>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Kullanıcı</th>
                        <th>Rol</th>
                        <th>Durum</th>
                        <th>Son Giriş</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr data-user-id="<?php echo $user['id'] ?? 0; ?>">
                                <td>
                                    <div class="user-name"><?php echo htmlspecialchars($user['full_name'] ?? '' ?? ''); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($user['username'] ?? '' ?? ''); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($user['email'] ?? '' ?? ''); ?></div>
                                </td>
                                <td>
                                    <span class="user-role role-<?php echo $user['role'] ?? 'user'; ?>">
                                        <?php echo ucfirst($user['role'] ?? 'user'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['locked_until'] ?? null && strtotime($user['locked_until'] ?? null) > time()): ?>
                                        <span class="status-badge status-locked">Kilitli</span>
                                    <?php elseif ($user['is_active'] ?? 0): ?>
                                        <span class="status-badge status-active">Aktif</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive">Pasif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['last_login'] ?? null): ?>
                                        <?php echo date('d.m.Y H:i', strtotime($user['last_login'])); ?>
                                    <?php else: ?>
                                        <span style="color: #999;">Hiç giriş yapmamış</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo date('d.m.Y', strtotime($user['created_at'] ?? '')); ?>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="user-edit.php?id=<?php echo $user['id'] ?? 0; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['id'] ?? 0 != $_SESSION['admin_id'] ?? 0): ?>
                                        <button class="btn btn-danger btn-sm" 
                                                data-ajax-action="delete_user" 
                                                data-target-id="<?php echo $user['id'] ?? 0; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($user['locked_until'] ?? null && strtotime($user['locked_until'] ?? null) > time()): ?>
                                        <button class="btn btn-warning btn-sm" 
                                                data-ajax-action="unlock_user" 
                                                data-target-id="<?php echo $user['id'] ?? 0; ?>">
                                            <i class="fas fa-unlock"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 50px; color: #666;">
                                <i class="fas fa-users" style="font-size: 2em; margin-bottom: 10px;"></i>
                                <br>Henüz kullanıcı bulunmuyor.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>


