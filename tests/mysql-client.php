<?php
/**
 * NextCode Group - MySQL Client
 * Web tabanlı MySQL veritabanı yönetim aracı
 * 
 * @author NextCode Group
 * @version 1.0
 * @since 2024
 */

// Güvenlik kontrolü
define('SECURE_ACCESS', true);
session_start();

// Admin giriş kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin/login.php');
    exit;
}

// Veritabanı bağlantısı
require_once 'config/database.php';

// Güvenlik fonksiyonları
require_once 'config/security.php';

// MySQL Client sınıfı
class NextCodeMySQLClient {
    private $pdo;
    private $host;
    private $database;
    private $username;
    private $charset = 'utf8mb4';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->database = $_ENV['DB_NAME'] ?? 'nextcode_db';
        $this->username = $_ENV['DB_USER'] ?? 'root';
    }
    
    /**
     * Veritabanı bağlantısını test et
     */
    public function testConnection() {
        try {
            $stmt = $this->pdo->query("SELECT 1");
            return [
                'success' => true,
                'message' => 'Veritabanı bağlantısı başarılı',
                'info' => [
                    'host' => $this->host,
                    'database' => $this->database,
                    'username' => $this->username,
                    'charset' => $this->charset
                ]
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Veritabanı bağlantı hatası: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Tüm tabloları listele
     */
    public function getTables() {
        try {
            $stmt = $this->pdo->query("SHOW TABLES");
            $tables = [];
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            return [
                'success' => true,
                'tables' => $tables,
                'count' => count($tables)
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Tablo listesi alınamadı: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Tablo yapısını getir
     */
    public function getTableStructure($tableName) {
        try {
            // Tablo yapısı
            $stmt = $this->pdo->query("DESCRIBE `$tableName`");
            $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Tablo bilgileri
            $stmt = $this->pdo->query("SHOW TABLE STATUS LIKE '$tableName'");
            $tableInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Kayıt sayısı
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM `$tableName`");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            return [
                'success' => true,
                'table' => $tableName,
                'structure' => $structure,
                'info' => $tableInfo,
                'record_count' => $count
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Tablo yapısı alınamadı: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Tablo verilerini getir
     */
    public function getTableData($tableName, $limit = 50, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM `$tableName` LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Toplam kayıt sayısı
            $countStmt = $this->pdo->query("SELECT COUNT(*) as count FROM `$tableName`");
            $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            return [
                'success' => true,
                'table' => $tableName,
                'data' => $data,
                'total_count' => $totalCount,
                'limit' => $limit,
                'offset' => $offset
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Veri alınamadı: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * SQL sorgusu çalıştır
     */
    public function executeQuery($query) {
        try {
            // Güvenlik kontrolü
            $query = trim($query);
            $queryUpper = strtoupper($query);
            
            // Tehlikeli komutları engelle
            $dangerousCommands = ['DROP', 'DELETE', 'TRUNCATE', 'ALTER', 'CREATE', 'INSERT', 'UPDATE'];
            foreach ($dangerousCommands as $cmd) {
                if (strpos($queryUpper, $cmd) === 0) {
                    return [
                        'success' => false,
                        'message' => "Güvenlik nedeniyle '$cmd' komutu engellendi. Sadece SELECT sorgularına izin verilir."
                    ];
                }
            }
            
            // SELECT sorgusu kontrolü
            if (strpos($queryUpper, 'SELECT') !== 0) {
                return [
                    'success' => false,
                    'message' => 'Sadece SELECT sorgularına izin verilir.'
                ];
            }
            
            $stmt = $this->pdo->query($query);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'data' => $data,
                'row_count' => count($data),
                'query' => $query
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Sorgu hatası: ' . $e->getMessage(),
                'query' => $query
            ];
        }
    }
    
    /**
     * Veritabanı istatistikleri
     */
    public function getDatabaseStats() {
        try {
            $stats = [];
            
            // Tablo sayısı
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE()");
            $stats['table_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Toplam kayıt sayısı
            $stmt = $this->pdo->query("SELECT SUM(table_rows) as total_rows FROM information_schema.tables WHERE table_schema = DATABASE()");
            $stats['total_records'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_rows'];
            
            // Veritabanı boyutu
            $stmt = $this->pdo->query("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB' FROM information_schema.tables WHERE table_schema = DATABASE()");
            $stats['database_size'] = $stmt->fetch(PDO::FETCH_ASSOC)['DB Size in MB'];
            
            // En büyük tablolar
            $stmt = $this->pdo->query("SELECT table_name, table_rows, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size in MB' FROM information_schema.tables WHERE table_schema = DATABASE() ORDER BY (data_length + index_length) DESC LIMIT 5");
            $stats['largest_tables'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'stats' => $stats
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'İstatistik alınamadı: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Tablo indekslerini getir
     */
    public function getTableIndexes($tableName) {
        try {
            $stmt = $this->pdo->query("SHOW INDEX FROM `$tableName`");
            $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'table' => $tableName,
                'indexes' => $indexes
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'İndeks bilgisi alınamadı: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Yedekleme önerileri
     */
    public function getBackupRecommendations() {
        $recommendations = [
            'Önemli tablolar: blog_posts, portfolio_projects, contact_messages',
            'Düzenli yedekleme: Günlük otomatik yedekleme önerilir',
            'Yedekleme komutu: mysqldump -u ' . $this->username . ' -p ' . $this->database . ' > backup_' . date('Y-m-d') . '.sql',
            'Geri yükleme: mysql -u ' . $this->username . ' -p ' . $this->database . ' < backup_file.sql'
        ];
        
        return [
            'success' => true,
            'recommendations' => $recommendations
        ];
    }
}

// Sayfa başlığı
$page_title = 'MySQL Client - NextCode Group';
$current_page = 'mysql-client';

// MySQL Client instance
$mysqlClient = new NextCodeMySQLClient($pdo);

// AJAX isteklerini işle
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $response = [];
    
    switch ($action) {
        case 'test_connection':
            $response = $mysqlClient->testConnection();
            break;
            
        case 'get_tables':
            $response = $mysqlClient->getTables();
            break;
            
        case 'get_table_structure':
            $tableName = $_POST['table'] ?? '';
            if ($tableName) {
                $response = $mysqlClient->getTableStructure($tableName);
            } else {
                $response = ['success' => false, 'message' => 'Tablo adı gerekli'];
            }
            break;
            
        case 'get_table_data':
            $tableName = $_POST['table'] ?? '';
            $limit = (int)($_POST['limit'] ?? 50);
            $offset = (int)($_POST['offset'] ?? 0);
            if ($tableName) {
                $response = $mysqlClient->getTableData($tableName, $limit, $offset);
            } else {
                $response = ['success' => false, 'message' => 'Tablo adı gerekli'];
            }
            break;
            
        case 'execute_query':
            $query = $_POST['query'] ?? '';
            if ($query) {
                $response = $mysqlClient->executeQuery($query);
            } else {
                $response = ['success' => false, 'message' => 'Sorgu gerekli'];
            }
            break;
            
        case 'get_stats':
            $response = $mysqlClient->getDatabaseStats();
            break;
            
        case 'get_indexes':
            $tableName = $_POST['table'] ?? '';
            if ($tableName) {
                $response = $mysqlClient->getTableIndexes($tableName);
            } else {
                $response = ['success' => false, 'message' => 'Tablo adı gerekli'];
            }
            break;
            
        case 'get_backup_info':
            $response = $mysqlClient->getBackupRecommendations();
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Geçersiz işlem'];
    }
    
    echo json_encode($response);
    exit;
}

// Header include
require_once 'includes/header.php';
?>

<style>
/* MySQL Client Özel Stilleri */
.mysql-client {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px 0;
}

.client-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

.client-header {
    text-align: center;
    margin-bottom: 30px;
    color: white;
}

.client-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.client-header p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.client-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.client-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.client-card h3 {
    color: #2d3748;
    margin-bottom: 20px;
    font-size: 1.4rem;
    font-weight: 600;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
}

.status-success {
    background: #c6f6d5;
    color: #22543d;
}

.status-error {
    background: #fed7d7;
    color: #742a2a;
}

.btn-client {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 5px;
}

.btn-client:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-client:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.table-selector {
    width: 100%;
    padding: 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    margin-bottom: 15px;
    transition: border-color 0.3s ease;
}

.table-selector:focus {
    outline: none;
    border-color: #667eea;
}

.query-input {
    width: 100%;
    min-height: 120px;
    padding: 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-family: 'Fira Code', monospace;
    font-size: 0.9rem;
    resize: vertical;
    margin-bottom: 15px;
}

.query-input:focus {
    outline: none;
    border-color: #667eea;
}

.result-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.result-table th,
.result-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.result-table th {
    background: #f7fafc;
    font-weight: 600;
    color: #2d3748;
}

.result-table tr:hover {
    background: #f7fafc;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    border: 1px solid #e2e8f0;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 5px;
}

.stat-label {
    color: #4a5568;
    font-size: 0.9rem;
    font-weight: 500;
}

.loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-success {
    background: #c6f6d5;
    color: #22543d;
    border: 1px solid #9ae6b4;
}

.alert-error {
    background: #fed7d7;
    color: #742a2a;
    border: 1px solid #feb2b2;
}

.alert-info {
    background: #bee3f8;
    color: #2a4365;
    border: 1px solid #90cdf4;
}

@media (max-width: 768px) {
    .client-grid {
        grid-template-columns: 1fr;
    }
    
    .client-header h1 {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
}
</style>

<div class="mysql-client">
    <div class="client-container">
        <!-- Header -->
        <div class="client-header">
            <h1><i class="fas fa-database"></i> NextCode MySQL Client</h1>
            <p>Veritabanı yönetim aracı - Güvenli ve kullanıcı dostu arayüz</p>
        </div>

        <!-- Bağlantı Durumu -->
        <div class="client-card" id="connectionCard">
            <h3><i class="fas fa-plug"></i> Bağlantı Durumu</h3>
            <div id="connectionStatus">
                <div class="loading"></div> Bağlantı test ediliyor...
            </div>
        </div>

        <!-- İstatistikler -->
        <div class="client-card" id="statsCard">
            <h3><i class="fas fa-chart-bar"></i> Veritabanı İstatistikleri</h3>
            <div id="databaseStats">
                <div class="loading"></div> İstatistikler yükleniyor...
            </div>
        </div>

        <!-- Tablo Seçici -->
        <div class="client-grid">
            <div class="client-card">
                <h3><i class="fas fa-table"></i> Tablo Yönetimi</h3>
                <select class="table-selector" id="tableSelector">
                    <option value="">Tablo seçin...</option>
                </select>
                
                <button class="btn-client" onclick="loadTableStructure()">
                    <i class="fas fa-info-circle"></i> Tablo Yapısı
                </button>
                <button class="btn-client" onclick="loadTableData()">
                    <i class="fas fa-eye"></i> Verileri Görüntüle
                </button>
                <button class="btn-client" onclick="loadTableIndexes()">
                    <i class="fas fa-key"></i> İndeksler
                </button>
                
                <div id="tableInfo"></div>
            </div>

            <div class="client-card">
                <h3><i class="fas fa-code"></i> SQL Sorgu Çalıştırıcı</h3>
                <textarea class="query-input" id="queryInput" placeholder="SELECT * FROM users LIMIT 10;"></textarea>
                
                <button class="btn-client" onclick="executeQuery()">
                    <i class="fas fa-play"></i> Sorguyu Çalıştır
                </button>
                <button class="btn-client" onclick="clearQuery()">
                    <i class="fas fa-trash"></i> Temizle
                </button>
                
                <div id="queryResult"></div>
            </div>
        </div>

        <!-- Yedekleme Bilgileri -->
        <div class="client-card">
            <h3><i class="fas fa-download"></i> Yedekleme Önerileri</h3>
            <div id="backupInfo">
                <div class="loading"></div> Yedekleme bilgileri yükleniyor...
            </div>
        </div>

        <!-- Tablo Verileri Gösterici -->
        <div class="client-card" id="tableDataCard" style="display: none;">
            <h3><i class="fas fa-table"></i> Tablo Verileri</h3>
            <div id="tableDataContent"></div>
        </div>
    </div>
</div>

<script>
// MySQL Client JavaScript
class NextCodeMySQLClient {
    constructor() {
        this.init();
    }
    
    init() {
        this.testConnection();
        this.loadTables();
        this.loadStats();
        this.loadBackupInfo();
    }
    
    async makeRequest(action, data = {}) {
        try {
            const formData = new FormData();
            formData.append('action', action);
            
            for (const key in data) {
                formData.append(key, data[key]);
            }
            
            const response = await fetch('mysql-client.php', {
                method: 'POST',
                body: formData
            });
            
            return await response.json();
        } catch (error) {
            console.error('Request error:', error);
            return { success: false, message: 'İstek hatası: ' + error.message };
        }
    }
    
    async testConnection() {
        const result = await this.makeRequest('test_connection');
        const statusDiv = document.getElementById('connectionStatus');
        
        if (result.success) {
            statusDiv.innerHTML = `
                <div class="status-indicator status-success">
                    <i class="fas fa-check-circle"></i> ${result.message}
                </div>
                <div style="margin-top: 10px; font-size: 0.9rem; color: #4a5568;">
                    <strong>Host:</strong> ${result.info.host} | 
                    <strong>Database:</strong> ${result.info.database} | 
                    <strong>User:</strong> ${result.info.username}
                </div>
            `;
        } else {
            statusDiv.innerHTML = `
                <div class="status-indicator status-error">
                    <i class="fas fa-exclamation-triangle"></i> ${result.message}
                </div>
            `;
        }
    }
    
    async loadTables() {
        const result = await this.makeRequest('get_tables');
        const select = document.getElementById('tableSelector');
        
        if (result.success) {
            select.innerHTML = '<option value="">Tablo seçin...</option>';
            result.tables.forEach(table => {
                select.innerHTML += `<option value="${table}">${table}</option>`;
            });
        }
    }
    
    async loadStats() {
        const result = await this.makeRequest('get_stats');
        const statsDiv = document.getElementById('databaseStats');
        
        if (result.success) {
            const stats = result.stats;
            statsDiv.innerHTML = `
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">${stats.table_count}</div>
                        <div class="stat-label">Tablo Sayısı</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.total_records || 0}</div>
                        <div class="stat-label">Toplam Kayıt</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.database_size || 0}</div>
                        <div class="stat-label">Boyut (MB)</div>
                    </div>
                </div>
                ${stats.largest_tables.length > 0 ? `
                    <h4 style="margin-top: 20px; margin-bottom: 10px;">En Büyük Tablolar:</h4>
                    <div class="result-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tablo</th>
                                    <th>Kayıt Sayısı</th>
                                    <th>Boyut (MB)</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${stats.largest_tables.map(table => `
                                    <tr>
                                        <td>${table.table_name}</td>
                                        <td>${table.table_rows}</td>
                                        <td>${table['Size in MB']}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                ` : ''}
            `;
        } else {
            statsDiv.innerHTML = `<div class="alert alert-error">${result.message}</div>`;
        }
    }
    
    async loadTableStructure() {
        const tableName = document.getElementById('tableSelector').value;
        if (!tableName) {
            alert('Lütfen bir tablo seçin');
            return;
        }
        
        const result = await this.makeRequest('get_table_structure', { table: tableName });
        const infoDiv = document.getElementById('tableInfo');
        
        if (result.success) {
            infoDiv.innerHTML = `
                <div class="alert alert-info">
                    <strong>${result.table}</strong> - ${result.record_count} kayıt
                </div>
                <div class="result-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Alan</th>
                                <th>Tip</th>
                                <th>Null</th>
                                <th>Key</th>
                                <th>Varsayılan</th>
                                <th>Ekstra</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${result.structure.map(field => `
                                <tr>
                                    <td><strong>${field.Field}</strong></td>
                                    <td>${field.Type}</td>
                                    <td>${field.Null}</td>
                                    <td>${field.Key}</td>
                                    <td>${field.Default || '-'}</td>
                                    <td>${field.Extra || '-'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            infoDiv.innerHTML = `<div class="alert alert-error">${result.message}</div>`;
        }
    }
    
    async loadTableData() {
        const tableName = document.getElementById('tableSelector').value;
        if (!tableName) {
            alert('Lütfen bir tablo seçin');
            return;
        }
        
        const result = await this.makeRequest('get_table_data', { 
            table: tableName, 
            limit: 50, 
            offset: 0 
        });
        
        if (result.success) {
            const card = document.getElementById('tableDataCard');
            const content = document.getElementById('tableDataContent');
            
            if (result.data.length > 0) {
                const columns = Object.keys(result.data[0]);
                
                content.innerHTML = `
                    <div class="alert alert-info">
                        <strong>${result.table}</strong> - ${result.total_count} toplam kayıt, 50 kayıt gösteriliyor
                    </div>
                    <div class="result-table">
                        <table>
                            <thead>
                                <tr>
                                    ${columns.map(col => `<th>${col}</th>`).join('')}
                                </tr>
                            </thead>
                            <tbody>
                                ${result.data.map(row => `
                                    <tr>
                                        ${columns.map(col => `<td>${row[col] || '-'}</td>`).join('')}
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                content.innerHTML = `<div class="alert alert-info">Bu tabloda veri bulunamadı</div>`;
            }
            
            card.style.display = 'block';
        }
    }
    
    async loadTableIndexes() {
        const tableName = document.getElementById('tableSelector').value;
        if (!tableName) {
            alert('Lütfen bir tablo seçin');
            return;
        }
        
        const result = await this.makeRequest('get_indexes', { table: tableName });
        const infoDiv = document.getElementById('tableInfo');
        
        if (result.success) {
            if (result.indexes.length > 0) {
                infoDiv.innerHTML = `
                    <div class="alert alert-info">
                        <strong>${result.table}</strong> - İndeks Bilgileri
                    </div>
                    <div class="result-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>İndeks</th>
                                    <th>Alan</th>
                                    <th>Sıra</th>
                                    <th>Tip</th>
                                    <th>Benzersiz</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${result.indexes.map(idx => `
                                    <tr>
                                        <td><strong>${idx.Key_name}</strong></td>
                                        <td>${idx.Column_name}</td>
                                        <td>${idx.Seq_in_index}</td>
                                        <td>${idx.Index_type}</td>
                                        <td>${idx.Non_unique == 0 ? 'Evet' : 'Hayır'}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                infoDiv.innerHTML = `<div class="alert alert-info">Bu tabloda indeks bulunamadı</div>`;
            }
        } else {
            infoDiv.innerHTML = `<div class="alert alert-error">${result.message}</div>`;
        }
    }
    
    async executeQuery() {
        const query = document.getElementById('queryInput').value.trim();
        if (!query) {
            alert('Lütfen bir SQL sorgusu girin');
            return;
        }
        
        const result = await this.makeRequest('execute_query', { query: query });
        const resultDiv = document.getElementById('queryResult');
        
        if (result.success) {
            if (result.data.length > 0) {
                const columns = Object.keys(result.data[0]);
                
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        Sorgu başarıyla çalıştırıldı - ${result.row_count} sonuç
                    </div>
                    <div class="result-table">
                        <table>
                            <thead>
                                <tr>
                                    ${columns.map(col => `<th>${col}</th>`).join('')}
                                </tr>
                            </thead>
                            <tbody>
                                ${result.data.map(row => `
                                    <tr>
                                        ${columns.map(col => `<td>${row[col] || '-'}</td>`).join('')}
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-info">
                        Sorgu başarıyla çalıştırıldı - Sonuç bulunamadı
                    </div>
                `;
            }
        } else {
            resultDiv.innerHTML = `<div class="alert alert-error">${result.message}</div>`;
        }
    }
    
    async loadBackupInfo() {
        const result = await this.makeRequest('get_backup_info');
        const backupDiv = document.getElementById('backupInfo');
        
        if (result.success) {
            backupDiv.innerHTML = `
                <div class="alert alert-info">
                    <h4>Yedekleme Önerileri:</h4>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        ${result.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                    </ul>
                </div>
            `;
        } else {
            backupDiv.innerHTML = `<div class="alert alert-error">${result.message}</div>`;
        }
    }
    
    clearQuery() {
        document.getElementById('queryInput').value = '';
        document.getElementById('queryResult').innerHTML = '';
    }
}

// Sayfa yüklendiğinde MySQL Client'ı başlat
document.addEventListener('DOMContentLoaded', function() {
    new NextCodeMySQLClient();
});

// Tablo seçici değiştiğinde
document.getElementById('tableSelector').addEventListener('change', function() {
    document.getElementById('tableInfo').innerHTML = '';
    document.getElementById('tableDataCard').style.display = 'none';
});

// Enter tuşu ile sorgu çalıştırma
document.getElementById('queryInput').addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'Enter') {
        executeQuery();
    }
});
</script>

<?php
// Footer include
require_once 'includes/footer.php';
?>






