<?php
// Admin Panel AJAX Handler - NextCode Group
define("SECURE_ACCESS", true);

// Güvenlik fonksiyonlarını dahil et
require_once "includes/security.php";

// JSON header
header('Content-Type: application/json');

// CORS headers for AJAX
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Giriş kontrolü
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// CSRF Token kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($csrf_token)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid CSRF token'
        ]);
        exit;
    }
}

// Veritabanı bağlantısı
require_once "../config/database.php";

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'update_message_status':
            $message_id = (int)($_POST['message_id'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? '');
            
            if ($message_id && in_array($status, ['read', 'unread', 'archived'])) {
                $stmt = $pdo->prepare("UPDATE contact_messages SET status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$status, $message_id]);
                
                logAuditEvent('CONTENT', 'MESSAGE_STATUS_UPDATED', "Message ID: $message_id, Status: $status", 'INFO');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Message status updated successfully',
                    'data' => ['id' => $message_id, 'status' => $status]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid parameters'
                ]);
            }
            break;
            
        case 'delete_message':
            $message_id = (int)($_POST['message_id'] ?? 0);
            
            if ($message_id) {
                $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
                $stmt->execute([$message_id]);
                
                logAuditEvent('CONTENT', 'MESSAGE_DELETED', "Message ID: $message_id", 'INFO');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Message deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid message ID'
                ]);
            }
            break;
            
        case 'mark_message_read':
            $message_id = (int)($_POST['message_id'] ?? 0);
            
            if ($message_id) {
                $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
                $stmt->execute([$message_id]);
                
                logAuditEvent('CONTACT', 'MESSAGE_MARKED_READ', "Message ID: $message_id", 'INFO');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Message marked as read'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid message ID'
                ]);
            }
            break;
            
        case 'mark_message_unread':
            $message_id = (int)($_POST['message_id'] ?? 0);
            
            if ($message_id) {
                $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'unread' WHERE id = ?");
                $stmt->execute([$message_id]);
                
                logAuditEvent('CONTACT', 'MESSAGE_MARKED_UNREAD', "Message ID: $message_id", 'INFO');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Message marked as unread'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid message ID'
                ]);
            }
            break;
            
        case 'delete_portfolio':
        case 'delete_portfolio_project':
            $project_id = (int)($_POST['project_id'] ?? 0);
            
            if ($project_id) {
                $stmt = $pdo->prepare("DELETE FROM portfolio_projects WHERE id = ?");
                $stmt->execute([$project_id]);
                
                logAuditEvent('CONTENT', 'PORTFOLIO_DELETED', "Project ID: $project_id", 'INFO');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Portfolio project deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid project ID'
                ]);
            }
            break;
            
        case 'delete_blog_post':
            $post_id = (int)($_POST['post_id'] ?? 0);
            
            if ($post_id) {
                $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
                $stmt->execute([$post_id]);
                
                logAuditEvent('CONTENT', 'BLOG_POST_DELETED', "Post ID: $post_id", 'INFO');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Blog post deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid post ID'
                ]);
            }
            break;
            
        case 'delete_user':
            $user_id = (int)($_POST['user_id'] ?? 0);
            $current_user_id = $_SESSION['admin_user_id'] ?? 0;
            
            if ($user_id && $user_id != $current_user_id) {
                $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
                $stmt->execute([$user_id]);
                
                logAuditEvent('USER_MANAGEMENT', 'USER_DELETED', "User ID: $user_id", 'INFO');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ]);
            }
            break;
            
        case 'unlock_user':
            $user_id = (int)($_POST['user_id'] ?? 0);
            
            if ($user_id) {
                $stmt = $pdo->prepare("UPDATE admin_users SET locked_until = NULL, login_attempts = 0 WHERE id = ?");
                $stmt->execute([$user_id]);
                
                logAuditEvent('USER_MANAGEMENT', 'USER_UNLOCKED', "User ID: $user_id", 'INFO');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'User unlocked successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid user ID'
                ]);
            }
            break;
            
        case 'get_stats':
            // Dashboard istatistikleri
            $stats = [];
            
            $stmt = $pdo->query("SELECT COUNT(*) FROM portfolio_projects");
            $stats['total_projects'] = $stmt->fetchColumn();
            
            $stmt = $pdo->query("SELECT COUNT(*) FROM blog_posts");
            $stats['total_posts'] = $stmt->fetchColumn();
            
            $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'");
            $stats['unread_messages'] = $stmt->fetchColumn();
            
            $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users WHERE is_active = 1");
            $stats['active_users'] = $stmt->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            break;
            
        case 'get_recent_activities':
            // Son aktiviteler
            $stmt = $pdo->query("
                SELECT action, details, timestamp, username 
                FROM admin_logs 
                ORDER BY timestamp DESC 
                LIMIT 10
            ");
            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $activities
            ]);
            break;
            
        case 'add_content':
            $page_name = sanitizeInput($_POST['page_name'] ?? '', 'text');
            $section_name = sanitizeInput($_POST['section_name'] ?? '', 'text');
            $content_key = sanitizeInput($_POST['content_key'] ?? '', 'text');
            $content_value = sanitizeInput($_POST['content_value'] ?? '', 'text');
            $content_type = sanitizeInput($_POST['content_type'] ?? 'text', 'text');
            
            if ($page_name && $section_name && $content_key && $content_value) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO site_content (page_name, section_name, content_key, content_value, content_type, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                    ");
                    $stmt->execute([$page_name, $section_name, $content_key, $content_value, $content_type]);
                    
                    $content_id = $pdo->lastInsertId();
                    
                    logAuditEvent('CONTENT', 'CONTENT_ADDED', "Content ID: $content_id, Page: $page_name, Section: $section_name", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'İçerik başarıyla eklendi',
                        'data' => [
                            'id' => $content_id,
                            'page_name' => $page_name,
                            'section_name' => $section_name,
                            'content_key' => $content_key,
                            'content_type' => $content_type
                        ]
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'İçerik eklenirken hata oluştu: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Tüm alanları doldurun'
                ]);
            }
            break;
            
        case 'update_content':
            $content_id = (int)($_POST['content_id'] ?? 0);
            $content_value = sanitizeInput($_POST['content_value'] ?? '', 'text');
            
            if ($content_id && $content_value !== '') {
                try {
                    $stmt = $pdo->prepare("UPDATE site_content SET content_value = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$content_value, $content_id]);
                    
                    logAuditEvent('CONTENT', 'CONTENT_UPDATED', "Content ID: $content_id", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'İçerik güncellendi',
                        'data' => ['id' => $content_id, 'content_value' => $content_value]
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'İçerik güncellenirken hata oluştu: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Geçersiz parametreler'
                ]);
            }
            break;
            
        case 'delete_content':
            $content_id = (int)($_POST['content_id'] ?? 0);
            
            if ($content_id) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM site_content WHERE id = ?");
                    $stmt->execute([$content_id]);
                    
                    logAuditEvent('CONTENT', 'CONTENT_DELETED', "Content ID: $content_id", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'İçerik silindi'
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'İçerik silinirken hata oluştu: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Geçersiz içerik ID'
                ]);
            }
            break;
            
        case 'add_blog_post':
            $title = sanitizeInput($_POST['title'] ?? '', 'text');
            $content = sanitizeInput($_POST['content'] ?? '', 'text');
            $excerpt = sanitizeInput($_POST['excerpt'] ?? '', 'text');
            $category_id = (int)($_POST['category_id'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'draft', 'text');
            $meta_title = sanitizeInput($_POST['meta_title'] ?? '', 'text');
            $meta_description = sanitizeInput($_POST['meta_description'] ?? '', 'text');
            
            if ($title && $content && $category_id) {
                try {
                    // Generate slug
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO blog_posts (title, content, excerpt, category_id, status, slug, meta_title, meta_description, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ");
                    $stmt->execute([$title, $content, $excerpt, $category_id, $status, $slug, $meta_title, $meta_description]);
                    
                    $post_id = $pdo->lastInsertId();
                    
                    logAuditEvent('BLOG', 'POST_ADDED', "Post ID: $post_id, Title: $title, Status: $status", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Blog yazısı başarıyla eklendi',
                        'data' => [
                            'id' => $post_id,
                            'title' => $title,
                            'status' => $status,
                            'redirect_url' => 'blog.php'
                        ]
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Blog yazısı eklenirken hata oluştu: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lütfen tüm gerekli alanları doldurun'
                ]);
            }
            break;
            
        case 'update_blog_post':
            $post_id = (int)($_POST['post_id'] ?? 0);
            $title = sanitizeInput($_POST['title'] ?? '', 'text');
            $content = sanitizeInput($_POST['content'] ?? '', 'text');
            $excerpt = sanitizeInput($_POST['excerpt'] ?? '', 'text');
            $category_id = (int)($_POST['category_id'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'draft', 'text');
            $meta_title = sanitizeInput($_POST['meta_title'] ?? '', 'text');
            $meta_description = sanitizeInput($_POST['meta_description'] ?? '', 'text');
            
            if ($post_id && $title && $content && $category_id) {
                try {
                    // Generate slug
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
                    
                    $stmt = $pdo->prepare("
                        UPDATE blog_posts SET 
                        title = ?, content = ?, excerpt = ?, category_id = ?, status = ?, slug = ?, 
                        meta_title = ?, meta_description = ?, updated_at = NOW() 
                        WHERE id = ?
                    ");
                    $stmt->execute([$title, $content, $excerpt, $category_id, $status, $slug, $meta_title, $meta_description, $post_id]);
                    
                    logAuditEvent('BLOG', 'POST_UPDATED', "Post ID: $post_id, Title: $title, Status: $status", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Blog yazısı başarıyla güncellendi',
                        'data' => [
                            'id' => $post_id,
                            'title' => $title,
                            'status' => $status,
                            'redirect_url' => 'blog.php'
                        ]
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Blog yazısı güncellenirken hata oluştu: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lütfen tüm gerekli alanları doldurun'
                ]);
            }
            break;
            
        case 'add_portfolio':
            $title = sanitizeInput($_POST['title'] ?? '', 'text');
            $description = sanitizeInput($_POST['description'] ?? '', 'text');
            $image_url = sanitizeInput($_POST['image_url'] ?? '', 'url');
            $category = sanitizeInput($_POST['category'] ?? '', 'text');
            $status = sanitizeInput($_POST['status'] ?? 'active', 'text');
            $project_url = sanitizeInput($_POST['project_url'] ?? '', 'url');
            $technologies = sanitizeInput($_POST['technologies'] ?? '', 'text');
            
            if ($title && $description && $category) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO portfolio_projects (title, description, image_url, category, status, project_url, technologies, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ");
                    $stmt->execute([$title, $description, $image_url, $category, $status, $project_url, $technologies]);
                    
                    $project_id = $pdo->lastInsertId();
                    
                    logAuditEvent('PORTFOLIO', 'PROJECT_ADDED', "Project ID: $project_id, Title: $title, Category: $category", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Portfolio projesi başarıyla eklendi',
                        'data' => [
                            'id' => $project_id,
                            'title' => $title,
                            'category' => $category,
                            'redirect_url' => 'portfolio.php'
                        ]
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Portfolio projesi eklenirken hata oluştu: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lütfen tüm gerekli alanları doldurun'
                ]);
            }
            break;
            
        case 'update_portfolio':
            $project_id = (int)($_POST['project_id'] ?? 0);
            $title = sanitizeInput($_POST['title'] ?? '', 'text');
            $description = sanitizeInput($_POST['description'] ?? '', 'text');
            $image_url = sanitizeInput($_POST['image_url'] ?? '', 'url');
            $category = sanitizeInput($_POST['category'] ?? '', 'text');
            $status = sanitizeInput($_POST['status'] ?? 'active', 'text');
            $project_url = sanitizeInput($_POST['project_url'] ?? '', 'url');
            $technologies = sanitizeInput($_POST['technologies'] ?? '', 'text');
            
            if ($project_id && $title && $description && $category) {
                try {
                    $stmt = $pdo->prepare("
                        UPDATE portfolio_projects SET 
                        title = ?, description = ?, image_url = ?, category = ?, status = ?, 
                        project_url = ?, technologies = ?, updated_at = NOW() 
                        WHERE id = ?
                    ");
                    $stmt->execute([$title, $description, $image_url, $category, $status, $project_url, $technologies, $project_id]);
                    
                    logAuditEvent('PORTFOLIO', 'PROJECT_UPDATED', "Project ID: $project_id, Title: $title, Category: $category", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Portfolio projesi başarıyla güncellendi',
                        'data' => [
                            'id' => $project_id,
                            'title' => $title,
                            'category' => $category,
                            'redirect_url' => 'portfolio.php'
                        ]
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Portfolio projesi güncellenirken hata oluştu: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lütfen tüm gerekli alanları doldurun'
                ]);
            }
            break;
            
        case 'add_user':
            $username = sanitizeInput($_POST['username'] ?? '', 'username');
            $email = sanitizeInput($_POST['email'] ?? '', 'email');
            $full_name = sanitizeInput($_POST['full_name'] ?? '', 'text');
            $password = $_POST['password'] ?? '';
            $role = sanitizeInput($_POST['role'] ?? 'admin', 'text');
            
            if ($username && $email && $full_name && $password) {
                try {
                    // Check if username or email already exists
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    $exists = $stmt->fetchColumn();
                    
                    if ($exists > 0) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Bu kullanıcı adı veya e-posta zaten kullanılıyor'
                        ]);
                        return;
                    }
                    
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO admin_users (username, email, full_name, password_hash, role, is_active, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())
                    ");
                    $stmt->execute([$username, $email, $full_name, $password_hash, $role]);
                    
                    $user_id = $pdo->lastInsertId();
                    
                    logAuditEvent('USER', 'USER_ADDED', "User ID: $user_id, Username: $username, Role: $role", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Kullanıcı başarıyla eklendi',
                        'data' => [
                            'id' => $user_id,
                            'username' => $username,
                            'role' => $role,
                            'redirect_url' => 'users.php'
                        ]
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Kullanıcı eklenirken hata oluştu: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lütfen tüm gerekli alanları doldurun'
                ]);
            }
            break;
            
        case 'update_user':
            $user_id = (int)($_POST['user_id'] ?? 0);
            $username = sanitizeInput($_POST['username'] ?? '', 'username');
            $email = sanitizeInput($_POST['email'] ?? '', 'email');
            $full_name = sanitizeInput($_POST['full_name'] ?? '', 'text');
            $role = sanitizeInput($_POST['role'] ?? 'admin', 'text');
            $is_active = (int)($_POST['is_active'] ?? 1);
            $password = $_POST['password'] ?? '';
            
            if ($user_id && $username && $email && $full_name) {
                try {
                    // Check if username or email already exists (excluding current user)
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE (username = ? OR email = ?) AND id != ?");
                    $stmt->execute([$username, $email, $user_id]);
                    $exists = $stmt->fetchColumn();
                    
                    if ($exists > 0) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Bu kullanıcı adı veya e-posta zaten kullanılıyor'
                        ]);
                        return;
                    }
                    
                    if ($password) {
                        // Update with new password
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("
                            UPDATE admin_users SET 
                            username = ?, email = ?, full_name = ?, password_hash = ?, role = ?, is_active = ?, updated_at = NOW() 
                            WHERE id = ?
                        ");
                        $stmt->execute([$username, $email, $full_name, $password_hash, $role, $is_active, $user_id]);
                    } else {
                        // Update without password
                        $stmt = $pdo->prepare("
                            UPDATE admin_users SET 
                            username = ?, email = ?, full_name = ?, role = ?, is_active = ?, updated_at = NOW() 
                            WHERE id = ?
                        ");
                        $stmt->execute([$username, $email, $full_name, $role, $is_active, $user_id]);
                    }
                    
                    logAuditEvent('USER', 'USER_UPDATED', "User ID: $user_id, Username: $username, Role: $role", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Kullanıcı başarıyla güncellendi',
                        'data' => [
                            'id' => $user_id,
                            'username' => $username,
                            'role' => $role,
                            'redirect_url' => 'users.php'
                        ]
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Kullanıcı güncellenirken hata oluştu: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lütfen tüm gerekli alanları doldurun'
                ]);
            }
            break;
            
        case 'upload_media':
            if (isset($_FILES['file'])) {
                $file = $_FILES['file'];
                
                // File validation
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/webm', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                
                if (!in_array($file['type'], $allowed_types)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Desteklenmeyen dosya tipi'
                    ]);
                    break;
                }
                
                // File size limit (10MB)
                if ($file['size'] > 10 * 1024 * 1024) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Dosya boyutu çok büyük (max 10MB)'
                    ]);
                    break;
                }
                
                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $upload_dir = '../uploads/media/';
                
                // Create upload directory if not exists
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $upload_path = $upload_dir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    try {
                        // Save to database
                        $stmt = $pdo->prepare("
                            INSERT INTO media_files (filename, original_name, file_path, upload_path, file_size, mime_type, file_type, alt_text, uploaded_by, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                        ");
                        $stmt->execute([
                            $filename,
                            $file['name'],
                            $upload_path,
                            'uploads/media/' . $filename,
                            $file['size'],
                            $file['type'],
                            $file['type'],
                            pathinfo($file['name'], PATHINFO_FILENAME),
                            $_SESSION['admin_id']
                        ]);
                        
                        $file_id = $pdo->lastInsertId();
                        
                        logAuditEvent('MEDIA', 'FILE_UPLOADED', "File ID: $file_id, Filename: " . $file['name'], 'INFO');
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Dosya başarıyla yüklendi',
                            'data' => [
                                'id' => $file_id,
                                'filename' => $filename,
                                'original_name' => $file['name'],
                                'upload_path' => 'uploads/media/' . $filename
                            ]
                        ]);
                    } catch (Exception $e) {
                        unlink($upload_path); // Delete uploaded file
                        echo json_encode([
                            'success' => false,
                            'message' => 'Veritabanı hatası: ' . $e->getMessage()
                        ]);
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Dosya yükleme hatası'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Dosya bulunamadı'
                ]);
            }
            break;
            
        case 'delete_media':
            $file_id = (int)($_POST['file_id'] ?? 0);
            
            if ($file_id) {
                try {
                    // Get file info
                    $stmt = $pdo->prepare("SELECT filename, upload_path FROM media_files WHERE id = ?");
                    $stmt->execute([$file_id]);
                    $file = $stmt->fetch();
                    
                    if ($file) {
                        // Delete from database
                        $stmt = $pdo->prepare("DELETE FROM media_files WHERE id = ?");
                        $stmt->execute([$file_id]);
                        
                        // Delete physical file
                        $file_path = '../' . $file['upload_path'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                        
                        logAuditEvent('MEDIA', 'FILE_DELETED', "File ID: $file_id, Filename: " . $file['filename'], 'INFO');
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Dosya silindi'
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Dosya bulunamadı'
                        ]);
                    }
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Silme hatası: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Geçersiz dosya ID'
                ]);
            }
            break;
            
        case 'delete_web_content':
            $content_id = (int)($_POST['content_id'] ?? 0);
            
            if ($content_id) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM web_content WHERE id = ?");
                    $stmt->execute([$content_id]);
                    
                    logAuditEvent('WEB_CONTENT', 'CONTENT_DELETED', "Content ID: $content_id", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'İçerik silindi'
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Silme hatası: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Geçersiz içerik ID'
                ]);
            }
            break;
            
        case 'update_web_content':
            $content_id = (int)($_POST['content_id'] ?? 0);
            $title = sanitizeInput($_POST['title'] ?? '', 'text');
            $description = sanitizeInput($_POST['description'] ?? '', 'text');
            $content = sanitizeInput($_POST['content'] ?? '', 'text');
            $alt_text = sanitizeInput($_POST['alt_text'] ?? '', 'text');
            $status = sanitizeInput($_POST['status'] ?? 'active', 'text');
            
            if ($content_id && ($title || $description)) {
                try {
                    $stmt = $pdo->prepare("
                        UPDATE web_content 
                        SET title = ?, description = ?, content = ?, alt_text = ?, status = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$title, $description, $content, $alt_text, $status, $content_id]);
                    
                    logAuditEvent('WEB_CONTENT', 'CONTENT_UPDATED', "Content ID: $content_id", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'İçerik güncellendi'
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Güncelleme hatası: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Geçersiz veri'
                ]);
            }
            break;
            
        case 'clear_web_content':
            try {
                $stmt = $pdo->prepare("DELETE FROM web_content");
                $stmt->execute();
                
                logAuditEvent('WEB_CONTENT', 'ALL_CONTENT_CLEARED', "All web content cleared", 'WARNING');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Tüm içerik temizlendi'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Temizleme hatası: ' . $e->getMessage()
                ]);
            }
            break;
            
        case 'add_role':
            $role_name = sanitizeInput($_POST['role_name'] ?? '', 'text');
            $role_key = sanitizeInput($_POST['role_key'] ?? '', 'text');
            $description = sanitizeInput($_POST['description'] ?? '', 'text');
            $permissions = json_encode($_POST['permissions'] ?? []);
            $is_active = (int)($_POST['is_active'] ?? 1);
            
            if ($role_name && $role_key) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO user_roles (role_name, role_key, description, permissions, is_active) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$role_name, $role_key, $description, $permissions, $is_active]);
                    
                    logAuditEvent('USER_MANAGEMENT', 'ROLE_ADDED', "Role added: $role_name ($role_key)", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Role added successfully'
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Add failed: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Role name and key are required'
                ]);
            }
            break;
            
        case 'update_role':
            $role_id = (int)($_POST['role_id'] ?? 0);
            $role_name = sanitizeInput($_POST['role_name'] ?? '', 'text');
            $role_key = sanitizeInput($_POST['role_key'] ?? '', 'text');
            $description = sanitizeInput($_POST['description'] ?? '', 'text');
            $permissions = json_encode($_POST['permissions'] ?? []);
            $is_active = (int)($_POST['is_active'] ?? 1);
            
            if ($role_id && $role_name && $role_key) {
                try {
                    $stmt = $pdo->prepare("
                        UPDATE user_roles 
                        SET role_name = ?, role_key = ?, description = ?, permissions = ?, is_active = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$role_name, $role_key, $description, $permissions, $is_active, $role_id]);
                    
                    logAuditEvent('USER_MANAGEMENT', 'ROLE_UPDATED', "Role updated: $role_name ($role_key)", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Role updated successfully'
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Update failed: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid parameters'
                ]);
            }
            break;
            
        case 'delete_role':
            $role_id = (int)($_POST['role_id'] ?? 0);
            
            if ($role_id) {
                try {
                    // Önce bu rolü kullanan kullanıcıları kontrol et
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE role = (SELECT role_key FROM user_roles WHERE id = ?)");
                    $stmt->execute([$role_id]);
                    $user_count = $stmt->fetchColumn();
                    
                    if ($user_count > 0) {
                        echo json_encode([
                            'success' => false,
                            'message' => "Cannot delete role: $user_count user(s) are using this role"
                        ]);
                        break;
                    }
                    
                    $stmt = $pdo->prepare("DELETE FROM user_roles WHERE id = ?");
                    $stmt->execute([$role_id]);
                    
                    logAuditEvent('USER_MANAGEMENT', 'ROLE_DELETED', "Role deleted: ID $role_id", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Role deleted successfully'
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Delete failed: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid role ID'
                ]);
            }
            break;
            
        case 'update_user_role':
            $user_id = (int)($_POST['user_id'] ?? 0);
            $role = sanitizeInput($_POST['role'] ?? '', 'text');
            
            if ($user_id && $role) {
                try {
                    $stmt = $pdo->prepare("UPDATE admin_users SET role = ? WHERE id = ?");
                    $stmt->execute([$role, $user_id]);
                    
                    logAuditEvent('USER_MANAGEMENT', 'USER_ROLE_UPDATED', "User role updated: ID $user_id to $role", 'INFO');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'User role updated successfully'
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Update failed: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid parameters'
                ]);
            }
            break;
            
        case 'update_settings':
            $site_title = sanitizeInput($_POST['site_title'] ?? '', 'text');
            $site_description = sanitizeInput($_POST['site_description'] ?? '', 'text');
            $admin_email = sanitizeInput($_POST['admin_email'] ?? '', 'email');
            
            try {
                // Update site content
                $stmt = $pdo->prepare("
                    INSERT INTO site_content (page, section, content_key, content_value, created_at, updated_at)
                    VALUES ('settings', 'general', 'site_title', ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()
                ");
                $stmt->execute([$site_title]);
                
                $stmt = $pdo->prepare("
                    INSERT INTO site_content (page, section, content_key, content_value, created_at, updated_at)
                    VALUES ('settings', 'general', 'site_description', ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()
                ");
                $stmt->execute([$site_description]);
                
                $stmt = $pdo->prepare("
                    INSERT INTO site_content (page, section, content_key, content_value, created_at, updated_at)
                    VALUES ('settings', 'general', 'admin_email', ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()
                ");
                $stmt->execute([$admin_email]);
                
                logAuditEvent('SETTINGS', 'SETTINGS_UPDATED', "Site settings updated", 'INFO');
                echo json_encode(['success' => true, 'message' => 'Ayarlar başarıyla güncellendi']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Ayarlar güncellenirken hata: ' . $e->getMessage()]);
            }
            break;
            
        case 'update_profile':
            $user_id = (int)($_POST['user_id'] ?? 0);
            $full_name = sanitizeInput($_POST['full_name'] ?? '', 'text');
            $email = sanitizeInput($_POST['email'] ?? '', 'email');
            $phone = sanitizeInput($_POST['phone'] ?? '', 'text');
            $bio = sanitizeInput($_POST['bio'] ?? '', 'text');
            
            if ($user_id) {
                try {
                    $stmt = $pdo->prepare("
                        UPDATE admin_users 
                        SET full_name = ?, email = ?, phone = ?, bio = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$full_name, $email, $phone, $bio, $user_id]);
                    
                    // Update session
                    $_SESSION['admin_email'] = $email;
                    $_SESSION['admin_name'] = $full_name;
                    
                    logAuditEvent('USER_MANAGEMENT', 'PROFILE_UPDATED', "Profile updated for user: $user_id", 'INFO');
                    echo json_encode(['success' => true, 'message' => 'Profil başarıyla güncellendi']);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Profil güncellenirken hata: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Geçersiz kullanıcı ID']);
            }
            break;
            
    default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            break;
    }
    
} catch (Exception $e) {
    logAuditEvent('SYSTEM', 'AJAX_ERROR', $e->getMessage(), 'ERROR');
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
