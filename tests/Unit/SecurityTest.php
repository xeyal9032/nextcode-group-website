<?php
/**
 * Security Functions Tests
 * Güvenlik fonksiyonlarını test eder
 */

namespace NextCode\Tests\Unit;

use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    public function testSanitizeInput()
    {
        // Test basic sanitization
        $input = "<script>alert('XSS')</script>";
        $sanitized = htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
        
        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertStringNotContainsString('</script>', $sanitized);
    }
    
    public function testSanitizeArray()
    {
        $input = [
            'name' => '<b>John</b>',
            'email' => 'john@example.com',
            'message' => '<script>alert("test")</script>'
        ];
        
        $sanitized = array_map(function($data) {
            return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
        }, $input);
        
        $this->assertEquals('John', $sanitized['name']);
        $this->assertEquals('john@example.com', $sanitized['email']);
        $this->assertStringNotContainsString('<script>', $sanitized['message']);
    }
    
    public function testValidateEmail()
    {
        // Valid emails
        $this->assertTrue(filter_var('test@example.com', FILTER_VALIDATE_EMAIL) !== false);
        $this->assertTrue(filter_var('user.name@example.co.uk', FILTER_VALIDATE_EMAIL) !== false);
        
        // Invalid emails
        $this->assertFalse(filter_var('invalid.email', FILTER_VALIDATE_EMAIL) !== false);
        $this->assertFalse(filter_var('invalid@', FILTER_VALIDATE_EMAIL) !== false);
        $this->assertFalse(filter_var('@example.com', FILTER_VALIDATE_EMAIL) !== false);
    }
    
    public function testPasswordHashing()
    {
        $password = 'SecurePassword123!';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertNotEquals($password, $hash);
        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('WrongPassword', $hash));
    }
    
    public function testCSRFTokenGeneration()
    {
        // Simulate session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Generate token
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $token = $_SESSION['csrf_token'];
        
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes = 64 hex characters
    }
    
    public function testCSRFTokenVerification()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $validToken = $_SESSION['csrf_token'];
        $invalidToken = bin2hex(random_bytes(32));
        
        // Valid token
        $this->assertTrue(hash_equals($_SESSION['csrf_token'], $validToken));
        
        // Invalid token
        $this->assertFalse(hash_equals($_SESSION['csrf_token'], $invalidToken));
    }
    
    public function testXSSPrevention()
    {
        $maliciousInputs = [
            '<script>alert("XSS")</script>',
            '<img src=x onerror=alert("XSS")>',
            '<iframe src="javascript:alert(\'XSS\')"></iframe>',
            'javascript:alert("XSS")',
            '<svg onload=alert("XSS")>'
        ];
        
        foreach ($maliciousInputs as $input) {
            $sanitized = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            
            $this->assertStringNotContainsString('<script', $sanitized);
            $this->assertStringNotContainsString('javascript:', $sanitized);
            $this->assertStringNotContainsString('onerror=', $sanitized);
            $this->assertStringNotContainsString('onload=', $sanitized);
        }
    }
    
    public function testSQLInjectionPrevention()
    {
        $maliciousInputs = [
            "1' OR '1'='1",
            "1'; DROP TABLE users; --",
            "' UNION SELECT * FROM users --",
            "admin'--",
            "1' AND 1=1 --"
        ];
        
        // Test with PDO prepared statements
        $pdo = new \PDO('sqlite::memory:');
        $pdo->exec("CREATE TABLE users (id INTEGER, username TEXT)");
        $pdo->exec("INSERT INTO users VALUES (1, 'admin')");
        
        foreach ($maliciousInputs as $input) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$input]);
            $result = $stmt->fetchAll();
            
            // Should return empty array (no SQL injection)
            $this->assertEmpty($result);
        }
    }
    
    public function testSecureSessionConfiguration()
    {
        // Check session configuration
        $this->assertNotEmpty(ini_get('session.cookie_httponly'));
        $this->assertNotEmpty(ini_get('session.use_strict_mode'));
    }
}


