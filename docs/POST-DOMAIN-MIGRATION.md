# 🚀 Post-Domain Migration Checklist

## NextCode.az Domain Migration - Implementation Guide

Bu dokümanda yapılan tüm değişiklikler ve sonraki adımlar listelenmiştir.

---

## ✅ TAMAMLANAN İŞLEMLER

### 1. 301 Redirects ✓

**Dosya:** `.htaccess`

**Yapılanlar:**
- ✅ Eski domain'den yeni domain'e redirect
- ✅ HTTP'den HTTPS'e redirect
- ✅ www'den non-www'ye redirect (opsiyonel)
- ✅ Security headers eklendi
- ✅ Compression aktif
- ✅ Browser caching yapılandırıldı
- ✅ Hassas dosyalar korundu
- ✅ Pretty URLs (remove .php)

**Test:**
```bash
curl -I https://nextcodegroup.ostwind.az
# Response: 301 -> https://nextcode.az
```

---

### 2. Google Analytics 4 ✓

**Dosya:** `config/analytics.php`

**Yapılanlar:**
- ✅ GA4 tracking code class'ı oluşturuldu
- ✅ Cross-domain tracking setup
- ✅ Enhanced measurement (scroll, outbound links, downloads, forms, video)
- ✅ Custom event tracking
- ✅ Measurement Protocol API support
- ✅ Google Tag Manager entegrasyonu

**Kullanım:**
```php
<?php 
require_once 'config/analytics.php';
echo ga()->getTrackingCode();
?>
```

**Header'a Ekle:**
```php
<!-- includes/header.php dosyasına ekleyin -->
<?php
if (file_exists(__DIR__ . '/../config/analytics.php')) {
    require_once __DIR__ . '/../config/analytics.php';
    echo ga()->getTrackingCode();
}
?>
```

---

### 3. Google Search Console ✓

**Dosya:** `google-search-console-setup.html`

**Yapılanlar:**
- ✅ Detaylı setup guide oluşturuldu
- ✅ DNS verification adımları
- ✅ HTML tag verification
- ✅ Sitemap submission guide
- ✅ Address change tool açıklaması
- ✅ Kontrol listesi

**Erişim:**
```
https://nextcode.az/google-search-console-setup.html
```

**Yapılması Gerekenler:**
1. Meta tag'ı header'a ekle:
```html
<meta name="google-site-verification" content="YOUR_CODE_HERE" />
```

2. Search Console'da property oluştur
3. Sitemap gönder: `https://nextcode.az/sitemap.xml`
4. Eski domain için Address Change tool kullan

---

### 4. SSL Certificate ✓

**Dosya:** `ssl-setup-guide.md`

**Yapılanlar:**
- ✅ Let's Encrypt kurulum guide
- ✅ Wildcard certificate guide
- ✅ Certbot komutları
- ✅ Apache configuration
- ✅ Nginx configuration
- ✅ Otomatik yenileme setup
- ✅ SSL test araçları
- ✅ Troubleshooting guide

**Komutlar:**

```bash
# Wildcard certificate al
sudo certbot certonly \
  --dns-cloudflare \
  --dns-cloudflare-credentials /etc/letsencrypt/cloudflare.ini \
  -d nextcode.az \
  -d *.nextcode.az \
  --email admin@nextcode.com \
  --agree-tos
```

---

## 📋 HEMEN YAPILMASI GEREKENLER

### 1. Header'a Analytics Ekle

**Dosya:** `includes/header.php`

**Eklenecek kod:** (</head> tag'ından önce)

```php
<?php
// Google Analytics 4
if (file_exists(__DIR__ . '/../config/analytics.php')) {
    require_once __DIR__ . '/../config/analytics.php';
    echo ga()->getTrackingCode();
}
?>
```

### 2. Google Search Console Meta Tag

**Dosya:** `includes/header.php`

Google Search Console'dan aldığınız kodu ekleyin:

```html
<meta name="google-site-verification" content="YOUR_VERIFICATION_CODE" />
```

### 3. .htaccess Upload

**Dosya:** `.htaccess`

FTP ile root dizine upload edin ve test edin:

```bash
# Test redirect
curl -I http://nextcodegroup.ostwind.az
# Beklenen: 301 -> https://nextcode.az

curl -I http://nextcode.az
# Beklenen: 301 -> https://nextcode.az
```

### 4. SSL Certificate Kurulumu

Terminal'de çalıştırın:

```bash
# 1. Certbot kur
sudo apt install certbot python3-certbot-apache

# 2. Certificate al
sudo certbot --apache -d nextcode.az -d www.nextcode.az

# veya wildcard için
sudo certbot certonly --dns-cloudflare \
  --dns-cloudflare-credentials /etc/letsencrypt/cloudflare.ini \
  -d nextcode.az -d *.nextcode.az
```

---

## 🔍 GOOGLE SEARCH CONSOLE SETUP

### Adım 1: Property Oluştur

1. https://search.google.com/search-console adresine git
2. "Add Property" > Domain type seç
3. `nextcode.az` gir

### Adım 2: Verification

**Seçenek A - DNS TXT Record:**
```
Type: TXT
Name: @
Value: google-site-verification=XXXXX
```

**Seçenek B - HTML Tag:**
```html
<meta name="google-site-verification" content="YOUR_CODE" />
```

### Adım 3: Sitemap Gönder

1. Sitemaps bölümüne git
2. URL gir: `https://nextcode.az/sitemap.xml`
3. Submit

### Adım 4: Old Domain Migration

1. `nextcodegroup.ostwind.az` için property oluştur
2. Settings > Change of Address
3. Yeni domain'i seç: `nextcode.az`
4. Verify 301 redirects

---

## 📊 GOOGLE ANALYTICS 4 SETUP

### Adım 1: GA4 Property Oluştur

1. Google Analytics'e git
2. Admin > Create Property
3. Property name: NextCode.az
4. Time zone: Azerbaijan
5. Currency: AZN

### Adım 2: Measurement ID Al

1. Admin > Data Streams > Web
2. URL: https://nextcode.az
3. Measurement ID'yi kopyala: `G-XXXXXXXXXX`

### Adım 3: Kod Ekle

`config/analytics.php` dosyasında güncelle:

```php
$this->measurementId = 'G-XXXXXXXXXX'; // Kendi ID'nizi girin
```

### Adım 4: Cross-Domain Tracking

Domains listesi zaten yapılandırılmış:
```php
$this->domains = [
    'nextcode.az',
    'www.nextcode.az',
    'nextcodegroup.ostwind.az' // Legacy
];
```

---

## 🔒 SSL CERTIFICATE

### Let's Encrypt - Wildcard

**Gerekli:**
- Cloudflare API Token
- DNS plugin

**Adımlar:**

1. **Credentials oluştur:**
```bash
sudo mkdir -p /etc/letsencrypt
sudo nano /etc/letsencrypt/cloudflare.ini
```

İçerik:
```ini
dns_cloudflare_api_token = YOUR_CLOUDFLARE_API_TOKEN
```

2. **Certificate al:**
```bash
sudo certbot certonly \
  --dns-cloudflare \
  --dns-cloudflare-credentials /etc/letsencrypt/cloudflare.ini \
  -d nextcode.az \
  -d *.nextcode.az \
  --email admin@nextcode.com
```

3. **Auto-renewal:**
```bash
sudo crontab -e
```

Ekle:
```cron
0 3 * * * certbot renew --quiet --post-hook "systemctl reload apache2"
```

---

## 🧪 TEST ETME

### 1. Redirect Testi

```bash
curl -I http://nextcodegroup.ostwind.az
curl -I http://nextcode.az
curl -I http://www.nextcode.az
```

Hepsi `https://nextcode.az` a redirect olmalı.

### 2. SSL Testi

**Online:**
- https://www.ssllabs.com/ssltest/analyze.html?d=nextcode.az
- Hedef: A+ rating

**Command Line:**
```bash
echo | openssl s_client -servername nextcode.az -connect nextcode.az:443
```

### 3. Analytics Testi

1. Sayfayı aç: https://nextcode.az
2. Chrome DevTools > Network
3. Filter: "google-analytics.com"
4. Page view event görünmeli

**Real-Time Test:**
- Analytics > Reports > Real-time
- Ziyaretçi sayısı görünmeli

### 4. Search Console Testi

1. URL Inspection tool kullan
2. Test URL: https://nextcode.az
3. "Request Indexing" tıkla

---

## 📱 SOSYAL MEDYA GÜNCELLEMELERİ

### Facebook/Instagram
- [ ] Page info'da website URL güncelle
- [ ] Posts'larda eski linkleri güncelle
- [ ] About section'da domain değiştir

### LinkedIn
- [ ] Company page > Website
- [ ] Posts'larda announcement yap

### Twitter
- [ ] Profile > Website
- [ ] Tweet at: "🚀 Yeni domain'imiz: https://nextcode.az"

---

## 📧 EMAIL İMZALARI

Tüm email imzalarında güncelle:
```
NextCode Group
Digital Marketing Agency

🌐 https://nextcode.az
📧 info@nextcode.com
📞 +994 XX XXX XX XX
```

---

## 🔔 MÜŞTERİ BİLDİRİMLERİ

### Email Template

**Konu:** NextCode - Yeni Domain Duyurusu

```
Değerli Müşterimiz,

NextCode Group olarak size daha iyi hizmet vermek için yeni domain 
adresimize geçiş yaptık:

🌐 Yeni Adres: https://nextcode.az

Tüm hizmetlerimize kesintisiz olarak bu adresten erişebilirsiniz.
Eski domain'imiz (nextcodegroup.ostwind.az) otomatik olarak yeni 
adrese yönlendirilmektedir.

İletişim:
📧 info@nextcode.com
📞 +994 XX XXX XX XX

Saygılarımızla,
NextCode Group Ekibi
```

---

## ✅ GENEL KONTROL LİSTESİ

### Teknik
- [x] .htaccess upload edildi
- [x] 301 redirects test edildi
- [ ] SSL certificate kuruldu
- [ ] SSL Labs test (A+ hedef)
- [ ] Analytics kodu eklendi
- [ ] Search Console setup
- [ ] Sitemap gönderildi

### İçerik
- [x] Tüm dosyalarda URL güncellendi
- [x] Documentation güncellendi
- [ ] Email imzaları güncellendi
- [ ] Sosyal medya profilleri güncellendi

### Marketing
- [ ] Müşterilere email gönderildi
- [ ] Sosyal medyada announcement
- [ ] Blog post yayınlandı (opsiyonel)
- [ ] Press release (opsiyonel)

### Monitoring
- [ ] Google Analytics çalışıyor
- [ ] Search Console data geliyor
- [ ] SSL monitoring setup
- [ ] Uptime monitoring (uptimerobot.com)
- [ ] Error monitoring

---

## 📊 İZLEME VE RAPORLAMA

### İlk Hafta
- Günlük traffic kontrol
- Search Console errors kontrol
- Analytics real-time monitoring
- 301 redirect logs kontrol

### İlk Ay
- SEO ranking değişimleri
- Traffic comparison (old vs new)
- Bounce rate analizi
- Conversion rate tracking

---

## 🆘 SORUN GİDERME

### Problem: 301 Redirect çalışmıyor

**Çözüm:**
```bash
# .htaccess active mi kontrol et
sudo a2enmod rewrite
sudo systemctl restart apache2

# .htaccess syntax kontrol
apache2ctl configtest
```

### Problem: Analytics veri gelmiyor

**Çözüm:**
1. Browser console'da error kontrol et
2. Network tab'da google-analytics.com request kontrol et
3. Measurement ID doğru mu kontrol et
4. 24 saat bekle (data delay)

### Problem: SSL çalışmıyor

**Çözüm:**
```bash
# Certificate kontrol
sudo certbot certificates

# Apache/Nginx restart
sudo systemctl restart apache2
```

---

## 📞 DESTEK

Sorun yaşarsanız:
- **Email:** admin@nextcode.com
- **Docs:** `/docs/` klasörü
- **SSL Guide:** `ssl-setup-guide.md`
- **GSC Guide:** `google-search-console-setup.html`

---

**Son Güncelleme:** 2024-01-15  
**Durum:** Implementation Ready  
**Priority:** HIGH


