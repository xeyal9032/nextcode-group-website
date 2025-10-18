<?php
// Add missing columns to portfolio_projects table
$pdo = new PDO('mysql:host=gtorg.mysql.tools;dbname=gtorg_nextcode;charset=utf8mb4', 'gtorg_nextcode', ';849#dVEyg', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

echo "Adding missing columns to portfolio_projects...\n";

// Add missing columns
$columns = [
    'challenges' => 'TEXT',
    'solutions' => 'TEXT', 
    'features' => 'TEXT',
    'description' => 'LONGTEXT',
    'full_description' => 'LONGTEXT',
    'client_name' => 'VARCHAR(255)',
    'project_date' => 'DATE',
    'project_url' => 'VARCHAR(500)',
    'github_url' => 'VARCHAR(500)'
];

foreach ($columns as $column => $type) {
    try {
        $pdo->exec("ALTER TABLE portfolio_projects ADD COLUMN $column $type");
        echo "✅ Added column: $column\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "⚠️  Column $column already exists\n";
        } else {
            echo "❌ Error adding $column: " . $e->getMessage() . "\n";
        }
    }
}

echo "Updating sample data with additional fields...\n";

// Update existing projects with sample data
$updates = [
    1 => [
        'description' => 'TechCorp A.Ş. için modern ve profesyonel kurumsal web sitesi geliştirdik. Site, kullanıcı deneyimi odaklı tasarım, SEO optimizasyonu ve responsive yapı ile donatıldı. Yönetim paneli sayesinde içerik güncellemeleri kolayca yapılabiliyor.',
        'challenges' => 'Kurumsal kimlik ile modern tasarım arasında denge kurmak, SEO performansını optimize etmek ve yönetim panelini kullanıcı dostu hale getirmek.',
        'solutions' => 'Responsive tasarım prensipleri uygulandı, SEO optimizasyonu yapıldı ve kullanıcı dostu admin paneli geliştirildi.',
        'features' => "Responsive Tasarım\nSEO Optimizasyonu\nYönetim Paneli\nİletişim Formu\nBlog Sistemi\nÇoklu Dil Desteği",
        'client_name' => 'TechCorp A.Ş.',
        'project_date' => '2024-01-15',
        'project_url' => 'https://techcorp.com'
    ],
    2 => [
        'description' => 'Büyük veri analizi ve yapay zeka kullanarak müşteri davranışlarını analiz eden kapsamlı platform. Gerçek zamanlı veri işleme ve tahminleme özellikleri ile donatıldı.',
        'challenges' => 'Büyük veri setlerini işlemek, gerçek zamanlı analiz yapmak ve kullanıcı dostu dashboard tasarlamak.',
        'solutions' => 'Apache Spark ile büyük veri işleme, TensorFlow ile makine öğrenmesi modelleri ve React ile modern dashboard geliştirildi.',
        'features' => "Gerçek Zamanlı Analiz\nMakine Öğrenmesi\nBüyük Veri İşleme\nDashboard\nAPI Entegrasyonu\nRaporlama Sistemi",
        'client_name' => 'DataCorp Ltd.',
        'project_date' => '2024-02-20',
        'project_url' => 'https://analytics.datacorp.com'
    ],
    3 => [
        'description' => 'Giyim markası için kapsamlı e-ticaret çözümü. Ödeme entegrasyonları, stok yönetimi, kullanıcı hesapları ve admin paneli ile tam özellikli platform.',
        'challenges' => 'Güvenli ödeme sistemi entegrasyonu, stok takibi ve kullanıcı deneyimini optimize etmek.',
        'solutions' => 'Stripe ödeme entegrasyonu, MongoDB ile esnek veri yapısı ve React ile hızlı kullanıcı arayüzü geliştirildi.',
        'features' => "E-Ticaret Sistemi\nÖdeme Entegrasyonu\nStok Yönetimi\nKullanıcı Hesapları\nAdmin Paneli\nMobil Uyumlu",
        'client_name' => 'Fashion Store',
        'project_date' => '2024-03-10',
        'project_url' => 'https://fashionstore.com'
    ]
];

foreach ($updates as $id => $data) {
    $sql = "UPDATE portfolio_projects SET ";
    $params = [];
    $setParts = [];
    
    foreach ($data as $field => $value) {
        $setParts[] = "$field = ?";
        $params[] = $value;
    }
    
    $sql .= implode(', ', $setParts) . " WHERE id = ?";
    $params[] = $id;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo "✅ Updated project $id\n";
}

echo "Portfolio database enhancement completed successfully!\n";
?>
