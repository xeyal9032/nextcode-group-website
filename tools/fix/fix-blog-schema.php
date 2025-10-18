<?php
// Blog veritabanı şemasını düzelt
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

echo "<h1>Blog Veritabanı Şeması Düzeltme</h1>";

try {
    require_once 'config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        echo "<p style='color: red;'>✗ Veritabanı bağlantısı başarısız!</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✓ Veritabanı bağlantısı başarılı</p>";
    
    // Mevcut blog_posts tablosunun yapısını kontrol et
    echo "<h2>Mevcut Tablo Yapısı:</h2>";
    $stmt = $pdo->query("DESCRIBE blog_posts");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Kolon</th><th>Tip</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Eksik kolonları ekle
    echo "<h2>Eksik Kolonları Ekleme:</h2>";
    
    $required_columns = [
        'author' => 'VARCHAR(255) DEFAULT "NextCode Team"',
        'read_time' => 'INT DEFAULT 5',
        'slug' => 'VARCHAR(255)',
        'excerpt' => 'TEXT',
        'featured_image' => 'VARCHAR(500)',
        'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ];
    
    foreach ($required_columns as $column => $definition) {
        // Kolonun var olup olmadığını kontrol et
        $column_exists = false;
        foreach ($columns as $col) {
            if ($col['Field'] === $column) {
                $column_exists = true;
                break;
            }
        }
        
        if (!$column_exists) {
            try {
                $sql = "ALTER TABLE blog_posts ADD COLUMN `$column` $definition";
                $pdo->exec($sql);
                echo "<p style='color: green;'>✓ Kolon eklendi: $column</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>✗ Kolon eklenemedi: $column - " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: blue;'>→ Kolon zaten mevcut: $column</p>";
        }
    }
    
    // Şimdi blog verilerini güncelle
    echo "<h2>Blog Verilerini Güncelleme:</h2>";
    
    $updated_posts = [
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
            'author' => 'NextCode Development Team',
            'read_time' => '15',
            'status' => 'published'
        ]
    ];
    
    // Blog verilerini güncelle
    foreach ($updated_posts as $id => $post_data) {
        // Önce hangi kolonların mevcut olduğunu kontrol et
        $stmt = $pdo->query("DESCRIBE blog_posts");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $update_fields = [];
        $update_values = [];
        
        // Mevcut kolonları kontrol et ve sadece mevcut olanları güncelle
        if (in_array('title', $columns)) {
            $update_fields[] = 'title = ?';
            $update_values[] = $post_data['title'];
        }
        
        if (in_array('slug', $columns)) {
            $update_fields[] = 'slug = ?';
            $update_values[] = $post_data['slug'];
        }
        
        if (in_array('content', $columns)) {
            $update_fields[] = 'content = ?';
            $update_values[] = $post_data['content'];
        }
        
        if (in_array('excerpt', $columns)) {
            $update_fields[] = 'excerpt = ?';
            $update_values[] = $post_data['excerpt'];
        }
        
        if (in_array('featured_image', $columns)) {
            $update_fields[] = 'featured_image = ?';
            $update_values[] = $post_data['featured_image'];
        }
        
        if (in_array('author', $columns)) {
            $update_fields[] = 'author = ?';
            $update_values[] = $post_data['author'];
        }
        
        if (in_array('read_time', $columns)) {
            $update_fields[] = 'read_time = ?';
            $update_values[] = $post_data['read_time'];
        }
        
        if (in_array('updated_at', $columns)) {
            $update_fields[] = 'updated_at = NOW()';
        }
        
        if (!empty($update_fields)) {
            $update_values[] = $id;
            
            $sql = "UPDATE blog_posts SET " . implode(', ', $update_fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($update_values);
            
            if ($result) {
                echo "<p style='color: green;'>✓ Blog post ID $id güncellendi: " . $post_data['title'] . "</p>";
                echo "<p>   - Güncellenen kolonlar: " . implode(', ', array_map(function($field) { return str_replace(' = ?', '', $field); }, $update_fields)) . "</p>";
            } else {
                echo "<p style='color: red;'>✗ Blog post ID $id güncellenemedi</p>";
            }
        } else {
            echo "<p style='color: orange;'>→ Blog post ID $id için güncellenecek kolon bulunamadı</p>";
        }
    }
    
    echo "<h2>✅ İşlem Tamamlandı!</h2>";
    echo "<p><strong>Şimdi test edin:</strong></p>";
    echo "<p><a href='blog-post.php?id=2' target='_blank'>Blog Post Sayfasını Aç</a></p>";
    echo "<p><a href='blog.php' target='_blank'>Blog Sayfasını Aç</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Hata: " . $e->getMessage() . "</p>";
}
?>


