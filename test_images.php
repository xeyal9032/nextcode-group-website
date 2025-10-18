<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Görsel Test Sayfası</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-image { margin: 20px; padding: 10px; border: 1px solid #ddd; }
        .test-image img { max-width: 300px; border: 2px solid #ccc; }
        .success { border-color: green !important; }
        .error { border-color: red !important; }
    </style>
</head>
<body>
    <h1>🖼️ Görsel Test Sayfası</h1>
    <p>Bu sayfa blog görsellerinin erişilebilirliğini test eder.</p>
    
    <h2>Blog Görselleri:</h2>
    <div class='test-image'>
        <h3>veb-inkisaf-trendleri.jpg</h3>
        <p><strong>URL:</strong> <a href='/images/blog/veb-inkisaf-trendleri.jpg' target='_blank'>/images/blog/veb-inkisaf-trendleri.jpg</a></p>
        <img src='/images/blog/veb-inkisaf-trendleri.jpg' alt='veb-inkisaf-trendleri.jpg' onload="this.parentElement.classList.add('success')" onerror="this.parentElement.classList.add('error')">
    </div>
    <div class='test-image'>
        <h3>mobil-tetbiq-inkisafi.jpg</h3>
        <p><strong>URL:</strong> <a href='/images/blog/mobil-tetbiq-inkisafi.jpg' target='_blank'>/images/blog/mobil-tetbiq-inkisafi.jpg</a></p>
        <img src='/images/blog/mobil-tetbiq-inkisafi.jpg' alt='mobil-tetbiq-inkisafi.jpg' onload="this.parentElement.classList.add('success')" onerror="this.parentElement.classList.add('error')">
    </div>
    <div class='test-image'>
        <h3>reqemsal-marketinq.jpg</h3>
        <p><strong>URL:</strong> <a href='/images/blog/reqemsal-marketinq.jpg' target='_blank'>/images/blog/reqemsal-marketinq.jpg</a></p>
        <img src='/images/blog/reqemsal-marketinq.jpg' alt='reqemsal-marketinq.jpg' onload="this.parentElement.classList.add('success')" onerror="this.parentElement.classList.add('error')">
    </div>
    <div class='test-image'>
        <h3>suni-intellekt.jpg</h3>
        <p><strong>URL:</strong> <a href='/images/blog/suni-intellekt.jpg' target='_blank'>/images/blog/suni-intellekt.jpg</a></p>
        <img src='/images/blog/suni-intellekt.jpg' alt='suni-intellekt.jpg' onload="this.parentElement.classList.add('success')" onerror="this.parentElement.classList.add('error')">
    </div>
    <div class='test-image'>
        <h3>reqemsal-sahibkarlig.jpg</h3>
        <p><strong>URL:</strong> <a href='/images/blog/reqemsal-sahibkarlig.jpg' target='_blank'>/images/blog/reqemsal-sahibkarlig.jpg</a></p>
        <img src='/images/blog/reqemsal-sahibkarlig.jpg' alt='reqemsal-sahibkarlig.jpg' onload="this.parentElement.classList.add('success')" onerror="this.parentElement.classList.add('error')">
    </div>
    <h2>Diğer Test Görselleri:</h2>
    <div class="test-image">
        <h3>SEO Optimization</h3>
        <img src="/images/blog/seo-optimization.jpg" alt="SEO" onload="this.parentElement.classList.add(\"success\")" onerror="this.parentElement.classList.add(\"error\")">
    </div>
    
    <div class="test-image">
        <h3>Web Development</h3>
        <img src="/images/blog/web-development-trends.jpg" alt="Web Dev" onload="this.parentElement.classList.add(\"success\")" onerror="this.parentElement.classList.add(\"error\")">
    </div>
    
    <div class="test-image">
        <h3>E-commerce</h3>
        <img src="/images/blog/ecommerce-development.jpg" alt="E-commerce" onload="this.parentElement.classList.add(\"success\")" onerror="this.parentElement.classList.add(\"error\")">
    </div>
    
    <script>
        // Test sonuçlarını göster
        setTimeout(function() {
            const images = document.querySelectorAll(".test-image img");
            let successCount = 0;
            let errorCount = 0;
            
            images.forEach(function(img) {
                if (img.parentElement.classList.contains("success")) {
                    successCount++;
                } else if (img.parentElement.classList.contains("error")) {
                    errorCount++;
                }
            });
            
            const result = document.createElement("div");
            result.style.cssText = "position: fixed; top: 10px; right: 10px; padding: 15px; background: #f0f0f0; border-radius: 5px; z-index: 1000;";
            result.innerHTML = "<h3>Test Sonuçları:</h3><p style=\"color: green;\">✅ Başarılı: " + successCount + "</p><p style=\"color: red;\">❌ Hata: " + errorCount + "</p>";
            document.body.appendChild(result);
        }, 3000);
    </script>
</body>
</html>