<?php
/**
 * Log Cleaner Script for NextCode Group
 * Bu script log dosyalarını otomatik olarak temizler ve yönetir
 * Proje yapısını değiştirmeden çalışır
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class LogCleaner {
    private $logDir;
    private $maxSize = 5 * 1024 * 1024; // 5MB
    private $maxAge = 30; // 30 gün
    private $keepFiles = 10; // En az 10 dosya tut
    
    public function __construct($logDir = null) {
        $this->logDir = $logDir ?: __DIR__ . '/../logs/';
    }
    
    /**
     * Log dosyalarını temizle
     */
    public function cleanLogs() {
        $results = [
            'cleaned_files' => [],
            'errors' => [],
            'total_size_before' => 0,
            'total_size_after' => 0
        ];
        
        if (!is_dir($this->logDir)) {
            $results['errors'][] = "Log dizini bulunamadı: {$this->logDir}";
            return $results;
        }
        
        $files = glob($this->logDir . '*.log');
        
        foreach ($files as $file) {
            $results['total_size_before'] += filesize($file);
            
            try {
                $this->cleanLogFile($file, $results);
            } catch (Exception $e) {
                $results['errors'][] = "Dosya temizlenirken hata: " . basename($file) . " - " . $e->getMessage();
            }
            
            $results['total_size_after'] += filesize($file);
        }
        
        return $results;
    }
    
    /**
     * Tek log dosyasını temizle
     */
    private function cleanLogFile($filePath, &$results) {
        $fileName = basename($filePath);
        $fileSize = filesize($filePath);
        $fileAge = (time() - filemtime($filePath)) / (24 * 60 * 60); // gün cinsinden
        
        // Dosya çok büyükse veya çok eskiyse temizle
        if ($fileSize > $this->maxSize || $fileAge > $this->maxAge) {
            $backupPath = $this->createBackup($filePath);
            
            if ($backupPath) {
                // Ana dosyayı temizle (son 100 satırı tut)
                $this->truncateFile($filePath, 100);
                
                $results['cleaned_files'][] = [
                    'file' => $fileName,
                    'size_before' => $fileSize,
                    'size_after' => filesize($filePath),
                    'backup_created' => basename($backupPath)
                ];
            }
        }
    }
    
    /**
     * Dosya yedekleme oluştur
     */
    private function createBackup($filePath) {
        $fileName = basename($filePath, '.log');
        $timestamp = date('Y-m-d_H-i-s');
        $backupDir = $this->logDir . 'backups/';
        
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $backupPath = $backupDir . $fileName . '_' . $timestamp . '.log';
        
        if (copy($filePath, $backupPath)) {
            return $backupPath;
        }
        
        return false;
    }
    
    /**
     * Dosyayı kırp (son N satırı tut)
     */
    private function truncateFile($filePath, $keepLines = 100) {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        $totalLines = count($lines);
        
        if ($totalLines <= $keepLines) {
            return; // Dosya zaten küçük
        }
        
        $keepLinesArray = array_slice($lines, -$keepLines);
        $content = implode("\n", $keepLinesArray) . "\n";
        
        file_put_contents($filePath, $content);
    }
    
    /**
     * Eski yedekleri temizle
     */
    public function cleanOldBackups() {
        $backupDir = $this->logDir . 'backups/';
        
        if (!is_dir($backupDir)) {
            return [];
        }
        
        $files = glob($backupDir . '*.log');
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $deletedFiles = [];
        
        // En son dosyaları tut, eski olanları sil
        if (count($files) > $this->keepFiles) {
            $filesToDelete = array_slice($files, $this->keepFiles);
            
            foreach ($filesToDelete as $file) {
                if (unlink($file)) {
                    $deletedFiles[] = basename($file);
                }
            }
        }
        
        return $deletedFiles;
    }
    
    /**
     * Log istatistiklerini al
     */
    public function getLogStats() {
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'largest_file' => null,
            'oldest_file' => null,
            'files' => []
        ];
        
        if (!is_dir($this->logDir)) {
            return $stats;
        }
        
        $files = glob($this->logDir . '*.log');
        $stats['total_files'] = count($files);
        
        foreach ($files as $file) {
            $size = filesize($file);
            $modified = filemtime($file);
            
            $stats['total_size'] += $size;
            $stats['files'][] = [
                'name' => basename($file),
                'size' => $size,
                'size_formatted' => $this->formatBytes($size),
                'modified' => $modified,
                'age_days' => round((time() - $modified) / (24 * 60 * 60), 1)
            ];
            
            if (!$stats['largest_file'] || $size > $stats['largest_file']['size']) {
                $stats['largest_file'] = [
                    'name' => basename($file),
                    'size' => $size,
                    'size_formatted' => $this->formatBytes($size)
                ];
            }
            
            if (!$stats['oldest_file'] || $modified < $stats['oldest_file']['modified']) {
                $stats['oldest_file'] = [
                    'name' => basename($file),
                    'modified' => $modified,
                    'age_days' => round((time() - $modified) / (24 * 60 * 60), 1)
                ];
            }
        }
        
        $stats['total_size_formatted'] = $this->formatBytes($stats['total_size']);
        
        return $stats;
    }
    
    /**
     * Byte'ları okunabilir formata çevir
     */
    private function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $cleaner = new LogCleaner();
    
    echo "=== NextCode Log Cleaner ===\n";
    echo "Başlangıç zamanı: " . date('Y-m-d H:i:s') . "\n\n";
    
    // İstatistikleri göster
    $stats = $cleaner->getLogStats();
    echo "Mevcut Log Durumu:\n";
    echo "- Toplam dosya: {$stats['total_files']}\n";
    echo "- Toplam boyut: {$stats['total_size_formatted']}\n";
    echo "- En büyük dosya: {$stats['largest_file']['name']} ({$stats['largest_file']['size_formatted']})\n";
    echo "- En eski dosya: {$stats['oldest_file']['name']} ({$stats['oldest_file']['age_days']} gün)\n\n";
    
    // Logları temizle
    echo "Log dosyaları temizleniyor...\n";
    $results = $cleaner->cleanLogs();
    
    if (!empty($results['cleaned_files'])) {
        echo "Temizlenen dosyalar:\n";
        foreach ($results['cleaned_files'] as $file) {
            echo "- {$file['file']}: {$file['size_before']} -> {$file['size_after']} bytes\n";
        }
    } else {
        echo "Temizlenecek dosya bulunamadı.\n";
    }
    
    if (!empty($results['errors'])) {
        echo "\nHatalar:\n";
        foreach ($results['errors'] as $error) {
            echo "- $error\n";
        }
    }
    
    // Eski yedekleri temizle
    echo "\nEski yedekler temizleniyor...\n";
    $deletedBackups = $cleaner->cleanOldBackups();
    
    if (!empty($deletedBackups)) {
        echo "Silinen yedek dosyalar:\n";
        foreach ($deletedBackups as $backup) {
            echo "- $backup\n";
        }
    } else {
        echo "Silinecek yedek dosya bulunamadı.\n";
    }
    
    echo "\nİşlem tamamlandı: " . date('Y-m-d H:i:s') . "\n";
    echo "Toplam tasarruf: " . $cleaner->formatBytes($results['total_size_before'] - $results['total_size_after']) . "\n";
}

// Web arayüzü için
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $cleaner = new LogCleaner();
    
    switch ($_GET['action']) {
        case 'stats':
            echo json_encode($cleaner->getLogStats());
            break;
            
        case 'clean':
            $results = $cleaner->cleanLogs();
            echo json_encode($results);
            break;
            
        case 'clean_backups':
            $deleted = $cleaner->cleanOldBackups();
            echo json_encode(['deleted_files' => $deleted]);
            break;
            
        default:
            echo json_encode(['error' => 'Geçersiz işlem']);
    }
    exit;
}
?>

