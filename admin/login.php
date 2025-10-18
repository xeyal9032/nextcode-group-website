<?php
// Güvenli Admin Login - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Zaten giriş yapmışsa dashboard'a yönlendir
if (isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true) {
    header("Location: index.php");
    exit;
}

$error_message = "";
$success_message = "";

// CSRF Token oluştur
$csrf_token = generateCSRFToken();

// Giriş işlemi
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF Token kontrolü
    $submitted_token = $_POST["csrf_token"] ?? "";
    if (!verifyCSRFToken($submitted_token)) {
        $error_message = "Güvenlik hatası. Lütfen sayfayı yenileyin.";
        logSecurityEvent('CSRF_TOKEN_INVALID', 'Invalid CSRF token submitted', 'WARNING');
    } else {
        // Rate limiting kontrolü
        $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!checkRateLimit($client_ip, 5, 300)) {
            $error_message = "Çok fazla başarısız giriş denemesi. 5 dakika bekleyin.";
            logSecurityEvent('RATE_LIMIT_EXCEEDED', 'Too many login attempts from IP: ' . $client_ip, 'WARNING');
        } else {
            // Input sanitization
            $username = sanitizeInput($_POST["username"] ?? "", "username");
            $password = $_POST["password"] ?? "";
            
            // Input validation
            if (!validateInput($username, "username") || !validateInput($password, "password")) {
                $error_message = "Kullanıcı adı veya şifre formatı hatalı.";
                logSecurityEvent('INVALID_INPUT_FORMAT', 'Invalid username/password format', 'WARNING');
            } else {
                // Veritabanından kullanıcı kontrolü
                $pdo = getSecureDatabaseConnection();
                
                // Veritabanı bağlantı hatası durumunda fallback
                if (!$pdo) {
                    $error_message = "Veritabanı bağlantı hatası. Lütfen daha sonra tekrar deneyin.";
                    logSecurityEvent('DATABASE_CONNECTION_ERROR', 'Failed to connect to database during login', 'ERROR');
                }
                
                if ($pdo) {
                    try {
                        $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name, role, is_active, locked_until FROM admin_users WHERE username = ?");
                        $stmt->execute([$username]);
                        $user = $stmt->fetch();
                        
                        if ($user) {
                            // Hesap kilidi kontrolü
                            if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                                $error_message = "Hesabınız geçici olarak kilitlenmiştir.";
                                logSecurityEvent('ACCOUNT_LOCKED', 'Attempted login to locked account: ' . $username, 'WARNING');
                            } elseif (!$user['is_active']) {
                                $error_message = "Hesabınız aktif değildir.";
                                logSecurityEvent('INACTIVE_ACCOUNT_LOGIN', 'Attempted login to inactive account: ' . $username, 'WARNING');
                            } elseif (verifyPassword($password, $user['password_hash'])) {
                                // Giriş başarılı
                                session_regenerate_id(true);
                                
                                $_SESSION["admin_logged_in"] = true;
                                $_SESSION["admin_user_id"] = $user['id'];
                                $_SESSION["admin_id"] = $user['id'];
                                $_SESSION["admin_username"] = $user['username'];
                                $_SESSION["admin_name"] = $user['full_name'];
                                $_SESSION["admin_role"] = $user['role'];
                                $_SESSION["admin_email"] = $user['email'];
                                $_SESSION["login_time"] = time();
                                
                                // Son giriş zamanını ve IP'yi güncelle
                                $stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW(), ip_address = ?, user_agent = ?, login_attempts = 0, locked_until = NULL WHERE id = ?");
                                $stmt->execute([$client_ip, $_SERVER['HTTP_USER_AGENT'] ?? '', $user['id']]);
                                
                                logSecurityEvent('SUCCESSFUL_LOGIN', 'User: ' . $username . ', IP: ' . $client_ip, 'INFO');
                                
                                secureRedirect("index.php");
                                exit;
                            } else {
                                // Başarısız giriş - login attempts artır
                                $stmt = $pdo->prepare("UPDATE admin_users SET login_attempts = login_attempts + 1 WHERE username = ?");
                                $stmt->execute([$username]);
                                
                                // 5 başarısız denemeden sonra hesabı kilitle
                                $stmt = $pdo->prepare("SELECT login_attempts FROM admin_users WHERE username = ?");
                                $stmt->execute([$username]);
                                $attempts = $stmt->fetchColumn();
                                
                                if ($attempts >= 5) {
                                    $stmt = $pdo->prepare("UPDATE admin_users SET locked_until = DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE username = ?");
                                    $stmt->execute([$username]);
                                    logSecurityEvent('ACCOUNT_LOCKED', 'Account locked due to failed attempts: ' . $username, 'WARNING');
                                }
                                
                                $error_message = "Kullanıcı adı veya şifre hatalı.";
                                logSecurityEvent('FAILED_LOGIN', 'Invalid credentials for: ' . $username . ', IP: ' . $client_ip, 'WARNING');
                            }
                        } else {
                            $error_message = "Kullanıcı adı veya şifre hatalı.";
                            logSecurityEvent('FAILED_LOGIN', 'Non-existent user: ' . $username . ', IP: ' . $client_ip, 'WARNING');
                        }
                    } catch (Exception $e) {
                        $error_message = "Giriş işlemi sırasında hata oluştu.";
                        logSecurityEvent('LOGIN_ERROR', $e->getMessage(), 'ERROR');
                    }
                } else {
                    $error_message = "Sistem hatası. Lütfen daha sonra tekrar deneyin.";
                    logSecurityEvent('DATABASE_CONNECTION_FAILED', 'Database connection failed during login', 'ERROR');
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
    <title>Basit Admin Login | NextCode</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 2.2em;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .login-header p {
            color: #666;
            font-size: 1.1em;
        }
        
        .demo-info {
            background: linear-gradient(135deg, #e7f3ff 0%, #d1ecf1 100%);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            border-left: 4px solid #667eea;
        }
        
        .demo-info h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        
        .demo-info p {
            color: #666;
            margin-bottom: 8px;
            font-size: 0.95em;
        }
        
        .demo-info strong {
            color: #333;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 1.1em;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.2em;
        }
        
        .form-control {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }
        
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-danger {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background: #efe;
            color: #363;
            border: 1px solid #cfc;
        }
        
        .debug-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 12px;
            border-left: 4px solid #6c757d;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            margin: 0 10px;
        }
        
        .login-footer a:hover {
            color: #764ba2;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .login-header h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-shield-alt"></i> Basit Admin Login</h1>
            <p>NextCode Admin Panel - Debug Modu</p>
        </div>
        
        <div class="demo-info">
            <h3><i class="fas fa-shield-alt"></i> Güvenli Giriş</h3>
            <p><strong>Güvenlik:</strong> Tüm girişler loglanır ve izlenir</p>
            <p><strong>Rate Limiting:</strong> 5 başarısız denemeden sonra 30 dakika kilit</p>
            <p><small>CSRF koruması ve şifre hash'leme aktif</small></p>
        </div>
        
        <?php if (isset($_GET['timeout'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-clock"></i> Oturum süreniz doldu. Lütfen tekrar giriş yapın.
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Kullanıcı Adı</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" class="form-control" 
                           required autocomplete="username" placeholder="Kullanıcı adınızı girin">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Şifre</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" 
                           required autocomplete="current-password" placeholder="Şifrenizi girin">
                </div>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Giriş Yap
            </button>
        </form>
        
        <?php if (!empty($debug_info)): ?>
            <div class="debug-info">
                <h4><i class="fas fa-bug"></i> Debug Bilgileri:</h4>
                <?php foreach ($debug_info as $info): ?>
                    <p><?php echo htmlspecialchars($info); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="login-footer">
            <a href="simple_login_test.php"><i class="fas fa-flask"></i> Basit Test</a>
            <a href="../index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
        </div>
    </div>
    
    <script>
        // Form validasyonu ve UX iyileştirmeleri
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const submitBtn = document.querySelector(".btn-login");
            
            form.addEventListener("submit", function() {
                submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Giriş yapılıyor...";
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>