<?php
// Portfolio tablolarının yapısını kontrol et

// Include API router for handling API requests
require_once __DIR__ . '/../api-router.php';

require_once __DIR__ . '/../config/database.php';

try {
    echo "<h2>Portfolio Tablolarının Yapısı</h2>";
    
    // portfolio_projects tablosunun yapısını kontrol et
    echo "<h3>portfolio_projects Tablosu</h3>";
    $stmt = $pdo->query("DESCRIBE portfolio_projects");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // portfolio_categories tablosunun yapısını kontrol et
    echo "<h3>portfolio_categories Tablosu</h3>";
    $stmt = $pdo->query("DESCRIBE portfolio_categories");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Örnek veri kontrolü
    echo "<h3>Örnek Veri Kontrolü</h3>";
    
    $stmt = $pdo->query("SELECT * FROM portfolio_projects LIMIT 1");
    $project = $stmt->fetch();
    
    if ($project) {
        echo "<h4>İlk Proje Verisi:</h4>";
        echo "<pre>" . print_r($project, true) . "</pre>";
    } else {
        echo "<p>Portfolio projeleri bulunamadı!</p>";
    }
    
    // Kategori verisi kontrolü
    $stmt = $pdo->query("SELECT * FROM portfolio_categories LIMIT 1");
    $category = $stmt->fetch();
    
    if ($category) {
        echo "<h4>İlk Kategori Verisi:</h4>";
        echo "<pre>" . print_r($category, true) . "</pre>";
    } else {
        echo "<p>Portfolio kategorileri bulunamadı!</p>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Hata Oluştu!</h3>";
    echo "<p>Hata: " . $e->getMessage() . "</p>";
}
?>
