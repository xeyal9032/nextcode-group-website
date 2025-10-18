<?php
// NextCode Group - Proje İçeriği ile Veritabanı Senkronizasyonu
define('SECURE_ACCESS', true);

require_once 'config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    echo "=== NextCode Group - Proje İçeriği Senkronizasyonu ===\n\n";
    
    // 1. Blog Kategorilerini Güncelle
    echo "1. Blog Kategorilerini Güncelleme...\n";
    $blogCategories = [
        ['name' => 'SEO', 'slug' => 'seo', 'description' => 'Axtarış sistemlərində optimizasiya və SEO strategiyaları'],
        ['name' => 'Veb İnkişaf', 'slug' => 'veb-inkisaf', 'description' => 'Müasir veb inkişaf texnologiyaları və trendlər'],
        ['name' => 'E-ticarət', 'slug' => 'e-ticaret', 'description' => 'Onlayn mağaza yaratma və e-ticarət həlləri'],
        ['name' => 'Kibertəhlükəsizlik', 'slug' => 'kibertehlukesizlik', 'description' => 'Veb təhlükəsizliyi və kibertəhlükəsizlik təcrübələri'],
        ['name' => 'Marketinq', 'slug' => 'marketinq', 'description' => 'Rəqəmsal marketinq və sosial media strategiyaları']
    ];
    
    foreach ($blogCategories as $category) {
        $stmt = $pdo->prepare("
            INSERT INTO blog_categories (name, slug, description) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            description = VALUES(description)
        ");
        $stmt->execute([$category['name'], $category['slug'], $category['description']]);
        echo "✓ Kategori: {$category['name']}\n";
    }
    
    // 2. Blog Yazılarını Güncelle
    echo "\n2. Blog Yazılarını Güncelleme...\n";
    $blogPosts = [
        [
            'id' => 1,
            'title' => 'SEO Optimizasiyası: Axtarış Nəticələrində Yüksəlmək',
            'slug' => 'seo-optimizasiyasi',
            'excerpt' => 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün hərtərəfli SEO strategiyaları, texnikaları və 2024 trendləri.',
            'featured_image' => 'images/blog/seo-optimization.jpg',
            'category_id' => 1,
            'status' => 'published',
            'featured' => 1
        ],
        [
            'id' => 2,
            'title' => 'Müasir Veb İnkişaf Trendləri 2024',
            'slug' => 'muasir-veb-inkisaf-trendleri-2024',
            'excerpt' => '2024-cü ildə veb inkişaf sahəsindəki ən vacib trendlər: süni intellekt inteqrasiyası, serversiz arxitektura, proqressiv veb tətbiqlər.',
            'featured_image' => 'images/blog/web-development-trends.jpg',
            'category_id' => 2,
            'status' => 'published',
            'featured' => 1
        ],
        [
            'id' => 3,
            'title' => 'E-ticarət İnkişafı: Online Mağaza Yaratmaq',
            'slug' => 'e-ticaret-inkisafi-online-magaza-yaratmaq',
            'excerpt' => 'E-ticarət platforması yaratmaq üçün hərtərəfli təlimat: platform seçimi, ödəniş inteqrasiyası, mobil optimizasiya və performans.',
            'featured_image' => 'images/blog/ecommerce-development.jpg',
            'category_id' => 3,
            'status' => 'published',
            'featured' => 0
        ],
        [
            'id' => 4,
            'title' => 'Kibertəhlükəsizlik: Veb Təhlükəsizliyi və Ən Yaxşı Təcrübələr',
            'slug' => 'kibertehlukesizlik-veb-tehlukesizliyi',
            'excerpt' => 'Veb təhlükəsizliyinin hərtərəfli təlimatı: təhlükələr, OWASP Top 10, təhlükəsizlik tətbiqi və ən yaxşı təcrübələr.',
            'featured_image' => 'images/blog/cybersecurity-guide.jpg',
            'category_id' => 4,
            'status' => 'published',
            'featured' => 0
        ],
        [
            'id' => 5,
            'title' => 'Kiçik Biznes üçün Rəqəmsal Marketinq Strategiyaları',
            'slug' => 'kicik-biznes-ucun-reqemsal-marketinq-strategiyalari',
            'excerpt' => 'Kiçik bizneslər üçün effektiv rəqəmsal marketinq strategiyaları və sosial media marketinqi haqqında praktik məsləhətlər.',
            'featured_image' => 'images/blog/small-business-marketing.jpg',
            'category_id' => 5,
            'status' => 'published',
            'featured' => 0
        ]
    ];
    
    foreach ($blogPosts as $post) {
        $stmt = $pdo->prepare("
            INSERT INTO blog_posts (id, title, slug, excerpt, featured_image, category_id, status, is_featured, published_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            title = VALUES(title),
            slug = VALUES(slug),
            excerpt = VALUES(excerpt),
            featured_image = VALUES(featured_image),
            category_id = VALUES(category_id),
            status = VALUES(status),
            is_featured = VALUES(is_featured),
            updated_at = NOW()
        ");
        $stmt->execute([
            $post['id'], $post['title'], $post['slug'], $post['excerpt'], 
            $post['featured_image'], $post['category_id'], $post['status'], $post['featured']
        ]);
        echo "✓ Blog yazısı: {$post['title']}\n";
    }
    
    // 3. Portfolio Kategorilerini Güncelle
    echo "\n3. Portfolio Kategorilerini Güncelleme...\n";
    $portfolioCategories = [
        ['name' => 'Web Development', 'slug' => 'web-development', 'description' => 'Modern web applications and websites', 'color' => '#2563eb', 'icon' => 'fas fa-code'],
        ['name' => 'Mobile Apps', 'slug' => 'mobile-apps', 'description' => 'iOS and Android mobile applications', 'color' => '#059669', 'icon' => 'fas fa-mobile-alt'],
        ['name' => 'E-commerce', 'slug' => 'e-commerce', 'description' => 'Online stores and shopping platforms', 'color' => '#dc2626', 'icon' => 'fas fa-shopping-cart'],
        ['name' => 'SEO & Marketing', 'slug' => 'seo-marketing', 'description' => 'Search engine optimization and digital marketing', 'color' => '#7c3aed', 'icon' => 'fas fa-chart-line'],
        ['name' => 'UI/UX Design', 'slug' => 'ui-ux-design', 'description' => 'User interface and user experience design', 'color' => '#ea580c', 'icon' => 'fas fa-paint-brush'],
        ['name' => 'Corporate Websites', 'slug' => 'corporate-websites', 'description' => 'Business and corporate web solutions', 'color' => '#0891b2', 'icon' => 'fas fa-building']
    ];
    
    foreach ($portfolioCategories as $category) {
        $stmt = $pdo->prepare("
            INSERT INTO portfolio_categories (name, slug, description) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            description = VALUES(description)
        ");
        $stmt->execute([$category['name'], $category['slug'], $category['description']]);
        echo "✓ Portfolio kategorisi: {$category['name']}\n";
    }
    
    // 4. Portfolio Projelerini Güncelle
    echo "\n4. Portfolio Projelerini Güncelleme...\n";
    $portfolioProjects = [
        [
            'title' => 'Kosmetika Mağazası E-commerce Platform',
            'slug' => 'kosmetika-magazasi-ecommerce',
            'short_description' => 'Modern və funksional kosmetika mağazası üçün e-commerce platforması',
            'description' => 'Müştəri üçün tam funksional e-commerce platforması hazırladıq. Platform müasir dizayn, asan naviqasiya və güclü admin paneli ilə təchiz edilib.',
            'category_id' => 3,
            'featured_image' => 'images/portfolio/ecommerce-project.jpg',
            'technologies' => 'PHP, MySQL, JavaScript, Bootstrap, PayPal API',
            'client_name' => 'Beauty Store LLC',
            'project_date' => '2024-01-15',
            'is_featured' => 1,
            'is_published' => 1
        ],
        [
            'title' => 'Mobil Oyun Tətbiqi',
            'slug' => 'mobil-oyun-tetbiqi',
            'short_description' => 'iOS və Android üçün əyləncəli puzzle oyunu',
            'description' => 'Yaradıcı puzzle oyunu hazırladıq. Oyun müxtəlif səviyyələr, achievement sistemi və sosial paylaşım funksiyaları ilə təchiz edilib.',
            'category_id' => 2,
            'featured_image' => 'images/portfolio/mobile-app.jpg',
            'technologies' => 'React Native, Firebase, Redux, Admob',
            'client_name' => 'GameDev Studio',
            'project_date' => '2024-02-20',
            'is_featured' => 1,
            'is_published' => 1
        ],
        [
            'title' => 'Hüquq Firması Korporativ Saytı',
            'slug' => 'huquq-firmasi-korporativ-sayti',
            'short_description' => 'Profesional hüquq firması üçün korporativ web saytı',
            'description' => 'Hüquq firması üçün etibarlı və professional korporativ sayt hazırladıq. Sayt müştəri etimadını artırmaq üçün dizayn edilib.',
            'category_id' => 6,
            'featured_image' => 'images/portfolio/corporate-website.jpg',
            'technologies' => 'WordPress, PHP, MySQL, Custom Theme',
            'client_name' => 'Legal Partners LLC',
            'project_date' => '2024-03-10',
            'is_featured' => 0,
            'is_published' => 1
        ]
    ];
    
    foreach ($portfolioProjects as $project) {
        $stmt = $pdo->prepare("
            INSERT INTO portfolio_projects (title, slug, short_description, description, category_id, featured_image, technologies, client_name, featured, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed')
            ON DUPLICATE KEY UPDATE 
            title = VALUES(title),
            short_description = VALUES(short_description),
            description = VALUES(description),
            category_id = VALUES(category_id),
            featured_image = VALUES(featured_image),
            technologies = VALUES(technologies),
            client_name = VALUES(client_name),
            featured = VALUES(featured),
            status = 'completed',
            updated_at = NOW()
        ");
        $stmt->execute([
            $project['title'], $project['slug'], $project['short_description'], $project['description'],
            $project['category_id'], $project['featured_image'], $project['technologies'], 
            $project['client_name'], $project['is_featured']
        ]);
        echo "✓ Portfolio projesi: {$project['title']}\n";
    }
    
    // 5. Servisleri Güncelle
    echo "\n5. Servisleri Güncelleme...\n";
    $services = [
        [
            'title' => 'SEO Optimizasiyası',
            'description' => 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün hərtərəfli SEO xidmətləri',
            'icon' => 'fas fa-search',
            'price' => 'Başlanğıc: 500₼',
            'features' => 'Keywor araşdırması, Texniki SEO, Məzmun optimizasiyası, Link building, Rəqib analizi'
        ],
        [
            'title' => 'Veb İnkişaf',
            'description' => 'Müasir texnologiyalar ilə responsive və performanslı veb saytlar',
            'icon' => 'fas fa-code',
            'price' => 'Başlanğıc: 800₼',
            'features' => 'Responsive dizayn, CMS inteqrasiyası, E-ticarət həlləri, Performans optimizasiyası'
        ],
        [
            'title' => 'E-ticarət Platforması',
            'description' => 'Tam funksional onlayn mağaza və e-ticarət həlləri',
            'icon' => 'fas fa-shopping-cart',
            'price' => 'Başlanğıc: 1200₼',
            'features' => 'Məhsul idarəetməsi, Ödəniş inteqrasiyası, Sifariş izləmə, Admin paneli'
        ],
        [
            'title' => 'Kibertəhlükəsizlik',
            'description' => 'Veb saytlarınızın təhlükəsizliyini təmin edən xidmətlər',
            'icon' => 'fas fa-shield-alt',
            'price' => 'Başlanğıc: 600₼',
            'features' => 'Təhlükəsizlik audit, SSL sertifikatı, Firewall konfiqurasiyası, Monitorinq'
        ],
        [
            'title' => 'Rəqəmsal Marketinq',
            'description' => 'Sosial media və rəqəmsal marketinq strategiyaları',
            'icon' => 'fas fa-chart-line',
            'price' => 'Başlanğıc: 400₼',
            'features' => 'Sosial media idarəetməsi, Məzmun yaradılması, Reklam kampaniyaları, Analitika'
        ]
    ];
    
    foreach ($services as $index => $service) {
        $stmt = $pdo->prepare("
            INSERT INTO services (title, description, icon, is_active, order_index) 
            VALUES (?, ?, ?, 1, ?)
            ON DUPLICATE KEY UPDATE 
            title = VALUES(title),
            description = VALUES(description),
            icon = VALUES(icon),
            is_active = 1,
            order_index = VALUES(order_index),
            updated_at = NOW()
        ");
        $stmt->execute([$service['title'], $service['description'], $service['icon'], $index + 1]);
        echo "✓ Servis: {$service['title']}\n";
    }
    
    // 6. Site İçeriğini Güncelle
    echo "\n6. Site İçeriğini Güncelleme...\n";
    $siteContent = [
        ['content_key' => 'site_title', 'content_value' => 'NextCode Group', 'content_type' => 'text', 'page_section' => 'general'],
        ['content_key' => 'site_description', 'content_value' => 'Professional solutions for your success in the digital world.', 'content_type' => 'text', 'page_section' => 'general'],
        ['content_key' => 'contact_email', 'content_value' => 'info@nextcode.com', 'content_type' => 'text', 'page_section' => 'contact'],
        ['content_key' => 'contact_phone', 'content_value' => '+380 97 258 00 00', 'content_type' => 'text', 'page_section' => 'contact'],
        ['content_key' => 'contact_address', 'content_value' => 'Bakı şəhəri, Nəsimi rayonu, 28 May küçəsi 15', 'content_type' => 'text', 'page_section' => 'contact'],
        ['content_key' => 'hero_title', 'content_value' => 'Digital Dünyada Uğurunuz üçün Professional Həllər', 'content_type' => 'text', 'page_section' => 'home'],
        ['content_key' => 'hero_subtitle', 'content_value' => 'Veb inkişaf, SEO, e-ticarət və rəqəmsal marketinq xidmətləri ilə biznesinizi növbəti səviyyəyə aparırıq.', 'content_type' => 'text', 'page_section' => 'home']
    ];
    
    foreach ($siteContent as $content) {
        $stmt = $pdo->prepare("
            INSERT INTO site_content (content_key, content_value, content_type, page_section, is_active) 
            VALUES (?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE 
            content_value = VALUES(content_value),
            content_type = VALUES(content_type),
            page_section = VALUES(page_section),
            is_active = 1,
            updated_at = NOW()
        ");
        $stmt->execute([$content['content_key'], $content['content_value'], $content['content_type'], $content['page_section']]);
        echo "✓ Site içeriği: {$content['content_key']}\n";
    }
    
    echo "\n=== Senkronizasyon Tamamlandı ===\n";
    echo "✓ Blog kategorileri güncellendi\n";
    echo "✓ Blog yazıları güncellendi\n";
    echo "✓ Portfolio kategorileri güncellendi\n";
    echo "✓ Portfolio projeleri güncellendi\n";
    echo "✓ Servisler güncellendi\n";
    echo "✓ Site içeriği güncellendi\n\n";
    
    echo "Veritabanı artık proje içeriğiyle tam uyumlu!\n";
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage() . "\n";
}
?>
