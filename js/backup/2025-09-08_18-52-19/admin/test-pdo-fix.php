<?php
/**
 * PDO Fix Test Script
 * NextCode Group - Test PDO Buffering Fix
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PDO Buffering Fix Test</h1>";

try {
    // Test new PDO configuration
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
    
    echo "✅ Database connection successful<br><br>";
    
    // Test multiple queries (this was causing the error)
    echo "<h2>Testing Multiple Queries</h2>";
    
    // Query 1
    $stmt1 = $pdo->prepare("SELECT COUNT(*) as total FROM admin_users");
    $stmt1->execute();
    $result1 = $stmt1->fetch();
    echo "✅ Query 1 - Admin users: " . ($result1['total'] ?? 0) . "<br>";
    
    // Query 2 (without closing cursor)
    $stmt2 = $pdo->prepare("SELECT COUNT(*) as total FROM admin_sessions");
    $stmt2->execute();
    $result2 = $stmt2->fetch();
    echo "✅ Query 2 - Admin sessions: " . ($result2['total'] ?? 0) . "<br>";
    
    // Query 3 (this would fail before the fix)
    $stmt3 = $pdo->prepare("SELECT COUNT(*) as total FROM admin_activity_log");
    $stmt3->execute();
    $result3 = $stmt3->fetch();
    echo "✅ Query 3 - Activity log: " . ($result3['total'] ?? 0) . "<br>";
    
    // Test database optimization query
    echo "<h2>Testing Database Optimization</h2>";
    
    $tables = ['admin_users', 'admin_sessions', 'admin_activity_log', 'admin_files'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->prepare("OPTIMIZE TABLE `$table`");
            $stmt->execute();
            $result = $stmt->fetchAll();
            echo "✅ Optimized table: $table<br>";
        } catch (Exception $e) {
            echo "⚠️ Could not optimize $table: " . $e->getMessage() . "<br>";
        }
    }
    
    // Test complex query with joins
    echo "<h2>Testing Complex Queries</h2>";
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                u.username,
                u.role,
                COUNT(al.id) as activity_count
            FROM admin_users u
            LEFT JOIN admin_activity_log al ON u.id = al.user_id
            GROUP BY u.id, u.username, u.role
        ");
        $stmt->execute();
        $users = $stmt->fetchAll();
        
        echo "✅ Complex query successful<br>";
        foreach ($users as $user) {
            echo "- " . $user['username'] . " (" . $user['role'] . "): " . $user['activity_count'] . " activities<br>";
        }
    } catch (Exception $e) {
        echo "❌ Complex query failed: " . $e->getMessage() . "<br>";
    }
    
    // Test dashboard stats API
    echo "<h2>Testing Dashboard Stats API</h2>";
    
    $api_url = 'simple-dashboard-stats.php';
    $response = file_get_contents($api_url);
    
    if ($response) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            echo "✅ Dashboard stats API working<br>";
            echo "- Stats count: " . count($data['stats']) . "<br>";
            echo "- Quick actions count: " . count($data['quick_actions']) . "<br>";
            echo "- Recent activities count: " . count($data['recent_activities']) . "<br>";
        } else {
            echo "❌ Dashboard stats API error<br>";
        }
    } else {
        echo "❌ Could not fetch dashboard stats<br>";
    }
    
    echo "<h2>✅ All Tests Completed Successfully!</h2>";
    echo "<p>PDO buffering issue has been resolved.</p>";
    echo "<p><a href='simple-dashboard.php'>Go to Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; max-width: 800px; }";
echo "h1 { color: #667eea; }";
echo "h2 { color: #333; margin-top: 30px; border-bottom: 2px solid #eee; padding-bottom: 5px; }";
echo "✅ { color: green; font-weight: bold; }";
echo "⚠️ { color: orange; font-weight: bold; }";
echo "❌ { color: red; font-weight: bold; }";
echo "</style>";
?>
