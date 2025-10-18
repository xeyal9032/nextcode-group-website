# 🚀 ACIL: Google Analytics Upload Talimatları

## ⚠️ ÖNEMLİ

Google Analytics tag'ı ekledik ama henüz sunucuda değil!  
**FTP ile upload etmeniz gerekiyor.**

---

## 📤 UPLOAD EDİLMESİ GEREKEN DOSYALAR

### 1️⃣ ÖNCELİKLİ (Analytics için)

**Ana Dosya:**
```
includes/header.php
```

**Bu dosya tüm sayfalarda kullanıldığı için Analytics tüm sitede aktif olacak!**

---

## 🔧 FTP BİLGİLERİ

**CuteFTP 9 Kullanarak:**

```
Host:     gtorg.ftp.tools
Username: gtorg_nextcode
Password: JDH6h9T2zb8UC47t@rn56@
```

---

## 📋 UPLOAD ADIMLARI

### CuteFTP 9 ile:

1. **CuteFTP 9'u açın**

2. **Bağlantı kurun:**
   - File > Connect
   - Host: `gtorg.ftp.tools`
   - Username: `gtorg_nextcode`
   - Password: `JDH6h9T2zb8UC47t@rn56@`

3. **includes klasörüne gidin:**
   - Sağ panel (sunucu) > `includes/` klasörü

4. **header.php dosyasını upload edin:**
   - Sol panel (local): `C:\Users\xeyal\Desktop\nextcode\includes\header.php`
   - Sağ panel (sunucu): `includes/` klasörüne sürükle-bırak
   - **Overwrite:** YES

5. **Bağlantıyı kapatın**

---

## ✅ HEMEN SONRA TEST

Upload'dan **hemen sonra**:

### 1. Browser Cache Temizle
```
Ctrl + Shift + Delete
```

### 2. Sayfayı Hard Refresh
```
Ctrl + F5
```

### 3. Console Kontrolü
```
F12 > Console
```

Görmelisiniz:
```
✅ Google Analytics 4 initialized - ID: G-8FYSTD1FVH
```

### 4. Network Kontrolü
```
F12 > Network tab
Filter: "collect" veya "gtag"
```

Request görmelisiniz:
```
www.google-analytics.com/g/collect?...
Status: 200 ✅
```

### 5. Google Analytics Real-Time
```
https://analytics.google.com/analytics/web/#/realtime
```

**1-2 dakika içinde görüneceksiniz!**

---

## 🎯 OPSIYONEL UPLOAD'LAR

Upload ettikten sonra bunlar da upload edilebilir (opsiyonel):

### Analytics Test Sayfaları:
```
analytics-test.php
analytics-verification.html
google-search-console-setup.html
```

### Diğer Güncellemeler:
```
.htaccess (301 redirects)
config/analytics.php
manifest.json
ANALYTICS-READY.md
```

---

## ⚡ HIZLI KONTROL

Upload'dan sonra console'a yapıştır:

```javascript
// Test
typeof gtag
// Sonuç: "function" ✅

dataLayer.length
// Sonuç: 2+ ✅

gtag('event', 'test_event', { test: true });
// Hata yok ✅
```

Hepsi ✅ ise → **Analytics çalışıyor!**

---

## 🆘 SORUN GİDERME

### Google Tag bulunamıyor diyor?

**Çözüm:**
1. ✅ `includes/header.php` upload edildi mi?
2. ✅ Browser cache temizlendi mi? (Ctrl + Shift + Delete)
3. ✅ Hard refresh yapıldı mı? (Ctrl + F5)
4. ✅ AdBlock kapalı mı?

### Console'da hata var?

**Kontrol edin:**
```
F12 > Console
```

**Yaygın hatalar:**
- "gtag is not defined" → header.php upload edilmemiş
- Network error → İnternet bağlantısı
- CORS error → Server yapılandırması

---

## 📞 DESTEK

Sorun yaşarsanız:
- Upload sonrası test URL: https://nextcode.az/analytics-verification.html
- Detaylı test: https://nextcode.az/analytics-test.php

---

## ✅ UPLOAD CHECKLIST

- [ ] CuteFTP 9 aç
- [ ] FTP bağlantısı kur
- [ ] includes/header.php upload et
- [ ] Upload başarılı (100%)
- [ ] Browser cache temizle (Ctrl + Shift + Delete)
- [ ] Hard refresh (Ctrl + F5)
- [ ] Console kontrol: "Google Analytics 4 initialized" ✅
- [ ] Network kontrol: "collect" request ✅
- [ ] Real-time'da görün (1-2 dakika)

---

**ÖNEMLİ:** Sadece `includes/header.php` dosyasını upload edin, Analytics hemen çalışacak! 🚀


