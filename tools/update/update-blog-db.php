<?php
// Blog veritabanını güncelle
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Blog Veritabanı Güncelleme</h1>";

try {
    require_once 'config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        echo "<p style='color: red;'>✗ Veritabanı bağlantısı başarısız!</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✓ Veritabanı bağlantısı başarılı</p>";
    
    // Güncel blog verileri
    $updated_posts = [
        1 => [
            'title' => 'SEO Optimizasiyası: Axtarış Nəticələrində Yüksəlmək',
            'slug' => 'seo-optimizasiyasi',
            'content' => '<p>SEO (Search Engine Optimization) optimizasiyası, veb saytınızın Google, Bing və digər axtarış sistemlərində daha yaxşı görünməsi üçün edilən texniki və məzmun təkmilləşdirmələridir. Bu proses, hədəflənmiş auditoriyanıza çatmaq və daha çox trafik əldə etmək üçün vacibdir.</p>
            
            <h2>SEO-nun Əsas Elementləri</h2>
            <p>SEO optimizasiyası üç əsas kateqoriyaya bölünür:</p>
            <ul>
                <li><strong>Texniki SEO:</strong> Saytın texniki performansını yaxşılaşdırır</li>
                <li><strong>Məzmun SEO:</strong> Saytın məzmununu axtarış sistemləri üçün optimallaşdırır</li>
                <li><strong>Off-page SEO:</strong> Saytın xarici faktorlarını təkmilləşdirir</li>
            </ul>
            
            <h3>Texniki SEO Elementləri</h3>
            <p>Texniki SEO aşağıdakı elementləri əhatə edir:</p>
            <ul>
                <li><strong>Saytın yüklənmə sürəti:</strong> Core Web Vitals metrikaları</li>
                <li><strong>Mobil uyğunluq:</strong> Responsive dizayn və mobil-first yanaşma</li>
                <li><strong>SSL sertifikatı:</strong> HTTPS protokolu və təhlükəsizlik</li>
                <li><strong>URL strukturu:</strong> Təmiz və aydın URL-lər</li>
                <li><strong>Meta taglər:</strong> Title, description və keywords</li>
                <li><strong>Schema markup:</strong> Strukturlaşdırılmış məlumat</li>
                <li><strong>XML sitemap:</strong> Axtarış sistemləri üçün sayt xəritəsi</li>
                <li><strong>Robots.txt:</strong> Crawler idarəetməsi</li>
            </ul>
            
            <h3>Məzmun SEO Elementləri</h3>
            <p>Məzmun SEO-nun əsas elementləri:</p>
            <ul>
                <li><strong>Keyworlərin düzgün istifadəsi:</strong> Natural və strategik keywor yerləşdirmə</li>
                <li><strong>Keyfiyyətli və orijinal məzmun:</strong> E-E-A-T prinsipləri</li>
                <li><strong>Başlıq strukturu:</strong> H1, H2, H3 hierarxiyası</li>
                <li><strong>Daxili və xarici linklər:</strong> Link building strategiyası</li>
                <li><strong>Alt taglər:</strong> Şəkil optimizasiyası</li>
                <li><strong>Məzmun uzunluğu:</strong> Dərin və əhatəli məzmun</li>
                <li><strong>İstifadəçi təcrübəsi:</strong> UX/UI optimizasiyası</li>
            </ul>
            
            <h3>Off-page SEO Elementləri</h3>
            <p>Off-page SEO faktorları:</p>
            <ul>
                <li><strong>Backlinklər:</strong> Keyfiyyətli xarici linklər</li>
                <li><strong>Sosial media siqnalı:</strong> Sosial media mövcudluğu</li>
                <li><strong>Brend tanınması:</strong> Brand awareness və authority</li>
                <li><strong>Local SEO:</strong> Yerli axtarış optimizasiyası</li>
            </ul>
            
            <h2>SEO Strategiyası və Planlaşdırma</h2>
            <p>Uğurlu SEO strategiyası üçün aşağıdakı addımları atın:</p>
            <ol>
                <li><strong>Keywor araşdırması:</strong> Google Keyword Planner və digər alətlər</li>
                <li><strong>Rəqib analizi:</strong> SERP analizi və rəqib strategiyaları</li>
                <li><strong>Texniki audit:</strong> Site audit və performans analizi</li>
                <li><strong>Məzmun planı:</strong> Editorial calendar və content strategy</li>
                <li><strong>Link building:</strong> Backlink əldə etmə strategiyası</li>
                <li><strong>Nəticələri izləmə:</strong> Google Analytics və Search Console</li>
                <li><strong>Davamlı təkmilləşdirmə:</strong> A/B testing və optimizasiya</li>
            </ol>
            
            <h2>SEO Alətləri və Platformalar</h2>
            <p>SEO işində istifadə edilən əsas alətlər:</p>
            <ul>
                <li><strong>Google Search Console:</strong> Rəsmi Google aləti</li>
                <li><strong>Google Analytics:</strong> Trafik analizi</li>
                <li><strong>SEMrush:</strong> Comprehensive SEO platform</li>
                <li><strong>Ahrefs:</strong> Backlink və keywor analizi</li>
                <li><strong>Moz:</strong> Domain authority və link building</li>
                <li><strong>Screaming Frog:</strong> Texniki SEO audit</li>
            </ul>
            
            <h2>SEO Metrikaları və KPI-lər</h2>
            <p>SEO uğurunu ölçmək üçün vacib metrikalar:</p>
            <ul>
                <li><strong>Organic trafik:</strong> Axtarış sistemlərindən gələn ziyarətçilər</li>
                <li><strong>Keyword rankings:</strong> Keyworlər üzrə pozisiyalar</li>
                <li><strong>Click-through rate (CTR):</strong> SERP-də klik faizi</li>
                <li><strong>Conversion rate:</strong> Məqsədli hərəkətlər</li>
                <li><strong>Domain authority:</strong> Saytın gücü və etibarlılığı</li>
                <li><strong>Core Web Vitals:</strong> Sayt performansı</li>
            </ul>
            
            <h2>SEO-nun Gələcəyi və Trendlər</h2>
            <p>2024-cü ildə SEO sahəsində gözlənilən trendlər:</p>
            <ul>
                <li><strong>AI və Machine Learning:</strong> Google algoritmlərinin təkmilləşməsi</li>
                <li><strong>Voice Search:</strong> Səsli axtarış optimizasiyası</li>
                <li><strong>Mobile-first indexing:</strong> Mobil prioritet</li>
                <li><strong>Core Web Vitals:</strong> Performans prioritet</li>
                <li><strong>E-A-T:</strong> Expertise, Authoritativeness, Trustworthiness</li>
                <li><strong>Zero-click searches:</strong> SERP-də cavab alma</li>
            </ul>
            
            <p>SEO optimizasiyası uzunmüddətli prosesdir və nəticələr 3-6 ay ərzində görünə bilər. Sabırlı olun, davamlı strategiya izləyin və məzmununuzun keyfiyyətinə diqqət yetirin. Düzgün SEO strategiyası ilə saytınız axtarış sistemlərində daha yüksək pozisiyalar əldə edə bilər.</p>',
            'excerpt' => 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün comprehensive SEO strategiyaları, texnikaları və 2024 trendləri.',
            'featured_image' => 'images/blog/seo-optimization.jpg',
            'category_id' => 1,
            'author' => 'NextCode SEO Team',
            'read_time' => '12',
            'status' => 'published'
        ],
        2 => [
            'title' => 'Modern Web Development Trends 2024',
            'slug' => 'modern-web-development-trends',
            'content' => '<p>Web development sahəsi sürətlə dəyişir və 2024-cü ildə yeni texnologiyalar və trendlər meydana çıxır. Bu məqalədə, müasir web development-in ən vacib trendlərini və texnologiyalarını araşdıracağıq.</p>
            
            <h2>AI və Machine Learning İnteqrasiyası</h2>
            <p>Artificial Intelligence və Machine Learning texnologiyaları web development sahəsində inqilab yaradır:</p>
            <ul>
                <li><strong>ChatGPT və AI Assistants:</strong> Kod yazma və debug prosesini sürətləndirir</li>
                <li><strong>Automated Testing:</strong> AI əsaslı test generasiyası</li>
                <li><strong>Personalization:</strong> İstifadəçi təcrübəsinin fərdiləşdirilməsi</li>
                <li><strong>Content Generation:</strong> Dinamik məzmun yaradılması</li>
                <li><strong>Predictive Analytics:</strong> İstifadəçi davranışının proqnozlaşdırılması</li>
            </ul>
            
            <h3>AI Development Tools</h3>
            <p>AI əsaslı development alətləri:</p>
            <ul>
                <li><strong>GitHub Copilot:</strong> AI-powered code completion</li>
                <li><strong>Tabnine:</strong> Intelligent code suggestions</li>
                <li><strong>Amazon CodeWhisperer:</strong> AWS AI coding assistant</li>
                <li><strong>Replit Ghostwriter:</strong> AI pair programming</li>
            </ul>
            
            <h2>Serverless Architecture</h2>
            <p>Serverless computing web development-in gələcəyidir:</p>
            <ul>
                <li><strong>Function as a Service (FaaS):</strong> AWS Lambda, Azure Functions</li>
                <li><strong>Backend as a Service (BaaS):</strong> Firebase, Supabase</li>
                <li><strong>Edge Computing:</strong> CDN və edge functions</li>
                <li><strong>Microservices:</strong> Scalable və maintainable architecture</li>
                <li><strong>Containerization:</strong> Docker və Kubernetes</li>
            </ul>
            
            <h3>Serverless Benefits</h3>
            <p>Serverless architecture-in üstünlükləri:</p>
            <ul>
                <li><strong>Cost Efficiency:</strong> Pay-per-use pricing model</li>
                <li><strong>Auto-scaling:</strong> Automatic resource management</li>
                <li><strong>Reduced Complexity:</strong> No server management</li>
                <li><strong>Faster Deployment:</strong> Simplified deployment process</li>
            </ul>
            
            <h2>Progressive Web Apps (PWAs)</h2>
            <p>PWAs native app təcrübəsini web-də təmin edir:</p>
            <ul>
                <li><strong>Service Workers:</strong> Offline functionality</li>
                <li><strong>Web App Manifest:</strong> App-like experience</li>
                <li><strong>Push Notifications:</strong> User engagement</li>
                <li><strong>Responsive Design:</strong> Cross-platform compatibility</li>
                <li><strong>Fast Loading:</strong> Optimized performance</li>
            </ul>
            
            <h3>PWA Implementation</h3>
            <p>PWA development best practices:</p>
            <ul>
                <li><strong>Lighthouse Auditing:</strong> Performance optimization</li>
                <li><strong>Workbox:</strong> Service worker management</li>
                <li><strong>App Shell Model:</strong> Fast initial loading</li>
                <li><strong>Offline-first Design:</strong> Reliable user experience</li>
            </ul>
            
            <h2>WebAssembly (WASM)</h2>
            <p>WebAssembly high-performance web applications üçün yeni texnologiyadır:</p>
            <ul>
                <li><strong>Near-native Performance:</strong> C/C++/Rust performance</li>
                <li><strong>Cross-platform:</strong> Universal browser support</li>
                <li><strong>Security:</strong> Sandboxed execution</li>
                <li><strong>Language Agnostic:</strong> Multiple language support</li>
            </ul>
            
            <h3>WASM Use Cases</h3>
            <p>WebAssembly-in praktik tətbiqləri:</p>
            <ul>
                <li><strong>Gaming:</strong> High-performance games</li>
                <li><strong>Image/Video Processing:</strong> Media manipulation</li>
                <li><strong>Scientific Computing:</strong> Complex calculations</li>
                <li><strong>CAD Applications:</strong> Engineering software</li>
            </ul>
            
            <h2>Modern JavaScript Frameworks</h2>
            <p>2024-cü ildə ən populyar JavaScript frameworks:</p>
            <ul>
                <li><strong>React 18:</strong> Concurrent features və Suspense</li>
                <li><strong>Vue 3:</strong> Composition API və better TypeScript support</li>
                <li><strong>Angular 17:</strong> Standalone components və signals</li>
                <li><strong>Svelte 5:</strong> Runes və improved reactivity</li>
                <li><strong>SolidJS:</strong> Fine-grained reactivity</li>
            </ul>
            
            <h3>Framework Trends</h3>
            <p>Framework development trendləri:</p>
            <ul>
                <li><strong>Server-side Rendering (SSR):</strong> Next.js, Nuxt.js</li>
                <li><strong>Static Site Generation (SSG):</strong> Astro, SvelteKit</li>
                <li><strong>Islands Architecture:</strong> Selective hydration</li>
                <li><strong>Edge Runtime:</strong> Vercel Edge Functions</li>
            </ul>
            
            <h2>CSS və Styling Innovations</h2>
            <p>Modern CSS features və styling approaches:</p>
            <ul>
                <li><strong>CSS Grid və Flexbox:</strong> Advanced layouts</li>
                <li><strong>CSS Custom Properties:</strong> Dynamic theming</li>
                <li><strong>Container Queries:</strong> Component-based responsive design</li>
                <li><strong>CSS-in-JS:</strong> Styled-components, Emotion</li>
                <li><strong>Utility-first CSS:</strong> Tailwind CSS</li>
            </ul>
            
            <h2>Performance Optimization</h2>
            <p>Web performance optimization texnikaları:</p>
            <ul>
                <li><strong>Core Web Vitals:</strong> LCP, FID, CLS optimization</li>
                <li><strong>Code Splitting:</strong> Dynamic imports</li>
                <li><strong>Lazy Loading:</strong> Images və components</li>
                <li><strong>Bundle Optimization:</strong> Tree shaking, minification</li>
                <li><strong>Caching Strategies:</strong> Service worker caching</li>
            </ul>
            
            <h2>Security Best Practices</h2>
            <p>Modern web security considerations:</p>
            <ul>
                <li><strong>HTTPS Everywhere:</strong> SSL/TLS encryption</li>
                <li><strong>Content Security Policy (CSP):</strong> XSS protection</li>
                <li><strong>OWASP Guidelines:</strong> Security best practices</li>
                <li><strong>Authentication:</strong> JWT, OAuth 2.0</li>
                <li><strong>Input Validation:</strong> Sanitization və validation</li>
            </ul>
            
            <h2>Development Tools və Workflow</h2>
            <p>Modern development toolchain:</p>
            <ul>
                <li><strong>Vite:</strong> Fast build tool</li>
                <li><strong>ESBuild:</strong> Ultra-fast bundler</li>
                <li><strong>TypeScript:</strong> Type safety</li>
                <li><strong>ESLint/Prettier:</strong> Code quality</li>
                <li><strong>Husky:</strong> Git hooks</li>
            </ul>
            
            <h2>Future Predictions</h2>
            <p>Web development-in gələcəyi üçün proqnozlar:</p>
            <ul>
                <li><strong>AI-first Development:</strong> AI-assisted coding</li>
                <li><strong>Edge Computing:</strong> Distributed applications</li>
                <li><strong>Web3 Integration:</strong> Blockchain və decentralized apps</li>
                <li><strong>AR/VR Web:</strong> Immersive web experiences</li>
                <li><strong>Quantum Computing:</strong> Next-generation computing</li>
            </ul>
            
            <p>Web development sahəsi sürətlə inkişaf edir və developer-lər öz biliklərini davamlı yeniləməlidirlər. Bu trendlərə uyğunlaşmaq və yeni texnologiyaları öyrənmək, uğurlu web developer olmaq üçün vacibdir.</p>',
            'excerpt' => '2024-cü ildə web development sahəsindəki ən vacib trendlər: AI inteqrasiyası, serverless architecture, PWAs və WebAssembly.',
            'featured_image' => 'images/blog/web-development-trends.jpg',
            'category_id' => 2,
            'author' => 'NextCode Development Team',
            'read_time' => '15',
            'status' => 'published'
        ]
    ];
    
    // Blog verilerini güncelle
    foreach ($updated_posts as $id => $post_data) {
        $stmt = $pdo->prepare("
            UPDATE blog_posts 
            SET title = ?, slug = ?, content = ?, excerpt = ?, 
                featured_image = ?, author = ?, read_time = ?, 
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $result = $stmt->execute([
            $post_data['title'],
            $post_data['slug'],
            $post_data['content'],
            $post_data['excerpt'],
            $post_data['featured_image'],
            $post_data['author'],
            $post_data['read_time'],
            $id
        ]);
        
        if ($result) {
            echo "<p style='color: green;'>✓ Blog post ID $id güncellendi: " . $post_data['title'] . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Blog post ID $id güncellenemedi</p>";
        }
    }
    
    echo "<h2>Güncelleme Tamamlandı!</h2>";
    echo "<p><a href='blog-post.php?id=1'>SEO Makalesini Görüntüle</a></p>";
    echo "<p><a href='blog-post.php?id=2'>Web Development Makalesini Görüntüle</a></p>";
    echo "<p><a href='blog.php'>Blog Sayfasına Dön</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Hata: " . $e->getMessage() . "</p>";
}
?>


