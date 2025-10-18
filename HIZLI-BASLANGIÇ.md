# ⚡ HIZLI BAŞLANGIÇ KILAVUZU

## 🚀 NextCode Group - Yeni Modern Admin Paneli

**Production URL:** https://nextcode.az

---

## 📋 3 ADIMDA BAŞLANGIÇ

### **ADIM 1: İçerikleri Doldurun** (2 dakika)

Tarayıcınızda açın:
```
https://nextcode.az/admin/populate-content.php
```

Bu script otomatik olarak ekler:
- ✅ Site ayarları (iletişim bilgileri, sosyal medya)
- ✅ Ana sayfa içerikleri
- ✅ 6 hizmet
- ✅ 5 FAQ sorusu
- ✅ Site istatistikleri

**Beklenen Sonuç:**
```
✓ Content population completed successfully!
```

---

### **ADIM 2: Admin Paneline Giriş** (1 dakika)

```
URL: https://nextcode.az/admin/login.php
Kullanıcı Adı: admin
Şifre: Admin123!@#
```

**⚠️ ÖNEMLİ:** İlk girişten sonra şifrenizi değiştirin!

---

### **ADIM 3: Modern Dashboard'ı Kullanın** (Hemen!)

Giriş yaptıktan sonra:
```
https://nextcode.az/admin/modern-dashboard.php
```

**Görecekleriniz:**
- 📊 Real-time istatistikler
- 📝 Son aktiviteler
- 🎨 Modern sidebar menü
- ⚡ AJAX ile hızlı yükleme

---

## 🎯 İLK GÖREVLERİNİZ

### **1. İlk Blog Yazınızı Ekleyin** (5 dakika)

```
Dashboard → Blog Posts → New Post

1. Title yaz: "Hoş Geldiniz"
2. Content ekle (TinyMCE editor ile)
3. "Upload Image" → Görsel seç
4. Status: "Published" seç
5. "Publish" butonuna tıkla
6. Web sitesinde blog.php'de görünsün! ✅
```

---

### **2. İlk Portfolio Projenizi Ekleyin** (5 dakika)

```
Dashboard → Portfolio → New Project

1. Title: "E-Commerce Platform"
2. Description: Proje detayı
3. Category: "Web Development"
4. Technology tags: "PHP" + Enter, "MySQL" + Enter
5. "Upload Image" → Görsel seç
6. Status: "Completed"
7. Published: ✓ işaretle
8. "Save Project" tıkla
9. Web sitesinde portfolio.php'de görünsün! ✅
```

---

### **3. Mesajları Kontrol Edin** (2 dakika)

```
Dashboard → Messages

1. "Unread" filter'a tıkla
2. Mesaja tıkla (modal açılır)
3. Detayları oku
4. Otomatik "read" olarak işaretlenir
5. İstenirse "Delete" ile sil
```

---

## 🎨 ÖZELLİKLER HIZLI BAKIŞ

### **Dashboard**
```
✅ Blog Posts count
✅ Portfolio count
✅ New Messages count
✅ Services count
✅ Recent activities
✅ Refresh button
```

### **Blog Management**
```
✅ List all posts (pagination)
✅ Search (title/content)
✅ Create new post
✅ Edit existing
✅ Delete post
✅ Rich text editor (TinyMCE)
✅ Image upload
✅ SEO fields
```

### **Portfolio Management**
```
✅ Grid view
✅ Search projects
✅ Create new project
✅ Edit existing
✅ Delete project
✅ Technology tags
✅ Image upload
✅ Status management
```

### **Messages**
```
✅ Filter (All/Unread/Read)
✅ Detail modal (AJAX)
✅ Auto mark-as-read
✅ Delete message
✅ Sender info
✅ IP tracking
```

### **File Upload**
```
✅ Drag & drop
✅ File preview
✅ Progress bar
✅ Auto optimize
✅ Secure storage
✅ Type validation
```

---

## 🔥 HIZLI AKSIYONLAR

### **Blog**
```bash
# Yeni Yazı
/admin/blog-editor.php

# Listele
/admin/blog-list.php

# Düzenle
/admin/blog-editor.php?id=1
```

### **Portfolio**
```bash
# Yeni Proje
/admin/portfolio-editor.php

# Listele
/admin/portfolio-list.php

# Düzenle
/admin/portfolio-editor.php?id=1
```

### **Messages**
```bash
# Tüm Mesajlar
/admin/messages-list.php

# Sadece Okunmamışlar
/admin/messages-list.php?status=unread
```

---

## 📱 RESPONSIVE KULLANIM

### **Mobilde:**
- ✅ Sidebar toggle butonu
- ✅ Touch-friendly butonlar
- ✅ Optimized forms
- ✅ Swipe gestures

### **Tablet'te:**
- ✅ 2 kolon layout
- ✅ Sidebar daima görünür
- ✅ Larger touch targets

---

## 🔐 GÜVENLİK KONTROL

**Yapılması Gerekenler:**

```bash
# 1. Admin Şifresini Değiştirin
Dashboard → Users → Change Password

# 2. Upload Klasörü İzinleri
chmod 755 uploads/
chmod 755 uploads/*/

# 3. .htaccess Kontrolü
# .htaccess dosyasının yüklendiğinden emin olun

# 4. Error Logging
# logs/ klasörü yazılabilir olmalı
chmod 755 logs/
```

---

## 🐛 SORUN GİDERME

### **Problem: Dashboard boş görünüyor**
```
Çözüm:
1. populate-content.php çalıştırın
2. F12 → Console → Hata var mı bakın
3. admin/ajax/get-stats.php'yi direkt açın
```

### **Problem: Upload çalışmıyor**
```
Çözüm:
1. uploads/ yazılabilir mi: chmod 755 uploads/
2. PHP ayarları:
   upload_max_filesize = 10M
   post_max_size = 10M
3. Browser console'da hata kontrol edin
```

### **Problem: Blog kaydetmiyor**
```
Çözüm:
1. Title ve Content dolu mu kontrol edin
2. F12 → Network → blog-management.php isteğine bakın
3. Response'da error varsa okuyun
```

---

## 💡 İPUÇLARI

1. **Slug:** Title yazınca otomatik oluşur
2. **SEO:** Meta Title ve Description doldurun
3. **Image:** 1200x630px ideal boyut
4. **Tags:** Virgülle ayırın
5. **Featured:** Önemli içerikleri işaretleyin

---

## 📈 BAŞARI KONTROL

**✅ Her şey çalışıyorsa:**

- Dashboard istatistikler gösteriyor
- Blog list açılıyor ve boş
- Blog editor açılıyor
- Image upload modal çalışıyor
- Portfolio list açılıyor ve boş
- Messages list açılıyor
- Web sitesi https://nextcode.az çalışıyor

**🎉 Tebrikler! Artık hazırsınız!**

---

## 🎓 ÖĞRETİCİ VİDEO LİNKLERİ

- [ ] Admin Panel Tanıtımı (Yakında)
- [ ] Blog Nasıl Eklenir (Yakında)
- [ ] Portfolio Nasıl Eklenir (Yakında)
- [ ] Mesajlar Nasıl Yönetilir (Yakında)

---

## 🆘 DESTEK

Herhangi bir sorun için:

**Email:** xeyalcemilli9032@gmail.com  
**Telefon:** +380 97 258 00 00  
**Website:** https://nextcode.az  

---

**Başarılar dileriz! 🚀**

NextCode Group Team















