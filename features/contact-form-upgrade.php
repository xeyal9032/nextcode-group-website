<?php
/**
 * NextCode Group - Contact Form Upgrade
 * Mevcut contact form sistemini geliştirir
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once __DIR__ . '/../config/database.php';

class ContactFormUpgrade {
    private $pdo;
    
    public function __construct() {
        try {
            $database = new Database();
            $this->pdo = $database->getConnection();
        } catch (Exception $e) {
            $this->pdo = null;
        }
    }
    
    /**
     * Contact form için gelişmiş özellikler
     */
    public function createContactFormTables() {
        if (!$this->pdo) return false;
        
        try {
            // Contact form templates
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS contact_form_templates (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    slug VARCHAR(255) NOT NULL UNIQUE,
                    fields TEXT NOT NULL,
                    email_template TEXT,
                    auto_reply_template TEXT,
                    is_active TINYINT(1) DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_slug (slug),
                    INDEX idx_active (is_active)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Contact form submissions tracking
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS contact_form_submissions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    form_type VARCHAR(100) DEFAULT "general",
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    phone VARCHAR(50),
                    company VARCHAR(255),
                    subject VARCHAR(255) NOT NULL,
                    message TEXT NOT NULL,
                    service_type VARCHAR(100),
                    budget_range VARCHAR(100),
                    project_timeline VARCHAR(100),
                    preferred_contact VARCHAR(50),
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    referrer_url VARCHAR(500),
                    status ENUM("new", "read", "replied", "closed") DEFAULT "new",
                    priority ENUM("low", "medium", "high", "urgent") DEFAULT "medium",
                    assigned_to INT,
                    notes TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_email (email),
                    INDEX idx_status (status),
                    INDEX idx_priority (priority),
                    INDEX idx_created_at (created_at),
                    INDEX idx_form_type (form_type)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Contact form analytics
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS contact_form_analytics (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    date DATE NOT NULL,
                    form_type VARCHAR(100) DEFAULT "general",
                    total_submissions INT DEFAULT 0,
                    successful_submissions INT DEFAULT 0,
                    failed_submissions INT DEFAULT 0,
                    avg_response_time INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_date_form (date, form_type),
                    INDEX idx_date (date),
                    INDEX idx_form_type (form_type)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Contact form fields
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS contact_form_fields (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    form_type VARCHAR(100) DEFAULT "general",
                    field_name VARCHAR(100) NOT NULL,
                    field_label VARCHAR(255) NOT NULL,
                    field_type ENUM("text", "email", "tel", "textarea", "select", "checkbox", "radio", "file") DEFAULT "text",
                    field_options TEXT,
                    is_required TINYINT(1) DEFAULT 0,
                    validation_rules TEXT,
                    placeholder_text VARCHAR(255),
                    help_text VARCHAR(500),
                    sort_order INT DEFAULT 0,
                    is_active TINYINT(1) DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_form_type (form_type),
                    INDEX idx_sort_order (sort_order),
                    INDEX idx_active (is_active)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Contact form spam protection
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS contact_form_spam_log (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    ip_address VARCHAR(45) NOT NULL,
                    email VARCHAR(255),
                    spam_score DECIMAL(5,2) DEFAULT 0,
                    spam_reasons TEXT,
                    blocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_ip_address (ip_address),
                    INDEX idx_blocked_at (blocked_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            return true;
        } catch (Exception $e) {
            error_log('Contact form tables creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Default form templates'leri ekle
     */
    public function insertDefaultTemplates() {
        if (!$this->pdo) return false;
        
        try {
            $templates = [
                [
                    'name' => 'General Contact',
                    'slug' => 'general',
                    'fields' => json_encode([
                        'name' => ['required' => true, 'type' => 'text'],
                        'email' => ['required' => true, 'type' => 'email'],
                        'phone' => ['required' => false, 'type' => 'tel'],
                        'company' => ['required' => false, 'type' => 'text'],
                        'subject' => ['required' => true, 'type' => 'text'],
                        'message' => ['required' => true, 'type' => 'textarea']
                    ]),
                    'email_template' => 'New contact form submission from {{name}} ({{email}}). Subject: {{subject}}. Message: {{message}}',
                    'auto_reply_template' => 'Thank you {{name}} for contacting NextCode Group. We will get back to you within 24 hours.'
                ],
                [
                    'name' => 'Project Inquiry',
                    'slug' => 'project',
                    'fields' => json_encode([
                        'name' => ['required' => true, 'type' => 'text'],
                        'email' => ['required' => true, 'type' => 'email'],
                        'phone' => ['required' => true, 'type' => 'tel'],
                        'company' => ['required' => true, 'type' => 'text'],
                        'project_type' => ['required' => true, 'type' => 'select', 'options' => ['Web Development', 'Mobile App', 'E-commerce', 'SEO', 'Digital Marketing']],
                        'budget_range' => ['required' => true, 'type' => 'select', 'options' => ['Under $5,000', '$5,000 - $10,000', '$10,000 - $25,000', '$25,000 - $50,000', 'Over $50,000']],
                        'timeline' => ['required' => true, 'type' => 'select', 'options' => ['ASAP', '1-2 months', '3-6 months', '6+ months']],
                        'message' => ['required' => true, 'type' => 'textarea']
                    ]),
                    'email_template' => 'New project inquiry from {{name}} ({{company}}). Project: {{project_type}}. Budget: {{budget_range}}. Timeline: {{timeline}}. Message: {{message}}',
                    'auto_reply_template' => 'Thank you {{name}} for your project inquiry. Our team will review your requirements and contact you within 24 hours.'
                ],
                [
                    'name' => 'Support Request',
                    'slug' => 'support',
                    'fields' => json_encode([
                        'name' => ['required' => true, 'type' => 'text'],
                        'email' => ['required' => true, 'type' => 'email'],
                        'phone' => ['required' => false, 'type' => 'tel'],
                        'issue_type' => ['required' => true, 'type' => 'select', 'options' => ['Technical Issue', 'Billing Question', 'Feature Request', 'Bug Report', 'Other']],
                        'priority' => ['required' => true, 'type' => 'select', 'options' => ['Low', 'Medium', 'High', 'Urgent']],
                        'message' => ['required' => true, 'type' => 'textarea']
                    ]),
                    'email_template' => 'New support request from {{name}} ({{email}}). Issue: {{issue_type}}. Priority: {{priority}}. Message: {{message}}',
                    'auto_reply_template' => 'Thank you {{name}} for contacting our support team. We will address your {{issue_type}} issue as soon as possible.'
                ]
            ];
            
            foreach ($templates as $template) {
                $stmt = $this->pdo->prepare("
                    INSERT IGNORE INTO contact_form_templates 
                    (name, slug, fields, email_template, auto_reply_template) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $template['name'],
                    $template['slug'],
                    $template['fields'],
                    $template['email_template'],
                    $template['auto_reply_template']
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Default templates insertion failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Contact form submission'ını kaydet
     */
    public function saveFormSubmission($formData, $formType = 'general') {
        if (!$this->pdo) return false;
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO contact_form_submissions 
                (form_type, name, email, phone, company, subject, message, service_type, budget_range, project_timeline, preferred_contact, ip_address, user_agent, referrer_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $formType,
                $formData['name'],
                $formData['email'],
                $formData['phone'] ?? null,
                $formData['company'] ?? null,
                $formData['subject'],
                $formData['message'],
                $formData['service_type'] ?? null,
                $formData['budget_range'] ?? null,
                $formData['project_timeline'] ?? null,
                $formData['preferred_contact'] ?? null,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $_SERVER['HTTP_REFERER'] ?? null
            ]);
            
            if ($result) {
                // Analytics'i güncelle
                $this->updateAnalytics($formType, 'successful');
                
                // Auto-reply gönder
                $this->sendAutoReply($formData, $formType);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log('Form submission failed: ' . $e->getMessage());
            $this->updateAnalytics($formType, 'failed');
            return false;
        }
    }
    
    /**
     * Spam kontrolü yap
     */
    public function checkSpam($formData) {
        $spamScore = 0;
        $reasons = [];
        
        // IP kontrolü
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if ($this->isSpamIP($ip)) {
            $spamScore += 50;
            $reasons[] = 'Known spam IP';
        }
        
        // Email kontrolü
        if (strpos($formData['email'], '@') === false) {
            $spamScore += 30;
            $reasons[] = 'Invalid email format';
        }
        
        // Mesaj uzunluğu kontrolü
        if (strlen($formData['message']) < 10) {
            $spamScore += 20;
            $reasons[] = 'Message too short';
        }
        
        // Spam kelimeler kontrolü
        $spamWords = ['viagra', 'casino', 'loan', 'free money', 'click here'];
        foreach ($spamWords as $word) {
            if (stripos($formData['message'], $word) !== false) {
                $spamScore += 25;
                $reasons[] = "Contains spam word: {$word}";
            }
        }
        
        // Spam skorunu kaydet
        if ($spamScore > 50) {
            $this->logSpamAttempt($ip, $formData['email'], $spamScore, $reasons);
            return true;
        }
        
        return false;
    }
    
    /**
     * Auto-reply gönder
     */
    private function sendAutoReply($formData, $formType) {
        try {
            $template = $this->getFormTemplate($formType);
            if (!$template) return false;
            
            $message = $template['auto_reply_template'];
            
            // Template değişkenlerini değiştir
            foreach ($formData as $key => $value) {
                $message = str_replace("{{$key}}", $value, $message);
            }
            
            $headers = "From: noreply@nextcodegroup.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            return mail($formData['email'], 'Thank you for contacting NextCode Group', $message, $headers);
        } catch (Exception $e) {
            error_log('Auto-reply failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Form template'ini al
     */
    private function getFormTemplate($formType) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM contact_form_templates WHERE slug = ? AND is_active = 1");
            $stmt->execute([$formType]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Analytics'i güncelle
     */
    private function updateAnalytics($formType, $status) {
        try {
            $date = date('Y-m-d');
            
            $stmt = $this->pdo->prepare("
                INSERT INTO contact_form_analytics (date, form_type, total_submissions, successful_submissions, failed_submissions) 
                VALUES (?, ?, 1, ?, ?)
                ON DUPLICATE KEY UPDATE 
                total_submissions = total_submissions + 1,
                successful_submissions = successful_submissions + ?,
                failed_submissions = failed_submissions + ?
            ");
            
            $successful = ($status === 'successful') ? 1 : 0;
            $failed = ($status === 'failed') ? 1 : 0;
            
            $stmt->execute([$date, $formType, $successful, $failed, $successful, $failed]);
        } catch (Exception $e) {
            error_log('Analytics update failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Spam IP kontrolü
     */
    private function isSpamIP($ip) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM contact_form_spam_log WHERE ip_address = ? AND blocked_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $stmt->execute([$ip]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Spam denemesini kaydet
     */
    private function logSpamAttempt($ip, $email, $spamScore, $reasons) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO contact_form_spam_log (ip_address, email, spam_score, spam_reasons) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$ip, $email, $spamScore, json_encode($reasons)]);
        } catch (Exception $e) {
            error_log('Spam logging failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Contact form istatistiklerini al
     */
    public function getContactFormStats() {
        if (!$this->pdo) return [];
        
        try {
            $stats = [];
            
            // Toplam submission sayısı
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM contact_form_submissions");
            $stats['total_submissions'] = $stmt->fetchColumn();
            
            // Bugünkü submission'lar
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM contact_form_submissions WHERE DATE(created_at) = CURDATE()");
            $stats['today_submissions'] = $stmt->fetchColumn();
            
            // Form türü dağılımı
            $stmt = $this->pdo->query("
                SELECT form_type, COUNT(*) as count 
                FROM contact_form_submissions 
                GROUP BY form_type 
                ORDER BY count DESC
            ");
            $stats['form_types'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Status dağılımı
            $stmt = $this->pdo->query("
                SELECT status, COUNT(*) as count 
                FROM contact_form_submissions 
                GROUP BY status 
                ORDER BY count DESC
            ");
            $stats['status_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Son 7 günlük trend
            $stmt = $this->pdo->query("
                SELECT DATE(created_at) as date, COUNT(*) as count 
                FROM contact_form_submissions 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                GROUP BY DATE(created_at) 
                ORDER BY date DESC
            ");
            $stats['weekly_trend'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (Exception $e) {
            error_log('Contact form stats fetch failed: ' . $e->getMessage());
            return [];
        }
    }
}

// CLI veya web erişimi için
if (php_sapi_name() === 'cli' || isset($_GET['setup'])) {
    $contactFormUpgrade = new ContactFormUpgrade();
    
    echo "=== Contact Form Upgrade ===\n";
    
    if ($contactFormUpgrade->createContactFormTables()) {
        echo "✓ Contact form tables created successfully\n";
        
        if ($contactFormUpgrade->insertDefaultTemplates()) {
            echo "✓ Default form templates inserted successfully\n";
        }
        
        // İstatistikleri göster
        $stats = $contactFormUpgrade->getContactFormStats();
        echo "\nContact Form Statistics:\n";
        echo "- Total Submissions: {$stats['total_submissions']}\n";
        echo "- Today's Submissions: {$stats['today_submissions']}\n";
        
        if (!empty($stats['form_types'])) {
            echo "\nForm Types:\n";
            foreach ($stats['form_types'] as $type) {
                echo "  - {$type['form_type']}: {$type['count']} submissions\n";
            }
        }
    } else {
        echo "✗ Failed to create contact form tables\n";
    }
    
    echo "\n=== Contact Form Upgrade Complete ===\n";
}
?>
