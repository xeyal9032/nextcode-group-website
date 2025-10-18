# 🧪 NextCode Web Projesi - Test Senaryoları

**Tarih:** 12 Ekim 2025  
**Test Edici:** _________________  
**Test Ortamı:** Production (https://nextcode.az)

---

## TEST 1: Contact Form Testi

### Adımlar:
1. **URL Aç:** https://nextcode.az/contact.php
2. **Formu Doldur:**
   - Ad: `Xeyal Cemilli` (boşluklu - önemli!)
   - Email: `test@nextcode.az`
   - Telefon: `+994501234567`
   - Şirkət: `Test Şirkəti`
   - Mövzu: `Test Mesajı`
   - Xidmət: `SEO Optimizasyonu`
   - Büdcə: `1000-2500 AZN`
   - Mesaj: `Bu bir test mesajıdır. Lütfen dikkate almayın.`
   - ✅ Məxfilik siyasətini qəbul edirəm (checkbox işaretle)
3. **"Mesaj Göndər" butonuna tıkla**
4. **Beklenen Sonuç:**
   - ✅ Başarılı mesajı göster: "Mesajınız uğurla göndərildi"
   - ✅ Form temizlenmeli
   - ✅ Hiçbir hata mesajı olmamalı

### Veritabanı Kontrolü:
```sql
-- SSH veya phpMyAdmin'de çalıştır:
SELECT * FROM contact_messages 
ORDER BY created_at DESC 
LIMIT 1;

-- Beklenen Sonuç:
-- first_name = "Xeyal"
-- last_name = "Cemilli"
-- email = "test@nextcode.az"
-- phone = "+994501234567"
-- subject = "Test Mesajı"
-- message = "Bu bir test mesajıdır..."
-- status = "unread"
-- ip_address = (senin IP'n)
```

**✅ BAŞARILI** [ ]  
**❌ BAŞARISIZ** [ ] → Hata: ___________________

---

## TEST 2: Console Guard Testi

### Adımlar:
1. **URL Aç:** https://nextcode.az
2. **F12 tuşuna bas** (Developer Tools)
3. **Console sekmesine geç**
4. **Beklenen Sonuç:**
   - ✅ İlk mesaj: "🔒 Console logs disabled in production" (kırmızı, bold)
   - ✅ Sayfa yüklenirken başka console.log mesajı OLMAMALI
5. **Sayfada gezin:**
   - Ana sayfa ✅
   - Hakkımızda ✅
   - Hizmetler ✅
   - Portfolio ✅
   - Blog ✅
   - Contact ✅
6. **Her sayfada Console temiz mi kontrol et**

### Development Testi (Opsiyonel):
```
1. localhost'ta test et (http://localhost/nextcode)
2. Console'da: "✅ Console logs enabled (Development mode)" görmeli
3. Tüm console.log'lar aktif olmalı
```

**✅ BAŞARILI (Production'da console kapalı)** [ ]  
**✅ BAŞARILI (Localhost'ta console açık)** [ ]  
**❌ BAŞARISIZ** [ ] → Hata: ___________________

---

## TEST 3: XSS Güvenlik Testi

### Adımlar:
1. **Admin Panele Giriş:** https://nextcode.az/admin/login.php
   - Kullanıcı: `admin`
   - Şifre: `Admin123!@#`
2. **Content Management'a git**
3. **Bir içerik ekle/düzenle**
4. **XSS Denemesi:**
   ```html
   <script>alert('XSS Attack!')</script>
   <img src=x onerror="alert('XSS')">
   <a href="javascript:alert('XSS')">Click me</a>
   ```
5. **Kaydet ve sayfaya git**
6. **Beklenen Sonuç:**
   - ✅ Alert ÇIKAMAZ
   - ✅ Script çalışMAMALI
   - ✅ Sadece güvenli HTML gösterilmeli
   - ✅ Sayfada şöyle görünmeli:
     ```
     alert('XSS Attack!')  (metin olarak, çalışmadan)
     Click me (normal link, javascript: kaldırılmış)
     ```

### Ek XSS Testleri:
```html
Test 1: <p onclick="alert('xss')">Tıkla</p>
Sonuç: onclick özelliği kaldırılmalı

Test 2: <strong>Kalın metin</strong>
Sonuç: Çalışmalı (izin verilen tag)

Test 3: <iframe src="https://evil.com"></iframe>
Sonuç: Tamamen kaldırılmalı (tehlikeli tag)
```

**✅ BAŞARILI (XSS engellendi)** [ ]  
**❌ BAŞARISIZ (Alert çıktı)** [ ] → ACİL GÜVENLİK AÇIĞI!

---

## TEST 4: Email/Social Media Testi

### Email Kontrolü:
1. **URL Aç:** https://nextcode.az/contact.php
2. **Email bölümünü bul**
3. **Beklenen Sonuç:**
   - ✅ 1. Email: `xeyalcemilli9032@gmail.com` (mailto: linki ile)
   - ✅ 2. Email: `info@nextcode.az` (mailto: linki ile)
   - ❌ Duplicate email olMAMALI

### Social Media Link Kontrolü:
1. **Sosial Şəbəkələr bölümünü bul**
2. **Her linke tıkla ve kontrol et:**
   - ✅ Facebook: `https://www.facebook.com/OstWind.LLC/?locale=ru_RU`
   - ✅ Instagram: `https://www.instagram.com/nextcodegroup/`
   - ✅ LinkedIn: `https://www.linkedin.com/company/nextcode-group`
   - ✅ WhatsApp: `https://wa.me/380972580000`
3. **Her link yeni sekmede açılmalı** (target="_blank")
4. **# placeholder link OLMAMALI**

**✅ Email doğru** [ ]  
**✅ Social media linkleri çalışıyor** [ ]  
**❌ BAŞARISIZ** [ ] → Hata: ___________________

---

## TEST 5: Admin Remember Me Testi

### Adımlar:
1. **Admin Logout** (eğer giriş yaptıysan)
2. **Login Sayfası:** https://nextcode.az/admin/login.php
3. **Giriş Bilgileri:**
   - Kullanıcı: `admin`
   - Şifre: `Admin123!@#`
   - ✅ **"Beni hatırla" checkbox'ını işaretle**
4. **"Giriş Yap" butonuna tıkla**
5. **Dashboard'a yönlendirileceksin**
6. **Tarayıcıyı TAMAMEN KAPAT** (tüm pencereler)
7. **Tarayıcıyı tekrar aç**
8. **URL gir:** https://nextcode.az/admin/dashboard.php
9. **Beklenen Sonuç:**
   - ✅ Giriş yapılmış olmalı (login sayfasına yönlendirilMEMELI)
   - ✅ Dashboard görünmeli
   - ✅ Kullanıcı bilgileri üstte görünmeli

### Cookie Kontrolü (Opsiyonel):
```
1. F12 → Application → Cookies → nextcode.az
2. "admin_remember_token" cookie'si var mı?
3. Expire date 30 gün sonra mı?
```

### Veritabanı Kontrolü:
```sql
SELECT username, remember_token, last_login 
FROM admin_users 
WHERE username = 'admin';

-- Beklenen:
-- remember_token: Uzun bir hash değeri (64 karakter)
-- last_login: Son giriş zamanı
```

**✅ BAŞARILI (Remember me çalışıyor)** [ ]  
**❌ BAŞARISIZ (Login sayfasına yönlendirdi)** [ ] → Hata: ___________________

---

## BONUS TEST: Session & CSRF Testi

### Adımlar:
1. **Contact Form aç:** https://nextcode.az/contact.php
2. **F12 → Console**
3. **Şunu yaz:**
   ```javascript
   console.log(document.querySelector('input[name="csrf_token"]').value);
   ```
4. **Beklenen Sonuç:**
   - ✅ Uzun bir token görmeli (64 karakter)
   - ✅ Her sayfa yenilemede farklı olmalı

### CSRF Koruma Testi:
1. **Formu doldur ama GÖNDERME**
2. **F12 → Console → Şunu çalıştır:**
   ```javascript
   document.querySelector('input[name="csrf_token"]').value = 'invalid_token';
   ```
3. **Şimdi formu gönder**
4. **Beklenen Sonuç:**
   - ✅ Hata mesajı: "Təhlükəsizlik xətası baş verdi"
   - ✅ Form gönderilMEMELI

**✅ BAŞARILI (CSRF koruması çalışıyor)** [ ]  
**❌ BAŞARISIZ** [ ] → ACİL GÜVENLİK AÇIĞI!

---

## 📊 TEST SONUÇLARI ÖZETI

| Test | Durum | Notlar |
|------|-------|--------|
| Contact Form | [ ] ✅ [ ] ❌ | |
| Console Guard | [ ] ✅ [ ] ❌ | |
| XSS Güvenlik | [ ] ✅ [ ] ❌ | |
| Email/Social Media | [ ] ✅ [ ] ❌ | |
| Admin Remember Me | [ ] ✅ [ ] ❌ | |
| BONUS: Session/CSRF | [ ] ✅ [ ] ❌ | |

**TOPLAM BAŞARILI:** _____ / 6  
**TOPLAM BAŞARISIZ:** _____ / 6

---

## 🔥 ACİL DURUMLAR

### Test Başarısız Olursa:

#### Contact Form Çalışmıyorsa:
```sql
-- Tablo yapısını kontrol et:
DESCRIBE contact_messages;

-- first_name ve last_name sütunları var mı?
-- Yoksa:
ALTER TABLE contact_messages 
    ADD COLUMN name VARCHAR(255) AFTER id;
```

#### Console Guard Çalışmıyorsa:
```javascript
// Browser console'da test et:
window.location.hostname
// "nextcode.az" döndürüyorsa ve console hala açıksa:
// js/console-guard.js dosyası yüklenmemiş olabilir
```

#### XSS Koruma Çalışmıyorsa:
```
ACİL: includes/content_helper.php dosyasını kontrol et
getHtmlContent() fonksiyonu güncel mi?
```

#### Remember Me Çalışmıyorsa:
```sql
-- Sütun var mı kontrol et:
SHOW COLUMNS FROM admin_users LIKE 'remember_token';

-- Yoksa ekle:
ALTER TABLE admin_users 
    ADD COLUMN remember_token VARCHAR(255) NULL AFTER password_hash;
```

---

## 📝 NOTLAR

**Test Eden:** _________________  
**Test Tarihi:** _________________  
**Test Süresi:** _________________  
**Toplam Hata Sayısı:** _________________

**Ek Gözlemler:**
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________

---

**İmza:** _________________  
**Tarih:** _________________


