# 🌐 DNS Configuration Guide - NextCode.az

## Google Search Console DNS Verification

**Domain:** nextcode.az  
**Verification Method:** DNS TXT Record  
**Code:** 406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw

---

## 📋 DNS TXT KAYDI EKLEME

### Gerekli Bilgiler:

```
Type:  TXT
Name:  @ (veya nextcode.az)
Value: google-site-verification=406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw
TTL:   3600 (1 saat) veya Auto
```

---

## 🔧 DNS SAĞLAYICILARA GÖRE ADIMLAR

### Cloudflare (En Yaygın)

**Adımlar:**

1. **Cloudflare Dashboard'a gidin:**
   ```
   https://dash.cloudflare.com
   ```

2. **Domain seçin:** nextcode.az

3. **DNS bölümüne gidin:**
   - Sol menü > DNS > Records

4. **Add record:**
   - **Type:** TXT
   - **Name:** @ (veya boş bırakın)
   - **Content:** `google-site-verification=406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw`
   - **TTL:** Auto
   - **Proxy status:** DNS only (gri bulut)

5. **Save**

**Screenshot:**
```
┌─────────────────────────────────────────────────────┐
│ Type: TXT                                           │
│ Name: @                                             │
│ Content: google-site-verification=406-Of9HVH...    │
│ TTL: Auto                                           │
│ Proxy: DNS only                                     │
└─────────────────────────────────────────────────────┘
```

---

### GoDaddy

**Adımlar:**

1. **GoDaddy'ye giriş:**
   ```
   https://dcc.godaddy.com/manage/dns
   ```

2. **Domain seçin:** nextcode.az

3. **DNS Management > Add**

4. **Kayıt bilgileri:**
   - **Type:** TXT
   - **Host:** @
   - **TXT Value:** `google-site-verification=406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw`
   - **TTL:** 1 Hour

5. **Save**

---

### Namecheap

**Adımlar:**

1. **Namecheap Dashboard:**
   ```
   https://ap.www.namecheap.com/domains/list/
   ```

2. **Manage > Advanced DNS**

3. **Add New Record:**
   - **Type:** TXT Record
   - **Host:** @
   - **Value:** `google-site-verification=406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw`
   - **TTL:** Automatic

4. **Save**

---

### cPanel / DirectAdmin

**cPanel:**

1. **cPanel'e giriş**
2. **Zone Editor** veya **Advanced DNS Zone Editor**
3. **Add Record:**
   - **Type:** TXT
   - **Name:** nextcode.az. (nokta ile bitmeli)
   - **Record:** `google-site-verification=406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw`
   - **TTL:** 14400

4. **Add Record**

---

### Diğer Sağlayıcılar

Genel format:
```
Record Type:  TXT
Host/Name:    @
Value/Data:   google-site-verification=406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw
TTL:          3600 (veya Auto)
```

---

## ⏱️ DNS YAYILMASI

**Süre:** 5 dakika - 48 saat  
**Genellikle:** 10-30 dakika

### DNS Yayılmasını Kontrol Edin:

**Online Tool:**
```
https://www.whatsmydns.net/#TXT/nextcode.az
```

**Command Line:**
```bash
# Windows (PowerShell)
nslookup -type=TXT nextcode.az

# Mac/Linux
dig TXT nextcode.az

# Cloudflare DNS
dig TXT nextcode.az @1.1.1.1

# Google DNS
dig TXT nextcode.az @8.8.8.8
```

**Beklenen Çıktı:**
```
nextcode.az.  3600  IN  TXT  "google-site-verification=406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw"
```

---

## 🔍 VERİFİCATION KONTROL

### İki Yöntem Kullanılabilir:

#### Yöntem 1: HTML Tag (Hızlı) ✅
- **Durum:** Zaten eklendi
- **Lokasyon:** `includes/header.php`
- **Avantaj:** Hemen çalışır (FTP upload sonrası)

#### Yöntem 2: DNS TXT Record
- **Durum:** Eklemeniz gerekiyor
- **Lokasyon:** DNS sağlayıcı paneli
- **Avantaj:** Domain-level verification

---

## 🎯 ÖNERİLEN YÖNTEM

### Hemen Çalışması İçin: HTML Tag

**1. FTP Upload:**
```
includes/header.php → Sunucuya upload
```

**2. Test:**
```
https://nextcode.az
F12 > Elements > <head>
```

**Görmelisiniz:**
```html
<meta name="google-site-verification" content="406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw" />
```

**3. Google'da Retry:**
- Search Console > Retry
- ✅ Başarılı!

### Uzun Vadeli: DNS TXT Record

**Avantajları:**
- Domain-level verification
- Subdomain'leri de kapsar
- Tag kaldırılsa bile çalışır

---

## 📊 MEVCUT VERIFICATION KODLARI

Sitenizde **2 verification kodu** var:

**1. HTML Dosyası:**
```
google6b45980adf7adfc5.html
```

**2. Meta Tag (2 adet):**
```html
<meta name="google-site-verification" content="google6b45980adf7adfc5" />
<meta name="google-site-verification" content="406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw" />
```

**Her iki yöntem de çalışır! Google hangisini bulursa kullanır.**

---

## 🧪 DNS TEST KOMUTU

### Windows PowerShell:

```powershell
nslookup -type=TXT nextcode.az
```

### Beklenen Sonuç (DNS eklendikten sonra):

```
nextcode.az
    text = "google-site-verification=406-Of9HVHlYOHFUyGI6qWv43vR-v5W5laqeTZNtbjw"
```

---

## ✅ UPLOAD CHECKLIST

### Hemen (HTML Tag):
- [ ] `includes/header.php` FTP upload
- [ ] Cache temizle
- [ ] https://nextcode.az test
- [ ] Google'da retry

### Opsiyonel (DNS):
- [ ] DNS sağlayıcıya giriş
- [ ] TXT record ekle
- [ ] DNS yayılmasını bekle (10-30 dk)
- [ ] nslookup ile test

---

## 🎯 ÖNERİ

**En hızlı yol:**

1. ✅ **FTP Upload** → `includes/header.php` (2 dakika)
2. ✅ **Test** → Console + Google retry (1 dakika)
3. ✅ **Başarılı!** 🎉

**DNS TXT record opsiyonel ama önerilen!**

---

## 📞 DESTEK

DNS veya verification konusunda:
- Guide: `DNS-SETUP-GUIDE.md`
- Upload: `FINAL-UPLOAD-INSTRUCTIONS.md`
- Email: admin@nextcode.com

---

## 🚀 ŞİMDİ YAPIN

**Öncelik 1: FTP Upload**
```
includes/header.php → Upload
```

**Bu upload ile:**
- ✅ GTM çalışacak
- ✅ GA4 çalışacak  
- ✅ Verification çalışacak

**DNS TXT record'u daha sonra da ekleyebilirsiniz!**

Upload edelim mi? 📤

