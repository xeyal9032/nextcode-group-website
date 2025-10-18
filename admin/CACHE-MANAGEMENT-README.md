# Admin Panel Cache Yönetimi

## Genel Bakış

NextCode Group admin panelinde gelişmiş cache yönetim sistemi bulunmaktadır. Bu sistem admin panelinin performansını artırır ve gereksiz veritabanı sorgularını önler.

## Cache Sistemi Özellikleri

### 1. Otomatik Cache Yönetimi
- Dashboard istatistikleri cache'lenir
- Blog kategorileri cache'lenir
- Site içerikleri cache'lenir
- Cache süresi: 5 dakika (300 saniye)

### 2. Cache Temizleme Seçenekleri
- **Dashboard Cache**: Dashboard istatistikleri ve genel veriler
- **Blog Cache**: Blog yazıları ve kategoriler
- **Portfolio Cache**: Portfolio projeleri
- **Mesaj Cache**: İletişim mesajları
- **PHP OPcache**: PHP OPcache temizleme
- **Otomatik Temizleme**: Süresi dolmuş dosyaları otomatik temizleme
- **Tüm Cache**: Tüm cache dosyalarını temizleme

### 3. Cache Sağlık Durumu
- Cache sağlık skoru hesaplama
- Otomatik öneriler
- Performans analizi

## Kullanım

### Admin Panel Üzerinden
1. Admin paneline giriş yapın
2. "Cache Yönetimi" bölümüne gidin
3. İstediğiniz cache temizleme işlemini seçin

### Komut Satırından
```bash
php admin/admin-cache-cleaner.php
```

### Cron Job Kurulumu
Otomatik cache temizleme için cron job kurabilirsiniz:

```bash
# Her 5 dakikada bir çalıştır
0,5,10,15,20,25,30,35,40,45,50,55 * * * * /usr/bin/php /path/to/admin-cache-cleaner.php
```

## Cache Dosyaları

Cache dosyaları `cache/admin/` dizininde saklanır:
- Dosya formatı: `md5(key).cache`
- Her dosya timestamp ve data içerir
- Süresi dolmuş dosyalar otomatik temizlenir

## Cache Sağlık Durumu

### Skor Hesaplama
- **100%**: Mükemmel - Tüm cache dosyaları geçerli
- **80-99%**: İyi - Çoğu cache dosyası geçerli
- **60-79%**: Orta - Bazı cache dosyaları süresi dolmuş
- **40-59%**: Zayıf - Çok fazla süresi dolmuş dosya
- **0-39%**: Kötü - Cache sistemi optimize edilmeli

### Öneriler
Sistem otomatik olarak cache optimizasyon önerileri sunar:
- Süresi dolmuş dosya sayısı fazlaysa otomatik temizleme önerilir
- Cache boyutu büyükse cache süresini azaltma önerilir
- Çok fazla cache dosyası varsa cache stratejisi gözden geçirilmelidir

## Performans Optimizasyonu

### Cache Stratejisi
1. **Sık kullanılan veriler**: Dashboard istatistikleri, blog kategorileri
2. **Orta sıklıkta veriler**: Site içerikleri, portfolio projeleri
3. **Düşük sıklıkta veriler**: Mesajlar, kullanıcı bilgileri

### Önerilen Ayarlar
- Cache süresi: 5 dakika (300 saniye)
- Otomatik temizleme: Her 5 dakikada bir
- Cache boyutu limiti: 1MB

## Sorun Giderme

### Cache Temizleme Sorunları
1. Cache dizini yazma izinlerini kontrol edin
2. Disk alanını kontrol edin
3. PHP error loglarını kontrol edin

### Performans Sorunları
1. Cache sağlık durumunu kontrol edin
2. Cache süresini ayarlayın
3. Otomatik temizleme cron job'unu kurun

## Güvenlik

- Cache dosyaları sadece admin panelinden erişilebilir
- Cache temizleme işlemleri loglanır
- CSRF koruması mevcuttur

## Geliştirici Notları

### Cache Sınıfı Yapısı
```php
AdminCache - Temel cache işlemleri
AdminCachedData - Cache'lenmiş veri yönetimi
```

### Cache Anahtarları
- `dashboard_stats`: Dashboard istatistikleri
- `blog_categories`: Blog kategorileri
- `site_content_all`: Tüm site içerikleri
- `site_content_{page}`: Sayfa özel içerikleri

### Otomatik Cache Temizleme
İçerik güncellemelerinde ilgili cache otomatik temizlenir:
- Blog yazısı ekleme/güncelleme → Blog cache temizlenir
- Portfolio projesi ekleme/güncelleme → Portfolio cache temizlenir
- Site içeriği güncelleme → İçerik cache temizlenir

## Versiyon Geçmişi

- **v1.0**: Temel cache sistemi
- **v1.1**: Otomatik temizleme eklendi
- **v1.2**: Cache sağlık durumu eklendi
- **v1.3**: Cron job desteği eklendi
