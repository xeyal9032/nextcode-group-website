<?php
/**
 * Admin Panel Database Configuration
 * NextCode Group - Advanced Admin System
 */

class AdminDatabase {
    private $host = 'gtorg.mysql.tools';
    private $db_name = 'gtorg_nextcode';
    private $username = 'gtorg_nextcode';
    private $password = ';849#dVEyg';
    private $conn;
    
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
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                ]
            );
            
            return $this->conn;
        } catch(PDOException $exception) {
            error_log('Admin Database connection failed: ' . $exception->getMessage());
            return null;
        }
    }
    
    public function createAdminTables() {
        if (!$this->conn) {
            $this->getConnection();
        }
        
        if (!$this->conn) {
            return false;
        }
        
        try {
            // Admin users table
            $this->conn->exec('
                CREATE TABLE IF NOT EXISTS admin_users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    password_hash VARCHAR(255) NOT NULL,
                    full_name VARCHAR(255),
                    role ENUM("super_admin", "admin", "editor") DEFAULT "admin",
                    is_active TINYINT(1) DEFAULT 1,
                    last_login TIMESTAMP NULL,
                    login_attempts INT DEFAULT 0,
                    locked_until TIMESTAMP NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_username (username),
                    INDEX idx_email (email)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Admin sessions table
            $this->conn->exec('
                CREATE TABLE IF NOT EXISTS admin_sessions (
                    id VARCHAR(128) PRIMARY KEY,
                    user_id INT NOT NULL,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    expires_at TIMESTAMP NOT NULL,
                    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
                    INDEX idx_user_id (user_id),
                    INDEX idx_expires (expires_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Admin activity log
            $this->conn->exec('
                CREATE TABLE IF NOT EXISTS admin_activity_log (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT,
                    action VARCHAR(100) NOT NULL,
                    resource VARCHAR(255),
                    details JSON,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE SET NULL,
                    INDEX idx_user_id (user_id),
                    INDEX idx_action (action),
                    INDEX idx_created (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // File manager table
            $this->conn->exec('
                CREATE TABLE IF NOT EXISTS admin_files (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    filename VARCHAR(255) NOT NULL,
                    original_name VARCHAR(255) NOT NULL,
                    file_path VARCHAR(500) NOT NULL,
                    file_size INT NOT NULL,
                    mime_type VARCHAR(100),
                    file_type ENUM("image", "document", "code", "other") DEFAULT "other",
                    uploaded_by INT,
                    is_system_file TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (uploaded_by) REFERENCES admin_users(id) ON DELETE SET NULL,
                    INDEX idx_filename (filename),
                    INDEX idx_file_type (file_type),
                    INDEX idx_uploaded_by (uploaded_by)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            return true;
        } catch (PDOException $e) {
            error_log('Admin table creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public function createDefaultAdmin() {
        if (!$this->conn) {
            $this->getConnection();
        }
        
        if (!$this->conn) {
            return false;
        }
        
        try {
            // Check if admin exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM admin_users WHERE role = 'super_admin'");
            $stmt->execute();
            $count = $stmt->fetchColumn();
            
            if ($count == 0) {
                // Create default super admin
                $password_hash = password_hash('Admin123!@#NextCode', PASSWORD_ARGON2ID);
                $stmt = $this->conn->prepare("
                    INSERT INTO admin_users (username, email, password_hash, full_name, role, is_active) 
                    VALUES (?, ?, ?, ?, 'super_admin', 1)
                ");
                $stmt->execute(['admin', 'admin@nextcodegroup.com', $password_hash, 'Super Administrator']);
                return true;
            }
            
            return true;
        } catch (PDOException $e) {
            error_log('Default admin creation failed: ' . $e->getMessage());
            return false;
        }
    }
}

// Initialize database
$adminDb = new AdminDatabase();
$pdo = $adminDb->getConnection();

if ($pdo) {
    $adminDb->createAdminTables();
    $adminDb->createDefaultAdmin();
}
?>
