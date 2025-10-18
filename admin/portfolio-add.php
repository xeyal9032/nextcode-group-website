<?php
// Portfolio Proje Ekleme - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

require_once "../config/database.php";

$success_message = "";
$error_message = "";

// CSRF Token oluştur
$csrf_token = generateCSRFToken();

// Form işleme
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submitted_token = $_POST["csrf_token"] ?? "";
    if (!verifyCSRFToken($submitted_token)) {
        $error_message = "Güvenlik hatası. Lütfen sayfayı yenileyin.";
    } else {
        // Input sanitization
        $title = sanitizeInput($_POST["title"] ?? "", "text");
        $description = sanitizeInput($_POST["description"] ?? "", "html");
        $category = sanitizeInput($_POST["category"] ?? "", "text");
        $technologies = sanitizeInput($_POST["technologies"] ?? "", "text");
        $client = sanitizeInput($_POST["client"] ?? "", "text");
        $project_url = sanitizeInput($_POST["project_url"] ?? "", "url");
        $github_url = sanitizeInput($_POST["github_url"] ?? "", "url");
        $image_url = sanitizeInput($_POST["featured_image"] ?? "", "url");
        $is_active = isset($_POST["is_active"]) ? 1 : 0;
        
        // Validation
        if (empty($title) || empty($description)) {
            $error_message = "Başlık ve açıklama alanları zorunludur.";
        } else {
            try {
                $db = new Database();
                $pdo = $db->getConnection();
                
                if ($pdo) {
                    $stmt = $pdo->prepare("
                        INSERT INTO portfolio_projects 
                        (title, description, category, technologies, client, project_url, github_url, image_url, is_active, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ");
                    
                    $stmt->execute([
                        $title, $description, $category, $technologies, $client, 
                        $project_url, $github_url, $image_url, $is_active
                    ]);
                    
                    $success_message = "Proje başarıyla eklendi!";
                    logAuditEvent(AUDIT_CATEGORY_CONTENT, 'PORTFOLIO_ADDED', 'New portfolio project added: ' . $title, AUDIT_LEVEL_INFO);
                    
                    // Cache temizle
                    require_once "../config/admin-cache.php";
                    AdminCachedData::clearPortfolioCache();
                    
                    // Formu temizle
                    $_POST = [];
                } else {
                    $error_message = "Veritabanı bağlantı hatası.";
                }
            } catch (Exception $e) {
                $error_message = "Proje eklenirken hata oluştu: " . $e->getMessage();
                logAuditEvent(AUDIT_CATEGORY_SYSTEM, 'PORTFOLIO_ADD_ERROR', $e->getMessage(), AUDIT_LEVEL_ERROR);
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
    <title>Yeni Proje Ekle | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="js/admin-ajax.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .form-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; font-size: 1.1em; }
        .form-control { width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-control:focus { outline: none; border-color: #667eea; }
        textarea.form-control { min-height: 120px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        .checkbox-group input[type="checkbox"] { width: 18px; height: 18px; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; text-decoration: none; font-weight: bold; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; margin-right: 10px; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-danger { background: #fee; color: #c33; border: 1px solid #fcc; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 8px; margin-bottom: 20px; transition: all 0.3s; }
        .back-btn:hover { background: #5a6268; transform: translateY(-2px); }
        @media (max-width: 768px) { 
            .form-row { grid-template-columns: 1fr; }
            .container { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="portfolio.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Portfolio Yönetimi
        </a>
        
        <div class="header">
            <h1><i class="fas fa-plus"></i> Yeni Proje Ekle</h1>
            <p>Portfolio'ya yeni bir proje ekleyin</p>
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
        
        <div class="form-container">
            <form method="POST" action="ajax-handler.php" data-ajax-submit>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <div class="form-group">
                    <label for="title"><i class="fas fa-heading"></i> Proje Başlığı *</label>
                    <input type="text" id="title" name="title" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                           placeholder="Proje başlığını girin">
                </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Proje Açıklaması *</label>
                    <textarea id="description" name="description" class="form-control" required rows="6"
                              placeholder="Proje detaylarını açıklayın..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category"><i class="fas fa-tags"></i> Kategori</label>
                        <select id="category" name="category" class="form-control">
                            <option value="">Kategori Seçin</option>
                            <option value="Web Development" <?php echo ($_POST['category'] ?? '') === 'Web Development' ? 'selected' : ''; ?>>Web Development</option>
                            <option value="Mobile App" <?php echo ($_POST['category'] ?? '') === 'Mobile App' ? 'selected' : ''; ?>>Mobile App</option>
                            <option value="E-commerce" <?php echo ($_POST['category'] ?? '') === 'E-commerce' ? 'selected' : ''; ?>>E-commerce</option>
                            <option value="Digital Marketing" <?php echo ($_POST['category'] ?? '') === 'Digital Marketing' ? 'selected' : ''; ?>>Digital Marketing</option>
                            <option value="UI/UX Design" <?php echo ($_POST['category'] ?? '') === 'UI/UX Design' ? 'selected' : ''; ?>>UI/UX Design</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="client"><i class="fas fa-user-tie"></i> Müşteri</label>
                        <input type="text" id="client" name="client" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['client'] ?? ''); ?>"
                               placeholder="Müşteri adı">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="technologies"><i class="fas fa-code"></i> Kullanılan Teknolojiler</label>
                    <input type="text" id="technologies" name="technologies" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['technologies'] ?? ''); ?>"
                           placeholder="PHP, JavaScript, MySQL, Bootstrap vb. (virgülle ayırın)">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="project_url"><i class="fas fa-external-link-alt"></i> Proje URL'si</label>
                        <input type="url" id="project_url" name="project_url" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['project_url'] ?? ''); ?>"
                               placeholder="https://example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="github_url"><i class="fab fa-github"></i> GitHub URL'si</label>
                        <input type="url" id="github_url" name="github_url" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['github_url'] ?? ''); ?>"
                               placeholder="https://github.com/username/project">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="featured_image"><i class="fas fa-image"></i> Öne Çıkan Resim URL'si</label>
                    <input type="url" id="featured_image" name="featured_image" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['featured_image'] ?? ''); ?>"
                           placeholder="https://example.com/image.jpg">
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" 
                               <?php echo isset($_POST['is_active']) ? 'checked' : 'checked'; ?>>
                        <label for="is_active">Projeyi aktif olarak göster</label>
                    </div>
                </div>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e1e5e9;">
                    <button type="button" class="btn btn-primary" onclick="addPortfolioProject()">
                        <i class="fas fa-save"></i> Projeyi Kaydet
                    </button>
                    <a href="portfolio.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> İptal
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const submitBtn = form.querySelector("button[type='submit']");
            
            form.addEventListener("submit", function() {
                submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Kaydediliyor...";
                submitBtn.disabled = true;
            });
            
            // URL validasyonu
            const urlInputs = document.querySelectorAll("input[type='url']");
            urlInputs.forEach(input => {
                input.addEventListener("blur", function() {
                    if (this.value && !this.checkValidity()) {
                        this.style.borderColor = "#dc3545";
                    } else {
                        this.style.borderColor = "#e1e5e9";
                    }
                });
            });
        });

        // AJAX Portfolio Add Function
        function addPortfolioProject() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            formData.append('action', 'add_portfolio');
            
            // Show loading state
            const submitBtn = document.querySelector('button[onclick="addPortfolioProject()"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Kaydediliyor...';
            submitBtn.disabled = true;
            
            fetch('ajax-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    adminAJAX.showNotification(data.message, 'success');
                    
                    // Redirect after success
                    setTimeout(() => {
                        window.location.href = data.data.redirect_url;
                    }, 1500);
                } else {
                    adminAJAX.showNotification(data.message, 'error');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                adminAJAX.showNotification('Bir hata oluştu: ' + error.message, 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
    </script>
</body>
</html>