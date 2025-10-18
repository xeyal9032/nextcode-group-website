<?php
// NextCode Group - Database Indexing Optimization
// Advanced database indexing and query optimization

if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

require_once __DIR__ . '/database.php';

class DatabaseOptimizer {
    private $pdo;
    
    public function __construct($pdo = null) {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    /**
     * Create optimized indexes for all tables
     */
    public function createOptimizedIndexes() {
        try {
            // Blog indexes
            $this->addBlogIndexes();
            
            // Portfolio indexes
            $this->addPortfolioIndexes();
            
            // Contact indexes
            $this->addContactIndexes();
            
            // User indexes
            $this->addUserIndexes();
            
            // Content indexes
            $this->addContentIndexes();
            
            // Site statistics indexes
            $this->addStatisticsIndexes();
            
            return true;
        } catch (Exception $e) {
            error_log('Index creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add indexes for blog tables
     */
    private function addBlogIndexes() {
        $indexes = [
            // Blog posts table indexes
            'ALTER TABLE blog_posts ADD INDEX IF NOT EXISTS idx_blog_status_published (status, published_at)',
            'ALTER TABLE blog_posts ADD INDEX IF NOT EXISTS idx_blog_featured (is_featured, published_at)',
            'ALTER TABLE blog_posts ADD INDEX IF NOT EXISTS idx_blog_category_status (category_id, status)',
            'ALTER TABLE blog_posts ADD INDEX IF NOT EXISTS idx_blog_title_search ((UPPER(title)))',
            'ALTER TABLE blog_posts ADD INDEX IF NOT EXISTS idx_blog_content_length ((CHAR_LENGTH(content)))',
            'ALTER TABLE blog_posts ADD INDEX IF NOT EXISTS idx_blog_created_month ((YEAR(created_at), MONTH(created_at)))',
            
            // Blog categories table indexes
            'ALTER TABLE blog_categories ADD INDEX IF NOT EXISTS idx_cat_slug_active (slug, is_active)',
            'ALTER TABLE blog_categories ADD INDEX IF NOT EXISTS idx_cat_name_sort (name, sort_order)',
        ];
        
        $this->executeIndexQueries($indexes);
    }
    
    /**
     * Add indexes for portfolio tables
     */
    private function addPortfolioIndexes() {
        $indexes = [
            // Portfolio projects table indexes
            'ALTER TABLE portfolio_projects ADD INDEX IF NOT EXISTS idx_portfolio_category_featured (category, is_featured)',
            'ALTER TABLE portfolio_projects ADD INDEX IF NOT EXISTS idx_portfolio_status_order (is_published, sort_order)',
            'ALTER TABLE portfolio_projects ADD INDEX IF NOT EXISTS idx_portfolio_views (views DESC)',
            'ALTER TABLE portfolio_projects ADD INDEX IF NOT EXISTS idx_portfolio_created_status (created_at, is_published)',
            'ALTER TABLE portfolio_projects ADD INDEX IF NOT EXISTS idx_portfolio_technologies ((FIND_IN_SET("technology", technologies)))',
            
            // Portfolio categories table indexes
            'ALTER TABLE portfolio_categories ADD INDEX IF NOT EXISTS idx_port_cat_slug_active (slug, is_active)',
            'ALTER TABLE portfolio_categories ADD INDEX IF NOT EXISTS idx_port_cat_sort_alpha (sort_order, name)',
        ];
        
        $this->executeIndexQueries($indexes);
    }
    
    /**
     * Add indexes for contact tables
     */
    private function addContactIndexes() {
        $indexes = [
            // Contact messages table indexes
            'ALTER TABLE contact_messages ADD INDEX IF NOT EXISTS idx_contact_status_date (status, created_at DESC)',
            'ALTER TABLE contact_messages ADD INDEX IF NOT EXISTS idx_contact_email_status (email, status)',
            'ALTER TABLE contact_messages ADD INDEX IF NOT EXISTS idx_contact_subject_status (subject(255), status)',
            'ALTER TABLE contact_messages ADD INDEX IF NOT EXISTS idx_contact_ip_date (ip_address, created_at)',
            'ALTER TABLE contact_messages ADD INDEX IF NOT EXISTS idx_contact_created_index (created_at DESC)',
            'ALTER TABLE contact_messages ADD INDEX IF NOT EXISTS idx_contact_first_last_name (first_name(50), last_name(50))',
            
            // Contact info table indexes
            'ALTER TABLE contact_info ADD INDEX IF NOT EXISTS idx_contact_company_email (company_name, email)',
            'ALTER TABLE contact_info ADD INDEX IF NOT EXISTS idx_contact_social_platforms (social_facebook, social_twitter, social_instagram, social_linkedin)',
        ];
        
        $this->executeIndexQueries($indexes);
    }
    
    /**
     * Add indexes for user tables
     */
    private function addUserIndexes() {
        $indexes = [
            // Admin users table indexes
            'ALTER TABLE admin_users ADD INDEX IF NOT EXISTS idx_admin_email_active (email, is_active)',
            'ALTER TABLE admin_users ADD INDEX IF NOT EXISTS idx_admin_username_active (username, is_active)',
            'ALTER TABLE admin_users ADD INDEX IF NOT EXISTS idx_admin_role_active (role, is_active)',
            'ALTER TABLE admin_users ADD INDEX IF NOT EXISTS idx_admin_last_login ((DATE(last_login)))',
            'ALTER TABLE admin_users ADD INDEX IF NOT EXISTS idx_admin_login_attempts (login_attempts, locked_until)',
            'ALTER TABLE admin_users ADD INDEX IF NOT EXISTS idx_admin_ip_last_login (ip_address, last_login)',
            
            // Admin logs table indexes
            'ALTER TABLE admin_logs ADD INDEX IF NOT EXISTS idx_log_admin_action_date (admin_id, action, created_at DESC)',
            'ALTER TABLE admin_logs ADD INDEX IF NOT EXISTS idx_log_ip_date (ip_address, created_at DESC)',
            'ALTER TABLE admin_logs ADD INDEX IF NOT EXISTS idx_log_created_index (created_at DESC)',
        ];
        
        $this->executeIndexQueries($indexes);
    }
    
    /**
     * Add indexes for content tables
     */
    private function addContentIndexes() {
        $indexes = [
            // Site content table indexes
            'ALTER TABLE site_content ADD INDEX IF NOT EXISTS idx_content_page_section (page_name, section_name)',
            'ALTER TABLE site_content ADD INDEX IF NOT EXISTS idx_content_key_active (content_key, is_active)',
            'ALTER TABLE site_content ADD INDEX IF NOT EXISTS idx_content_type_active (content_type, is_active)',
            'ALTER TABLE site_content ADD INDEX IF NOT EXISTS idx_content_updated_date (updated_at DESC)',
            
            // Site images table indexes
            'ALTER TABLE site_images ADD INDEX IF NOT EXISTS idx_images_key_active (image_key, is_active)',
            'ALTER TABLE site_images ADD INDEX IF NOT EXISTS idx_images_section_order (page_section, sort_order)',
            'ALTER TABLE site_images ADD INDEX IF NOT EXISTS idx_images_active_order (is_active, sort_order)',
            
            // Pages table indexes
            'ALTER TABLE pages ADD INDEX IF NOT EXISTS idx_pages_slug_status (slug, status)',
            'ALTER TABLE pages ADD INDEX IF NOT EXISTS idx_pages_status_views (status, views DESC)',
            'ALTER TABLE pages ADD INDEX IF NOT EXISTS idx_pages_meta_search ((MATCH(meta_title, meta_description) AGAINST("search term" IN NATURAL LANGUAGE MODE)))',
        ];
        
        $this->executeIndexQueries($indexes);
    }
    
    /**
     * Add indexes for statistics tables
     */
    private function addStatisticsIndexes() {
        $indexes = [
            // Site statistics table indexes
            'ALTER TABLE site_statistics ADD INDEX IF NOT EXISTS idx_stats_active_date (is_active, created_at DESC)',
            'ALTER TABLE site_statistics ADD INDEX IF NOT EXISTS idx_stats_projects_clients (projects_completed, total_clients)',
            
            // Team members table indexes
            'ALTER TABLE team_members ADD INDEX IF NOT EXISTS idx_team_active_sort (is_active, sort_order)',
            'ALTER TABLE team_members ADD INDEX IF NOT EXISTS idx_team_position_name (position, name)',
            
            // Company stats table indexes
            'ALTER TABLE company_stats ADD INDEX IF NOT EXISTS idx_company_founded_active (founded_year, is_active)',
            'ALTER TABLE company_stats ADD INDEX IF NOT EXISTS idx_company_stats_active (is_active, total_projects DESC)',
            
            // Services table indexes
            'ALTER TABLE services ADD INDEX IF NOT EXISTS idx_services_status_order (status, order_index)',
            'ALTER TABLE services ADD INDEX IF NOT EXISTS idx_services_title_search ((UPPER(title)))',
            
            // FAQ table indexes
            'ALTER TABLE faq ADD INDEX IF NOT EXISTS idx_faq_category_active (category, is_active)',
            'ALTER TABLE faq ADD INDEX IF NOT EXISTS idx_faq_active_sort (is_active, sort_order)',
            'ALTER TABLE faq ADD INDEX IF NOT EXISTS idx_faq_question_search ((MATCH(question) AGAINST("search query" IN NATURAL LANGUAGE MODE)))',
            
            // Pricing packages table indexes
            'ALTER TABLE pricing_packages ADD INDEX IF NOT EXISTS idx_pricing_active_popular (is_active, is_popular)',
            'ALTER TABLE pricing_packages ADD INDEX IF NOT EXISTS idx_pricing_price_sort (price, sort_order)',
            'ALTER TABLE pricing_packages ADD INDEX IF NOT EXISTS idx_pricing_currency_active (currency, is_active)',
            
            // Service packages table indexes
            'ALTER TABLE service_packages ADD INDEX IF NOT EXISTS idx_service_packages_active_sort (is_active, sort_order)',
            'ALTER TABLE service_packages ADD INDEX IF NOT EXISTS idx_service_packages_price_range (price, is_active)',
            
            // FAQ categories table indexes
            'ALTER TABLE faq_categories ADD INDEX IF NOT EXISTS idx_faq_cat_active_sort (is_active, sort_order)',
            'ALTER TABLE faq_categories ADD INDEX IF NOT EXISTS idx_faq_cat_name_active (name, is_active)',
            
            // Package features table indexes
            'ALTER TABLE package_features ADD INDEX IF NOT EXISTS idx_package_features_active_sort (is_active, sort_order)',
            'ALTER TABLE package_features ADD INDEX IF NOT EXISTS idx_package_features_name_search ((UPPER(name)))',
        ];
        
        $this->executeIndexQueries($indexes);
    }
    
    /**
     * Execute index creation queries
     */
    private function executeIndexQueries($queries) {
        foreach ($queries as $query) {
            try {
                $this->pdo->exec($query);
            } catch (PDOException $e) {
                error_log('Index creation error: ' . $e->getMessage() . ' Query: ' . $query);
            }
        }
    }
    
    /**
     * Analyze table performance and suggest optimizations
     */
    public function analyzePerformance() {
        $analysis = [];
        
        $tables = [
            'blog_posts', 'blog_categories', 'portfolio_projects', 'portfolio_categories',
            'contact_messages', 'contact_info', 'admin_users', 'admin_logs',
            'site_content', 'site_images', 'pages', 'site_statistics',
            'team_members', 'company_stats', 'services', 'faq',
            'pricing_packages', 'service_packages', 'faq_categories', 'package_features'
        ];
        
        foreach ($tables as $table) {
            try {
                // Get table size and row count
                $sizeQuery = "SELECT TABLE_ROWS, ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size_MB' FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
                $stmt = $this->pdo->prepare($sizeQuery);
                $stmt->execute([$table]);
                $sizeInfo = $stmt->fetch();
                
                // Get index information
                $indexQuery = "SHOW INDEX FROM $table";
                $indexStmt = $this->pdo->query($indexQuery);
                $indexes = $indexStmt->fetchAll();
                
                // Analyze slow queries (if slow query log is enabled)
                $slowQueryInfo = $this->getSlowQueryInfo($table);
                
                $analysis[$table] = [
                    'rows' => $sizeInfo['TABLE_ROWS'] ?? 0,
                    'size_mb' => $sizeInfo['Size_MB'] ?? 0,
                    'indexes' => count($indexes),
                    'slow_queries' => $slowQueryInfo,
                    'recommendations' => $this->getTableRecommendations($table, $sizeInfo['TABLE_ROWS'] ?? 0)
                ];
                
            } catch (Exception $e) {
                error_log("Performance analysis failed for table $table: " . $e->getMessage());
            }
        }
        
        return $analysis;
    }
    
    /**
     * Get slow query information for a table
     */
    private function getSlowQueryInfo($table) {
        try {
            $query = "SELECT COUNT(*) as count, AVG(Query_time) as avg_time FROM mysql.slow_log WHERE sql_text LIKE '%$table%' AND start_time > DATE_SUB(NOW(), INTERVAL 1 DAY)";
            $stmt = $this->pdo->query($query);
            return $stmt->fetch();
        } catch (Exception $e) {
            return ['count' => 0, 'avg_time' => 0];
        }
    }
    
    /**
     * Get recommendations for table optimization
     */
    private function getTableRecommendations($table, $rowCount) {
        $recommendations = [];
        
        if ($rowCount > 10000) {
            $recommendations[] = 'Consider partitioning for large datasets';
            $recommendations[] = 'Regular table optimization recommended';
        }
        
        if ($rowCount > 100000) {
            $recommendations[] = 'Archive old data to improve performance';
            $recommendations[] = 'Consider read replicas for heavy queries';
        }
        
        $recommendations[] = 'Regular index maintenance recommended';
        $recommendations[] = 'Monitor query performance with EXPLAIN';
        
        return $recommendations;
    }
    
    /**
     * Optimize specific queries
     */
    public function optimizeQuery($query, $bindings = []) {
        try {
            // Use EXPLAIN to analyze query
            $explainQuery = "EXPLAIN " . $query;
            $stmt = $this->pdo->prepare($explainQuery);
            $stmt->execute($bindings);
            $explanation = $stmt->fetchAll();
            
            // Analyze explanation results
            $analysis = $this->analyzeQueryPlan($explanation);
            
            // Suggest optimizations
            $suggestions = $this->getQueryOptimizationSuggestions($analysis, $query);
            
            return [
                'explanation' => $explanation,
                'analysis' => $analysis,
                'suggestions' => $suggestions
            ];
            
        } catch (Exception $e) {
            error_log('Query optimization failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Analyze query execution plan
     */
    private function analyzeQueryPlan($explanation) {
        $analysis = [
            'has_full_table_scan' => false,
            'has_temp_tables' => false,
            'has_filesort' => false,
            'estimated_rows' => 0,
            'key_used' => null,
            'index_possible_keys' => []
        ];
        
        foreach ($explanation as $row) {
            if ($row['type'] === 'ALL') {
                $analysis['has_full_table_scan'] = true;
            }
            
            if ($row['Extra'] && strpos($row['Extra'], 'Using temporary') !== false) {
                $analysis['has_temp_tables'] = true;
            }
            
            if ($row['Extra'] && strpos($row['Extra'], 'Using filesort') !== false) {
                $analysis['has_filesort'] = true;
            }
            
            $analysis['estimated_rows'] += $row['rows'];
            $analysis['index_possible_keys'] = array_merge($analysis['index_possible_keys'], explode(',', $row['possible_keys']));
            
            if ($row['key']) {
                $analysis['key_used'] = $row['key'];
            }
        }
        
        return $analysis;
    }
    
    /**
     * Get query optimization suggestions
     */
    private function getQueryOptimizationSuggestions($analysis, $query) {
        $suggestions = [];
        
        if ($analysis['has_full_table_scan']) {
            $suggestions[] = 'Query performs full table scan - consider adding appropriate indexes';
        }
        
        if ($analysis['has_temp_tables']) {
            $suggestions[] = 'Query uses temporary tables - consider optimizing JOIN or ORDER BY';
        }
        
        if ($analysis['has_filesort']) {
            $suggestions[] = 'Query uses filesort - consider adding index for ORDER BY columns';
        }
        
        if ($analysis['estimated_rows'] > 10000) {
            $suggestions[] = 'Query examines many rows - consider adding WHERE conditions or indexes';
        }
        
        if (empty($analysis['key_used'])) {
            $suggestions[] = 'No index used - consider adding appropriate indexes';
        }
        
        // Pattern-based suggestions
        if (preg_match('/SELECT \* FROM/', $query)) {
            $suggestions[] = 'Avoid SELECT * - specify only needed columns';
        }
        
        if (preg_match('/LIKE \'%/', $query)) {
            $suggestions[] = 'Leading wildcard in LIKE may prevent index usage';
        }
        
        return $suggestions;
    }
    
    /**
     * Get database performance metrics
     */
    public function getPerformanceMetrics() {
        $metrics = [];
        
        try {
            // Connection status
            $statusQuery = "SHOW STATUS LIKE '%connections'";
            $stmt = $this->pdo->query($statusQuery);
            $connections = $stmt->fetchAll();
            
            // Query cache
            $cacheQuery = "SHOW STATUS LIKE 'Qcache%'";
            $stmt = $this->pdo->query($cacheQuery);
            $cache = $stmt->fetchAll();
            
            // Table locks
            $locksQuery = "SHOW STATUS LIKE '%lock%'";
            $stmt = $this->pdo->query($locksQuery);
            $locks = $stmt->fetchAll();
            
            $metrics = [
                'connections' => $this->arrayToKeyValue($connections),
                'query_cache' => $this->arrayToKeyValue($cache),
                'locks' => $this->arrayToKeyValue($locks),
                'timestamp' => time()
            ];
            
        } catch (Exception $e) {
            error_log('Performance metrics collection failed: ' . $e->getMessage());
        }
        
        return $metrics;
    }
    
    /**
     * Convert array to key-value format
     */
    private function arrayToKeyValue($array) {
        $result = [];
        foreach ($array as $row) {
            $result[$row['Variable_name']] = $row['Value'];
        }
        return $result;
    }
}

// Initialize database optimizer
$dbOptimizer = new DatabaseOptimizer($pdo);

// Helper functions
function create_database_indexes() {
    global $dbOptimizer;
    return $dbOptimizer->createOptimizedIndexes();
}

function analyze_database_performance() {
    global $dbOptimizer;
    return $dbOptimizer->analyzePerformance();
}

function optimize_database_query($query, $bindings = []) {
    global $dbOptimizer;
    return $dbOptimizer-> optimizeQuery($query, $bindings);
}

function get_database_metrics() {
    global $dbOptimizer;
    return $dbOptimizer->getPerformanceMetrics();
}

?>

