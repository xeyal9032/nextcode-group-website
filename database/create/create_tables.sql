-- NextCode Group Database Tables
-- Create database tables for web application

-- Admin Users Table - REMOVED (Admin panel kaldırıldı)
-- CREATE TABLE IF NOT EXISTS admin_users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     username VARCHAR(50) UNIQUE NOT NULL,
--     password VARCHAR(255) NOT NULL,
--     email VARCHAR(100) UNIQUE NOT NULL,
--     full_name VARCHAR(100),
--     role ENUM('admin', 'editor') DEFAULT 'admin',
--     status ENUM('active', 'inactive') DEFAULT 'active',
--     last_login TIMESTAMP NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
-- );

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    price VARCHAR(50),
    features TEXT,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Portfolio Projects Table
CREATE TABLE IF NOT EXISTS portfolio_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    url VARCHAR(255),
    image_url VARCHAR(255),
    technologies TEXT,
    client_name VARCHAR(100),
    project_date DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blog Categories Table
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blog Posts Table
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
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    -- FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL -- Admin referansı kaldırıldı
    author_id INT NULL -- Admin referansı olmadan bırakıldı
);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'archived') DEFAULT 'unread',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Newsletter Subscribers Table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Media Files Table
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
    -- FOREIGN KEY (uploaded_by) REFERENCES admin_users(id) ON DELETE SET NULL -- Admin referansı kaldırıldı
    uploaded_by INT NULL -- Admin referansı olmadan bırakıldı
);

-- Navigation Items Table
CREATE TABLE IF NOT EXISTS navigation_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES navigation_items(id) ON DELETE CASCADE
);

-- Site Settings Table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'number', 'boolean', 'json') DEFAULT 'text',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Content Pages Table
CREATE TABLE IF NOT EXISTS content_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(100) NOT NULL,
    page_slug VARCHAR(100) UNIQUE NOT NULL,
    title VARCHAR(200),
    content LONGTEXT,
    meta_title VARCHAR(200),
    meta_description TEXT,
    language VARCHAR(5) DEFAULT 'az',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Activity Log Table
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE SET NULL -- Admin referansı kaldırıldı
    user_id INT NULL -- Admin referansı olmadan bırakıldı
);

-- Insert default admin user - REMOVED (Admin panel kaldırıldı)
-- INSERT IGNORE INTO admin_users (username, password, email, full_name, role) 
-- VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@nextcode.com', 'Admin User', 'admin');

-- Insert default services
INSERT IGNORE INTO services (title, description, icon, sort_order) VALUES
('SEO Optimizasiya', 'Axtarış motorlarında yüksək reytinq əldə edin və daha çox müştəri cəlb edin.', 'fas fa-search', 1),
('Sosial Media', 'Sosial şəbəkələrdə brendinizi gücləndirib auditoriya yaradın.', 'fas fa-users', 2),
('Brendinq', 'Unikal brend kimliyi yaradıb rəqiblərdən fərqlənin.', 'fas fa-bolt', 3),
('Reklam Kampaniyaları', 'Effektiv reklam strategiyaları ilə satışlarınızı artırın.', 'fas fa-chart-line', 4);

-- Insert default site settings
INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
('company_name', 'NextCode Group', 'text', 'Company Name'),
('company_email', 'info@nextcode.com', 'text', 'Company Email'),
('company_phone', '+380972580000', 'text', 'Company Phone'),
('company_address', 'Bakı şəhəri, Nəsimi rayonu, 28 May küçəsi 15', 'text', 'Company Address'),
('site_title', 'NextCode Group - Rəqəmsal Marketinq Agentliyi', 'text', 'Site Title'),
('site_description', 'NextCode Group - SEO, sosial media, brendinq və reklam kampaniyaları xidmətləri təklif edən peşəkar marketinq agentliyi.', 'textarea', 'Site Description');

-- Insert default blog categories
INSERT IGNORE INTO blog_categories (name, slug, description) VALUES
('SEO', 'seo', 'Axtarış motoru optimizasiyası məqalələri'),
('Veb İnkişaf', 'veb-inkisaf', 'Veb inkişaf trendləri və texnologiyaları'),
('E-ticarət', 'e-ticaret', 'E-ticarət platformaları və onlayn mağaza inkişafı'),
('Kibertəhlükəsizlik', 'kibertehlukesizlik', 'Veb təhlükəsizliyi və kibertəhlükəsizlik təcrübələri');