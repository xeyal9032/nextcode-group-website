<?php
// NextCode Group - Blog Post Page
define('SECURE_ACCESS', true);

require_once 'config/database.php';
require_once 'includes/page_functions.php';
require_once 'includes/content_helper.php';

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$post_id) {
    header('Location: blog.php');
    exit;
}

// Try to get post from database first
$current_post = null;
try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        // Check if blog_posts table exists
        $tableExists = false;
        try {
            $checkTable = $pdo->query("SHOW TABLES LIKE 'blog_posts'");
            $tableExists = $checkTable->rowCount() > 0;
        } catch (Exception $e) {
            $tableExists = false;
        }
        
        if ($tableExists) {
            $stmt = $pdo->prepare("
                SELECT bp.*, bc.name as category_name 
                FROM blog_posts bp 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                WHERE bp.id = ? AND bp.status = 'published'
            ");
            $stmt->execute([$post_id]);
            $current_post = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
} catch (Exception $e) {
    error_log('Database error in blog-post.php: ' . $e->getMessage());
}

// If not found in database, use fallback data
if (!$current_post) {
    $fallback_posts = [
        1 => [
            'id' => 1,
            'title' => 'SEO Optimizasiyası: Axtarış Nəticələrində Yüksəlmək',
            'slug' => 'seo-optimizasiyasi',
            'content' => '<p>SEO optimizasiyası, veb saytınızın axtarış sistemlərində daha yaxşı görünməsi üçün edilən texniki və məzmun təkmilləşdirmələridir. Bu proses, Google, Bing və digər axtarış sistemlərində daha yüksək reytinq əldə etməyə kömək edir.</p>
            
            <h2>SEO-nun Əsas Elementləri</h2>
            <p>SEO optimizasiyası iki əsas kateqoriyaya bölünür:</p>
            <ul>
                <li><strong>Texniki SEO:</strong> Saytın texniki performansını yaxşılaşdırır</li>
                <li><strong>Məzmun SEO:</strong> Saytın məzmununu axtarış sistemləri üçün optimallaşdırır</li>
            </ul>
            
            <h3>Texniki SEO Elementləri</h3>
            <p>Texniki SEO aşağıdakı elementləri əhatə edir:</p>
            <ul>
                <li>Saytın yüklənmə sürəti</li>
                <li>Mobil uyğunluq</li>
                <li>SSL sertifikatı</li>
                <li>URL strukturu</li>
                <li>Meta taglər</li>
            </ul>
            
            <h3>Məzmun SEO Elementləri</h3>
            <p>Məzmun SEO-nun əsas elementləri:</p>
            <ul>
                <li>Keyworlərin düzgün istifadəsi</li>
                <li>Keyfiyyətli və orijinal məzmun</li>
                <li>Başlıq strukturu (H1, H2, H3)</li>
                <li>Daxili və xarici linklər</li>
                <li>Alt taglər</li>
            </ul>
            
            <h2>SEO Strategiyası</h2>
            <p>Uğurlu SEO strategiyası üçün aşağıdakı addımları atın:</p>
            <ol>
                <li>Keywor araşdırması aparın</li>
                <li>Rəqib analizi edin</li>
                <li>Texniki audit keçirin</li>
                <li>Məzmun planı hazırlayın</li>
                <li>Nəticələri izləyin və təkmilləşdirin</li>
            </ol>
            
            <p>SEO optimizasiyası uzunmüddətli prosesdir və nəticələr 3-6 ay ərzində görünə bilər. Sabırlı olun və strategiyanızı davam etdirin.</p>',
            'excerpt' => 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün SEO strategiyaları və texnikaları.',
                    'featured_image' => 'images/blog/blog-1.jpg',
        'category_name' => 'SEO',
            'author' => 'NextCode Team',
            'read_time' => '8',
            'created_at' => '2024-01-15 10:00:00'
        ],
        2 => [
            'id' => 2,
            'title' => 'Sosial Media Marketinqi: Brendinizi Gücləndirin',
            'slug' => 'sosial-media-marketinqi',
            'content' => '<p>Sosial media marketinqi, brendinizi sosial platformalarda gücləndirmək üçün strategik yanaşmadır. Bu gün, sosial media marketinqi hər hansı uğurlu biznes strategiyasının ayrılmaz hissəsidir.</p>
            
            <h2>Sosial Media Platformaları</h2>
            <p>Ən effektiv sosial media platformaları:</p>
            <ul>
                <li><strong>Facebook:</strong> Ən geniş auditoriya</li>
                <li><strong>Instagram:</strong> Vizual məzmun üçün ideal</li>
                <li><strong>LinkedIn:</strong> B2B marketinq üçün</li>
                <li><strong>TikTok:</strong> Gənc auditoriya üçün</li>
            </ul>
            
            <h3>Məzmun Strategiyası</h3>
            <p>Uğurlu sosial media məzmunu üçün:</p>
            <ul>
                <li>Düzgün məzmun planı hazırlayın</li>
                <li>Auditoriyanızı tanıyın</li>
                <li>Düzgün vaxtda paylaşın</li>
                <li>Vizual məzmunlardan istifadə edin</li>
                <li>İstifadəçilərlə qarşılıqlı əlaqə saxlayın</li>
            </ul>
            
            <h2>Paid Advertising</h2>
            <p>Sosial media reklamları:</p>
            <ul>
                <li>Hədəflənmiş auditoriya</li>
                <li>Dəqiq nəticə ölçmə</li>
                <li>Yüksək ROI</li>
                <li>Tez nəticə</li>
            </ul>
            
            <p>Sosial media marketinqi düzgün strategiya ilə brendinizi gücləndirə və müştəri bazınızı artıra bilər.</p>',
            'excerpt' => 'Sosial media platformalarında brendinizi gücləndirmək üçün praktik məsləhətlər və strategiyalar.',
                    'featured_image' => 'images/blog/blog-2.jpg',
        'category_name' => 'Sosial Media',
            'author' => 'NextCode Team',
            'read_time' => '6',
            'created_at' => '2024-01-10 14:30:00'
        ],
        3 => [
            'id' => 3,
            'title' => 'Modern Web Dizayn Trendləri 2024',
            'slug' => 'modern-web-dizayn-trendleri',
            'content' => '<p>2024-cü ildə web dizayn sahəsində bir sıra yeni trendlər və yeniliklər müşahidə olunur. Bu trendlər istifadəçi təcrübəsini yaxşılaşdırmaq və daha cəlbedici veb saytlar yaratmaq üçün dizaynerlərə kömək edir.</p>
            
            <h2>2024-cü İlin Əsas Trendləri</h2>
            <ul>
                <li><strong>Minimalizm:</strong> Sadə və təmiz dizaynlar</li>
                <li><strong>Dark Mode:</strong> Gözə daha rahat</li>
                <li><strong>Micro-interactions:</strong> Kiçik animasiyalar</li>
                <li><strong>3D Elementlər:</strong> Daha real görünüş</li>
                <li><strong>Gradientlər:</strong> Rəngli keçidlər</li>
            </ul>
            
            <h3>Responsive Dizayn</h3>
            <p>Mobil cihazların artan populyarlığı səbəbindən responsive dizayn daha da vacib olub. Saytınız bütün cihazlarda mükəmməl görünməlidir.</p>
            
            <h3>Performance Optimization</h3>
            <p>Yüklənmə sürəti istifadəçi təcrübəsi üçün kritikdir. Optimizasiya edilmiş saytlar daha yaxşı nəticələr göstərir.</p>
            
            <p>Modern web dizayn trendləri istifadəçi təcrübəsini yaxşılaşdırmaq və daha cəlbedici veb saytlar yaratmaq üçün vacibdir.</p>',
            'excerpt' => '2024-cü ildə web dizayn sahəsində populyar olan trendlər və yeniliklər.',
                    'featured_image' => 'images/blog/blog-3.jpg',
        'category_name' => 'Web Dizayn',
            'author' => 'NextCode Team',
            'read_time' => '10',
            'created_at' => '2024-01-05 09:15:00'
        ],
        4 => [
            'id' => 4,
            'title' => 'Digital Marketinq Strategiyaları',
            'slug' => 'digital-marketinq-strategiyalari',
            'content' => '<p>Digital marketinq, müasir biznes dünyasında uğur qazanmaq üçün vacib strategiyadır. Düzgün strategiya ilə brendinizi gücləndirə və satışlarınızı artıra bilərsiniz.</p>
            
            <h2>Digital Marketinq Kanalları</h2>
            <ul>
                <li><strong>SEO:</strong> Axtarış sistemlərində yüksəlmək</li>
                <li><strong>PPC:</strong> Ödənişli reklamlar</li>
                <li><strong>Sosial Media:</strong> Brend şüuru</li>
                <li><strong>Email Marketing:</strong> Birbaşa əlaqə</li>
                <li><strong>Content Marketing:</strong> Məzmun marketinqi</li>
            </ul>
            
            <h3>Strategiya Planlaması</h3>
            <p>Uğurlu digital marketinq strategiyası üçün:</p>
            <ol>
                <li>Hədəf auditoriyanızı müəyyənləşdirin</li>
                <li>Rəqiblərinizi analiz edin</li>
                <li>Büdcə planlaması edin</li>
                <li>KPI-ləri müəyyənləşdirin</li>
                <li>Nəticələri izləyin</li>
            </ol>
            
            <h2>ROI Ölçmə</h2>
            <p>Digital marketinqin uğurunu ölçmək üçün:</p>
            <ul>
                <li>Conversion rate</li>
                <li>Cost per acquisition</li>
                <li>Customer lifetime value</li>
                <li>Return on investment</li>
            </ul>
            
            <p>Digital marketinq strategiyası düzgün planlanmalı və daim izlənilməlidir.</p>',
            'excerpt' => 'Rəqəmsal dünyada uğurlu marketinq strategiyaları və taktikaları.',
                    'featured_image' => 'images/blog/blog-4.jpg',
        'category_name' => 'Marketing',
            'author' => 'NextCode Team',
            'read_time' => '12',
            'created_at' => '2024-01-01 16:45:00'
        ]
    ];
    
    $current_post = $fallback_posts[$post_id] ?? null;
}

if (!$current_post) {
    header('Location: blog.php');
    exit;
}

$formatted_date = date('d F Y', strtotime($current_post['created_at']));

// Dynamic content variables
$page_title = $current_post['title'] . ' - NextCode Group Blog';
$meta_description = $current_post['excerpt'];
$current_page = 'blog';

// Include header
require_once 'includes/header.php';
?>

<style>
/* Blog post stilleri */
.post-featured-image {
    margin: 20px 0;
    text-align: center;
}

.post-featured-image img {
    width: 100%;
    height: auto;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
}

.author-info {
    display: flex;
    align-items: center;
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px;
}

.author-name {
    font-weight: bold;
    display: block;
}

.author-title {
    color: #666;
    font-size: 14px;
}
</style>
<!-- Blog Post Header -->
<section class="blog-post-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="post-meta mb-3">
                    <span class="category-badge"><?php echo htmlspecialchars($current_post['category_name']); ?></span>
                    <span class="post-date"><i class="fas fa-calendar"></i> <?php echo htmlspecialchars($formatted_date); ?></span>
                    <span class="read-time"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($current_post['read_time']); ?> dəq</span>
                </div>
                <h1 class="post-title"><?php echo htmlspecialchars($current_post['title']); ?></h1>
                
                <!-- Featured Image -->
                <div class="post-featured-image mb-4">
                    <img src="<?php echo htmlspecialchars($current_post['featured_image']); ?>" alt="<?php echo htmlspecialchars($current_post['title']); ?>" class="img-fluid" onerror="this.src='images/blog/blog-1.jpg'">
                </div>
                
                <div class="author-info">
                    <img src="images/author-avatar.jpg" alt="<?php echo htmlspecialchars($current_post['author']); ?>" class="author-avatar">
                    <div>
                        <span class="author-name"><?php echo htmlspecialchars($current_post['author']); ?></span>
                        <span class="author-title">Digital Marketing Mütəxəssisi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Post Content -->
<section class="blog-post-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="post-content">
                    <?php echo $current_post['content']; ?>
                </div>
                
                <!-- Share buttons -->
                <div class="share-buttons mt-5">
                    <h4>Bu məqaləni paylaşın:</h4>
                    <div class="share-links">
                        <a href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-link facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($current_post['title']); ?>" target="_blank" class="share-link twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-link linkedin">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($current_post['title'] . ' - ' . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-link whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Related posts -->
                <div class="related-posts mt-5">
                    <h4>Əlaqəli məqalələr:</h4>
                    <div class="row">
                        <?php
                        $related_posts = array_filter($fallback_posts, function($post) use ($current_post) {
                            return $post['id'] != $current_post['id'] && $post['category_name'] == $current_post['category_name'];
                        });
                        
                        $related_posts = array_slice($related_posts, 0, 3);
                        
                        foreach ($related_posts as $post) {
                            echo '<div class="col-md-4 mb-3">';
                            echo '<div class="related-post-card">';
                            echo '<img src="' . htmlspecialchars($post['featured_image']) . '" alt="' . htmlspecialchars($post['title']) . '" class="img-fluid">';
                            echo '<h5><a href="blog-post.php?id=' . $post['id'] . '">' . htmlspecialchars($post['title']) . '</a></h5>';
                            echo '<p>' . htmlspecialchars(substr($post['excerpt'], 0, 100)) . '...</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="newsletter">
    <div class="container">
        <h3>Ən Son Məqalələrdən Xəbərdar Olun</h3>
        <p>Digital marketing sahəsindəki son yeniliklər və xüsusi təkliflərdən xəbərdar olmaq üçün abunə olun.</p>
        <form class="newsletter-form">
            <input type="email" class="newsletter-input" placeholder="E-mail ünvanınız" required>
            <button type="submit" class="newsletter-btn">Abunə Ol</button>
        </form>
    </div>
</section>

<?php
// Include footer
require_once 'includes/footer.php';
?>
