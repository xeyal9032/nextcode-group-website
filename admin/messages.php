<?php
// Güvenli Mesaj Yönetimi - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// Giriş kontrolü
checkAdminPermission('admin');

require_once "../config/database.php";

// Mesaj değişkenlerini başlat
$success_message = '';
$error_message = '';

// Başarılı işlem mesajları
if (isset($_GET['success'])) {
    switch ($_GET['success'] ?? '') {
        case '1':
            $success_message = "Mesaj durumu başarıyla güncellendi!";
            break;
    }
}

// Hata mesajları
if (isset($_GET['error'])) {
    $error_message = $_GET['error'] ?? '';
}

// Silme mesajları
if (isset($_GET['deleted'])) {
    $success_message = "Mesaj başarıyla silindi!";
}

// Mesaj verilerini çek
$messages = [];
$total_messages = 0;
$unread_messages = 0;

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        $stmt = $pdo->query("SELECT id, first_name, last_name, email, subject, message, status, created_at FROM contact_messages ORDER BY created_at DESC");
        $messages = $stmt->fetchAll();
        $total_messages = count($messages);
        $unread_messages = count(array_filter($messages, function($m) { return $m['status'] ?? 'unread' == 'unread'; }));
    }
} catch (Exception $e) {
    $error_message = "Veritabanı hatası: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesaj Yönetimi | NextCode Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="js/admin-ajax.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #333; font-size: 2.5em; margin-bottom: 10px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 15px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .stat-number { font-size: 2.5em; font-weight: bold; color: #667eea; margin-bottom: 10px; }
        .stat-label { color: #666; font-size: 1.1em; }
        .messages-grid { display: grid; gap: 20px; }
        .message-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .message-card:hover { transform: translateY(-3px); }
        .message-card.unread { border-left: 5px solid #667eea; }
        .message-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .message-sender { font-weight: bold; color: #333; font-size: 1.1em; }
        .message-date { color: #999; font-size: 0.9em; }
        .message-subject { font-weight: bold; color: #667eea; margin-bottom: 10px; }
        .message-content { color: #666; line-height: 1.6; margin-bottom: 15px; }
        .message-meta { display: flex; justify-content: space-between; align-items: center; }
        .message-email { color: #667eea; font-size: 0.9em; }
        .message-actions { display: flex; gap: 10px; }
        .btn { padding: 8px 15px; border: none; border-radius: 8px; text-decoration: none; font-weight: bold; transition: all 0.3s; display: inline-flex; align-items: center; gap: 5px; font-size: 0.9em; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; }
        .btn-danger { background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 3px 10px rgba(0,0,0,0.2); }
        .back-btn { background: #6c757d; color: white; padding: 12px 25px; border: none; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .back-btn:hover { background: #5a6268; color: white; }
        .unread-badge { background: #dc3545; color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        @media (max-width: 768px) {
            .message-header { flex-direction: column; align-items: flex-start; gap: 10px; }
            .message-meta { flex-direction: column; gap: 10px; align-items: flex-start; }
            .message-actions { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Dashboard'a Dön
        </a>
        
        <div class="header">
            <h1><i class="fas fa-envelope"></i> Mesaj Yönetimi</h1>
            <p>İletişim formu mesajlarını görüntüleyin ve yönetin.</p>
        </div>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success" style="margin-bottom: 20px; background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger" style="margin-bottom: 20px; background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_messages; ?></div>
                <div class="stat-label">Toplam Mesaj</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $unread_messages; ?></div>
                <div class="stat-label">Okunmamış</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_messages - $unread_messages; ?></div>
                <div class="stat-label">Okunmuş</div>
            </div>
        </div>
        
        <div class="messages-grid">
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message-card <?php echo $message['status'] ?? 'unread' == 'unread' ? 'unread' : ''; ?>" data-message-id="<?php echo $message['id'] ?? 0; ?>">
                        <div class="message-header">
                            <div class="message-sender">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($message['first_name'] ?? '' . ' ' . $message['last_name'] ?? ''); ?>
                                <?php if ($message['status'] ?? 'unread' == 'unread'): ?>
                                    <span class="unread-badge">YENİ</span>
                                <?php endif; ?>
                            </div>
                            <div class="message-date">
                                <?php echo date('d.m.Y H:i', strtotime($message['created_at'] ?? '')); ?>
                            </div>
                        </div>
                        
                        <div class="message-subject">
                            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($message['subject'] ?? '' ?? ''); ?>
                        </div>
                        
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($message['message'] ?? '' ?? '')); ?>
                        </div>
                        
                        <div class="message-meta">
                            <div class="message-email">
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($message['email'] ?? '' ?? ''); ?>
                            </div>
                            <div class="message-actions">
                                <a href="mailto:<?php echo htmlspecialchars($message['email'] ?? '' ?? ''); ?>" class="btn btn-success">
                                    <i class="fas fa-reply"></i> Yanıtla
                                </a>
                                <?php if ($message['status'] ?? 'unread' == 'unread'): ?>
                                <button class="btn btn-primary" 
                                        data-ajax-action="update_message_status" 
                                        data-target-id="<?php echo $message['id'] ?? 0; ?>" 
                                        data-status="<?php echo $message['status'] ?? 'unread' == 'unread' ? 'read' : 'unread'; ?>">
                                    <i class="fas fa-eye<?php echo $message['status'] ?? 'unread' == 'unread' ? '' : '-slash'; ?>"></i> 
                                    <?php echo $message['status'] ?? 'unread' == 'unread' ? 'Okundu İşaretle' : 'Okunmadı İşaretle'; ?>
                                </button>
                                <?php else: ?>
                                <button class="btn btn-warning" 
                                        data-ajax-action="update_message_status" 
                                        data-target-id="<?php echo $message['id'] ?? 0; ?>" 
                                        data-status="unread">
                                    <i class="fas fa-eye-slash"></i> Okunmadı İşaretle
                                </button>
                                <?php endif; ?>
                                <button class="btn btn-danger" 
                                        data-ajax-action="delete_message" 
                                        data-target-id="<?php echo $message['id'] ?? 0; ?>">
                                    <i class="fas fa-trash"></i> Sil
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 50px; background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <i class="fas fa-inbox" style="font-size: 4em; color: #ccc; margin-bottom: 20px;"></i>
                    <h3 style="color: #666; margin-bottom: 10px;">Henüz Mesaj Bulunmuyor</h3>
                    <p style="color: #999;">İletişim formundan gelen mesajlar burada görünecek.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>