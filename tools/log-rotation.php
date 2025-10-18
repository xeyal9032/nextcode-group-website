<?php
/**
 * NextCode Group - Log Rotation System
 * Log dosyalarını temizleme ve rotasyon sistemi
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class LogRotation {
    private $logDir;
    private $maxFileSize;
    private $maxFiles;
    private $retentionDays;
    
    public function __construct($logDir = 'logs', $maxFileSize = 10485760, $maxFiles = 5, $retentionDays = 30) {
        $this->logDir = $logDir;
        $this->maxFileSize = $maxFileSize; // 10MB
        $this->maxFiles = $maxFiles;
        $this->retentionDays = $retentionDays;
        
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }
    
    public function rotateLogs() {
        $results = [
            'rotated' => 0,
            'cleaned' => 0,
            'errors' => []
        ];
        
        try {
            // Log dosyalarını bul
            $logFiles = glob($this->logDir . '/*.log');
            
            foreach ($logFiles as $logFile) {
                if (is_file($logFile)) {
                    // Dosya boyutu kontrolü
                    if (filesize($logFile) > $this->maxFileSize) {
                        $this->rotateFile($logFile);
                        $results['rotated']++;
                    }
                }
            }
            
            // Eski log dosyalarını temizle
            $results['cleaned'] = $this->cleanOldLogs();
            
            return $results;
            
        } catch (Exception $e) {
            $results['errors'][] = $e->getMessage();
            return $results;
        }
    }
    
    private function rotateFile($logFile) {
        $baseName = pathinfo($logFile, PATHINFO_FILENAME);
        $extension = pathinfo($logFile, PATHINFO_EXTENSION);
        $timestamp = date('Y-m-d_H-i-s');
        
        // Mevcut rotasyon dosyalarını bul
        $existingFiles = glob($this->logDir . '/' . $baseName . '_*.' . $extension);
        
        // En eski dosyaları sil (maxFiles limitini aşarsa)
        if (count($existingFiles) >= $this->maxFiles) {
            usort($existingFiles, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            $toDelete = array_slice($existingFiles, 0, count($existingFiles) - $this->maxFiles + 1);
            foreach ($toDelete as $file) {
                unlink($file);
            }
        }
        
        // Mevcut dosyayı yeni isimle taşı
        $newName = $this->logDir . '/' . $baseName . '_' . $timestamp . '.' . $extension;
        rename($logFile, $newName);
        
        // Yeni boş dosya oluştur
        touch($logFile);
        chmod($logFile, 0644);
    }
    
    private function cleanOldLogs() {
        $cleaned = 0;
        $cutoffTime = time() - ($this->retentionDays * 24 * 60 * 60);
        
        // Tüm log dosyalarını kontrol et
        $allLogFiles = glob($this->logDir . '/*.log*');
        
        foreach ($allLogFiles as $logFile) {
            if (is_file($logFile) && filemtime($logFile) < $cutoffTime) {
                unlink($logFile);
                $cleaned++;
            }
        }
        
        return $cleaned;
    }
    
    public function cleanAllLogs() {
        $results = [
            'cleaned' => 0,
            'errors' => []
        ];
        
        try {
            $logFiles = glob($this->logDir . '/*.log*');
            
            foreach ($logFiles as $logFile) {
                if (is_file($logFile)) {
                    unlink($logFile);
                    $results['cleaned']++;
                }
            }
            
            return $results;
            
        } catch (Exception $e) {
            $results['errors'][] = $e->getMessage();
            return $results;
        }
    }
    
    public function getLogStats() {
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'largest_file' => null,
            'oldest_file' => null,
            'newest_file' => null
        ];
        
        $logFiles = glob($this->logDir . '/*.log*');
        
        foreach ($logFiles as $logFile) {
            if (is_file($logFile)) {
                $stats['total_files']++;
                $fileSize = filesize($logFile);
                $stats['total_size'] += $fileSize;
                
                if (!$stats['largest_file'] || $fileSize > filesize($stats['largest_file'])) {
                    $stats['largest_file'] = $logFile;
                }
                
                if (!$stats['oldest_file'] || filemtime($logFile) < filemtime($stats['oldest_file'])) {
                    $stats['oldest_file'] = $logFile;
                }
                
                if (!$stats['newest_file'] || filemtime($logFile) > filemtime($stats['newest_file'])) {
                    $stats['newest_file'] = $logFile;
                }
            }
        }
        
        $stats['total_size_mb'] = round($stats['total_size'] / 1024 / 1024, 2);
        
        return $stats;
    }
    
    public function compressOldLogs() {
        $compressed = 0;
        $errors = [];
        
        try {
            // 7 günden eski log dosyalarını sıkıştır
            $cutoffTime = time() - (7 * 24 * 60 * 60);
            $logFiles = glob($this->logDir . '/*.log');
            
            foreach ($logFiles as $logFile) {
                if (is_file($logFile) && filemtime($logFile) < $cutoffTime) {
                    $compressedFile = $logFile . '.gz';
                    
                    if (function_exists('gzopen')) {
                        $fp_in = fopen($logFile, 'rb');
                        $fp_out = gzopen($compressedFile, 'wb9');
                        
                        if ($fp_in && $fp_out) {
                            while (!feof($fp_in)) {
                                gzwrite($fp_out, fread($fp_in, 8192));
                            }
                            
                            fclose($fp_in);
                            gzclose($fp_out);
                            
                            unlink($logFile);
                            $compressed++;
                        }
                    }
                }
            }
            
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        return ['compressed' => $compressed, 'errors' => $errors];
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $rotation = new LogRotation();
    
    $action = $argv[1] ?? 'rotate';
    
    switch ($action) {
        case 'rotate':
            $result = $rotation->rotateLogs();
            echo "Log rotasyonu tamamlandı:\n";
            echo "Rotasyon yapılan dosya: " . $result['rotated'] . "\n";
            echo "Temizlenen dosya: " . $result['cleaned'] . "\n";
            if (!empty($result['errors'])) {
                echo "Hatalar: " . implode(', ', $result['errors']) . "\n";
            }
            break;
            
        case 'clean':
            $result = $rotation->cleanAllLogs();
            echo "Log temizleme tamamlandı:\n";
            echo "Temizlenen dosya: " . $result['cleaned'] . "\n";
            if (!empty($result['errors'])) {
                echo "Hatalar: " . implode(', ', $result['errors']) . "\n";
            }
            break;
            
        case 'stats':
            $stats = $rotation->getLogStats();
            echo "Log İstatistikleri:\n";
            echo "Toplam dosya: " . $stats['total_files'] . "\n";
            echo "Toplam boyut: " . $stats['total_size_mb'] . " MB\n";
            if ($stats['largest_file']) {
                echo "En büyük dosya: " . basename($stats['largest_file']) . " (" . round(filesize($stats['largest_file']) / 1024 / 1024, 2) . " MB)\n";
            }
            break;
            
        case 'compress':
            $result = $rotation->compressOldLogs();
            echo "Log sıkıştırma tamamlandı:\n";
            echo "Sıkıştırılan dosya: " . $result['compressed'] . "\n";
            if (!empty($result['errors'])) {
                echo "Hatalar: " . implode(', ', $result['errors']) . "\n";
            }
            break;
            
        default:
            echo "Kullanım: php log-rotation.php [rotate|clean|stats|compress]\n";
            break;
    }
}

?>

