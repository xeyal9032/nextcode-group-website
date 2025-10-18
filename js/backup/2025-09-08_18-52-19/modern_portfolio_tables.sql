-- Modern Portfolio Database Structure
-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS portfolio_analytics;
DROP TABLE IF EXISTS portfolio_project_tags;
DROP TABLE IF EXISTS portfolio_tags;
DROP TABLE IF EXISTS portfolio_project_technologies;
DROP TABLE IF EXISTS portfolio_technologies;
DROP TABLE IF EXISTS portfolio_images;
DROP TABLE IF EXISTS portfolio_projects;
DROP TABLE IF EXISTS portfolio_categories;

-- Portfolio Categories Table
CREATE TABLE portfolio_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7) DEFAULT '#007bff',
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Portfolio Projects Table
CREATE TABLE portfolio_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    short_description TEXT,
    description LONGTEXT,
    featured_image VARCHAR(500),
    category VARCHAR(100),
    technologies TEXT,
    project_url VARCHAR(500),
    github_url VARCHAR(500),
    demo_url VARCHAR(500),
    client_name VARCHAR(255),
    project_date DATE,
    completion_date DATE,
    duration VARCHAR(50),
    team_size INT,
    budget DECIMAL(10,2),
    status ENUM('active', 'inactive', 'draft') DEFAULT 'active',
    is_featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    sort_order INT DEFAULT 0,
    seo_title VARCHAR(255),
    seo_description TEXT,
    seo_keywords TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_created (created_at)
);

-- Portfolio Images Table
CREATE TABLE portfolio_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    caption TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE,
    INDEX idx_project (project_id),
    INDEX idx_featured (is_featured)
);

-- Portfolio Technologies Table
CREATE TABLE portfolio_technologies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(100),
    color VARCHAR(7) DEFAULT '#6c757d',
    category VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Portfolio Project Technologies (Many-to-Many)
CREATE TABLE portfolio_project_technologies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    technology_id INT NOT NULL,
    proficiency_level ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'intermediate',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (technology_id) REFERENCES portfolio_technologies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_tech (project_id, technology_id)
);

-- Portfolio Tags Table
CREATE TABLE portfolio_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    color VARCHAR(7) DEFAULT '#28a745',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Portfolio Project Tags (Many-to-Many)
CREATE TABLE portfolio_project_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    tag_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES portfolio_tags(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_tag (project_id, tag_id)
);

-- Portfolio Analytics Table
CREATE TABLE portfolio_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    visitor_ip VARCHAR(45),
    user_agent TEXT,
    referrer VARCHAR(500),
    view_date DATE NOT NULL,
    view_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE,
    INDEX idx_project_date (project_id, view_date),
    INDEX idx_date (view_date)
);

-- Insert sample categories
INSERT INTO portfolio_categories (name, slug, description, color, icon, sort_order) VALUES
('Web Development', 'web-development', 'Modern web applications and websites', '#007bff', 'fas fa-globe', 1),
('Mobile Apps', 'mobile-apps', 'iOS and Android mobile applications', '#28a745', 'fas fa-mobile-alt', 2),
('E-commerce', 'e-commerce', 'Online stores and shopping platforms', '#ffc107', 'fas fa-shopping-cart', 3),
('UI/UX Design', 'ui-ux-design', 'User interface and experience design', '#e83e8c', 'fas fa-paint-brush', 4),
('API Development', 'api-development', 'RESTful APIs and backend services', '#6f42c1', 'fas fa-code', 5);

-- Insert sample technologies
INSERT INTO portfolio_technologies (name, slug, description, icon, color, category, sort_order) VALUES
('PHP', 'php', 'Server-side scripting language', 'fab fa-php', '#777bb4', 'Backend', 1),
('JavaScript', 'javascript', 'Dynamic programming language', 'fab fa-js-square', '#f7df1e', 'Frontend', 2),
('React', 'react', 'JavaScript library for building user interfaces', 'fab fa-react', '#61dafb', 'Frontend', 3),
('Vue.js', 'vuejs', 'Progressive JavaScript framework', 'fab fa-vuejs', '#4fc08d', 'Frontend', 4),
('Node.js', 'nodejs', 'JavaScript runtime for server-side development', 'fab fa-node-js', '#339933', 'Backend', 5),
('MySQL', 'mysql', 'Relational database management system', 'fas fa-database', '#4479a1', 'Database', 6),
('Bootstrap', 'bootstrap', 'CSS framework for responsive design', 'fab fa-bootstrap', '#7952b3', 'Frontend', 7),
('Laravel', 'laravel', 'PHP web application framework', 'fab fa-laravel', '#ff2d20', 'Backend', 8);

-- Insert sample project
INSERT INTO portfolio_projects (
    title, slug, short_description, description, featured_image, category, technologies,
    project_url, github_url, demo_url, client_name, project_date, completion_date,
    duration, team_size, budget, status, is_featured, views
) VALUES (
    'Modern E-commerce Platform',
    'modern-ecommerce-platform',
    'A comprehensive e-commerce solution with modern design and advanced features.',
    'This project involved creating a full-featured e-commerce platform with user authentication, product management, shopping cart, payment integration, and admin dashboard. The platform is built with modern technologies and follows best practices for security and performance.',
    '/assets/images/portfolio/ecommerce-featured.jpg',
    'E-commerce',
    'PHP, JavaScript, MySQL, Bootstrap, jQuery',
    'https://demo-ecommerce.nextcode.az',
    'https://github.com/nextcode/ecommerce-platform',
    'https://demo-ecommerce.nextcode.az',
    'NextCode Solutions',
    '2024-01-15',
    '2024-03-20',
    '2 months',
    3,
    5000.00,
    'active',
    TRUE,
    156
);

-- Insert sample tags
INSERT INTO portfolio_tags (name, slug, color) VALUES
('Responsive', 'responsive', '#007bff'),
('Modern', 'modern', '#28a745'),
('Fast', 'fast', '#ffc107'),
('Secure', 'secure', '#dc3545'),
('SEO Friendly', 'seo-friendly', '#6f42c1');

-- Insert sample project images
INSERT INTO portfolio_images (project_id, image_path, alt_text, caption, is_featured, sort_order) VALUES
(1, '/assets/images/portfolio/ecommerce-1.jpg', 'E-commerce Homepage', 'Modern and clean homepage design', TRUE, 1),
(1, '/assets/images/portfolio/ecommerce-2.jpg', 'Product Listing', 'Advanced product filtering and search', FALSE, 2),
(1, '/assets/images/portfolio/ecommerce-3.jpg', 'Shopping Cart', 'Intuitive shopping cart interface', FALSE, 3),
(1, '/assets/images/portfolio/ecommerce-4.jpg', 'Admin Dashboard', 'Comprehensive admin control panel', FALSE, 4);

-- Insert sample project tags
INSERT INTO portfolio_project_tags (project_id, tag_id) VALUES
(1, 1), -- Responsive
(1, 2), -- Modern
(1, 3), -- Fast
(1, 4); -- Secure

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;