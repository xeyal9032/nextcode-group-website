<?php
/**
 * Asset Optimizer
 * CSS ve JavaScript dosyalarını optimize eder ve minify eder
 */

class AssetOptimizer {
    private $cssPath;
    private $jsPath;
    private $outputPath;
    private $version;
    
    public function __construct($cssPath = 'css/', $jsPath = 'js/', $outputPath = 'assets/optimized/') {
        $this->cssPath = rtrim($cssPath, '/') . '/';
        $this->jsPath = rtrim($jsPath, '/') . '/';
        $this->outputPath = rtrim($outputPath, '/') . '/';
        $this->version = date('YmdHis'); // Cache busting için
        
        // Output klasörünü oluştur
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }
    }
    
    /**
     * CSS dosyalarını optimize et
     */
    public function optimizeCSS($files = []) {
        if (empty($files)) {
            $files = [
                'styles.css',
                'modern-styles.css',
                'responsive.css',
                'animations.css'
            ];
        }
        
        $combinedCSS = '';
        $lastModified = 0;
        
        foreach ($files as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $modified = filemtime($filePath);
                
                if ($modified > $lastModified) {
                    $lastModified = $modified;
                }
                
                // CSS'i optimize et
                $content = $this->minifyCSS($content);
                $combinedCSS .= $content . "\n";
            }
        }
        
        // Optimize edilmiş CSS'i kaydet
        $outputFile = $this->outputPath . 'styles.min.css';
        file_put_contents($outputFile, $combinedCSS);
        
        return [
            'file' => $outputFile,
            'size' => filesize($outputFile),
            'version' => $this->version
        ];
    }
    
    /**
     * JavaScript dosyalarını optimize et
     */
    public function optimizeJS($files = []) {
        if (empty($files)) {
            $files = [
                'main.js',
                'modern-interactions.js',
                'performance-metrics.js'
            ];
        }
        
        $combinedJS = '';
        $lastModified = 0;
        
        foreach ($files as $file) {
            $filePath = $this->jsPath . $file;
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $modified = filemtime($filePath);
                
                if ($modified > $lastModified) {
                    $lastModified = $modified;
                }
                
                // JavaScript'i optimize et
                $content = $this->minifyJS($content);
                $combinedJS .= $content . "\n";
            }
        }
        
        // Optimize edilmiş JS'i kaydet
        $outputFile = $this->outputPath . 'main.min.js';
        file_put_contents($outputFile, $combinedJS);
        
        return [
            'file' => $outputFile,
            'size' => filesize($outputFile),
            'version' => $this->version
        ];
    }
    
    /**
     * CSS minify et
     */
    private function minifyCSS($css) {
        // Yorumları kaldır
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Gereksiz boşlukları kaldır
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*{\s*/', '{', $css);
        $css = preg_replace('/;\s*/', ';', $css);
        $css = preg_replace('/\s*}\s*/', '}', $css);
        $css = preg_replace('/\s*,\s*/', ',', $css);
        $css = preg_replace('/\s*:\s*/', ':', $css);
        
        // Son noktalı virgülü kaldır
        $css = preg_replace('/;}/', '}', $css);
        
        // Başındaki ve sonundaki boşlukları kaldır
        $css = trim($css);
        
        return $css;
    }
    
    /**
     * JavaScript minify et
     */
    private function minifyJS($js) {
        // Tek satır yorumları kaldır
        $js = preg_replace('/\/\/.*$/m', '', $js);
        
        // Çok satırlı yorumları kaldır
        $js = preg_replace('/\/\*.*?\*\//s', '', $js);
        
        // Gereksiz boşlukları kaldır
        $js = preg_replace('/\s+/', ' ', $js);
        $js = preg_replace('/\s*{\s*/', '{', $js);
        $js = preg_replace('/\s*}\s*/', '}', $js);
        $js = preg_replace('/\s*;\s*/', ';', $js);
        $js = preg_replace('/\s*,\s*/', ',', $js);
        $js = preg_replace('/\s*=\s*/', '=', $js);
        $js = preg_replace('/\s*\+\s*/', '+', $js);
        $js = preg_replace('/\s*-\s*/', '-', $js);
        $js = preg_replace('/\s*\*\s*/', '*', $js);
        $js = preg_replace('/\s*\/\s*/', '/', $js);
        
        // Başındaki ve sonundaki boşlukları kaldır
        $js = trim($js);
        
        return $js;
    }
    
    /**
     * Critical CSS oluştur
     */
    public function generateCriticalCSS($htmlContent) {
        $criticalCSS = '';
        
        // Above-the-fold CSS'i tespit et
        $criticalSelectors = [
            'body', 'html', '.container', '.header', '.nav', '.hero',
            '.modern-section', '.modern-container', '.modern-grid',
            '.modern-card', '.modern-btn', '.modern-heading'
        ];
        
        $cssFiles = [
            'styles.css',
            'modern-styles.css',
            'responsive.css'
        ];
        
        foreach ($cssFiles as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                
                // Critical CSS'i çıkar
                foreach ($criticalSelectors as $selector) {
                    $pattern = '/([^{}]*' . preg_quote($selector, '/') . '[^{}]*\{[^}]*\})/i';
                    if (preg_match($pattern, $content, $matches)) {
                        $criticalCSS .= $matches[1] . "\n";
                    }
                }
            }
        }
        
        // Critical CSS'i minify et
        $criticalCSS = $this->minifyCSS($criticalCSS);
        
        // Kaydet
        $outputFile = $this->outputPath . 'critical.css';
        file_put_contents($outputFile, $criticalCSS);
        
        return $outputFile;
    }
    
    /**
     * Asset URL'si oluştur
     */
    public function getAssetUrl($file, $version = null) {
        $version = $version ?: $this->version;
        return $this->outputPath . $file . '?v=' . $version;
    }
    
    /**
     * Tüm asset'leri optimize et
     */
    public function optimizeAll() {
        $results = [];
        
        // CSS optimize et
        $cssResult = $this->optimizeCSS();
        $results['css'] = $cssResult;
        
        // JS optimize et
        $jsResult = $this->optimizeJS();
        $results['js'] = $jsResult;
        
        // Critical CSS oluştur
        $criticalCSS = $this->generateCriticalCSS('');
        $results['critical_css'] = $criticalCSS;
        
        return $results;
    }
    
    /**
     * Optimizasyon raporu oluştur
     */
    public function generateReport() {
        $originalCSSSize = 0;
        $originalJSSize = 0;
        
        // Orijinal dosya boyutlarını hesapla
        $cssFiles = ['styles.css', 'modern-styles.css', 'responsive.css', 'animations.css'];
        foreach ($cssFiles as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath)) {
                $originalCSSSize += filesize($filePath);
            }
        }
        
        $jsFiles = ['main.js', 'modern-interactions.js', 'performance-metrics.js'];
        foreach ($jsFiles as $file) {
            $filePath = $this->jsPath . $file;
            if (file_exists($filePath)) {
                $originalJSSize += filesize($filePath);
            }
        }
        
        // Optimize edilmiş dosya boyutlarını al
        $optimizedCSSFile = $this->outputPath . 'styles.min.css';
        $optimizedJSFile = $this->outputPath . 'main.min.js';
        
        $optimizedCSSSize = file_exists($optimizedCSSFile) ? filesize($optimizedCSSFile) : 0;
        $optimizedJSSize = file_exists($optimizedJSFile) ? filesize($optimizedJSFile) : 0;
        
        return [
            'css' => [
                'original_size' => $originalCSSSize,
                'optimized_size' => $optimizedCSSSize,
                'savings' => $originalCSSSize - $optimizedCSSSize,
                'savings_percent' => $originalCSSSize > 0 ? round((($originalCSSSize - $optimizedCSSSize) / $originalCSSSize) * 100, 2) : 0
            ],
            'js' => [
                'original_size' => $originalJSSize,
                'optimized_size' => $optimizedJSSize,
                'savings' => $originalJSSize - $optimizedJSSize,
                'savings_percent' => $originalJSSize > 0 ? round((($originalJSSize - $optimizedJSSize) / $originalJSSize) * 100, 2) : 0
            ],
            'total_savings' => ($originalCSSSize + $originalJSSize) - ($optimizedCSSSize + $optimizedJSSize),
            'total_savings_percent' => ($originalCSSSize + $originalJSSize) > 0 ? 
                round((($originalCSSSize + $originalJSSize) - ($optimizedCSSSize + $optimizedJSSize)) / ($originalCSSSize + $originalJSSize) * 100, 2) : 0
        ];
    }
}

// Kullanım örneği
if (php_sapi_name() === 'cli') {
    $optimizer = new AssetOptimizer();
    $results = $optimizer->optimizeAll();
    $report = $optimizer->generateReport();
    
    echo "Asset Optimization Completed!\n";
    echo "CSS Savings: " . $report['css']['savings_percent'] . "%\n";
    echo "JS Savings: " . $report['js']['savings_percent'] . "%\n";
    echo "Total Savings: " . $report['total_savings_percent'] . "%\n";
}
?>


