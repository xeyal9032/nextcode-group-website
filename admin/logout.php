<?php
// Güvenli Çıkış - NextCode Group
define("SECURE_ACCESS", true);
define("ADMIN_ACCESS", true);

// Session başlat
session_start();

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Çıkış işlemini logla (basit versiyon)
    $username = $_SESSION['admin_username'] ?? 'Unknown';
    error_log("Admin logout: User $username logged out at " . date('Y-m-d H:i:s'));
    
    // Session verilerini temizle
    $_SESSION = array();
    
    // Session cookie'sini sil
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Session'ı yok et
    session_destroy();
}

// Login sayfasına yönlendir
header("Location: login.php");
exit;
?>