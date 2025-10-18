<?php
/**
 * NextCode Group - Database Performance Optimizer
 * Mevcut veritabanını bozmadan performans indexleri ekler
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/error_handler.php';

class DatabaseOptimizer {
    private $pdo;
    private $logFile;
    
    public function __construct() {
        try {
            $database = new Database();
            $this->pdo = $database->getConnection();
            $this->logFile = __DIR__ . '/../logs/optimization.log';
            
            if (!$this->pdo) {
                throw new Exception('Database connection failed');
            }
        } catch (Exception $e) {
            error_log('DatabaseOptimizer initialization failed: ' . $e->getMessage());
            $this->pdo = null;
        }
    }
    
    /**
     * Performans indexlerini güvenli şekilde ekle
     */
    public function addPerformanceIndexes() {
        if (!$this->pdo) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        $results = [];
        $indexes = $this->getIndexDefinitions();
        
        foreach ($indexes as $table => $tableIndexes) {
            foreach ($tableIndexes as $indexName => $indexDef) {
                try {
                    // Index'in var olup olmadığını kontrol et
                    if ($this->indexExists($table, $indexName)) {
                        $results[] = "Index {$indexName} already exists on table {$table}";
                        continue;
                    }
                    
                    // Index'i ekle
                    $sql = "ALTER TABLE {$table} ADD INDEX {$indexName} ({$indexDef})";
                    $this->pdo->exec($sql);
                    $results[] = "Successfully added index {$indexName} to table {$table}";
                    
                } catch (Exception $e) {
                    $results[] = "Failed to add index {$indexName} to table {$table}: " . $e->getMessage();
                    error_log("Index creation failed: " . $e->getMessage());
                }
            }
        }
        
        return ['success' => true, 'results' => $results];
    }
    
    /**
     * Index tanımlarını al
     */
    private function getIndexDefinitions() {
        return [
            'blog_posts' => [
                'idx_blog_status_published' => 'status, published_at',
                'idx_blog_category_status' => 'category_id, status',
                'idx_blog_featured_status' => 'is_featured, status',
                'idx_blog_created_status' => 'created_at, status'
            ],
            'portfolio_projects' => [
                'idx_portfolio_status_featured' => 'status, featured',
                'idx_portfolio_category_status' => 'category, status',
                'idx_portfolio_sort_status' => 'sort_order, status'
            ],
            'services' => [
                'idx_services_active_order' => 'is_active, order_index',
                'idx_services_title_active' => 'title, is_active'
            ],
            'contact_messages' => [
                'idx_contact_status_created' => 'status, created_at',
                'idx_contact_email_created' => 'email, created_at',
                'idx_contact_read_created' => 'is_read, created_at'
            ],
            'site_content' => [
                'idx_content_page_section' => 'page_name, section_name',
                'idx_content_active_sort' => 'is_active, id'
            ],
            'pages' => [
                'idx_pages_slug_status' => 'slug, status',
                'idx_pages_status_created' => 'status, created_at'
            ]
        ];
    }
    
    /**
     * Index'in var olup olmadığını kontrol et
     */
    private function indexExists($table, $indexName) {
        try {
            $sql = "SHOW INDEX FROM {$table} WHERE Key_name = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$indexName]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Tabloları optimize et
     */
    public function optimizeTables() {
        if (!$this->pdo) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        $tables = [
            'blog_posts', 'portfolio_projects', 'services', 'contact_messages',
            'site_content', 'pages', 'admin_users', 'site_statistics',
            'team_members', 'company_stats', 'service_packages', 'faq_categories',
            'package_features', 'faq', 'pricing_packages', 'blog_categories',
            'portfolio_categories', 'about_content', 'contact_info'
        ];
        
        $results = [];
        
        foreach ($tables as $table) {
            try {
                if ($this->tableExists($table)) {
                    $this->pdo->exec("OPTIMIZE TABLE {$table}");
                    $results[] = "Optimized table: {$table}";
                }
            } catch (Exception $e) {
                $results[] = "Failed to optimize table {$table}: " . $e->getMessage();
            }
        }
        
        return ['success' => true, 'results' => $results];
    }
    
    /**
     * Tablo var mı kontrol et
     */
    private function tableExists($tableName) {
        try {
            $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$tableName]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Database istatistiklerini al
     */
    public function getDatabaseStats() {
        if (!$this->pdo) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        try {
            $stats = [];
            
            // Tablo boyutları
            $sql = "SELECT 
                        table_name,
                        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)',
                        table_rows
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE()
                    ORDER BY (data_length + index_length) DESC";
            
            $stmt = $this->pdo->query($sql);
            $stats['tables'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Toplam boyut
            $sql = "SELECT 
                        ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Total Size (MB)'
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE()";
            
            $stmt = $this->pdo->query($sql);
            $stats['total_size'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'stats' => $stats];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

// CLI veya web erişimi için
if (php_sapi_name() === 'cli' || isset($_GET['run'])) {
    $optimizer = new DatabaseOptimizer();
    
    echo "=== Database Performance Optimization ===\n";
    
    // Indexleri ekle
    echo "\n1. Adding performance indexes...\n";
    $indexResult = $optimizer->addPerformanceIndexes();
    if ($indexResult['success']) {
        foreach ($indexResult['results'] as $result) {
            echo "   - {$result}\n";
        }
    }
    
    // Tabloları optimize et
    echo "\n2. Optimizing tables...\n";
    $optimizeResult = $optimizer->optimizeTables();
    if ($optimizeResult['success']) {
        foreach ($optimizeResult['results'] as $result) {
            echo "   - {$result}\n";
        }
    }
    
    // İstatistikleri göster
    echo "\n3. Database statistics...\n";
    $statsResult = $optimizer->getDatabaseStats();
    if ($statsResult['success']) {
        echo "   Total Database Size: {$statsResult['stats']['total_size']['Total Size (MB)']} MB\n";
        echo "   Tables:\n";
        foreach ($statsResult['stats']['tables'] as $table) {
            echo "     - {$table['table_name']}: {$table['Size (MB)']} MB ({$table['table_rows']} rows)\n";
        }
    }
    
    echo "\n=== Optimization Complete ===\n";
}
?>
