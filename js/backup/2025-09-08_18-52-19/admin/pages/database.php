<?php
/**
 * Admin Panel - Database Manager
 * NextCode Group - Advanced Database Management System
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
if (!$adminSecurity->hasPermission('database_read')) {
    die('Access denied: Insufficient permissions');
}

$current_user = [
    'id' => $_SESSION['admin_user_id'],
    'username' => $_SESSION['admin_username'],
    'role' => $_SESSION['admin_role']
];

// Handle database operations
$message = '';
$error = '';
$query_result = null;

if ($_POST) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!$adminSecurity->validateCSRFToken($csrf_token)) {
        $error = 'Security error. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'execute_query':
                if ($adminSecurity->hasPermission('database_write')) {
                    $result = executeQuery();
                    if ($result['success']) {
                        $message = $result['message'];
                        $query_result = $result['data'] ?? null;
                    } else {
                        $error = $result['message'];
                    }
                } else {
                    $error = 'Database write permission denied';
                }
                break;
                
            case 'backup_database':
                $result = backupDatabase();
                if ($result['success']) {
                    $message = $result['message'];
                } else {
                    $error = $result['message'];
                }
                break;
                
            case 'optimize_tables':
                if ($adminSecurity->hasPermission('database_write')) {
                    $result = optimizeTables();
                    if ($result['success']) {
                        $message = $result['message'];
                    } else {
                        $error = $result['message'];
                    }
                } else {
                    $error = 'Database write permission denied';
                }
                break;
        }
    }
}

// Get database information
$db_info = getDatabaseInfo();
$tables = getTables();

// Generate CSRF token
$csrf_token = $adminSecurity->generateCSRFToken();

function executeQuery() {
    global $pdo, $adminSecurity, $current_user;
    
    $query = trim($_POST['query'] ?? '');
    if (empty($query)) {
        return ['success' => false, 'message' => 'No query provided'];
    }
    
    // Security check: prevent dangerous operations
    $dangerous_keywords = ['DROP', 'DELETE', 'TRUNCATE', 'ALTER', 'CREATE', 'INSERT', 'UPDATE'];
    $query_upper = strtoupper($query);
    
    foreach ($dangerous_keywords as $keyword) {
        if (strpos($query_upper, $keyword) !== false) {
            if ($current_user['role'] !== 'super_admin') {
                return ['success' => false, 'message' => "Operation '$keyword' requires super admin privileges"];
            }
        }
    }
    
    try {
        $start_time = microtime(true);
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $execution_time = round((microtime(true) - $start_time) * 1000, 2);
        
        // Log the query
        $adminSecurity->logActivity($current_user['id'], 'database_query', substr($query, 0, 100) . '...');
        
        if (stripos($query, 'SELECT') === 0) {
            // SELECT query
            $results = $stmt->fetchAll();
            return [
                'success' => true,
                'message' => count($results) . " rows returned in {$execution_time}ms",
                'data' => [
                    'type' => 'select',
                    'rows' => $results,
                    'execution_time' => $execution_time,
                    'row_count' => count($results)
                ]
            ];
        } else {
            // Non-SELECT query
            $affected_rows = $stmt->rowCount();
            return [
                'success' => true,
                'message' => "$affected_rows rows affected in {$execution_time}ms",
                'data' => [
                    'type' => 'modify',
                    'affected_rows' => $affected_rows,
                    'execution_time' => $execution_time
                ]
            ];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Query error: ' . $e->getMessage()];
    }
}

function backupDatabase() {
    global $pdo, $adminSecurity, $current_user;
    
    try {
        $backup_dir = '../../backups/database/';
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backup_dir . $filename;
        
        $backup_content = generateDatabaseBackup();
        
        if (file_put_contents($filepath, $backup_content)) {
            $adminSecurity->logActivity($current_user['id'], 'database_backup', "Backup created: $filename");
            return ['success' => true, 'message' => "Database backup created: $filename"];
        } else {
            return ['success' => false, 'message' => 'Failed to create backup file'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Backup error: ' . $e->getMessage()];
    }
}

function generateDatabaseBackup() {
    global $pdo;
    
    $backup = "-- NextCode Group Database Backup\n";
    $backup .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $backup .= "-- Database: gtorg_nextcode\n\n";
    
    $backup .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    // Get all tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        // Table structure
        $create_table = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
        $backup .= "-- Table: $table\n";
        $backup .= "DROP TABLE IF EXISTS `$table`;\n";
        $backup .= $create_table['Create Table'] . ";\n\n";
        
        // Table data
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            $columns = array_keys($rows[0]);
            $backup .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES\n";
            
            $values = [];
            foreach ($rows as $row) {
                $escaped_values = array_map(function($value) use ($pdo) {
                    return $pdo->quote($value);
                }, array_values($row));
                $values[] = "(" . implode(", ", $escaped_values) . ")";
            }
            
            $backup .= implode(",\n", $values) . ";\n\n";
        }
    }
    
    $backup .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
    return $backup;
}

function optimizeTables() {
    global $pdo, $adminSecurity, $current_user;
    
    try {
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $optimized_count = 0;
        
        foreach ($tables as $table) {
            $pdo->exec("OPTIMIZE TABLE `$table`");
            $optimized_count++;
        }
        
        $adminSecurity->logActivity($current_user['id'], 'database_optimize', "Optimized $optimized_count tables");
        return ['success' => true, 'message' => "Optimized $optimized_count tables"];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Optimization error: ' . $e->getMessage()];
    }
}

function getDatabaseInfo() {
    global $pdo;
    
    try {
        $info = [];
        
        // Database version
        $version = $pdo->query("SELECT VERSION() as version")->fetch();
        $info['version'] = $version['version'];
        
        // Database size
        $size_query = $pdo->query("
            SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
        ");
        $size = $size_query->fetch();
        $info['size_mb'] = $size['size_mb'] ?? 0;
        
        // Table count
        $table_count = $pdo->query("
            SELECT COUNT(*) as count 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
        ")->fetch();
        $info['table_count'] = $table_count['count'];
        
        // Connection info
        $info['host'] = 'gtorg.mysql.ukraine.com.ua:3306';
        $info['database'] = 'gtorg_nextcode';
        
        return $info;
    } catch (PDOException $e) {
        return [
            'version' => 'Unknown',
            'size_mb' => 0,
            'table_count' => 0,
            'host' => 'Unknown',
            'database' => 'Unknown'
        ];
    }
}

function getTables() {
    global $pdo;
    
    try {
        $query = $pdo->query("
            SELECT 
                table_name,
                table_rows,
                ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb,
                table_comment
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
            ORDER BY table_name
        ");
        
        return $query->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function formatBytes($bytes) {
    if ($bytes === 0) return '0 B';
    
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

// Log page access
$adminSecurity->logActivity($current_user['id'], 'page_access', 'Database Manager accessed');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Manager - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <style>
        .database-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            height: calc(100vh - var(--topbar-height) - 4rem);
        }
        
        .database-sidebar {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }
        
        .database-main {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-section {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .sidebar-section:last-child {
            border-bottom: none;
        }
        
        .sidebar-section h3 {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }
        
        .db-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .db-info-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
        }
        
        .db-info-label {
            color: var(--text-secondary);
        }
        
        .db-info-value {
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .table-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .table-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem;
            cursor: pointer;
            border-radius: var(--radius);
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }
        
        .table-item:hover {
            background: var(--bg-secondary);
        }
        
        .table-item.active {
            background: var(--primary-color);
            color: var(--text-light);
        }
        
        .table-name {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .table-rows {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        
        .query-editor {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .query-toolbar {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .query-actions {
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
        
        .btn-warning {
            background: var(--warning-color);
            color: var(--text-light);
            border-color: var(--warning-color);
        }
        
        .query-input {
            flex: 1;
            padding: 1rem;
            border: none;
            outline: none;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            resize: none;
            background: var(--bg-secondary);
            min-height: 200px;
        }
        
        .query-results {
            flex: 1;
            overflow: auto;
            padding: 1rem;
        }
        
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
        
        .result-table th,
        .result-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .result-table th {
            background: var(--bg-secondary);
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .result-table tr:hover {
            background: var(--bg-secondary);
        }
        
        .result-info {
            background: var(--bg-secondary);
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        
        .result-info strong {
            color: var(--primary-color);
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
        
        .quick-queries {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .quick-query-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .quick-query-btn:hover {
            background: var(--primary-color);
            color: var(--text-light);
        }
        
        .no-results {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        
        .no-results i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .database-layout {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .query-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .query-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body data-page="database">
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
                        <li class="active">
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
                    <h1>Veritabanı Yöneticisi</h1>
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
                
                <div class="database-layout">
                    <!-- Database Sidebar -->
                    <div class="database-sidebar">
                        <!-- Database Info -->
                        <div class="sidebar-section">
                            <h3>Veritabanı Bilgileri</h3>
                            <div class="db-info">
                                <div class="db-info-item">
                                    <span class="db-info-label">Host:</span>
                                    <span class="db-info-value"><?php echo htmlspecialchars($db_info['host']); ?></span>
                                </div>
                                <div class="db-info-item">
                                    <span class="db-info-label">Database:</span>
                                    <span class="db-info-value"><?php echo htmlspecialchars($db_info['database']); ?></span>
                                </div>
                                <div class="db-info-item">
                                    <span class="db-info-label">Version:</span>
                                    <span class="db-info-value"><?php echo htmlspecialchars($db_info['version']); ?></span>
                                </div>
                                <div class="db-info-item">
                                    <span class="db-info-label">Size:</span>
                                    <span class="db-info-value"><?php echo $db_info['size_mb']; ?> MB</span>
                                </div>
                                <div class="db-info-item">
                                    <span class="db-info-label">Tables:</span>
                                    <span class="db-info-value"><?php echo $db_info['table_count']; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Database Actions -->
                        <div class="sidebar-section">
                            <h3>İşlemler</h3>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="backup_database">
                                    <button type="submit" class="btn btn-success" style="width: 100%;">
                                        <i class="fas fa-download"></i>
                                        Yedek Al
                                    </button>
                                </form>
                                
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="optimize_tables">
                                    <button type="submit" class="btn btn-warning" style="width: 100%;" 
                                            onclick="return confirm('Tabloları optimize etmek istiyor musunuz?')">
                                        <i class="fas fa-tools"></i>
                                        Optimize Et
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Tables List -->
                        <div class="sidebar-section">
                            <h3>Tablolar (<?php echo count($tables); ?>)</h3>
                            <div class="table-list">
                                <?php foreach ($tables as $table): ?>
                                    <div class="table-item" onclick="selectTable('<?php echo $table['table_name']; ?>')">
                                        <div class="table-name">
                                            <i class="fas fa-table"></i>
                                            <?php echo htmlspecialchars($table['table_name']); ?>
                                        </div>
                                        <div class="table-rows">
                                            <?php echo number_format($table['table_rows']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Query Editor -->
                    <div class="database-main">
                        <div class="query-editor">
                            <!-- Toolbar -->
                            <div class="query-toolbar">
                                <div class="quick-queries">
                                    <button class="quick-query-btn" onclick="insertQuery('SHOW TABLES;')">SHOW TABLES</button>
                                    <button class="quick-query-btn" onclick="insertQuery('SHOW DATABASES;')">SHOW DATABASES</button>
                                    <button class="quick-query-btn" onclick="insertQuery('SELECT * FROM ')">SELECT *</button>
                                    <button class="quick-query-btn" onclick="insertQuery('DESCRIBE ')">DESCRIBE</button>
                                </div>
                                
                                <div class="query-actions">
                                    <button class="btn" onclick="clearQuery()">
                                        <i class="fas fa-trash"></i>
                                        Temizle
                                    </button>
                                    <button class="btn btn-primary" onclick="executeQuery()">
                                        <i class="fas fa-play"></i>
                                        Çalıştır (Ctrl+Enter)
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Query Input -->
                            <form method="POST" id="queryForm">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="action" value="execute_query">
                                <textarea name="query" id="queryInput" class="query-input" 
                                          placeholder="SQL sorgusunu buraya yazın..."><?php echo htmlspecialchars($_POST['query'] ?? ''); ?></textarea>
                            </form>
                            
                            <!-- Query Results -->
                            <div class="query-results">
                                <?php if ($query_result): ?>
                                    <div class="result-info">
                                        <?php if ($query_result['type'] === 'select'): ?>
                                            <strong><?php echo $query_result['row_count']; ?></strong> satır döndürüldü 
                                            (<strong><?php echo $query_result['execution_time']; ?>ms</strong>)
                                        <?php else: ?>
                                            <strong><?php echo $query_result['affected_rows']; ?></strong> satır etkilendi 
                                            (<strong><?php echo $query_result['execution_time']; ?>ms</strong>)
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($query_result['type'] === 'select' && !empty($query_result['rows'])): ?>
                                        <div style="overflow-x: auto;">
                                            <table class="result-table">
                                                <thead>
                                                    <tr>
                                                        <?php foreach (array_keys($query_result['rows'][0]) as $column): ?>
                                                            <th><?php echo htmlspecialchars($column); ?></th>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($query_result['rows'] as $row): ?>
                                                        <tr>
                                                            <?php foreach ($row as $value): ?>
                                                                <td><?php echo htmlspecialchars($value ?? 'NULL'); ?></td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php elseif ($query_result['type'] === 'select' && empty($query_result['rows'])): ?>
                                        <div class="no-results">
                                            <i class="fas fa-inbox"></i>
                                            <p>Sorgu sonucu boş</p>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="no-results">
                                        <i class="fas fa-database"></i>
                                        <h3>SQL Sorgu Editörü</h3>
                                        <p>Yukarıdaki alana SQL sorgunuzu yazın ve çalıştır butonuna tıklayın</p>
                                        <div style="margin-top: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                            <p><strong>Kısayollar:</strong></p>
                                            <p>Ctrl+Enter: Sorguyu çalıştır</p>
                                            <p>Ctrl+A: Tümünü seç</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        let selectedTable = '';
        
        function selectTable(tableName) {
            selectedTable = tableName;
            
            // Update UI
            document.querySelectorAll('.table-item').forEach(item => {
                item.classList.remove('active');
            });
            event.target.closest('.table-item').classList.add('active');
            
            // Insert SELECT query
            const query = `SELECT * FROM \`${tableName}\` LIMIT 100;`;
            document.getElementById('queryInput').value = query;
        }
        
        function insertQuery(query) {
            const queryInput = document.getElementById('queryInput');
            const cursorPos = queryInput.selectionStart;
            const textBefore = queryInput.value.substring(0, cursorPos);
            const textAfter = queryInput.value.substring(queryInput.selectionEnd);
            
            queryInput.value = textBefore + query + textAfter;
            queryInput.focus();
            queryInput.setSelectionRange(cursorPos + query.length, cursorPos + query.length);
        }
        
        function clearQuery() {
            document.getElementById('queryInput').value = '';
            document.getElementById('queryInput').focus();
        }
        
        function executeQuery() {
            const query = document.getElementById('queryInput').value.trim();
            if (!query) {
                alert('Lütfen bir sorgu girin');
                return;
            }
            
            document.getElementById('queryForm').submit();
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                executeQuery();
            }
        });
        
        // Auto-focus on query input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('queryInput').focus();
        });
        
        // Syntax highlighting for common SQL keywords
        const queryInput = document.getElementById('queryInput');
        queryInput.addEventListener('input', function() {
            // Simple syntax highlighting could be added here
            // For now, just basic functionality
        });
        
        // Table row click to copy value
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'TD') {
                const value = e.target.textContent;
                if (value && value !== 'NULL') {
                    navigator.clipboard.writeText(value).then(() => {
                        // Visual feedback
                        const originalBg = e.target.style.backgroundColor;
                        e.target.style.backgroundColor = 'var(--success-color)';
                        e.target.style.color = 'white';
                        setTimeout(() => {
                            e.target.style.backgroundColor = originalBg;
                            e.target.style.color = '';
                        }, 200);
                    }).catch(() => {
                        console.log('Clipboard copy failed');
                    });
                }
            }
        });
        
        // Query history (simple localStorage implementation)
        const queryHistory = JSON.parse(localStorage.getItem('queryHistory') || '[]');
        
        function saveQueryToHistory(query) {
            if (query.trim() && !queryHistory.includes(query)) {
                queryHistory.unshift(query);
                if (queryHistory.length > 10) {
                    queryHistory.pop();
                }
                localStorage.setItem('queryHistory', JSON.stringify(queryHistory));
            }
        }
        
        // Save query when form is submitted
        document.getElementById('queryForm').addEventListener('submit', function() {
            const query = document.getElementById('queryInput').value.trim();
            saveQueryToHistory(query);
        });
    </script>
</body>
</html>
