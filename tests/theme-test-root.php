<?php
$current_page = 'theme-test';
require_once 'includes/header.php';
?>

<!-- Theme Test Page -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-5">Tema Test Sayfası</h1>
                
                <!-- Test Cards -->
                <div class="row mb-5">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title">Test Kartı 1</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Bu bir test kartıdır. Gece ve gündüz modunda nasıl göründüğünü test ediyoruz.</p>
                                <a href="#" class="btn btn-primary">Test Butonu</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title">Test Kartı 2</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Bu kart da tema değişikliklerini test etmek için kullanılıyor.</p>
                                <a href="#" class="btn btn-secondary">Test Butonu</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title">Test Kartı 3</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Son test kartı. Tüm elementlerin tema desteğini kontrol ediyoruz.</p>
                                <a href="#" class="btn btn-outline-primary">Test Butonu</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Test Forms -->
                <div class="row mb-5">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Form Test</h5>
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
                                        <select class="form-select">
                                            <option>Seçenek 1</option>
                                            <option>Seçenek 2</option>
                                            <option>Seçenek 3</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Gönder</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Alert Test</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success" role="alert">
                                    Bu bir başarı mesajıdır.
                                </div>
                                <div class="alert alert-warning" role="alert">
                                    Bu bir uyarı mesajıdır.
                                </div>
                                <div class="alert alert-danger" role="alert">
                                    Bu bir hata mesajıdır.
                                </div>
                                <div class="alert alert-info" role="alert">
                                    Bu bir bilgi mesajıdır.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Test Tables -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Tablo Test</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Başlık 1</th>
                                            <th>Başlık 2</th>
                                            <th>Başlık 3</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Veri 1</td>
                                            <td>Veri 2</td>
                                            <td>Veri 3</td>
                                        </tr>
                                        <tr>
                                            <td>Veri 4</td>
                                            <td>Veri 5</td>
                                            <td>Veri 6</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Test Text Elements -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Metin Elementleri Test</h5>
                            </div>
                            <div class="card-body">
                                <h1>Başlık 1</h1>
                                <h2>Başlık 2</h2>
                                <h3>Başlık 3</h3>
                                <h4>Başlık 4</h4>
                                <h5>Başlık 5</h5>
                                <h6>Başlık 6</h6>
                                
                                <p>Bu bir paragraf metnidir. Gece ve gündüz modunda okunabilirliği test ediyoruz.</p>
                                
                                <p class="text-muted">Bu muted metindir.</p>
                                <p class="text-primary">Bu primary metindir.</p>
                                <p class="text-secondary">Bu secondary metindir.</p>
                                <p class="text-success">Bu success metindir.</p>
                                <p class="text-warning">Bu warning metindir.</p>
                                <p class="text-danger">Bu danger metindir.</p>
                                <p class="text-info">Bu info metindir.</p>
                                
                                <blockquote class="blockquote">
                                    <p>Bu bir blockquote örneğidir.</p>
                                    <footer class="blockquote-footer">Test Yazarı</footer>
                                </blockquote>
                                
                                <code>Bu kod metnidir</code>
                                
                                <pre><code>Bu pre kod bloğudur
Çok satırlı kod örneği
Tema desteğini test ediyoruz</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Theme Toggle Test -->
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Tema Değiştirme Test</h5>
                            </div>
                            <div class="card-body">
                                <p>Tema butonunu kullanarak gece/gündüz modu arasında geçiş yapın ve tüm elementlerin düzgün göründüğünü kontrol edin.</p>
                                <button id="themeToggle" class="btn btn-outline-secondary btn-lg me-3" title="Tema Değiştir" type="button">
                                    <i class="fas fa-moon" id="themeIcon"></i> Tema Değiştir
                                </button>
                                <button class="btn btn-primary btn-lg" onclick="testTheme()">Tema Test Et</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function testTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    // Tema değiştir
    document.documentElement.setAttribute('data-theme', newTheme);
    
    // Icon güncelle
    const themeIcon = document.getElementById('themeIcon');
    if (newTheme === 'dark') {
        themeIcon.className = 'fas fa-sun';
    } else {
        themeIcon.className = 'fas fa-moon';
    }
    
    // Test sonucu göster
    alert(`Tema ${newTheme === 'dark' ? 'Koyu' : 'Açık'} moda değiştirildi!`);
}

// Tema değişikliklerini dinle
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            testTheme();
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
