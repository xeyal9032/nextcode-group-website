<?php
// Portfolio Database Setup Script
// Bu script portfolio sistemi için gerekli tabloları oluşturur

// Include API router for handling API requests
require_once __DIR__ . '/../api-router.php';

require_once __DIR__ . '/../config/database.php';

try {
    echo "<h2>NextCode Portfolio Database Setup</h2>";
    echo "<p>Veritabanına bağlanılıyor...</p>";
    
    // Portfolio tablolarını oluştur
    $portfolioTablesSQL = file_get_contents(__DIR__ . '/portfolio_tables.sql');
    $statements = array_filter(array_map('trim', explode(';', $portfolioTablesSQL)));
    
    echo "<p>" . count($statements) . " SQL komutu çalıştırılıyor...</p>";
    echo "<ul>";
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $pdo->exec($statement);
            
            // Tablo adını çıkar
            if (preg_match('/CREATE TABLE.*?`?([a-zA-Z_]+)`?/i', $statement, $matches)) {
                echo "<li style='color: green;'>✓ Tablo oluşturuldu: {$matches[1]}</li>";
            } elseif (preg_match('/INSERT.*?INTO.*?`?([a-zA-Z_]+)`?/i', $statement, $matches)) {
                echo "<li style='color: blue;'>✓ Veri eklendi: {$matches[1]}</li>";
            } else {
                echo "<li style='color: gray;'>✓ Komut çalıştırıldı</li>";
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false) {
                if (preg_match('/CREATE TABLE.*?`?([a-zA-Z_]+)`?/i', $statement, $matches)) {
                    echo "<li style='color: orange;'>⚠ Tablo zaten mevcut: {$matches[1]}</li>";
                }
            } elseif (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "<li style='color: orange;'>⚠ Tekrarlanan veri (atlandı)</li>";
            } else {
                echo "<li style='color: red;'>✗ Hata: " . $e->getMessage() . "</li>";
            }
        }
    }
    
    echo "</ul>";
    
    // Örnek verileri ekle
    echo "<h3>Örnek Portfolio Verileri Ekleniyor...</h3>";
    $sampleDataSQL = file_get_contents(__DIR__ . '/sample_portfolio_data.sql');
    $sampleStatements = array_filter(array_map('trim', explode(';', $sampleDataSQL)));
    
    echo "<ul>";
    foreach ($sampleStatements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) continue;
        
        try {
            $pdo->exec($statement);
            
            if (preg_match('/INSERT.*?INTO.*?`?([a-zA-Z_]+)`?/i', $statement, $matches)) {
                echo "<li style='color: blue;'>✓ Örnek veri eklendi: {$matches[1]}</li>";
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "<li style='color: orange;'>⚠ Veri zaten mevcut (atlandı)</li>";
            } else {
                echo "<li style='color: red;'>✗ Hata: " . $e->getMessage() . "</li>";
            }
        }
    }
    echo "</ul>";
    
    // Tabloları doğrula
    echo "<h3>Tablo Doğrulaması</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'portfolio_%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $expectedTables = [
        'portfolio_categories',
        'portfolio_projects',
        'portfolio_images',
        'portfolio_testimonials'
    ];
    
    echo "<ul>";
    foreach ($expectedTables as $table) {
        if (in_array($table, $tables)) {
            echo "<li style='color: green;'>✓ {$table}</li>";
        } else {
            echo "<li style='color: red;'>✗ {$table} (eksik)</li>";
        }
    }
    echo "</ul>";
    
    // Portfolio verilerini kontrol et
    echo "<h3>Portfolio Veri Kontrolü</h3>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM portfolio_categories");
    $categoryCount = $stmt->fetch()['count'];
    echo "<p>Kategori sayısı: <strong>{$categoryCount}</strong></p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM portfolio_projects");
    $projectCount = $stmt->fetch()['count'];
    echo "<p>Proje sayısı: <strong>{$projectCount}</strong></p>";
    
    if ($projectCount > 0) {
        $stmt = $pdo->query("SELECT title, category_id FROM portfolio_projects LIMIT 5");
        $projects = $stmt->fetchAll();
        
        echo "<h4>İlk 5 Proje:</h4>";
        echo "<ul>";
        foreach ($projects as $project) {
            echo "<li>{$project['title']} (Kategori ID: {$project['category_id']})</li>";
        }
        echo "</ul>";
    }
    
    echo "<h3>✅ Portfolio Kurulumu Tamamlandı!</h3>";
    echo "<p>Artık portfolio sayfası çalışmalı ve 'Detallar' butonları açılmalı.</p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Hata Oluştu!</h3>";
    echo "<p>Hata: " . $e->getMessage() . "</p>";
}
?>
