<?php
// NextCode Group - Əlaqə Səhifəsi
// Production Environment Configuration

// Define secure access constant
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Include API router for handling API requests
require_once 'api-router.php';

// Database bağlantısını include et
require_once 'config/database.php';

// Security configuration include et
require_once 'config/security.php';

// Session başlat
session_start();

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
        // Sanitize and validate input
        $name = filter_var(trim($_POST['name'] ?? ''), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $phone = filter_var(trim($_POST['phone'] ?? ''), FILTER_SANITIZE_STRING);
        $subject = filter_var(trim($_POST['subject'] ?? ''), FILTER_SANITIZE_STRING);
        $message = filter_var(trim($_POST['message'] ?? ''), FILTER_SANITIZE_STRING);
        
        // Validation
        if (empty($name) || empty($email) || empty($message)) {
            $error_message = 'Zəhmət olmasa bütün vacib sahələri doldurun.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Düzgün email ünvanı daxil edin.';
        } else {
            try {
                // Insert into database
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, created_at, ip_address) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
                $stmt->execute([$name, $email, $phone, $subject, $message, $_SERVER['REMOTE_ADDR']]);
                
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
        'address' => 'Xocalı prospekti 11, Block A, 3-cü mərtəbə, Bakı 1008, Azərbaycan',
        'phone' => '+380972580000',
        'email' => 'xeyalcemilli9032@gmail.com',
        'working_hours' => 'B.e - Cümə: 09:00 - 18:00'
    ];
// Include header
require_once 'includes/header.php';
?>
            </div>
        </div>
    </nav>

    <!-- Professional Hero Section -->
    <section class="contact-hero">
        <div class="hero-background"></div>
        <div class="hero-overlay"></div>
        <div class="hero-pattern"></div>
        <div class="modern-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-comments"></i>
                    <span>Professional Contact</span>
                </div>
                
                <!-- Theme Toggle Button -->
                <div class="theme-toggle-container">
                    <button class="theme-toggle-btn" onclick="toggleTheme()" title="Tema Değiştir">
                        <i class="fas fa-sun theme-icon-light"></i>
                        <i class="fas fa-moon theme-icon-dark"></i>
                    </button>
                </div>
                <h1 class="hero-title"><?php echo getTextContent('contact_header_title', 'Bizimlə Əlaqə - Ultra Modern'); ?></h1>
                <p class="hero-subtitle"><?php echo getTextContent('contact_header_subtitle', 'Layihənizi müzakirə etmək və sizə necə kömək edə biləcəyimizi öyrənmək üçün bizimlə əlaqə saxlayın'); ?></p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Professional Support</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24h</div>
                        <div class="stat-label">Response Time</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Client Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern Contact Info Section -->
    <section class="contact-info-section">
        <div class="modern-container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="fas fa-info-circle"></i>
                    <span>Əlaqə Məlumatları</span>
                </div>
                <h2 class="section-title"><?php echo getTextContent('contact_info_title', 'Bizə Müraciət Edin'); ?></h2>
                <p class="section-subtitle"><?php echo getTextContent('contact_info_subtitle', 'Bizə müraciət etməyin müxtəlif yolları'); ?></p>
                    </div>
                    
            <div class="contact-cards-grid">
                <div class="contact-card contact-card--address" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                    <div class="card-content">
                        <h3 class="card-title"><?php echo getTextContent('contact_address_title', 'Ünvan'); ?></h3>
                        <p class="card-text" data-address><?php echo getHtmlContent('contact_address_value', 'Bakı şəhəri, Nəsimi rayonu<br>28 May küçəsi 15'); ?></p>
                    </div>
                    <div class="card-action">
                        <button class="action-btn" onclick="openGoogleMaps()">
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                            </div>
                        </div>

                <div class="contact-card contact-card--phone" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                    <div class="card-content">
                        <h3 class="card-title"><?php echo getTextContent('contact_phone_title', 'Telefon'); ?></h3>
                        <p class="card-text"><?php echo getHtmlContent('contact_phone_value', '<span data-phone>+380972580000</span><br>+994997554919'); ?></p>
                    </div>
                    <div class="card-action">
                        <button class="action-btn" onclick="callPhone()">
                            <i class="fas fa-phone"></i>
                        </button>
                            </div>
                        </div>

                <div class="contact-card contact-card--email" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                    <div class="card-content">
                        <h3 class="card-title">Email</h3>
                        <p class="card-text" data-email>xeyalcemilli9032@gmail.com<br>xeyalcemilli9032@gmail.com</p>
                    </div>
                    <div class="card-action">
                        <button class="action-btn" onclick="sendEmail()">
                            <i class="fas fa-envelope"></i>
                        </button>
                            </div>
                        </div>

                <div class="contact-card contact-card--hours" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                    <div class="card-content">
                        <h3 class="card-title">İş Saatları</h3>
                        <p class="card-text">Bazar ertəsi - Cümə: 09:00-18:00<br>Şənbə: 10:00-15:00</p>
                    </div>
                    <div class="card-action">
                        <div class="status-indicator">
                            <span class="status-dot"></span>
                            <span class="status-text">Açıq</span>
                        </div>
                            </div>
                        </div>
                    </div>

            <!-- Social Media Section -->
            <div class="social-section">
                <h3 class="social-title">Sosial Şəbəkələr</h3>
                <div class="social-links">
                    <a href="https://www.facebook.com/OstWind.LLC/?locale=ru_RU" class="social-link social-link--facebook" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-facebook-f"></i>
                        <span>Facebook</span>
                            </a>
                    <a href="https://www.instagram.com/nextcodegroup/" class="social-link social-link--instagram" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-instagram"></i>
                        <span>Instagram</span>
                            </a>
                    <a href="#" class="social-link social-link--linkedin" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-linkedin-in"></i>
                        <span>LinkedIn</span>
                            </a>
                    <a href="#" class="social-link social-link--twitter" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-twitter"></i>
                        <span>Twitter</span>
                            </a>
                    <a href="#" class="social-link social-link--youtube" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-youtube"></i>
                        <span>YouTube</span>
                            </a>
                        </div>
                    </div>
                </div>
    </section>

    <!-- Modern Contact Form Section -->
    <section class="contact-form-section">
        <div class="modern-container">
            <div class="form-container">
                <div class="form-header">
                    <div class="form-badge">
                        <i class="fas fa-paper-plane"></i>
                        <span>Mesaj Göndərin</span>
                    </div>
                    <h2 class="form-title">Layihənizi Müzakirə Edək</h2>
                    <p class="form-subtitle">Layihəniz haqqında bizə yazın və ən qısa zamanda sizinlə əlaqə saxlayaq</p>
                        </div>
                        
                <div class="form-wrapper">
                    <form class="modern-contact-form" id="contactForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="name">
                                    <i class="fas fa-user"></i>
                                    Ad *
                                </label>
                                <input type="text" id="name" name="name" class="form-input" required>
                                <div class="form-focus-line"></div>
                            </div>
                                </div>

                        <div class="form-row form-row--2">
                            <div class="form-group">
                                <label class="form-label" for="email">
                                    <i class="fas fa-envelope"></i>
                                    Email *
                                </label>
                                <input type="email" id="email" name="email" class="form-input" required>
                                <div class="form-focus-line"></div>
                                    </div>
                            <div class="form-group">
                                <label class="form-label" for="phone">
                                    <i class="fas fa-phone"></i>
                                    Telefon
                                </label>
                                <input type="tel" id="phone" name="phone" class="form-input">
                                <div class="form-focus-line"></div>
                                    </div>
                                </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="company">
                                    <i class="fas fa-building"></i>
                                    Şirkət/Təşkilat
                                </label>
                                <input type="text" id="company" name="company" class="form-input">
                                <div class="form-focus-line"></div>
                            </div>
                                </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="subject">
                                    <i class="fas fa-tag"></i>
                                    Mövzu *
                                </label>
                                <input type="text" id="subject" name="subject" class="form-input" placeholder="Mesajınızın mövzusu..." required>
                                <div class="form-focus-line"></div>
                            </div>
                                </div>

                        <div class="form-row form-row--2">
                            <div class="form-group">
                                <label class="form-label" for="service">
                                    <i class="fas fa-cogs"></i>
                                    Maraqlandığınız Xidmət
                                </label>
                                <div class="select-wrapper">
                                    <select id="service" name="service" class="form-select">
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
                                    <i class="fas fa-chevron-down select-arrow"></i>
                                    </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="budget">
                                    <i class="fas fa-dollar-sign"></i>
                                    Büdcə Aralığı
                                </label>
                                <div class="select-wrapper">
                                    <select id="budget" name="budget" class="form-select">
                                            <option value="">Büdcə seçin</option>
                                            <option value="500-1000">500-1000 AZN</option>
                                            <option value="1000-2500">1000-2500 AZN</option>
                                            <option value="2500-5000">2500-5000 AZN</option>
                                            <option value="5000+">5000+ AZN</option>
                                        </select>
                                    <i class="fas fa-chevron-down select-arrow"></i>
                                </div>
                                    </div>
                                </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="message">
                                    <i class="fas fa-comment"></i>
                                    Mesaj *
                                </label>
                                <textarea id="message" name="message" class="form-textarea" placeholder="Layihəniz haqqında ətraflı məlumat verin..." required></textarea>
                                <div class="form-focus-line"></div>
                            </div>
                                </div>

                        <div class="form-checkboxes">
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                        <input type="checkbox" id="privacy" name="privacy" required>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">Şəxsi məlumatlarımın işlənməsinə razıyam və <a href="#">Məxfilik Siyasəti</a>ni qəbul edirəm *</span>
                                    </label>
                                </div>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                        <input type="checkbox" id="newsletter" name="newsletter">
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">Yeniliklər və xüsusi təkliflər haqqında məlumat almaq istəyirəm</span>
                                    </label>
                            </div>
                                </div>

                        <div class="form-submit">
                            <button type="submit" class="submit-btn" id="contactFormSubmit">
                                <span class="btn-text">Mesaj Göndər</span>
                                <span class="btn-icon">
                                    <i class="fas fa-paper-plane"></i>
                                </span>
                                <div class="btn-loading">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                </button>
                        </div>
                            </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern Map Section -->
    <section class="map-section">
        <div class="modern-container">
            <div class="map-header">
                <div class="map-badge">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Bizim Yerləşdiyimiz Yer</span>
                </div>
                <h2 class="map-title">Ofisimizə Gəlin</h2>
                <p class="map-subtitle">Ofisimizə gəlmək istəyirsinizsə, əvvəlcədən görüş təyin edin</p>
            </div>
            
            <div class="map-container">
                <div class="map-card">
                    <div class="map-image">
                        <img src="https://trae-api-us.mchost.guru/api/ide/v1/text_to_image?prompt=modern%20office%20building%20professional%20business%20headquarters%20glass%20facade%20urban%20architecture&image_size=landscape_4_3" alt="Ofis" loading="lazy">
                        <div class="map-overlay">
                            <div class="map-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="map-info">
                        <div class="map-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="detail-content">
                                    <h4>Ünvan</h4>
                                    <p>Bakı şəhəri, Nəsimi rayonu<br>28 May küçəsi 15</p>
                                </div>
                    </div>
                    
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <div class="detail-content">
                                    <h4>İş Saatları</h4>
                                    <p>Bazar ertəsi - Cümə: 09:00-18:00<br>Şənbə: 10:00-15:00</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="map-actions">
                            <button class="action-btn action-btn--primary" onclick="openGoogleMaps()">
                                <i class="fas fa-map"></i>
                                <span>Google Maps Aç</span>
                        </button>
                            <button class="action-btn action-btn--secondary" onclick="getDirections()">
                                <i class="fas fa-route"></i>
                                <span>Yol Tarifi Al</span>
                        </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern WhatsApp Section -->
    <section class="whatsapp-section">
        <div class="modern-container">
            <div class="whatsapp-container">
                <div class="whatsapp-card">
                    <div class="whatsapp-header">
                        <div class="whatsapp-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="whatsapp-info">
                            <h2 class="whatsapp-title">Dərhal Əlaqə</h2>
                            <p class="whatsapp-subtitle">Tez cavab üçün WhatsApp üzərindən bizimlə əlaqə saxlayın</p>
                        </div>
            </div>
            
                    <div class="whatsapp-content">
                        <div class="whatsapp-features">
                            <div class="feature-item">
                                <i class="fas fa-bolt"></i>
                                <span>Sürətli Cavab</span>
                    </div>
                            <div class="feature-item">
                                <i class="fas fa-comments"></i>
                                <span>Canlı Söhbət</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Mobil Dostu</span>
                            </div>
                        </div>
                        
                        <div class="whatsapp-actions">
                        <a href="https://wa.me/380972580000?text=Merhaba! NextCode Group'dan bilgi almak istiyorum." 
                               class="whatsapp-btn whatsapp-btn--primary" 
                           target="_blank" 
                               rel="noopener noreferrer">
                                <i class="fab fa-whatsapp"></i>
                                <span>WhatsApp-a Yazın</span>
                            </a>
                            
                            <button class="whatsapp-btn whatsapp-btn--secondary" onclick="copyWhatsAppNumber()">
                                <i class="fas fa-copy"></i>
                                <span>Nömrəni Kopyala</span>
                        </button>
                    </div>
                    
                        <div class="whatsapp-hours">
                            <i class="fas fa-clock"></i>
                            <span>Həftə içi: 09:00 - 18:00 | Həftə sonu: 10:00 - 16:00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern FAQ Section -->
    <section class="faq-section">
        <div class="modern-container">
            <div class="faq-header">
                <div class="faq-badge">
                    <i class="fas fa-question-circle"></i>
                    <span>Tez-tez Verilən Suallar</span>
                </div>
                <h2 class="faq-title">Ən Çox Soruşulan Suallar</h2>
                <p class="faq-subtitle">Ən çox soruşulan sualların cavabları</p>
            </div>
            
            <div class="faq-container">
                <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <div class="question-content">
                        <h3>Layihə üçün nə qədər vaxt lazımdır?</h3>
                            <p>Layihənin mürəkkəbliyindən asılı olaraq 2-8 həftə arası vaxt tələb olunur.</p>
                        </div>
                        <div class="question-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                        <p>Layihənin mürəkkəbliyindən asılı olaraq 2-8 həftə arası vaxt tələb olunur. Dəqiq müddət ilkin məsləhət zamanı müəyyən edilir.</p>
                            <ul>
                                <li>Veb sayt: 2-4 həftə</li>
                                <li>Mobil tətbiq: 4-6 həftə</li>
                                <li>E-commerce platform: 6-8 həftə</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <div class="question-content">
                        <h3>Ödəniş şərtləri necədir?</h3>
                            <p>50% avans, 50% layihə tamamlandıqdan sonra ödənilir.</p>
                        </div>
                        <div class="question-icon">
                        <i class="fas fa-plus"></i>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                        <p>Adətən 50% avans, 50% layihə tamamlandıqdan sonra ödənilir. Böyük layihələr üçün hissə-hissə ödəniş mümkündür.</p>
                            <ul>
                                <li>Bank köçürməsi</li>
                                <li>Kartla ödəniş</li>
                                <li>PayPal</li>
                                <li>Kripto valyuta</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <div class="question-content">
                        <h3>Texniki dəstək təqdim edirsinizmi?</h3>
                            <p>Bəli, bütün layihələr üçün 3 ay pulsuz texniki dəstək təqdim edirik.</p>
                        </div>
                        <div class="question-icon">
                        <i class="fas fa-plus"></i>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                        <p>Bəli, bütün layihələr üçün 3 ay pulsuz texniki dəstək təqdim edirik. Sonrasında aylıq dəstək paketlərimiz mövcuddur.</p>
                            <ul>
                                <li>24/7 texniki dəstək</li>
                                <li>Remote server idarəetməsi</li>
                                <li>Məlumatların yedəklənməsi</li>
                                <li>Təhlükəsizlik yeniləmələri</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <div class="question-content">
                        <h3>Uzaqdan işləyirsinizmi?</h3>
                            <p>Bəli, Azərbaycanın hər yerindən və xaricdən müştərilərlə işləyirik.</p>
                        </div>
                        <div class="question-icon">
                        <i class="fas fa-plus"></i>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                        <p>Bəli, Azərbaycanın hər yerindən və xaricdən müştərilərlə işləyirik. Bütün proseslər onlayn aparılır.</p>
                            <ul>
                                <li>Video konfranslar</li>
                                <li>Onlayn layihə idarəetməsi</li>
                                <li>Real-vaxt əlaqə</li>
                                <li>Cloud-based əməkdaşlıq</li>
                            </ul>
                        </div>
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



    <style>
        /* Ultra Modern Professional Contact Page - v4.1 - <?php echo time(); ?> */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #0f172a;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            transition: all 0.3s ease;
            overflow-x: hidden;
        }
        
        /* Ultra Modern Hero Section */
        .contact-hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            overflow: hidden;
        }
        
        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(236, 72, 153, 0.1) 0%, transparent 50%);
            z-index: 1;
        }
        
        .hero-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                linear-gradient(45deg, rgba(255,255,255,0.05) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(255,255,255,0.05) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.05) 75%),
                linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.05) 75%);
            background-size: 60px 60px;
            background-position: 0 0, 0 30px, 30px -30px, -30px 0px;
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 3;
            text-align: center;
            color: white;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 2rem;
            width: 100%;
            box-sizing: border-box;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255,255,255,0.1);
            padding: 0.75rem 1.5rem;
            border-radius: 2rem;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 2rem;
            font-weight: 600;
            font-size: 0.875rem;
            letter-spacing: 0.025em;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .hero-title {
            font-size: clamp(4rem, 8vw, 7rem);
            font-weight: 900;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #ff6b6b 0%, #4ecdc4 50%, #45b7d1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.1;
            letter-spacing: -0.05em;
            text-shadow: 0 8px 32px rgba(0,0,0,0.5);
            animation: titleGlow 3s ease-in-out infinite alternate;
        }
        
        @keyframes titleGlow {
            0% {
                filter: brightness(1) drop-shadow(0 0 20px rgba(255, 107, 107, 0.5));
            }
            100% {
                filter: brightness(1.2) drop-shadow(0 0 40px rgba(78, 205, 196, 0.8));
            }
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 3rem;
            opacity: 0.9;
            line-height: 1.6;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            font-weight: 400;
            color: rgba(255,255,255,0.8);
        }
        
        .hero-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .stat-item {
            text-align: center;
            padding: 2rem;
            background: rgba(255,255,255,0.08);
            border-radius: 1.5rem;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.15);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
        }
        
        .stat-item:hover {
            transform: translateY(-8px);
            background: rgba(255,255,255,0.12);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            font-size: 0.875rem;
            opacity: 0.8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.7);
        }
        
        /* Theme Toggle Button */
        .theme-toggle-container {
            position: absolute;
            top: 2rem;
            right: 2rem;
            z-index: 10;
        }
        
        .theme-toggle-btn {
            width: 3rem;
            height: 3rem;
            border: none;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .theme-toggle-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.1);
        }
        
        .theme-icon-light,
        .theme-icon-dark {
            position: absolute;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .theme-icon-light {
            opacity: 1;
            transform: rotate(0deg);
        }
        
        .theme-icon-dark {
            opacity: 0;
            transform: rotate(180deg);
        }
        
        body.dark-mode .theme-icon-light {
            opacity: 0;
            transform: rotate(-180deg);
        }
        
        body.dark-mode .theme-icon-dark {
            opacity: 1;
            transform: rotate(0deg);
        }
        
        body.dark-mode .theme-toggle-btn {
            background: rgba(0,0,0,0.3);
            border-color: rgba(255,255,255,0.2);
        }
        
        body.dark-mode .theme-toggle-btn:hover {
            background: rgba(0,0,0,0.4);
        }
        
        .contact-hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%);
            overflow: hidden;
        }
        
        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse"><path d="M 50 0 L 0 0 0 50" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            z-index: 1;
        }
        
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.2);
            z-index: 2;
        }
        
        .hero-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 40% 40%, rgba(255,255,255,0.05) 0%, transparent 50%);
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 3;
            text-align: center;
            color: white;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 2rem;
            width: 100%;
            box-sizing: border-box;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255,255,255,0.15);
            padding: 0.75rem 1.5rem;
            border-radius: 2rem;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 2rem;
            font-weight: 600;
            font-size: 0.875rem;
            letter-spacing: 0.025em;
        }
        
        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
            line-height: 1.1;
            letter-spacing: -0.025em;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 3rem;
            opacity: 0.9;
            line-height: 1.6;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            font-weight: 400;
        }
        
        .hero-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .stat-item {
            text-align: center;
            padding: 1.5rem;
            background: rgba(255,255,255,0.1);
            border-radius: 1rem;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        
        .stat-item:hover {
            transform: translateY(-4px);
            background: rgba(255,255,255,0.15);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            font-size: 0.875rem;
            opacity: 0.8;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Ultra Modern Contact Info Section */
        .contact-info-section {
            padding: 8rem 0;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            position: relative;
        }
        
        .contact-info-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.05) 0%, transparent 50%);
            z-index: 1;
        }
        
        .modern-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            width: 100%;
            box-sizing: border-box;
            position: relative;
            z-index: 2;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 6rem;
        }
        
        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 3rem;
            margin-bottom: 2rem;
            font-weight: 700;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            box-shadow: 0 8px 32px rgba(59, 130, 246, 0.3);
        }
        
        .section-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 900;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #0f172a 0%, #475569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.025em;
            line-height: 1.1;
        }
        
        .section-subtitle {
            font-size: 1.25rem;
            color: #64748b;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            font-weight: 400;
        }
        
        .contact-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 3rem;
            margin-bottom: 6rem;
        }
        
        .contact-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 2rem;
            padding: 3rem;
            box-shadow: 
                0 20px 40px rgba(0,0,0,0.1),
                0 0 0 1px rgba(59, 130, 246, 0.1),
                inset 0 1px 0 rgba(255,255,255,0.8);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
            background-clip: padding-box;
        }
        
        .contact-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4);
            border-radius: inherit;
            padding: 2px;
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .contact-card:hover::before {
            opacity: 1;
        }
        
        .contact-card:hover {
            transform: translateY(-12px);
            box-shadow: 
                0 25px 50px -12px rgba(0,0,0,0.15),
                0 0 0 1px rgba(59, 130, 246, 0.1);
        }
        
        .card-icon {
            width: 5rem;
            height: 5rem;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        }
        
        .card-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            opacity: 0.1;
            border-radius: inherit;
        }
        
        .contact-card--address .card-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .contact-card--phone .card-icon {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        }
        
        .contact-card--email .card-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .contact-card--hours .card-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #0f172a;
            letter-spacing: -0.025em;
        }
        
        .card-text {
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 2rem;
            font-size: 1rem;
            font-weight: 400;
        }
        
        .card-action {
            display: flex;
            justify-content: flex-end;
        }
        
        .action-btn {
            width: 3rem;
            height: 3rem;
            border: none;
            border-radius: 1rem;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .action-btn:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            border-radius: 2rem;
            border: 1px solid #22c55e;
        }
        
        .status-dot {
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse 2s infinite;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        }
        
        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }
        
        .status-text {
            color: #166534;
            font-weight: 700;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Social Section */
        .social-section {
            text-align: center;
        }
        
        .social-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: #2d3748;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .social-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 25px;
            border-radius: 50px;
            text-decoration: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .social-link--facebook { background: #1877f2; }
        .social-link--instagram { background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); }
        .social-link--linkedin { background: #0077b5; }
        .social-link--twitter { background: #1da1f2; }
        .social-link--youtube { background: #ff0000; }
        
        .social-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        /* Contact Form Section */
        .contact-form-section {
            padding: 100px 0;
            background: white;
        }
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .form-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .form-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #2d3748;
        }
        
        .form-subtitle {
            font-size: 1.2rem;
            color: #718096;
        }
        
        .form-wrapper {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .modern-contact-form {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-row--2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            flex: 1;
            position: relative;
        }
        
        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
        }
        
        .form-input,
        .form-textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-focus-line {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }
        
        .form-input:focus + .form-focus-line,
        .form-textarea:focus + .form-focus-line {
            width: 100%;
        }
        
        .select-wrapper {
            position: relative;
        }
        
        .form-select {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            background: white;
            appearance: none;
            cursor: pointer;
        }
        
        .select-arrow {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #718096;
            pointer-events: none;
        }
        
        .form-checkboxes {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
        }
        
        .checkbox-label input[type="checkbox"] {
            display: none;
        }
        
        .checkbox-custom {
            width: 20px;
            height: 20px;
            border: 2px solid #e2e8f0;
            border-radius: 4px;
            position: relative;
            transition: all 0.3s ease;
            flex-shrink: 0;
            margin-top: 2px;
        }
        
        .checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
        
        .checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        
        .checkbox-text {
            color: #4a5568;
            line-height: 1.5;
        }
        
        .form-submit {
            text-align: center;
            margin-top: 20px;
        }
        
        .submit-btn {
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 18px 40px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            overflow: hidden;
            min-width: 200px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-text {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-icon {
            margin-left: 10px;
        }
        
        .btn-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
        }
        
        .submit-btn.loading .btn-text,
        .submit-btn.loading .btn-icon {
            opacity: 0;
        }
        
        .submit-btn.loading .btn-loading {
            display: block;
        }
        
        /* Map Section */
        .map-section {
            padding: 100px 0;
            background: #f8f9fa;
        }
        
        .map-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .map-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .map-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #2d3748;
        }
        
        .map-subtitle {
            font-size: 1.2rem;
            color: #718096;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .map-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .map-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 400px;
        }
        
        .map-image {
            position: relative;
            overflow: hidden;
        }
        
        .map-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .map-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(102, 126, 234, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .map-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            backdrop-filter: blur(10px);
        }
        
        .map-info {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .map-details {
            margin-bottom: 30px;
        }
        
        .map-details .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .map-details .detail-item i {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        
        .detail-content h4 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: #2d3748;
        }
        
        .detail-content p {
            color: #718096;
            line-height: 1.5;
        }
        
        .map-actions {
            display: flex;
            gap: 15px;
        }
        
        .action-btn--primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            justify-content: center;
        }
        
        .action-btn--secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 15px 25px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            justify-content: center;
        }
        
        .action-btn--primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .action-btn--secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        /* WhatsApp Section */
        .whatsapp-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: white;
        }
        
        .whatsapp-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .whatsapp-card {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .whatsapp-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .whatsapp-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            backdrop-filter: blur(10px);
        }
        
        .whatsapp-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .whatsapp-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .whatsapp-content {
            text-align: center;
        }
        
        .whatsapp-features {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .feature-item i {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            backdrop-filter: blur(10px);
        }
        
        .feature-item span {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .whatsapp-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .whatsapp-btn {
            padding: 18px 35px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
            border: none;
        }
        
        .whatsapp-btn--primary {
            background: white;
            color: #25d366;
        }
        
        .whatsapp-btn--secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .whatsapp-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .whatsapp-btn--primary:hover {
            background: #f8f9fa;
        }
        
        .whatsapp-btn--secondary:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .whatsapp-hours {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        /* FAQ Section */
        .faq-section {
            padding: 100px 0;
            background: white;
        }
        
        .faq-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .faq-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .faq-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #2d3748;
        }
        
        .faq-subtitle {
            font-size: 1.2rem;
            color: #718096;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .faq-item {
            background: white;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .faq-item:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .faq-question {
            padding: 25px 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .faq-question:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .question-content h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #2d3748;
            transition: color 0.3s ease;
        }
        
        .question-content p {
            font-size: 0.95rem;
            color: #718096;
            margin: 0;
            transition: color 0.3s ease;
        }
        
        .faq-question:hover .question-content h3,
        .faq-question:hover .question-content p {
            color: white;
        }
        
        .question-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        
        .faq-question:hover .question-icon {
            background: rgba(255,255,255,0.2);
            transform: rotate(45deg);
        }
        
        .faq-item.active .question-icon {
            background: rgba(255,255,255,0.2);
            transform: rotate(45deg);
        }
        
        .faq-item.active .faq-question {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .faq-item.active .question-content h3,
        .faq-item.active .question-content p {
            color: white;
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .faq-item.active .faq-answer {
            max-height: 500px;
        }
        
        .answer-content {
            padding: 0 30px 25px;
            color: #4a5568;
            line-height: 1.6;
        }
        
        .answer-content p {
            margin-bottom: 15px;
        }
        
        .answer-content ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .answer-content li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            color: #4a5568;
        }
        
        .answer-content li::before {
            content: '✓';
            color: #48bb78;
            font-weight: bold;
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Text Alignment and Overflow Fixes */
        .hero-title {
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            max-width: 100%;
        }
        
        .hero-subtitle {
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            max-width: 100%;
        }
        
        .stat-label {
            word-wrap: break-word;
            overflow-wrap: break-word;
            text-align: center;
        }
        
        .card-text {
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
                line-height: 1.2;
                padding: 0 1rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
                padding: 0 1rem;
                line-height: 1.5;
            }
            
            .hero-stats {
                grid-template-columns: 1fr;
                gap: 1rem;
                padding: 0 1rem;
            }
            
            .stat-item {
                padding: 1.25rem;
                text-align: center;
            }
            
            .stat-label {
                font-size: 0.8rem;
                line-height: 1.3;
            }
            
            .contact-cards-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
                padding: 0 1rem;
            }
            
            .modern-container {
                padding: 0 1rem;
            }
            
            .contact-card {
                padding: 1.5rem;
                margin: 0;
            }
            
            .hero-content {
                padding: 0 1rem;
            }
            
            .section-title {
                font-size: 2rem;
                padding: 0 1rem;
            }
            
            .section-subtitle {
                font-size: 1rem;
                padding: 0 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .hero-title {
                font-size: 1.8rem;
                line-height: 1.1;
                padding: 0 0.5rem;
            }
            
            .hero-subtitle {
                font-size: 0.95rem;
                padding: 0 0.5rem;
                line-height: 1.4;
            }
            
            .stat-item {
                padding: 1rem;
                margin: 0.5rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
            
            .stat-label {
                font-size: 0.75rem;
                line-height: 1.2;
            }
            
            .contact-card {
                padding: 1rem;
                margin: 0.5rem;
            }
            
            .card-title {
                font-size: 1.1rem;
            }
            
            .card-text {
                font-size: 0.9rem;
                line-height: 1.4;
            }
            
            .section-title {
                font-size: 1.5rem;
                padding: 0 0.5rem;
            }
            
            .section-subtitle {
                font-size: 0.9rem;
                padding: 0 0.5rem;
            }
        }
        
        @media (max-width: 360px) {
            .hero-title {
                font-size: 1.5rem;
            }
            
            .hero-subtitle {
                font-size: 0.9rem;
            }
            
            .stat-label {
                font-size: 0.7rem;
            }
            
            .contact-card {
                padding: 0.75rem;
            }
        }
        
        /* Theme Toggle Responsive */
        @media (max-width: 768px) {
            .theme-toggle-container {
                top: 1rem;
                right: 1rem;
            }
            
            .theme-toggle-btn {
                width: 2.5rem;
                height: 2.5rem;
            }
            
            .theme-icon-light,
            .theme-icon-dark {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .theme-toggle-container {
                top: 0.5rem;
                right: 0.5rem;
            }
            
            .theme-toggle-btn {
                width: 2rem;
                height: 2rem;
            }
            
            .theme-icon-light,
            .theme-icon-dark {
                font-size: 0.9rem;
            }
        }
    </style>

    <script>
        // Dark Mode Support
        document.addEventListener('DOMContentLoaded', function() {
            // Check for saved theme preference or default to light mode
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.body.classList.toggle('dark-mode', savedTheme === 'dark');
            
            // Listen for theme changes from other pages
            window.addEventListener('storage', function(e) {
                if (e.key === 'theme') {
                    document.body.classList.toggle('dark-mode', e.newValue === 'dark');
                }
            });
            
            // Listen for theme toggle events
            document.addEventListener('themeChanged', function(e) {
                document.body.classList.toggle('dark-mode', e.detail.theme === 'dark');
            });
        });
        
        // Theme Toggle Function
        function toggleTheme() {
            const body = document.body;
            const isDark = body.classList.contains('dark-mode');
            const newTheme = isDark ? 'light' : 'dark';
            
            // Toggle dark mode class
            body.classList.toggle('dark-mode', !isDark);
            
            // Save theme preference
            localStorage.setItem('theme', newTheme);
            
            // Dispatch custom event for other components
            const event = new CustomEvent('themeChanged', {
                detail: { theme: newTheme }
            });
            document.dispatchEvent(event);
            
            // Update other pages if they're open
            window.dispatchEvent(new StorageEvent('storage', {
                key: 'theme',
                newValue: newTheme,
                oldValue: isDark ? 'dark' : 'light'
            }));
        }
        
        // FAQ Toggle Function
        function toggleFAQ(element) {
            const faqItem = element.closest('.faq-item');
            const isActive = faqItem.classList.contains('active');
            
            // Close all FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                faqItem.classList.add('active');
            }
        }
        
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
        
        function callPhone() {
            window.location.href = 'tel:+380972580000';
        }
        
        function sendEmail() {
            window.location.href = 'mailto:xeyalcemilli9032@gmail.com';
        }
        
        function copyWhatsAppNumber() {
            const phoneNumber = '+380972580000';
            navigator.clipboard.writeText(phoneNumber).then(() => {
                // Show success message
                const btn = document.querySelector('#copyNumberBtn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i><span>Kopyalandı!</span>';
                btn.style.background = '#48bb78';
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = '';
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
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    
                    // Simulate form submission (replace with actual AJAX call)
                    setTimeout(() => {
                        // Show success modal
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                        
                        // Reset form
                        form.reset();
                        
                        // Reset button
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                    }, 2000);
                });
            }
        });
    </script>

    <script src="js/error-handler.js"></script>
    <script src="js/contact-info-loader.js"></script>
    <script src="js/contact-simple.js"></script>

<?php
// Include footer
require_once 'includes/footer.php';
?>