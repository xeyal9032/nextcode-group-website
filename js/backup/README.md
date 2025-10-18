# NextCode Group Web Projesi - Backup Sistemi

## 📋 Backup Sistemi Hakkında

Bu klasör NextCode Group web projesinin tüm dosyalarının backup'larını içerir. Her backup tarih damgası ile organize edilmiştir.

## 🗂️ Backup Klasör Yapısı

```
backup/
├── 2025-09-08_18-52-19/          # Backup klasörü (tarih-saat)
│   ├── php/                      # Ana PHP dosyaları
│   ├── css/                      # CSS dosyaları
│   ├── js/                       # JavaScript dosyaları
│   ├── config/                   # Konfigürasyon dosyaları
│   ├── database/                 # Veritabanı dosyaları
│   ├── images/                   # Görsel dosyalar
│   ├── admin/                    # Admin paneli
│   ├── api/                      # API dosyaları
│   ├── includes/                 # Include dosyaları
│   ├── documentation/            # Dokümantasyon
│   └── backup-info.json          # Backup bilgileri
├── backup-script.ps1             # Otomatik backup scripti
├── restore-script.ps1            # Restore scripti
└── README.md                     # Bu dosya
```

## 🚀 Backup Oluşturma

### Otomatik Backup (Önerilen)

```powershell
# PowerShell'de çalıştırın
.\backup-script.ps1
```

### Manuel Backup

```powershell
# Belirli parametrelerle
.\backup-script.ps1 -BackupPath "backup" -Compress -IncludeDatabase
```

## 🔄 Backup'tan Geri Yükleme

### Restore İşlemi

```powershell
# Belirli bir backup'tan geri yükleme
.\restore-script.ps1 -BackupPath "backup\2025-09-08_18-52-19" -TargetPath "."

# Mevcut dosyaları üzerine yazma
.\restore-script.ps1 -BackupPath "backup\2025-09-08_18-52-19" -TargetPath "." -Force
```

## 📊 Backup Bilgileri

Her backup klasöründe `backup-info.json` dosyası bulunur:

```json
{
  "timestamp": "2025-09-08_18-52-19",
  "date": "2025-09-08 18:52:19",
  "project_name": "NextCode Group Web Projesi",
  "files_count": 150,
  "size_mb": 25.5,
  "include_database": true,
  "backup_notes": "Tüm web projesi dosyalarının tam backup'ı"
}
```

## 🔧 Backup Script Parametreleri

### backup-script.ps1

- `-BackupPath`: Backup klasörü yolu (varsayılan: "backup")
- `-Compress`: Backup'ı sıkıştır (varsayılan: false)
- `-IncludeDatabase`: Veritabanı dosyalarını dahil et (varsayılan: true)

### restore-script.ps1

- `-BackupPath`: Backup klasörü yolu (zorunlu)
- `-TargetPath`: Hedef klasör yolu (varsayılan: ".")
- `-Force`: Mevcut dosyaları üzerine yaz (varsayılan: false)

## 📁 Yedeklenen Dosya Türleri

- **PHP Dosyaları**: `*.php`
- **CSS Dosyaları**: `*.css`
- **JavaScript Dosyaları**: `*.js`
- **Veritabanı Dosyaları**: `*.sql`
- **Dokümantasyon**: `*.md`, `*.txt`
- **Konfigürasyon**: `*.htaccess`, `*.conf`
- **Görseller**: `*.jpg`, `*.png`, `*.svg`

## 🗄️ Kritik Dosyalar

Aşağıdaki dosyalar her backup'ta mutlaka bulunur:

- `index.php` - Ana sayfa
- `config/database.php` - Veritabanı konfigürasyonu
- `config/security.php` - Güvenlik ayarları
- `includes/header.php` - Sayfa başlığı
- `includes/footer.php` - Sayfa alt bilgisi
- `admin/index.php` - Admin paneli
- `api-router.php` - API yönlendirici
- `router.php` - Ana yönlendirici

## ⚠️ Önemli Notlar

1. **Düzenli Backup**: Haftalık otomatik backup önerilir
2. **Güvenlik**: Backup dosyaları güvenli bir yerde saklanmalı
3. **Test**: Backup'ları düzenli olarak test edin
4. **Boyut**: Backup boyutunu takip edin
5. **Temizlik**: Eski backup'ları düzenli olarak silin

## 🆘 Acil Durum Kurtarma

### Hızlı Restore

```powershell
# En son backup'tan hızlı geri yükleme
$latestBackup = Get-ChildItem "backup" -Directory | Sort-Object CreationTime -Descending | Select-Object -First 1
.\restore-script.ps1 -BackupPath $latestBackup.FullName -TargetPath "." -Force
```

### Backup Kontrolü

```powershell
# Backup'ları listele
Get-ChildItem "backup" -Directory | Sort-Object CreationTime -Descending

# Backup bilgilerini kontrol et
Get-Content "backup\2025-09-08_18-52-19\backup-info.json" | ConvertFrom-Json
```

## 📞 Destek

Backup sistemi ile ilgili sorunlar için:
- Dokümantasyonu kontrol edin
- Backup log'larını inceleyin
- Sistem yöneticisine başvurun

---

**Son Güncelleme**: 2025-09-08  
**Versiyon**: 1.0.0  
**Oluşturan**: AI Assistant
