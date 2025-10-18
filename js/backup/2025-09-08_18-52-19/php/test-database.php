<?php
// Database test script for contact_messages table
require_once 'config/database.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Database Test Results</h1>";

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if (!$pdo) {
        echo "<p style='color: red;'>❌ Database connection failed!</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test table existence
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'contact_messages'");
    if ($tableCheck->rowCount() == 0) {
        echo "<p style='color: red;'>❌ Contact messages table does not exist!</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Contact messages table exists!</p>";
    
    // Show table structure
    echo "<h2>Table Structure:</h2>";
    $columns = $pdo->query("SHOW COLUMNS FROM contact_messages")->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test insert operation
    echo "<h2>Testing Insert Operation:</h2>";
    
    $testData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '+994501234567',
        'company' => 'Test Company',
        'service' => 'Web Development',
        'budget' => '1000-2500 AZN',
        'subject' => 'Test Subject',
        'message' => 'This is a test message from the database test script.'
    ];
    
    $sql = "INSERT INTO contact_messages (name, email, phone, company, service, budget, subject, message, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $testData['name'],
        $testData['email'],
        $testData['phone'],
        $testData['company'],
        $testData['service'],
        $testData['budget'],
        $testData['subject'],
        $testData['message']
    ]);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Test insert successful!</p>";
        echo "<p>Inserted ID: " . $pdo->lastInsertId() . "</p>";
        
        // Show the inserted record
        $insertedId = $pdo->lastInsertId();
        $record = $pdo->query("SELECT * FROM contact_messages WHERE id = $insertedId")->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>Inserted Record:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        foreach ($record as $key => $value) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($key) . "</strong></td>";
            echo "<td>" . htmlspecialchars($value) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Clean up test data
        $pdo->exec("DELETE FROM contact_messages WHERE id = $insertedId");
        echo "<p style='color: blue;'>🧹 Test record cleaned up.</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Test insert failed!</p>";
        $errorInfo = $stmt->errorInfo();
        echo "<p>Error: " . htmlspecialchars($errorInfo[2]) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='test-contact-final.html'>Test Contact Form</a></p>";
echo "<p><a href='contact.php'>Go to Contact Page</a></p>";
?>
