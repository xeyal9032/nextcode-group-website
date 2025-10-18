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

// Blog posts will be loaded dynamically via API
// Fallback blog data in case API fails
$fallback_posts = [
    [
        'id' => 1,
        'title' => 'SEO Optimizasiyası: Axtarış Nəticələrində Yüksəlmək',
        'slug' => 'seo-optimizasiyasi',
        'excerpt' => 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün SEO strategiyaları və texnikaları. Google, Bing və digər axtarış sistemlərində daha yüksək reytinq əldə etmək üçün SEO strategiyaları və texnikaları.',
        'featured_image' => 'images/blog/blog-1.jpg',
        'category_name' => 'SEO',
        'tags' => 'SEO, Axtarış, Optimizasiya',
        'is_featured' => 1,
        'created_at' => '2024-01-15 10:00:00',
        'read_time' => '8'
    ],
    [
        'id' => 2,
        'title' => 'Sosial Media Marketinqi: Brendinizi Gücləndirin',
        'slug' => 'sosial-media-marketinqi',
        'excerpt' => 'Sosial media platformalarında brendinizi gücləndirmək üçün praktik məsləhətlər və strategiyalar. Facebook, Instagram, LinkedIn və TikTok platformalarında effektiv marketinq.',
        'featured_image' => 'images/blog/blog-2.jpg',
        'category_name' => 'Sosial Media',
        'tags' => 'Sosial Media, Marketinq, Brend',
        'is_featured' => 1,
        'created_at' => '2024-01-10 14:30:00',
        'read_time' => '6'
    ],
    [
        'id' => 3,
        'title' => 'Modern Web Dizayn Trendləri 2024',
        'slug' => 'modern-web-dizayn-trendleri',
        'excerpt' => '2024-cü ildə web dizayn sahəsində populyar olan trendlər və yeniliklər. Minimalizm, dark mode, 3D elementlər və mikro animasiyalar.',
        'featured_image' => 'images/blog/blog-3.jpg',
        'category_name' => 'Web Dizayn',
        'tags' => 'Web Dizayn, Trendlər, UX/UI',
        'is_featured' => 0,
        'created_at' => '2024-01-05 09:15:00',
        'read_time' => '10'
    ],
    [
        'id' => 4,
        'title' => 'Digital Marketinq Strategiyaları',
        'slug' => 'digital-marketinq-strategiyalari',
        'excerpt' => 'Rəqəmsal dünyada uğurlu marketinq strategiyaları və taktikaları. SEO, PPC, Sosial Media və Email Marketing kanallarında ROI artırma.',
        'featured_image' => 'images/blog/blog-4.jpg',
        'category_name' => 'Marketing',
        'tags' => 'Digital Marketinq, Strategiya, ROI',
        'is_featured' => 1,
        'created_at' => '2024-01-01 16:45:00',
        'read_time' => '12'
    ]
];

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
                <button class="modern-btn modern-btn--outline" data-category="social-media">Sosial Media</button>
                <button class="modern-btn modern-btn--outline" data-category="web-design">Web Dizayn</button>
                <button class="modern-btn modern-btn--outline" data-category="marketing">Marketing</button>
                <button class="modern-btn modern-btn--outline" data-category="branding">Brendinq</button>
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
                <div class="modern-text-center modern-m-8">
                    <div class="modern-loading-spinner">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="modern-text">Blog məqalələri yüklənir...</p>
                    </div>
                </div>
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
                        document.getElementById('blogGrid').innerHTML = '<div class="modern-text-center modern-m-8"><p class="modern-text">Blog məqalələri yüklənə bilmədi.</p></div>';
                    }
                } catch (error) {
                    console.error('Error loading blog posts:', error);
                    document.getElementById('blogGrid').innerHTML = '<div class="modern-text-center modern-m-8"><p class="modern-text">Xəta baş verdi.</p></div>';
                }
            }
            
            function displayBlogPosts(posts) {
                const blogGrid = document.getElementById('blogGrid');
                let html = '';
                
                posts.forEach(post => {
                    const excerpt = post.excerpt || post.content.substring(0, 150) + '...';
                    const imageUrl = post.featured_image || 'images/blog/blog-1.jpg';
                    
                    html += `
                        <div class="modern-card modern-card--blog modern-animate--fadeInUp" data-category="${post.category_name || 'general'}">
                            <div class="modern-card__image">
                                <img src="${imageUrl}" alt="${post.title}" class="modern-card__img" onerror="this.src='images/placeholder.svg'">
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