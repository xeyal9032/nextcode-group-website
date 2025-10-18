<?php
/**
 * Simple Login Test - Direct Database Authentication
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: simple-login.php');
    exit();
}

// Check if already logged in
if (isset($_SESSION['simple_admin_logged_in'])) {
    echo "<h1>✅ Admin Panel - Logged In Successfully!</h1>";
    echo "<p>Welcome, " . $_SESSION['admin_username'] . "!</p>";
    echo "<p><a href='simple-login.php?logout=1' style='color: red;'>Logout</a></p>";
    echo "<p><a href='index.php' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Dashboard</a></p>";
    echo "<p><a href='pages/file-manager.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>File Manager</a></p>";
    echo "<p><a href='pages/code-editor.php' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Code Editor</a></p>";
    echo "<p><a href='pages/database.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Database Manager</a></p>";
    exit();
}

$error = '';
$success = '';

// Handle login
if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } else {
        try {
            // Direct database connection
            $pdo = new PDO(
                'mysql:host=gtorg.mysql.tools;dbname=gtorg_nextcode;charset=utf8mb4',
                'gtorg_nextcode',
                ';849#dVEyg',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Get user
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Login successful
                $_SESSION['simple_admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_email'] = $user['email'];
                
                // Update last login
                $stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW(), login_attempts = 0 WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                header('Location: simple-login.php');
                exit();
            } else {
                $error = 'Invalid username or password';
                
                // Update login attempts if user exists
                if ($user) {
                    $stmt = $pdo->prepare("UPDATE admin_users SET login_attempts = login_attempts + 1 WHERE id = ?");
                    $stmt->execute([$user['id']]);
                }
            }
            
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Admin Login - NextCode Group</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        .login-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .login-form {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .login-button {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #fcc;
        }
        
        .success {
            background: #efe;
            color: #3c3;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #cfc;
        }
        
        .demo-credentials {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #667eea;
        }
        
        .demo-credentials h4 {
            color: #667eea;
            margin-bottom: 8px;
        }
        
        .demo-credentials p {
            margin: 5px 0;
            font-family: monospace;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🔐 Simple Admin Login</h1>
            <p>NextCode Group - Direct Authentication</p>
        </div>
        
        <div class="login-form">
            <?php if ($error): ?>
                <div class="error">❌ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success">✅ <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="simple-login.php">
                <div class="form-group">
                    <label for="username">👤 Kullanıcı Adı</label>
                    <input type="text" id="username" name="username" value="admin" required>
                </div>
                
                <div class="form-group">
                    <label for="password">🔑 Şifre</label>
                    <input type="password" id="password" name="password" value="Admin123!@#NextCode" required>
                </div>
                
                <button type="submit" class="login-button">
                    🚀 Giriş Yap
                </button>
            </form>
            
            <div class="demo-credentials">
                <h4>🎯 Test Bilgileri</h4>
                <p><strong>Kullanıcı:</strong> admin</p>
                <p><strong>Şifre:</strong> Admin123!@#NextCode</p>
            </div>
        </div>
        
        <div class="footer">
            <p>© 2024 NextCode Group. Secure Admin Panel.</p>
            <p><a href="reset-admin.php" style="color: #667eea;">Reset Admin User</a> | <a href="simple-test.php" style="color: #667eea;">Database Test</a></p>
        </div>
    </div>
</body>
</html>
