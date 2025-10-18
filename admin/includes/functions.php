<?php
/**
 * Admin Panel Helper Functions
 * NextCode Group
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    die('Direct access not allowed');
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'd.m.Y H:i') {
    if (empty($date) || $date === '0000-00-00 00:00:00') {
        return 'Tarih yok';
    }
    
    try {
        $dateTime = new DateTime($date);
        return $dateTime->format($format);
    } catch (Exception $e) {
        return 'Geçersiz tarih';
    }
}

/**
 * Format file size for display
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * Generate slug from text
 */
function generateSlug($text) {
    // Turkish characters to English
    $turkish = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'I', 'İ', 'Ö', 'Ş', 'Ü'];
    $english = ['c', 'g', 'i', 'o', 's', 'u', 'c', 'g', 'i', 'i', 'o', 's', 'u'];
    
    $text = str_replace($turkish, $english, $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Truncate text with ellipsis
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get status badge HTML
 */
function getStatusBadge($status, $type = 'default') {
    $badges = [
        'active' => ['class' => 'success', 'text' => 'Aktif'],
        'inactive' => ['class' => 'danger', 'text' => 'Pasif'],
        'pending' => ['class' => 'warning', 'text' => 'Beklemede'],
        'published' => ['class' => 'success', 'text' => 'Yayında'],
        'draft' => ['class' => 'secondary', 'text' => 'Taslak'],
        'read' => ['class' => 'info', 'text' => 'Okundu'],
        'unread' => ['class' => 'warning', 'text' => 'Okunmadı']
    ];
    
    if (isset($badges[$status])) {
        $badge = $badges[$status];
        return '<span class="badge bg-' . $badge['class'] . '">' . $badge['text'] . '</span>';
    }
    
    return '<span class="badge bg-' . $type . '">' . ucfirst($status) . '</span>';
}

/**
 * Get priority badge HTML
 */
function getPriorityBadge($priority) {
    $priorities = [
        'high' => ['class' => 'danger', 'text' => 'Yüksek'],
        'medium' => ['class' => 'warning', 'text' => 'Orta'],
        'low' => ['class' => 'success', 'text' => 'Düşük']
    ];
    
    if (isset($priorities[$priority])) {
        $badge = $priorities[$priority];
        return '<span class="badge bg-' . $badge['class'] . '">' . $badge['text'] . '</span>';
    }
    
    return '<span class="badge bg-secondary">' . ucfirst($priority) . '</span>';
}

/**
 * Generate random password
 */
function generatePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle($chars), 0, $length);
}

/**
 * Check if email is valid
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Get user IP address
 */
function getUserIP() {
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Get browser info
 */
function getBrowserInfo() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $browsers = [
        'Chrome' => '/Chrome/',
        'Firefox' => '/Firefox/',
        'Safari' => '/Safari/',
        'Edge' => '/Edge/',
        'Opera' => '/Opera/',
        'IE' => '/MSIE/'
    ];
    
    foreach ($browsers as $browser => $pattern) {
        if (preg_match($pattern, $userAgent)) {
            return $browser;
        }
    }
    
    return 'Unknown';
}

/**
 * Get OS info
 */
function getOSInfo() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $os = [
        'Windows' => '/Windows/',
        'Mac' => '/Mac/',
        'Linux' => '/Linux/',
        'Android' => '/Android/',
        'iOS' => '/iPhone|iPad/'
    ];
    
    foreach ($os as $system => $pattern) {
        if (preg_match($pattern, $userAgent)) {
            return $system;
        }
    }
    
    return 'Unknown';
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get pagination HTML
 */
function getPagination($currentPage, $totalPages, $baseUrl, $params = []) {
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Pagination"><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($currentPage > 1) {
        $prevUrl = $baseUrl . '?' . http_build_query(array_merge($params, ['page' => $currentPage - 1]));
        $html .= '<li class="page-item"><a class="page-link" href="' . $prevUrl . '"><i class="fas fa-chevron-left"></i> Önceki</a></li>';
    }
    
    // Page numbers
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $pageUrl = $baseUrl . '?' . http_build_query(array_merge($params, ['page' => $i]));
        $activeClass = ($i === $currentPage) ? ' active' : '';
        $html .= '<li class="page-item' . $activeClass . '"><a class="page-link" href="' . $pageUrl . '">' . $i . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $nextUrl = $baseUrl . '?' . http_build_query(array_merge($params, ['page' => $currentPage + 1]));
        $html .= '<li class="page-item"><a class="page-link" href="' . $nextUrl . '">Sonraki <i class="fas fa-chevron-right"></i></a></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Get breadcrumb HTML
 */
function getBreadcrumb($items) {
    if (empty($items)) {
        return '';
    }
    
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    foreach ($items as $index => $item) {
        $isLast = ($index === count($items) - 1);
        $activeClass = $isLast ? ' active' : '';
        
        if ($isLast) {
            $html .= '<li class="breadcrumb-item' . $activeClass . '" aria-current="page">' . htmlspecialchars($item['title']) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['title']) . '</a></li>';
        }
    }
    
    $html .= '</ol></nav>';
    
    return $html;
}

/**
 * Get time ago string
 */
function getTimeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) {
        return 'Az önce';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' dakika önce';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' saat önce';
    } elseif ($time < 2592000) {
        $days = floor($time / 86400);
        return $days . ' gün önce';
    } elseif ($time < 31536000) {
        $months = floor($time / 2592000);
        return $months . ' ay önce';
    } else {
        $years = floor($time / 31536000);
        return $years . ' yıl önce';
    }
}

/**
 * Clean HTML content
 */
function cleanHTML($html) {
    // Remove script tags
    $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
    
    // Remove style tags
    $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);
    
    // Remove dangerous attributes
    $html = preg_replace('/\s*(on\w+|javascript:|data:|vbscript:)/i', '', $html);
    
    return $html;
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file is image
 */
function isImage($filename) {
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    return in_array(getFileExtension($filename), $imageExtensions);
}

/**
 * Check if file is video
 */
function isVideo($filename) {
    $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
    return in_array(getFileExtension($filename), $videoExtensions);
}

/**
 * Check if file is document
 */
function isDocument($filename) {
    $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
    return in_array(getFileExtension($filename), $documentExtensions);
}

/**
 * Get file type icon
 */
function getFileIcon($filename) {
    if (isImage($filename)) {
        return 'fas fa-image';
    } elseif (isVideo($filename)) {
        return 'fas fa-video';
    } elseif (isDocument($filename)) {
        return 'fas fa-file-alt';
    } else {
        return 'fas fa-file';
    }
}

/**
 * Format number with thousands separator
 */
function formatNumber($number) {
    return number_format($number, 0, ',', '.');
}

/**
 * Get relative time
 */
function getRelativeTime($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) {
        return 'Şimdi';
    } elseif ($time < 3600) {
        return floor($time / 60) . 'dk';
    } elseif ($time < 86400) {
        return floor($time / 3600) . 'sa';
    } elseif ($time < 2592000) {
        return floor($time / 86400) . 'g';
    } elseif ($time < 31536000) {
        return floor($time / 2592000) . 'ay';
    } else {
        return floor($time / 31536000) . 'y';
    }
}

/**
 * Check if string is JSON
 */
function isJSON($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Array to CSV
 */
function arrayToCSV($array, $filename = 'export.csv') {
    $output = fopen('php://temp', 'w');
    
    // Add BOM for UTF-8
    fwrite($output, "\xEF\xBB\xBF");
    
    // Add headers
    if (!empty($array)) {
        fputcsv($output, array_keys($array[0]));
    }
    
    // Add data
    foreach ($array as $row) {
        fputcsv($output, $row);
    }
    
    rewind($output);
    $csv = stream_get_contents($output);
    fclose($output);
    
    return $csv;
}

/**
 * Get directory size
 */
function getDirectorySize($directory) {
    $size = 0;
    $files = glob($directory . '/*');
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $size += filesize($file);
        } elseif (is_dir($file)) {
            $size += getDirectorySize($file);
        }
    }
    
    return $size;
}

/**
 * Clean directory
 */
function cleanDirectory($directory, $olderThan = 3600) {
    $files = glob($directory . '/*');
    $cleaned = 0;
    
    foreach ($files as $file) {
        if (is_file($file) && (time() - filemtime($file)) > $olderThan) {
            if (unlink($file)) {
                $cleaned++;
            }
        }
    }
    
    return $cleaned;
}

/**
 * Log admin action
 */
function logAdminAction($action, $details = '', $level = 'INFO') {
    global $pdo;
    
    if (function_exists('logAuditEvent')) {
        logAuditEvent('ADMIN_ACTION', $action, $details, $level);
    }
}

/**
 * Get admin statistics
 */
function getAdminStats() {
    global $pdo;
    
    if (!$pdo) {
        return [];
    }
    
    $stats = [];
    
    try {
        // Blog posts
        $stmt = $pdo->query("SELECT COUNT(*) FROM blog_posts");
        $stats['blog_posts'] = $stmt->fetchColumn();
        
        // Portfolio projects
        $stmt = $pdo->query("SELECT COUNT(*) FROM portfolio_projects");
        $stats['portfolio_projects'] = $stmt->fetchColumn();
        
        // Admin users
        $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
        $stats['admin_users'] = $stmt->fetchColumn();
        
        // Contact messages
        $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages");
        $stats['contact_messages'] = $stmt->fetchColumn();
        
        // Unread messages
        $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'");
        $stats['unread_messages'] = $stmt->fetchColumn();
        
    } catch (Exception $e) {
        error_log("Admin stats error: " . $e->getMessage());
    }
    
    return $stats;
}
?>
