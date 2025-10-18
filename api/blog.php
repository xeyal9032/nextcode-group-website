<?php
// Define secure access constant
define('SECURE_ACCESS', true);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/database.php';

// Global $pdo değişkenini kullan
global $pdo;

try {
    // Handle different endpoints
    $path = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($path, PHP_URL_PATH);
    $path = str_replace('/api/blog', '', $path);

    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
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
            'excerpt' => 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün hərtərəfli SEO strategiyaları, texnikaları və 2024 trendləri. Google, Bing və digər axtarış sistemlərində daha yüksək pozisiyalar əldə etmək üçün texniki və məzmun SEO.',
            'featured_image' => '/images/blog/seo-optimization.jpg',
            'category_id' => 1,
            'category_name' => 'SEO',
            'tags' => 'SEO, Axtarış, Optimizasiya, Google Analytics',
            'is_featured' => 1,
            'status' => 'published',
            'created_at' => '2024-01-15 10:00:00',
            'read_time' => '12'
        ],
        [
            'id' => 2,
            'title' => 'Müasir Web İnkişaf Trendləri 2024',
            'slug' => 'muasir-web-inkisaf-trendleri-2024',
            'content' => '2024-cü ildə veb inkişaf sahəsindəki ən vacib trendlər...',
            'excerpt' => '2024-cü ildə veb inkişaf sahəsindəki ən vacib trendlər: süni intellekt inteqrasiyası, serversiz arxitektura, proqressiv veb tətbiqlər və VebAssembli.',
            'featured_image' => '/images/blog/web-development-trends.jpg',
            'category_id' => 2,
            'category_name' => 'Veb İnkişaf',
            'tags' => 'Veb İnkişaf, Süni İntellekt, Serversiz, PWA, VebAssembli',
            'is_featured' => 1,
            'status' => 'published',
            'created_at' => '2024-01-10 14:30:00',
            'read_time' => '15'
        ],
        [
            'id' => 3,
            'title' => 'E-ticarət İnkişafı: Online Mağaza Yaratmaq',
            'slug' => 'e-ticaret-inkisafi-online-magaza-yaratmaq',
            'content' => 'E-ticarət platforması yaratmaq üçün hərtərəfli təlimat...',
            'excerpt' => 'E-ticarət platforması yaratmaq üçün hərtərəfli təlimat: platform seçimi, ödəniş inteqrasiyası, mobil optimizasiya və performans. Onlayn mağaza yaratmaq və idarə etmək üçün vacib texniki biliklər və ən yaxşı təcrübələr.',
            'featured_image' => '/images/blog/ecommerce-development.jpg',
            'category_id' => 3,
            'category_name' => 'E-ticarət',
            'tags' => 'E-ticarət, Shopify, WooCommerce, Ödəniş Portalı',
            'is_featured' => 1,
            'status' => 'published',
            'created_at' => '2024-01-05 09:15:00',
            'read_time' => '18'
        ],
        [
            'id' => 4,
            'title' => 'Kibertəhlükəsizlik: Web Təhlükəsizliyi və Ən Yaxşı Təcrübələr',
            'slug' => 'kibertehlukesizlik-web-tehlukesizliyi-ve-en-yaxsi-tecrubeler',
            'content' => 'Veb təhlükəsizliyinin hərtərəfli təlimatı...',
            'excerpt' => 'Veb təhlükəsizliyinin hərtərəfli təlimatı: təhlükələr, OWASP Top 10, təhlükəsizlik tətbiqi və ən yaxşı təcrübələr. Kibertəhlükəsizlik sahəsində vacib prinsiplər və müasir təhlükəsizlik trendləri.',
            'featured_image' => '/images/blog/cybersecurity-guide.jpg',
            'category_id' => 4,
            'category_name' => 'Kibertəhlükəsizlik',
            'tags' => 'Kibertəhlükəsizlik, Veb Təhlükəsizliyi, OWASP, Ən Yaxşı Təcrübələr',
            'is_featured' => 1,
            'status' => 'published',
            'created_at' => '2024-01-01 16:45:00',
            'read_time' => '20'
        ],
        [
            'id' => 5,
            'title' => 'Digital Marketinq Strategiyaları 2024',
            'slug' => 'digital-marketinq-strategiyalari-2024',
            'content' => '2024-cü ildə digital marketinq sahəsindəki ən effektiv strategiyalar...',
            'excerpt' => 'Digital marketinq sahəsində uğur qazanmaq üçün 2024-cü ildə vacib olan strategiyalar: sosial media marketinqi, content marketinq, email marketinq və influencer marketinq.',
            'featured_image' => '/images/blog/small-business-marketing.jpg',
            'category_id' => 5,
            'category_name' => 'Marketinq',
            'tags' => 'Digital Marketinq, Sosial Media, Content Marketinq, Email Marketinq',
            'is_featured' => 1,
            'status' => 'published',
            'created_at' => '2024-01-20 11:30:00',
            'read_time' => '14'
        ],
        [
            'id' => 6,
            'title' => 'Mobil Tətbiq İnkişafı: iOS və Android',
            'slug' => 'mobil-tetbiq-inkisafi-ios-ve-android',
            'content' => 'Mobil tətbiq inkişafı haqqında hərtərəfli təlimat...',
            'excerpt' => 'iOS və Android platformaları üçün mobil tətbiq inkişafı: React Native, Flutter, native inkişaf və cross-platform həllər. Mobil tətbiq dizaynı və performans optimizasiyası.',
            'featured_image' => '/images/blog/mobile-app-development.jpg',
            'category_id' => 6,
            'category_name' => 'Mobil İnkişaf',
            'tags' => 'Mobil Tətbiq, iOS, Android, React Native, Flutter',
            'is_featured' => 1,
            'status' => 'published',
            'created_at' => '2024-01-25 16:20:00',
            'read_time' => '16'
        ]
    ];
}
?>