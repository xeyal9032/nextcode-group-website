# ✅ GOOGLE ANALYTICS 4 - KURULUM TAMAMLANDI

## 🎉 Başarıyla Yapılandırıldı!

**Tarih:** 2024-01-15  
**Measurement ID:** G-8FYSTD1FVH  
**Domain:** https://nextcode.az

---

## ✅ YAPILAN İŞLEMLER

### 1. Google Site Verification
- ✅ Verification dosyası: `google6b45980adf7adfc5.html`
- ✅ Meta tag eklendi: `<meta name="google-site-verification" content="google6b45980adf7adfc5" />`
- ✅ Durum: **Aktif**

### 2. Google Analytics 4 Kodu
- ✅ Measurement ID: **G-8FYSTD1FVH**
- ✅ Header'a eklendi: `includes/header.php`
- ✅ Tüm sayfalarda aktif
- ✅ Debug mode: Kapalı (production)

### 3. Enhanced Tracking
- ✅ Page views
- ✅ Scroll depth (25%, 50%, 75%, 100%)
- ✅ Outbound link clicks
- ✅ Form submissions
- ✅ Button clicks
- ✅ Video plays
- ✅ File downloads
- ✅ JavaScript errors

### 4. Cross-Domain Tracking
- ✅ nextcode.az
- ✅ www.nextcode.az
- ✅ nextcodegroup.ostwind.az (legacy)

---

## 🧪 TEST YÖNTEMLERİ

### Yöntem 1: Real-Time Reports (En Kesin)

**Adımlar:**
1. Google Analytics'e gidin: https://analytics.google.com
2. **Reports > Realtime** seçin
3. Yeni sekmede sitenizi açın: https://nextcode.az
4. 30 saniye - 2 dakika içinde real-time'da görünmelisiniz

**Göreceğiniz Veriler:**
```
Users: 1
Page views: /index.php
Events: page_view, button_click, vb.
Location: Azerbaijan
Device: Desktop/Mobile
```

### Yöntem 2: Chrome DevTools

**Adımlar:**
1. https://nextcode.az açın
2. `F12` basın (DevTools)
3. **Network** tab
4. Filter: "collect" veya "google-analytics"
5. Sayfayı yenileyin (`Ctrl + F5`)

**Göreceğiniz Request:**
```
Request URL: https://www.google-analytics.com/g/collect?...
Status: 200 OK
Payload: en=page_view&v=2&tid=G-8FYSTD1FVH...
```

### Yöntem 3: Console Kontrolü

**F12 > Console'a yazın:**

```javascript
// 1. gtag var mı?
typeof gtag
// Sonuç: "function"

// 2. DataLayer aktif mi?
dataLayer.length
// Sonuç: 2+ olmalı

// 3. DataLayer içeriği
dataLayer
// Sonuç: Array of events

// 4. Test event gönder
gtag('event', 'test_event', {
  event_category: 'test',
  event_label: 'Console Test'
});
// Sonuç: Hata vermemeli
```

### Yöntem 4: Otomatik Test Sayfası

```
https://nextcode.az/analytics-test.php
```

Bu sayfa tüm testleri otomatik çalıştırır ve sonuçları gösterir.

### Yöntem 5: Verification Sayfası

```
https://nextcode.az/analytics-verification.html
```

Debug mode aktif, detaylı console logları.

---

## 📊 BEKLENTİLER

### İlk 24 Saat
- Real-time data hemen gelir
- Standard reports 24-48 saat delay olabilir
- Events hemen görünür
- Conversions 24 saat delay

### Tracking Edilen Veriler
```
📄 Page Views
🖱️ Button Clicks
📝 Form Submissions
🔗 Outbound Links
📂 File Downloads
🎥 Video Plays
📊 Scroll Depth (25%, 50%, 75%, 100%)
❌ JavaScript Errors
```

---

## 🎯 HIZLI TEST

### Konsol Testi (30 saniye)

```javascript
// 1. Console aç (F12)
console.log(typeof gtag); 
// Sonuç: "function" ✅

// 2. Test event gönder
gtag('event', 'quick_test');
// Hata yok ✅

// 3. DataLayer kontrol
dataLayer.length > 0
// Sonuç: true ✅
```

Hepsi ✅ ise → **Analytics çalışıyor!**

---

## 📱 TRACKING ÖZET

### Otomatik Tracking
- ✅ Page views (her sayfa)
- ✅ Session duration
- ✅ User engagement
- ✅ Bounce rate

### Enhanced Tracking (Custom)
- ✅ Scroll depth events
- ✅ Outbound clicks
- ✅ Form submissions
- ✅ Button interactions
- ✅ Video engagement
- ✅ File downloads
- ✅ Error tracking

### Cross-Domain
- ✅ nextcode.az ↔ nextcodegroup.ostwind.az
- ✅ Session continuity
- ✅ User journey tracking

---

## 🔧 CONFIGURATION

### Aktif Ayarlar

```javascript
{
  'send_page_view': true,
  'linker': {
    'domains': ['nextcode.az', 'www.nextcode.az', 'nextcodegroup.ostwind.az'],
    'accept_incoming': true
  },
  'cookie_flags': 'SameSite=None;Secure',
  'anonymize_ip': true,
  'allow_google_signals': true,
  'cookie_domain': 'nextcode.az'
}
```

### Cookie Ayarları
- **Domain:** .nextcode.az
- **Secure:** true
- **SameSite:** None
- **Privacy:** IP anonymization aktif

---

## 🚀 SONRAKİ ADIMLAR

### Google Analytics'te Yapılacaklar

1. **Events Oluştur:**
   - Admin > Events > Create event
   - Contact form submission
   - Newsletter signup
   - Purchase (e-commerce)

2. **Conversions Tanımla:**
   - Admin > Conversions
   - "contact_form_submit" → Conversion
   - "newsletter_subscribe" → Conversion

3. **Audience Oluştur:**
   - Explore > User segments
   - Active users
   - Returning users
   - High engagement users

4. **Custom Reports:**
   - Library > Create custom report
   - Page performance
   - User journey
   - Conversion funnel

---

## 📈 İZLEME ÖNERİLERİ

### Günlük Kontrol
- Real-time users
- Top pages
- Traffic sources
- Active events

### Haftalık Analiz
- User engagement
- Conversion rates
- Popular content
- Bounce rate trends

### Aylık Rapor
- Traffic growth
- Goal completions
- ROI analysis
- User demographics

---

## 🛠️ DEBUG MODE

Detaylı test için debug mode aktif edin:

**Console'a yazın:**
```javascript
gtag('config', 'G-8FYSTD1FVH', {'debug_mode': true});
```

Sonra:
1. Network tab'da detaylı payload görürsünüz
2. Console'da tüm events log edilir
3. Validation errors gösterilir

---

## ✅ BAŞARIYLA KURULDU!

**Google Analytics 4 artık tüm sayfalarda çalışıyor! 🎉**

### Test Sayfaları:
- https://nextcode.az/analytics-test.php (Detaylı test)
- https://nextcode.az/analytics-verification.html (Hızlı doğrulama)

### Real-Time Kontrol:
- https://analytics.google.com/analytics/web/#/realtime

---

## 📞 Destek

Sorun yaşarsanız:
- Test sayfası: `/analytics-test.php`
- Email: admin@nextcode.com
- Docs: `/docs/POST-DOMAIN-MIGRATION.md`

---

**NextCode Analytics** - Production Ready! ✅


