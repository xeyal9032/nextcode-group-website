<?php
/**
 * Admin Panel - File Manager
 * NextCode Group - Advanced File Management System
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
if (!$adminSecurity->hasPermission('file_read')) {
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

if ($_POST) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!$adminSecurity->validateCSRFToken($csrf_token)) {
        $error = 'Security error. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'upload':
                $result = handleFileUpload();
                if ($result['success']) {
                    $message = $result['message'];
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'delete':
                $result = handleFileDelete();
                if ($result['success']) {
                    $message = $result['message'];
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'create_folder':
                $result = handleCreateFolder();
                if ($result['success']) {
                    $message = $result['message'];
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Get current directory
$current_dir = $_GET['dir'] ?? '/';
$current_dir = '/' . trim($current_dir, '/');
if ($current_dir === '/') {
    $current_dir = '';
}

// Get file list
$files = getFileList($current_dir);
$breadcrumbs = generateBreadcrumbs($current_dir);

// Generate CSRF token
$csrf_token = $adminSecurity->generateCSRFToken();

function handleFileUpload() {
    global $adminSecurity, $current_user, $pdo;
    
    if (!$adminSecurity->hasPermission('file_upload')) {
        return ['success' => false, 'message' => 'Upload permission denied'];
    }
    
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error'];
    }
    
    $file = $_FILES['file'];
    $target_dir = $_POST['target_dir'] ?? '';
    
    // Validate file
    $validation_errors = $adminSecurity->validateFileUpload($file);
    if (!empty($validation_errors)) {
        return ['success' => false, 'message' => implode(', ', $validation_errors)];
    }
    
    // Create upload directory if it doesn't exist
    $upload_path = '../../' . trim($target_dir, '/');
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    
    // Generate safe filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = pathinfo($file['name'], PATHINFO_FILENAME);
    $safe_filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename) . '.' . $extension;
    
    // Check if file exists and create unique name
    $counter = 1;
    $original_filename = $safe_filename;
    while (file_exists($upload_path . '/' . $safe_filename)) {
        $safe_filename = pathinfo($original_filename, PATHINFO_FILENAME) . '_' . $counter . '.' . $extension;
        $counter++;
    }
    
    $target_file = $upload_path . '/' . $safe_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        // Log to database
        try {
            if ($pdo) {
                $stmt = $pdo->prepare("
                    INSERT INTO admin_files (filename, original_name, file_path, file_size, mime_type, file_type, uploaded_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $file_type = getFileType($file['type']);
                $relative_path = $target_dir . '/' . $safe_filename;
                
                $stmt->execute([
                    $safe_filename,
                    $file['name'],
                    $relative_path,
                    $file['size'],
                    $file['type'],
                    $file_type,
                    $current_user['id']
                ]);
            }
        } catch (PDOException $e) {
            error_log('File upload database error: ' . $e->getMessage());
        }
        
        // Log activity
        global $adminSecurity;
        $adminSecurity->logActivity($current_user['id'], 'file_upload', "Uploaded: $safe_filename", $target_dir);
        
        return ['success' => true, 'message' => "File uploaded successfully: $safe_filename"];
    } else {
        return ['success' => false, 'message' => 'Failed to upload file'];
    }
}

function handleFileDelete() {
    global $adminSecurity, $current_user;
    
    if (!$adminSecurity->hasPermission('file_delete')) {
        return ['success' => false, 'message' => 'Delete permission denied'];
    }
    
    $file_path = $_POST['file_path'] ?? '';
    if (empty($file_path)) {
        return ['success' => false, 'message' => 'No file specified'];
    }
    
    // Security check: prevent directory traversal
    $safe_path = realpath('../../' . $file_path);
    $base_path = realpath('../../');
    
    if (!$safe_path || strpos($safe_path, $base_path) !== 0) {
        return ['success' => false, 'message' => 'Invalid file path'];
    }
    
    if (file_exists($safe_path)) {
        if (unlink($safe_path)) {
            // Log activity
            $adminSecurity->logActivity($current_user['id'], 'file_delete', "Deleted: $file_path");
            return ['success' => true, 'message' => 'File deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete file'];
        }
    } else {
        return ['success' => false, 'message' => 'File not found'];
    }
}

function handleCreateFolder() {
    global $adminSecurity, $current_user;
    
    if (!$adminSecurity->hasPermission('file_write')) {
        return ['success' => false, 'message' => 'Create folder permission denied'];
    }
    
    $folder_name = $_POST['folder_name'] ?? '';
    $parent_dir = $_POST['parent_dir'] ?? '';
    
    if (empty($folder_name)) {
        return ['success' => false, 'message' => 'Folder name required'];
    }
    
    // Sanitize folder name
    $safe_folder_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $folder_name);
    $folder_path = '../../' . trim($parent_dir, '/') . '/' . $safe_folder_name;
    
    if (is_dir($folder_path)) {
        return ['success' => false, 'message' => 'Folder already exists'];
    }
    
    if (mkdir($folder_path, 0755, true)) {
        // Log activity
        $adminSecurity->logActivity($current_user['id'], 'folder_create', "Created folder: $safe_folder_name", $parent_dir);
        return ['success' => true, 'message' => "Folder created successfully: $safe_folder_name"];
    } else {
        return ['success' => false, 'message' => 'Failed to create folder'];
    }
}

function getFileList($dir) {
    $base_path = '../../';
    $full_path = $base_path . trim($dir, '/');
    
    if (!is_dir($full_path)) {
        return [];
    }
    
    $files = [];
    $items = scandir($full_path);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $item_path = $full_path . '/' . $item;
        $relative_path = $dir . '/' . $item;
        
        $file_info = [
            'name' => $item,
            'path' => $relative_path,
            'is_dir' => is_dir($item_path),
            'size' => is_file($item_path) ? filesize($item_path) : 0,
            'modified' => filemtime($item_path),
            'permissions' => substr(sprintf('%o', fileperms($item_path)), -4),
            'extension' => pathinfo($item, PATHINFO_EXTENSION),
            'type' => is_dir($item_path) ? 'folder' : getFileType(mime_content_type($item_path))
        ];
        
        $files[] = $file_info;
    }
    
    // Sort: directories first, then files
    usort($files, function($a, $b) {
        if ($a['is_dir'] && !$b['is_dir']) return -1;
        if (!$a['is_dir'] && $b['is_dir']) return 1;
        return strcmp($a['name'], $b['name']);
    });
    
    return $files;
}

function generateBreadcrumbs($dir) {
    $breadcrumbs = [['name' => 'Root', 'path' => '/']];
    
    if (empty($dir) || $dir === '/') {
        return $breadcrumbs;
    }
    
    $parts = explode('/', trim($dir, '/'));
    $current_path = '';
    
    foreach ($parts as $part) {
        $current_path .= '/' . $part;
        $breadcrumbs[] = ['name' => $part, 'path' => $current_path];
    }
    
    return $breadcrumbs;
}

function getFileType($mime_type) {
    if (strpos($mime_type, 'image/') === 0) return 'image';
    if (strpos($mime_type, 'text/') === 0) return 'code';
    if (in_array($mime_type, ['application/json', 'application/javascript'])) return 'code';
    if (strpos($mime_type, 'application/') === 0) return 'document';
    return 'other';
}

function formatFileSize($bytes) {
    if ($bytes === 0) return '0 B';
    
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

function getFileIcon($file) {
    if ($file['is_dir']) {
        return 'fas fa-folder';
    }
    
    switch ($file['type']) {
        case 'image':
            return 'fas fa-image';
        case 'code':
            return 'fas fa-code';
        case 'document':
            return 'fas fa-file-alt';
        default:
            return 'fas fa-file';
    }
}

// Log page access
$adminSecurity->logActivity($current_user['id'], 'page_access', 'File Manager accessed');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <style>
        .file-manager {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }
        
        .file-manager-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-secondary);
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .breadcrumb-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius);
            transition: all 0.2s ease;
        }
        
        .breadcrumb-item a:hover {
            background: var(--primary-color);
            color: var(--text-light);
        }
        
        .breadcrumb-separator {
            color: var(--text-muted);
        }
        
        .toolbar {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            background: var(--bg-primary);
            color: var(--text-primary);
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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
        
        .btn-danger {
            background: var(--danger-color);
            color: var(--text-light);
            border-color: var(--danger-color);
        }
        
        .file-list {
            min-height: 400px;
        }
        
        .file-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid var(--border-light);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .file-item:hover {
            background: var(--bg-secondary);
        }
        
        .file-item:last-child {
            border-bottom: none;
        }
        
        .file-icon {
            width: 2rem;
            text-align: center;
            margin-right: 1rem;
            font-size: 1.125rem;
        }
        
        .file-name {
            flex: 1;
            font-weight: 500;
        }
        
        .file-size {
            width: 100px;
            text-align: right;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        
        .file-date {
            width: 150px;
            text-align: right;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        
        .file-actions {
            width: 100px;
            text-align: right;
        }
        
        .file-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
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
            max-height: 80vh;
            overflow-y: auto;
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
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 0.875rem;
        }
        
        .form-input:focus {
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
        
        .file-preview {
            max-width: 100%;
            max-height: 300px;
            margin: 1rem 0;
            border-radius: var(--radius);
        }
        
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: var(--radius);
            padding: 2rem;
            text-align: center;
            transition: all 0.2s ease;
        }
        
        .upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
        }
        
        .upload-area i {
            font-size: 3rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .file-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .file-size,
            .file-date,
            .file-actions {
                width: auto;
            }
            
            .toolbar {
                flex-direction: column;
            }
        }
    </style>
</head>
<body data-page="file-manager">
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
                        <li class="active">
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
                    <h1>Dosya Yöneticisi</h1>
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
                
                <div class="file-manager">
                    <div class="file-manager-header">
                        <!-- Breadcrumbs -->
                        <nav class="breadcrumb">
                            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                                <?php if ($index > 0): ?>
                                    <span class="breadcrumb-separator">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                <?php endif; ?>
                                <div class="breadcrumb-item">
                                    <a href="?dir=<?php echo urlencode($crumb['path']); ?>">
                                        <?php echo htmlspecialchars($crumb['name']); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </nav>
                        
                        <!-- Toolbar -->
                        <div class="toolbar">
                            <button class="btn btn-primary" onclick="showUploadModal()">
                                <i class="fas fa-upload"></i>
                                Dosya Yükle
                            </button>
                            
                            <button class="btn" onclick="showCreateFolderModal()">
                                <i class="fas fa-folder-plus"></i>
                                Klasör Oluştur
                            </button>
                            
                            <button class="btn" onclick="refreshFileList()">
                                <i class="fas fa-sync-alt"></i>
                                Yenile
                            </button>
                        </div>
                    </div>
                    
                    <!-- File List -->
                    <div class="file-list">
                        <?php if (empty($files)): ?>
                            <div class="file-item">
                                <div style="text-align: center; width: 100%; padding: 2rem; color: var(--text-muted);">
                                    <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                                    <p>Bu klasör boş</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($files as $file): ?>
                                <div class="file-item" <?php if ($file['is_dir']): ?>onclick="navigateToFolder('<?php echo addslashes($file['path']); ?>')"<?php endif; ?>>
                                    <div class="file-icon">
                                        <i class="<?php echo getFileIcon($file); ?>" style="color: <?php echo $file['is_dir'] ? '#f59e0b' : '#6b7280'; ?>"></i>
                                    </div>
                                    
                                    <div class="file-name">
                                        <?php echo htmlspecialchars($file['name']); ?>
                                    </div>
                                    
                                    <div class="file-size">
                                        <?php if (!$file['is_dir']): ?>
                                            <?php echo formatFileSize($file['size']); ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="file-date">
                                        <?php echo date('d.m.Y H:i', $file['modified']); ?>
                                    </div>
                                    
                                    <div class="file-actions">
                                        <?php if (!$file['is_dir']): ?>
                                            <button class="btn btn-danger" onclick="deleteFile('<?php echo addslashes($file['path']); ?>', '<?php echo addslashes($file['name']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal" id="uploadModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Dosya Yükle</h3>
                <button class="modal-close" onclick="closeModal('uploadModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="upload">
                <input type="hidden" name="target_dir" value="<?php echo htmlspecialchars($current_dir); ?>">
                
                <div class="form-group">
                    <div class="upload-area" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Dosyaları buraya sürükleyin veya tıklayarak seçin</p>
                        <input type="file" name="file" id="fileInput" style="display: none;" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i>
                        Yükle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div class="modal" id="createFolderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Klasör Oluştur</h3>
                <button class="modal-close" onclick="closeModal('createFolderModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="create_folder">
                <input type="hidden" name="parent_dir" value="<?php echo htmlspecialchars($current_dir); ?>">
                
                <div class="form-group">
                    <label class="form-label" for="folderName">Klasör Adı</label>
                    <input type="text" name="folder_name" id="folderName" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-folder-plus"></i>
                        Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        // File Manager specific scripts
        function showUploadModal() {
            document.getElementById('uploadModal').classList.add('active');
        }
        
        function showCreateFolderModal() {
            document.getElementById('createFolderModal').classList.add('active');
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
        
        function navigateToFolder(path) {
            window.location.href = '?dir=' + encodeURIComponent(path);
        }
        
        function deleteFile(path, name) {
            if (confirm(`"${name}" dosyasını silmek istediğinizden emin misiniz?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="file_path" value="${path}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function refreshFileList() {
            window.location.reload();
        }
        
        // Drag and drop functionality
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
            }
        });
        
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                uploadArea.innerHTML = `
                    <i class="fas fa-file"></i>
                    <p>Seçilen dosya: ${file.name}</p>
                    <p>Boyut: ${AdminUtils.formatFileSize(file.size)}</p>
                `;
            }
        });
        
        // Close modals with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal.active').forEach(modal => {
                    modal.classList.remove('active');
                });
            }
        });
        
        // Close modals when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
