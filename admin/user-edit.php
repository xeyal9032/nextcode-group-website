<?php
// Kullanıcı Düzenleme - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

$success_message = "";
$error_message = "";
$user = null;

$user_id = intval($_GET['id'] ?? 0);

if ($user_id <= 0) {
    header("Location: users.php?error=Geçersiz kullanıcı ID");
    exit;
}

// Kullanıcı verilerini çek
$pdo = getSecureDatabaseConnection();
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, full_name, role, is_active, locked_until, created_at FROM admin_users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!$user) {
            header("Location: users.php?error=Kullanıcı bulunamadı");
            exit;
        }
    } catch (Exception $e) {
        $error_message = "Kullanıcı verileri alınamadı.";
        logAuditEvent(AUDIT_CATEGORY_SYSTEM, 'USER_FETCH_ERROR', $e->getMessage(), AUDIT_LEVEL_ERROR);
    }
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
        $username = sanitizeInput($_POST["username"] ?? "", "username");
        $email = sanitizeInput($_POST["email"] ?? "", "email");
        $full_name = sanitizeInput($_POST["full_name"] ?? "", "text");
        $role = sanitizeInput($_POST["role"] ?? "viewer", "text");
        $password = $_POST["password"] ?? "";
        $is_active = isset($_POST["is_active"]) ? 1 : 0;
        
        // Validation
        if (empty($username) || empty($email) || empty($full_name)) {
            $error_message = "Tüm zorunlu alanları doldurun.";
        } elseif (!validateInput($username, "username")) {
            $error_message = "Kullanıcı adı 3-20 karakter arasında olmalı ve sadece harf, rakam ve alt çizgi içermeli.";
        } elseif (!validateInput($email, "email")) {
            $error_message = "Geçerli bir e-posta adresi girin.";
        } elseif (!empty($password) && !validateInput($password, "password")) {
            $error_message = "Şifre en az 8 karakter olmalıdır.";
        } elseif (!in_array($role, ['admin', 'editor', 'viewer'])) {
            $error_message = "Geçersiz rol seçimi.";
        } else {
            try {
                if ($pdo) {
                    // Kullanıcı adı ve e-posta benzersizlik kontrolü (kendi ID'si hariç)
                    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE (username = ? OR email = ?) AND id != ?");
                    $stmt->execute([$username, $email, $user_id]);
                    if ($stmt->fetch()) {
                        $error_message = "Bu kullanıcı adı veya e-posta adresi zaten kullanılıyor.";
                    } else {
                        // Şifre güncelleme
                        if (!empty($password)) {
                            $password_hash = hashPassword($password);
                            $stmt = $pdo->prepare("
                                UPDATE admin_users 
                                SET username = ?, email = ?, full_name = ?, role = ?, password_hash = ?, is_active = ?, updated_at = NOW()
                                WHERE id = ?
                            ");
                            $stmt->execute([$username, $email, $full_name, $role, $password_hash, $is_active, $user_id]);
                        } else {
                            $stmt = $pdo->prepare("
                                UPDATE admin_users 
                                SET username = ?, email = ?, full_name = ?, role = ?, is_active = ?, updated_at = NOW()
                                WHERE id = ?
                            ");
                            $stmt->execute([$username, $email, $full_name, $role, $is_active, $user_id]);
                        }
                        
                        $success_message = "Kullanıcı başarıyla güncellendi!";
                        logAuditEvent(AUDIT_CATEGORY_USER_MANAGEMENT, 'USER_UPDATED', 'User updated: ' . $username, AUDIT_LEVEL_INFO);
                        
                        // Güncellenmiş verileri tekrar çek
                        $stmt = $pdo->prepare("SELECT id, username, email, full_name, role, is_active, locked_until, created_at FROM admin_users WHERE id = ?");
                        $stmt->execute([$user_id]);
                        $user = $stmt->fetch();
                    }
                } else {
                    $error_message = "Veritabanı bağlantı hatası.";
                }
            } catch (Exception $e) {
                $error_message = "Kullanıcı güncellenirken hata oluştu: " . $e->getMessage();
                logAuditEvent(AUDIT_CATEGORY_SYSTEM, 'USER_UPDATE_ERROR', $e->getMessage(), AUDIT_LEVEL_ERROR);
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
    <title>Kullanıcı Düzenle | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="js/admin-ajax.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .form-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; font-size: 1.1em; }
        .form-control { width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-control:focus { outline: none; border-color: #667eea; }
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
        .role-info { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 10px; }
        .role-info h4 { color: #333; margin-bottom: 10px; }
        .role-info ul { margin-left: 20px; color: #666; }
        .password-info { background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 10px; border-left: 4px solid #ffc107; }
        .password-info p { color: #856404; margin-bottom: 5px; }
        @media (max-width: 768px) { 
            .form-row { grid-template-columns: 1fr; }
            .container { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="users.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kullanıcı Yönetimi
        </a>
        
        <div class="header">
            <h1><i class="fas fa-user-edit"></i> Kullanıcı Düzenle</h1>
            <p><?php echo htmlspecialchars($user['full_name'] ?? 'Kullanıcı'); ?> düzenleniyor</p>
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
        
        <?php if ($user): ?>
        <div class="form-container">
            <form method="POST" action="ajax-handler.php" data-ajax-submit>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Kullanıcı Adı *</label>
                        <input type="text" id="username" name="username" class="form-control" required 
                               value="<?php echo htmlspecialchars($user['username']); ?>"
                               placeholder="Kullanıcı adı">
                        <small style="color: #666;">3-20 karakter, sadece harf, rakam ve alt çizgi</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> E-posta *</label>
                        <input type="email" id="email" name="email" class="form-control" required 
                               value="<?php echo htmlspecialchars($user['email']); ?>"
                               placeholder="ornek@email.com">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-id-card"></i> Tam Adı *</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required 
                           value="<?php echo htmlspecialchars($user['full_name']); ?>"
                           placeholder="Ad Soyad">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="role"><i class="fas fa-user-shield"></i> Rol *</label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="viewer" <?php echo $user['role'] === 'viewer' ? 'selected' : ''; ?>>Görüntüleyici</option>
                            <option value="editor" <?php echo $user['role'] === 'editor' ? 'selected' : ''; ?>>Editör</option>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Yönetici</option>
                        </select>
                        
                        <div class="role-info">
                            <h4>Rol Yetkileri:</h4>
                            <ul id="role-description">
                                <li><strong>Görüntüleyici:</strong> Sadece içerikleri görüntüleyebilir</li>
                                <li><strong>Editör:</strong> İçerik ekleyebilir, düzenleyebilir ve silebilir</li>
                                <li><strong>Yönetici:</strong> Tüm yetkiler + kullanıcı yönetimi</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Yeni Şifre</label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Değiştirmek istemiyorsanız boş bırakın">
                        
                        <div class="password-info">
                            <p><i class="fas fa-info-circle"></i> Şifre değiştirmek istemiyorsanız bu alanı boş bırakın.</p>
                            <p>Yeni şifre en az 8 karakter olmalıdır.</p>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" 
                               <?php echo $user['is_active'] ? 'checked' : ''; ?>>
                        <label for="is_active">Kullanıcıyı aktif olarak göster</label>
                    </div>
                </div>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e1e5e9;">
                    <button type="button" class="btn btn-primary" onclick="updateUser()">
                        <i class="fas fa-save"></i> Değişiklikleri Kaydet
                    </button>
                    <a href="users.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> İptal
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
            
            // Form gönderilmeden önce loading state
            form.addEventListener("submit", function() {
                submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Kaydediliyor...";
                submitBtn.disabled = true;
            });
            
            // Kullanıcı adı validasyonu
            const usernameInput = document.getElementById("username");
            usernameInput.addEventListener("input", function() {
                const value = this.value;
                const isValid = /^[a-zA-Z0-9_]{3,20}$/.test(value);
                this.style.borderColor = isValid ? "#e1e5e9" : "#dc3545";
            });
            
            // E-posta validasyonu
            const emailInput = document.getElementById("email");
            emailInput.addEventListener("blur", function() {
                const isValid = this.checkValidity();
                this.style.borderColor = isValid ? "#e1e5e9" : "#dc3545";
            });
            
            // Şifre validasyonu
            const passwordInput = document.getElementById("password");
            passwordInput.addEventListener("input", function() {
                if (this.value.length > 0 && this.value.length < 8) {
                    this.style.borderColor = "#dc3545";
                } else {
                    this.style.borderColor = "#e1e5e9";
                }
            });
        });

        // AJAX User Update Function
        function updateUser() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            formData.append('action', 'update_user');
            formData.append('user_id', <?php echo $user_id; ?>);
            
            // Show loading state
            const submitBtn = document.querySelector('button[onclick="updateUser()"]');
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