<?php
// CSS Minifier ve Critical CSS Generator
// Bu script CSS dosyalarını minify eder ve critical CSS'i ayırır

class CSSOptimizer {
    private $cssDir = 'css/';
    private $outputDir = 'css/optimized/';
    
    public function __construct() {
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    public function minifyCSS($inputFile, $outputFile = null) {
        $css = file_get_contents($inputFile);
        
        // CSS minification
        $css = $this->minify($css);
        
        if (!$outputFile) {
            $outputFile = str_replace('.css', '.min.css', $inputFile);
        }
        
        file_put_contents($outputFile, $css);
        
        $originalSize = filesize($inputFile);
        $minifiedSize = filesize($outputFile);
        $savings = round((($originalSize - $minifiedSize) / $originalSize) * 100, 2);
        
        echo "✓ Minified: " . basename($inputFile) . " ({$originalSize} bytes → {$minifiedSize} bytes, {$savings}% savings)\n";
        
        return $outputFile;
    }
    
    private function minify($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove unnecessary whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/;\s*}/', '}', $css);
        $css = preg_replace('/\s*{\s*/', '{', $css);
        $css = preg_replace('/;\s*/', ';', $css);
        $css = preg_replace('/\s*,\s*/', ',', $css);
        $css = preg_replace('/\s*>\s*/', '>', $css);
        $css = preg_replace('/\s*\+\s*/', '+', $css);
        $css = preg_replace('/\s*~\s*/', '~', $css);
        
        // Remove unnecessary semicolons
        $css = preg_replace('/;}/', '}', $css);
        
        // Remove leading and trailing whitespace
        $css = trim($css);
        
        return $css;
    }
    
    public function generateCriticalCSS() {
        $criticalCSS = '
/* Critical CSS - Above the fold styles */
:root {
    --bg-primary: #ffffff;
    --bg-secondary: #f8f9fa;
    --text-primary: #2c3e50;
    --text-secondary: #7f8c8d;
    --primary-color: #3498db;
    --border-color: #e9ecef;
    --transition-normal: 0.3s ease;
}

[data-theme="dark"] {
    --bg-primary: #0d1117;
    --bg-secondary: #161b22;
    --text-primary: #f0f6fc;
    --text-secondary: #8b949e;
    --border-color: #30363d;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    line-height: 1.7;
    color: var(--text-primary);
    background-color: var(--bg-primary);
    font-weight: 400;
    font-size: 16px;
    transition: background-color var(--transition-normal), color var(--transition-normal);
}

.navbar {
    background-color: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    transition: background-color var(--transition-normal), border-color var(--transition-normal);
}

.hero-section {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.hero-content {
    text-align: center;
    color: white;
    z-index: 2;
    position: relative;
}

.hero-title {
    font-size: clamp(2rem, 8vw, 4rem);
    font-weight: 700;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 16px;
    padding: 14px 28px;
    color: white;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: all var(--transition-normal);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-section {
        min-height: 70vh;
        padding: 1.5rem 0;
    }
    
    .hero-title {
        font-size: clamp(1.6rem, 6vw, 2.5rem);
    }
}
';
        
        file_put_contents($this->outputDir . 'critical.css', $this->minify($criticalCSS));
        echo "✓ Generated critical.css\n";
    }
    
    public function optimizeAllCSS() {
        echo "🚀 Starting CSS Optimization...\n\n";
        
        // Critical CSS'i oluştur
        $this->generateCriticalCSS();
        
        // Ana CSS dosyalarını minify et
        $mainCSSFiles = [
            'styles.css',
            'modern-styles.css',
            'theme-support.css',
            'theme-variables.css',
            'responsive.css',
            'animations.css'
        ];
        
        foreach ($mainCSSFiles as $file) {
            $filePath = $this->cssDir . $file;
            if (file_exists($filePath)) {
                $this->minifyCSS($filePath, $this->outputDir . str_replace('.css', '.min.css', $file));
            }
        }
        
        echo "\n✅ CSS Optimization completed!\n";
    }
}

// Script'i çalıştır
$optimizer = new CSSOptimizer();
$optimizer->optimizeAllCSS();
?>
