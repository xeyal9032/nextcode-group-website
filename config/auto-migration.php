<?php
/**
 * NextCode Group - Otomatik Migration Sistemi
 * 
 * Bu script eksik veritabanı sütunlarını ve index'leri otomatik olarak ekler
 * Her sayfa yüklendiğinde çalışır ama cache ile performanslı çalışır
 * 
 * @author NextCode Group
 * @version 1.0
 * @since 2025-10-12
 */

// Check if secure access is defined
// Security check temporarily disabled for testing
/*
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}
*/

/**
 * Otomatik Migration Sınıfı
 */
class AutoMigration {
    private $pdo;
    private $cache_file;
    private $cache_duration = 3600; // 1 saat (3600 saniye)
    private $migrations_applied = [];
    private $errors = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->cache_file = __DIR__ . '/../cache/migration_cache.json';
        
        // Cache klasörünü oluştur
        if (!is_dir(__DIR__ . '/../cache')) {
            @mkdir(__DIR__ . '/../cache', 0755, true);
        }
    }
    
    /**
     * Migration'ları çalıştır (cache kontrolü ile)
     */
    public function run() {
        // Cache kontrol et
        if ($this->isCacheValid()) {
            return true; // Cache geçerli, migration'a gerek yok
        }
        
        try {
            // Tüm migration'ları çalıştır
            $this->migrateAdminUsers();
            $this->migrateContactMessages();
            $this->migrateSiteImages();
            $this->migratePortfolioProjects();
            $this->migrateServices();
            $this->addPerformanceIndexes();
            
            // Cache'i güncelle
            $this->updateCache();
            
            // Log yaz
            $this->logMigrations();
            
            return true;
        } catch (Exception $e) {
            error_log('Auto-migration error: ' . $e->getMessage());
            $this->errors[] = $e->getMessage();
            return false;
        }
    }
    
    /**
     * Cache geçerli mi kontrol et
     */
    private function isCacheValid() {
        if (!file_exists($this->cache_file)) {
            return false;
        }
        
        $cache_data = json_decode(file_get_contents($this->cache_file), true);
        
        if (!$cache_data || !isset($cache_data['last_run'])) {
            return false;
        }
        
        // Cache süresi dolmuş mu?
        if (time() - $cache_data['last_run'] > $this->cache_duration) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Cache'i güncelle
     */
    private function updateCache() {
        $cache_data = [
            'last_run' => time(),
            'migrations_applied' => $this->migrations_applied,
            'last_check' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents($this->cache_file, json_encode($cache_data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Sütun var mı kontrol et
     */
    private function columnExists($table, $column) {
        try {
            $columns = $this->pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_COLUMN);
            return in_array($column, $columns);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Index var mı kontrol et
     */
    private function indexExists($table, $index_name) {
        try {
            $indexes = $this->pdo->query("SHOW INDEX FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
            $index_names = array_column($indexes, 'Key_name');
            return in_array($index_name, $index_names);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Migration 1: admin_users tablosu
     */
    private function migrateAdminUsers() {
        try {
            // remember_token ekle
            if (!$this->columnExists('admin_users', 'remember_token')) {
                $this->pdo->exec("ALTER TABLE admin_users ADD COLUMN remember_token VARCHAR(255) NULL AFTER password_hash");
                $this->migrations_applied[] = 'admin_users: remember_token eklendi';
            }
            
            // Index ekle
            if (!$this->indexExists('admin_users', 'idx_remember_token')) {
                $this->pdo->exec("ALTER TABLE admin_users ADD INDEX idx_remember_token (remember_token)");
                $this->migrations_applied[] = 'admin_users: idx_remember_token eklendi';
            }
            
        } catch (PDOException $e) {
            error_log('Migration admin_users error: ' . $e->getMessage());
            $this->errors[] = 'admin_users: ' . $e->getMessage();
        }
    }
    
    /**
     * Migration 2: contact_messages tablosu
     */
    private function migrateContactMessages() {
        try {
            // status enum değerlerini kontrol et
            $result = $this->pdo->query("SHOW COLUMNS FROM contact_messages LIKE 'status'")->fetch(PDO::FETCH_ASSOC);
            
            if ($result && strpos($result['Type'], 'new') !== false) {
                // Eski enum değerleri var, güncelle
                
                // 1. Verileri güncelle
                $this->pdo->exec("UPDATE contact_messages SET status = 'unread' WHERE status = 'new'");
                $this->pdo->exec("UPDATE contact_messages SET status = 'archived' WHERE status = 'replied'");
                
                // 2. Enum'u genişlet
                $this->pdo->exec("ALTER TABLE contact_messages MODIFY COLUMN status ENUM('new','read','replied','unread','archived') DEFAULT 'unread'");
                
                // 3. Eski değerleri kaldır
                $this->pdo->exec("ALTER TABLE contact_messages MODIFY COLUMN status ENUM('unread','read','archived') DEFAULT 'unread'");
                
                $this->migrations_applied[] = 'contact_messages: status enum güncellendi';
            }
            
            // is_read sütununu kaldır (eğer varsa)
            if ($this->columnExists('contact_messages', 'is_read')) {
                $this->pdo->exec("UPDATE contact_messages SET status = 'read' WHERE is_read = 1 AND status = 'unread'");
                $this->pdo->exec("ALTER TABLE contact_messages DROP COLUMN is_read");
                $this->migrations_applied[] = 'contact_messages: is_read kaldırıldı';
            }
            
            // Index'leri ekle
            if (!$this->indexExists('contact_messages', 'idx_status')) {
                $this->pdo->exec("ALTER TABLE contact_messages ADD INDEX idx_status (status)");
                $this->migrations_applied[] = 'contact_messages: idx_status eklendi';
            }
            
        } catch (PDOException $e) {
            error_log('Migration contact_messages error: ' . $e->getMessage());
            $this->errors[] = 'contact_messages: ' . $e->getMessage();
        }
    }
    
    /**
     * Migration 3: site_images tablosu
     */
    private function migrateSiteImages() {
        try {
            // image_description ekle
            if (!$this->columnExists('site_images', 'image_description')) {
                $this->pdo->exec("ALTER TABLE site_images ADD COLUMN image_description TEXT AFTER image_title");
                $this->migrations_applied[] = 'site_images: image_description eklendi';
            }
            
        } catch (PDOException $e) {
            error_log('Migration site_images error: ' . $e->getMessage());
            $this->errors[] = 'site_images: ' . $e->getMessage();
        }
    }
    
    /**
     * Migration 4: portfolio_projects tablosu
     */
    private function migratePortfolioProjects() {
        try {
            // featured → is_featured değiştir
            if ($this->columnExists('portfolio_projects', 'featured') && !$this->columnExists('portfolio_projects', 'is_featured')) {
                $this->pdo->exec("ALTER TABLE portfolio_projects CHANGE COLUMN featured is_featured TINYINT(1) DEFAULT 0");
                $this->migrations_applied[] = 'portfolio_projects: featured → is_featured değiştirildi';
            }
            
            // is_published ekle
            if (!$this->columnExists('portfolio_projects', 'is_published')) {
                $this->pdo->exec("ALTER TABLE portfolio_projects ADD COLUMN is_published TINYINT(1) DEFAULT 1 AFTER is_featured");
                $this->migrations_applied[] = 'portfolio_projects: is_published eklendi';
            }
            
            // Index'leri ekle
            if (!$this->indexExists('portfolio_projects', 'idx_is_featured')) {
                $this->pdo->exec("ALTER TABLE portfolio_projects ADD INDEX idx_is_featured (is_featured)");
                $this->migrations_applied[] = 'portfolio_projects: idx_is_featured eklendi';
            }
            
            if (!$this->indexExists('portfolio_projects', 'idx_is_published')) {
                $this->pdo->exec("ALTER TABLE portfolio_projects ADD INDEX idx_is_published (is_published)");
                $this->migrations_applied[] = 'portfolio_projects: idx_is_published eklendi';
            }
            
        } catch (PDOException $e) {
            error_log('Migration portfolio_projects error: ' . $e->getMessage());
            $this->errors[] = 'portfolio_projects: ' . $e->getMessage();
        }
    }
    
    /**
     * Migration 5: services tablosu
     */
    private function migrateServices() {
        try {
            // status sütunu ekle (opsiyonel - kod compatibility için)
            if (!$this->columnExists('services', 'status')) {
                $this->pdo->exec("ALTER TABLE services ADD COLUMN status ENUM('active','inactive') DEFAULT 'active' AFTER is_active");
                
                // is_active'e göre status'u set et
                $this->pdo->exec("UPDATE services SET status = IF(is_active = 1, 'active', 'inactive')");
                
                $this->migrations_applied[] = 'services: status eklendi';
            }
            
        } catch (PDOException $e) {
            error_log('Migration services error: ' . $e->getMessage());
            $this->errors[] = 'services: ' . $e->getMessage();
        }
    }
    
    /**
     * Migration 6: Performans index'leri ekle
     */
    private function addPerformanceIndexes() {
        $indexes = [
            ['contact_messages', 'idx_email_status', 'email, status'],
            ['contact_messages', 'idx_created_status', 'created_at, status'],
            ['blog_posts', 'idx_category_status', 'category_id, status'],
            ['blog_posts', 'idx_status_published', 'status, published_at'],
            ['portfolio_projects', 'idx_category_published', 'category, is_published'],
            ['portfolio_projects', 'idx_featured_published', 'is_featured, is_published'],
            ['site_content', 'idx_page_active', 'page_name, is_active'],
            ['site_content', 'idx_section_active', 'section_name, is_active'],
        ];
        
        foreach ($indexes as $index_info) {
            list($table, $index_name, $columns) = $index_info;
            
            try {
                if (!$this->indexExists($table, $index_name)) {
                    $this->pdo->exec("ALTER TABLE `{$table}` ADD INDEX `{$index_name}` ({$columns})");
                    $this->migrations_applied[] = "{$table}: {$index_name} eklendi";
                }
            } catch (PDOException $e) {
                // Index ekleme hatası - kritik değil, devam et
                error_log("Index creation warning: {$table}.{$index_name} - " . $e->getMessage());
            }
        }
    }
    
    /**
     * Migration log'u yaz
     */
    private function logMigrations() {
        if (count($this->migrations_applied) > 0) {
            $log_entry = date('Y-m-d H:i:s') . ' - Auto-migration executed:' . PHP_EOL;
            $log_entry .= implode(PHP_EOL, $this->migrations_applied) . PHP_EOL;
            error_log($log_entry);
        }
    }
    
    /**
     * Migration'ları zorla çalıştır (cache'i atla)
     */
    public function forceRun() {
        if (file_exists($this->cache_file)) {
            unlink($this->cache_file);
        }
        return $this->run();
    }
    
    /**
     * Uygulanan migration'ları getir
     */
    public function getAppliedMigrations() {
        return $this->migrations_applied;
    }
    
    /**
     * Hataları getir
     */
    public function getErrors() {
        return $this->errors;
    }
}

// Otomatik migration'ı çalıştır (eğer PDO varsa)
if (isset($pdo) && $pdo !== null) {
    try {
        $migration = new AutoMigration($pdo);
        $migration->run();
    } catch (Exception $e) {
        error_log('Auto-migration initialization error: ' . $e->getMessage());
    }
}

?>


