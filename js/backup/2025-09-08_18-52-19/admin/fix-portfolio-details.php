<?php
// Fix technologies column
$pdo = new PDO('mysql:host=gtorg.mysql.tools;dbname=gtorg_nextcode;charset=utf8mb4', 'gtorg_nextcode', ';849#dVEyg', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

echo "Fixing technologies column...\n";
$pdo->exec('ALTER TABLE portfolio_projects MODIFY COLUMN technologies TEXT');

echo "Technologies column fixed!\n";

// Now update the project details
$projectUpdates = [
    1 => [
        'title' => 'Modern Kurumsal Web Sitesi',
        'short_description' => 'Teknoloji şirketi için modern, responsive ve SEO uyumlu kurumsal web sitesi tasarımı.',
        'technologies' => 'HTML5,CSS3,JavaScript,PHP'
    ],
    2 => [
        'title' => 'AI Destekli Müşteri Analiz Platformu', 
        'short_description' => 'Büyük veri analizi ve yapay zeka kullanarak müşteri davranışlarını analiz eden platform.',
        'technologies' => 'Python,TensorFlow,Apache Spark,MySQL'
    ],
    3 => [
        'title' => 'E-Ticaret Platformu',
        'short_description' => 'Giyim markası için kapsamlı e-ticaret çözümü. Ödeme entegrasyonları, stok yönetimi ile birlikte.',
        'technologies' => 'React,Node.js,MongoDB,Stripe'
    ],
    4 => [
        'title' => 'Mobil Fitness Uygulaması',
        'short_description' => 'Kişiselleştirilmiş antrenman programları, beslenme takibi ve sosyal özellikler içeren fitness uygulaması.',
        'technologies' => 'React Native,Firebase,Node.js'
    ],
    5 => [
        'title' => 'SaaS CRM Sistemi',
        'short_description' => 'Küçük ve orta ölçekli işletmeler için bulut tabanlı müşteri ilişkileri yönetim sistemi.',
        'technologies' => 'Angular,Spring Boot,PostgreSQL'
    ],
    6 => [
        'title' => 'Kripto Para Cüzdan Uygulaması',
        'short_description' => 'Güvenli, çoklu kripto para desteği olan mobil cüzdan uygulaması. DeFi entegrasyonu ve NFT desteği.',
        'technologies' => 'React Native,Solidity,Web3.js'
    ],
    7 => [
        'title' => 'Hastane Yönetim Sistemi',
        'short_description' => 'Hastane ve klinikler için kapsamlı dijital hasta yönetim sistemi. Randevu, hasta kayıtları ve tıbbi veri yönetimi.',
        'technologies' => 'React,.NET Core,SQL Server'
    ],
    8 => [
        'title' => 'Çok Kanallı E-ticaret Platformu',
        'short_description' => 'B2B ve B2C satışları destekleyen, çoklu mağaza yönetimi olan kapsamlı e-ticaret platformu.',
        'technologies' => 'Vue.js,Django,PostgreSQL'
    ],
    9 => [
        'title' => 'Finansal Analiz Dashboard',
        'short_description' => 'Gerçek zamanlı finansal veri analizi ve görselleştirme platformu. Portföy yönetimi ve risk analizi araçları.',
        'technologies' => 'React,D3.js,Python'
    ],
    10 => [
        'title' => 'Mobil Bankacılık Uygulaması',
        'short_description' => 'Modern mobil bankacılık deneyimi sunan, biyometrik kimlik doğrulama ve güvenli işlem desteği olan uygulama.',
        'technologies' => 'React Native,Node.js,PostgreSQL'
    ],
    11 => [
        'title' => 'IoT Akıllı Ev Platformu',
        'short_description' => 'Akıllı ev cihazlarını yöneten, enerji tasarrufu sağlayan ve güvenlik özellikleri olan IoT platformu.',
        'technologies' => 'React,Node.js,MQTT'
    ],
    12 => [
        'title' => 'Sosyal Medya Yönetim Platformu',
        'short_description' => 'Çoklu sosyal medya hesaplarını tek platformdan yöneten, içerik planlama ve analiz araçları sunan sistem.',
        'technologies' => 'Vue.js,Laravel,MySQL'
    ],
    13 => [
        'title' => 'Oyun Geliştirme Platformu',
        'short_description' => 'Oyun geliştiriciler için araçlar, asset yönetimi ve yayın platformu sunan kapsamlı oyun geliştirme ekosistemi.',
        'technologies' => 'Unity,C#,.NET Core'
    ],
    14 => [
        'title' => 'Restoran Zinciri Rebrand',
        'short_description' => 'Cross-platform mobil uygulama geliştirme platformu ile iOS ve Android için tek kod tabanından uygulama oluşturma.',
        'technologies' => 'Adobe Illustrator,Photoshop,InDesign'
    ],
    15 => [
        'title' => 'Dijital Pazarlama Kampanyası',
        'short_description' => 'Gerçek zamanlı veri analizi, görselleştirme ve raporlama araçları sunan kapsamlı business intelligence platformu.',
        'technologies' => 'Google Ads,Facebook Ads,SEO Tools'
    ],
    16 => [
        'title' => 'Eğitim Platformu',
        'short_description' => 'Mikroservis mimarisi için API yönetimi, güvenlik, rate limiting ve monitoring özellikleri sunan gateway sistemi.',
        'technologies' => 'Vue.js,Laravel,MySQL'
    ]
];

foreach ($projectUpdates as $id => $data) {
    $stmt = $pdo->prepare("UPDATE portfolio_projects SET title = ?, short_description = ?, technologies = ? WHERE id = ?");
    $stmt->execute([$data['title'], $data['short_description'], $data['technologies'], $id]);
    echo "✅ Updated project $id details\n";
}

echo "All portfolio projects updated successfully!\n";
?>

