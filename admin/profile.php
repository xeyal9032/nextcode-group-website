<?php
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}
require_once 'includes/security.php';

// Admin yetkisi kontrolü
checkAdminPermission();

$pdo = getSecureDatabaseConnection();
if (!$pdo) {
    die('Database connection failed');
}

$page_title = 'Profil';
$page_description = 'Profil bilgilerinizi düzenleyin';

$success_message = "";
$error_message = "";

// Profil güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $username = sanitizeInput($_POST['username'] ?? '', 'text');
        $email = sanitizeInput($_POST['email'] ?? '', 'email');
        $full_name = sanitizeInput($_POST['full_name'] ?? '', 'text');
        $phone = sanitizeInput($_POST['phone'] ?? '', 'text');
        $bio = sanitizeInput($_POST['bio'] ?? '', 'text');
        
        // Validation
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'Kullanıcı adı gereklidir';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Kullanıcı adı en az 3 karakter olmalıdır';
        }
        
        if (empty($email)) {
            $errors[] = 'E-posta adresi gereklidir';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir e-posta adresi giriniz';
        }
        
        if (empty($errors)) {
            try {
                $user_id = $_SESSION['admin_user_id'] ?? null;
                
                if (!$user_id) {
                    $error_message = 'Kullanıcı oturumu bulunamadı. Lütfen tekrar giriş yapın.';
                } else {
                    // Check if username or email already exists (excluding current user)
                    $stmt = $pdo->prepare("
                    SELECT id FROM admin_users 
                    WHERE (username = ? OR email = ?) AND id != ?
                ");
                $stmt->execute([$username, $email, $user_id]);
                
                if ($stmt->rowCount() > 0) {
                    $error_message = 'Bu kullanıcı adı veya e-posta adresi zaten kullanılıyor';
                } else {
                    // Update profile
                    $stmt = $pdo->prepare("
                        UPDATE admin_users 
                        SET username = ?, email = ?, full_name = ?, phone = ?, bio = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$username, $email, $full_name, $phone, $bio, $user_id]);
                    
                    // Update session
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['admin_email'] = $email;
                    
                    logAuditEvent('USER_MANAGEMENT', 'PROFILE_UPDATED', "Profile updated for user: $username", 'INFO');
                    
                    $success_message = 'Profil başarıyla güncellendi';
                }
                }
            } catch (Exception $e) {
                $error_message = 'Güncelleme hatası: ' . $e->getMessage();
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
    
    elseif ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($current_password)) {
            $errors[] = 'Mevcut şifre gereklidir';
        }
        
        if (empty($new_password)) {
            $errors[] = 'Yeni şifre gereklidir';
        } elseif (strlen($new_password) < 8) {
            $errors[] = 'Yeni şifre en az 8 karakter olmalıdır';
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = 'Yeni şifreler eşleşmiyor';
        }
        
        if (empty($errors)) {
            try {
                $user_id = $_SESSION['admin_user_id'] ?? null;
                
                if (!$user_id) {
                    $error_message = 'Kullanıcı oturumu bulunamadı. Lütfen tekrar giriş yapın.';
                } else {
                    // Verify current password
                $stmt = $pdo->prepare("SELECT password FROM admin_users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$user || !password_verify($current_password, $user['password'])) {
                    $error_message = 'Mevcut şifre yanlış';
                } else {
                    // Update password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("
                        UPDATE admin_users 
                        SET password = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$hashed_password, $user_id]);
                    
                    logAuditEvent('USER_MANAGEMENT', 'PASSWORD_CHANGED', "Password changed for user: " . ($_SESSION['admin_username'] ?? 'Unknown'), 'INFO');
                    
                    $success_message = 'Şifre başarıyla değiştirildi';
                }
                }
            } catch (Exception $e) {
                $error_message = 'Şifre değiştirme hatası: ' . $e->getMessage();
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

// Kullanıcı bilgilerini getir
$user_info = [];
try {
    $user_id = $_SESSION['admin_user_id'] ?? null;
    
    if (!$user_id) {
        $error_message = 'Kullanıcı oturumu bulunamadı. Lütfen tekrar giriş yapın.';
    } else {
        $stmt = $pdo->prepare("
        SELECT 
            au.*,
            ur.role_name,
            ur.description as role_description
        FROM admin_users au
        LEFT JOIN user_roles ur ON au.role = ur.role_key
        WHERE au.id = ?
    ");
        $stmt->execute([$user_id]);
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("User info fetch error: " . $e->getMessage());
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1><i class="fas fa-user"></i> Profil</h1>
                <p>Profil bilgilerinizi düzenleyin ve hesap ayarlarınızı yönetin</p>
            </div>
        </div>
    </div>

    <!-- Başarı/Hata Mesajları -->
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Profil Bilgileri -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user-edit"></i> Profil Bilgileri</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" data-ajax-submit>
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kullanıcı Adı *</label>
                                    <input type="text" name="username" class="form-control" 
                                           value="<?php echo htmlspecialchars($user_info['username'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">E-posta *</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ad Soyad</label>
                                    <input type="text" name="full_name" class="form-control" 
                                           value="<?php echo htmlspecialchars($user_info['full_name'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Telefon</label>
                                    <input type="tel" name="phone" class="form-control" 
                                           value="<?php echo htmlspecialchars($user_info['phone'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Hakkımda</label>
                            <textarea name="bio" class="form-control" rows="4" 
                                      placeholder="Kendiniz hakkında kısa bir açıklama..."><?php echo htmlspecialchars($user_info['bio'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Profili Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Şifre Değiştirme -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-lock"></i> Şifre Değiştir</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" data-ajax-submit>
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="mb-3">
                            <label class="form-label">Mevcut Şifre *</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Yeni Şifre *</label>
                                    <input type="password" name="new_password" class="form-control" required minlength="8">
                                    <small class="form-text text-muted">En az 8 karakter olmalıdır</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Yeni Şifre Tekrar *</label>
                                    <input type="password" name="confirm_password" class="form-control" required minlength="8">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key"></i> Şifreyi Değiştir
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Profil Özeti -->
        <div class="col-lg-4">
            <!-- Kullanıcı Kartı -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user"></i> Profil Özeti</h5>
                </div>
                <div class="card-body text-center">
                    <div class="profile-avatar mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    
                    <h4><?php echo htmlspecialchars($user_info['full_name'] ?? $user_info['username'] ?? 'Kullanıcı'); ?></h4>
                    <p class="text-muted">@<?php echo htmlspecialchars($user_info['username'] ?? ''); ?></p>
                    
                    <?php if ($user_info['email']): ?>
                        <p class="mb-2">
                            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user_info['email']); ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($user_info['phone']): ?>
                        <p class="mb-2">
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($user_info['phone']); ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <span class="badge bg-info"><?php echo htmlspecialchars($user_info['role_name'] ?? $user_info['role'] ?? 'Admin'); ?></span>
                    </div>
                    
                    <?php if ($user_info['bio']): ?>
                        <div class="mt-3">
                            <small class="text-muted"><?php echo htmlspecialchars($user_info['bio']); ?></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hesap Bilgileri -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Hesap Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="account-info">
                        <div class="info-item mb-3">
                            <strong>Kullanıcı ID:</strong><br>
                            <span class="text-muted">#<?php echo $user_info['id'] ?? ''; ?></span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <strong>Kayıt Tarihi:</strong><br>
                            <span class="text-muted"><?php echo formatDate($user_info['created_at'] ?? ''); ?></span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <strong>Son Güncelleme:</strong><br>
                            <span class="text-muted"><?php echo formatDate($user_info['updated_at'] ?? ''); ?></span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <strong>Son Giriş:</strong><br>
                            <span class="text-muted"><?php echo formatDate($user_info['last_login'] ?? ''); ?></span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <strong>Durum:</strong><br>
                            <?php 
                            $status = $user_info['status'] ?? 'active';
                            $statusClass = $status === 'active' ? 'success' : 'danger';
                            $statusText = $status === 'active' ? 'Aktif' : 'Pasif';
                            ?>
                            <span class="badge bg-<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                        </div>
                        
                        <?php if ($user_info['failed_attempts'] > 0): ?>
                            <div class="info-item mb-3">
                                <strong>Başarısız Giriş:</strong><br>
                                <span class="text-warning"><?php echo $user_info['failed_attempts']; ?> kez</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-bolt"></i> Hızlı İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="index.php" class="btn btn-outline-primary">
                            <i class="fas fa-tachometer-alt"></i> Dashboard'a Dön
                        </a>
                        
                        <a href="settings.php" class="btn btn-outline-secondary">
                            <i class="fas fa-cog"></i> Ayarlar
                        </a>
                        
                        <a href="audit-logs.php?user_id=<?php echo $user_info['id'] ?? ''; ?>" class="btn btn-outline-info">
                            <i class="fas fa-history"></i> Aktivite Geçmişi
                        </a>
                        
                        <button type="button" class="btn btn-outline-warning" onclick="exportProfile()">
                            <i class="fas fa-download"></i> Profili Dışa Aktar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/admin-ajax.js"></script>
<script>
function exportProfile() {
    // Profil bilgilerini JSON olarak dışa aktar
    const profileData = {
        username: '<?php echo addslashes($user_info['username'] ?? ''); ?>',
        email: '<?php echo addslashes($user_info['email'] ?? ''); ?>',
        full_name: '<?php echo addslashes($user_info['full_name'] ?? ''); ?>',
        phone: '<?php echo addslashes($user_info['phone'] ?? ''); ?>',
        bio: '<?php echo addslashes($user_info['bio'] ?? ''); ?>',
        role: '<?php echo addslashes($user_info['role_name'] ?? $user_info['role'] ?? ''); ?>',
        created_at: '<?php echo $user_info['created_at'] ?? ''; ?>',
        last_login: '<?php echo $user_info['last_login'] ?? ''; ?>',
        exported_at: new Date().toISOString()
    };
    
    const dataStr = JSON.stringify(profileData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    
    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = 'profile_' + profileData.username + '_' + new Date().toISOString().split('T')[0] + '.json';
    link.click();
    
    adminAJAX.showNotification('Profil bilgileri dışa aktarıldı', 'success');
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const newPassword = document.querySelector('input[name="new_password"]');
    const confirmPassword = document.querySelector('input[name="confirm_password"]');
    
    function validatePasswordMatch() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Şifreler eşleşmiyor');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    if (newPassword && confirmPassword) {
        newPassword.addEventListener('input', validatePasswordMatch);
        confirmPassword.addEventListener('input', validatePasswordMatch);
    }
    
    // Auto-save form data
    const forms = document.querySelectorAll('form[data-ajax-submit]');
    forms.forEach(form => {
        const formId = form.querySelector('input[name="action"]').value;
        
        // Load auto-saved data
        loadAutoSavedData(formId);
        
        // Auto-save on input
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const formData = new FormData(form);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                localStorage.setItem(`autosave_${formId}`, JSON.stringify(data));
            });
        });
    });
});

function loadAutoSavedData(formId) {
    const savedData = localStorage.getItem(`autosave_${formId}`);
    if (savedData) {
        try {
            const data = JSON.parse(savedData);
            const form = document.querySelector(`form input[name="action"][value="${formId}"]`).closest('form');
            
            Object.keys(data).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input && input.type !== 'password') {
                    input.value = data[key];
                }
            });
        } catch (e) {
            console.error('Auto-save data parse error:', e);
        }
    }
}

function clearAutoSavedData(formId) {
    localStorage.removeItem(`autosave_${formId}`);
}
</script>

<?php include 'includes/footer.php'; ?>
