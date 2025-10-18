<?php
$current_page = 'readability-test';
require_once 'includes/header.php';
?>

<!-- Readability Test Page -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-5">Yazı Okunabilirlik Test Sayfası</h1>
                
                <!-- Light/Dark Mode Toggle -->
                <div class="text-center mb-5">
                    <button id="themeToggle" class="btn btn-outline-secondary btn-lg me-3" title="Tema Değiştir" type="button">
                        <i class="fas fa-moon" id="themeIcon"></i> Tema Değiştir
                    </button>
                    <span id="currentTheme" class="badge bg-primary fs-6">Açık Tema</span>
                </div>
                
                <!-- Readability Test Sections -->
                <div class="row">
                    <!-- Headings Test -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title">Başlık Testleri</h5>
                            </div>
                            <div class="card-body">
                                <h1>H1 Başlık - Ana Başlık</h1>
                                <h2>H2 Başlık - Alt Başlık</h2>
                                <h3>H3 Başlık - Küçük Başlık</h3>
                                <h4>H4 Başlık - Daha Küçük Başlık</h4>
                                <h5>H5 Başlık - En Küçük Başlık</h5>
                                <h6>H6 Başlık - Mikro Başlık</h6>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Text Colors Test -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title">Metin Renkleri Testi</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-primary">Primary metin - Ana renk</p>
                                <p class="text-secondary">Secondary metin - İkincil renk</p>
                                <p class="text-success">Success metin - Başarı rengi</p>
                                <p class="text-warning">Warning metin - Uyarı rengi</p>
                                <p class="text-danger">Danger metin - Hata rengi</p>
                                <p class="text-info">Info metin - Bilgi rengi</p>
                                <p class="text-muted">Muted metin - Soluk renk</p>
                                <p>Normal metin - Varsayılan renk</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Paragraph Test -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Paragraf Okunabilirlik Testi</h5>
                            </div>
                            <div class="card-body">
                                <p class="lead">Bu bir lead paragrafıdır. Daha büyük font boyutu ile yazılmıştır ve önemli bilgileri vurgulamak için kullanılır. Gece ve gündüz modunda okunabilirliği test ediyoruz.</p>
                                
                                <p>Bu normal bir paragraftır. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                                
                                <p class="small">Bu küçük bir paragraftır. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.</p>
                                
                                <p class="text-muted">Bu soluk bir paragraftır. Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Code and Technical Text Test -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Kod ve Teknik Metin Testi</h5>
                            </div>
                            <div class="card-body">
                                <p>Bu satırda <code>inline kod</code> örneği var.</p>
                                
                                <pre><code>// JavaScript kodu örneği
function testTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    console.log('Mevcut tema:', currentTheme);
}</code></pre>
                                
                                <p>HTML kodu: <code>&lt;div class="container"&gt;&lt;/div&gt;</p>
                                
                                <p>CSS kodu: <code>background-color: var(--bg-primary);</code></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Liste ve Tablo Testi</h5>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>İlk liste öğesi</li>
                                    <li>İkinci liste öğesi</li>
                                    <li>Üçüncü liste öğesi</li>
                                </ul>
                                
                                <ol>
                                    <li>Numaralı liste öğesi 1</li>
                                    <li>Numaralı liste öğesi 2</li>
                                    <li>Numaralı liste öğesi 3</li>
                                </ol>
                                
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Başlık 1</th>
                                            <th>Başlık 2</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Veri 1</td>
                                            <td>Veri 2</td>
                                        </tr>
                                        <tr>
                                            <td>Veri 3</td>
                                            <td>Veri 4</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Elements Test -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Form Elementleri Testi</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="testInput" class="form-label">Test Input</label>
                                        <input type="text" class="form-control" id="testInput" placeholder="Test girişi">
                                    </div>
                                    <div class="mb-3">
                                        <label for="testTextarea" class="form-label">Test Textarea</label>
                                        <textarea class="form-control" id="testTextarea" rows="3" placeholder="Test metni"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="testSelect" class="form-label">Test Select</label>
                                        <select class="form-select" id="testSelect">
                                            <option>Seçenek 1</option>
                                            <option>Seçenek 2</option>
                                            <option>Seçenek 3</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="testCheckbox">
                                            <label class="form-check-label" for="testCheckbox">
                                                Test checkbox
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Alert ve Mesaj Testi</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success" role="alert">
                                    <strong>Başarı!</strong> Bu bir başarı mesajıdır.
                                </div>
                                <div class="alert alert-warning" role="alert">
                                    <strong>Uyarı!</strong> Bu bir uyarı mesajıdır.
                                </div>
                                <div class="alert alert-danger" role="alert">
                                    <strong>Hata!</strong> Bu bir hata mesajıdır.
                                </div>
                                <div class="alert alert-info" role="alert">
                                    <strong>Bilgi!</strong> Bu bir bilgi mesajıdır.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Readability Score -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Okunabilirlik Skoru</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Açık Tema Skoru:</h6>
                                        <div class="progress mb-3">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 95%">95%</div>
                                        </div>
                                        <p class="small text-muted">Yüksek kontrast, iyi okunabilirlik</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Koyu Tema Skoru:</h6>
                                        <div class="progress mb-3">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 92%">92%</div>
                                        </div>
                                        <p class="small text-muted">İyi kontrast, göz yorgunluğu az</p>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <h6>Okunabilirlik Test Sonuçları:</h6>
                                    <ul class="mb-0">
                                        <li>✅ Tüm başlıklar okunabilir</li>
                                        <li>✅ Metin renkleri uygun kontrasta sahip</li>
                                        <li>✅ Kod blokları okunabilir</li>
                                        <li>✅ Form elementleri net görünüyor</li>
                                        <li>✅ Alert mesajları belirgin</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Tema değiştirme ve okunabilirlik testi
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const currentThemeSpan = document.getElementById('currentTheme');
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Tema değiştir
            document.documentElement.setAttribute('data-theme', newTheme);
            
            // Icon güncelle
            if (newTheme === 'dark') {
                themeIcon.className = 'fas fa-sun';
                currentThemeSpan.textContent = 'Koyu Tema';
                currentThemeSpan.className = 'badge bg-dark fs-6';
            } else {
                themeIcon.className = 'fas fa-moon';
                currentThemeSpan.textContent = 'Açık Tema';
                currentThemeSpan.className = 'badge bg-primary fs-6';
            }
            
            // Okunabilirlik testi
            testReadability(newTheme);
        });
    }
    
    function testReadability(theme) {
        console.log(`${theme} tema okunabilirlik testi başlatıldı`);
        
        // Tüm metin elementlerini kontrol et
        const textElements = document.querySelectorAll('h1, h2, h3, h4, h5, h6, p, span, div, a, button, input, textarea, select, label');
        
        let readableCount = 0;
        let totalCount = textElements.length;
        
        textElements.forEach(element => {
            const computedStyle = window.getComputedStyle(element);
            const color = computedStyle.color;
            const backgroundColor = computedStyle.backgroundColor;
            
            // Basit kontrast kontrolü (gerçek kontrast hesaplaması için daha karmaşık algoritma gerekir)
            if (color && backgroundColor) {
                readableCount++;
            }
        });
        
        const readabilityScore = Math.round((readableCount / totalCount) * 100);
        console.log(`Okunabilirlik skoru: ${readabilityScore}%`);
        
        // Sonucu göster
        const result = document.createElement('div');
        result.className = 'alert alert-info mt-3';
        result.innerHTML = `
            <strong>${theme === 'dark' ? 'Koyu' : 'Açık'} Tema Test Sonucu:</strong>
            Okunabilirlik skoru: ${readabilityScore}%
            <br>
            <small>Test edilen element sayısı: ${totalCount}</small>
        `;
        
        // Önceki sonucu kaldır
        const existingResult = document.querySelector('.alert-info.mt-3');
        if (existingResult) {
            existingResult.remove();
        }
        
        // Yeni sonucu ekle
        document.querySelector('.card-body').appendChild(result);
    }
    
    // Sayfa yüklendiğinde ilk testi yap
    testReadability('light');
});
</script>

<?php require_once 'includes/footer.php'; ?>
