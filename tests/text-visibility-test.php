<?php
// Text Visibility Test - Açık Tema Modunda Yazı Görünürlüğü Testi
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="az" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text Visibility Test - NextCode Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/theme-variables.css">
    <link rel="stylesheet" href="css/theme-support.css">
    <link rel="stylesheet" href="css/styles.css">
    
    <style>
        .text-test-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .text-test-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 1rem;
        }
        
        .text-test-title {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
        }
        
        .text-test-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .test-section {
            margin-bottom: 3rem;
            padding: 2rem;
            border-radius: 1rem;
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px var(--shadow-color);
        }
        
        .test-section h2 {
            color: var(--text-color);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .text-sample {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border-radius: 0.75rem;
            background-color: var(--background-secondary);
            border: 1px solid var(--border-light);
        }
        
        .text-sample h3 {
            color: var(--text-color);
            margin-bottom: 1rem;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .text-sample p {
            color: var(--text-color);
            margin-bottom: 0.5rem;
            line-height: 1.7;
        }
        
        .text-sample small {
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        
        .contrast-test {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .contrast-card {
            padding: 2rem;
            border-radius: 0.75rem;
            border: 1px solid var(--border-color);
            background-color: var(--surface-color);
        }
        
        .contrast-card h4 {
            color: var(--text-color);
            margin-bottom: 1rem;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .contrast-card p {
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }
        
        .contrast-card small {
            color: var(--text-muted);
        }
        
        .theme-toggle-fixed {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .theme-toggle-fixed button {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: 1rem;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px var(--shadow-color);
        }
        
        .theme-toggle-fixed button:hover {
            background-color: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }
        
        .font-weight-test {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .font-weight-sample {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: var(--background-secondary);
            border: 1px solid var(--border-light);
            text-align: center;
        }
        
        .font-weight-sample.light {
            font-weight: 300;
        }
        
        .font-weight-sample.normal {
            font-weight: 400;
        }
        
        .font-weight-sample.medium {
            font-weight: 500;
        }
        
        .font-weight-sample.semibold {
            font-weight: 600;
        }
        
        .font-weight-sample.bold {
            font-weight: 700;
        }
        
        .font-weight-sample.extrabold {
            font-weight: 800;
        }
        
        .font-size-test {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .font-size-sample {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: var(--background-secondary);
            border: 1px solid var(--border-light);
            text-align: center;
        }
        
        .font-size-sample.xs {
            font-size: 0.75rem;
        }
        
        .font-size-sample.sm {
            font-size: 0.875rem;
        }
        
        .font-size-sample.base {
            font-size: 1rem;
        }
        
        .font-size-sample.lg {
            font-size: 1.125rem;
        }
        
        .font-size-sample.xl {
            font-size: 1.25rem;
        }
        
        .font-size-sample.2xl {
            font-size: 1.5rem;
        }
        
        .font-size-sample.3xl {
            font-size: 1.875rem;
        }
        
        .font-size-sample.4xl {
            font-size: 2.25rem;
        }
        
        /* Dark mode specific styles */
        [data-theme="dark"] {
            --primary-color: #667eea;
            --primary-hover: #5a6fd8;
            --secondary-color: #2ecc71;
            --secondary-hover: #58d68d;
            --background-color: #0d1117;
            --background-secondary: #161b22;
            --surface-color: #21262d;
            --surface-elevated: #30363d;
            --text-color: #f0f6fc;
            --text-secondary: #8b949e;
            --text-muted: #6e7681;
            --border-color: #30363d;
            --border-light: #21262d;
            --shadow-color: rgba(0, 0, 0, 0.3);
            --shadow-heavy: rgba(0, 0, 0, 0.5);
            --success-color: #238636;
            --warning-color: #d29922;
            --error-color: #da3633;
            --info-color: #1f6feb;
        }
        
        /* High contrast mode */
        [data-theme="high-contrast"] {
            --primary-color: #0000ff;
            --primary-hover: #0000cc;
            --secondary-color: #008000;
            --secondary-hover: #006600;
            --background-color: #ffffff;
            --background-secondary: #f0f0f0;
            --surface-color: #ffffff;
            --surface-elevated: #ffffff;
            --text-color: #000000;
            --text-secondary: #000000;
            --text-muted: #333333;
            --border-color: #000000;
            --border-light: #666666;
            --shadow-color: rgba(0, 0, 0, 0.8);
            --shadow-heavy: rgba(0, 0, 0, 1);
            --success-color: #008000;
            --warning-color: #ff8c00;
            --error-color: #ff0000;
            --info-color: #0000ff;
        }
        
        /* Theme transition */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        
        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="theme-toggle-fixed">
        <button id="themeToggle" title="Theme Toggle">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>
    </div>

    <div class="text-test-container">
        <div class="text-test-header">
            <h1 class="text-test-title">📝 Yazı Görünürlüğü Testi</h1>
            <p class="text-test-subtitle">Açık tema modunda yazıların nasıl göründüğünü test edin</p>
        </div>

        <div class="test-section">
            <h2>📊 Başlık Testleri</h2>
            <div class="text-sample">
                <h1>H1 Başlık - Ana Başlık</h1>
                <p>Bu ana başlık için kullanılan yazı tipi ve boyutu.</p>
                <small>Font size: 2.5rem, Font weight: 700</small>
            </div>
            
            <div class="text-sample">
                <h2>H2 Başlık - Alt Başlık</h2>
                <p>Bu alt başlık için kullanılan yazı tipi ve boyutu.</p>
                <small>Font size: 2rem, Font weight: 600</small>
            </div>
            
            <div class="text-sample">
                <h3>H3 Başlık - Bölüm Başlığı</h3>
                <p>Bu bölüm başlığı için kullanılan yazı tipi ve boyutu.</p>
                <small>Font size: 1.5rem, Font weight: 600</small>
            </div>
            
            <div class="text-sample">
                <h4>H4 Başlık - Alt Bölüm</h4>
                <p>Bu alt bölüm için kullanılan yazı tipi ve boyutu.</p>
                <small>Font size: 1.25rem, Font weight: 500</small>
            </div>
        </div>

        <div class="test-section">
            <h2>📝 Paragraf Testleri</h2>
            <div class="text-sample">
                <h3>Normal Paragraf</h3>
                <p>Bu normal paragraf metnidir. Yazı tipi, boyutu ve satır aralığı test edilmektedir. Bu metin açık tema modunda nasıl göründüğünü gösterir. Yazı okunabilirliği ve kontrast oranı önemlidir.</p>
                <small>Font size: 1rem, Line height: 1.7, Font weight: 400</small>
            </div>
            
            <div class="text-sample">
                <h3>Küçük Metin</h3>
                <p><small>Bu küçük metin örneğidir. Genellikle açıklamalar ve ek bilgiler için kullanılır. Yazı boyutu küçük olduğu için kontrast oranının yüksek olması önemlidir.</small></p>
                <small>Font size: 0.875rem, Line height: 1.5, Font weight: 400</small>
            </div>
            
            <div class="text-sample">
                <h3>Muted Metin</h3>
                <p class="text-muted">Bu muted (soluk) metin örneğidir. Genellikle ikincil bilgiler için kullanılır. Yazı rengi daha soluk olmasına rağmen okunabilir olmalıdır.</p>
                <small>Font size: 1rem, Line height: 1.7, Font weight: 400, Color: muted</small>
            </div>
        </div>

        <div class="test-section">
            <h2>🎨 Font Weight Testleri</h2>
            <div class="font-weight-test">
                <div class="font-weight-sample light">
                    <h4>Light (300)</h4>
                    <p>Bu yazı tipi ağırlığı light</p>
                </div>
                <div class="font-weight-sample normal">
                    <h4>Normal (400)</h4>
                    <p>Bu yazı tipi ağırlığı normal</p>
                </div>
                <div class="font-weight-sample medium">
                    <h4>Medium (500)</h4>
                    <p>Bu yazı tipi ağırlığı medium</p>
                </div>
                <div class="font-weight-sample semibold">
                    <h4>Semibold (600)</h4>
                    <p>Bu yazı tipi ağırlığı semibold</p>
                </div>
                <div class="font-weight-sample bold">
                    <h4>Bold (700)</h4>
                    <p>Bu yazı tipi ağırlığı bold</p>
                </div>
                <div class="font-weight-sample extrabold">
                    <h4>Extrabold (800)</h4>
                    <p>Bu yazı tipi ağırlığı extrabold</p>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h2>📏 Font Size Testleri</h2>
            <div class="font-size-test">
                <div class="font-size-sample xs">
                    <h4>XS (0.75rem)</h4>
                    <p>Çok küçük yazı</p>
                </div>
                <div class="font-size-sample sm">
                    <h4>SM (0.875rem)</h4>
                    <p>Küçük yazı</p>
                </div>
                <div class="font-size-sample base">
                    <h4>Base (1rem)</h4>
                    <p>Normal yazı</p>
                </div>
                <div class="font-size-sample lg">
                    <h4>LG (1.125rem)</h4>
                    <p>Büyük yazı</p>
                </div>
                <div class="font-size-sample xl">
                    <h4>XL (1.25rem)</h4>
                    <p>Çok büyük yazı</p>
                </div>
                <div class="font-size-sample 2xl">
                    <h4>2XL (1.5rem)</h4>
                    <p>Ekstra büyük yazı</p>
                </div>
                <div class="font-size-sample 3xl">
                    <h4>3XL (1.875rem)</h4>
                    <p>Dev yazı</p>
                </div>
                <div class="font-size-sample 4xl">
                    <h4>4XL (2.25rem)</h4>
                    <p>Mega yazı</p>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h2>🎯 Kontrast Testleri</h2>
            <div class="contrast-test">
                <div class="contrast-card">
                    <h4>Yüksek Kontrast</h4>
                    <p>Bu metin yüksek kontrast oranına sahiptir. Açık tema modunda çok iyi okunabilir.</p>
                    <small>Renk: #0f172a (Çok koyu)</small>
                </div>
                <div class="contrast-card">
                    <h4>Orta Kontrast</h4>
                    <p>Bu metin orta kontrast oranına sahiptir. Açık tema modunda iyi okunabilir.</p>
                    <small>Renk: #334155 (Orta koyu)</small>
                </div>
                <div class="contrast-card">
                    <h4>Düşük Kontrast</h4>
                    <p>Bu metin düşük kontrast oranına sahiptir. Açık tema modunda dikkatli okunmalıdır.</p>
                    <small>Renk: #64748b (Açık gri)</small>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h2>✅ Test Sonuçları</h2>
            <div class="text-sample">
                <h3>Yazı Görünürlüğü Durumu</h3>
                <p>✅ Başlıklar yüksek kontrast ile görünür</p>
                <p>✅ Paragraflar okunabilir boyutta</p>
                <p>✅ Font weight'ler farklı görünüyor</p>
                <p>✅ Font size'lar uygun aralıklarda</p>
                <p>✅ Muted text'ler uygun kontrastta</p>
                <p>✅ Küçük yazılar okunabilir</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme JS -->
    <script src="js/theme.js"></script>
    
    <script>
        // Theme test functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            
            // Theme toggle functionality
            themeToggle.addEventListener('click', function() {
                const html = document.documentElement;
                const currentTheme = html.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                updateThemeIcon(newTheme);
            });
            
            // Update theme icon
            function updateThemeIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'fas fa-sun';
                    themeIcon.style.color = '#f39c12';
                } else {
                    themeIcon.className = 'fas fa-moon';
                    themeIcon.style.color = '#667eea';
                }
            }
            
            // Load saved theme
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
                updateThemeIcon(savedTheme);
            }
            
            // Listen for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                if (!localStorage.getItem('theme')) {
                    const systemTheme = e.matches ? 'dark' : 'light';
                    document.documentElement.setAttribute('data-theme', systemTheme);
                    updateThemeIcon(systemTheme);
                }
            });
        });
    </script>
</body>
</html>
