<?php
/**
 * Simple Admin Dashboard
 * NextCode Group - Working Dashboard
 */

session_start();

// Simple auth check
if (!isset($_SESSION['simple_admin_logged_in']) || !$_SESSION['simple_admin_logged_in']) {
    header('Location: simple-login.php');
    exit();
}

$current_user = [
    'username' => $_SESSION['admin_username'] ?? 'admin',
    'role' => $_SESSION['admin_role'] ?? 'super_admin',
    'email' => $_SESSION['admin_email'] ?? 'admin@nextcodegroup.com'
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NextCode Group</title>
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
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .user-details h3 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .user-details p {
            font-size: 12px;
            color: #64748b;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #64748b;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1e293b;
        }
        
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .quick-action-card {
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .quick-action-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        
        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .action-content h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #1e293b;
        }
        
        .action-content p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }
        
        .recent-activities-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-action {
            font-size: 14px;
            font-weight: 500;
            color: #1e293b;
            margin-bottom: 2px;
        }
        
        .activity-user {
            font-size: 12px;
            color: #64748b;
        }
        
        .activity-time {
            font-size: 12px;
            color: #64748b;
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
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-stats {
                grid-template-columns: 1fr;
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
                    <i class="fas fa-code"></i>
                    <span>NextCode Admin</span>
                </div>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-section">
                    <h3>Ana Menu</h3>
                    <ul>
                        <li class="active">
                            <a href="simple-dashboard.php">
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
                            <a href="#" onclick="window.open('../', '_blank')">
                                <i class="fas fa-external-link-alt"></i>
                                <span>Siteyi Görüntüle</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="menu-section">
                    <h3>Araçlar</h3>
                    <ul>
                        <li>
                            <a href="simple-test.php">
                                <i class="fas fa-vial"></i>
                                <span>Sistem Testi</span>
                            </a>
                        </li>
                        <li>
                            <a href="reset-admin.php">
                                <i class="fas fa-user-cog"></i>
                                <span>Admin Sıfırla</span>
                            </a>
                        </li>
                        <li>
                            <a href="simple-login.php?logout=1">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Çıkış</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($current_user['username'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <h3><?php echo ucfirst($current_user['username']); ?></h3>
                        <p><?php echo ucfirst(str_replace('_', ' ', $current_user['role'])); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Dashboard Stats -->
            <div class="dashboard-stats">
                <!-- Stats will be loaded by JavaScript -->
            </div>
            
            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <div class="card">
                    <h2>Hızlı İşlemler</h2>
                    <div class="quick-actions-grid">
                        <!-- Quick actions will be loaded by JavaScript -->
                    </div>
                </div>
                
                <div class="card">
                    <h2>Son Aktiviteler</h2>
                    <div class="recent-activities-list">
                        <!-- Recent activities will be loaded by JavaScript -->
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="simple-dashboard.js"></script>
</body>
</html>
