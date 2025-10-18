<?php
define("SECURE_ACCESS", true);
require_once "includes/security.php";

// Admin yetkisi kontrolü
checkAdminPermission();

$pdo = getSecureDatabaseConnection();
if (!$pdo) {
    die("Database connection failed");
}

$page_title = "Ayarlar";
$page_description = "Sistem ayarlarını yönetin";

$success_message = "";
$error_message = "";

include "includes/header.php";
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1><i class="fas fa-cog"></i> Ayarlar</h1>
                <p>Sistem ayarlarını yönetin ve konfigürasyonu düzenleyin</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-globe"></i> Genel Ayarlar</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="ajax-handler.php" data-ajax-submit>
                        <input type="hidden" name="action" value="update_settings">
                        
                        <div class="mb-3">
                            <label class="form-label">Site Başlığı</label>
                            <input type="text" name="site_title" class="form-control" value="NextCode Group">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Site Açıklaması</label>
                            <textarea name="site_description" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Admin E-posta</label>
                            <input type="email" name="admin_email" class="form-control">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Ayarları Kaydet
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-shield-alt"></i> Güvenlik Ayarları</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="two_factor" id="two_factor">
                        <label class="form-check-label" for="two_factor">
                            İki Faktörlü Kimlik Doğrulama
                        </label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="ip_whitelist" id="ip_whitelist">
                        <label class="form-check-label" for="ip_whitelist">
                            IP Whitelist
                        </label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="session_timeout" id="session_timeout">
                        <label class="form-check-label" for="session_timeout">
                            Oturum Zaman Aşımı
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/admin-ajax.js"></script>
<?php include "includes/footer.php"; ?>