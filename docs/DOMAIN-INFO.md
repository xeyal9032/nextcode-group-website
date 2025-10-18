# NextCode Domain Information

## 🌐 Production Domain

**Primary Domain:** https://nextcode.az

### Domain History

1. **Old Domain (Legacy):** https://nextcodegroup.ostwind.az
   - Status: Legacy/Redirecting
   - Used until: 2024-01

2. **Current Domain:** https://nextcode.az
   - Status: Active
   - Since: 2024-01
   - SSL: Active
   - CDN: Active

## 📧 Email Addresses

- **Primary:** info@nextcode.com
- **Support:** support@nextcode.com
- **Admin:** admin@nextcode.com

**Old Email (Legacy):** info@nextcodegroup.com

## 🔗 Important URLs

### Production URLs
```
Main Site:      https://nextcode.az
API Endpoint:   https://nextcode.az/api
API Docs:       https://nextcode.az/api-docs/
Admin Panel:    https://nextcode.az/admin/
Cache Monitor:  https://nextcode.az/monitoring/cache-monitor.php
```

### Development URLs
```
Local:          http://localhost:8000
API:            http://localhost:8000/api
```

## 🔐 DNS Configuration

### A Records
```
@ -> Server IP
www -> Server IP
```

### CNAME Records
```
api.nextcode.az -> nextcode.az
cdn.nextcode.az -> CDN provider
```

### MX Records
```
@ -> mail.nextcode.az (Priority 10)
```

### TXT Records
```
SPF: v=spf1 include:_spf.google.com ~all
DKIM: [Your DKIM record]
DMARC: v=DMARC1; p=quarantine; rua=mailto:postmaster@nextcode.az
```

## 🔒 SSL/TLS

- **Provider:** Let's Encrypt / Cloudflare
- **Type:** Wildcard (*.nextcode.az)
- **Validity:** Auto-renewal
- **Force HTTPS:** Enabled

## 📊 Analytics & Tracking

### Google Analytics
- **Property ID:** [Your GA4 ID]
- **Domain:** nextcode.az

### Google Search Console
- **Verified:** Yes
- **Sitemap:** https://nextcode.az/sitemap.xml

### Social Media

- **Facebook:** https://facebook.com/nextcodegroup
- **Instagram:** https://instagram.com/nextcodegroup
- **LinkedIn:** https://linkedin.com/company/nextcodegroup
- **Twitter:** https://twitter.com/nextcodegroup

## 🚀 Deployment

### FTP Details
```
Host: nextcode.az or IP
Port: 21 (FTP) / 22 (SFTP)
Username: [Your FTP username]
Password: [Your FTP password]
Root Directory: /public_html or /www
```

### SSH Access
```
Host: nextcode.az
Port: 22
User: [Your SSH username]
Key: [Your SSH key path]
```

## 📝 Nameservers

```
ns1.yourprovider.com
ns2.yourprovider.com
```

## 🔄 Domain Migration Checklist

When migrating from old to new domain:

- [x] Update all hardcoded URLs in codebase
- [x] Update manifest.json
- [x] Update API documentation
- [x] Update CI/CD workflows
- [x] Update README and docs
- [ ] Setup 301 redirects from old domain
- [ ] Update Google Analytics
- [ ] Update Google Search Console
- [ ] Update social media links
- [ ] Update email signatures
- [ ] Notify clients/users
- [ ] Update SSL certificates
- [ ] Test all functionality

## 🔧 Configuration Files to Update

When changing domain, update these files:
1. `manifest.json` - start_url, scope
2. `api-docs/openapi.yaml` - servers
3. `README.md` - all URLs
4. `.github/workflows/*.yml` - deployment URLs
5. `config/database.php` - allowed origins
6. `includes/header.php` - canonical URLs
7. All documentation in `/docs/`

## 🌍 CDN Configuration

If using CDN:
```
CDN URL: https://cdn.nextcode.az
Assets: /assets/, /images/, /css/, /js/
Cache TTL: 1 year for static assets
```

## 📱 Mobile App Deep Links

If applicable:
```
iOS: nextcode://
Android: nextcode://
Universal Links: https://nextcode.az/app/
```

## 🔍 SEO

### robots.txt
```
Location: https://nextcode.az/robots.txt
Status: Active
```

### sitemap.xml
```
Location: https://nextcode.az/sitemap.xml
Last Updated: Auto-generated
Submitted to: Google, Bing
```

## 📞 Contact

Domain-related inquiries:
- Email: admin@nextcode.com
- Phone: +994 XX XXX XX XX

---

**Last Updated:** 2024-01-15
**Managed By:** NextCode IT Team

