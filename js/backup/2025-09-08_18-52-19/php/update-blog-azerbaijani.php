<?php
// Include API router for handling API requests
require_once 'api-router.php';

require_once 'config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Blog yazısı 1 - Web Development
    $stmt = $pdo->prepare("UPDATE blog_posts SET 
        title = ?, 
        excerpt = ?, 
        content = ? 
        WHERE id = 1");
    
    $title1 = "2024-cü ildə Müasir Veb İnkişaf Tendensiyaları";
    $excerpt1 = "2024-cü ildə veb inkişafını formalaşdıran ən son texnologiyaları kəşf edin";
    $content1 = "<h2>Müasir Veb İnkişaf Tendensiyaları</h2><p>2024-cü il üçün veb inkişafında ən son tendensiyaları kəşf edin. AI inteqrasiyasından proqressiv veb tətbiqlərə qədər, veb inkişafının gələcəyini formalaşdıran texnologiyaları öyrənin.</p><p>Əsas tendensiyalar:</p><ul><li>AI ilə gücləndirilmiş inkişaf alətləri</li><li>Serversiz arxitektura</li><li>Proqressiv Veb Tətbiqləri (PWA)</li><li>WebAssembly inteqrasiyası</li></ul>";
    
    $stmt->execute([$title1, $excerpt1, $content1]);
    echo "Blog yazısı 1 yeniləndi\n";
    
    // Blog yazısı 2 - Mobile Apps
    $stmt = $pdo->prepare("UPDATE blog_posts SET 
        title = ?, 
        excerpt = ?, 
        content = ? 
        WHERE id = 2");
    
    $title2 = "Responsiv Mobil Tətbiqlər Yaratmaq";
    $excerpt2 = "Responsiv mobil tətbiqlər yaratmaq üçün ətraflı bələdçi";
    $content2 = "<h2>Mobil Tətbiq İnkişaf Bələdçisi</h2><p>Bütün cihazlarda mükəmməl işləyən responsiv mobil tətbiqlər yaratmağı öyrənin. Bu bələdçi sizə müasir mobil inkişaf texnikalarını və ən yaxşı təcrübələri təqdim edir.</p><p>Əsas mövzular:</p><ul><li>Responsiv dizayn prinsipləri</li><li>Cross-platform inkişaf</li><li>Performans optimallaşdırması</li><li>İstifadəçi təcrübəsi dizaynı</li></ul>";
    
    $stmt->execute([$title2, $excerpt2, $content2]);
    echo "Blog yazısı 2 yeniləndi\n";
    
    // Blog yazısı 3 - Digital Marketing
    $stmt = $pdo->prepare("UPDATE blog_posts SET 
        title = ?, 
        excerpt = ?, 
        content = ? 
        WHERE id = 3");
    
    $title3 = "Kiçik Biznes üçün Rəqəmsal Marketinq Strategiyaları";
    $excerpt3 = "Kiçik biznesinizi böyütmək üçün sübut edilmiş rəqəmsal marketinq strategiyaları";
    $content3 = "<h2>Kiçik Bizneslər üçün Rəqəmsal Marketinq</h2><p>Kiçik bizneslərin böyüməsinə kömək edən effektiv rəqəmsal marketinq strategiyaları. Bu strategiyalar məhdud büdcə ilə maksimum nəticə əldə etməyə yönəlmişdir.</p><p>Əsas strategiyalar:</p><ul><li>Sosial media marketinqi</li><li>Məzmun marketinqi</li><li>Axtarış motoru optimallaşdırması (SEO)</li><li>Email marketinq kampaniyaları</li></ul>";
    
    $stmt->execute([$title3, $excerpt3, $content3]);
    echo "Blog yazısı 3 yeniləndi\n";
    
    // Blog yazısı 4 - AI
    $stmt = $pdo->prepare("UPDATE blog_posts SET 
        title = ?, 
        excerpt = ?, 
        content = ? 
        WHERE id = 4");
    
    $title4 = "Süni İntellektin Gələcəyi";
    $excerpt4 = "Süni intellektin transformativ gücünü araşdırmaq";
    $content4 = "<h2>AI İnqilabı</h2><p>Süni İntellekt sənayeləri dəyişdirir və bizim işləmə tərzimizi yenidən formalaşdırır. Bu texnologiyanın gələcəkdə necə inkişaf edəcəyini və bizim həyatımıza necə təsir edəcəyini öyrənin.</p><p>AI-nin təsir sahələri:</p><ul><li>Səhiyyə və tibb</li><li>Maliyyə xidmətləri</li><li>Nəqliyyat və avtonomluq</li><li>Təhsil və öyrənmə</li></ul>";
    
    $stmt->execute([$title4, $excerpt4, $content4]);
    echo "Blog yazısı 4 yeniləndi\n";
    
    // Blog yazısı 5 - Entrepreneurship
    $stmt = $pdo->prepare("UPDATE blog_posts SET 
        title = ?, 
        excerpt = ?, 
        content = ? 
        WHERE id = 5");
    
    $title5 = "Rəqəmsal Dövrdə Sahibkarlıq";
    $excerpt5 = "Rəqəmsal dövrdə uğurlu biznes qurmaq üçün bələdçi";
    $content5 = "<h2>Rəqəmsal Sahibkarlıq</h2><p>Bugünkü rəqəmsal mənzərədə biznes qurmaq yeni strategiyalar və yanaşmalar tələb edir. Uğurlu rəqəmsal sahibkar olmaq üçün lazım olan bilik və bacarıqları öyrənin.</p><p>Əsas mövzular:</p><ul><li>Rəqəmsal biznes modelləri</li><li>Online brend yaratmaq</li><li>E-ticarət strategiyaları</li><li>Rəqəmsal marketinq və satış</li></ul>";
    
    $stmt->execute([$title5, $excerpt5, $content5]);
    echo "Blog yazısı 5 yeniləndi\n";
    
    echo "Bütün blog yazıları Azərbaycan dilinə çevrildi!\n";
    
} catch (Exception $e) {
    echo "Xəta: " . $e->getMessage() . "\n";
}
?>