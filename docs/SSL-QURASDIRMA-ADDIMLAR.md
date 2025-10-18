# 🚀 SSL Sertifikatı Quraşdırma - Addım-addım Təlimat

## 📋 Hazırlıq

### ✅ Tələblər
- Domain adı (məsələn: yourdomain.com)
- Hosting hesabı (cPanel və ya FTP giriş)
- Admin panel girişi
- Email ünvanı (domain doğrulama üçün)

### 📁 Hazır Fayllar
- ✅ `.htaccess` faylı SSL üçün hazırlanıb
- ✅ Security header-lər əlavə edilib
- ✅ HTTPS yönləndirməsi hazırdır (deaktiv)

---

## 🎯 Addım 1: Hosting Provider SSL Dəstəyini Yoxlayın

### cPanel Hosting
```
1. cPanel-ə daxil olun
2. "SSL/TLS" bölümünü tapın
3. "Let's Encrypt" və ya "AutoSSL" seçimini axtarın
4. Əgər varsa, Addım 2-yə keçin
5. Əgər yoxdursa, Addım 3-ə keçin
```

### Shared Hosting
```
1. Hosting provider-in control panel-ə daxil olun
2. "SSL Certificate" və ya "Security" bölümünü tapın
3. "Free SSL" seçimini axtarın
4. Mövcudluğunu yoxlayın
```

---

## 🆓 Addım 2: Ücretsiz SSL (Let's Encrypt) Quraşdırma

### cPanel AutoSSL
```
1. cPanel → SSL/TLS
2. "Manage AutoSSL" seçin
3. Domain-i seçin
4. "Run AutoSSL" düyməsini basın
5. 5-10 dəqiqə gözləyin
6. Status "Active" olana qədər yeniləyin
```

### Manual Let's Encrypt
```
1. cPanel → SSL/TLS
2. "Let's Encrypt SSL" seçin
3. Domain adını daxil edin:
   - yourdomain.com
   - www.yourdomain.com
4. Email ünvanını daxil edin
5. "Issue" düyməsini basın
6. Doğrulama prosesini gözləyin
```

### Cloudflare SSL (Asan Yol)
```
1. cloudflare.com-da hesab yaradın
2. Domain əlavə edin
3. DNS server-lərini Cloudflare-ə yönləndirin
4. SSL/TLS → "Full (strict)" seçin
5. "Always Use HTTPS" aktivləşdirin
6. 24 saat gözləyin (DNS propagation)
```

---

## 🔧 Addım 3: Manual SSL Sertifikat Quraşdırma

### SSL Sertifikat Alın
```
1. SSL provider seçin:
   - Let's Encrypt (ücretsiz)
   - ZeroSSL (ücretsiz)
   - Comodo/Sectigo (ödənişli)
   - DigiCert (ödənişli)

2. Domain validation tamamlayın
3. Sertifikat fayllarını yükləyin:
   - certificate.crt
   - private.key
   - ca_bundle.crt (intermediate)
```

### cPanel-də Manual Quraşdırma
```
1. cPanel → SSL/TLS
2. "Manage SSL sites" seçin
3. Domain seçin
4. Sertifikat fayllarını yapışdırın:
   - Certificate (CRT): certificate.crt məzmunu
   - Private Key (KEY): private.key məzmunu
   - Certificate Authority Bundle: ca_bundle.crt məzmunu
5. "Install Certificate" düyməsini basın
```

---

## ✅ Addım 4: SSL Aktivləşdirməni Yoxlayın

### Brauzer Testi
```
1. https://yourdomain.com ünvanını açın
2. URL yanında kilit işarəsini yoxlayın
3. "Connection is secure" mesajını təsdiqləyin
4. Sertifikat məlumatlarını yoxlayın
```

### Online Test
```
1. SSL Labs Test: https://www.ssllabs.com/ssltest/
2. Domain adınızı daxil edin
3. "Submit" düyməsini basın
4. A və ya A+ reytinqi alın
```

### Command Line Test
```bash
# SSL bağlantısını test edin
curl -I https://yourdomain.com

# Sertifikat məlumatları
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com
```

---

## 🔄 Addım 5: HTTPS Yönləndirməsini Aktivləşdirin

### .htaccess Faylını Yeniləyin

**Mövcud .htaccess faylında aşağıdakı sətirləri açın:**

```apache
# Bu sətirləri tapın və # işarələrini silin:

# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Belə olmalıdır:
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### www Yönləndirməsi (İstəyə bağlı)
```apache
# www-suz versiyaya yönləndirmə üçün:
RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^(.*)$ https://%1%{REQUEST_URI} [R=301,L]
```

### Test Edin
```
1. http://yourdomain.com açın
2. Avtomatik https://yourdomain.com-a yönləndirilməlidir
3. http://www.yourdomain.com açın
4. https://yourdomain.com-a yönləndirilməlidir
```

---

## 🛡️ Addım 6: Security Header-lərini Aktivləşdirin

### HSTS Aktivləşdirin
```apache
# .htaccess faylında bu sətri açın:
# Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"

# Belə olmalıdır:
Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
```

### Content Security Policy
```apache
# CSP header-ini də aktivləşdirin:
Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:; font-src 'self' https:; connect-src 'self' https:;"
```

---

## 🔍 Addım 7: Tam Test və Doğrulama

### SSL Test Checklist
```
✅ https://yourdomain.com açılır
✅ http://yourdomain.com https-ə yönləndirilir
✅ www.yourdomain.com düzgün yönləndirilir
✅ Brauzer kilit işarəsi göstərir
✅ SSL Labs A/A+ reytinqi
✅ Mixed content xətası yoxdur
✅ Bütün səhifələr HTTPS-də açılır
```

### Performance Test
```
1. PageSpeed Insights: https://pagespeed.web.dev/
2. GTmetrix: https://gtmetrix.com/
3. WebPageTest: https://www.webpagetest.org/
```

### Security Test
```
1. Security Headers: https://securityheaders.com/
2. SSL Labs: https://www.ssllabs.com/ssltest/
3. Mozilla Observatory: https://observatory.mozilla.org/
```

---

## 🚨 Problemlər və Həllər

### ❌ "Your connection is not private"
```
🔍 Səbəblər:
- SSL sertifikatı düzgün quraşdırılmayıb
- Domain adı uyğunsuzluğu
- Intermediate sertifikat əksikdir

✅ Həll:
1. SSL quraşdırmasını yenidən yoxlayın
2. Domain adının sertifikatda olduğunu təsdiqləyin
3. CA Bundle (intermediate) əlavə edin
```

### ❌ Mixed Content Xətaları
```
🔍 Səbəb: HTTP resurslar HTTPS səhifədə yüklənir

✅ Həll:
1. Bütün CSS/JS fayllarını HTTPS-ə çevirin
2. Şəkil URL-lərini yoxlayın
3. External API-ləri HTTPS-ə çevirin
```

### ❌ SSL Sertifikat Vaxtı Bitib
```
🔍 Səbəb: Sertifikat müddəti bitib

✅ Həll:
1. Yeni sertifikat əldə edin
2. Avtomatik yeniləmə quraşdırın
3. Monitoring quraşdırın
```

### ❌ Cloudflare SSL Loop
```
🔍 Səbəb: Cloudflare və server SSL konfiqurasiya uyğunsuzluğu

✅ Həll:
1. Cloudflare SSL: "Full (strict)" seçin
2. Server-də SSL sertifikatı quraşdırın
3. Origin sertifikatı istifadə edin
```

---

## 📅 Maintenance və Monitoring

### Avtomatik Yeniləmə
```
1. Let's Encrypt avtomatik yenilənir (90 gün)
2. Hosting provider AutoSSL aktivləşdirin
3. Cloudflare avtomatik idarə edir
```

### Monitoring Quraşdırın
```
1. SSL expiry monitoring:
   - SSL Labs monitoring
   - UptimeRobot SSL monitoring
   - Pingdom SSL checks

2. Email bildirişləri quraşdırın
3. Aylıq SSL test aparın
```

### Backup Plan
```
1. SSL sertifikat fayllarını backup edin
2. Konfiqurasiya fayllarını saxlayın
3. Emergency contact məlumatları hazırlayın
```

---

## 📞 Dəstək və Kömək

### Hosting Provider Dəstəyi
```
1. SSL quraşdırma problemi:
   - Hosting dəstək ticket açın
   - "SSL certificate installation" qeyd edin
   - Domain adını və xəta mesajını göndərin

2. Sual nümunəsi:
   "Salam, yourdomain.com üçün SSL sertifikatı quraşdırmaqda problem yaşayıram. 
   Let's Encrypt istifadə etmək istəyirəm. Kömək edə bilərsinizmi?"
```

### Community Dəstək
```
1. Let's Encrypt Community: https://community.letsencrypt.org/
2. Stack Overflow: SSL configuration tag
3. Reddit: r/webdev, r/sysadmin
```

### Professional Dəstək
```
1. SSL sertifikat provider dəstəyi
2. Web development agencies
3. System administrator xidmətləri
```

---

## ✅ Uğur Checklist

### SSL Quraşdırma Tamamlandı
```
✅ SSL sertifikatı quraşdırıldı
✅ HTTPS yönləndirməsi aktivdir
✅ Security header-lər konfiqurasiya edildi
✅ SSL Labs A/A+ reytinqi alındı
✅ Bütün səhifələr HTTPS-də işləyir
✅ Mixed content xətası yoxdur
✅ Performance test keçildi
✅ Monitoring quraşdırıldı
✅ Backup plan hazırlandı
```

### Növbəti Addımlar
```
1. 🔄 Aylıq SSL test
2. 📊 Performance monitoring
3. 🛡️ Security audit
4. 📱 Mobile optimization
5. 🚀 CDN konfiqurasiyası
```

---

*Bu təlimat SSL sertifikatı quraşdırması üçün addım-addım yol göstərir. Hər hosting environment fərqli ola bilər, ona görə də hosting provider-inizin sənədlərini də nəzərdən keçirin.*

**🎯 Məqsəd:** Tam təhlükəsiz HTTPS veb sayt əldə etmək
**⏱️ Müddət:** 30 dəqiqə - 2 saat (hosting provider-dən asılı)
**💰 Qiymət:** Ücretsiz (Let's Encrypt) - $300/il (Premium SSL)