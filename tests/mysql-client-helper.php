<?php
/**
 * NextCode Group - MySQL Client Helper
 * MySQL client için yardımcı fonksiyonlar ve araçlar
 * 
 * @author NextCode Group
 * @version 1.0
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class MySQLClientHelper {
    
    /**
     * Güvenli SQL sorguları için hazır şablonlar
     */
    public static function getQueryTemplates() {
        return [
            'users' => [
                'title' => 'Kullanıcı Sorguları',
                'queries' => [
                    'SELECT * FROM admin_users LIMIT 10;' => 'Admin kullanıcıları listele',
                    'SELECT COUNT(*) as total FROM admin_users;' => 'Toplam admin sayısı',
                    'SELECT username, email, created_at FROM admin_users ORDER BY created_at DESC;' => 'Son kayıt olan adminler'
                ]
            ],
            'blog' => [
                'title' => 'Blog Sorguları',
                'queries' => [
                    'SELECT id, title, status, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 10;' => 'Son blog yazıları',
                    'SELECT COUNT(*) as total FROM blog_posts WHERE status = "published";' => 'Yayınlanmış blog sayısı',
                    'SELECT category, COUNT(*) as count FROM blog_posts GROUP BY category;' => 'Kategori bazında blog sayıları'
                ]
            ],
            'portfolio' => [
                'title' => 'Portföy Sorguları',
                'queries' => [
                    'SELECT id, title, category_name, is_published FROM portfolio_projects ORDER BY sort_order;' => 'Portföy projeleri',
                    'SELECT category_name, COUNT(*) as count FROM portfolio_projects GROUP BY category_name;' => 'Kategori bazında proje sayıları',
                    'SELECT * FROM portfolio_projects WHERE is_published = 1 LIMIT 5;' => 'Yayınlanmış projeler'
                ]
            ],
            'contact' => [
                'title' => 'İletişim Sorguları',
                'queries' => [
                    'SELECT name, email, subject, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 10;' => 'Son mesajlar',
                    'SELECT COUNT(*) as total FROM contact_messages;' => 'Toplam mesaj sayısı',
                    'SELECT COUNT(*) as unread FROM contact_messages WHERE status = "unread";' => 'Okunmamış mesajlar'
                ]
            ],
            'analytics' => [
                'title' => 'Analitik Sorguları',
                'queries' => [
                    'SHOW TABLES;' => 'Tüm tabloları listele',
                    'SELECT table_name, table_rows FROM information_schema.tables WHERE table_schema = DATABASE();' => 'Tablo bilgileri',
                    'SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema = DATABASE();' => 'Toplam tablo sayısı'
                ]
            ]
        ];
    }
    
    /**
     * Yaygın hatalar ve çözümleri
     */
    public static function getCommonErrors() {
        return [
            [
                'error' => 'Table doesn\'t exist',
                'solution' => 'Tablo adını kontrol edin veya SHOW TABLES; komutu ile mevcut tabloları listeleyin',
                'prevention' => 'Tablo adlarını büyük/küçük harf duyarlılığına dikkat ederek yazın'
            ],
            [
                'error' => 'Access denied for user',
                'solution' => 'Veritabanı kullanıcı adı ve şifresini kontrol edin',
                'prevention' => 'Güvenli şifreler kullanın ve düzenli olarak değiştirin'
            ],
            [
                'error' => 'Connection refused',
                'solution' => 'Veritabanı sunucusunun çalıştığından emin olun',
                'prevention' => 'Veritabanı servisini otomatik başlatma için yapılandırın'
            ],
            [
                'error' => 'Too many connections',
                'solution' => 'Bağlantı havuzunu kontrol edin ve gereksiz bağlantıları kapatın',
                'prevention' => 'Bağlantı limitlerini artırın veya connection pooling kullanın'
            ]
        ];
    }
    
    /**
     * Performans optimizasyon önerileri
     */
    public static function getPerformanceTips() {
        return [
            [
                'tip' => 'İndeks Kullanımı',
                'description' => 'Sık kullanılan sütunlarda indeks oluşturun',
                'example' => 'CREATE INDEX idx_email ON users(email);',
                'benefit' => 'Sorgu performansını %90\'a kadar artırabilir'
            ],
            [
                'tip' => 'LIMIT Kullanımı',
                'description' => 'Büyük tablolarda LIMIT kullanarak sonuç sayısını sınırlayın',
                'example' => 'SELECT * FROM large_table LIMIT 100;',
                'benefit' => 'Bellek kullanımını azaltır ve hızlı sonuç alırsınız'
            ],
            [
                'tip' => 'Gereksiz Sütunları Seçmeyin',
                'description' => 'Sadece ihtiyacınız olan sütunları SELECT ile seçin',
                'example' => 'SELECT id, name FROM users; -- * yerine',
                'benefit' => 'Ağ trafiğini ve bellek kullanımını azaltır'
            ],
            [
                'tip' => 'WHERE Koşulları',
                'description' => 'İndekslenmiş sütunlarda WHERE koşulları kullanın',
                'example' => 'SELECT * FROM users WHERE email = "test@example.com";',
                'benefit' => 'Tam tablo taraması yerine indeks taraması yapar'
            ]
        ];
    }
    
    /**
     * Yedekleme komutları
     */
    public static function getBackupCommands() {
        return [
            'full_backup' => [
                'command' => 'mysqldump -u username -p database_name > backup_full_' . date('Y-m-d_H-i-s') . '.sql',
                'description' => 'Tam veritabanı yedeği'
            ],
            'structure_only' => [
                'command' => 'mysqldump -u username -p --no-data database_name > backup_structure_' . date('Y-m-d') . '.sql',
                'description' => 'Sadece tablo yapıları (veri olmadan)'
            ],
            'data_only' => [
                'command' => 'mysqldump -u username -p --no-create-info database_name > backup_data_' . date('Y-m-d') . '.sql',
                'description' => 'Sadece veriler (tablo yapısı olmadan)'
            ],
            'single_table' => [
                'command' => 'mysqldump -u username -p database_name table_name > backup_table_' . date('Y-m-d') . '.sql',
                'description' => 'Tek tablo yedeği'
            ]
        ];
    }
    
    /**
     * Geri yükleme komutları
     */
    public static function getRestoreCommands() {
        return [
            'full_restore' => [
                'command' => 'mysql -u username -p database_name < backup_file.sql',
                'description' => 'Tam yedekten geri yükleme'
            ],
            'selective_restore' => [
                'command' => 'mysql -u username -p database_name -e "source backup_file.sql"',
                'description' => 'Seçici geri yükleme'
            ]
        ];
    }
    
    /**
     * Güvenlik kontrolleri
     */
    public static function getSecurityChecks() {
        return [
            [
                'check' => 'Boş şifre kontrolü',
                'query' => "SELECT user, host FROM mysql.user WHERE authentication_string = '';",
                'action' => 'Boş şifreli kullanıcıları kaldırın veya şifre verin'
            ],
            [
                'check' => 'Root uzaktan erişim',
                'query' => "SELECT user, host FROM mysql.user WHERE user = 'root' AND host != 'localhost';",
                'action' => 'Root kullanıcısının uzaktan erişimini kısıtlayın'
            ],
            [
                'check' => 'Güvenli olmayan şifreler',
                'query' => "SELECT user, host FROM mysql.user WHERE authentication_string = PASSWORD('password');",
                'action' => 'Güçlü şifreler kullanın'
            ]
        ];
    }
    
    /**
     * Sistem bilgileri
     */
    public static function getSystemInfo() {
        return [
            'mysql_version' => 'SELECT VERSION() as mysql_version;',
            'database_size' => 'SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS "DB Size in MB" FROM information_schema.tables WHERE table_schema = DATABASE();',
            'table_count' => 'SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = DATABASE();',
            'connection_info' => 'SHOW STATUS LIKE "Connections";',
            'process_list' => 'SHOW PROCESSLIST;'
        ];
    }
    
    /**
     * Tablo optimizasyon komutları
     */
    public static function getOptimizationCommands() {
        return [
            [
                'command' => 'OPTIMIZE TABLE table_name;',
                'description' => 'Tabloyu optimize eder ve fragmantasyonu azaltır'
            ],
            [
                'command' => 'ANALYZE TABLE table_name;',
                'description' => 'Tablo istatistiklerini günceller'
            ],
            [
                'command' => 'CHECK TABLE table_name;',
                'description' => 'Tablo bütünlüğünü kontrol eder'
            ],
            [
                'command' => 'REPAIR TABLE table_name;',
                'description' => 'Bozuk tabloları onarır'
            ]
        ];
    }
    
    /**
     * İndeks yönetimi
     */
    public static function getIndexManagement() {
        return [
            'show_indexes' => 'SHOW INDEX FROM table_name;',
            'create_index' => 'CREATE INDEX index_name ON table_name (column_name);',
            'drop_index' => 'DROP INDEX index_name ON table_name;',
            'analyze_index' => 'ANALYZE TABLE table_name;'
        ];
    }
}

// Yardımcı fonksiyonlar
function formatBytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    return round($size, $precision) . ' ' . $units[$i];
}

function formatNumber($number) {
    return number_format($number, 0, ',', '.');
}

function getTableStatus($pdo, $tableName) {
    try {
        $stmt = $pdo->query("SHOW TABLE STATUS LIKE '$tableName'");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function getTableInfo($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT 
                table_name,
                table_rows,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                ROUND((data_length / 1024 / 1024), 2) AS data_mb,
                ROUND((index_length / 1024 / 1024), 2) AS index_mb
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
            ORDER BY (data_length + index_length) DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// MySQL Client için yardımcı JavaScript fonksiyonları
function getMySQLClientJS() {
    return "
    // MySQL Client Yardımcı Fonksiyonları
    
    // Sorgu şablonlarını yükle
    function loadQueryTemplates() {
        const templates = " . json_encode(MySQLClientHelper::getQueryTemplates()) . ";
        return templates;
    }
    
    // Hızlı sorgu çalıştır
    function quickQuery(query) {
        document.getElementById('queryInput').value = query;
        executeQuery();
    }
    
    // Tablo bilgilerini göster
    function showTableInfo(tableName) {
        const info = document.createElement('div');
        info.className = 'table-info-popup';
        info.innerHTML = \`
            <div class='popup-header'>
                <h3>\${tableName} Tablo Bilgileri</h3>
                <button onclick='this.parentElement.parentElement.remove()'>×</button>
            </div>
            <div class='popup-content'>
                <p>Tablo detayları yükleniyor...</p>
            </div>
        \`;
        document.body.appendChild(info);
    }
    
    // Sonuçları CSV olarak indir
    function downloadCSV(data, filename) {
        if (!data || data.length === 0) return;
        
        const headers = Object.keys(data[0]);
        const csvContent = [
            headers.join(','),
            ...data.map(row => headers.map(header => \`\"\${row[header] || ''}\"\`).join(','))
        ].join('\\n');
        
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename || 'mysql_export_' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
        window.URL.revokeObjectURL(url);
    }
    ";
}

// CSS stilleri
function getMySQLClientCSS() {
    return "
    .table-info-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border-radius: 10px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        z-index: 1000;
        min-width: 400px;
        max-width: 80vw;
        max-height: 80vh;
        overflow: auto;
    }
    
    .popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #f7fafc;
        border-radius: 10px 10px 0 0;
    }
    
    .popup-header h3 {
        margin: 0;
        color: #2d3748;
    }
    
    .popup-header button {
        background: #e53e3e;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
    }
    
    .popup-content {
        padding: 20px;
    }
    
    .query-template {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        margin: 10px 0;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .query-template:hover {
        background: #edf2f7;
        border-color: #cbd5e0;
    }
    
    .query-template .query-code {
        font-family: 'Fira Code', monospace;
        background: #2d3748;
        color: #e2e8f0;
        padding: 10px;
        border-radius: 5px;
        margin: 10px 0;
        font-size: 0.9rem;
    }
    
    .query-template .query-desc {
        color: #4a5568;
        font-size: 0.9rem;
        margin-top: 5px;
    }
    ";
}

// MySQL Client için yardımcı HTML bileşenleri
function getMySQLClientHTML() {
    return "
    <!-- Sorgu Şablonları -->
    <div class='client-card' id='templatesCard' style='display: none;'>
        <h3><i class='fas fa-code'></i> Sorgu Şablonları</h3>
        <div id='templatesContent'></div>
    </div>
    
    <!-- Performans İpuçları -->
    <div class='client-card' id='performanceCard' style='display: none;'>
        <h3><i class='fas fa-tachometer-alt'></i> Performans İpuçları</h3>
        <div id='performanceContent'></div>
    </div>
    
    <!-- Güvenlik Kontrolleri -->
    <div class='client-card' id='securityCard' style='display: none;'>
        <h3><i class='fas fa-shield-alt'></i> Güvenlik Kontrolleri</h3>
        <div id='securityContent'></div>
    </div>
    ";
}
?>






