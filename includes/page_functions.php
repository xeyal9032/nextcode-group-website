<?php
// NextCode Group - Page Functions
// Production Environment Configuration

// Include API router for handling API requests
require_once __DIR__ . '/../api-router.php';

// Check if secure access is defined
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

/**
 * Safe redirect function that prevents redirect loops
 * @param string $url
 * @param int $status_code
 */
function safeRedirect($url, $status_code = 302) {
    // If URL is relative, make it absolute with current protocol and host
    if (strpos($url, 'http') !== 0) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $url = $protocol . '://' . $host . '/' . ltrim($url, '/');
    }
    
    // Set proper status code
    http_response_code($status_code);
    header("Location: $url");
    exit;
}

/**
 * Get page by slug from database
 * @param string $slug
 * @return array|false
 */
function getPageBySlug($slug) {
    global $pdo;
    
    if (!$pdo) {
        return false;
    }
    
    try {
        // Check if table exists first
        if (!tableExists('pages')) {
            return false; // Table doesn't exist yet
        }
        
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Database error in getPageBySlug: ' . $e->getMessage());
        return false;
    }
}

/**
 * Increment page views
 * @param int $page_id
 * @return bool
 */
function incrementPageViews($page_id) {
    global $pdo;
    
    if (!$pdo) {
        return false;
    }
    
    try {
        // Check if table exists first
        if (!tableExists('pages')) {
            return false; // Table doesn't exist yet
        }
        
        $stmt = $pdo->prepare("UPDATE pages SET views = views + 1 WHERE id = ?");
        return $stmt->execute([$page_id]);
    } catch (PDOException $e) {
        error_log('Database error in incrementPageViews: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get all published pages
 * @return array
 */
function getAllPages() {
    global $pdo;
    
    if (!$pdo) {
        return [];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE status = 'published' ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error fetching pages: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get page content by ID
 * @param int $id
 * @return array|false
 */
function getPageById($id) {
    global $pdo;
    
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Error fetching page by ID: ' . $e->getMessage());
        return false;
    }
}

/**
 * Sanitize output for HTML
 * @param string $string
 * @return string
 */
function sanitizeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date for display
 * @param string $date
 * @return string
 */
function formatDate($date) {
    return date('d.m.Y', strtotime($date));
}

/**
 * Get site settings
 * @return array
 */
function getSiteSettings() {
    global $pdo;
    
    if (!$pdo) {
        return [];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM site_settings WHERE is_public = 1");
        $stmt->execute();
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    } catch (PDOException $e) {
        error_log('Error fetching site settings: ' . $e->getMessage());
        return [];
    }
}

/**
 * Check if table exists
 * @param string $table_name
 * @return bool
 */
function tableExists($table_name) {
    global $pdo;
    
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
        $stmt->execute([$table_name]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log('Error checking table existence: ' . $e->getMessage());
        return false;
    }
}

/**
 * Create default page if not exists
 * @param string $slug
 * @param string $title
 * @param string $content
 * @return bool
 */
function createDefaultPage($slug, $title, $content = '') {
    global $pdo;
    
    if (!$pdo) {
        return false;
    }
    
    try {
        // Check if page already exists
        if (getPageBySlug($slug)) {
            return true;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO pages (title, slug, content, meta_description, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, 'published', NOW(), NOW())
        ");
        
        return $stmt->execute([
            $title,
            $slug,
            $content,
            $title . ' - NextCode Group'
        ]);
    } catch (PDOException $e) {
        error_log('Error creating default page: ' . $e->getMessage());
        return false;
    }
}

/**
 * Generate dynamic navigation menu
 * @param string $current_page
 * @return string
 */
function generateDynamicNavigation($current_page = 'home') {
    $nav_items = [
        'home' => ['url' => 'index.php', 'text' => 'Ana Səhifə', 'icon' => 'fas fa-home'],
        'about' => ['url' => 'about.php', 'text' => 'Haqqımızda', 'icon' => 'fas fa-info-circle'],
        'services' => ['url' => 'services.php', 'text' => 'Xidmətlər', 'icon' => 'fas fa-cogs'],
        'portfolio' => ['url' => 'portfolio.php', 'text' => 'Portfolio', 'icon' => 'fas fa-briefcase'],
        'blog' => ['url' => 'blog.php', 'text' => 'Blog', 'icon' => 'fas fa-blog'],
        'contact' => ['url' => 'contact.php', 'text' => 'Əlaqə', 'icon' => 'fas fa-envelope']
    ];
    
    $html = '';
    
    foreach ($nav_items as $page => $item) {
        $active_class = ($current_page === $page) ? ' active' : '';
        $html .= '<li class="nav-item">';
        $html .= '<a class="nav-link' . $active_class . '" href="' . $item['url'] . '">';
        $html .= '<i class="' . $item['icon'] . ' me-1"></i>';
        $html .= $item['text'];
        $html .= '</a>';
        $html .= '</li>';
    }
    
    return $html;
}

// Initialize default pages if tables exist
if (tableExists('pages')) {
    createDefaultPage('home', 'NextCode Group - Rəqəmsal Marketinq Agentliyi', 'Ana səhifə məzmunu');
}
?>