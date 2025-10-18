-- Pricing Tables for NextCode Group
-- Created for pricing page functionality

-- Pricing packages table
CREATE TABLE IF NOT EXISTS pricing_packages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    currency TEXT DEFAULT 'AZN',
    billing_period TEXT DEFAULT 'monthly', -- monthly, yearly
    discount_percentage INTEGER DEFAULT 0,
    is_popular INTEGER DEFAULT 0,
    is_featured INTEGER DEFAULT 0,
    active INTEGER DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    button_text TEXT DEFAULT 'Seç',
    button_link TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Package features table
CREATE TABLE IF NOT EXISTS package_features (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    package_id INTEGER,
    feature_name TEXT NOT NULL,
    feature_description TEXT,
    feature_value TEXT, -- e.g., "5 GB", "Unlimited", "Yes", "No"
    is_included INTEGER DEFAULT 1, -- 1 = included, 0 = not included
    is_highlighted INTEGER DEFAULT 0,
    active INTEGER DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES pricing_packages(id) ON DELETE CASCADE
);

-- Insert sample pricing packages
INSERT INTO pricing_packages (name, slug, description, price, billing_period, is_popular, sort_order, button_text, button_link) VALUES
('Başlanğıc', 'basic', 'Kiçik bizneslər üçün ideal paket', 299.00, 'monthly', 0, 1, 'Başla', '/contact'),
('Peşəkar', 'professional', 'Böyüyən bizneslər üçün ən populyar seçim', 599.00, 'monthly', 1, 2, 'Seç', '/contact'),
('Premium', 'premium', 'Böyük şirkətlər üçün tam həll', 999.00, 'monthly', 0, 3, 'Əlaqə', '/contact'),
('Korporativ', 'enterprise', 'Fərdi həllər və tam dəstək', 1999.00, 'monthly', 0, 4, 'Danışaq', '/contact');

-- Insert features for Basic package
INSERT INTO package_features (package_id, feature_name, feature_description, feature_value, is_included, sort_order) VALUES
(1, 'SEO Analizi', 'Aylıq SEO performans hesabatı', '1 dəfə', 1, 1),
(1, 'Sosial Media', 'Facebook və Instagram idarəetməsi', '2 platform', 1, 2),
(1, 'Məzmun Yaradılması', 'Aylıq blog məqalələri', '4 məqalə', 1, 3),
(1, 'Google Ads', 'Aylıq reklam büdcəsi', '500 AZN', 1, 4),
(1, 'Dəstək', 'Email dəstəyi', 'İş saatları', 1, 5),
(1, 'Hesabat', 'Aylıq performans hesabatı', 'Əsas metrikalar', 1, 6);

-- Insert features for Professional package
INSERT INTO package_features (package_id, feature_name, feature_description, feature_value, is_included, sort_order) VALUES
(2, 'SEO Analizi', 'Həftəlik SEO performans hesabatı', 'Həftəlik', 1, 1),
(2, 'Sosial Media', 'Bütün əsas platformların idarəetməsi', '5 platform', 1, 2),
(2, 'Məzmun Yaradılması', 'Həftəlik blog və sosial media məzmunu', '8 məqalə', 1, 3),
(2, 'Google Ads', 'Aylıq reklam büdcəsi', '1000 AZN', 1, 4),
(2, 'Email Marketinq', 'Aylıq email kampaniyaları', '4 kampaniya', 1, 5),
(2, 'Dəstək', 'Telefon və email dəstəyi', '24/7', 1, 6),
(2, 'Hesabat', 'Həftəlik detallı hesabat', 'Tam analitika', 1, 7),
(2, 'Veb Sayt', 'Landing page yaradılması', '2 səhifə', 1, 8);

-- Insert features for Premium package
INSERT INTO package_features (package_id, feature_name, feature_description, feature_value, is_included, sort_order) VALUES
(3, 'SEO Analizi', 'Gündəlik SEO monitorinq', 'Real-time', 1, 1),
(3, 'Sosial Media', 'Bütün platformlar + influencer əməkdaşlığı', 'Limitsiz', 1, 2),
(3, 'Məzmun Yaradılması', 'Gündəlik məzmun və video', '15 məqalə', 1, 3),
(3, 'Google Ads', 'Aylıq reklam büdcəsi', '2000 AZN', 1, 4),
(3, 'Email Marketinq', 'Avtomatik email ardıcıllığı', 'Limitsiz', 1, 5),
(3, 'Dəstək', 'Şəxsi menecər və 24/7 dəstək', 'Premium', 1, 6),
(3, 'Hesabat', 'Real-time dashboard', 'Canlı data', 1, 7),
(3, 'Veb Sayt', 'Tam veb sayt inkişafı', 'Limitsiz', 1, 8),
(3, 'Branding', 'Logo və brand identity', 'Daxildir', 1, 9),
(3, 'CRM İnteqrasiya', 'Müştəri idarəetmə sistemi', 'Daxildir', 1, 10);

-- Insert features for Enterprise package
INSERT INTO package_features (package_id, feature_name, feature_description, feature_value, is_included, sort_order) VALUES
(4, 'SEO Analizi', 'Korporativ SEO strategiyası', 'Fərdi', 1, 1),
(4, 'Sosial Media', 'Çoxkanallı sosial media strategiyası', 'Fərdi', 1, 2),
(4, 'Məzmun Yaradılması', 'Korporativ məzmun strategiyası', 'Limitsiz', 1, 3),
(4, 'Reklam', 'Çoxkanallı reklam kampaniyaları', '5000+ AZN', 1, 4),
(4, 'Email Marketinq', 'Korporativ email həlləri', 'Fərdi', 1, 5),
(4, 'Dəstək', 'Xüsusi komanda və prioritet dəstək', 'VIP', 1, 6),
(4, 'Hesabat', 'Korporativ dashboard və BI', 'Fərdi', 1, 7),
(4, 'Veb Həllər', 'Tam rəqəmsal transformasiya', 'Fərdi', 1, 8),
(4, 'Branding', 'Tam brand strategiyası', 'Fərdi', 1, 9),
(4, 'İnteqrasiya', 'Bütün sistemlərin inteqrasiyası', 'Fərdi', 1, 10),
(4, 'Təlim', 'Komanda təlimi və məsləhət', 'Daxildir', 1, 11),
(4, 'SLA', 'Xidmət səviyyəsi razılaşması', '99.9%', 1, 12);