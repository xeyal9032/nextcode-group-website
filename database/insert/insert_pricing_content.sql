-- Pricing Page Content for SQLite Database
-- Azerbaycan dilinde pricing sayfası içerikleri

-- Pricing Page Meta
INSERT OR REPLACE INTO site_content (content_key, content_value, content_type, page_section, description) VALUES
('pricing_page_title', 'Qiymət Planları - NextCode Group', 'text', 'pricing', 'Pricing sayfası başlığı'),
('pricing_meta_description', 'Peşəkar rəqəmsal marketinq xidmətləri qiymətləri. Biznesinizin böyüməsi üçün mükəmməl planı seçin.', 'text', 'pricing', 'Pricing sayfası meta açıklaması'),

-- Pricing Hero Section
('pricing_hero_badge', 'Premium Qiymət Planları', 'text', 'pricing', 'Pricing hero badge metni'),
('pricing_hero_title', 'Mükəmməl Planınızı Seçin', 'text', 'pricing', 'Pricing hero başlığı'),
('pricing_hero_subtitle', 'Biznesinizin potensialını peşəkar rəqəmsal marketinq xidmətlərimizlə açın. Uğur yolculuğunuza bugün çevik qiymət seçimlərimizlə başlayın.', 'text', 'pricing', 'Pricing hero alt başlığı'),

-- Pricing Stats
('pricing_stat_clients', 'Məmnun Müştəri', 'text', 'pricing', 'Müştəri sayısı etiketi'),
('pricing_stat_success', 'Uğur Nisbəti', 'text', 'pricing', 'Uğur nisbəti etiketi'),
('pricing_stat_support', 'Dəstək', 'text', 'pricing', 'Dəstək etiketi'),

-- Pricing Toggle Section
('pricing_toggle_title', 'Ödəniş Dövrünü Seçin', 'text', 'pricing', 'Pricing toggle başlığı'),
('pricing_toggle_subtitle', 'İllik planlarla daha çox qənaət edin - 2 ay pulsuz əldə edin!', 'text', 'pricing', 'Pricing toggle alt başlığı'),
('pricing_toggle_monthly', 'Aylıq', 'text', 'pricing', 'Aylıq ödəniş etiketi'),
('pricing_toggle_annual', 'İllik', 'text', 'pricing', 'İllik ödəniş etiketi'),
('pricing_toggle_discount', '20% ENDİRİM', 'text', 'pricing', 'Endirim etiketi'),
('pricing_toggle_info', 'İllik planlar 2 ay pulsuz xidmət daxildir!', 'text', 'pricing', 'Pricing toggle bilgi metni'),

-- Basic Plan
('pricing_basic_badge', 'Başlanğıc', 'text', 'pricing', 'Basic plan badge'),
('pricing_basic_title', 'Basic Pro', 'text', 'pricing', 'Basic plan başlığı'),
('pricing_basic_description', 'Kiçik bizneslər və startaplar üçün onlayn mövcudluqlarını qurmaq istəyənlər üçün mükəmməl.', 'text', 'pricing', 'Basic plan açıklaması'),
('pricing_basic_price_monthly', '299', 'text', 'pricing', 'Basic plan aylık fiyat'),
('pricing_basic_price_yearly', '239', 'text', 'pricing', 'Basic plan yıllık fiyat'),
('pricing_basic_price_note', 'İllik ödəniş: ₼2,390 (₼598 qənaət)', 'text', 'pricing', 'Basic plan fiyat notu'),
('pricing_basic_button', 'Başla', 'text', 'pricing', 'Basic plan buton metni'),
('pricing_basic_guarantee', '30 günlük pul geri qaytarılması zəmanəti', 'text', 'pricing', 'Basic plan garanti'),
('pricing_basic_trial', '14 günlük pulsuz sınaq', 'text', 'pricing', 'Basic plan deneme'),

-- Basic Plan Features
('pricing_basic_feature_1_title', 'SEO Optimizasiya', 'text', 'pricing', 'Basic plan özellik 1 başlık'),
('pricing_basic_feature_1_desc', 'Google reytinqi üçün 5 açar söz', 'text', 'pricing', 'Basic plan özellik 1 açıklama'),
('pricing_basic_feature_1_benefit', '+150% orqanik trafik artımı', 'text', 'pricing', 'Basic plan özellik 1 fayda'),
('pricing_basic_feature_2_title', 'Sosial Medya İdarəetməsi', 'text', 'pricing', 'Basic plan özellik 2 başlık'),
('pricing_basic_feature_2_desc', 'Facebook + Instagram məzmun yaradılması', 'text', 'pricing', 'Basic plan özellik 2 açıklama'),
('pricing_basic_feature_2_benefit', 'Günlük 300+ etkileşim zəmanəti', 'text', 'pricing', 'Basic plan özellik 2 fayda'),
('pricing_basic_feature_3_title', 'Performans Hesabatları', 'text', 'pricing', 'Basic plan özellik 3 başlık'),
('pricing_basic_feature_3_desc', 'Aylıq detallı analitika və görüşlər', 'text', 'pricing', 'Basic plan özellik 3 açıklama'),
('pricing_basic_feature_3_benefit', 'ROI izləməsi daxildir', 'text', 'pricing', 'Basic plan özellik 3 fayda'),
('pricing_basic_feature_4_title', 'Prioritet Dəstək', 'text', 'pricing', 'Basic plan özellik 4 başlık'),
('pricing_basic_feature_4_desc', '24 saat cavab zəmanəti', 'text', 'pricing', 'Basic plan özellik 4 açıklama'),
('pricing_basic_feature_4_benefit', 'Ekspert məsləhət dəstəyi', 'text', 'pricing', 'Basic plan özellik 4 fayda'),
('pricing_basic_feature_5_title', 'Veb Sayt Optimizasiyası', 'text', 'pricing', 'Basic plan özellik 5 başlık'),
('pricing_basic_feature_5_desc', 'Sürət və performans təkmilləşdirmələri', 'text', 'pricing', 'Basic plan özellik 5 açıklama'),
('pricing_basic_feature_5_benefit', '40% daha sürətli yükləmə', 'text', 'pricing', 'Basic plan özellik 5 fayda'),

-- Professional Plan
('pricing_professional_badge', 'Peşəkar', 'text', 'pricing', 'Professional plan badge'),
('pricing_professional_title', 'Professional', 'text', 'pricing', 'Professional plan başlığı'),
('pricing_professional_description', 'Böyüyən bizneslər və orta ölçülü şirkətlər üçün hərtərəfli rəqəmsal marketinq axtaranlar üçün ideal.', 'text', 'pricing', 'Professional plan açıklaması'),
('pricing_professional_price_monthly', '599', 'text', 'pricing', 'Professional plan aylık fiyat'),
('pricing_professional_price_yearly', '479', 'text', 'pricing', 'Professional plan yıllık fiyat'),
('pricing_professional_price_note', 'İllik ödəniş: ₼4,790 (₼1,198 qənaət)', 'text', 'pricing', 'Professional plan fiyat notu'),
('pricing_professional_button', 'İndi Başla - 20% Endirim', 'text', 'pricing', 'Professional plan buton metni'),
('pricing_professional_guarantee', '30 günlük pul geri qaytarılması zəmanəti', 'text', 'pricing', 'Professional plan garanti'),
('pricing_professional_trial', '14 günlük pulsuz sınaq + bonus xüsusiyyətlər', 'text', 'pricing', 'Professional plan deneme'),
('pricing_professional_popular', 'Ən Populyar', 'text', 'pricing', 'Professional plan popüler etiketi'),

-- Professional Plan Features
('pricing_professional_feature_1_title', 'Qabaqcıl SEO', 'text', 'pricing', 'Professional plan özellik 1 başlık'),
('pricing_professional_feature_1_desc', '15 açar söz + rəqib analizi', 'text', 'pricing', 'Professional plan özellik 1 açıklama'),
('pricing_professional_feature_1_benefit', '+300% orqanik trafik artımı', 'text', 'pricing', 'Professional plan özellik 1 fayda'),
('pricing_professional_feature_2_title', 'Çox Platformlu Sosial Medya', 'text', 'pricing', 'Professional plan özellik 2 başlık'),
('pricing_professional_feature_2_desc', '4 platform + məzmun yaradılması', 'text', 'pricing', 'Professional plan özellik 2 açıklama'),
('pricing_professional_feature_2_benefit', 'Günlük 500+ etkileşim', 'text', 'pricing', 'Professional plan özellik 2 fayda'),
('pricing_professional_feature_3_title', 'Google Ads İdarəetməsi', 'text', 'pricing', 'Professional plan özellik 3 başlık'),
('pricing_professional_feature_3_desc', 'Kampaniya qurulumu və optimizasiya', 'text', 'pricing', 'Professional plan özellik 3 açıklama'),
('pricing_professional_feature_3_benefit', '₼500 reklam krediti daxildir', 'text', 'pricing', 'Professional plan özellik 3 fayda'),
('pricing_professional_feature_4_title', 'Brend Xidmətləri', 'text', 'pricing', 'Professional plan özellik 4 başlık'),
('pricing_professional_feature_4_desc', 'Logo və korporativ kimlik dizaynı', 'text', 'pricing', 'Professional plan özellik 4 açıklama'),
('pricing_professional_feature_4_benefit', 'Peşəkar brend qaydaları', 'text', 'pricing', 'Professional plan özellik 4 fayda'),
('pricing_professional_feature_5_title', 'Həftəlik Hesabatlar', 'text', 'pricing', 'Professional plan özellik 5 başlık'),
('pricing_professional_feature_5_desc', 'Detallı performans analitikası', 'text', 'pricing', 'Professional plan özellik 5 açıklama'),
('pricing_professional_feature_5_benefit', 'Real vaxt dashboard girişi', 'text', 'pricing', 'Professional plan özellik 5 fayda'),
('pricing_professional_feature_6_title', 'Telefon + Email Dəstək', 'text', 'pricing', 'Professional plan özellik 6 başlık'),
('pricing_professional_feature_6_desc', 'Prioritet müştəri xidməti', 'text', 'pricing', 'Professional plan özellik 6 açıklama'),
('pricing_professional_feature_6_benefit', 'Dedicated hesab meneceri', 'text', 'pricing', 'Professional plan özellik 6 fayda'),

-- Enterprise Plan
('pricing_enterprise_badge', 'Enterprise', 'text', 'pricing', 'Enterprise plan badge'),
('pricing_enterprise_title', 'Enterprise', 'text', 'pricing', 'Enterprise plan başlığı'),
('pricing_enterprise_description', 'Böyük şirkətlər və korporasiyalar üçün tam miqyaslı rəqəmsal marketinq tələb edənlər üçün tam həll.', 'text', 'pricing', 'Enterprise plan açıklaması'),
('pricing_enterprise_price_monthly', '999', 'text', 'pricing', 'Enterprise plan aylık fiyat'),
('pricing_enterprise_price_yearly', '799', 'text', 'pricing', 'Enterprise plan yıllık fiyat'),
('pricing_enterprise_price_note', 'İllik ödəniş: ₼7,990 (₼1,998 qənaət)', 'text', 'pricing', 'Enterprise plan fiyat notu'),
('pricing_enterprise_button', 'Satışla Əlaqə', 'text', 'pricing', 'Enterprise plan buton metni'),
('pricing_enterprise_guarantee', 'Tam zəmanət və dəstək', 'text', 'pricing', 'Enterprise plan garanti'),
('pricing_enterprise_trial', '30 günlük pulsuz sınaq + VIP dəstək', 'text', 'pricing', 'Enterprise plan deneme'),

-- Enterprise Plan Features
('pricing_enterprise_feature_1_title', 'Limitsiz SEO', 'text', 'pricing', 'Enterprise plan özellik 1 başlık'),
('pricing_enterprise_feature_1_desc', 'Limitsiz açar söz + AI analizi', 'text', 'pricing', 'Enterprise plan özellik 1 açıklama'),
('pricing_enterprise_feature_1_benefit', '+500% orqanik trafik artımı', 'text', 'pricing', 'Enterprise plan özellik 1 fayda'),
('pricing_enterprise_feature_2_title', 'Bütün Sosial Medya Platformları', 'text', 'pricing', 'Enterprise plan özellik 2 başlık'),
('pricing_enterprise_feature_2_desc', 'Tam platform idarəetməsi + avtomatlaşdırma', 'text', 'pricing', 'Enterprise plan özellik 2 açıklama'),
('pricing_enterprise_feature_2_benefit', 'Günlük 1000+ etkileşim', 'text', 'pricing', 'Enterprise plan özellik 2 fayda'),
('pricing_enterprise_feature_3_title', 'Tam Reklam İdarəetməsi', 'text', 'pricing', 'Enterprise plan özellik 3 başlık'),
('pricing_enterprise_feature_3_desc', 'Google, Facebook, LinkedIn, TikTok', 'text', 'pricing', 'Enterprise plan özellik 3 açıklama'),
('pricing_enterprise_feature_3_benefit', '₼1000 reklam krediti daxildir', 'text', 'pricing', 'Enterprise plan özellik 3 fayda'),
('pricing_enterprise_feature_4_title', 'Tam Brend Paketi', 'text', 'pricing', 'Enterprise plan özellik 4 başlık'),
('pricing_enterprise_feature_4_desc', 'Korporativ kimlik + marketinq materialları', 'text', 'pricing', 'Enterprise plan özellik 4 açıklama'),
('pricing_enterprise_feature_4_benefit', 'Tam brend qaydaları', 'text', 'pricing', 'Enterprise plan özellik 4 fayda'),
('pricing_enterprise_feature_5_title', '24/7 VIP Dəstək', 'text', 'pricing', 'Enterprise plan özellik 5 başlık'),
('pricing_enterprise_feature_5_desc', 'Dedicated hesab meneceri', 'text', 'pricing', 'Enterprise plan özellik 5 açıklama'),
('pricing_enterprise_feature_5_benefit', 'Prioritet cavab vaxtı', 'text', 'pricing', 'Enterprise plan özellik 5 fayda'),
('pricing_enterprise_feature_6_title', 'Günlük Hesabatlar', 'text', 'pricing', 'Enterprise plan özellik 6 başlık'),
('pricing_enterprise_feature_6_desc', 'Real vaxt performans analitikası', 'text', 'pricing', 'Enterprise plan özellik 6 açıklama'),
('pricing_enterprise_feature_6_benefit', 'Xüsusi dashboard girişi', 'text', 'pricing', 'Enterprise plan özellik 6 fayda'),

-- CTA Section
('pricing_cta_title', 'Biznesinizi Dəyişdirməyə Hazırsınız?', 'text', 'pricing', 'Pricing CTA başlığı'),
('pricing_cta_subtitle', 'Minlərlə uğurlu biznesin NextCode Group-a rəqəmsal marketinq ehtiyacları üçün etibar etdiyi qoşulun. Uğur yolculuğunuza bugün başlayın!', 'text', 'pricing', 'Pricing CTA alt başlığı'),
('pricing_cta_button_1', 'Pulsuz Məsləhət Al', 'text', 'pricing', 'Pricing CTA buton 1'),
('pricing_cta_button_2', '14 Günlük Sınaq Başlat', 'text', 'pricing', 'Pricing CTA buton 2'),

-- Common Elements
('pricing_features_header', 'Daxil olan xidmətlər:', 'text', 'pricing', 'Özellikler başlığı'),
('pricing_period_monthly', '/ay', 'text', 'pricing', 'Aylık periyot etiketi'),
('pricing_period_yearly', '/il', 'text', 'pricing', 'Yıllık periyot etiketi');
