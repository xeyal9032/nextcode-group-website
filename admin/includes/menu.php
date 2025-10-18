<?php
/**
 * Admin Menu System
 * NextCode Group
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    die('Direct access not allowed');
}

/**
 * Generate admin navigation menu
 */
function generateAdminMenu($currentPage = '') {
    $role = $_SESSION['admin_role'] ?? 'viewer';
    $menuItems = getAdminMenu($role);
    
    $html = '<ul class="nav flex-column">';
    
    foreach ($menuItems as $item) {
        $activeClass = $item['active'] ? ' active' : '';
        $html .= '<li class="nav-item">';
        $html .= '<a class="nav-link' . $activeClass . '" href="' . htmlspecialchars($item['url']) . '">';
        $html .= '<i class="' . $item['icon'] . '"></i>';
        $html .= ' ' . htmlspecialchars($item['title']);
        $html .= '</a>';
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    
    return $html;
}

/**
 * Generate breadcrumb navigation
 */
function generateBreadcrumb($items = []) {
    if (empty($items)) {
        return '';
    }
    
    $html = '<nav aria-label="breadcrumb">';
    $html .= '<ol class="breadcrumb">';
    
    foreach ($items as $index => $item) {
        $isLast = ($index === count($items) - 1);
        $activeClass = $isLast ? ' active' : '';
        
        if ($isLast) {
            $html .= '<li class="breadcrumb-item' . $activeClass . '" aria-current="page">';
            $html .= htmlspecialchars($item['title']);
            $html .= '</li>';
        } else {
            $html .= '<li class="breadcrumb-item">';
            $html .= '<a href="' . htmlspecialchars($item['url']) . '">';
            $html .= htmlspecialchars($item['title']);
            $html .= '</a>';
            $html .= '</li>';
        }
    }
    
    $html .= '</ol>';
    $html .= '</nav>';
    
    return $html;
}

/**
 * Get page title
 */
function getPageTitle($page) {
    $titles = [
        'index.php' => 'Dashboard',
        'blog.php' => 'Blog Yönetimi',
        'blog-add.php' => 'Yeni Blog Yazısı',
        'blog-edit.php' => 'Blog Yazısını Düzenle',
        'portfolio.php' => 'Portfolio Yönetimi',
        'portfolio-add.php' => 'Yeni Portfolio Projesi',
        'portfolio-edit.php' => 'Portfolio Projesini Düzenle',
        'users.php' => 'Kullanıcı Yönetimi',
        'user-add.php' => 'Yeni Kullanıcı',
        'user-edit.php' => 'Kullanıcıyı Düzenle',
        'user-roles.php' => 'Rol Yönetimi',
        'messages.php' => 'Mesaj Yönetimi',
        'content.php' => 'İçerik Yönetimi',
        'media.php' => 'Medya Yönetimi',
        'web-content-scanner.php' => 'İçerik Tarayıcı',
        'audit-logs.php' => 'Audit Logs',
        'settings.php' => 'Ayarlar',
        'profile.php' => 'Profil',
        'login.php' => 'Giriş Yap',
        'logout.php' => 'Çıkış Yap'
    ];
    
    return $titles[$page] ?? 'Admin Panel';
}

/**
 * Get page description
 */
function getPageDescription($page) {
    $descriptions = [
        'index.php' => 'Admin paneli ana sayfası ve genel istatistikler',
        'blog.php' => 'Blog yazılarını görüntüleyin, düzenleyin ve yönetin',
        'blog-add.php' => 'Yeni blog yazısı oluşturun',
        'blog-edit.php' => 'Mevcut blog yazısını düzenleyin',
        'portfolio.php' => 'Portfolio projelerini görüntüleyin, düzenleyin ve yönetin',
        'portfolio-add.php' => 'Yeni portfolio projesi ekleyin',
        'portfolio-edit.php' => 'Mevcut portfolio projesini düzenleyin',
        'users.php' => 'Admin kullanıcılarını görüntüleyin, düzenleyin ve yönetin',
        'user-add.php' => 'Yeni admin kullanıcısı ekleyin',
        'user-edit.php' => 'Mevcut kullanıcıyı düzenleyin',
        'user-roles.php' => 'Kullanıcı rolleri ve yetkilerini yönetin',
        'messages.php' => 'İletişim mesajlarını görüntüleyin ve yönetin',
        'content.php' => 'Site içeriklerini düzenleyin ve yönetin',
        'media.php' => 'Medya dosyalarını yükleyin, düzenleyin ve yönetin',
        'web-content-scanner.php' => 'Web sitesi içeriklerini tarayın ve yönetin',
        'audit-logs.php' => 'Admin paneli denetim kayıtları ve güvenlik olayları',
        'settings.php' => 'Sistem ayarlarını düzenleyin',
        'profile.php' => 'Kendi profil bilgilerinizi düzenleyin',
        'login.php' => 'Admin paneline giriş yapın',
        'logout.php' => 'Admin panelinden çıkış yapın'
    ];
    
    return $descriptions[$page] ?? 'Admin paneli sayfası';
}

/**
 * Get quick actions for current page
 */
function getQuickActions($page) {
    $actions = [
        'blog.php' => [
            ['title' => 'Yeni Yazı Ekle', 'url' => 'blog-add.php', 'icon' => 'fas fa-plus', 'class' => 'btn-primary'],
            ['title' => 'Kategoriler', 'url' => 'blog.php#categories', 'icon' => 'fas fa-tags', 'class' => 'btn-secondary']
        ],
        'portfolio.php' => [
            ['title' => 'Yeni Proje Ekle', 'url' => 'portfolio-add.php', 'icon' => 'fas fa-plus', 'class' => 'btn-primary'],
            ['title' => 'Kategoriler', 'url' => 'portfolio.php#categories', 'icon' => 'fas fa-tags', 'class' => 'btn-secondary']
        ],
        'users.php' => [
            ['title' => 'Yeni Kullanıcı', 'url' => 'user-add.php', 'icon' => 'fas fa-user-plus', 'class' => 'btn-primary'],
            ['title' => 'Roller', 'url' => 'user-roles.php', 'icon' => 'fas fa-user-shield', 'class' => 'btn-secondary']
        ],
        'content.php' => [
            ['title' => 'Yeni İçerik', 'url' => 'content.php#add', 'icon' => 'fas fa-plus', 'class' => 'btn-primary'],
            ['title' => 'İçerik Tarayıcı', 'url' => 'web-content-scanner.php', 'icon' => 'fas fa-search', 'class' => 'btn-secondary']
        ],
        'media.php' => [
            ['title' => 'Dosya Yükle', 'url' => 'media.php#upload', 'icon' => 'fas fa-upload', 'class' => 'btn-primary'],
            ['title' => 'Galeri', 'url' => 'media.php#gallery', 'icon' => 'fas fa-th', 'class' => 'btn-secondary']
        ]
    ];
    
    return $actions[$page] ?? [];
}

/**
 * Generate quick actions HTML
 */
function generateQuickActions($page) {
    $actions = getQuickActions($page);
    
    if (empty($actions)) {
        return '';
    }
    
    $html = '<div class="quick-actions mb-3">';
    
    foreach ($actions as $action) {
        $html .= '<a href="' . htmlspecialchars($action['url']) . '" class="btn ' . $action['class'] . ' btn-sm me-2">';
        $html .= '<i class="' . $action['icon'] . '"></i>';
        $html .= ' ' . htmlspecialchars($action['title']);
        $html .= '</a>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Get sidebar stats
 */
function getSidebarStats() {
    global $pdo;
    
    if (!$pdo) {
        return [];
    }
    
    $stats = [];
    
    try {
        // Blog posts count
        $stmt = $pdo->query("SELECT COUNT(*) FROM blog_posts");
        $stats['blog_posts'] = $stmt->fetchColumn();
        
        // Portfolio projects count
        $stmt = $pdo->query("SELECT COUNT(*) FROM portfolio_projects");
        $stats['portfolio_projects'] = $stmt->fetchColumn();
        
        // Unread messages count
        $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'");
        $stats['unread_messages'] = $stmt->fetchColumn();
        
        // Admin users count
        $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
        $stats['admin_users'] = $stmt->fetchColumn();
        
    } catch (Exception $e) {
        error_log("Sidebar stats error: " . $e->getMessage());
    }
    
    return $stats;
}

/**
 * Generate sidebar stats HTML
 */
function generateSidebarStats() {
    $stats = getSidebarStats();
    
    if (empty($stats)) {
        return '';
    }
    
    $html = '<div class="sidebar-stats mt-4">';
    $html .= '<h6 class="text-white-50 mb-3">İstatistikler</h6>';
    
    if (isset($stats['blog_posts'])) {
        $html .= '<div class="stat-item mb-2">';
        $html .= '<small class="text-white-50">Blog Yazıları:</small>';
        $html .= '<div class="text-white">' . $stats['blog_posts'] . '</div>';
        $html .= '</div>';
    }
    
    if (isset($stats['portfolio_projects'])) {
        $html .= '<div class="stat-item mb-2">';
        $html .= '<small class="text-white-50">Portfolio Projeleri:</small>';
        $html .= '<div class="text-white">' . $stats['portfolio_projects'] . '</div>';
        $html .= '</div>';
    }
    
    if (isset($stats['unread_messages']) && $stats['unread_messages'] > 0) {
        $html .= '<div class="stat-item mb-2">';
        $html .= '<small class="text-white-50">Okunmamış Mesajlar:</small>';
        $html .= '<div class="text-warning">' . $stats['unread_messages'] . '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Get user menu items
 */
function getUserMenuItems() {
    $items = [
        [
            'title' => 'Profil',
            'url' => 'profile.php',
            'icon' => 'fas fa-user-edit'
        ],
        [
            'title' => 'Ayarlar',
            'url' => 'settings.php',
            'icon' => 'fas fa-cog'
        ]
    ];
    
    // Add logout
    $items[] = [
        'title' => 'Çıkış Yap',
        'url' => 'logout.php',
        'icon' => 'fas fa-sign-out-alt',
        'class' => 'text-danger'
    ];
    
    return $items;
}

/**
 * Generate user menu HTML
 */
function generateUserMenu() {
    $items = getUserMenuItems();
    $username = $_SESSION['admin_username'] ?? 'Admin';
    $userRole = $_SESSION['admin_role'] ?? 'admin';
    
    $html = '<div class="dropdown">';
    $html .= '<a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">';
    $html .= '<i class="fas fa-user"></i>';
    $html .= ' ' . htmlspecialchars($username);
    $html .= '<br><small class="text-white-50">' . ucfirst($userRole) . '</small>';
    $html .= '</a>';
    
    $html .= '<ul class="dropdown-menu dropdown-menu-end">';
    
    foreach ($items as $item) {
        $class = isset($item['class']) ? ' ' . $item['class'] : '';
        $html .= '<li>';
        $html .= '<a class="dropdown-item' . $class . '" href="' . htmlspecialchars($item['url']) . '">';
        $html .= '<i class="' . $item['icon'] . '"></i>';
        $html .= ' ' . htmlspecialchars($item['title']);
        $html .= '</a>';
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Get notification items
 */
function getNotificationItems() {
    global $pdo;
    
    if (!$pdo) {
        return [];
    }
    
    $notifications = [];
    
    try {
        // Unread messages
        $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'");
        $unreadCount = $stmt->fetchColumn();
        
        if ($unreadCount > 0) {
            $notifications[] = [
                'type' => 'message',
                'title' => 'Yeni Mesajlar',
                'message' => $unreadCount . ' okunmamış mesaj var',
                'url' => 'messages.php',
                'icon' => 'fas fa-envelope',
                'class' => 'text-warning'
            ];
        }
        
        // Recent audit logs
        $stmt = $pdo->query("
            SELECT COUNT(*) FROM admin_audit_logs 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $recentLogs = $stmt->fetchColumn();
        
        if ($recentLogs > 10) {
            $notifications[] = [
                'type' => 'security',
                'title' => 'Güvenlik Uyarısı',
                'message' => 'Son 1 saatte ' . $recentLogs . ' güvenlik olayı',
                'url' => 'audit-logs.php',
                'icon' => 'fas fa-shield-alt',
                'class' => 'text-danger'
            ];
        }
        
    } catch (Exception $e) {
        error_log("Notification items error: " . $e->getMessage());
    }
    
    return $notifications;
}

/**
 * Generate notification menu HTML
 */
function generateNotificationMenu() {
    $notifications = getNotificationItems();
    $count = count($notifications);
    
    $html = '<div class="dropdown me-3">';
    $html .= '<a class="nav-link dropdown-toggle text-white position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">';
    $html .= '<i class="fas fa-bell"></i>';
    
    if ($count > 0) {
        $html .= '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">';
        $html .= $count;
        $html .= '</span>';
    }
    
    $html .= '</a>';
    
    if ($count > 0) {
        $html .= '<ul class="dropdown-menu dropdown-menu-end notification-menu">';
        
        foreach ($notifications as $notification) {
            $html .= '<li>';
            $html .= '<a class="dropdown-item' . ($notification['class'] ?? '') . '" href="' . htmlspecialchars($notification['url']) . '">';
            $html .= '<i class="' . $notification['icon'] . '"></i>';
            $html .= '<div>';
            $html .= '<strong>' . htmlspecialchars($notification['title']) . '</strong>';
            $html .= '<br><small>' . htmlspecialchars($notification['message']) . '</small>';
            $html .= '</div>';
            $html .= '</a>';
            $html .= '</li>';
        }
        
        $html .= '</ul>';
    }
    
    $html .= '</div>';
    
    return $html;
}
?>
