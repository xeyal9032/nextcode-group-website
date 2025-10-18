-- Portfolio System Sample Data
-- Bu dosya örnek portfolio verilerini içerir

-- Önce mevcut verileri temizle (isteğe bağlı)
-- DELETE FROM portfolio_images;
-- DELETE FROM portfolio_projects;
-- DELETE FROM portfolio_testimonials;
-- DELETE FROM portfolio_categories;

-- Kategoriler
INSERT INTO portfolio_categories (name, description, icon, color, sort_order) VALUES
('Web Tasarım', 'Modern ve responsive web siteleri', 'fas fa-laptop-code', '#2563eb', 1),
('E-Ticaret', 'Online mağaza ve satış platformları', 'fas fa-shopping-cart', '#10b981', 2),
('Mobil Uygulama', 'iOS ve Android mobil uygulamalar', 'fas fa-mobile-alt', '#f59e0b', 3),
('Kurumsal Kimlik', 'Logo ve marka tasarımları', 'fas fa-palette', '#ef4444', 4),
('SEO & Pazarlama', 'Dijital pazarlama ve SEO hizmetleri', 'fas fa-chart-line', '#8b5cf6', 5),
('AI & Makine Öğrenmesi', 'Yapay zeka ve veri analizi projeleri', 'fas fa-brain', '#06b6d4', 6),
('SaaS Platformları', 'Software as a Service çözümleri', 'fas fa-cloud', '#84cc16', 7),
('Blockchain & Fintech', 'Kripto para ve finansal teknolojiler', 'fas fa-coins', '#f97316', 8),
('Sağlık Teknolojileri', 'Digital health ve medtech çözümleri', 'fas fa-heartbeat', '#ec4899', 9),
('Eğitim Teknolojileri', 'EdTech ve öğrenme platformları', 'fas fa-graduation-cap', '#6366f1', 10);

-- Projeler (16 proje)
INSERT INTO portfolio_projects (title, description, client_name, category_id, project_url, technologies, duration, results, status, featured, sort_order, created_at) VALUES
(
    'Modern Kurumsal Web Sitesi',
    'Teknoloji şirketi için modern, responsive ve SEO uyumlu kurumsal web sitesi tasarımı. Kullanıcı deneyimi odaklı arayüz ve yönetim paneli ile birlikte geliştirildi.',
    'TechCorp A.Ş.',
    1,
    'https://techcorp.example.com',
    'HTML5, CSS3, JavaScript, PHP, MySQL, Bootstrap',
    '6 hafta',
    '{"Sayfa Hızı": "+85%", "Mobil Uyumluluk": "100%", "SEO Skoru": "95/100", "Kullanıcı Memnuniyeti": "4.8/5"}',
    'completed',
    1,
    1,
    NOW()
),
(
    'E-Ticaret Platformu',
    'Giyim markası için kapsamlı e-ticaret çözümü. Ödeme entegrasyonları, stok yönetimi, müşteri paneli ve detaylı raporlama sistemi ile birlikte.',
    'Fashion Store',
    2,
    'https://fashionstore.example.com',
    'React, Node.js, MongoDB, Stripe API, AWS',
    '10 hafta',
    '{"Satış Artışı": "+150%", "Sipariş Sayısı": "+200%", "Mobil Satış": "+300%", "Müşteri Dönüşü": "+75%"}',
    'completed',
    1,
    2,
    NOW()
),
(
    'Mobil Fitness Uygulaması',
    'Kişiselleştirilmiş antrenman programları, beslenme takibi ve sosyal özellikler içeren kapsamlı fitness uygulaması.',
    'FitLife Gym',
    3,
    'https://apps.apple.com/fitlife',
    'React Native, Firebase, Node.js, MongoDB',
    '12 hafta',
    '{"İndirme Sayısı": "50K+", "Aktif Kullanıcı": "15K", "Uygulama Puanı": "4.7/5", "Günlük Kullanım": "45 dk"}',
    'completed',
    1,
    3,
    NOW()
),
(
    'Restoran Zinciri Rebrand',
    'Ulusal restoran zinciri için komple marka yenileme projesi. Logo, kurumsal kimlik, menü tasarımı ve dijital varlıklar.',
    'Lezzet Durağı',
    4,
    NULL,
    'Adobe Illustrator, Photoshop, InDesign',
    '8 hafta',
    '{"Marka Bilinirliği": "+120%", "Müşteri Trafiği": "+80%", "Sosyal Medya": "+250%", "Satış Artışı": "+65%"}',
    'completed',
    0,
    4,
    NOW()
),
(
    'Dijital Pazarlama Kampanyası',
    'Emlak firması için kapsamlı dijital pazarlama stratejisi. SEO, SEM, sosyal medya yönetimi ve içerik pazarlaması.',
    'Premium Emlak',
    5,
    'https://premiuememlak.example.com',
    'Google Ads, Facebook Ads, SEO Tools, Analytics',
    '16 hafta',
    '{"Organik Trafik": "+300%", "Lead Artışı": "+180%", "Dönüşüm Oranı": "+95%", "ROI": "450%"}',
    'completed',
    0,
    5,
    NOW()
),
(
    'Eğitim Platformu',
    'Online eğitim platformu geliştirme. Video dersleri, sınavlar, sertifikalar ve öğrenci takip sistemi.',
    'EduTech Academy',
    10,
    'https://edutech.example.com',
    'Vue.js, Laravel, MySQL, AWS S3, Vimeo API',
    '14 hafta',
    '{"Kayıtlı Öğrenci": "5K+", "Tamamlanan Kurs": "2.5K", "Memnuniyet": "4.9/5", "Sertifika": "1.8K"}',
    'completed',
    0,
    6,
    NOW()
),
(
    'AI Destekli Müşteri Analiz Platformu',
    'Büyük veri analizi ve yapay zeka kullanarak müşteri davranışlarını analiz eden, satış tahminleri yapan platform.',
    'DataInsight Corp',
    6,
    'https://datainsight.example.com',
    'Python, TensorFlow, Apache Spark, Kubernetes, MongoDB',
    '20 hafta',
    '{"Tahmin Doğruluğu": "94%", "İşlem Hızı": "+300%", "Maliyet Azalması": "40%", "ROI": "600%"}',
    'completed',
    1,
    7,
    NOW()
),
(
    'SaaS CRM Sistemi',
    'Küçük ve orta ölçekli işletmeler için bulut tabanlı müşteri ilişkileri yönetim sistemi.',
    'CloudCRM Solutions',
    7,
    'https://cloudcrm.example.com',
    'Angular, Spring Boot, PostgreSQL, Redis, AWS',
    '18 hafta',
    '{"Aktif Kullanıcı": "2.5K+", "Müşteri Memnuniyeti": "96%", "Satış Artışı": "180%", "Maliyet Azalması": "35%"}',
    'completed',
    1,
    8,
    NOW()
),
(
    'Kripto Para Cüzdan Uygulaması',
    'Güvenli, çoklu kripto para desteği olan mobil cüzdan uygulaması. DeFi entegrasyonu ve NFT desteği.',
    'CryptoVault',
    8,
    'https://cryptovault.example.com',
    'React Native, Solidity, Web3.js, IPFS, MetaMask',
    '16 hafta',
    '{"Kullanıcı Sayısı": "100K+", "Güvenlik Skoru": "99.9%", "Desteklenen Coin": "50+", "İşlem Hacmi": "$2M+"}',
    'completed',
    1,
    9,
    NOW()
),
(
    'Hastane Yönetim Sistemi',
    'Hastane ve klinikler için kapsamlı dijital hasta yönetim sistemi. Randevu, hasta kayıtları ve tıbbi veri yönetimi.',
    'MediCare Plus',
    9,
    'https://medicare.example.com',
    'React, .NET Core, SQL Server, HL7 FHIR, Azure',
    '24 hafta',
    '{"Hasta Memnuniyeti": "95%", "Randevu Süresi": "-60%", "Veri Güvenliği": "HIPAA Uyumlu", "Maliyet Azalması": "45%"}',
    'completed',
    1,
    10,
    NOW()
),
(
    'Çok Kanallı E-ticaret Platformu',
    'B2B ve B2C satışları destekleyen, çoklu mağaza yönetimi olan kapsamlı e-ticaret platformu.',
    'OmniStore',
    2,
    'https://omnistore.example.com',
    'Vue.js, Django, PostgreSQL, Elasticsearch, Kafka',
    '22 hafta',
    '{"Satış Artışı": "450%", "Mağaza Sayısı": "1K+", "Ürün Çeşidi": "2M+", "Mobil Satış": "65%"}',
    'completed',
    1,
    11,
    NOW()
),
(
    'Finansal Analiz Dashboard',
    'Gerçek zamanlı finansal veri analizi ve görselleştirme platformu. Portföy yönetimi ve risk analizi araçları.',
    'FinanceAnalytics',
    6,
    'https://financeanalytics.example.com',
    'React, D3.js, Python, FastAPI, TimescaleDB',
    '14 hafta',
    '{"Kullanıcı Verimliliği": "+45%", "Karar Hızı": "3x", "Veri Güncelliği": "Real-time", "Müşteri Memnuniyeti": "98%"}',
    'completed',
    0,
    12,
    NOW()
),
(
    'Mobil Bankacılık Uygulaması',
    'Modern mobil bankacılık deneyimi sunan, biyometrik kimlik doğrulama ve güvenli işlem desteği olan uygulama.',
    'DigitalBank',
    3,
    'https://digitalbank.example.com',
    'React Native, Node.js, PostgreSQL, Redis, Biometric API',
    '20 hafta',
    '{"Aktif Kullanıcı": "200K+", "İşlem Güvenliği": "99.99%", "Müşteri Memnuniyeti": "96%", "İşlem Hızı": "5 sn"}',
    'completed',
    1,
    13,
    NOW()
),
(
    'IoT Akıllı Ev Platformu',
    'Akıllı ev cihazlarını yöneten, enerji tasarrufu sağlayan ve güvenlik özellikleri olan IoT platformu.',
    'SmartHome Hub',
    7,
    'https://smarthome.example.com',
    'React, Node.js, MQTT, MongoDB, AWS IoT',
    '26 hafta',
    '{"Enerji Tasarrufu": "30%", "Cihaz Uyumluluğu": "100+", "Güvenlik Skoru": "99.5%", "Kullanıcı Memnuniyeti": "94%"}',
    'completed',
    0,
    14,
    NOW()
),
(
    'Sosyal Medya Yönetim Platformu',
    'Çoklu sosyal medya hesaplarını tek platformdan yöneten, içerik planlama ve analiz araçları sunan sistem.',
    'SocialManager Pro',
    5,
    'https://socialmanager.example.com',
    'Vue.js, Laravel, MySQL, Social APIs, Analytics',
    '12 hafta',
    '{"Zaman Tasarrufu": "70%", "İçerik Etkileşimi": "+120%", "Takipçi Artışı": "85%", "ROI": "350%"}',
    'completed',
    0,
    15,
    NOW()
),
(
    'Oyun Geliştirme Platformu',
    'Oyun geliştiriciler için araçlar, asset yönetimi ve yayın platformu sunan kapsamlı oyun geliştirme ekosistemi.',
    'GameDev Studio',
    7,
    'https://gamedev.example.com',
    'Unity, C#, .NET Core, SQL Server, Azure Game Services',
    '28 hafta',
    '{"Geliştirici Sayısı": "5K+", "Yayınlanan Oyun": "500+", "Toplam İndirme": "10M+", "Geliştirici Memnuniyeti": "92%"}',
    'completed',
    1,
    16,
    NOW()
);

-- Proje görselleri
INSERT INTO portfolio_images (project_id, image_path, alt_text, is_featured, sort_order) VALUES
-- TechCorp Web Sitesi görselleri
(1, '/assets/images/portfolio/techcorp-1.jpg', 'TechCorp ana sayfa tasarımı', 1, 1),
(1, '/assets/images/portfolio/techcorp-2.jpg', 'TechCorp hizmetler sayfası', 0, 2),
(1, '/assets/images/portfolio/techcorp-3.jpg', 'TechCorp iletişim sayfası', 0, 3),

-- Fashion Store görselleri
(2, '/assets/images/portfolio/fashion-1.jpg', 'Fashion Store ana sayfa', 1, 1),
(2, '/assets/images/portfolio/fashion-2.jpg', 'Ürün detay sayfası', 0, 2),
(2, '/assets/images/portfolio/fashion-3.jpg', 'Sepet ve ödeme sayfası', 0, 3),
(2, '/assets/images/portfolio/fashion-4.jpg', 'Mobil görünüm', 0, 4),

-- FitLife App görselleri
(3, '/assets/images/portfolio/fitlife-1.jpg', 'FitLife ana ekran', 1, 1),
(3, '/assets/images/portfolio/fitlife-2.jpg', 'Antrenman programları', 0, 2),
(3, '/assets/images/portfolio/fitlife-3.jpg', 'Beslenme takibi', 0, 3),
(3, '/assets/images/portfolio/fitlife-4.jpg', 'Sosyal özellikler', 0, 4),

-- Lezzet Durağı görselleri
(4, '/assets/images/portfolio/lezzet-1.jpg', 'Yeni logo tasarımı', 1, 1),
(4, '/assets/images/portfolio/lezzet-2.jpg', 'Kurumsal kimlik uygulamaları', 0, 2),
(4, '/assets/images/portfolio/lezzet-3.jpg', 'Menü tasarımı', 0, 3),

-- Premium Emlak görselleri
(5, '/assets/images/portfolio/emlak-1.jpg', 'Dijital pazarlama kampanyası', 1, 1),
(5, '/assets/images/portfolio/emlak-2.jpg', 'SEO sonuçları', 0, 2),
(5, '/assets/images/portfolio/emlak-3.jpg', 'Sosyal medya yönetimi', 0, 3),

-- EduTech görselleri
(6, '/assets/images/portfolio/edutech-1.jpg', 'Platform ana sayfası', 1, 1),
(6, '/assets/images/portfolio/edutech-2.jpg', 'Kurs detay sayfası', 0, 2),
(6, '/assets/images/portfolio/edutech-3.jpg', 'Öğrenci paneli', 0, 3),

-- AI Analytics Platform görselleri
(7, '/assets/images/portfolio/ai-analytics-1.jpg', 'AI Dashboard ana ekran', 1, 1),
(7, '/assets/images/portfolio/ai-analytics-2.jpg', 'Veri görselleştirme', 0, 2),
(7, '/assets/images/portfolio/ai-analytics-3.jpg', 'Makine öğrenmesi modelleri', 0, 3),

-- SaaS CRM görselleri
(8, '/assets/images/portfolio/crm-1.jpg', 'CRM Dashboard', 1, 1),
(8, '/assets/images/portfolio/crm-2.jpg', 'Müşteri yönetimi', 0, 2),
(8, '/assets/images/portfolio/crm-3.jpg', 'Satış pipeline', 0, 3),

-- Crypto Wallet görselleri
(9, '/assets/images/portfolio/crypto-1.jpg', 'Cüzdan ana ekran', 1, 1),
(9, '/assets/images/portfolio/crypto-2.jpg', 'Kripto işlemleri', 0, 2),
(9, '/assets/images/portfolio/crypto-3.jpg', 'NFT galerisi', 0, 3),

-- Hastane Yönetim Sistemi görselleri
(10, '/assets/images/portfolio/hospital-1.jpg', 'Hasta dashboard', 1, 1),
(10, '/assets/images/portfolio/hospital-2.jpg', 'Randevu sistemi', 0, 2),
(10, '/assets/images/portfolio/hospital-3.jpg', 'Tıbbi kayıtlar', 0, 3),

-- OmniStore görselleri
(11, '/assets/images/portfolio/omnistore-1.jpg', 'Çok kanallı dashboard', 1, 1),
(11, '/assets/images/portfolio/omnistore-2.jpg', 'Mağaza yönetimi', 0, 2),
(11, '/assets/images/portfolio/omnistore-3.jpg', 'Envanter sistemi', 0, 3),

-- Finansal Analiz görselleri
(12, '/assets/images/portfolio/finance-1.jpg', 'Finansal dashboard', 1, 1),
(12, '/assets/images/portfolio/finance-2.jpg', 'Portföy analizi', 0, 2),
(12, '/assets/images/portfolio/finance-3.jpg', 'Risk yönetimi', 0, 3),

-- Digital Bank görselleri
(13, '/assets/images/portfolio/bank-1.jpg', 'Bankacılık ana ekran', 1, 1),
(13, '/assets/images/portfolio/bank-2.jpg', 'Para transferi', 0, 2),
(13, '/assets/images/portfolio/bank-3.jpg', 'Yatırım portföyü', 0, 3),

-- Smart Home görselleri
(14, '/assets/images/portfolio/smarthome-1.jpg', 'Akıllı ev kontrolü', 1, 1),
(14, '/assets/images/portfolio/smarthome-2.jpg', 'Enerji yönetimi', 0, 2),
(14, '/assets/images/portfolio/smarthome-3.jpg', 'Güvenlik sistemi', 0, 3),

-- Social Manager görselleri
(15, '/assets/images/portfolio/social-1.jpg', 'Sosyal medya dashboard', 1, 1),
(15, '/assets/images/portfolio/social-2.jpg', 'İçerik planlayıcı', 0, 2),
(15, '/assets/images/portfolio/social-3.jpg', 'Analitik raporlar', 0, 3),

-- Game Dev Platform görselleri
(16, '/assets/images/portfolio/gamedev-1.jpg', 'Oyun geliştirme araçları', 1, 1),
(16, '/assets/images/portfolio/gamedev-2.jpg', 'Asset yönetimi', 0, 2),
(16, '/assets/images/portfolio/gamedev-3.jpg', 'Yayın platformu', 0, 3);

-- Müşteri yorumları
INSERT INTO portfolio_testimonials (project_id, author_name, author_position, author_company, content, avatar, rating, is_featured, sort_order, created_at) VALUES
(
    1,
    'Ahmet Yılmaz',
    'Genel Müdür',
    'TechCorp A.Ş.',
    'Profesyonel ekip, zamanında teslimat ve mükemmel sonuç. Web sitemiz sayesinde müşteri sayımız %85 arttı. Kesinlikle tavsiye ederim.',
    '/assets/images/testimonials/ahmet-yilmaz.jpg',
    5,
    1,
    1,
    NOW()
),
(
    2,
    'Elif Kaya',
    'E-Ticaret Müdürü',
    'Fashion Store',
    'E-ticaret platformumuz beklentilerimizi aştı. Kullanıcı dostu arayüz ve güçlü altyapı sayesinde satışlarımız ikiye katlandı.',
    '/assets/images/testimonials/elif-kaya.jpg',
    5,
    1,
    2,
    NOW()
),
(
    3,
    'Mehmet Demir',
    'Kurucu',
    'FitLife Gym',
    'Mobil uygulamamız üyelerimiz tarafından çok beğenildi. Kullanım oranları ve üye memnuniyeti rekor seviyede.',
    '/assets/images/testimonials/mehmet-demir.jpg',
    5,
    1,
    3,
    NOW()
),
(
    4,
    'Fatma Özkan',
    'Pazarlama Müdürü',
    'Lezzet Durağı',
    'Marka yenileme projemiz müthiş sonuçlar verdi. Yeni kimliğimiz müşterilerimiz tarafından çok olumlu karşılandı.',
    '/assets/images/testimonials/fatma-ozkan.jpg',
    5,
    0,
    4,
    NOW()
),
(
    5,
    'Can Arslan',
    'Satış Müdürü',
    'Premium Emlak',
    'Dijital pazarlama stratejileri sayesinde online görünürlüğümüz ve satışlarımız dramatik şekilde arttı. ROI oranımız beklentilerimizi aştı.',
    '/assets/images/testimonials/can-arslan.jpg',
    5,
    0,
    5,
    NOW()
),
(
    6,
    'Zeynep Şahin',
    'Eğitim Koordinatörü',
    'EduTech Academy',
    'Eğitim platformumuz öğrencilerimiz için vazgeçilmez oldu. Kullanıcı deneyimi ve teknik altyapı mükemmel.',
    '/assets/images/testimonials/zeynep-sahin.jpg',
    5,
    0,
    6,
    NOW()
),
(
    7,
    'Dr. Ali Korkmaz',
    'CTO',
    'DataInsight Corp',
    'AI platformumuz sayesinde veri analizi süreçlerimiz 10 kat hızlandı. Makine öğrenmesi modelleri beklentilerimizi aştı.',
    '/assets/images/testimonials/ali-korkmaz.jpg',
    5,
    1,
    7,
    NOW()
),
(
    8,
    'Selin Yıldız',
    'Satış Direktörü',
    'CloudCRM Solutions',
    'CRM sistemimiz satış ekibimizin verimliliğini %180 artırdı. Müşteri memnuniyeti ve satış oranları rekor seviyede.',
    '/assets/images/testimonials/selin-yildiz.jpg',
    5,
    1,
    8,
    NOW()
),
(
    9,
    'Burak Demir',
    'CEO',
    'CryptoVault',
    'Kripto cüzdan uygulamamız kullanıcılarımız tarafından çok beğenildi. Güvenlik ve kullanıcı deneyimi mükemmel.',
    '/assets/images/testimonials/burak-demir.jpg',
    5,
    1,
    9,
    NOW()
),
(
    10,
    'Dr. Ayşe Kaya',
    'Başhekim',
    'MediCare Plus',
    'Hastane yönetim sistemimiz sayesinde hasta memnuniyeti %95\'e çıktı. Randevu süreçleri çok daha verimli hale geldi.',
    '/assets/images/testimonials/ayse-kaya.jpg',
    5,
    1,
    10,
    NOW()
),
(
    11,
    'Murat Özkan',
    'E-ticaret Müdürü',
    'OmniStore',
    'Çok kanallı platformumuz satışlarımızı %450 artırdı. Mağaza yönetimi ve envanter sistemi çok kullanışlı.',
    '/assets/images/testimonials/murat-ozkan.jpg',
    5,
    1,
    11,
    NOW()
),
(
    12,
    'Deniz Yılmaz',
    'Portföy Müdürü',
    'FinanceAnalytics',
    'Finansal analiz platformumuz karar verme süreçlerimizi 3 kat hızlandırdı. Veri görselleştirme araçları mükemmel.',
    '/assets/images/testimonials/deniz-yilmaz.jpg',
    5,
    0,
    12,
    NOW()
),
(
    13,
    'Emre Demir',
    'Dijital Bankacılık Müdürü',
    'DigitalBank',
    'Mobil bankacılık uygulamamız müşteri memnuniyetini %96\'ya çıkardı. Güvenlik ve kullanıcı deneyimi çok yüksek.',
    '/assets/images/testimonials/emre-demir.jpg',
    5,
    1,
    13,
    NOW()
),
(
    14,
    'Gizem Korkmaz',
    'IoT Ürün Müdürü',
    'SmartHome Hub',
    'Akıllı ev platformumuz enerji tasarrufumuzu %30 artırdı. Kullanıcı arayüzü çok sezgisel.',
    '/assets/images/testimonials/gizem-korkmaz.jpg',
    5,
    0,
    14,
    NOW()
),
(
    15,
    'Berk Yıldız',
    'Sosyal Medya Müdürü',
    'SocialManager Pro',
    'Sosyal medya yönetim platformumuz zaman tasarrufumuzu %70 artırdı. İçerik planlama araçları çok kullanışlı.',
    '/assets/images/testimonials/berk-yildiz.jpg',
    5,
    0,
    15,
    NOW()
),
(
    16,
    'Ozan Demir',
    'Oyun Geliştirici',
    'GameDev Studio',
    'Oyun geliştirme platformumuz sayesinde 500+ oyun yayınlandı. Geliştirici araçları ve yayın sistemi mükemmel.',
    '/assets/images/testimonials/ozan-demir.jpg',
    5,
    1,
    16,
    NOW()
);

-- Genel testimonials (proje bağımsız)
INSERT INTO portfolio_testimonials (author_name, author_position, author_company, content, avatar, rating, is_featured, sort_order, created_at) VALUES
(
    'Oğuz Kaan',
    'CEO',
    'StartupTech',
    'Birlikte çalıştığımız en profesyonel ekip. Projelerimizi zamanında ve bütçe dahilinde teslim ettiler. Uzun vadeli iş ortaklığımız devam ediyor.',
    '/assets/images/testimonials/oguz-kaan.jpg',
    5,
    1,
    17,
    NOW()
),
(
    'Ayşe Tunç',
    'Pazarlama Müdürü',
    'Global Brands',
    'Yaratıcı çözümleri ve teknik uzmanlıkları sayesinde markamızın dijital dönüşümünü başarıyla gerçekleştirdik.',
    '/assets/images/testimonials/ayse-tunc.jpg',
    5,
    1,
    18,
    NOW()
);

-- Veritabanı istatistikleri için view oluştur
CREATE OR REPLACE VIEW portfolio_stats AS
SELECT 
    (SELECT COUNT(*) FROM portfolio_projects WHERE status = 'completed') as completed_projects,
    (SELECT COUNT(*) FROM portfolio_projects WHERE featured = 1) as featured_projects,
    (SELECT COUNT(*) FROM portfolio_categories) as total_categories,
    (SELECT COUNT(*) FROM portfolio_testimonials WHERE rating = 5) as five_star_reviews,
    (SELECT AVG(rating) FROM portfolio_testimonials) as average_rating,
    (SELECT COUNT(*) FROM portfolio_images) as total_images;

-- Örnek sorgu: En popüler kategoriler
-- SELECT c.name, COUNT(p.id) as project_count 
-- FROM portfolio_categories c 
-- LEFT JOIN portfolio_projects p ON c.id = p.category_id 
-- GROUP BY c.id, c.name 
-- ORDER BY project_count DESC;

-- Örnek sorgu: Son projeler
-- SELECT p.title, p.client_name, c.name as category, p.created_at
-- FROM portfolio_projects p
-- JOIN portfolio_categories c ON p.category_id = c.id
-- ORDER BY p.created_at DESC
-- LIMIT 5;