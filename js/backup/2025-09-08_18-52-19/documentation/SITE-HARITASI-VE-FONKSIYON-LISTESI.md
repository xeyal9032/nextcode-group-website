# 🗺️ SİTE HARİTASI VE FONKSİYON LİSTESİ

## 📋 İÇİNDEKİLER
1. [Site Haritası](#site-haritası)
2. [Sayfa Bazlı Fonksiyon Listesi](#sayfa-bazlı-fonksiyon-listesi)
3. [JavaScript Fonksiyon Referansları](#javascript-fonksiyon-referansları)
4. [CSS Class Referansları](#css-class-referansları)
5. [API Endpoint Listesi](#api-endpoint-listesi)
6. [Hızlı Erişim Linkleri](#hızlı-erişim-linkleri)

---

## 🗺️ SİTE HARİTASI

### 📁 Ana Dizin Yapısı
```
🌐 Web Sitesi (ftp-upload/)
├── 🏠 Ana Sayfa (index.html)
├── 📖 Hakkımızda (about.html)
├── 🛠️ Hizmetler (services.html)
├── 💼 Portfolio (portfolio.html)
├── 📝 Blog (blog.html)
│   └── 📄 Blog Yazı Detayı (blog-post.html)
├── 📞 İletişim (contact.html)
├── 💰 Fiyatlandırma (pricing.html)
├── ❓ SSS (faq.html)
├── 🔧 Admin Paneli (admin/)
│   ├── 🔐 Giriş (login.php)
│   └── 📝 Blog Yönetimi (blog.php)
├── 🎨 Stil Dosyaları (css/)
├── ⚙️ JavaScript Dosyaları (js/)
├── 🖼️ Görseller (images/)
├── 🔌 API (api/)
├── ⚙️ Yapılandırma (config/)
├── 💾 Yedekleme (backup/)
└── 📚 Dokümantasyon (documentation/)
```

### 🔗 Sayfa Bağlantıları ve Navigasyon

#### Ana Navigasyon Menüsü
- **Ana Sayfa** → `index.html`
- **Hakkımızda** → `about.html`
- **Hizmetler** → `services.html`
- **Portfolio** → `portfolio.html`
- **Blog** → `blog.html`
- **İletişim** → `contact.html`
- **Fiyatlandırma** → `pricing.html`
- **SSS** → `faq.html`

#### Alt Sayfalar ve Bağlantılar
- **Blog Yazı Detayı** → `blog-post.html?id={post_id}`
- **Portfolio Proje Detayı** → Modal popup (JavaScript)
- **Hizmet Detayları** → Modal popup (JavaScript)
- **Paket Detayları** → Modal popup (JavaScript)

#### Admin Sayfaları
- **Admin Girişi** → `admin/login.php`
- **Blog Yönetimi** → `admin/blog.php`

---

## 📄 SAYFA BAZLI FONKSİYON LİSTESİ

### 🏠 Ana Sayfa (index.html)

#### 🎯 Ana Özellikler
- ✅ Hero bölümü (ana banner)
- ✅ Hizmetler özeti kartları
- ✅ Portfolio örnekleri
- ✅ Müşteri yorumları slider'ı
- ✅ İletişim CTA butonları
- ✅ Sosyal medya linkleri
- ✅ Scroll animasyonları
- ✅ Paralaks efektleri

#### ⚙️ JavaScript Fonksiyonları
- `initModernAnimations()` - Modern animasyonlar
- `initSmoothScrolling()` - Yumuşak kaydırma
- `initScrollAnimations()` - Scroll animasyonları
- `initHeaderScrollEffect()` - Header efektleri
- `initPortfolioEffects()` - Portfolio efektleri
- `initModernInteractions()` - Modern etkileşimler
- `initParallaxEffects()` - Paralaks efektleri

#### 🎨 CSS Sınıfları
- `.hero-section` - Ana banner bölümü
- `.services-preview` - Hizmetler önizleme
- `.portfolio-preview` - Portfolio önizleme
- `.testimonials-slider` - Müşteri yorumları
- `.cta-buttons` - Eylem çağrısı butonları
- `.social-links` - Sosyal medya linkleri

---

### 📖 Hakkımızda (about.html)

#### 🎯 Ana Özellikler
- ✅ Şirket hikayesi
- ✅ Ekip üyeleri kartları
- ✅ Misyon ve vizyon
- ✅ Değerlerimiz listesi
- ✅ Sertifikalar galerisi
- ✅ Sayaç animasyonları
- ✅ Zaman çizelgesi

#### ⚙️ JavaScript Fonksiyonları
- `initAbout()` - Hakkımızda başlatma
- `initTeamCarousel()` - Ekip carousel'ı
- `initCounters()` - Sayaç animasyonları
- `initTimeline()` - Zaman çizelgesi
- `showTeamMember(memberId)` - Ekip üyesi detayı

#### 🎨 CSS Sınıfları
- `.company-story` - Şirket hikayesi
- `.team-members` - Ekip üyeleri
- `.mission-vision` - Misyon vizyon
- `.values-list` - Değerler listesi
- `.certificates` - Sertifikalar
- `.counter-section` - Sayaç bölümü
- `.timeline` - Zaman çizelgesi

---

### 🛠️ Hizmetler (services.html)

#### 🎯 Ana Özellikler
- ✅ Hizmet kategorileri
- ✅ Detaylı açıklamalar
- ✅ Fiyat bilgileri
- ✅ Paket karşılaştırması
- ✅ Teklif alma formu
- ✅ Hizmet detay modalları

#### ⚙️ JavaScript Fonksiyonları
- `initServices()` - Hizmetler başlatma
- `toggleServiceDetails(serviceId)` - Hizmet detayları
- `openQuoteModal(serviceType)` - Teklif modalı
- `calculateServicePrice(options)` - Hizmet fiyat hesaplama
- `submitQuoteRequest(formData)` - Teklif isteği gönderimi

#### 🎨 CSS Sınıfları
- `.service-categories` - Hizmet kategorileri
- `.service-details` - Hizmet detayları
- `.pricing-table` - Fiyat tablosu
- `.package-comparison` - Paket karşılaştırması
- `.quote-form` - Teklif formu
- `.service-modal` - Hizmet modalı

---

### 💼 Portfolio (portfolio.html)

#### 🎯 Ana Özellikler
- ✅ 15 gerçek proje
- ✅ Kategori filtreleme
- ✅ Proje detay modalları
- ✅ Müşteri testimonialları
- ✅ Teknoloji etiketleri
- ✅ Responsive galeri
- ✅ Proje arama
- ✅ Proje sıralama

#### ⚙️ JavaScript Fonksiyonları
- `initPortfolio()` - Portfolio başlatma
- `filterProjects(category)` - Proje filtreleme
- `searchProjects(query)` - Proje arama
- `sortProjects(criteria)` - Proje sıralama
- `openProjectModal(projectId)` - Proje detayı açma
- `closeModal()` - Modal kapatma
- `navigateModal(direction)` - Modal navigasyonu
- `loadProjectDetails(projectId)` - Proje detayları yükleme

#### 🎨 CSS Sınıfları
- `.portfolio-grid` - Portfolio ızgarası
- `.project-card` - Proje kartı
- `.filter-buttons` - Filtre butonları
- `.project-modal` - Proje modalı
- `.technology-tags` - Teknoloji etiketleri
- `.testimonial-section` - Testimonial bölümü
- `.search-bar` - Arama çubuğu

---

### 📝 Blog (blog.html)

#### 🎯 Ana Özellikler
- ✅ Blog yazıları listesi
- ✅ Kategori filtreleme
- ✅ Arama fonksiyonu
- ✅ Sayfalama sistemi
- ✅ Sosyal paylaşım
- ✅ Yazı detay sayfası
- ✅ Yorum sistemi

#### ⚙️ JavaScript Fonksiyonları
- `initBlog()` - Blog başlatma
- `filterBlogPosts(category)` - Blog filtreleme
- `searchBlogPosts(query)` - Blog arama
- `loadMorePosts()` - Daha fazla yazı yükleme
- `shareBlogPost(postId, platform)` - Blog paylaşımı
- `loadBlogPost(postId)` - Blog yazısı yükleme

#### 🎨 CSS Sınıfları
- `.blog-grid` - Blog ızgarası
- `.blog-card` - Blog kartı
- `.blog-filters` - Blog filtreleri
- `.pagination` - Sayfalama
- `.share-buttons` - Paylaşım butonları
- `.blog-post-detail` - Blog yazı detayı

---

### 📞 İletişim (contact.html)

#### 🎯 Ana Özellikler
- ✅ Kapsamlı iletişim formu
- ✅ Google Maps entegrasyonu
- ✅ İletişim bilgileri
- ✅ Sosyal medya linkleri
- ✅ Canlı sohbet widget'ı
- ✅ SSS bölümü
- ✅ Form doğrulama

#### ⚙️ JavaScript Fonksiyonları
- `validateContactForm(formData)` - Form doğrulama
- `submitContactForm(formData)` - Form gönderimi
- `setupMap()` - Google Maps kurulumu
- `loadGoogleMap()` - Harita yükleme
- `openGoogleMaps()` - Google Maps açma
- `getDirections()` - Yol tarifi alma
- `setupLiveChat()` - Canlı sohbet kurulumu
- `initializeChatInterface()` - Sohbet arayüzü
- `sendChatMessage(message)` - Mesaj gönderme
- `generateBotResponse(message)` - Bot yanıtı

#### 🎨 CSS Sınıfları
- `.contact-form` - İletişim formu
- `.map-container` - Harita konteyneri
- `.contact-info` - İletişim bilgileri
- `.live-chat` - Canlı sohbet
- `.form-validation` - Form doğrulama
- `.success-message` - Başarı mesajı

---

### 💰 Fiyatlandırma (pricing.html)

#### 🎯 Ana Özellikler
- ✅ Paket karşılaştırma tablosu
- ✅ Aylık/yıllık fiyat değiştirici
- ✅ Özellik listesi
- ✅ Paket seçimi modalları
- ✅ Özel paket hesaplayıcı
- ✅ Fiyat animasyonları

#### ⚙️ JavaScript Fonksiyonları
- `togglePricing(isYearly)` - Fiyat değiştirme
- `openPackageModal(packageType)` - Paket modalı açma
- `selectPackage(packageData)` - Paket seçimi
- `calculateCustomPackage(options)` - Özel paket hesaplama
- `animatePriceChange()` - Fiyat değişim animasyonu
- `showPackageDetails(packageId)` - Paket detayları

#### 🎨 CSS Sınıfları
- `.pricing-table` - Fiyatlandırma tablosu
- `.price-toggle` - Fiyat değiştirici
- `.package-card` - Paket kartı
- `.feature-list` - Özellik listesi
- `.package-modal` - Paket modalı
- `.custom-calculator` - Özel hesaplayıcı

---

### ❓ SSS (faq.html)

#### 🎯 Ana Özellikler
- ✅ Akordeon yapısı
- ✅ Kategori filtreleme
- ✅ Arama fonksiyonu
- ✅ İletişim linkleri
- ✅ Genişletilmiş yanıtlar

#### ⚙️ JavaScript Fonksiyonları
- `initFAQ()` - SSS başlatma
- `toggleFAQItem(itemId)` - SSS öğesi açma/kapama
- `searchFAQ(query)` - SSS arama
- `filterFAQByCategory(category)` - Kategori filtreleme
- `expandAllFAQ()` - Tümünü genişlet
- `collapseAllFAQ()` - Tümünü daralt

#### 🎨 CSS Sınıfları
- `.faq-accordion` - SSS akordeon
- `.faq-item` - SSS öğesi
- `.faq-filters` - SSS filtreleri
- `.faq-search` - SSS arama
- `.faq-category` - SSS kategorisi

---

## ⚙️ JAVASCRIPT FONKSİYON REFERANSLARI

### 🔧 Ana Fonksiyonlar (main.js)

#### Sayfa Başlatma Fonksiyonları
```javascript
// Ana başlatma fonksiyonu
initializeWebsite()

// Alt başlatma fonksiyonları
initModernAnimations()
initSmoothScrolling()
initScrollAnimations()
initContactForm()
initHeaderScrollEffect()
initPortfolioEffects()
initModernInteractions()
initParallaxEffects()
```

#### Yardımcı Fonksiyonlar
```javascript
// Performans optimizasyonu
debounce(func, wait)
throttle(func, limit)

// DOM yardımcıları
isElementInViewport(element)
getElementOffset(element)
scrollToElement(element, duration)

// Animasyon yardımcıları
fadeIn(element, duration)
fadeOut(element, duration)
slideUp(element, duration)
slideDown(element, duration)
```

### 📞 İletişim Fonksiyonları (contact.js)

#### Form İşlemleri
```javascript
// Form doğrulama
validateContactForm(formData)
validateField(field)
validateEmail(email)
validatePhone(phone)

// Form gönderimi
submitContactForm(formData)
handleFormSubmission()
showFormSuccess()
showFormError(message)
```

#### Harita İşlemleri
```javascript
// Google Maps
setupMap()
loadGoogleMap()
openGoogleMaps()
getDirections()
addMapMarker(lat, lng, title)
```

#### Canlı Sohbet
```javascript
// Sohbet sistemi
setupLiveChat()
initializeChatInterface()
sendChatMessage(message)
generateBotResponse(message)
showChatNotification()
```

### 💼 Portfolio Fonksiyonları (portfolio.js)

#### Proje İşlemleri
```javascript
// Portfolio yönetimi
initPortfolio()
filterProjects(category)
searchProjects(query)
sortProjects(criteria)
loadProjects()

// Modal işlemleri
openProjectModal(projectId)
closeModal()
navigateModal(direction)
loadProjectDetails(projectId)
```

### 💰 Fiyatlandırma Fonksiyonları (pricing.js)

#### Paket İşlemleri
```javascript
// Fiyatlandırma
togglePricing(isYearly)
openPackageModal(packageType)
selectPackage(packageData)
calculateCustomPackage(options)
animatePriceChange()
```

---

## 🎨 CSS CLASS REFERANSLARI

### 🎯 Ana Layout Sınıfları
```css
/* Container sınıfları */
.container-fluid
.container
.row
.col-*

/* Flexbox yardımcıları */
.d-flex
.justify-content-*
.align-items-*
.flex-direction-*

/* Spacing yardımcıları */
.m-* (margin)
.p-* (padding)
.mt-*, .mb-*, .ml-*, .mr-* (yönlü margin)
.pt-*, .pb-*, .pl-*, .pr-* (yönlü padding)
```

### 🎨 Özel Tasarım Sınıfları
```css
/* Animasyon sınıfları */
.fade-in
.slide-up
.slide-down
.bounce-in
.zoom-in

/* Hover efektleri */
.hover-scale
.hover-shadow
.hover-brightness
.hover-rotate

/* Buton stilleri */
.btn-primary-custom
.btn-secondary-custom
.btn-outline-custom
.btn-gradient

/* Kart stilleri */
.card-modern
.card-shadow
.card-hover
.card-gradient
```

### 📱 Responsive Sınıfları
```css
/* Görünürlük kontrolü */
.d-none
.d-block
.d-sm-none, .d-md-block, vb.

/* Text hizalama */
.text-left, .text-center, .text-right
.text-sm-left, .text-md-center, vb.

/* Boyut kontrolü */
.w-25, .w-50, .w-75, .w-100
.h-25, .h-50, .h-75, .h-100
```

---

## 🔌 API ENDPOINT LİSTESİ

### 📝 İletişim API'leri
```
POST   /api/contact              # İletişim formu gönderimi
GET    /api/contact/{id}         # İletişim detayı
GET    /api/contacts             # İletişim listesi (Admin)
DELETE /api/contact/{id}         # İletişim silme (Admin)
PUT    /api/contact/{id}/status  # İletişim durumu güncelleme
```

### 📖 Blog API'leri
```
GET    /api/blog/posts           # Blog yazıları listesi
GET    /api/blog/post/{slug}     # Blog yazısı detayı
POST   /api/blog/post            # Yeni blog yazısı (Admin)
PUT    /api/blog/post/{id}       # Blog yazısı güncelleme (Admin)
DELETE /api/blog/post/{id}       # Blog yazısı silme (Admin)
GET    /api/blog/categories      # Blog kategorileri
GET    /api/blog/tags            # Blog etiketleri
```

### 👤 Kullanıcı API'leri
```
POST   /api/auth/login           # Kullanıcı girişi
POST   /api/auth/logout          # Kullanıcı çıkışı
GET    /api/auth/profile         # Kullanıcı profili
PUT    /api/auth/profile         # Profil güncelleme
POST   /api/auth/change-password # Şifre değiştirme
```

### 📊 Analytics API'leri
```
GET    /api/analytics/visitors   # Ziyaretçi istatistikleri
GET    /api/analytics/pages      # Sayfa görüntüleme istatistikleri
GET    /api/analytics/events     # Olay istatistikleri
POST   /api/analytics/track      # Olay takibi
```

---

## 🔗 HIZLI ERİŞİM LİNKLERİ

### 📄 Sayfa Linkleri
- [Ana Sayfa](../index.html)
- [Hakkımızda](../about.html)
- [Hizmetler](../services.html)
- [Portfolio](../portfolio.html)
- [Blog](../blog.html)
- [İletişim](../contact.html)
- [Fiyatlandırma](../pricing.html)
- [SSS](../faq.html)

### 🛠️ Admin Linkleri
- [Admin Girişi](../admin/login.php)
- [Blog Yönetimi](../admin/blog.php)

### 📚 Dokümantasyon Linkleri
- [Ana Dokümantasyon](./WEB-SITESI-DOKUMANTASYONU.md)
- [Değişiklik Geçmişi](./CHANGELOG.md)
- [Site Haritası](./SITE-HARITASI-VE-FONKSIYON-LISTESI.md)
- [Acil Durum Rehberi](./ACIL-DURUM-KURTARMA-REHBERI.md)

### 💾 Yedekleme Linkleri
- [Yedekleme Scripti](../backup/backup-script.ps1)
- [Yedekleme Çalıştırıcı](../backup/run-backup.bat)

### 🎨 Kaynak Dosyalar
- [Ana CSS](../css/styles.css)
- [Ana JavaScript](../js/main.js)
- [Bootstrap CSS](../css/bootstrap.min.css)
- [Bootstrap JS](../js/bootstrap.bundle.min.js)

---

## 🔍 ARAMA VE FİLTRELEME REHBERİ

### 📝 Fonksiyon Arama
Belirli bir fonksiyonu bulmak için:
1. `Ctrl + F` ile sayfada arama yapın
2. Fonksiyon adını yazın (örn: `validateContactForm`)
3. İlgili bölüme gidin

### 🎨 CSS Sınıfı Arama
Belirli bir CSS sınıfını bulmak için:
1. `Ctrl + F` ile sayfada arama yapın
2. Sınıf adını yazın (örn: `.btn-primary-custom`)
3. İlgili bölüme gidin

### 🔌 API Endpoint Arama
Belirli bir API endpoint'ini bulmak için:
1. `Ctrl + F` ile sayfada arama yapın
2. Endpoint yolunu yazın (örn: `/api/contact`)
3. İlgili bölüme gidin

---

**Son Güncelleme:** 09.01.2024
**Hazırlayan:** Web Geliştirme Ekibi
**Versiyon:** 1.0.0