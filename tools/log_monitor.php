<?php
/**
 * Log Monitor Dashboard
 * Log dosyalarını gerçek zamanlı izleme ve analiz
 */

define('SECURE_ACCESS', true);

class LogMonitor {
    private $logDir;
    
    public function __construct($logDir = null) {
        $this->logDir = $logDir ?: __DIR__ . '/../logs/';
    }
    
    /**
     * Log dosyalarının genel durumunu analiz et
     */
    public function analyzeLogs() {
        $analysis = [
            'total_files' => 0,
            'total_size' => 0,
            'error_count' => 0,
            'warning_count' => 0,
            'files_analysis' => [],
            'recent_errors' => [],
            'performance_issues' => [],
            'security_events' => []
        ];
        
        if (!is_dir($this->logDir)) {
            return $analysis;
        }
        
        $files = glob($this->logDir . '*.log');
        $analysis['total_files'] = count($files);
        
        foreach ($files as $file) {
            $fileAnalysis = $this->analyzeFile($file);
            $analysis['files_analysis'][] = $fileAnalysis;
            $analysis['total_size'] += $fileAnalysis['size'];
            $analysis['error_count'] += $fileAnalysis['error_count'];
            $analysis['warning_count'] += $fileAnalysis['warning_count'];
            
            // Son hataları topla
            if (!empty($fileAnalysis['recent_errors'])) {
                $analysis['recent_errors'] = array_merge(
                    $analysis['recent_errors'], 
                    $fileAnalysis['recent_errors']
                );
            }
            
            // Performans sorunlarını topla
            if (!empty($fileAnalysis['performance_issues'])) {
                $analysis['performance_issues'] = array_merge(
                    $analysis['performance_issues'], 
                    $fileAnalysis['performance_issues']
                );
            }
            
            // Güvenlik olaylarını topla
            if (!empty($fileAnalysis['security_events'])) {
                $analysis['security_events'] = array_merge(
                    $analysis['security_events'], 
                    $fileAnalysis['security_events']
                );
            }
        }
        
        // Son hataları tarihe göre sırala
        usort($analysis['recent_errors'], function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return $analysis;
    }
    
    /**
     * Tek dosyayı analiz et
     */
    private function analyzeFile($filePath) {
        $fileName = basename($filePath);
        $size = filesize($filePath);
        $modified = filemtime($filePath);
        
        $analysis = [
            'name' => $fileName,
            'size' => $size,
            'size_formatted' => $this->formatBytes($size),
            'modified' => $modified,
            'age_hours' => round((time() - $modified) / 3600, 1),
            'lines_count' => 0,
            'error_count' => 0,
            'warning_count' => 0,
            'recent_errors' => [],
            'performance_issues' => [],
            'security_events' => []
        ];
        
        if (!file_exists($filePath) || $size === 0) {
            return $analysis;
        }
        
        // Son 1000 satırı oku
        $lines = $this->getLastLines($filePath, 1000);
        $analysis['lines_count'] = count($lines);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Hata sayısını artır
            if (preg_match('/ERROR|FATAL|CRITICAL/i', $line)) {
                $analysis['error_count']++;
                $analysis['recent_errors'][] = [
                    'message' => $line,
                    'timestamp' => $this->extractTimestamp($line),
                    'file' => $fileName
                ];
            }
            
            // Uyarı sayısını artır
            if (preg_match('/WARNING|WARN/i', $line)) {
                $analysis['warning_count']++;
            }
            
            // Performans sorunlarını tespit et
            if (preg_match('/slow|timeout|performance|memory/i', $line)) {
                $analysis['performance_issues'][] = [
                    'message' => $line,
                    'timestamp' => $this->extractTimestamp($line),
                    'file' => $fileName
                ];
            }
            
            // Güvenlik olaylarını tespit et
            if (preg_match('/SECURITY|LOGIN|ATTACK|INTRUSION/i', $line)) {
                $analysis['security_events'][] = [
                    'message' => $line,
                    'timestamp' => $this->extractTimestamp($line),
                    'file' => $fileName
                ];
            }
        }
        
        // Son 10 hatayı tut
        $analysis['recent_errors'] = array_slice($analysis['recent_errors'], -10);
        
        return $analysis;
    }
    
    /**
     * Dosyanın son N satırını oku
     */
    private function getLastLines($filePath, $lines = 100) {
        $file = new SplFileObject($filePath);
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key() + 1;
        
        $startLine = max(0, $totalLines - $lines);
        $file->seek($startLine);
        
        $result = [];
        while (!$file->eof()) {
            $line = $file->current();
            if (trim($line)) {
                $result[] = $line;
            }
            $file->next();
        }
        
        return $result;
    }
    
    /**
     * Satırdan timestamp çıkar
     */
    private function extractTimestamp($line) {
        if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
            return $matches[1];
        }
        return date('Y-m-d H:i:s');
    }
    
    /**
     * Byte'ları formatla
     */
    private function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Log durumu özeti
     */
    public function getStatusSummary() {
        $analysis = $this->analyzeLogs();
        
        $status = 'healthy';
        $issues = [];
        
        // Toplam boyut kontrolü (50MB üzeri)
        if ($analysis['total_size'] > 50 * 1024 * 1024) {
            $status = 'warning';
            $issues[] = 'Log dosyaları çok büyük (' . $this->formatBytes($analysis['total_size']) . ')';
        }
        
        // Hata sayısı kontrolü (son 24 saatte 100+ hata)
        if ($analysis['error_count'] > 100) {
            $status = 'critical';
            $issues[] = 'Çok fazla hata tespit edildi (' . $analysis['error_count'] . ' hata)';
        }
        
        // Güvenlik olayları kontrolü
        if (!empty($analysis['security_events'])) {
            $status = 'warning';
            $issues[] = 'Güvenlik olayları tespit edildi (' . count($analysis['security_events']) . ' olay)';
        }
        
        return [
            'status' => $status,
            'issues' => $issues,
            'summary' => $analysis
        ];
    }
}

// Web arayüzü
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $monitor = new LogMonitor();
    
    switch ($_GET['action']) {
        case 'analyze':
            echo json_encode($monitor->analyzeLogs());
            break;
            
        case 'status':
            echo json_encode($monitor->getStatusSummary());
            break;
            
        default:
            echo json_encode(['error' => 'Geçersiz işlem']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Monitor - NextCode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-healthy { color: #10b981; }
        .status-warning { color: #f59e0b; }
        .status-critical { color: #ef4444; }
        .log-entry { font-family: monospace; font-size: 12px; }
        .metric-card { background: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="my-4"><i class="fas fa-chart-line"></i> Log Monitor Dashboard</h1>
                
                <div class="row" id="metricsContainer">
                    <!-- Metrikler buraya yüklenecek -->
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-exclamation-triangle"></i> Son Hatalar</h5>
                            </div>
                            <div class="card-body">
                                <div id="recentErrors">
                                    <!-- Hatalar buraya yüklenecek -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-shield-alt"></i> Güvenlik Olayları</h5>
                            </div>
                            <div class="card-body">
                                <div id="securityEvents">
                                    <!-- Güvenlik olayları buraya yüklenecek -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-file-alt"></i> Log Dosyaları Analizi</h5>
                            </div>
                            <div class="card-body">
                                <div id="filesAnalysis">
                                    <!-- Dosya analizleri buraya yüklenecek -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function loadLogAnalysis() {
            try {
                const response = await fetch('log_monitor.php?action=analyze');
                const data = await response.json();
                
                updateMetrics(data);
                updateRecentErrors(data.recent_errors);
                updateSecurityEvents(data.security_events);
                updateFilesAnalysis(data.files_analysis);
                
            } catch (error) {
                console.error('Log analizi yüklenirken hata:', error);
            }
        }
        
        function updateMetrics(data) {
            const container = document.getElementById('metricsContainer');
            container.innerHTML = `
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <h3 class="text-primary">${data.total_files}</h3>
                        <p class="mb-0">Toplam Log Dosyası</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <h3 class="text-info">${data.total_size_formatted || '0 B'}</h3>
                        <p class="mb-0">Toplam Boyut</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <h3 class="text-danger">${data.error_count}</h3>
                        <p class="mb-0">Toplam Hata</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <h3 class="text-warning">${data.warning_count}</h3>
                        <p class="mb-0">Toplam Uyarı</p>
                    </div>
                </div>
            `;
        }
        
        function updateRecentErrors(errors) {
            const container = document.getElementById('recentErrors');
            if (errors.length === 0) {
                container.innerHTML = '<p class="text-success">Son hata bulunamadı!</p>';
                return;
            }
            
            container.innerHTML = errors.slice(0, 5).map(error => `
                <div class="log-entry mb-2 p-2 bg-light rounded">
                    <small class="text-muted">${error.timestamp}</small><br>
                    <span class="text-danger">${error.message}</span>
                </div>
            `).join('');
        }
        
        function updateSecurityEvents(events) {
            const container = document.getElementById('securityEvents');
            if (events.length === 0) {
                container.innerHTML = '<p class="text-success">Güvenlik olayı bulunamadı!</p>';
                return;
            }
            
            container.innerHTML = events.slice(0, 5).map(event => `
                <div class="log-entry mb-2 p-2 bg-warning bg-opacity-10 rounded">
                    <small class="text-muted">${event.timestamp}</small><br>
                    <span class="text-warning">${event.message}</span>
                </div>
            `).join('');
        }
        
        function updateFilesAnalysis(files) {
            const container = document.getElementById('filesAnalysis');
            container.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Dosya Adı</th>
                                <th>Boyut</th>
                                <th>Yaş (saat)</th>
                                <th>Satır Sayısı</th>
                                <th>Hata Sayısı</th>
                                <th>Uyarı Sayısı</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${files.map(file => `
                                <tr>
                                    <td>${file.name}</td>
                                    <td>${file.size_formatted}</td>
                                    <td>${file.age_hours}</td>
                                    <td>${file.lines_count}</td>
                                    <td class="text-danger">${file.error_count}</td>
                                    <td class="text-warning">${file.warning_count}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        }
        
        // Sayfa yüklendiğinde analizi başlat
        document.addEventListener('DOMContentLoaded', function() {
            loadLogAnalysis();
            
            // Her 30 saniyede bir güncelle
            setInterval(loadLogAnalysis, 30000);
        });
    </script>
</body>
</html>

