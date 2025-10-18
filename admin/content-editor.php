<?php
// İçerik Düzenleyici - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

$success_message = "";
$error_message = "";

// CSRF Token oluştur
$csrf_token = generateCSRFToken();

$pdo = getSecureDatabaseConnection();
if (!$pdo) {
    $error_message = "Veritabanı bağlantı hatası.";
}

// Sayfa içeriklerini çek
$pages = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM pages ORDER BY title");
        $pages = $stmt->fetchAll();
    } catch (Exception $e) {
        $error_message = "Sayfa verileri alınamadı.";
    }
}

// Seçili sayfa içeriğini çek
$selected_page = null;
$page_id = sanitizeInput($_GET['page_id'] ?? '', 'int');
if ($page_id && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
        $stmt->execute([$page_id]);
        $selected_page = $stmt->fetch();
    } catch (Exception $e) {
        $error_message = "Sayfa verisi alınamadı.";
    }
}

// Form işleme
if ($_SERVER["REQUEST_METHOD"] === "POST" && $selected_page) {
    // CSRF Token kontrolü
    $submitted_token = $_POST["csrf_token"] ?? "";
    if (!verifyCSRFToken($submitted_token)) {
        $error_message = "Güvenlik hatası. Lütfen sayfayı yenileyin.";
    } else {
        // Input sanitization
        $title = sanitizeInput($_POST["title"] ?? "", "text");
        $content = $_POST["content"] ?? ""; // HTML content için sanitize etmiyoruz
        $meta_title = sanitizeInput($_POST["meta_title"] ?? "", "text");
        $meta_description = sanitizeInput($_POST["meta_description"] ?? "", "text");
        
        // Validation
        if (!validateInput($title, "text") || !validateInput($content, "text")) {
            $error_message = "Başlık ve içerik gereklidir.";
        } else {
            if ($pdo) {
                try {
                    // Meta title (eğer boşsa başlık kullan)
                    if (empty($meta_title)) {
                        $meta_title = $title;
                    }
                    
                    $stmt = $pdo->prepare("UPDATE pages SET title = ?, content = ?, meta_title = ?, meta_description = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$title, $content, $meta_title, $meta_description, $page_id]);
                    
                    $success_message = "Sayfa içeriği başarıyla güncellendi!";
                    logSecurityEvent('CONTENT_UPDATED', 'Page updated: ' . $title, 'INFO');
                    
                    // Güncellenmiş sayfa verisini tekrar çek
                    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
                    $stmt->execute([$page_id]);
                    $selected_page = $stmt->fetch();
                    
                } catch (Exception $e) {
                    $error_message = "Veritabanı hatası: " . $e->getMessage();
                    logSecurityEvent('CONTENT_UPDATE_ERROR', $e->getMessage(), 'ERROR');
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
    <title>İçerik Düzenleyici | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .content-wrapper { display: grid; grid-template-columns: 300px 1fr; gap: 30px; }
        .sidebar { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); height: fit-content; }
        .sidebar h3 { color: #333; margin-bottom: 20px; font-size: 1.3em; }
        .page-list { list-style: none; }
        .page-item { margin-bottom: 10px; }
        .page-link { display: block; padding: 12px 15px; text-decoration: none; color: #666; border-radius: 8px; transition: all 0.3s; border: 2px solid transparent; }
        .page-link:hover, .page-link.active { background: #f8f9fa; color: #333; border-color: #667eea; }
        .editor-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; font-size: 1.1em; }
        .form-control { width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-control:focus { outline: none; border-color: #667eea; }
        .editor-wrapper { border: 2px solid #e1e5e9; border-radius: 8px; min-height: 400px; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; text-decoration: none; font-weight: bold; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .meta-section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .meta-section h4 { color: #333; margin-bottom: 15px; }
        .no-page-selected { text-align: center; padding: 50px; color: #666; background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 8px; margin-bottom: 20px; transition: all 0.3s; }
        .back-btn:hover { background: #5a6268; transform: translateY(-2px); }
        @media (max-width: 768px) { 
            .content-wrapper { grid-template-columns: 1fr; }
            .sidebar { order: 2; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Dashboard'a Dön
        </a>
        
        <div class="header">
            <h1><i class="fas fa-edit"></i> İçerik Düzenleyici</h1>
            <p>Web sitesi sayfalarının içeriklerini düzenleyin</p>
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
                    <?php foreach ($pages as $page): ?>
                        <li class="page-item">
                            <a href="?page_id=<?php echo $page['id']; ?>" 
                               class="page-link <?php echo ($selected_page && $selected_page['id'] == $page['id']) ? 'active' : ''; ?>">
                                <i class="fas fa-file"></i> <?php echo htmlspecialchars($page['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="editor-container">
                <?php if ($selected_page): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        
                        <div class="form-group">
                            <label for="title"><i class="fas fa-heading"></i> Sayfa Başlığı *</label>
                            <input type="text" id="title" name="title" class="form-control" required 
                                   value="<?php echo htmlspecialchars($selected_page['title']); ?>" maxlength="255">
                        </div>
                        
                        <div class="form-group">
                            <label for="content"><i class="fas fa-edit"></i> Sayfa İçeriği *</label>
                            <div id="editor" class="editor-wrapper"></div>
                            <textarea id="content" name="content" style="display: none;" required><?php echo htmlspecialchars($selected_page['content']); ?></textarea>
                        </div>
                        
                        <div class="meta-section">
                            <h4><i class="fas fa-search"></i> SEO Ayarları</h4>
                            
                            <div class="form-group">
                                <label for="meta_title"><i class="fas fa-tag"></i> Meta Title</label>
                                <input type="text" id="meta_title" name="meta_title" class="form-control" 
                                       value="<?php echo htmlspecialchars($selected_page['meta_title']); ?>" 
                                       placeholder="Arama motorları için başlık" maxlength="60">
                                <small style="color: #666; font-size: 0.9em;">Önerilen uzunluk: 50-60 karakter</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="meta_description"><i class="fas fa-align-left"></i> Meta Description</label>
                                <textarea id="meta_description" name="meta_description" class="form-control" 
                                          placeholder="Arama motorları için açıklama" maxlength="160"><?php echo htmlspecialchars($selected_page['meta_description']); ?></textarea>
                                <small style="color: #666; font-size: 0.9em;">Önerilen uzunluk: 150-160 karakter</small>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 15px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Değişiklikleri Kaydet
                            </button>
                            <a href="content-editor.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> İptal
                            </a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="no-page-selected">
                        <i class="fas fa-file-alt" style="font-size: 3em; margin-bottom: 20px; color: #ddd;"></i>
                        <h3>Sayfa Seçin</h3>
                        <p>Düzenlemek istediğiniz sayfayı soldaki menüden seçin.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quill Editor -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        <?php if ($selected_page): ?>
        // Quill Editor'ü başlat
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            },
            placeholder: 'Sayfa içeriğini buraya yazın...'
        });
        
        // Mevcut içeriği editöre yükle
        var existingContent = document.querySelector("#content").value;
        if (existingContent) {
            quill.root.innerHTML = existingContent;
        }
        
        // Form gönderilmeden önce editor içeriğini textarea'ya aktar
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            if (form) {
                form.addEventListener("submit", function() {
                    // Editor içeriğini textarea'ya aktar
                    document.querySelector("#content").value = quill.root.innerHTML;
                    
                    const submitBtn = document.querySelector("button[type='submit']");
                    submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Kaydediliyor...";
                    submitBtn.disabled = true;
                });
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>



