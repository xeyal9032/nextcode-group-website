<?php
/**
 * Database Connection Tests
 * Tests veritabanı bağlantı ve işlemlerini kontrol eder
 */

namespace NextCode\Tests\Unit;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $pdo;
    
    protected function setUp(): void
    {
        // Test için in-memory SQLite kullan
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        // Test tablosu oluştur
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS test_table (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
    
    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(\PDO::class, $this->pdo);
        $this->assertEquals(\PDO::ERRMODE_EXCEPTION, $this->pdo->getAttribute(\PDO::ATTR_ERRMODE));
    }
    
    public function testInsertData()
    {
        $stmt = $this->pdo->prepare("INSERT INTO test_table (name, email) VALUES (?, ?)");
        $result = $stmt->execute(['John Doe', 'john@example.com']);
        
        $this->assertTrue($result);
        $this->assertEquals(1, $this->pdo->lastInsertId());
    }
    
    public function testSelectData()
    {
        // Insert test data
        $stmt = $this->pdo->prepare("INSERT INTO test_table (name, email) VALUES (?, ?)");
        $stmt->execute(['Jane Doe', 'jane@example.com']);
        
        // Select data
        $stmt = $this->pdo->query("SELECT * FROM test_table WHERE email = 'jane@example.com'");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertEquals('Jane Doe', $result['name']);
        $this->assertEquals('jane@example.com', $result['email']);
    }
    
    public function testUpdateData()
    {
        // Insert test data
        $stmt = $this->pdo->prepare("INSERT INTO test_table (name, email) VALUES (?, ?)");
        $stmt->execute(['Test User', 'test@example.com']);
        $id = $this->pdo->lastInsertId();
        
        // Update data
        $stmt = $this->pdo->prepare("UPDATE test_table SET name = ? WHERE id = ?");
        $result = $stmt->execute(['Updated User', $id]);
        
        $this->assertTrue($result);
        
        // Verify update
        $stmt = $this->pdo->prepare("SELECT name FROM test_table WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertEquals('Updated User', $result['name']);
    }
    
    public function testDeleteData()
    {
        // Insert test data
        $stmt = $this->pdo->prepare("INSERT INTO test_table (name, email) VALUES (?, ?)");
        $stmt->execute(['Delete Me', 'delete@example.com']);
        $id = $this->pdo->lastInsertId();
        
        // Delete data
        $stmt = $this->pdo->prepare("DELETE FROM test_table WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        $this->assertTrue($result);
        
        // Verify deletion
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM test_table WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertEquals(0, $result['count']);
    }
    
    public function testPreparedStatementsSQLInjectionProtection()
    {
        // Attempt SQL injection
        $maliciousInput = "'; DROP TABLE test_table; --";
        
        $stmt = $this->pdo->prepare("SELECT * FROM test_table WHERE email = ?");
        $stmt->execute([$maliciousInput]);
        
        // Table should still exist
        $stmt = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='test_table'");
        $result = $stmt->fetch();
        
        $this->assertNotFalse($result);
    }
    
    protected function tearDown(): void
    {
        $this->pdo = null;
    }
}


