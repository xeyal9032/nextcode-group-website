<?php
/**
 * NextCode Group - Blog System Enhancements
 * Mevcut blog sistemini geliştirir
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once __DIR__ . '/../config/database.php';

class BlogEnhancements {
    private $pdo;
    
    public function __construct() {
        try {
            $database = new Database();
            $this->pdo = $database->getConnection();
        } catch (Exception $e) {
            $this->pdo = null;
        }
    }
    
    /**
     * Blog için gelişmiş özellikler tablosunu oluştur
     */
    public function createBlogEnhancementTables() {
        if (!$this->pdo) return false;
        
        try {
            // Blog views tracking
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS blog_views (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    post_id INT NOT NULL,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_post_id (post_id),
                    INDEX idx_viewed_at (viewed_at),
                    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Blog comments
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS blog_comments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    post_id INT NOT NULL,
                    parent_id INT DEFAULT NULL,
                    author_name VARCHAR(255) NOT NULL,
                    author_email VARCHAR(255) NOT NULL,
                    author_website VARCHAR(500),
                    content TEXT NOT NULL,
                    status ENUM("pending", "approved", "spam", "rejected") DEFAULT "pending",
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_post_id (post_id),
                    INDEX idx_status (status),
                    INDEX idx_created_at (created_at),
                    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
                    FOREIGN KEY (parent_id) REFERENCES blog_comments(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Blog likes/dislikes
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS blog_reactions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    post_id INT NOT NULL,
                    reaction_type ENUM("like", "dislike", "love", "share") DEFAULT "like",
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_reaction (post_id, ip_address, reaction_type),
                    INDEX idx_post_id (post_id),
                    INDEX idx_reaction_type (reaction_type),
                    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Blog reading time calculation
            $this->pdo->exec('
                ALTER TABLE blog_posts 
                ADD COLUMN reading_time INT DEFAULT 0 AFTER excerpt
            ');
            
            $this->pdo->exec('
                ALTER TABLE blog_posts 
                ADD COLUMN view_count INT DEFAULT 0 AFTER reading_time
            ');
            
            $this->pdo->exec('
                ALTER TABLE blog_posts 
                ADD COLUMN like_count INT DEFAULT 0 AFTER view_count
            ');
            
            $this->pdo->exec('
                ALTER TABLE blog_posts 
                ADD COLUMN comment_count INT DEFAULT 0 AFTER like_count
            ');
            
            return true;
        } catch (Exception $e) {
            error_log('Blog enhancement tables creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Blog post view'ını kaydet
     */
    public function trackBlogView($postId) {
        if (!$this->pdo) return false;
        
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            // View'ı kaydet
            $stmt = $this->pdo->prepare("
                INSERT INTO blog_views (post_id, ip_address, user_agent) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$postId, $ip, $userAgent]);
            
            // View count'u güncelle
            $stmt = $this->pdo->prepare("
                UPDATE blog_posts 
                SET view_count = view_count + 1 
                WHERE id = ?
            ");
            $stmt->execute([$postId]);
            
            return true;
        } catch (Exception $e) {
            error_log('Blog view tracking failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Blog post reaction'ını kaydet
     */
    public function addBlogReaction($postId, $reactionType) {
        if (!$this->pdo) return false;
        
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            // Reaction'ı kaydet
            $stmt = $this->pdo->prepare("
                INSERT INTO blog_reactions (post_id, reaction_type, ip_address, user_agent) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP
            ");
            $stmt->execute([$postId, $reactionType, $ip, $userAgent]);
            
            // Reaction count'u güncelle
            $stmt = $this->pdo->prepare("
                UPDATE blog_posts 
                SET like_count = (
                    SELECT COUNT(*) FROM blog_reactions 
                    WHERE post_id = ? AND reaction_type = 'like'
                )
                WHERE id = ?
            ");
            $stmt->execute([$postId, $postId]);
            
            return true;
        } catch (Exception $e) {
            error_log('Blog reaction failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Blog comment ekle
     */
    public function addBlogComment($postId, $commentData) {
        if (!$this->pdo) return false;
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO blog_comments 
                (post_id, parent_id, author_name, author_email, author_website, content, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $postId,
                $commentData['parent_id'] ?? null,
                $commentData['author_name'],
                $commentData['author_email'],
                $commentData['author_website'] ?? null,
                $commentData['content'],
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            if ($result) {
                // Comment count'u güncelle
                $stmt = $this->pdo->prepare("
                    UPDATE blog_posts 
                    SET comment_count = (
                        SELECT COUNT(*) FROM blog_comments 
                        WHERE post_id = ? AND status = 'approved'
                    )
                    WHERE id = ?
                ");
                $stmt->execute([$postId, $postId]);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log('Blog comment failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Blog post'ları için reading time hesapla
     */
    public function calculateReadingTime($content) {
        // Ortalama okuma hızı: 200 kelime/dakika
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = ceil($wordCount / 200);
        return max(1, $readingTime); // En az 1 dakika
    }
    
    /**
     * Popüler blog post'larını al
     */
    public function getPopularPosts($limit = 5) {
        if (!$this->pdo) return [];
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM blog_posts 
                WHERE status = 'published' 
                ORDER BY view_count DESC, like_count DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Popular posts fetch failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Blog istatistiklerini al
     */
    public function getBlogStats() {
        if (!$this->pdo) return [];
        
        try {
            $stats = [];
            
            // Toplam post sayısı
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'");
            $stats['total_posts'] = $stmt->fetchColumn();
            
            // Toplam view sayısı
            $stmt = $this->pdo->query("SELECT SUM(view_count) FROM blog_posts");
            $stats['total_views'] = $stmt->fetchColumn() ?: 0;
            
            // Toplam comment sayısı
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM blog_comments WHERE status = 'approved'");
            $stats['total_comments'] = $stmt->fetchColumn();
            
            // En popüler post
            $stmt = $this->pdo->query("
                SELECT title, view_count FROM blog_posts 
                WHERE status = 'published' 
                ORDER BY view_count DESC 
                LIMIT 1
            ");
            $stats['most_popular'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (Exception $e) {
            error_log('Blog stats fetch failed: ' . $e->getMessage());
            return [];
        }
    }
}

// CLI veya web erişimi için
if (php_sapi_name() === 'cli' || isset($_GET['setup'])) {
    $blogEnhancements = new BlogEnhancements();
    
    echo "=== Blog System Enhancements ===\n";
    
    if ($blogEnhancements->createBlogEnhancementTables()) {
        echo "✓ Blog enhancement tables created successfully\n";
        
        // İstatistikleri göster
        $stats = $blogEnhancements->getBlogStats();
        echo "\nBlog Statistics:\n";
        echo "- Total Posts: {$stats['total_posts']}\n";
        echo "- Total Views: {$stats['total_views']}\n";
        echo "- Total Comments: {$stats['total_comments']}\n";
        if ($stats['most_popular']) {
            echo "- Most Popular: {$stats['most_popular']['title']} ({$stats['most_popular']['view_count']} views)\n";
        }
    } else {
        echo "✗ Failed to create blog enhancement tables\n";
    }
    
    echo "\n=== Blog Enhancements Complete ===\n";
}
?>
