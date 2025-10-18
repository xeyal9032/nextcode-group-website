<?php
// Advanced Logger Class for NextCode Group
// Gelişmiş hata loglama ve analiz sistemi

// Define secure access constant
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class AdvancedLogger {
    private $logDir;
    private $logFile;
    private $errorFile;
    private $debugFile;
    private $performanceFile;
    private $enabled;
    
    public function __construct($logDir = null) {
        $this->logDir = $logDir ?: __DIR__ . '/../logs';
        $this->logFile = $this->logDir . '/application.log';
        $this->errorFile = $this->logDir . '/errors.log';
        $this->debugFile = $this->logDir . '/debug.log';
        $this->performanceFile = $this->logDir . '/performance.log';
        $this->enabled = true;
        
        // Log dizinini oluştur
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }
    
    /**
     * Genel log yazma
     */
    public function log($message, $level = 'INFO', $context = []) {
        if (!$this->enabled) return;
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
        
        $logEntry = [
            'timestamp' => $timestamp,
            'level' => $level,
            'message' => $message,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'url' => $url,
            'context' => $context,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
        
        // Kritik seviyede ise error log'a da yaz
        if (in_array($level, ['ERROR', 'CRITICAL', 'EMERGENCY'])) {
            file_put_contents($this->errorFile, $logLine, FILE_APPEND | LOCK_EX);
        }
    }
    
    /**
     * Hata loglama
     */
    public function error($message, $exception = null, $context = []) {
        $context['exception'] = $exception ? [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ] : null;
        
        $this->log($message, 'ERROR', $context);
    }
    
    /**
     * Debug loglama
     */
    public function debug($message, $context = []) {
        $context['backtrace'] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $this->log($message, 'DEBUG', $context);
        
        // Debug dosyasına da yaz
        $debugLine = date('Y-m-d H:i:s') . " [DEBUG] " . $message . " " . json_encode($context) . "\n";
        file_put_contents($this->debugFile, $debugLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Performans loglama
     */
    public function performance($operation, $startTime, $endTime = null, $context = []) {
        $endTime = $endTime ?: microtime(true);
        $duration = ($endTime - $startTime) * 1000; // milisaniye
        
        $context['duration_ms'] = round($duration, 2);
        $context['memory_before'] = $context['memory_before'] ?? 0;
        $context['memory_after'] = memory_get_usage(true);
        $context['memory_peak'] = memory_get_peak_usage(true);
        
        $this->log("Performance: $operation", 'PERFORMANCE', $context);
        
        // Performans dosyasına da yaz
        $perfLine = date('Y-m-d H:i:s') . " [PERF] $operation - {$context['duration_ms']}ms - " . json_encode($context) . "\n";
        file_put_contents($this->performanceFile, $perfLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Veritabanı sorgu loglama
     */
    public function query($sql, $params = [], $duration = null, $context = []) {
        $context['sql'] = $sql;
        $context['params'] = $params;
        $context['duration'] = $duration;
        
        $this->log("Database Query: " . substr($sql, 0, 100) . "...", 'QUERY', $context);
    }
    
    /**
     * Email gönderim loglama
     */
    public function email($to, $subject, $success, $error = null, $context = []) {
        $context['to'] = $to;
        $context['subject'] = $subject;
        $context['success'] = $success;
        $context['error'] = $error;
        
        $level = $success ? 'INFO' : 'ERROR';
        $message = $success ? "Email sent successfully" : "Email sending failed";
        
        $this->log($message, $level, $context);
    }
    
    /**
     * API çağrı loglama
     */
    public function api($endpoint, $method, $status, $duration = null, $context = []) {
        $context['endpoint'] = $endpoint;
        $context['method'] = $method;
        $context['status'] = $status;
        $context['duration'] = $duration;
        
        $level = $status >= 400 ? 'ERROR' : 'INFO';
        $message = "API Call: $method $endpoint - Status: $status";
        
        $this->log($message, $level, $context);
    }
    
    /**
     * Güvenlik olayı loglama
     */
    public function security($event, $details = [], $severity = 'MEDIUM') {
        $context = [
            'event' => $event,
            'details' => $details,
            'severity' => $severity,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'referer' => $_SERVER['HTTP_REFERER'] ?? 'unknown'
        ];
        
        $level = match($severity) {
            'LOW' => 'INFO',
            'MEDIUM' => 'WARNING',
            'HIGH' => 'ERROR',
            'CRITICAL' => 'CRITICAL',
            default => 'WARNING'
        };
        
        $this->log("Security Event: $event", $level, $context);
    }
    
    /**
     * Log dosyalarını temizle
     */
    public function cleanup($days = 30) {
        $files = [
            $this->logFile,
            $this->errorFile,
            $this->debugFile,
            $this->performanceFile
        ];
        
        $cutoff = time() - ($days * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if (file_exists($file) && filemtime($file) < $cutoff) {
                // Eski logları arşivle
                $archiveFile = $file . '.' . date('Y-m-d', $cutoff) . '.archive';
                rename($file, $archiveFile);
                
                // Arşiv dosyasını sıkıştır
                if (function_exists('gzopen')) {
                    $gz = gzopen($archiveFile . '.gz', 'w9');
                    gzwrite($gz, file_get_contents($archiveFile));
                    gzclose($gz);
                    unlink($archiveFile);
                }
            }
        }
    }
    
    /**
     * Log istatistikleri
     */
    public function getStats($hours = 24) {
        $stats = [
            'total_logs' => 0,
            'errors' => 0,
            'warnings' => 0,
            'debugs' => 0,
            'performance_entries' => 0,
            'top_errors' => [],
            'performance_avg' => 0
        ];
        
        $cutoff = time() - ($hours * 60 * 60);
        
        if (file_exists($this->logFile)) {
            $lines = file($this->logFile, FILE_IGNORE_NEW_LINES);
            
            foreach ($lines as $line) {
                $log = json_decode($line, true);
                if ($log && strtotime($log['timestamp']) > $cutoff) {
                    $stats['total_logs']++;
                    
                    switch ($log['level']) {
                        case 'ERROR':
                        case 'CRITICAL':
                        case 'EMERGENCY':
                            $stats['errors']++;
                            break;
                        case 'WARNING':
                            $stats['warnings']++;
                            break;
                        case 'DEBUG':
                            $stats['debugs']++;
                            break;
                        case 'PERFORMANCE':
                            $stats['performance_entries']++;
                            break;
                    }
                }
            }
        }
        
        return $stats;
    }
    
    /**
     * Logger'ı etkinleştir/devre dışı bırak
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }
    
    /**
     * Log dosyası yolu
     */
    public function getLogFile() {
        return $this->logFile;
    }
}

// Global logger instance
$GLOBALS['logger'] = new AdvancedLogger();

// Helper fonksiyonlar
function logInfo($message, $context = []) {
    global $logger;
    $logger->log($message, 'INFO', $context);
}

function logError($message, $exception = null, $context = []) {
    global $logger;
    $logger->error($message, $exception, $context);
}

function logDebug($message, $context = []) {
    global $logger;
    $logger->debug($message, $context);
}

function logPerformance($operation, $startTime, $endTime = null, $context = []) {
    global $logger;
    $logger->performance($operation, $startTime, $endTime, $context);
}

function logQuery($sql, $params = [], $duration = null, $context = []) {
    global $logger;
    $logger->query($sql, $params, $duration, $context);
}

function logEmail($to, $subject, $success, $error = null, $context = []) {
    global $logger;
    $logger->email($to, $subject, $success, $error, $context);
}

function logSecurity($event, $details = [], $severity = 'MEDIUM') {
    global $logger;
    $logger->security($event, $details, $severity);
}

// Error handler
set_error_handler(function($severity, $message, $file, $line) {
    global $logger;
    $exception = new ErrorException($message, 0, $severity, $file, $line);
    $logger->error("PHP Error: $message", $exception, [
        'severity' => $severity,
        'file' => $file,
        'line' => $line
    ]);
});

// Exception handler
set_exception_handler(function($exception) {
    global $logger;
    $logger->error("Uncaught Exception: " . $exception->getMessage(), $exception);
    
    // Production'da detaylı hata gösterme
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error']);
    } else {
        echo "Uncaught Exception: " . $exception->getMessage();
    }
});

// Shutdown handler
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        global $logger;
        $exception = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
        $logger->error("Fatal Error: " . $error['message'], $exception);
    }
});
?>
