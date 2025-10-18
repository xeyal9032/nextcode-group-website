<?php
/**
 * PHPUnit Bootstrap File
 * Test ortamını hazırlar
 */

// Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Test ortamı ayarları
define('SECURE_ACCESS', true);
define('TESTING', true);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Session için test ayarları
ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0);
ini_set('session.use_trans_sid', 0);
ini_set('session.cache_limiter', '');

// Test veritabanı bağlantısı
function getTestDatabaseConnection()
{
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

// Test helper fonksiyonları
function createTestUser($pdo, $data = [])
{
    $defaults = [
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT)
    ];
    
    $data = array_merge($defaults, $data);
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            email TEXT NOT NULL,
            password TEXT NOT NULL
        )
    ");
    
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$data['username'], $data['email'], $data['password']]);
    
    return $pdo->lastInsertId();
}

// Test için mock email sender
class MockEmailSender
{
    private $sentEmails = [];
    
    public function send($to, $subject, $message)
    {
        $this->sentEmails[] = [
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'timestamp' => time()
        ];
        return true;
    }
    
    public function getSentEmails()
    {
        return $this->sentEmails;
    }
    
    public function getLastEmail()
    {
        return end($this->sentEmails);
    }
}

echo "\n";
echo "=====================================\n";
echo "  NextCode PHPUnit Test Suite\n";
echo "=====================================\n";
echo "Environment: Testing\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHPUnit Version: " . PHPUnit\Runner\Version::id() . "\n";
echo "=====================================\n\n";
