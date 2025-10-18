<?php
// NextCode Group - Blog Detay Səhifəsi
define('SECURE_ACCESS', true);

// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';
require_once 'includes/page_functions.php';

// Blog post ID'sini al
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Blog posts data (sample data for now)
$blog_posts = [
    1 => [
        'id' => 1,
        'title' => '2024-cü ildə SEO Strategiyaları: Uğurlu Optimizasiya üçün Tam Bələdçi',
        'content' => '
            <p>Google-un ən son alqoritmlərini nəzərə alaraq, 2024-cü ildə SEO strategiyalarınızı necə qurmalısınız? Bu məqalədə ən effektiv üsulları öyrənəcəksiniz.</p>
            
            <h2>1. Core Web Vitals və Sayfa Sürəti</h2>
            <p>Google-un Core Web Vitals metrikaları artıq axtarış nəticələrində daha da vacib rol oynayır. LCP (Largest Contentful Paint), FID (First Input Delay) və CLS (Cumulative Layout Shift) kimi metrikaları optimallaşdırmaq üçün:</p>
            <ul>
                <li>Şəkilləri lazy loading ilə yükləyin</li>
                <li>CSS və JavaScript fayllarını minify edin</li>
                <li>CDN istifadə edin</li>
                <li>Browser caching tətbiq edin</li>
            </ul>
            
            <h2>2. AI və Machine Learning Alqoritmları</h2>
            <p>2024-cü ildə Google RankBrain və BERT kimi AI alqoritmları daha da təkmilləşir. Bu alqoritmlar üçün məzmununuzu optimallaşdırmaq üçün:</p>
            <ul>
                <li>Təbii dil istifadə edin</li>
                <li>Long-tail açar sözlərə fokuslanın</li>
                <li>Semantic search üçün məzmun yaradın</li>
                <li>FAQ bölmələri əlavə edin</li>
            </ul>
            
            <h2>3. Mobile-First Indexing</h2>
            <p>Google artıq əsasən mobile versiyaları index edir. Mobile optimallaşdırma üçün:</p>
            <ul>
                <li>Responsive dizayn tətbiq edin</li>
                <li>Touch-friendly elementlər yaradın</li>
                <li>Mobile sürətini artırın</li>
                <li>AMP səhifələri yaradın</li>
            </ul>
            
            <h2>4. E-A-T (Expertise, Authoritativeness, Trustworthiness)</h2>
            <p>Google məzmunun keyfiyyətini qiymətləndirərkən E-A-T faktorlarına böyük əhəmiyyət verir:</p>
            <ul>
                <li>Məzmununuzu mütəxəssis şəxslər yazsın</li>
                <li>Mənbələri göstərin</li>
                <li>Author bio və credentials əlavə edin</li>
                <li>Güvənilir məlumatlar təqdim edin</li>
            </ul>
            
            <h2>5. Voice Search Optimizasiyası</h2>
            <p>Voice search artıq SEO-nun vacib hissəsidir. Voice search üçün optimallaşdırmaq üçün:</p>
            <ul>
                <li>Conversational açar sözlər istifadə edin</li>
                <li>FAQ bölmələri yaradın</li>
                <li>Featured snippets üçün optimallaşdırın</li>
                <li>Local SEO-ya fokuslanın</li>
            </ul>
            
            <h2>Nəticə</h2>
            <p>2024-cü ildə SEO strategiyalarınızı bu trendlərə uyğun olaraq yeniləmək, axtarış nəticələrində yaxşı nəticələr əldə etmək üçün vacibdir. Əsas diqqəti məzmun keyfiyyətinə, texniki optimallaşdırmaya və istifadəçi təcrübəsinə yönəldin.</p>
        ',
        'category' => 'SEO',
        'author' => 'Əli Məmmədov',
        'date' => '15 Yanvar 2024',
        'read_time' => '8 dəq',
        'views' => '2.5K',
        'likes' => '156',
        'comments' => '23',
        'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=400&fit=crop',
        'tags' => ['SEO', 'Google', 'Optimizasiya', '2024', 'Strategiya']
    ],
    2 => [
        'id' => 2,
        'title' => 'Instagram Marketing: Məzmun Strategiyası və Engagement Artırma',
        'content' => '
            <p>Instagram-da uğurlu olmaq üçün hansı strategiyaları tətbiq etməlisiniz? Məzmun yaratmaqdan tutmuş engagement artırmağa qədər bütün mərhələləri bu məqalədə öyrənəcəksiniz.</p>
            
            <h2>1. Məzmun Strategiyası</h2>
            <p>Instagram-da uğurlu olmaq üçün düzgün məzmun strategiyası qurmaq vacibdir:</p>
            <ul>
                <li>Brand identity-nizi müəyyən edin</li>
                <li>Content calendar yaradın</li>
                <li>Müxtəlif məzmun növləri istifadə edin</li>
                <li>Trendləri izləyin</li>
            </ul>
            
            <h2>2. Engagement Artırma</h2>
            <p>Auditoriyanızla daha yaxşı əlaqə qurmaq üçün:</p>
            <ul>
                <li>Interactive content yaradın</li>
                <li>Stories və Reels istifadə edin</li>
                <li>Hashtag strategiyası qurun</li>
                <li>User-generated content təşviq edin</li>
            </ul>
            
            <h2>3. Instagram Reels Strategiyası</h2>
            <p>Reels Instagram-da ən populyar məzmun növüdür:</p>
            <ul>
                <li>Trending audio istifadə edin</li>
                <li>Vertical video formatında çəkin</li>
                <li>15-60 saniyəlik məzmun yaradın</li>
                <li>Engaging thumbnail-lər istifadə edin</li>
            </ul>
            
            <h2>4. Instagram Stories Marketing</h2>
            <p>Stories ilə müştərilərinizlə daha yaxşı əlaqə qurun:</p>
            <ul>
                <li>Behind-the-scenes məzmun paylaşın</li>
                <li>Interactive elementlər əlavə edin</li>
                <li>Polls və questions istifadə edin</li>
                <li>Highlight-lər yaradın</li>
            </ul>
            
            <h2>Nəticə</h2>
            <p>Instagram marketing-də uğurlu olmaq üçün düzgün strategiya qurmaq və daim yenilikləri izləmək vacibdir. Məzmun keyfiyyətinə fokuslanın və auditoriyanızla aktiv əlaqə saxlayın.</p>
        ',
        'category' => 'Sosial Media',
        'author' => 'Leyla Həsənova',
        'date' => '12 Yanvar 2024',
        'read_time' => '5 dəq',
        'views' => '1.8K',
        'likes' => '89',
        'comments' => '15',
        'image' => 'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=800&h=400&fit=crop',
        'tags' => ['Instagram', 'Marketing', 'Sosial Media', 'Engagement']
    ],
    3 => [
        'id' => 3,
        'title' => '2024-cü ildə Web Dizayn Trendləri: Minimalizmdən Interaktivliyə',
        'content' => '
            <p>Bu il web dizaynda hansı trendlər populyar olacaq? Minimalist dizayndan tutmuş interaktiv elementlərə qədər bütün yenilikləri bu məqalədə kəşf edəcəksiniz.</p>
            
            <h2>1. Neumorphism Dizayn</h2>
            <p>Neumorphism 2024-də ən populyar dizayn trendlərindən biridir:</p>
            <ul>
                <li>Soft shadows və subtle highlights</li>
                <li>Monochrome color palette</li>
                <li>Minimalist və clean görünüş</li>
                <li>3D elementlər</li>
            </ul>
            
            <h2>2. Glassmorphism</h2>
            <p>Glassmorphism modern və elegant görünüş təmin edir:</p>
            <ul>
                <li>Frosted glass effekti</li>
                <li>Transparent backgrounds</li>
                <li>Subtle borders</li>
                <li>Layered elements</li>
            </ul>
            
            <h2>3. Micro-interactions</h2>
            <p>Micro-interactions istifadəçi təcrübəsini yaxşılaşdırır:</p>
            <ul>
                <li>Hover animations</li>
                <li>Loading states</li>
                <li>Button feedback</li>
                <li>Smooth transitions</li>
            </ul>
            
            <h2>4. Dark Mode</h2>
            <p>Dark mode artıq standart hala gəlmişdir:</p>
            <ul>
                <li>Eye-friendly design</li>
                <li>Battery saving (mobile)</li>
                <li>Modern görünüş</li>
                <li>Accessibility improvement</li>
            </ul>
            
            <h2>Nəticə</h2>
            <p>2024-də web dizayn trendləri istifadəçi təcrübəsinə və funksionallığa fokuslanır. Minimalist yanaşma ilə interaktiv elementləri birləşdirərək modern və cəlbedici saytlar yaradın.</p>
        ',
        'category' => 'Web Dizayn',
        'author' => 'Rəşad Quliyev',
        'date' => '10 Yanvar 2024',
        'read_time' => '7 dəq',
        'views' => '2.1K',
        'likes' => '134',
        'comments' => '28',
        'image' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&h=400&fit=crop',
        'tags' => ['Web Dizayn', 'Trendlər', '2024', 'UI/UX', 'Dizayn']
    ],
    4 => [
        'id' => 4,
        'title' => 'Email Marketing Kampaniyaları: Açılma Dərəcəsini Artırma Yolları',
        'content' => '
            <p>Email marketing kampaniyalarınızın açılma dərəcəsini necə artıra bilərsiniz? Effektiv strategiyalar və məsləhətlər ilə ROI-nizi artırın.</p>
            
            <h2>1. Subject Line Optimizasiyası</h2>
            <p>Subject line email-in açılma dərəcəsini təyin edən ən vacib faktordur:</p>
            <ul>
                <li>Personalization istifadə edin</li>
                <li>Urgency yaradın</li>
                <li>Emotional triggers istifadə edin</li>
                <li>A/B testing aparın</li>
            </ul>
            
            <h2>2. Sender Reputation</h2>
            <p>Sender reputation email deliverability-ni təyin edir:</p>
            <ul>
                <li>Consistent sending schedule</li>
                <li>Clean email list</li>
                <li>Engagement monitoring</li>
                <li>Spam complaint-ləri azaldın</li>
            </ul>
            
            <h2>3. Email Content Quality</h2>
            <p>Keyfiyyətli məzmun engagement-i artırır:</p>
            <ul>
                <li>Relevant content</li>
                <li>Clear call-to-action</li>
                <li>Mobile-friendly design</li>
                <li>Personalized recommendations</li>
            </ul>
            
            <h2>4. Segmentation və Personalization</h2>
            <p>Auditoriyanızı segmentləyərək daha effektiv kampaniyalar yaradın:</p>
            <ul>
                <li>Demographic data</li>
                <li>Behavioral patterns</li>
                <li>Purchase history</li>
                <li>Engagement level</li>
            </ul>
            
            <h2>Nəticə</h2>
            <p>Email marketing-də uğurlu olmaq üçün subject line, sender reputation, content quality və personalization-a diqqət yetirin. Bu strategiyaları tətbiq edərək açılma dərəcənizi əhəmiyyətli dərəcədə artıra bilərsiniz.</p>
        ',
        'category' => 'Marketing',
        'author' => 'Aynur Əliyeva',
        'date' => '8 Yanvar 2024',
        'read_time' => '6 dəq',
        'views' => '1.6K',
        'likes' => '76',
        'comments' => '12',
        'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=400&fit=crop',
        'tags' => ['Email Marketing', 'Kampaniyalar', 'ROI', 'Personalization', 'Engagement']
    ],
    5 => [
        'id' => 5,
        'title' => 'Güçlü Brend İdentiteti Yaratmaq: Addımlar və Strategiyalar',
        'content' => '
            <p>Uğurlu brend identiteti necə yaradılır? Logo dizaynından tutmuş brend dəyərlərinə qədər bütün mərhələləri addım-addım bu məqalədə öyrənəcəksiniz.</p>
            
            <h2>1. Brend Strategiyası</h2>
            <p>Güclü brend identiteti yaratmaq üçün düzgün strategiya qurmaq vacibdir:</p>
            <ul>
                <li>Brand mission və vision müəyyən edin</li>
                <li>Target audience analizi</li>
                <li>Competitor research</li>
                <li>Unique value proposition</li>
            </ul>
            
            <h2>2. Visual Identity</h2>
            <p>Visual identity brendinizin görünən hissəsidir:</p>
            <ul>
                <li>Logo design və variations</li>
                <li>Color palette</li>
                <li>Typography system</li>
                <li>Visual guidelines</li>
            </ul>
            
            <h2>3. Brand Voice və Messaging</h2>
            <p>Brand voice brendinizin xarakterini təyin edir:</p>
            <ul>
                <li>Tone of voice guidelines</li>
                <li>Key messaging</li>
                <li>Brand story</li>
                <li>Communication style</li>
            </ul>
            
            <h2>4. Brand Experience</h2>
            <p>Brand experience müştərilərinizin brendinizlə qarşılaşdığı bütün nöqtələrdir:</p>
            <ul>
                <li>Customer touchpoints</li>
                <li>Service quality</li>
                <li>Consistency</li>
                <li>Emotional connection</li>
            </ul>
            
            <h2>Nəticə</h2>
            <p>Güclü brend identiteti yaratmaq üzərində işləməyə dəyər investisiyadır. Düzgün strategiya, visual identity, brand voice və brand experience ilə auditoriyanızda uzunmüddətli impression yarada bilərsiniz.</p>
        ',
        'category' => 'Brendinq',
        'author' => 'Nigar Məmmədova',
        'date' => '5 Yanvar 2024',
        'read_time' => '8 dəq',
        'views' => '1.9K',
        'likes' => '112',
        'comments' => '19',
        'image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=400&fit=crop',
        'tags' => ['Brendinq', 'Brand Identity', 'Marketing', 'Strategiya', 'Design']
    ]
];

// Post'u al
$post = $blog_posts[$post_id] ?? $blog_posts[1];

// Related posts
$related_posts = array_filter($blog_posts, function($p) use ($post_id) {
    return $p['id'] != $post_id;
});

// Dynamic content variables
$page_title = htmlspecialchars($post['title']) . ' - NextCode Group';
$meta_description = substr(strip_tags($post['content']), 0, 160) . '...';
$current_page = 'blog';

require_once 'includes/header.php';
?>
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --text-dark: #2d3748;
            --text-light: #718096;
            --bg-light: #f7fafc;
            --shadow: 0 10px 40px rgba(0,0,0,0.1);
            --shadow-hover: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            line-height: 1.7;
        }
        
        /* Navigation Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--text-dark) !important;
            transition: all 0.3s ease;
            margin: 0 10px;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            color: var(--primary-color) !important;
            font-weight: 600;
        }
        
        /* Article Header */
        .article-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 120px 0 80px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .article-header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.3;
        }
        
        .article-meta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
        }
        
        .meta-item i {
            color: var(--accent-color);
        }
        
        /* Article Content */
        .article-content {
            background: white;
            margin-top: -40px;
            border-radius: 20px 20px 0 0;
            padding: 60px 0;
            position: relative;
            z-index: 2;
        }
        
        .article-image {
            width: 100%;
            max-width: 800px;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            margin: 0 auto 40px;
            display: block;
            box-shadow: var(--shadow);
        }
        
        .article-text {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .article-text h2 {
            color: var(--text-dark);
            font-size: 1.8rem;
            font-weight: 700;
            margin: 40px 0 20px 0;
            padding-top: 20px;
            border-top: 2px solid var(--bg-light);
        }
        
        .article-text h3 {
            color: var(--text-dark);
            font-size: 1.4rem;
            font-weight: 600;
            margin: 30px 0 15px 0;
        }
        
        .article-text p {
            color: var(--text-light);
            font-size: 1.1rem;
            margin-bottom: 20px;
            line-height: 1.8;
        }
        
        .article-text ul {
            color: var(--text-light);
            font-size: 1.1rem;
            margin: 20px 0;
            padding-left: 30px;
        }
        
        .article-text li {
            margin-bottom: 10px;
        }
        
        /* Article Actions */
        .article-actions {
            background: var(--bg-light);
            padding: 40px 0;
            text-align: center;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-like {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-like:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-share {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .btn-share:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Tags */
        .article-tags {
            background: var(--bg-light);
            padding: 30px 0;
            text-align: center;
        }
        
        .tag {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            margin: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .tag:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Related Posts */
        .related-posts {
            background: white;
            padding: 80px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
        }
        
        .related-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.4s ease;
            height: 100%;
        }
        
        .related-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }
        
        .related-image {
            height: 200px;
            overflow: hidden;
        }
        
        .related-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        
        .related-card:hover .related-image img {
            transform: scale(1.1);
        }
        
        .related-content {
            padding: 25px;
        }
        
        .related-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .related-excerpt {
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .related-btn {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .related-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--text-dark) 0%, #1a202c 100%);
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer h5 {
            color: var(--accent-color);
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .footer p, .footer a {
            color: #cbd5e0;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer a:hover {
            color: var(--accent-color);
            transform: translateX(5px);
        }
        
        .footer-bottom {
            border-top: 1px solid #4a5568;
            padding-top: 30px;
            margin-top: 40px;
            text-align: center;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .article-header h1 {
                font-size: 2rem;
            }
            
            .article-meta {
                flex-direction: column;
                gap: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .article-text {
                padding: 0 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.php">
                <i class="fas fa-code text-primary me-2"></i>
                <span class="text-gradient">NextCode</span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="portfolio.php">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pricing.php">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="faq.php">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Article Header -->
    <section class="article-header">
        <div class="container">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="article-meta">
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($post['author']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span><?php echo htmlspecialchars($post['date']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span><?php echo htmlspecialchars($post['read_time']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-eye"></i>
                    <span><?php echo htmlspecialchars($post['views']); ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Article Content -->
    <section class="article-content">
        <div class="container">
            <img src="<?php echo $post['image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="article-image">
            
            <div class="article-text">
                <?php echo $post['content']; ?>
                            </div>
                        </div>
    </section>

    <!-- Article Actions -->
    <section class="article-actions">
        <div class="container">
            <div class="action-buttons">
                <button class="action-btn btn-like" onclick="likePost()">
                    <i class="fas fa-heart"></i>
                    <span id="likeCount"><?php echo $post['likes']; ?></span> Bəyəndim
                </button>
                <button class="action-btn btn-share" onclick="sharePost()">
                    <i class="fas fa-share"></i>
                    Paylaş
                </button>
                                </div>
                            </div>
    </section>

    <!-- Tags -->
    <section class="article-tags">
        <div class="container">
            <?php foreach ($post['tags'] as $tag): ?>
                <a href="blog.php?tag=<?php echo urlencode($tag); ?>" class="tag">
                    #<?php echo htmlspecialchars($tag); ?>
                </a>
            <?php endforeach; ?>
                        </div>
    </section>

                    <!-- Related Posts -->
    <section class="related-posts">
        <div class="container">
            <h2 class="section-title">Əlaqəli Məqalələr</h2>
                        <div class="row">
                <?php foreach (array_slice($related_posts, 0, 3) as $related_post): ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="related-card">
                            <div class="related-image">
                                <img src="<?php echo $related_post['image']; ?>" alt="<?php echo htmlspecialchars($related_post['title']); ?>" loading="lazy">
                                    </div>
                            <div class="related-content">
                                <h3 class="related-title"><?php echo htmlspecialchars($related_post['title']); ?></h3>
                                <p class="related-excerpt"><?php echo htmlspecialchars(substr($related_post['content'], 0, 150)) . '...'; ?></p>
                                <a href="blog-detail.php?id=<?php echo $related_post['id']; ?>" class="related-btn">
                                    Oxu
                                </a>
                                    </div>
                                </div>
                            </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <script>
        function likePost() {
            const likeCount = document.getElementById('likeCount');
            const currentLikes = parseInt(likeCount.textContent);
            likeCount.textContent = currentLikes + 1;
            
            // Like button'u disable et
            const likeBtn = document.querySelector('.btn-like');
            likeBtn.disabled = true;
            likeBtn.style.opacity = '0.6';
        }
        
        function sharePost() {
            if (navigator.share) {
                navigator.share({
                    title: '<?php echo addslashes($post['title']); ?>',
                    text: '<?php echo addslashes(substr(strip_tags($post['content']), 0, 100)); ?>...',
                    url: window.location.href
                });
            } else {
                // Fallback: URL'yi kopyala
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link kopyalandı!');
                });
            }
        }
    </script>

<?php require_once 'includes/footer.php'; ?>