<?php
/**
 * NextCode Group - Advanced MySQL Client
 * Gelişmiş veritabanı yönetim aracı
 * 
 * @author NextCode Group
 * @version 1.0
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
require_once 'config/security.php';
require_once 'mysql-client-helper.php';

// Sayfa başlığı
$page_title = 'Advanced MySQL Client - NextCode Group';
$current_page = 'mysql-client-advanced';

// AJAX isteklerini işle
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $response = [];
    
    switch ($action) {
        case 'get_query_templates':
            $response = [
                'success' => true,
                'templates' => MySQLClientHelper::getQueryTemplates()
            ];
            break;
            
        case 'get_performance_tips':
            $response = [
                'success' => true,
                'tips' => MySQLClientHelper::getPerformanceTips()
            ];
            break;
            
        case 'get_security_checks':
            $response = [
                'success' => true,
                'checks' => MySQLClientHelper::getSecurityChecks()
            ];
            break;
            
        case 'get_system_info':
            $response = [
                'success' => true,
                'info' => MySQLClientHelper::getSystemInfo()
            ];
            break;
            
        case 'export_table':
            $tableName = $_POST['table'] ?? '';
            $format = $_POST['format'] ?? 'csv';
            
            if ($tableName) {
                try {
                    $stmt = $pdo->query("SELECT * FROM `$tableName`");
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if ($format === 'csv') {
                        $csv = '';
                        if (!empty($data)) {
                            $headers = array_keys($data[0]);
                            $csv .= implode(',', $headers) . "\n";
                            foreach ($data as $row) {
                                $csv .= implode(',', array_map(function($field) {
                                    return '"' . str_replace('"', '""', $field) . '"';
                                }, $row)) . "\n";
                            }
                        }
                        
                        header('Content-Type: text/csv');
                        header('Content-Disposition: attachment; filename="' . $tableName . '_' . date('Y-m-d') . '.csv"');
                        echo $csv;
                        exit;
                    }
                    
                    $response = [
                        'success' => true,
                        'data' => $data,
                        'count' => count($data)
                    ];
                } catch (PDOException $e) {
                    $response = [
                        'success' => false,
                        'message' => 'Export hatası: ' . $e->getMessage()
                    ];
                }
            } else {
                $response = ['success' => false, 'message' => 'Tablo adı gerekli'];
            }
            break;
            
        case 'optimize_table':
            $tableName = $_POST['table'] ?? '';
            if ($tableName) {
                try {
                    $stmt = $pdo->query("OPTIMIZE TABLE `$tableName`");
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $response = [
                        'success' => true,
                        'message' => 'Tablo optimize edildi',
                        'result' => $result
                    ];
                } catch (PDOException $e) {
                    $response = [
                        'success' => false,
                        'message' => 'Optimize hatası: ' . $e->getMessage()
                    ];
                }
            } else {
                $response = ['success' => false, 'message' => 'Tablo adı gerekli'];
            }
            break;
            
        case 'analyze_table':
            $tableName = $_POST['table'] ?? '';
            if ($tableName) {
                try {
                    $stmt = $pdo->query("ANALYZE TABLE `$tableName`");
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $response = [
                        'success' => true,
                        'message' => 'Tablo analiz edildi',
                        'result' => $result
                    ];
                } catch (PDOException $e) {
                    $response = [
                        'success' => false,
                        'message' => 'Analiz hatası: ' . $e->getMessage()
                    ];
                }
            } else {
                $response = ['success' => false, 'message' => 'Tablo adı gerekli'];
            }
            break;
            
        case 'check_table':
            $tableName = $_POST['table'] ?? '';
            if ($tableName) {
                try {
                    $stmt = $pdo->query("CHECK TABLE `$tableName`");
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $response = [
                        'success' => true,
                        'message' => 'Tablo kontrol edildi',
                        'result' => $result
                    ];
                } catch (PDOException $e) {
                    $response = [
                        'success' => false,
                        'message' => 'Kontrol hatası: ' . $e->getMessage()
                    ];
                }
            } else {
                $response = ['success' => false, 'message' => 'Tablo adı gerekli'];
            }
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
/* Advanced MySQL Client Stilleri */
.advanced-mysql-client {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    min-height: 100vh;
    padding: 20px 0;
}

.advanced-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 20px;
}

.advanced-header {
    text-align: center;
    margin-bottom: 30px;
    color: white;
}

.advanced-header h1 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 15px;
    text-shadow: 0 4px 8px rgba(0,0,0,0.3);
    background: linear-gradient(45deg, #00d4ff, #667eea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.advanced-header p {
    font-size: 1.2rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
}

.advanced-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.advanced-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.advanced-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
}

.advanced-card h3 {
    color: #2d3748;
    margin-bottom: 25px;
    font-size: 1.6rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.advanced-card h3 i {
    color: #667eea;
    font-size: 1.4rem;
}

.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.feature-item {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
}

.feature-item:hover {
    transform: translateX(5px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.1);
}

.feature-title {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
    font-size: 1.1rem;
}

.feature-desc {
    color: #4a5568;
    font-size: 0.9rem;
    line-height: 1.5;
}

.template-category {
    margin-bottom: 25px;
}

.template-category h4 {
    color: #2d3748;
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e2e8f0;
}

.template-query {
    background: #2d3748;
    color: #e2e8f0;
    padding: 15px;
    border-radius: 8px;
    font-family: 'Fira Code', monospace;
    font-size: 0.9rem;
    margin: 10px 0;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid #4a5568;
}

.template-query:hover {
    background: #4a5568;
    border-color: #667eea;
    transform: translateY(-2px);
}

.template-desc {
    color: #718096;
    font-size: 0.85rem;
    margin-top: 5px;
    font-style: italic;
}

.btn-advanced {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 5px;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-advanced:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.btn-advanced:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-secondary {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
}

.btn-success {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
}

.btn-warning {
    background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
}

.btn-danger {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.stat-label {
    font-size: 1rem;
    font-weight: 500;
    opacity: 0.9;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: rgba(255,255,255,0.2);
    border-radius: 4px;
    overflow: hidden;
    margin: 10px 0;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #00d4ff, #ffffff);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.alert-advanced {
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 500;
    border-left: 4px solid;
}

.alert-info {
    background: linear-gradient(135deg, #bee3f8 0%, #90cdf4 100%);
    color: #2a4365;
    border-left-color: #3182ce;
}

.alert-success {
    background: linear-gradient(135deg, #c6f6d5 0%, #9ae6b4 100%);
    color: #22543d;
    border-left-color: #38a169;
}

.alert-warning {
    background: linear-gradient(135deg, #fef5e7 0%, #fbd38d 100%);
    color: #744210;
    border-left-color: #ed8936;
}

.alert-error {
    background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
    color: #742a2a;
    border-left-color: #e53e3e;
}

.table-actions {
    display: flex;
    gap: 10px;
    margin: 15px 0;
    flex-wrap: wrap;
}

@media (max-width: 1200px) {
    .advanced-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .advanced-grid {
        grid-template-columns: 1fr;
    }
    
    .advanced-header h1 {
        font-size: 2.5rem;
    }
    
    .feature-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
}
</style>

<div class="advanced-mysql-client">
    <div class="advanced-container">
        <!-- Header -->
        <div class="advanced-header">
            <h1><i class="fas fa-database"></i> Advanced MySQL Client</h1>
            <p>Profesyonel veritabanı yönetim aracı - Gelişmiş özellikler ve optimizasyon araçları</p>
        </div>

        <!-- İstatistikler -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-number" id="tableCount">-</div>
                <div class="stat-label">Toplam Tablo</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalRecords">-</div>
                <div class="stat-label">Toplam Kayıt</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="dbSize">-</div>
                <div class="stat-label">Veritabanı Boyutu (MB)</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="connections">-</div>
                <div class="stat-label">Aktif Bağlantı</div>
            </div>
        </div>

        <!-- Ana Grid -->
        <div class="advanced-grid">
            <!-- Sorgu Şablonları -->
            <div class="advanced-card">
                <h3><i class="fas fa-code"></i> Sorgu Şablonları</h3>
                <div id="queryTemplates">
                    <div class="loading"></div> Şablonlar yükleniyor...
                </div>
            </div>

            <!-- Performans İpuçları -->
            <div class="advanced-card">
                <h3><i class="fas fa-tachometer-alt"></i> Performans İpuçları</h3>
                <div id="performanceTips">
                    <div class="loading"></div> İpuçları yükleniyor...
                </div>
            </div>

            <!-- Güvenlik Kontrolleri -->
            <div class="advanced-card">
                <h3><i class="fas fa-shield-alt"></i> Güvenlik Kontrolleri</h3>
                <div id="securityChecks">
                    <div class="loading"></div> Kontroller yükleniyor...
                </div>
            </div>
        </div>

        <!-- Tablo Yönetimi -->
        <div class="advanced-card">
            <h3><i class="fas fa-table"></i> Gelişmiş Tablo Yönetimi</h3>
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="feature-title">Tablo Optimizasyonu</div>
                    <div class="feature-desc">Tabloları optimize edin ve performansı artırın</div>
                    <button class="btn-advanced" onclick="optimizeSelectedTable()">
                        <i class="fas fa-magic"></i> Optimize Et
                    </button>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Tablo Analizi</div>
                    <div class="feature-desc">Tablo istatistiklerini güncelleyin</div>
                    <button class="btn-advanced" onclick="analyzeSelectedTable()">
                        <i class="fas fa-chart-line"></i> Analiz Et
                    </button>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Bütünlük Kontrolü</div>
                    <div class="feature-desc">Tablo bütünlüğünü kontrol edin</div>
                    <button class="btn-advanced" onclick="checkSelectedTable()">
                        <i class="fas fa-check-circle"></i> Kontrol Et
                    </button>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Veri Dışa Aktarma</div>
                    <div class="feature-desc">Tabloları CSV formatında dışa aktarın</div>
                    <button class="btn-advanced" onclick="exportSelectedTable()">
                        <i class="fas fa-download"></i> Dışa Aktar
                    </button>
                </div>
            </div>
            
            <div class="table-actions">
                <select class="table-selector" id="tableSelector">
                    <option value="">Tablo seçin...</option>
                </select>
                <button class="btn-advanced" onclick="refreshTables()">
                    <i class="fas fa-sync"></i> Yenile
                </button>
            </div>
        </div>

        <!-- Sistem Bilgileri -->
        <div class="advanced-card">
            <h3><i class="fas fa-info-circle"></i> Sistem Bilgileri</h3>
            <div id="systemInfo">
                <div class="loading"></div> Sistem bilgileri yükleniyor...
            </div>
        </div>

        <!-- Yedekleme Araçları -->
        <div class="advanced-card">
            <h3><i class="fas fa-archive"></i> Yedekleme Araçları</h3>
            <div class="alert-advanced alert-info">
                <h4>Yedekleme Komutları:</h4>
                <div style="margin-top: 15px;">
                    <strong>Tam Yedekleme:</strong><br>
                    <code style="background: #2d3748; color: #e2e8f0; padding: 8px; border-radius: 4px; display: block; margin: 5px 0;">
                        mysqldump -u username -p database_name > backup_full_<?php echo date('Y-m-d_H-i-s'); ?>.sql
                    </code>
                    
                    <strong>Yapı Yedekleme:</strong><br>
                    <code style="background: #2d3748; color: #e2e8f0; padding: 8px; border-radius: 4px; display: block; margin: 5px 0;">
                        mysqldump -u username -p --no-data database_name > backup_structure_<?php echo date('Y-m-d'); ?>.sql
                    </code>
                    
                    <strong>Veri Yedekleme:</strong><br>
                    <code style="background: #2d3748; color: #e2e8f0; padding: 8px; border-radius: 4px; display: block; margin: 5px 0;">
                        mysqldump -u username -p --no-create-info database_name > backup_data_<?php echo date('Y-m-d'); ?>.sql
                    </code>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Advanced MySQL Client JavaScript
class AdvancedMySQLClient {
    constructor() {
        this.init();
    }
    
    init() {
        this.loadStats();
        this.loadQueryTemplates();
        this.loadPerformanceTips();
        this.loadSecurityChecks();
        this.loadSystemInfo();
        this.loadTables();
    }
    
    async makeRequest(action, data = {}) {
        try {
            const formData = new FormData();
            formData.append('action', action);
            
            for (const key in data) {
                formData.append(key, data[key]);
            }
            
            const response = await fetch('mysql-client-advanced.php', {
                method: 'POST',
                body: formData
            });
            
            return await response.json();
        } catch (error) {
            console.error('Request error:', error);
            return { success: false, message: 'İstek hatası: ' + error.message };
        }
    }
    
    async loadStats() {
        try {
            // Basit istatistik yükleme
            const result = await this.makeRequest('get_system_info');
            if (result.success) {
                document.getElementById('tableCount').textContent = '15'; // Örnek
                document.getElementById('totalRecords').textContent = '2,450'; // Örnek
                document.getElementById('dbSize').textContent = '12.5'; // Örnek
                document.getElementById('connections').textContent = '3'; // Örnek
            }
        } catch (error) {
            console.error('Stats loading error:', error);
        }
    }
    
    async loadQueryTemplates() {
        const result = await this.makeRequest('get_query_templates');
        const container = document.getElementById('queryTemplates');
        
        if (result.success) {
            let html = '';
            for (const [category, data] of Object.entries(result.templates)) {
                html += `
                    <div class="template-category">
                        <h4>${data.title}</h4>
                        ${Object.entries(data.queries).map(([query, desc]) => `
                            <div class="template-query" onclick="quickQuery('${query}')">
                                ${query}
                                <div class="template-desc">${desc}</div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            container.innerHTML = html;
        } else {
            container.innerHTML = `<div class="alert-advanced alert-error">${result.message}</div>`;
        }
    }
    
    async loadPerformanceTips() {
        const result = await this.makeRequest('get_performance_tips');
        const container = document.getElementById('performanceTips');
        
        if (result.success) {
            const html = result.tips.map(tip => `
                <div class="feature-item">
                    <div class="feature-title">${tip.tip}</div>
                    <div class="feature-desc">${tip.description}</div>
                    <div style="background: #2d3748; color: #e2e8f0; padding: 10px; border-radius: 5px; margin-top: 10px; font-family: 'Fira Code', monospace; font-size: 0.8rem;">
                        ${tip.example}
                    </div>
                    <div style="color: #38a169; font-size: 0.85rem; margin-top: 5px; font-weight: 500;">
                        ${tip.benefit}
                    </div>
                </div>
            `).join('');
            
            container.innerHTML = html;
        } else {
            container.innerHTML = `<div class="alert-advanced alert-error">${result.message}</div>`;
        }
    }
    
    async loadSecurityChecks() {
        const result = await this.makeRequest('get_security_checks');
        const container = document.getElementById('securityChecks');
        
        if (result.success) {
            const html = result.checks.map(check => `
                <div class="feature-item">
                    <div class="feature-title">${check.check}</div>
                    <div class="feature-desc">${check.action}</div>
                    <div style="background: #2d3748; color: #e2e8f0; padding: 10px; border-radius: 5px; margin-top: 10px; font-family: 'Fira Code', monospace; font-size: 0.8rem;">
                        ${check.query}
                    </div>
                </div>
            `).join('');
            
            container.innerHTML = html;
        } else {
            container.innerHTML = `<div class="alert-advanced alert-error">${result.message}</div>`;
        }
    }
    
    async loadSystemInfo() {
        const container = document.getElementById('systemInfo');
        container.innerHTML = `
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="feature-title">MySQL Sürümü</div>
                    <div class="feature-desc">SELECT VERSION();</div>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Veritabanı Boyutu</div>
                    <div class="feature-desc">Tablo boyutlarını görüntüleyin</div>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Bağlantı Bilgileri</div>
                    <div class="feature-desc">Aktif bağlantıları kontrol edin</div>
                </div>
                <div class="feature-item">
                    <div class="feature-title">İşlem Listesi</div>
                    <div class="feature-desc">Çalışan sorguları görüntüleyin</div>
                </div>
            </div>
        `;
    }
    
    async loadTables() {
        // Tablo listesini yükle
        const select = document.getElementById('tableSelector');
        select.innerHTML = '<option value="">Tablo seçin...</option>';
        
        // Örnek tablolar
        const tables = ['admin_users', 'blog_posts', 'portfolio_projects', 'contact_messages', 'services', 'faq'];
        tables.forEach(table => {
            select.innerHTML += `<option value="${table}">${table}</option>`;
        });
    }
    
    async optimizeSelectedTable() {
        const tableName = document.getElementById('tableSelector').value;
        if (!tableName) {
            alert('Lütfen bir tablo seçin');
            return;
        }
        
        const result = await this.makeRequest('optimize_table', { table: tableName });
        if (result.success) {
            alert(`Tablo ${tableName} başarıyla optimize edildi!`);
        } else {
            alert('Hata: ' + result.message);
        }
    }
    
    async analyzeSelectedTable() {
        const tableName = document.getElementById('tableSelector').value;
        if (!tableName) {
            alert('Lütfen bir tablo seçin');
            return;
        }
        
        const result = await this.makeRequest('analyze_table', { table: tableName });
        if (result.success) {
            alert(`Tablo ${tableName} başarıyla analiz edildi!`);
        } else {
            alert('Hata: ' + result.message);
        }
    }
    
    async checkSelectedTable() {
        const tableName = document.getElementById('tableSelector').value;
        if (!tableName) {
            alert('Lütfen bir tablo seçin');
            return;
        }
        
        const result = await this.makeRequest('check_table', { table: tableName });
        if (result.success) {
            alert(`Tablo ${tableName} kontrolü tamamlandı!`);
        } else {
            alert('Hata: ' + result.message);
        }
    }
    
    async exportSelectedTable() {
        const tableName = document.getElementById('tableSelector').value;
        if (!tableName) {
            alert('Lütfen bir tablo seçin');
            return;
        }
        
        const result = await this.makeRequest('export_table', { table: tableName, format: 'csv' });
        if (result.success) {
            alert(`Tablo ${tableName} başarıyla dışa aktarıldı!`);
        } else {
            alert('Hata: ' + result.message);
        }
    }
    
    refreshTables() {
        this.loadTables();
    }
}

// Global fonksiyonlar
function quickQuery(query) {
    // Ana MySQL client'a yönlendir
    window.open(`mysql-client.php?query=${encodeURIComponent(query)}`, '_blank');
}

// Sayfa yüklendiğinde Advanced MySQL Client'ı başlat
document.addEventListener('DOMContentLoaded', function() {
    new AdvancedMySQLClient();
});
</script>

<?php
// Footer include
require_once 'includes/footer.php';
?>






