# 🚀 NextCode AJAX System

Modern, güvenli ve kullanımı kolay AJAX sistemi dokümantasyonu.

## 📋 İçindekiler

- [Kurulum](#kurulum)
- [Temel Kullanım](#temel-kullanım)
- [Özellikler](#özellikler)
- [API Routes](#api-routes)
- [Örnekler](#örnekler)
- [Güvenlik](#güvenlik)

## 🔧 Kurulum

### 1. Header'a Script Ekleyin

```html
<!-- AJAX Handler -->
<script src="/js/ajax-handler.js"></script>
```

### 2. CSRF Token Meta Tag

```html
<meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
```

### 3. PHP Backend

Backend handler zaten `api/ajax-handler.php` dosyasında hazır.

## 🎯 Temel Kullanım

### GET İsteği

```javascript
// Basit GET
const result = await ajax.get('ajax-handler.php?action=blog_posts');

// Parametreli GET
const result = await ajax.get('ajax-handler.php', {
    params: {
        action: 'blog_posts',
        limit: 10,
        category: 'web-development'
    }
});
```

### POST İsteği

```javascript
const result = await ajax.post('ajax-handler.php', {
    action: 'contact_form',
    name: 'Əli Məmmədov',
    email: 'ali@example.com',
    message: 'Test mesaj'
});

if (result.success) {
    console.log('Başarılı:', result.data);
}
```

### Form Submit

```javascript
// HTML Form
<form id="contact-form">
    <input name="name" required>
    <input name="email" type="email" required>
    <textarea name="message" required></textarea>
    <button type="submit">Gönder</button>
</form>

// JavaScript
const form = document.getElementById('contact-form');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const result = await ajax.submitForm(form, {
        url: 'ajax-handler.php?action=contact_form',
        successMessage: 'Mesajınız gönderildi!',
        onSuccess: (data) => {
            // Başarı sonrası işlemler
        }
    });
});
```

## ⚡ Özellikler

### 1. Otomatik Loading Indicator

AJAX istekleri sırasında otomatik loading bar gösterir:

```javascript
// Otomatik olarak çalışır
await ajax.get('...');
// Loading bar otomatik kapanır
```

### 2. Notification Sistemi

Başarı ve hata mesajları otomatik gösterilir:

```javascript
ajax.showNotification('success', 'İşlem başarılı!');
ajax.showNotification('error', 'Hata oluştu!');
ajax.showNotification('info', 'Bilgi mesajı');
```

### 3. CSRF Koruması

Tüm POST/PUT/DELETE istekleri otomatik CSRF token ile korunur.

### 4. Error Handling

```javascript
try {
    const result = await ajax.post('...');
} catch (error) {
    console.error('İstek başarısız:', error);
}

// Veya global error handler
const ajax = new AjaxHandler({
    onError: (error) => {
        console.error('Global error:', error);
    }
});
```

### 5. Timeout Kontrolü

```javascript
const ajax = new AjaxHandler({
    timeout: 30000 // 30 saniye
});
```

### 6. Retry Mekanizması

```javascript
await ajax.retry(async () => {
    return await ajax.get('unstable-endpoint.php');
}, 3, 1000); // 3 deneme, 1 saniye aralık
```

## 🛣️ API Routes

Backend'de kayıtlı route'lar:

### GET Routes

| Action | Parametreler | Açıklama |
|--------|-------------|----------|
| `blog_posts` | `limit`, `offset`, `category` | Blog yazılarını listele |
| `portfolio_projects` | `category`, `featured` | Portfolio projelerini listele |
| `search` | `q`, `type` | Site içi arama |

### POST Routes

| Action | Parametreler | Açıklama |
|--------|-------------|----------|
| `contact_form` | `name`, `email`, `phone`, `message` | İletişim formu |
| `newsletter_subscribe` | `email` | Newsletter aboneliği |

### PUT Routes

| Action | Parametreler | Açıklama |
|--------|-------------|----------|
| `update_profile` | `name`, `email` | Profil güncelleme |

### DELETE Routes

| Action | Parametreler | Açıklama |
|--------|-------------|----------|
| `delete_item` | `table`, `id` | Kayıt silme |

## 📝 Örnekler

### 1. Real-Time Search

```javascript
const searchInput = document.getElementById('search');

let timeout;
searchInput.addEventListener('input', (e) => {
    clearTimeout(timeout);
    
    timeout = setTimeout(async () => {
        const query = e.target.value;
        
        if (query.length < 3) return;
        
        const result = await ajax.get('ajax-handler.php', {
            params: { action: 'search', q: query }
        });
        
        displaySearchResults(result.data);
    }, 300);
});
```

### 2. Infinite Scroll

```javascript
let page = 0;

window.addEventListener('scroll', async () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
        page++;
        
        const result = await ajax.get('ajax-handler.php', {
            params: {
                action: 'blog_posts',
                offset: page * 10
            }
        });
        
        appendPosts(result.data);
    }
});
```

### 3. File Upload

```javascript
const fileInput = document.getElementById('file-input');

fileInput.addEventListener('change', async (e) => {
    const file = e.target.files[0];
    
    const result = await ajax.uploadFile(
        'ajax-handler.php?action=upload',
        file,
        {
            onProgress: (percent) => {
                updateProgressBar(percent);
            }
        }
    );
    
    if (result.success) {
        console.log('Yüklendi:', result.data.url);
    }
});
```

### 4. Live Validation

```javascript
const emailInput = document.getElementById('email');

emailInput.addEventListener('blur', async () => {
    const email = emailInput.value;
    
    const result = await ajax.get('ajax-handler.php', {
        params: {
            action: 'check_email',
            email: email
        }
    });
    
    if (result.data.exists) {
        showError('Bu email zaten kullanılıyor');
    }
});
```

### 5. Auto-Save

```javascript
const form = document.getElementById('editor-form');

let timeout;
form.addEventListener('input', () => {
    clearTimeout(timeout);
    
    timeout = setTimeout(async () => {
        const formData = new FormData(form);
        
        await ajax.post('ajax-handler.php', {
            action: 'auto_save',
            ...Object.fromEntries(formData)
        });
        
        showSaveIndicator('Kaydedildi ✓');
    }, 2000);
});
```

### 6. Polling

```javascript
// Her 5 saniyede bir güncelleme kontrolü
setInterval(async () => {
    const result = await ajax.get('ajax-handler.php', {
        params: { action: 'get_notifications' }
    });
    
    updateNotificationBadge(result.data.count);
}, 5000);
```

### 7. Batch Requests

```javascript
async function loadDashboard() {
    const [stats, posts, messages] = await Promise.all([
        ajax.get('ajax-handler.php?action=get_stats'),
        ajax.get('ajax-handler.php?action=recent_posts'),
        ajax.get('ajax-handler.php?action=get_messages')
    ]);
    
    renderDashboard({ stats, posts, messages });
}
```

## 🔒 Güvenlik

### CSRF Protection

Tüm non-GET istekler otomatik CSRF token ile korunur:

```php
// Backend'de otomatik kontrol
if (!validateCSRF()) {
    ajaxError('CSRF validation failed', 403);
}
```

### Input Sanitization

Backend'de tüm input'lar sanitize edilir:

```php
$name = sanitizeInput(getInput('name'));
```

### XSS Protection

HTML output'ları escape edilir:

```php
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

### Rate Limiting

Backend'e rate limiting eklenebilir:

```php
// Rate limiter örneği (eklenecek)
if (!checkRateLimit($_SERVER['REMOTE_ADDR'], 60, 60)) {
    ajaxError('Too many requests', 429);
}
```

## 🎨 Customization

### Custom Loading Indicator

```javascript
const ajax = new AjaxHandler();

// Override loading methods
ajax.showLoadingIndicator = function() {
    document.getElementById('my-loader').classList.add('show');
};

ajax.hideLoadingIndicator = function() {
    document.getElementById('my-loader').classList.remove('show');
};
```

### Custom Error Handler

```javascript
const ajax = new AjaxHandler({
    onError: (error) => {
        // Custom error handling
        Sentry.captureException(error);
        showCustomErrorModal(error.message);
    }
});
```

### Custom Notification

```javascript
ajax.showNotification = function(type, message) {
    // Use your notification library
    toastr[type](message);
};
```

## 📊 Debugging

### Console Logging

```javascript
// Enable debug mode
window.AJAX_DEBUG = true;

// AJAX Handler otomatik log yapacak
const result = await ajax.get('...');
// Console: "GET /api/ajax-handler.php - 200 OK - 123ms"
```

### Network Tab

Chrome DevTools > Network tab'da:
- XHR filter ile AJAX isteklerini görün
- Headers, Response, Timing bilgilerini inceleyin

## 🚀 Performance Tips

### 1. Debouncing

```javascript
// Search için debounce kullanın
let timeout;
searchInput.addEventListener('input', (e) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => search(e.target.value), 300);
});
```

### 2. Caching

```javascript
// Cache sonuçları
const cache = new Map();

async function getData(key) {
    if (cache.has(key)) {
        return cache.get(key);
    }
    
    const result = await ajax.get(`...?key=${key}`);
    cache.set(key, result.data);
    
    return result.data;
}
```

### 3. Request Cancellation

```javascript
let controller;

async function search(query) {
    // Önceki isteği iptal et
    if (controller) {
        controller.abort();
    }
    
    controller = new AbortController();
    
    try {
        const result = await fetch('...', {
            signal: controller.signal
        });
        // ...
    } catch (err) {
        if (err.name === 'AbortError') {
            console.log('Request cancelled');
        }
    }
}
```

## 🧪 Testing

### Manual Testing

```javascript
// Console'da test edin
await ajax.get('ajax-handler.php?action=blog_posts');
await ajax.post('ajax-handler.php', { action: 'test' });
```

### Unit Testing

```javascript
// Jest örneği
test('AJAX handler sends correct data', async () => {
    const result = await ajax.post('test-endpoint', {
        name: 'Test'
    });
    
    expect(result.success).toBe(true);
});
```

## 📚 İleri Seviye

### WebSocket Integration

```javascript
// AJAX fallback ile WebSocket
class RealtimeConnection {
    constructor() {
        this.ws = null;
        this.connect();
    }
    
    connect() {
        try {
            this.ws = new WebSocket('wss://...');
        } catch (error) {
            // Fallback to AJAX polling
            this.startPolling();
        }
    }
    
    startPolling() {
        setInterval(async () => {
            const result = await ajax.get('ajax-handler.php?action=updates');
            this.handleUpdate(result.data);
        }, 5000);
    }
}
```

## 🤝 Destek

Sorularınız için:
- Email: info@nextcode.com
- GitHub Issues
- Dokümantasyon: `/docs/`

---

**NextCode AJAX System** - Modern, Güvenli, Hızlı 🚀

