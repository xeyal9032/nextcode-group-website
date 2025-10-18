<?php
/**
 * Admin Panel - Site Settings
 * NextCode Group - Advanced Settings Management
 */

define('ADMIN_ACCESS', true);

// Include configurations
require_once '../config/database.php';
require_once '../config/security.php';

// Check authentication
if (!$adminSecurity->checkAuth()) {
    header('Location: ../login.php');
    exit();
}

// Check permissions
if (!$adminSecurity->hasPermission('settings_read')) {
    die('Access denied: Insufficient permissions');
}

$current_user = [
    'id' => $_SESSION['admin_user_id'],
    'username' => $_SESSION['admin_username'],
    'role' => $_SESSION['admin_role']
];

$message = '';
$error = '';

// Handle settings update
if ($_POST && isset($_POST['action'])) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!$adminSecurity->validateCSRFToken($csrf_token)) {
        $error = 'Security error. Please try again.';
    } else {
        $action = $_POST['action'];
        
        switch ($action) {
            case 'update_settings':
                if ($adminSecurity->hasPermission('settings_write')) {
                    $result = updateSettings();
                    if ($result['success']) {
                        $message = $result['message'];
                    } else {
                        $error = $result['message'];
                    }
                } else {
                    $error = 'Settings write permission denied';
                }
                break;
        }
    }
}

// Get current settings
$site_settings = getSiteSettings();

// Generate CSRF token
$csrf_token = $adminSecurity->generateCSRFToken();

function updateSettings() {
    global $pdo, $adminSecurity, $current_user;
    
    $settings = $_POST['settings'] ?? [];
    
    if (empty($settings)) {
        return ['success' => false, 'message' => 'No settings to update'];
    }
    
    try {
        $updated_count = 0;
        
        foreach ($settings as $key => $value) {
            // Sanitize value based on setting type
            $sanitized_value = sanitizeSettingValue($key, $value);
            
            $stmt = $pdo->prepare("
                INSERT INTO site_settings (setting_key, setting_value, updated_at) 
                VALUES (?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value), 
                updated_at = NOW()
            ");
            
            if ($stmt->execute([$key, $sanitized_value])) {
                $updated_count++;
            }
        }
        
        // Log activity
        $adminSecurity->logActivity($current_user['id'], 'settings_update', "Updated $updated_count settings");
        
        return ['success' => true, 'message' => "$updated_count settings updated successfully"];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

function sanitizeSettingValue($key, $value) {
    // Basic sanitization based on setting key
    switch ($key) {
        case 'site_email':
        case 'contact_email':
            return filter_var($value, FILTER_SANITIZE_EMAIL);
            
        case 'site_url':
        case 'logo_url':
        case 'favicon_url':
            return filter_var($value, FILTER_SANITIZE_URL);
            
        case 'maintenance_mode':
        case 'user_registration':
        case 'email_notifications':
            return $value ? '1' : '0';
            
        default:
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}

function getSiteSettings() {
    global $pdo;
    
    $settings = [
        // General Settings
        'site_name' => 'NextCode Group',
        'site_description' => 'Professional digital marketing services',
        'site_keywords' => 'digital marketing, SEO, web development',
        'site_url' => 'https://nextcode.com',
        'site_email' => 'info@nextcode.com',
        'admin_email' => 'admin@nextcode.com',
        
        // Contact Information
        'contact_phone' => '+90 555 123 4567',
        'contact_address' => 'Istanbul, Turkey',
        'contact_working_hours' => 'Mon-Fri: 9:00-18:00',
        
        // Social Media
        'social_facebook' => 'https://facebook.com/nextcode',
        'social_twitter' => 'https://twitter.com/nextcode',
        'social_instagram' => 'https://instagram.com/nextcode',
        'social_linkedin' => 'https://linkedin.com/company/nextcode',
        
        // SEO & Analytics
        'google_analytics' => '',
        'google_tag_manager' => '',
        'meta_title' => 'NextCode Group - Digital Marketing Agency',
        'meta_description' => 'Professional digital marketing services',
        
        // Design & Branding
        'logo_url' => '/assets/images/logo.png',
        'favicon_url' => '/favicon.svg',
        'theme_color' => '#667eea',
        'secondary_color' => '#764ba2',
        
        // System Settings
        'maintenance_mode' => '0',
        'user_registration' => '1',
        'email_notifications' => '1',
        'cache_enabled' => '1',
        'debug_mode' => '0'
    ];
    
    if (!$pdo) {
        return $settings;
    }
    
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
        $db_settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Merge with defaults
        return array_merge($settings, $db_settings);
    } catch (PDOException $e) {
        error_log('Settings query error: ' . $e->getMessage());
        return $settings;
    }
}

// Log page access
$adminSecurity->logActivity($current_user['id'], 'page_access', 'Site Settings accessed');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <style>
        .settings-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .settings-tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2rem;
            overflow-x: auto;
        }
        
        .settings-tab {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-secondary);
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
            border-bottom: 2px solid transparent;
        }
        
        .settings-tab:hover {
            color: var(--text-primary);
        }
        
        .settings-tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .settings-panel {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .settings-panel.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .settings-form {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section:last-child {
            margin-bottom: 0;
        }
        
        .form-section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-light);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-help {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }
        
        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-checkbox input[type="checkbox"] {
            width: auto;
        }
        
        .color-input-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .color-preview {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            cursor: pointer;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            background: var(--bg-primary);
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-weight: 500;
        }
        
        .btn:hover {
            background: var(--bg-secondary);
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: var(--text-light);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-success {
            background: var(--success-color);
            color: var(--text-light);
            border-color: var(--success-color);
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-light);
        }
        
        .alert {
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 2rem;
            border: 1px solid;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
        }
        
        .settings-preview {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .preview-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }
        
        .preview-content {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body data-page="settings">
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
                        <li>
                            <a href="../index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="file-manager.php">
                                <i class="fas fa-folder-open"></i>
                                <span>Dosya Yöneticisi</span>
                            </a>
                        </li>
                        <li>
                            <a href="code-editor.php">
                                <i class="fas fa-code"></i>
                                <span>Kod Editörü</span>
                            </a>
                        </li>
                        <li>
                            <a href="database.php">
                                <i class="fas fa-database"></i>
                                <span>Veritabanı</span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="settings.php">
                                <i class="fas fa-cog"></i>
                                <span>Site Ayarları</span>
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
                    <h1>Site Ayarları</h1>
                </div>
                
                <div class="top-bar-right">
                    <div class="user-menu">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($current_user['username']); ?></span>
                            <span class="user-role"><?php echo ucfirst($current_user['role']); ?></span>
                        </div>
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="dropdown-menu">
                            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                <div class="settings-container">
                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Settings Tabs -->
                    <div class="settings-tabs">
                        <button class="settings-tab active" onclick="showTab('general')">
                            <i class="fas fa-cog"></i> Genel
                        </button>
                        <button class="settings-tab" onclick="showTab('contact')">
                            <i class="fas fa-address-book"></i> İletişim
                        </button>
                        <button class="settings-tab" onclick="showTab('social')">
                            <i class="fas fa-share-alt"></i> Sosyal Medya
                        </button>
                        <button class="settings-tab" onclick="showTab('seo')">
                            <i class="fas fa-search"></i> SEO
                        </button>
                        <button class="settings-tab" onclick="showTab('design')">
                            <i class="fas fa-palette"></i> Tasarım
                        </button>
                        <button class="settings-tab" onclick="showTab('system')">
                            <i class="fas fa-server"></i> Sistem
                        </button>
                    </div>
                    
                    <!-- Settings Form -->
                    <form method="POST" class="settings-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="action" value="update_settings">
                        
                        <!-- General Settings -->
                        <div class="settings-panel active" id="general">
                            <div class="form-section">
                                <h3 class="form-section-title">Site Bilgileri</h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label" for="site_name">Site Adı</label>
                                        <input type="text" id="site_name" name="settings[site_name]" class="form-input" 
                                               value="<?php echo htmlspecialchars($site_settings['site_name']); ?>">
                                        <div class="form-help">Sitenizin ana başlığı</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label" for="site_url">Site URL</label>
                                        <input type="url" id="site_url" name="settings[site_url]" class="form-input" 
                                               value="<?php echo htmlspecialchars($site_settings['site_url']); ?>">
                                        <div class="form-help">https:// ile başlamalı</div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="site_description">Site Açıklaması</label>
                                    <textarea id="site_description" name="settings[site_description]" class="form-textarea"><?php echo htmlspecialchars($site_settings['site_description']); ?></textarea>
                                    <div class="form-help">Site hakkında kısa açıklama</div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="site_keywords">Anahtar Kelimeler</label>
                                    <input type="text" id="site_keywords" name="settings[site_keywords]" class="form-input" 
                                           value="<?php echo htmlspecialchars($site_settings['site_keywords']); ?>">
                                    <div class="form-help">Virgülle ayırarak yazın</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Settings -->
                        <div class="settings-panel" id="contact">
                            <div class="form-section">
                                <h3 class="form-section-title">İletişim Bilgileri</h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label" for="site_email">Site E-mail</label>
                                        <input type="email" id="site_email" name="settings[site_email]" class="form-input" 
                                               value="<?php echo htmlspecialchars($site_settings['site_email']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label" for="contact_phone">Telefon</label>
                                        <input type="tel" id="contact_phone" name="settings[contact_phone]" class="form-input" 
                                               value="<?php echo htmlspecialchars($site_settings['contact_phone']); ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="contact_address">Adres</label>
                                    <textarea id="contact_address" name="settings[contact_address]" class="form-textarea"><?php echo htmlspecialchars($site_settings['contact_address']); ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="contact_working_hours">Çalışma Saatleri</label>
                                    <input type="text" id="contact_working_hours" name="settings[contact_working_hours]" class="form-input" 
                                           value="<?php echo htmlspecialchars($site_settings['contact_working_hours']); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Social Media Settings -->
                        <div class="settings-panel" id="social">
                            <div class="form-section">
                                <h3 class="form-section-title">Sosyal Medya Hesapları</h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label" for="social_facebook">Facebook</label>
                                        <input type="url" id="social_facebook" name="settings[social_facebook]" class="form-input" 
                                               value="<?php echo htmlspecialchars($site_settings['social_facebook']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label" for="social_twitter">Twitter</label>
                                        <input type="url" id="social_twitter" name="settings[social_twitter]" class="form-input" 
                                               value="<?php echo htmlspecialchars($site_settings['social_twitter']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label" for="social_instagram">Instagram</label>
                                        <input type="url" id="social_instagram" name="settings[social_instagram]" class="form-input" 
                                               value="<?php echo htmlspecialchars($site_settings['social_instagram']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label" for="social_linkedin">LinkedIn</label>
                                        <input type="url" id="social_linkedin" name="settings[social_linkedin]" class="form-input" 
                                               value="<?php echo htmlspecialchars($site_settings['social_linkedin']); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SEO Settings -->
                        <div class="settings-panel" id="seo">
                            <div class="form-section">
                                <h3 class="form-section-title">SEO ve Analitik</h3>
                                <div class="form-group">
                                    <label class="form-label" for="meta_title">Meta Title</label>
                                    <input type="text" id="meta_title" name="settings[meta_title]" class="form-input" 
                                           value="<?php echo htmlspecialchars($site_settings['meta_title']); ?>">
                                    <div class="form-help">60 karakter önerilir</div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="meta_description">Meta Description</label>
                                    <textarea id="meta_description" name="settings[meta_description]" class="form-textarea"><?php echo htmlspecialchars($site_settings['meta_description']); ?></textarea>
                                    <div class="form-help">160 karakter önerilir</div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="google_analytics">Google Analytics ID</label>
                                    <input type="text" id="google_analytics" name="settings[google_analytics]" class="form-input" 
                                           value="<?php echo htmlspecialchars($site_settings['google_analytics']); ?>"
                                           placeholder="G-XXXXXXXXXX">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Design Settings -->
                        <div class="settings-panel" id="design">
                            <div class="form-section">
                                <h3 class="form-section-title">Tasarım ve Branding</h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label" for="theme_color">Ana Renk</label>
                                        <div class="color-input-group">
                                            <input type="color" id="theme_color" name="settings[theme_color]" 
                                                   value="<?php echo htmlspecialchars($site_settings['theme_color']); ?>"
                                                   class="color-preview">
                                            <input type="text" class="form-input" 
                                                   value="<?php echo htmlspecialchars($site_settings['theme_color']); ?>"
                                                   onchange="document.getElementById('theme_color').value = this.value">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label" for="secondary_color">İkincil Renk</label>
                                        <div class="color-input-group">
                                            <input type="color" id="secondary_color" name="settings[secondary_color]" 
                                                   value="<?php echo htmlspecialchars($site_settings['secondary_color']); ?>"
                                                   class="color-preview">
                                            <input type="text" class="form-input" 
                                                   value="<?php echo htmlspecialchars($site_settings['secondary_color']); ?>"
                                                   onchange="document.getElementById('secondary_color').value = this.value">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label" for="logo_url">Logo URL</label>
                                        <input type="url" id="logo_url" name="settings[logo_url]" class="form-input" 
                                               value="<?php echo htmlspecialchars($site_settings['logo_url']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label" for="favicon_url">Favicon URL</label>
                                        <input type="url" id="favicon_url" name="settings[favicon_url]" class="form-input" 
                                               value="<?php echo htmlspecialchars($site_settings['favicon_url']); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- System Settings -->
                        <div class="settings-panel" id="system">
                            <div class="form-section">
                                <h3 class="form-section-title">Sistem Ayarları</h3>
                                <div class="form-group">
                                    <label class="form-checkbox">
                                        <input type="checkbox" name="settings[maintenance_mode]" value="1" 
                                               <?php echo $site_settings['maintenance_mode'] ? 'checked' : ''; ?>>
                                        <span>Bakım Modu</span>
                                    </label>
                                    <div class="form-help">Aktif olduğunda site ziyaretçilere kapalı olur</div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-checkbox">
                                        <input type="checkbox" name="settings[user_registration]" value="1" 
                                               <?php echo $site_settings['user_registration'] ? 'checked' : ''; ?>>
                                        <span>Kullanıcı Kaydı</span>
                                    </label>
                                    <div class="form-help">Yeni kullanıcıların kayıt olmasına izin ver</div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-checkbox">
                                        <input type="checkbox" name="settings[email_notifications]" value="1" 
                                               <?php echo $site_settings['email_notifications'] ? 'checked' : ''; ?>>
                                        <span>E-mail Bildirimleri</span>
                                    </label>
                                    <div class="form-help">Sistem e-mail bildirimlerini gönder</div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-checkbox">
                                        <input type="checkbox" name="settings[cache_enabled]" value="1" 
                                               <?php echo $site_settings['cache_enabled'] ? 'checked' : ''; ?>>
                                        <span>Önbellek Sistemi</span>
                                    </label>
                                    <div class="form-help">Performans için önbellek kullan</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn" onclick="resetForm()">
                                <i class="fas fa-undo"></i>
                                Sıfırla
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        function showTab(tabName) {
            // Hide all panels
            document.querySelectorAll('.settings-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.settings-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected panel
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to selected tab
            event.target.classList.add('active');
        }
        
        function resetForm() {
            if (confirm('Tüm değişiklikleri geri almak istediğinizden emin misiniz?')) {
                location.reload();
            }
        }
        
        // Color input synchronization
        document.addEventListener('DOMContentLoaded', function() {
            const colorInputs = document.querySelectorAll('input[type="color"]');
            
            colorInputs.forEach(colorInput => {
                const textInput = colorInput.parentNode.querySelector('input[type="text"]');
                
                colorInput.addEventListener('change', function() {
                    textInput.value = this.value;
                });
                
                textInput.addEventListener('change', function() {
                    if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                        colorInput.value = this.value;
                    }
                });
            });
        });
        
        // Form validation
        document.querySelector('.settings-form').addEventListener('submit', function(e) {
            const requiredFields = ['site_name', 'site_url', 'site_email'];
            let isValid = true;
            
            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field && !field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--danger-color)';
                    field.focus();
                } else if (field) {
                    field.style.borderColor = 'var(--border-color)';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Lütfen gerekli alanları doldurun.');
            }
        });
        
        // Auto-save functionality (optional)
        let autoSaveTimeout;
        
        function autoSave() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                // You can implement auto-save here
                console.log('Auto-save triggered');
            }, 5000); // 5 seconds after last change
        }
        
        // Attach auto-save to form inputs
        document.querySelectorAll('.form-input, .form-textarea, .form-select').forEach(input => {
            input.addEventListener('input', autoSave);
        });
    </script>
</body>
</html>
