<?php
/**
 * Simple Image Optimizer
 * GD extension olmadan basit resim optimizasyonu
 */

class SimpleImageOptimizer {
    private $inputPath;
    private $outputPath;
    
    public function __construct($inputPath = 'images/', $outputPath = 'images/optimized/') {
        $this->inputPath = rtrim($inputPath, '/') . '/';
        $this->outputPath = rtrim($outputPath, '/') . '/';
        
        // Output klasörünü oluştur
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }
    }
    
    /**
     * Resim bilgilerini analiz et
     */
    public function analyzeImage($imagePath) {
        $fullPath = $this->inputPath . $imagePath;
        
        if (!file_exists($fullPath)) {
            return false;
        }
        
        $imageInfo = getimagesize($fullPath);
        if (!$imageInfo) {
            return false;
        }
        
        $fileSize = filesize($fullPath);
        $mimeType = $imageInfo['mime'];
        
        return [
            'path' => $imagePath,
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'size' => $fileSize,
            'mime' => $mimeType,
            'format' => $this->getImageFormat($mimeType),
            'optimizable' => $this->isOptimizable($mimeType, $fileSize)
        ];
    }
    
    /**
     * Resim formatını belirle
     */
    private function getImageFormat($mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
                return 'jpeg';
            case 'image/png':
                return 'png';
            case 'image/gif':
                return 'gif';
            case 'image/webp':
                return 'webp';
            case 'image/svg+xml':
                return 'svg';
            default:
                return 'unknown';
        }
    }
    
    /**
     * Resmin optimize edilebilir olup olmadığını kontrol et
     */
    private function isOptimizable($mimeType, $fileSize) {
        // SVG ve WebP zaten optimize
        if ($mimeType === 'image/svg+xml' || $mimeType === 'image/webp') {
            return false;
        }
        
        // Çok küçük dosyalar optimize etme
        if ($fileSize < 1024) { // 1KB'den küçük
            return false;
        }
        
        return true;
    }
    
    /**
     * Resim optimizasyon önerileri
     */
    public function getOptimizationSuggestions($imageInfo) {
        $suggestions = [];
        
        if (!$imageInfo['optimizable']) {
            return $suggestions;
        }
        
        // Boyut önerileri
        if ($imageInfo['width'] > 1920) {
            $suggestions[] = "Resim genişliği çok büyük ({$imageInfo['width']}px). 1920px'e kadar küçültün.";
        }
        
        if ($imageInfo['height'] > 1080) {
            $suggestions[] = "Resim yüksekliği çok büyük ({$imageInfo['height']}px). 1080px'e kadar küçültün.";
        }
        
        // Dosya boyutu önerileri
        if ($imageInfo['size'] > 500 * 1024) { // 500KB
            $suggestions[] = "Dosya boyutu çok büyük (" . round($imageInfo['size'] / 1024, 2) . "KB). Sıkıştırma önerilir.";
        }
        
        // Format önerileri
        if ($imageInfo['format'] === 'png' && $imageInfo['size'] > 100 * 1024) {
            $suggestions[] = "PNG formatı büyük dosya boyutu oluşturuyor. JPEG'e çevirmeyi düşünün.";
        }
        
        if ($imageInfo['format'] === 'gif') {
            $suggestions[] = "GIF formatı eski. Modern formatlar (WebP, AVIF) kullanmayı düşünün.";
        }
        
        return $suggestions;
    }
    
    /**
     * Lazy loading HTML oluştur
     */
    public function generateLazyLoadingHTML($imagePath, $alt = '', $class = '', $sizes = '') {
        $imageInfo = $this->analyzeImage($imagePath);
        
        if (!$imageInfo) {
            return '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($alt) . '" class="' . htmlspecialchars($class) . '">';
        }
        
        $html = '<img ';
        $html .= 'src="' . htmlspecialchars($imagePath) . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'class="' . htmlspecialchars($class) . '" ';
        $html .= 'loading="lazy" ';
        $html .= 'width="' . $imageInfo['width'] . '" ';
        $html .= 'height="' . $imageInfo['height'] . '" ';
        
        if ($sizes) {
            $html .= 'sizes="' . htmlspecialchars($sizes) . '" ';
        }
        
        $html .= '>';
        
        return $html;
    }
    
    /**
     * Responsive resim HTML'i oluştur
     */
    public function generateResponsiveHTML($imagePath, $alt = '', $class = '', $sizes = '') {
        $imageInfo = $this->analyzeImage($imagePath);
        
        if (!$imageInfo) {
            return '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($alt) . '" class="' . htmlspecialchars($class) . '">';
        }
        
        $baseName = pathinfo($imagePath, PATHINFO_FILENAME);
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        
        $html = '<picture>';
        
        // WebP formatı için (eğer varsa)
        $webpPath = str_replace($extension, 'webp', $imagePath);
        if (file_exists($this->inputPath . $webpPath)) {
            $html .= '<source srcset="' . htmlspecialchars($webpPath) . '" type="image/webp">';
        }
        
        // Fallback için orijinal resim
        $html .= '<img ';
        $html .= 'src="' . htmlspecialchars($imagePath) . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'class="' . htmlspecialchars($class) . '" ';
        $html .= 'loading="lazy" ';
        $html .= 'width="' . $imageInfo['width'] . '" ';
        $html .= 'height="' . $imageInfo['height'] . '" ';
        
        if ($sizes) {
            $html .= 'sizes="' . htmlspecialchars($sizes) . '" ';
        }
        
        $html .= '>';
        $html .= '</picture>';
        
        return $html;
    }
    
    /**
     * Tüm resimleri analiz et
     */
    public function analyzeAllImages($directory = '') {
        $path = $this->inputPath . $directory;
        $files = glob($path . '*.{jpg,jpeg,png,gif,webp,svg}', GLOB_BRACE);
        
        $results = [];
        $totalSize = 0;
        $optimizableCount = 0;
        $totalOptimizableSize = 0;
        
        foreach ($files as $file) {
            $relativePath = str_replace($this->inputPath, '', $file);
            $imageInfo = $this->analyzeImage($relativePath);
            
            if ($imageInfo) {
                $imageInfo['suggestions'] = $this->getOptimizationSuggestions($imageInfo);
                $results[] = $imageInfo;
                
                $totalSize += $imageInfo['size'];
                
                if ($imageInfo['optimizable']) {
                    $optimizableCount++;
                    $totalOptimizableSize += $imageInfo['size'];
                }
            }
        }
        
        return [
            'images' => $results,
            'summary' => [
                'total_images' => count($results),
                'total_size' => $totalSize,
                'optimizable_count' => $optimizableCount,
                'optimizable_size' => $totalOptimizableSize,
                'optimization_potential' => $totalSize > 0 ? 
                    round(($totalOptimizableSize / $totalSize) * 100, 2) : 0
            ]
        ];
    }
    
    /**
     * Resim optimizasyon raporu oluştur
     */
    public function generateOptimizationReport() {
        $analysis = $this->analyzeAllImages();
        
        $report = "=== RESİM OPTİMİZASYON RAPORU ===\n\n";
        $report .= "Toplam Resim: " . $analysis['summary']['total_images'] . "\n";
        $report .= "Toplam Boyut: " . round($analysis['summary']['total_size'] / 1024, 2) . " KB\n";
        $report .= "Optimize Edilebilir: " . $analysis['summary']['optimizable_count'] . "\n";
        $report .= "Optimizasyon Potansiyeli: " . $analysis['summary']['optimization_potential'] . "%\n\n";
        
        $report .= "=== ÖNERİLER ===\n";
        
        foreach ($analysis['images'] as $image) {
            if (!empty($image['suggestions'])) {
                $report .= "\n" . $image['path'] . ":\n";
                foreach ($image['suggestions'] as $suggestion) {
                    $report .= "  - " . $suggestion . "\n";
                }
            }
        }
        
        return $report;
    }
}

// Kullanım örneği
if (php_sapi_name() === 'cli') {
    $optimizer = new SimpleImageOptimizer();
    $analysis = $optimizer->analyzeAllImages();
    
    echo "Image Analysis Completed!\n";
    echo "Total Images: " . $analysis['summary']['total_images'] . "\n";
    echo "Total Size: " . round($analysis['summary']['total_size'] / 1024, 2) . " KB\n";
    echo "Optimizable: " . $analysis['summary']['optimizable_count'] . "\n";
    echo "Optimization Potential: " . $analysis['summary']['optimization_potential'] . "%\n";
    
    // Detaylı rapor
    $report = $optimizer->generateOptimizationReport();
    file_put_contents('image_optimization_report.txt', $report);
    echo "\nDetailed report saved to: image_optimization_report.txt\n";
}
?>


