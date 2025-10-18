<?php
/**
 * Web Project Admin Panel
 * NextCode Group - Complete Web Management System
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

// Get web project statistics
$web_stats = [
    'total_pages' => 0,
    'total_blog_posts' => 0,
    'total_portfolio_items' => 0,
    'total_contact_messages' => 0,
    'total_services' => 0,
    'total_faq_items' => 0
];

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
    
    // Get statistics
    $tables = [
        'pages' => 'total_pages',
        'blog_posts' => 'total_blog_posts',
        'portfolio_projects' => 'total_portfolio_items',
        'contact_messages' => 'total_contact_messages',
        'services' => 'total_services',
        'faq' => 'total_faq_items'
    ];
    
    foreach ($tables as $table => $stat_key) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM $table");
            $stmt->execute();
            $result = $stmt->fetch();
            $web_stats[$stat_key] = $result['count'] ?? 0;
        } catch (Exception $e) {
            $web_stats[$stat_key] = 0;
        }
    }
    
} catch (Exception $e) {
    // Fallback stats
    $web_stats = [
        'total_pages' => 8,
        'total_blog_posts' => 5,
        'total_portfolio_items' => 15,
        'total_contact_messages' => 12,
        'total_services' => 4,
        'total_faq_items' => 6
    ];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Project Admin - NextCode Group</title>
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
            width: 300px;
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
            margin-left: 300px;
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
        
        .web-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        
        .web-management-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .management-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .management-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .management-card h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .management-card h3 i {
            color: #667eea;
        }
        
        .management-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            color: #334155;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .action-btn i {
            width: 16px;
            text-align: center;
        }
        
        .quick-access {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .quick-access h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1e293b;
        }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .quick-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
            text-decoration: none;
            color: #334155;
            transition: all 0.3s ease;
        }
        
        .quick-link:hover {
            background: #667eea;
            color: white;
        }
        
        .quick-link i {
            font-size: 18px;
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
            
            .web-stats {
                grid-template-columns: 1fr;
            }
            
            .web-management-grid {
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
                    <i class="fas fa-globe"></i>
                    <span>Web Project Admin</span>
                </div>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-section">
                    <h3>Ana Menu</h3>
                    <ul>
                        <li class="active">
                            <a href="web-admin.php">
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
                    <h3>Web Sayfaları</h3>
                    <ul>
                        <li>
                            <a href="../index.php" target="_blank">
                                <i class="fas fa-home"></i>
                                <span>Ana Sayfa</span>
                            </a>
                        </li>
                        <li>
                            <a href="../about.php" target="_blank">
                                <i class="fas fa-info-circle"></i>
                                <span>Hakkımızda</span>
                            </a>
                        </li>
                        <li>
                            <a href="../services.php" target="_blank">
                                <i class="fas fa-cogs"></i>
                                <span>Hizmetler</span>
                            </a>
                        </li>
                        <li>
                            <a href="../portfolio.php" target="_blank">
                                <i class="fas fa-briefcase"></i>
                                <span>Portfolio</span>
                            </a>
                        </li>
                        <li>
                            <a href="../blog.php" target="_blank">
                                <i class="fas fa-blog"></i>
                                <span>Blog</span>
                            </a>
                        </li>
                        <li>
                            <a href="../contact.php" target="_blank">
                                <i class="fas fa-envelope"></i>
                                <span>İletişim</span>
                            </a>
                        </li>
                        <li>
                            <a href="../pricing.php" target="_blank">
                                <i class="fas fa-dollar-sign"></i>
                                <span>Fiyatlandırma</span>
                            </a>
                        </li>
                        <li>
                            <a href="../faq.php" target="_blank">
                                <i class="fas fa-question-circle"></i>
                                <span>SSS</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="menu-section">
                    <h3>İçerik Yönetimi</h3>
                    <ul>
                        <li>
                            <a href="pages/blog-manager.php">
                                <i class="fas fa-edit"></i>
                                <span>Blog Yönetimi</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/portfolio-manager.php">
                                <i class="fas fa-images"></i>
                                <span>Portfolio Yönetimi</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/services-manager.php">
                                <i class="fas fa-tools"></i>
                                <span>Hizmet Yönetimi</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/contact-manager.php">
                                <i class="fas fa-comments"></i>
                                <span>Mesaj Yönetimi</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="menu-section">
                    <h3>Araçlar</h3>
                    <ul>
                        <li>
                            <a href="pages/settings.php">
                                <i class="fas fa-cog"></i>
                                <span>Site Ayarları</span>
                            </a>
                        </li>
                        <li>
                            <a href="simple-test.php">
                                <i class="fas fa-vial"></i>
                                <span>Sistem Testi</span>
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
                <h1>Web Project Dashboard</h1>
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
            
            <!-- Web Statistics -->
            <div class="web-stats">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #667eea20; color: #667eea">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $web_stats['total_pages']; ?></div>
                        <div class="stat-label">Toplam Sayfa</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #28a74520; color: #28a745">
                        <i class="fas fa-blog"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $web_stats['total_blog_posts']; ?></div>
                        <div class="stat-label">Blog Yazısı</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #17a2b820; color: #17a2b8">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $web_stats['total_portfolio_items']; ?></div>
                        <div class="stat-label">Portfolio Projesi</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #ffc10720; color: #ffc107">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $web_stats['total_contact_messages']; ?></div>
                        <div class="stat-label">İletişim Mesajı</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #6f42c120; color: #6f42c1">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $web_stats['total_services']; ?></div>
                        <div class="stat-label">Hizmet</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #dc354520; color: #dc3545">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $web_stats['total_faq_items']; ?></div>
                        <div class="stat-label">SSS Maddesi</div>
                    </div>
                </div>
            </div>
            
            <!-- Web Management -->
            <div class="web-management-grid">
                <div class="management-card">
                    <h3><i class="fas fa-file-alt"></i> Sayfa Yönetimi</h3>
                    <div class="management-actions">
                        <a href="pages/page-manager.php" class="action-btn">
                            <i class="fas fa-list"></i>
                            <span>Tüm Sayfaları Görüntüle</span>
                        </a>
                        <a href="pages/code-editor.php?file=index.php" class="action-btn">
                            <i class="fas fa-edit"></i>
                            <span>Ana Sayfa Düzenle</span>
                        </a>
                        <a href="pages/code-editor.php?file=about.php" class="action-btn">
                            <i class="fas fa-info"></i>
                            <span>Hakkımızda Düzenle</span>
                        </a>
                        <a href="pages/code-editor.php?file=contact.php" class="action-btn">
                            <i class="fas fa-envelope"></i>
                            <span>İletişim Sayfası</span>
                        </a>
                    </div>
                </div>
                
                <div class="management-card">
                    <h3><i class="fas fa-blog"></i> Blog Yönetimi</h3>
                    <div class="management-actions">
                        <a href="pages/blog-manager.php" class="action-btn">
                            <i class="fas fa-list"></i>
                            <span>Tüm Blog Yazıları</span>
                        </a>
                        <a href="pages/blog-manager.php?action=add" class="action-btn">
                            <i class="fas fa-plus"></i>
                            <span>Yeni Blog Yazısı</span>
                        </a>
                        <a href="pages/blog-manager.php?action=categories" class="action-btn">
                            <i class="fas fa-tags"></i>
                            <span>Kategoriler</span>
                        </a>
                        <a href="../blog.php" target="_blank" class="action-btn">
                            <i class="fas fa-external-link-alt"></i>
                            <span>Blog Sayfasını Görüntüle</span>
                        </a>
                    </div>
                </div>
                
                <div class="management-card">
                    <h3><i class="fas fa-briefcase"></i> Portfolio Yönetimi</h3>
                    <div class="management-actions">
                        <a href="pages/portfolio-manager.php" class="action-btn">
                            <i class="fas fa-list"></i>
                            <span>Tüm Projeler</span>
                        </a>
                        <a href="pages/portfolio-manager.php?action=add" class="action-btn">
                            <i class="fas fa-plus"></i>
                            <span>Yeni Proje Ekle</span>
                        </a>
                        <a href="pages/file-manager.php?folder=images/portfolio" class="action-btn">
                            <i class="fas fa-images"></i>
                            <span>Portfolio Görselleri</span>
                        </a>
                        <a href="../portfolio.php" target="_blank" class="action-btn">
                            <i class="fas fa-external-link-alt"></i>
                            <span>Portfolio Sayfasını Görüntüle</span>
                        </a>
                    </div>
                </div>
                
                <div class="management-card">
                    <h3><i class="fas fa-envelope"></i> İletişim Yönetimi</h3>
                    <div class="management-actions">
                        <a href="pages/contact-manager.php" class="action-btn">
                            <i class="fas fa-list"></i>
                            <span>Tüm Mesajlar</span>
                        </a>
                        <a href="pages/contact-manager.php?status=new" class="action-btn">
                            <i class="fas fa-envelope"></i>
                            <span>Yeni Mesajlar</span>
                        </a>
                        <a href="pages/contact-manager.php?status=replied" class="action-btn">
                            <i class="fas fa-reply"></i>
                            <span>Yanıtlanan Mesajlar</span>
                        </a>
                        <a href="pages/settings.php?section=contact" class="action-btn">
                            <i class="fas fa-cog"></i>
                            <span>İletişim Ayarları</span>
                        </a>
                    </div>
                </div>
                
                <div class="management-card">
                    <h3><i class="fas fa-cogs"></i> Hizmet Yönetimi</h3>
                    <div class="management-actions">
                        <a href="pages/services-manager.php" class="action-btn">
                            <i class="fas fa-list"></i>
                            <span>Tüm Hizmetler</span>
                        </a>
                        <a href="pages/services-manager.php?action=add" class="action-btn">
                            <i class="fas fa-plus"></i>
                            <span>Yeni Hizmet Ekle</span>
                        </a>
                        <a href="pages/pricing-manager.php" class="action-btn">
                            <i class="fas fa-dollar-sign"></i>
                            <span>Fiyatlandırma</span>
                        </a>
                        <a href="../services.php" target="_blank" class="action-btn">
                            <i class="fas fa-external-link-alt"></i>
                            <span>Hizmetler Sayfasını Görüntüle</span>
                        </a>
                    </div>
                </div>
                
                <div class="management-card">
                    <h3><i class="fas fa-question-circle"></i> SSS Yönetimi</h3>
                    <div class="management-actions">
                        <a href="pages/faq-manager.php" class="action-btn">
                            <i class="fas fa-list"></i>
                            <span>Tüm SSS Maddeleri</span>
                        </a>
                        <a href="pages/faq-manager.php?action=add" class="action-btn">
                            <i class="fas fa-plus"></i>
                            <span>Yeni SSS Ekle</span>
                        </a>
                        <a href="pages/faq-manager.php?action=categories" class="action-btn">
                            <i class="fas fa-tags"></i>
                            <span>SSS Kategorileri</span>
                        </a>
                        <a href="../faq.php" target="_blank" class="action-btn">
                            <i class="fas fa-external-link-alt"></i>
                            <span>SSS Sayfasını Görüntüle</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Access -->
            <div class="quick-access">
                <h3>Hızlı Erişim</h3>
                <div class="quick-links">
                    <a href="../index.php" target="_blank" class="quick-link">
                        <i class="fas fa-home"></i>
                        <span>Ana Sayfa</span>
                    </a>
                    <a href="../about.php" target="_blank" class="quick-link">
                        <i class="fas fa-info-circle"></i>
                        <span>Hakkımızda</span>
                    </a>
                    <a href="../services.php" target="_blank" class="quick-link">
                        <i class="fas fa-cogs"></i>
                        <span>Hizmetler</span>
                    </a>
                    <a href="../portfolio.php" target="_blank" class="quick-link">
                        <i class="fas fa-briefcase"></i>
                        <span>Portfolio</span>
                    </a>
                    <a href="../blog.php" target="_blank" class="quick-link">
                        <i class="fas fa-blog"></i>
                        <span>Blog</span>
                    </a>
                    <a href="../contact.php" target="_blank" class="quick-link">
                        <i class="fas fa-envelope"></i>
                        <span>İletişim</span>
                    </a>
                    <a href="../pricing.php" target="_blank" class="quick-link">
                        <i class="fas fa-dollar-sign"></i>
                        <span>Fiyatlandırma</span>
                    </a>
                    <a href="../faq.php" target="_blank" class="quick-link">
                        <i class="fas fa-question-circle"></i>
                        <span>SSS</span>
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
