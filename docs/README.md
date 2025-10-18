# NextCode Group - Digital Marketing Agency

[![CI/CD Pipeline](https://github.com/nextcodegroup/web/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/nextcodegroup/web/actions/workflows/ci-cd.yml)
[![Code Coverage](https://codecov.io/gh/nextcodegroup/web/branch/main/graph/badge.svg)](https://codecov.io/gh/nextcodegroup/web)
[![Security Scan](https://github.com/nextcodegroup/web/workflows/security-scan/badge.svg)](https://github.com/nextcodegroup/web/actions)
[![Docker](https://img.shields.io/docker/automated/nextcodegroup/web.svg)](https://hub.docker.com/r/nextcodegroup/web)

Azərbaycan rəqəmsal marketinq agentliyi üçün modern web platforması. SEO, sosial media, brendinq və reklam kampaniyaları xidmətləri təklif edən peşəkar marketinq agentliyi.

## 🚀 Özellikler

- **Modern Web Teknolojileri**: PHP 8.2+, MySQL 8.0+, Bootstrap 5
- **Responsive Tasarım**: Mobile-first yaklaşım
- **Performance Optimizasyonu**: Critical CSS, lazy loading, CDN desteği
- **SEO Friendly**: Structured data, meta tags, sitemap
- **API Documentation**: Swagger/OpenAPI ile tam dokümantasyon
- **Unit Tests**: PHPUnit ile kapsamlı test coverage
- **Docker Support**: Containerization ve orchestration
- **CI/CD Pipeline**: GitHub Actions ile otomatik deployment
- **Monitoring**: Prometheus, Grafana ile monitoring ve alerting

## 📋 Gereksinimler

- PHP 8.2 veya üzeri
- MySQL 8.0 veya üzeri
- Nginx veya Apache
- Node.js 18+ (development için)
- Composer
- Docker (opsiyonel)

## 🛠️ Kurulum

### Geliştirme Ortamı

```bash
# Repository'yi klonlayın
git clone https://github.com/nextcodegroup/web.git
cd web

# Composer dependencies
composer install

# NPM dependencies
npm install

# Veritabanı kurulumu
mysql -u root -p < database/create_tables.sql

# Environment configuration
cp .env.example .env
# .env dosyasını düzenleyin

# Cache temizleme
php clear-cache.php

# Development server başlatma
php -S localhost:8000
```

### Docker ile Kurulum

```bash
# Docker Compose ile tüm servisleri başlatın
docker-compose up -d

# Veritabanı migration'ları
docker-compose exec web php setup.php

# Logları kontrol edin
docker-compose logs -f
```

## 🧪 Testler

```bash
# Unit tests
composer test

# Test coverage
composer test-coverage

# Code quality checks
composer quality

# Frontend tests
npm test

# E2E tests
npm run test:e2e
```

## 📚 API Dokümantasyonu

API dokümantasyonu `api-docs/` dizininde bulunmaktadır:

- **Swagger UI**: `http://localhost/api-docs/`
- **OpenAPI Spec**: `api-docs/swagger.json`

### API Endpoints

- `GET /api/blog` - Blog yazıları
- `GET /api/portfolio` - Portfolio projeleri
- `GET /api/services` - Hizmetler
- `POST /api/contact` - İletişim formu
- `GET /api/content` - Dinamik içerik
- `GET /api/analytics` - Analytics verileri

## 📊 Monitoring

### Grafana Dashboard
- URL: `http://localhost:3000`
- Username: `admin`
- Password: `admin_password_123`

### Prometheus Metrics
- URL: `http://localhost:9090`
- Application metrics: `http://localhost/api/metrics`

### Kibana Logs
- URL: `http://localhost:5601`

## 🚀 Deployment

### Staging Environment
```bash
# Staging'e deploy
git push origin develop
# GitHub Actions otomatik olarak staging'e deploy edecek
```

### Production Environment
```bash
# Production'a deploy
git push origin main
# GitHub Actions otomatik olarak production'a deploy edecek
```

### Manual Deployment
```bash
# Production server'a bağlan
ssh user@production-server

# Uygulamayı güncelle
cd /var/www/nextcode-production
git pull origin main
docker-compose pull
docker-compose up -d

# Cache temizle
docker-compose exec web php clear-cache.php
```

## 🔧 Development

### Code Standards
- **PHP**: PSR-12 standard
- **JavaScript**: ESLint + Standard config
- **CSS**: Stylelint + BEM methodology

### Git Workflow
```bash
# Feature branch oluştur
git checkout -b feature/new-feature

# Değişiklikleri commit et
git add .
git commit -m "feat: add new feature"

# Push et
git push origin feature/new-feature

# Pull request oluştur
```

### Database Migrations
```bash
# Yeni migration oluştur
php database/create_migration.php "add_new_table"

# Migration'ları çalıştır
php database/migrate.php
```

## 📁 Proje Yapısı

```
nextcode/
├── api/                    # API endpoints
├── api-docs/              # API documentation
├── assets/                # Static assets
├── backup/                # Backup scripts
├── config/                # Configuration files
├── css/                   # Stylesheets
├── database/              # Database files
├── docker/                # Docker configurations
├── images/                # Images
├── includes/              # PHP includes
├── js/                    # JavaScript files
├── monitoring/            # Monitoring configs
├── tests/                 # Test files
├── .github/               # GitHub Actions
├── docker-compose.yml     # Docker Compose
├── Dockerfile            # Docker image
└── README.md             # This file
```

## 🔒 Güvenlik

- **XSS Protection**: Input sanitization
- **SQL Injection**: PDO prepared statements
- **CSRF Protection**: Token validation
- **Security Headers**: Nginx configuration
- **Dependency Scanning**: Composer security checker

## 📈 Performance

- **Lighthouse Score**: 90+ (Performance, Accessibility, SEO)
- **Core Web Vitals**: Optimized
- **Caching**: Redis + Nginx
- **CDN**: Ready for implementation
- **Image Optimization**: WebP support

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit edin (`git commit -m 'Add amazing feature'`)
4. Push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

## 📞 İletişim

- **Website**: [https://nextcodegroup.com](https://nextcodegroup.com)
- **Email**: info@nextcodegroup.com
- **GitHub**: [@nextcodegroup](https://github.com/nextcodegroup)

## 🙏 Teşekkürler

- Bootstrap team
- FontAwesome
- PHP community
- Docker team
- GitHub Actions team
