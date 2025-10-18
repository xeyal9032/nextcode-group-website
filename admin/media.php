<?php
// Media Management - NextCode Group
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

// Veritabanı bağlantısı
$pdo = getSecureDatabaseConnection();
$media_files = [];
$total_files = 0;
$total_size = 0;

if ($pdo) {
    try {
        // Medya dosyalarını çek
        $stmt = $pdo->query("
            SELECT id, filename, original_name, file_type, file_size, upload_path, 
                   created_at, uploaded_by, is_active 
            FROM media_files 
            ORDER BY created_at DESC
        ");
        $media_files = $stmt->fetchAll();
        
        $total_files = count($media_files);
        $total_size = array_sum(array_column($media_files, 'file_size'));
        
        // Dosya boyutunu formatla
        function formatFileSize($bytes) {
            if ($bytes >= 1073741824) {
                return round($bytes / 1073741824, 2) . ' GB';
            } elseif ($bytes >= 1048576) {
                return round($bytes / 1048576, 2) . ' MB';
            } elseif ($bytes >= 1024) {
                return round($bytes / 1024, 2) . ' KB';
            } else {
                return $bytes . ' bytes';
            }
        }
        
    } catch (Exception $e) {
        $media_files = [];
    }
}
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medya Yönetimi | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="js/admin-ajax.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #2c3e50; margin-bottom: 10px; font-size: 28px; }
        .header p { color: #7f8c8d; font-size: 16px; }
        
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 32px; font-weight: bold; color: #3498db; margin-bottom: 10px; }
        .stat-label { color: #7f8c8d; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        
        .media-actions { background: white; padding: 25px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .upload-area { border: 2px dashed #bdc3c7; border-radius: 15px; padding: 40px; text-align: center; margin-bottom: 20px; transition: all 0.3s ease; }
        .upload-area:hover { border-color: #3498db; background: #f8f9fa; }
        .upload-area.dragover { border-color: #3498db; background: #e3f2fd; }
        .upload-icon { font-size: 48px; color: #bdc3c7; margin-bottom: 20px; }
        .upload-text { color: #7f8c8d; font-size: 18px; margin-bottom: 15px; }
        .upload-btn { background: #3498db; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-size: 16px; transition: all 0.3s ease; }
        .upload-btn:hover { background: #2980b9; }
        
        .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .media-item { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s ease; }
        .media-item:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
        .media-preview { width: 100%; height: 200px; object-fit: cover; }
        .media-info { padding: 15px; }
        .media-name { font-weight: bold; color: #2c3e50; margin-bottom: 8px; word-break: break-word; }
        .media-meta { color: #7f8c8d; font-size: 12px; margin-bottom: 5px; }
        .media-actions { display: flex; gap: 10px; margin-top: 15px; }
        .btn { padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #229954; }
        
        .file-input { display: none; }
        .progress-bar { width: 100%; height: 4px; background: #ecf0f1; border-radius: 2px; overflow: hidden; margin-top: 10px; }
        .progress-fill { height: 100%; background: #3498db; transition: width 0.3s ease; }
        
        .filter-tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .filter-tab { padding: 10px 20px; border: 2px solid #ecf0f1; border-radius: 8px; background: white; cursor: pointer; transition: all 0.3s ease; }
        .filter-tab.active { border-color: #3498db; background: #3498db; color: white; }
        .filter-tab:hover { border-color: #3498db; }
        
        @media (max-width: 768px) {
            .media-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
            .container { padding: 15px; }
            .header { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-images"></i> Medya Yönetimi</h1>
            <p>Fotoğrafları, videoları ve diğer medya dosyalarını yönetin.</p>
        </div>

        <!-- Statistics -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_files; ?></div>
                <div class="stat-label">Toplam Dosya</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo formatFileSize($total_size); ?></div>
                <div class="stat-label">Toplam Boyut</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($media_files, function($f) { return $f['is_active'] ?? 0 == 1; })); ?></div>
                <div class="stat-label">Aktif Dosya</div>
            </div>
        </div>

        <!-- Upload Area -->
        <div class="media-actions">
            <h3><i class="fas fa-upload"></i> Dosya Yükle</h3>
            
            <div class="upload-area" id="uploadArea">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="upload-text">
                    Dosyalarınızı buraya sürükleyin veya seçmek için tıklayın
                </div>
                <button type="button" class="upload-btn" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-plus"></i> Dosya Seç
                </button>
                <input type="file" id="fileInput" class="file-input" multiple accept="image/*,video/*,.pdf,.doc,.docx">
                <div class="progress-bar" id="progressBar" style="display: none;">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
            </div>
            
            <div class="filter-tabs">
                <div class="filter-tab active" data-filter="all">
                    <i class="fas fa-th"></i> Tümü
                </div>
                <div class="filter-tab" data-filter="image">
                    <i class="fas fa-image"></i> Resimler
                </div>
                <div class="filter-tab" data-filter="video">
                    <i class="fas fa-video"></i> Videolar
                </div>
                <div class="filter-tab" data-filter="document">
                    <i class="fas fa-file"></i> Belgeler
                </div>
            </div>
        </div>

        <!-- Media Grid -->
        <div class="media-grid" id="mediaGrid">
            <?php if (!empty($media_files)): ?>
                <?php foreach ($media_files as $file): ?>
                    <div class="media-item" data-type="<?php echo strpos($file['file_type'] ?? '', 'image/') === 0 ? 'image' : (strpos($file['file_type'] ?? '', 'video/') === 0 ? 'video' : 'document'); ?>">
                        <?php if (strpos($file['file_type'] ?? '', 'image/') === 0): ?>
                            <img src="../<?php echo htmlspecialchars($file['upload_path'] ?? '' ?? ''); ?>" alt="<?php echo htmlspecialchars($file['original_name'] ?? '' ?? ''); ?>" class="media-preview">
                        <?php elseif (strpos($file['file_type'] ?? '', 'video/') === 0): ?>
                            <video class="media-preview" controls>
                                <source src="../<?php echo htmlspecialchars($file['upload_path'] ?? '' ?? ''); ?>" type="<?php echo htmlspecialchars($file['file_type'] ?? '' ?? ''); ?>">
                            </video>
                        <?php else: ?>
                            <div class="media-preview" style="display: flex; align-items: center; justify-content: center; background: #ecf0f1;">
                                <i class="fas fa-file" style="font-size: 48px; color: #bdc3c7;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="media-info">
                            <div class="media-name"><?php echo htmlspecialchars($file['original_name'] ?? '' ?? ''); ?></div>
                            <div class="media-meta">
                                <i class="fas fa-calendar"></i> <?php echo date('d.m.Y H:i', strtotime($file['created_at'] ?? '')); ?>
                            </div>
                            <div class="media-meta">
                                <i class="fas fa-weight-hanging"></i> <?php echo formatFileSize($file['file_size'] ?? 0); ?>
                            </div>
                            <div class="media-meta">
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($file['file_type'] ?? '' ?? ''); ?>
                            </div>
                            
                            <div class="media-actions">
                                <button class="btn btn-primary" onclick="copyToClipboard('<?php echo htmlspecialchars($file['upload_path'] ?? '' ?? ''); ?>')">
                                    <i class="fas fa-copy"></i> Kopyala
                                </button>
                                <a href="../<?php echo htmlspecialchars($file['upload_path'] ?? '' ?? ''); ?>" target="_blank" class="btn btn-success">
                                    <i class="fas fa-eye"></i> Görüntüle
                                </a>
                                <button class="btn btn-danger" onclick="deleteMedia(<?php echo $file['id'] ?? 0; ?>)">
                                    <i class="fas fa-trash"></i> Sil
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #7f8c8d;">
                    <i class="fas fa-images" style="font-size: 64px; margin-bottom: 20px; opacity: 0.5;"></i>
                    <h3>Henüz medya dosyası yüklenmemiş</h3>
                    <p>İlk medya dosyanızı yüklemek için yukarıdaki alanı kullanın.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Drag and drop functionality
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('fileInput');
            
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                handleFiles(files);
            });
            
            fileInput.addEventListener('change', function(e) {
                handleFiles(e.target.files);
            });
            
            // Filter tabs
            const filterTabs = document.querySelectorAll('.filter-tab');
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.dataset.filter;
                    filterMedia(filter);
                });
            });
        });
        
        function handleFiles(files) {
            if (files.length === 0) return;
            
            const progressBar = document.getElementById('progressBar');
            const progressFill = document.getElementById('progressFill');
            
            progressBar.style.display = 'block';
            
            Array.from(files).forEach((file, index) => {
                uploadFile(file, (progress) => {
                    progressFill.style.width = progress + '%';
                });
            });
        }
        
        function uploadFile(file, progressCallback) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('action', 'upload_media');
            
            fetch('ajax-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    adminAJAX.showNotification('Dosya başarıyla yüklendi', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    adminAJAX.showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                adminAJAX.showNotification('Yükleme hatası: ' + error.message, 'error');
            });
        }
        
        function filterMedia(filter) {
            const mediaItems = document.querySelectorAll('.media-item');
            
            mediaItems.forEach(item => {
                if (filter === 'all' || item.dataset.type === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                adminAJAX.showNotification('URL kopyalandı', 'success');
            }).catch(() => {
                adminAJAX.showNotification('Kopyalama hatası', 'error');
            });
        }
        
        function deleteMedia(fileId) {
            if (!confirm('Bu dosyayı silmek istediğinizden emin misiniz?')) {
                return;
            }
            
            fetch('ajax-handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete_media&file_id=${fileId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    adminAJAX.showNotification('Dosya silindi', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    adminAJAX.showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                adminAJAX.showNotification('Silme hatası: ' + error.message, 'error');
            });
        }
    </script>
</body>
</html>
