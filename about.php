<?php
// NextCode Group - Haqqımızda Səhifəsi
// Production Environment Configuration

// Define secure access constant
define('SECURE_ACCESS', true);

// Include API router for handling API requests
require_once 'api-router.php';

// Database bağlantısını include et
require_once 'config/database.php';

// Security configuration include et
require_once 'config/security.php';

// Error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Include page functions
require_once 'includes/page_functions.php';

// Content helper include et
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
    $url = getImageUrl($image_key, $default_url);
    $alt = getImageAlt($image_key, $default_alt);
    $title = getImageTitle($image_key, $alt);
    
    if (empty($url)) {
        return '';
    }
    
    $attr_string = '';
    foreach ($attributes as $key => $value) {
        $attr_string .= " $key=\"$value\"";
    }
    
    return "<img src=\"$url\" alt=\"$alt\" title=\"$title\"$attr_string>";
}

// Dynamic content variables
$page_title = getTextContent('about_page_title', 'About - NextCode Group');
$meta_description = getTextContent('about_meta_description', 'Learn about NextCode Group team. Meet our experienced specialists and our success story.');
$current_page = 'about';

// Team members data (dynamic from database)
try {
    if (tableExists('team_members')) {
        $team_query = $pdo->prepare("SELECT * FROM team_members WHERE is_active = 1 ORDER BY sort_order ASC");
        $team_query->execute();
        $team_members = $team_query->fetchAll();
    } else {
        $team_members = [];
    }
} catch (PDOException $e) {
    error_log('Team query failed: ' . $e->getMessage());
    $team_members = [];
}

// Company statistics
try {
    if (tableExists('company_stats')) {
        $stats_query = $pdo->prepare("SELECT * FROM company_stats WHERE is_active = 1 ORDER BY id DESC LIMIT 1");
        $stats_query->execute();
        $company_stats = $stats_query->fetch();
    }
    
    if (!$company_stats) {
        $company_stats = [
            'founded_year' => getTextContent('about_stats_experience', '16+'),
            'team_size' => getTextContent('about_stats_customers', '250+'),
            'projects_completed' => getTextContent('about_stats_projects', '500+'),
            'countries_served' => getTextContent('about_stats_satisfaction', '95%')
        ];
    }
} catch (PDOException $e) {
    error_log('Company stats query failed: ' . $e->getMessage());
    $company_stats = [
        'founded_year' => getTextContent('about_stats_experience', '16+'),
        'team_size' => getTextContent('about_stats_customers', '250+'),
        'projects_completed' => getTextContent('about_stats_projects', '500+'),
        'countries_served' => getTextContent('about_stats_satisfaction', '95%')
    ];
}
// Include header
require_once 'includes/header.php';
?>

    <!-- Page Header -->
    <section class="modern-section modern-section--hero">
        <div class="modern-container">
            <div class="modern-text-center">
                <div class="modern-badge modern-badge--outline modern-m-4">
                    <i class="fas fa-info-circle me-2"></i>Haqqımızda
                </div>
                <h1 class="modern-heading modern-heading--xl"><?php echo getTextContent('about_title', 'About'); ?></h1>
                <p class="modern-text modern-text--lg"><?php echo getTextContent('about_subtitle', 'Bizim komandamız və missiyamız haqqında ətraflı məlumat'); ?></p>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="modern-section">
        <div class="modern-container">
            <div class="modern-grid modern-grid--2-cols modern-grid--gap-lg">
                <div class="modern-animate--fadeInLeft">
                    <div class="modern-badge modern-badge--outline modern-m-4">
                        <i class="fas fa-users me-2"></i>Komandamız
                    </div>
                    <h2 class="modern-heading modern-heading--lg"><?php echo getTextContent('about_who_title', 'Biz Kimik?'); ?></h2>
                    <div class="modern-text modern-text--md modern-m-6">
                        <?php echo getHtmlContent('about_description', 'NextCode Group olaraq, 2008-ci ildən bəri müştərilərimizin rəqəmsal dünyada uğur qazanması üçün çalışırıq. 16 illik təcrübəmizlə komandamız təcrübəli mütəxəssislərdən ibarətdir və hər bir layihəyə fərdi yanaşma tətbiq edirik.'); ?>
                    </div>
                    <div class="modern-text modern-text--md modern-m-6">
                        <?php echo getHtmlContent('about_mission', 'Bizim məqsədimiz sadəcə xidmət göstərmək deyil, uzunmüddətli tərəfdaşlıq qurmaq və müştərilərimizin biznesinə real dəyər əlavə etməkdir.'); ?>
                    </div>
                </div>
                <div class="modern-animate--fadeInRight">
                    <div class="modern-card modern-card--image">
                        <?php echo getImageTag('about_team_image', 'https://trae-api-us.mchost.guru/api/ide/v1/text_to_image?prompt=modern%20digital%20marketing%20team%20working%20together%20in%20office%2C%20professional%20atmosphere%2C%20laptops%20and%20charts%2C%20bright%20lighting&image_size=landscape_4_3', 'Our Team', ['class' => 'modern-card__image', 'loading' => 'lazy']); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="modern-section modern-section--light">
        <div class="modern-container">
            <div class="modern-text-center modern-m-8">
                <h2 class="modern-heading modern-heading--lg"><?php echo getTextContent('about_stats_title', 'Rəqəmlərlə Uğurumuz'); ?></h2>
            </div>
            <div class="modern-grid modern-grid--4-cols modern-grid--gap-md">
                <div class="modern-card modern-card--stat modern-animate--fadeInUp modern-animate--delay-1">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="modern-heading modern-heading--xl modern-text--gradient"><?php echo getTextContent('about_stats_customers', '250+'); ?></div>
                        <div class="modern-text modern-text--sm"><?php echo getTextContent('about_stats_customers_label', 'Məmnun Müştəri'); ?></div>
                    </div>
                </div>
                <div class="modern-card modern-card--stat modern-animate--fadeInUp modern-animate--delay-2">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="modern-heading modern-heading--xl modern-text--gradient"><?php echo getTextContent('about_stats_projects', '500+'); ?></div>
                        <div class="modern-text modern-text--sm"><?php echo getTextContent('about_stats_projects_label', 'Tamamlanmış Layihə'); ?></div>
                    </div>
                </div>
                <div class="modern-card modern-card--stat modern-animate--fadeInUp modern-animate--delay-3">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="modern-heading modern-heading--xl modern-text--gradient"><?php echo getTextContent('about_stats_experience', '16+'); ?></div>
                        <div class="modern-text modern-text--sm"><?php echo getTextContent('about_stats_experience_label', 'İl Təcrübə'); ?></div>
                    </div>
                </div>
                <div class="modern-card modern-card--stat modern-animate--fadeInUp modern-animate--delay-4">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="modern-heading modern-heading--xl modern-text--gradient"><?php echo getTextContent('about_stats_satisfaction', '95%'); ?></div>
                        <div class="modern-text modern-text--sm"><?php echo getTextContent('about_stats_satisfaction_label', 'Müştəri Məmnuniyyəti'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Experience Heritage Section -->
    <section class="modern-section">
        <div class="modern-container">
            <div class="modern-grid modern-grid--2-cols modern-grid--gap-lg">
                <div class="modern-animate--fadeInLeft">
                    <div class="modern-badge modern-badge--outline modern-m-4">
                        <i class="fas fa-award me-2"></i>2008-dən bəri
                    </div>
                    <h2 class="modern-heading modern-heading--lg">16 İllik Təcrübə və Güvən</h2>
                    <div class="modern-text modern-text--md modern-m-6">
                        NextCode Group 2008-ci ildə qurulduğu gündən bu yana rəqəmsal texnologiyalar sahəsində 
                        fasiləsiz xidmət göstərir. 16 il ərzində yüzlərlə müştəri ilə işləyərək, onların 
                        rəqəmsal transformasiya yolculuğunda etibarlı tərəfdaş olmuşuq.
                    </div>
                    <div class="modern-m-6">
                        <div class="modern-flex modern-flex-col modern-gap-3">
                            <div class="modern-flex modern-items-center modern-gap-3">
                                <i class="fas fa-check-circle modern-text--success"></i>
                                <span class="modern-text">2008-ci ildən bəri fasiləsiz xidmət</span>
                            </div>
                            <div class="modern-flex modern-items-center modern-gap-3">
                                <i class="fas fa-check-circle modern-text--success"></i>
                                <span class="modern-text">Sektorda qazanılmış güvən və nüfuz</span>
                            </div>
                            <div class="modern-flex modern-items-center modern-gap-3">
                                <i class="fas fa-check-circle modern-text--success"></i>
                                <span class="modern-text">Texnoloji yeniliklərlə həmişə bir addım önündə</span>
                            </div>
                            <div class="modern-flex modern-items-center modern-gap-3">
                                <i class="fas fa-check-circle modern-text--success"></i>
                                <span class="modern-text">Müştəri məmnuniyyətində sabit yüksək göstəricilər</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modern-animate--fadeInRight">
                    <div class="modern-flex modern-flex-col modern-gap-4">
                        <div class="modern-card modern-card--timeline">
                            <div class="modern-card__body">
                                <div class="modern-badge modern-badge--primary">2008</div>
                                <h4 class="modern-heading modern-heading--sm">Şirkətin Qurulması</h4>
                                <p class="modern-text modern-text--sm">NextCode Group-un təməli qoyuldu</p>
                            </div>
                        </div>
                        <div class="modern-card modern-card--timeline">
                            <div class="modern-card__body">
                                <div class="modern-badge modern-badge--primary">2012</div>
                                <h4 class="modern-heading modern-heading--sm">İlk 100 Müştəri</h4>
                                <p class="modern-text modern-text--sm">Müştəri bazamız 100-ü keçdi</p>
                            </div>
                        </div>
                        <div class="modern-card modern-card--timeline">
                            <div class="modern-card__body">
                                <div class="modern-badge modern-badge--primary">2018</div>
                                <h4 class="modern-heading modern-heading--sm">Beynəlxalq Genişlənmə</h4>
                                <p class="modern-text modern-text--sm">Regional bazara çıxış</p>
                            </div>
                        </div>
                        <div class="modern-card modern-card--timeline modern-card--active">
                            <div class="modern-card__body">
                                <div class="modern-badge modern-badge--success">2024</div>
                                <h4 class="modern-heading modern-heading--sm">16 İllik Təcrübə</h4>
                                <p class="modern-text modern-text--sm">Sektorda lider mövqe</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="modern-section modern-section--light">
        <div class="modern-container">
            <div class="modern-text-center modern-m-8">
                <h2 class="modern-heading modern-heading--lg">Bizim Komanda</h2>
                <p class="modern-text modern-text--md">Təcrübəli və yaradıcı mütəxəssislərimizlə tanış olun</p>
            </div>
            <div class="modern-grid modern-grid--4-cols modern-grid--gap-md">
                <div class="modern-card modern-card--team modern-animate--fadeInUp modern-animate--delay-1">
                    <div class="modern-card__image">
                        <img src="https://trae-api-us.mchost.guru/api/ide/v1/text_to_image?prompt=professional%20male%20CEO%20portrait%2C%20business%20suit%2C%20confident%20smile%2C%20office%20background&image_size=square" alt="Əli Məmmədov" class="modern-card__image" loading="lazy">
                    </div>
                    <div class="modern-card__body modern-text-center">
                        <h3 class="modern-heading modern-heading--md">Əli Məmmədov</h3>
                        <p class="modern-badge modern-badge--outline modern-m-3">CEO & Founder</p>
                        <p class="modern-text modern-text--sm">10 illik təcrübəyə malik rəqəmsal marketinq mütəxəssisi</p>
                        <div class="modern-flex modern-justify-center modern-gap-3 modern-m-4">
                            <a href="#" class="modern-btn modern-btn--ghost modern-btn--sm"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="modern-btn modern-btn--ghost modern-btn--sm"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="modern-card modern-card--team modern-animate--fadeInUp modern-animate--delay-2">
                    <div class="modern-card__image">
                        <img src="https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?w=300&h=300&fit=crop&crop=face&auto=compress&cs=tinysrgb" alt="Leyla Həsənova" class="modern-card__image" loading="lazy">
                    </div>
                    <div class="modern-card__body modern-text-center">
                        <h3 class="modern-heading modern-heading--md">Leyla Həsənova</h3>
                        <p class="modern-badge modern-badge--outline modern-m-3">Marketing Director</p>
                        <p class="modern-text modern-text--sm">Yaradıcı kampaniyalar və strategiyalar üzrə mütəxəssis</p>
                        <div class="modern-flex modern-justify-center modern-gap-3 modern-m-4">
                            <a href="#" class="modern-btn modern-btn--ghost modern-btn--sm"><i class="fab fa-linkedin"></i></a>
                            <a href="https://www.instagram.com/nextcodegroup/" class="modern-btn modern-btn--ghost modern-btn--sm"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <div class="modern-card modern-card--team modern-animate--fadeInUp modern-animate--delay-3">
                    <div class="modern-card__image">
                        <img src="https://trae-api-us.mchost.guru/api/ide/v1/text_to_image?prompt=professional%20male%20web%20developer%20portrait%2C%20casual%20shirt%2C%20glasses%2C%20tech%20office%20background&image_size=square" alt="Rəşad Quliyev" class="modern-card__image" loading="lazy">
                    </div>
                    <div class="modern-card__body modern-text-center">
                        <h3 class="modern-heading modern-heading--md">Rəşad Quliyev</h3>
                        <p class="modern-badge modern-badge--outline modern-m-3">Lead Developer</p>
                        <p class="modern-text modern-text--sm">Full-stack developer və texniki həllərin memarı</p>
                        <div class="modern-flex modern-justify-center modern-gap-3 modern-m-4">
                            <a href="#" class="modern-btn modern-btn--ghost modern-btn--sm"><i class="fab fa-github"></i></a>
                            <a href="#" class="modern-btn modern-btn--ghost modern-btn--sm"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="modern-card modern-card--team modern-animate--fadeInUp modern-animate--delay-4">
                    <div class="modern-card__image">
                        <img src="https://trae-api-us.mchost.guru/api/ide/v1/text_to_image?prompt=professional%20female%20designer%20portrait%2C%20creative%20workspace%2C%20artistic%20background%2C%20warm%20smile&image_size=square" alt="Səbinə Əliyeva" class="modern-card__image" loading="lazy">
                    </div>
                    <div class="modern-card__body modern-text-center">
                        <h3 class="modern-heading modern-heading--md">Səbinə Əliyeva</h3>
                        <p class="modern-badge modern-badge--outline modern-m-3">Creative Director</p>
                        <p class="modern-text modern-text--sm">UX/UI dizayn və brendinq üzrə mütəxəssis</p>
                        <div class="modern-flex modern-justify-center modern-gap-3 modern-m-4">
                            <a href="#" class="modern-btn modern-btn--ghost modern-btn--sm"><i class="fab fa-dribbble"></i></a>
                            <a href="#" class="modern-btn modern-btn--ghost modern-btn--sm"><i class="fab fa-behance"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="modern-section">
        <div class="modern-container">
            <div class="modern-grid modern-grid--2-cols modern-grid--gap-lg">
                <div class="modern-card modern-card--mission modern-animate--fadeInLeft">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Missiyamız</h3>
                        <p class="modern-text">Müştərilərimizin rəqəmsal dünyada güclü mövqe qazanması və biznes məqsədlərinə çatması üçün innovativ və effektiv həllər təqdim etmək.</p>
                    </div>
                </div>
                <div class="modern-card modern-card--vision modern-animate--fadeInRight">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Vizyonumuz</h3>
                        <p class="modern-text">Azərbaycanda rəqəmsal marketinq sahəsində lider agentlik olmaq və beynəlxalq bazarda tanınan bir brend yaratmaq.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="modern-section modern-section--light">
        <div class="modern-container">
            <div class="modern-text-center modern-m-8">
                <h2 class="modern-heading modern-heading--lg">Bizim Dəyərlərimiz</h2>
            </div>
            <div class="modern-grid modern-grid--4-cols modern-grid--gap-md">
                <div class="modern-card modern-card--value modern-animate--fadeInUp modern-animate--delay-1">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">İnnovasiya</h3>
                        <p class="modern-text">Həmişə yeni texnologiyalar və yaradıcı yanaşmalar axtarırıq</p>
                    </div>
                </div>
                <div class="modern-card modern-card--value modern-animate--fadeInUp modern-animate--delay-2">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Güvən</h3>
                        <p class="modern-text">Müştərilərimizlə uzunmüddətli və etibarlı əlaqələr qururuq</p>
                    </div>
                </div>
                <div class="modern-card modern-card--value modern-animate--fadeInUp modern-animate--delay-3">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Keyfiyyət</h3>
                        <p class="modern-text">Hər bir layihədə ən yüksək keyfiyyət standartlarını tətbiq edirik</p>
                    </div>
                </div>
                <div class="modern-card modern-card--value modern-animate--fadeInUp modern-animate--delay-4">
                    <div class="modern-card__body modern-text-center">
                        <div class="modern-card__icon modern-m-4">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h3 class="modern-heading modern-heading--md">Komanda İşi</h3>
                        <p class="modern-text">Güclü komanda ruhu ilə ən yaxşı nəticələrə çatırıq</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="modern-section modern-section--dark">
        <div class="modern-container">
            <div class="modern-text-center">
                <h2 class="modern-heading modern-heading--lg">Bizimlə İşləməyə Hazırsınız?</h2>
                <p class="modern-text modern-text--md modern-m-6">Biznesinizi rəqəmsal dünyada irəli aparmaq üçün bizimlə əlaqə saxlayın</p>
                <div class="modern-flex modern-justify-center modern-gap-4">
                    <a href="contact.php" class="modern-btn modern-btn--primary">Əlaqə Saxlayın</a>
                    <a href="portfolio.php" class="modern-btn modern-btn--outline">Portfolio</a>
                </div>
            </div>
        </div>
    </section>



    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Bizimlə Əlaqə Saxlayın</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quickContactForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modalName" class="form-label">Ad Soyad</label>
                                    <input type="text" class="form-control" id="modalName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modalEmail" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="modalEmail" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="modalPhone" class="form-label">Telefon</label>
                            <input type="tel" class="form-control" id="modalPhone" required>
                        </div>
                        <div class="mb-3">
                            <label for="modalService" class="form-label">Maraqlandığınız Xidmət</label>
                            <select class="form-select" id="modalService">
                                <option value="">Seçin...</option>
                                <option value="seo">SEO Optimizasiya</option>
                                <option value="social">Sosial Media</option>
                                <option value="branding">Brendinq</option>
                                <option value="advertising">Reklam Kampaniyaları</option>
                                <option value="web">Veb Dizayn</option>
                                <option value="other">Digər</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modalMessage" class="form-label">Mesaj</label>
                            <textarea class="form-control" id="modalMessage" rows="4" placeholder="Layihəniz haqqında qısaca məlumat verin..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <button type="button" class="btn btn-primary" id="submitQuickContact">Mesaj Göndər</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Member Detail Modal -->
    <div class="modal fade" id="teamMemberModal" tabindex="-1" aria-labelledby="teamMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="teamMemberModalLabel">Komanda Üzvü</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="team-member-detail" id="teamMemberDetail">
                        <!-- Team member details will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='contact.php'">Əlaqə Saxla</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/about.js"></script>
    <script src="js/contact-info-loader.js"></script>

<?php
// Include footer
require_once 'includes/footer.php';
?>