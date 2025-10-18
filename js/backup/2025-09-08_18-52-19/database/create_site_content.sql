-- Site Content Table for SQLite
-- Bu tablo web sitesinin dinamik içeriklerini saklar

CREATE TABLE IF NOT EXISTS site_content (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  content_key TEXT NOT NULL UNIQUE,
  content_value TEXT,
  content_type TEXT DEFAULT 'text' CHECK (content_type IN ('text','html','image','link','json')),
  page_section TEXT DEFAULT 'general',
  description TEXT,
  is_active INTEGER DEFAULT 1,
  sort_order INTEGER DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes
CREATE INDEX IF NOT EXISTS idx_content_key ON site_content(content_key);
CREATE INDEX IF NOT EXISTS idx_page_section ON site_content(page_section);
CREATE INDEX IF NOT EXISTS idx_is_active ON site_content(is_active);

-- Temel içerik verilerini ekle
INSERT OR IGNORE INTO site_content (content_key, content_value, content_type, page_section, description) VALUES
-- Hero Section
('hero_title', 'NextCode Group ile Dijital Dönüşümünüzü Başlatın', 'text', 'hero', 'Ana sayfa hero başlığı'),
('hero_subtitle', 'Modern web çözümleri, mobil uygulamalar ve dijital pazarlama hizmetleri ile işinizi büyütün', 'text', 'hero', 'Ana sayfa hero alt başlığı'),
('hero_button_text', 'Hemen Başlayın', 'text', 'hero', 'Hero bölümü buton metni'),
('hero_button_link', '/contact.php', 'link', 'hero', 'Hero bölümü buton linki'),
('hero_video_text', 'Tanıtım Videosunu İzleyin', 'text', 'hero', 'Video buton metni'),

-- About Section
('about_title', 'Hakkımızda', 'text', 'about', 'Hakkımızda bölümü başlığı'),
('about_subtitle', 'Dijital dünyada fark yaratan çözümler', 'text', 'about', 'Hakkımızda alt başlığı'),
('about_description', 'NextCode Group olarak, modern teknolojiler kullanarak işletmenizin dijital dönüşümünü destekliyoruz. Uzman ekibimizle web tasarım, mobil uygulama geliştirme ve dijital pazarlama alanlarında hizmet veriyoruz.', 'html', 'about', 'Hakkımızda açıklaması'),
('about_button_text', 'Daha Fazla Bilgi', 'text', 'about', 'Hakkımızda buton metni'),
('about_button_link', '/about.php', 'link', 'about', 'Hakkımızda buton linki'),

-- Services Section
('services_title', 'Hizmetlerimiz', 'text', 'services', 'Hizmetler bölümü başlığı'),
('services_subtitle', 'Kapsamlı dijital çözümler', 'text', 'services', 'Hizmetler alt başlığı'),
('services_description', 'Web tasarımdan mobil uygulamalara, SEO dan sosyal medya yönetimine kadar geniş hizmet yelpazemizle yanınızdayız.', 'text', 'services', 'Hizmetler açıklaması'),

-- Portfolio Section
('portfolio_title', 'Portföyümüz', 'text', 'portfolio', 'Portföy bölümü başlığı'),
('portfolio_subtitle', 'Başarılı projelerimiz', 'text', 'portfolio', 'Portföy alt başlığı'),
('portfolio_description', 'Farklı sektörlerden müşterilerimiz için gerçekleştirdiğimiz başarılı projeleri inceleyin.', 'text', 'portfolio', 'Portföy açıklaması'),
('portfolio_button_text', 'Tüm Projeleri Görüntüle', 'text', 'portfolio', 'Portföy buton metni'),
('portfolio_button_link', '/portfolio.php', 'link', 'portfolio', 'Portföy buton linki'),

-- Contact Section
('contact_title', 'İletişim', 'text', 'contact', 'İletişim bölümü başlığı'),
('contact_subtitle', 'Projeleriniz için bizimle iletişime geçin', 'text', 'contact', 'İletişim alt başlığı'),
('contact_description', 'Dijital projeleriniz için profesyonel destek almak istiyorsanız, hemen bizimle iletişime geçin.', 'text', 'contact', 'İletişim açıklaması'),
('contact_button_text', 'İletişime Geç', 'text', 'contact', 'İletişim buton metni'),
('contact_button_link', '/contact.php', 'link', 'contact', 'İletişim buton linki'),

-- Footer Section
('footer_description', 'NextCode Group - Dijital dönüşümünüzün güvenilir partneri', 'text', 'footer', 'Footer açıklaması'),
('footer_copyright', '© 2024 NextCode Group. Tüm hakları saklıdır.', 'text', 'footer', 'Telif hakkı metni'),

-- Company Info
('company_name', 'NextCode Group', 'text', 'general', 'Şirket adı'),
('company_email', 'info@nextcode.com', 'text', 'general', 'Şirket e-posta'),
('company_phone', '+90 555 123 4567', 'text', 'general', 'Şirket telefon'),
('company_address', 'İstanbul, Türkiye', 'text', 'general', 'Şirket adresi'),

-- Social Media
('social_facebook', 'https://facebook.com/nextcodegroup', 'link', 'general', 'Facebook linki'),
('social_instagram', 'https://instagram.com/nextcodegroup', 'link', 'general', 'Instagram linki'),
('social_linkedin', 'https://linkedin.com/company/nextcodegroup', 'link', 'general', 'LinkedIn linki'),
('social_twitter', 'https://twitter.com/nextcodegroup', 'link', 'general', 'Twitter linki');