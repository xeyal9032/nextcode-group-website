# 🔐 SSL Sertifikatı və HTTPS Konfiqurasiya Rehbəri

## 📋 Məzmun
1. [SSL Sertifikatı Nədir?](#ssl-sertifikatı-nədir)
2. [SSL Sertifikatı Növləri](#ssl-sertifikatı-növləri)
3. [Ücretsiz SSL Sertifikatı (Let's Encrypt)](#ücretsiz-ssl-sertifikatı-lets-encrypt)
4. [Hosting Provider SSL](#hosting-provider-ssl)
5. [Cloudflare SSL](#cloudflare-ssl)
6. [.htaccess HTTPS Yönləndirməsi](#htaccess-https-yönləndirməsi)
7. [SSL Test və Doğrulama](#ssl-test-və-doğrulama)
8. [Sorun Giderme](#sorun-giderme)

---

## 🔒 SSL Sertifikatı Nədir?

SSL (Secure Sockets Layer) sertifikatı, veb saytınız və istifadəçilər arasında təhlükəsiz bağlantı təmin edən rəqəmsal sertifikatdır.

### ✅ SSL-in Faydaları:
- **Məlumat Şifrələnməsi:** Bütün məlumatlar şifrələnir
- **Autentifikasiya:** Saytın həqiqiliyini təsdiqləyir
- **SEO Üstünlüyü:** Google HTTPS saytları üstün tutur
- **İstifadəçi Güvəni:** Brauzer "təhlükəsiz" işarəsi göstərir
- **PCI Compliance:** Ödəniş məlumatları üçün tələb olunur

---

## 📊 SSL Sertifikatı Növləri

### 1. 🆓 Domain Validated (DV)
- **Qiymət:** Ücretsiz - $100/il
- **Doğrulama:** Yalnız domain sahibliyi
- **Müddət:** 90 gün (Let's Encrypt) - 1 il
- **Uyğun:** Şəxsi saytlar, bloqlar

### 2. 🏢 Organization Validated (OV)
- **Qiymət:** $50 - $300/il
- **Doğrulama:** Domain + təşkilat məlumatları
- **Müddət:** 1-2 il
- **Uyğun:** Biznes saytları

### 3. 🏛️ Extended Validation (EV)
- **Qiymət:** $200 - $1000/il
- **Doğrulama:** Tam təşkilat yoxlaması
- **Müddət:** 1-2 il
- **Uyğun:** Bank, e-ticarət saytları

---

## 🆓 Ücretsiz SSL Sertifikatı (Let's Encrypt)

### Addım 1: Hosting Provider Dəstəyini Yoxlayın

Çox hosting providerləri Let's Encrypt-i avtomatik dəstəkləyir:

```bash
# cPanel-də SSL/TLS bölümünə gedin
# "Let's Encrypt" seçimini tapın
# Domain seçin və aktivləşdirin
```

### Addım 2: Manual Quraşdırma (Linux Server)

#### Certbot Quraşdırması:
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install certbot python3-certbot-apache

# CentOS/RHEL
sudo yum install certbot python3-certbot-apache
```

#### SSL Sertifikatı Əldə Etmək:
```bash
# Apache üçün
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Nginx üçün
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Yalnız sertifikat (manual konfiqurasiya)
sudo certbot certonly --webroot -w /var/www/html -d yourdomain.com
```

#### Avtomatik Yeniləmə:
```bash
# Crontab-a əlavə edin
sudo crontab -e

# Hər gün saat 2:30-da yoxlayın
30 2 * * * /usr/bin/certbot renew --quiet
```

### Addım 3: Sertifikat Faylları

Let's Encrypt sertifikatları `/etc/letsencrypt/live/yourdomain.com/` qovluğunda saxlanır:

```
cert.pem       # SSL sertifikatı
chain.pem      # Intermediate sertifikat
fullchain.pem  # Tam zəncir
privkey.pem    # Private key
```

---

## 🏢 Hosting Provider SSL

### Shared Hosting (cPanel)

#### Addım 1: cPanel-ə Daxil Olun
1. Hosting provider control panel-ə gedin
2. "SSL/TLS" bölümünü tapın
3. "Manage SSL sites" seçin

#### Addım 2: SSL Aktivləşdirin
```
1. Domain seçin
2. "AutoSSL" və ya "Let's Encrypt" seçin
3. "Install" düyməsini basın
4. 5-10 dəqiqə gözləyin
```

### VPS/Dedicated Server

#### Apache Konfiqurasiyası:
```apache
# /etc/apache2/sites-available/yourdomain-ssl.conf
<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/html
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourdomain.com/cert.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem
    SSLCertificateChainFile /etc/letsencrypt/live/yourdomain.com/chain.pem
    
    # Modern SSL konfiqurasiyası
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256
    SSLHonorCipherOrder off
    SSLSessionTickets off
</VirtualHost>
```

#### Nginx Konfiqurasiyası:
```nginx
# /etc/nginx/sites-available/yourdomain.com
server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/html;
    
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # Modern SSL konfiqurasiyası
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_prefer_server_ciphers off;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=63072000" always;
}
```

---

## ☁️ Cloudflare SSL

### Addım 1: Cloudflare Hesabı Yaradın
1. [cloudflare.com](https://cloudflare.com) saytına gedin
2. Hesab yaradın və domain əlavə edin
3. DNS ayarlarını Cloudflare-ə yönləndirin

### Addım 2: SSL Ayarları
```
1. Cloudflare dashboard-a gedin
2. "SSL/TLS" tab-ına keçin
3. "Full (strict)" seçin
4. "Edge Certificates" bölümündə:
   - "Always Use HTTPS" aktivləşdirin
   - "HTTP Strict Transport Security (HSTS)" aktivləşdirin
```

### Addım 3: Origin Sertifikatı
```
1. "Origin Server" tab-ına gedin
2. "Create Certificate" düyməsini basın
3. Domain adlarını daxil edin
4. Sertifikat və private key-i yükləyin
5. Server-də konfiqurasiya edin
```

---

## 🔄 .htaccess HTTPS Yönləndirməsi

### Mövcud .htaccess Faylını Yeniləyin

Faylınızda aşağıdakı kodları əlavə edin:

```apache
# HTTPS yönləndirməsi (SSL aktiv olduqdan sonra)
RewriteEngine On

# HTTP-dən HTTPS-ə yönləndirmə
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# www-suz versiyaya yönləndirmə (istəyə bağlı)
RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^(.*)$ https://%1%{REQUEST_URI} [R=301,L]
```

### Tam .htaccess Nümunəsi:

```apache
# Apache Configuration for Static Website with SSL

# Enable URL Rewriting
RewriteEngine On

# Force HTTPS (SSL aktiv olduqdan sonra açın)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Remove www (istəyə bağlı)
RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^(.*)$ https://%1%{REQUEST_URI} [R=301,L]

# Remove .html extension from URLs
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^.]+)$ $1.html [NC,L]

# Redirect .html URLs to clean URLs
RewriteCond %{THE_REQUEST} /([^.]+)\.html [NC]
RewriteRule ^ /%1? [NC,L,R=301]

# Security Headers
<IfModule mod_headers.c>
    # HSTS (HTTPS-dən sonra aktivləşdirin)
    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
    
    # Prevent clickjacking
    Header always append X-Frame-Options SAMEORIGIN
    
    # XSS Protection
    Header set X-XSS-Protection "1; mode=block"
    
    # Content Type Options
    Header set X-Content-Type-Options nosniff
    
    # Referrer Policy
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    
    # Content Security Policy
    Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:; font-src 'self' https:; connect-src 'self' https:;"
</IfModule>

# Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Browser Caching
<IfModule mod_expires.c>
    ExpiresActive on
    
    # Images
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
    
    # CSS and JavaScript
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/x-javascript "access plus 1 month"
    
    # Fonts
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType application/font-woff "access plus 1 year"
    ExpiresByType application/font-woff2 "access plus 1 year"
    
    # HTML
    ExpiresByType text/html "access plus 1 day"
</IfModule>

# Error Pages
ErrorDocument 404 /404.html
ErrorDocument 500 /500.html

# Deny access to sensitive files
<Files ".htaccess">
    Order allow,deny
    Deny from all
</Files>

<Files "*.log">
    Order allow,deny
    Deny from all
</Files>
```

---

## ✅ SSL Test və Doğrulama

### 1. Online SSL Test Alətləri

#### SSL Labs Test:
```
https://www.ssllabs.com/ssltest/
```
- A+ reytinqi alın
- Zəiflikləri müəyyən edin
- Konfiqurasiya tövsiyələri alın

#### Digər Test Alətləri:
```
# SSL Checker
https://www.sslchecker.com/

# SSL Shopper
https://www.sslshopper.com/ssl-checker.html

# DigiCert SSL Installation Checker
https://www.digicert.com/help/
```

### 2. Brauzer Yoxlaması

#### Chrome DevTools:
```
1. F12 basın
2. "Security" tab-ına gedin
3. "View certificate" düyməsini basın
4. Sertifikat məlumatlarını yoxlayın
```

#### Firefox:
```
1. URL yanındakı kilit işarəsinə basın
2. "Connection is secure" seçin
3. "More information" düyməsini basın
```

### 3. Command Line Test

#### OpenSSL ilə Test:
```bash
# Sertifikat məlumatlarını yoxlayın
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com

# Sertifikat tarixini yoxlayın
echo | openssl s_client -connect yourdomain.com:443 2>/dev/null | openssl x509 -noout -dates

# SSL protokollarını test edin
nmap --script ssl-enum-ciphers -p 443 yourdomain.com
```

#### cURL ilə Test:
```bash
# HTTPS bağlantısını test edin
curl -I https://yourdomain.com

# SSL sertifikat məlumatları
curl -vI https://yourdomain.com 2>&1 | grep -E '(SSL|TLS)'
```

---

## 🔧 Sorun Giderme

### ❌ Ümumi SSL Problemləri

#### 1. "Your connection is not private" Xətası

**Səbəblər:**
- SSL sertifikatı quraşdırılmayıb
- Sertifikat vaxtı bitib
- Domain adı uyğunsuzluğu
- Intermediate sertifikat yoxdur

**Həll:**
```bash
# Sertifikat statusunu yoxlayın
openssl x509 -in /path/to/certificate.crt -text -noout

# Sertifikat zəncirini yoxlayın
openssl verify -CAfile /path/to/ca-bundle.crt /path/to/certificate.crt
```

#### 2. Mixed Content Xətaları

**Səbəb:** HTTPS səhifədə HTTP resurslar yüklənir

**Həll:**
```html
<!-- Yanlış -->
<script src="http://example.com/script.js"></script>
<img src="http://example.com/image.jpg">

<!-- Düzgün -->
<script src="https://example.com/script.js"></script>
<img src="https://example.com/image.jpg">

<!-- Protocol-relative URLs -->
<script src="//example.com/script.js"></script>
```

#### 3. SSL Sertifikat Zəncir Problemi

**Səbəb:** Intermediate sertifikat əksikdir

**Həll:**
```bash
# Tam zənciri yoxlayın
openssl s_client -connect yourdomain.com:443 -showcerts

# Intermediate sertifikatı əlavə edin
cat certificate.crt intermediate.crt > fullchain.crt
```

### 🔄 Let's Encrypt Problemləri

#### 1. Rate Limit Xətası

**Səbəb:** Çox tez-tez sertifikat tələb edilib

**Həll:**
```bash
# Staging environment istifadə edin
certbot --staging --apache -d yourdomain.com

# Rate limit statusunu yoxlayın
https://crt.sh/?q=yourdomain.com
```

#### 2. Domain Validation Xətası

**Səbəb:** Domain DNS ayarları səhvdir

**Həll:**
```bash
# DNS yoxlaması
nslookup yourdomain.com
dig yourdomain.com

# Webroot yolunu yoxlayın
ls -la /var/www/html/.well-known/acme-challenge/
```

### 🏢 Hosting Provider Problemləri

#### 1. SSL Aktivləşmir

**Həll:**
1. Hosting provider dəstəyi ilə əlaqə saxlayın
2. DNS propagation gözləyin (24-48 saat)
3. Domain ownership doğrulayın

#### 2. Wildcard SSL Lazımdır

**Subdomain-lər üçün:**
```bash
# Let's Encrypt wildcard sertifikat
certbot certonly --manual --preferred-challenges=dns -d yourdomain.com -d *.yourdomain.com
```

---

## 📚 Əlavə Resurslar

### 📖 Faydalı Linklər
- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)
- [Mozilla SSL Configuration Generator](https://ssl-config.mozilla.org/)
- [SSL Labs Best Practices](https://github.com/ssllabs/research/wiki/SSL-and-TLS-Deployment-Best-Practices)
- [OWASP Transport Layer Protection](https://owasp.org/www-project-cheat-sheets/cheatsheets/Transport_Layer_Protection_Cheat_Sheet.html)

### 🛠️ Alətlər
- [Certbot](https://certbot.eff.org/) - Let's Encrypt client
- [acme.sh](https://acme.sh/) - Alternativ ACME client
- [SSL For Free](https://www.sslforfree.com/) - Ücretsiz SSL sertifikat
- [ZeroSSL](https://zerossl.com/) - Let's Encrypt alternativ

### 📞 Dəstək

SSL konfiqurasiyası ilə bağlı problemlər yaşayırsınızsa:

1. **Hosting Provider Dəstəyi:** İlk olaraq hosting provider-iniz ilə əlaqə saxlayın
2. **Community Forumlar:** Let's Encrypt community, Stack Overflow
3. **Professional Dəstək:** SSL sertifikat provider-lərin dəstək xidmətləri

---

## ⚠️ Vacib Qeydlər

### 🔐 Təhlükəsizlik
- Private key fayllarını heç vaxt paylaşmayın
- Sertifikat fayllarını backup edin
- Avtomatik yeniləmə quraşdırın
- SSL konfiqurasiyasını müntəzəm test edin

### 📅 Maintenance
- Sertifikat bitmə tarixlərini izləyin
- SSL test nəticələrini müntəzəm yoxlayın
- Yeni SSL protokollarını və cipher-ləri izləyin
- Security header-ləri yeniləyin

### 💡 Performance
- HTTP/2 aktivləşdirin
- OCSP Stapling konfiqurasiya edin
- Session resumption aktivləşdirin
- Perfect Forward Secrecy istifadə edin

---

*Bu rehbər SSL sertifikatı quraşdırması və HTTPS konfiqurasiyası üçün ətraflı təlimatlar təqdim edir. Hər hosting environment fərqli ola bilər, ona görə də öz hosting provider-inizin sənədlərini də nəzərdən keçirin.*