<?php
define('SECURE_ACCESS', true);
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Initialize database connection
$pdo = null;
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    error_log('Portfolio API database connection failed: ' . $e->getMessage());
    // Continue without database connection
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'getProjects':
        case 'get_projects':
            getProjects();
            break;
            
        case 'getProject':
        case 'get_project':
            getProject();
            break;
            
        case 'getCategories':
        case 'get_categories':
            getCategories();
            break;
            
        case 'getTechnologies':
        case 'get_technologies':
            getTechnologies();
            break;
            
        case 'searchProjects':
        case 'search_projects':
            searchProjects();
            break;
            
        case 'incrementView':
        case 'increment_view':
            incrementView();
            break;
            
        default:
            // Default action: get projects
            getProjects();
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function getProjects() {
    global $pdo;
    
    $category = $_GET['category'] ?? '';
    $limit = (int)($_GET['limit'] ?? 12);
    $offset = (int)($_GET['offset'] ?? 0);
    $featured_only = isset($_GET['featured']) ? (bool)$_GET['featured'] : false;
    
    try {
        // Check if database is available
        if (!$pdo) {
            // Return fallback data
            $fallbackProjects = getFallbackProjects();
            echo json_encode([
                'success' => true,
                'data' => $fallbackProjects,
                'message' => 'Using fallback data - database not available'
            ]);
            return;
        }
        
        // Check if tables exist
        $tablesExist = checkPortfolioTables();
        if (!$tablesExist) {
            // Return fallback data
            $fallbackProjects = getFallbackProjects();
            echo json_encode([
                'success' => true,
                'data' => $fallbackProjects,
                'message' => 'Using fallback data - portfolio tables not found'
            ]);
            return;
        }
        
        // Ana sorgu - portfolio_projects tablosundan veri çek
        $sql = "SELECT 
                    p.*,
                    c.name as category_name
                FROM portfolio_projects p
                LEFT JOIN portfolio_categories c ON p.category_id = c.id
                WHERE p.is_published = 1 AND p.active = 1";
        
        $params = [];
        
        if ($category) {
            $sql .= " AND c.name = ?";
            $params[] = $category;
        }
        
        if ($featured_only) {
            $sql .= " AND p.is_featured = 1";
        }
        
        $sql .= " ORDER BY p.sort_order ASC, p.created_at DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $projects = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $projects,
            'total' => count($projects)
        ]);
        
    } catch (Exception $e) {
        error_log('Portfolio projects query failed: ' . $e->getMessage());
        // Return fallback data on error
        $fallbackProjects = getFallbackProjects();
        echo json_encode([
            'success' => true,
            'data' => $fallbackProjects,
            'message' => 'Using fallback data due to database error'
        ]);
    }
}

function checkPortfolioTables() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'portfolio_projects'");
        $projectsTable = $stmt->rowCount() > 0;
        
        $stmt = $pdo->query("SHOW TABLES LIKE 'portfolio_categories'");
        $categoriesTable = $stmt->rowCount() > 0;
        
        return $projectsTable && $categoriesTable;
    } catch (Exception $e) {
        return false;
    }
}

function getFallbackProjects() {
    return [
        [
            'id' => 1,
            'title' => 'E-Ticaret Platformu',
            'category_name' => 'E-Ticaret',
            'short_description' => 'Modern ve kullanıcı dostu e-ticaret platformu',
            'featured_image' => 'images/portfolio/ecommerce-project.jpg',
            'demo_url' => 'https://demo.example.com/ecommerce',
            'technologies' => '["PHP", "MySQL", "JavaScript", "Bootstrap", "Stripe API"]'
        ],
        [
            'id' => 2,
            'title' => 'Kurumsal Web Sitesi',
            'category_name' => 'Kurumsal',
            'short_description' => 'Profesyonel kurumsal web sitesi tasarımı',
            'featured_image' => 'images/portfolio/corporate-website.jpg',
            'demo_url' => 'https://demo.example.com/corporate',
            'technologies' => '["HTML5", "CSS3", "JavaScript", "WordPress", "SEO"]'
        ],
        [
            'id' => 3,
            'title' => 'Mobil Uygulama Geliştirme',
            'category_name' => 'Mobil Uygulama',
            'short_description' => 'iOS ve Android için native mobil uygulama',
            'featured_image' => 'images/portfolio/mobile-app.jpg',
            'demo_url' => 'https://demo.example.com/mobile-app',
            'technologies' => '["React Native", "Node.js", "MongoDB", "Firebase", "Redux"]'
        ]
    ];
}

function getProject() {
    global $pdo;
    
    $id = (int)($_GET['id'] ?? 0);
    
    if (!$id) {
        throw new Exception('Proje ID gerekli');
    }
    
    try {
        // Proje detaylarını al
        $stmt = $pdo->prepare("
            SELECT 
                p.*,
                c.name as category_name
            FROM portfolio_projects p
            LEFT JOIN portfolio_categories c ON p.category_id = c.id
            WHERE p.id = ? AND p.is_published = 1 AND p.active = 1
        ");
        $stmt->execute([$id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$project) {
            throw new Exception('Proje bulunamadı');
        }
        
        // Proje görsellerini al
        try {
            $stmt = $pdo->prepare("SELECT * FROM portfolio_images WHERE project_id = ? ORDER BY sort_order");
            $stmt->execute([$id]);
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $images = [];
        }
        
        // Benzer projeleri al
        try {
            $stmt = $pdo->prepare("
                SELECT 
                    p.id, 
                    p.title, 
                    p.featured_image, 
                    p.short_description,
                    c.name as category_name
                FROM portfolio_projects p
                LEFT JOIN portfolio_categories c ON p.category_id = c.id
                WHERE c.name = ? AND p.id != ? AND p.is_published = 1 AND p.active = 1 
                ORDER BY RAND() LIMIT 3
            ");
            $stmt->execute([$project['category_name'], $id]);
            $related = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $related = [];
        }
        
        // Teknolojileri parse et
        if (!empty($project['technologies'])) {
            if (is_string($project['technologies']) && $project['technologies'][0] === '[') {
                $project['technologies'] = json_decode($project['technologies'], true) ?: [];
            } else {
                $project['technologies'] = array_filter(array_map('trim', explode(',', $project['technologies'])));
            }
        } else {
            $project['technologies'] = [];
        }
        
        // Sonuçları parse et
        if (!empty($project['results'])) {
            if (is_string($project['results']) && $project['results'][0] === '[') {
                $project['results'] = json_decode($project['results'], true) ?: [];
            } else {
                $project['results'] = array_filter(array_map('trim', explode(',', $project['results'])));
            }
        } else {
            $project['results'] = [];
        }
        
        $project['images'] = $images;
        $project['related_projects'] = $related;
        
        echo json_encode([
            'success' => true,
            'project' => $project
        ]);
        
    } catch (PDOException $e) {
        throw new Exception('Proje detayları yüklenirken hata oluştu: ' . $e->getMessage());
    }
}

function getCategories() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.*, 
                COUNT(p.id) as project_count 
            FROM portfolio_categories c 
            LEFT JOIN portfolio_projects p ON p.category_id = c.id AND p.is_published = 1 AND p.active = 1
            WHERE c.is_active = 1 
            GROUP BY c.id 
            ORDER BY c.sort_order
        ");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'categories' => $categories
        ]);
        
    } catch (PDOException $e) {
        throw new Exception('Kategoriler yüklenirken hata oluştu: ' . $e->getMessage());
    }
}

function getTechnologies() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM portfolio_technologies WHERE is_active = 1 ORDER BY sort_order");
        $stmt->execute();
        $technologies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'technologies' => $technologies
        ]);
        
    } catch (PDOException $e) {
        throw new Exception('Teknolojiler yüklenirken hata oluştu: ' . $e->getMessage());
    }
}

function searchProjects() {
    global $pdo;
    
    $query = $_GET['q'] ?? '';
    $limit = (int)($_GET['limit'] ?? 10);
    
    if (strlen($query) < 2) {
        throw new Exception('Arama terimi en az 2 karakter olmalı');
    }
    
    try {
        $sql = "
            SELECT 
                p.id, 
                p.title, 
                p.short_description, 
                p.featured_image,
                c.name as category_name
            FROM portfolio_projects p
            LEFT JOIN portfolio_categories c ON p.category_id = c.id
            WHERE p.is_published = 1 AND p.active = 1 AND (
                p.title LIKE ? OR 
                p.short_description LIKE ? OR 
                p.description LIKE ? OR 
                p.technologies LIKE ? OR 
                c.name LIKE ?
            )
            ORDER BY 
                CASE 
                    WHEN p.title LIKE ? THEN 1
                    WHEN p.short_description LIKE ? THEN 2
                    ELSE 3
                END,
                p.created_at DESC
            LIMIT ?
        ";
        
        $search_term = '%' . $query . '%';
        $title_priority = $query . '%';
        
        $params = [
            $search_term, $search_term, $search_term, $search_term, $search_term,
            $title_priority, $title_priority,
            $limit
        ];
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'results' => $results,
            'query' => $query
        ]);
        
    } catch (PDOException $e) {
        throw new Exception('Arama yapılırken hata oluştu: ' . $e->getMessage());
    }
}

function incrementView() {
    global $pdo;
    
    $id = (int)($_POST['id'] ?? 0);
    
    if (!$id) {
        throw new Exception('Proje ID gerekli');
    }
    
    try {
        // Proje görüntülenme sayısını güncelle
        $stmt = $pdo->prepare("UPDATE portfolio_projects SET view_count = view_count + 1 WHERE id = ?");
        $stmt->execute([$id]);
        
        // Analytics verilerini logla (eğer tablo varsa)
        try {
            $visitor_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            
            $stmt = $pdo->prepare("INSERT INTO portfolio_analytics 
                                  (project_id, visitor_ip, user_agent, referrer, view_date, view_time) 
                                  VALUES (?, ?, ?, ?, CURDATE(), CURTIME())");
            $stmt->execute([$id, $visitor_ip, $user_agent, $referrer]);
        } catch (PDOException $e) {
            // Analytics tablosu yoksa sessizce devam et
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Görüntülenme sayısı güncellendi'
        ]);
        
    } catch (PDOException $e) {
        throw new Exception('Görüntülenme sayısı güncellenirken hata oluştu: ' . $e->getMessage());
    }
}

// Helper function to sanitize output
function sanitizeOutput($data) {
    if (is_array($data)) {
        return array_map('sanitizeOutput', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Error handler
function handleError($errno, $errstr, $errfile, $errline) {
    error_log("Portfolio API Error: $errstr in $errfile on line $errline");
    
    if (!headers_sent()) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Sunucu hatası oluştu'
        ]);
    }
    exit;
}

set_error_handler('handleError');
?>