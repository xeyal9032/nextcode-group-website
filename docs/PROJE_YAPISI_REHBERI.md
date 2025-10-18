# 🏗️ NextCode Group - Proje Yapısı Rehberi

## 📋 Genel Bakış
Bu rehber, NextCode Group web projesinin kod yapısını, dosya organizasyonunu ve bağlantılarını anlamak için tasarlanmıştır.

---

## 🗂️ Ana Klasör Yapısı

### 📁 **Root Dizin (/nextcode/)**
```
nextcode/
├── index.php              # Ana sayfa
├── contact.php            # İletişim sayfası
├── about.php              # Hakkımızda sayfası
├── services.php           # Hizmetler sayfası
├── portfolio.php          # Portfolio sayfası
├── blog.php               # Blog ana sayfası
├── pricing.php            # Fiyatlandırma sayfası
├── faq.php                # SSS sayfası
└── api-router.php         # API yönlendirici
```

### 📁 **API Dizin (/api/)**
```
api/
├── contact.php            # İletişim formu API
├── blog.php               # Blog API
├── portfolio.php          # Portfolio API
├── analytics.php          # Analytics API
├── contact-info.php       # İletişim bilgileri API
└── webhook-manager.php    # Webhook yönetimi
```

### 📁 **Konfigürasyon (/config/)**
```
config/
├── database.php           # Veritabanı bağlantısı
├── email.php              # Email ayarları
├── security.php           # Güvenlik ayarları
├── analytics-config.php   # Analytics konfigürasyonu
└── cdn-config.php         # CDN ayarları
```

### 📁 **Includes (/includes/)**
```
includes/
├── header.php             # Sayfa başlığı
├── footer.php             # Sayfa alt bilgisi
├── page_functions.php     # Sayfa fonksiyonları
├── content_helper.php     # İçerik yardımcıları
├── error_handler.php      # Hata yönetimi
└── EmailSender.php        # Email gönderim sınıfı
```

### 📁 **CSS (/css/)**
```
css/
├── styles.css             # Ana stil dosyası
├── modern-styles.css      # Modern stiller
├── theme-variables.css    # Tema değişkenleri
├── responsive.css         # Responsive tasarım
├── readability-enhancements.css # Okunabilirlik iyileştirmeleri
└── inter-font.css         # Font tanımları
```

### 📁 **JavaScript (/js/)**
```
js/
├── critical.js            # Kritik JavaScript
├── theme.js               # Tema yönetimi
├── contact-simple.js      # İletişim formu
├── analytics.js           # Analytics tracking
└── error-handler.js       # JavaScript hata yönetimi
```

---

## 🔗 Dosya Bağlantıları ve İlişkiler

### 📄 **Ana Sayfa Akışı**
```
index.php
├── includes/header.php
├── css/styles.css
├── css/modern-styles.css
├── js/critical.js
└── includes/footer.php
```

### 📄 **İletişim Formu Akışı**
```
contact.php
├── config/database.php
├── config/security.php
├── includes/header.php
├── js/contact-simple.js
├── contact-handler.php (POST)
├── api/contact.php (AJAX)
└── includes/EmailSender.php
```

### 📄 **Blog Sistemi Akışı**
```
blog.php
├── api/blog.php
├── config/database.php
├── css/blog-dark-mode.css
└── js/blog-interactions.js
```

---

## 🎯 Kritik Dosyalar ve Sorumlulukları

### 🔧 **Veritabanı Bağlantısı**
- **Dosya:** `config/database.php`
- **Sorumluluk:** PDO bağlantısı, veritabanı konfigürasyonu
- **Kullanan:** Tüm PHP sayfaları, API'ler

### 📧 **Email Sistemi**
- **Dosya:** `config/email.php` + `includes/EmailSender.php`
- **Sorumluluk:** Email gönderimi, SMTP konfigürasyonu
- **Kullanan:** `contact-handler.php`, `api/contact.php`

### 🎨 **Tema Sistemi**
- **Dosya:** `css/theme-variables.css` + `js/theme.js`
- **Sorumluluk:** Light/Dark mode, tema değişkenleri
- **Kullanan:** Tüm sayfalar

### 🔒 **Güvenlik**
- **Dosya:** `config/security.php` + `includes/error_handler.php`
- **Sorumluluk:** CSRF koruması, güvenlik başlıkları
- **Kullanan:** Tüm form işlemleri

---

## 🚨 Hata Ayıklama ve Sorun Giderme

### 📊 **Log Dosyaları**
```
logs/
├── error.log              # PHP hataları
├── email.log              # Email gönderim logları
└── enhanced-backup.log    # Backup logları
```

### 🧪 **Test Dosyaları**
```
test-email-system.php      # Email sistemi testi
test-theme-toggle.php      # Tema sistemi testi
test-readability.php       # Okunabilirlik testi
comprehensive-test.php     # Kapsamlı sistem testi
```

### 🔍 **Debug Araçları**
- **Dosya:** `js/error-handler.js`
- **Sorumluluk:** JavaScript hata yakalama
- **Kullanım:** Tüm sayfalarda otomatik aktif

---

## 📈 Performans ve Optimizasyon

### ⚡ **Kritik CSS**
- **Dosya:** `css/critical.css`
- **Sorumluluk:** Above-the-fold stil optimizasyonu

### 🖼️ **CDN Konfigürasyonu**
- **Dosya:** `config/cdn-config.php`
- **Sorumluluk:** Statik dosya optimizasyonu

### 📊 **Analytics**
- **Dosya:** `api/analytics.php` + `js/analytics.js`
- **Sorumluluk:** Kullanıcı davranış analizi

---

## 🔄 Güncelleme ve Bakım

### 💾 **Backup Sistemi**
```
backup/
├── auto-backup.php        # Otomatik backup
├── enhanced-backup.php    # Gelişmiş backup
└── dashboard.php          # Backup yönetimi
```

### 🔧 **API Yönetimi**
- **Dosya:** `api-router.php`
- **Sorumluluk:** API endpoint yönlendirmesi

---

## 📚 Dokümantasyon

### 📖 **Mevcut Dokümantasyonlar**
- `README.md` - Genel proje bilgileri
- `EMAIL-SYSTEM-README.md` - Email sistemi rehberi
- `KAPSAMLI_PROJE_ANALIZ_RAPORU.md` - Detaylı analiz
- `VERITABANI_YAPISI.md` - Veritabanı şeması

---

## 🎯 Geliştirme İpuçları

### ✅ **Yeni Özellik Ekleme**
1. İlgili API dosyasını güncelleyin (`api/`)
2. Konfigürasyon ekleyin (`config/`)
3. Frontend entegrasyonu (`css/`, `js/`)
4. Test dosyası oluşturun
5. Dokümantasyonu güncelleyin

### 🐛 **Hata Ayıklama**
1. Log dosyalarını kontrol edin (`logs/`)
2. Test dosyalarını kullanın
3. Browser Developer Tools
4. PHP error reporting aktifleştirin

### 🔒 **Güvenlik**
1. CSRF token kontrolü
2. Input validation
3. SQL injection koruması
4. XSS koruması

---

**Son Güncelleme:** <?php echo date('d.m.Y H:i:s'); ?>
**Versiyon:** 1.0
**Durum:** ✅ Aktif ve Güncel
