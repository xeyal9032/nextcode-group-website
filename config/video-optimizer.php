<?php
/**
 * Video Optimizer
 * Video dosyalarını optimize eder ve farklı formatlar oluşturur
 */

class VideoOptimizer {
    private $inputPath;
    private $outputPath;
    
    public function __construct($inputPath = 'videos/', $outputPath = 'videos/optimized/') {
        $this->inputPath = rtrim($inputPath, '/') . '/';
        $this->outputPath = rtrim($outputPath, '/') . '/';
        
        // Output klasörünü oluştur
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }
    }
    
    /**
     * Video bilgilerini analiz et
     */
    public function analyzeVideo($videoFile) {
        $fullPath = $this->inputPath . $videoFile;
        
        if (!file_exists($fullPath)) {
            return false;
        }
        
        $fileSize = filesize($fullPath);
        $mimeType = mime_content_type($fullPath);
        
        return [
            'filename' => $videoFile,
            'size' => $fileSize,
            'size_mb' => round($fileSize / (1024 * 1024), 2),
            'mime_type' => $mimeType,
            'extension' => pathinfo($videoFile, PATHINFO_EXTENSION),
            'optimizable' => $this->isOptimizable($fileSize, $mimeType)
        ];
    }
    
    /**
     * Video'nun optimize edilebilir olup olmadığını kontrol et
     */
    private function isOptimizable($fileSize, $mimeType) {
        // Çok küçük dosyalar optimize etme
        if ($fileSize < 1024 * 1024) { // 1MB'den küçük
            return false;
        }
        
        // Desteklenen formatlar
        $supportedFormats = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv'];
        if (!in_array($mimeType, $supportedFormats)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Video optimizasyon önerileri
     */
    public function getOptimizationSuggestions($videoInfo) {
        $suggestions = [];
        
        if (!$videoInfo['optimizable']) {
            return $suggestions;
        }
        
        // Boyut önerileri
        if ($videoInfo['size_mb'] > 50) {
            $suggestions[] = "Video boyutu çok büyük ({$videoInfo['size_mb']}MB). 50MB'e kadar küçültün.";
        }
        
        if ($videoInfo['size_mb'] > 20) {
            $suggestions[] = "Video boyutu büyük ({$videoInfo['size_mb']}MB). Sıkıştırma önerilir.";
        }
        
        // Format önerileri
        if ($videoInfo['extension'] !== 'mp4') {
            $suggestions[] = "MP4 formatı önerilir. Mevcut format: " . strtoupper($videoInfo['extension']);
        }
        
        // Performans önerileri
        $suggestions[] = "Video için poster image ekleyin";
        $suggestions[] = "Lazy loading kullanın";
        $suggestions[] = "CDN kullanmayı düşünün";
        
        return $suggestions;
    }
    
    /**
     * Video için poster image oluştur
     */
    public function generatePosterImage($videoFile) {
        // Bu fonksiyon FFmpeg gerektirir
        // Basit implementasyon için placeholder
        return [
            'poster' => 'images/video-poster.jpg',
            'thumbnail' => 'images/video-thumbnail.jpg'
        ];
    }
    
    /**
     * Video HTML'i oluştur
     */
    public function generateVideoHTML($videoFile, $poster = '', $autoplay = false) {
        $videoInfo = $this->analyzeVideo($videoFile);
        
        if (!$videoInfo) {
            return '<p>Video dosyası bulunamadı.</p>';
        }
        
        $html = '<video ';
        $html .= 'id="videoPlayer" ';
        $html .= 'width="100%" ';
        $html .= 'height="315" ';
        $html .= 'controls ';
        $html .= 'preload="metadata" ';
        
        if ($poster) {
            $html .= 'poster="' . htmlspecialchars($poster) . '" ';
        }
        
        if ($autoplay) {
            $html .= 'autoplay ';
        }
        
        $html .= '>';
        $html .= '<source src="' . htmlspecialchars($this->inputPath . $videoFile) . '" type="video/mp4">';
        $html .= 'Tarayıcınız video etiketini desteklemiyor.';
        $html .= '</video>';
        
        return $html;
    }
    
    /**
     * Video optimizasyon raporu oluştur
     */
    public function generateOptimizationReport() {
        $files = glob($this->inputPath . '*.{mp4,avi,mov,wmv}', GLOB_BRACE);
        
        $report = "=== VIDEO OPTİMİZASYON RAPORU ===\n\n";
        
        if (empty($files)) {
            $report .= "Video dosyası bulunamadı.\n";
            return $report;
        }
        
        $totalSize = 0;
        $optimizableCount = 0;
        
        foreach ($files as $file) {
            $filename = basename($file);
            $videoInfo = $this->analyzeVideo($filename);
            
            if ($videoInfo) {
                $report .= "Video: {$filename}\n";
                $report .= "  Boyut: {$videoInfo['size_mb']} MB\n";
                $report .= "  Format: {$videoInfo['extension']}\n";
                $report .= "  MIME: {$videoInfo['mime_type']}\n";
                
                $suggestions = $this->getOptimizationSuggestions($videoInfo);
                if (!empty($suggestions)) {
                    $report .= "  Öneriler:\n";
                    foreach ($suggestions as $suggestion) {
                        $report .= "    - {$suggestion}\n";
                    }
                }
                
                $report .= "\n";
                
                $totalSize += $videoInfo['size'];
                if ($videoInfo['optimizable']) {
                    $optimizableCount++;
                }
            }
        }
        
        $report .= "Toplam Boyut: " . round($totalSize / (1024 * 1024), 2) . " MB\n";
        $report .= "Optimize Edilebilir: {$optimizableCount}\n";
        
        return $report;
    }
}

// Kullanım örneği
if (php_sapi_name() === 'cli') {
    $optimizer = new VideoOptimizer();
    
    echo "Video Optimizer Created!\n";
    
    // Video analizi
    $videoInfo = $optimizer->analyzeVideo('Видео WhatsApp 2025-10-04 в 15.31.15_0010b383.mp4');
    if ($videoInfo) {
        echo "Video: " . $videoInfo['filename'] . "\n";
        echo "Boyut: " . $videoInfo['size_mb'] . " MB\n";
        echo "Format: " . $videoInfo['extension'] . "\n";
        echo "Optimize Edilebilir: " . ($videoInfo['optimizable'] ? 'Evet' : 'Hayır') . "\n";
        
        $suggestions = $optimizer->getOptimizationSuggestions($videoInfo);
        if (!empty($suggestions)) {
            echo "Öneriler:\n";
            foreach ($suggestions as $suggestion) {
                echo "  - " . $suggestion . "\n";
            }
        }
    }
    
    // Rapor oluştur
    $report = $optimizer->generateOptimizationReport();
    file_put_contents('video_optimization_report.txt', $report);
    echo "\nVideo optimization report saved to: video_optimization_report.txt\n";
}
?>


