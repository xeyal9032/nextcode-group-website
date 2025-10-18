<?php
// Admin Cache Yönetimi - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

// Cache yönetimi
require_once "../config/admin-cache.php";

$message = "";
$message_type = "";

// Cache temizleme işlemleri
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    
    switch ($action) {
        case "clear_all":
            try {
                AdminCachedData::init();
                AdminCachedData::clearAllCache();
                $message = "Tüm cache dosyaları temizlendi.";
                $message_type = "success";
            } catch (Exception $e) {
                $message = "Cache temizleme hatası: " . $e->getMessage();
                $message_type = "error";
            }
            break;
            
        case "clear_dashboard":
            try {
                AdminCachedData::clearContentCache();
                $message = "Dashboard cache temizlendi.";
                $message_type = "success";
            } catch (Exception $e) {
                $message = "Dashboard cache temizleme hatası: " . $e->getMessage();
                $message_type = "error";
            }
            break;
            
        case "clear_blog":
            try {
                AdminCachedData::clearBlogCache();
                $message = "Blog cache temizlendi.";
                $message_type = "success";
            } catch (Exception $e) {
                $message = "Blog cache temizleme hatası: " . $e->getMessage();
                $message_type = "error";
            }
            break;
            
        case "clear_portfolio":
            try {
                AdminCachedData::clearPortfolioCache();
                $message = "Portfolio cache temizlendi.";
                $message_type = "success";
            } catch (Exception $e) {
                $message = "Portfolio cache temizleme hatası: " . $e->getMessage();
                $message_type = "error";
            }
            break;
            
        case "clear_messages":
            try {
                AdminCachedData::clearMessageCache();
                $message = "Mesaj cache temizlendi.";
                $message_type = "success";
            } catch (Exception $e) {
                $message = "Mesaj cache temizleme hatası: " . $e->getMessage();
                $message_type = "error";
            }
            break;
            
        case "clear_opcache":
            try {
                if (function_exists('opcache_reset')) {
                    $result = opcache_reset();
                    if ($result) {
                        $message = "OPcache başarıyla temizlendi.";
                        $message_type = "success";
                    } else {
                        $message = "OPcache temizleme başarısız.";
                        $message_type = "warning";
                    }
                } else {
                    $message = "OPcache mevcut değil - bu normal bir durumdur.";
                    $message_type = "info";
                }
            } catch (Exception $e) {
                $message = "OPcache hatası: " . $e->getMessage();
                $message_type = "error";
            }
            break;
            
        case "clear_frontend":
            try {
                // Basit frontend cache temizleme
                $cache_dirs = [
                    '../cache/pages/',
                    '../cache/api/',
                    '../cache/assets/',
                    '../temp/'
                ];
                
                $results = [];
                foreach ($cache_dirs as $dir) {
                    if (is_dir($dir)) {
                        $files = glob($dir . '*');
                        $count = 0;
                        foreach ($files as $file) {
                            if (is_file($file)) {
                                if (unlink($file)) {
                                    $count++;
                                }
                            }
                        }
                        $results[] = basename($dir) . ": $count dosya";
                    }
                }
                
                $message = "Frontend cache temizlendi: " . implode(', ', $results);
                $message_type = "success";
                
            } catch (Exception $e) {
                $message = "Frontend cache temizleme hatası: " . $e->getMessage();
                $message_type = "error";
            }
            break;
    }
}

// Cache istatistiklerini al
AdminCachedData::init();

// Cache istatistiklerini manuel olarak hesapla (production uyumlu)
$cache_dir = __DIR__ . '/../cache/admin/';
$files = glob($cache_dir . '*.cache');
$total_files = count($files);
$valid_files = 0;
$expired_files = 0;
$total_size = 0;

foreach ($files as $file) {
    $total_size += filesize($file);
    
    $cache_data = file_get_contents($file);
    $cache = unserialize($cache_data);
    
    if (time() - $cache['timestamp'] < 300) { // 5 minutes cache time
        $valid_files++;
    } else {
        $expired_files++;
    }
}

$cache_stats = [
    'total_files' => $total_files,
    'valid_files' => $valid_files,
    'expired_files' => $expired_files,
    'total_size' => $total_size,
    'cache_time' => 300
];

$cache_health = AdminCachedData::getCacheHealth();

// Cache dosyalarını listele
$cache_files = [];
$cache_dir = __DIR__ . '/../cache/admin/';
if (is_dir($cache_dir)) {
    $files = glob($cache_dir . '*.cache');
    foreach ($files as $file) {
        $cache_files[] = [
            'name' => basename($file),
            'size' => filesize($file),
            'modified' => filemtime($file),
            'age' => time() - filemtime($file)
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Yönetimi | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 20px; 
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .admin-header { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px); 
            padding: 30px; 
            border-radius: 20px; 
            margin-bottom: 30px; 
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); 
            text-align: center; 
        }
        .admin-header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .admin-header p { color: #666; font-size: 1.2em; margin-bottom: 20px; }
        .back-btn { 
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); 
            color: white; 
            padding: 12px 25px; 
            border: none; 
            border-radius: 10px; 
            text-decoration: none; 
            font-weight: bold; 
            transition: all 0.3s; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
        }
        .back-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3); }
        
        .message { 
            padding: 15px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
            font-weight: 600; 
        }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .stat-card { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px); 
            padding: 30px; 
            border-radius: 20px; 
            text-align: center; 
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); 
            transition: transform 0.3s; 
            border: 1px solid rgba(255, 255, 255, 0.2); 
        }
        .stat-card:hover { transform: translateY(-10px); }
        .stat-number { font-size: 3em; font-weight: bold; color: #667eea; margin-bottom: 10px; }
        .stat-label { font-size: 1.2em; color: #333; font-weight: 600; }
        
        .actions-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .action-card { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px); 
            padding: 30px; 
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); 
            transition: transform 0.3s; 
            border: 1px solid rgba(255, 255, 255, 0.2); 
        }
        .action-card:hover { transform: translateY(-5px); }
        .action-card h3 { color: #333; margin-bottom: 15px; font-size: 1.5em; display: flex; align-items: center; gap: 10px; }
        .action-card p { color: #666; margin-bottom: 20px; line-height: 1.6; }
        .action-btn { 
            padding: 12px 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border: none; 
            border-radius: 10px; 
            font-size: 0.9em; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.3s; 
            width: 100%; 
        }
        .action-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3); }
        .action-btn.danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
        .action-btn.danger:hover { box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3); }
        
        .files-table { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px); 
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); 
            overflow: hidden; 
        }
        .files-table h3 { 
            padding: 20px 30px; 
            background: rgba(102, 126, 234, 0.1); 
            color: #333; 
            margin: 0; 
            border-bottom: 1px solid rgba(0, 0, 0, 0.1); 
        }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 15px; text-align: left; border-bottom: 1px solid rgba(0, 0, 0, 0.1); }
        .table th { background: rgba(102, 126, 234, 0.1); font-weight: 600; color: #333; }
        .table tr:hover { background: rgba(102, 126, 234, 0.05); }
        
        @media (max-width: 768px) { 
            .stats-grid { grid-template-columns: 1fr; } 
            .actions-grid { grid-template-columns: 1fr; } 
            .admin-header h1 { font-size: 2em; } 
            .table { font-size: 0.9em; }
            .table th, .table td { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-memory"></i> Cache Yönetimi</h1>
            <p>Admin paneli cache dosyalarını yönetin ve performansı optimize edin</p>
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Dashboard'a Dön
            </a>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : ($message_type === 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $cache_stats['total_files']; ?></div>
                <div class="stat-label"><i class="fas fa-file"></i> Toplam Cache Dosyası</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $cache_stats['valid_files']; ?></div>
                <div class="stat-label"><i class="fas fa-check-circle"></i> Geçerli Cache</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $cache_stats['expired_files']; ?></div>
                <div class="stat-label"><i class="fas fa-clock"></i> Süresi Dolmuş</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($cache_stats['total_size'] / 1024, 1); ?> KB</div>
                <div class="stat-label"><i class="fas fa-hdd"></i> Toplam Boyut</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: <?php echo $cache_health['status'] === 'excellent' ? '#28a745' : ($cache_health['status'] === 'good' ? '#17a2b8' : ($cache_health['status'] === 'fair' ? '#ffc107' : '#dc3545')); ?>;">
                    <?php echo $cache_health['score']; ?>%
                </div>
                <div class="stat-label"><i class="fas fa-heartbeat"></i> Cache Sağlığı</div>
            </div>
        </div>

        <div class="actions-grid">
            <div class="action-card">
                <h3><i class="fas fa-tachometer-alt"></i> Dashboard Cache</h3>
                <p>Dashboard istatistikleri ve genel verilerin cache'ini temizleyin.</p>
                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="action" value="clear_dashboard">
                    <button type="submit" class="action-btn">
                        <i class="fas fa-trash"></i> Dashboard Cache Temizle
                    </button>
                </form>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-blog"></i> Blog Cache</h3>
                <p>Blog yazıları ve kategorilerin cache'ini temizleyin.</p>
                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="action" value="clear_blog">
                    <button type="submit" class="action-btn">
                        <i class="fas fa-trash"></i> Blog Cache Temizle
                    </button>
                </form>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-briefcase"></i> Portfolio Cache</h3>
                <p>Portfolio projelerinin cache'ini temizleyin.</p>
                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="action" value="clear_portfolio">
                    <button type="submit" class="action-btn">
                        <i class="fas fa-trash"></i> Portfolio Cache Temizle
                    </button>
                </form>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-envelope"></i> Mesaj Cache</h3>
                <p>İletişim mesajlarının cache'ini temizleyin.</p>
                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="action" value="clear_messages">
                    <button type="submit" class="action-btn">
                        <i class="fas fa-trash"></i> Mesaj Cache Temizle
                    </button>
                </form>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-code"></i> PHP OPcache</h3>
                <p>PHP OPcache'i temizleyin (sunucu yeniden başlatma gerekebilir).</p>
                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="action" value="clear_opcache">
                    <button type="submit" class="action-btn">
                        <i class="fas fa-trash"></i> OPcache Temizle
                    </button>
                </form>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-globe"></i> Frontend Cache</h3>
                <p>Tüm frontend cache dosyalarını temizler (OPcache, sayfa cache, API cache).</p>
                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="action" value="clear_frontend">
                    <button type="submit" class="action-btn">
                        <i class="fas fa-globe"></i> Frontend Cache Temizle
                    </button>
                </form>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-magic"></i> Otomatik Temizleme</h3>
                <p>Süresi dolmuş cache dosyalarını otomatik olarak temizler.</p>
                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="action" value="auto_clean">
                    <button type="submit" class="action-btn">
                        <i class="fas fa-magic"></i> Otomatik Temizleme
                    </button>
                </form>
            </div>

            <div class="action-card">
                <h3><i class="fas fa-bomb"></i> Tüm Cache</h3>
                <p><strong>Dikkat:</strong> Tüm cache dosyalarını temizler. Bu işlem geri alınamaz!</p>
                <form method="POST" style="margin: 0;" onsubmit="return confirm('Tüm cache dosyalarını silmek istediğinizden emin misiniz?');">
                    <input type="hidden" name="action" value="clear_all">
                    <button type="submit" class="action-btn danger">
                        <i class="fas fa-bomb"></i> Tüm Cache'i Temizle
                    </button>
                </form>
            </div>
        </div>

        <!-- Cache Sağlık Durumu ve Öneriler -->
        <div class="files-table" style="margin-bottom: 30px;">
            <h3><i class="fas fa-heartbeat"></i> Cache Sağlık Durumu</h3>
            <div style="padding: 20px;">
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: <?php echo $cache_health['status'] === 'excellent' ? '#28a745' : ($cache_health['status'] === 'good' ? '#17a2b8' : ($cache_health['status'] === 'fair' ? '#ffc107' : '#dc3545')); ?>; display: flex; align-items: center; justify-content: center; margin-right: 20px;">
                        <i class="fas fa-<?php echo $cache_health['status'] === 'excellent' ? 'check' : ($cache_health['status'] === 'good' ? 'thumbs-up' : ($cache_health['status'] === 'fair' ? 'exclamation-triangle' : 'times')); ?>" style="color: white; font-size: 24px;"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; color: #333;">Cache Sağlık Skoru: <?php echo $cache_health['score']; ?>%</h4>
                        <p style="margin: 5px 0 0 0; color: #666; text-transform: capitalize;">Durum: <?php echo $cache_health['status']; ?></p>
                    </div>
                </div>
                
                <h5 style="color: #333; margin-bottom: 10px;"><i class="fas fa-lightbulb"></i> Öneriler:</h5>
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($cache_health['recommendation'] as $recommendation): ?>
                        <li style="margin-bottom: 8px; color: #666;"><?php echo htmlspecialchars($recommendation); ?></li>
                    <?php endforeach; ?>
                </ul>
                
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #17a2b8;">
                    <h6 style="color: #333; margin-bottom: 10px;"><i class="fas fa-clock"></i> Otomatik Temizleme</h6>
                    <p style="margin: 0; color: #666; font-size: 14px;">
                        Cache dosyalarını otomatik olarak temizlemek için cron job kurabilirsiniz:
                    </p>
                    <code style="display: block; margin-top: 10px; padding: 10px; background: #e9ecef; border-radius: 5px; font-size: 12px;">
                        0,5,10,15,20,25,30,35,40,45,50,55 * * * * /usr/bin/php <?php echo realpath(__DIR__ . '/admin-cache-cleaner.php'); ?>
                    </code>
                    <p style="margin: 10px 0 0 0; color: #666; font-size: 12px;">
                        Bu komut her 5 dakikada bir süresi dolmuş cache dosyalarını temizler.
                    </p>
                </div>
            </div>
        </div>

        <?php if (!empty($cache_files)): ?>
        <div class="files-table">
            <h3><i class="fas fa-list"></i> Cache Dosyaları</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Dosya Adı</th>
                        <th>Boyut</th>
                        <th>Son Değişiklik</th>
                        <th>Yaş</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cache_files as $file): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file['name']); ?></td>
                        <td><?php echo number_format($file['size'] / 1024, 1); ?> KB</td>
                        <td><?php echo date('Y-m-d H:i:s', $file['modified']); ?></td>
                        <td><?php echo $file['age'] < 60 ? $file['age'] . ' saniye' : ($file['age'] < 3600 ? round($file['age'] / 60) . ' dakika' : round($file['age'] / 3600) . ' saat'); ?></td>
                        <td>
                            <?php if ($file['age'] < $cache_stats['cache_time']): ?>
                                <span style="color: #28a745;"><i class="fas fa-check-circle"></i> Geçerli</span>
                            <?php else: ?>
                                <span style="color: #dc3545;"><i class="fas fa-clock"></i> Süresi Dolmuş</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="files-table">
            <h3><i class="fas fa-list"></i> Cache Dosyaları</h3>
            <div style="padding: 30px; text-align: center; color: #666;">
                <i class="fas fa-folder-open" style="font-size: 3em; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>Henüz cache dosyası bulunmuyor.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const cards = document.querySelectorAll(".stat-card, .action-card");
            cards.forEach(card => {
                card.addEventListener("mouseenter", function() { 
                    this.style.transform = "translateY(-10px)"; 
                });
                card.addEventListener("mouseleave", function() { 
                    this.style.transform = "translateY(0)"; 
                });
            });
        });
    </script>
</body>
</html>
