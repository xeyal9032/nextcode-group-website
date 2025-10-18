# Progressive Web App (PWA) Guide

## 🚀 NextCode Group PWA Özellikleri

Bu döküman, NextCode Group web sitesinin Progressive Web App özelliklerini açıklar.

## 📋 İçindekiler

- [PWA Nedir?](#pwa-nedir)
- [Özellikler](#özellikler)
- [Kurulum](#kurulum)
- [Kullanım](#kullanım)
- [Offline Desteği](#offline-desteği)
- [Service Worker](#service-worker)
- [Cache Stratejisi](#cache-stratejisi)
- [Push Notifications](#push-notifications)

## 🌟 PWA Nedir?

Progressive Web App (PWA), modern web teknolojileri kullanarak native app deneyimi sunan web uygulamalarıdır.

### Avantajları

- ✅ Offline çalışma
- ✅ Ana ekrana eklenebilir
- ✅ Hızlı yükleme
- ✅ Push bildirimleri
- ✅ App-like deneyim
- ✅ Otomatik güncelleme

## ⚡ Özellikler

### 1. Service Worker
- Offline cache yönetimi
- Background sync
- Push notifications
- Otomatik güncelleme

### 2. App Manifest
- Uygulama meta verileri
- İkonlar ve temalar
- Display mode (standalone)
- Shortcuts

### 3. Offline Support
- Statik asset caching
- Dynamic content caching
- Offline fallback page
- Image caching

### 4. Install Prompt
- Install button
- Custom install UI
- Installation tracking
- Success messages

## 🔧 Kurulum

### Dosya Yapısı

```
/
├── sw.js                    # Service Worker
├── manifest.json            # App Manifest
├── offline.html             # Offline sayfası
├── js/pwa-installer.js      # PWA installer
└── images/icons/            # App ikonları
    ├── icon-72x72.png
    ├── icon-96x96.png
    ├── icon-128x128.png
    ├── icon-144x144.png
    ├── icon-152x152.png
    ├── icon-192x192.png
    ├── icon-384x384.png
    └── icon-512x512.png
```

### Header'a Eklemeler

```html
<!-- PWA Manifest -->
<link rel="manifest" href="/manifest.json">

<!-- Theme Color -->
<meta name="theme-color" content="#667eea">

<!-- Apple Touch Icon -->
<link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">

<!-- PWA Installer Script -->
<script src="/js/pwa-installer.js"></script>
```

### Service Worker Kaydı

Service Worker otomatik olarak `pwa-installer.js` tarafından kaydedilir.

## 📱 Kullanım

### Kullanıcı Perspektifinden

1. **Web sitesini ziyaret et**
2. **"Uygulamayı Yükle" butonuna tıkla**
3. **Install prompt'u onayla**
4. **App ana ekrana eklenir**

### Geliştirici Perspektifinden

```javascript
// PWA Installer başlatma
const pwa = new PWAInstaller();

// Manuel install prompt
pwa.promptInstall();

// Install durumunu kontrol et
const isInstalled = pwa.checkInstallStatus();

// Service Worker'ı güncelle
navigator.serviceWorker.getRegistration().then(reg => {
    reg.update();
});
```

## 🔌 Offline Desteği

### Cache Stratejileri

#### 1. Cache First (Static Assets)
```javascript
// CSS, JS, images için
// Cache'den al, yoksa network'den
```

#### 2. Network First (Dynamic Content)
```javascript
// API calls, HTML pages için
// Network'den al, başarısızsa cache'den
```

#### 3. Image Caching
```javascript
// Görseller için özel cache
// Limit: 50 görsel
```

### Offline Sayfası

Kullanıcı offline iken ve cache'de sayfa yoksa:
- `/offline.html` gösterilir
- Offline özellikler listelenir
- "Tekrar Dene" butonu
- Bağlantı gelince otomatik yenileme

## 🔄 Service Worker

### Lifecycle

1. **Install** - Static assets cache'lenir
2. **Activate** - Eski cache'ler temizlenir
3. **Fetch** - Request'ler intercept edilir

### Cache Versiyonu

```javascript
const CACHE_VERSION = 'nextcode-v1.0.0';
```

Versiyon değiştiğinde:
- Yeni cache oluşturulur
- Eski cache'ler silinir
- Update notification gösterilir

### Cache Limitleri

```javascript
const MAX_IMAGE_CACHE_SIZE = 50;  // 50 görsel
const MAX_API_CACHE_SIZE = 30;    // 30 API response
```

## 🔔 Push Notifications

### Setup

```javascript
// Permission iste
const permission = await Notification.requestPermission();

// Push subscription oluştur
const subscription = await registration.pushManager.subscribe({
    userVisibleOnly: true,
    applicationServerKey: 'YOUR_PUBLIC_KEY'
});

// Server'a gönder
await fetch('/api/push-subscribe', {
    method: 'POST',
    body: JSON.stringify(subscription)
});
```

### Notification Göster

```javascript
self.registration.showNotification('NextCode Group', {
    body: 'Yeni blog yazısı yayınlandı!',
    icon: '/images/icons/icon-192x192.png',
    badge: '/images/icons/badge-72x72.png',
    vibrate: [200, 100, 200],
    actions: [
        { action: 'open', title: 'Aç' },
        { action: 'close', title: 'Kapat' }
    ]
});
```

## 📊 Analytics ve Tracking

### Installation Tracking

```javascript
window.addEventListener('appinstalled', () => {
    // Google Analytics
    gtag('event', 'pwa_install', {
        event_category: 'PWA',
        event_label: 'App Installed'
    });
});
```

### Usage Tracking

```javascript
// Standalone mode check
if (window.matchMedia('(display-mode: standalone)').matches) {
    gtag('event', 'pwa_usage', {
        event_category: 'PWA',
        event_label: 'Running as PWA'
    });
}
```

## 🧪 Testing

### Chrome DevTools

1. **Application Tab açın**
2. **Service Workers** - Worker durumunu kontrol edin
3. **Manifest** - Manifest doğruluğunu kontrol edin
4. **Cache Storage** - Cache içeriğini görüntüleyin
5. **Offline** - Offline modunu test edin

### Lighthouse

```bash
# PWA audit
lighthouse https://your-site.com --view --preset=pwa

# Performance + PWA
lighthouse https://your-site.com --view --preset=desktop
```

### Manual Testing

1. **Install test**
   - Install button görünüyor mu?
   - Install çalışıyor mu?
   - Icon doğru mu?

2. **Offline test**
   - Network'ü kapatın
   - Sayfalar cache'den yükleniyor mu?
   - Offline page çalışıyor mu?

3. **Update test**
   - Service Worker'ı güncelleyin
   - Update notification gösteriliyor mu?

## 🔧 Troubleshooting

### Service Worker Kaydolmuyor

```javascript
// Console'da kontrol edin
navigator.serviceWorker.getRegistration().then(reg => {
    console.log('Registration:', reg);
});
```

### Cache Çalışmıyor

```javascript
// Cache içeriğini kontrol edin
caches.keys().then(keys => {
    console.log('Cache keys:', keys);
});
```

### Install Prompt Gösterilmiyor

- HTTPS gereklidir
- Manifest geçerli olmalı
- Service Worker kayıtlı olmalı
- beforeinstallprompt eventi bekleyin

## 📈 Performance Optimizasyonu

### Precaching

```javascript
// Kritik dosyaları önceden cache'le
const STATIC_ASSETS = [
    '/',
    '/css/critical.css',
    '/js/critical.js'
];
```

### Lazy Loading

```javascript
// Görselleri lazy load
<img loading="lazy" src="image.jpg">
```

### Background Sync

```javascript
// Offline form submissions
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-contact-forms') {
        event.waitUntil(syncContactForms());
    }
});
```

## 🔒 Güvenlik

- ✅ HTTPS zorunlu
- ✅ Content Security Policy
- ✅ CORS yapılandırması
- ✅ Secure headers

## 📝 Best Practices

1. **Cache güncelleme stratejisi belirleyin**
2. **Offline fallback sağlayın**
3. **Update notification gösterin**
4. **Analytics tracking ekleyin**
5. **Performance monitoring yapın**

## 🚀 Production Checklist

- [ ] Service Worker registered
- [ ] Manifest valid
- [ ] Icons generated (all sizes)
- [ ] Offline page created
- [ ] HTTPS enabled
- [ ] Meta tags added
- [ ] Install button working
- [ ] Update notification working
- [ ] Analytics tracking
- [ ] Performance tested
- [ ] Lighthouse audit passed (>90)

## 📚 Resources

- [MDN PWA Guide](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
- [Google PWA Checklist](https://web.dev/pwa-checklist/)
- [Service Worker Cookbook](https://serviceworke.rs/)
- [Workbox](https://developers.google.com/web/tools/workbox)

## 🤝 Destek

PWA ile ilgili sorular için:
- Email: info@nextcode.com
- Website: https://nextcode.az
- GitHub Issues
- Documentation

---

**NextCode Group PWA** - Modern web deneyimi! 🚀

