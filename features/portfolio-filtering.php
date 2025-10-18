<?php
/**
 * NextCode Group - Portfolio Filtering System
 * Mevcut portfolio sistemini geliştirir
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once __DIR__ . '/../config/database.php';

class PortfolioFiltering {
    private $pdo;
    
    public function __construct() {
        try {
            $database = new Database();
            $this->pdo = $database->getConnection();
        } catch (Exception $e) {
            $this->pdo = null;
        }
    }
    
    /**
     * Portfolio filtreleme için gelişmiş özellikler
     */
    public function createPortfolioFilteringTables() {
        if (!$this->pdo) return false;
        
        try {
            // Portfolio technologies tablosu
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS portfolio_technologies (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL UNIQUE,
                    slug VARCHAR(255) NOT NULL UNIQUE,
                    icon VARCHAR(100),
                    color VARCHAR(7) DEFAULT "#667eea",
                    description TEXT,
                    is_active TINYINT(1) DEFAULT 1,
                    sort_order INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_slug (slug),
                    INDEX idx_active (is_active),
                    INDEX idx_sort (sort_order)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Portfolio project technologies (many-to-many)
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS portfolio_project_technologies (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    project_id INT NOT NULL,
                    technology_id INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_project_tech (project_id, technology_id),
                    INDEX idx_project_id (project_id),
                    INDEX idx_technology_id (technology_id),
                    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE,
                    FOREIGN KEY (technology_id) REFERENCES portfolio_technologies(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Portfolio project views tracking
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS portfolio_project_views (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    project_id INT NOT NULL,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_project_id (project_id),
                    INDEX idx_viewed_at (viewed_at),
                    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Portfolio project likes
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS portfolio_project_likes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    project_id INT NOT NULL,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_project_like (project_id, ip_address),
                    INDEX idx_project_id (project_id),
                    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Portfolio projects tablosuna yeni sütunlar ekle (tek tek)
            $columns = [
                'view_count INT DEFAULT 0',
                'like_count INT DEFAULT 0', 
                'client_name VARCHAR(255)',
                'project_year YEAR',
                'project_duration VARCHAR(100)',
                'team_size VARCHAR(50)',
                'budget_range VARCHAR(100)',
                'project_url VARCHAR(500)',
                'github_url VARCHAR(500)',
                'demo_url VARCHAR(500)',
                'featured_image VARCHAR(500)',
                'gallery_images TEXT',
                'project_challenges TEXT',
                'project_solutions TEXT',
                'project_results TEXT',
                'testimonial TEXT',
                'testimonial_author VARCHAR(255)',
                'testimonial_position VARCHAR(255)',
                'testimonial_company VARCHAR(255)'
            ];
            
            foreach ($columns as $column) {
                try {
                    $this->pdo->exec("ALTER TABLE portfolio_projects ADD COLUMN {$column}");
                } catch (Exception $e) {
                    // Sütun zaten varsa devam et
                    continue;
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Portfolio filtering tables creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Default teknolojileri ekle
     */
    public function insertDefaultTechnologies() {
        if (!$this->pdo) return false;
        
        try {
            $technologies = [
                ['name' => 'PHP', 'slug' => 'php', 'icon' => 'fab fa-php', 'color' => '#777bb4'],
                ['name' => 'JavaScript', 'slug' => 'javascript', 'icon' => 'fab fa-js-square', 'color' => '#f7df1e'],
                ['name' => 'React', 'slug' => 'react', 'icon' => 'fab fa-react', 'color' => '#61dafb'],
                ['name' => 'Vue.js', 'slug' => 'vuejs', 'icon' => 'fab fa-vuejs', 'color' => '#4fc08d'],
                ['name' => 'Node.js', 'slug' => 'nodejs', 'icon' => 'fab fa-node-js', 'color' => '#339933'],
                ['name' => 'MySQL', 'slug' => 'mysql', 'icon' => 'fas fa-database', 'color' => '#4479a1'],
                ['name' => 'MongoDB', 'slug' => 'mongodb', 'icon' => 'fas fa-leaf', 'color' => '#47a248'],
                ['name' => 'Bootstrap', 'slug' => 'bootstrap', 'icon' => 'fab fa-bootstrap', 'color' => '#7952b3'],
                ['name' => 'CSS3', 'slug' => 'css3', 'icon' => 'fab fa-css3-alt', 'color' => '#1572b6'],
                ['name' => 'HTML5', 'slug' => 'html5', 'icon' => 'fab fa-html5', 'color' => '#e34f26'],
                ['name' => 'Laravel', 'slug' => 'laravel', 'icon' => 'fab fa-laravel', 'color' => '#ff2d20'],
                ['name' => 'WordPress', 'slug' => 'wordpress', 'icon' => 'fab fa-wordpress', 'color' => '#21759b'],
                ['name' => 'Git', 'slug' => 'git', 'icon' => 'fab fa-git-alt', 'color' => '#f05032'],
                ['name' => 'Docker', 'slug' => 'docker', 'icon' => 'fab fa-docker', 'color' => '#2496ed'],
                ['name' => 'AWS', 'slug' => 'aws', 'icon' => 'fab fa-aws', 'color' => '#ff9900']
            ];
            
            foreach ($technologies as $tech) {
                $stmt = $this->pdo->prepare("
                    INSERT IGNORE INTO portfolio_technologies 
                    (name, slug, icon, color, description) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $tech['name'],
                    $tech['slug'],
                    $tech['icon'],
                    $tech['color'],
                    "{$tech['name']} technology used in web development projects"
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Default technologies insertion failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Portfolio projelerini filtrele
     */
    public function filterProjects($filters = []) {
        if (!$this->pdo) return [];
        
        try {
            $sql = "
                SELECT DISTINCT p.*, 
                       GROUP_CONCAT(t.name) as technologies,
                       GROUP_CONCAT(t.icon) as tech_icons,
                       GROUP_CONCAT(t.color) as tech_colors
                FROM portfolio_projects p
                LEFT JOIN portfolio_project_technologies pt ON p.id = pt.project_id
                LEFT JOIN portfolio_technologies t ON pt.technology_id = t.id
                WHERE p.status = 'published' AND p.active = 1
            ";
            
            $params = [];
            $conditions = [];
            
            // Kategori filtresi
            if (!empty($filters['category'])) {
                $conditions[] = "p.category = ?";
                $params[] = $filters['category'];
            }
            
            // Teknoloji filtresi
            if (!empty($filters['technology'])) {
                $conditions[] = "t.slug = ?";
                $params[] = $filters['technology'];
            }
            
            // Yıl filtresi
            if (!empty($filters['year'])) {
                $conditions[] = "p.project_year = ?";
                $params[] = $filters['year'];
            }
            
            // Arama filtresi
            if (!empty($filters['search'])) {
                $conditions[] = "(p.title LIKE ? OR p.description LIKE ? OR p.client_name LIKE ?)";
                $searchTerm = "%{$filters['search']}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            // Sıralama
            $orderBy = "ORDER BY p.sort_order ASC, p.created_at DESC";
            if (!empty($filters['sort'])) {
                switch ($filters['sort']) {
                    case 'newest':
                        $orderBy = "ORDER BY p.created_at DESC";
                        break;
                    case 'oldest':
                        $orderBy = "ORDER BY p.created_at ASC";
                        break;
                    case 'popular':
                        $orderBy = "ORDER BY p.view_count DESC";
                        break;
                    case 'liked':
                        $orderBy = "ORDER BY p.like_count DESC";
                        break;
                    case 'alphabetical':
                        $orderBy = "ORDER BY p.title ASC";
                        break;
                }
            }
            
            if (!empty($conditions)) {
                $sql .= " AND " . implode(" AND ", $conditions);
            }
            
            $sql .= " GROUP BY p.id " . $orderBy;
            
            // Limit
            if (!empty($filters['limit'])) {
                $sql .= " LIMIT " . intval($filters['limit']);
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log('Portfolio filtering failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Portfolio view'ını kaydet
     */
    public function trackPortfolioView($projectId) {
        if (!$this->pdo) return false;
        
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            // View'ı kaydet
            $stmt = $this->pdo->prepare("
                INSERT INTO portfolio_project_views (project_id, ip_address, user_agent) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$projectId, $ip, $userAgent]);
            
            // View count'u güncelle
            $stmt = $this->pdo->prepare("
                UPDATE portfolio_projects 
                SET view_count = view_count + 1 
                WHERE id = ?
            ");
            $stmt->execute([$projectId]);
            
            return true;
        } catch (Exception $e) {
            error_log('Portfolio view tracking failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Portfolio like'ını kaydet
     */
    public function addPortfolioLike($projectId) {
        if (!$this->pdo) return false;
        
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            // Like'ı kaydet
            $stmt = $this->pdo->prepare("
                INSERT INTO portfolio_project_likes (project_id, ip_address, user_agent) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP
            ");
            $stmt->execute([$projectId, $ip, $userAgent]);
            
            // Like count'u güncelle
            $stmt = $this->pdo->prepare("
                UPDATE portfolio_projects 
                SET like_count = (
                    SELECT COUNT(*) FROM portfolio_project_likes 
                    WHERE project_id = ?
                )
                WHERE id = ?
            ");
            $stmt->execute([$projectId, $projectId]);
            
            return true;
        } catch (Exception $e) {
            error_log('Portfolio like failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Portfolio istatistiklerini al
     */
    public function getPortfolioStats() {
        if (!$this->pdo) return [];
        
        try {
            $stats = [];
            
            // Toplam proje sayısı
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM portfolio_projects WHERE status = 'published' AND active = 1");
            $stats['total_projects'] = $stmt->fetchColumn();
            
            // Toplam view sayısı
            $stmt = $this->pdo->query("SELECT SUM(view_count) FROM portfolio_projects");
            $stats['total_views'] = $stmt->fetchColumn() ?: 0;
            
            // Toplam like sayısı
            $stmt = $this->pdo->query("SELECT SUM(like_count) FROM portfolio_projects");
            $stats['total_likes'] = $stmt->fetchColumn() ?: 0;
            
            // Kategori dağılımı
            $stmt = $this->pdo->query("
                SELECT category, COUNT(*) as count 
                FROM portfolio_projects 
                WHERE status = 'published' AND active = 1 
                GROUP BY category 
                ORDER BY count DESC
            ");
            $stats['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Teknoloji dağılımı
            $stmt = $this->pdo->query("
                SELECT t.name, COUNT(pt.project_id) as count 
                FROM portfolio_technologies t
                LEFT JOIN portfolio_project_technologies pt ON t.id = pt.technology_id
                LEFT JOIN portfolio_projects p ON pt.project_id = p.id AND p.status = 'published' AND p.active = 1
                GROUP BY t.id, t.name 
                HAVING count > 0
                ORDER BY count DESC
                LIMIT 10
            ");
            $stats['technologies'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (Exception $e) {
            error_log('Portfolio stats fetch failed: ' . $e->getMessage());
            return [];
        }
    }
}

// CLI veya web erişimi için
if (php_sapi_name() === 'cli' || isset($_GET['setup'])) {
    $portfolioFiltering = new PortfolioFiltering();
    
    echo "=== Portfolio Filtering System ===\n";
    
    if ($portfolioFiltering->createPortfolioFilteringTables()) {
        echo "✓ Portfolio filtering tables created successfully\n";
        
        if ($portfolioFiltering->insertDefaultTechnologies()) {
            echo "✓ Default technologies inserted successfully\n";
        }
        
        // İstatistikleri göster
        $stats = $portfolioFiltering->getPortfolioStats();
        echo "\nPortfolio Statistics:\n";
        echo "- Total Projects: {$stats['total_projects']}\n";
        echo "- Total Views: {$stats['total_views']}\n";
        echo "- Total Likes: {$stats['total_likes']}\n";
        
        if (!empty($stats['categories'])) {
            echo "\nCategories:\n";
            foreach ($stats['categories'] as $category) {
                echo "  - {$category['category']}: {$category['count']} projects\n";
            }
        }
        
        if (!empty($stats['technologies'])) {
            echo "\nTop Technologies:\n";
            foreach ($stats['technologies'] as $tech) {
                echo "  - {$tech['name']}: {$tech['count']} projects\n";
            }
        }
    } else {
        echo "✗ Failed to create portfolio filtering tables\n";
    }
    
    echo "\n=== Portfolio Filtering Complete ===\n";
}
?>
