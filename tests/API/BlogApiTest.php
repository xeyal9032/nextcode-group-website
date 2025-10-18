<?php

use PHPUnit\Framework\TestCase;

/**
 * Blog API Test Cases
 */
class BlogApiTest extends TestCase
{
    private $pdo;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Use test database if available
        if (isset($GLOBALS['test_pdo'])) {
            $this->pdo = $GLOBALS['test_pdo'];
            $this->setupTestData();
        } else {
            $this->markTestSkipped('Test database not available');
        }
    }
    
    protected function tearDown(): void
    {
        if ($this->pdo) {
            $this->pdo->exec("DELETE FROM blog_posts");
            $this->pdo->exec("DELETE FROM blog_categories");
        }
        parent::tearDown();
    }
    
    private function setupTestData(): void
    {
        // Insert test categories
        $this->pdo->exec("
            INSERT INTO blog_categories (id, name, slug, description, status) VALUES
            (1, 'SEO', 'seo', 'SEO kateqoriyası', 'active'),
            (2, 'Web Development', 'web-development', 'Veb inkişaf kateqoriyası', 'active')
        ");
        
        // Insert test blog posts
        $this->pdo->exec("
            INSERT INTO blog_posts (id, title, slug, content, excerpt, category_id, status, is_featured, created_at) VALUES
            (1, 'Test Blog Post 1', 'test-blog-post-1', 'Test content 1', 'Test excerpt 1', 1, 'published', 1, NOW()),
            (2, 'Test Blog Post 2', 'test-blog-post-2', 'Test content 2', 'Test excerpt 2', 2, 'published', 0, NOW()),
            (3, 'Test Draft Post', 'test-draft-post', 'Draft content', 'Draft excerpt', 1, 'draft', 0, NOW())
        ");
    }
    
    public function testGetBlogPostsReturnsPublishedPosts(): void
    {
        $sql = "SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $posts = $stmt->fetchAll();
        
        $this->assertCount(2, $posts);
        $this->assertEquals('published', $posts[0]['status']);
    }
    
    public function testGetBlogPostsWithCategoryFilter(): void
    {
        $sql = "SELECT * FROM blog_posts WHERE status = 'published' AND category_id = ? ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([1]);
        $posts = $stmt->fetchAll();
        
        $this->assertCount(1, $posts);
        $this->assertEquals(1, $posts[0]['category_id']);
    }
    
    public function testGetFeaturedBlogPosts(): void
    {
        $sql = "SELECT * FROM blog_posts WHERE status = 'published' AND is_featured = 1 ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $posts = $stmt->fetchAll();
        
        $this->assertCount(1, $posts);
        $this->assertEquals(1, $posts[0]['is_featured']);
    }
    
    public function testGetBlogPostById(): void
    {
        $sql = "SELECT * FROM blog_posts WHERE id = ? AND status = 'published'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([1]);
        $post = $stmt->fetch();
        
        $this->assertNotFalse($post);
        $this->assertEquals(1, $post['id']);
        $this->assertEquals('Test Blog Post 1', $post['title']);
    }
    
    public function testGetBlogPostWithCategoryJoin(): void
    {
        $sql = "
            SELECT bp.*, bc.name as category_name 
            FROM blog_posts bp 
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
            WHERE bp.id = ? AND bp.status = 'published'
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([1]);
        $post = $stmt->fetch();
        
        $this->assertNotFalse($post);
        $this->assertEquals('SEO', $post['category_name']);
    }
    
    public function testBlogPostValidation(): void
    {
        // Test required fields
        $requiredFields = ['title', 'slug', 'content', 'status'];
        
        foreach ($requiredFields as $field) {
            $sql = "SELECT COUNT(*) as count FROM blog_posts WHERE $field IS NULL OR $field = ''";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            
            $this->assertEquals(0, $result['count'], "Field $field should not be null or empty");
        }
    }
    
    public function testBlogPostSlugUniqueness(): void
    {
        // Test that slugs are unique
        $sql = "SELECT slug, COUNT(*) as count FROM blog_posts GROUP BY slug HAVING count > 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $duplicates = $stmt->fetchAll();
        
        $this->assertEmpty($duplicates, 'Blog post slugs should be unique');
    }
    
    public function testBlogPostStatusValues(): void
    {
        // Test valid status values
        $sql = "SELECT DISTINCT status FROM blog_posts";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $validStatuses = ['draft', 'published', 'archived'];
        
        foreach ($statuses as $status) {
            $this->assertContains($status, $validStatuses, "Status '$status' should be valid");
        }
    }
}
