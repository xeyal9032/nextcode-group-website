<?php
/**
 * NextCode Group - Otomatik Backup Sistemi
 * Gelişmiş yedekleme ve geri yükleme sistemi
 */

// Güvenlik kontrolü
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once __DIR__ . '/../includes/error_handler.php';

class AutoBackup {
    private $backupDir;
    private $maxBackups;
    private $excludePatterns;
    private $logFile;
    
    public function __construct($backupDir = 'backup', $maxBackups = 10) {
        $this->backupDir = $backupDir;
        $this->maxBackups = $maxBackups;
        $this->logFile = __DIR__ . '/../logs/backup.log';
        $this->excludePatterns = [
            'backup/',
            'backups/',
            'logs/',
            'cache/',
            'tmp/',
            '.git/',
            'node_modules/',
            '*.log',
            '*.tmp',
            '.DS_Store',
            'Thumbs.db'
        ];
        
        $this->createDirectories();
    }
    
    private function createDirectories() {
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
        
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    public function createBackup($includeDatabase = true, $compress = false) {
        $timestamp = date('Y-m-d_H-i-s');
        $backupPath = $this->backupDir . '/' . $timestamp;
        
        try {
            $this->log("Backup başlatılıyor: $timestamp");
            
            // Backup klasörünü oluştur
            if (!mkdir($backupPath, 0755, true)) {
                throw new Exception("Backup klasörü oluşturulamadı: $backupPath");
            }
            
            $stats = [
                'timestamp' => $timestamp,
                'date' => date('Y-m-d H:i:s'),
                'files_count' => 0,
                'size_bytes' => 0,
                'include_database' => $includeDatabase,
                'compressed' => $compress,
                'status' => 'success'
            ];
            
            // Dosyaları kopyala
            $this->copyFiles($backupPath, $stats);
            
            // Veritabanı yedeği
            if ($includeDatabase) {
                $this->backupDatabase($backupPath, $stats);
            }
            
            // Backup bilgilerini kaydet
            $stats['size_mb'] = round($stats['size_bytes'] / 1024 / 1024, 2);
            file_put_contents($backupPath . '/backup-info.json', json_encode($stats, JSON_PRETTY_PRINT));
            
            // Eski backup'ları temizle
            $this->cleanOldBackups();
            
            $this->log("Backup tamamlandı: $timestamp - {$stats['files_count']} dosya, {$stats['size_mb']} MB");
            
            return [
                'success' => true,
                'backup_path' => $backupPath,
                'stats' => $stats
            ];
            
        } catch (Exception $e) {
            $this->log("Backup hatası: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function copyFiles($backupPath, &$stats) {
        $sourceDir = dirname(__DIR__);
        $this->copyDirectory($sourceDir, $backupPath, $stats);
    }
    
    private function copyDirectory($source, $dest, &$stats) {
        if (!is_dir($source)) {
            return;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $sourcePath = $item->getPathname();
            $relativePath = str_replace(dirname(__DIR__) . DIRECTORY_SEPARATOR, '', $sourcePath);
            $destPath = $dest . DIRECTORY_SEPARATOR . $relativePath;
            
            // Exclude patterns kontrolü
            if ($this->shouldExclude($relativePath)) {
                continue;
            }
            
            if ($item->isDir()) {
                if (!is_dir($destPath)) {
                    mkdir($destPath, 0755, true);
                }
            } else {
                if (copy($sourcePath, $destPath)) {
                    $stats['files_count']++;
                    $stats['size_bytes'] += filesize($sourcePath);
                }
            }
        }
    }
    
    private function shouldExclude($path) {
        foreach ($this->excludePatterns as $pattern) {
            if (fnmatch($pattern, $path) || strpos($path, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }
    
    private function backupDatabase($backupPath, &$stats) {
        try {
            require_once __DIR__ . '/../config/database.php';
            
            $db = new Database();
            $conn = $db->getConnection();
            
            if (!$conn) {
                throw new Exception("Veritabanı bağlantısı kurulamadı");
            }
            
            $dbName = 'gtorg_nextcode';
            $sqlFile = $backupPath . '/database_backup.sql';
            
            // MySQL dump komutu
            $command = "mysqldump -h gtorg.mysql.tools -u gtorg_nextcode -p';849#dVEyg' $dbName > \"$sqlFile\"";
            
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($sqlFile)) {
                $stats['database_backup'] = true;
                $stats['database_size'] = filesize($sqlFile);
                $this->log("Veritabanı yedeği oluşturuldu: " . basename($sqlFile));
            } else {
                throw new Exception("Veritabanı yedeği oluşturulamadı");
            }
            
        } catch (Exception $e) {
            $this->log("Veritabanı yedekleme hatası: " . $e->getMessage(), 'ERROR');
            $stats['database_backup'] = false;
            $stats['database_error'] = $e->getMessage();
        }
    }
    
    private function cleanOldBackups() {
        $backups = glob($this->backupDir . '/*', GLOB_ONLYDIR);
        
        if (count($backups) > $this->maxBackups) {
            // Tarihe göre sırala
            usort($backups, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // En eski backup'ları sil
            $toDelete = array_slice($backups, 0, count($backups) - $this->maxBackups);
            
            foreach ($toDelete as $backup) {
                $this->removeDirectory($backup);
                $this->log("Eski backup silindi: " . basename($backup));
            }
        }
    }
    
    private function removeDirectory($dir) {
        if (!is_dir($dir)) {
            return;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
        
        rmdir($dir);
    }
    
    public function restoreBackup($backupPath) {
        try {
            $this->log("Backup geri yükleniyor: $backupPath");
            
            if (!is_dir($backupPath)) {
                throw new Exception("Backup klasörü bulunamadı: $backupPath");
            }
            
            $backupInfo = $backupPath . '/backup-info.json';
            if (!file_exists($backupInfo)) {
                throw new Exception("Backup bilgileri bulunamadı");
            }
            
            $info = json_decode(file_get_contents($backupInfo), true);
            
            // Dosyaları geri yükle
            $this->restoreFiles($backupPath);
            
            // Veritabanını geri yükle
            if ($info['include_database'] && isset($info['database_backup'])) {
                $this->restoreDatabase($backupPath);
            }
            
            $this->log("Backup geri yüklendi: $backupPath");
            
            return [
                'success' => true,
                'message' => 'Backup başarıyla geri yüklendi'
            ];
            
        } catch (Exception $e) {
            $this->log("Geri yükleme hatası: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function restoreFiles($backupPath) {
        $targetDir = dirname(__DIR__);
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($backupPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $sourcePath = $item->getPathname();
            $relativePath = str_replace($backupPath . DIRECTORY_SEPARATOR, '', $sourcePath);
            $targetPath = $targetDir . DIRECTORY_SEPARATOR . $relativePath;
            
            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                if (!is_dir(dirname($targetPath))) {
                    mkdir(dirname($targetPath), 0755, true);
                }
                copy($sourcePath, $targetPath);
            }
        }
    }
    
    private function restoreDatabase($backupPath) {
        $sqlFile = $backupPath . '/database_backup.sql';
        
        if (!file_exists($sqlFile)) {
            throw new Exception("Veritabanı yedek dosyası bulunamadı");
        }
        
        require_once __DIR__ . '/../config/database.php';
        
        $db = new Database();
        $conn = $db->getConnection();
        
        if (!$conn) {
            throw new Exception("Veritabanı bağlantısı kurulamadı");
        }
        
        $sql = file_get_contents($sqlFile);
        $conn->exec($sql);
        
        $this->log("Veritabanı geri yüklendi: " . basename($sqlFile));
    }
    
    public function listBackups() {
        $backups = glob($this->backupDir . '/*', GLOB_ONLYDIR);
        $backupList = [];
        
        foreach ($backups as $backup) {
            $infoFile = $backup . '/backup-info.json';
            if (file_exists($infoFile)) {
                $info = json_decode(file_get_contents($infoFile), true);
                $info['path'] = $backup;
                $info['created'] = filemtime($backup);
                $backupList[] = $info;
            }
        }
        
        // Tarihe göre sırala (en yeni önce)
        usort($backupList, function($a, $b) {
            return $b['created'] - $a['created'];
        });
        
        return $backupList;
    }
    
    public function getBackupStats() {
        $backups = $this->listBackups();
        $totalSize = 0;
        $totalFiles = 0;
        
        foreach ($backups as $backup) {
            $totalSize += $backup['size_bytes'] ?? 0;
            $totalFiles += $backup['files_count'] ?? 0;
        }
        
        return [
            'total_backups' => count($backups),
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'total_files' => $totalFiles,
            'oldest_backup' => !empty($backups) ? end($backups)['date'] : null,
            'newest_backup' => !empty($backups) ? $backups[0]['date'] : null
        ];
    }
    
    private function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    public function scheduleBackup($interval = 'daily') {
        // Windows Task Scheduler için XML oluştur
        $taskName = "NextCode_AutoBackup";
        $scriptPath = __FILE__;
        
        $xml = '<?xml version="1.0" encoding="UTF-16"?>
<Task version="1.2" xmlns="http://schemas.microsoft.com/windows/2004/02/mit/task">
  <Triggers>
    <CalendarTrigger>
      <StartBoundary>' . date('Y-m-d') . 'T02:00:00</StartBoundary>
      <Enabled>true</Enabled>
      <ScheduleByDay>
        <DaysInterval>1</DaysInterval>
      </ScheduleByDay>
    </CalendarTrigger>
  </Triggers>
  <Principals>
    <Principal id="Author">
      <UserId>S-1-5-18</UserId>
      <RunLevel>LeastPrivilege</RunLevel>
    </Principal>
  </Principals>
  <Settings>
    <MultipleInstancesPolicy>IgnoreNew</MultipleInstancesPolicy>
    <DisallowStartIfOnBatteries>false</DisallowStartIfOnBatteries>
    <StopIfGoingOnBatteries>false</StopIfGoingOnBatteries>
    <AllowHardTerminate>true</AllowHardTerminate>
    <StartWhenAvailable>true</StartWhenAvailable>
    <RunOnlyIfNetworkAvailable>false</RunOnlyIfNetworkAvailable>
    <IdleSettings>
      <StopOnIdleEnd>true</StopOnIdleEnd>
      <RestartOnIdle>false</RestartOnIdle>
    </IdleSettings>
    <AllowStartOnDemand>true</AllowStartOnDemand>
    <Enabled>true</Enabled>
    <Hidden>false</Hidden>
    <RunOnlyIfIdle>false</RunOnlyIfIdle>
    <WakeToRun>false</WakeToRun>
    <ExecutionTimeLimit>PT1H</ExecutionTimeLimit>
    <Priority>7</Priority>
  </Settings>
  <Actions>
    <Exec>
      <Command>php</Command>
      <Arguments>"' . $scriptPath . '"</Arguments>
    </Exec>
  </Actions>
</Task>';
        
        $xmlFile = $this->backupDir . '/backup-task.xml';
        file_put_contents($xmlFile, $xml);
        
        $this->log("Scheduled task XML oluşturuldu: $xmlFile");
        
        return $xmlFile;
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $backup = new AutoBackup();
    
    $action = $argv[1] ?? 'create';
    
    switch ($action) {
        case 'create':
            $result = $backup->createBackup(true, false);
            if ($result['success']) {
                echo "Backup oluşturuldu: " . $result['backup_path'] . "\n";
            } else {
                echo "Backup hatası: " . $result['error'] . "\n";
                exit(1);
            }
            break;
            
        case 'list':
            $backups = $backup->listBackups();
            echo "Mevcut Backup'lar:\n";
            foreach ($backups as $b) {
                echo "- {$b['date']} - {$b['files_count']} dosya - {$b['size_mb']} MB\n";
            }
            break;
            
        case 'stats':
            $stats = $backup->getBackupStats();
            echo "Backup İstatistikleri:\n";
            echo "Toplam Backup: {$stats['total_backups']}\n";
            echo "Toplam Boyut: {$stats['total_size_mb']} MB\n";
            echo "Toplam Dosya: {$stats['total_files']}\n";
            break;
            
        case 'schedule':
            $xmlFile = $backup->scheduleBackup();
            echo "Scheduled task XML oluşturuldu: $xmlFile\n";
            break;
            
        default:
            echo "Kullanım: php auto-backup.php [create|list|stats|schedule]\n";
            break;
    }
}

?>
