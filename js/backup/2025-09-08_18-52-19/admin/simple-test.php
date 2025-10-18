<?php
/**
 * Simple Admin Test - No Dependencies
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Admin Test</h1>";

// Test database connection
echo "<h2>Database Connection Test</h2>";
try {
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
    echo "✅ Database connection successful<br>";
    
    // Test admin user
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "✅ Admin user found<br>";
        echo "- ID: " . $admin['id'] . "<br>";
        echo "- Username: " . $admin['username'] . "<br>";
        echo "- Email: " . $admin['email'] . "<br>";
        echo "- Role: " . $admin['role'] . "<br>";
        echo "- Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "<br>";
        
        // Test password
        if (password_verify('Admin123!@#NextCode', $admin['password_hash'])) {
            echo "✅ Password verification successful<br>";
        } else {
            echo "❌ Password verification failed<br>";
        }
    } else {
        echo "❌ Admin user not found<br>";
    }
    
    // Test other tables
    $tables = ['admin_sessions', 'admin_activity_log', 'admin_files', 'site_settings'];
    echo "<h3>Table Status</h3>";
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch();
            echo "✅ $table: " . $result['count'] . " records<br>";
        } catch (Exception $e) {
            echo "❌ $table: Error - " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<h2>Login Form Test</h2>";
?>
<form method="POST" action="simple-test.php" style="max-width: 400px;">
    <div style="margin-bottom: 15px;">
        <label>Username:</label><br>
        <input type="text" name="username" value="admin" style="width: 100%; padding: 8px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label>Password:</label><br>
        <input type="password" name="password" value="Admin123!@#NextCode" style="width: 100%; padding: 8px;">
    </div>
    <button type="submit" name="test_login" style="padding: 10px 20px; background: #667eea; color: white; border: none; cursor: pointer;">Test Login</button>
</form>

<?php
if (isset($_POST['test_login'])) {
    echo "<h3>Login Test Result</h3>";
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo "❌ Username and password required<br>";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                echo "✅ Login successful!<br>";
                echo "- Welcome: " . $user['full_name'] . "<br>";
                echo "- Role: " . $user['role'] . "<br>";
                echo "<p><a href='login.php'>Go to Real Login Page</a></p>";
            } else {
                echo "❌ Invalid username or password<br>";
            }
        } catch (Exception $e) {
            echo "❌ Login error: " . $e->getMessage() . "<br>";
        }
    }
}

echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo "h1 { color: #667eea; }";
echo "h2 { color: #333; margin-top: 30px; }";
echo "form { background: #f8f9fa; padding: 20px; border-radius: 8px; }";
echo "</style>";
?>
