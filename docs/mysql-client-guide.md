# NextCode MySQL Client - Kullanım Kılavuzu

## 📋 İçindekiler
1. [Genel Bakış](#genel-bakış)
2. [Temel MySQL Client](#temel-mysql-client)
3. [Advanced MySQL Client](#advanced-mysql-client)
4. [Güvenlik Özellikleri](#güvenlik-özellikleri)
5. [Performans İpuçları](#performans-ipuçları)
6. [Yedekleme ve Geri Yükleme](#yedekleme-ve-geri-yükleme)
7. [Sorun Giderme](#sorun-giderme)
8. [En İyi Uygulamalar](#en-iyi-uygulamalar)

## 🌟 Genel Bakış

NextCode MySQL Client, web tabanlı bir veritabanı yönetim aracıdır. İki farklı versiyonu bulunmaktadır:

- **Temel MySQL Client** (`mysql-client.php`) - Genel veritabanı işlemleri
- **Advanced MySQL Client** (`mysql-client-advanced.php`) - Gelişmiş özellikler ve optimizasyon

## 🔧 Temel MySQL Client

### Erişim
- **URL**: `/mysql-client.php`
- **Gereksinim**: Admin paneli girişi
- **Güvenlik**: CSRF koruması ve güvenli sorgu kontrolleri

### Temel Özellikler

#### 1. Bağlantı Testi
```php
// Otomatik olarak sayfa yüklendiğinde test edilir
- Veritabanı bağlantı durumu
- Host, database, kullanıcı bilgileri
- Bağlantı hızı ve durumu
```

#### 2. Tablo Yönetimi
```sql
-- Tüm tabloları listele
SHOW TABLES;

-- Tablo yapısını görüntüle
DESCRIBE table_name;

-- Tablo verilerini görüntüle
SELECT * FROM table_name LIMIT 50;
```

#### 3. SQL Sorgu Çalıştırıcı
- **Güvenlik**: Sadece SELECT sorgularına izin verilir
- **Engellenen Komutlar**: DROP, DELETE, TRUNCATE, ALTER, CREATE, INSERT, UPDATE
- **Hata Yönetimi**: Detaylı hata mesajları ve çözüm önerileri

### Kullanım Örnekleri

#### Blog Tablosu Sorguları
```sql
-- Son blog yazıları
SELECT id, title, status, created_at 
FROM blog_posts 
ORDER BY created_at DESC 
LIMIT 10;

-- Yayınlanmış blog sayısı
SELECT COUNT(*) as published_count 
FROM blog_posts 
WHERE status = 'published';

-- Kategori bazında blog sayıları
SELECT category, COUNT(*) as count 
FROM blog_posts 
GROUP BY category;
```

#### Portföy Tablosu Sorguları
```sql
-- Portföy projeleri
SELECT id, title, category_name, is_published 
FROM portfolio_projects 
ORDER BY sort_order;

-- Yayınlanmış projeler
SELECT * FROM portfolio_projects 
WHERE is_published = 1 
LIMIT 5;
```

#### İletişim Mesajları
```sql
-- Son mesajlar
SELECT name, email, subject, created_at 
FROM contact_messages 
ORDER BY created_at DESC 
LIMIT 10;

-- Okunmamış mesajlar
SELECT COUNT(*) as unread_count 
FROM contact_messages 
WHERE status = 'unread';
```

## 🚀 Advanced MySQL Client

### Erişim
- **URL**: `/mysql-client-advanced.php`
- **Gereksinim**: Admin paneli girişi
- **Özellikler**: Gelişmiş optimizasyon ve yönetim araçları

### Gelişmiş Özellikler

#### 1. Performans Optimizasyonu
```sql
-- Tablo optimizasyonu
OPTIMIZE TABLE table_name;

-- Tablo analizi
ANALYZE TABLE table_name;

-- Tablo bütünlük kontrolü
CHECK TABLE table_name;
```

#### 2. Sorgu Şablonları
- **Kullanıcı Sorguları**: Admin kullanıcıları ve roller
- **Blog Sorguları**: Blog yazıları ve kategoriler
- **Portföy Sorguları**: Projeler ve kategoriler
- **İletişim Sorguları**: Mesajlar ve istatistikler
- **Analitik Sorguları**: Sistem bilgileri

#### 3. Güvenlik Kontrolleri
```sql
-- Boş şifre kontrolü
SELECT user, host FROM mysql.user 
WHERE authentication_string = '';

-- Root uzaktan erişim kontrolü
SELECT user, host FROM mysql.user 
WHERE user = 'root' AND host != 'localhost';
```

#### 4. Sistem İzleme
```sql
-- MySQL sürümü
SELECT VERSION();

-- Veritabanı boyutu
SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) 
AS 'DB Size in MB' 
FROM information_schema.tables 
WHERE table_schema = DATABASE();

-- Aktif bağlantılar
SHOW PROCESSLIST;
```

## 🔒 Güvenlik Özellikleri

### 1. Erişim Kontrolü
- Admin paneli girişi gerekli
- Session tabanlı kimlik doğrulama
- CSRF token koruması

### 2. Sorgu Güvenliği
- Sadece SELECT sorgularına izin
- Tehlikeli komutların engellenmesi
- SQL injection koruması

### 3. Input Validasyonu
- Tüm girişlerin doğrulanması
- XSS koruması
- Güvenli veri işleme

### 4. Hata Yönetimi
- Hassas bilgilerin gizlenmesi
- Detaylı log kayıtları
- Güvenli hata mesajları

## ⚡ Performans İpuçları

### 1. İndeks Kullanımı
```sql
-- Sık kullanılan sütunlarda indeks oluştur
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_created_at ON blog_posts(created_at);
CREATE INDEX idx_category ON blog_posts(category);
```

### 2. Sorgu Optimizasyonu
```sql
-- LIMIT kullanarak sonuç sayısını sınırla
SELECT * FROM large_table LIMIT 100;

-- Sadece gerekli sütunları seç
SELECT id, name FROM users; -- * yerine

-- WHERE koşullarında indekslenmiş sütunları kullan
SELECT * FROM users WHERE email = 'test@example.com';
```

### 3. Tablo Optimizasyonu
```sql
-- Düzenli tablo optimizasyonu
OPTIMIZE TABLE blog_posts;
OPTIMIZE TABLE portfolio_projects;

-- İstatistik güncelleme
ANALYZE TABLE blog_posts;
```

## 💾 Yedekleme ve Geri Yükleme

### 1. Tam Yedekleme
```bash
# Tüm veritabanı
mysqldump -u username -p database_name > backup_full_2024-01-15.sql

# Belirli tablolar
mysqldump -u username -p database_name table1 table2 > backup_tables.sql
```

### 2. Yapı Yedekleme
```bash
# Sadece tablo yapıları
mysqldump -u username -p --no-data database_name > backup_structure.sql
```

### 3. Veri Yedekleme
```bash
# Sadece veriler
mysqldump -u username -p --no-create-info database_name > backup_data.sql
```

### 4. Geri Yükleme
```bash
# Tam geri yükleme
mysql -u username -p database_name < backup_file.sql

# Seçici geri yükleme
mysql -u username -p database_name -e "source backup_file.sql"
```

## 🔧 Sorun Giderme

### Yaygın Hatalar ve Çözümleri

#### 1. "Table doesn't exist" Hatası
```sql
-- Çözüm: Mevcut tabloları kontrol et
SHOW TABLES;

-- Tablo adını doğru yazdığınızdan emin olun
-- Büyük/küçük harf duyarlılığına dikkat edin
```

#### 2. "Access denied" Hatası
```bash
# Çözüm: Kullanıcı izinlerini kontrol et
mysql -u root -p -e "SHOW GRANTS FOR 'username'@'localhost';"

# Gerekli izinleri ver
GRANT SELECT, INSERT, UPDATE, DELETE ON database_name.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

#### 3. "Connection refused" Hatası
```bash
# Çözüm: MySQL servisinin çalıştığını kontrol et
sudo systemctl status mysql
sudo systemctl start mysql

# Port kontrolü
netstat -tlnp | grep 3306
```

#### 4. "Too many connections" Hatası
```sql
-- Çözüm: Bağlantı limitlerini kontrol et
SHOW VARIABLES LIKE 'max_connections';

-- Bağlantı sayısını artır (my.cnf'de)
max_connections = 200
```

## 📚 En İyi Uygulamalar

### 1. Güvenlik
- Güçlü şifreler kullanın
- Düzenli güvenlik güncellemeleri yapın
- Gereksiz kullanıcıları kaldırın
- Network erişimini sınırlayın

### 2. Performans
- Düzenli indeks analizi yapın
- Büyük tabloları optimize edin
- Gereksiz verileri temizleyin
- Query cache kullanın

### 3. Yedekleme
- Otomatik yedekleme sistemi kurun
- Farklı lokasyonlarda yedek saklayın
- Yedekleri düzenli test edin
- Recovery planı hazırlayın

### 4. İzleme
- Performans metriklerini takip edin
- Hata loglarını düzenli kontrol edin
- Kaynak kullanımını izleyin
- Uyarı sistemleri kurun

## 🔗 Faydalı Kaynaklar

### MySQL Dokümantasyonu
- [MySQL 8.0 Reference Manual](https://dev.mysql.com/doc/refman/8.0/en/)
- [MySQL Performance Tuning](https://dev.mysql.com/doc/refman/8.0/en/optimization.html)

### NextCode Projesi
- **Ana Sayfa**: `/`
- **Admin Paneli**: `/admin/`
- **MySQL Client**: `/mysql-client.php`
- **Advanced Client**: `/mysql-client-advanced.php`

### Destek
- **E-posta**: info@nextcodegroup.com
- **Telefon**: +380 97 258 00 00
- **Website**: https://nextcodegroup.ostwind.az

---

**NextCode Group** - Professional Database Management Solutions 🚀
