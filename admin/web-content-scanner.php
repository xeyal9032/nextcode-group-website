<?php
// Web Content Scanner - NextCode Group
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

// Veritabanı bağlantısı
$pdo = getSecureDatabaseConnection();

$scan_results = [];
$scan_stats = [
    'pages_scanned' => 0,
    'images_found' => 0,
    'content_items' => 0,
    'media_files' => 0,
    'errors' => 0
];

// Tarama işlemi
if (isset($_POST['start_scan'])) {
    $scan_results = performWebContentScan($pdo);
    $scan_stats = calculateScanStats($scan_results);
}

// Veritabanından mevcut içeriği getir (sadece tarama yapılmamışsa)
if ($pdo && !isset($_POST['start_scan'])) {
    try {
        $stmt = $pdo->query("
            SELECT id, content_type, filename, file_path, title, description, 
                   file_size, width, height, extension, mime_type, alt_text, 
                   last_modified, status, created_at 
            FROM web_content 
            ORDER BY created_at DESC
        ");
        $db_content = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Veritabanı içeriğini tarama sonuçları formatına çevir
        foreach ($db_content as $item) {
            $scan_results[$item['content_type']][] = $item;
        }
        
        // İstatistikleri güncelle
        $scan_stats = [
            'pages_scanned' => count($scan_results['page'] ?? []),
            'images_found' => count($scan_results['image'] ?? []),
            'content_items' => count($scan_results['page'] ?? []) + count($scan_results['style'] ?? []) + count($scan_results['script'] ?? []),
            'media_files' => count($scan_results['image'] ?? []),
            'errors' => 0
        ];
        
    } catch (Exception $e) {
        error_log("Database content fetch error: " . $e->getMessage());
    }
}

// İçerik yönetimi
if (isset($_POST['manage_content'])) {
    $action = $_POST['action'] ?? '';
    $content_id = $_POST['content_id'] ?? 0;
    
    switch ($action) {
        case 'update':
            updateScannedContent($pdo, $content_id, $_POST);
            break;
        case 'delete':
            deleteScannedContent($pdo, $content_id);
            break;
        case 'sync':
            syncWebContent($pdo);
            break;
    }
}

function performWebContentScan($pdo) {
    $results = [];
    $web_root = dirname(__DIR__);
    
    try {
        // 1. PHP Sayfalarını Tara
        $php_files = glob($web_root . '/*.php');
        $excluded_files = ['index.php', 'admin', 'api', 'config', 'includes', 'tests', '404.php', '500.php'];
        
        if ($php_files) {
            foreach ($php_files as $file) {
                $filename = basename($file);
                if (!in_array($filename, $excluded_files)) {
                    try {
                        $content = analyzePageContent($file);
                        if ($content) {
                            $results['pages'][] = $content;
                        }
                    } catch (Exception $e) {
                        error_log("Page analysis error for $filename: " . $e->getMessage());
                        continue;
                    }
                }
            }
        }
        
        // 2. Resim Dosyalarını Tara
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $image_dirs = ['/images/', '/assets/images/'];
        
        foreach ($image_dirs as $dir) {
            if (is_dir($web_root . $dir)) {
                foreach ($image_extensions as $ext) {
                    $images = glob($web_root . $dir . '*.' . $ext);
                    if ($images) {
                        foreach ($images as $image) {
                            try {
                                $results['images'][] = analyzeImageContent($image);
                            } catch (Exception $e) {
                                error_log("Image analysis error for $image: " . $e->getMessage());
                                continue;
                            }
                        }
                    }
                }
            }
        }
        
        // 3. CSS Dosyalarını Tara
        $css_dirs = ['/css/', '/assets/css/'];
        
        foreach ($css_dirs as $dir) {
            if (is_dir($web_root . $dir)) {
                $css_files = glob($web_root . $dir . '*.css');
                if ($css_files) {
                    foreach ($css_files as $css) {
                        try {
                            $results['styles'][] = analyzeStyleContent($css);
                        } catch (Exception $e) {
                            error_log("CSS analysis error for $css: " . $e->getMessage());
                            continue;
                        }
                    }
                }
            }
        }
        
        // 4. JavaScript Dosyalarını Tara
        $js_dirs = ['/js/', '/assets/js/'];
        
        foreach ($js_dirs as $dir) {
            if (is_dir($web_root . $dir)) {
                $js_files = glob($web_root . $dir . '*.js');
                if ($js_files) {
                    foreach ($js_files as $js) {
                        try {
                            $results['scripts'][] = analyzeScriptContent($js);
                        } catch (Exception $e) {
                            error_log("JS analysis error for $js: " . $e->getMessage());
                            continue;
                        }
                    }
                }
            }
        }
        
    } catch (Exception $e) {
        error_log("Web content scan error: " . $e->getMessage());
        $results['error'] = $e->getMessage();
    }
    
    return $results;
}

function analyzePageContent($file_path) {
    $content = file_get_contents($file_path);
    $filename = basename($file_path);
    
    // Sayfa başlığını çıkar
    $title = extractPageTitle($content);
    
    // Meta açıklamasını çıkar
    $description = extractMetaDescription($content);
    
    // Ana içeriği çıkar
    $main_content = extractMainContent($content);
    
    // Resimleri çıkar
    $images = extractImages($content);
    
    // Linkleri çıkar
    $links = extractLinks($content);
    
    return [
        'type' => 'page',
        'filename' => $filename,
        'file_path' => str_replace(dirname(__DIR__), '', $file_path),
        'title' => $title,
        'description' => $description,
        'content' => $main_content,
        'images' => $images,
        'links' => $links,
        'file_size' => filesize($file_path),
        'last_modified' => date('Y-m-d H:i:s', filemtime($file_path)),
        'word_count' => str_word_count(strip_tags($main_content)),
        'status' => 'active'
    ];
}

function analyzeImageContent($file_path) {
    $filename = basename($file_path);
    $file_info = pathinfo($file_path);
    
    // Resim boyutlarını al
    $image_info = getimagesize($file_path);
    $width = $image_info ? $image_info[0] : 0;
    $height = $image_info ? $image_info[1] : 0;
    
    return [
        'type' => 'image',
        'filename' => $filename,
        'file_path' => str_replace(dirname(__DIR__), '', $file_path),
        'file_size' => filesize($file_path),
        'width' => $width,
        'height' => $height,
        'extension' => $file_info['extension'],
        'mime_type' => $image_info ? $image_info['mime'] : 'image/' . $file_info['extension'],
        'last_modified' => date('Y-m-d H:i:s', filemtime($file_path)),
        'alt_text' => extractImageAltText($file_path),
        'status' => 'active'
    ];
}

function extractPageTitle($content) {
    if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $content, $matches)) {
        return trim(strip_tags($matches[1]));
    }
    return '';
}

function extractMetaDescription($content) {
    if (preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $content, $matches)) {
        return trim($matches[1]);
    }
    return '';
}

function extractMainContent($content) {
    // HTML etiketlerini temizle ve ana içeriği çıkar
    $content = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $content);
    $content = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $content);
    $content = preg_replace('/<nav[^>]*>.*?<\/nav>/is', '', $content);
    $content = preg_replace('/<footer[^>]*>.*?<\/footer>/is', '', $content);
    
    return trim(strip_tags($content));
}

function extractImages($content) {
    $images = [];
    if (preg_match_all('/<img[^>]*src=["\']([^"\']*)["\'][^>]*>/i', $content, $matches)) {
        foreach ($matches[1] as $src) {
            $images[] = $src;
        }
    }
    return $images;
}

function extractLinks($content) {
    $links = [];
    if (preg_match_all('/<a[^>]*href=["\']([^"\']*)["\'][^>]*>/i', $content, $matches)) {
        foreach ($matches[1] as $href) {
            $links[] = $href;
        }
    }
    return $links;
}

function extractImageAltText($file_path) {
    // Resim dosyasından alt text çıkarma (gelecekte AI ile geliştirilebilir)
    $filename = pathinfo($file_path, PATHINFO_FILENAME);
    return ucwords(str_replace(['-', '_'], ' ', $filename));
}

function analyzeStyleContent($file_path) {
    $content = file_get_contents($file_path);
    $filename = basename($file_path);
    
    // CSS kurallarını say
    $rule_count = preg_match_all('/[^{}]*\{[^{}]*\}/', $content);
    
    return [
        'type' => 'style',
        'filename' => $filename,
        'file_path' => str_replace(dirname(__DIR__), '', $file_path),
        'file_size' => filesize($file_path),
        'rule_count' => $rule_count,
        'last_modified' => date('Y-m-d H:i:s', filemtime($file_path)),
        'status' => 'active'
    ];
}

function analyzeScriptContent($file_path) {
    $content = file_get_contents($file_path);
    $filename = basename($file_path);
    
    // JavaScript fonksiyonlarını say
    $function_count = preg_match_all('/function\s+\w+/', $content);
    
    return [
        'type' => 'script',
        'filename' => $filename,
        'file_path' => str_replace(dirname(__DIR__), '', $file_path),
        'file_size' => filesize($file_path),
        'function_count' => $function_count,
        'last_modified' => date('Y-m-d H:i:s', filemtime($file_path)),
        'status' => 'active'
    ];
}

function calculateScanStats($results) {
    return [
        'pages_scanned' => count($results['pages'] ?? []),
        'images_found' => count($results['images'] ?? []),
        'content_items' => count($results['pages'] ?? []) + count($results['styles'] ?? []) + count($results['scripts'] ?? []),
        'media_files' => count($results['images'] ?? []),
        'errors' => 0
    ];
}

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

function updateScannedContent($pdo, $content_id, $data) {
    // Tarayıcı içeriğini güncelle
    $stmt = $pdo->prepare("
        UPDATE web_content 
        SET title = ?, description = ?, content = ?, alt_text = ?, 
            last_modified = NOW(), status = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $data['title'] ?? '',
        $data['description'] ?? '',
        $data['content'] ?? '',
        $data['alt_text'] ?? '',
        $data['status'] ?? 'active',
        $content_id
    ]);
}

function deleteScannedContent($pdo, $content_id) {
    $stmt = $pdo->prepare("DELETE FROM web_content WHERE id = ?");
    $stmt->execute([$content_id]);
}

function syncWebContent($pdo) {
    // Web içeriğini veritabanı ile senkronize et
    $results = performWebContentScan($pdo);
    
    foreach ($results as $type => $items) {
        foreach ($items as $item) {
            try {
                // Veritabanında var mı kontrol et
                $stmt = $pdo->prepare("SELECT id FROM web_content WHERE file_path = ? AND content_type = ?");
                $stmt->execute([$item['file_path'], $type]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Güncelle
                    $stmt = $pdo->prepare("
                        UPDATE web_content 
                        SET title = ?, description = ?, content = ?, file_size = ?, 
                            width = ?, height = ?, extension = ?, mime_type = ?, alt_text = ?,
                            last_modified = ?, status = ?, updated_at = NOW()
                        WHERE file_path = ? AND content_type = ?
                    ");
                    $stmt->execute([
                        $item['title'] ?? '',
                        $item['description'] ?? '',
                        $item['content'] ?? '',
                        $item['file_size'] ?? 0,
                        $item['width'] ?? 0,
                        $item['height'] ?? 0,
                        $item['extension'] ?? '',
                        $item['mime_type'] ?? '',
                        $item['alt_text'] ?? '',
                        $item['last_modified'],
                        'active',
                        $item['file_path'],
                        $type
                    ]);
                } else {
                    // Yeni ekle - INSERT IGNORE kullanarak duplicate key hatalarını önle
                    $stmt = $pdo->prepare("
                        INSERT IGNORE INTO web_content (content_type, filename, file_path, title, description, 
                                                       content, file_size, width, height, extension, mime_type, 
                                                       alt_text, last_modified, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([
                        $type,
                        $item['filename'] ?? '',
                        $item['file_path'] ?? '',
                        $item['title'] ?? '',
                        $item['description'] ?? '',
                        $item['content'] ?? '',
                        $item['file_size'] ?? 0,
                        $item['width'] ?? 0,
                        $item['height'] ?? 0,
                        $item['extension'] ?? '',
                        $item['mime_type'] ?? '',
                        $item['alt_text'] ?? '',
                        $item['last_modified'],
                        'active'
                    ]);
                }
            } catch (Exception $e) {
                // Hata durumunda logla ama devam et
                error_log("Web content sync error: " . $e->getMessage() . " for file: " . $item['file_path']);
                continue;
            }
        }
    }
}

// Web content tablosunu oluştur
function createWebContentTable($pdo) {
    $sql = "
    CREATE TABLE IF NOT EXISTS web_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content_type ENUM('page', 'image', 'style', 'script') NOT NULL,
        filename VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        title VARCHAR(255) NULL,
        description TEXT NULL,
        content LONGTEXT NULL,
        file_size INT DEFAULT 0,
        width INT DEFAULT 0,
        height INT DEFAULT 0,
        extension VARCHAR(10) NULL,
        mime_type VARCHAR(100) NULL,
        alt_text VARCHAR(255) NULL,
        last_modified TIMESTAMP NULL,
        status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_content_type (content_type),
        INDEX idx_status (status),
        INDEX idx_file_path (file_path)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($sql);
}

// Tabloyu oluştur
if ($pdo) {
    createWebContentTable($pdo);
}
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web İçerik Tarayıcısı | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="js/admin-ajax.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        
        .header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #2c3e50; margin-bottom: 10px; font-size: 28px; }
        .header p { color: #7f8c8d; font-size: 16px; }
        
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 32px; font-weight: bold; color: #3498db; margin-bottom: 10px; }
        .stat-label { color: #7f8c8d; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        
        .actions { background: white; padding: 25px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .action-buttons { display: flex; gap: 15px; flex-wrap: wrap; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #229954; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-warning:hover { background: #e67e22; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        
        .content-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
        .content-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s ease; }
        .content-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
        
        .content-header { padding: 20px; background: #f8f9fa; border-bottom: 1px solid #e9ecef; }
        .content-type { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .type-page { background: #e3f2fd; color: #1976d2; }
        .type-image { background: #f3e5f5; color: #7b1fa2; }
        .type-style { background: #e8f5e8; color: #388e3c; }
        .type-script { background: #fff3e0; color: #f57c00; }
        
        .content-body { padding: 20px; }
        .content-title { font-size: 18px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
        .content-description { color: #7f8c8d; font-size: 14px; margin-bottom: 15px; }
        .content-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; }
        .meta-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #7f8c8d; }
        
        .content-actions { display: flex; gap: 10px; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        
        .progress-bar { width: 100%; height: 8px; background: #ecf0f1; border-radius: 4px; overflow: hidden; margin: 20px 0; }
        .progress-fill { height: 100%; background: #3498db; transition: width 0.3s ease; }
        
        .loading { text-align: center; padding: 40px; color: #7f8c8d; }
        .loading i { font-size: 48px; margin-bottom: 20px; animation: spin 2s linear infinite; }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .empty-state { text-align: center; padding: 60px 20px; color: #7f8c8d; }
        .empty-state i { font-size: 64px; margin-bottom: 20px; opacity: 0.5; }
        
        @media (max-width: 768px) {
            .container { padding: 15px; }
            .action-buttons { flex-direction: column; }
            .content-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-search"></i> Web İçerik Tarayıcısı</h1>
            <p>Web projenizin tüm içeriğini otomatik olarak tarayın, analiz edin ve yönetin.</p>
        </div>

        <!-- Statistics -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $scan_stats['pages_scanned']; ?></div>
                <div class="stat-label">Taranan Sayfalar</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $scan_stats['images_found']; ?></div>
                <div class="stat-label">Bulunan Resimler</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $scan_stats['content_items']; ?></div>
                <div class="stat-label">İçerik Öğeleri</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $scan_stats['media_files']; ?></div>
                <div class="stat-label">Medya Dosyaları</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <h3><i class="fas fa-tools"></i> Tarama İşlemleri</h3>
            
            <div class="action-buttons">
                <form method="post" style="display: inline;">
                    <button type="submit" name="start_scan" class="btn btn-primary">
                        <i class="fas fa-play"></i> Taramayı Başlat
                    </button>
                </form>
                
                <form method="post" style="display: inline;">
                    <button type="submit" name="manage_content" value="sync" class="btn btn-success">
                        <i class="fas fa-sync"></i> İçeriği Senkronize Et
                    </button>
                    <input type="hidden" name="action" value="sync">
                </form>
                
                <button type="button" class="btn btn-warning" onclick="exportContent()">
                    <i class="fas fa-download"></i> İçeriği Dışa Aktar
                </button>
                
                <button type="button" class="btn btn-danger" onclick="clearContent()">
                    <i class="fas fa-trash"></i> İçeriği Temizle
                </button>
            </div>
            
            <?php if (!empty($scan_results)): ?>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 100%;"></div>
            </div>
            <p style="text-align: center; color: #27ae60; margin-top: 10px;">
                <i class="fas fa-check-circle"></i> Tarama tamamlandı!
            </p>
            <?php endif; ?>
        </div>

        <!-- Content Grid -->
        <div class="content-grid" id="contentGrid">
            <?php if (!empty($scan_results)): ?>
                <?php foreach ($scan_results as $type => $items): ?>
                    <?php foreach ($items as $item): ?>
                        <div class="content-card">
                            <div class="content-header">
                                <span class="content-type type-<?php echo $type; ?>">
                                    <?php echo $type; ?>
                                </span>
                                <div style="margin-top: 10px;">
                                    <strong><?php echo htmlspecialchars($item['filename']); ?></strong>
                                </div>
                            </div>
                            
                            <div class="content-body">
                                <?php if (!empty($item['title'])): ?>
                                    <div class="content-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                <?php endif; ?>
                                
                                <?php if (!empty($item['description'])): ?>
                                    <div class="content-description"><?php echo htmlspecialchars($item['description']); ?></div>
                                <?php endif; ?>
                                
                                <div class="content-meta">
                                    <?php if ($type === 'image'): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-expand-arrows-alt"></i>
                                            <?php echo $item['width']; ?> x <?php echo $item['height']; ?>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-file"></i>
                                            <?php echo formatFileSize($item['file_size']); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="meta-item">
                                            <i class="fas fa-file"></i>
                                            <?php echo formatFileSize($item['file_size']); ?>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('d.m.Y', strtotime($item['last_modified'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="content-actions">
                                    <button class="btn btn-primary btn-sm" onclick="editContent(<?php echo $item['id'] ?? 0; ?>)">
                                        <i class="fas fa-edit"></i> Düzenle
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick="viewContent('<?php echo htmlspecialchars($item['file_path'] ?? ''); ?>')">
                                        <i class="fas fa-eye"></i> Görüntüle
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteContent(<?php echo $item['id'] ?? 0; ?>)">
                                        <i class="fas fa-trash"></i> Sil
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <i class="fas fa-search"></i>
                    <h3>Henüz içerik taranmadı</h3>
                    <p>Web projenizin içeriğini taramak için "Taramayı Başlat" butonuna tıklayın.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function editContent(contentId) {
            if (contentId > 0) {
                // Basit düzenleme modalı
                const title = prompt('Başlık:', '');
                const description = prompt('Açıklama:', '');
                
                if (title !== null) {
                    fetch('ajax-handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=update_web_content&content_id=${contentId}&title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            adminAJAX.showNotification('İçerik güncellendi', 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            adminAJAX.showNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        adminAJAX.showNotification('Güncelleme hatası: ' + error.message, 'error');
                    });
                }
            } else {
                adminAJAX.showNotification('Bu içerik henüz veritabanına kaydedilmedi', 'warning');
            }
        }
        
        function viewContent(filePath) {
            if (filePath) {
                window.open('../' + filePath, '_blank');
            } else {
                adminAJAX.showNotification('Dosya yolu bulunamadı', 'error');
            }
        }
        
        function deleteContent(contentId) {
            if (contentId > 0) {
                if (confirm('Bu içeriği silmek istediğinizden emin misiniz?')) {
                    fetch('ajax-handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete_web_content&content_id=${contentId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            adminAJAX.showNotification('İçerik silindi', 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            adminAJAX.showNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        adminAJAX.showNotification('Silme hatası: ' + error.message, 'error');
                    });
                }
            } else {
                adminAJAX.showNotification('Bu içerik henüz veritabanına kaydedilmedi', 'warning');
            }
        }
        
        function exportContent() {
            adminAJAX.showNotification('İçerik dışa aktarma özelliği geliştiriliyor...', 'info');
        }
        
        function clearContent() {
            if (confirm('Tüm taranan içeriği temizlemek istediğinizden emin misiniz?')) {
                fetch('ajax-handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=clear_web_content'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        adminAJAX.showNotification('Tüm içerik temizlendi', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        adminAJAX.showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    adminAJAX.showNotification('Temizleme hatası: ' + error.message, 'error');
                });
            }
        }
    </script>
</body>
</html>
