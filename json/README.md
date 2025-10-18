# JSON Dosyaları Klasörü

Bu klasör NextCode Group projesindeki tüm JSON dosyalarını organize eder.

## 📁 Klasör Yapısı

### `config/` - Konfigürasyon Dosyaları
- `lighthouserc.json` - Lighthouse performance testing konfigürasyonu

### `api/` - API Dokümantasyonu
- `swagger.json` - Swagger/OpenAPI dokümantasyonu

### `backup/` - Backup Bilgileri
- `backup-info.json` - Proje backup bilgileri

## 📋 Root'ta Kalan JSON Dosyaları

Aşağıdaki JSON dosyaları proje çalışması için root'ta kalmalıdır:

- `package.json` - Node.js dependencies ve scripts
- `composer.json` - PHP dependencies ve autoload
- `manifest.json` - PWA (Progressive Web App) manifest

## 🔧 Kullanım

### Lighthouse Konfigürasyonu:
```bash
# Lighthouse test çalıştır
npx lighthouse --config=json/config/lighthouserc.json
```

### API Dokümantasyonu:
```bash
# Swagger UI'da görüntüle
npx swagger-ui-serve json/api/swagger.json
```

### Backup Bilgileri:
```json
{
  "backup_date": "2025-09-08",
  "backup_time": "18:52:19",
  "files_count": 696,
  "database_tables": 35,
  "total_size": "30.77 MB"
}
```

## 📝 Notlar

- Tüm JSON dosyaları UTF-8 encoding kullanır
- Backup dosyaları tarih bazlı organize edilir
- Konfigürasyon dosyaları environment-specific olabilir
- API dokümantasyonu otomatik güncellenir

## 🚀 Geliştirme

Yeni JSON dosyaları eklerken:
1. Uygun alt klasöre yerleştirin
2. Bu README'yi güncelleyin
3. Dosya formatını kontrol edin
4. Validation ekleyin


