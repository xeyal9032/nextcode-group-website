<?php
// Test Services Page
// Basit test dosyası

ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head>\n";
echo "<title>Test Services Page</title>\n";
echo "</head>\n";
echo "<body>\n";
echo "<h1>Services Test Page</h1>\n";

try {
    echo "<p>Testing database connection...</p>\n";
    
    // Test database connection
    require_once 'config/database.php';
    
    if ($pdo) {
        echo "<p style='color: green;'>✅ Database connection successful!</p>\n";
        
        // Test tableExists function
        require_once 'includes/page_functions.php';
        
        if (function_exists('tableExists')) {
            echo "<p style='color: green;'>✅ tableExists function loaded!</p>\n";
            
            if (tableExists('services')) {
                echo "<p style='color: green;'>✅ Services table exists!</p>\n";
            } else {
                echo "<p style='color: orange;'>⚠️ Services table does not exist!</p>\n";
            }
        } else {
            echo "<p style='color: red;'>❌ tableExists function not found!</p>\n";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Database connection failed!</p>\n";
    }
    
    echo "<p>Testing content helper...</p>\n";
    
    // Test content helper
    require_once 'includes/content_helper.php';
    
    $test_content = getTextContent('test_key', 'Default test content');
    echo "<p style='color: blue;'>Content Helper Test: " . htmlspecialchars($test_content) . "</p>\n";
    
    echo "<p style='color: green;'>✅ All tests completed successfully!</p>\n";
    echo "<p><a href='services.php'>Try Services Page</a></p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p>Stack trace:</p>\n";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>\n";
}

echo "</body>\n";
echo "</html>\n";
?>
