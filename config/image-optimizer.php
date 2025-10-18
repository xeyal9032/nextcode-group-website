<?php
/**
 * Image Optimizer
 * Resimleri optimize eder, WebP formatına çevirir ve lazy loading ekler
 */

class ImageOptimizer {
    private $inputPath;
    private $outputPath;
    private $webpPath;
    private $quality;
    
    public function __construct($inputPath = 'images/', $outputPath = 'images/optimized/', $webpPath = 'images/webp/', $quality = 85) {
        $this->inputPath = rtrim($inputPath, '/') . '/';
        $this->outputPath = rtrim($outputPath, '/') . '/';
        $this->webpPath = rtrim($webpPath, '/') . '/';
        $this->quality = $quality;
        
        // Output klasörlerini oluştur
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }
        if (!is_dir($this->webpPath)) {
            mkdir($this->webpPath, 0755, true);
        }
    }
    
    /**
     * Resmi optimize et
     */
    public function optimizeImage($imagePath, $maxWidth = 1920, $maxHeight = 1080) {
        $fullPath = $this->inputPath . $imagePath;
        
        if (!file_exists($fullPath)) {
            return false;
        }
        
        $imageInfo = getimagesize($fullPath);
        if (!$imageInfo) {
            return false;
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Resmi yükle
        $image = $this->loadImage($fullPath, $mimeType);
        if (!$image) {
            return false;
        }
        
        // Boyutları hesapla
        $dimensions = $this->calculateDimensions($originalWidth, $originalHeight, $maxWidth, $maxHeight);
        
        // Resmi yeniden boyutlandır
        $resizedImage = imagecreatetruecolor($dimensions['width'], $dimensions['height']);
        
        // Şeffaflık korunması
        if ($mimeType === 'image/png') {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefilledrectangle($resizedImage, 0, 0, $dimensions['width'], $dimensions['height'], $transparent);
        }
        
        imagecopyresampled(
            $resizedImage, $image,
            0, 0, 0, 0,
            $dimensions['width'], $dimensions['height'],
            $originalWidth, $originalHeight
        );
        
        // Optimize edilmiş resmi kaydet
        $outputFile = $this->outputPath . pathinfo($imagePath, PATHINFO_FILENAME) . '.jpg';
        $this->saveImage($resizedImage, $outputFile, 'image/jpeg', $this->quality);
        
        // WebP formatında kaydet
        $webpFile = $this->webpPath . pathinfo($imagePath, PATHINFO_FILENAME) . '.webp';
        $this->saveWebP($resizedImage, $webpFile, $this->quality);
        
        // Belleği temizle
        imagedestroy($image);
        imagedestroy($resizedImage);
        
        return [
            'original' => $imagePath,
            'optimized' => basename($outputFile),
            'webp' => basename($webpFile),
            'original_size' => filesize($fullPath),
            'optimized_size' => filesize($outputFile),
            'webp_size' => file_exists($webpFile) ? filesize($webpFile) : 0,
            'dimensions' => $dimensions
        ];
    }
    
    /**
     * Resmi yükle
     */
    private function loadImage($path, $mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                return false;
        }
    }
    
    /**
     * Resmi kaydet
     */
    private function saveImage($image, $path, $mimeType, $quality) {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagejpeg($image, $path, $quality);
            case 'image/png':
                return imagepng($image, $path, 9);
            case 'image/gif':
                return imagegif($image, $path);
            case 'image/webp':
                return imagewebp($image, $path, $quality);
            default:
                return false;
        }
    }
    
    /**
     * WebP formatında kaydet
     */
    private function saveWebP($image, $path, $quality) {
        if (function_exists('imagewebp')) {
            return imagewebp($image, $path, $quality);
        }
        return false;
    }
    
    /**
     * Boyutları hesapla
     */
    private function calculateDimensions($originalWidth, $originalHeight, $maxWidth, $maxHeight) {
        $ratio = $originalWidth / $originalHeight;
        
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return [
                'width' => $originalWidth,
                'height' => $originalHeight
            ];
        }
        
        if ($maxWidth / $maxHeight > $ratio) {
            $width = $maxHeight * $ratio;
            $height = $maxHeight;
        } else {
            $width = $maxWidth;
            $height = $maxWidth / $ratio;
        }
        
        return [
            'width' => (int)$width,
            'height' => (int)$height
        ];
    }
    
    /**
     * Responsive resim seti oluştur
     */
    public function createResponsiveImages($imagePath) {
        $sizes = [
            'small' => ['width' => 480, 'height' => 320],
            'medium' => ['width' => 768, 'height' => 512],
            'large' => ['width' => 1200, 'height' => 800],
            'xlarge' => ['width' => 1920, 'height' => 1080]
        ];
        
        $results = [];
        
        foreach ($sizes as $size => $dimensions) {
            $result = $this->optimizeImage($imagePath, $dimensions['width'], $dimensions['height']);
            if ($result) {
                $results[$size] = $result;
            }
        }
        
        return $results;
    }
    
    /**
     * Tüm resimleri optimize et
     */
    public function optimizeAllImages($directory = '') {
        $path = $this->inputPath . $directory;
        $files = glob($path . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        $results = [];
        $totalOriginalSize = 0;
        $totalOptimizedSize = 0;
        $totalWebpSize = 0;
        
        foreach ($files as $file) {
            $relativePath = str_replace($this->inputPath, '', $file);
            $result = $this->optimizeImage($relativePath);
            
            if ($result) {
                $results[] = $result;
                $totalOriginalSize += $result['original_size'];
                $totalOptimizedSize += $result['optimized_size'];
                $totalWebpSize += $result['webp_size'];
            }
        }
        
        return [
            'results' => $results,
            'summary' => [
                'total_images' => count($results),
                'total_original_size' => $totalOriginalSize,
                'total_optimized_size' => $totalOptimizedSize,
                'total_webp_size' => $totalWebpSize,
                'savings_percent' => $totalOriginalSize > 0 ? 
                    round((($totalOriginalSize - $totalOptimizedSize) / $totalOriginalSize) * 100, 2) : 0,
                'webp_savings_percent' => $totalOriginalSize > 0 ? 
                    round((($totalOriginalSize - $totalWebpSize) / $totalOriginalSize) * 100, 2) : 0
            ]
        ];
    }
    
    /**
     * Lazy loading HTML oluştur
     */
    public function generateLazyLoadingHTML($imagePath, $alt = '', $class = '', $sizes = '') {
        $baseName = pathinfo($imagePath, PATHINFO_FILENAME);
        $optimizedPath = $this->outputPath . $baseName . '.jpg';
        $webpPath = $this->webpPath . $baseName . '.webp';
        
        $html = '<picture>';
        
        // WebP formatı için
        if (file_exists($webpPath)) {
            $html .= '<source srcset="' . $webpPath . '" type="image/webp">';
        }
        
        // Fallback için optimize edilmiş resim
        $html .= '<img ';
        $html .= 'src="' . $optimizedPath . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'class="' . htmlspecialchars($class) . '" ';
        $html .= 'loading="lazy" ';
        if ($sizes) {
            $html .= 'sizes="' . htmlspecialchars($sizes) . '" ';
        }
        $html .= '>';
        
        $html .= '</picture>';
        
        return $html;
    }
    
    /**
     * Responsive resim HTML'i oluştur
     */
    public function generateResponsiveHTML($imagePath, $alt = '', $class = '', $sizes = '') {
        $baseName = pathinfo($imagePath, PATHINFO_FILENAME);
        
        $html = '<picture>';
        
        // WebP formatı için responsive
        $webpPath = $this->webpPath . $baseName . '.webp';
        if (file_exists($webpPath)) {
            $html .= '<source srcset="' . $webpPath . '" type="image/webp" sizes="' . htmlspecialchars($sizes) . '">';
        }
        
        // Fallback için optimize edilmiş resim
        $optimizedPath = $this->outputPath . $baseName . '.jpg';
        $html .= '<img ';
        $html .= 'src="' . $optimizedPath . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'class="' . htmlspecialchars($class) . '" ';
        $html .= 'loading="lazy" ';
        if ($sizes) {
            $html .= 'sizes="' . htmlspecialchars($sizes) . '" ';
        }
        $html .= '>';
        
        $html .= '</picture>';
        
        return $html;
    }
}

// Kullanım örneği
if (php_sapi_name() === 'cli') {
    $optimizer = new ImageOptimizer();
    $results = $optimizer->optimizeAllImages();
    
    echo "Image Optimization Completed!\n";
    echo "Total Images: " . $results['summary']['total_images'] . "\n";
    echo "Original Size: " . round($results['summary']['total_original_size'] / 1024, 2) . " KB\n";
    echo "Optimized Size: " . round($results['summary']['total_optimized_size'] / 1024, 2) . " KB\n";
    echo "WebP Size: " . round($results['summary']['total_webp_size'] / 1024, 2) . " KB\n";
    echo "Savings: " . $results['summary']['savings_percent'] . "%\n";
    echo "WebP Savings: " . $results['summary']['webp_savings_percent'] . "%\n";
}
?>