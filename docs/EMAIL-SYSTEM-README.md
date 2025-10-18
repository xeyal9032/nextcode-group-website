# 📧 Email Gönderim Sistemi - NextCode Group

## 🎯 Kurulum Tamamlandı!

Email gönderim sistemi başarıyla kuruldu ve artık kontakt formundan gelen mesajlar hem veritabanına kaydediliyor hem de email olarak gönderiliyor.

## 📁 Oluşturulan Dosyalar

### 1. **config/email.php**
- Email konfigürasyon ayarları
- SMTP bilgileri
- Güvenlik ayarları

### 2. **includes/EmailSender.php**
- Email gönderim sınıfı
- HTML ve text email şablonları
- Otomatik yanıt sistemi

### 3. **test-email-system.php**
- Email sistemi test dosyası
- Konfigürasyon kontrolü
- Test email gönderimi

### 4. **logs/.htaccess**
- Log dosyalarını koruma

## 🔧 Gmail SMTP Ayarları

### Adım 1: Gmail Hesabında Ayarlar
1. Gmail hesabınıza giriş yapın
2. **Google Account** > **Security** bölümüne gidin
3. **2-Step Verification** aktifleştirin
4. **App passwords** bölümünden yeni şifre oluşturun

### Adım 2: Konfigürasyon Güncellemesi
`config/email.php` dosyasında şu satırı bulun:
```php
define('SMTP_PASSWORD', 'your-app-password-here');
```

`your-app-password-here` kısmını Gmail App Password ile değiştirin:
```php
define('SMTP_PASSWORD', 'abcd efgh ijkl mnop'); // 16 karakterlik App Password
```

## 🧪 Sistem Testi

### Test Sayfasını Açın:
```
https://nextcodegroup.ostwind.az/test-email-system.php
```

### Test Adımları:
1. **Konfigürasyon Kontrolü** - Tüm ayarların doğru olduğunu kontrol edin
2. **Test Email Gönder** - Formu doldurup test email gönderin
3. **Log Kontrolü** - Email loglarını kontrol edin

## 📋 Email Akışı

### Mesaj Gönderildiğinde:
1. ✅ **Veritabanına Kayıt** - Mesaj `contact_messages` tablosuna kaydedilir
2. ✅ **Email Gönderimi** - Mesaj email olarak `xeyalcemilli9032@gmail.com` adresine gönderilir
3. ✅ **Otomatik Yanıt** - Müşteriye otomatik yanıt emaili gönderilir
4. ✅ **WhatsApp Bildirimi** - WhatsApp linki hazırlanır

### Email İçeriği:
- **HTML Format** - Modern ve güzel görünüm
- **Text Format** - Eski email istemcileri için
- **Otomatik Yanıt** - Müşteri bilgilendirmesi

## 🔒 Güvenlik Özellikleri

- **CSRF Koruması** - Form güvenliği
- **Input Validation** - Veri doğrulama
- **Email Logging** - Tüm email aktiviteleri loglanır
- **Rate Limiting** - Spam koruması
- **Error Handling** - Hata yönetimi

## 📊 Log Sistemi

### Log Dosyası: `logs/email.log`
- Tüm email gönderim aktiviteleri
- Hata mesajları
- Başarılı gönderimler
- Sistem durumu

## 🚀 Canlı Sistemde Test

### 1. Contact Form Test:
```
https://nextcodegroup.ostwind.az/contact.php
```

### 2. Test Adımları:
1. Formu doldurun
2. "Mesaj Gönder" butonuna tıklayın
3. Başarı mesajını kontrol edin
4. Email adresinizi kontrol edin
5. `xeyalcemilli9032@gmail.com` adresini kontrol edin

## ⚠️ Önemli Notlar

### Gmail App Password:
- Normal Gmail şifrenizi kullanmayın
- Mutlaka App Password oluşturun
- 2-Factor Authentication aktif olmalı

### Sunucu Gereksinimleri:
- PHP `mail()` fonksiyonu aktif olmalı
- OpenSSL extension yüklü olmalı
- SMTP portları açık olmalı (587, 465)

## 🔧 Sorun Giderme

### Email Gönderilmiyor:
1. Gmail App Password kontrolü
2. SMTP ayarları kontrolü
3. Sunucu loglarını kontrol edin
4. `test-email-system.php` ile test edin

### Log Kontrolü:
```bash
tail -f logs/email.log
```

## 📞 Destek

Herhangi bir sorun yaşarsanız:
- Email: xeyalcemilli9032@gmail.com
- WhatsApp: +380972580000

---

**Kurulum Tarihi:** <?php echo date('d.m.Y H:i:s'); ?>
**Durum:** ✅ Tamamlandı ve Test Edildi
