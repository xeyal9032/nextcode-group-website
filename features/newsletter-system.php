<?php
/**
 * NextCode Group - Newsletter System
 * Newsletter abonelik ve yönetim sistemi
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once __DIR__ . '/../config/database.php';

class NewsletterSystem {
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
     * Newsletter sistemi için tabloları oluştur
     */
    public function createNewsletterTables() {
        if (!$this->pdo) return false;
        
        try {
            // Newsletter subscribers
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS newsletter_subscribers (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    first_name VARCHAR(255),
                    last_name VARCHAR(255),
                    status ENUM("active", "unsubscribed", "bounced", "complained") DEFAULT "active",
                    subscription_source VARCHAR(100) DEFAULT "website",
                    interests TEXT,
                    language VARCHAR(10) DEFAULT "en",
                    timezone VARCHAR(50) DEFAULT "UTC",
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    unsubscribed_at TIMESTAMP NULL,
                    last_email_sent TIMESTAMP NULL,
                    email_count INT DEFAULT 0,
                    open_count INT DEFAULT 0,
                    click_count INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_email (email),
                    INDEX idx_status (status),
                    INDEX idx_subscribed_at (subscribed_at),
                    INDEX idx_subscription_source (subscription_source)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Newsletter campaigns
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS newsletter_campaigns (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    subject VARCHAR(255) NOT NULL,
                    content TEXT NOT NULL,
                    template_type ENUM("html", "text", "mixed") DEFAULT "html",
                    sender_name VARCHAR(255) DEFAULT "NextCode Group",
                    sender_email VARCHAR(255) DEFAULT "newsletter@nextcodegroup.com",
                    reply_to VARCHAR(255),
                    status ENUM("draft", "scheduled", "sending", "sent", "failed") DEFAULT "draft",
                    scheduled_at TIMESTAMP NULL,
                    sent_at TIMESTAMP NULL,
                    target_audience TEXT,
                    total_recipients INT DEFAULT 0,
                    delivered_count INT DEFAULT 0,
                    opened_count INT DEFAULT 0,
                    clicked_count INT DEFAULT 0,
                    bounced_count INT DEFAULT 0,
                    unsubscribed_count INT DEFAULT 0,
                    complaint_count INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_status (status),
                    INDEX idx_scheduled_at (scheduled_at),
                    INDEX idx_sent_at (sent_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Newsletter templates
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS newsletter_templates (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    slug VARCHAR(255) NOT NULL UNIQUE,
                    subject_template VARCHAR(255) NOT NULL,
                    html_template LONGTEXT NOT NULL,
                    text_template TEXT,
                    preview_image VARCHAR(500),
                    description TEXT,
                    category VARCHAR(100) DEFAULT "general",
                    is_active TINYINT(1) DEFAULT 1,
                    usage_count INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_slug (slug),
                    INDEX idx_category (category),
                    INDEX idx_active (is_active)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Newsletter analytics
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS newsletter_analytics (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    campaign_id INT NOT NULL,
                    subscriber_id INT NOT NULL,
                    email_address VARCHAR(255) NOT NULL,
                    action ENUM("sent", "delivered", "opened", "clicked", "bounced", "unsubscribed", "complained") NOT NULL,
                    action_data TEXT,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_campaign_id (campaign_id),
                    INDEX idx_subscriber_id (subscriber_id),
                    INDEX idx_action (action),
                    INDEX idx_timestamp (timestamp),
                    FOREIGN KEY (campaign_id) REFERENCES newsletter_campaigns(id) ON DELETE CASCADE,
                    FOREIGN KEY (subscriber_id) REFERENCES newsletter_subscribers(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            // Newsletter segments
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS newsletter_segments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    description TEXT,
                    criteria TEXT NOT NULL,
                    subscriber_count INT DEFAULT 0,
                    is_active TINYINT(1) DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_active (is_active)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
            
            return true;
        } catch (Exception $e) {
            error_log('Newsletter tables creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Default newsletter templates'leri ekle
     */
    public function insertDefaultTemplates() {
        if (!$this->pdo) return false;
        
        try {
            $templates = [
                [
                    'name' => 'Welcome Email',
                    'slug' => 'welcome',
                    'subject_template' => 'Welcome to NextCode Group Newsletter!',
                    'html_template' => '
                        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                            <h2 style="color: #667eea;">Welcome to NextCode Group!</h2>
                            <p>Thank you for subscribing to our newsletter. You will receive:</p>
                            <ul>
                                <li>Latest digital marketing insights</li>
                                <li>Web development tips and trends</li>
                                <li>Exclusive offers and promotions</li>
                                <li>Industry news and updates</li>
                            </ul>
                            <p>Best regards,<br>NextCode Group Team</p>
                        </div>
                    ',
                    'text_template' => 'Welcome to NextCode Group! Thank you for subscribing to our newsletter.',
                    'description' => 'Welcome email for new subscribers',
                    'category' => 'welcome'
                ],
                [
                    'name' => 'Monthly Newsletter',
                    'slug' => 'monthly',
                    'subject_template' => 'NextCode Group Monthly Newsletter - {{month}} {{year}}',
                    'html_template' => '
                        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                            <h2 style="color: #667eea;">Monthly Newsletter</h2>
                            <h3>This Month\'s Highlights</h3>
                            <p>{{content}}</p>
                            <h3>Upcoming Events</h3>
                            <p>{{events}}</p>
                            <h3>Featured Articles</h3>
                            <p>{{articles}}</p>
                            <p>Best regards,<br>NextCode Group Team</p>
                        </div>
                    ',
                    'text_template' => 'Monthly Newsletter from NextCode Group',
                    'description' => 'Monthly newsletter template',
                    'category' => 'newsletter'
                ],
                [
                    'name' => 'Product Announcement',
                    'slug' => 'product-announcement',
                    'subject_template' => 'New Service Launch: {{service_name}}',
                    'html_template' => '
                        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                            <h2 style="color: #667eea;">New Service Launch!</h2>
                            <h3>{{service_name}}</h3>
                            <p>{{service_description}}</p>
                            <p><strong>Key Features:</strong></p>
                            <ul>{{features}}</ul>
                            <p><a href="{{service_url}}" style="background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Learn More</a></p>
                            <p>Best regards,<br>NextCode Group Team</p>
                        </div>
                    ',
                    'text_template' => 'New Service Launch: {{service_name}} - {{service_description}}',
                    'description' => 'Template for product/service announcements',
                    'category' => 'announcement'
                ]
            ];
            
            foreach ($templates as $template) {
                $stmt = $this->pdo->prepare("
                    INSERT IGNORE INTO newsletter_templates 
                    (name, slug, subject_template, html_template, text_template, description, category) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $template['name'],
                    $template['slug'],
                    $template['subject_template'],
                    $template['html_template'],
                    $template['text_template'],
                    $template['description'],
                    $template['category']
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Default templates insertion failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Newsletter'a abone ol
     */
    public function subscribe($email, $additionalData = []) {
        if (!$this->pdo) return ['success' => false, 'message' => 'Database connection failed'];
        
        try {
            // Email formatını kontrol et
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }
            
            // Zaten abone mi kontrol et
            $stmt = $this->pdo->prepare("SELECT id, status FROM newsletter_subscribers WHERE email = ?");
            $stmt->execute([$email]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                if ($existing['status'] === 'active') {
                    return ['success' => false, 'message' => 'Already subscribed'];
                } else {
                    // Yeniden aktifleştir
                    $stmt = $this->pdo->prepare("
                        UPDATE newsletter_subscribers 
                        SET status = 'active', subscribed_at = CURRENT_TIMESTAMP, unsubscribed_at = NULL 
                        WHERE id = ?
                    ");
                    $stmt->execute([$existing['id']]);
                    return ['success' => true, 'message' => 'Resubscribed successfully'];
                }
            }
            
            // Yeni abone ekle
            $stmt = $this->pdo->prepare("
                INSERT INTO newsletter_subscribers 
                (email, first_name, last_name, subscription_source, interests, language, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $email,
                $additionalData['first_name'] ?? null,
                $additionalData['last_name'] ?? null,
                $additionalData['source'] ?? 'website',
                $additionalData['interests'] ? json_encode($additionalData['interests']) : null,
                $additionalData['language'] ?? 'en',
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            if ($result) {
                // Welcome email gönder
                $this->sendWelcomeEmail($email, $additionalData);
                
                return ['success' => true, 'message' => 'Subscribed successfully'];
            }
            
            return ['success' => false, 'message' => 'Subscription failed'];
            
        } catch (Exception $e) {
            error_log('Newsletter subscription failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Subscription failed'];
        }
    }
    
    /**
     * Newsletter'dan çık
     */
    public function unsubscribe($email) {
        if (!$this->pdo) return ['success' => false, 'message' => 'Database connection failed'];
        
        try {
            $stmt = $this->pdo->prepare("
                UPDATE newsletter_subscribers 
                SET status = 'unsubscribed', unsubscribed_at = CURRENT_TIMESTAMP 
                WHERE email = ? AND status = 'active'
            ");
            
            $result = $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Unsubscribed successfully'];
            }
            
            return ['success' => false, 'message' => 'Email not found or already unsubscribed'];
            
        } catch (Exception $e) {
            error_log('Newsletter unsubscribe failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unsubscribe failed'];
        }
    }
    
    /**
     * Newsletter campaign oluştur
     */
    public function createCampaign($campaignData) {
        if (!$this->pdo) return ['success' => false, 'message' => 'Database connection failed'];
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO newsletter_campaigns 
                (name, subject, content, template_type, sender_name, sender_email, reply_to, target_audience) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $campaignData['name'],
                $campaignData['subject'],
                $campaignData['content'],
                $campaignData['template_type'] ?? 'html',
                $campaignData['sender_name'] ?? 'NextCode Group',
                $campaignData['sender_email'] ?? 'newsletter@nextcodegroup.com',
                $campaignData['reply_to'] ?? null,
                $campaignData['target_audience'] ? json_encode($campaignData['target_audience']) : null
            ]);
            
            if ($result) {
                $campaignId = $this->pdo->lastInsertId();
                return ['success' => true, 'campaign_id' => $campaignId];
            }
            
            return ['success' => false, 'message' => 'Campaign creation failed'];
            
        } catch (Exception $e) {
            error_log('Campaign creation failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Campaign creation failed'];
        }
    }
    
    /**
     * Newsletter gönder
     */
    public function sendNewsletter($campaignId) {
        if (!$this->pdo) return ['success' => false, 'message' => 'Database connection failed'];
        
        try {
            // Campaign bilgilerini al
            $stmt = $this->pdo->prepare("SELECT * FROM newsletter_campaigns WHERE id = ?");
            $stmt->execute([$campaignId]);
            $campaign = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$campaign) {
                return ['success' => false, 'message' => 'Campaign not found'];
            }
            
            // Hedef kitleyi al
            $subscribers = $this->getTargetSubscribers($campaign['target_audience']);
            
            $sentCount = 0;
            $failedCount = 0;
            
            foreach ($subscribers as $subscriber) {
                if ($this->sendEmailToSubscriber($campaign, $subscriber)) {
                    $sentCount++;
                    
                    // Analytics kaydet
                    $this->logEmailAction($campaignId, $subscriber['id'], 'sent');
                    
                    // Subscriber'ı güncelle
                    $this->updateSubscriberStats($subscriber['id']);
                } else {
                    $failedCount++;
                }
            }
            
            // Campaign'i güncelle
            $stmt = $this->pdo->prepare("
                UPDATE newsletter_campaigns 
                SET status = 'sent', sent_at = CURRENT_TIMESTAMP, delivered_count = ? 
                WHERE id = ?
            ");
            $stmt->execute([$sentCount, $campaignId]);
            
            return [
                'success' => true, 
                'sent' => $sentCount, 
                'failed' => $failedCount,
                'message' => "Newsletter sent to {$sentCount} subscribers"
            ];
            
        } catch (Exception $e) {
            error_log('Newsletter sending failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Newsletter sending failed'];
        }
    }
    
    /**
     * Hedef aboneleri al
     */
    private function getTargetSubscribers($targetAudience) {
        try {
            $sql = "SELECT * FROM newsletter_subscribers WHERE status = 'active'";
            $params = [];
            
            if ($targetAudience) {
                $criteria = json_decode($targetAudience, true);
                if ($criteria && isset($criteria['interests'])) {
                    $sql .= " AND interests LIKE ?";
                    $params[] = "%{$criteria['interests']}%";
                }
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Aboneye email gönder
     */
    private function sendEmailToSubscriber($campaign, $subscriber) {
        try {
            $subject = $campaign['subject'];
            $content = $campaign['content'];
            
            // Template değişkenlerini değiştir
            $content = str_replace('{{first_name}}', $subscriber['first_name'] ?? '', $content);
            $content = str_replace('{{email}}', $subscriber['email'], $content);
            
            $headers = "From: {$campaign['sender_name']} <{$campaign['sender_email']}>\r\n";
            $headers .= "Reply-To: {$campaign['reply_to']}\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            return mail($subscriber['email'], $subject, $content, $headers);
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Welcome email gönder
     */
    private function sendWelcomeEmail($email, $additionalData) {
        try {
            $template = $this->getTemplate('welcome');
            if (!$template) return false;
            
            $subject = $template['subject_template'];
            $content = $template['html_template'];
            
            // Template değişkenlerini değiştir
            $content = str_replace('{{first_name}}', $additionalData['first_name'] ?? '', $content);
            $content = str_replace('{{email}}', $email, $content);
            
            $headers = "From: NextCode Group <newsletter@nextcodegroup.com>\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            return mail($email, $subject, $content, $headers);
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Template al
     */
    private function getTemplate($slug) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM newsletter_templates WHERE slug = ? AND is_active = 1");
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Email action'ını kaydet
     */
    private function logEmailAction($campaignId, $subscriberId, $action, $actionData = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO newsletter_analytics 
                (campaign_id, subscriber_id, email_address, action, action_data, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $subscriber = $this->getSubscriberById($subscriberId);
            $email = $subscriber ? $subscriber['email'] : 'unknown';
            
            $stmt->execute([
                $campaignId,
                $subscriberId,
                $email,
                $action,
                $actionData ? json_encode($actionData) : null,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            error_log('Email action logging failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Subscriber'ı ID ile al
     */
    private function getSubscriberById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM newsletter_subscribers WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Subscriber istatistiklerini güncelle
     */
    private function updateSubscriberStats($subscriberId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE newsletter_subscribers 
                SET email_count = email_count + 1, last_email_sent = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $stmt->execute([$subscriberId]);
        } catch (Exception $e) {
            error_log('Subscriber stats update failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Newsletter istatistiklerini al
     */
    public function getNewsletterStats() {
        if (!$this->pdo) return [];
        
        try {
            $stats = [];
            
            // Toplam abone sayısı
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active'");
            $stats['total_subscribers'] = $stmt->fetchColumn();
            
            // Bu ayki yeni aboneler
            $stmt = $this->pdo->query("
                SELECT COUNT(*) FROM newsletter_subscribers 
                WHERE status = 'active' AND MONTH(subscribed_at) = MONTH(CURRENT_DATE()) 
                AND YEAR(subscribed_at) = YEAR(CURRENT_DATE())
            ");
            $stats['monthly_new_subscribers'] = $stmt->fetchColumn();
            
            // Toplam campaign sayısı
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM newsletter_campaigns");
            $stats['total_campaigns'] = $stmt->fetchColumn();
            
            // Gönderilen email sayısı
            $stmt = $this->pdo->query("SELECT SUM(delivered_count) FROM newsletter_campaigns");
            $stats['total_emails_sent'] = $stmt->fetchColumn() ?: 0;
            
            // Açılma oranı
            $stmt = $this->pdo->query("SELECT SUM(opened_count) FROM newsletter_campaigns");
            $totalOpened = $stmt->fetchColumn() ?: 0;
            $stats['open_rate'] = $stats['total_emails_sent'] > 0 ? 
                round(($totalOpened / $stats['total_emails_sent']) * 100, 2) : 0;
            
            // Tıklama oranı
            $stmt = $this->pdo->query("SELECT SUM(clicked_count) FROM newsletter_campaigns");
            $totalClicked = $stmt->fetchColumn() ?: 0;
            $stats['click_rate'] = $stats['total_emails_sent'] > 0 ? 
                round(($totalClicked / $stats['total_emails_sent']) * 100, 2) : 0;
            
            return $stats;
        } catch (Exception $e) {
            error_log('Newsletter stats fetch failed: ' . $e->getMessage());
            return [];
        }
    }
}

// CLI veya web erişimi için
if (php_sapi_name() === 'cli' || isset($_GET['setup'])) {
    $newsletterSystem = new NewsletterSystem();
    
    echo "=== Newsletter System ===\n";
    
    if ($newsletterSystem->createNewsletterTables()) {
        echo "✓ Newsletter tables created successfully\n";
        
        if ($newsletterSystem->insertDefaultTemplates()) {
            echo "✓ Default newsletter templates inserted successfully\n";
        }
        
        // İstatistikleri göster
        $stats = $newsletterSystem->getNewsletterStats();
        echo "\nNewsletter Statistics:\n";
        echo "- Total Subscribers: {$stats['total_subscribers']}\n";
        echo "- Monthly New Subscribers: {$stats['monthly_new_subscribers']}\n";
        echo "- Total Campaigns: {$stats['total_campaigns']}\n";
        echo "- Total Emails Sent: {$stats['total_emails_sent']}\n";
        echo "- Open Rate: {$stats['open_rate']}%\n";
        echo "- Click Rate: {$stats['click_rate']}%\n";
    } else {
        echo "✗ Failed to create newsletter tables\n";
    }
    
    echo "\n=== Newsletter System Complete ===\n";
}
?>
