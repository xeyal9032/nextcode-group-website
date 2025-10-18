<?php
/**
 * Advanced Error Handling System for NextCode Group
 * Comprehensive error management and logging
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    http_response_code(403);
    exit('Direct access forbidden');
}

class ErrorHandler {
    private static $instance = null;
    private $logFile;
    private $errorTypes = [
        E_ERROR => 'FATAL ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE ERROR',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE ERROR',
        E_CORE_WARNING => 'CORE WARNING',
        E_COMPILE_ERROR => 'COMPILE ERROR',
        E_COMPILE_WARNING => 'COMPILE WARNING',
        E_USER_ERROR => 'USER ERROR',
        E_USER_WARNING => 'USER WARNING',
        E_USER_NOTICE => 'USER NOTICE',
        // E_STRICT deprecated in PHP 8.0
        // E_STRICT => 'STRICT NOTICE',
        E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER DEPRECATED'
    ];
    
    public function __construct() {
        $this->logFile = __DIR__ . '/../logs/error.log';
        $this->createLogDirectory();
        $this->setErrorHandlers();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function createLogDirectory() {
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    private function setErrorHandlers() {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }
    
    public function handleError($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
        $errorType = $this->errorTypes[$severity] ?? 'UNKNOWN';
        $this->logError($errorType, $message, $file, $line);
        
        // Don't execute PHP internal error handler
        return true;
    }
    
    public function handleException($exception) {
        $this->logError(
            'EXCEPTION',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
        
        $this->displayErrorPage($exception);
    }
    
    public function handleShutdown() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->logError(
                'FATAL ERROR',
                $error['message'],
                $error['file'],
                $error['line']
            );
            
            $this->displayErrorPage(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        }
    }
    
    private function logError($type, $message, $file, $line, $trace = null) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        
        $logEntry = sprintf(
            "[%s] %s: %s in %s on line %d | IP: %s | URI: %s | Method: %s | User-Agent: %s",
            $timestamp,
            $type,
            $message,
            $file,
            $line,
            $ip,
            $requestUri,
            $method,
            $userAgent
        );
        
        if ($trace) {
            $logEntry .= "\nStack Trace:\n" . $trace;
        }
        
        $logEntry .= "\n" . str_repeat('-', 80) . "\n";
        
        error_log($logEntry, 3, $this->logFile);
        
        // Also log to system error log for critical errors
        if (in_array($type, ['FATAL ERROR', 'EXCEPTION', 'CORE ERROR', 'COMPILE ERROR'])) {
            error_log("NextCode Error: $message in $file on line $line");
        }
    }
    
    private function displayErrorPage($exception) {
        // Don't display errors in production
        if (defined('PRODUCTION_MODE') && PRODUCTION_MODE) {
            http_response_code(500);
            include __DIR__ . '/../500.php';
            return;
        }
        
        // Development mode - show detailed error
        http_response_code(500);
        echo "<!DOCTYPE html>\n";
        echo "<html><head><title>Error - NextCode Group</title></head><body>\n";
        echo "<h1>Application Error</h1>\n";
        echo "<p><strong>Error:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>\n";
        echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . "</p>\n";
        echo "<p><strong>Line:</strong> " . $exception->getLine() . "</p>\n";
        echo "<h2>Stack Trace:</h2>\n";
        echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>\n";
        echo "</body></html>\n";
    }
    
    public function logCustomError($message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'unknown';
        
        $logEntry = sprintf(
            "[%s] CUSTOM ERROR: %s | IP: %s | URI: %s",
            $timestamp,
            $message,
            $ip,
            $requestUri
        );
        
        if (!empty($context)) {
            $logEntry .= " | Context: " . json_encode($context);
        }
        
        $logEntry .= "\n" . str_repeat('-', 80) . "\n";
        
        error_log($logEntry, 3, $this->logFile);
    }
    
    public function logSecurityEvent($event, $details = '') {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'unknown';
        
        $logEntry = sprintf(
            "[%s] SECURITY EVENT: %s | Details: %s | IP: %s | URI: %s | User-Agent: %s",
            $timestamp,
            $event,
            $details,
            $ip,
            $requestUri,
            $userAgent
        );
        
        $logEntry .= "\n" . str_repeat('-', 80) . "\n";
        
        error_log($logEntry, 3, __DIR__ . '/../logs/security.log');
    }
    
    public function getErrorLog($lines = 100) {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $logContent = file_get_contents($this->logFile);
        $logLines = explode("\n", $logContent);
        $logLines = array_filter($logLines);
        
        return array_slice($logLines, -$lines);
    }
    
    public function clearErrorLog() {
        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
    }
    
    public function getErrorStats() {
        if (!file_exists($this->logFile)) {
            return [
                'total_errors' => 0,
                'error_types' => [],
                'recent_errors' => 0
            ];
        }
        
        $logContent = file_get_contents($this->logFile);
        $logLines = explode("\n", $logContent);
        $logLines = array_filter($logLines);
        
        $stats = [
            'total_errors' => count($logLines),
            'error_types' => [],
            'recent_errors' => 0
        ];
        
        $recentTime = time() - 3600; // Last hour
        
        foreach ($logLines as $line) {
            if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                $logTime = strtotime($matches[1]);
                if ($logTime > $recentTime) {
                    $stats['recent_errors']++;
                }
            }
            
            if (preg_match('/\] ([A-Z\s]+):/', $line, $matches)) {
                $errorType = $matches[1];
                if (!isset($stats['error_types'][$errorType])) {
                    $stats['error_types'][$errorType] = 0;
                }
                $stats['error_types'][$errorType]++;
            }
        }
        
        return $stats;
    }
}

// Global error handling functions
function logError($message, $context = []) {
    ErrorHandler::getInstance()->logCustomError($message, $context);
}

function logSecurityEvent($event, $details = '') {
    ErrorHandler::getInstance()->logSecurityEvent($event, $details);
}

function handleDatabaseError($pdo, $query = '') {
    $errorInfo = $pdo->errorInfo();
    if ($errorInfo[0] !== '00000') {
        logError("Database Error: " . $errorInfo[2], [
            'query' => $query,
            'error_code' => $errorInfo[0],
            'sql_state' => $errorInfo[1]
        ]);
        return true;
    }
    return false;
}

function handleApiError($message, $code = 500, $context = []) {
    logError("API Error: $message", $context);
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => $message,
        'code' => $code,
        'timestamp' => date('c')
    ]);
    exit;
}

function handleValidationError($field, $message) {
    logError("Validation Error: $field - $message", [
        'field' => $field,
        'value' => $_POST[$field] ?? $_GET[$field] ?? 'not provided'
    ]);
    return [
        'field' => $field,
        'message' => $message
    ];
}

// Initialize error handler
ErrorHandler::getInstance();

?>
