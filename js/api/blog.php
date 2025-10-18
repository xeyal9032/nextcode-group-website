<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/database.php';

$pdo = null;
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    error_log('Blog API database connection failed: ' . $e->getMessage());
    // Continue without database connection
}

try {
    // Handle different endpoints
    $path = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($path, PHP_URL_PATH);
    $path = str_replace('/api/blog', '', $path);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $limit = $_GET['limit'] ?? 10;
        $category = $_GET['category'] ?? null;
        $featured = $_GET['featured'] ?? null;
        
        // Handle /recent endpoint
        if ($path === '/recent') {
            $featured = 1; // Show only featured posts for recent
            $limit = 6; // Limit recent posts
        } else {
            // For main blog page, show all posts
            $featured = null;
            $limit = $_GET['limit'] ?? 20;
        }
        
        // Check if database is available
        if (!$pdo) {
            // Return fallback data
            $fallbackPosts = getFallbackBlogPosts();
            echo json_encode([
                'success' => true,
                'data' => $fallbackPosts,
                'message' => 'Using fallback data - database not available'
            ]);
            exit;
        }
        
        // Önce blog_posts tablosunun varlığını kontrol et
        $tableExists = false;
        try {
            $checkTable = $pdo->query("SHOW TABLES LIKE 'blog_posts'");
            $tableExists = $checkTable->rowCount() > 0;
        } catch (Exception $e) {
            $tableExists = false;
        }
        
        if ($tableExists) {
            // Tablo varsa verileri çek
            $sql = "SELECT bp.id, bp.title, bp.slug, bp.content, bp.excerpt, bp.featured_image, bp.category_id, bp.tags, bp.meta_title, bp.meta_description, bp.is_featured, bp.author_id, bp.status, bp.created_at, bp.updated_at, bc.name as category_name FROM blog_posts bp 
                    LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                    WHERE bp.status = 'published'";
            $params = [];
                
            if ($category) {
                $sql .= " AND bp.category_id = ?";
                $params[] = $category;
            }
            
            if ($featured) {
                $sql .= " AND bp.is_featured = 1";
            }
            
            $sql .= " ORDER BY bp.is_featured DESC, bp.created_at DESC LIMIT ?";
            $params[] = (int)$limit;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Tablo yoksa fallback data kullan
            $posts = getFallbackBlogPosts();
        }
        
        echo json_encode([
            'success' => true,
            'data' => $posts
        ]);
    }
} catch (Exception $e) {
    error_log('Blog API error: ' . $e->getMessage());
    // Return fallback data on error
    $fallbackPosts = getFallbackBlogPosts();
    echo json_encode([
        'success' => true,
        'data' => $fallbackPosts,
        'message' => 'Using fallback data due to error'
    ]);
}

function getFallbackBlogPosts() {
    return [
        [
            'id' => 1,
            'title' => 'SEO Optimizasiyası: Axtarış Nəticələrində Yüksəlmək',
            'slug' => 'seo-optimizasiyasi',
            'content' => 'SEO optimizasiyası haqqında ətraflı məlumat və praktik məsləhətlər...',
            'excerpt' => 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün SEO strategiyaları və texnikaları.',
            'featured_image' => 'images/blog/blog-1.jpg',
            'category_id' => 1,
            'category_name' => 'SEO',
            'tags' => 'SEO, Axtarış, Optimizasiya',
            'is_featured' => 1,
            'status' => 'published',
            'created_at' => '2024-01-15 10:00:00',
            'read_time' => '8'
        ],
        [
            'id' => 2,
            'title' => 'Sosial Media Marketinqi: Brendinizi Gücləndirin',
            'slug' => 'sosial-media-marketinqi',
            'content' => 'Sosial media platformalarında effektiv marketinq strategiyaları...',
            'excerpt' => 'Sosial media platformalarında brendinizi gücləndirmək üçün praktik məsləhətlər və strategiyalar.',
            'featured_image' => 'images/blog/blog-2.jpg',
            'category_id' => 2,
            'category_name' => 'Sosial Media',
            'tags' => 'Sosial Media, Marketinq, Brend',
            'is_featured' => 1,
            'status' => 'published',
            'created_at' => '2024-01-10 14:30:00',
            'read_time' => '6'
        ],
        [
            'id' => 3,
            'title' => 'Modern Web Dizayn Trendləri 2024',
            'slug' => 'modern-web-dizayn-trendleri',
            'content' => '2024-cü ildə web dizayn sahəsində populyar olan trendlər...',
            'excerpt' => '2024-cü ildə web dizayn sahəsində populyar olan trendlər və yeniliklər.',
            'featured_image' => 'images/blog/blog-3.jpg',
            'category_id' => 3,
            'category_name' => 'Web Dizayn',
            'tags' => 'Web Dizayn, Trendlər, UX/UI',
            'is_featured' => 0,
            'status' => 'published',
            'created_at' => '2024-01-05 09:15:00',
            'read_time' => '10'
        ],
        [
            'id' => 4,
            'title' => 'Digital Marketinq Strategiyaları',
            'slug' => 'digital-marketinq-strategiyalari',
            'content' => 'Rəqəmsal dünyada uğurlu marketinq strategiyaları...',
            'excerpt' => 'Rəqəmsal dünyada uğurlu marketinq strategiyaları və taktikaları.',
            'featured_image' => 'images/blog/blog-4.jpg',
            'category_id' => 4,
            'category_name' => 'Marketing',
            'tags' => 'Digital Marketinq, Strategiya, ROI',
            'is_featured' => 1,
            'status' => 'published',
            'created_at' => '2024-01-01 16:45:00',
            'read_time' => '12'
        ]
    ];
}
?>