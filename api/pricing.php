<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Create tables if they don't exist
    $conn->exec('
        CREATE TABLE IF NOT EXISTS pricing_packages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            currency TEXT DEFAULT "AZN",
            billing_period TEXT DEFAULT "monthly",
            discount_percentage INTEGER DEFAULT 0,
            original_price DECIMAL(10,2),
            is_popular INTEGER DEFAULT 0,
            is_featured INTEGER DEFAULT 0,
            active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            button_text TEXT DEFAULT "Seç",
            button_link TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $conn->exec('
        CREATE TABLE IF NOT EXISTS package_features (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            package_id INTEGER,
            feature_name TEXT NOT NULL,
            feature_description TEXT,
            feature_value TEXT,
            is_included INTEGER DEFAULT 1,
            is_highlighted INTEGER DEFAULT 0,
            active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (package_id) REFERENCES pricing_packages(id) ON DELETE CASCADE
        )
    ');
    
    $conn->exec('
        CREATE TABLE IF NOT EXISTS pricing_testimonials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            client_name TEXT NOT NULL,
            client_position TEXT,
            client_company TEXT,
            client_image TEXT,
            testimonial_text TEXT NOT NULL,
            rating INTEGER DEFAULT 5,
            package_id INTEGER,
            is_featured INTEGER DEFAULT 0,
            active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (package_id) REFERENCES pricing_packages(id) ON DELETE SET NULL
        )
    ');
    
    // Insert sample data if tables are empty
    $stmt = $conn->query('SELECT COUNT(*) as count FROM pricing_packages');
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        // Insert sample pricing packages
        $conn->exec("INSERT INTO pricing_packages (name, slug, description, price, original_price, discount_percentage, billing_period, is_popular, is_featured, button_text, button_link, sort_order) VALUES 
            ('Başlanğıc', 'starter', 'Kiçik biznes və startaplar üçün ideal həll', 299.00, 399.00, 25, 'monthly', 0, 0, 'İndi Başla', '/contact', 1),
            ('Peşəkar', 'professional', 'Orta ölçülü şirkətlər üçün ən populyar seçim', 599.00, 799.00, 25, 'monthly', 1, 1, 'Seç', '/contact', 2),
            ('Müəssisə', 'enterprise', 'Böyük şirkətlər üçün tam həll', 1299.00, 1599.00, 19, 'monthly', 0, 0, 'Əlaqə Saxla', '/contact', 3),
            ('Fərdi', 'custom', 'Sizin ehtiyaclarınıza uyğun fərdi həll', 0.00, 0.00, 0, 'custom', 0, 0, 'Təklif Al', '/contact', 4)
        ");
        
        // Insert features for Starter package
        $conn->exec("INSERT INTO package_features (package_id, feature_name, feature_description, feature_value, is_included, is_highlighted, sort_order) VALUES 
            (1, 'Web Sayt', 'Responsive və modern dizayn', '5 səhifə', 1, 1, 1),
            (1, 'Mobil Uyğunluq', 'Bütün cihazlarda mükəmməl görünüş', 'Tam dəstək', 1, 0, 2),
            (1, 'SEO Optimallaşdırması', 'Axtarış mühərriklərində yüksək reytinq', 'Əsas SEO', 1, 0, 3),
            (1, 'SSL Sertifikatı', 'Təhlükəsiz bağlantı', 'Daxildir', 1, 0, 4),
            (1, 'Texniki Dəstək', 'Email dəstəyi', '3 ay', 1, 0, 5),
            (1, 'Hosting', 'İllik hosting xidməti', '1 il', 1, 0, 6),
            (1, 'CMS', 'Məzmun idarəetmə sistemi', 'Yoxdur', 0, 0, 7),
            (1, 'E-commerce', 'Onlayn mağaza funksiyası', 'Yoxdur', 0, 0, 8)
        ");
        
        // Insert features for Professional package
        $conn->exec("INSERT INTO package_features (package_id, feature_name, feature_description, feature_value, is_included, is_highlighted, sort_order) VALUES 
            (2, 'Web Sayt', 'Responsive və modern dizayn', '15 səhifə', 1, 1, 1),
            (2, 'Mobil Uyğunluq', 'Bütün cihazlarda mükəmməl görünüş', 'Tam dəstək', 1, 0, 2),
            (2, 'SEO Optimallaşdırması', 'Axtarış mühərriklərində yüksək reytinq', 'Qabaqcıl SEO', 1, 1, 3),
            (2, 'SSL Sertifikatı', 'Təhlükəsiz bağlantı', 'Daxildir', 1, 0, 4),
            (2, 'Texniki Dəstək', 'Email və telefon dəstəyi', '6 ay', 1, 0, 5),
            (2, 'Hosting', 'İllik hosting xidməti', '1 il', 1, 0, 6),
            (2, 'CMS', 'Məzmun idarəetmə sistemi', 'WordPress', 1, 1, 7),
            (2, 'E-commerce', 'Onlayn mağaza funksiyası', 'Əsas', 1, 0, 8),
            (2, 'Analitika', 'Google Analytics inteqrasiyası', 'Daxildir', 1, 0, 9),
            (2, 'Sosial Media', 'Sosial şəbəkə inteqrasiyası', 'Daxildir', 1, 0, 10)
        ");
        
        // Insert features for Enterprise package
        $conn->exec("INSERT INTO package_features (package_id, feature_name, feature_description, feature_value, is_included, is_highlighted, sort_order) VALUES 
            (3, 'Web Sayt', 'Responsive və modern dizayn', 'Limitsiz', 1, 1, 1),
            (3, 'Mobil Uyğunluq', 'Bütün cihazlarda mükəmməl görünüş', 'Tam dəstək', 1, 0, 2),
            (3, 'SEO Optimallaşdırması', 'Axtarış mühərriklərində yüksək reytinq', 'Premium SEO', 1, 1, 3),
            (3, 'SSL Sertifikatı', 'Təhlükəsiz bağlantı', 'Daxildir', 1, 0, 4),
            (3, 'Texniki Dəstək', '24/7 dəstək', '1 il', 1, 1, 5),
            (3, 'Hosting', 'Premium hosting xidməti', '1 il', 1, 0, 6),
            (3, 'CMS', 'Fərdi CMS həlli', 'Custom', 1, 1, 7),
            (3, 'E-commerce', 'Tam e-commerce həlli', 'Premium', 1, 1, 8),
            (3, 'Analitika', 'Qabaqcıl analitika', 'Premium', 1, 0, 9),
            (3, 'Sosial Media', 'Sosial şəbəkə inteqrasiyası', 'Daxildir', 1, 0, 10),
            (3, 'API İnteqrasiya', 'Üçüncü tərəf API-lər', 'Limitsiz', 1, 1, 11),
            (3, 'Təhlükəsizlik', 'Qabaqcıl təhlükəsizlik', 'Premium', 1, 0, 12)
        ");
        
        // Insert features for Custom package
        $conn->exec("INSERT INTO package_features (package_id, feature_name, feature_description, feature_value, is_included, is_highlighted, sort_order) VALUES 
            (4, 'Fərdi Dizayn', 'Sizin brendinizə uyğun unikal dizayn', 'Tam fərdi', 1, 1, 1),
            (4, 'Fərdi Funksionallıq', 'Ehtiyaclarınıza uyğun xüsusi funksiyalar', 'Tam fərdi', 1, 1, 2),
            (4, 'Məsləhət Xidməti', 'Texnologiya məsləhətçiliyi', 'Daxildir', 1, 0, 3),
            (4, 'Layihə İdarəetməsi', 'Peşəkar layihə idarəetməsi', 'Daxildir', 1, 0, 4),
            (4, 'Texniki Dəstək', 'Fərdi dəstək planı', 'Fərdi', 1, 1, 5),
            (4, 'Təlim', 'Komanda təlimi', 'Daxildir', 1, 0, 6)
        ");
        
        // Insert sample testimonials
        $conn->exec("INSERT INTO pricing_testimonials (client_name, client_position, client_company, client_image, testimonial_text, rating, package_id, is_featured, sort_order) VALUES 
            ('Əli Məmmədov', 'CEO', 'TechStart MMC', '/images/testimonials/ali.jpg', 'NextCode ilə işləmək bizim üçün ən doğru qərar oldu. Peşəkar komanda və keyfiyyətli xidmət.', 5, 2, 1, 1),
            ('Leyla Həsənova', 'Marketing Director', 'Digital Solutions', '/images/testimonials/leyla.jpg', 'Müəssisə paketini seçdik və nəticədən çox məmnunuq. Satışlarımız 40% artdı.', 5, 3, 1, 2),
            ('Rəşad Əliyev', 'Founder', 'StartupAZ', '/images/testimonials/rashad.jpg', 'Başlanğıc paketi startapımız üçün mükəmməl idi. Qiymət-keyfiyyət nisbəti əla!', 5, 1, 1, 3),
            ('Nigar Quliyeva', 'CTO', 'InnovateTech', '/images/testimonials/nigar.jpg', 'Fərdi həll bizim xüsusi ehtiyaclarımızı tam qarşıladı. Tövsiyə edirəm!', 5, 4, 1, 4),
            ('Elvin Məhərrəmov', 'Product Manager', 'E-commerce Pro', '/images/testimonials/elvin.jpg', 'E-commerce funksionallığı mükəmməldir. Müştəri təcrübəsi çox yaxşılaşdı.', 5, 2, 0, 5),
            ('Səbinə Əhmədova', 'Brand Manager', 'Creative Agency', '/images/testimonials/sabina.jpg', 'Dizayn və funksionallıq balansı mükəmməldir. Müştərilərimiz çox məmnundur.', 5, 3, 0, 6)
        ");
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? 'all';
        
        switch ($action) {
            case 'packages':
                $stmt = $conn->prepare("SELECT * FROM pricing_packages WHERE active = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get features for each package
                foreach ($packages as &$package) {
                    $stmt = $conn->prepare("SELECT * FROM package_features WHERE package_id = ? AND active = 1 ORDER BY sort_order ASC");
                    $stmt->execute([$package['id']]);
                    $package['features'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $packages
                ]);
                break;
                
            case 'testimonials':
                $stmt = $conn->prepare("SELECT * FROM pricing_testimonials WHERE active = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $testimonials
                ]);
                break;
                
            case 'featured':
                // Get featured packages and testimonials
                $stmt = $conn->prepare("SELECT * FROM pricing_packages WHERE active = 1 AND is_featured = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $featured_packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $stmt = $conn->prepare("SELECT * FROM pricing_testimonials WHERE active = 1 AND is_featured = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $featured_testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'packages' => $featured_packages,
                        'testimonials' => $featured_testimonials
                    ]
                ]);
                break;
                
            default:
                // Return all data
                $stmt = $conn->prepare("SELECT * FROM pricing_packages WHERE active = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get features for each package
                foreach ($packages as &$package) {
                    $stmt = $conn->prepare("SELECT * FROM package_features WHERE package_id = ? AND active = 1 ORDER BY sort_order ASC");
                    $stmt->execute([$package['id']]);
                    $package['features'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                $stmt = $conn->prepare("SELECT * FROM pricing_testimonials WHERE active = 1 ORDER BY sort_order ASC");
                $stmt->execute();
                $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'packages' => $packages,
                        'testimonials' => $testimonials
                    ]
                ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Yalnız GET metodu dəstəklənir.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Sistem xətası: ' . $e->getMessage()
    ]);
}
?>