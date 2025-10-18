# 🏷️ Google Tag Manager Setup Guide

## NextCode.az - GTM Yapılandırması

**GTM Container ID:** GTM-K6RHVXZ4  
**Domain:** https://nextcode.az  
**Durum:** ✅ Kurulum Tamamlandı

---

## ✅ YAPILAN İŞLEMLER

### 1. GTM Kodu Header'a Eklendi
**Dosya:** `includes/header.php`

**Lokasyon:**
- `<head>` tag'ının hemen sonrası
- `<body>` tag'ının hemen sonrası (noscript)

### 2. GA4 Entegrasyonu
- GA4 (G-8FYSTD1FVH) zaten header'da
- GTM ile birlikte çalışabilir
- GTM'den de GA4'ü yönetebilirsiniz

---

## 🎯 GOOGLE TAG MANAGER AVANTAJLARI

### GTM ile Yapabilecekleriniz:

1. **Tag Yönetimi**
   - Google Analytics
   - Facebook Pixel
   - LinkedIn Insight
   - Hotjar
   - Custom HTML tags

2. **Kod Değişikliği Olmadan**
   - Yeni tag ekle/kaldır
   - Trigger'ları güncelle
   - Variable'ları düzenle

3. **Versiyon Kontrolü**
   - Workspace'ler
   - Preview mode
   - Version history
   - Rollback özelliği

4. **Debugging**
   - Preview mode
   - Debug console
   - Tag firing kontrolü

---

## 🚀 UPLOAD SONRASI

### FTP Upload Checklist:
- [ ] `includes/header.php` upload edildi
- [ ] Browser cache temizlendi
- [ ] Hard refresh yapıldı (Ctrl + F5)
- [ ] Console'da GTM log var

### Console'da Görülmesi Gerekenler:

```javascript
// F12 > Console

// 1. GTM loaded
console.log('GTM:', window.google_tag_manager);
// Object {...} ✅

// 2. DataLayer aktif
console.log('DataLayer:', dataLayer.length);
// 2+ ✅

// 3. GA4 initialized
// ✅ Google Analytics 4 initialized - ID: G-8FYSTD1FVH
```

---

## 🧪 GTM TEST

### 1. Preview Mode

**Adımlar:**
1. GTM Dashboard: https://tagmanager.google.com
2. Container: GTM-K6RHVXZ4 seç
3. **Preview** butonuna tıkla
4. URL gir: https://nextcode.az
5. **Connect** tıkla

**Görmelisiniz:**
- Tag Assistant açılır
- Tag firing durumları
- Variables
- DataLayer events

### 2. Network Test

**Chrome DevTools:**
1. F12 > Network
2. Filter: "gtm.js"
3. Sayfa yenile

**Request:**
```
https://www.googletagmanager.com/gtm.js?id=GTM-K6RHVXZ4
Status: 200 ✅
```

### 3. Console Test

```javascript
// GTM container var mı?
google_tag_manager['GTM-K6RHVXZ4']
// Object {...} ✅

// DataLayer kontrol
dataLayer
// Array of events ✅
```

---

## 📊 GTM DASHBOARD'DA YAPILANDIRILACAKLAR

### GA4 Tag Ekle (Opsiyonel - zaten header'da var)

**Eğer GA4'ü GTM'den yönetmek isterseniz:**

1. **GTM Dashboard'a gidin**
2. **Tags > New**
3. **Tag Configuration:**
   - Tag Type: Google Analytics: GA4 Configuration
   - Measurement ID: G-8FYSTD1FVH

4. **Triggering:**
   - All Pages

5. **Save**

### Facebook Pixel Ekle

1. **Tags > New**
2. **Tag Type:** Custom HTML
3. **HTML:**
```html
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', 'YOUR_PIXEL_ID');
fbq('track', 'PageView');
</script>
```

4. **Trigger:** All Pages
5. **Save**

### Custom Events

**Contact Form Success:**

1. **Tags > New**
2. **Tag Type:** GA4 Event
3. **Event Name:** contact_form_success
4. **Trigger:** Form Submission

---

## 🎨 GTM VARIABLES

### Built-in Variables

Aktif edin:
- Page URL
- Page Hostname
- Page Path
- Referrer
- Click Element
- Click URL
- Form ID
- Form Classes

### User-Defined Variables

Oluşturun:
- Page Type
- User ID (login varsa)
- Content Category

---

## 🔔 TRIGGERS

### Recommended Triggers:

1. **All Pages** - Page view
2. **Form Submit** - Form gönderimi
3. **Click - Outbound Links** - Dış linkler
4. **Click - Download** - Dosya indirme
5. **Scroll Depth** - 25%, 50%, 75%, 100%
6. **Video** - Play, pause, complete

---

## 📱 DEBUGGING

### GTM Debug Console

**Preview Mode'da:**
- Tag firing durumu
- Variable values
- DataLayer state
- Errors

### Chrome Extension

**Google Tag Assistant:**
1. Chrome Web Store'dan yükle
2. Extension'ı aktif et
3. Siteyi aç
4. Extension icon'a tıkla
5. Tag'ları gör

---

## ⚡ HIZLI BAŞLANGIÇ

### Upload Sonrası İlk 5 Dakika:

**1. Upload et:**
```
includes/header.php → FTP
```

**2. Test et:**
```javascript
// Console'da
console.log(google_tag_manager); // Object ✅
```

**3. GTM'e git:**
```
https://tagmanager.google.com
Container: GTM-K6RHVXZ4
```

**4. Preview Mode:**
```
Preview > https://nextcode.az > Connect
```

**5. Publish:**
```
Submit > Publish
```

---

## 🎯 ŞUANDA DURUM

### Header'da Şunlar Var:

1. ✅ **Google Tag Manager** (GTM-K6RHVXZ4)
2. ✅ **Google Analytics 4** (G-8FYSTD1FVH)
3. ✅ **Site Verification** (google6b45980adf7adfc5)

**İkisini de kullanabilirsiniz veya GA4'ü GTM'den yönetebilirsiniz!**

---

## 🔄 İKİ YÖNTEM

### Yöntem 1: İkisi Birlikte (Şu anki durum)
```
✅ GTM (tag management)
✅ GA4 (direct code)
```

**Avantaj:** GA4 hemen çalışır, GTM ile ek tag'lar eklersiniz

### Yöntem 2: Sadece GTM
```
✅ GTM (tag management)
└── GA4 (GTM içinden)
```

**Avantaj:** Her şey GTM'den yönetilir

**Öneri:** Şu anki yapı (ikisi birlikte) en iyisi! ✅

---

## 📋 UPLOAD CHECKLIST

- [ ] includes/header.php upload edildi (FTP)
- [ ] Browser cache temizlendi
- [ ] Console'da GTM log var
- [ ] Network'de gtm.js request var
- [ ] Google Analytics real-time'da görünüyor
- [ ] GTM Preview mode test edildi

---

## 🎉 SONUÇ

**Hem GA4 hem GTM kodu eklendi!**

**Yapmanız gereken:**
1. 📤 FTP ile `includes/header.php` upload et
2. 🧪 Test et: https://nextcode.az/analytics-verification.html
3. ✅ Google'da "Retry" tıkla

**Upload ettikten sonra her şey çalışacak!** 🚀

Upload edelim mi? 📤

