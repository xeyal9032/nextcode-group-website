<?php
// NextCode Group - Əlaqə Səhifəsi
// Production Environment Configuration

// Define secure access constant
define('SECURE_ACCESS', true);

// Include API router for handling API requests
require_once 'api-router.php';

// Database bağlantısını include et
require_once 'config/database.php';

// Security configuration include et
require_once 'config/security.php';

// Ensure session is started (security.php should handle this, but double-check)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Include page functions
require_once 'includes/page_functions.php';
require_once 'includes/content_helper.php';

// Görsel yönetimi fonksiyonları
function getImageUrl($image_key, $default_url = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT image_url FROM site_images WHERE image_key = ? AND is_active = 1");
        $stmt->execute([$image_key]);
        $result = $stmt->fetch();
        
        return $result ? $result['image_url'] : $default_url;
    } catch (Exception $e) {
        error_log('Error getting image URL: ' . $e->getMessage());
        return $default_url;
    }
}

function getImageAlt($image_key, $default_alt = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT image_alt FROM site_images WHERE image_key = ? AND is_active = 1");
        $stmt->execute([$image_key]);
        $result = $stmt->fetch();
        
        return $result ? $result['image_alt'] : $default_alt;
    } catch (Exception $e) {
        error_log('Error getting image alt: ' . $e->getMessage());
        return $default_alt;
    }
}

function getImageTitle($image_key, $default_title = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT image_title FROM site_images WHERE image_key = ? AND is_active = 1");
        $stmt->execute([$image_key]);
        $result = $stmt->fetch();
        
        return $result ? $result['image_title'] : $default_title;
    } catch (Exception $e) {
        error_log('Error getting image title: ' . $e->getMessage());
        return $default_title;
    }
}

function getImageTag($image_key, $default_url = '', $default_alt = '', $attributes = []) {
    $url = htmlspecialchars(getImageUrl($image_key, $default_url), ENT_QUOTES, 'UTF-8');
    $alt = htmlspecialchars(getImageAlt($image_key, $default_alt), ENT_QUOTES, 'UTF-8');
    $title = htmlspecialchars(getImageTitle($image_key, $alt), ENT_QUOTES, 'UTF-8');
    
    if (empty($url)) {
        return '';
    }
    
    $attr_string = '';
    foreach ($attributes as $key => $value) {
        // Sanitize both key and value to prevent XSS
        $safe_key = preg_replace('/[^a-z0-9\-_]/i', '', $key);
        $safe_value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $attr_string .= " {$safe_key}=\"{$safe_value}\"";
    }
    
    return "<img src=\"{$url}\" alt=\"{$alt}\" title=\"{$title}\"{$attr_string}>";
}

// Dynamic content variables
$page_title = getTextContent('contact_page_title', 'Contact - NextCode Group');
$meta_description = getTextContent('contact_meta_description', 'Contact NextCode Group. Get information about our digital marketing services.');
$current_page = 'contact';

// Form submission handling
$message_sent = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = 'Təhlükəsizlik xətası baş verdi. Yenidən cəhd edin.';
    } else {
        // Sanitize and validate input (PHP 8.1+ compatible)
        $name = htmlspecialchars(strip_tags(trim($_POST['name'] ?? '')), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $phone = htmlspecialchars(strip_tags(trim($_POST['phone'] ?? '')), ENT_QUOTES, 'UTF-8');
        $subject = htmlspecialchars(strip_tags(trim($_POST['subject'] ?? '')), ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars(strip_tags(trim($_POST['message'] ?? '')), ENT_QUOTES, 'UTF-8');
        
        // Validation
        if (empty($name) || empty($email) || empty($message)) {
            $error_message = 'Zəhmət olmasa bütün vacib sahələri doldurun.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Düzgün email ünvanı daxil edin.';
        } else {
            try {
                // Split name into first_name and last_name
                $name_parts = explode(' ', $name, 2);
                $first_name = $name_parts[0];
                $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
                
                // Insert into database
                $stmt = $pdo->prepare("INSERT INTO contact_messages (first_name, last_name, email, phone, subject, message, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([
                    $first_name, 
                    $last_name, 
                    $email, 
                    $phone, 
                    $subject, 
                    $message, 
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
                
                $message_sent = true;
                
                // Clear form data
                $_POST = [];
                
            } catch (PDOException $e) {
                error_log('Contact form error: ' . $e->getMessage());
                $error_message = 'Mesaj göndərilmədi. Zəhmət olmasa yenidən cəhd edin.';
            }
        }
    }
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Company contact information
    $contact_info = [
        'address' => 'Bakı şəhəri, Nəsimi rayonu, 28 May küçəsi 15',
        'phone' => '+380 97 258 00 00',
        'email' => 'xeyalcemilli9032@gmail.com',
        'working_hours' => 'B.e - Cümə: 09:00 - 18:00'
    ];
// Include header
require_once 'includes/header.php';
?>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="modern-section modern-section--hero main-content">
        <div class="modern-container">
            <div class="modern-text-center">
                <h1 class="modern-heading modern-heading--h1 modern-m-6"><?php echo getTextContent('contact_header_title', 'Bizimlə Əlaqə'); ?></h1>
                <p class="modern-text modern-text--lead modern-m-6"><?php echo getTextContent('contact_header_subtitle', 'Layihənizi müzakirə etmək və sizə necə kömək edə biləcəyimizi öyrənmək üçün bizimlə əlaqə saxlayın'); ?></p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="modern-section modern-section--light">
        <div class="modern-container">
            <div class="modern-grid modern-grid--2">
                <!-- Contact Info -->
                <div class="modern-animate--fadeInLeft">
                    <div class="modern-text-center modern-m-8">
                        <h2 class="modern-heading modern-heading--h2 modern-m-6"><?php echo getTextContent('contact_info_title', 'Əlaqə Məlumatları'); ?></h2>
                        <p class="modern-text modern-text--lead modern-m-6"><?php echo getTextContent('contact_info_subtitle', 'Bizə müraciət etməyin müxtəlif yolları'); ?></p>
                    </div>
                    
                    <div class="modern-grid modern-grid--2">
                        <div class="modern-card modern-m-4">
                            <div class="modern-card__body modern-text-center">
                                <div class="modern-badge modern-badge--primary modern-m-4">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <h3 class="modern-heading modern-heading--h5 modern-m-4"><?php echo getTextContent('contact_address_title', 'Ünvan'); ?></h3>
                                <p class="modern-text"><?php echo getHtmlContent('contact_address_value', 'Bakı şəhəri, Nəsimi rayonu<br>28 May küçəsi 15'); ?></p>
                            </div>
                        </div>

                        <div class="modern-card modern-m-4">
                            <div class="modern-card__body modern-text-center">
                                <div class="modern-badge modern-badge--secondary modern-m-4">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <h3 class="modern-heading modern-heading--h5 modern-m-4"><?php echo getTextContent('contact_phone_title', 'Telefon'); ?></h3>
                                <p class="modern-text"><?php echo getHtmlContent('contact_phone_value', '<span data-phone>+380 97 258 00 00</span><br>+994997554919'); ?></p>
                            </div>
                        </div>

                        <div class="modern-card modern-m-4">
                            <div class="modern-card__body modern-text-center">
                                <div class="modern-badge modern-badge--accent modern-m-4">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <h3 class="modern-heading modern-heading--h5 modern-m-4">Email</h3>
                                <p class="modern-text">
                                    <a href="mailto:xeyalcemilli9032@gmail.com" style="color: inherit; text-decoration: none;">xeyalcemilli9032@gmail.com</a><br>
                                    <a href="mailto:info@nextcode.az" style="color: inherit; text-decoration: none;">info@nextcode.az</a>
                                </p>
                            </div>
                        </div>

                        <div class="modern-card modern-m-4">
                            <div class="modern-card__body modern-text-center">
                                <div class="modern-badge modern-badge--success modern-m-4">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h3 class="modern-heading modern-heading--h5 modern-m-4">İş Saatları</h3>
                                <p class="modern-text">Bazar ertəsi - Cümə: 09:00-18:00<br>Şənbə: 10:00-15:00</p>
                            </div>
                        </div>
                    </div>

                    <div class="modern-text-center modern-m-8">
                        <h3 class="modern-heading modern-heading--h4 modern-m-6">Sosial Şəbəkələr</h3>
                        <div class="modern-flex modern-justify-center modern-flex-wrap">
                            <a href="https://www.facebook.com/OstWind.LLC/?locale=ru_RU" 
                               class="modern-btn modern-btn--ghost modern-btn--sm modern-m-2" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://www.instagram.com/nextcodegroup/" 
                               class="modern-btn modern-btn--ghost modern-btn--sm modern-m-2" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.linkedin.com/company/nextcode-group" 
                               class="modern-btn modern-btn--ghost modern-btn--sm modern-m-2" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               title="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="https://wa.me/380972580000" 
                               class="modern-btn modern-btn--ghost modern-btn--sm modern-m-2" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="modern-animate--fadeInRight">
                    <div class="modern-card">
                        <div class="modern-card__header">
                            <h2 class="modern-heading modern-heading--h3">Mesaj Göndərin</h2>
                            <p class="modern-text">Layihəniz haqqında bizə yazın və ən qısa zamanda sizinlə əlaqə saxlayaq</p>
                        </div>
                        
                        <div class="modern-card__body">
                            <form class="contact-form" id="contactForm">
                                <div class="modern-form-group">
                                    <label class="modern-form-label" for="name">Ad *</label>
                                    <input type="text" id="name" name="name" class="modern-form-input" required>
                                </div>

                                <div class="modern-grid modern-grid--2">
                                    <div class="modern-form-group">
                                        <label class="modern-form-label" for="email">Email *</label>
                                        <input type="email" id="email" name="email" class="modern-form-input" required>
                                    </div>
                                    <div class="modern-form-group">
                                        <label class="modern-form-label" for="phone">Telefon</label>
                                        <input type="tel" id="phone" name="phone" class="modern-form-input">
                                    </div>
                                </div>

                                <div class="modern-form-group">
                                    <label class="modern-form-label" for="company">Şirkət/Təşkilat</label>
                                    <input type="text" id="company" name="company" class="modern-form-input">
                                </div>

                                <div class="modern-form-group">
                                    <label class="modern-form-label" for="subject">Mövzu *</label>
                                    <input type="text" id="subject" name="subject" class="modern-form-input" placeholder="Mesajınızın mövzusu..." required>
                                </div>

                                <div class="modern-grid modern-grid--2">
                                    <div class="modern-form-group">
                                        <label class="modern-form-label" for="service">Maraqlandığınız Xidmət</label>
                                        <select id="service" name="service" class="modern-form-select">
                                            <option value="">Xidmət seçin</option>
                                            <option value="seo">SEO Optimizasyonu</option>
                                            <option value="social-media">Sosyal Medya İdarəçiliyi</option>
                                            <option value="branding">Brendinq və Dizayn</option>
                                            <option value="advertising">Reklam Kampaniyaları</option>
                                            <option value="web-design">Veb Sayt Hazırlanması</option>
                                            <option value="email-marketing">Email Marketinq</option>
                                            <option value="consultation">Məsləhət</option>
                                            <option value="other">Digər</option>
                                        </select>
                                    </div>
                                    <div class="modern-form-group">
                                        <label class="modern-form-label" for="budget">Büdcə Aralığı</label>
                                        <select id="budget" name="budget" class="modern-form-select">
                                            <option value="">Büdcə seçin</option>
                                            <option value="500-1000">500-1000 AZN</option>
                                            <option value="1000-2500">1000-2500 AZN</option>
                                            <option value="2500-5000">2500-5000 AZN</option>
                                            <option value="5000+">5000+ AZN</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="modern-form-group">
                                    <label class="modern-form-label" for="message">Mesaj *</label>
                                    <textarea id="message" name="message" class="modern-form-textarea" placeholder="Layihəniz haqqında ətraflı məlumat verin..." required></textarea>
                                </div>

                                <div class="modern-form-group">
                                    <label class="modern-form-label">
                                        <input type="checkbox" id="privacy" name="privacy" required>
                                        <span class="modern-text">Şəxsi məlumatlarımın işlənməsinə razıyam və <a href="#">Məxfilik Siyasəti</a>ni qəbul edirəm *</span>
                                    </label>
                                </div>

                                <div class="modern-form-group">
                                    <label class="modern-form-label">
                                        <input type="checkbox" id="newsletter" name="newsletter">
                                        <span class="modern-text">Yeniliklər və xüsusi təkliflər haqqında məlumat almaq istəyirəm</span>
                                    </label>
                                </div>

                                <button type="submit" class="modern-btn modern-btn--primary modern-btn--lg" id="contactFormSubmit">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Mesaj Göndər
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="modern-section">
        <div class="modern-container">
            <div class="modern-text-center modern-m-8">
                <h2 class="modern-heading modern-heading--h2 modern-m-6">Bizim Yerləşdiyimiz Yer</h2>
                <p class="modern-text modern-text--lead modern-m-6">Ofisimizə gəlmək istəyirsinizsə, əvvəlcədən görüş təyin edin</p>
            </div>
            
            <div class="modern-card modern-card--elevated">
                <div class="modern-card__body modern-text-center">
                    <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&h=400&fit=crop" alt="Office Building" class="modern-card__image" loading="lazy" style="width: 100%; height: 300px; object-fit: cover; border-radius: 8px;">
                    
                    <div class="modern-m-6">
                        <i class="fas fa-map-marked-alt" style="font-size: 3rem; color: var(--primary-color);"></i>
                        <h3 class="modern-heading modern-heading--h4 modern-m-4">Xəritə burada göstəriləcək</h3>
                        <p class="modern-text modern-text--small modern-text-muted">Google Maps inteqrasiyası</p>
                    </div>
                    
                    <div class="modern-flex modern-justify-center modern-flex-wrap modern-m-6">
                        <button class="modern-btn modern-btn--primary modern-m-2" id="openGoogleMaps">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Google Maps Aç
                        </button>
                        <button class="modern-btn modern-btn--outline modern-m-2" id="getDirections">
                            <i class="fas fa-route me-2"></i>
                            Yol Tarifi Al
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- WhatsApp Contact Section -->
    <section class="modern-section modern-section--light whatsapp-section">
        <div class="modern-container">
            <div class="modern-text-center modern-m-8">
                <h2 class="modern-heading modern-heading--h2 modern-m-6">Dərhal Əlaqə</h2>
                <p class="modern-text modern-text--lead modern-m-6">Tez cavab üçün WhatsApp üzərindən bizimlə əlaqə saxlayın</p>
            </div>
            
            <div class="modern-card modern-card--elevated modern-text-center">
                <div class="modern-card__body">
                    <div class="modern-badge modern-badge--success modern-m-4" style="font-size: 3rem;">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h3 class="modern-heading modern-heading--h4 modern-m-4">WhatsApp üzərindən Mesaj Göndərin</h3>
                    <p class="modern-text modern-m-6">Layihəniz haqqında sürətli məsləhət və qiymət təklifi üçün WhatsApp-a yazın</p>
                    
                    <div class="modern-flex modern-justify-center modern-flex-wrap modern-gap-4 modern-m-6">
                        <a href="https://wa.me/380972580000?text=Merhaba! NextCode Group'dan bilgi almak istiyorum." 
                           class="modern-btn modern-btn--success modern-btn--lg" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           id="whatsappButton">
                            <i class="fab fa-whatsapp me-2"></i>
                            WhatsApp-a Yazın
                        </a>
                        
                        <button class="modern-btn modern-btn--outline modern-btn--lg" 
                                onclick="copyWhatsAppNumber()"
                                id="copyNumberBtn">
                            <i class="fas fa-copy me-2"></i>
                            Nömrəni Kopyala
                        </button>
                    </div>
                    
                    <div class="modern-text modern-text--small modern-text-muted modern-m-4">
                        <i class="fas fa-clock me-2"></i>
                        Həftə içi: 09:00 - 18:00 | Həftə sonu: 10:00 - 16:00
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="contact-faq">
        <div class="container">
            <div class="section-header">
                <h2>Tez-tez Verilən Suallar</h2>
                <p>Ən çox soruşulan sualların cavabları</p>
            </div>
            
            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq1" role="button">
                        <h3>Layihə üçün nə qədər vaxt lazımdır?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer collapse" id="faq1">
                        <p>Layihənin mürəkkəbliyindən asılı olaraq 2-8 həftə arası vaxt tələb olunur. Dəqiq müddət ilkin məsləhət zamanı müəyyən edilir.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Ödəniş şərtləri necədir?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Adətən 50% avans, 50% layihə tamamlandıqdan sonra ödənilir. Böyük layihələr üçün hissə-hissə ödəniş mümkündür.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Texniki dəstək təqdim edirsinizmi?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Bəli, bütün layihələr üçün 3 ay pulsuz texniki dəstək təqdim edirik. Sonrasında aylıq dəstək paketlərimiz mövcuddur.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Uzaqdan işləyirsinizmi?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Bəli, Azərbaycanın hər yerindən və xaricdən müştərilərlə işləyirik. Bütün proseslər onlayn aparılır.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>




    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Mesaj Göndərildi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Mesajınız uğurla göndərildi! Ən qısa zamanda sizinlə əlaqə saxlayacağıq.</p>
                    <p class="text-muted">Adətən 24 saat ərzində cavab veririk.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tamam</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Info Modal -->
    <div class="modal fade" id="serviceInfoModal" tabindex="-1" aria-labelledby="serviceInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceInfoModalLabel">Xidmət Məlumatları</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="service-info-content" id="serviceInfoContent">
                        <!-- Service information will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='pricing.php'">View Pricing</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        // Contact form functions
        function openGoogleMaps() {
            const address = "Bakı şəhəri, Nəsimi rayonu, 28 May küçəsi 15";
            const encodedAddress = encodeURIComponent(address);
            window.open(`https://www.google.com/maps/search/?api=1&query=${encodedAddress}`, '_blank');
        }
        
        function getDirections() {
            const address = "Bakı şəhəri, Nəsimi rayonu, 28 May küçəsi 15";
            const encodedAddress = encodeURIComponent(address);
            window.open(`https://www.google.com/maps/dir/?api=1&destination=${encodedAddress}`, '_blank');
        }
        
        function copyWhatsAppNumber() {
            const phoneNumber = '+380 97 258 00 00';
            navigator.clipboard.writeText(phoneNumber).then(() => {
                // Show success message
                const btn = document.querySelector('#copyNumberBtn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Kopyalandı!';
                btn.classList.remove('modern-btn--outline');
                btn.classList.add('modern-btn--success');
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('modern-btn--success');
                    btn.classList.add('modern-btn--outline');
                }, 2000);
            }).catch(() => {
                alert('Nömrəni kopyalamaq mümkün olmadı: ' + phoneNumber);
            });
        }
        
        // Form submission handling
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            const submitBtn = document.getElementById('contactFormSubmit');
            
            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Show loading state
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Göndərilir...';
                    submitBtn.disabled = true;
                    
                    // Collect form data
                    const formData = new FormData(form);
                    const formObject = {};
                    formData.forEach((value, key) => {
                        formObject[key] = value;
                    });
                    
                    // Send form data
                    fetch('/api/contact.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success modal
                            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                            successModal.show();
                            
                            // Reset form
                            form.reset();
                        } else {
                            alert('Mesaj göndərilmədi: ' + (data.error || 'Bilinməyən xəta'));
                        }
                    })
                    .catch(error => {
                        console.error('Form submission error:', error);
                        alert('Mesaj göndərilmədi: ' + error.message);
                    })
                    .finally(() => {
                        // Reset button
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Mesaj Göndər';
                        submitBtn.disabled = false;
                    });
                });
            }
            
            // FAQ Toggle Function
            document.querySelectorAll('.faq-question').forEach(question => {
                question.addEventListener('click', function() {
                    const faqItem = this.closest('.faq-item');
                    const answer = faqItem.querySelector('.faq-answer');
                    const icon = this.querySelector('i');
                    
                    // Close all other FAQ items
                    document.querySelectorAll('.faq-item').forEach(item => {
                        if (item !== faqItem) {
                            item.classList.remove('active');
                            item.querySelector('.faq-answer').style.maxHeight = '0';
                            item.querySelector('.faq-question i').classList.remove('fa-minus');
                            item.querySelector('.faq-question i').classList.add('fa-plus');
                        }
                    });
                    
                    // Toggle current FAQ item
                    if (faqItem.classList.contains('active')) {
                        faqItem.classList.remove('active');
                        answer.style.maxHeight = '0';
                        icon.classList.remove('fa-minus');
                        icon.classList.add('fa-plus');
                    } else {
                        faqItem.classList.add('active');
                        answer.style.maxHeight = answer.scrollHeight + 'px';
                        icon.classList.remove('fa-plus');
                        icon.classList.add('fa-minus');
                    }
                });
            });
        });
    </script>

    <script src="js/error-handler.js"></script>
    <script src="js/contact-simple.js"></script>

<?php
// Include footer
require_once 'includes/footer.php';
?>