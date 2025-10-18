<?php
define('SECURE_ACCESS', true);

// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';
require_once 'includes/page_functions.php';

// Proje ID'sini al
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$project_id) {
    header('Location: portfolio.php');
    exit;
}

// PDO bağlantısını al
try {
    $pdo = $pdo ?? (new Database())->getConnection();
} catch (Exception $e) {
    $pdo = null;
}

// Proje detaylarını veritabanından çek veya statik veriden al
$project = null;
$project_images = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                p.*,
                c.name as category_name
            FROM portfolio_projects p
            LEFT JOIN portfolio_categories c ON p.category_id = c.id
            WHERE p.id = ? AND p.is_published = 1 AND p.active = 1
        ");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($project) {
            // Proje görsellerini çek
            $stmt = $pdo->prepare("SELECT * FROM portfolio_images WHERE project_id = ? ORDER BY sort_order");
            $stmt->execute([$project_id]);
            $project_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        error_log("Portfolio detay veritabanı hatası: " . $e->getMessage());
        $project = null;
    }
}

// Eğer veritabanından veri alınamadıysa, statik veriyi kullan
if (!$project) {
    $static_projects = [
        1 => [
            'id' => 1,
            'title' => 'E-Ticaret Platformu',
            'slug' => 'e-ticaret-platformu',
            'category_name' => 'E-Ticaret',
            'category_id' => 2,
            'short_description' => 'Modern ve kullanıcı dostu e-ticaret platformu',
            'description' => 'Bu proje, modern e-ticaret ihtiyaçlarını karşılayan kapsamlı bir online satış platformudur. Responsive tasarım, güvenli ödeme sistemi ve gelişmiş yönetim paneli içerir. Platform, kullanıcı dostu arayüzü ile müşterilerin kolayca alışveriş yapmasını sağlar.',
            'client_name' => 'Global Market Ltd.',
            'project_url' => 'https://example.com/ecommerce',
            'demo_url' => 'https://demo.example.com/ecommerce',
            'github_url' => 'https://github.com/example/ecommerce',
                    'technologies' => '["PHP", "MySQL", "JavaScript", "Bootstrap", "Stripe API"]',
        'featured_image' => 'images/portfolio/ecommerce-project.jpg',
        'project_date' => '2023-01-15',
        'challenges' => 'Güvenli ödeme sistemi, stok yönetimi ve kullanıcı deneyimi',
        'solutions' => 'Stripe API entegrasyonu, real-time stok takibi ve responsive tasarım',
                'features' => 'Güvenli ödeme sistemi\nStok yönetimi\nKullanıcı paneli\nAdmin dashboard\nAnalytics raporları',
        'completion_date' => '2023-06-20',
            'duration' => '6 ay',
            'budget' => 25000.00,
            'team_size' => 5,
            'results' => '["Satış artışı: %300", "Kullanıcı memnuniyeti: %95", "Performans artışı: %200"]',
            'created_at' => '2023-01-15',
            'active' => 1,
            'is_published' => 1,
            'sort_order' => 1,
            'project_status' => 'completed',
            'is_featured' => 1,
            'view_count' => 0,
            'seo_title' => 'E-Ticaret Platformu - Modern Online Satış Çözümü',
            'seo_description' => 'Modern ve kullanıcı dostu e-ticaret platformu. Responsive tasarım, güvenli ödeme sistemi ve gelişmiş yönetim paneli.',
            'seo_keywords' => 'e-ticaret, online satış, e-commerce, responsive tasarım, güvenli ödeme',
            'updated_at' => '2023-01-15'
        ],
        2 => [
            'id' => 2,
            'title' => 'Kurumsal Web Sitesi',
            'slug' => 'kurumsal-web-sitesi',
            'category_name' => 'Kurumsal',
            'category_id' => 4,
            'short_description' => 'Profesyonel kurumsal web sitesi tasarımı',
            'description' => 'Şirketin kurumsal kimliğini yansıtan modern ve profesyonel web sitesi. SEO optimizasyonu, hızlı yükleme ve mobil uyumluluk özellikleri ile donatılmıştır. Kurumsal iletişim ve marka bilinirliği artışı sağlanmıştır.',
            'client_name' => 'Tech Solutions Corp.',
            'project_url' => 'https://example.com/corporate',
            'demo_url' => 'https://demo.example.com/corporate',
            'github_url' => 'https://github.com/example/corporate',
                    'technologies' => '["HTML5", "CSS3", "JavaScript", "WordPress", "SEO"]',
        'featured_image' => 'images/portfolio/corporate-website.jpg',
        'project_date' => '2023-03-10',
        'challenges' => 'Kurumsal kimlik tutarlılığı, SEO optimizasyonu ve mobil uyumluluk gereksinimleri',
        'solutions' => 'Modern tasarım sistemi, responsive framework ve SEO best practices uygulandı',
                'features' => 'Responsive tasarım\nSEO optimizasyonu\nHızlı yükleme\nMobil uyumluluk\nİçerik yönetim sistemi',
        'completion_date' => '2023-05-15',
            'duration' => '3 ay',
            'budget' => '15000',
            'team_size' => 3,
            'results' => '["Trafik artışı: %150", "SEO sıralaması: İlk 10", "Mobil kullanım: %70"]',
            'created_at' => '2023-03-10',
            'active' => 1,
            'is_published' => 1,
            'sort_order' => 2,
            'project_status' => 'completed',
            'is_featured' => 0,
            'view_count' => 0,
            'seo_title' => 'Kurumsal Web Sitesi - Profesyonel Şirket Kimliği',
            'seo_description' => 'Profesyonel kurumsal web sitesi tasarımı. SEO optimizasyonu, hızlı yükleme ve mobil uyumluluk.',
            'seo_keywords' => 'kurumsal web sitesi, şirket kimliği, SEO, mobil uyumlu, profesyonel tasarım',
            'updated_at' => '2023-03-10'
        ],
        3 => [
            'id' => 3,
            'title' => 'Mobil Uygulama Geliştirme',
            'slug' => 'mobil-uygulama-gelistirme',
            'category_name' => 'Mobil Uygulama',
            'category_id' => 3,
            'short_description' => 'iOS ve Android için native mobil uygulama',
            'description' => 'Cross-platform mobil uygulama geliştirme projesi. Hem iOS hem de Android platformları için optimize edilmiş, kullanıcı dostu arayüz ve performans odaklı tasarım. Uygulama mağazalarında yüksek puanlar alınmıştır.',
            'client_name' => 'App Innovators',
            'project_url' => 'https://example.com/mobile-app',
            'demo_url' => 'https://demo.example.com/mobile-app',
            'github_url' => 'https://github.com/example/mobile-app',
                    'technologies' => '["React Native", "Node.js", "MongoDB", "Firebase", "Redux"]',
        'featured_image' => 'images/portfolio/mobile-app.jpg',
        'project_date' => '2023-02-20',
        'challenges' => 'Cross-platform uyumluluk, performans optimizasyonu ve native özellikler',
        'solutions' => 'React Native framework, performans optimizasyonu ve platform-specific API\'ler',
                'features' => 'Cross-platform uyumluluk\nNative performans\nOffline çalışma\nPush bildirimler\nAnalytics entegrasyonu',
        'completion_date' => '2023-08-10',
            'duration' => '6 ay',
            'budget' => '35000',
            'team_size' => 6,
            'results' => '["İndirme sayısı: 50K+", "Kullanıcı puanı: 4.8/5", "Crash oranı: %0.1"]',
            'created_at' => '2023-02-20',
            'active' => 1,
            'is_published' => 1,
            'sort_order' => 3,
            'project_status' => 'completed',
            'is_featured' => 1,
            'view_count' => 0,
            'seo_title' => 'Mobil Uygulama Geliştirme - iOS ve Android',
            'seo_description' => 'Cross-platform mobil uygulama geliştirme. iOS ve Android için optimize edilmiş, kullanıcı dostu arayüz.',
            'seo_keywords' => 'mobil uygulama, iOS, Android, cross-platform, React Native, mobil geliştirme',
            'updated_at' => '2023-02-20'
        ],
        4 => [
            'id' => 4,
            'title' => 'AI Chatbot Sistemi',
            'slug' => 'ai-chatbot-sistemi',
            'category_name' => 'AI & Makine Öğrenmesi',
            'category_id' => 5,
            'short_description' => 'Yapay zeka destekli müşteri hizmetleri chatbotu',
            'description' => 'Doğal dil işleme teknolojileri kullanılarak geliştirilmiş akıllı chatbot sistemi. Müşteri sorularını anlayıp uygun cevaplar verebilen, sürekli öğrenen yapay zeka çözümü. Müşteri hizmetleri maliyetlerini %60 azaltmıştır.',
            'client_name' => 'AI Solutions Inc.',
            'project_url' => 'https://example.com/ai-chatbot',
            'demo_url' => 'https://demo.example.com/ai-chatbot',
            'github_url' => 'https://github.com/example/ai-chatbot',
                    'technologies' => '["Python", "TensorFlow", "NLP", "Django", "PostgreSQL"]',
        'featured_image' => 'images/portfolio/ai-chatbot.jpg',
        'project_date' => '2023-04-05',
        'challenges' => 'Doğal dil işleme, makine öğrenmesi modeli ve gerçek zamanlı yanıt',
        'solutions' => 'TensorFlow NLP, Django backend ve WebSocket real-time iletişim',
                'features' => 'Doğal dil işleme\nMakine öğrenmesi\nGerçek zamanlı yanıt\nÇok dilli destek\nAnalytics dashboard',
        'completion_date' => '2023-09-30',
            'duration' => '6 ay',
            'budget' => '45000',
            'team_size' => 4,
            'results' => '["Doğruluk oranı: %92", "Yanıt süresi: 2 saniye", "Maliyet tasarrufu: %60"]',
            'created_at' => '2023-04-05',
            'active' => 1,
            'is_published' => 1,
            'sort_order' => 4,
            'project_status' => 'completed',
            'is_featured' => 1,
            'view_count' => 0,
            'seo_title' => 'AI Chatbot Sistemi - Yapay Zeka Destekli',
            'seo_description' => 'Yapay zeka destekli müşteri hizmetleri chatbotu. Doğal dil işleme ve sürekli öğrenme.',
            'seo_keywords' => 'AI chatbot, yapay zeka, müşteri hizmetleri, NLP, makine öğrenmesi',
            'updated_at' => '2023-04-05'
        ],
        5 => [
            'id' => 5,
            'title' => 'Responsive Web Tasarım',
            'slug' => 'responsive-web-tasarim',
            'category_name' => 'Web Tasarım',
            'category_id' => 1,
            'short_description' => 'Tüm cihazlarda mükemmel görünen web tasarımı',
            'description' => 'Modern responsive web tasarım projesi. Desktop, tablet ve mobil cihazlarda mükemmel görünüm sağlayan, kullanıcı deneyimi odaklı tasarım çözümü. Tüm cihazlarda tutarlı kullanıcı deneyimi sunar.',
            'client_name' => 'Creative Design Studio',
            'project_url' => 'https://example.com/responsive-design',
            'demo_url' => 'https://demo.example.com/responsive-design',
            'github_url' => 'https://github.com/example/responsive-design',
                    'technologies' => '["HTML5", "CSS3", "JavaScript", "Sass", "Gulp"]',
        'featured_image' => 'images/portfolio/responsive-design.jpg',
        'project_date' => '2023-05-12',
        'challenges' => 'Tüm cihazlarda tutarlı görünüm ve performans optimizasyonu',
        'solutions' => 'Mobile-first tasarım, CSS Grid/Flexbox ve performans optimizasyonu',
                'features' => 'Mobile-first tasarım\nCSS Grid/Flexbox\nPerformans optimizasyonu\nCross-browser uyumluluk\nModern CSS özellikleri',
        'completion_date' => '2023-07-25',
            'duration' => '3 ay',
            'budget' => '12000',
            'team_size' => 2,
            'results' => '["Mobil uyumluluk: %100", "Yükleme hızı: 2 saniye", "Kullanıcı memnuniyeti: %98"]',
            'created_at' => '2023-01-15',
            'active' => 1,
            'is_published' => 1,
            'sort_order' => 5,
            'project_status' => 'completed',
            'is_featured' => 0,
            'view_count' => 0,
            'seo_title' => 'Responsive Web Tasarım - Tüm Cihazlarda Mükemmel',
            'seo_description' => 'Modern responsive web tasarım. Desktop, tablet ve mobil cihazlarda mükemmel görünüm.',
            'seo_keywords' => 'responsive tasarım, mobil uyumlu, web tasarım, tüm cihazlar, modern tasarım',
            'updated_at' => '2023-05-12'
        ]
    ];
    
    $project = $static_projects[$project_id] ?? null;
}

if (!$project) {
    header('Location: portfolio.php');
    exit;
}

// Sayfa bilgileri
$page_title = $project['title'] . ' - Portfolio Təfərrüatı';
$meta_description = substr($project['description'], 0, 160);
$meta_keywords = $project['category_name'] . ', ' . $project['technologies'] . ', veb dizayn, veb inkişaf, portfolio, NextCode, Azərbaycan';
$current_page = 'portfolio';

// Teknolojileri parse et
$technologies = [];
if (!empty($project['technologies'])) {
    if (is_string($project['technologies']) && $project['technologies'][0] === '[') {
        $technologies = json_decode($project['technologies'], true) ?: [];
    } else {
        $technologies = array_filter(array_map('trim', explode(',', $project['technologies'])));
    }
}

// Sonuçları parse et
$results = [];
if (!empty($project['results'])) {
    if (is_string($project['results']) && $project['results'][0] === '[') {
        $results = json_decode($project['results'], true) ?: [];
    }
}

require_once 'includes/header.php';
?>

<!-- Project Hero Section -->
<section class="project-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Əsas Səhifə</a></li>
                        <li class="breadcrumb-item"><a href="portfolio.php">Portfolio</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($project['title']); ?></li>
                    </ol>
                </nav>
                
                <div class="project-category"><?php echo htmlspecialchars($project['category_name']); ?></div>
                <h1 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h1>
                <p class="project-summary"><?php echo htmlspecialchars($project['short_description'] ?? substr($project['description'], 0, 200) . '...'); ?></p>
                
                <div class="project-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>Tamamlanma: <?php echo date('F Y', strtotime($project['completion_date'] ?? $project['created_at'])); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span>Müddət: <?php echo $project['duration'] ?? '2-4 həftə'; ?></span>
                    </div>
                    <?php if ($project['client_name']): ?>
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <span>Müştəri: <?php echo htmlspecialchars($project['client_name']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($project['project_url']): ?>
                <div class="project-actions">
                    <a href="<?php echo htmlspecialchars($project['project_url']); ?>" target="_blank" class="btn btn-primary btn-lg">
                        <i class="fas fa-external-link-alt"></i> Layihəni Görüntülə
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <div class="project-featured-image">
                                         <img src="<?php echo htmlspecialchars($project['featured_image'] ?? 'images/placeholder.svg'); ?>?v=<?php echo time(); ?>" 
                          alt="<?php echo htmlspecialchars($project['title']); ?>" 
                          class="img-fluid rounded-lg">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Project Gallery -->
<?php if (!empty($project_images)): ?>
<section class="project-gallery">
    <div class="container">
        <h2 class="section-title">Layihə Şəkilləri</h2>
        <div class="row gallery-grid">
            <?php foreach ($project_images as $index => $image): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#galleryModal" data-slide="<?php echo $index; ?>">
                    <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($image['alt_text'] ?? $project['title']); ?>" 
                         class="img-fluid rounded">
                    <div class="gallery-overlay">
                        <i class="fas fa-search-plus"></i>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Project Details -->
<section class="project-details">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="project-content">
                    <h2>Layihə Haqqında</h2>
                    <div class="content-text">
                        <?php echo nl2br(htmlspecialchars($project['description'])); ?>
                    </div>
                    
                    <?php if (!empty($project['challenges'])): ?>
                    <h3>Qarşılaşılan Çətinliklər</h3>
                    <div class="content-text">
                        <?php echo nl2br(htmlspecialchars($project['challenges'])); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($project['solutions'])): ?>
                    <h3>Həllər</h3>
                    <div class="content-text">
                        <?php echo nl2br(htmlspecialchars($project['solutions'])); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($results)): ?>
                    <h3>Nəticələr</h3>
                    <div class="content-text">
                        <?php foreach ($results as $result): ?>
                            <p><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($result); ?></p>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="project-sidebar">
                    <!-- Technologies -->
                    <?php if (!empty($technologies)): ?>
                    <div class="sidebar-section">
                        <h4>İstifadə Olunan Texnologiyalar</h4>
                        <div class="tech-stack">
                            <?php foreach ($technologies as $tech): ?>
                                <span class="tech-badge"><?php echo htmlspecialchars($tech); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Project Info -->
                    <div class="sidebar-section">
                        <h4>Layihə Məlumatları</h4>
                        <div class="info-list">
                            <div class="info-item">
                                <strong>Kateqoriya:</strong>
                                <span><?php echo htmlspecialchars($project['category_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Status:</strong>
                                <span class="status-badge status-<?php echo $project['active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $project['active'] ? 'Tamamlandı' : 'Davam edir'; ?>
                                </span>
                            </div>
                            <?php if ($project['team_size']): ?>
                            <div class="info-item">
                                <strong>Komanda Böyüklüyü:</strong>
                                <span><?php echo htmlspecialchars($project['team_size']); ?> nəfər</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Features -->
                    <?php if (!empty($project['features'])): ?>
                    <div class="sidebar-section">
                        <h4>Xüsusiyyətlər</h4>
                        <ul class="feature-list">
                            <?php 
                            $features = array_filter(array_map('trim', explode('\n', $project['features'])));
                            foreach ($features as $feature): 
                            ?>
                                <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Contact CTA -->
                    <div class="sidebar-section cta-section">
                        <h4>Belə Bir Layihəyə Ehtiyacınız Varmı?</h4>
                        <p>Sizə də belə bir layihə edə bilərik. Dərhal əlaqə saxlayın!</p>
                        <a href="contact.php" class="btn btn-primary btn-block">
                            <i class="fas fa-envelope"></i> Əlaqə Saxla
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Projects -->
<section class="related-projects">
    <div class="container">
        <h2 class="section-title text-center">Oxşar Layihələr</h2>
        <div class="row">
            <?php
            // Benzer projeleri çek
            if ($pdo) {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM portfolio_projects WHERE category_id = ? AND id != ? AND is_published = 1 AND active = 1 ORDER BY RAND() LIMIT 3");
                    $stmt->execute([$project['category_id'], $project_id]);
                    $related_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($related_projects as $related): 
                ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="related-project-card">
                            <div class="related-image">
                                <img src="<?php echo htmlspecialchars($related['featured_image'] ?? 'images/placeholder.svg'); ?>" 
                                     alt="<?php echo htmlspecialchars($related['title']); ?>" 
                                     class="img-fluid">
                            </div>
                            <div class="related-content">
                                <div class="related-category"><?php echo htmlspecialchars($related['category_name']); ?></div>
                                <h5><?php echo htmlspecialchars($related['title']); ?></h5>
                                <p><?php echo htmlspecialchars(substr($related['description'], 0, 100)) . '...'; ?></p>
                                <a href="portfolio-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    Təfərrüatları Gör
                                </a>
                            </div>
                        </div>
                    </div>
                <?php 
                    endforeach;
                } catch (PDOException $e) {
                    error_log("Oxşar layihələr xətası: " . $e->getMessage());
                }
            } else {
                // PDO bağlantısı yoksa statik verileri göster
                $static_related = [
                    [
                        'id' => 3,
                        'title' => 'Mobil Uygulama Geliştirme',
                        'category_name' => 'Mobil Uygulama',
                        'description' => 'Cross-platform mobil uygulama geliştirme projesi.',
                        'featured_image' => 'images/portfolio/mobile-app.svg'
                    ],
                    [
                        'id' => 4,
                        'title' => 'AI Chatbot Sistemi',
                        'category_name' => 'AI & Makine Öğrenmesi',
                        'description' => 'Yapay zeka destekli müşteri hizmetleri chatbotu.',
                        'featured_image' => 'images/portfolio/ai-chatbot.svg'
                    ],
                    [
                        'id' => 5,
                        'title' => 'Responsive Web Tasarım',
                        'category_name' => 'Web Tasarım',
                        'description' => 'Modern responsive web tasarım projesi.',
                        'featured_image' => 'images/portfolio/responsive-design.svg'
                    ]
                ];
                
                foreach ($static_related as $related): 
                ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="related-project-card">
                            <div class="related-image">
                                <img src="<?php echo htmlspecialchars($related['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($related['title']); ?>" 
                                     class="img-fluid">
                            </div>
                            <div class="related-content">
                                <div class="related-category"><?php echo htmlspecialchars($related['category_name']); ?></div>
                                <h5><?php echo htmlspecialchars($related['title']); ?></h5>
                                <p><?php echo htmlspecialchars(substr($related['description'], 0, 100)) . '...'; ?></p>
                                <a href="portfolio-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    Təfərrüatları Gör
                                </a>
                            </div>
                        </div>
                    </div>
                <?php 
                endforeach;
            }
            ?>
        </div>
    </div>
</section>

<!-- Gallery Modal -->
<?php if (!empty($project_images)): ?>
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Layihə Şəkilləri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($project_images as $index => $image): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($image['alt_text'] ?? $project['title']); ?>" 
                                 class="d-block w-100">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($project_images) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
/* Modern Portfolio Detail Styles */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    --glass-bg: rgba(255, 255, 255, 0.25);
    --glass-border: rgba(255, 255, 255, 0.18);
    --shadow-light: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    --shadow-heavy: 0 20px 40px 0 rgba(31, 38, 135, 0.5);
}

.project-hero {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    color: white;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    padding: 120px 0;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.project-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
    pointer-events: none;
}

.project-hero .container {
    position: relative;
    z-index: 2;
}

.breadcrumb {
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid var(--glass-border);
    border-radius: 50px;
    padding: 12px 24px;
    margin-bottom: 40px;
    animation: fadeInDown 1s ease-out;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.breadcrumb-item a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.breadcrumb-item a:hover {
    color: white;
}

.breadcrumb-item.active {
    color: white;
    font-weight: 600;
}

.project-category {
    background: var(--accent-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 1.1rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 20px;
    display: inline-block;
    animation: fadeInLeft 1s ease-out 0.3s both;
}

@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.project-title {
    font-size: 4.5rem;
    font-weight: 900;
    margin-bottom: 30px;
    text-shadow: 0 8px 16px rgba(0,0,0,0.4);
    line-height: 1.1;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: fadeInLeft 1s ease-out 0.6s both;
}

.project-summary {
    font-size: 1.3rem;
    line-height: 1.7;
    margin-bottom: 40px;
    color: rgba(255, 255, 255, 0.9);
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    animation: fadeInLeft 1s ease-out 0.9s both;
}

.project-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
    margin-bottom: 50px;
    animation: fadeInUp 1s ease-out 1.2s both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1rem;
    font-weight: 500;
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    padding: 12px 20px;
    border-radius: 30px;
    transition: all 0.3s ease;
}

.meta-item:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

.meta-item i {
    color: #4facfe;
    font-size: 1.1rem;
}

.project-actions {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    animation: fadeInUp 1s ease-out 1.5s both;
}

.project-actions .btn {
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid var(--glass-border);
    color: white;
    padding: 15px 30px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.project-actions .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.project-actions .btn:hover::before {
    left: 100%;
}

.project-actions .btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-3px);
    box-shadow: var(--shadow-heavy);
}

.project-actions .btn-primary {
    background: var(--accent-gradient);
    border: none;
}

.project-actions .btn-primary:hover {
    background: var(--secondary-gradient);
    box-shadow: 0 15px 35px rgba(79, 172, 254, 0.4);
}

.project-featured-image {
    position: relative;
    animation: fadeInRight 1s ease-out 1.8s both;
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.project-featured-image img {
    border-radius: 25px;
    box-shadow: var(--shadow-heavy);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    filter: brightness(0.95) contrast(1.1);
}

.project-featured-image:hover img {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 30px 60px rgba(0,0,0,0.4);
    filter: brightness(1.05) contrast(1.2);
}

/* Modern Project Gallery */
.project-gallery {
    padding: 100px 0;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    position: relative;
}

.project-gallery::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="%23667eea" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>') repeat;
    pointer-events: none;
}

.project-gallery .container {
    position: relative;
    z-index: 1;
}

.section-title {
    font-size: 3rem;
    font-weight: 800;
    color: #2c3e50;
    margin-bottom: 60px;
    text-align: center;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: var(--accent-gradient);
    border-radius: 2px;
}

.gallery-item {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.gallery-item:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 30px 60px rgba(0,0,0,0.2);
}

.gallery-item img {
    width: 100%;
    height: 280px;
    object-fit: cover;
    transition: all 0.5s ease;
    filter: brightness(0.95);
}

.gallery-item:hover img {
    filter: brightness(1.1);
    transform: scale(1.05);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.4s ease;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-overlay i {
    color: white;
    font-size: 2.5rem;
    text-shadow: 0 4px 8px rgba(0,0,0,0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Modern Project Details */
.project-details {
    padding: 100px 0;
    background: white;
}

.project-content h2,
.project-content h3 {
    color: #2c3e50;
    margin-bottom: 25px;
}

.project-content h2 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 40px;
    position: relative;
    padding-left: 40px;
}

.project-content h2::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 40px;
    background: var(--accent-gradient);
    border-radius: 3px;
}

.project-content h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-top: 50px;
    position: relative;
    padding-left: 30px;
}

.project-content h3::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 30px;
    background: var(--secondary-gradient);
    border-radius: 2px;
}

.content-text {
    font-size: 1.2rem;
    line-height: 1.9;
    color: #495057;
    margin-bottom: 35px;
    background: #f8f9fa;
    padding: 30px;
    border-radius: 15px;
    border-left: 4px solid #667eea;
}

.project-sidebar {
    padding-left: 40px;
}

.sidebar-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
    margin-bottom: 40px;
    color: white;
    position: relative;
    overflow: hidden;
}

.sidebar-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
    pointer-events: none;
}

.sidebar-section h4 {
    color: white;
    font-weight: 700;
    margin-bottom: 25px;
    font-size: 1.4rem;
    position: relative;
    z-index: 1;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.tech-stack {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    position: relative;
    z-index: 1;
}

.tech-badge {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: white;
    padding: 10px 18px;
    border-radius: 25px;
    font-size: 0.95rem;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.tech-badge:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.info-list {
    space-y: 20px;
    position: relative;
    z-index: 1;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    font-weight: 500;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item strong {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
}

.status-badge {
    padding: 6px 15px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: rgba(40, 167, 69, 0.2);
    color: #4facfe;
    border: 1px solid rgba(79, 172, 254, 0.3);
}

.feature-list {
    list-style: none;
    padding: 0;
    position: relative;
    z-index: 1;
}

.feature-list li {
    padding: 12px 0;
    display: flex;
    align-items: center;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    padding-left: 35px;
}

.feature-list li:hover {
    transform: translateX(5px);
}

.feature-list li::before {
    content: '✓';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    color: #4facfe;
    font-weight: bold;
    font-size: 1.2rem;
    width: 25px;
    height: 25px;
    background: rgba(79, 172, 254, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cta-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-align: center;
}

.cta-section h4 {
    color: white;
}

.cta-section p {
    opacity: 0.9;
    margin-bottom: 20px;
}

.btn-block {
    width: 100%;
}

/* Modern CTA Section */
.project-cta {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 120px 0;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.project-cta::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="hexagons" width="50" height="43.4" patternUnits="userSpaceOnUse"><polygon points="25,0 50,14.4 50,28.9 25,43.4 0,28.9 0,14.4" fill="none" stroke="%23ffffff" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23hexagons)"/></svg>') repeat;
    animation: float 20s ease-in-out infinite;
}

.cta-content {
    position: relative;
    z-index: 1;
}

.cta-content h2 {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 25px;
    text-shadow: 0 4px 8px rgba(0,0,0,0.3);
    animation: fadeInUp 1s ease-out;
}

.cta-content p {
    font-size: 1.4rem;
    margin-bottom: 40px;
    opacity: 0.95;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
    animation: fadeInUp 1s ease-out 0.2s both;
}

.btn-cta {
    background: white;
    color: #667eea;
    padding: 20px 50px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.2rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-block;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 1px;
    animation: fadeInUp 1s ease-out 0.4s both;
}

.btn-cta::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s;
}

.btn-cta:hover::before {
    left: 100%;
}

.btn-cta:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    color: #667eea;
    text-decoration: none;
}

/* Modern Related Projects */
.related-projects {
    padding: 120px 0;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    position: relative;
}

.related-projects::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23667eea" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>') repeat;
    pointer-events: none;
}

.related-projects .container {
    position: relative;
    z-index: 1;
}

.related-project-card {
    background: white;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    position: relative;
}

.related-project-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: var(--accent-gradient);
    transform: scaleX(0);
    transition: transform 0.5s ease;
}

.related-project-card:hover::before {
    transform: scaleX(1);
}

.related-project-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 30px 60px rgba(0,0,0,0.2);
}

.related-image {
    height: 250px;
    overflow: hidden;
}

.related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.5s ease;
    filter: brightness(0.95);
}

.related-project-card:hover .related-image img {
    filter: brightness(1.1);
    transform: scale(1.05);
}

.related-content {
    padding: 35px;
    position: relative;
}

.related-category {
    color: #667eea;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 15px;
    letter-spacing: 1px;
}

.related-content h5 {
    color: #2c3e50;
    margin-bottom: 15px;
    font-weight: 700;
    font-size: 1.4rem;
    transition: color 0.3s ease;
}

.related-project-card:hover .related-content h5 {
    color: #667eea;
}

.related-content p {
    color: #6c757d;
    font-size: 1rem;
    margin-bottom: 20px;
    line-height: 1.6;
}

/* Modern Responsive Design */
@media (max-width: 1200px) {
    .project-title {
        font-size: 3.5rem;
    }
    
    .cta-content h2 {
        font-size: 3rem;
    }
    
    .related-projects .row {
        gap: 30px;
    }
}

@media (max-width: 992px) {
    .project-hero {
        padding: 80px 0;
    }
    
    .project-title {
        font-size: 3rem;
    }
    
    .project-sidebar {
        padding-left: 0;
        margin-top: 50px;
    }
    
    .gallery-grid .col-lg-4 {
        margin-bottom: 20px;
    }
}

@media (max-width: 768px) {
    .project-hero {
        padding: 60px 0;
    }
    
    .project-title {
        font-size: 2.5rem;
        line-height: 1.2;
    }
    
    .project-summary {
        font-size: 1.1rem;
    }
    
    .project-meta {
        flex-direction: column;
        gap: 15px;
    }
    
    .project-actions {
        flex-direction: column;
        gap: 15px;
        justify-content: center;
    }
    
    .project-actions .btn {
        width: 100%;
        text-align: center;
    }
    
    .project-details {
        padding: 60px 0;
    }
    
    .project-content h2 {
        font-size: 2rem;
    }
    
    .project-content h3 {
        font-size: 1.5rem;
    }
    
    .sidebar-section {
        padding: 30px;
        margin-bottom: 30px;
    }
    
    .gallery-item img {
        height: 200px;
    }
    
    .cta-content h2 {
        font-size: 2.5rem;
    }
    
    .cta-content p {
        font-size: 1.1rem;
    }
    
    .btn-cta {
        padding: 15px 35px;
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .project-hero {
        padding: 40px 0;
    }
    
    .project-title {
        font-size: 2rem;
    }
    
    .breadcrumb {
        font-size: 0.9rem;
        padding: 10px 20px;
    }
    
    .project-category {
        font-size: 0.9rem;
    }
    
    .content-text {
        padding: 20px;
        font-size: 1rem;
    }
    
    .sidebar-section {
        padding: 25px;
    }
    
    .tech-badge {
        font-size: 0.8rem;
        padding: 8px 14px;
    }
    
    .cta-content h2 {
        font-size: 2rem;
    }
    
    .btn-cta {
        padding: 12px 25px;
        font-size: 0.9rem;
    }
    
    .related-content {
        padding: 25px;
    }
    
    .meta-item {
        font-size: 0.9rem;
        padding: 10px 16px;
    }
}
</style>

<script>
// Modern Gallery Modal with Enhanced Features
document.addEventListener('DOMContentLoaded', function() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    const galleryModal = document.getElementById('galleryModal');
    const carousel = document.getElementById('galleryCarousel');
    
    if (galleryItems.length > 0 && galleryModal && carousel) {
        galleryItems.forEach(item => {
            item.addEventListener('click', function() {
                const slideIndex = parseInt(this.getAttribute('data-slide'));
                const carouselInstance = new bootstrap.Carousel(carousel);
                carouselInstance.to(slideIndex);
                
                // Add fade in animation
                galleryModal.style.opacity = '0';
                setTimeout(() => {
                    galleryModal.style.opacity = '1';
                    galleryModal.style.transition = 'opacity 0.3s ease';
                }, 10);
            });
        });
    }
    
    // Enhanced modal interactions
    if (galleryModal) {
        galleryModal.addEventListener('hidden.bs.modal', function() {
            document.body.style.overflow = 'auto';
        });
        
        galleryModal.addEventListener('shown.bs.modal', function() {
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && galleryModal && galleryModal.classList.contains('show')) {
            bootstrap.Modal.getInstance(galleryModal).hide();
        }
    });
    
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Animate elements on scroll
    const animateElements = document.querySelectorAll('.gallery-item, .sidebar-section, .related-project-card, .content-text');
    
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(el);
    });
    
    // Parallax effect for hero section
    const hero = document.querySelector('.project-hero');
    if (hero) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.2;
            hero.style.transform = `translateY(${rate}px)`;
        });
    }
    
    // Enhanced hover effects for gallery items
    galleryItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.boxShadow = '0 25px 50px rgba(0,0,0,0.2)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
        });
    });
    
    // Tech badges animation
    const techBadges = document.querySelectorAll('.tech-badge');
    techBadges.forEach((badge, index) => {
        badge.style.animationDelay = `${index * 0.1}s`;
        badge.classList.add('fadeInUp');
    });
    
    // Stagger animation for related projects
    const relatedCards = document.querySelectorAll('.related-project-card');
    relatedCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 200);
    });
    
    // Loading animation for images
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.3s ease';
        
        img.onload = function() {
            this.style.opacity = '1';
        };
        
        if (img.complete) {
            img.style.opacity = '1';
        }
    });
});

// Smooth scrolling with easing
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add CSS animations
const dynamicStyle = document.createElement('style');
dynamicStyle.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    .tech-badge {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
    }
    
    .gallery-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    
    .related-project-card {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
`;
document.head.appendChild(dynamicStyle);
</script>

<?php require_once 'includes/footer.php'; ?>