<?php
// NextCode Group - Blog Page
define('SECURE_ACCESS', true);

// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';
require_once 'includes/page_functions.php';
require_once 'includes/content_helper.php';

// Dynamic content variables
$page_title = getTextContent('blog_page_title', 'Blog - NextCode Group');
$meta_description = getTextContent('blog_meta_description', 'NextCode Group blog - digital marketing insights, SEO tips, and web development articles.');
$current_page = 'blog';

// Get blog posts from database
$blog_posts = [];
$categories = [];

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        // Get published blog posts with categories
        $stmt = $pdo->query("
            SELECT bp.id, bp.title, bp.slug, bp.excerpt, bp.content, bp.featured_image, 
                   bp.tags, bp.is_featured, bp.created_at, bp.read_time,
                   bc.name as category_name, bc.slug as category_slug
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id
            WHERE bp.status = 'published'
            ORDER BY bp.is_featured DESC, bp.created_at DESC
            LIMIT 20
        ");
        $blog_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get categories
        $stmt = $pdo->query("SELECT id, name, slug, description FROM blog_categories ORDER BY name");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // Fallback data if database fails
    $blog_posts = [
        [
            "id" => 23,
            "title" => "2024-cü İldə Müasir Veb İnkişaf Trendləri",
            "slug" => "2024-muasir-veb-inkisaf-trendleri",
            "excerpt" => "2024-cü ildə veb inkişaf sahəsində ən son trendləri kəşf edin. AI inteqrasiyasından progressive veb tətbiqlərinə qədər texnologiyaları öyrənin.",
            "featured_image" => "/images/blog/veb-inkisaf-trendleri.jpg",
            "category_name" => "Veb İnkişaf",
            "tags" => "veb inkişaf, trendlər, AI, serverless, PWA, WebAssembly",
            "is_featured" => 1,
            "created_at" => "2025-10-13 20:40:53",
            "read_time" => "5"
        ],
        [
            "id" => 24,
            "title" => "Responsive Mobil Tətbiqlər Qurmaq",
            "slug" => "responsive-mobil-tetbiqler-qurmaq",
            "excerpt" => "Responsive mobil tətbiqlər qurmağın hərtərəfli təlimatı. Ən yaxşı təcrübələr, frameworklər və cross-platform inkişaf texnikalarını öyrənin.",
            "featured_image" => "/images/blog/mobil-tetbiq-inkisafi.jpg",
            "category_name" => "Mobil Tətbiqlər",
            "tags" => "mobil tətbiqlər, responsive dizayn, React Native, Flutter, cross-platform",
            "is_featured" => 0,
            "created_at" => "2025-10-13 20:40:53",
            "read_time" => "5"
        ],
        [
            "id" => 25,
            "title" => "Kiçik Biznes üçün Rəqəmsal Marketinq Strategiyaları",
            "slug" => "kicik-biznes-reqemsal-marketinq-strategiyalari",
            "excerpt" => "Kiçik biznes üçün sübut olunmuş rəqəmsal marketinq strategiyaları. SEO, sosial media, məzmun marketinqi və email marketinq texnikalarını öyrənin.",
            "featured_image" => "/images/blog/reqemsal-marketinq.jpg",
            "category_name" => "Rəqəmsal Marketinq",
            "tags" => "rəqəmsal marketinq, SEO, sosial media, məzmun marketinqi, kiçik biznes",
            "is_featured" => 0,
            "created_at" => "2025-10-13 20:40:54",
            "read_time" => "5"
        ],
        [
            "id" => 26,
            "title" => "Süni İntellektin Gələcəyi",
            "slug" => "suni-intellektin-geleceyi",
            "excerpt" => "Süni İntellektin transformasiya gücünü araşdırın. Ən son AI inkişafları, sənaye tətbiqləri və gələcək nəticələri haqqında məlumat əldə edin.",
            "featured_image" => "/images/blog/suni-intellekt.jpg",
            "category_name" => "Texnologiya",
            "tags" => "süni intellekt, maşın öyrənməsi, AI, texnologiya trendləri, avtomatlaşdırma",
            "is_featured" => 1,
            "created_at" => "2025-10-13 20:40:54",
            "read_time" => "5"
        ],
        [
            "id" => 27,
            "title" => "Rəqəmsal Dövrdə Sahibkarlıq",
            "slug" => "reqemsal-dovrde-sahibkarlig",
            "excerpt" => "Rəqəmsal dövrdə uğurlu bizneslər qurmaq təlimatı. Rəqəmsal biznes modelləri, online marketinq və uzaq komanda idarəetməsini öyrənin.",
            "featured_image" => "/images/blog/reqemsal-sahibkarlig.jpg",
            "category_name" => "Biznes",
            "tags" => "sahibkarlıq, rəqəmsal biznes, startup, online marketinq, uzaq iş",
            "is_featured" => 0,
            "created_at" => "2025-10-13 20:40:54",
            "read_time" => "5"
        ]
    ];
}

// Include header
require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="modern-section modern-section--hero main-content">
    <div class="modern-container">
        <div class="modern-text-center">
            <div class="modern-badge modern-badge--outline modern-m-4">
                <i class="fas fa-blog me-2"></i>Blog
            </div>
            <h1 class="modern-heading modern-heading--xl"><?php echo getTextContent('blog_header_title', 'Blog'); ?></h1>
            <p class="modern-text modern-text--lg"><?php echo getTextContent('blog_header_subtitle', 'Digital marketing dünyasından ən son məqalələr və məsləhətlər'); ?></p>
        </div>
    </div>
</section>

<!-- Blog Filter -->
<section class="modern-section">
    <div class="modern-container">
        <div class="modern-flex modern-flex-wrap modern-justify-center modern-gap-4 modern-m-6">
            <button class="modern-btn modern-btn--outline modern-btn--active" data-category="all">Hamısı</button>
            <button class="modern-btn modern-btn--outline" data-category="seo">SEO</button>
            <button class="modern-btn modern-btn--outline" data-category="veb-inkisaf">Veb İnkişaf</button>
            <button class="modern-btn modern-btn--outline" data-category="e-ticaret">E-ticarət</button>
            <button class="modern-btn modern-btn--outline" data-category="kibertehlukesizlik">Kibertəhlükəsizlik</button>
            <button class="modern-btn modern-btn--outline" data-category="marketing">Marketinq</button>
        </div>
        <div class="modern-flex modern-justify-center modern-m-6">
            <div class="modern-search-box">
                <input type="text" id="blogSearch" placeholder="Məqalələrdə axtarış..." class="modern-search-input">
                <i class="fas fa-search modern-search-icon"></i>
            </div>
        </div>
    </div>
</section>

<!-- Blog Grid -->
<section class="modern-section modern-section--light">
    <div class="modern-container">
        <div class="modern-text-center modern-m-8">
            <h2 class="modern-heading modern-heading--lg">Son Məqalələr</h2>
        </div>
        <div class="modern-grid modern-grid--3-cols" id="blogGrid">
            <!-- Content will be loaded dynamically via API -->
            <!-- Static blog content will be shown here -->
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="modern-section">
    <div class="modern-container">
        <div class="modern-text-center">
            <h3 class="modern-heading modern-heading--md">Ən Son Məqalələrdən Xəbərdar Olun</h3>
            <p class="modern-text modern-m-4">Digital marketing sahəsindəki son yeniliklər və xüsusi təkliflərdən xəbərdar olmaq üçün abunə olun.</p>
            <form class="modern-newsletter-form modern-m-6">
                <div class="modern-flex modern-justify-center modern-gap-4">
                    <input type="email" class="modern-input" placeholder="E-mail ünvanınız" required>
                    <button type="submit" class="modern-btn modern-btn--primary">Abunə Ol</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Blog specific CSS -->
<link rel="stylesheet" href="css/blog-styles.css">
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load blog posts from API
    loadBlogPosts();
    
    // Blog filtering
    const filterButtons = document.querySelectorAll('[data-category]');
    const searchInput = document.getElementById('blogSearch');
    
    async function loadBlogPosts() {
        try {
            const response = await fetch('/api/blog.php');
            const result = await response.json();
            
            if (result.success && result.data) {
                displayBlogPosts(result.data);
            } else {
                // Fallback to static content
                displayBlogPosts(<?php echo json_encode($blog_posts); ?>);
            }
        } catch (error) {
            console.error('Error loading blog posts:', error);
            // Fallback to static content
            displayBlogPosts(<?php echo json_encode($blog_posts); ?>);
        }
    }
    
    function displayBlogPosts(posts) {
        const blogGrid = document.getElementById('blogGrid');
        let html = '';
        
        posts.forEach(post => {
            const excerpt = post.excerpt || post.content.substring(0, 150) + '...';
            const imageUrl = post.featured_image || '/images/placeholder.svg';
            
            html += `
                <div class="modern-card modern-card--blog modern-animate--fadeInUp" data-category="${post.category_name || 'general'}">
                    <div class="modern-card__image">
                        <img src="${imageUrl}" alt="${post.title}" class="modern-card__img" style="width: 100%; height: 250px; object-fit: cover;" onload="this.classList.add('loaded')" onerror="this.src='/images/placeholder.svg'">
                        <div class="modern-badge modern-badge--primary modern-card__category">${post.category_name || 'Ümumi'}</div>
                    </div>
                    <div class="modern-card__body">
                        <h3 class="modern-heading modern-heading--md modern-m-4">
                            <a href="blog-post.php?id=${post.id}" class="modern-link">${post.title}</a>
                        </h3>
                        <p class="modern-text modern-m-4">${excerpt}</p>
                        <div class="modern-flex modern-justify-between modern-items-center modern-m-4">
                            <span class="modern-text modern-text--small modern-text-muted">${new Date(post.created_at).toLocaleDateString('az-AZ')}</span>
                            <a href="blog-post.php?id=${post.id}" class="modern-btn modern-btn--outline modern-btn--sm">Daha çox oxu</a>
                        </div>
                    </div>
                </div>
            `;
        });
        
        blogGrid.innerHTML = html;
        
        // Re-initialize filter functionality
        initializeFilters();
    }
    
    function initializeFilters() {
        const blogItems = document.querySelectorAll('[data-category]');
    
        // Filter functionality
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                
                // Update active button
                filterButtons.forEach(b => b.classList.remove('modern-btn--active'));
                this.classList.add('modern-btn--active');
                
                // Filter items
                blogItems.forEach(item => {
                    if (category === 'all' || item.getAttribute('data-category') === category) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
        
        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            blogItems.forEach(item => {
                const title = item.querySelector('.modern-heading').textContent.toLowerCase();
                const excerpt = item.querySelector('.modern-text').textContent.toLowerCase();
                const category = item.querySelector('.modern-badge').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || excerpt.includes(searchTerm) || category.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Reset filters when searching
            if (searchTerm !== '') {
                filterButtons.forEach(btn => btn.classList.remove('modern-btn--active'));
                document.querySelector('[data-category="all"]').classList.add('modern-btn--active');
            }
        });
    }
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?>