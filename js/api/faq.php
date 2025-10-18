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
        CREATE TABLE IF NOT EXISTS faq_categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            icon_class TEXT,
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $conn->exec('
        CREATE TABLE IF NOT EXISTS faq_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER,
            question TEXT NOT NULL,
            answer TEXT NOT NULL,
            is_featured INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            views INTEGER DEFAULT 0,
            helpful_votes INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES faq_categories(id) ON DELETE SET NULL
        )
    ');
    
    // Insert sample data if tables are empty
    $stmt = $conn->query('SELECT COUNT(*) as count FROM faq_categories');
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        // Insert sample FAQ categories
        $conn->exec("INSERT INTO faq_categories (name, slug, description, icon_class, sort_order) VALUES 
            ('Ümumi Suallar', 'general', 'Şirkətimiz və xidmətlərimiz haqqında ümumi məlumatlar', 'fas fa-info-circle', 1),
            ('Qiymətləndirmə', 'pricing', 'Qiymətlər və ödəniş şərtləri ilə bağlı suallar', 'fas fa-dollar-sign', 2),
            ('Texniki Dəstək', 'technical', 'Texniki məsələlər və dəstək xidmətləri', 'fas fa-cogs', 3),
            ('Layihə Prosesi', 'project-process', 'Layihə inkişafı və iş prosesi haqqında', 'fas fa-project-diagram', 4),
            ('Hosting və Domen', 'hosting', 'Hosting və domen xidmətləri ilə bağlı', 'fas fa-server', 5),
            ('Təhlükəsizlik', 'security', 'Məlumat təhlükəsizliği və gizlilik', 'fas fa-shield-alt', 6)
        ");
        
        // Insert sample FAQ items
        $conn->exec("INSERT INTO faq_items (category_id, question, answer, is_featured, sort_order) VALUES 
            (1, 'NextCode Group nə vaxtdan fəaliyyət göstərir?', 'NextCode Group 2016-cı ildən etibarən Azərbaycanda texnologiya sahəsində fəaliyyət göstərir. 8+ illik təcrübəmizlə 150+ uğurlu layihə həyata keçirmişik.', 1, 1),
            (1, 'Hansı texnologiyalarla işləyirsiniz?', 'Biz müasir texnologiyalarla işləyirik: React, Vue.js, Node.js, Python, PHP, Laravel, AI/ML, Blockchain, Mobile (React Native, Flutter) və s. Həmişə ən son texnologiyaları izləyirik.', 1, 2),
            (1, 'Xarici müştərilərlə işləyirsinizmi?', 'Bəli, biz həm yerli, həm də beynəlxalq müştərilərlə işləyirik. Komandamızda ingilis dilini mükəmməl bilən mütəxəssislər var.', 0, 3),
            (1, 'Layihə üçün nə qədər vaxt lazımdır?', 'Layihənin mürəkkəbliyindən asılı olaraq 2 həftədən 6 aya qədər vaxt tələb oluna bilər. Dəqiq müddəti layihənin təfərrüatlarını öyrəndikdən sonra bildiririk.', 1, 4),
            
            (2, 'Qiymətlər necə müəyyən edilir?', 'Qiymətlər layihənin mürəkkəbliyi, funksionallığı, dizayn tələbləri və müddətinə görə müəyyən edilir. Hər layihə üçün fərdi qiymət təklifi hazırlayırıq.', 1, 1),
            (2, 'Ödəniş şərtləri necədir?', 'Adətən layihənin 50%-i əvvəlcədən, qalan hissəsi isə layihə tamamlandıqdan sonra ödənilir. Böyük layihələr üçün hissə-hissə ödəniş mümkündür.', 0, 2),
            (2, 'Hansı valyutalarla ödəniş qəbul edirsiniz?', 'AZN, USD və EUR valyutalarında ödəniş qəbul edirik. Bank köçürməsi, kart ödənişi və digər üsullar mövcuddur.', 0, 3),
            (2, 'Endirim imkanları varmı?', 'Uzunmüddətli əməkdaşlıq, böyük layihələr və qeyri-kommersiya təşkilatları üçün endirim imkanları mövcuddur.', 0, 4),
            
            (3, 'Texniki dəstək necə təmin edilir?', 'Layihə tamamlandıqdan sonra 3-12 ay texniki dəstək təmin edirik. Dəstək email, telefon və ya uzaqdan bağlantı vasitəsilə həyata keçirilir.', 1, 1),
            (3, 'Saytımda problem yaranarsa nə etməliyəm?', 'Dərhal bizə müraciət edin. Kritik problemləri 24 saat ərzində, digər məsələləri isə 48 saat ərzində həll edirik.', 1, 2),
            (3, 'Saytımı özüm yeniləyə bilərəmmi?', 'Bəli, CMS (məzmun idarəetmə sistemi) ilə hazırladığımız saytları özünüz asanlıqla yeniləyə bilərsiniz. Lazım olduqda təlim də veririk.', 0, 3),
            (3, 'Backup (ehtiyat nüsxə) xidməti varmı?', 'Bəli, bütün layihələr üçün avtomatik backup sistemi qururuq. Məlumatlarınız təhlükəsizdir.', 0, 4),
            
            (4, 'Layihə necə başlayır?', 'İlk olaraq sizinlə görüşərək tələblərinizi öyrənirik, sonra texniki sənəd hazırlayırıq və razılaşdıqdan sonra inkişafa başlayırıq.', 1, 1),
            (4, 'Layihə gedişatını necə izləyə bilərəm?', 'Hər həftə progress hesabatı göndəririk və demo versiyalar hazırlayırıq. İstədiyiniz vaxt layihənin vəziyyətini öyrənə bilərsiniz.', 0, 2),
            (4, 'Layihədə dəyişiklik etmək mümkündürmü?', 'Bəli, inkişaf prosesində dəyişikliklər etmək mümkündür. Böyük dəyişikliklər əlavə xərc tələb edə bilər.', 0, 3),
            (4, 'Test prosesi necə gedir?', 'Hər mərhələdə test edirik və sizə demo təqdim edirik. Son mərhələdə isə hərtərəfli test aparırıq.', 0, 4),
            
            (5, 'Hosting xidməti təmin edirsinizmi?', 'Bəli, layihələriniz üçün hosting xidməti təmin edirik. İllik hosting paketi qiymətə daxildir.', 1, 1),
            (5, 'Domen adı necə seçilir?', 'Domen adını sizinlə birlikdə seçirik və qeydiyyatını aparırıq. .az, .com və digər uzantılar mövcuddur.', 0, 2),
            (5, 'SSL sertifikatı daxildirmi?', 'Bəli, bütün layihələrdə SSL sertifikatı pulsuz təmin edilir. Saytınız təhlükəsiz olacaq.', 0, 3),
            (5, 'Hosting yüksəltmək mümkündürmü?', 'Bəli, saytınızın böyüməsi ilə hosting paketini yüksəltmək mümkündür.', 0, 4),
            
            (6, 'Məlumatlarımın təhlükəsizliyi necə təmin edilir?', 'Bütün məlumatlar şifrələnir və təhlükəsiz serverlərdə saxlanılır. GDPR və digər beynəlxalq standartlara uyğun işləyirik.', 1, 1),
            (6, 'Backup nə qədər müddətə saxlanılır?', 'Backup məlumatları minimum 1 il saxlanılır. İstəyə görə daha uzun müddət də mümkündür.', 0, 2),
            (6, 'Məlumat itkisi halında nə olur?', 'Backup sistemimiz sayəsində məlumatları bərpa etmək mümkündür. Məlumat itkisi riski minimuma endirilmişdir.', 0, 3),
            (6, 'Gizlilik sazişi imzalanırmi?', 'Bəli, bütün layihələr üçün gizlilik sazişi (NDA) imzalanır. Məlumatlarınız tam qorunur.', 0, 4)
        ");
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? 'all';
        $category = $_GET['category'] ?? '';
        
        switch ($action) {
            case 'categories':
                $stmt = $conn->prepare("SELECT c.*, COUNT(f.id) as faq_count FROM faq_categories c LEFT JOIN faq_items f ON c.id = f.category_id AND f.is_active = 1 WHERE c.is_active = 1 GROUP BY c.id ORDER BY c.sort_order ASC");
                $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $categories
                ]);
                break;
                
            case 'items':
                $sql = "SELECT f.*, c.name as category_name, c.slug as category_slug FROM faq_items f LEFT JOIN faq_categories c ON f.category_id = c.id WHERE f.is_active = 1";
                $params = [];
                
                if ($category && $category !== 'all') {
                    $sql .= " AND c.slug = ?";
                    $params[] = $category;
                }
                
                $sql .= " ORDER BY f.sort_order ASC";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute($params);
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $items
                ]);
                break;
                
            case 'featured':
                $stmt = $conn->prepare("SELECT f.*, c.name as category_name, c.slug as category_slug FROM faq_items f LEFT JOIN faq_categories c ON f.category_id = c.id WHERE f.is_active = 1 AND f.is_featured = 1 ORDER BY f.sort_order ASC");
                $stmt->execute();
                $featured = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $featured
                ]);
                break;
                
            case 'search':
                $query = $_GET['q'] ?? '';
                if (empty($query)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Axtarış sorğusu boş ola bilməz.'
                    ]);
                    break;
                }
                
                $stmt = $conn->prepare("SELECT f.*, c.name as category_name, c.slug as category_slug FROM faq_items f LEFT JOIN faq_categories c ON f.category_id = c.id WHERE f.is_active = 1 AND (f.question LIKE ? OR f.answer LIKE ?) ORDER BY f.sort_order ASC");
                $searchTerm = '%' . $query . '%';
                $stmt->execute([$searchTerm, $searchTerm]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $results,
                    'query' => $query,
                    'count' => count($results)
                ]);
                break;
                
            default:
                // Return all data
                $stmt = $conn->prepare("SELECT c.*, COUNT(f.id) as faq_count FROM faq_categories c LEFT JOIN faq_items f ON c.id = f.category_id AND f.is_active = 1 WHERE c.is_active = 1 GROUP BY c.id ORDER BY c.sort_order ASC");
                $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $sql = "SELECT f.*, c.name as category_name, c.slug as category_slug FROM faq_items f LEFT JOIN faq_categories c ON f.category_id = c.id WHERE f.is_active = 1";
                $params = [];
                
                if ($category && $category !== 'all') {
                    $sql .= " AND c.slug = ?";
                    $params[] = $category;
                }
                
                $sql .= " ORDER BY f.sort_order ASC";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute($params);
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'categories' => $categories,
                        'items' => $items
                    ]
                ]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        switch ($action) {
            case 'vote_helpful':
                $faq_id = (int)($input['faq_id'] ?? 0);
                
                if (!$faq_id) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'FAQ ID tələb olunur.'
                    ]);
                    break;
                }
                
                $stmt = $conn->prepare("UPDATE faq_items SET helpful_votes = helpful_votes + 1 WHERE id = ? AND is_active = 1");
                if ($stmt->execute([$faq_id])) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Rəyiniz qeydə alındı.'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Xəta baş verdi.'
                    ]);
                }
                break;
                
            case 'increment_view':
                $faq_id = (int)($input['faq_id'] ?? 0);
                
                if (!$faq_id) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'FAQ ID tələb olunur.'
                    ]);
                    break;
                }
                
                $stmt = $conn->prepare("UPDATE faq_items SET views = views + 1 WHERE id = ? AND is_active = 1");
                $stmt->execute([$faq_id]);
                
                echo json_encode([
                    'success' => true
                ]);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Bilinməyən əməliyyat.'
                ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Yalnız GET və POST metodları dəstəklənir.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Sistem xətası: ' . $e->getMessage()
    ]);
}
?>