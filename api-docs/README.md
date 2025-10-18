# NextCode Group API Documentation

## 📖 Genel Bakış

NextCode Group API, modern RESTful prensiplere uygun olarak tasarlanmış, güvenli ve performanslı bir backend API'dir.

## 🚀 Hızlı Başlangıç

### Base URL
```
Production: https://nextcode.az/api
Development: http://localhost:8000/api
```

### Örnek İstek
```javascript
// Blog yazılarını getir
fetch('https://nextcodegroup.ostwind.az/api/blog.php?limit=5')
  .then(response => response.json())
  .then(data => console.log(data));
```

## 📚 API Endpoints

### 1. İletişim API
**Endpoint:** `POST /api/contact.php`

İletişim formu gönderimi için kullanılır.

**Request Body:**
```json
{
  "name": "Əli Məmmədov",
  "email": "ali@example.com",
  "phone": "+994501234567",
  "subject": "Web sitesi hakkında",
  "message": "Merhaba, web siteniz hakkında bilgi almak istiyorum."
}
```

**Response:**
```json
{
  "success": true,
  "message": "Mesajınız uğurla göndərildi.",
  "email_sent": true
}
```

**Validation Kuralları:**
- `name`: Zorunlu, min 2 karakter
- `email`: Zorunlu, geçerli email formatı
- `phone`: Opsiyonel, max 50 karakter
- `subject`: Opsiyonel, max 255 karakter
- `message`: Zorunlu, min 10 karakter

### 2. Blog API
**Endpoint:** `GET /api/blog.php`

Blog yazılarını listeler.

**Query Parameters:**
- `category` (string, opsiyonel): Kategori filtresi
- `limit` (integer, opsiyonel): Sayfa başına gösterilecek yazı sayısı (default: 10)
- `offset` (integer, opsiyonel): Başlangıç offset (default: 0)
- `search` (string, opsiyonel): Arama sorgusu

**Örnek İstek:**
```
GET /api/blog.php?category=web-development&limit=5
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Modern Web Development Trends 2024",
      "slug": "modern-web-development-trends-2024",
      "excerpt": "Discover the latest trends...",
      "featured_image": "https://example.com/image.jpg",
      "category": "Web Development",
      "published_at": "2024-01-15T10:00:00Z"
    }
  ],
  "total": 25,
  "page": 1
}
```

### 3. Portfolio API
**Endpoint:** `GET /api/portfolio.php`

Portfolio projelerini listeler.

**Query Parameters:**
- `action` (string, zorunlu): `get_projects` veya `get_project`
- `id` (integer, opsiyonel): Proje ID (get_project için gerekli)
- `category` (string, opsiyonel): Kategori filtresi
- `featured` (boolean, opsiyonel): Sadece öne çıkan projeler

**Örnek İstek:**
```
GET /api/portfolio.php?action=get_projects&featured=true
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "E-commerce Platform",
      "description": "Modern e-commerce solution",
      "category": "Web Development",
      "image_url": "https://example.com/project.jpg",
      "technologies": ["PHP", "MySQL", "Bootstrap"],
      "status": "completed",
      "is_featured": true
    }
  ]
}
```

### 4. Services API
**Endpoint:** `GET /api/services.php`

Hizmetleri listeler.

**Query Parameters:**
- `featured` (boolean, opsiyonel): Sadece öne çıkan hizmetler

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "SEO Optimizasyon",
      "description": "Arama motoru optimizasyonu",
      "icon": "fas fa-search",
      "features": ["Keyword research", "On-page SEO", "Link building"],
      "status": "active"
    }
  ]
}
```

### 5. Analytics API
**Endpoint:** `GET /api/analytics-api.php`

Site analitiklerini getirir.

**Query Parameters:**
- `action` (string, zorunlu): `page_views`, `top_pages`, `traffic_sources`
- `period` (string, opsiyonel): `today`, `week`, `month`, `year` (default: week)

**Örnek İstek:**
```
GET /api/analytics-api.php?action=page_views&period=week
```

## 🔐 Güvenlik

### CSRF Koruması
Form gönderimlerinde CSRF token kullanılır:

```javascript
fetch('/api/contact.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-Token': csrfToken
  },
  body: JSON.stringify(data)
});
```

### Rate Limiting
API endpoint'lerinde rate limiting uygulanır:
- Maximum 100 istek / dakika per IP

### Input Validation
Tüm input'lar server-side validate edilir ve sanitize edilir.

## 📊 HTTP Status Codes

| Status Code | Açıklama |
|------------|----------|
| 200 | Başarılı işlem |
| 400 | Validation hatası veya bad request |
| 404 | Kaynak bulunamadı |
| 405 | Method not allowed |
| 429 | Too many requests (rate limit) |
| 500 | Sunucu hatası |

## ⚠️ Error Handling

Tüm hatalar standardize edilmiş formatta döner:

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": [
    {
      "field": "email",
      "message": "Düzgün email ünvanı daxil edin"
    }
  ]
}
```

## 🔧 CORS

API, tüm origin'lerden gelen isteklere izin verir:
```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, OPTIONS
Access-Control-Allow-Headers: Content-Type
```

## 📖 OpenAPI Specification

API'nin tam OpenAPI 3.0 specification'ı `openapi.yaml` dosyasında bulunur.

**Swagger UI ile görüntüleme:**
```
https://nextcode.az/api-docs/
```

## 🧪 Testing

API endpoint'lerini test etmek için:

### cURL ile test
```bash
# Blog yazılarını getir
curl -X GET "https://nextcode.az/api/blog.php?limit=5"

# İletişim formu gönder
curl -X POST "https://nextcode.az/api/contact.php" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "message": "Test message"
  }'
```

### Postman Collection
Postman collection dosyası `postman_collection.json` içinde bulunur.

## 📝 Changelog

### v1.0.0 (2024-01-15)
- İlk API release
- İletişim, Blog, Portfolio, Services, Analytics endpoint'leri
- OpenAPI 3.0 documentation
- Rate limiting ve security headers

## 📞 Destek

API ile ilgili sorularınız için:
- Email: info@nextcode.com
- Website: https://nextcode.az
- GitHub Issues: [Link]

## 📄 Lisans

MIT License - Detaylar için LICENSE dosyasına bakın.

