-- NextCode Group - Database Performance Indexes
-- Mevcut tabloları bozmadan performans indexleri ekler

-- Blog tabloları için performans indexleri
ALTER TABLE blog_posts ADD INDEX idx_blog_status_published (status, published_at);
ALTER TABLE blog_posts ADD INDEX idx_blog_category_status (category_id, status);
ALTER TABLE blog_posts ADD INDEX idx_blog_featured_status (is_featured, status);
ALTER TABLE blog_posts ADD INDEX idx_blog_created_status (created_at, status);

-- Portfolio tabloları için performans indexleri
ALTER TABLE portfolio_projects ADD INDEX idx_portfolio_status_featured (status, featured);
ALTER TABLE portfolio_projects ADD INDEX idx_portfolio_category_status (category, status);
ALTER TABLE portfolio_projects ADD INDEX idx_portfolio_sort_status (sort_order, status);

-- Services tablosu için performans indexleri
ALTER TABLE services ADD INDEX idx_services_active_order (is_active, order_index);
ALTER TABLE services ADD INDEX idx_services_title_active (title, is_active);

-- FAQ tablosu için performans indexleri
ALTER TABLE faq ADD INDEX idx_faq_category_active (category, is_active);
ALTER TABLE faq ADD INDEX idx_faq_sort_active (sort_order, is_active);

-- Pricing tabloları için performans indexleri
ALTER TABLE pricing_packages ADD INDEX idx_pricing_active_popular (is_active, is_popular);
ALTER TABLE pricing_packages ADD INDEX idx_pricing_sort_active (sort_order, is_active);

-- Contact messages için performans indexleri
ALTER TABLE contact_messages ADD INDEX idx_contact_status_created (status, created_at);
ALTER TABLE contact_messages ADD INDEX idx_contact_email_created (email, created_at);
ALTER TABLE contact_messages ADD INDEX idx_contact_read_created (is_read, created_at);

-- Site content için performans indexleri
ALTER TABLE site_content ADD INDEX idx_content_page_section (page_name, section_name);
ALTER TABLE site_content ADD INDEX idx_content_active_sort (is_active, id);

-- Pages tablosu için performans indexleri
ALTER TABLE pages ADD INDEX idx_pages_slug_status (slug, status);
ALTER TABLE pages ADD INDEX idx_pages_status_created (status, created_at);

-- Admin users için performans indexleri
ALTER TABLE admin_users ADD INDEX idx_admin_active_role (is_active, role);
ALTER TABLE admin_users ADD INDEX idx_admin_email_active (email, is_active);

-- Site statistics için performans indexleri
ALTER TABLE site_statistics ADD INDEX idx_stats_active_created (is_active, created_at);

-- Team members için performans indexleri
ALTER TABLE team_members ADD INDEX idx_team_active_sort (is_active, sort_order);

-- Company stats için performans indexleri
ALTER TABLE company_stats ADD INDEX idx_company_active_created (is_active, created_at);

-- Service packages için performans indexleri
ALTER TABLE service_packages ADD INDEX idx_service_active_sort (is_active, sort_order);

-- FAQ categories için performans indexleri
ALTER TABLE faq_categories ADD INDEX idx_faq_cat_active_sort (is_active, sort_order);

-- Package features için performans indexleri
ALTER TABLE package_features ADD INDEX idx_package_feat_active_sort (is_active, sort_order);

-- Composite indexler (çoklu sütun sorgular için)
ALTER TABLE blog_posts ADD INDEX idx_blog_composite (status, is_featured, published_at);
ALTER TABLE portfolio_projects ADD INDEX idx_portfolio_composite (status, featured, sort_order);
ALTER TABLE contact_messages ADD INDEX idx_contact_composite (status, is_read, created_at);

-- Full-text search indexleri (arama performansı için)
ALTER TABLE blog_posts ADD FULLTEXT idx_blog_search (title, content, excerpt);
ALTER TABLE portfolio_projects ADD FULLTEXT idx_portfolio_search (title, description);
ALTER TABLE services ADD FULLTEXT idx_services_search (title, description);

-- Optimize tablolar (fragmentation temizleme)
OPTIMIZE TABLE blog_posts;
OPTIMIZE TABLE portfolio_projects;
OPTIMIZE TABLE services;
OPTIMIZE TABLE contact_messages;
OPTIMIZE TABLE site_content;
OPTIMIZE TABLE pages;
OPTIMIZE TABLE admin_users;
OPTIMIZE TABLE site_statistics;
OPTIMIZE TABLE team_members;
OPTIMIZE TABLE company_stats;
OPTIMIZE TABLE service_packages;
OPTIMIZE TABLE faq_categories;
OPTIMIZE TABLE package_features;
OPTIMIZE TABLE faq;
OPTIMIZE TABLE pricing_packages;
OPTIMIZE TABLE blog_categories;
OPTIMIZE TABLE portfolio_categories;
OPTIMIZE TABLE about_content;
OPTIMIZE TABLE contact_info;
