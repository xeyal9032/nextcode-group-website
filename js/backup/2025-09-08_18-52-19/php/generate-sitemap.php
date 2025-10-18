<?php
/**
 * Sitemap Generator for NextCode Group
 * Automatically generates XML sitemap for all pages
 */

// Set content type to XML
header('Content-Type: application/xml; charset=utf-8');

// Get base URL
$base_url = 'https://' . $_SERVER['HTTP_HOST'];
$current_time = date('c');

// Define main pages with priority and change frequency
$pages = [
    'index.php' => ['priority' => '1.0', 'changefreq' => 'daily'],
    'about.php' => ['priority' => '0.8', 'changefreq' => 'weekly'],
    'services.php' => ['priority' => '0.9', 'changefreq' => 'weekly'],
    'portfolio.php' => ['priority' => '0.8', 'changefreq' => 'weekly'],
    'blog.php' => ['priority' => '0.7', 'changefreq' => 'daily'],
    'pricing.php' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'contact.php' => ['priority' => '0.9', 'changefreq' => 'monthly'],
    'faq.php' => ['priority' => '0.6', 'changefreq' => 'monthly']
];

// Get blog posts from database
$blog_posts = [];
try {
    $db_file = __DIR__ . '/database/nextcode.db';
    if (file_exists($db_file)) {
        $dsn = "sqlite:$db_file";
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        $stmt = $pdo->query("SELECT id, title, updated_at FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC");
        $blog_posts = $stmt->fetchAll();
    }
} catch (Exception $e) {
    // Database error, continue without blog posts
}

// Get portfolio projects from database
$portfolio_projects = [];
try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT id, title, updated_at FROM portfolio_projects WHERE status = 'published' ORDER BY created_at DESC");
        $portfolio_projects = $stmt->fetchAll();
    }
} catch (Exception $e) {
    // Database error, continue without portfolio projects
}

// Start XML output
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

// Add main pages
foreach ($pages as $page => $settings) {
    $url = $base_url . '/' . $page;
    $lastmod = file_exists($page) ? date('c', filemtime($page)) : $current_time;
    
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($url) . "</loc>\n";
    echo "    <lastmod>" . $lastmod . "</lastmod>\n";
    echo "    <changefreq>" . $settings['changefreq'] . "</changefreq>\n";
    echo "    <priority>" . $settings['priority'] . "</priority>\n";
    
    // Add hreflang for Azerbaijani
    echo "    <xhtml:link rel=\"alternate\" hreflang=\"az\" href=\"" . htmlspecialchars($url) . "\" />\n";
    echo "    <xhtml:link rel=\"alternate\" hreflang=\"en\" href=\"" . htmlspecialchars($url) . "\" />\n";
    
    echo "  </url>\n";
}

// Add blog posts
foreach ($blog_posts as $post) {
    $url = $base_url . '/blog-detail.php?id=' . $post['id'];
    $lastmod = $post['updated_at'] ?: $current_time;
    
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($url) . "</loc>\n";
    echo "    <lastmod>" . $lastmod . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.6</priority>\n";
    echo "  </url>\n";
}

// Add portfolio projects
foreach ($portfolio_projects as $project) {
    $url = $base_url . '/portfolio-detail.php?id=' . $project['id'];
    $lastmod = $project['updated_at'] ?: $current_time;
    
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($url) . "</loc>\n";
    echo "    <lastmod>" . $lastmod . "</lastmod>\n";
    echo "    <changefreq>monthly</changefreq>\n";
    echo "    <priority>0.5</priority>\n";
    echo "  </url>\n";
}

echo "</urlset>";
?>
