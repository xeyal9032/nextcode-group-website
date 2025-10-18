<?php
/**
 * NextCode Group - Secure Database Configuration
 * Environment variables kullanarak güvenli veritabanı bağlantısı
 */

// Check if secure access is defined
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

// Environment helper'ı dahil et
require_once __DIR__ . '/env-helper.php';

// Environment variables'ı yükle
EnvHelper::load();

class SecureDatabase {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    
    public function __construct() {
        // Environment'dan güvenli şekilde bilgileri al
        $config = EnvHelper::getDatabaseConfig();
        
        $this->host = $config['host'];
        $this->db_name = $config['name'];
        $this->username = $config['user'];
        $this->password = $config['pass'];
    }
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 10,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
            
            // Test bağlantı
            $this->conn->query('SELECT 1');
            
            return $this->conn;
        } catch(PDOException $exception) {
            // Güvenlik için detaylı hata bilgilerini log'a yaz
            error_log('Database connection failed: ' . $exception->getMessage());
            error_log('Database host: ' . $this->host);
            error_log('Database name: ' . $this->db_name);
            error_log('Database user: ' . $this->username);
            
            // Production'da genel hata mesajı döndür
            if (EnvHelper::get('APP_ENV', 'production') === 'production') {
                error_log('Database connection error in production');
                return null;
            }
            
            // Development'ta daha detaylı hata
            throw new Exception('Database connection failed: ' . $exception->getMessage());
        }
    }
    
    /**
     * Güvenli sorgu çalıştırma
     */
    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Query execution failed: ' . $e->getMessage());
            error_log('SQL: ' . $sql);
            throw new Exception('Query execution failed');
        }
    }
    
    /**
     * Güvenli veri ekleme
     */
    public function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log('Insert failed: ' . $e->getMessage());
            throw new Exception('Insert operation failed');
        }
    }
    
    /**
     * Güvenli veri güncelleme
     */
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        foreach (array_keys($data) as $key) {
            $setClause[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array_merge($data, $whereParams));
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log('Update failed: ' . $e->getMessage());
            throw new Exception('Update operation failed');
        }
    }
    
    /**
     * Güvenli veri silme
     */
    public function delete($table, $where, $whereParams = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($whereParams);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log('Delete failed: ' . $e->getMessage());
            throw new Exception('Delete operation failed');
        }
    }
    
    /**
     * Bağlantıyı kapat
     */
    public function close() {
        $this->conn = null;
    }
}

// Create secure database instance
try {
    $secureDatabase = new SecureDatabase();
    $pdo = $secureDatabase->getConnection();
} catch (Exception $e) {
    error_log('Secure database connection failed: ' . $e->getMessage());
    $pdo = null;
}

// Legacy compatibility için global $pdo değişkenini koru
if ($pdo) {
    // Mevcut database.php'deki tablo oluşturma kodlarını koru
    try {
        // Create blog_categories table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS blog_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                slug VARCHAR(255) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        
        // Diğer tablolar için mevcut kodu koru...
        // (Mevcut database.php'deki tüm tablo oluşturma kodları buraya eklenebilir)
        
    } catch (PDOException $e) {
        error_log('Database setup failed: ' . $e->getMessage());
    }
}
?>


