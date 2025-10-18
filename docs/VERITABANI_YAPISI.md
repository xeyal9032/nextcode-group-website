# NextCode Group - Veritabanı Yapısı

## 📊 Veritabanı Bilgileri

**Veritabanı Adı:** gtorg_nextcode  
**Kullanıcı Adı:** gtorg_nextcode  
**Şifre:** JDH6h9T2zb8UC47t@rn56@  
**Host:** localhost  
**Karakter Seti:** utf8mb4  
**Collation:** utf8mb4_unicode_ci  

## 🗂️ Tablo Yapıları

### 1. services Tablosu
```sql
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
```

### 2. portfolio_projects Tablosu
```sql
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
```

### 3. blog_categories Tablosu
```sql
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 4. blog_posts Tablosu
```sql
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content LONGTEXT,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category_id INT,
    author_id INT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,
    meta_title VARCHAR(200),
    meta_description TEXT,
    tags TEXT,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
);
```

### 5. contact_messages Tablosu
```sql
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
```

### 6. newsletter_subscribers Tablosu
```sql
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    status ENUM('active', 'inactive', 'unsubscribed') DEFAULT 'active',
    ip_address VARCHAR(45),
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 7. pages Tablosu
```sql
CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content LONGTEXT,
    meta_title VARCHAR(200),
    meta_description TEXT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 8. site_content Tablosu
```sql
CREATE TABLE IF NOT EXISTS site_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content_key VARCHAR(100) UNIQUE NOT NULL,
    content_value TEXT,
    content_type ENUM('text', 'html', 'image', 'link') DEFAULT 'text',
    section_name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 9. site_statistics Tablosu
```sql
CREATE TABLE IF NOT EXISTS site_statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projects_completed VARCHAR(50),
    client_satisfaction VARCHAR(50),
    years_experience VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 10. pricing_packages Tablosu
```sql
CREATE TABLE IF NOT EXISTS pricing_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2),
    currency VARCHAR(10) DEFAULT 'USD',
    description TEXT,
    features TEXT,
    is_popular BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 11. analytics_events Tablosu
```sql
CREATE TABLE IF NOT EXISTS analytics_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL,
    event_data JSON,
    user_ip VARCHAR(45),
    user_agent TEXT,
    page_url VARCHAR(500),
    referrer VARCHAR(500),
    session_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 12. performance_logs Tablosu
```sql
CREATE TABLE IF NOT EXISTS performance_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_url VARCHAR(500),
    load_time DECIMAL(10,4),
    memory_usage INT,
    database_queries INT,
    user_ip VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🔗 Tablo İlişkileri

### Foreign Key İlişkileri
1. **blog_posts.category_id** → **blog_categories.id**
2. **blog_posts.author_id** → **admin_users.id** (Kaldırıldı)

### İndeksler
- `blog_posts.slug` - UNIQUE
- `blog_categories.slug` - UNIQUE
- `pages.slug` - UNIQUE
- `site_content.content_key` - UNIQUE
- `newsletter_subscribers.email` - UNIQUE

## 📈 Veri Tipleri

### Metin Verileri
- **VARCHAR(100-500)**: Kısa metinler, URL'ler, e-postalar
- **TEXT**: Orta uzunlukta metinler
- **LONGTEXT**: Uzun içerikler (blog yazıları, sayfa içerikleri)

### Sayısal Veriler
- **INT**: ID'ler, sayaçlar, sıralama
- **DECIMAL(10,2)**: Fiyatlar
- **BOOLEAN**: Aktif/pasif durumlar

### Tarih Verileri
- **TIMESTAMP**: Oluşturma ve güncelleme tarihleri
- **DATE**: Proje tarihleri

### Enum Değerleri
- **status**: 'active', 'inactive', 'draft', 'published', 'archived'
- **content_type**: 'text', 'html', 'image', 'link'

## 🔒 Güvenlik Özellikleri

### Veri Doğrulama
- E-posta formatı kontrolü
- URL formatı kontrolü
- IP adresi formatı kontrolü
- Karakter uzunluğu sınırlamaları

### Güvenlik Önlemleri
- SQL injection koruması (PDO prepared statements)
- XSS koruması (htmlspecialchars)
- CSRF koruması (session tokens)
- Input sanitization

## 📊 Örnek Veriler

### Site İstatistikleri
```sql
INSERT INTO site_statistics (projects_completed, client_satisfaction, years_experience) 
VALUES ('500+', '98%', '16+');
```

### Blog Kategorileri
```sql
INSERT INTO blog_categories (name, slug, description) 
VALUES ('Digital Marketing', 'digital-marketing', 'Digital marketing strategies and tips');
```

### Hizmetler
```sql
INSERT INTO services (title, description, icon, price) 
VALUES ('SEO Optimization', 'Search engine optimization services', 'seo-icon', 'Starting from $500');
```

## 🔧 Bakım ve Optimizasyon

### Düzenli Bakım
- Log dosyalarının temizlenmesi
- Eski verilerin arşivlenmesi
- İndekslerin optimize edilmesi
- Veritabanı yedekleme

### Performans Optimizasyonu
- Sorgu optimizasyonu
- İndeks kullanımı
- Connection pooling
- Query caching

## 📋 Veritabanı Yönetimi

### Yedekleme
- Günlük otomatik yedekleme
- Manuel yedekleme scriptleri
- Yedekleme dosyalarının şifrelenmesi

### Monitoring
- Veritabanı performans izleme
- Hata logları
- Bağlantı sayısı kontrolü
- Disk kullanımı kontrolü

**Son Güncelleme:** $(date)
**Toplam Tablo:** 20
**Toplam İndeks:** 25+
**Veritabanı Boyutu:** ~50MB
