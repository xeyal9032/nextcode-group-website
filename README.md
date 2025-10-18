# NextCode Group - Digital Marketing Agency Website

## 📋 Proje Hakkında

NextCode Group, Azərbaycan'da faaliyet gösteren profesyonel bir rəqəmsal marketinq agentliyidir. Bu web projesi, şirketin tüm dijital hizmetlerini tanıtan, müşterilerle iletişim kuran ve portföy sergileyen modern ve güvenli bir web sitesidir.

**Proje Durumu:** ✅ Production Ready  
**Son Güncelleme:** 2025-01-09  
**Teknoloji Stack:** PHP 8.2+ | MySQL | Bootstrap 5 | Modern JavaScript

## 🚀 Özellikler

### 🌐 Ana Sayfalar
- **Ana Sayfa (index.php)**: Şirket tanıtımı, hizmetler özeti, istatistikler
- **Hakkımızda (about.php)**: Takım tanıtımı, şirket hikayesi, değerler
- **Hizmetler (services.php)**: Detaylı hizmet listesi ve paketleri
- **Portföy (portfolio.php)**: Projeler ve başarı hikayeleri
- **Blog (blog.php)**: Dijital pazarlama makaleleri
- **İletişim (contact.php)**: İletişim formu ve bilgileri
- **Fiyatlandırma (pricing.php)**: Hizmet fiyatları ve paketler
- **SSS (faq.php)**: Sık sorulan sorular

### 🔧 Teknik Özellikler
- **Responsive Tasarım**: Tüm cihazlarda mükemmel görünüm
- **Modern UI/UX**: Glassmorphism ve modern animasyonlar
- **SEO Optimizasyonu**: Arama motorları için optimize edilmiş
- **Hızlı Yükleme**: Optimize edilmiş CSS/JS dosyaları
- **Güvenlik**: CSRF koruması, input validasyonu, güvenli oturum yönetimi
- **API Entegrasyonu**: RESTful API yapısı
- **Admin Paneli**: İçerik yönetim sistemi
- **Çoklu Dil Desteği**: Azərbaycan ve İngilizce

## 📁 Proje Yapısı

```
nextcode/
├── 📄 Ana Sayfalar
│   ├── index.php              # Ana sayfa
│   ├── about.php              # Hakkımızda
│   ├── services.php           # Hizmetler
│   ├── portfolio.php          # Portföy
│   ├── blog.php               # Blog
│   ├── contact.php            # İletişim
│   ├── pricing.php            # Fiyatlandırma
│   └── faq.php                # SSS
│
├── 🔧 Konfigürasyon (config/)
│   ├── database.php           # Veritabanı bağlantısı
│   ├── security.php           # Güvenlik ayarları
│   └── email.php              # E-posta konfigürasyonu
│
├── 🔌 API Endpoints (api/)
│   ├── contact.php            # İletişim API
│   ├── portfolio.php          # Portföy API
│   ├── blog.php               # Blog API
│   └── analytics.php          # Analitik API
│
├── 👨‍💼 Admin Paneli (admin/)
│   ├── dashboard.php          # Admin kontrol paneli
│   ├── content.php            # İçerik yönetimi
│   └── settings.php           # Site ayarları
│
├── 🎨 Stil Dosyaları (css/)
│   ├── modern-styles.css      # Ana stil dosyası
│   ├── responsive.css         # Responsive tasarım
│   └── animations.css         # Animasyonlar
│
├── ⚡ JavaScript (js/)
│   ├── main.js                # Ana JavaScript
│   ├── portfolio.js           # Portföy JS
│   └── contact.js             # İletişim JS
│
├── 🗄️ Veritabanı (database/)
│   └── create_tables.sql      # Tablo oluşturma
│
└── 📚 Dokümantasyon (docs/)
    ├── README.md              # Dokümantasyon merkezi
    ├── VERITABANI_YAPISI.md   # Veritabanı şeması
    └── EMAIL-SYSTEM-README.md # Email sistemi
```

## 🔗 Dosya Bağlantıları ve İlişkileri

### Ana Sayfa (index.php) Bağlantıları:
- **Header**: `includes/header.php` → Tüm meta taglar, CSS/JS dosyaları
- **Footer**: `includes/footer.php` → Alt bilgi, sosyal medya linkleri
- **Veritabanı**: `config/database.php` → Site istatistikleri, dinamik içerik
- **API**: `api-router.php` → İletişim formu, analitik veriler
- **CSS**: `css/modern-styles.css`, `css/animations.css`
- **JS**: `js/main.js`, `js/modern-interactions.js`

### Hakkımızda (about.php) Bağlantıları:
- **Veritabanı**: Takım üyeleri, şirket istatistikleri
- **Görseller**: `images/team/` klasöründen takım fotoğrafları
- **API**: `api/about.php` → Takım ve şirket bilgileri
- **CSS**: `css/modern-styles.css` → Modern kartlar ve animasyonlar

### Hizmetler (services.php) Bağlantıları:
- **Veritabanı**: `services` tablosu → Hizmet detayları
- **API**: `api/services.php` → Hizmet verileri
- **Modal**: Hizmet detay modal'ları için JavaScript
- **CSS**: `css/modern-styles.css` → Hizmet kartları stilleri

### Portföy (portfolio.php) Bağlantıları:
- **Veritabanı**: `portfolio_projects` tablosu → Proje verileri
- **API**: `api/portfolio.php` → Portföy verileri
- **Görseller**: `images/portfolio/` klasörü
- **CSS**: `css/portfolio.css` → Portföy grid ve kart stilleri
- **JS**: `js/portfolio.js` → Filtreleme ve modal işlevleri

### Blog (blog.php) Bağlantıları:
- **Veritabanı**: `blog_posts` tablosu → Blog yazıları
- **API**: `api/blog.php` → Blog verileri
- **Görseller**: `images/blog/` klasörü
- **CSS**: `css/blog-styles.css` → Blog grid stilleri
- **JS**: `js/blog.js` → Arama ve filtreleme

### İletişim (contact.php) Bağlantıları:
- **API**: `api/contact.php` → Form gönderimi
- **Veritabanı**: `contact_messages` tablosu → Mesaj kayıtları
- **E-posta**: `includes/EmailSender.php` → E-posta gönderimi
- **JS**: `js/contact.js` → Form validasyonu

### Admin Paneli Bağlantıları:
- **Giriş**: `admin/login.php` → Kullanıcı doğrulama
- **Dashboard**: `admin/dashboard.php` → Ana kontrol paneli
- **Veritabanı**: Tüm tablolar için CRUD işlemleri
- **API**: Admin işlemleri için API endpoint'leri
- **CSS**: `css/admin.css` → Admin paneli stilleri

## 🛠️ Kurulum

### Gereksinimler
- PHP 8.2 veya üzeri
- MySQL 5.7 veya üzeri
- Composer
- Node.js (opsiyonel - geliştirme için)

### Kurulum Adımları

1. **Projeyi İndirin**
```bash
git clone [repository-url]
cd nextcode
```

2. **Bağımlılıkları Yükleyin**
```bash
composer install
npm install  # Opsiyonel
```

3. **Veritabanını Kurun**
```bash
# Veritabanı oluşturun
mysql -u root -p -e "CREATE DATABASE nextcode_db;"

# Tabloları oluşturun
mysql -u root -p nextcode_db < database/create_tables.sql
```

4. **Ortam Değişkenlerini Ayarlayın**
```bash
cp .env.example .env
# .env dosyasını düzenleyin
```

5. **Dosya İzinlerini Ayarlayın**
```bash
chmod 755 -R .
chmod 777 -R logs/
chmod 777 -R temp/
```

## ⚙️ Konfigürasyon

### Veritabanı Ayarları (.env)
```env
DB_HOST=localhost
DB_NAME=nextcode_db
DB_USER=your_username
DB_PASS=your_password
```

### E-posta Ayarları
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
```

### Güvenlik Ayarları
```env
APP_KEY=your-secret-key-here
SESSION_LIFETIME=120
SECURITY_HEADERS_ENABLED=true
```

## 🔐 Admin Paneli

### Giriş Bilgileri
- **URL**: `/admin/`
- **Varsayılan Kullanıcı**: admin
- **Varsayılan Şifre**: admin123 (İlk kurulumda değiştirin!)

### Admin Paneli Özellikleri
- 📊 Dashboard istatistikleri
- 📝 İçerik yönetimi
- 🖼️ Portföy yönetimi
- 📰 Blog yönetimi
- 💬 Mesaj yönetimi
- ⚙️ Site ayarları
- 👥 Kullanıcı yönetimi

## 🌐 API Endpoints

### İletişim API
```
POST /api/contact.php
- İletişim formu verilerini gönderir
```

### Portföy API
```
GET /api/portfolio.php?action=get_projects
GET /api/portfolio.php?action=get_project&id=1
```

### Blog API
```
GET /api/blog.php
GET /api/blog.php?category=seo&limit=5
```

### Hizmetler API
```
GET /api/services.php
GET /api/services.php?featured=1
```

## 📱 Responsive Tasarım

Proje tüm cihazlarda mükemmel görünüm sağlar:
- 📱 Mobil (320px+)
- 📱 Tablet (768px+)
- 💻 Desktop (1024px+)
- 🖥️ Large Desktop (1440px+)

## 🎨 Tasarım Sistemi

### Renk Paleti
- **Primary**: #667eea → #764ba2 (Gradient)
- **Secondary**: #00d4ff
- **Success**: #48bb78
- **Warning**: #ed8936
- **Danger**: #f56565

### Tipografi
- **Ana Font**: Inter (Google Fonts)
- **Başlık Font**: Inter (Bold)
- **Kod Font**: 'Fira Code', monospace

### Bileşenler
- Modern kartlar (glassmorphism)
- Gradient butonlar
- Animasyonlu geçişler
- Responsive grid sistemi

## 🚀 Performans Optimizasyonu

- **CSS/JS Minification**: Otomatik dosya sıkıştırma
- **Image Optimization**: Görsel optimizasyonu
- **Lazy Loading**: Gecikmeli yükleme
- **Caching**: Akıllı önbellekleme
- **CDN Ready**: CDN entegrasyonu hazır

## 🔒 Güvenlik Özellikleri

- **CSRF Protection**: Cross-site request forgery koruması
- **Input Validation**: Giriş doğrulama
- **SQL Injection Protection**: Prepared statements
- **XSS Protection**: Cross-site scripting koruması
- **Secure Headers**: Güvenlik başlıkları
- **Session Security**: Güvenli oturum yönetimi

## 📊 SEO Özellikleri

- **Meta Tags**: Optimize edilmiş meta etiketler
- **Open Graph**: Sosyal medya paylaşım optimizasyonu
- **Schema Markup**: Yapılandırılmış veri
- **Sitemap**: Otomatik site haritası
- **Robots.txt**: Arama motoru direktifleri

## 🧪 Test ve Geliştirme

### Test Komutları
```bash
# PHP Unit Testleri
composer test

# Code Style Kontrolü
composer cs

# Static Analysis
composer stan

# Güvenlik Kontrolü
composer security
```

### Geliştirme Modu
```bash
# Geliştirme sunucusu
php -S localhost:8000

# CSS/JS Watch
npm run dev
```

## 📈 Analitik ve İzleme

- **Google Analytics**: Entegre analitik
- **Performance Monitoring**: Performans izleme
- **Error Tracking**: Hata takibi
- **User Behavior**: Kullanıcı davranış analizi

## 🔄 Güncelleme ve Bakım

### Otomatik Yedekleme
```bash
# Yedekleme scripti
./scripts/backup.sh

# Geri yükleme
./scripts/restore.sh backup_file.sql
```

### Güncelleme Kontrolü
```bash
# Bağımlılık güncellemeleri
composer update
npm update
```

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📞 Destek

- **E-posta**: info@nextcodegroup.com
- **Telefon**: +380 97 258 00 00
- **Website**: https://nextcode.az
- **Dokümantasyon**: `/docs/` klasörü

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için `LICENSE` dosyasına bakın.

## 👥 Geliştirici Ekibi

- **NextCode Group** - Ana geliştirici
- **E-posta**: xeyalcemilli9032@gmail.com

## 🎯 Gelecek Planları

- [ ] Çoklu dil desteği genişletme
- [ ] PWA (Progressive Web App) özellikleri
- [ ] AI destekli içerik önerileri
- [ ] Gelişmiş analitik dashboard
- [ ] Mobil uygulama entegrasyonu
- [ ] E-ticaret modülü
- [ ] CRM entegrasyonu

---

**NextCode Group** - Digital Marketing Agency  
*Rəqəmsal Dünyada Liderlik Edin* 🚀
