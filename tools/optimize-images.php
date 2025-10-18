<?php
// Image Optimizer - WebP conversion and lazy loading
// Bu script resimleri optimize eder ve WebP formatına çevirir

class ImageOptimizer {
    private $imageDir = 'images/';
    private $outputDir = 'images/optimized/';
    private $webpDir = 'images/webp/';
    
    public function __construct() {
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
        if (!file_exists($this->webpDir)) {
            mkdir($this->webpDir, 0755, true);
        }
    }
    
    public function optimizeImage($inputFile, $quality = 85) {
        $info = pathinfo($inputFile);
        $extension = strtolower($info['extension']);
        
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return false;
        }
        
        $outputFile = $this->outputDir . basename($inputFile);
        
        // Resim boyutunu kontrol et
        $imageSize = getimagesize($inputFile);
        if (!$imageSize) {
            return false;
        }
        
        $width = $imageSize[0];
        $height = $imageSize[1];
        
        // Resim kaynağını oluştur
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $source = imagecreatefromjpeg($inputFile);
                break;
            case 'png':
                $source = imagecreatefrompng($inputFile);
                break;
            case 'gif':
                $source = imagecreatefromgif($inputFile);
                break;
            default:
                return false;
        }
        
        if (!$source) {
            return false;
        }
        
        // Optimize edilmiş resmi kaydet
        $result = false;
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $result = imagejpeg($source, $outputFile, $quality);
                break;
            case 'png':
                $result = imagepng($source, $outputFile, 9);
                break;
            case 'gif':
                $result = imagegif($source, $outputFile);
                break;
        }
        
        imagedestroy($source);
        
        if ($result) {
            $originalSize = filesize($inputFile);
            $optimizedSize = filesize($outputFile);
            $savings = round((($originalSize - $optimizedSize) / $originalSize) * 100, 2);
            
            echo "✓ Optimized: " . basename($inputFile) . " ({$originalSize} bytes → {$optimizedSize} bytes, {$savings}% savings)\n";
        }
        
        return $result;
    }
    
    public function convertToWebP($inputFile, $quality = 85) {
        $info = pathinfo($inputFile);
        $extension = strtolower($info['extension']);
        
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return false;
        }
        
        $webpFile = $this->webpDir . $info['filename'] . '.webp';
        
        // Resim kaynağını oluştur
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $source = imagecreatefromjpeg($inputFile);
                break;
            case 'png':
                $source = imagecreatefrompng($inputFile);
                break;
            default:
                return false;
        }
        
        if (!$source) {
            return false;
        }
        
        // WebP formatına çevir
        $result = imagewebp($source, $webpFile, $quality);
        imagedestroy($source);
        
        if ($result) {
            $originalSize = filesize($inputFile);
            $webpSize = filesize($webpFile);
            $savings = round((($originalSize - $webpSize) / $originalSize) * 100, 2);
            
            echo "✓ WebP: " . basename($inputFile) . " → " . basename($webpFile) . " ({$originalSize} bytes → {$webpSize} bytes, {$savings}% savings)\n";
        }
        
        return $result;
    }
    
    public function generateResponsiveImages() {
        $responsiveHTML = '
<!-- Responsive Images Helper -->
<picture>
    <source srcset="images/webp/image.webp" type="image/webp">
    <source srcset="images/optimized/image.jpg" type="image/jpeg">
    <img src="images/optimized/image.jpg" alt="Description" loading="lazy" width="800" height="600">
</picture>

<!-- Lazy Loading Implementation -->
<script>
// Lazy loading for images
document.addEventListener("DOMContentLoaded", function() {
    const lazyImages = document.querySelectorAll("img[data-src]");
    
    if ("IntersectionObserver" in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove("lazy");
                    img.classList.add("loaded");
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for older browsers
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.classList.remove("lazy");
            img.classList.add("loaded");
        });
    }
});
</script>

<!-- CSS for lazy loading -->
<style>
.lazy {
    opacity: 0;
    transition: opacity 0.3s;
}

.loaded {
    opacity: 1;
}

img {
    max-width: 100%;
    height: auto;
}
</style>
';
        
        file_put_contents($this->outputDir . 'responsive-images.html', $responsiveHTML);
        echo "✓ Generated responsive-images.html\n";
    }
    
    public function optimizeAllImages() {
        echo "🚀 Starting Image Optimization...\n\n";
        
        // Responsive images helper oluştur
        $this->generateResponsiveImages();
        
        // Resim dosyalarını bul
        $imageFiles = glob($this->imageDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        if (empty($imageFiles)) {
            echo "No images found in {$this->imageDir}\n";
            return;
        }
        
        foreach ($imageFiles as $imageFile) {
            // Optimize et
            $this->optimizeImage($imageFile);
            
            // WebP'ye çevir
            $this->convertToWebP($imageFile);
        }
        
        echo "\n✅ Image Optimization completed!\n";
    }
}

// Script'i çalıştır
$optimizer = new ImageOptimizer();
$optimizer->optimizeAllImages();
?>
