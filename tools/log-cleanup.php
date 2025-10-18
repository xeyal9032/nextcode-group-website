<?php
/**
 * NextCode Group - Log Cleanup Utility
 * Hızlı log temizleme aracı
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class LogCleanup {
    private $logDir;
    private $backupDir;
    
    public function __construct($logDir = 'logs', $backupDir = 'logs/backup') {
        $this->logDir = $logDir;
        $this->backupDir = $backupDir;
        
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
        
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }
    
    public function quickClean($backup = true) {
        $results = [
            'cleaned' => 0,
            'backed_up' => 0,
            'errors' => []
        ];
        
        try {
            $logFiles = glob($this->logDir . '/*.log');
            
            foreach ($logFiles as $logFile) {
                if (is_file($logFile) && filesize($logFile) > 0) {
                    // Backup oluştur
                    if ($backup) {
                        $backupFile = $this->backupDir . '/' . basename($logFile) . '.' . date('Y-m-d_H-i-s');
                        if (copy($logFile, $backupFile)) {
                            $results['backed_up']++;
                        }
                    }
                    
                    // Dosyayı temizle (içeriği sil, dosyayı silme)
                    file_put_contents($logFile, '');
                    $results['cleaned']++;
                }
            }
            
            return $results;
            
        } catch (Exception $e) {
            $results['errors'][] = $e->getMessage();
            return $results;
        }
    }
    
    public function cleanSpecificLogs($patterns = []) {
        if (empty($patterns)) {
            $patterns = ['*.log', '*.log.*'];
        }
        
        $results = [
            'cleaned' => 0,
            'errors' => []
        ];
        
        try {
            foreach ($patterns as $pattern) {
                $files = glob($this->logDir . '/' . $pattern);
                
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                        $results['cleaned']++;
                    }
                }
            }
            
            return $results;
            
        } catch (Exception $e) {
            $results['errors'][] = $e->getMessage();
            return $results;
        }
    }
    
    public function getLogSizes() {
        $sizes = [];
        $logFiles = glob($this->logDir . '/*.log*');
        
        foreach ($logFiles as $logFile) {
            if (is_file($logFile)) {
                $sizes[basename($logFile)] = [
                    'size' => filesize($logFile),
                    'size_mb' => round(filesize($logFile) / 1024 / 1024, 2),
                    'modified' => date('Y-m-d H:i:s', filemtime($logFile))
                ];
            }
        }
        
        return $sizes;
    }
    
    public function archiveOldLogs($days = 7) {
        $results = [
            'archived' => 0,
            'errors' => []
        ];
        
        try {
            $cutoffTime = time() - ($days * 24 * 60 * 60);
            $logFiles = glob($this->logDir . '/*.log*');
            
            foreach ($logFiles as $logFile) {
                if (is_file($logFile) && filemtime($logFile) < $cutoffTime) {
                    $archiveFile = $this->backupDir . '/' . basename($logFile) . '.archive';
                    
                    if (rename($logFile, $archiveFile)) {
                        $results['archived']++;
                    }
                }
            }
            
            return $results;
            
        } catch (Exception $e) {
            $results['errors'][] = $e->getMessage();
            return $results;
        }
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $cleanup = new LogCleanup();
    
    $action = $argv[1] ?? 'quick';
    
    switch ($action) {
        case 'quick':
            $result = $cleanup->quickClean(true);
            echo "Hızlı log temizleme tamamlandı:\n";
            echo "Temizlenen dosya: " . $result['cleaned'] . "\n";
            echo "Yedeklenen dosya: " . $result['backed_up'] . "\n";
            if (!empty($result['errors'])) {
                echo "Hatalar: " . implode(', ', $result['errors']) . "\n";
            }
            break;
            
        case 'clean':
            $result = $cleanup->cleanSpecificLogs();
            echo "Log temizleme tamamlandı:\n";
            echo "Silinen dosya: " . $result['cleaned'] . "\n";
            if (!empty($result['errors'])) {
                echo "Hatalar: " . implode(', ', $result['errors']) . "\n";
            }
            break;
            
        case 'sizes':
            $sizes = $cleanup->getLogSizes();
            echo "Log dosya boyutları:\n";
            foreach ($sizes as $file => $info) {
                echo "- $file: {$info['size_mb']} MB (Son değişiklik: {$info['modified']})\n";
            }
            break;
            
        case 'archive':
            $days = $argv[2] ?? 7;
            $result = $cleanup->archiveOldLogs($days);
            echo "Eski log arşivleme tamamlandı ($days gün):\n";
            echo "Arşivlenen dosya: " . $result['archived'] . "\n";
            if (!empty($result['errors'])) {
                echo "Hatalar: " . implode(', ', $result['errors']) . "\n";
            }
            break;
            
        default:
            echo "Kullanım: php log-cleanup.php [quick|clean|sizes|archive]\n";
            echo "  quick   - Hızlı temizleme (yedekleme ile)\n";
            echo "  clean   - Tam temizleme (dosya silme)\n";
            echo "  sizes   - Dosya boyutlarını göster\n";
            echo "  archive - Eski dosyaları arşivle (varsayılan: 7 gün)\n";
            break;
    }
}

?>

