<?php
/**
 * Admin Panel - Main Entry Point
 * NextCode Group - Advanced Admin System
 */

define('ADMIN_ACCESS', true);

// Include configurations
require_once 'config/database.php';
require_once 'config/security.php';

// Check if user is logged in
if (!$adminSecurity->checkAuth()) {
    header('Location: login.php');
    exit();
}

// Get current user info
$current_user = [
    'id' => $_SESSION['admin_user_id'],
    'username' => $_SESSION['admin_username'],
    'role' => $_SESSION['admin_role'],
    'full_name' => $_SESSION['admin_full_name']
];

// Get dashboard statistics
$stats = [];
try {
    if ($pdo) {
        // File count
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_files");
        $stats['total_files'] = $stmt->fetch()['total'] ?? 0;
        
        // Recent activity count
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_activity_log WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stats['recent_activity'] = $stmt->fetch()['total'] ?? 0;
        
        // Active sessions
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_sessions WHERE expires_at > NOW()");
        $stats['active_sessions'] = $stmt->fetch()['total'] ?? 0;
        
        // Get recent activities
        $stmt = $pdo->prepare("
            SELECT al.*, au.username, au.full_name
            FROM admin_activity_log al
            LEFT JOIN admin_users au ON al.user_id = au.id
            ORDER BY al.created_at DESC
            LIMIT 10
        ");
        $stmt->execute();
        $recent_activities = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    error_log('Dashboard stats error: ' . $e->getMessage());
    $recent_activities = [];
}

// Log page access
$adminSecurity->logActivity($current_user['id'], 'page_access', 'Dashboard accessed');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NextCode Group</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-code"></i>
                    <span>NextCode Admin</span>
                </div>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-section">
                    <h3>Ana Menu</h3>
                    <ul>
                        <li class="active">
                            <a href="index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/file-manager.php">
                                <i class="fas fa-folder-open"></i>
                                <span>Dosya Yöneticisi</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/code-editor.php">
                                <i class="fas fa-code"></i>
                                <span>Kod Editörü</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/database.php">
                                <i class="fas fa-database"></i>
                                <span>Veritabanı</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="menu-section">
                    <h3>Site Yönetimi</h3>
                    <ul>
                        <li>
                            <a href="pages/settings.php">
                                <i class="fas fa-cog"></i>
                                <span>Site Ayarları</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/users.php">
                                <i class="fas fa-users"></i>
                                <span>Kullanıcılar</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/logs.php">
                                <i class="fas fa-list-alt"></i>
                                <span>Sistem Logları</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="menu-section">
                    <h3>Araçlar</h3>
                    <ul>
                        <li>
                            <a href="pages/backup.php">
                                <i class="fas fa-download"></i>
                                <span>Yedekleme</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/performance.php">
                                <i class="fas fa-chart-line"></i>
                                <span>Performans</span>
                            </a>
                        </li>
                        <li>
                            <a href="../index.php" target="_blank">
                                <i class="fas fa-external-link-alt"></i>
                                <span>Siteyi Görüntüle</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="top-bar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Dashboard</h1>
                </div>
                
                <div class="top-bar-right">
                    <div class="user-menu">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($current_user['full_name']); ?></span>
                            <span class="user-role"><?php echo ucfirst($current_user['role']); ?></span>
                        </div>
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="dropdown-menu">
                            <a href="pages/profile.php"><i class="fas fa-user"></i> Profil</a>
                            <a href="pages/settings.php"><i class="fas fa-cog"></i> Ayarlar</a>
                            <hr>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="content">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo number_format($stats['total_files'] ?? 0); ?></h3>
                            <p>Toplam Dosya</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-activity"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo number_format($stats['recent_activity'] ?? 0); ?></h3>
                            <p>Son 24 Saat Aktivite</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo number_format($stats['active_sessions'] ?? 0); ?></h3>
                            <p>Aktif Oturum</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Online</h3>
                            <p>Sistem Durumu</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h2>Hızlı İşlemler</h2>
                    <div class="action-grid">
                        <a href="pages/file-manager.php" class="action-card">
                            <i class="fas fa-folder-plus"></i>
                            <h3>Dosya Yükle</h3>
                            <p>Yeni dosya yükle veya mevcut dosyaları düzenle</p>
                        </a>
                        
                        <a href="pages/code-editor.php" class="action-card">
                            <i class="fas fa-code"></i>
                            <h3>Kod Düzenle</h3>
                            <p>Site dosyalarını doğrudan düzenle</p>
                        </a>
                        
                        <a href="pages/database.php" class="action-card">
                            <i class="fas fa-database"></i>
                            <h3>Veritabanı</h3>
                            <p>Veritabanı tablolarını yönet</p>
                        </a>
                        
                        <a href="pages/backup.php" class="action-card">
                            <i class="fas fa-download"></i>
                            <h3>Yedek Al</h3>
                            <p>Site ve veritabanı yedeği oluştur</p>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="recent-activity">
                    <h2>Son Aktiviteler</h2>
                    <div class="activity-list">
                        <?php if (!empty($recent_activities)): ?>
                            <?php foreach ($recent_activities as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-<?php echo getActivityIcon($activity['action']); ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h4><?php echo htmlspecialchars($activity['full_name'] ?? $activity['username'] ?? 'System'); ?></h4>
                                        <p><?php echo htmlspecialchars($activity['action']); ?> - <?php echo htmlspecialchars($activity['resource']); ?></p>
                                        <span class="activity-time"><?php echo date('d.m.Y H:i', strtotime($activity['created_at'])); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-activity">
                                <i class="fas fa-info-circle"></i>
                                <p>Henüz aktivite bulunmuyor</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/admin.js"></script>
    <script>
        // Dashboard specific scripts
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize dashboard
            console.log('Admin Dashboard loaded');
            
            // Auto-refresh stats every 30 seconds
            setInterval(function() {
                // You can implement AJAX refresh here
            }, 30000);
        });
    </script>
</body>
</html>

<?php
function getActivityIcon($action) {
    $icons = [
        'login' => 'sign-in-alt',
        'logout' => 'sign-out-alt',
        'file_upload' => 'upload',
        'file_edit' => 'edit',
        'file_delete' => 'trash',
        'database_query' => 'database',
        'settings_change' => 'cog',
        'page_access' => 'eye'
    ];
    
    return $icons[$action] ?? 'info-circle';
}
?>
