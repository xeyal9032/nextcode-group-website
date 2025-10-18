<?php
// Debug contact form
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        form { max-width: 500px; }
        input, select, textarea { width: 100%; padding: 8px; margin: 5px 0; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Contact Form Debug</h1>
    
    <form id="debugForm">
        <div>
            <label>Ad *</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Email *</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Telefon</label>
            <input type="tel" name="phone">
        </div>
        <div>
            <label>Şirkət</label>
            <input type="text" name="company">
        </div>
        <div>
            <label>Xidmət</label>
            <select name="service">
                <option value="">Seçin</option>
                <option value="seo">SEO</option>
                <option value="web-design">Web Dizayn</option>
            </select>
        </div>
        <div>
            <label>Büdcə</label>
            <select name="budget">
                <option value="">Seçin</option>
                <option value="500-1000">500-1000 AZN</option>
                <option value="1000-2500">1000-2500 AZN</option>
            </select>
        </div>
        <div>
            <label>Mesaj *</label>
            <textarea name="message" rows="4" required></textarea>
        </div>
        <button type="submit">Test Göndər</button>
    </form>
    
    <div id="result"></div>
    
    <script>
        document.getElementById('debugForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultDiv = document.getElementById('result');
            
            // Show form data
            let debugInfo = '<div class="debug"><h3>Göndərilən Məlumatlar:</h3><ul>';
            for (let [key, value] of formData.entries()) {
                debugInfo += `<li><strong>${key}:</strong> ${value}</li>`;
            }
            debugInfo += '</ul></div>';
            
            resultDiv.innerHTML = debugInfo + '<div class="debug">Göndərilir...</div>';
            
            fetch('contact-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(data => {
                console.log('Response data:', data);
                try {
                    const jsonData = JSON.parse(data);
                    if (jsonData.success) {
                        resultDiv.innerHTML += `
                            <div class="debug success">
                                <h3>✅ Uğurlu!</h3>
                                <p>${jsonData.message}</p>
                                ${jsonData.whatsapp_url ? `<p><strong>WhatsApp Link:</strong> <a href="${jsonData.whatsapp_url}" target="_blank">WhatsApp-da Aç</a></p>` : ''}
                            </div>
                        `;
                        
                        if (jsonData.whatsapp_url && jsonData.redirect) {
                            setTimeout(() => {
                                window.open(jsonData.whatsapp_url, '_blank');
                            }, 2000);
                        }
                    } else {
                        resultDiv.innerHTML += `
                            <div class="debug error">
                                <h3>❌ Xəta!</h3>
                                <p>${jsonData.message}</p>
                            </div>
                        `;
                    }
                } catch (e) {
                    resultDiv.innerHTML += `
                        <div class="debug error">
                            <h3>❌ JSON Parse Xətası!</h3>
                            <p>Server cavabı: ${data}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML += `
                    <div class="debug error">
                        <h3>❌ Şəbəkə Xətası!</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            });
        });
    </script>
</body>
</html>
