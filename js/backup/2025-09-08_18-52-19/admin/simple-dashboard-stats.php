<?php
/**
 * Simple Dashboard Statistics API
 * NextCode Group - Direct Database Stats
 */

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Direct database connection
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
    
    $stats = [];
    
    // Count files in project
    $file_count = 0;
    $project_root = dirname(__DIR__);
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($project_root));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $file_count++;
        }
    }
    
    $stats[] = [
        'label' => 'Toplam Dosya',
        'value' => number_format($file_count),
        'icon' => 'file-alt',
        'color' => '#667eea'
    ];
    
    // Recent activity (last 24 hours)
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM admin_activity_log 
            WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();
        $recent_activity = $result['total'] ?? 1;
    } catch (Exception $e) {
        $recent_activity = 1;
    }
    
    $stats[] = [
        'label' => 'Son 24 Saat Aktivite',
        'value' => number_format($recent_activity),
        'icon' => 'clock',
        'color' => '#28a745'
    ];
    
    // Active sessions
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM admin_sessions 
            WHERE expires_at > NOW()
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();
        $active_sessions = $result['total'] ?? 1;
    } catch (Exception $e) {
        $active_sessions = 1;
    }
    
    $stats[] = [
        'label' => 'Aktif Oturum',
        'value' => number_format($active_sessions),
        'icon' => 'user-check',
        'color' => '#17a2b8'
    ];
    
    // System status
    $stats[] = [
        'label' => 'Sistem Durumu',
        'value' => 'Online',
        'icon' => 'server',
        'color' => '#ffc107'
    ];
    
    // Quick actions
    $quick_actions = [
        [
            'title' => 'Dosya Yükle',
            'description' => 'Yeni dosya yükle veya mevcut dosyaları düzenle',
            'icon' => 'upload',
            'url' => 'pages/file-manager.php',
            'color' => '#667eea'
        ],
        [
            'title' => 'Kod Düzenle',
            'description' => 'Site dosyalarını doğrudan düzenle',
            'icon' => 'code',
            'url' => 'pages/code-editor.php',
            'color' => '#28a745'
        ],
        [
            'title' => 'Veritabanı',
            'description' => 'Veritabanı tablolarını yönet',
            'icon' => 'database',
            'url' => 'pages/database.php',
            'color' => '#17a2b8'
        ],
        [
            'title' => 'Yedek Al',
            'description' => 'Site ve veritabanı yedek oluştur',
            'icon' => 'download',
            'url' => '#',
            'color' => '#ffc107'
        ]
    ];
    
    // Recent activities
    $recent_activities = [
        [
            'action' => 'Admin Login',
            'user' => 'Administrator',
            'time' => date('H:i'),
            'icon' => 'sign-in-alt',
            'color' => '#28a745'
        ],
        [
            'action' => 'Dashboard Viewed',
            'user' => 'Administrator', 
            'time' => date('H:i'),
            'icon' => 'eye',
            'color' => '#17a2b8'
        ],
        [
            'action' => 'System Online',
            'user' => 'System',
            'time' => date('H:i'),
            'icon' => 'server',
            'color' => '#ffc107'
        ]
    ];
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'quick_actions' => $quick_actions,
        'recent_activities' => $recent_activities,
        'timestamp' => time(),
        'formatted_time' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Dashboard stats yüklenemedi: ' . $e->getMessage(),
        'fallback_stats' => [
            ['label' => 'Toplam Dosya', 'value' => '500+', 'icon' => 'file-alt', 'color' => '#667eea'],
            ['label' => 'Son Aktivite', 'value' => '1', 'icon' => 'clock', 'color' => '#28a745'],
            ['label' => 'Aktif Oturum', 'value' => '1', 'icon' => 'user-check', 'color' => '#17a2b8'],
            ['label' => 'Sistem Durumu', 'value' => 'Online', 'icon' => 'server', 'color' => '#ffc107']
        ]
    ]);
}
?>
