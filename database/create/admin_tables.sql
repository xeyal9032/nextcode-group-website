-- Admin Panel Tabloları - NextCode Group
-- Admin paneli için gerekli tablolar

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor', 'viewer') DEFAULT 'viewer',
    is_active BOOLEAN DEFAULT TRUE,
    login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin Logs Table (Audit Trail)
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    level ENUM('INFO', 'WARNING', 'ERROR', 'CRITICAL') DEFAULT 'INFO',
    category ENUM('LOGIN', 'USER_MANAGEMENT', 'CONTENT', 'SECURITY', 'SYSTEM') NOT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    user_id INT NULL,
    username VARCHAR(50),
    ip_address VARCHAR(45),
    user_agent TEXT,
    request_uri TEXT,
    request_method VARCHAR(10),
    session_id VARCHAR(128),
    referer TEXT,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_timestamp (timestamp),
    INDEX idx_level (level),
    INDEX idx_category (category),
    INDEX idx_user_id (user_id)
);

-- Site Content Table (Dinamik İçerik Yönetimi)
CREATE TABLE IF NOT EXISTS site_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(100) NOT NULL,
    section_name VARCHAR(100) NOT NULL,
    content_key VARCHAR(100) NOT NULL,
    content_value LONGTEXT,
    content_type ENUM('text', 'html', 'image', 'json') DEFAULT 'text',
    language VARCHAR(5) DEFAULT 'az',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_content (page_name, section_name, content_key, language),
    INDEX idx_page_name (page_name),
    INDEX idx_section_name (section_name)
);

-- Blog Categories Table (Güncellenmiş)
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#667eea',
    icon VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blog Posts Table (Güncellenmiş)
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content LONGTEXT,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category_id INT,
    author_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,
    meta_title VARCHAR(200),
    meta_description TEXT,
    tags TEXT,
    view_count INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_category_id (category_id),
    INDEX idx_author_id (author_id),
    INDEX idx_published_at (published_at)
);

-- Portfolio Projects Table (Güncellenmiş)
CREATE TABLE IF NOT EXISTS portfolio_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    technologies TEXT,
    client VARCHAR(100),
    project_url VARCHAR(255),
    github_url VARCHAR(255),
    featured_image VARCHAR(255),
    gallery_images TEXT, -- JSON format
    is_active BOOLEAN DEFAULT TRUE,
    featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_is_active (is_active),
    INDEX idx_featured (featured)
);

-- Contact Messages Table (Güncellenmiş)
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Services Table (Güncellenmiş)
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    price VARCHAR(50),
    features TEXT, -- JSON format
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Pricing Packages Table
CREATE TABLE IF NOT EXISTS pricing_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'AZN',
    features TEXT, -- JSON format
    is_popular BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- FAQ Table
CREATE TABLE IF NOT EXISTS faq_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100),
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Media Files Table (Güncellenmiş)
CREATE TABLE IF NOT EXISTS media_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500),
    file_size INT,
    mime_type VARCHAR(100),
    alt_text VARCHAR(255),
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_uploaded_by (uploaded_by),
    INDEX idx_mime_type (mime_type)
);

-- Site Settings Table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'number', 'boolean', 'json', 'image') DEFAULT 'text',
    description TEXT,
    group_name VARCHAR(50) DEFAULT 'general',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_group_name (group_name)
);

-- Newsletter Subscribers Table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    status ENUM('active', 'unsubscribed', 'bounced') DEFAULT 'active',
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    ip_address VARCHAR(45),
    source VARCHAR(100), -- nereden abone oldu
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_email (email)
);

-- Testimonials Table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    client_position VARCHAR(100),
    client_company VARCHAR(100),
    client_photo VARCHAR(255),
    testimonial TEXT NOT NULL,
    rating INT DEFAULT 5,
    project_type VARCHAR(100),
    is_featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_is_featured (is_featured)
);

-- Default Admin User Ekleme
INSERT IGNORE INTO admin_users (username, email, password_hash, full_name, role, is_active) 
VALUES ('admin', 'admin@nextcode.az', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin', TRUE);

-- Default Blog Categories
INSERT IGNORE INTO blog_categories (name, slug, description, color, icon) VALUES
('SEO Optimizasiya', 'seo-optimizasiya', 'Axtarış motoru optimizasiyası məqalələri', '#28a745', 'fas fa-search'),
('Veb İnkişaf', 'veb-inkisaf', 'Veb inkişaf trendləri və texnologiyaları', '#007bff', 'fas fa-code'),
('E-ticarət', 'e-ticaret', 'E-ticarət platformaları və onlayn mağaza inkişafı', '#ffc107', 'fas fa-shopping-cart'),
('Kibertəhlükəsizlik', 'kibertehlukesizlik', 'Veb təhlükəsizliyi və kibertəhlükəsizlik təcrübələri', '#dc3545', 'fas fa-shield-alt'),
('Rəqəmsal Marketinq', 'reqemsal-marketinq', 'Rəqəmsal marketinq strategiyaları və kampaniyaları', '#6f42c1', 'fas fa-bullhorn');

-- Default Services
INSERT IGNORE INTO services (title, description, icon, sort_order) VALUES
('SEO Optimizasiya', 'Axtarış motorlarında yüksək reytinq əldə edin və daha çox müştəri cəlb edin.', 'fas fa-search', 1),
('Sosial Media Marketinq', 'Sosial şəbəkələrdə brendinizi gücləndirib auditoriya yaradın.', 'fas fa-users', 2),
('Brendinq', 'Unikal brend kimliyi yaradıb rəqiblərdən fərqlənin.', 'fas fa-bolt', 3),
('Reklam Kampaniyaları', 'Effektiv reklam strategiyaları ilə satışlarınızı artırın.', 'fas fa-chart-line', 4),
('Veb İnkişaf', 'Müasir və funksional veb saytlar hazırlayırıq.', 'fas fa-code', 5),
('E-ticarət Həlləri', 'Onlayn mağaza və e-ticarət platformaları inkişaf etdiririk.', 'fas fa-shopping-cart', 6);

-- Default Site Settings
INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_type, description, group_name) VALUES
('company_name', 'NextCode Group', 'text', 'Şirkət Adı', 'general'),
('company_email', 'info@nextcode.az', 'text', 'Şirkət E-poçtu', 'general'),
('company_phone', '+380 97 258 00 00', 'text', 'Şirkət Telefonu', 'general'),
('company_address', 'Bakı şəhəri, Nəsimi rayonu, 28 May küçəsi 15', 'text', 'Şirkət Ünvanı', 'general'),
('site_title', 'NextCode Group - Rəqəmsal Marketinq Agentliyi', 'text', 'Sayt Başlığı', 'seo'),
('site_description', 'NextCode Group - SEO, sosial media, brendinq və reklam kampaniyaları xidmətləri təklif edən peşəkar marketinq agentliyi.', 'textarea', 'Sayt Açıqlaması', 'seo'),
('site_keywords', 'rəqəmsal marketinq, SEO, sosial media, brendinq, veb inkişaf, Azərbaycan', 'text', 'Sayt Açar Sözləri', 'seo'),
('google_analytics', '', 'text', 'Google Analytics ID', 'analytics'),
('facebook_pixel', '', 'text', 'Facebook Pixel ID', 'analytics'),
('google_tag_manager', '', 'text', 'Google Tag Manager ID', 'analytics');

-- Default FAQ Items
INSERT IGNORE INTO faq_items (question, answer, category, sort_order) VALUES
('NextCode Group nə xidmətlər təklif edir?', 'Biz SEO optimizasiya, sosial media marketinq, brendinq, reklam kampaniyaları, veb inkişaf və e-ticarət həlləri kimi geniş spektrli rəqəmsal marketinq xidmətləri təklif edirik.', 'general', 1),
('SEO xidməti nə qədər vaxt alır?', 'SEO nəticələri 3-6 ay arasında görünməyə başlayır. Tam effekt 6-12 ay arasında əldə edilir. Bu proses davamlı və uzunmüddətli strategiyadır.', 'seo', 2),
('Sosial media marketinq strategiyası necə hazırlanır?', 'Hədəf auditoriyanızı analiz edərək, brendinizə uyğun kontent strategiyası hazırlayırıq. Düzenli kontent yaradırıq və performansı izləyirik.', 'social-media', 3),
('Veb saytımın inkişafı nə qədər vaxt alır?', 'Sadə veb saytlar 2-4 həftə, mürəkkəb e-ticarət saytları isə 6-12 həftə arasında hazır olur. Layihənin mürəkkəbliyi və tələbləri vaxtı müəyyən edir.', 'web-development', 4);

-- Default Pricing Packages
INSERT IGNORE INTO pricing_packages (name, description, price, currency, features, is_popular, sort_order) VALUES
('Starter', 'Kiçik bizneslər üçün əsas xidmətlər', 500.00, 'AZN', '["SEO analizi", "Sosial media hesabları", "Aylıq hesabat"]', FALSE, 1),
('Professional', 'Orta ölçülü bizneslər üçün geniş paket', 1200.00, 'AZN', '["Tam SEO optimizasiya", "Sosial media marketinq", "Kontent yaradılması", "Aylıq hesabat", "E-poçt dəstəyi"]', TRUE, 2),
('Enterprise', 'Böyük şirkətlər üçün tam xidmət', 2500.00, 'AZN', '["Tam SEO optimizasiya", "Sosial media marketinq", "Kontent yaradılması", "Reklam kampaniyaları", "Veb inkişaf", "Həftəlik hesabat", "Telefon dəstəyi", "Prioritet dəstək"]', FALSE, 3);







