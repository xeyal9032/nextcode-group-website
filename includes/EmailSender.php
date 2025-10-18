<?php
// Email Sender Class for NextCode Group
// Production-ready email sending functionality

// Include email configuration
require_once __DIR__ . '/../config/email.php';

class EmailSender {
    private $config;
    private $lastError;
    
    public function __construct() {
        $this->config = getEmailConfig();
        $this->lastError = '';
    }
    
    /**
     * Send contact form email
     */
    public function sendContactEmail($data) {
        if (!isEmailEnabled()) {
            $this->lastError = 'Email service is disabled';
            return false;
        }
        
        try {
            // Validate input data
            if (!$this->validateContactData($data)) {
                return false;
            }
            
            // Create email content
            $subject = CONTACT_SUBJECT_PREFIX . ($data['subject'] ?? 'Yeni Müştəri Mesajı');
            $htmlBody = $this->createContactEmailHTML($data);
            $textBody = $this->createContactEmailText($data);
            
            // Send email
            $result = $this->sendEmail([
                'to_email' => CONTACT_EMAIL,
                'to_name' => CONTACT_NAME,
                'subject' => $subject,
                'html_body' => $htmlBody,
                'text_body' => $textBody,
                'reply_to' => [
                    'email' => $data['email'],
                    'name' => $data['name']
                ]
            ]);
            
            if ($result) {
                // Send auto-reply to customer
                $this->sendAutoReply($data);
                logEmailActivity('Contact email sent successfully to: ' . CONTACT_EMAIL);
                return true;
            }
            
        } catch (Exception $e) {
            $this->lastError = 'Email sending failed: ' . $e->getMessage();
            logEmailActivity('Contact email failed: ' . $e->getMessage(), 'ERROR');
        }
        
        return false;
    }
    
    /**
     * Send auto-reply email to customer
     */
    private function sendAutoReply($data) {
        try {
            $subject = AUTO_REPLY_SUBJECT;
            $htmlBody = $this->createAutoReplyHTML($data);
            $textBody = $this->createAutoReplyText($data);
            
            $this->sendEmail([
                'to_email' => $data['email'],
                'to_name' => $data['name'],
                'subject' => $subject,
                'html_body' => $htmlBody,
                'text_body' => $textBody
            ]);
            
            logEmailActivity('Auto-reply sent to: ' . $data['email']);
            
        } catch (Exception $e) {
            logEmailActivity('Auto-reply failed: ' . $e->getMessage(), 'ERROR');
        }
    }
    
    /**
     * Main email sending function
     */
    private function sendEmail($emailData) {
        // Use PHP's built-in mail function for simplicity
        // In production, you might want to use PHPMailer or similar
        
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>';
        
        if (isset($emailData['reply_to'])) {
            $headers[] = 'Reply-To: ' . $emailData['reply_to']['name'] . ' <' . $emailData['reply_to']['email'] . '>';
        }
        
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        $headers[] = 'X-Priority: 3';
        
        $headerString = implode("\r\n", $headers);
        
        // Send email
        $result = mail(
            $emailData['to_email'],
            $emailData['subject'],
            $emailData['html_body'],
            $headerString
        );
        
        if (!$result) {
            $this->lastError = 'Mail function returned false';
            return false;
        }
        
        return true;
    }
    
    /**
     * Create HTML email content for contact form
     */
    private function createContactEmailHTML($data) {
        $html = '
        <!DOCTYPE html>
        <html lang="az">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Yeni Müştəri Mesajı</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px; border-radius: 10px; }
                .header { background: #1e40af; color: white; padding: 20px; border-radius: 10px 10px 0 0; text-align: center; }
                .content { background: white; padding: 20px; border-radius: 0 0 10px 10px; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #1e40af; }
                .value { margin-top: 5px; }
                .message-box { background: #f0f9ff; border-left: 4px solid #1e40af; padding: 15px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🆕 Yeni Müştəri Mesajı</h1>
                    <p>NextCode Group Website</p>
                </div>
                <div class="content">
                    <div class="field">
                        <div class="label">👤 Ad:</div>
                        <div class="value">' . htmlspecialchars($data['name']) . '</div>
                    </div>
                    
                    <div class="field">
                        <div class="label">📧 Email:</div>
                        <div class="value"><a href="mailto:' . htmlspecialchars($data['email']) . '">' . htmlspecialchars($data['email']) . '</a></div>
                    </div>';
        
        if (!empty($data['phone'])) {
            $html .= '
                    <div class="field">
                        <div class="label">📞 Telefon:</div>
                        <div class="value"><a href="tel:' . htmlspecialchars($data['phone']) . '">' . htmlspecialchars($data['phone']) . '</a></div>
                    </div>';
        }
        
        if (!empty($data['company'])) {
            $html .= '
                    <div class="field">
                        <div class="label">🏢 Şirkət:</div>
                        <div class="value">' . htmlspecialchars($data['company']) . '</div>
                    </div>';
        }
        
        if (!empty($data['service'])) {
            $html .= '
                    <div class="field">
                        <div class="label">🔧 Maraqlanılan Xidmət:</div>
                        <div class="value">' . htmlspecialchars($data['service']) . '</div>
                    </div>';
        }
        
        if (!empty($data['budget'])) {
            $html .= '
                    <div class="field">
                        <div class="label">💰 Büdcə:</div>
                        <div class="value">' . htmlspecialchars($data['budget']) . '</div>
                    </div>';
        }
        
        if (!empty($data['subject'])) {
            $html .= '
                    <div class="field">
                        <div class="label">📋 Mövzu:</div>
                        <div class="value">' . htmlspecialchars($data['subject']) . '</div>
                    </div>';
        }
        
        $html .= '
                    <div class="message-box">
                        <div class="label">💬 Mesaj:</div>
                        <div class="value">' . nl2br(htmlspecialchars($data['message'])) . '</div>
                    </div>
                    
                    <div class="field">
                        <div class="label">⏰ Tarix:</div>
                        <div class="value">' . date('d.m.Y H:i') . '</div>
                    </div>
                    
                    <div class="field">
                        <div class="label">🌐 IP Ünvanı:</div>
                        <div class="value">' . ($_SERVER['REMOTE_ADDR'] ?? 'Bilinmir') . '</div>
                    </div>
                </div>
                <div class="footer">
                    <p>Bu mesaj NextCode Group veb saytının əlaqə formasından göndərilmişdir.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Create text email content for contact form
     */
    private function createContactEmailText($data) {
        $text = "YENİ MÜŞTƏRİ MESAJI\n";
        $text .= "==================\n\n";
        $text .= "Ad: " . $data['name'] . "\n";
        $text .= "Email: " . $data['email'] . "\n";
        
        if (!empty($data['phone'])) {
            $text .= "Telefon: " . $data['phone'] . "\n";
        }
        
        if (!empty($data['company'])) {
            $text .= "Şirkət: " . $data['company'] . "\n";
        }
        
        if (!empty($data['service'])) {
            $text .= "Xidmət: " . $data['service'] . "\n";
        }
        
        if (!empty($data['budget'])) {
            $text .= "Büdcə: " . $data['budget'] . "\n";
        }
        
        if (!empty($data['subject'])) {
            $text .= "Mövzu: " . $data['subject'] . "\n";
        }
        
        $text .= "\nMesaj:\n";
        $text .= "-------\n";
        $text .= $data['message'] . "\n\n";
        $text .= "Tarix: " . date('d.m.Y H:i') . "\n";
        $text .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Bilinmir') . "\n";
        
        return $text;
    }
    
    /**
     * Create auto-reply HTML email
     */
    private function createAutoReplyHTML($data) {
        $html = '
        <!DOCTYPE html>
        <html lang="az">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mesajınız Alındı</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px; border-radius: 10px; }
                .header { background: #10b981; color: white; padding: 20px; border-radius: 10px 10px 0 0; text-align: center; }
                .content { background: white; padding: 20px; border-radius: 0 0 10px 10px; }
                .highlight { background: #f0f9ff; border-left: 4px solid #1e40af; padding: 15px; margin: 20px 0; }
                .contact-info { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Mesajınız Alındı!</h1>
                    <p>NextCode Group</p>
                </div>
                <div class="content">
                    <p>Salam ' . htmlspecialchars($data['name']) . ',</p>
                    
                    <p>Mesajınızı aldıq və təşəkkür edirik ki, bizimlə əlaqə saxladınız!</p>
                    
                    <div class="highlight">
                        <h3>📋 Mesajınızın Mövzusu:</h3>
                        <p>' . htmlspecialchars($data['subject'] ?? 'Ümumi Sual') . '</p>
                    </div>
                    
                    <p>Bizim komandamız mesajınızı nəzərdən keçirir və ən qısa zamanda sizinlə əlaqə saxlayacaq.</p>
                    
                    <div class="contact-info">
                        <h3>📞 Tez Əlaqə</h3>
                        <p><strong>Telefon:</strong> +380972580000<br>
                        <strong>Email:</strong> xeyalcemilli9032@gmail.com<br>
                        <strong>WhatsApp:</strong> <a href="https://wa.me/380972580000">WhatsApp-a Yazın</a></p>
                    </div>
                    
                    <p>Hörmətlə,<br><strong>NextCode Group Komandası</strong></p>
                </div>
                <div class="footer">
                    <p>Bu avtomatik cavab mesajıdır. Lütfən bu mesaja cavab yazmayın.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Create auto-reply text email
     */
    private function createAutoReplyText($data) {
        $text = "MESAJINIZ ALINDI!\n";
        $text .= "==================\n\n";
        $text .= "Salam " . $data['name'] . ",\n\n";
        $text .= "Mesajınızı aldıq və təşəkkür edirik ki, bizimlə əlaqə saxladınız!\n\n";
        $text .= "Mesajınızın Mövzusu: " . ($data['subject'] ?? 'Ümumi Sual') . "\n\n";
        $text .= "Bizim komandamız mesajınızı nəzərdən keçirir və ən qısa zamanda sizinlə əlaqə saxlayacaq.\n\n";
        $text .= "TEZ ƏLAQƏ:\n";
        $text .= "Telefon: +380972580000\n";
        $text .= "Email: xeyalcemilli9032@gmail.com\n";
        $text .= "WhatsApp: https://wa.me/380972580000\n\n";
        $text .= "Hörmətlə,\nNextCode Group Komandası\n\n";
        $text .= "Bu avtomatik cavab mesajıdır. Lütfən bu mesaja cavab yazmayın.\n";
        
        return $text;
    }
    
    /**
     * Validate contact form data
     */
    private function validateContactData($data) {
        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            $this->lastError = 'Ad, email və mesaj sahələri mütləqdir';
            return false;
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->lastError = 'Düzgün email ünvanı daxil edin';
            return false;
        }
        
        return true;
    }
    
    /**
     * Get last error message
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Test email configuration
     */
    public function testEmailConfig() {
        if (!isEmailEnabled()) {
            return ['success' => false, 'message' => 'Email service is disabled'];
        }
        
        if (!validateEmailConfig()) {
            return ['success' => false, 'message' => 'Email configuration is invalid'];
        }
        
        return ['success' => true, 'message' => 'Email configuration is valid'];
    }
}
?>
