<?php
/**
 * AJAX Handler Backend
 * Tüm AJAX isteklerini yöneten central handler
 */

// Security
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// CORS (gerekirse)
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

// AJAX kontrolü
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// Session başlat
session_start();

// CSRF kontrolü
function validateCSRF() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }
    }
    return true;
}

// CSRF token oluştur
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Response helper
function jsonResponse($success, $data = null, $message = '', $statusCode = 200) {
    http_response_code($statusCode);
    
    $response = [
        'success' => $success,
        'timestamp' => time(),
        'csrf_token' => generateCSRFToken()
    ];
    
    if ($message) {
        $response['message'] = $message;
    }
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Error handler
function ajaxError($message, $statusCode = 400, $details = null) {
    $response = [
        'success' => false,
        'error' => $message,
        'timestamp' => time()
    ];
    
    if ($details !== null) {
        $response['details'] = $details;
    }
    
    jsonResponse(false, null, $message, $statusCode);
}

// CSRF kontrolü
if (!validateCSRF()) {
    ajaxError('CSRF token validation failed', 403);
}

// Database connection
require_once __DIR__ . '/../config/database.php';

// Input helper
function getInput($key, $default = null) {
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    return $data[$key] ?? $_POST[$key] ?? $_GET[$key] ?? $default;
}

// Sanitize input
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Router
class AjaxRouter {
    private $routes = [];
    
    public function register($method, $action, $callback) {
        $this->routes[strtoupper($method)][$action] = $callback;
    }
    
    public function handle() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = getInput('action');
        
        if (!$action) {
            ajaxError('Action parameter is required', 400);
        }
        
        if (!isset($this->routes[$method][$action])) {
            ajaxError("Action not found: $action", 404);
        }
        
        try {
            $callback = $this->routes[$method][$action];
            $result = $callback();
            
            if (is_array($result)) {
                jsonResponse(true, $result['data'] ?? null, $result['message'] ?? '');
            } else {
                jsonResponse(true, $result);
            }
        } catch (Exception $e) {
            error_log('AJAX Error: ' . $e->getMessage());
            ajaxError('Internal server error: ' . $e->getMessage(), 500);
        }
    }
}

// Router instance
$router = new AjaxRouter();

// ============================================
// ROUTES
// ============================================

// GET: Blog posts
$router->register('GET', 'blog_posts', function() use ($pdo) {
    $limit = (int) getInput('limit', 10);
    $offset = (int) getInput('offset', 0);
    $category = getInput('category');
    
    $sql = "SELECT * FROM blog_posts WHERE status = 'published'";
    $params = [];
    
    if ($category) {
        $sql .= " AND category_id = (SELECT id FROM blog_categories WHERE slug = ?)";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY published_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $posts = $stmt->fetchAll();
    
    return [
        'data' => $posts,
        'message' => 'Blog posts retrieved successfully'
    ];
});

// GET: Portfolio projects
$router->register('GET', 'portfolio_projects', function() use ($pdo) {
    $category = getInput('category');
    $featured = getInput('featured');
    
    $sql = "SELECT * FROM portfolio_projects WHERE is_published = 1";
    $params = [];
    
    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    if ($featured) {
        $sql .= " AND is_featured = 1";
    }
    
    $sql .= " ORDER BY sort_order ASC, created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $projects = $stmt->fetchAll();
    
    return ['data' => $projects];
});

// POST: Contact form
$router->register('POST', 'contact_form', function() use ($pdo) {
    $name = sanitizeInput(getInput('name'));
    $email = sanitizeInput(getInput('email'));
    $phone = sanitizeInput(getInput('phone'));
    $subject = sanitizeInput(getInput('subject'));
    $message = sanitizeInput(getInput('message'));
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors['name'] = 'Ad sahəsi mütləqdir';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email sahəsi mütləqdir';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Düzgün email ünvanı daxil edin';
    }
    
    if (empty($message)) {
        $errors['message'] = 'Mesaj sahəsi mütləqdir';
    }
    
    if (!empty($errors)) {
        ajaxError('Validation failed', 400, $errors);
    }
    
    // Save to database
    $stmt = $pdo->prepare("
        INSERT INTO contact_messages (name, email, phone, subject, message, status, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, ?, ?, 'new', ?, ?, NOW())
    ");
    
    $stmt->execute([
        $name,
        $email,
        $phone,
        $subject,
        $message,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ]);
    
    // Send email (optional)
    try {
        require_once __DIR__ . '/../includes/EmailSender.php';
        $emailSender = new EmailSender();
        $emailSender->sendContactEmail([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message
        ]);
    } catch (Exception $e) {
        error_log('Email sending failed: ' . $e->getMessage());
    }
    
    return [
        'data' => ['id' => $pdo->lastInsertId()],
        'message' => 'Mesajınız uğurla göndərildi. Tezliklə sizinlə əlaqə saxlayacağıq.'
    ];
});

// POST: Newsletter subscribe
$router->register('POST', 'newsletter_subscribe', function() use ($pdo) {
    $email = sanitizeInput(getInput('email'));
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        ajaxError('Düzgün email ünvanı daxil edin', 400);
    }
    
    // Check if already subscribed
    $stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        ajaxError('Bu email artıq qeydiyyatdan keçib', 400);
    }
    
    // Subscribe
    $stmt = $pdo->prepare("
        INSERT INTO newsletter_subscribers (email, status, subscribed_at)
        VALUES (?, 'active', NOW())
    ");
    
    $stmt->execute([$email]);
    
    return [
        'message' => 'Newsletter abunəliyi uğurla tamamlandı!'
    ];
});

// PUT: Update profile
$router->register('PUT', 'update_profile', function() use ($pdo) {
    if (!isset($_SESSION['user_id'])) {
        ajaxError('Unauthorized', 401);
    }
    
    $name = sanitizeInput(getInput('name'));
    $email = sanitizeInput(getInput('email'));
    
    $stmt = $pdo->prepare("
        UPDATE users SET name = ?, email = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->execute([$name, $email, $_SESSION['user_id']]);
    
    return ['message' => 'Profil uğurla yeniləndi'];
});

// DELETE: Delete item
$router->register('DELETE', 'delete_item', function() use ($pdo) {
    if (!isset($_SESSION['admin_logged_in'])) {
        ajaxError('Unauthorized', 401);
    }
    
    $table = getInput('table');
    $id = (int) getInput('id');
    
    // Whitelist tables
    $allowedTables = ['blog_posts', 'portfolio_projects', 'contact_messages'];
    
    if (!in_array($table, $allowedTables)) {
        ajaxError('Invalid table', 400);
    }
    
    $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->execute([$id]);
    
    return ['message' => 'Silindi'];
});

// GET: Search
$router->register('GET', 'search', function() use ($pdo) {
    $query = sanitizeInput(getInput('q'));
    $type = getInput('type', 'all'); // blog, portfolio, all
    
    if (empty($query)) {
        return ['data' => []];
    }
    
    $results = [];
    
    // Blog search
    if ($type === 'all' || $type === 'blog') {
        $stmt = $pdo->prepare("
            SELECT id, title, slug, excerpt, 'blog' as type
            FROM blog_posts
            WHERE status = 'published' AND (title LIKE ? OR content LIKE ?)
            LIMIT 10
        ");
        $stmt->execute(["%$query%", "%$query%"]);
        $results = array_merge($results, $stmt->fetchAll());
    }
    
    // Portfolio search
    if ($type === 'all' || $type === 'portfolio') {
        $stmt = $pdo->prepare("
            SELECT id, title, description, 'portfolio' as type
            FROM portfolio_projects
            WHERE is_published = 1 AND (title LIKE ? OR description LIKE ?)
            LIMIT 10
        ");
        $stmt->execute(["%$query%", "%$query%"]);
        $results = array_merge($results, $stmt->fetchAll());
    }
    
    return ['data' => $results];
});

// Handle request
$router->handle();


