<?php
/**
 * Portfolio Image Optimizer
 * NextCode Group - Portfolio Image Management
 */

// Check if all portfolio images exist and create missing ones
$portfolioImages = [
    'corporate-website.jpg' => 'Modern Kurumsal Web Sitesi',
    'ai-chatbot.jpg' => 'AI Destekli Müşteri Analiz Platformu', 
    'ecommerce-project.jpg' => 'E-Ticaret Platformu',
    'mobile-app.jpg' => 'Mobil Fitness Uygulaması',
    'healthcare-saas.svg' => 'SaaS CRM Sistemi',
    'crypto-tracker.svg' => 'Kripto Para Cüzdan Uygulaması',
    'marketplace.svg' => 'Çok Kanallı E-ticaret Platformu',
    'neural-visualizer.svg' => 'Finansal Analiz Dashboard',
    'smart-city.svg' => 'IoT Akıllı Ev Platformu',
    'project-management.svg' => 'Sosyal Medya Yönetim Platformu',
    'responsive-design.jpg' => 'Restoran Zinciri Rebrand',
    'ai-content-generator.svg' => 'Dijital Pazarlama Kampanyası'
];

$portfolioDir = 'images/portfolio/';

echo "Checking portfolio images...\n";

foreach ($portfolioImages as $filename => $title) {
    $filepath = $portfolioDir . $filename;
    
    if (file_exists($filepath)) {
        echo "✅ $filename exists\n";
    } else {
        echo "❌ $filename missing - creating placeholder\n";
        
        // Create a simple placeholder image
        if (strpos($filename, '.svg') !== false) {
            // Create SVG placeholder
            $svg = createSVGPlaceholder($title);
            file_put_contents($filepath, $svg);
        } else {
            // Create JPG placeholder
            createJPGPlaceholder($filepath, $title);
        }
        
        echo "✅ Created $filename\n";
    }
}

function createSVGPlaceholder($title) {
    $shortTitle = substr($title, 0, 20);
    return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<svg width=\"400\" height=\"300\" xmlns=\"http://www.w3.org/2000/svg\">
  <defs>
    <linearGradient id=\"grad1\" x1=\"0%\" y1=\"0%\" x2=\"100%\" y2=\"100%\">
      <stop offset=\"0%\" style=\"stop-color:#667eea;stop-opacity:1\" />
      <stop offset=\"100%\" style=\"stop-color:#764ba2;stop-opacity:1\" />
    </linearGradient>
  </defs>
  <rect width=\"400\" height=\"300\" fill=\"url(#grad1)\"/>
  <text x=\"200\" y=\"150\" font-family=\"Arial, sans-serif\" font-size=\"18\" font-weight=\"bold\" text-anchor=\"middle\" fill=\"white\">$shortTitle</text>
  <text x=\"200\" y=\"180\" font-family=\"Arial, sans-serif\" font-size=\"14\" text-anchor=\"middle\" fill=\"rgba(255,255,255,0.8)\">Portfolio Project</text>
</svg>";
}

function createJPGPlaceholder($filepath, $title) {
    // Create a simple colored rectangle as placeholder
    $width = 400;
    $height = 300;
    
    $image = imagecreate($width, $height);
    
    // Create gradient background
    $color1 = imagecolorallocate($image, 102, 126, 234); // #667eea
    $color2 = imagecolorallocate($image, 118, 75, 162);  // #764ba2
    $white = imagecolorallocate($image, 255, 255, 255);
    
    // Fill with gradient
    for ($i = 0; $i < $height; $i++) {
        $ratio = $i / $height;
        $r = 102 + ($ratio * (118 - 102));
        $g = 126 + ($ratio * (75 - 126));
        $b = 234 + ($ratio * (162 - 234));
        $color = imagecolorallocate($image, $r, $g, $b);
        imageline($image, 0, $i, $width, $i, $color);
    }
    
    // Add text
    $shortTitle = substr($title, 0, 25);
    imagestring($image, 5, 50, 120, $shortTitle, $white);
    imagestring($image, 3, 50, 150, 'Portfolio Project', $white);
    
    // Save image
    imagejpeg($image, $filepath, 90);
    imagedestroy($image);
}

echo "Portfolio image check completed!\n";
?>

