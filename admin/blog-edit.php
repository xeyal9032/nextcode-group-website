<?php
// Blog Yazısı Düzenleme - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

require_once "../config/database.php";

$success_message = "";
$error_message = "";
$post = null;
$categories = [];

$post_id = intval($_GET['id'] ?? 0);

if ($post_id <= 0) {
    header("Location: blog.php?error=Geçersiz yazı ID");
    exit;
}

// Kategorileri ve yazı verilerini çek
try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        // Kategorileri çek
        $stmt = $pdo->query("SELECT id, name, description FROM blog_categories ORDER BY name");
        $categories = $stmt->fetchAll();
        
        // Yazı verilerini çek
        $stmt = $pdo->prepare("SELECT id, title, content, excerpt, category_id, status, slug, meta_title, meta_description, created_at, updated_at FROM blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();
        
        if (!$post) {
            header("Location: blog.php?error=Blog yazısı bulunamadı");
            exit;
        }
    }
} catch (Exception $e) {
    $error_message = "Veriler alınamadı.";
}

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
        $slug = sanitizeInput($_POST["slug"] ?? "", "text");
        $excerpt = sanitizeInput($_POST["excerpt"] ?? "", "html");
        $content = sanitizeInput($_POST["content"] ?? "", "html");
        $category_id = intval($_POST["category_id"] ?? 0);
        $featured_image = sanitizeInput($_POST["featured_image"] ?? "", "url");
        $meta_title = sanitizeInput($_POST["meta_title"] ?? "", "text");
        $meta_description = sanitizeInput($_POST["meta_description"] ?? "", "text");
        $status = sanitizeInput($_POST["status"] ?? "draft", "text");
        
        // Slug oluştur
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        }
        
        // Validation
        if (empty($title) || empty($content)) {
            $error_message = "Başlık ve içerik alanları zorunludur.";
        } else {
            try {
                if ($pdo) {
                    // Slug benzersizlik kontrolü (kendi ID'si hariç)
                    $stmt = $pdo->prepare("SELECT id FROM blog_posts WHERE slug = ? AND id != ?");
                    $stmt->execute([$slug, $post_id]);
                    if ($stmt->fetch()) {
                        $slug .= '-' . time();
                    }
                    
                    $stmt = $pdo->prepare("
                        UPDATE blog_posts 
                        SET title = ?, slug = ?, excerpt = ?, content = ?, category_id = ?, 
                            featured_image = ?, meta_title = ?, meta_description = ?, status = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    
                    $stmt->execute([
                        $title, $slug, $excerpt, $content, $category_id, 
                        $featured_image, $meta_title, $meta_description, $status, $post_id
                    ]);
                    
                    $success_message = "Blog yazısı başarıyla güncellendi!";
                    logAuditEvent(AUDIT_CATEGORY_CONTENT, 'BLOG_UPDATED', 'Blog post updated: ' . $title, AUDIT_LEVEL_INFO);
                    
                    // Güncellenmiş verileri tekrar çek
                    $stmt = $pdo->prepare("SELECT id, title, content, excerpt, category_id, status, slug, meta_title, meta_description, created_at, updated_at FROM blog_posts WHERE id = ?");
                    $stmt->execute([$post_id]);
                    $post = $stmt->fetch();
                } else {
                    $error_message = "Veritabanı bağlantı hatası.";
                }
            } catch (Exception $e) {
                $error_message = "Blog yazısı güncellenirken hata oluştu: " . $e->getMessage();
                logAuditEvent(AUDIT_CATEGORY_SYSTEM, 'BLOG_UPDATE_ERROR', $e->getMessage(), AUDIT_LEVEL_ERROR);
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
    <title>Blog Yazısı Düzenle | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="js/admin-ajax.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .form-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; font-size: 1.1em; }
        .form-control { width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-control:focus { outline: none; border-color: #667eea; }
        textarea.form-control { min-height: 120px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; text-decoration: none; font-weight: bold; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; margin-right: 10px; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-danger { background: #fee; color: #c33; border: 1px solid #fcc; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 8px; margin-bottom: 20px; transition: all 0.3s; }
        .back-btn:hover { background: #5a6268; transform: translateY(-2px); }
        .content-editor { min-height: 400px; }
        .meta-section { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .meta-section h3 { color: #333; margin-bottom: 15px; }
        @media (max-width: 768px) { 
            .form-row { grid-template-columns: 1fr; }
            .container { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="blog.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Blog Yönetimi
        </a>
        
        <div class="header">
            <h1><i class="fas fa-edit"></i> Blog Yazısı Düzenle</h1>
            <p><?php echo htmlspecialchars($post['title'] ?? 'Blog Yazısı'); ?> düzenleniyor</p>
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
        
        <?php if ($post): ?>
        <div class="form-container">
            <form method="POST" action="ajax-handler.php" data-ajax-submit>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <div class="form-group">
                    <label for="title"><i class="fas fa-heading"></i> Yazı Başlığı *</label>
                    <input type="text" id="title" name="title" class="form-control" required 
                           value="<?php echo htmlspecialchars($post['title']); ?>"
                           placeholder="Blog yazısı başlığını girin">
                </div>
                
                <div class="form-group">
                    <label for="slug"><i class="fas fa-link"></i> URL Slug</label>
                    <input type="text" id="slug" name="slug" class="form-control" 
                           value="<?php echo htmlspecialchars($post['slug']); ?>"
                           placeholder="otomatik-olarak-olusturulur">
                    <small style="color: #666;">Boş bırakırsanız başlıktan otomatik oluşturulur</small>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id"><i class="fas fa-tags"></i> Kategori</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="">Kategori Seçin</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo $post['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status"><i class="fas fa-eye"></i> Durum</label>
                        <select id="status" name="status" class="form-control">
                            <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Taslak</option>
                            <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Yayınlanmış</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="excerpt"><i class="fas fa-quote-left"></i> Özet</label>
                    <textarea id="excerpt" name="excerpt" class="form-control" rows="3"
                              placeholder="Yazının kısa özeti..."><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="content"><i class="fas fa-align-left"></i> İçerik *</label>
                    <textarea id="content" name="content" class="form-control content-editor" required rows="15"
                              placeholder="Blog yazısı içeriğini buraya yazın..."><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="featured_image"><i class="fas fa-image"></i> Öne Çıkan Resim URL'si</label>
                    <input type="url" id="featured_image" name="featured_image" class="form-control" 
                           value="<?php echo htmlspecialchars($post['featured_image']); ?>"
                           placeholder="https://example.com/image.jpg">
                </div>
                
                <div class="meta-section">
                    <h3><i class="fas fa-search"></i> SEO Ayarları</h3>
                    
                    <div class="form-group">
                        <label for="meta_title"><i class="fas fa-tag"></i> Meta Başlık</label>
                        <input type="text" id="meta_title" name="meta_title" class="form-control" 
                               value="<?php echo htmlspecialchars($post['meta_title']); ?>"
                               placeholder="Arama motorları için başlık">
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_description"><i class="fas fa-align-left"></i> Meta Açıklama</label>
                        <textarea id="meta_description" name="meta_description" class="form-control" rows="3"
                                  placeholder="Arama motorları için açıklama..."><?php echo htmlspecialchars($post['meta_description']); ?></textarea>
                    </div>
                </div>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e1e5e9;">
                    <button type="button" class="btn btn-primary" onclick="updateBlogPost()">
                        <i class="fas fa-save"></i> Değişiklikleri Kaydet
                    </button>
                    <a href="blog.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> İptal
                    </a>
                    <a href="blog-delete.php?id=<?php echo $post_id; ?>" class="btn btn-danger" 
                       onclick="return confirm('Bu blog yazısını silmek istediğinizden emin misiniz?')">
                        <i class="fas fa-trash"></i> Yazıyı Sil
                    </a>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const submitBtn = form.querySelector("button[type='submit']");
            const titleInput = document.getElementById("title");
            const slugInput = document.getElementById("slug");
            
            // Slug otomatik oluşturma
            titleInput.addEventListener("input", function() {
                if (!slugInput.value) {
                    const slug = this.value
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                    slugInput.value = slug;
                }
            });
            
            // Form gönderilmeden önce loading state
            form.addEventListener("submit", function() {
                submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Kaydediliyor...";
                submitBtn.disabled = true;
            });
            
            // Karakter sayacı
            const metaDescription = document.getElementById("meta_description");
            const metaTitle = document.getElementById("meta_title");
            
            metaDescription.addEventListener("input", function() {
                const length = this.value.length;
                const maxLength = 160;
                if (length > maxLength) {
                    this.style.borderColor = "#dc3545";
                } else {
                    this.style.borderColor = "#e1e5e9";
                }
            });
            
            metaTitle.addEventListener("input", function() {
                const length = this.value.length;
                const maxLength = 60;
                if (length > maxLength) {
                    this.style.borderColor = "#dc3545";
                } else {
                    this.style.borderColor = "#e1e5e9";
                }
            });
        });

        // AJAX Blog Update Function
        function updateBlogPost() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            formData.append('action', 'update_blog_post');
            formData.append('post_id', <?php echo $post_id; ?>);
            
            // Show loading state
            const submitBtn = document.querySelector('button[onclick="updateBlogPost()"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Güncelleniyor...';
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