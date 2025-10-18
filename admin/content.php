<?php
// Site İçerik Yönetimi - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

$success_message = "";
$error_message = "";
$selected_page = sanitizeInput($_GET['page'] ?? 'home', 'text');
$selected_section = sanitizeInput($_GET['section'] ?? '', 'text');

// CSRF Token oluştur
$csrf_token = generateCSRFToken();

$pdo = getSecureDatabaseConnection();

// Sayfa listesi
$pages = [
    'home' => 'Ana Sayfa',
    'about' => 'Hakkımızda',
    'services' => 'Hizmetler',
    'portfolio' => 'Portfolio',
    'blog' => 'Blog',
    'contact' => 'İletişim',
    'pricing' => 'Fiyatlandırma',
    'faq' => 'Sıkça Sorulan Sorular'
];

// Bölüm listesi
$sections = [];

// Seçili sayfa için bölümleri çek
if ($pdo && $selected_page) {
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT section_name FROM site_content WHERE page_name = ? ORDER BY section_name");
        $stmt->execute([$selected_page]);
        $sections = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        $error_message = "Bölümler alınamadı.";
    }
}

// İçerikleri çek
$contents = [];
if ($pdo && $selected_page) {
    try {
        $sql = "SELECT id, page_name, section_name, content_key, content_value, content_type, created_at, updated_at FROM site_content WHERE page_name = ?";
        $params = [$selected_page];
        
        if ($selected_section) {
            $sql .= " AND section_name = ?";
            $params[] = $selected_section;
        }
        
        $sql .= " ORDER BY section_name, content_key";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $contents = $stmt->fetchAll();
    } catch (Exception $e) {
        $error_message = "İçerikler alınamadı.";
    }
}

// Form işleme
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submitted_token = $_POST["csrf_token"] ?? "";
    if (!verifyCSRFToken($submitted_token)) {
        $error_message = "Güvenlik hatası. Lütfen sayfayı yenileyin.";
    } else {
        $action = $_POST["action"] ?? "";
        
        if ($action === "save_content") {
            // Toplu içerik kaydetme
            if ($pdo) {
                try {
                    $pdo->beginTransaction();
                    
                    foreach ($_POST as $key => $value) {
                        if (strpos($key, 'content_') === 0) {
                            $content_id = str_replace('content_', '', $key);
                            $content_value = sanitizeInput($value, 'html');
                            
                            $stmt = $pdo->prepare("UPDATE site_content SET content_value = ?, updated_at = NOW() WHERE id = ?");
                            $stmt->execute([$content_value, $content_id]);
                        }
                    }
                    
                    $pdo->commit();
                    $success_message = "İçerikler başarıyla güncellendi!";
                    logSecurityEvent('CONTENT_UPDATED', 'Site content updated for page: ' . $selected_page, 'INFO');
                    
                    // Cache temizle
                    require_once "../config/admin-cache.php";
                    AdminCachedData::clearContentCache();
                    
                    // İçerikleri tekrar çek
                    $sql = "SELECT id, page_name, section_name, content_key, content_value, content_type, created_at, updated_at FROM site_content WHERE page_name = ?";
                    $params = [$selected_page];
                    
                    if ($selected_section) {
                        $sql .= " AND section_name = ?";
                        $params[] = $selected_section;
                    }
                    
                    $sql .= " ORDER BY section_name, content_key";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $contents = $stmt->fetchAll();
                    
                } catch (Exception $e) {
                    $pdo->rollback();
                    $error_message = "Veritabanı hatası: " . $e->getMessage();
                    logSecurityEvent('CONTENT_UPDATE_ERROR', $e->getMessage(), 'ERROR');
                }
            }
        } elseif ($action === "add_content") {
            // Yeni içerik ekleme
            $content_key = sanitizeInput($_POST["content_key"] ?? "", "text");
            $content_value = sanitizeInput($_POST["content_value"] ?? "", "html");
            $content_type = sanitizeInput($_POST["content_type"] ?? "text", "text");
            $section_name = sanitizeInput($_POST["section_name"] ?? "", "text");
            
            if ($pdo && $content_key && $section_name) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO site_content (page_name, section_name, content_key, content_value, content_type) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()");
                    $stmt->execute([$selected_page, $section_name, $content_key, $content_value, $content_type]);
                    
                    $success_message = "Yeni içerik başarıyla eklendi!";
                    logSecurityEvent('CONTENT_ADDED', 'New content added: ' . $content_key, 'INFO');
                    
                    // Cache temizle
                    require_once "../config/admin-cache.php";
                    AdminCachedData::clearContentCache();
                    
                    // İçerikleri tekrar çek
                    $sql = "SELECT id, page_name, section_name, content_key, content_value, content_type, created_at, updated_at FROM site_content WHERE page_name = ?";
                    $params = [$selected_page];
                    
                    if ($selected_section) {
                        $sql .= " AND section_name = ?";
                        $params[] = $selected_section;
                    }
                    
                    $sql .= " ORDER BY section_name, content_key";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $contents = $stmt->fetchAll();
                    
                } catch (Exception $e) {
                    $error_message = "İçerik eklenirken hata: " . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token; ?>">
    <title>Site İçerik Yönetimi | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="js/admin-ajax.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .content-wrapper { display: grid; grid-template-columns: 250px 1fr; gap: 30px; }
        .sidebar { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); height: fit-content; }
        .sidebar h3 { color: #333; margin-bottom: 20px; font-size: 1.3em; }
        .page-list { list-style: none; }
        .page-item { margin-bottom: 10px; }
        .page-link { display: block; padding: 12px 15px; text-decoration: none; color: #666; border-radius: 8px; transition: all 0.3s; border: 2px solid transparent; }
        .page-link:hover, .page-link.active { background: #f8f9fa; color: #333; border-color: #667eea; }
        .section-list { margin-top: 20px; }
        .section-item { margin-bottom: 8px; }
        .section-link { display: block; padding: 8px 12px; text-decoration: none; color: #666; border-radius: 6px; transition: all 0.3s; font-size: 0.9em; }
        .section-link:hover, .section-link.active { background: #e9ecef; color: #333; }
        .main-content { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; font-size: 1.1em; }
        .form-control { width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-control:focus { outline: none; border-color: #667eea; }
        textarea.form-control { min-height: 120px; resize: vertical; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; text-decoration: none; font-weight: bold; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-danger { background: #fee; color: #c33; border: 1px solid #fcc; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .content-item { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #667eea; }
        .content-header { display: flex; justify-content: between; align-items: center; margin-bottom: 15px; }
        .content-key { font-weight: bold; color: #333; font-size: 1.1em; }
        .content-type { background: #667eea; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; margin-left: 10px; }
        .content-section { color: #666; font-size: 0.9em; margin-bottom: 10px; }
        .back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 8px; margin-bottom: 20px; transition: all 0.3s; }
        .back-btn:hover { background: #5a6268; transform: translateY(-2px); }
        .add-content-form { background: #f8f9fa; padding: 25px; border-radius: 10px; margin-bottom: 30px; border: 2px dashed #667eea; }
        .add-content-form h3 { color: #333; margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .no-content { text-align: center; padding: 50px; color: #666; background: white; border-radius: 15px; }
        @media (max-width: 768px) { 
            .content-wrapper { grid-template-columns: 1fr; }
            .sidebar { order: 2; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Dashboard'a Dön
        </a>
        
        <div class="header">
            <h1><i class="fas fa-edit"></i> Site İçerik Yönetimi</h1>
            <p>Tüm sayfa içeriklerini A'dan Z'ye yönetin</p>
        </div>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="content-wrapper">
            <div class="sidebar">
                <h3><i class="fas fa-file-alt"></i> Sayfalar</h3>
                <ul class="page-list">
                    <?php foreach ($pages as $page_key => $page_name): ?>
                        <li class="page-item">
                            <a href="?page=<?php echo $page_key; ?>" 
                               class="page-link <?php echo ($selected_page == $page_key) ? 'active' : ''; ?>">
                                <i class="fas fa-file"></i> <?php echo htmlspecialchars($page_name); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <?php if (!empty($sections)): ?>
                    <div class="section-list">
                        <h4><i class="fas fa-layer-group"></i> Bölümler</h4>
                        <ul class="page-list">
                            <li class="section-item">
                                <a href="?page=<?php echo $selected_page; ?>" 
                                   class="section-link <?php echo empty($selected_section) ? 'active' : ''; ?>">
                                    <i class="fas fa-list"></i> Tümü
                                </a>
                            </li>
                            <?php foreach ($sections as $section): ?>
                                <li class="section-item">
                                    <a href="?page=<?php echo $selected_page; ?>&section=<?php echo urlencode($section); ?>" 
                                       class="section-link <?php echo ($selected_section == $section) ? 'active' : ''; ?>">
                                        <i class="fas fa-folder"></i> <?php echo htmlspecialchars($section); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="main-content">
                <?php if ($selected_page): ?>
                    <h2><i class="fas fa-edit"></i> <?php echo htmlspecialchars($pages[$selected_page]); ?> İçerikleri</h2>
                    
                    <div class="add-content-form">
                        <h3><i class="fas fa-plus"></i> Yeni İçerik Ekle</h3>
                        <form id="add-content-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="section_name">Bölüm Adı *</label>
                                    <input type="text" id="section_name" name="section_name" class="form-control" required 
                                           placeholder="örn: hero, about, services">
                                </div>
                                
                                <div class="form-group">
                                    <label for="content_key">İçerik Anahtarı *</label>
                                    <input type="text" id="content_key" name="content_key" class="form-control" required 
                                           placeholder="örn: title, description, button_text">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="content_type">İçerik Tipi</label>
                                    <select id="content_type" name="content_type" class="form-control">
                                        <option value="text">Metin</option>
                                        <option value="html">HTML</option>
                                        <option value="image">Resim URL</option>
                                        <option value="json">JSON</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="content_value">İçerik *</label>
                                <textarea id="content_value" name="content_value" class="form-control" required 
                                          placeholder="İçeriği buraya yazın..."></textarea>
                            </div>
                            
                            <button type="button" class="btn btn-success" onclick="addContentAJAX()">
                                <i class="fas fa-plus"></i> İçerik Ekle
                            </button>
                        </form>
                    </div>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="action" value="save_content">
                        
                        <?php if (!empty($contents)): ?>
                            <?php 
                            $current_section = '';
                            foreach ($contents as $content): 
                                if ($content['section_name'] ?? '' != $current_section):
                                    $current_section = $content['section_name'] ?? '';
                                    if (!empty($current_section)):
                            ?>
                                <h3 style="margin: 30px 0 20px 0; color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                                    <i class="fas fa-folder"></i> <?php echo htmlspecialchars($current_section); ?>
                                </h3>
                            <?php 
                                    endif;
                                endif; 
                            ?>
                            
                            <div class="content-item" data-content-id="<?php echo $content['id'] ?? 0; ?>">
                                <div class="content-header">
                                    <div>
                                        <span class="content-key"><?php echo htmlspecialchars($content['content_key'] ?? '' ?? ''); ?></span>
                                        <span class="content-type"><?php echo htmlspecialchars($content['content_type'] ?? 'text' ?? ''); ?></span>
                                    </div>
                                    <div class="content-actions">
                                        <button class="btn btn-danger btn-sm" 
                                                data-ajax-action="delete_content" 
                                                data-target-id="<?php echo $content['id'] ?? 0; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="content-section">
                                    <strong>Bölüm:</strong> <?php echo htmlspecialchars($content['section_name'] ?? '' ?? ''); ?>
                                    <span style="margin-left: 20px;">
                                        <strong>Güncellendi:</strong> <?php echo date('d.m.Y H:i', strtotime($content['updated_at'] ?? '')); ?>
                                    </span>
                                </div>
                                
                                <?php if ($content['content_type'] ?? 'text' === 'html'): ?>
                                    <textarea class="form-control content-input" data-content-id="<?php echo $content['id'] ?? 0; ?>" rows="6"><?php echo htmlspecialchars($content['content_value'] ?? '' ?? ''); ?></textarea>
                                <?php elseif ($content['content_type'] ?? 'text' === 'text'): ?>
                                    <input type="text" class="form-control content-input" data-content-id="<?php echo $content['id'] ?? 0; ?>" 
                                           value="<?php echo htmlspecialchars($content['content_value'] ?? '' ?? ''); ?>">
                                <?php else: ?>
                                    <textarea class="form-control content-input" data-content-id="<?php echo $content['id'] ?? 0; ?>" rows="3"><?php echo htmlspecialchars($content['content_value'] ?? '' ?? ''); ?></textarea>
                                <?php endif; ?>
                                
                                <div class="content-save-btn" style="margin-top: 10px;">
                                    <button type="button" class="btn btn-primary btn-sm" 
                                            onclick="adminAJAX.updateContent(<?php echo $content['id'] ?? 0; ?>, document.querySelector('[data-content-id=\'<?php echo $content['id'] ?? 0; ?>\'] .content-input').value)">
                                        <i class="fas fa-save"></i> Kaydet
                                    </button>
                                </div>
                            </div>
                            
                            <?php endforeach; ?>
                            
                            <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Tüm Değişiklikleri Kaydet
                                </button>
                                <a href="?page=<?php echo $selected_page; ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> İptal
                                </a>
                            </div>
                            
                        <?php else: ?>
                            <div class="no-content">
                                <i class="fas fa-file-alt" style="font-size: 3em; margin-bottom: 20px; color: #ddd;"></i>
                                <h3>Henüz İçerik Yok</h3>
                                <p>Bu sayfa için henüz içerik eklenmemiş.</p>
                                <p>Yukarıdaki formu kullanarak yeni içerik ekleyebilirsiniz.</p>
                            </div>
                        <?php endif; ?>
                    </form>
                    
                <?php else: ?>
                    <div class="no-content">
                        <i class="fas fa-mouse-pointer" style="font-size: 3em; margin-bottom: 20px; color: #ddd;"></i>
                        <h3>Sayfa Seçin</h3>
                        <p>Düzenlemek istediğiniz sayfayı soldaki menüden seçin.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Content management loaded");
            
            // Form gönderilmeden önce loading state
            const forms = document.querySelectorAll("form");
            forms.forEach(form => {
                form.addEventListener("submit", function() {
                    const submitBtn = form.querySelector("button[type='submit']");
                    if (submitBtn) {
                        submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Kaydediliyor...";
                        submitBtn.disabled = true;
                    }
                });
            });
            
            // AJAX Content Management Functions
            window.addContentAJAX = function() {
                const sectionName = document.getElementById('section_name').value;
                const contentKey = document.getElementById('content_key').value;
                const contentValue = document.getElementById('content_value').value;
                const contentType = document.getElementById('content_type').value;
                
                if (!sectionName || !contentKey || !contentValue) {
                    alert('Lütfen tüm alanları doldurun');
                    return;
                }
                
                // Get current page from URL
                const urlParams = new URLSearchParams(window.location.search);
                const currentPage = urlParams.get('page') || 'home';
                
                adminAJAX.addContent(currentPage, sectionName, contentKey, contentValue, contentType);
                
                // Clear form
                document.getElementById('section_name').value = '';
                document.getElementById('content_key').value = '';
                document.getElementById('content_value').value = '';
                document.getElementById('content_type').value = 'text';
            };
            
            // İçerik tipi değiştiğinde placeholder güncelle
            const contentTypeSelect = document.getElementById("content_type");
            const contentValueTextarea = document.getElementById("content_value");
            
            if (contentTypeSelect && contentValueTextarea) {
                contentTypeSelect.addEventListener("change", function() {
                    switch(this.value) {
                        case 'text':
                            contentValueTextarea.placeholder = "Kısa metin girin...";
                            break;
                        case 'html':
                            contentValueTextarea.placeholder = "HTML içeriği girin...";
                            break;
                        case 'image':
                            contentValueTextarea.placeholder = "Resim URL'si girin...";
                            break;
                        case 'json':
                            contentValueTextarea.placeholder = "JSON verisi girin...";
                            break;
                    }
                });
            }
        });
    </script>
</body>
</html>


