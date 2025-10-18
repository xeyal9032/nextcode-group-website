<?php
/**
 * Pricing Content Database Update Page
 * Web üzerinden pricing içeriklerini veritabanına ekler
 */

// Define secure access constant
define('SECURE_ACCESS', true);

// Security check - temporarily disabled for update
// if (!isset($_GET['key']) || $_GET['key'] !== 'update_pricing_2024') {
//     die('Unauthorized access');
// }

// Database connection
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "<h2>Pricing İçerikleri Veritabanına Ekleniyor...</h2>";
    
    // Pricing content data
    $pricingContent = [
        // Pricing Page Meta
        ['pricing_page_title', 'Qiymət Planları - NextCode Group', 'text', 'pricing'],
        ['pricing_meta_description', 'Peşəkar rəqəmsal marketinq xidmətləri qiymətləri. Biznesinizin böyüməsi üçün mükəmməl planı seçin.', 'text', 'pricing'],
        
        // Pricing Hero Section
        ['pricing_hero_badge', 'Premium Qiymət Planları', 'text', 'pricing'],
        ['pricing_hero_title', 'Mükəmməl Planınızı Seçin', 'text', 'pricing'],
        ['pricing_hero_subtitle', 'Biznesinizin potensialını peşəkar rəqəmsal marketinq xidmətlərimizlə açın. Uğur yolculuğunuza bugün çevik qiymət seçimlərimizlə başlayın.', 'text', 'pricing'],
        
        // Pricing Stats
        ['pricing_stat_clients', 'Məmnun Müştəri', 'text', 'pricing'],
        ['pricing_stat_success', 'Uğur Nisbəti', 'text', 'pricing'],
        ['pricing_stat_support', 'Dəstək', 'text', 'pricing'],
        
        // Pricing Toggle Section
        ['pricing_toggle_title', 'Ödəniş Dövrünü Seçin', 'text', 'pricing'],
        ['pricing_toggle_subtitle', 'İllik planlarla daha çox qənaət edin - 2 ay pulsuz əldə edin!', 'text', 'pricing'],
        ['pricing_toggle_monthly', 'Aylıq', 'text', 'pricing'],
        ['pricing_toggle_annual', 'İllik', 'text', 'pricing'],
        ['pricing_toggle_discount', '20% ENDİRİM', 'text', 'pricing'],
        ['pricing_toggle_info', 'İllik planlar 2 ay pulsuz xidmət daxildir!', 'text', 'pricing'],
        
        // Basic Plan
        ['pricing_basic_badge', 'Başlanğıc', 'text', 'pricing'],
        ['pricing_basic_title', 'Basic Pro', 'text', 'pricing'],
        ['pricing_basic_description', 'Kiçik bizneslər və startaplar üçün onlayn mövcudluqlarını qurmaq istəyənlər üçün mükəmməl.', 'text', 'pricing'],
        ['pricing_basic_price_monthly', '299', 'text', 'pricing'],
        ['pricing_basic_price_yearly', '239', 'text', 'pricing'],
        ['pricing_basic_price_note', 'İllik ödəniş: ₼2,390 (₼598 qənaət)', 'text', 'pricing'],
        ['pricing_basic_button', 'Başla', 'text', 'pricing'],
        ['pricing_basic_guarantee', '30 günlük pul geri qaytarılması zəmanəti', 'text', 'pricing'],
        ['pricing_basic_trial', '14 günlük pulsuz sınaq', 'text', 'pricing'],
        
        // Basic Plan Features
        ['pricing_basic_feature_1_title', 'SEO Optimizasiya', 'text', 'pricing'],
        ['pricing_basic_feature_1_desc', 'Google reytinqi üçün 5 açar söz', 'text', 'pricing'],
        ['pricing_basic_feature_1_benefit', '+150% orqanik trafik artımı', 'text', 'pricing'],
        ['pricing_basic_feature_2_title', 'Sosial Medya İdarəetməsi', 'text', 'pricing'],
        ['pricing_basic_feature_2_desc', 'Facebook + Instagram məzmun yaradılması', 'text', 'pricing'],
        ['pricing_basic_feature_2_benefit', 'Günlük 300+ etkileşim zəmanəti', 'text', 'pricing'],
        ['pricing_basic_feature_3_title', 'Performans Hesabatları', 'text', 'pricing'],
        ['pricing_basic_feature_3_desc', 'Aylıq detallı analitika və görüşlər', 'text', 'pricing'],
        ['pricing_basic_feature_3_benefit', 'ROI izləməsi daxildir', 'text', 'pricing'],
        ['pricing_basic_feature_4_title', 'Prioritet Dəstək', 'text', 'pricing'],
        ['pricing_basic_feature_4_desc', '24 saat cavab zəmanəti', 'text', 'pricing'],
        ['pricing_basic_feature_4_benefit', 'Ekspert məsləhət dəstəyi', 'text', 'pricing'],
        ['pricing_basic_feature_5_title', 'Veb Sayt Optimizasiyası', 'text', 'pricing'],
        ['pricing_basic_feature_5_desc', 'Sürət və performans təkmilləşdirmələri', 'text', 'pricing'],
        ['pricing_basic_feature_5_benefit', '40% daha sürətli yükləmə', 'text', 'pricing'],
        
        // Professional Plan
        ['pricing_professional_badge', 'Peşəkar', 'text', 'pricing'],
        ['pricing_professional_title', 'Professional', 'text', 'pricing'],
        ['pricing_professional_description', 'Böyüyən bizneslər və orta ölçülü şirkətlər üçün hərtərəfli rəqəmsal marketinq axtaranlar üçün ideal.', 'text', 'pricing'],
        ['pricing_professional_price_monthly', '599', 'text', 'pricing'],
        ['pricing_professional_price_yearly', '479', 'text', 'pricing'],
        ['pricing_professional_price_note', 'İllik ödəniş: ₼4,790 (₼1,198 qənaət)', 'text', 'pricing'],
        ['pricing_professional_button', 'İndi Başla - 20% Endirim', 'text', 'pricing'],
        ['pricing_professional_guarantee', '30 günlük pul geri qaytarılması zəmanəti', 'text', 'pricing'],
        ['pricing_professional_trial', '14 günlük pulsuz sınaq + bonus xüsusiyyətlər', 'text', 'pricing'],
        ['pricing_professional_popular', 'Ən Populyar', 'text', 'pricing'],
        
        // Professional Plan Features
        ['pricing_professional_feature_1_title', 'Qabaqcıl SEO', 'text', 'pricing'],
        ['pricing_professional_feature_1_desc', '15 açar söz + rəqib analizi', 'text', 'pricing'],
        ['pricing_professional_feature_1_benefit', '+300% orqanik trafik artımı', 'text', 'pricing'],
        ['pricing_professional_feature_2_title', 'Çox Platformlu Sosial Medya', 'text', 'pricing'],
        ['pricing_professional_feature_2_desc', '4 platform + məzmun yaradılması', 'text', 'pricing'],
        ['pricing_professional_feature_2_benefit', 'Günlük 500+ etkileşim', 'text', 'pricing'],
        ['pricing_professional_feature_3_title', 'Google Ads İdarəetməsi', 'text', 'pricing'],
        ['pricing_professional_feature_3_desc', 'Kampaniya qurulumu və optimizasiya', 'text', 'pricing'],
        ['pricing_professional_feature_3_benefit', '₼500 reklam krediti daxildir', 'text', 'pricing'],
        ['pricing_professional_feature_4_title', 'Brend Xidmətləri', 'text', 'pricing'],
        ['pricing_professional_feature_4_desc', 'Logo və korporativ kimlik dizaynı', 'text', 'pricing'],
        ['pricing_professional_feature_4_benefit', 'Peşəkar brend qaydaları', 'text', 'pricing'],
        ['pricing_professional_feature_5_title', 'Həftəlik Hesabatlar', 'text', 'pricing'],
        ['pricing_professional_feature_5_desc', 'Detallı performans analitikası', 'text', 'pricing'],
        ['pricing_professional_feature_5_benefit', 'Real vaxt dashboard girişi', 'text', 'pricing'],
        ['pricing_professional_feature_6_title', 'Telefon + Email Dəstək', 'text', 'pricing'],
        ['pricing_professional_feature_6_desc', 'Prioritet müştəri xidməti', 'text', 'pricing'],
        ['pricing_professional_feature_6_benefit', 'Dedicated hesab meneceri', 'text', 'pricing'],
        
        // Enterprise Plan
        ['pricing_enterprise_badge', 'Enterprise', 'text', 'pricing'],
        ['pricing_enterprise_title', 'Enterprise', 'text', 'pricing'],
        ['pricing_enterprise_description', 'Böyük şirkətlər və korporasiyalar üçün tam miqyaslı rəqəmsal marketinq tələb edənlər üçün tam həll.', 'text', 'pricing'],
        ['pricing_enterprise_price_monthly', '999', 'text', 'pricing'],
        ['pricing_enterprise_price_yearly', '799', 'text', 'pricing'],
        ['pricing_enterprise_price_note', 'İllik ödəniş: ₼7,990 (₼1,998 qənaət)', 'text', 'pricing'],
        ['pricing_enterprise_button', 'Satışla Əlaqə', 'text', 'pricing'],
        ['pricing_enterprise_guarantee', 'Tam zəmanət və dəstək', 'text', 'pricing'],
        ['pricing_enterprise_trial', '30 günlük pulsuz sınaq + VIP dəstək', 'text', 'pricing'],
        
        // Enterprise Plan Features
        ['pricing_enterprise_feature_1_title', 'Limitsiz SEO', 'text', 'pricing'],
        ['pricing_enterprise_feature_1_desc', 'Limitsiz açar söz + AI analizi', 'text', 'pricing'],
        ['pricing_enterprise_feature_1_benefit', '+500% orqanik trafik artımı', 'text', 'pricing'],
        ['pricing_enterprise_feature_2_title', 'Bütün Sosial Medya Platformları', 'text', 'pricing'],
        ['pricing_enterprise_feature_2_desc', 'Tam platform idarəetməsi + avtomatlaşdırma', 'text', 'pricing'],
        ['pricing_enterprise_feature_2_benefit', 'Günlük 1000+ etkileşim', 'text', 'pricing'],
        ['pricing_enterprise_feature_3_title', 'Tam Reklam İdarəetməsi', 'text', 'pricing'],
        ['pricing_enterprise_feature_3_desc', 'Google, Facebook, LinkedIn, TikTok', 'text', 'pricing'],
        ['pricing_enterprise_feature_3_benefit', '₼1000 reklam krediti daxildir', 'text', 'pricing'],
        ['pricing_enterprise_feature_4_title', 'Tam Brend Paketi', 'text', 'pricing'],
        ['pricing_enterprise_feature_4_desc', 'Korporativ kimlik + marketinq materialları', 'text', 'pricing'],
        ['pricing_enterprise_feature_4_benefit', 'Tam brend qaydaları', 'text', 'pricing'],
        ['pricing_enterprise_feature_5_title', '24/7 VIP Dəstək', 'text', 'pricing'],
        ['pricing_enterprise_feature_5_desc', 'Dedicated hesab meneceri', 'text', 'pricing'],
        ['pricing_enterprise_feature_5_benefit', 'Prioritet cavab vaxtı', 'text', 'pricing'],
        ['pricing_enterprise_feature_6_title', 'Günlük Hesabatlar', 'text', 'pricing'],
        ['pricing_enterprise_feature_6_desc', 'Real vaxt performans analitikası', 'text', 'pricing'],
        ['pricing_enterprise_feature_6_benefit', 'Xüsusi dashboard girişi', 'text', 'pricing'],
        
        // CTA Section
        ['pricing_cta_title', 'Biznesinizi Dəyişdirməyə Hazırsınız?', 'text', 'pricing'],
        ['pricing_cta_subtitle', 'Minlərlə uğurlu biznesin NextCode Group-a rəqəmsal marketinq ehtiyacları üçün etibar etdiyi qoşulun. Uğur yolculuğunuza bugün başlayın!', 'text', 'pricing'],
        ['pricing_cta_button_1', 'Pulsuz Məsləhət Al', 'text', 'pricing'],
        ['pricing_cta_button_2', '14 Günlük Sınaq Başlat', 'text', 'pricing'],
        
        // Common Elements
        ['pricing_features_header', 'Daxil olan xidmətlər:', 'text', 'pricing'],
        ['pricing_period_monthly', '/ay', 'text', 'pricing'],
        ['pricing_period_yearly', '/il', 'text', 'pricing']
    ];
    
    // Insert or update content
    $stmt = $pdo->prepare("
        INSERT INTO site_content 
        (content_key, content_value, content_type, page_section, is_active, sort_order) 
        VALUES (?, ?, ?, ?, 1, 0)
        ON DUPLICATE KEY UPDATE 
        content_value = VALUES(content_value),
        content_type = VALUES(content_type),
        page_section = VALUES(page_section),
        is_active = VALUES(is_active),
        sort_order = VALUES(sort_order),
        updated_at = CURRENT_TIMESTAMP
    ");
    
    $successCount = 0;
    echo "<ul>";
    foreach ($pricingContent as $content) {
        try {
            $stmt->execute($content);
            $successCount++;
            echo "<li style='color: green;'>✓ {$content[0]}: " . substr($content[1], 0, 50) . "...</li>";
        } catch (Exception $e) {
            echo "<li style='color: red;'>✗ Error inserting {$content[0]}: " . $e->getMessage() . "</li>";
        }
    }
    echo "</ul>";
    
    echo "<h3>✅ Pricing İçerikleri Başarıyla Eklendi!</h3>";
    echo "<p>Toplam <strong>{$successCount}</strong> içerik güncellendi.</p>";
    
    // Verify content
    echo "<h3>Veritabanındaki Pricing İçerikleri:</h3>";
    $stmt = $pdo->prepare("SELECT content_key, content_value FROM site_content WHERE page_section = 'pricing' ORDER BY content_key");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach ($results as $row) {
        echo "<li><strong>{$row['content_key']}</strong>: " . substr($row['content_value'], 0, 100) . "...</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Hata: " . $e->getMessage() . "</h3>";
}
?>
