<?php
/**
 * Admin User Reset Script
 * NextCode Group - Reset Admin Credentials
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin User Reset</h1>";

try {
    // Direct database connection
    $pdo = new PDO(
        'mysql:host=gtorg.mysql.tools;dbname=gtorg_nextcode;charset=utf8mb4',
        'gtorg_nextcode',
        ';849#dVEyg',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "✅ Database connected<br><br>";
    
    // Step 1: Delete existing admin user
    echo "<h2>Step 1: Cleaning existing admin user</h2>";
    $stmt = $pdo->prepare("DELETE FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    echo "✅ Removed old admin user<br>";
    
    // Step 2: Create fresh admin user
    echo "<h2>Step 2: Creating new admin user</h2>";
    $username = 'admin';
    $email = 'admin@nextcodegroup.com';
    $password = 'Admin123!@#NextCode';
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("
        INSERT INTO admin_users (
            username, 
            email, 
            password_hash, 
            full_name, 
            role, 
            is_active,
            login_attempts,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $result = $stmt->execute([
        $username,
        $email,
        $password_hash,
        'Super Administrator',
        'super_admin',
        1,
        0
    ]);
    
    if ($result) {
        echo "✅ New admin user created successfully<br>";
        
        // Verify the user
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "<h3>✅ User Verification</h3>";
            echo "- ID: " . $user['id'] . "<br>";
            echo "- Username: " . $user['username'] . "<br>";
            echo "- Email: " . $user['email'] . "<br>";
            echo "- Role: " . $user['role'] . "<br>";
            echo "- Active: " . ($user['is_active'] ? 'Yes' : 'No') . "<br>";
            
            // Test password verification
            if (password_verify($password, $user['password_hash'])) {
                echo "✅ Password verification: SUCCESS<br>";
            } else {
                echo "❌ Password verification: FAILED<br>";
            }
        }
    } else {
        echo "❌ Failed to create admin user<br>";
    }
    
    // Step 3: Clean sessions
    echo "<h2>Step 3: Cleaning sessions</h2>";
    $pdo->exec("DELETE FROM admin_sessions");
    echo "✅ All sessions cleared<br>";
    
    echo "<h2>🎉 Reset Complete!</h2>";
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>New Login Credentials:</h3>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> Admin123!@#NextCode</p>";
    echo "<p><strong>Email:</strong> admin@nextcodegroup.com</p>";
    echo "</div>";
    
    echo "<p><a href='login.php' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; max-width: 800px; }";
echo "h1 { color: #667eea; }";
echo "h2 { color: #333; margin-top: 30px; border-bottom: 2px solid #eee; padding-bottom: 5px; }";
echo "✅ { color: green; font-weight: bold; }";
echo "❌ { color: red; font-weight: bold; }";
echo "</style>";
?>
