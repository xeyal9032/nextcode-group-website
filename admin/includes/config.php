<?php
/**
 * Admin Configuration
 * NextCode Group
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    die('Direct access not allowed');
}

// Admin panel configuration
define('ADMIN_VERSION', '1.0.0');
define('ADMIN_NAME', 'NextCode Admin Panel');

// Security settings
define('ADMIN_SESSION_TIMEOUT', 3600); // 1 hour
define('ADMIN_MAX_LOGIN_ATTEMPTS', 5);
define('ADMIN_LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File upload settings
define('ADMIN_MAX_FILE_SIZE', 5242880); // 5MB
define('ADMIN_MAX_IMAGE_SIZE', 2097152); // 2MB
define('ADMIN_ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ADMIN_ALLOWED_FILE_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// Pagination settings
define('ADMIN_DEFAULT_PAGE_SIZE', 20);
define('ADMIN_MAX_PAGE_SIZE', 100);

// Cache settings
define('ADMIN_CACHE_ENABLED', true);
define('ADMIN_CACHE_TTL', 3600); // 1 hour

// Logging settings
define('ADMIN_LOG_ENABLED', true);
define('ADMIN_LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('ADMIN_LOG_MAX_SIZE', 10485760); // 10MB

// Email settings
define('ADMIN_EMAIL_ENABLED', false);
define('ADMIN_FROM_EMAIL', 'admin@nextcode.az');
define('ADMIN_FROM_NAME', 'NextCode Admin');

// Backup settings
define('ADMIN_BACKUP_ENABLED', true);
define('ADMIN_BACKUP_RETENTION_DAYS', 30);

// API settings
define('ADMIN_API_ENABLED', false);
define('ADMIN_API_RATE_LIMIT', 100); // requests per hour

// Theme settings
define('ADMIN_THEME', 'default');
define('ADMIN_SIDEBAR_COLLAPSED', false);
define('ADMIN_DARK_MODE', false);

// Feature flags
define('ADMIN_FEATURE_BACKUP', true);
define('ADMIN_FEATURE_ANALYTICS', true);
define('ADMIN_FEATURE_NOTIFICATIONS', true);
define('ADMIN_FEATURE_BULK_OPERATIONS', true);
define('ADMIN_FEATURE_SEARCH', true);
define('ADMIN_FEATURE_EXPORT', true);
define('ADMIN_FEATURE_IMPORT', true);

// Database settings
define('ADMIN_DB_CHARSET', 'utf8mb4');
define('ADMIN_DB_COLLATION', 'utf8mb4_unicode_ci');

// Timezone
date_default_timezone_set('Europe/Istanbul');

/**
 * Get admin configuration value
 */
function getAdminConfig($key, $default = null) {
    $config = [
        'version' => ADMIN_VERSION,
        'name' => ADMIN_NAME,
        'session_timeout' => ADMIN_SESSION_TIMEOUT,
        'max_login_attempts' => ADMIN_MAX_LOGIN_ATTEMPTS,
        'login_lockout_time' => ADMIN_LOGIN_LOCKOUT_TIME,
        'max_file_size' => ADMIN_MAX_FILE_SIZE,
        'max_image_size' => ADMIN_MAX_IMAGE_SIZE,
        'allowed_image_types' => ADMIN_ALLOWED_IMAGE_TYPES,
        'allowed_file_types' => ADMIN_ALLOWED_FILE_TYPES,
        'default_page_size' => ADMIN_DEFAULT_PAGE_SIZE,
        'max_page_size' => ADMIN_MAX_PAGE_SIZE,
        'cache_enabled' => ADMIN_CACHE_ENABLED,
        'cache_ttl' => ADMIN_CACHE_TTL,
        'log_enabled' => ADMIN_LOG_ENABLED,
        'log_level' => ADMIN_LOG_LEVEL,
        'log_max_size' => ADMIN_LOG_MAX_SIZE,
        'email_enabled' => ADMIN_EMAIL_ENABLED,
        'from_email' => ADMIN_FROM_EMAIL,
        'from_name' => ADMIN_FROM_NAME,
        'backup_enabled' => ADMIN_BACKUP_ENABLED,
        'backup_retention_days' => ADMIN_BACKUP_RETENTION_DAYS,
        'api_enabled' => ADMIN_API_ENABLED,
        'api_rate_limit' => ADMIN_API_RATE_LIMIT,
        'theme' => ADMIN_THEME,
        'sidebar_collapsed' => ADMIN_SIDEBAR_COLLAPSED,
        'dark_mode' => ADMIN_DARK_MODE,
        'feature_backup' => ADMIN_FEATURE_BACKUP,
        'feature_analytics' => ADMIN_FEATURE_ANALYTICS,
        'feature_notifications' => ADMIN_FEATURE_NOTIFICATIONS,
        'feature_bulk_operations' => ADMIN_FEATURE_BULK_OPERATIONS,
        'feature_search' => ADMIN_FEATURE_SEARCH,
        'feature_export' => ADMIN_FEATURE_EXPORT,
        'feature_import' => ADMIN_FEATURE_IMPORT,
        'db_charset' => ADMIN_DB_CHARSET,
        'db_collation' => ADMIN_DB_COLLATION
    ];
    
    return $config[$key] ?? $default;
}

/**
 * Set admin configuration value (runtime only)
 */
function setAdminConfig($key, $value) {
    static $runtimeConfig = [];
    $runtimeConfig[$key] = $value;
}

/**
 * Get all admin configuration
 */
function getAllAdminConfig() {
    return [
        'version' => ADMIN_VERSION,
        'name' => ADMIN_NAME,
        'session_timeout' => ADMIN_SESSION_TIMEOUT,
        'max_login_attempts' => ADMIN_MAX_LOGIN_ATTEMPTS,
        'login_lockout_time' => ADMIN_LOGIN_LOCKOUT_TIME,
        'max_file_size' => ADMIN_MAX_FILE_SIZE,
        'max_image_size' => ADMIN_MAX_IMAGE_SIZE,
        'allowed_image_types' => ADMIN_ALLOWED_IMAGE_TYPES,
        'allowed_file_types' => ADMIN_ALLOWED_FILE_TYPES,
        'default_page_size' => ADMIN_DEFAULT_PAGE_SIZE,
        'max_page_size' => ADMIN_MAX_PAGE_SIZE,
        'cache_enabled' => ADMIN_CACHE_ENABLED,
        'cache_ttl' => ADMIN_CACHE_TTL,
        'log_enabled' => ADMIN_LOG_ENABLED,
        'log_level' => ADMIN_LOG_LEVEL,
        'log_max_size' => ADMIN_LOG_MAX_SIZE,
        'email_enabled' => ADMIN_EMAIL_ENABLED,
        'from_email' => ADMIN_FROM_EMAIL,
        'from_name' => ADMIN_FROM_NAME,
        'backup_enabled' => ADMIN_BACKUP_ENABLED,
        'backup_retention_days' => ADMIN_BACKUP_RETENTION_DAYS,
        'api_enabled' => ADMIN_API_ENABLED,
        'api_rate_limit' => ADMIN_API_RATE_LIMIT,
        'theme' => ADMIN_THEME,
        'sidebar_collapsed' => ADMIN_SIDEBAR_COLLAPSED,
        'dark_mode' => ADMIN_DARK_MODE,
        'feature_backup' => ADMIN_FEATURE_BACKUP,
        'feature_analytics' => ADMIN_FEATURE_ANALYTICS,
        'feature_notifications' => ADMIN_FEATURE_NOTIFICATIONS,
        'feature_bulk_operations' => ADMIN_FEATURE_BULK_OPERATIONS,
        'feature_search' => ADMIN_FEATURE_SEARCH,
        'feature_export' => ADMIN_FEATURE_EXPORT,
        'feature_import' => ADMIN_FEATURE_IMPORT,
        'db_charset' => ADMIN_DB_CHARSET,
        'db_collation' => ADMIN_DB_COLLATION
    ];
}

/**
 * Check if feature is enabled
 */
function isFeatureEnabled($feature) {
    switch ($feature) {
        case 'backup':
            return ADMIN_FEATURE_BACKUP;
        case 'analytics':
            return ADMIN_FEATURE_ANALYTICS;
        case 'notifications':
            return ADMIN_FEATURE_NOTIFICATIONS;
        case 'bulk_operations':
            return ADMIN_FEATURE_BULK_OPERATIONS;
        case 'search':
            return ADMIN_FEATURE_SEARCH;
        case 'export':
            return ADMIN_FEATURE_EXPORT;
        case 'import':
            return ADMIN_FEATURE_IMPORT;
        default:
            return false;
    }
}

/**
 * Get admin panel info
 */
function getAdminInfo() {
    return [
        'version' => ADMIN_VERSION,
        'name' => ADMIN_NAME,
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'Unknown',
        'request_time' => $_SERVER['REQUEST_TIME'] ?? time(),
        'memory_usage' => memory_get_usage(true),
        'memory_peak' => memory_get_peak_usage(true),
        'loaded_extensions' => get_loaded_extensions(),
        'timezone' => date_default_timezone_get()
    ];
}

/**
 * Get admin permissions
 */
function getAdminPermissions($role = 'admin') {
    $permissions = [
        'super_admin' => [
            'dashboard' => true,
            'blog' => ['view', 'create', 'edit', 'delete', 'publish'],
            'portfolio' => ['view', 'create', 'edit', 'delete'],
            'users' => ['view', 'create', 'edit', 'delete', 'manage_roles'],
            'messages' => ['view', 'reply', 'delete'],
            'content' => ['view', 'create', 'edit', 'delete'],
            'media' => ['view', 'upload', 'delete'],
            'settings' => ['view', 'edit'],
            'logs' => ['view', 'export'],
            'backup' => ['view', 'create', 'restore'],
            'analytics' => ['view', 'export']
        ],
        'admin' => [
            'dashboard' => true,
            'blog' => ['view', 'create', 'edit', 'delete', 'publish'],
            'portfolio' => ['view', 'create', 'edit', 'delete'],
            'users' => ['view', 'create', 'edit'],
            'messages' => ['view', 'reply', 'delete'],
            'content' => ['view', 'create', 'edit', 'delete'],
            'media' => ['view', 'upload', 'delete'],
            'settings' => ['view'],
            'logs' => ['view'],
            'backup' => ['view'],
            'analytics' => ['view']
        ],
        'editor' => [
            'dashboard' => true,
            'blog' => ['view', 'create', 'edit', 'publish'],
            'portfolio' => ['view', 'create', 'edit'],
            'content' => ['view', 'create', 'edit'],
            'media' => ['view', 'upload'],
            'messages' => ['view', 'reply']
        ],
        'author' => [
            'dashboard' => true,
            'blog' => ['view', 'create', 'edit'],
            'content' => ['view', 'create', 'edit'],
            'media' => ['view', 'upload'],
            'messages' => ['view']
        ],
        'viewer' => [
            'dashboard' => true,
            'blog' => ['view'],
            'portfolio' => ['view'],
            'content' => ['view'],
            'media' => ['view'],
            'messages' => ['view']
        ]
    ];
    
    return $permissions[$role] ?? $permissions['viewer'];
}

/**
 * Check admin permission
 */
function checkAdminPermission($action, $resource = null) {
    $role = $_SESSION['admin_role'] ?? 'viewer';
    $permissions = getAdminPermissions($role);
    
    if ($resource === null) {
        return isset($permissions[$action]);
    }
    
    if (!isset($permissions[$resource])) {
        return false;
    }
    
    if (is_bool($permissions[$resource])) {
        return $permissions[$resource];
    }
    
    if (is_array($permissions[$resource])) {
        return in_array($action, $permissions[$resource]);
    }
    
    return false;
}

/**
 * Get admin menu items
 */
function getAdminMenu($role = 'admin') {
    $permissions = getAdminPermissions($role);
    $menu = [];
    
    if ($permissions['dashboard']) {
        $menu[] = [
            'title' => 'Dashboard',
            'url' => 'index.php',
            'icon' => 'fas fa-tachometer-alt',
            'active' => basename($_SERVER['PHP_SELF']) == 'index.php'
        ];
    }
    
    if (isset($permissions['blog'])) {
        $menu[] = [
            'title' => 'Blog Yönetimi',
            'url' => 'blog.php',
            'icon' => 'fas fa-blog',
            'active' => in_array(basename($_SERVER['PHP_SELF']), ['blog.php', 'blog-add.php', 'blog-edit.php'])
        ];
    }
    
    if (isset($permissions['portfolio'])) {
        $menu[] = [
            'title' => 'Portfolio',
            'url' => 'portfolio.php',
            'icon' => 'fas fa-briefcase',
            'active' => in_array(basename($_SERVER['PHP_SELF']), ['portfolio.php', 'portfolio-add.php', 'portfolio-edit.php'])
        ];
    }
    
    if (isset($permissions['messages'])) {
        $menu[] = [
            'title' => 'Mesajlar',
            'url' => 'messages.php',
            'icon' => 'fas fa-envelope',
            'active' => basename($_SERVER['PHP_SELF']) == 'messages.php'
        ];
    }
    
    if (isset($permissions['users'])) {
        $menu[] = [
            'title' => 'Kullanıcılar',
            'url' => 'users.php',
            'icon' => 'fas fa-users',
            'active' => in_array(basename($_SERVER['PHP_SELF']), ['users.php', 'user-add.php', 'user-edit.php', 'user-roles.php'])
        ];
    }
    
    if (isset($permissions['content'])) {
        $menu[] = [
            'title' => 'İçerik Yönetimi',
            'url' => 'content.php',
            'icon' => 'fas fa-file-alt',
            'active' => basename($_SERVER['PHP_SELF']) == 'content.php'
        ];
    }
    
    if (isset($permissions['media'])) {
        $menu[] = [
            'title' => 'Medya Yönetimi',
            'url' => 'media.php',
            'icon' => 'fas fa-images',
            'active' => basename($_SERVER['PHP_SELF']) == 'media.php'
        ];
    }
    
    if (isset($permissions['logs'])) {
        $menu[] = [
            'title' => 'Audit Logs',
            'url' => 'audit-logs.php',
            'icon' => 'fas fa-clipboard-list',
            'active' => basename($_SERVER['PHP_SELF']) == 'audit-logs.php'
        ];
    }
    
    return $menu;
}
?>
