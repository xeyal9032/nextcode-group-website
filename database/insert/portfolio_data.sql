-- NextCode Group - Portfolio Projects Data
-- Bu dosya portfolio_projects tablosunu mevcut projelerle doldurur

-- Önce tabloyu temizleyelim (eğer varsa)
TRUNCATE TABLE portfolio_projects;

-- Proje 1: E-Ticaret Platformu
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name, 
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    1, 'E-Ticaret Platformu', 'Modern Online Alışveriş Deneyimi',
    'Tam özellikli e-ticaret platformu, mobil uyumlu tasarım ve güvenli ödeme sistemi ile.',
    'E-Ticaret', 8, 6, '45K', 100,
    'Müşterilerin online alışveriş deneyimini iyileştirmek ve satışları artırmak',
    'Karmaşık ürün katalog yönetimi ve güvenli ödeme entegrasyonu',
    'Mikroservis mimarisi ve modern frontend framework kullanarak modüler geliştirme',
    'Satışlar %150 arttı, mobil kullanıcı deneyimi %200 iyileşti',
    'images/portfolio/ecommerce-main.jpg',
    'images/portfolio/ecommerce-1.jpg',
    'images/portfolio/ecommerce-2.jpg',
    'images/portfolio/ecommerce-3.jpg',
    1, NOW(), NOW()
);

-- Proje 2: Mobil Uygulama
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    2, 'Mobil Uygulama', 'iOS ve Android Uygulaması',
    'Cross-platform mobil uygulama, React Native ile geliştirildi.',
    'Mobil Geliştirme', 6, 4, '35K', 100,
    'Kullanıcıların mobil cihazlarda kolay erişim sağlaması',
    'Cross-platform uyumluluk ve performans optimizasyonu',
    'React Native kullanarak tek kod tabanından iki platform için geliştirme',
    'App Store ve Google Play\'de 4.8+ yıldız, 50K+ indirme',
    'images/portfolio/mobile-app-main.jpg',
    'images/portfolio/mobile-app-1.jpg',
    'images/portfolio/mobile-app-2.jpg',
    'images/portfolio/mobile-app-3.jpg',
    1, NOW(), NOW()
);

-- Proje 3: Kurumsal Web Sitesi
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    3, 'Kurumsal Web Sitesi', 'Profesyonel Şirket Tanıtımı',
    'Modern tasarım ve SEO optimizasyonu ile kurumsal web sitesi.',
    'Web Geliştirme', 4, 3, '25K', 100,
    'Şirketin online varlığını güçlendirmek ve müşteri erişimini artırmak',
    'SEO optimizasyonu ve responsive tasarım gereksinimleri',
    'Semantic HTML, CSS Grid ve modern JavaScript ile performans odaklı geliştirme',
    'Organik trafik %300 arttı, sayfa yükleme hızı %60 iyileşti',
    'images/portfolio/corporate-website-main.jpg',
    'images/portfolio/corporate-website-1.jpg',
    'images/portfolio/corporate-website-2.jpg',
    'images/portfolio/corporate-website-3.jpg',
    1, NOW(), NOW()
);

-- Proje 4: Fitness Uygulaması
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    4, 'Fitness Uygulaması', 'Kişisel Fitness Takibi',
    'Kişiselleştirilmiş antrenman planları ve ilerleme takibi.',
    'Sağlık & Fitness', 7, 5, '40K', 100,
    'Kullanıcıların fitness hedeflerine ulaşmasına yardımcı olmak',
    'Karmaşık antrenman algoritmaları ve kullanıcı veri yönetimi',
    'AI destekli antrenman önerileri ve gamification elementleri',
    'Aktif kullanıcı sayısı %400 arttı, kullanıcı memnuniyeti %95',
    'images/portfolio/fitness-app-main.jpg',
    'images/portfolio/fitness-app-1.jpg',
    'images/portfolio/fitness-app-2.jpg',
    'images/portfolio/fitness-app-3.jpg',
    1, NOW(), NOW()
);

-- Proje 5: Blog Platformu
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    5, 'Blog Platformu', 'İçerik Yönetim Sistemi',
    'Modern blog platformu, içerik yönetimi ve SEO optimizasyonu.',
    'İçerik Yönetimi', 5, 4, '30K', 100,
    'Yazarların kolayca içerik oluşturması ve yönetmesi',
    'İçerik editörü ve kategori yönetimi sistemi',
    'WYSIWYG editör ve gelişmiş kategori hiyerarşisi',
    'Aylık 1000+ makale yayınlandı, organik trafik %250 arttı',
    'images/portfolio/blog-platform-main.jpg',
    'images/portfolio/blog-platform-1.jpg',
    'images/portfolio/blog-platform-2.jpg',
    'images/portfolio/blog-platform-3.jpg',
    1, NOW(), NOW()
);

-- Proje 6: Restoran Sipariş Sistemi
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    6, 'Restoran Sipariş Sistemi', 'Online Sipariş ve Rezervasyon',
    'Restoranlar için online sipariş ve masa rezervasyon sistemi.',
    'Restoran Teknolojisi', 6, 4, '35K', 100,
    'Restoranların online sipariş alması ve rezervasyon yönetimi',
    'Gerçek zamanlı stok takibi ve ödeme entegrasyonu',
    'WebSocket ile gerçek zamanlı güncellemeler ve güvenli ödeme sistemi',
    'Ortalama sipariş değeri %30 arttı, müşteri memnuniyeti %90',
    'images/portfolio/restaurant-system-main.jpg',
    'images/portfolio/restaurant-system-1.jpg',
    'images/portfolio/restaurant-system-2.jpg',
    'images/portfolio/restaurant-system-3.jpg',
    1, NOW(), NOW()
);

-- Proje 7: Eğitim Platformu
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    7, 'Eğitim Platformu', 'Online Öğrenme Sistemi',
    'Video tabanlı eğitim platformu, interaktif quiz ve sertifika sistemi.',
    'Eğitim Teknolojisi', 8, 6, '50K', 100,
    'Öğrencilerin uzaktan eğitim alması ve ilerleme takibi',
    'Video streaming optimizasyonu ve interaktif içerik yönetimi',
    'Adaptive bitrate streaming ve gamification elementleri',
    '10K+ aktif öğrenci, %85 tamamlama oranı',
    'images/portfolio/education-platform-main.jpg',
    'images/portfolio/education-platform-1.jpg',
    'images/portfolio/education-platform-2.jpg',
    'images/portfolio/education-platform-3.jpg',
    1, NOW(), NOW()
);

-- Proje 8: Emlak Portalı
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    8, 'Emlak Portalı', 'Gayrimenkul Arama Platformu',
    'Gelişmiş filtreleme ve harita entegrasyonu ile emlak arama.',
    'Emlak Teknolojisi', 7, 5, '45K', 100,
    'Kullanıcıların ideal evi kolayca bulması',
    'Karmaşık arama algoritmaları ve harita entegrasyonu',
    'Elasticsearch ile gelişmiş arama ve Google Maps API entegrasyonu',
    'Aylık 50K+ arama, %70 kullanıcı memnuniyeti',
    'images/portfolio/real-estate-portal-main.jpg',
    'images/portfolio/real-estate-portal-1.jpg',
    'images/portfolio/real-estate-portal-2.jpg',
    'images/portfolio/real-estate-portal-3.jpg',
    1, NOW(), NOW()
);

-- Proje 9: Sosyal Medya Uygulaması
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    9, 'Sosyal Medya Uygulaması', 'Topluluk Platformu',
    'Fotoğraf paylaşımı ve sosyal etkileşim odaklı mobil uygulama.',
    'Sosyal Medya', 9, 7, '60K', 100,
    'Kullanıcıların fotoğraf paylaşması ve sosyal etkileşim kurması',
    'Gerçek zamanlı bildirimler ve içerik moderasyonu',
    'Push notification sistemi ve AI destekli içerik filtreleme',
    '100K+ kullanıcı, günlük 10K+ fotoğraf paylaşımı',
    'images/portfolio/social-media-app-main.jpg',
    'images/portfolio/social-media-app-1.jpg',
    'images/portfolio/social-media-app-2.jpg',
    'images/portfolio/social-media-app-3.jpg',
    1, NOW(), NOW()
);

-- Proje 10: Finans Uygulaması
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    10, 'Finans Uygulaması', 'Kişisel Finans Yönetimi',
    'Gelir-gider takibi, bütçe planlama ve yatırım analizi.',
    'Finans Teknolojisi', 8, 6, '55K', 100,
    'Kullanıcıların finansal durumlarını takip etmesi ve planlama yapması',
    'Güvenli veri şifreleme ve karmaşık finansal hesaplamalar',
    'End-to-end şifreleme ve makine öğrenmesi ile harcama kategorilendirme',
    '50K+ aktif kullanıcı, ortalama tasarruf %25 arttı',
    'images/portfolio/finance-app-main.jpg',
    'images/portfolio/finance-app-1.jpg',
    'images/portfolio/finance-app-2.jpg',
    'images/portfolio/finance-app-3.jpg',
    1, NOW(), NOW()
);

-- Proje 11: Turizm Rezervasyon Sistemi
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    11, 'Turizm Rezervasyon Sistemi', 'Seyahat Planlama Platformu',
    'Uçak, otel ve tur rezervasyonu için kapsamlı platform.',
    'Turizm Teknolojisi', 7, 5, '40K', 100,
    'Seyahatseverlerin tek platformdan tüm rezervasyonlarını yapması',
    'Çoklu API entegrasyonu ve dinamik fiyatlandırma',
    'Microservices mimarisi ve real-time fiyat güncellemeleri',
    'Aylık 1000+ rezervasyon, %80 müşteri memnuniyeti',
    'images/portfolio/tourism-booking-main.jpg',
    'images/portfolio/tourism-booking-1.jpg',
    'images/portfolio/tourism-booking-2.jpg',
    'images/portfolio/tourism-booking-3.jpg',
    1, NOW(), NOW()
);

-- Proje 12: Sağlık Takip Uygulaması
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    12, 'Sağlık Takip Uygulaması', 'Kişisel Sağlık Monitörü',
    'Vital bulgular takibi, ilaç hatırlatıcıları ve doktor randevuları.',
    'Sağlık Teknolojisi', 8, 6, '50K', 100,
    'Kullanıcıların sağlık durumlarını takip etmesi ve doktorlarla iletişim kurması',
    'HIPAA uyumluluğu ve tıbbi veri güvenliği',
    'End-to-end şifreleme ve HIPAA standartlarına uygun veri yönetimi',
    '25K+ kullanıcı, %90 doktor memnuniyeti',
    'images/portfolio/health-tracking-main.jpg',
    'images/portfolio/health-tracking-1.jpg',
    'images/portfolio/health-tracking-2.jpg',
    'images/portfolio/health-tracking-3.jpg',
    1, NOW(), NOW()
);

-- Proje 13: Oyun Geliştirme
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    13, 'Oyun Geliştirme', 'Mobil Oyun Uygulaması',
    'Unity ile geliştirilen 3D mobil oyun, çok oyunculu mod.',
    'Oyun Geliştirme', 10, 8, '70K', 100,
    'Eğlenceli ve bağımlılık yaratan mobil oyun deneyimi',
    '3D grafik optimizasyonu ve çok oyunculu senkronizasyon',
    'Unity LOD sistemi ve Photon multiplayer entegrasyonu',
    '500K+ indirme, App Store\'da 4.7 yıldız',
    'images/portfolio/game-development-main.jpg',
    'images/portfolio/game-development-1.jpg',
    'images/portfolio/game-development-2.jpg',
    'images/portfolio/game-development-3.jpg',
    1, NOW(), NOW()
);

-- Proje 14: IoT Dashboard
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    14, 'IoT Dashboard', 'Akıllı Ev Kontrol Paneli',
    'IoT cihazları için merkezi kontrol paneli ve otomasyon sistemi.',
    'IoT Teknolojisi', 6, 4, '35K', 100,
    'Kullanıcıların tüm IoT cihazlarını tek yerden yönetmesi',
    'Çoklu protokol desteği ve gerçek zamanlı veri işleme',
    'MQTT ve CoAP protokol desteği ile real-time dashboard',
    '1000+ cihaz bağlantısı, %99.9 uptime',
    'images/portfolio/iot-dashboard-main.jpg',
    'images/portfolio/iot-dashboard-1.jpg',
    'images/portfolio/iot-dashboard-2.jpg',
    'images/portfolio/iot-dashboard-3.jpg',
    1, NOW(), NOW()
);

-- Proje 15: Blockchain Uygulaması
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    15, 'Blockchain Uygulaması', 'DeFi Platformu',
    'Merkezi olmayan finans platformu, kripto para işlemleri.',
    'Blockchain', 12, 10, '100K', 100,
    'Kullanıcıların güvenli ve şeffaf finansal işlemler yapması',
    'Blockchain güvenliği ve smart contract geliştirme',
    'Ethereum smart contracts ve Layer 2 scaling çözümleri',
    '10M+ işlem hacmi, %99.99 güvenlik oranı',
    'images/portfolio/blockchain-app-main.jpg',
    'images/portfolio/blockchain-app-1.jpg',
    'images/portfolio/blockchain-app-2.jpg',
    'images/portfolio/blockchain-app-3.jpg',
    1, NOW(), NOW()
);

-- Proje 16: AI Chatbot
INSERT INTO portfolio_projects (
    id, title, subtitle, description, category_name,
    duration, team_size, budget, completion_rate,
    objective, challenges, solution, results,
    image, image_1, image_2, image_3,
    active, created_at, updated_at
) VALUES (
    16, 'AI Chatbot', 'Yapay Zeka Destekli Müşteri Hizmetleri',
    'Doğal dil işleme ile müşteri sorularını yanıtlayan chatbot.',
    'Yapay Zeka', 8, 6, '45K', 100,
    'Müşteri hizmetleri süreçlerini otomatikleştirmek ve 7/24 destek sağlamak',
    'Doğal dil anlama ve Türkçe dil desteği',
    'GPT tabanlı NLP ve çok dilli destek sistemi',
    'Müşteri memnuniyeti %85, yanıt süresi %90 azaldı',
    'images/portfolio/ai-chatbot-main.jpg',
    'images/portfolio/ai-chatbot-1.jpg',
    'images/portfolio/ai-chatbot-2.jpg',
    'images/portfolio/ai-chatbot-3.jpg',
    1, NOW(), NOW()
);

-- Veritabanı güncellemelerini tamamla
COMMIT;
