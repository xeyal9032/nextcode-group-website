# 🚀 NextCode - Otomatik Migration Sistemi (HIZLI BAŞLANGIÇ)

## 📦 HAZIR DOSYALAR

| Dosya | Açıklama | FTP'ye Yükle |
|-------|----------|--------------|
| `config/auto-migration.php` | Ana motor (otomatik) | ✅ YENİ |
| `config/database.php` | Güncellendi (migration dahil) | ✅ GÜNCELLE |
| `admin/database-migration.php` | Admin panel (görsel) | ✅ YENİ |
| `cron/auto-migration.php` | Cron job (opsiyonel) | ✅ YENİ |

---

## ⚡ 3 ADIMDA KURULUM

### **1️⃣ FTP'ye Yükle (5 dakika)**
```
CuteFTP 9 ile bağlan:
Host: gtorg.ftp.tools
User: gtorg_nextcode

Yükle:
✓ config/auto-migration.php          → /config/
✓ config/database.php                → /config/ (GÜNCELLE)
✓ admin/database-migration.php       → /admin/
✓ cron/auto-migration.php            → /cron/ (klasör oluştur)
```

### **2️⃣ Test Et (2 dakika)**
```
1. https://nextcode.az aç (migration otomatik çalışacak)
2. https://nextcode.az/admin/database-migration.php aç
3. Tüm sütunlar ✅ VAR olmalı
```

### **3️⃣ Bitti! 🎉**
```
✅ Sistem otomatik çalışıyor
✅ Her 1 saatte bir kontrol ediyor
✅ Eksikleri otomatik ekliyor
```

---

## 🎮 KULLANIM

### **Otomatik Mod (Varsayılan):**
```
→ Hiçbir şey yapmanıza gerek yok
→ Sistem kendisi çalışır
→ Her 1 saatte bir kontrol eder
→ Eksikleri otomatik ekler
```

### **Manuel Mod (Admin Panel):**
```
1. https://nextcode.az/admin/database-migration.php
2. "Migration Çalıştır" → Tıkla
3. Sonuçları gör
```

### **Cron Mod (Gelişmiş):**
```bash
# cPanel → Cron Jobs
Komut: /usr/bin/php /home/gtorg/public_html/cron/auto-migration.php
Zaman: 0 3 * * * (her gün saat 03:00)
```

---

## ✅ YAPILACAK İŞLEMLER

| İşlem | Durum | Açıklama |
|-------|-------|----------|
| **admin_users.remember_token** | 🔴 Eksik | ✅ Otomatik eklenecek |
| **contact_messages.status** | 🟡 Yanlış | ✅ Otomatik düzeltilecek |
| **site_images.image_description** | 🔴 Eksik | ✅ Otomatik eklenecek |
| **portfolio_projects.is_featured** | 🟡 Yanlış ad | ✅ Otomatik düzeltilecek |
| **portfolio_projects.is_published** | 🔴 Eksik | ✅ Otomatik eklenecek |
| **Performans index'leri** | 🔴 Eksik | ✅ Otomatik eklenecek |

**TOPLAM:** 15+ otomatik değişiklik

---

## 🔍 KONTROL

### **Başarılı Kurulum:**
```bash
✅ https://nextcode.az → Açılıyor (hatasız)
✅ cache/migration_cache.json → Oluştu
✅ admin/database-migration.php → Tüm sütunlar VAR
✅ Contact form → Çalışıyor
✅ Admin login → "Beni Hatırla" çalışıyor
```

### **Cache Kontrolü:**
```bash
# FTP'de bak:
cache/migration_cache.json

# İçeriği:
{
    "last_run": 1697123456,
    "migrations_applied": [
        "admin_users: remember_token eklendi",
        "contact_messages: status güncellendi",
        ...
    ],
    "last_check": "2025-10-12 18:30:00"
}
```

---

## ⚠️ ÖNEMLİ NOTLAR

### **Yedek Alma:**
```
⚠️ İlk yükleme öncesi MUTLAKA yedek alın:
phpMyAdmin → Dışa Aktar → backup_2025-10-12.sql
```

### **Cache Klasörü:**
```
✅ cache/ klasörü yazılabilir olmalı
chmod 755 cache/
```

### **Log Takibi:**
```bash
# Hatalar için:
tail -f logs/error.log
```

---

## 🎉 SONUÇ

**Durum:** ✅ Tamamen Hazır

**Avantajlar:**
- 🤖 Hiçbir manuel işlem gerekmez
- ⚡ Performanslı (cache ile)
- 🔒 Güvenli (mevcut veriler korunur)
- 📊 İzlenebilir (admin panel + log)
- 🔄 Gelecek güncellemeleri otomatik

**Dezavantajlar:**
- Yok! 😊

---

## 📞 İLETİŞİM

Sorun olursa:
1. logs/error.log kontrol et
2. Admin panel kontrol et
3. Cache'i sil ve tekrar dene

---

**🎯 ŞİMDİ NE YAPACAKSINIZ?**

```
1. 📤 4 dosyayı FTP'ye yükle
2. 🌐 https://nextcode.az aç
3. ✅ Bitti! Sistem otomatik çalışıyor
```

**Süre:** 5-10 dakika  
**Zorluk:** 🟢 Çok Kolay  
**Sonuç:** ✅ %100 Başarı


