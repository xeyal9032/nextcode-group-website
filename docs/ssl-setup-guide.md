# 🔒 SSL Certificate Setup Guide

## NextCode.az SSL/TLS Configuration

Bu guide, Let's Encrypt ile wildcard SSL sertifikası kurulumunu ve yapılandırmasını açıklar.

---

## 📋 İçindekiler

- [Let's Encrypt Kurulumu](#lets-encrypt-kurulumu)
- [Wildcard Certificate](#wildcard-certificate)
- [Certbot Kullanımı](#certbot-kullanımı)
- [Otomatik Yenileme](#otomatik-yenileme)
- [Apache Konfigürasyonu](#apache-konfigürasyonu)
- [Nginx Konfigürasyonu](#nginx-konfigürasyonu)
- [SSL Test](#ssl-test)

---

## 🚀 Let's Encrypt Kurulumu

### 1. Certbot Kurulumu (Ubuntu/Debian)

```bash
# Certbot repository ekle
sudo apt update
sudo apt install certbot

# Apache için
sudo apt install python3-certbot-apache

# Nginx için
sudo apt install python3-certbot-nginx

# DNS plugin (Wildcard için gerekli)
sudo apt install python3-certbot-dns-cloudflare
```

### 2. Certbot Kurulumu (CentOS/RHEL)

```bash
sudo yum install certbot

# Apache için
sudo yum install python3-certbot-apache

# Nginx için
sudo yum install python3-certbot-nginx
```

---

## 🌟 Wildcard Certificate

Wildcard certificate, tüm subdomain'leri (*.nextcode.az) kapsayan bir sertifikadır.

### DNS Challenge Method

Wildcard sertifika için DNS challenge kullanılmalıdır.

#### Cloudflare API Setup

1. **Cloudflare API Token Oluştur:**
   - Cloudflare Dashboard > My Profile > API Tokens
   - "Create Token" > "Edit zone DNS" template
   - Zone Resources: Include > Specific zone > nextcode.az
   - Token'ı kaydet

2. **Credentials dosyası oluştur:**

```bash
sudo mkdir -p /etc/letsencrypt
sudo nano /etc/letsencrypt/cloudflare.ini
```

İçeriği:
```ini
# Cloudflare API token
dns_cloudflare_api_token = YOUR_API_TOKEN_HERE
```

Güvenlik:
```bash
sudo chmod 600 /etc/letsencrypt/cloudflare.ini
```

#### Wildcard Certificate Al

```bash
sudo certbot certonly \
  --dns-cloudflare \
  --dns-cloudflare-credentials /etc/letsencrypt/cloudflare.ini \
  -d nextcode.az \
  -d *.nextcode.az \
  --email admin@nextcode.com \
  --agree-tos \
  --non-interactive
```

### Başarılı Kurulum Mesajı

```
IMPORTANT NOTES:
 - Congratulations! Your certificate and chain have been saved at:
   /etc/letsencrypt/live/nextcode.az/fullchain.pem
   Your key file has been saved at:
   /etc/letsencrypt/live/nextcode.az/privkey.pem
```

---

## 🔧 Certbot Kullanımı

### Standart Certificate (Non-Wildcard)

Apache ile:
```bash
sudo certbot --apache -d nextcode.az -d www.nextcode.az
```

Nginx ile:
```bash
sudo certbot --nginx -d nextcode.az -d www.nextcode.az
```

### Manuel Certificate

```bash
sudo certbot certonly --manual -d nextcode.az -d www.nextcode.az
```

### Certificate Listele

```bash
sudo certbot certificates
```

### Certificate Yenile

```bash
sudo certbot renew
```

### Belirli Domain'i Yenile

```bash
sudo certbot renew --cert-name nextcode.az
```

---

## ⏰ Otomatik Yenileme

Let's Encrypt sertifikaları 90 günde bir yenilenmeli.

### Cron Job Oluştur

```bash
sudo crontab -e
```

Ekle:
```cron
# Let's Encrypt auto-renewal (Her gün 03:00'de kontrol et)
0 3 * * * certbot renew --quiet --post-hook "systemctl reload apache2"

# veya Nginx için
0 3 * * * certbot renew --quiet --post-hook "systemctl reload nginx"
```

### Systemd Timer (Modern Yöntem)

Certbot otomatik olarak systemd timer oluşturur:

```bash
# Timer durumunu kontrol et
sudo systemctl status certbot.timer

# Timer'ı etkinleştir
sudo systemctl enable certbot.timer

# Timer'ı başlat
sudo systemctl start certbot.timer
```

### Test Renewal

```bash
sudo certbot renew --dry-run
```

---

## 🔨 Apache Konfigürasyonu

### SSL Virtual Host

```apache
<VirtualHost *:443>
    ServerName nextcode.az
    ServerAlias www.nextcode.az
    
    DocumentRoot /var/www/nextcode
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/nextcode.az/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/nextcode.az/privkey.pem
    
    # Modern SSL Configuration
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384
    SSLHonorCipherOrder off
    SSLSessionTickets off
    
    # HSTS (mod_headers required)
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
    
    # OCSP Stapling
    SSLUseStapling on
    SSLStaplingCache "shmcb:logs/stapling-cache(150000)"
    
    <Directory /var/www/nextcode>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/nextcode-error.log
    CustomLog ${APACHE_LOG_DIR}/nextcode-access.log combined
</VirtualHost>

# HTTP to HTTPS Redirect
<VirtualHost *:80>
    ServerName nextcode.az
    ServerAlias www.nextcode.az
    
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>
```

### Apache Modules Aktifleştir

```bash
sudo a2enmod ssl
sudo a2enmod headers
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## 🔧 Nginx Konfigürasyonu

### SSL Server Block

```nginx
# HTTPS Server
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    
    server_name nextcode.az www.nextcode.az;
    root /var/www/nextcode;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/nextcode.az/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/nextcode.az/privkey.pem;
    
    # SSL Settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # SSL Session
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_session_tickets off;
    
    # OCSP Stapling
    ssl_stapling on;
    ssl_stapling_verify on;
    resolver 8.8.8.8 8.8.4.4 valid=300s;
    resolver_timeout 5s;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    access_log /var/log/nginx/nextcode-access.log;
    error_log /var/log/nginx/nextcode-error.log;
}

# HTTP to HTTPS Redirect
server {
    listen 80;
    listen [::]:80;
    
    server_name nextcode.az www.nextcode.az;
    
    return 301 https://$server_name$request_uri;
}
```

### Nginx Test ve Restart

```bash
sudo nginx -t
sudo systemctl restart nginx
```

---

## 🧪 SSL Test

### Online Tools

1. **SSL Labs:**
   ```
   https://www.ssllabs.com/ssltest/analyze.html?d=nextcode.az
   ```
   - Hedef: A+ rating

2. **Mozilla Observatory:**
   ```
   https://observatory.mozilla.org/analyze/nextcode.az
   ```

3. **Security Headers:**
   ```
   https://securityheaders.com/?q=nextcode.az
   ```

### Command Line Test

```bash
# SSL certificate bilgisi
echo | openssl s_client -servername nextcode.az -connect nextcode.az:443 2>/dev/null | openssl x509 -noout -dates

# SSL cipher test
nmap --script ssl-enum-ciphers -p 443 nextcode.az

# HSTS test
curl -I https://nextcode.az | grep -i strict
```

---

## 📊 SSL Certificate Bilgileri

### Certificate Dosyaları

```
/etc/letsencrypt/live/nextcode.az/
├── cert.pem           # Certificate only
├── chain.pem          # Intermediate certificates
├── fullchain.pem      # cert.pem + chain.pem (Apache/Nginx için)
└── privkey.pem        # Private key
```

### Certificate Detayları Görüntüle

```bash
sudo openssl x509 -in /etc/letsencrypt/live/nextcode.az/cert.pem -text -noout
```

---

## 🔄 Certificate Yenileme Süreci

1. **Otomatik Yenileme:** 60 gün kaldığında certbot otomatik yeniler
2. **Web Server Restart:** Yeni certificate için restart gerekir
3. **Notification:** Email ile bildirim gelir

---

## 🚨 Troubleshooting

### Problem: Certificate yenilenemedi

```bash
# Log kontrol et
sudo tail -f /var/log/letsencrypt/letsencrypt.log

# Manuel yenile
sudo certbot renew --force-renewal
```

### Problem: Port 80/443 kullanımda

```bash
# Port kullanımını kontrol et
sudo netstat -tulpn | grep :80
sudo netstat -tulpn | grep :443

# Standalone mode kullan
sudo certbot certonly --standalone -d nextcode.az
```

### Problem: DNS challenge başarısız

```bash
# DNS propagation kontrol et
dig TXT _acme-challenge.nextcode.az

# API token kontrol et
cat /etc/letsencrypt/cloudflare.ini
```

---

## ✅ SSL Setup Checklist

- [ ] Certbot kuruldu
- [ ] DNS plugin kuruldu (wildcard için)
- [ ] Cloudflare API token oluşturuldu
- [ ] Wildcard certificate alındı
- [ ] Apache/Nginx yapılandırıldı
- [ ] HTTP to HTTPS redirect aktif
- [ ] HSTS header eklendi
- [ ] SSL Labs test yapıldı (A+ hedef)
- [ ] Otomatik yenileme yapılandırıldı
- [ ] Cron job/systemd timer test edildi
- [ ] Security headers kontrol edildi

---

## 📞 Destek

SSL kurulumunda sorun yaşarsanız:
- Email: admin@nextcode.com
- Let's Encrypt Community: https://community.letsencrypt.org/

---

**Last Updated:** 2024-01-15  
**Certificate Provider:** Let's Encrypt  
**Validity:** 90 days (auto-renewal)


