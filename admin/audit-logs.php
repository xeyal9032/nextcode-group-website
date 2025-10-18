<?php
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}
require_once 'includes/security.php';

// Admin yetkisi kontrolü
checkAdminPermission();

$pdo = getSecureDatabaseConnection();
if (!$pdo) {
    die('Database connection failed');
}

// Audit logs sayfası
$page_title = 'Audit Logs';
$page_description = 'Admin paneli denetim kayıtları';

// Filtreleme parametreleri
$category_filter = $_GET['category'] ?? '';
$level_filter = $_GET['level'] ?? '';
$user_filter = $_GET['user'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$page_num = (int)($_GET['page'] ?? 1);
$per_page = 50;

// Filtreleme sorgusu oluştur
$where_conditions = [];
$params = [];

if (!empty($category_filter)) {
    $where_conditions[] = "category = ?";
    $params[] = $category_filter;
}

if (!empty($level_filter)) {
    $where_conditions[] = "level = ?";
    $params[] = $level_filter;
}

if (!empty($user_filter)) {
    $where_conditions[] = "user_id = ?";
    $params[] = $user_filter;
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(created_at) >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(created_at) <= ?";
    $params[] = $date_to;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Toplam kayıt sayısını al
$count_sql = "SELECT COUNT(*) FROM admin_audit_logs $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_records = $stmt->fetchColumn();

// Sayfalama hesapla
$total_pages = ceil($total_records / $per_page);
$offset = ($page_num - 1) * $per_page;

// Audit logs'ları al
$sql = "
    SELECT 
        aal.*,
        au.username,
        au.email
    FROM admin_audit_logs aal
    LEFT JOIN admin_users au ON aal.user_id = au.id
    $where_clause
    ORDER BY aal.created_at DESC
    LIMIT $per_page OFFSET $offset
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$audit_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kategoriler ve seviyeler
$categories = [];
$levels = [];
$users = [];

try {
    $stmt = $pdo->query("SELECT DISTINCT category FROM admin_audit_logs ORDER BY category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $stmt = $pdo->query("SELECT DISTINCT level FROM admin_audit_logs ORDER BY level");
    $levels = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $stmt = $pdo->query("
        SELECT DISTINCT au.id, au.username, au.email 
        FROM admin_users au 
        INNER JOIN admin_audit_logs aal ON au.id = aal.user_id 
        ORDER BY au.username
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Filter data fetch error: " . $e->getMessage());
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1><i class="fas fa-clipboard-list"></i> Audit Logs</h1>
                <p>Admin paneli denetim kayıtları ve güvenlik olayları</p>
            </div>
        </div>
    </div>

    <!-- Filtreleme Formu -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-filter"></i> Filtreler</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Kategori</label>
                            <select name="category" class="form-select">
                                <option value="">Tüm Kategoriler</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" 
                                            <?php echo $category_filter === $cat ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Seviye</label>
                            <select name="level" class="form-select">
                                <option value="">Tüm Seviyeler</option>
                                <?php foreach ($levels as $level): ?>
                                    <option value="<?php echo htmlspecialchars($level); ?>" 
                                            <?php echo $level_filter === $level ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($level); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Kullanıcı</label>
                            <select name="user" class="form-select">
                                <option value="">Tüm Kullanıcılar</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>" 
                                            <?php echo $user_filter == $user['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Başlangıç Tarihi</label>
                            <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Bitiş Tarihi</label>
                            <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrele
                                </button>
                                <a href="audit-logs.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Temizle
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- İstatistikler -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo number_format($total_records); ?></h4>
                                    <p class="mb-0">Toplam Kayıt</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clipboard-list fa-2x"></i>
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
                                    <h4><?php echo count($categories); ?></h4>
                                    <p class="mb-0">Kategori</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-tags fa-2x"></i>
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
                                    <p class="mb-0">Aktif Kullanıcı</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
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
                                    <h4><?php echo $total_pages; ?></h4>
                                    <p class="mb-0">Sayfa</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-file-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Logs Tablosu -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Audit Logs</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($audit_logs)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Audit log bulunamadı</h5>
                            <p class="text-muted">Seçilen kriterlere uygun kayıt bulunmuyor.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tarih</th>
                                        <th>Kullanıcı</th>
                                        <th>Kategori</th>
                                        <th>Aksiyon</th>
                                        <th>Seviye</th>
                                        <th>Detaylar</th>
                                        <th>IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($audit_logs as $log): ?>
                                        <tr>
                                            <td><?php echo $log['id']; ?></td>
                                            <td>
                                                <small><?php echo date('d.m.Y H:i:s', strtotime($log['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <?php if ($log['username']): ?>
                                                    <strong><?php echo htmlspecialchars($log['username']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($log['email'] ?? ''); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">Sistem</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($log['category']); ?></span>
                                            </td>
                                            <td>
                                                <code><?php echo htmlspecialchars($log['action']); ?></code>
                                            </td>
                                            <td>
                                                <?php
                                                $level_class = match($log['level']) {
                                                    'ERROR' => 'danger',
                                                    'WARNING' => 'warning',
                                                    'INFO' => 'info',
                                                    'DEBUG' => 'secondary',
                                                    default => 'light'
                                                };
                                                ?>
                                                <span class="badge bg-<?php echo $level_class; ?>"><?php echo htmlspecialchars($log['level']); ?></span>
                                            </td>
                                            <td>
                                                <?php if ($log['details']): ?>
                                                    <small><?php echo htmlspecialchars($log['details']); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?php echo htmlspecialchars($log['ip_address'] ?? ''); ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Sayfalama -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Audit logs pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page_num > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page_num - 1])); ?>">
                                                <i class="fas fa-chevron-left"></i> Önceki
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $page_num - 2); $i <= min($total_pages, $page_num + 2); $i++): ?>
                                        <li class="page-item <?php echo $i === $page_num ? 'active' : ''; ?>">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page_num < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page_num + 1])); ?>">
                                                Sonraki <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/admin-ajax.js"></script>
<?php include 'includes/footer.php'; ?>