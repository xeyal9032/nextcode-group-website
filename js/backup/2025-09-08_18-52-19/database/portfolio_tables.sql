-- Portfolio System Database Tables
-- Created for comprehensive portfolio management system

-- Categories table for project categorization
CREATE TABLE IF NOT EXISTS portfolio_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7) DEFAULT '#2563eb',
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Main projects table
CREATE TABLE IF NOT EXISTS portfolio_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    short_description TEXT,
    full_description LONGTEXT,
    client_name VARCHAR(255),
    project_url VARCHAR(500),
    github_url VARCHAR(500),
    demo_url VARCHAR(500),
    technologies JSON,
    category_id INT,
    featured_image VARCHAR(500),
    project_date DATE,
    completion_date DATE,
    project_status ENUM('planning', 'in_progress', 'completed', 'on_hold') DEFAULT 'completed',
    budget DECIMAL(10,2),
    team_size INT,
    duration_months INT,
    key_results JSON,
    challenges_solved TEXT,
    lessons_learned TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    is_published BOOLEAN DEFAULT TRUE,
    view_count INT DEFAULT 0,
    sort_order INT DEFAULT 0,
    seo_title VARCHAR(255),
    seo_description TEXT,
    seo_keywords VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES portfolio_categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_featured (is_featured),
    INDEX idx_published (is_published),
    INDEX idx_slug (slug)
);

-- Project images gallery
CREATE TABLE IF NOT EXISTS portfolio_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    image_title VARCHAR(255),
    image_alt VARCHAR(255),
    image_description TEXT,
    image_type ENUM('thumbnail', 'gallery', 'before', 'after', 'mockup', 'screenshot') DEFAULT 'gallery',
    sort_order INT DEFAULT 0,
    is_primary BOOLEAN DEFAULT FALSE,
    file_size INT,
    image_width INT,
    image_height INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE,
    INDEX idx_project (project_id),
    INDEX idx_type (image_type),
    INDEX idx_primary (is_primary)
);

-- Client testimonials
CREATE TABLE IF NOT EXISTS portfolio_testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    client_name VARCHAR(255) NOT NULL,
    client_position VARCHAR(255),
    client_company VARCHAR(255),
    client_email VARCHAR(255),
    client_photo VARCHAR(500),
    testimonial_text LONGTEXT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    is_featured BOOLEAN DEFAULT FALSE,
    is_published BOOLEAN DEFAULT TRUE,
    testimonial_date DATE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE SET NULL,
    INDEX idx_project (project_id),
    INDEX idx_featured (is_featured),
    INDEX idx_published (is_published)
);

-- Insert default categories
INSERT INTO portfolio_categories (name, slug, description, color, icon, sort_order) VALUES
('Web Development', 'web-development', 'Modern web applications and websites', '#2563eb', 'fas fa-code', 1),
('Mobile Apps', 'mobile-apps', 'iOS and Android mobile applications', '#059669', 'fas fa-mobile-alt', 2),
('E-commerce', 'e-commerce', 'Online stores and shopping platforms', '#dc2626', 'fas fa-shopping-cart', 3),
('SEO & Marketing', 'seo-marketing', 'Search engine optimization and digital marketing', '#7c3aed', 'fas fa-chart-line', 4),
('UI/UX Design', 'ui-ux-design', 'User interface and user experience design', '#ea580c', 'fas fa-paint-brush', 5),
('Corporate Websites', 'corporate-websites', 'Business and corporate web solutions', '#0891b2', 'fas fa-building', 6);

-- Insert sample projects
INSERT INTO portfolio_projects (
    title, slug, short_description, full_description, client_name, 
    category_id, featured_image, project_date, technologies, key_results
) VALUES
(
    'Kosmetika Mağazası E-commerce Platform',
    'kosmetika-magazasi',
    'Modern və funksional kosmetika mağazası üçün e-commerce platforması',
    'Müştəri üçün tam funksional e-commerce platforması hazırladıq. Platform müasir dizayn, asan naviqasiya və güclü admin paneli ilə təchiz edilib.',
    'Beauty Store LLC',
    3,
    '/assets/images/portfolio/kosmetika-store.jpg',
    '2024-01-15',
    '["PHP", "MySQL", "JavaScript", "Bootstrap", "PayPal API"]',
    '["45% artım satışlarda", "60% artım konversiya nisbətində", "200+ məhsul kataloqu"]'
),
(
    'Mobil Oyun Tətbiqi',
    'mobil-oyun-tetbiqi',
    'iOS və Android üçün əyləncəli puzzle oyunu',
    'Yaradıcı puzzle oyunu hazırladıq. Oyun müxtəlif səviyyələr, achievement sistemi və sosial paylaşım funksiyaları ilə təchiz edilib.',
    'GameDev Studio',
    2,
    '/assets/images/portfolio/mobile-game.jpg',
    '2024-02-20',
    '["React Native", "Firebase", "Redux", "Admob"]',
    '["50,000+ yükləmə", "4.8 reytinq App Store-da", "25% retention rate"]'
),
(
    'Hüquq Firması Korporativ Saytı',
    'huquq-firmasi-sayti',
    'Peşəkar hüquq firması üçün korporativ veb sayt',
    'Hüquq firması üçün peşəkar və etibarlı görünüşlü veb sayt hazırladıq. Sayt müştəri portali, blog və əlaqə formları ilə təchiz edilib.',
    'Legal Partners',
    6,
    '/assets/images/portfolio/law-firm.jpg',
    '2024-03-10',
    '["WordPress", "PHP", "MySQL", "Bootstrap", "Contact Form 7"]',
    '["300% artım müştəri sorğularında", "Peşəkar imicin güclənməsi", "SEO optimizasiyası"]'
);

-- Insert sample testimonials
INSERT INTO portfolio_testimonials (
    project_id, client_name, client_position, client_company, 
    testimonial_text, rating, is_featured
) VALUES
(
    1,
    'Aysel Məmmədova',
    'Direktor',
    'Beauty Store LLC',
    'NextCode komandası ilə işləmək böyük zövq idi. Onlar bizim tələblərimizi mükəmməl başa düşdülər və gözlədiyimizdən də yaxşı nəticə verdilər.',
    5,
    TRUE
),
(
    2,
    'Rəşad Əliyev',
    'CEO',
    'GameDev Studio',
    'Mobil oyunumuz üçün hazırladıqları platform həqiqətən peşəkardır. Texniki dəstək və keyfiyyət əla səviyyədədir.',
    5,
    TRUE
),
(
    3,
    'Vüqar Həsənov',
    'Baş Hüquqşünas',
    'Legal Partners',
    'Veb saytımız sayəsində müştəri axını əhəmiyyətli dərəcədə artdı. Dizayn və funksionallıq mükəmməldir.',
    5,
    FALSE
);