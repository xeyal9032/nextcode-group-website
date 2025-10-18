<?php
// Performance Test Script
// Bu script optimizasyon sonuçlarını test eder

class PerformanceTester {
    private $results = [];
    
    public function testCSSOptimization() {
        echo "🔍 Testing CSS Optimization...\n";
        
        $originalSize = 0;
        $optimizedSize = 0;
        
        $cssFiles = [
            'styles.css' => 'optimized/styles.min.css',
            'modern-styles.css' => 'optimized/modern-styles.min.css',
            'theme-support.css' => 'optimized/theme-support.min.css',
            'theme-variables.css' => 'optimized/theme-variables.min.css',
            'responsive.css' => 'optimized/responsive.min.css',
            'animations.css' => 'optimized/animations.min.css'
        ];
        
        foreach ($cssFiles as $original => $optimized) {
            $originalPath = "css/{$original}";
            $optimizedPath = "css/{$optimized}";
            
            if (file_exists($originalPath) && file_exists($optimizedPath)) {
                $originalFileSize = filesize($originalPath);
                $optimizedFileSize = filesize($optimizedPath);
                
                $originalSize += $originalFileSize;
                $optimizedSize += $optimizedFileSize;
                
                $savings = round((($originalFileSize - $optimizedFileSize) / $originalFileSize) * 100, 2);
                
                echo "  ✓ {$original}: " . $this->formatBytes($originalFileSize) . " → " . $this->formatBytes($optimizedFileSize) . " ({$savings}% savings)\n";
            }
        }
        
        $totalSavings = round((($originalSize - $optimizedSize) / $originalSize) * 100, 2);
        echo "  📊 Total CSS: " . $this->formatBytes($originalSize) . " → " . $this->formatBytes($optimizedSize) . " ({$totalSavings}% savings)\n\n";
        
        $this->results['css'] = [
            'original' => $originalSize,
            'optimized' => $optimizedSize,
            'savings' => $totalSavings
        ];
    }
    
    public function testJSOptimization() {
        echo "🔍 Testing JavaScript Optimization...\n";
        
        $originalSize = 0;
        $optimizedSize = 0;
        
        $jsFiles = [
            'main.js' => 'optimized/main.min.js',
            'theme.js' => 'optimized/theme.min.js',
            'portfolio.js' => 'optimized/portfolio.min.js',
            'blog.js' => 'optimized/blog.min.js',
            'services.js' => 'optimized/services.min.js'
        ];
        
        foreach ($jsFiles as $original => $optimized) {
            $originalPath = "js/{$original}";
            $optimizedPath = "js/{$optimized}";
            
            if (file_exists($originalPath) && file_exists($optimizedPath)) {
                $originalFileSize = filesize($originalPath);
                $optimizedFileSize = filesize($optimizedPath);
                
                $originalSize += $originalFileSize;
                $optimizedSize += $optimizedFileSize;
                
                $savings = round((($originalFileSize - $optimizedFileSize) / $originalFileSize) * 100, 2);
                
                echo "  ✓ {$original}: " . $this->formatBytes($originalFileSize) . " → " . $this->formatBytes($optimizedFileSize) . " ({$savings}% savings)\n";
            }
        }
        
        $totalSavings = round((($originalSize - $optimizedSize) / $originalSize) * 100, 2);
        echo "  📊 Total JS: " . $this->formatBytes($originalSize) . " → " . $this->formatBytes($optimizedSize) . " ({$totalSavings}% savings)\n\n";
        
        $this->results['js'] = [
            'original' => $originalSize,
            'optimized' => $optimizedSize,
            'savings' => $totalSavings
        ];
    }
    
    public function testImageOptimization() {
        echo "🔍 Testing Image Optimization...\n";
        
        $imageFiles = glob('images/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        $totalOriginalSize = 0;
        $totalOptimizedSize = 0;
        
        foreach ($imageFiles as $imageFile) {
            $originalSize = filesize($imageFile);
            $totalOriginalSize += $originalSize;
            
            echo "  📸 " . basename($imageFile) . ": " . $this->formatBytes($originalSize) . "\n";
        }
        
        echo "  📊 Total Images: " . $this->formatBytes($totalOriginalSize) . " (optimization recommended)\n\n";
        
        $this->results['images'] = [
            'original' => $totalOriginalSize,
            'optimized' => $totalOptimizedSize,
            'savings' => 0
        ];
    }
    
    public function generatePerformanceReport() {
        echo "📊 PERFORMANCE OPTIMIZATION REPORT\n";
        echo "================================\n\n";
        
        $totalOriginal = 0;
        $totalOptimized = 0;
        
        foreach ($this->results as $type => $data) {
            $totalOriginal += $data['original'];
            $totalOptimized += $data['optimized'];
            
            echo "{$type}: " . $this->formatBytes($data['original']) . " → " . $this->formatBytes($data['optimized']) . " ({$data['savings']}% savings)\n";
        }
        
        $totalSavings = round((($totalOriginal - $totalOptimized) / $totalOriginal) * 100, 2);
        
        echo "\n🎯 TOTAL SAVINGS: " . $this->formatBytes($totalOriginal) . " → " . $this->formatBytes($totalOptimized) . " ({$totalSavings}% savings)\n\n";
        
        echo "✅ OPTIMIZATION BENEFITS:\n";
        echo "• Faster page load times\n";
        echo "• Reduced bandwidth usage\n";
        echo "• Better Core Web Vitals scores\n";
        echo "• Improved user experience\n";
        echo "• Better SEO rankings\n\n";
        
        echo "🚀 NEXT STEPS:\n";
        echo "• Enable GZIP compression on server\n";
        echo "• Set up CDN for static assets\n";
        echo "• Implement browser caching\n";
        echo "• Use WebP images where possible\n";
        echo "• Consider HTTP/2 server push\n";
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    public function runAllTests() {
        echo "🚀 Starting Performance Tests...\n\n";
        
        $this->testCSSOptimization();
        $this->testJSOptimization();
        $this->testImageOptimization();
        $this->generatePerformanceReport();
    }
}

// Test'i çalıştır
$tester = new PerformanceTester();
$tester->runAllTests();
?>
