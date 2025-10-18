<?php

use PHPUnit\Framework\TestCase;

/**
 * Content Helper Functions Test
 */
class ContentHelperTest extends TestCase
{
    private $pdo;
    
    protected function setUp(): void
    {
        parent::setUp();
        
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
            $this->pdo->exec("DELETE FROM site_content");
        }
        parent::tearDown();
    }
    
    private function setupTestData(): void
    {
        // Insert test content
        $this->pdo->exec("
            INSERT INTO site_content (page_name, section_name, content_key, content_value, content_type, is_active) VALUES
            ('home', 'hero', 'hero_title', 'Test Hero Title', 'text', 1),
            ('home', 'hero', 'hero_subtitle', 'Test Hero Subtitle', 'text', 1),
            ('home', 'services', 'services_title', 'Test Services Title', 'text', 1),
            ('about', 'intro', 'about_text', '<p>Test About Text</p>', 'html', 1)
        ");
    }
    
    public function testGetTextContent(): void
    {
        // Test with existing content
        $content = getTextContent('hero_title', 'Default Title');
        $this->assertEquals('Test Hero Title', $content);
        
        // Test with non-existing content
        $content = getTextContent('non_existing_key', 'Default Value');
        $this->assertEquals('Default Value', $content);
        
        // Test HTML escaping
        $content = getTextContent('about_text', 'Default');
        $this->assertEquals('&lt;p&gt;Test About Text&lt;/p&gt;', $content);
    }
    
    public function testGetHtmlContent(): void
    {
        // Test HTML content (should not be escaped)
        $content = getHtmlContent('about_text', 'Default HTML');
        $this->assertEquals('<p>Test About Text</p>', $content);
        
        // Test with non-existing content
        $content = getHtmlContent('non_existing_key', '<p>Default HTML</p>');
        $this->assertEquals('<p>Default HTML</p>', $content);
    }
    
    public function testGetLinkContent(): void
    {
        // Insert test link content
        $this->pdo->exec("
            INSERT INTO site_content (page_name, section_name, content_key, content_value, content_type, is_active) VALUES
            ('home', 'hero', 'hero_button_link', 'contact.php', 'text', 1)
        ");
        
        $content = getLinkContent('hero_button_link', '#');
        $this->assertEquals('contact.php', $content);
        
        // Test with non-existing content
        $content = getLinkContent('non_existing_link', '/default');
        $this->assertEquals('/default', $content);
    }
    
    public function testGetImageContent(): void
    {
        // Insert test image content
        $this->pdo->exec("
            INSERT INTO site_content (page_name, section_name, content_key, content_value, content_type, is_active) VALUES
            ('home', 'hero', 'hero_image', 'images/hero.jpg', 'text', 1)
        ");
        
        $content = getImageContent('hero_image', 'images/default.jpg');
        $this->assertEquals('images/hero.jpg', $content);
        
        // Test with non-existing content
        $content = getImageContent('non_existing_image', 'images/placeholder.jpg');
        $this->assertEquals('images/placeholder.jpg', $content);
    }
    
    public function testGetSectionContent(): void
    {
        $sectionContent = getSectionContent('hero');
        
        $this->assertIsArray($sectionContent);
        $this->assertCount(2, $sectionContent); // hero_title and hero_subtitle
        
        // Check structure
        $firstContent = $sectionContent[0];
        $this->assertArrayHasKey('content_key', $firstContent);
        $this->assertArrayHasKey('content_value', $firstContent);
        $this->assertArrayHasKey('content_type', $firstContent);
    }
    
    public function testContentHelperClass(): void
    {
        $helper = new ContentHelper();
        
        // Test getContent method
        $content = $helper->getContent('hero_title', 'Default');
        $this->assertEquals('Test Hero Title', $content);
        
        // Test getTextContent method
        $content = $helper->getTextContent('hero_title', 'Default');
        $this->assertEquals('Test Hero Title', $content);
        
        // Test getHtmlContent method
        $content = $helper->getHtmlContent('about_text', 'Default');
        $this->assertEquals('<p>Test About Text</p>', $content);
    }
    
    public function testContentCaching(): void
    {
        $helper = new ContentHelper();
        
        // First call - should hit database
        $startTime = microtime(true);
        $content1 = $helper->getContent('hero_title', 'Default');
        $firstCallTime = microtime(true) - $startTime;
        
        // Second call - should hit cache
        $startTime = microtime(true);
        $content2 = $helper->getContent('hero_title', 'Default');
        $secondCallTime = microtime(true) - $startTime;
        
        $this->assertEquals($content1, $content2);
        $this->assertLessThan($firstCallTime, $secondCallTime, 'Second call should be faster (cached)');
    }
    
    public function testClearCache(): void
    {
        $helper = new ContentHelper();
        
        // Get content to populate cache
        $content1 = $helper->getContent('hero_title', 'Default');
        
        // Clear cache
        ContentHelper::clearCache();
        
        // Get content again - should hit database
        $content2 = $helper->getContent('hero_title', 'Default');
        
        $this->assertEquals($content1, $content2);
    }
    
    public function testGetAllContent(): void
    {
        $helper = new ContentHelper();
        $allContent = $helper->getAllContent();
        
        $this->assertIsArray($allContent);
        $this->assertGreaterThan(0, count($allContent));
        
        // Check structure of first item
        $firstContent = $allContent[0];
        $requiredKeys = ['page_name', 'section_name', 'content_key', 'content_value', 'content_type', 'is_active'];
        
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $firstContent);
        }
    }
    
    public function testContentTypeValidation(): void
    {
        // Test different content types
        $contentTypes = ['text', 'html', 'image', 'json'];
        
        foreach ($contentTypes as $type) {
            $this->pdo->exec("
                INSERT INTO site_content (page_name, section_name, content_key, content_value, content_type, is_active) VALUES
                ('test', 'test', 'test_$type', 'test_value', '$type', 1)
            ");
            
            $helper = new ContentHelper();
            $content = $helper->getContent("test_$type", 'default');
            $this->assertEquals('test_value', $content);
            
            $this->pdo->exec("DELETE FROM site_content WHERE content_key = 'test_$type'");
        }
    }
}
