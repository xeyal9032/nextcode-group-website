/**
 * AJAX Usage Examples
 * Kullanım örnekleri
 */

// ============================================
// 1. BASİT GET İSTEĞİ
// ============================================

async function loadBlogPosts() {
    try {
        const result = await ajax.get('ajax-handler.php', {
            params: {
                action: 'blog_posts',
                limit: 5
            }
        });
        
        if (result.success) {
            console.log('Blog posts:', result.data);
            displayBlogPosts(result.data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// ============================================
// 2. POST İSTEĞİ
// ============================================

async function submitContactForm() {
    const result = await ajax.post('ajax-handler.php', {
        action: 'contact_form',
        name: 'Əli Məmmədov',
        email: 'ali@example.com',
        message: 'Test mesaj'
    });
    
    if (result.success) {
        alert('Mesaj gönderildi!');
    }
}

// ============================================
// 3. FORM SUBMIT (Otomatik)
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const result = await ajax.submitForm(contactForm, {
                url: 'ajax-handler.php?action=contact_form',
                successMessage: 'Mesajınız uğurla göndərildi!',
                onSuccess: (data) => {
                    console.log('Form submitted:', data);
                    // Redirect veya başka işlem
                }
            });
        });
    }
});

// ============================================
// 4. REAL-TIME SEARCH
// ============================================

function setupSearch() {
    const searchInput = document.getElementById('search-input');
    
    if (!searchInput) return;
    
    let timeout;
    
    searchInput.addEventListener('input', (e) => {
        clearTimeout(timeout);
        
        timeout = setTimeout(async () => {
            const query = e.target.value;
            
            if (query.length < 3) return;
            
            const result = await ajax.get('ajax-handler.php', {
                params: {
                    action: 'search',
                    q: query
                }
            });
            
            if (result.success) {
                displaySearchResults(result.data);
            }
        }, 300); // 300ms debounce
    });
}

// ============================================
// 5. FILE UPLOAD
// ============================================

async function uploadImage(file) {
    const result = await ajax.uploadFile('ajax-handler.php?action=upload_image', file, {
        onProgress: (percent) => {
            console.log(`Upload: ${percent}%`);
            updateProgressBar(percent);
        }
    });
    
    if (result.success) {
        console.log('Image uploaded:', result.data);
    }
}

// Drag & Drop example
function setupFileUpload() {
    const dropZone = document.getElementById('drop-zone');
    
    if (!dropZone) return;
    
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });
    
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });
    
    dropZone.addEventListener('drop', async (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        
        for (let file of files) {
            await uploadImage(file);
        }
    });
}

// ============================================
// 6. INFINITE SCROLL
// ============================================

let currentPage = 0;
let loading = false;

async function loadMorePosts() {
    if (loading) return;
    
    loading = true;
    currentPage++;
    
    const result = await ajax.get('ajax-handler.php', {
        params: {
            action: 'blog_posts',
            limit: 10,
            offset: currentPage * 10
        }
    });
    
    loading = false;
    
    if (result.success) {
        appendPosts(result.data);
    }
}

// Scroll listener
window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
        loadMorePosts();
    }
});

// ============================================
// 7. LIVE VALIDATION
// ============================================

function setupLiveValidation() {
    const emailInput = document.getElementById('email');
    
    if (!emailInput) return;
    
    let timeout;
    
    emailInput.addEventListener('input', (e) => {
        clearTimeout(timeout);
        
        timeout = setTimeout(async () => {
            const email = e.target.value;
            
            if (!email) return;
            
            // Email format check
            if (!isValidEmail(email)) {
                showFieldError(emailInput, 'Geçersiz email formatı');
                return;
            }
            
            // Check if email exists
            const result = await ajax.get('ajax-handler.php', {
                params: {
                    action: 'check_email',
                    email: email
                }
            });
            
            if (result.success && result.data.exists) {
                showFieldError(emailInput, 'Bu email zaten kullanılıyor');
            } else {
                clearFieldError(emailInput);
            }
        }, 500);
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showFieldError(input, message) {
    let error = input.nextElementSibling;
    
    if (!error || !error.classList.contains('field-error')) {
        error = document.createElement('div');
        error.className = 'field-error';
        input.parentNode.insertBefore(error, input.nextSibling);
    }
    
    error.textContent = message;
    input.classList.add('error');
}

function clearFieldError(input) {
    const error = input.nextElementSibling;
    
    if (error && error.classList.contains('field-error')) {
        error.remove();
    }
    
    input.classList.remove('error');
}

// ============================================
// 8. AUTO-SAVE
// ============================================

function setupAutoSave(formSelector, endpoint) {
    const form = document.querySelector(formSelector);
    
    if (!form) return;
    
    let timeout;
    
    form.addEventListener('input', () => {
        clearTimeout(timeout);
        
        timeout = setTimeout(async () => {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            const result = await ajax.post(endpoint, data);
            
            if (result.success) {
                showAutoSaveIndicator('saved');
            }
        }, 2000); // 2 saniye sonra kaydet
        
        showAutoSaveIndicator('saving');
    });
}

function showAutoSaveIndicator(status) {
    let indicator = document.getElementById('auto-save-indicator');
    
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'auto-save-indicator';
        document.body.appendChild(indicator);
    }
    
    if (status === 'saving') {
        indicator.textContent = '💾 Kaydediliyor...';
        indicator.className = 'auto-save-indicator saving';
    } else {
        indicator.textContent = '✓ Kaydedildi';
        indicator.className = 'auto-save-indicator saved';
        
        setTimeout(() => {
            indicator.classList.add('hide');
        }, 2000);
    }
}

// ============================================
// 9. POLLING (Periyodik veri güncelleme)
// ============================================

class Poller {
    constructor(url, interval = 5000) {
        this.url = url;
        this.interval = interval;
        this.timer = null;
    }
    
    start(callback) {
        this.stop(); // Önceki timer'ı temizle
        
        this.timer = setInterval(async () => {
            const result = await ajax.get(this.url);
            
            if (result.success) {
                callback(result.data);
            }
        }, this.interval);
    }
    
    stop() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }
}

// Kullanım
const notificationPoller = new Poller('ajax-handler.php?action=get_notifications', 10000);

notificationPoller.start((notifications) => {
    updateNotificationBadge(notifications.length);
});

// ============================================
// 10. BATCH REQUESTS
// ============================================

async function loadDashboardData() {
    const requests = [
        ajax.get('ajax-handler.php?action=get_stats'),
        ajax.get('ajax-handler.php?action=get_recent_posts'),
        ajax.get('ajax-handler.php?action=get_messages')
    ];
    
    try {
        const [stats, posts, messages] = await Promise.all(requests);
        
        updateStats(stats.data);
        displayPosts(posts.data);
        displayMessages(messages.data);
    } catch (error) {
        console.error('Failed to load dashboard data:', error);
    }
}

// ============================================
// Helper Functions
// ============================================

function displayBlogPosts(posts) {
    const container = document.getElementById('blog-posts');
    if (!container) return;
    
    container.innerHTML = posts.map(post => `
        <div class="blog-post-card">
            <h3>${post.title}</h3>
            <p>${post.excerpt}</p>
            <a href="/blog/${post.slug}">Devamını Oku</a>
        </div>
    `).join('');
}

function displaySearchResults(results) {
    const container = document.getElementById('search-results');
    if (!container) return;
    
    if (results.length === 0) {
        container.innerHTML = '<p>Sonuç bulunamadı</p>';
        return;
    }
    
    container.innerHTML = results.map(result => `
        <div class="search-result">
            <h4>${result.title}</h4>
            <p>${result.excerpt || result.description}</p>
            <span class="badge">${result.type}</span>
        </div>
    `).join('');
}

function appendPosts(posts) {
    const container = document.getElementById('blog-posts');
    if (!container) return;
    
    posts.forEach(post => {
        const postEl = document.createElement('div');
        postEl.className = 'blog-post-card';
        postEl.innerHTML = `
            <h3>${post.title}</h3>
            <p>${post.excerpt}</p>
            <a href="/blog/${post.slug}">Devamını Oku</a>
        `;
        container.appendChild(postEl);
    });
}

function updateProgressBar(percent) {
    const bar = document.getElementById('progress-bar');
    if (bar) {
        bar.style.width = `${percent}%`;
    }
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notification-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'block' : 'none';
    }
}

// Initialize examples
document.addEventListener('DOMContentLoaded', () => {
    setupSearch();
    setupFileUpload();
    setupLiveValidation();
    
    console.log('AJAX examples initialized');
});


