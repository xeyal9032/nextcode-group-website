<?php
// Blog veritabanını güncelle - Part 2
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Blog Veritabanı Güncelleme - Part 2</h1>";

try {
    require_once 'config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        echo "<p style='color: red;'>✗ Veritabanı bağlantısı başarısız!</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✓ Veritabanı bağlantısı başarılı</p>";
    
    // Kalan blog verileri
    $updated_posts = [
        3 => [
            'title' => 'E-commerce Development: Online Mağaza Yaratmaq',
            'slug' => 'ecommerce-development-guide',
            'content' => '<p>E-commerce development, online mağaza yaratmaq və idarə etmək üçün vacib texniki biliklərdir. Bu məqalədə, uğurlu e-commerce platforması yaratmaq üçün lazım olan bütün aspektləri araşdıracağıq.</p>
            
            <h2>E-commerce Platform Seçimi</h2>
            <p>Düzgün platforma seçimi e-commerce uğurunun əsasını təşkil edir:</p>
            <ul>
                <li><strong>Shopify:</strong> User-friendly və hosted solution</li>
                <li><strong>WooCommerce:</strong> WordPress əsaslı və customizable</li>
                <li><strong>Magento:</strong> Enterprise-level və scalable</li>
                <li><strong>BigCommerce:</strong> Built-in features və performance</li>
                <li><strong>Custom Development:</strong> Tailored solution</li>
            </ul>
            
            <h3>Platform Comparison</h3>
            <p>Platform müqayisəsi:</p>
            <ul>
                <li><strong>Ease of Use:</strong> Shopify > WooCommerce > Magento</li>
                <li><strong>Customization:</strong> Magento > WooCommerce > Shopify</li>
                <li><strong>Performance:</strong> BigCommerce > Shopify > WooCommerce</li>
                <li><strong>Cost:</strong> WooCommerce > Shopify > Magento</li>
            </ul>
            
            <h2>Essential E-commerce Features</h2>
            <p>Hər e-commerce platformasında olmalı əsas funksiyalar:</p>
            <ul>
                <li><strong>Product Management:</strong> Inventory, variants, categories</li>
                <li><strong>Shopping Cart:</strong> Add/remove items, quantity updates</li>
                <li><strong>Checkout Process:</strong> Guest və registered user checkout</li>
                <li><strong>Payment Gateway:</strong> Multiple payment methods</li>
                <li><strong>Order Management:</strong> Order tracking və status updates</li>
                <li><strong>User Accounts:</strong> Registration, login, profile management</li>
                <li><strong>Search Functionality:</strong> Product search və filtering</li>
                <li><strong>Reviews və Ratings:</strong> Customer feedback system</li>
            </ul>
            
            <h3>Advanced Features</h3>
            <p>Advanced e-commerce funksiyaları:</p>
            <ul>
                <li><strong>AI Recommendations:</strong> Personalized product suggestions</li>
                <li><strong>Wishlist:</strong> Save for later functionality</li>
                <li><strong>Multi-language Support:</strong> Internationalization</li>
                <li><strong>Multi-currency:</strong> Global payment support</li>
                <li><strong>Inventory Management:</strong> Stock tracking və alerts</li>
                <li><strong>Analytics Dashboard:</strong> Sales və customer insights</li>
            </ul>
            
            <h2>Payment Gateway Integration</h2>
            <p>Payment gateway seçimi və inteqrasiyası:</p>
            <ul>
                <li><strong>Stripe:</strong> Developer-friendly və global</li>
                <li><strong>PayPal:</strong> Widely accepted və trusted</li>
                <li><strong>Square:</strong> Small business focused</li>
                <li><strong>Authorize.Net:</strong> Enterprise solution</li>
                <li><strong>Local Gateways:</strong> Region-specific options</li>
            </ul>
            
            <h3>Security Considerations</h3>
            <p>Payment security best practices:</p>
            <ul>
                <li><strong>PCI DSS Compliance:</strong> Payment card industry standards</li>
                <li><strong>SSL Certificates:</strong> Encrypted data transmission</li>
                <li><strong>Tokenization:</strong> Secure payment data storage</li>
                <li><strong>Fraud Detection:</strong> Automated fraud prevention</li>
                <li><strong>3D Secure:</strong> Additional authentication layer</li>
            </ul>
            
            <h2>Mobile Commerce (M-commerce)</h2>
            <p>Mobile commerce optimization:</p>
            <ul>
                <li><strong>Responsive Design:</strong> Mobile-first approach</li>
                <li><strong>Progressive Web App:</strong> App-like experience</li>
                <li><strong>Mobile Payment:</strong> Apple Pay, Google Pay</li>
                <li><strong>Touch Optimization:</strong> Finger-friendly interface</li>
                <li><strong>Fast Loading:</strong> Optimized for mobile networks</li>
            </ul>
            
            <h2>SEO for E-commerce</h2>
            <p>E-commerce SEO strategies:</p>
            <ul>
                <li><strong>Product Page Optimization:</strong> Title, description, images</li>
                <li><strong>Category Structure:</strong> Logical hierarchy</li>
                <li><strong>Internal Linking:</strong> Cross-product recommendations</li>
                <li><strong>Schema Markup:</strong> Product structured data</li>
                <li><strong>Site Speed:</strong> Core Web Vitals optimization</li>
            </ul>
            
            <h3>Content Strategy</h3>
            <p>E-commerce content marketing:</p>
            <ul>
                <li><strong>Product Descriptions:</strong> Detailed və compelling</li>
                <li><strong>Blog Content:</strong> Educational və informative</li>
                <li><strong>User-generated Content:</strong> Reviews və testimonials</li>
                <li><strong>Video Content:</strong> Product demonstrations</li>
                <li><strong>Social Proof:</strong> Customer photos və stories</li>
            </ul>
            
            <h2>Performance Optimization</h2>
            <p>E-commerce performance best practices:</p>
            <ul>
                <li><strong>Image Optimization:</strong> WebP format, lazy loading</li>
                <li><strong>CDN Implementation:</strong> Global content delivery</li>
                <li><strong>Caching Strategy:</strong> Page və database caching</li>
                <li><strong>Database Optimization:</strong> Query optimization</li>
                <li><strong>Code Minification:</strong> CSS, JS compression</li>
            </ul>
            
            <h2>Analytics və Tracking</h2>
            <p>E-commerce analytics setup:</p>
            <ul>
                <li><strong>Google Analytics 4:</strong> Enhanced e-commerce tracking</li>
                <li><strong>Conversion Tracking:</strong> Goal və funnel analysis</li>
                <li><strong>Heatmap Tools:</strong> User behavior analysis</li>
                <li><strong>A/B Testing:</strong> Conversion optimization</li>
                <li><strong>Customer Journey:</strong> Multi-touch attribution</li>
            </ul>
            
            <h2>Customer Experience (CX)</h2>
            <p>E-commerce customer experience optimization:</p>
            <ul>
                <li><strong>User Interface:</strong> Intuitive navigation</li>
                <li><strong>Search Functionality:</strong> Smart search və filters</li>
                <li><strong>Personalization:</strong> Tailored recommendations</li>
                <li><strong>Customer Support:</strong> Live chat, FAQ, help center</li>
                <li><strong>Return Policy:</strong> Clear və customer-friendly</li>
            </ul>
            
            <h2>Future Trends</h2>
            <p>E-commerce future trends:</p>
            <ul>
                <li><strong>Voice Commerce:</strong> Voice-activated shopping</li>
                <li><strong>AR/VR Shopping:</strong> Virtual try-on experiences</li>
                <li><strong>AI Chatbots:</strong> Intelligent customer service</li>
                <li><strong>Subscription Commerce:</strong> Recurring revenue models</li>
                <li><strong>Social Commerce:</strong> Shopping on social platforms</li>
            </ul>
            
            <p>E-commerce development mürəkkəb prosesdir və müxtəlif texniki və marketinq aspektləri tələb edir. Düzgün strategiya və implementation ilə uğurlu online mağaza yarada bilərsiniz.</p>',
            'excerpt' => 'E-commerce platforması yaratmaq üçün comprehensive guide: platform seçimi, payment integration, mobile optimization və performance.',
            'featured_image' => 'images/blog/ecommerce-development.jpg',
            'category_id' => 3,
            'author' => 'NextCode E-commerce Team',
            'read_time' => '18',
            'status' => 'published'
        ],
        4 => [
            'title' => 'Cybersecurity: Web Təhlükəsizliyi və Best Practices',
            'slug' => 'cybersecurity-web-security',
            'content' => '<p>Cybersecurity, web saytlarının və digital məlumatların təhlükəsizliyini təmin etmək üçün vacib sahədir. Bu məqalədə, web təhlükəsizliyinin əsas prinsiplərini və best practices-lərini araşdıracağıq.</p>
            
            <h2>Web Security Threats</h2>
            <p>Web saytları üçün əsas təhlükələr:</p>
            <ul>
                <li><strong>SQL Injection:</strong> Database hücumları</li>
                <li><strong>Cross-Site Scripting (XSS):</strong> Client-side kod injection</li>
                <li><strong>Cross-Site Request Forgery (CSRF):</strong> Unauthorized actions</li>
                <li><strong>DDoS Attacks:</strong> Service disruption</li>
                <li><strong>Brute Force:</strong> Password cracking attempts</li>
                <li><strong>Phishing:</strong> Social engineering attacks</li>
                <li><strong>Malware:</strong> Malicious software distribution</li>
            </ul>
            
            <h3>OWASP Top 10</h3>
            <p>OWASP Top 10 web application security risks:</p>
            <ul>
                <li><strong>A01: Broken Access Control:</strong> Authorization vulnerabilities</li>
                <li><strong>A02: Cryptographic Failures:</strong> Weak encryption</li>
                <li><strong>A03: Injection:</strong> Code injection attacks</li>
                <li><strong>A04: Insecure Design:</strong> Design flaws</li>
                <li><strong>A05: Security Misconfiguration:</strong> Improper configuration</li>
                <li><strong>A06: Vulnerable Components:</strong> Outdated dependencies</li>
                <li><strong>A07: Authentication Failures:</strong> Weak authentication</li>
                <li><strong>A08: Software Integrity:</strong> Supply chain attacks</li>
                <li><strong>A09: Logging Failures:</strong> Insufficient logging</li>
                <li><strong>A10: Server-Side Request Forgery:</strong> SSRF attacks</li>
            </ul>
            
            <h2>Security Implementation</h2>
            <p>Web security implementation strategies:</p>
            <ul>
                <li><strong>HTTPS Everywhere:</strong> SSL/TLS encryption</li>
                <li><strong>Content Security Policy (CSP):</strong> XSS prevention</li>
                <li><strong>Input Validation:</strong> Data sanitization</li>
                <li><strong>Output Encoding:</strong> XSS protection</li>
                <li><strong>Authentication:</strong> Multi-factor authentication</li>
                <li><strong>Authorization:</strong> Role-based access control</li>
                <li><strong>Session Management:</strong> Secure session handling</li>
            </ul>
            
            <h3>Authentication Security</h3>
            <p>Secure authentication practices:</p>
            <ul>
                <li><strong>Password Policies:</strong> Strong password requirements</li>
                <li><strong>Multi-Factor Authentication:</strong> Additional security layer</li>
                <li><strong>Account Lockout:</strong> Brute force protection</li>
                <li><strong>Password Hashing:</strong> bcrypt, Argon2 algorithms</li>
                <li><strong>Session Timeout:</strong> Automatic session expiration</li>
                <li><strong>CAPTCHA:</strong> Bot protection</li>
            </ul>
            
            <h2>Data Protection</h2>
            <p>Data protection strategies:</p>
            <ul>
                <li><strong>Encryption:</strong> Data at rest və in transit</li>
                <li><strong>Data Masking:</strong> Sensitive data protection</li>
                <li><strong>Backup Security:</strong> Encrypted backups</li>
                <li><strong>Data Retention:</strong> GDPR compliance</li>
                <li><strong>Privacy by Design:</strong> Built-in privacy protection</li>
            </ul>
            
            <h3>GDPR Compliance</h3>
            <p>General Data Protection Regulation requirements:</p>
            <ul>
                <li><strong>Data Minimization:</strong> Collect only necessary data</li>
                <li><strong>Consent Management:</strong> Clear consent mechanisms</li>
                <li><strong>Right to Access:</strong> Data portability</li>
                <li><strong>Right to Erasure:</strong> Data deletion</li>
                <li><strong>Data Protection Officer:</strong> DPO appointment</li>
                <li><strong>Privacy Impact Assessment:</strong> PIA requirements</li>
            </ul>
            
            <h2>Security Testing</h2>
            <p>Security testing methodologies:</p>
            <ul>
                <li><strong>Penetration Testing:</strong> Ethical hacking</li>
                <li><strong>Vulnerability Scanning:</strong> Automated security scans</li>
                <li><strong>Code Review:</strong> Security code analysis</li>
                <li><strong>Static Application Security Testing (SAST):</strong> Source code analysis</li>
                <li><strong>Dynamic Application Security Testing (DAST):</strong> Runtime testing</li>
                <li><strong>Interactive Application Security Testing (IAST):</strong> Hybrid approach</li>
            </ul>
            
            <h3>Security Tools</h3>
            <p>Popular security testing tools:</p>
            <ul>
                <li><strong>OWASP ZAP:</strong> Web application security scanner</li>
                <li><strong>Burp Suite:</strong> Web vulnerability scanner</li>
                <li><strong>Nmap:</strong> Network security scanner</li>
                <li><strong>Nessus:</strong> Vulnerability assessment</li>
                <li><strong>Metasploit:</strong> Penetration testing framework</li>
                <li><strong>SonarQube:</strong> Code quality və security</li>
            </ul>
            
            <h2>Incident Response</h2>
            <p>Security incident response plan:</p>
            <ul>
                <li><strong>Preparation:</strong> Incident response team</li>
                <li><strong>Identification:</strong> Threat detection</li>
                <li><strong>Containment:</strong> Threat isolation</li>
                <li><strong>Eradication:</strong> Threat removal</li>
                <li><strong>Recovery:</strong> System restoration</li>
                <li><strong>Lessons Learned:</strong> Post-incident analysis</li>
            </ul>
            
            <h2>Security Monitoring</h2>
            <p>Continuous security monitoring:</p>
            <ul>
                <li><strong>SIEM Systems:</strong> Security information management</li>
                <li><strong>Log Analysis:</strong> Security event monitoring</li>
                <li><strong>Threat Intelligence:</strong> Proactive threat detection</li>
                <li><strong>Behavioral Analytics:</strong> Anomaly detection</li>
                <li><strong>Real-time Alerts:</strong> Immediate threat notification</li>
            </ul>
            
            <h2>Cloud Security</h2>
            <p>Cloud security considerations:</p>
            <ul>
                <li><strong>Shared Responsibility Model:</strong> Cloud provider vs customer</li>
                <li><strong>Identity and Access Management:</strong> IAM policies</li>
                <li><strong>Data Encryption:</strong> Cloud data protection</li>
                <li><strong>Network Security:</strong> VPC, firewalls</li>
                <li><strong>Compliance:</strong> Cloud compliance frameworks</li>
            </ul>
            
            <h2>Mobile Security</h2>
            <p>Mobile application security:</p>
            <ul>
                <li><strong>App Store Security:</strong> Official app distribution</li>
                <li><strong>Code Obfuscation:</strong> Reverse engineering protection</li>
                <li><strong>Certificate Pinning:</strong> SSL/TLS security</li>
                <li><strong>Biometric Authentication:</strong> Fingerprint, face recognition</li>
                <li><strong>App Sandboxing:</strong> Isolated execution environment</li>
            </ul>
            
            <h2>Future Security Trends</h2>
            <p>Emerging security technologies:</p>
            <ul>
                <li><strong>Zero Trust Architecture:</strong> Never trust, always verify</li>
                <li><strong>AI-powered Security:</strong> Machine learning threat detection</li>
                <li><strong>Quantum Cryptography:</strong> Post-quantum security</li>
                <li><strong>Blockchain Security:</strong> Decentralized security models</li>
                <li><strong>IoT Security:</strong> Internet of Things protection</li>
            </ul>
            
            <p>Cybersecurity davamlı prosesdir və daim yenilənməlidir. Düzgün security practices və tools ilə web saytlarınızı və məlumatlarınızı qoruyub saxlaya bilərsiniz.</p>',
            'excerpt' => 'Web təhlükəsizliyinin comprehensive guide-ı: threats, OWASP Top 10, security implementation və best practices.',
            'featured_image' => 'images/blog/cybersecurity-guide.jpg',
            'category_id' => 4,
            'author' => 'NextCode Security Team',
            'read_time' => '20',
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
    echo "<p><a href='blog-post.php?id=3'>E-commerce Makalesini Görüntüle</a></p>";
    echo "<p><a href='blog-post.php?id=4'>Cybersecurity Makalesini Görüntüle</a></p>";
    echo "<p><a href='blog.php'>Blog Sayfasına Dön</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Hata: " . $e->getMessage() . "</p>";
}
?>


