<?php
/**
 * Admin Panel - Code Editor
 * NextCode Group - Advanced Code Editor with Syntax Highlighting
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
if (!$adminSecurity->hasPermission('file_write')) {
    die('Access denied: Insufficient permissions');
}

$current_user = [
    'id' => $_SESSION['admin_user_id'],
    'username' => $_SESSION['admin_username'],
    'role' => $_SESSION['admin_role']
];

// Handle file operations
$message = '';
$error = '';
$current_file = '';
$file_content = '';

if ($_POST) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!$adminSecurity->validateCSRFToken($csrf_token)) {
        $error = 'Security error. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'save':
                $result = saveFile();
                if ($result['success']) {
                    $message = $result['message'];
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'create':
                $result = createFile();
                if ($result['success']) {
                    $message = $result['message'];
                    $current_file = $_POST['file_path'];
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Load file if specified
if (isset($_GET['file'])) {
    $current_file = $_GET['file'];
    $file_content = loadFile($current_file);
}

// Get file list for sidebar
$project_files = getProjectFiles();

// Generate CSRF token
$csrf_token = $adminSecurity->generateCSRFToken();

function saveFile() {
    global $adminSecurity, $current_user;
    
    $file_path = $_POST['file_path'] ?? '';
    $content = $_POST['content'] ?? '';
    
    if (empty($file_path)) {
        return ['success' => false, 'message' => 'No file specified'];
    }
    
    // Security check: prevent directory traversal
    $safe_path = realpath('../../' . $file_path);
    $base_path = realpath('../../');
    
    if (!$safe_path) {
        $safe_path = $base_path . '/' . ltrim($file_path, '/');
    }
    
    if (strpos($safe_path, $base_path) !== 0) {
        return ['success' => false, 'message' => 'Invalid file path'];
    }
    
    // Create directory if it doesn't exist
    $dir = dirname($safe_path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Backup existing file
    if (file_exists($safe_path)) {
        $backup_path = $safe_path . '.backup.' . time();
        copy($safe_path, $backup_path);
    }
    
    if (file_put_contents($safe_path, $content) !== false) {
        // Log activity
        $adminSecurity->logActivity($current_user['id'], 'file_edit', "Edited: $file_path");
        return ['success' => true, 'message' => 'File saved successfully'];
    } else {
        return ['success' => false, 'message' => 'Failed to save file'];
    }
}

function createFile() {
    global $adminSecurity, $current_user;
    
    $file_path = $_POST['file_path'] ?? '';
    $file_type = $_POST['file_type'] ?? 'php';
    
    if (empty($file_path)) {
        return ['success' => false, 'message' => 'File path required'];
    }
    
    // Add extension if not provided
    if (pathinfo($file_path, PATHINFO_EXTENSION) === '') {
        $file_path .= '.' . $file_type;
    }
    
    $safe_path = '../../' . ltrim($file_path, '/');
    
    if (file_exists($safe_path)) {
        return ['success' => false, 'message' => 'File already exists'];
    }
    
    // Create directory if needed
    $dir = dirname($safe_path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Create file with template content
    $template_content = getFileTemplate($file_type);
    
    if (file_put_contents($safe_path, $template_content) !== false) {
        // Log activity
        $adminSecurity->logActivity($current_user['id'], 'file_create', "Created: $file_path");
        return ['success' => true, 'message' => 'File created successfully'];
    } else {
        return ['success' => false, 'message' => 'Failed to create file'];
    }
}

function loadFile($file_path) {
    if (empty($file_path)) {
        return '';
    }
    
    $safe_path = realpath('../../' . $file_path);
    $base_path = realpath('../../');
    
    if (!$safe_path || strpos($safe_path, $base_path) !== 0) {
        return '';
    }
    
    if (file_exists($safe_path) && is_readable($safe_path)) {
        return file_get_contents($safe_path);
    }
    
    return '';
}

function getProjectFiles($dir = '../../', $prefix = '') {
    $files = [];
    $exclude_dirs = ['.git', 'node_modules', 'vendor', 'cache', 'logs', 'tmp'];
    $exclude_files = ['.DS_Store', 'Thumbs.db', '.gitignore'];
    
    if (!is_dir($dir)) {
        return $files;
    }
    
    $items = scandir($dir);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $item_path = $dir . '/' . $item;
        $relative_path = $prefix . $item;
        
        if (in_array($item, $exclude_dirs) || in_array($item, $exclude_files)) {
            continue;
        }
        
        if (is_dir($item_path)) {
            $files[] = [
                'name' => $item,
                'path' => $relative_path,
                'type' => 'folder',
                'children' => getProjectFiles($item_path, $relative_path . '/')
            ];
        } else {
            $extension = pathinfo($item, PATHINFO_EXTENSION);
            if (in_array($extension, ['php', 'html', 'css', 'js', 'json', 'txt', 'md', 'sql'])) {
                $files[] = [
                    'name' => $item,
                    'path' => $relative_path,
                    'type' => 'file',
                    'extension' => $extension
                ];
            }
        }
    }
    
    return $files;
}

function getFileTemplate($type) {
    $templates = [
        'php' => "<?php\n/**\n * New PHP File\n * Created: " . date('Y-m-d H:i:s') . "\n */\n\n",
        'html' => "<!DOCTYPE html>\n<html lang=\"tr\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>New Page</title>\n</head>\n<body>\n    \n</body>\n</html>",
        'css' => "/**\n * New CSS File\n * Created: " . date('Y-m-d H:i:s') . "\n */\n\n",
        'js' => "/**\n * New JavaScript File\n * Created: " . date('Y-m-d H:i:s') . "\n */\n\n",
        'json' => "{\n    \n}",
        'txt' => "",
        'md' => "# New Document\n\nCreated: " . date('Y-m-d H:i:s') . "\n\n"
    ];
    
    return $templates[$type] ?? '';
}

function getFileIcon($extension) {
    $icons = [
        'php' => 'fab fa-php',
        'html' => 'fab fa-html5',
        'css' => 'fab fa-css3-alt',
        'js' => 'fab fa-js-square',
        'json' => 'fas fa-code',
        'txt' => 'fas fa-file-alt',
        'md' => 'fab fa-markdown',
        'sql' => 'fas fa-database'
    ];
    
    return $icons[$extension] ?? 'fas fa-file';
}

function renderFileTree($files, $level = 0) {
    $html = '';
    
    foreach ($files as $file) {
        $indent = str_repeat('  ', $level);
        
        if ($file['type'] === 'folder') {
            $html .= "<div class=\"file-tree-folder\" style=\"margin-left: {$level}rem;\">";
            $html .= "<div class=\"folder-header\" onclick=\"toggleFolder(this)\">";
            $html .= "<i class=\"fas fa-chevron-right folder-toggle\"></i>";
            $html .= "<i class=\"fas fa-folder folder-icon\"></i>";
            $html .= "<span>" . htmlspecialchars($file['name']) . "</span>";
            $html .= "</div>";
            
            if (!empty($file['children'])) {
                $html .= "<div class=\"folder-content\" style=\"display: none;\">";
                $html .= renderFileTree($file['children'], $level + 1);
                $html .= "</div>";
            }
            
            $html .= "</div>";
        } else {
            $html .= "<div class=\"file-tree-file\" style=\"margin-left: {$level}rem;\" onclick=\"loadFile('{$file['path']}')\">";
            $html .= "<i class=\"" . getFileIcon($file['extension']) . " file-icon\"></i>";
            $html .= "<span>" . htmlspecialchars($file['name']) . "</span>";
            $html .= "</div>";
        }
    }
    
    return $html;
}

// Log page access
$adminSecurity->logActivity($current_user['id'], 'page_access', 'Code Editor accessed');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Editor - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/material.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <style>
        .code-editor-layout {
            display: flex;
            height: calc(100vh - var(--topbar-height));
        }
        
        .file-sidebar {
            width: 300px;
            background: var(--bg-primary);
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
            flex-shrink: 0;
        }
        
        .file-sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-secondary);
        }
        
        .file-sidebar-header h3 {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        .file-tree {
            padding: 1rem 0;
        }
        
        .file-tree-folder,
        .file-tree-file {
            padding: 0.25rem 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .file-tree-file:hover,
        .folder-header:hover {
            background: var(--bg-secondary);
        }
        
        .file-tree-file.active {
            background: var(--primary-color);
            color: var(--text-light);
        }
        
        .folder-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 1rem;
            cursor: pointer;
        }
        
        .folder-toggle {
            transition: transform 0.2s ease;
            font-size: 0.75rem;
        }
        
        .folder-toggle.expanded {
            transform: rotate(90deg);
        }
        
        .folder-icon,
        .file-icon {
            width: 1rem;
            text-align: center;
            font-size: 0.875rem;
        }
        
        .folder-icon {
            color: #f59e0b;
        }
        
        .editor-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .editor-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .editor-file-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        
        .editor-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
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
        
        .editor-container {
            flex: 1;
            overflow: hidden;
            position: relative;
        }
        
        .CodeMirror {
            height: 100% !important;
            font-family: 'JetBrains Mono', 'Courier New', monospace !important;
            font-size: 14px !important;
        }
        
        .no-file-selected {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-muted);
            text-align: center;
        }
        
        .no-file-selected i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            max-width: 500px;
            width: 90%;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-muted);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-input,
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 0.875rem;
        }
        
        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .alert {
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
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
        
        .editor-status {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg-dark);
            color: var(--text-light);
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            display: flex;
            justify-content: space-between;
        }
        
        @media (max-width: 768px) {
            .code-editor-layout {
                flex-direction: column;
            }
            
            .file-sidebar {
                width: 100%;
                height: 200px;
            }
            
            .editor-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body data-page="code-editor">
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
                        <li class="active">
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
                    <h1>Kod Editörü</h1>
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

            <!-- Code Editor Layout -->
            <div class="code-editor-layout">
                <!-- File Sidebar -->
                <div class="file-sidebar">
                    <div class="file-sidebar-header">
                        <h3>Proje Dosyaları</h3>
                        <button class="btn btn-primary" onclick="showCreateFileModal()">
                            <i class="fas fa-plus"></i>
                            Yeni Dosya
                        </button>
                    </div>
                    
                    <div class="file-tree">
                        <?php echo renderFileTree($project_files); ?>
                    </div>
                </div>
                
                <!-- Editor Main -->
                <div class="editor-main">
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
                    
                    <!-- Editor Toolbar -->
                    <div class="editor-toolbar">
                        <div class="editor-file-info">
                            <?php if ($current_file): ?>
                                <i class="<?php echo getFileIcon(pathinfo($current_file, PATHINFO_EXTENSION)); ?>"></i>
                                <span><?php echo htmlspecialchars($current_file); ?></span>
                            <?php else: ?>
                                <span>Dosya seçilmedi</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="editor-actions">
                            <button class="btn btn-success" onclick="saveCurrentFile()" <?php echo !$current_file ? 'disabled' : ''; ?>>
                                <i class="fas fa-save"></i>
                                Kaydet (Ctrl+S)
                            </button>
                            
                            <button class="btn" onclick="showCreateFileModal()">
                                <i class="fas fa-plus"></i>
                                Yeni
                            </button>
                            
                            <select id="editorTheme" onchange="changeEditorTheme()">
                                <option value="default">Varsayılan</option>
                                <option value="monokai">Monokai</option>
                                <option value="material">Material</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Editor Container -->
                    <div class="editor-container">
                        <?php if ($current_file): ?>
                            <textarea id="codeEditor"><?php echo htmlspecialchars($file_content); ?></textarea>
                        <?php else: ?>
                            <div class="no-file-selected">
                                <i class="fas fa-code"></i>
                                <h3>Dosya Seçilmedi</h3>
                                <p>Düzenlemek için sol panelden bir dosya seçin</p>
                                <button class="btn btn-primary" onclick="showCreateFileModal()">
                                    <i class="fas fa-plus"></i>
                                    Yeni Dosya Oluştur
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Editor Status -->
                    <?php if ($current_file): ?>
                        <div class="editor-status">
                            <div>
                                <span id="editorMode"><?php echo strtoupper(pathinfo($current_file, PATHINFO_EXTENSION)); ?></span>
                                <span id="editorPosition">Satır 1, Sütun 1</span>
                            </div>
                            <div>
                                <span id="editorEncoding">UTF-8</span>
                                <span id="editorModified"></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Create File Modal -->
    <div class="modal" id="createFileModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Yeni Dosya Oluştur</h3>
                <button class="modal-close" onclick="closeModal('createFileModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label class="form-label" for="filePath">Dosya Yolu</label>
                    <input type="text" name="file_path" id="filePath" class="form-input" 
                           placeholder="örn: includes/new-file.php" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="fileType">Dosya Türü</label>
                    <select name="file_type" id="fileType" class="form-select">
                        <option value="php">PHP</option>
                        <option value="html">HTML</option>
                        <option value="css">CSS</option>
                        <option value="js">JavaScript</option>
                        <option value="json">JSON</option>
                        <option value="txt">Text</option>
                        <option value="md">Markdown</option>
                        <option value="sql">SQL</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Save Form (Hidden) -->
    <form id="saveForm" method="POST" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($current_file); ?>">
        <textarea name="content" id="saveContent"></textarea>
    </form>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/php/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/sql/sql.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/markdown/markdown.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/matchbrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closebrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/foldcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/foldgutter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/brace-fold.min.js"></script>
    <script src="../js/admin.js"></script>
    <script>
        let editor = null;
        let isModified = false;
        
        // Initialize CodeMirror editor
        <?php if ($current_file): ?>
        document.addEventListener('DOMContentLoaded', function() {
            initializeEditor();
        });
        <?php endif; ?>
        
        function initializeEditor() {
            const textarea = document.getElementById('codeEditor');
            if (!textarea) return;
            
            const extension = '<?php echo pathinfo($current_file, PATHINFO_EXTENSION); ?>';
            const mode = getEditorMode(extension);
            
            editor = CodeMirror.fromTextArea(textarea, {
                lineNumbers: true,
                mode: mode,
                theme: 'default',
                indentUnit: 4,
                smartIndent: true,
                matchBrackets: true,
                autoCloseBrackets: true,
                foldGutter: true,
                gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                extraKeys: {
                    "Ctrl-S": function(cm) {
                        saveCurrentFile();
                    },
                    "F11": function(cm) {
                        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                    },
                    "Esc": function(cm) {
                        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                    }
                }
            });
            
            // Track changes
            editor.on('change', function() {
                isModified = true;
                updateStatus();
            });
            
            // Update cursor position
            editor.on('cursorActivity', function() {
                updateCursorPosition();
            });
            
            updateStatus();
            updateCursorPosition();
        }
        
        function getEditorMode(extension) {
            const modes = {
                'php': 'application/x-httpd-php',
                'html': 'htmlmixed',
                'css': 'css',
                'js': 'javascript',
                'json': 'application/json',
                'sql': 'sql',
                'md': 'markdown',
                'txt': 'text/plain'
            };
            
            return modes[extension] || 'text/plain';
        }
        
        function updateStatus() {
            const statusModified = document.getElementById('editorModified');
            if (statusModified) {
                statusModified.textContent = isModified ? '● Değiştirildi' : '';
            }
        }
        
        function updateCursorPosition() {
            if (!editor) return;
            
            const cursor = editor.getCursor();
            const positionElement = document.getElementById('editorPosition');
            if (positionElement) {
                positionElement.textContent = `Satır ${cursor.line + 1}, Sütun ${cursor.ch + 1}`;
            }
        }
        
        function saveCurrentFile() {
            if (!editor || !isModified) return;
            
            const content = editor.getValue();
            document.getElementById('saveContent').value = content;
            document.getElementById('saveForm').submit();
        }
        
        function loadFile(filePath) {
            if (isModified && !confirm('Kaydedilmemiş değişiklikler var. Devam etmek istiyor musunuz?')) {
                return;
            }
            
            window.location.href = '?file=' + encodeURIComponent(filePath);
        }
        
        function changeEditorTheme() {
            if (!editor) return;
            
            const theme = document.getElementById('editorTheme').value;
            editor.setOption('theme', theme);
        }
        
        function toggleFolder(element) {
            const toggle = element.querySelector('.folder-toggle');
            const content = element.parentNode.querySelector('.folder-content');
            
            if (content.style.display === 'none') {
                content.style.display = 'block';
                toggle.classList.add('expanded');
            } else {
                content.style.display = 'none';
                toggle.classList.remove('expanded');
            }
        }
        
        function showCreateFileModal() {
            document.getElementById('createFileModal').classList.add('active');
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                saveCurrentFile();
            }
        });
        
        // Warn before leaving with unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (isModified) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
        
        // File tree active state
        document.addEventListener('DOMContentLoaded', function() {
            const currentFile = '<?php echo $current_file; ?>';
            if (currentFile) {
                const fileElements = document.querySelectorAll('.file-tree-file');
                fileElements.forEach(element => {
                    if (element.textContent.trim() === currentFile.split('/').pop()) {
                        element.classList.add('active');
                    }
                });
            }
        });
        
        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal.active').forEach(modal => {
                    modal.classList.remove('active');
                });
            }
        });
        
        // Close modals when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
