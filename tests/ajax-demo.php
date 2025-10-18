<?php
/**
 * AJAX System Demo Page
 * Tüm AJAX özelliklerini gösteren demo sayfası
 */

define('SECURE_ACCESS', true);
session_start();

// CSRF token oluştur
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_title = 'AJAX System Demo - NextCode Group';
$current_page = 'ajax-demo';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <title><?php echo $page_title; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .demo-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .demo-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .demo-card h2 {
            color: #667eea;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .demo-card h2 i {
            font-size: 1.5rem;
        }
        
        .code-block {
            background: #1f2937;
            color: #f3f4f6;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1rem 0;
        }
        
        .code-block code {
            color: #f3f4f6;
            font-family: 'Courier New', monospace;
        }
        
        .btn-demo {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .btn-demo:hover {
            transform: translateY(-2px);
        }
        
        .result-box {
            background: #f3f4f6;
            border-left: 4px solid #667eea;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
            display: none;
        }
        
        .result-box.show {
            display: block;
        }
        
        .search-results {
            margin-top: 1rem;
        }
        
        .search-result-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }
        
        .field-error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .auto-save-indicator {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: none;
        }
        
        .auto-save-indicator.show {
            display: block;
        }
        
        #blog-posts {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .blog-post-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            transition: transform 0.3s;
        }
        
        .blog-post-card:hover {
            transform: translateY(-4px);
        }
        
        .progress-bar-container {
            width: 100%;
            height: 20px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 1rem;
        }
        
        #progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <div class="demo-card">
            <h1 style="text-align: center; color: #667eea;">
                <i class="fas fa-bolt"></i> AJAX System Demo
            </h1>
            <p style="text-align: center; color: #6b7280;">
                Modern AJAX sisteminin tüm özelliklerini test edin
            </p>
        </div>

        <!-- 1. Simple GET Request -->
        <div class="demo-card">
            <h2><i class="fas fa-download"></i> 1. Basit GET İsteği</h2>
            <p>Blog yazılarını AJAX ile yükleyin.</p>
            
            <button class="btn btn-demo" onclick="loadBlogPosts()">
                <i class="fas fa-sync"></i> Blog Yazılarını Yükle
            </button>
            
            <div id="blog-posts"></div>
            
            <div class="code-block">
                <code>
const result = await ajax.get('ajax-handler.php', {<br>
&nbsp;&nbsp;params: { action: 'blog_posts', limit: 6 }<br>
});
                </code>
            </div>
        </div>

        <!-- 2. Form Submit -->
        <div class="demo-card">
            <h2><i class="fas fa-paper-plane"></i> 2. Form Gönderimi</h2>
            <p>İletişim formu otomatik AJAX ile gönderilir.</p>
            
            <form id="contact-form">
                <div class="mb-3">
                    <label class="form-label">Ad Soyad *</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Telefon</label>
                    <input type="tel" class="form-control" name="phone">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Mesaj *</label>
                    <textarea class="form-control" name="message" rows="3" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-demo">
                    <i class="fas fa-paper-plane"></i> Gönder
                </button>
            </form>
            
            <div class="code-block">
                <code>
await ajax.submitForm(form, {<br>
&nbsp;&nbsp;url: 'ajax-handler.php?action=contact_form',<br>
&nbsp;&nbsp;successMessage: 'Mesajınız gönderildi!'<br>
});
                </code>
            </div>
        </div>

        <!-- 3. Real-Time Search -->
        <div class="demo-card">
            <h2><i class="fas fa-search"></i> 3. Canlı Arama</h2>
            <p>Yazarken anlık arama sonuçları.</p>
            
            <div class="mb-3">
                <input type="text" id="search-input" class="form-control" 
                       placeholder="Arama yapın... (en az 3 karakter)">
            </div>
            
            <div id="search-results" class="search-results"></div>
            
            <div class="code-block">
                <code>
searchInput.addEventListener('input', async (e) => {<br>
&nbsp;&nbsp;const result = await ajax.get('ajax-handler.php', {<br>
&nbsp;&nbsp;&nbsp;&nbsp;params: { action: 'search', q: e.target.value }<br>
&nbsp;&nbsp;});<br>
});
                </code>
            </div>
        </div>

        <!-- 4. Live Validation -->
        <div class="demo-card">
            <h2><i class="fas fa-check-circle"></i> 4. Canlı Doğrulama</h2>
            <p>Email kontrolü gerçek zamanlı yapılır.</p>
            
            <div class="mb-3">
                <label class="form-label">Email Kontrol</label>
                <input type="email" id="email-check" class="form-control" 
                       placeholder="Email girin">
            </div>
            
            <div class="code-block">
                <code>
emailInput.addEventListener('blur', async () => {<br>
&nbsp;&nbsp;const result = await ajax.get('check_email.php');<br>
&nbsp;&nbsp;if (result.data.exists) showError('Email kullanımda');<br>
});
                </code>
            </div>
        </div>

        <!-- 5. File Upload -->
        <div class="demo-card">
            <h2><i class="fas fa-upload"></i> 5. Dosya Yükleme</h2>
            <p>Progress bar ile dosya yükleme.</p>
            
            <div class="mb-3">
                <input type="file" id="file-input" class="form-control" accept="image/*">
            </div>
            
            <div class="progress-bar-container">
                <div id="progress-bar"></div>
            </div>
            
            <div class="code-block">
                <code>
await ajax.uploadFile('upload.php', file, {<br>
&nbsp;&nbsp;onProgress: (percent) => updateBar(percent)<br>
});
                </code>
            </div>
        </div>

        <!-- 6. Auto-Save -->
        <div class="demo-card">
            <h2><i class="fas fa-save"></i> 6. Otomatik Kaydetme</h2>
            <p>Yazarken otomatik kaydedilir (2 saniye sonra).</p>
            
            <form id="auto-save-form">
                <div class="mb-3">
                    <label class="form-label">Not Defteri</label>
                    <textarea class="form-control" name="content" rows="5" 
                              placeholder="Yazmaya başlayın..."></textarea>
                </div>
            </form>
            
            <div class="auto-save-indicator" id="auto-save-indicator"></div>
            
            <div class="code-block">
                <code>
form.addEventListener('input', () => {<br>
&nbsp;&nbsp;setTimeout(() => ajax.post('auto-save.php', data), 2000);<br>
});
                </code>
            </div>
        </div>

        <!-- 7. Batch Requests -->
        <div class="demo-card">
            <h2><i class="fas fa-layer-group"></i> 7. Toplu İstek</h2>
            <p>Birden fazla isteği paralel gönder.</p>
            
            <button class="btn btn-demo" onclick="loadDashboard()">
                <i class="fas fa-tachometer-alt"></i> Dashboard Yükle
            </button>
            
            <div id="dashboard-result" class="result-box"></div>
            
            <div class="code-block">
                <code>
const [stats, posts, messages] = await Promise.all([<br>
&nbsp;&nbsp;ajax.get('stats.php'),<br>
&nbsp;&nbsp;ajax.get('posts.php'),<br>
&nbsp;&nbsp;ajax.get('messages.php')<br>
]);
                </code>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/js/ajax-handler.js"></script>
    <script src="/js/ajax-examples.js"></script>
    
    <script>
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            // Form submit
            const contactForm = document.getElementById('contact-form');
            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                await ajax.submitForm(contactForm, {
                    url: 'api/ajax-handler.php',
                    successMessage: 'Mesajınız başarıyla gönderildi!',
                    onSuccess: () => {
                        contactForm.reset();
                    }
                });
            });
            
            // Search
            setupSearch();
            
            // Email validation
            setupLiveValidation();
            
            // File upload
            const fileInput = document.getElementById('file-input');
            fileInput.addEventListener('change', async (e) => {
                const file = e.target.files[0];
                if (!file) return;
                
                // Simulate upload
                for (let i = 0; i <= 100; i += 10) {
                    await new Promise(resolve => setTimeout(resolve, 200));
                    updateProgressBar(i);
                }
                
                ajax.showNotification('success', 'Dosya yüklendi!');
            });
            
            // Auto-save
            const autoSaveForm = document.getElementById('auto-save-form');
            let autoSaveTimeout;
            
            autoSaveForm.addEventListener('input', () => {
                clearTimeout(autoSaveTimeout);
                
                showAutoSaveIndicator('saving');
                
                autoSaveTimeout = setTimeout(() => {
                    showAutoSaveIndicator('saved');
                }, 2000);
            });
        });
        
        // Helper functions
        async function loadBlogPosts() {
            const result = await ajax.get('api/ajax-handler.php', {
                params: {
                    action: 'blog_posts',
                    limit: 6
                }
            });
            
            if (result.success && result.data) {
                displayBlogPosts(result.data.data || result.data);
            }
        }
        
        async function loadDashboard() {
            const dashboardResult = document.getElementById('dashboard-result');
            dashboardResult.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Yükleniyor...';
            dashboardResult.classList.add('show');
            
            try {
                const [stats, posts] = await Promise.all([
                    ajax.get('api/ajax-handler.php?action=blog_posts&limit=3'),
                    ajax.get('api/ajax-handler.php?action=portfolio_projects')
                ]);
                
                dashboardResult.innerHTML = `
                    <h5>✅ Dashboard Yüklendi</h5>
                    <p><strong>Stats:</strong> ${stats.success ? 'OK' : 'Failed'}</p>
                    <p><strong>Posts:</strong> ${posts.success ? 'OK' : 'Failed'}</p>
                `;
            } catch (error) {
                dashboardResult.innerHTML = '<p style="color: red;">❌ Hata: ' + error.message + '</p>';
            }
        }
        
        console.log('🚀 AJAX Demo initialized');
    </script>
</body>
</html>


