<?php
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}
require_once 'includes/security.php';

// Admin yetkisi kontrolü
checkAdminPermission('admin');

$pdo = getSecureDatabaseConnection();
if (!$pdo) {
    die('Database connection failed');
}

$page_title = 'User Roles';
$page_description = 'Kullanıcı rolleri ve yetkileri yönetimi';

$success_message = "";
$error_message = "";

// Başarılı işlem mesajları
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case '1':
            $success_message = "Rol başarıyla eklendi!";
            break;
        case '2':
            $success_message = "Rol başarıyla güncellendi!";
            break;
        case '3':
            $success_message = "Rol başarıyla silindi!";
            break;
        case '4':
            $success_message = "Kullanıcı rolü başarıyla güncellendi!";
            break;
    }
}

// Hata mesajları
if (isset($_GET['error'])) {
    $error_message = $_GET['error'];
}

// Roller tablosunu oluştur
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role_name VARCHAR(50) UNIQUE NOT NULL,
            role_key VARCHAR(50) UNIQUE NOT NULL,
            description TEXT,
            permissions JSON,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Varsayılan rolleri ekle
    $default_roles = [
        ['role_name' => 'Super Admin', 'role_key' => 'super_admin', 'description' => 'Tüm yetkilere sahip admin', 'permissions' => json_encode(['*'])],
        ['role_name' => 'Admin', 'role_key' => 'admin', 'description' => 'Genel admin yetkileri', 'permissions' => json_encode(['content', 'users', 'messages', 'portfolio', 'blog'])],
        ['role_name' => 'Editor', 'role_key' => 'editor', 'description' => 'İçerik editörü', 'permissions' => json_encode(['content', 'blog', 'portfolio'])],
        ['role_name' => 'Author', 'role_key' => 'author', 'description' => 'Blog yazarı', 'permissions' => json_encode(['blog'])],
        ['role_name' => 'Viewer', 'role_key' => 'viewer', 'description' => 'Sadece görüntüleme', 'permissions' => json_encode(['view'])],
    ];
    
    foreach ($default_roles as $role) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_roles (role_name, role_key, description, permissions) VALUES (?, ?, ?, ?)");
        $stmt->execute([$role['role_name'], $role['role_key'], $role['description'], $role['permissions']]);
    }
    
} catch (Exception $e) {
    error_log("User roles table creation failed: " . $e->getMessage());
}

// Rolleri getir
$roles = [];
$users = [];
try {
    $stmt = $pdo->query("SELECT * FROM user_roles ORDER BY role_name");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("
        SELECT au.id, au.username, au.email, au.role, ur.role_name 
        FROM admin_users au 
        LEFT JOIN user_roles ur ON au.role = ur.role_key 
        ORDER BY au.username
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("User roles fetch failed: " . $e->getMessage());
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1><i class="fas fa-user-shield"></i> User Roles</h1>
                <p>Kullanıcı rolleri ve yetkileri yönetimi</p>
            </div>
        </div>
    </div>

    <!-- Başarı/Hata Mesajları -->
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- İstatistikler -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo count($roles); ?></h4>
                            <p class="mb-0">Toplam Rol</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-shield fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo count($users); ?></h4>
                            <p class="mb-0">Toplam Kullanıcı</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo count(array_filter($roles, function($r) { return $r['is_active'] == 1; })); ?></h4>
                            <p class="mb-0">Aktif Rol</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo count(array_filter($roles, function($r) { return $r['is_active'] == 0; })); ?></h4>
                            <p class="mb-0">Pasif Rol</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roller Yönetimi -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-user-shield"></i> Roller</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                        <i class="fas fa-plus"></i> Yeni Rol Ekle
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($roles)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Henüz rol tanımlanmamış</h5>
                            <p class="text-muted">İlk rolünüzü ekleyerek başlayın.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Rol Adı</th>
                                        <th>Rol Key</th>
                                        <th>Açıklama</th>
                                        <th>Yetkiler</th>
                                        <th>Durum</th>
                                        <th>Oluşturulma</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roles as $role): ?>
                                        <tr>
                                            <td><?php echo $role['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($role['role_name']); ?></strong>
                                            </td>
                                            <td>
                                                <code><?php echo htmlspecialchars($role['role_key']); ?></code>
                                            </td>
                                            <td><?php echo htmlspecialchars($role['description'] ?? ''); ?></td>
                                            <td>
                                                <?php 
                                                $permissions = json_decode($role['permissions'], true);
                                                if (is_array($permissions)) {
                                                    foreach ($permissions as $perm) {
                                                        echo '<span class="badge bg-secondary me-1">' . htmlspecialchars($perm) . '</span>';
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $role['is_active'] ? 'success' : 'danger'; ?>">
                                                    <?php echo $role['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?php echo date('d.m.Y H:i', strtotime($role['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" onclick="editRole(<?php echo $role['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" onclick="deleteRole(<?php echo $role['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Kullanıcı Rolleri -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-users"></i> Kullanıcı Rolleri</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($users)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Henüz kullanıcı bulunmuyor</h5>
                            <p class="text-muted">Kullanıcı ekleyerek başlayın.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Kullanıcı</th>
                                        <th>E-posta</th>
                                        <th>Mevcut Rol</th>
                                        <th>Rol Değiştir</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($user['role_name'] ?? $user['role'] ?? 'Rol Yok'); ?></span>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm" onchange="updateUserRole(<?php echo $user['id']; ?>, this.value)">
                                                    <option value="">Rol Seçin</option>
                                                    <?php foreach ($roles as $role): ?>
                                                        <option value="<?php echo $role['role_key']; ?>" <?php echo $user['role'] === $role['role_key'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($role['role_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <a href="user-edit.php?id=<?php echo $user['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-edit"></i> Düzenle
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Rol Ekleme Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Yeni Rol Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="ajax-handler.php" data-ajax-submit>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_role">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rol Adı *</label>
                                <input type="text" name="role_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rol Key *</label>
                                <input type="text" name="role_key" class="form-control" required>
                                <small class="form-text text-muted">Örnek: editor, author, viewer</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yetkiler</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="content">
                                    <label class="form-check-label">İçerik Yönetimi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="blog">
                                    <label class="form-check-label">Blog Yönetimi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="portfolio">
                                    <label class="form-check-label">Portfolio Yönetimi</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="users">
                                    <label class="form-check-label">Kullanıcı Yönetimi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="messages">
                                    <label class="form-check-label">Mesaj Yönetimi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="media">
                                    <label class="form-check-label">Medya Yönetimi</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Rolü aktif olarak ayarla</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Rol Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="js/admin-ajax.js"></script>
<script>
function editRole(roleId) {
    // Rol düzenleme fonksiyonu
    window.location.href = 'role-edit.php?id=' + roleId;
}

function deleteRole(roleId) {
    if (confirm('Bu rolü silmek istediğinizden emin misiniz?')) {
        const formData = new FormData();
        formData.append('action', 'delete_role');
        formData.append('role_id', roleId);
        
        fetch('ajax-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                adminAJAX.showNotification('Rol başarıyla silindi', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                adminAJAX.showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            adminAJAX.showNotification('Silme hatası: ' + error.message, 'error');
        });
    }
}

function updateUserRole(userId, roleKey) {
    if (!roleKey) {
        adminAJAX.showNotification('Lütfen bir rol seçin', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'update_user_role');
    formData.append('user_id', userId);
    formData.append('role', roleKey);
    
    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            adminAJAX.showNotification('Kullanıcı rolü güncellendi', 'success');
        } else {
            adminAJAX.showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        adminAJAX.showNotification('Güncelleme hatası: ' + error.message, 'error');
    });
}
</script>

<?php include 'includes/footer.php'; ?>
