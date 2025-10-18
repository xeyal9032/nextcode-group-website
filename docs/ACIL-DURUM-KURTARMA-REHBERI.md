# 🚨 ACİL DURUM KURTARMA REHBERİ

## 📋 İÇİNDEKİLER
1. [Acil Durum Türleri](#acil-durum-türleri)
2. [Hızlı Kurtarma Adımları](#hızlı-kurtarma-adımları)
3. [Yedekleme ve Geri Yükleme](#yedekleme-ve-geri-yükleme)
4. [Yaygın Sorunlar ve Çözümleri](#yaygın-sorunlar-ve-çözümleri)
5. [İletişim Bilgileri](#iletişim-bilgileri)
6. [Kontrol Listeleri](#kontrol-listeleri)

---

## 🚨 ACİL DURUM TÜRLERİ

### 🔴 Kritik Acil Durumlar (Anında Müdahale)
- ❌ **Site tamamen erişilemez**
- ❌ **Veritabanı bağlantı hatası**
- ❌ **Güvenlik ihlali tespit edildi**
- ❌ **Sunucu çöktü**
- ❌ **Dosyalar silinmiş/bozulmuş**

### 🟡 Orta Öncelikli Durumlar (1-2 saat içinde)
- ⚠️ **Belirli sayfalar çalışmıyor**
- ⚠️ **JavaScript hataları**
- ⚠️ **CSS stilleri yüklenmiyor**
- ⚠️ **İletişim formu çalışmıyor**
- ⚠️ **Performans sorunları**

### 🟢 Düşük Öncelikli Durumlar (24 saat içinde)
- ℹ️ **Küçük tasarım sorunları**
- ℹ️ **İçerik güncellemeleri**
- ℹ️ **SEO optimizasyonları**
- ℹ️ **Analytics sorunları**

---

## ⚡ HIZLI KURTARMA ADIMLARI

### 🔴 Kritik Durum: Site Erişilemez

#### 1️⃣ Anında Kontroller (0-5 dakika)
```powershell
# Sunucu durumu kontrolü
ping your-domain.com

# DNS kontrolü
nslookup your-domain.com

# Port kontrolü
telnet your-domain.com 80
telnet your-domain.com 443
```

#### 2️⃣ Hızlı Tanı (5-10 dakika)
- [ ] **Hosting sağlayıcı durumu** kontrol et
- [ ] **Domain süresi** kontrol et
- [ ] **SSL sertifikası** kontrol et
- [ ] **Sunucu disk alanı** kontrol et
- [ ] **Sunucu bellek kullanımı** kontrol et

#### 3️⃣ Acil Müdahale (10-30 dakika)
```powershell
# Yedekten geri yükleme
cd C:\Users\xeyal\Desktop\nextcode\ftp-upload\backup
.\run-backup.bat

# Son yedek dosyasını bul
Get-ChildItem -Path ".\backups" -Filter "*.zip" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
```

### 🔴 Kritik Durum: Veritabanı Hatası

#### 1️⃣ Veritabanı Bağlantı Kontrolü
```sql
-- MySQL bağlantı testi
SHOW DATABASES;
SHOW TABLES;
SELECT 1;
```

#### 2️⃣ Veritabanı Onarımı
```sql
-- Tablo onarımı
REPAIR TABLE contacts;
REPAIR TABLE blog_posts;
REPAIR TABLE users;

-- Tablo optimizasyonu
OPTIMIZE TABLE contacts;
OPTIMIZE TABLE blog_posts;
```

#### 3️⃣ Yedekten Geri Yükleme
```sql
-- Veritabanı yedekten geri yükleme
mysql -u username -p database_name < backup_file.sql
```

### 🟡 Orta Öncelik: JavaScript Hataları

#### 1️⃣ Hata Tespiti
```javascript
// Browser console'da hata kontrolü
console.log('JavaScript test');

// Ana fonksiyonları test et
if (typeof initializeWebsite === 'function') {
    console.log('Main functions loaded');
} else {
    console.error('Main functions missing');
}
```

#### 2️⃣ Dosya Kontrolü
- [ ] `js/main.js` dosyası mevcut mu?
- [ ] `js/bootstrap.bundle.min.js` yükleniyor mu?
- [ ] Syntax hataları var mı?
- [ ] CDN linkleri çalışıyor mu?

#### 3️⃣ Hızlı Düzeltme
```html
<!-- Yedek CDN linkleri -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
```

---

## 💾 YEDEKLEME VE GERİ YÜKLEME

### 📦 Otomatik Yedekleme Çalıştırma

#### Windows PowerShell
```powershell
# Yedekleme klasörüne git
cd C:\Users\xeyal\Desktop\nextcode\ftp-upload\backup

# Yedekleme scriptini çalıştır
.\run-backup.bat

# Yedekleme durumunu kontrol et
Get-ChildItem -Path ".\backups" | Sort-Object LastWriteTime -Descending
```

### 🔄 Manuel Yedekleme

#### Dosya Yedekleme
```powershell
# Tüm web dosyalarını yedekle
$date = Get-Date -Format "yyyyMMdd_HHmmss"
$backupName = "manual_backup_$date.zip"
Compress-Archive -Path "C:\Users\xeyal\Desktop\nextcode\ftp-upload\*" -DestinationPath "C:\Users\xeyal\Desktop\nextcode\ftp-upload\backup\backups\$backupName"
```

#### Veritabanı Yedekleme
```bash
# MySQL veritabanı yedekleme
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### ⚡ Hızlı Geri Yükleme

#### 1️⃣ Son Yedekten Geri Yükleme
```powershell
# Son yedek dosyasını bul
$latestBackup = Get-ChildItem -Path "C:\Users\xeyal\Desktop\nextcode\ftp-upload\backup\backups" -Filter "*.zip" | Sort-Object LastWriteTime -Descending | Select-Object -First 1

# Yedekten geri yükle
Expand-Archive -Path $latestBackup.FullName -DestinationPath "C:\Users\xeyal\Desktop\nextcode\ftp-upload\restore" -Force
```

#### 2️⃣ Belirli Dosyaları Geri Yükleme
```powershell
# Sadece belirli dosyaları geri yükle
Copy-Item "C:\Users\xeyal\Desktop\nextcode\ftp-upload\restore\index.html" -Destination "C:\Users\xeyal\Desktop\nextcode\ftp-upload\index.html" -Force
Copy-Item "C:\Users\xeyal\Desktop\nextcode\ftp-upload\restore\css\*" -Destination "C:\Users\xeyal\Desktop\nextcode\ftp-upload\css\" -Force
```

---

## 🔧 YAYGIN SORUNLAR VE ÇÖZÜMLERİ

### 🚫 Problem: Site Yavaş Yükleniyor

#### Tanı Adımları
- [ ] **Sunucu kaynak kullanımı** kontrol et
- [ ] **Veritabanı sorgu süreleri** kontrol et
- [ ] **Büyük dosyalar** tespit et
- [ ] **CDN durumu** kontrol et

#### Çözüm Adımları
```html
<!-- Görsel optimizasyonu -->
<img src="image.jpg" loading="lazy" alt="Açıklama">

<!-- CSS optimizasyonu -->
<link rel="preload" href="css/styles.css" as="style">

<!-- JavaScript optimizasyonu -->
<script src="js/main.js" defer></script>
```

### 🚫 Problem: İletişim Formu Çalışmıyor

#### Tanı Adımları
```javascript
// Form elementi kontrolü
const form = document.getElementById('contactForm');
console.log('Form element:', form);

// Form validation kontrolü
if (typeof validateContactForm === 'function') {
    console.log('Validation function exists');
} else {
    console.error('Validation function missing');
}
```

#### Çözüm Adımları
1. **Form HTML yapısını kontrol et**
2. **JavaScript dosyalarının yüklendiğini kontrol et**
3. **PHP mail fonksiyonunu test et**
4. **SMTP ayarlarını kontrol et**

### 🚫 Problem: Google Maps Yüklenmiyor

#### Tanı Adımları
```javascript
// Google Maps API kontrolü
if (typeof google !== 'undefined' && google.maps) {
    console.log('Google Maps API loaded');
} else {
    console.error('Google Maps API not loaded');
}
```

#### Çözüm Adımları
```html
<!-- Google Maps API yeniden yükle -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
```

### 🚫 Problem: CSS Stilleri Yüklenmiyor

#### Tanı Adımları
- [ ] **CSS dosya yolları** doğru mu?
- [ ] **Dosya izinleri** uygun mu?
- [ ] **CDN linkleri** çalışıyor mu?
- [ ] **Cache sorunu** var mı?

#### Çözüm Adımları
```html
<!-- Cache bypass -->
<link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">

<!-- Yedek CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
```

---

## 📞 İLETİŞİM BİLGİLERİ

### 🆘 Acil Durum Kişileri

#### 🔧 Teknik Destek
- **Ana Geliştirici:** [İsim] - [Telefon] - [E-posta]
- **Sistem Yöneticisi:** [İsim] - [Telefon] - [E-posta]
- **Proje Yöneticisi:** [İsim] - [Telefon] - [E-posta]

#### 🏢 Hosting ve Altyapı
- **Hosting Sağlayıcı:** [Şirket Adı] - [Destek Hattı]
- **Domain Sağlayıcı:** [Şirket Adı] - [Destek Hattı]
- **CDN Sağlayıcı:** [Şirket Adı] - [Destek Hattı]

#### 🔐 Güvenlik
- **Güvenlik Uzmanı:** [İsim] - [Telefon] - [E-posta]
- **SSL Sertifika Sağlayıcı:** [Şirket Adı] - [Destek Hattı]

### 📋 Önemli Bilgiler

#### 🔑 Erişim Bilgileri
```
FTP Bilgileri:
- Host: [FTP_HOST]
- Kullanıcı: [FTP_USER]
- Port: [FTP_PORT]

Veritabanı Bilgileri:
- Host: [DB_HOST]
- Veritabanı: [DB_NAME]
- Kullanıcı: [DB_USER]

Admin Panel:
- URL: [ADMIN_URL]
- Kullanıcı: [ADMIN_USER]
```

#### 🌐 Önemli URL'ler
- **Ana Site:** https://your-domain.com
- **Admin Panel:** https://your-domain.com/admin
- **Hosting Panel:** [HOSTING_PANEL_URL]
- **Domain Panel:** [DOMAIN_PANEL_URL]

---

## ✅ KONTROL LİSTELERİ

### 🔴 Kritik Acil Durum Kontrol Listesi

#### Site Erişilemezlik
- [ ] Sunucu ping testi yap
- [ ] DNS çözümleme kontrol et
- [ ] SSL sertifikası kontrol et
- [ ] Hosting panel kontrol et
- [ ] Domain süresi kontrol et
- [ ] Sunucu disk alanı kontrol et
- [ ] Error log'ları incele
- [ ] Son yedekten geri yükle
- [ ] Hosting desteği ile iletişime geç
- [ ] Kullanıcıları bilgilendir

#### Veritabanı Sorunu
- [ ] Veritabanı bağlantısı test et
- [ ] Tablo bütünlüğü kontrol et
- [ ] Error log'ları incele
- [ ] Veritabanı onarımı yap
- [ ] Yedekten geri yükle
- [ ] Hosting desteği ile iletişime geç
- [ ] Veri kaybı değerlendirmesi yap

### 🟡 Orta Öncelik Kontrol Listesi

#### JavaScript Hatası
- [ ] Browser console kontrol et
- [ ] JavaScript dosyaları kontrol et
- [ ] CDN linkleri kontrol et
- [ ] Syntax hataları ara
- [ ] Fonksiyon çağrıları test et
- [ ] Cache temizle
- [ ] Yedek dosyalardan geri yükle

#### CSS Yükleme Sorunu
- [ ] CSS dosya yolları kontrol et
- [ ] Dosya izinleri kontrol et
- [ ] CDN linkleri kontrol et
- [ ] Cache temizle
- [ ] Minified dosyaları kontrol et
- [ ] Yedek dosyalardan geri yükle

### 🟢 Düşük Öncelik Kontrol Listesi

#### Performans Optimizasyonu
- [ ] Sayfa yükleme süreleri ölç
- [ ] Görsel boyutları kontrol et
- [ ] JavaScript performansı analiz et
- [ ] CSS optimizasyonu yap
- [ ] Cache stratejileri gözden geçir
- [ ] CDN kullanımını optimize et

---

## 🔄 KURTARMA SONRASI İŞLEMLER

### ✅ Doğrulama Adımları

#### 1️⃣ Fonksiyonalite Testi
- [ ] **Ana sayfa** yükleniyor mu?
- [ ] **Navigasyon menüsü** çalışıyor mu?
- [ ] **İletişim formu** çalışıyor mu?
- [ ] **Portfolio modalları** açılıyor mu?
- [ ] **Blog sayfası** çalışıyor mu?
- [ ] **Fiyatlandırma hesaplayıcı** çalışıyor mu?
- [ ] **Google Maps** yükleniyor mu?
- [ ] **Canlı sohbet** çalışıyor mu?

#### 2️⃣ Performans Testi
- [ ] **Sayfa yükleme süreleri** normal mi?
- [ ] **JavaScript hataları** var mı?
- [ ] **CSS stilleri** doğru yükleniyor mu?
- [ ] **Görseller** optimize mi?
- [ ] **Mobile responsive** çalışıyor mu?

#### 3️⃣ Güvenlik Kontrolü
- [ ] **SSL sertifikası** aktif mi?
- [ ] **Form güvenliği** çalışıyor mu?
- [ ] **Admin panel** güvenli mi?
- [ ] **Dosya izinleri** uygun mu?
- [ ] **Güvenlik başlıkları** aktif mi?

### 📝 Raporlama

#### Acil Durum Raporu Şablonu
```
ACİL DURUM RAPORU
==================

Tarih/Saat: [TARIH_SAAT]
Sorun Türü: [SORUN_TÜRÜ]
Etkilenen Alanlar: [ETKİLENEN_ALANLAR]
Çözüm Süresi: [ÇÖZÜM_SÜRESİ]

Sorun Açıklaması:
[DETAYLI_AÇIKLAMA]

Uygulanan Çözüm:
[ÇÖZÜM_ADIMLARI]

Önleyici Tedbirler:
[ÖNLEYİCİ_TEDBİRLER]

Sorumlu Kişi: [SORUMLU_KİŞİ]
```

---

## 🛡️ ÖNLEYİCİ TEDBİRLER

### 📅 Düzenli Bakım Programı

#### Günlük Kontroller
- [ ] Site erişilebilirlik kontrolü
- [ ] Error log kontrolü
- [ ] Performans metrikleri kontrolü
- [ ] Güvenlik log kontrolü

#### Haftalık Kontroller
- [ ] Otomatik yedekleme kontrolü
- [ ] Veritabanı optimizasyonu
- [ ] Güvenlik güncellemeleri
- [ ] Broken link kontrolü

#### Aylık Kontroller
- [ ] Tam sistem yedeği
- [ ] Güvenlik taraması
- [ ] Performans analizi
- [ ] SSL sertifika kontrolü

#### Üç Aylık Kontroller
- [ ] Kapsamlı güvenlik denetimi
- [ ] Teknoloji stack güncellemesi
- [ ] Disaster recovery testi
- [ ] Dokümantasyon güncellemesi

### 🔔 İzleme ve Uyarı Sistemi

#### Otomatik İzleme
```bash
# Site erişilebilirlik izleme scripti
#!/bin/bash
while true; do
    if ! curl -f -s https://your-domain.com > /dev/null; then
        echo "ALERT: Site is down!" | mail -s "Site Down Alert" admin@yourdomain.com
    fi
    sleep 300  # 5 dakikada bir kontrol
done
```

---

**🚨 ACİL DURUM HATTI: [TELEFON_NUMARASI]**

**Son Güncelleme:** 09.01.2024
**Hazırlayan:** Web Geliştirme Ekibi
**Versiyon:** 1.0.0

---

> ⚠️ **UYARI:** Bu rehber acil durumlarda hızlı müdahale için hazırlanmıştır. Karmaşık sorunlarda mutlaka teknik destek ekibi ile iletişime geçin.