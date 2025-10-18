/**
 * Service Worker for NextCode Group PWA
 * Provides offline functionality and caching
 */

const CACHE_VERSION = 'nextcode-v1.0.0';
const CACHE_NAME = `nextcode-${CACHE_VERSION}`;

// Assets to cache on install
const STATIC_ASSETS = [
    '/',
    '/index.php',
    '/about.php',
    '/services.php',
    '/portfolio.php',
    '/blog.php',
    '/contact.php',
    '/css/modern-styles.css',
    '/css/theme.css',
    '/css/animations.css',
    '/css/responsive-enhanced.css',
    '/css/font-fix.css',
    '/js/main.js',
    '/js/theme.js',
    '/js/modern-interactions.js',
    '/js/error-suppressor.js',
    '/favicon.svg',
    '/manifest.json',
    '/offline.html'
];

// Dynamic cache for images and API responses
const DYNAMIC_CACHE = `${CACHE_NAME}-dynamic`;
const IMAGE_CACHE = `${CACHE_NAME}-images`;
const API_CACHE = `${CACHE_NAME}-api`;

// Cache size limits
const MAX_IMAGE_CACHE_SIZE = 50;
const MAX_API_CACHE_SIZE = 30;

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Service Worker: Caching static assets');
                // Cache assets individually to avoid failures
                return Promise.allSettled(
                    STATIC_ASSETS.map(url => 
                        cache.add(url).catch(err => {
                            console.log('Failed to cache:', url, '(will fetch on demand)');
                        })
                    )
                );
            })
            .then(() => {
                console.log('Service Worker: Installation complete');
            })
            .catch((error) => {
                console.log('Service Worker: Some assets failed to cache, continuing...');
            })
    );
    
    // Force the waiting service worker to become the active service worker
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating...');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME && cache !== DYNAMIC_CACHE && 
                        cache !== IMAGE_CACHE && cache !== API_CACHE) {
                        console.log('Service Worker: Deleting old cache:', cache);
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    
    // Claim control of all clients
    return self.clients.claim();
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip cross-origin requests
    if (url.origin !== location.origin) {
        return;
    }
    
    // API requests
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(networkFirstStrategy(request, API_CACHE));
        return;
    }
    
    // Image requests
    if (request.destination === 'image') {
        event.respondWith(cacheFirstStrategy(request, IMAGE_CACHE, MAX_IMAGE_CACHE_SIZE));
        return;
    }
    
    // Static assets
    if (STATIC_ASSETS.some(asset => url.pathname.includes(asset))) {
        event.respondWith(cacheFirstStrategy(request, CACHE_NAME));
        return;
    }
    
    // Dynamic content
    event.respondWith(networkFirstStrategy(request, DYNAMIC_CACHE));
});

/**
 * Cache First Strategy
 * Try cache first, fallback to network
 */
async function cacheFirstStrategy(request, cacheName, cacheLimit = null) {
    try {
        const cachedResponse = await caches.match(request);
        
        if (cachedResponse) {
            // Return cached response immediately
            console.log('Service Worker: Serving from cache:', request.url);
            return cachedResponse;
        }
        
        // Fetch from network
        const networkResponse = await fetch(request);
        
        // Cache the response if successful
        if (networkResponse && networkResponse.status === 200) {
            const cache = await caches.open(cacheName);
            
            // Limit cache size if specified
            if (cacheLimit) {
                await limitCacheSize(cacheName, cacheLimit);
            }
            
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        // Silently handle errors for non-critical resources
        const url = request.url;
        
        // Return empty response for fonts (browser will use fallback)
        if (url.includes('.woff') || url.includes('.woff2') || url.includes('.ttf')) {
            return new Response('', {status: 200});
        }
        
        // Return empty response for images
        if (url.includes('.jpg') || url.includes('.png') || url.includes('.svg')) {
            return new Response('', {status: 200});
        }
        
        // Return empty CSS for missing stylesheets
        if (url.includes('.css')) {
            return new Response('/* File not found */', {
                status: 200,
                headers: {'Content-Type': 'text/css'}
            });
        }
        
        // Return offline page for navigation requests
        if (request.destination === 'document') {
            const offline = await caches.match('/offline.html');
            if (offline) return offline;
        }
        
        // Return empty response for all other failures
        return new Response('', {status: 200});
    }
}

/**
 * Network First Strategy
 * Try network first, fallback to cache
 */
async function networkFirstStrategy(request, cacheName) {
    try {
        // Try network first
        const networkResponse = await fetch(request);
        
        // Cache successful responses (only GET requests)
        if (networkResponse && networkResponse.status === 200) {
            // Service Worker can only cache GET requests
            if (request.method === 'GET') {
                const cache = await caches.open(cacheName);
                cache.put(request, networkResponse.clone());
            }
        }
        
        return networkResponse;
    } catch (error) {
        // Silently handle /api/analytics errors (not critical)
        if (request.url.includes('/api/analytics')) {
            // Return empty successful response
            return new Response(JSON.stringify({success: true, cached: true}), {
                status: 200,
                headers: {'Content-Type': 'application/json'}
            });
        }
        
        console.log('Service Worker: Network failed, trying cache:', request.url);
        
        // Fallback to cache
        const cachedResponse = await caches.match(request);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline page for navigation requests
        if (request.destination === 'document') {
            return caches.match('/offline.html');
        }
        
        // Return empty response for other failed requests
        return new Response('', {status: 200});
    }
}

/**
 * Limit cache size by removing oldest entries
 */
async function limitCacheSize(cacheName, maxSize) {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    
    if (keys.length > maxSize) {
        // Remove oldest entries
        const deleteCount = keys.length - maxSize;
        for (let i = 0; i < deleteCount; i++) {
            await cache.delete(keys[i]);
        }
    }
}

// Background sync for offline form submissions
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-contact-forms') {
        event.waitUntil(syncContactForms());
    }
});

/**
 * Sync offline contact form submissions
 */
async function syncContactForms() {
    try {
        // Get stored form submissions from IndexedDB
        const db = await openDB();
        const forms = await db.getAll('contact-forms');
        
        // Send each form
        for (const form of forms) {
            await fetch('/api/contact.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(form.data)
            });
            
            // Remove from IndexedDB after successful send
            await db.delete('contact-forms', form.id);
        }
        
        console.log('Service Worker: Contact forms synced');
    } catch (error) {
        console.error('Service Worker: Sync failed:', error);
        throw error;
    }
}

// Push notification handler
self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};
    
    const options = {
        body: data.body || 'New notification from NextCode Group',
        icon: '/favicon.svg',
        badge: '/favicon.svg',
        vibrate: [200, 100, 200],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: data.primaryKey || '1'
        },
        actions: [
            {
                action: 'explore',
                title: 'Open',
                icon: '/images/checkmark.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/images/xmark.png'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title || 'NextCode Group', options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Message handler for cache management from main thread
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cache) => caches.delete(cache))
                );
            })
        );
    }
});

// Helper function to open IndexedDB
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('nextcode-db', 1);
        
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
        
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains('contact-forms')) {
                db.createObjectStore('contact-forms', { keyPath: 'id', autoIncrement: true });
            }
        };
    });
}

console.log('Service Worker: Loaded');
