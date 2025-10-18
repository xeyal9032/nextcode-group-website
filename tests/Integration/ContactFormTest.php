<?php
/**
 * Contact Form Integration Tests
 * İletişim formu end-to-end testleri
 */

namespace NextCode\Tests\Integration;

use PHPUnit\Framework\TestCase;

class ContactFormTest extends TestCase
{
    private $pdo;
    
    protected function setUp(): void
    {
        // Test database setup
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        // Create contact_messages table
        $this->pdo->exec("
            CREATE TABLE contact_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                subject TEXT,
                message TEXT NOT NULL,
                status TEXT DEFAULT 'new',
                ip_address TEXT,
                user_agent TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
    
    public function testContactFormSubmissionSuccess()
    {
        $formData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+994501234567',
            'subject' => 'Test Subject',
            'message' => 'This is a test message.'
        ];
        
        // Validate data
        $errors = $this->validateContactForm($formData);
        $this->assertEmpty($errors, 'Form validation should pass');
        
        // Insert into database
        $stmt = $this->pdo->prepare("
            INSERT INTO contact_messages (name, email, phone, subject, message, status, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $formData['name'],
            $formData['email'],
            $formData['phone'],
            $formData['subject'],
            $formData['message'],
            'new',
            '127.0.0.1',
            'PHPUnit Test'
        ]);
        
        $this->assertTrue($result);
        
        // Verify insertion
        $stmt = $this->pdo->query("SELECT * FROM contact_messages WHERE email = 'john@example.com'");
        $record = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertEquals('John Doe', $record['name']);
        $this->assertEquals('john@example.com', $record['email']);
        $this->assertEquals('new', $record['status']);
    }
    
    public function testContactFormValidationErrors()
    {
        $invalidData = [
            'name' => '', // Empty name
            'email' => 'invalid-email', // Invalid email
            'phone' => '',
            'subject' => '',
            'message' => '' // Empty message
        ];
        
        $errors = $this->validateContactForm($invalidData);
        
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('message', $errors);
    }
    
    public function testContactFormSQLInjectionProtection()
    {
        $maliciousData = [
            'name' => "'; DROP TABLE contact_messages; --",
            'email' => 'hacker@example.com',
            'phone' => '',
            'subject' => 'Test',
            'message' => 'Test message'
        ];
        
        // Use prepared statements
        $stmt = $this->pdo->prepare("
            INSERT INTO contact_messages (name, email, message)
            VALUES (?, ?, ?)
        ");
        
        $stmt->execute([
            $maliciousData['name'],
            $maliciousData['email'],
            $maliciousData['message']
        ]);
        
        // Table should still exist
        $stmt = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='contact_messages'");
        $result = $stmt->fetch();
        
        $this->assertNotFalse($result, 'Table should still exist after SQL injection attempt');
    }
    
    public function testContactFormXSSProtection()
    {
        $xssData = [
            'name' => '<script>alert("XSS")</script>',
            'email' => 'test@example.com',
            'phone' => '',
            'subject' => '<img src=x onerror=alert("XSS")>',
            'message' => 'Normal message'
        ];
        
        // Sanitize data
        $sanitized = [
            'name' => htmlspecialchars($xssData['name'], ENT_QUOTES, 'UTF-8'),
            'email' => $xssData['email'],
            'subject' => htmlspecialchars($xssData['subject'], ENT_QUOTES, 'UTF-8'),
            'message' => htmlspecialchars($xssData['message'], ENT_QUOTES, 'UTF-8')
        ];
        
        $this->assertStringNotContainsString('<script>', $sanitized['name']);
        $this->assertStringNotContainsString('onerror=', $sanitized['subject']);
    }
    
    private function validateContactForm($data)
    {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Ad sahəsi mütləqdir';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email sahəsi mütləqdir';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Düzgün email ünvanı daxil edin';
        }
        
        if (empty($data['message'])) {
            $errors['message'] = 'Mesaj sahəsi mütləqdir';
        }
        
        return $errors;
    }
    
    protected function tearDown(): void
    {
        $this->pdo = null;
    }
}


