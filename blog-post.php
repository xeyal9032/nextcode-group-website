<?php
// NextCode Group - Blog Post Page
define('SECURE_ACCESS', true);

require_once 'config/database.php';
require_once 'includes/page_functions.php';
require_once 'includes/content_helper.php';

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Production ready: Remove debug output for security
// Debug mode is disabled for production environment

if (!$post_id) {
    safeRedirect('blog.php');
}

// Try to get post from database first
$current_post = null;
try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        // Check if blog_posts table exists
        $tableExists = false;
        try {
            $checkTable = $pdo->query("SHOW TABLES LIKE 'blog_posts'");
            $tableExists = $checkTable->rowCount() > 0;
        } catch (Exception $e) {
            $tableExists = false;
        }
        
        if ($tableExists) {
            $stmt = $pdo->prepare("
                SELECT bp.*, bc.name as category_name 
                FROM blog_posts bp 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                WHERE bp.id = ? AND bp.status = 'published'
            ");
            $stmt->execute([$post_id]);
            $current_post = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
} catch (Exception $e) {
    error_log('Database error in blog-post.php: ' . $e->getMessage());
}

// If not found in database, use fallback data
if (!$current_post) {
    $fallback_posts = [
        1 => [
            'id' => 1,
            'title' => 'SEO Optimizasiyası: Axtarış Nəticələrində Yüksəlmək',
            'slug' => 'seo-optimizasiyasi',
            'content' => '<p>SEO (Search Engine Optimization) optimizasiyası, veb saytınızın Google, Bing və digər axtarış sistemlərində daha yaxşı görünməsi üçün edilən texniki və məzmun təkmilləşdirmələridir. Bu proses, hədəflənmiş auditoriyanıza çatmaq və daha çox trafik əldə etmək üçün vacibdir. 2024-cü ildə SEO sahəsi sürətlə dəyişir və yeni strategiyalar tələb edir.</p>
            
            <h2>SEO-nun Əsas Elementləri</h2>
            <p>SEO optimizasiyası üç əsas kateqoriyaya bölünür və hər biri özünəməxsus vaciblik daşıyır:</p>
            <ul>
                <li><strong>Texniki SEO:</strong> Saytın texniki performansını yaxşılaşdırır və axtarış sistemlərinin saytı düzgün indeksləməsini təmin edir</li>
                <li><strong>Məzmun SEO:</strong> Saytın məzmununu axtarış sistemləri üçün optimallaşdırır və istifadəçi təcrübəsini yaxşılaşdırır</li>
                <li><strong>Off-page SEO:</strong> Saytın xarici faktorlarını təkmilləşdirir və domain authority artırır</li>
            </ul>
            
            <h3>Texniki SEO Elementləri</h3>
            <p>Texniki SEO aşağıdakı elementləri əhatə edir və bunlar saytın əsasını təşkil edir:</p>
            <ul>
                <li><strong>Saytın yüklənmə sürəti:</strong> Core Web Vitals metrikaları (LCP, FID, CLS) və sayt performansı</li>
                <li><strong>Mobil uyğunluq:</strong> Responsive dizayn və mobil-first yanaşma ilə bütün cihazlarda optimal görünüm</li>
                <li><strong>SSL sertifikatı:</strong> HTTPS protokolu və təhlükəsizlik standartları</li>
                <li><strong>URL strukturu:</strong> Təmiz və aydın URL-lər ilə istifadəçi dostluğu</li>
                <li><strong>Meta taglər:</strong> Title, description və keywords ilə SERP optimizasiyası</li>
                <li><strong>Schema markup:</strong> Strukturlaşdırılmış məlumat ilə axtarış sistemlərinə kömək</li>
                <li><strong>XML sitemap:</strong> Axtarış sistemləri üçün sayt xəritəsi və səhifə kataloqu</li>
                <li><strong>Robots.txt:</strong> Crawler idarəetməsi və indeksləmə kontrolu</li>
                <li><strong>Canonical URL-lər:</strong> Duplicate content problemlərinin həlli</li>
                <li><strong>Internal linking:</strong> Daxili link strukturu və sayt arxitekturası</li>
            </ul>
            
            <h3>Məzmun SEO Elementləri</h3>
            <p>Məzmun SEO-nun əsas elementləri və best practices:</p>
            <ul>
                <li><strong>Keyworlərin düzgün istifadəsi:</strong> Natural və strategik keywor yerləşdirmə ilə keyword density optimizasiyası</li>
                <li><strong>Keyfiyyətli və orijinal məzmun:</strong> E-E-A-T prinsipləri (Experience, Expertise, Authoritativeness, Trustworthiness)</li>
                <li><strong>Başlıq strukturu:</strong> H1, H2, H3 hierarxiyası ilə məzmun təşkili</li>
                <li><strong>Daxili və xarici linklər:</strong> Link building strategiyası və authority artırma</li>
                <li><strong>Alt taglər:</strong> Şəkil optimizasiyası və accessibility</li>
                <li><strong>Məzmun uzunluğu:</strong> Dərin və əhatəli məzmun ilə comprehensive coverage</li>
                <li><strong>İstifadəçi təcrübəsi:</strong> UX/UI optimizasiyası və user engagement</li>
                <li><strong>Content freshness:</strong> Məzmunun yenilənməsi və güncellik</li>
                <li><strong>Semantic SEO:</strong> Topic clusters və content hubs</li>
                <li><strong>Long-tail keywords:</strong> Specific və niche keyworlər</li>
            </ul>
            
            <h3>Off-page SEO Elementləri</h3>
            <p>Off-page SEO faktorları və domain authority artırma:</p>
            <ul>
                <li><strong>Backlinklər:</strong> Keyfiyyətli xarici linklər və link profile management</li>
                <li><strong>Sosial media siqnalı:</strong> Sosial media mövcudluğu və engagement</li>
                <li><strong>Brend tanınması:</strong> Brand awareness və authority building</li>
                <li><strong>Local SEO:</strong> Yerli axtarış optimizasiyası və Google My Business</li>
                <li><strong>Guest posting:</strong> Industry publications və thought leadership</li>
                <li><strong>Influencer partnerships:</strong> Industry experts ilə collaboration</li>
                <li><strong>PR və media coverage:</strong> Press releases və media mentions</li>
            </ul>
            
            <h2>SEO Strategiyası və Planlaşdırma</h2>
            <p>Uğurlu SEO strategiyası üçün aşağıdakı addımları atın və systematic approach izləyin:</p>
            <ol>
                <li><strong>Keywor araşdırması:</strong> Google Keyword Planner, SEMrush, Ahrefs və digər alətlər ilə comprehensive research</li>
                <li><strong>Rəqib analizi:</strong> SERP analizi, rəqib strategiyaları və gap analysis</li>
                <li><strong>Texniki audit:</strong> Site audit, performans analizi və technical issues identification</li>
                <li><strong>Məzmun planı:</strong> Editorial calendar, content strategy və content gap analysis</li>
                <li><strong>Link building:</strong> Backlink əldə etmə strategiyası və outreach campaigns</li>
                <li><strong>Nəticələri izləmə:</strong> Google Analytics, Search Console və performance tracking</li>
                <li><strong>Davamlı təkmilləşdirmə:</strong> A/B testing, optimizasiya və continuous improvement</li>
                <li><strong>Reporting:</strong> Monthly reports və ROI measurement</li>
            </ol>
            
            <h2>SEO Alətləri və Platformalar</h2>
            <p>SEO işində istifadə edilən əsas alətlər və onların funksiyaları:</p>
            <ul>
                <li><strong>Google Search Console:</strong> Rəsmi Google aləti, indexing status və performance data</li>
                <li><strong>Google Analytics:</strong> Trafik analizi, user behavior və conversion tracking</li>
                <li><strong>SEMrush:</strong> Comprehensive SEO platform, competitor analysis və keyword research</li>
                <li><strong>Ahrefs:</strong> Backlink analizi, keyword research və site audit</li>
                <li><strong>Moz:</strong> Domain authority, link building və local SEO tools</li>
                <li><strong>Screaming Frog:</strong> Texniki SEO audit və site crawling</li>
                <li><strong>GTmetrix:</strong> Site speed analysis və performance optimization</li>
                <li><strong>PageSpeed Insights:</strong> Core Web Vitals və mobile optimization</li>
            </ul>
            
            <h2>SEO Metrikaları və KPI-lər</h2>
            <p>SEO uğurunu ölçmək üçün vacib metrikalar və performance indicators:</p>
            <ul>
                <li><strong>Organic trafik:</strong> Axtarış sistemlərindən gələn ziyarətçilər və traffic growth</li>
                <li><strong>Keyword rankings:</strong> Keyworlər üzrə pozisiyalar və SERP visibility</li>
                <li><strong>Click-through rate (CTR):</strong> SERP-də klik faizi və snippet optimization</li>
                <li><strong>Conversion rate:</strong> Məqsədli hərəkətlər və goal completions</li>
                <li><strong>Domain authority:</strong> Saytın gücü və etibarlılığı</li>
                <li><strong>Core Web Vitals:</strong> Sayt performansı və user experience metrics</li>
                <li><strong>Backlink profile:</strong> Link quality və quantity analysis</li>
                <li><strong>Brand mentions:</strong> Online reputation və brand awareness</li>
            </ul>
            
            <h2>Local SEO Optimizasiyası</h2>
            <p>Yerli bizneslər üçün Local SEO strategiyası:</p>
            <ul>
                <li><strong>Google My Business:</strong> Business listing optimization və review management</li>
                <li><strong>Local citations:</strong> Directory submissions və NAP consistency</li>
                <li><strong>Local keywords:</strong> Location-based keyword targeting</li>
                <li><strong>Local content:</strong> Community-focused content və local events</li>
                <li><strong>Local link building:</strong> Local partnerships və community involvement</li>
            </ul>
            
            <h2>E-commerce SEO</h2>
            <p>Online mağazalar üçün E-commerce SEO strategiyası:</p>
            <ul>
                <li><strong>Product page optimization:</strong> Product descriptions, images və structured data</li>
                <li><strong>Category optimization:</strong> Category pages və navigation structure</li>
                <li><strong>Product schema markup:</strong> Rich snippets və product information</li>
                <li><strong>Internal linking:</strong> Cross-selling və upselling opportunities</li>
                <li><strong>Customer reviews:</strong> Review schema və user-generated content</li>
            </ul>
            
            <h2>SEO-nun Gələcəyi və Trendlər</h2>
            <p>2024-cü ildə SEO sahəsində gözlənilən trendlər və future predictions:</p>
            <ul>
                <li><strong>AI və Machine Learning:</strong> Google algoritmlərinin təkmilləşməsi və RankBrain evolution</li>
                <li><strong>Voice Search:</strong> Səsli axtarış optimizasiyası və conversational queries</li>
                <li><strong>Mobile-first indexing:</strong> Mobil prioritet və mobile experience</li>
                <li><strong>Core Web Vitals:</strong> Performans prioritet və user experience metrics</li>
                <li><strong>E-A-T:</strong> Expertise, Authoritativeness, Trustworthiness və content quality</li>
                <li><strong>Zero-click searches:</strong> SERP-də cavab alma və featured snippets</li>
                <li><strong>Visual search:</strong> Image search optimization və visual content</li>
                <li><strong>Video SEO:</strong> YouTube optimization və video content strategy</li>
            </ul>
            
            <h2>Common SEO Mistakes</h2>
            <p>SEO-də edilən ümumi səhvlər və onların həlli:</p>
            <ul>
                <li><strong>Keyword stuffing:</strong> Over-optimization və natural language usage</li>
                <li><strong>Duplicate content:</strong> Content uniqueness və canonical tags</li>
                <li><strong>Poor site structure:</strong> Navigation optimization və user experience</li>
                <li><strong>Ignoring mobile:</strong> Mobile-first approach və responsive design</li>
                <li><strong>Slow loading times:</strong> Performance optimization və Core Web Vitals</li>
                <li><strong>Low-quality backlinks:</strong> Link quality over quantity</li>
                <li><strong>Neglecting analytics:</strong> Data-driven decisions və performance tracking</li>
            </ul>
            
            <h2>SEO ROI və Business Impact</h2>
            <p>SEO-nun business üzərində təsiri və ROI measurement:</p>
            <ul>
                <li><strong>Cost per acquisition:</strong> SEO vs paid advertising comparison</li>
                <li><strong>Long-term value:</strong> Organic traffic sustainability</li>
                <li><strong>Brand visibility:</strong> Search presence və market share</li>
                <li><strong>Customer lifetime value:</strong> SEO traffic quality və conversion rates</li>
                <li><strong>Competitive advantage:</strong> Market positioning və industry leadership</li>
            </ul>
            
            <h2>Conclusion</h2>
            <p>SEO optimizasiyası uzunmüddətli prosesdir və nəticələr 3-6 ay ərzində görünə bilər. Sabırlı olun, davamlı strategiya izləyin və məzmununuzun keyfiyyətinə diqqət yetirin. Düzgün SEO strategiyası ilə saytınız axtarış sistemlərində daha yüksək pozisiyalar əldə edə bilər.</p>
            
            <p>2024-cü ildə SEO sahəsi sürətlə dəyişir və süni intellekt, səsli axtarış, mobil prioritet indeksləmə kimi trendlər prioritet olur. Bu dəyişikliklərə uyğunlaşmaq və istifadəçi təcrübəsinə fokuslanmaq vacibdir. Düzgün SEO strategiyası ilə üzvi trafik artırmaq və biznes məqsədlərinə çatmaq mümkündür.</p>',
            'excerpt' => 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün hərtərəfli SEO strategiyaları, texnikaları və 2024 trendləri. Google, Bing və digər axtarış sistemlərində daha yüksək pozisiyalar əldə etmək üçün texniki və məzmun SEO.',
            'featured_image' => 'images/blog/seo-optimization.jpg',
            'category_name' => 'SEO',
            'author' => 'NextCode SEO Team',
            'read_time' => '12',
            'created_at' => '2024-01-15 10:00:00'
        ],
        2 => [
            'id' => 2,
            'title' => 'Müasir Web İnkişaf Trendləri 2024',
            'slug' => 'muasir-web-inkisaf-trendleri-2024',
            'content' => '<p>Web development sahəsi sürətlə dəyişir və 2024-cü ildə yeni texnologiyalar və trendlər meydana çıxır. Bu məqalədə, müasir web development-in ən vacib trendlərini və texnologiyalarını araşdıracağıq. Texnologiya dünyasında hər gün yeniliklər baş verir və developer-lər bu dəyişikliklərə uyğunlaşmalıdırlar.</p>
            
            <h2>AI və Machine Learning İnteqrasiyası</h2>
            <p>Artificial Intelligence və Machine Learning texnologiyaları web development sahəsində inqilab yaradır. 2024-cü ildə AI artıq sadəcə trend deyil, vacib bir alət halına gəlib. AI texnologiyaları developer-lərin işini asanlaşdırır və daha effektiv hala gətirir.</p>
            
            <h3>AI-in Web Development-də Tətbiqi</h3>
            <p>AI texnologiyaları müxtəlif sahələrdə tətbiq olunur:</p>
            <ul>
                <li><strong>ChatGPT və AI Assistants:</strong> Kod yazma və debug prosesini sürətləndirir. Developer-lər natural dil ilə kod yazmaq və problemləri həll etmək üçün AI-dan istifadə edirlər.</li>
                <li><strong>Automated Testing:</strong> AI əsaslı test generasiyası sayəsində test yazmaq daha sürətli və effektiv olur.</li>
                <li><strong>Personalization:</strong> İstifadəçi təcrübəsinin fərdiləşdirilməsi üçün AI alqoritmləri istifadə olunur.</li>
                <li><strong>Content Generation:</strong> Dinamik məzmun yaradılması və avtomatik mətn generasiyası.</li>
                <li><strong>Predictive Analytics:</strong> İstifadəçi davranışının proqnozlaşdırılması və trend analizi.</li>
            </ul>
            
            <h3>AI Development Tools</h3>
            <p>AI əsaslı development alətləri developer-lərin gündəlik işlərində vacib rol oynayır:</p>
            <ul>
                <li><strong>GitHub Copilot:</strong> AI-powered code completion ilə kod yazma sürətini artırır</li>
                <li><strong>Tabnine:</strong> Intelligent code suggestions və autocomplete funksiyaları</li>
                <li><strong>Amazon CodeWhisperer:</strong> AWS ekosistemi üçün AI coding assistant</li>
                <li><strong>Replit Ghostwriter:</strong> AI pair programming və collaborative coding</li>
                <li><strong>Cursor AI:</strong> Advanced code editing və generation</li>
            </ul>
            
            <h2>Serverless Architecture</h2>
            <p>Serverless computing web development-in gələcəyidir və 2024-cü ildə daha da populyar olur. Serverless architecture developer-lərə server idarəetməsi olmadan scalable applications yaratmaq imkanı verir.</p>
            
            <h3>Serverless Platformalar</h3>
            <p>Müxtəlif serverless platformalar mövcuddur:</p>
            <ul>
                <li><strong>Function as a Service (FaaS):</strong> AWS Lambda, Azure Functions, Google Cloud Functions</li>
                <li><strong>Backend as a Service (BaaS):</strong> Firebase, Supabase, AWS Amplify</li>
                <li><strong>Edge Computing:</strong> CDN və edge functions ilə performans optimizasiyası</li>
                <li><strong>Microservices:</strong> Scalable və maintainable architecture</li>
                <li><strong>Containerization:</strong> Docker və Kubernetes ilə container orchestration</li>
            </ul>
            
            <h3>Serverless Benefits</h3>
            <p>Serverless architecture-in əsas üstünlükləri:</p>
            <ul>
                <li><strong>Cost Efficiency:</strong> Pay-per-use pricing model ilə yalnız istifadə etdiyiniz qədər ödəyirsiniz</li>
                <li><strong>Auto-scaling:</strong> Automatic resource management və traffic spikes-ə avtomatik cavab</li>
                <li><strong>Reduced Complexity:</strong> Server management, maintenance və infrastructure concerns-lərdən azadlıq</li>
                <li><strong>Faster Deployment:</strong> Simplified deployment process və continuous integration</li>
                <li><strong>Global Availability:</strong> Edge locations ilə dünya miqyasında performans</li>
            </ul>
            
            <h2>Progressive Web Apps (PWAs)</h2>
            <p>PWAs native app təcrübəsini web-də təmin edir və 2024-cü ildə daha da inkişaf edir. PWAs istifadəçilərə app store-dan endirmədən native app təcrübəsi təqdim edir.</p>
            
            <h3>PWA Xüsusiyyətləri</h3>
            <p>PWAs-in əsas xüsusiyyətləri:</p>
            <ul>
                <li><strong>Service Workers:</strong> Offline functionality və background sync</li>
                <li><strong>Web App Manifest:</strong> App-like experience və home screen installation</li>
                <li><strong>Push Notifications:</strong> User engagement və retention artırma</li>
                <li><strong>Responsive Design:</strong> Cross-platform compatibility və device adaptation</li>
                <li><strong>Fast Loading:</strong> Optimized performance və Core Web Vitals compliance</li>
                <li><strong>Secure Context:</strong> HTTPS requirement və security best practices</li>
            </ul>
            
            <h3>PWA Implementation</h3>
            <p>PWA development best practices:</p>
            <ul>
                <li><strong>Lighthouse Auditing:</strong> Performance optimization və PWA score improvement</li>
                <li><strong>Workbox:</strong> Service worker management və caching strategies</li>
                <li><strong>App Shell Model:</strong> Fast initial loading və skeleton screens</li>
                <li><strong>Offline-first Design:</strong> Reliable user experience və graceful degradation</li>
                <li><strong>Progressive Enhancement:</strong> Core functionality ilə başlayıb advanced features əlavə etmək</li>
            </ul>
            
            <h2>WebAssembly (WASM)</h2>
            <p>WebAssembly high-performance web applications üçün yeni texnologiyadır və 2024-cü ildə daha geniş tətbiq tapır. WASM C/C++/Rust kimi dillərdə yazılmış kodları browser-də işlədə bilmək imkanı verir.</p>
            
            <h3>WASM Xüsusiyyətləri</h3>
            <p>WebAssembly-in əsas xüsusiyyətləri:</p>
            <ul>
                <li><strong>Near-native Performance:</strong> C/C++/Rust performance ilə JavaScript-dən çox daha sürətli</li>
                <li><strong>Cross-platform:</strong> Universal browser support və platform independence</li>
                <li><strong>Security:</strong> Sandboxed execution və memory safety</li>
                <li><strong>Language Agnostic:</strong> Multiple language support və existing code reuse</li>
                <li><strong>Compact Binary Format:</strong> Small file sizes və fast loading</li>
            </ul>
            
            <h3>WASM Use Cases</h3>
            <p>WebAssembly-in praktik tətbiqləri:</p>
            <ul>
                <li><strong>Gaming:</strong> High-performance games və game engines</li>
                <li><strong>Image/Video Processing:</strong> Media manipulation və real-time editing</li>
                <li><strong>Scientific Computing:</strong> Complex calculations və data processing</li>
                <li><strong>CAD Applications:</strong> Engineering software və 3D modeling</li>
                <li><strong>Cryptocurrency:</strong> Blockchain applications və mining algorithms</li>
                <li><strong>Machine Learning:</strong> AI model inference və neural networks</li>
            </ul>
            
            <h2>Modern JavaScript Frameworks</h2>
            <p>2024-cü ildə JavaScript frameworks sahəsində böyük inkişaflar baş verib. Yeni versiyalar və features developer-lərə daha yaxşı təcrübə təqdim edir.</p>
            
            <h3>Populyar Frameworks</h3>
            <p>2024-cü ildə ən populyar JavaScript frameworks:</p>
            <ul>
                <li><strong>React 18:</strong> Concurrent features, Suspense və automatic batching</li>
                <li><strong>Vue 3:</strong> Composition API, better TypeScript support və improved performance</li>
                <li><strong>Angular 17:</strong> Standalone components, signals və improved developer experience</li>
                <li><strong>Svelte 5:</strong> Runes system, improved reactivity və better performance</li>
                <li><strong>SolidJS:</strong> Fine-grained reactivity və excellent performance</li>
                <li><strong>Qwik:</strong> Resumable framework və instant loading</li>
            </ul>
            
            <h3>Framework Trends</h3>
            <p>Framework development trendləri:</p>
            <ul>
                <li><strong>Server-side Rendering (SSR):</strong> Next.js, Nuxt.js, SvelteKit</li>
                <li><strong>Static Site Generation (SSG):</strong> Astro, SvelteKit, Next.js</li>
                <li><strong>Islands Architecture:</strong> Selective hydration və partial page updates</li>
                <li><strong>Edge Runtime:</strong> Vercel Edge Functions, Cloudflare Workers</li>
                <li><strong>Micro-frontends:</strong> Module Federation və independent deployments</li>
            </ul>
            
            <h2>CSS və Styling Innovations</h2>
            <p>CSS sahəsində də böyük inkişaflar baş verib. Yeni features və approaches developer-lərə daha yaxşı styling imkanları verir.</p>
            
            <h3>Modern CSS Features</h3>
            <p>Modern CSS features və styling approaches:</p>
            <ul>
                <li><strong>CSS Grid və Flexbox:</strong> Advanced layouts və complex positioning</li>
                <li><strong>CSS Custom Properties:</strong> Dynamic theming və maintainable styles</li>
                <li><strong>Container Queries:</strong> Component-based responsive design</li>
                <li><strong>CSS-in-JS:</strong> Styled-components, Emotion, Stitches</li>
                <li><strong>Utility-first CSS:</strong> Tailwind CSS, UnoCSS</li>
                <li><strong>CSS Modules:</strong> Scoped styles və component isolation</li>
                <li><strong>CSS Houdini:</strong> Low-level CSS API access</li>
            </ul>
            
            <h2>Performance Optimization</h2>
            <p>Web performance optimization 2024-cü ildə daha da vacib olub. Google-in Core Web Vitals metrikaları və user experience prioritetləri performance optimization-u vacib edir.</p>
            
            <h3>Performance Teknikaları</h3>
            <p>Web performance optimization texnikaları:</p>
            <ul>
                <li><strong>Core Web Vitals:</strong> LCP, FID, CLS optimization və user experience metrics</li>
                <li><strong>Code Splitting:</strong> Dynamic imports və lazy loading</li>
                <li><strong>Lazy Loading:</strong> Images, components və route-based splitting</li>
                <li><strong>Bundle Optimization:</strong> Tree shaking, minification və compression</li>
                <li><strong>Caching Strategies:</strong> Service worker caching, CDN optimization</li>
                <li><strong>Critical CSS:</strong> Above-the-fold optimization və render blocking elimination</li>
                <li><strong>Resource Hints:</strong> Preload, prefetch, preconnect strategies</li>
            </ul>
            
            <h2>Security Best Practices</h2>
            <p>Web security 2024-cü ildə daha da vacib olub. Artan cyber threats və regulations developer-ləri daha yaxşı security practices tətbiq etməyə məcbur edir.</p>
            
            <h3>Security Considerations</h3>
            <p>Modern web security considerations:</p>
            <ul>
                <li><strong>HTTPS Everywhere:</strong> SSL/TLS encryption və secure data transmission</li>
                <li><strong>Content Security Policy (CSP):</strong> XSS protection və resource loading control</li>
                <li><strong>OWASP Guidelines:</strong> Security best practices və vulnerability prevention</li>
                <li><strong>Authentication:</strong> JWT, OAuth 2.0, Multi-factor authentication</li>
                <li><strong>Input Validation:</strong> Sanitization və validation ilə injection attacks prevention</li>
                <li><strong>API Security:</strong> Rate limiting, authentication, authorization</li>
                <li><strong>Dependency Management:</strong> Regular updates və vulnerability scanning</li>
            </ul>
            
            <h2>Development Tools və Workflow</h2>
            <p>Development tools və workflow 2024-cü ildə daha da inkişaf edib. Yeni tools və approaches developer productivity-ni artırır.</p>
            
            <h3>Modern Development Toolchain</h3>
            <p>Modern development toolchain:</p>
            <ul>
                <li><strong>Vite:</strong> Fast build tool və development server</li>
                <li><strong>ESBuild:</strong> Ultra-fast bundler və JavaScript compiler</li>
                <li><strong>TypeScript:</strong> Type safety və better developer experience</li>
                <li><strong>ESLint/Prettier:</strong> Code quality və formatting</li>
                <li><strong>Husky:</strong> Git hooks və pre-commit validation</li>
                <li><strong>Jest/Vitest:</strong> Testing frameworks və unit testing</li>
                <li><strong>Playwright/Cypress:</strong> End-to-end testing və browser automation</li>
            </ul>
            
            <h2>Future Predictions</h2>
            <p>Web development-in gələcəyi üçün proqnozlar və emerging technologies:</p>
            
            <h3>Emerging Technologies</h3>
            <ul>
                <li><strong>AI-first Development:</strong> AI-assisted coding və automated development</li>
                <li><strong>Edge Computing:</strong> Distributed applications və edge-native architectures</li>
                <li><strong>Web3 Integration:</strong> Blockchain və decentralized applications</li>
                <li><strong>AR/VR Web:</strong> Immersive web experiences və spatial computing</li>
                <li><strong>Quantum Computing:</strong> Next-generation computing və quantum algorithms</li>
                <li><strong>WebGPU:</strong> High-performance graphics və compute shaders</li>
                <li><strong>Web Streams API:</strong> Real-time data processing və streaming</li>
            </ul>
            
            <h2>Developer Skills və Learning</h2>
            <p>2024-cü ildə web developer-lər üçün vacib olan skills və learning paths:</p>
            
            <h3>Essential Skills</h3>
            <ul>
                <li><strong>Modern JavaScript:</strong> ES2024 features, async programming, modules</li>
                <li><strong>TypeScript:</strong> Type safety, interfaces, generics</li>
                <li><strong>React/Vue/Angular:</strong> Component-based development</li>
                <li><strong>Node.js:</strong> Server-side JavaScript və backend development</li>
                <li><strong>Database Knowledge:</strong> SQL, NoSQL, database optimization</li>
                <li><strong>DevOps:</strong> CI/CD, containerization, cloud platforms</li>
                <li><strong>Testing:</strong> Unit testing, integration testing, E2E testing</li>
            </ul>
            
            <h3>Learning Resources</h3>
            <ul>
                <li><strong>Official Documentation:</strong> Framework və library documentation</li>
                <li><strong>Online Courses:</strong> Udemy, Coursera, freeCodeCamp</li>
                <li><strong>YouTube Channels:</strong> Web development tutorials və coding channels</li>
                <li><strong>Blogs və Articles:</strong> Medium, Dev.to, personal developer blogs</li>
                <li><strong>Open Source Projects:</strong> GitHub repositories və contribution</li>
                <li><strong>Developer Communities:</strong> Stack Overflow, Reddit, Discord servers</li>
            </ul>
            
            <h2>Conclusion</h2>
            <p>Veb inkişaf sahəsi sürətlə inkişaf edir və proqramçılar öz biliklərini davamlı yeniləməlidirlər. 2024-cü ildə süni intellekt inteqrasiyası, serversiz arxitektura, proqressiv veb tətbiqlər və VebAssembli kimi texnologiyalar əsas trendlərdir. Bu trendlərə uyğunlaşmaq və yeni texnologiyaları öyrənmək, uğurlu veb proqramçı olmaq üçün vacibdir.</p>
            
            <p>Gələcəkdə daha çox süni intellekt köməkli inkişaf, kənar hesablama və imersiv veb təcrübələr gözləyirik. Proqramçılar bu dəyişikliklərə hazır olmalı və öz bacarıqlarını davamlı inkişaf etdirməlidirlər.</p>',
            'excerpt' => '2024-cü ildə veb inkişaf sahəsindəki ən vacib trendlər: süni intellekt inteqrasiyası, serversiz arxitektura, proqressiv veb tətbiqlər və VebAssembli.',
            'featured_image' => 'images/blog/web-development-trends.jpg',
            'category_name' => 'Veb İnkişaf',
            'author' => 'NextCode Development Team',
            'read_time' => '15',
            'created_at' => '2024-01-10 14:30:00'
        ],
        3 => [
            'id' => 3,
            'title' => 'E-ticarət İnkişafı: Online Mağaza Yaratmaq',
            'slug' => 'e-ticaret-inkisafi-online-magaza-yaratmaq',
            'content' => '<p>E-commerce development, online mağaza yaratmaq və idarə etmək üçün vacib texniki biliklərdir. Bu məqalədə, uğurlu e-commerce platforması yaratmaq üçün lazım olan bütün aspektləri araşdıracağıq. 2024-cü ildə e-commerce sahəsi sürətlə inkişaf edir və yeni texnologiyalar business-lərə daha yaxşı imkanlar təqdim edir.</p>
            
            <h2>E-commerce Platform Seçimi</h2>
            <p>Düzgün platforma seçimi e-commerce uğurunun əsasını təşkil edir. Hər platform özünəməxsus üstünlüklər və məhdudiyyətlərə malikdir:</p>
            <ul>
                <li><strong>Shopify:</strong> User-friendly interface, hosted solution və comprehensive app ecosystem</li>
                <li><strong>WooCommerce:</strong> WordPress əsaslı, highly customizable və cost-effective</li>
                <li><strong>Magento:</strong> Enterprise-level features, scalable architecture və advanced functionality</li>
                <li><strong>BigCommerce:</strong> Built-in features, excellent performance və SEO optimization</li>
                <li><strong>Custom Development:</strong> Tailored solution, full control və unique requirements</li>
                <li><strong>PrestaShop:</strong> Open-source, multilingual support və extensive customization</li>
                <li><strong>OpenCart:</strong> Lightweight, easy to use və suitable for small businesses</li>
            </ul>
            
            <h3>Platform Comparison və Seçim Kriteriyaları</h3>
            <p>Platform müqayisəsi və seçim zamanı nəzərə alınmalı faktorlar:</p>
            <ul>
                <li><strong>Ease of Use:</strong> Shopify > WooCommerce > BigCommerce > Magento</li>
                <li><strong>Customization:</strong> Magento > WooCommerce > BigCommerce > Shopify</li>
                <li><strong>Performance:</strong> BigCommerce > Shopify > Magento > WooCommerce</li>
                <li><strong>Cost:</strong> WooCommerce > Shopify > BigCommerce > Magento</li>
                <li><strong>SEO Capabilities:</strong> BigCommerce > Magento > WooCommerce > Shopify</li>
                <li><strong>Scalability:</strong> Magento > BigCommerce > Shopify > WooCommerce</li>
                <li><strong>Support Quality:</strong> Shopify > BigCommerce > Magento > WooCommerce</li>
            </ul>
            
            <h2>Essential E-commerce Features</h2>
            <p>Hər e-commerce platformasında olmalı əsas funksiyalar və must-have features:</p>
            <ul>
                <li><strong>Product Management:</strong> Inventory tracking, product variants, categories və bulk operations</li>
                <li><strong>Shopping Cart:</strong> Add/remove items, quantity updates, save for later və cart abandonment recovery</li>
                <li><strong>Checkout Process:</strong> Guest checkout, registered user checkout, one-page checkout və multi-step process</li>
                <li><strong>Payment Gateway:</strong> Multiple payment methods, secure processing və PCI compliance</li>
                <li><strong>Order Management:</strong> Order tracking, status updates, fulfillment workflow və customer notifications</li>
                <li><strong>User Accounts:</strong> Registration, login, profile management, order history və wishlist</li>
                <li><strong>Search Functionality:</strong> Product search, filtering, sorting və autocomplete suggestions</li>
                <li><strong>Reviews və Ratings:</strong> Customer feedback system, moderation tools və review display</li>
                <li><strong>Shipping Management:</strong> Multiple shipping options, rate calculation və tracking integration</li>
                <li><strong>Tax Calculation:</strong> Automatic tax calculation, multiple tax rates və compliance</li>
            </ul>
            
            <h3>Advanced E-commerce Features</h3>
            <p>Advanced e-commerce funksiyaları və competitive advantages:</p>
            <ul>
                <li><strong>AI Recommendations:</strong> Personalized product suggestions, machine learning algorithms və behavioral analysis</li>
                <li><strong>Wishlist:</strong> Save for later functionality, shareable wishlists və price drop notifications</li>
                <li><strong>Multi-language Support:</strong> Internationalization, currency conversion və localized content</li>
                <li><strong>Multi-currency:</strong> Global payment support, real-time exchange rates və localized pricing</li>
                <li><strong>Inventory Management:</strong> Stock tracking, low stock alerts, automated reordering və supplier integration</li>
                <li><strong>Analytics Dashboard:</strong> Sales analytics, customer insights, performance metrics və business intelligence</li>
                <li><strong>CRM Integration:</strong> Customer relationship management, email marketing və customer segmentation</li>
                <li><strong>Subscription Commerce:</strong> Recurring billing, subscription management və customer retention</li>
            </ul>
            
            <h2>Payment Gateway Integration</h2>
            <p>Payment gateway seçimi və inteqrasiyası - e-commerce uğurunun əsas elementi:</p>
            <ul>
                <li><strong>Stripe:</strong> Developer-friendly, global reach, comprehensive APIs və advanced fraud protection</li>
                <li><strong>PayPal:</strong> Widely accepted, trusted by customers, express checkout və buyer protection</li>
                <li><strong>Square:</strong> Small business focused, integrated POS, simple pricing və local market support</li>
                <li><strong>Authorize.Net:</strong> Enterprise solution, reliable processing, advanced features və merchant account required</li>
                <li><strong>Local Gateways:</strong> Region-specific options, local currency support və regulatory compliance</li>
                <li><strong>Cryptocurrency:</strong> Bitcoin, Ethereum support, lower fees və global accessibility</li>
                <li><strong>Mobile Payments:</strong> Apple Pay, Google Pay, Samsung Pay integration və contactless payments</li>
            </ul>
            
            <h3>Payment Security və Best Practices</h3>
            <p>Payment security best practices və compliance requirements:</p>
            <ul>
                <li><strong>PCI DSS Compliance:</strong> Payment card industry standards, security requirements və regular audits</li>
                <li><strong>SSL Certificates:</strong> Encrypted data transmission, HTTPS implementation və certificate management</li>
                <li><strong>Tokenization:</strong> Secure payment data storage, token-based transactions və data protection</li>
                <li><strong>Fraud Detection:</strong> Automated fraud prevention, machine learning algorithms və risk assessment</li>
                <li><strong>3D Secure:</strong> Additional authentication layer, reduced chargebacks və enhanced security</li>
                <li><strong>Data Encryption:</strong> End-to-end encryption, secure key management və data privacy</li>
                <li><strong>Regular Security Audits:</strong> Vulnerability assessments, penetration testing və compliance monitoring</li>
            </ul>
            
            <h2>Mobile Commerce (M-commerce)</h2>
            <p>Mobile commerce optimization və mobile-first approach:</p>
            <ul>
                <li><strong>Responsive Design:</strong> Mobile-first approach, fluid layouts və device compatibility</li>
                <li><strong>Progressive Web App:</strong> App-like experience, offline functionality və push notifications</li>
                <li><strong>Mobile Payment:</strong> Apple Pay, Google Pay, Samsung Pay integration və biometric authentication</li>
                <li><strong>Touch Optimization:</strong> Finger-friendly interface, gesture support və touch targets</li>
                <li><strong>Fast Loading:</strong> Optimized for mobile networks, compressed images və lazy loading</li>
                <li><strong>Mobile UX:</strong> Simplified navigation, thumb-friendly design və mobile-specific features</li>
                <li><strong>App Store Optimization:</strong> Mobile app development, ASO strategies və app marketing</li>
            </ul>
            
            <h2>SEO for E-commerce</h2>
            <p>E-commerce SEO strategies və search visibility optimization:</p>
            <ul>
                <li><strong>Product Page Optimization:</strong> Title tags, meta descriptions, product images və structured data</li>
                <li><strong>Category Structure:</strong> Logical hierarchy, breadcrumb navigation və URL optimization</li>
                <li><strong>Internal Linking:</strong> Cross-product recommendations, related products və navigation flow</li>
                <li><strong>Schema Markup:</strong> Product structured data, review markup və rich snippets</li>
                <li><strong>Site Speed:</strong> Core Web Vitals optimization, image compression və caching strategies</li>
                <li><strong>Mobile SEO:</strong> Mobile-first indexing, responsive design və mobile usability</li>
                <li><strong>Local SEO:</strong> Local business optimization, Google My Business və location-based targeting</li>
            </ul>
            
            <h3>E-commerce Content Strategy</h3>
            <p>E-commerce content marketing və customer engagement:</p>
            <ul>
                <li><strong>Product Descriptions:</strong> Detailed, compelling descriptions, feature benefits və customer-focused copy</li>
                <li><strong>Blog Content:</strong> Educational content, industry insights və thought leadership</li>
                <li><strong>User-generated Content:</strong> Customer reviews, testimonials, photos və social proof</li>
                <li><strong>Video Content:</strong> Product demonstrations, tutorials, unboxing videos və brand stories</li>
                <li><strong>Social Proof:</strong> Customer photos, case studies, influencer partnerships və social media integration</li>
                <li><strong>Email Marketing:</strong> Newsletter campaigns, abandoned cart recovery və personalized recommendations</li>
                <li><strong>Influencer Marketing:</strong> Brand partnerships, sponsored content və social media campaigns</li>
            </ul>
            
            <h2>Performance Optimization</h2>
            <p>E-commerce performance best practices və speed optimization:</p>
            <ul>
                <li><strong>Image Optimization:</strong> WebP format, lazy loading, responsive images və CDN delivery</li>
                <li><strong>CDN Implementation:</strong> Global content delivery, edge caching və performance improvement</li>
                <li><strong>Caching Strategy:</strong> Page caching, database caching, object caching və full-page cache</li>
                <li><strong>Database Optimization:</strong> Query optimization, indexing, database cleanup və performance monitoring</li>
                <li><strong>Code Minification:</strong> CSS, JS compression, unused code removal və bundle optimization</li>
                <li><strong>Server Optimization:</strong> Server configuration, PHP optimization, memory management və resource allocation</li>
                <li><strong>Third-party Optimization:</strong> Script optimization, external resource management və loading prioritization</li>
            </ul>
            
            <h2>Analytics və Tracking</h2>
            <p>E-commerce analytics setup və business intelligence:</p>
            <ul>
                <li><strong>Google Analytics 4:</strong> Enhanced e-commerce tracking, conversion goals və customer journey analysis</li>
                <li><strong>Conversion Tracking:</strong> Goal setup, funnel analysis, attribution modeling və ROI measurement</li>
                <li><strong>Heatmap Tools:</strong> User behavior analysis, click tracking, scroll mapping və usability insights</li>
                <li><strong>A/B Testing:</strong> Conversion optimization, multivariate testing, statistical significance və continuous improvement</li>
                <li><strong>Customer Journey:</strong> Multi-touch attribution, customer lifetime value və retention analysis</li>
                <li><strong>Sales Analytics:</strong> Revenue tracking, product performance, seasonal trends və forecasting</li>
                <li><strong>Marketing Attribution:</strong> Campaign tracking, channel performance, ROI analysis və budget optimization</li>
            </ul>
            
            <h2>Customer Experience (CX)</h2>
            <p>E-commerce customer experience optimization və user satisfaction:</p>
            <ul>
                <li><strong>User Interface:</strong> Intuitive navigation, clear product presentation və user-friendly design</li>
                <li><strong>Search Functionality:</strong> Smart search, advanced filters, autocomplete suggestions və search analytics</li>
                <li><strong>Personalization:</strong> Tailored recommendations, personalized content və customer segmentation</li>
                <li><strong>Customer Support:</strong> Live chat, FAQ, help center, ticket system və knowledge base</li>
                <li><strong>Return Policy:</strong> Clear return policy, easy returns process, customer-friendly terms və automated processing</li>
                <li><strong>Shipping Experience:</strong> Fast shipping, tracking updates, delivery notifications və shipping options</li>
                <li><strong>Post-purchase Experience:</strong> Order confirmation, delivery updates, customer feedback və loyalty programs</li>
            </ul>
            
            <h2>E-commerce Security</h2>
            <p>E-commerce security measures və data protection:</p>
            <ul>
                <li><strong>Data Encryption:</strong> SSL/TLS encryption, data at rest encryption və secure data transmission</li>
                <li><strong>User Authentication:</strong> Strong passwords, two-factor authentication və account security</li>
                <li><strong>Fraud Prevention:</strong> Risk assessment, suspicious activity monitoring və automated fraud detection</li>
                <li><strong>Regular Backups:</strong> Data backup, disaster recovery, business continuity və data integrity</li>
                <li><strong>Security Monitoring:</strong> Real-time monitoring, intrusion detection, vulnerability scanning və incident response</li>
                <li><strong>Compliance:</strong> GDPR compliance, data privacy, cookie consent və regulatory requirements</li>
            </ul>
            
            <h2>Future Trends və Innovation</h2>
            <p>E-commerce future trends və emerging technologies:</p>
            <ul>
                <li><strong>Voice Commerce:</strong> Voice-activated shopping, smart speakers integration və conversational commerce</li>
                <li><strong>AR/VR Shopping:</strong> Virtual try-on experiences, 3D product visualization və immersive shopping</li>
                <li><strong>AI Chatbots:</strong> Intelligent customer service, automated support və conversational AI</li>
                <li><strong>Subscription Commerce:</strong> Recurring revenue models, subscription management və customer retention</li>
                <li><strong>Social Commerce:</strong> Shopping on social platforms, influencer commerce və social selling</li>
                <li><strong>Blockchain Commerce:</strong> Cryptocurrency payments, smart contracts və decentralized marketplaces</li>
                <li><strong>IoT Integration:</strong> Smart devices, connected products və automated purchasing</li>
            </ul>
            
            <h2>E-commerce Marketing Strategies</h2>
            <p>Digital marketing strategies for e-commerce success:</p>
            <ul>
                <li><strong>Search Engine Marketing:</strong> Google Ads, Bing Ads, keyword targeting və ad optimization</li>
                <li><strong>Social Media Marketing:</strong> Facebook, Instagram, TikTok advertising və social commerce</li>
                <li><strong>Email Marketing:</strong> Newsletter campaigns, abandoned cart recovery, personalized emails və automation</li>
                <li><strong>Content Marketing:</strong> Blog content, video marketing, influencer partnerships və SEO content</li>
                <li><strong>Affiliate Marketing:</strong> Partner programs, commission structures, tracking systems və performance management</li>
                <li><strong>Retargeting Campaigns:</strong> Display advertising, social retargeting, email retargeting və cross-device tracking</li>
            </ul>
            
            <h2>Conclusion</h2>
            <p>E-ticarət inkişafı mürəkkəb prosesdir və müxtəlif texniki və marketinq aspektləri tələb edir. Düzgün strategiya və tətbiq ilə uğurlu onlayn mağaza yarada bilərsiniz. 2024-cü ildə e-ticarət sahəsi sürətlə inkişaf edir və yeni texnologiyalar bizneslərə daha yaxşı imkanlar təqdim edir.</p>
            
            <p>Uğurlu e-ticarət platforması yaratmaq üçün müştəri təcrübəsinə fokuslanmaq, performans optimizasiyası etmək və təhlükəsizlik tədbirlərini tətbiq etmək vacibdir. Düzgün strategiya ilə onlayn mağazanızı rəqabətli bazar mühitində uğurla idarə edə bilərsiniz.</p>',
            'excerpt' => 'E-ticarət platforması yaratmaq üçün hərtərəfli təlimat: platform seçimi, ödəniş inteqrasiyası, mobil optimizasiya və performans. Onlayn mağaza yaratmaq və idarə etmək üçün vacib texniki biliklər və ən yaxşı təcrübələr.',
            'featured_image' => 'images/blog/ecommerce-development.jpg',
            'category_name' => 'E-ticarət',
            'author' => 'NextCode E-commerce Team',
            'read_time' => '18',
            'created_at' => '2024-01-05 09:15:00'
        ],
        4 => [
            'id' => 4,
            'title' => 'Kibertəhlükəsizlik: Web Təhlükəsizliyi və Ən Yaxşı Təcrübələr',
            'slug' => 'kibertehlukesizlik-web-tehlukesizliyi-ve-en-yaxsi-tecrubeler',
            'content' => '<p>Kibertəhlükəsizlik, web saytlarının və rəqəmsal məlumatların təhlükəsizliyini təmin etmək üçün vacib sahədir. Bu məqalədə, web təhlükəsizliyinin əsas prinsiplərini və ən yaxşı təcrübələrini araşdıracağıq. 2024-cü ildə kibertəhlükələr artır və business-lər üçün təhlükəsizlik prioritet olur.</p>
            
            <h2>Web Security Threats və Təhlükələr</h2>
            <p>Web saytları üçün əsas təhlükələr və cyber attack növləri:</p>
            <ul>
                <li><strong>SQL Injection:</strong> Database hücumları, data breach və unauthorized access</li>
                <li><strong>Cross-Site Scripting (XSS):</strong> Client-side kod injection, user data theft və session hijacking</li>
                <li><strong>Cross-Site Request Forgery (CSRF):</strong> Unauthorized actions, account takeover və data manipulation</li>
                <li><strong>DDoS Attacks:</strong> Service disruption, website downtime və business continuity issues</li>
                <li><strong>Brute Force:</strong> Password cracking attempts, account compromise və unauthorized access</li>
                <li><strong>Phishing:</strong> Social engineering attacks, credential theft və financial fraud</li>
                <li><strong>Malware:</strong> Malicious software distribution, system compromise və data exfiltration</li>
                <li><strong>Ransomware:</strong> Data encryption, ransom demands və business disruption</li>
                <li><strong>Man-in-the-Middle (MitM):</strong> Data interception, communication compromise və privacy violations</li>
                <li><strong>Insider Threats:</strong> Internal security breaches, data theft və privilege abuse</li>
            </ul>
            
            <h3>OWASP Top 10 2023</h3>
            <p>OWASP Top 10 web application security risks və ən çox yayılmış vulnerabilities:</p>
            <ul>
                <li><strong>A01: Broken Access Control:</strong> Authorization vulnerabilities, privilege escalation və unauthorized data access</li>
                <li><strong>A02: Cryptographic Failures:</strong> Weak encryption, sensitive data exposure və cryptographic vulnerabilities</li>
                <li><strong>A03: Injection:</strong> Code injection attacks, SQL injection, NoSQL injection və LDAP injection</li>
                <li><strong>A04: Insecure Design:</strong> Design flaws, architectural weaknesses və security by design failures</li>
                <li><strong>A05: Security Misconfiguration:</strong> Improper configuration, default settings və missing security controls</li>
                <li><strong>A06: Vulnerable Components:</strong> Outdated dependencies, known vulnerabilities və supply chain risks</li>
                <li><strong>A07: Authentication Failures:</strong> Weak authentication, broken authentication mechanisms və credential management</li>
                <li><strong>A08: Software Integrity:</strong> Supply chain attacks, malicious code injection və integrity failures</li>
                <li><strong>A09: Logging Failures:</strong> Insufficient logging, security monitoring gaps və incident detection issues</li>
                <li><strong>A10: Server-Side Request Forgery:</strong> SSRF attacks, internal network access və service enumeration</li>
            </ul>
            
            <h2>Security Implementation və Best Practices</h2>
            <p>Web security implementation strategies və comprehensive security approach:</p>
            <ul>
                <li><strong>HTTPS Everywhere:</strong> SSL/TLS encryption, secure data transmission və certificate management</li>
                <li><strong>Content Security Policy (CSP):</strong> XSS prevention, resource loading control və script execution policies</li>
                <li><strong>Input Validation:</strong> Data sanitization, input filtering və validation rules</li>
                <li><strong>Output Encoding:</strong> XSS protection, data encoding və safe output rendering</li>
                <li><strong>Authentication:</strong> Multi-factor authentication, strong passwords və secure login mechanisms</li>
                <li><strong>Authorization:</strong> Role-based access control, permission management və access policies</li>
                <li><strong>Session Management:</strong> Secure session handling, session timeout və session security</li>
                <li><strong>Error Handling:</strong> Secure error messages, information disclosure prevention və debugging controls</li>
            </ul>
            
            <h3>Authentication Security və Access Control</h3>
            <p>Secure authentication practices və user access management:</p>
            <ul>
                <li><strong>Password Policies:</strong> Strong password requirements, complexity rules və password expiration</li>
                <li><strong>Multi-Factor Authentication:</strong> Additional security layer, SMS, email və authenticator apps</li>
                <li><strong>Account Lockout:</strong> Brute force protection, failed attempt limits və temporary account suspension</li>
                <li><strong>Password Hashing:</strong> bcrypt, Argon2 algorithms, salt usage və secure password storage</li>
                <li><strong>Session Timeout:</strong> Automatic session expiration, idle timeout və security policies</li>
                <li><strong>CAPTCHA:</strong> Bot protection, human verification və automated attack prevention</li>
                <li><strong>Single Sign-On (SSO):</strong> Centralized authentication, reduced password fatigue və improved security</li>
                <li><strong>Biometric Authentication:</strong> Fingerprint, face recognition və behavioral biometrics</li>
            </ul>
            
            <h2>Data Protection və Privacy</h2>
            <p>Data protection strategies və privacy compliance:</p>
            <ul>
                <li><strong>Encryption:</strong> Data at rest və in transit, end-to-end encryption və key management</li>
                <li><strong>Data Masking:</strong> Sensitive data protection, PII anonymization və data obfuscation</li>
                <li><strong>Backup Security:</strong> Encrypted backups, secure storage və recovery procedures</li>
                <li><strong>Data Retention:</strong> GDPR compliance, data lifecycle management və retention policies</li>
                <li><strong>Privacy by Design:</strong> Built-in privacy protection, data minimization və privacy-first approach</li>
                <li><strong>Data Classification:</strong> Data categorization, sensitivity levels və handling procedures</li>
                <li><strong>Data Loss Prevention (DLP):</strong> Data leakage prevention, monitoring və policy enforcement</li>
            </ul>
            
            <h3>GDPR Compliance və Data Privacy</h3>
            <p>General Data Protection Regulation requirements və privacy compliance:</p>
            <ul>
                <li><strong>Data Minimization:</strong> Collect only necessary data, purpose limitation və data reduction</li>
                <li><strong>Consent Management:</strong> Clear consent mechanisms, opt-in processes və consent withdrawal</li>
                <li><strong>Right to Access:</strong> Data portability, subject access requests və data transparency</li>
                <li><strong>Right to Erasure:</strong> Data deletion, right to be forgotten və data removal procedures</li>
                <li><strong>Data Protection Officer:</strong> DPO appointment, privacy expertise və compliance oversight</li>
                <li><strong>Privacy Impact Assessment:</strong> PIA requirements, risk assessment və mitigation strategies</li>
                <li><strong>Data Breach Notification:</strong> Incident reporting, notification timelines və regulatory compliance</li>
                <li><strong>Privacy Policies:</strong> Clear privacy notices, user rights explanation və legal compliance</li>
            </ul>
            
            <h2>Security Testing və Vulnerability Assessment</h2>
            <p>Security testing methodologies və comprehensive security evaluation:</p>
            <ul>
                <li><strong>Penetration Testing:</strong> Ethical hacking, vulnerability assessment və security validation</li>
                <li><strong>Vulnerability Scanning:</strong> Automated security scans, vulnerability detection və risk assessment</li>
                <li><strong>Code Review:</strong> Security code analysis, manual code inspection və static analysis</li>
                <li><strong>Static Application Security Testing (SAST):</strong> Source code analysis, vulnerability detection və code quality</li>
                <li><strong>Dynamic Application Security Testing (DAST):</strong> Runtime testing, black-box testing və application scanning</li>
                <li><strong>Interactive Application Security Testing (IAST):</strong> Hybrid approach, real-time analysis və comprehensive coverage</li>
                <li><strong>Dependency Scanning:</strong> Third-party vulnerability assessment, supply chain security və component analysis</li>
                <li><strong>Infrastructure Testing:</strong> Network security testing, server hardening və configuration assessment</li>
            </ul>
            
            <h3>Security Tools və Automation</h3>
            <p>Popular security testing tools və automated security solutions:</p>
            <ul>
                <li><strong>OWASP ZAP:</strong> Web application security scanner, automated testing və vulnerability detection</li>
                <li><strong>Burp Suite:</strong> Web vulnerability scanner, manual testing və security assessment</li>
                <li><strong>Nmap:</strong> Network security scanner, port scanning və service enumeration</li>
                <li><strong>Nessus:</strong> Vulnerability assessment, compliance scanning və security monitoring</li>
                <li><strong>Metasploit:</strong> Penetration testing framework, exploit development və security validation</li>
                <li><strong>SonarQube:</strong> Code quality və security, static analysis və continuous inspection</li>
                <li><strong>Veracode:</strong> Application security platform, SAST/DAST testing və security analytics</li>
                <li><strong>Checkmarx:</strong> Static application security testing, code analysis və vulnerability management</li>
            </ul>
            
            <h2>Incident Response və Security Operations</h2>
            <p>Security incident response plan və cyber incident management:</p>
            <ul>
                <li><strong>Preparation:</strong> Incident response team, procedures, tools və training</li>
                <li><strong>Identification:</strong> Threat detection, incident classification və severity assessment</li>
                <li><strong>Containment:</strong> Threat isolation, system quarantine və damage limitation</li>
                <li><strong>Eradication:</strong> Threat removal, vulnerability patching və system cleaning</li>
                <li><strong>Recovery:</strong> System restoration, service resumption və business continuity</li>
                <li><strong>Lessons Learned:</strong> Post-incident analysis, improvement planning və process enhancement</li>
                <li><strong>Communication:</strong> Stakeholder notification, public relations və regulatory reporting</li>
                <li><strong>Documentation:</strong> Incident logging, evidence preservation və legal compliance</li>
            </ul>
            
            <h2>Security Monitoring və Threat Detection</h2>
            <p>Continuous security monitoring və proactive threat management:</p>
            <ul>
                <li><strong>SIEM Systems:</strong> Security information management, log aggregation və event correlation</li>
                <li><strong>Log Analysis:</strong> Security event monitoring, pattern detection və anomaly identification</li>
                <li><strong>Threat Intelligence:</strong> Proactive threat detection, threat feeds və intelligence sharing</li>
                <li><strong>Behavioral Analytics:</strong> Anomaly detection, user behavior analysis və machine learning</li>
                <li><strong>Real-time Alerts:</strong> Immediate threat notification, automated response və escalation procedures</li>
                <li><strong>Network Monitoring:</strong> Traffic analysis, intrusion detection və network security</li>
                <li><strong>Endpoint Detection:</strong> Endpoint security, malware detection və device monitoring</li>
                <li><strong>Cloud Security Monitoring:</strong> Cloud infrastructure security, compliance monitoring və threat detection</li>
            </ul>
            
            <h2>Cloud Security və Infrastructure</h2>
            <p>Cloud security considerations və infrastructure protection:</p>
            <ul>
                <li><strong>Shared Responsibility Model:</strong> Cloud provider vs customer responsibilities, security boundaries</li>
                <li><strong>Identity and Access Management:</strong> IAM policies, role-based access və privilege management</li>
                <li><strong>Data Encryption:</strong> Cloud data protection, encryption at rest və in transit</li>
                <li><strong>Network Security:</strong> VPC configuration, firewalls, security groups və network segmentation</li>
                <li><strong>Compliance:</strong> Cloud compliance frameworks, regulatory requirements və audit standards</li>
                <li><strong>Container Security:</strong> Docker security, Kubernetes security və container orchestration</li>
                <li><strong>Serverless Security:</strong> Function security, API security və serverless architecture protection</li>
                <li><strong>Cloud Backup Security:</strong> Backup encryption, secure storage və recovery procedures</li>
            </ul>
            
            <h2>Mobile Security və Application Protection</h2>
            <p>Mobile application security və device protection:</p>
            <ul>
                <li><strong>App Store Security:</strong> Official app distribution, code signing və app validation</li>
                <li><strong>Code Obfuscation:</strong> Reverse engineering protection, code protection və intellectual property</li>
                <li><strong>Certificate Pinning:</strong> SSL/TLS security, certificate validation və MITM prevention</li>
                <li><strong>Biometric Authentication:</strong> Fingerprint, face recognition, voice recognition və behavioral biometrics</li>
                <li><strong>App Sandboxing:</strong> Isolated execution environment, permission management və access control</li>
                <li><strong>Mobile Device Management:</strong> MDM solutions, device compliance və remote management</li>
                <li><strong>API Security:</strong> Mobile API protection, authentication və rate limiting</li>
                <li><strong>Data Protection:</strong> Mobile data encryption, secure storage və data transmission</li>
            </ul>
            
            <h2>IoT Security və Connected Devices</h2>
            <p>Internet of Things security və connected device protection:</p>
            <ul>
                <li><strong>Device Authentication:</strong> IoT device identity, secure boot və device certificates</li>
                <li><strong>Network Security:</strong> IoT network protection, segmentation və traffic monitoring</li>
                <li><strong>Data Encryption:</strong> IoT data protection, secure communication və data integrity</li>
                <li><strong>Firmware Security:</strong> Secure firmware updates, code signing və vulnerability management</li>
                <li><strong>Physical Security:</strong> Device tampering protection, secure hardware və physical access control</li>
                <li><strong>Privacy Protection:</strong> IoT privacy, data minimization və user consent</li>
                <li><strong>Lifecycle Management:</strong> Device provisioning, management və decommissioning</li>
            </ul>
            
            <h2>Future Security Trends və Emerging Technologies</h2>
            <p>Emerging security technologies və future security landscape:</p>
            <ul>
                <li><strong>Zero Trust Architecture:</strong> Never trust, always verify, micro-segmentation və identity-based security</li>
                <li><strong>AI-powered Security:</strong> Machine learning threat detection, behavioral analysis və automated response</li>
                <li><strong>Quantum Cryptography:</strong> Post-quantum security, quantum-resistant algorithms və future-proof encryption</li>
                <li><strong>Blockchain Security:</strong> Decentralized security models, smart contract security və distributed trust</li>
                <li><strong>IoT Security:</strong> Internet of Things protection, connected device security və edge computing</li>
                <li><strong>5G Security:</strong> Next-generation network security, edge security və mobile security evolution</li>
                <li><strong>Extended Detection and Response (XDR):</strong> Unified security platform, cross-domain detection və automated response</li>
                <li><strong>Security Orchestration:</strong> SOAR platforms, automated workflows və security process automation</li>
            </ul>
            
            <h2>Security Governance və Risk Management</h2>
            <p>Security governance, risk management və compliance:</p>
            <ul>
                <li><strong>Security Policies:</strong> Security governance, policy development və compliance management</li>
                <li><strong>Risk Assessment:</strong> Security risk analysis, threat modeling və vulnerability assessment</li>
                <li><strong>Compliance Management:</strong> Regulatory compliance, audit preparation və compliance monitoring</li>
                <li><strong>Security Awareness:</strong> Employee training, security education və culture development</li>
                <li><strong>Vendor Management:</strong> Third-party security, supplier risk management və vendor assessment</li>
                <li><strong>Business Continuity:</strong> Disaster recovery, business continuity planning və incident preparedness</li>
                <li><strong>Security Metrics:</strong> KPI development, security measurement və performance tracking</li>
            </ul>
            
            <h2>Conclusion</h2>
            <p>Kibertəhlükəsizlik davamlı prosesdir və daim yenilənməlidir. Düzgün təhlükəsizlik təcrübələri və alətlər ilə veb saytlarınızı və məlumatlarınızı qoruyub saxlaya bilərsiniz. 2024-cü ildə kibertəhlükələr artır və bizneslər üçün hərtərəfli təhlükəsizlik yanaşması vacibdir.</p>
            
            <p>Uğurlu kibertəhlükəsizlik strategiyası üçün dərin müdafiə yanaşması, davamlı monitorinq, işçi təlimi və texnologiya investisiyası vacibdir. Düzgün təhlükəsizlik tədbirləri ilə bizneslərinizi kibertəhlükələrdən qoruyub, müştəri etimadı və qanuni uyğunluq təmin edə bilərsiniz.</p>',
            'excerpt' => 'Veb təhlükəsizliyinin hərtərəfli təlimatı: təhlükələr, OWASP Top 10, təhlükəsizlik tətbiqi və ən yaxşı təcrübələr. Kibertəhlükəsizlik sahəsində vacib prinsiplər və müasir təhlükəsizlik trendləri.',
            'featured_image' => 'images/blog/cybersecurity-guide.jpg',
            'category_name' => 'Kibertəhlükəsizlik',
            'author' => 'NextCode Security Team',
            'read_time' => '20',
            'created_at' => '2024-01-01 16:45:00'
        ]
    ];
    
    $current_post = $fallback_posts[$post_id] ?? null;
}

if (!$current_post) {
    safeRedirect('blog.php');
}

$formatted_date = date('d F Y', strtotime($current_post['created_at'] ?? 'now'));

// Dynamic content variables
$page_title = ($current_post['title'] ?? 'Başlıq tapılmadı') . ' - NextCode Group Blog';
$meta_description = $current_post['excerpt'] ?? 'Məzmun tapılmadı';
$meta_keywords = $current_post['tags'] ?? 'NextCode, Blog, Digital Marketing';
$current_page = 'blog';

// Social media meta data
$og_image = 'https://nextcodegroup.ostwind.az/' . ($current_post['featured_image'] ?? 'images/blog/blog-1.jpg');
$og_title = $current_post['title'] ?? 'Başlıq tapılmadı';
$og_description = $current_post['excerpt'] ?? 'Məzmun tapılmadı';

// Include header
require_once 'includes/header.php';
?>

<style>
/* Blog post stilleri */
.post-featured-image {
    margin: 20px 0;
    text-align: center;
}

.post-featured-image img {
    width: 100%;
    height: auto;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
}

.author-info {
    display: flex;
    align-items: center;
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px;
}

.author-name {
    font-weight: bold;
    display: block;
}

.author-title {
    color: #666;
    font-size: 14px;
}

/* Blog post content styles */
.post-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
}

.post-content h1,
.post-content h2,
.post-content h3,
.post-content h4,
.post-content h5,
.post-content h6 {
    color: #2c3e50;
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.post-content p {
    margin-bottom: 1.5rem;
    color: #444;
}

.post-content ul,
.post-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.post-content li {
    margin-bottom: 0.5rem;
    color: #444;
}

.post-content strong {
    color: #2c3e50;
    font-weight: 600;
}

.post-content a {
    color: #3498db;
    text-decoration: none;
    border-bottom: 1px solid transparent;
    transition: all 0.3s ease;
}

.post-content a:hover {
    color: #2980b9;
    border-bottom-color: #2980b9;
}

.post-content blockquote {
    background: #f8f9fa;
    border-left: 4px solid #3498db;
    padding: 1rem 1.5rem;
    margin: 1.5rem 0;
    border-radius: 0 8px 8px 0;
    font-style: italic;
}

.post-content code {
    background: #f1f3f4;
    color: #e83e8c;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, monospace;
    font-size: 0.9em;
}

.post-content pre {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    overflow-x: auto;
    margin: 1.5rem 0;
    border: 1px solid #e9ecef;
}

.post-content pre code {
    background: transparent;
    padding: 0;
    color: #333;
}

/* Post meta styles */
.post-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.category-badge {
    background: #3498db;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.post-date,
.read-time {
    color: #6c757d;
    font-size: 0.875rem;
}

.post-date i,
.read-time i {
    color: #3498db;
    margin-right: 0.5rem;
}

/* Share buttons */
.share-buttons {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 12px;
    margin-top: 2rem;
    border: 1px solid #e9ecef;
}

.share-buttons h4 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.share-links {
    display: flex;
    gap: 1rem;
}

.share-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s ease;
    color: white;
}

.share-link.facebook { background: #3b5998; }
.share-link.twitter { background: #1da1f2; }
.share-link.linkedin { background: #0077b5; }
.share-link.whatsapp { background: #25d366; }

.share-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Newsletter */
.newsletter {
    background: #f8f9fa;
    padding: 3rem 0;
    margin-top: 3rem;
    text-align: center;
}

.newsletter h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.newsletter p {
    color: #6c757d;
    margin-bottom: 2rem;
}

.newsletter-form {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.newsletter-input {
    padding: 0.75rem 1rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    width: 300px;
    font-size: 1rem;
}

.newsletter-input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.newsletter-btn {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.newsletter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
}

/* Related posts */
.related-posts {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 12px;
    margin-top: 3rem;
    border: 1px solid #e9ecef;
}

.related-posts h4 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.related-post-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.3s ease;
    height: 100%;
}

.related-post-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: #3498db;
}

.related-post-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.related-post-card h5 {
    margin-bottom: 0.5rem;
}

.related-post-card h5 a {
    color: #2c3e50;
    text-decoration: none;
}

.related-post-card h5 a:hover {
    color: #3498db;
}

.related-post-card p {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .newsletter-form {
        flex-direction: column;
    }
    
    .newsletter-input {
        width: 100%;
    }
    
    .share-links {
        justify-content: center;
    }
    
    .post-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<!-- Dark mode CSS -->
<link rel="stylesheet" href="css/blog-dark-mode.css">
<!-- Blog Post Header -->
<section class="blog-post-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="post-meta mb-3">
                    <span class="category-badge"><?php echo htmlspecialchars($current_post['category_name'] ?? 'Blog'); ?></span>
                    <span class="post-date"><i class="fas fa-calendar"></i> <?php echo htmlspecialchars($formatted_date); ?></span>
                    <span class="read-time"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($current_post['read_time'] ?? '5'); ?> dəq</span>
                </div>
                <h1 class="post-title"><?php echo htmlspecialchars($current_post['title'] ?? 'Başlıq tapılmadı'); ?></h1>
                
                <!-- Featured Image -->
                <div class="post-featured-image mb-4">
                    <img src="<?php echo htmlspecialchars($current_post['featured_image'] ?? 'images/blog/blog-1.jpg'); ?>" alt="<?php echo htmlspecialchars($current_post['title'] ?? 'Başlıq tapılmadı'); ?>" class="img-fluid" onerror="this.src='images/blog/blog-1.jpg'">
                </div>
                
                <div class="author-info">
                    <img src="images/author-avatar.jpg" alt="<?php echo htmlspecialchars($current_post['author'] ?? 'Müəllif'); ?>" class="author-avatar">
                    <div>
                        <span class="author-name"><?php echo htmlspecialchars($current_post['author'] ?? 'Müəllif'); ?></span>
                        <span class="author-title">Digital Marketing Mütəxəssisi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Post Content -->
<section class="blog-post-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="post-content">
                    <?php echo $current_post['content'] ?? '<p>Məzmun tapılmadı.</p>'; ?>
                </div>
                
                <!-- Share buttons -->
                <div class="share-buttons mt-5">
                    <h4>Bu məqaləni paylaşın:</h4>
                    <div class="share-links">
                        <a href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-link facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($current_post['title'] ?? 'Başlıq tapılmadı'); ?>" target="_blank" class="share-link twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-link linkedin">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode(($current_post['title'] ?? 'Başlıq tapılmadı') . ' - ' . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-link whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Related posts -->
                <div class="related-posts mt-5">
                    <h4>Əlaqəli məqalələr:</h4>
                    <div class="row">
                        <?php
                        // Ensure $fallback_posts is defined and is an array
                        if (!isset($fallback_posts) || !is_array($fallback_posts)) {
                            $fallback_posts = [];
                        }
                        
                        $related_posts = array_filter($fallback_posts, function($post) use ($current_post) {
                            return $post['id'] != $current_post['id'] && $post['category_name'] == $current_post['category_name'];
                        });
                        
                        $related_posts = array_slice($related_posts, 0, 3);
                        
                        foreach ($related_posts as $post) {
                            echo '<div class="col-md-4 mb-3">';
                            echo '<div class="related-post-card">';
                            echo '<img src="' . htmlspecialchars($post['featured_image'] ?? 'images/blog/blog-1.jpg') . '" alt="' . htmlspecialchars($post['title'] ?? 'Başlıq tapılmadı') . '" class="img-fluid">';
                            echo '<h5><a href="blog-post.php?id=' . ($post['id'] ?? '1') . '">' . htmlspecialchars($post['title'] ?? 'Başlıq tapılmadı') . '</a></h5>';
                            echo '<p>' . htmlspecialchars(substr($post['excerpt'] ?? 'Məzmun tapılmadı', 0, 100)) . '...</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="newsletter">
    <div class="container">
        <h3>Ən Son Məqalələrdən Xəbərdar Olun</h3>
        <p>Digital marketing sahəsindəki son yeniliklər və xüsusi təkliflərdən xəbərdar olmaq üçün abunə olun.</p>
        <form class="newsletter-form">
            <input type="email" class="newsletter-input" placeholder="E-mail ünvanınız" required>
            <button type="submit" class="newsletter-btn">Abunə Ol</button>
        </form>
    </div>
</section>

<?php
// Include footer
require_once 'includes/footer.php';
?>
