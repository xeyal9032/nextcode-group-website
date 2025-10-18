<?php
// 404 Error Page - NextCode Admin

http_response_code(404);
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Sayfa Bulunamadı | NextCode Admin</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
            margin: 0; 
            padding: 20px; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
        }
        .container { 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.1); 
            text-align: center; 
            max-width: 600px; 
        }
        .error-code { 
            font-size: 6em; 
            color: #dc3545; 
            margin: 0; 
            font-weight: bold; 
        }
        .error-message { 
            font-size: 1.5em; 
            color: #333; 
            margin: 20px 0; 
        }
        .error-description { 
            color: #666; 
            margin: 20px 0; 
            line-height: 1.6; 
        }
        .btn { 
            display: inline-block; 
            padding: 12px 25px; 
            background: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 8px; 
            margin: 10px; 
            transition: all 0.3s; 
        }
        .btn:hover { 
            background: #0056b3; 
            transform: translateY(-2px); 
        }
        .links { 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #e9ecef; 
        }
        .links h3 { 
            color: #333; 
            margin-bottom: 15px; 
        }
        .links a { 
            display: block; 
            color: #007bff; 
            text-decoration: none; 
            margin: 5px 0; 
            padding: 5px 0; 
        }
        .links a:hover { 
            text-decoration: underline; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">404</div>
        <div class="error-message">Sayfa Bulunamadı</div>
        <div class="error-description">
            Üzr istəyirik, axtardığınız səhifə mövcud deyil və ya köçürülüb.
        </div>
        
        <div class="links">
            <h3>🔗 Faydalı Linklər</h3>
            <a href="login.php">Admin Giriş</a>
            <a href="index.php">Admin Dashboard</a>
            <a href="content.php">İçerik Yönetimi</a>
            <a href="database-status.php">Veritabanı Durumu</a>
            <a href="update-database.php">Veritabanı Güncelle</a>
            <a href="test-404.php">404 Test</a>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="index.php" class="btn">Dashboard'a Dön</a>
            <a href="login.php" class="btn">Giriş Yap</a>
        </div>
        
        <div style="margin-top: 20px; font-size: 0.9em; color: #999;">
            NextCode Group Admin Panel
        </div>
    </div>
</body>
</html>



