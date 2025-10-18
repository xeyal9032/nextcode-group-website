# WEB SİTESİ KAPSAMLI DOKÜMANTASYONU

## 📋 İÇİNDEKİLER
1. [Genel Bakış](#genel-bakış)
2. [Dosya Yapısı](#dosya-yapısı)
3. [Sayfalar ve Özellikleri](#sayfalar-ve-özellikleri)
4. [JavaScript Fonksiyonları](#javascript-fonksiyonları)
5. [CSS Stilleri](#css-stilleri)
6. [Veritabanı Yapısı](#veritabanı-yapısı)
7. [API Endpoints](#api-endpoints)
8. [Güvenlik Özellikleri](#güvenlik-özellikleri)
9. [Performans Optimizasyonları](#performans-optimizasyonları)
10. [Bakım ve Güncelleme](#bakım-ve-güncelleme)

---

## 🌐 GENEL BAKIŞ

### Proje Bilgileri
- **Proje Adı:** Kurumsal Web Sitesi
- **Teknolojiler:** HTML5, CSS3, JavaScript, Bootstrap 5, PHP, MySQL
- **Responsive:** Evet (Mobile-First)
- **Browser Desteği:** Chrome, Firefox, Safari, Edge
- **Son Güncelleme:** $(Get-Date -Format 'dd.MM.yyyy')

### Özellikler
- ✅ Responsive tasarım
- ✅ SEO optimizasyonu
- ✅ Google Analytics entegrasyonu
- ✅ İletişim formu
- ✅ Portfolio galerisi
- ✅ Blog sistemi
- ✅ Admin paneli
- ✅ Çoklu dil desteği hazırlığı

---

## 📁 DOSYA YAPISI

```
ftp-upload/
├── 📄 index.html              # Ana sayfa
├── 📄 about.html              # Hakkımızda sayfası
├── 📄 services.html           # Hizmetler sayfası
├── 📄 portfolio.html          # Portfolio sayfası
├── 📄 blog.html               # Blog ana sayfası
├── 📄 blog-post.html          # Blog yazı detayı
├── 📄 contact.html            # İletişim sayfası
├── 📄 pricing.html            # Fiyatlandırma sayfası
├── 📄 faq.html                # SSS sayfası
├── 📄 .htaccess               # Apache yapılandırması
├── 📄 database.sql            # Veritabanı yapısı
├── 📄 README.md               # Proje açıklaması
│
├── 📁 css/
│   ├── 📄 styles.css          # Ana stil dosyası
│   ├── 📄 bootstrap.min.css   # Bootstrap framework
│   └── 📄 admin.css           # Admin panel stilleri
│
├── 📁 js/
│   ├── 📄 main.js             # Ana JavaScript dosyası
│   ├── 📄 contact.js          # İletişim formu JS
│   ├── 📄 portfolio.js        # Portfolio JS
│   ├── 📄 pricing.js          # Fiyatlandırma JS
│   ├── 📄 blog.js             # Blog JS
│   ├── 📄 faq.js              # SSS JS
│   ├── 📄 about.js            # Hakkımızda JS
│   ├── 📄 services.js         # Hizmetler JS
│   ├── 📄 analytics.js        # Google Analytics
│   └── 📄 admin.js            # Admin panel JS
│
├── 📁 images/
│   ├── 📁 portfolio/          # Portfolio görselleri
│   ├── 📁 blog/               # Blog görselleri
│   ├── 📁 team/               # Ekip fotoğrafları
│   └── 📁 icons/              # İkonlar
│
├── 📁 admin/
│   ├── 📄 login.php           # Admin girişi
│   └── 📄 blog.php            # Blog yönetimi
│
├── 📁 api/
│   └── (API dosyaları)
│
├── 📁 config/
│   └── (Yapılandırma dosyaları)
│
├── 📁 backup/
│   ├── 📄 backup-script.ps1   # Otomatik yedekleme
│   └── 📄 run-backup.bat      # Yedekleme çalıştırıcı
│
└── 📁 documentation/
    └── 📄 WEB-SITESI-DOKUMANTASYONU.md
```

---

## 📄 SAYFALAR VE ÖZELLİKLERİ

### 🏠 Ana Sayfa (index.html)
**Özellikler:**
- Hero bölümü (ana banner)
- Hizmetler özeti
- Portfolio örnekleri
- Müşteri yorumları
- İletişim CTA butonları
- Sosyal medya linkleri

**JavaScript Fonksiyonları:**
- `initModernAnimations()` - Sayfa animasyonları
- `initSmoothScrolling()` - Yumuşak kaydırma
- `initScrollAnimations()` - Scroll animasyonları
- `initHeaderScrollEffect()` - Header efektleri

### 📖 Hakkımızda (about.html)
**Özellikler:**
- Şirket hikayesi
- Ekip üyeleri
- Misyon ve vizyon
- Değerlerimiz
- Sertifikalar

### 🛠️ Hizmetler (services.html)
**Özellikler:**
- Hizmet kategorileri
- Detaylı açıklamalar
- Fiyat bilgileri
- Paket karşılaştırması
- Teklif alma formu

### 💼 Portfolio (portfolio.html)
**Özellikler:**
- 15+ gerçek proje
- Kategori filtreleme
- Proje detay modalları
- Müşteri testimonialları
- Teknoloji etiketleri

**JavaScript Fonksiyonları:**
- `initPortfolio()` - Portfolio başlatma
- `filterProjects(category)` - Proje filtreleme
- `openProjectModal(projectId)` - Proje detayı
- `closeModal()` - Modal kapatma

### 📝 Blog (blog.html)
**Özellikler:**
- Blog yazıları listesi
- Kategori filtreleme
- Arama fonksiyonu
- Sayfalama
- Sosyal paylaşım

### 📞 İletişim (contact.html)
**Özellikler:**
- İletişim formu
- Google Maps entegrasyonu
- İletişim bilgileri
- Sosyal medya linkleri
- Canlı sohbet widget'ı
- SSS bölümü

**JavaScript Fonksiyonları:**
- `validateContactForm()` - Form doğrulama
- `submitContactForm()` - Form gönderimi
- `setupMap()` - Harita kurulumu
- `setupLiveChat()` - Canlı sohbet

### 💰 Fiyatlandırma (pricing.html)
**Özellikler:**
- Paket karşılaştırması
- Aylık/yıllık fiyatlar
- Özellik listesi
- Paket seçimi modalları
- Özel paket hesaplayıcı

**JavaScript Fonksiyonları:**
- `togglePricing(isYearly)` - Fiyat değiştirme
- `openPackageModal(packageType)` - Paket modalı
- `calculateCustomPackage()` - Özel paket hesaplama

### ❓ SSS (faq.html)
**Özellikler:**
- Akordeon yapısı
- Kategori filtreleme
- Arama fonksiyonu
- İletişim linkleri

---

## ⚙️ JAVASCRIPT FONKSİYONLARI

### 🔧 Ana Fonksiyonlar (main.js)

#### Sayfa Başlatma
```javascript
// Sayfa yüklendiğinde çalışan ana fonksiyon
function initializeWebsite() {
    initModernAnimations();
    initSmoothScrolling();
    initScrollAnimations();
    initContactForm();
    initHeaderScrollEffect();
    initPortfolioEffects();
    initModernInteractions();
    initParallaxEffects();
}
```

#### Animasyon Fonksiyonları
- `initModernAnimations()` - Modern animasyonlar
- `initScrollAnimations()` - Scroll animasyonları
- `initParallaxEffects()` - Paralaks efektleri
- `initHeaderScrollEffect()` - Header scroll efekti

#### Form Fonksiyonları
- `validateContactForm(formData)` - Form doğrulama
- `submitContactForm(formData)` - Form gönderimi
- `showAlert(message, type)` - Bildirim gösterme

#### Yardımcı Fonksiyonlar
- `debounce(func, wait)` - Fonksiyon geciktirme
- `throttle(func, limit)` - Fonksiyon sınırlama
- `isElementInViewport(element)` - Element görünürlük kontrolü

### 📞 İletişim Fonksiyonları (contact.js)

#### Form İşlemleri
- `setupFormValidation()` - Form doğrulama kurulumu
- `validateField(field)` - Alan doğrulama
- `handleFormSubmission()` - Form gönderim işlemi

#### Harita İşlemleri
- `setupMap()` - Google Maps kurulumu
- `loadGoogleMap()` - Harita yükleme
- `openGoogleMaps()` - Google Maps açma
- `getDirections()` - Yol tarifi alma

#### Canlı Sohbet
- `setupLiveChat()` - Sohbet kurulumu
- `initializeChatInterface()` - Sohbet arayüzü
- `sendChatMessage(message)` - Mesaj gönderme
- `generateBotResponse(message)` - Bot yanıtı

### 💼 Portfolio Fonksiyonları (portfolio.js)

#### Proje İşlemleri
- `initPortfolio()` - Portfolio başlatma
- `filterProjects(category)` - Proje filtreleme
- `searchProjects(query)` - Proje arama
- `sortProjects(criteria)` - Proje sıralama

#### Modal İşlemleri
- `openProjectModal(projectId)` - Proje detayı açma
- `closeModal()` - Modal kapatma
- `navigateModal(direction)` - Modal navigasyonu

### 💰 Fiyatlandırma Fonksiyonları (pricing.js)

#### Paket İşlemleri
- `togglePricing(isYearly)` - Fiyat değiştirme
- `openPackageModal(packageType)` - Paket modalı açma
- `selectPackage(packageData)` - Paket seçimi
- `calculateCustomPackage()` - Özel paket hesaplama

---

## 🎨 CSS STİLLERİ

### 📱 Responsive Tasarım
```css
/* Mobile First Approach */
@media (min-width: 576px) { /* Small devices */ }
@media (min-width: 768px) { /* Medium devices */ }
@media (min-width: 992px) { /* Large devices */ }
@media (min-width: 1200px) { /* Extra large devices */ }
```

### 🎭 Animasyonlar
- Fade in/out efektleri
- Slide animasyonları
- Hover efektleri
- Loading animasyonları
- Paralaks efektleri

### 🎨 Renk Paleti
```css
:root {
    --primary-color: #007bff;
    --secondary-color: #6c757d;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
}
```

---

## 🗄️ VERİTABANI YAPISI

### 📊 Tablolar

#### `contacts` Tablosu
```sql
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    company VARCHAR(100),
    service VARCHAR(50),
    budget VARCHAR(50),
    message TEXT,
    privacy_accepted BOOLEAN DEFAULT FALSE,
    newsletter_subscription BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `blog_posts` Tablosu
```sql
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category VARCHAR(50),
    tags TEXT,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## 🔌 API ENDPOINTS

### 📝 İletişim API'leri
- `POST /api/contact` - İletişim formu gönderimi
- `GET /api/contact/{id}` - İletişim detayı
- `GET /api/contacts` - İletişim listesi (Admin)

### 📖 Blog API'leri
- `GET /api/blog/posts` - Blog yazıları listesi
- `GET /api/blog/post/{slug}` - Blog yazısı detayı
- `POST /api/blog/post` - Yeni blog yazısı (Admin)
- `PUT /api/blog/post/{id}` - Blog yazısı güncelleme (Admin)
- `DELETE /api/blog/post/{id}` - Blog yazısı silme (Admin)

---

## 🔒 GÜVENLİK ÖZELLİKLERİ

### 🛡️ Güvenlik Önlemleri
- CSRF koruması
- XSS koruması
- SQL Injection koruması
- Rate limiting
- Input validation
- Output encoding

### 🔐 .htaccess Güvenlik Kuralları
```apache
# Güvenlik başlıkları
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# Dosya erişim kısıtlamaları
<Files "*.sql">
    Order allow,deny
    Deny from all
</Files>
```

---

## ⚡ PERFORMANS OPTİMİZASYONLARI

### 🚀 Optimizasyon Teknikleri
- CSS/JS minification
- Image optimization
- Lazy loading
- Caching strategies
- CDN kullanımı
- GZIP compression

### 📊 Performans Metrikleri
- Page Load Time: < 3 saniye
- First Contentful Paint: < 1.5 saniye
- Largest Contentful Paint: < 2.5 saniye
- Cumulative Layout Shift: < 0.1

---

## 🔧 BAKIM VE GÜNCELLEME

### 📅 Düzenli Bakım Görevleri
- [ ] Haftalık yedekleme kontrolü
- [ ] Aylık güvenlik güncellemeleri
- [ ] Üç aylık performans analizi
- [ ] Altı aylık içerik güncellemesi
- [ ] Yıllık teknoloji stack değerlendirmesi

### 🔄 Güncelleme Prosedürü
1. Mevcut sitenin yedeğini al
2. Test ortamında değişiklikleri uygula
3. Fonksiyonalite testlerini çalıştır
4. Canlı ortama deploy et
5. Post-deployment testleri yap
6. Changelog'u güncelle

---

## 📞 DESTEK VE İLETİŞİM

### 🆘 Acil Durum Kişileri
- **Geliştirici:** [İletişim Bilgisi]
- **Sistem Yöneticisi:** [İletişim Bilgisi]
- **Proje Yöneticisi:** [İletişim Bilgisi]

### 📚 Faydalı Kaynaklar
- [Bootstrap Dokümantasyonu](https://getbootstrap.com/docs/)
- [JavaScript MDN](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
- [CSS Grid Guide](https://css-tricks.com/snippets/css/complete-guide-grid/)
- [PHP Dokümantasyonu](https://www.php.net/docs.php)

---

**Son Güncelleme:** $(Get-Date -Format 'dd.MM.yyyy HH:mm')
**Versiyon:** 1.0.0
**Hazırlayan:** Web Geliştirme Ekibi