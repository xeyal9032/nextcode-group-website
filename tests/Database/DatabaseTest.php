<?php

use PHPUnit\Framework\TestCase;

/**
 * Database Connection and Operations Test
 */
class DatabaseTest extends TestCase
{
    private $pdo;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        if (isset($GLOBALS['test_pdo'])) {
            $this->pdo = $GLOBALS['test_pdo'];
        } else {
            $this->markTestSkipped('Test database not available');
        }
    }
    
    public function testDatabaseConnection(): void
    {
        $this->assertInstanceOf(PDO::class, $this->pdo);
        $this->assertNotFalse($this->pdo);
    }
    
    public function testDatabaseTablesExist(): void
    {
        $requiredTables = [
            'blog_posts', 'blog_categories', 'portfolio_projects',
            'portfolio_categories', 'services', 'contact_messages',
            'site_settings', 'site_content', 'pages'
        ];
        
        foreach ($requiredTables as $table) {
            $sql = "SHOW TABLES LIKE '$table'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            
            $this->assertNotFalse($result, "Table '$table' should exist");
        }
    }
    
    public function testBlogCategoriesTableStructure(): void
    {
        $sql = "DESCRIBE blog_categories";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $requiredColumns = ['id', 'name', 'slug', 'description', 'status', 'created_at', 'updated_at'];
        
        foreach ($requiredColumns as $column) {
            $this->assertContains($column, $columns, "Column '$column' should exist in blog_categories table");
        }
    }
    
    public function testBlogPostsTableStructure(): void
    {
        $sql = "DESCRIBE blog_posts";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $requiredColumns = ['id', 'title', 'slug', 'content', 'excerpt', 'category_id', 'status', 'is_featured', 'created_at', 'updated_at'];
        
        foreach ($requiredColumns as $column) {
            $this->assertContains($column, $columns, "Column '$column' should exist in blog_posts table");
        }
    }
    
    public function testForeignKeyConstraints(): void
    {
        // Test foreign key between blog_posts and blog_categories
        $sql = "
            SELECT 
                CONSTRAINT_NAME,
                TABLE_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE 
                REFERENCED_TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'blog_posts'
                AND COLUMN_NAME = 'category_id'
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $foreignKey = $stmt->fetch();
        
        $this->assertNotFalse($foreignKey, 'Foreign key constraint should exist');
        $this->assertEquals('blog_categories', $foreignKey['REFERENCED_TABLE_NAME']);
    }
    
    public function testInsertAndRetrieveData(): void
    {
        // Insert test category
        $sql = "INSERT INTO blog_categories (name, slug, description, status) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute(['Test Category', 'test-category', 'Test description', 'active']);
        
        $this->assertTrue($result, 'Category insert should succeed');
        $categoryId = $this->pdo->lastInsertId();
        
        // Insert test blog post
        $sql = "INSERT INTO blog_posts (title, slug, content, excerpt, category_id, status, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'Test Post',
            'test-post',
            'Test content',
            'Test excerpt',
            $categoryId,
            'published',
            0
        ]);
        
        $this->assertTrue($result, 'Blog post insert should succeed');
        $postId = $this->pdo->lastInsertId();
        
        // Retrieve and verify
        $sql = "SELECT * FROM blog_posts WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$postId]);
        $post = $stmt->fetch();
        
        $this->assertNotFalse($post, 'Blog post should be retrievable');
        $this->assertEquals('Test Post', $post['title']);
        $this->assertEquals($categoryId, $post['category_id']);
        
        // Cleanup
        $this->pdo->exec("DELETE FROM blog_posts WHERE id = $postId");
        $this->pdo->exec("DELETE FROM blog_categories WHERE id = $categoryId");
    }
    
    public function testDatabaseTransactions(): void
    {
        $this->pdo->beginTransaction();
        
        try {
            // Insert test data
            $sql = "INSERT INTO blog_categories (name, slug, description, status) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['Transaction Test', 'transaction-test', 'Test', 'active']);
            
            $categoryId = $this->pdo->lastInsertId();
            
            // Verify insert worked
            $sql = "SELECT * FROM blog_categories WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$categoryId]);
            $category = $stmt->fetch();
            
            $this->assertNotFalse($category, 'Category should exist in transaction');
            
            // Rollback transaction
            $this->pdo->rollBack();
            
            // Verify rollback worked
            $sql = "SELECT * FROM blog_categories WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$categoryId]);
            $category = $stmt->fetch();
            
            $this->assertFalse($category, 'Category should not exist after rollback');
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    
    public function testDatabasePerformance(): void
    {
        $startTime = microtime(true);
        
        // Perform a simple query
        $sql = "SELECT COUNT(*) as count FROM blog_posts";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        $this->assertIsNumeric($result['count'], 'Count should be numeric');
        $this->assertLessThan(100, $executionTime, 'Query should execute in less than 100ms');
    }
}
