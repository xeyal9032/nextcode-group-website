<?php
/**
 * Enhanced Input Validation
 * Güçlendirilmiş input validation sistemi
 */

class EnhancedInputValidator {
    private $errors = [];
    private $rules = [];
    private $sanitizedData = [];
    
    public function __construct() {
        $this->setupDefaultRules();
    }
    
    /**
     * Varsayılan validation kuralları
     */
    private function setupDefaultRules() {
        $this->rules = [
            'required' => function($value) {
                return !empty(trim($value));
            },
            'email' => function($value) {
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            },
            'url' => function($value) {
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            },
            'phone' => function($value) {
                return preg_match('/^[\+]?[0-9\s\-\(\)]{10,}$/', $value);
            },
            'numeric' => function($value) {
                return is_numeric($value);
            },
            'integer' => function($value) {
                return filter_var($value, FILTER_VALIDATE_INT) !== false;
            },
            'float' => function($value) {
                return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
            },
            'boolean' => function($value) {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
            },
            'date' => function($value) {
                $d = DateTime::createFromFormat('Y-m-d', $value);
                return $d && $d->format('Y-m-d') === $value;
            },
            'datetime' => function($value) {
                $d = DateTime::createFromFormat('Y-m-d H:i:s', $value);
                return $d && $d->format('Y-m-d H:i:s') === $value;
            },
            'ip' => function($value) {
                return filter_var($value, FILTER_VALIDATE_IP) !== false;
            },
            'mac' => function($value) {
                return filter_var($value, FILTER_VALIDATE_MAC) !== false;
            },
            'min_length' => function($value, $min) {
                return strlen($value) >= $min;
            },
            'max_length' => function($value, $max) {
                return strlen($value) <= $max;
            },
            'min_value' => function($value, $min) {
                return $value >= $min;
            },
            'max_value' => function($value, $max) {
                return $value <= $max;
            },
            'regex' => function($value, $pattern) {
                return preg_match($pattern, $value);
            },
            'in' => function($value, $array) {
                return in_array($value, $array);
            },
            'not_in' => function($value, $array) {
                return !in_array($value, $array);
            },
            'file' => function($value) {
                return is_uploaded_file($value);
            },
            'image' => function($value) {
                if (!is_uploaded_file($value)) return false;
                $imageInfo = getimagesize($value);
                return $imageInfo !== false;
            },
            'mimes' => function($value, $allowedMimes) {
                if (!is_uploaded_file($value)) return false;
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $value);
                finfo_close($finfo);
                return in_array($mimeType, $allowedMimes);
            },
            'max_size' => function($value, $maxSize) {
                if (!is_uploaded_file($value)) return false;
                return filesize($value) <= $maxSize;
            },
            'alpha' => function($value) {
                return preg_match('/^[a-zA-Z\s]+$/', $value);
            }
        ];
    }
    
    /**
     * Validation kuralı ekle
     */
    public function addRule($name, $callback) {
        $this->rules[$name] = $callback;
    }
    
    /**
     * Veri doğrula
     */
    public function validate($data, $rules) {
        $this->errors = [];
        $this->sanitizedData = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';
            
            // Sanitize et
            $this->sanitizedData[$field] = $this->sanitize($value, $fieldRules);
            
            // Validation kurallarını uygula
            $this->validateField($field, $this->sanitizedData[$field], $fieldRules);
        }
        
        return empty($this->errors);
    }
    
    /**
     * Alan doğrula
     */
    private function validateField($field, $value, $rules) {
        foreach ($rules as $rule) {
            if (is_array($rule)) {
                $ruleName = $rule[0];
                $params = array_slice($rule, 1);
            } else {
                $ruleName = $rule;
                $params = [];
            }
            
            if (!isset($this->rules[$ruleName])) {
                $this->errors[$field][] = "Unknown validation rule: {$ruleName}";
                continue;
            }
            
            $callback = $this->rules[$ruleName];
            $isValid = $callback($value, ...$params);
            
            if (!$isValid) {
                $this->errors[$field][] = $this->getErrorMessage($ruleName, $field, $params);
            }
        }
    }
    
    /**
     * Hata mesajı oluştur
     */
    private function getErrorMessage($rule, $field, $params) {
        $messages = [
            'required' => "{$field} alanı zorunludur",
            'email' => "{$field} geçerli bir email adresi olmalıdır",
            'url' => "{$field} geçerli bir URL olmalıdır",
            'phone' => "{$field} geçerli bir telefon numarası olmalıdır",
            'numeric' => "{$field} sayısal bir değer olmalıdır",
            'integer' => "{$field} tam sayı olmalıdır",
            'float' => "{$field} ondalık sayı olmalıdır",
            'boolean' => "{$field} true/false değeri olmalıdır",
            'date' => "{$field} geçerli bir tarih olmalıdır (YYYY-MM-DD)",
            'datetime' => "{$field} geçerli bir tarih-saat olmalıdır (YYYY-MM-DD HH:MM:SS)",
            'ip' => "{$field} geçerli bir IP adresi olmalıdır",
            'mac' => "{$field} geçerli bir MAC adresi olmalıdır",
            'min_length' => "{$field} en az {$params[0]} karakter olmalıdır",
            'max_length' => "{$field} en fazla {$params[0]} karakter olmalıdır",
            'min_value' => "{$field} en az {$params[0]} olmalıdır",
            'max_value' => "{$field} en fazla {$params[0]} olmalıdır",
            'regex' => "{$field} geçerli format değil",
            'in' => "{$field} geçerli değerlerden biri olmalıdır: " . implode(', ', $params[0]),
            'not_in' => "{$field} yasaklı değerlerden biri olamaz: " . implode(', ', $params[0]),
            'file' => "{$field} geçerli bir dosya olmalıdır",
            'image' => "{$field} geçerli bir resim dosyası olmalıdır",
            'mimes' => "{$field} izin verilen dosya türlerinden biri olmalıdır: " . implode(', ', $params[0]),
            'max_size' => "{$field} dosya boyutu en fazla " . $this->formatBytes($params[0]) . " olmalıdır"
        ];
        
        return $messages[$rule] ?? "{$field} geçersiz";
    }
    
    /**
     * Veri sanitize et
     */
    private function sanitize($value, $rules) {
        // Temel sanitization
        $value = trim($value);
        
        // HTML etiketlerini kaldır
        if (in_array('html', $rules)) {
            $value = strip_tags($value);
        }
        
        // HTML karakterlerini encode et
        if (in_array('escape', $rules)) {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        
        // Sadece alfanumerik karakterler
        if (in_array('alpha', $rules)) {
            $value = preg_replace('/[^a-zA-Z0-9]/', '', $value);
        }
        
        // Sadece harfler
        if (in_array('alpha_only', $rules)) {
            $value = preg_replace('/[^a-zA-Z]/', '', $value);
        }
        
        // Sadece sayılar
        if (in_array('numeric_only', $rules)) {
            $value = preg_replace('/[^0-9]/', '', $value);
        }
        
        // Email için
        if (in_array('email', $rules)) {
            $value = filter_var($value, FILTER_SANITIZE_EMAIL);
        }
        
        // URL için
        if (in_array('url', $rules)) {
            $value = filter_var($value, FILTER_SANITIZE_URL);
        }
        
        // Sayısal değerler için
        if (in_array('numeric', $rules)) {
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        }
        
        return $value;
    }
    
    /**
     * Hataları al
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Sanitize edilmiş veriyi al
     */
    public function getSanitizedData() {
        return $this->sanitizedData;
    }
    
    /**
     * Belirli alan için hataları al
     */
    public function getFieldErrors($field) {
        return $this->errors[$field] ?? [];
    }
    
    /**
     * Hata var mı kontrol et
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Belirli alan için hata var mı kontrol et
     */
    public function hasFieldError($field) {
        return !empty($this->errors[$field]);
    }
    
    /**
     * Dosya boyutunu formatla
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * XSS koruması
     */
    public function preventXSS($value) {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * SQL injection koruması
     */
    public function preventSQLInjection($value) {
        // Prepared statements kullanılmalı, bu sadece ekstra koruma
        return addslashes($value);
    }
    
    /**
     * CSRF token doğrula
     */
    public function validateCSRFToken($token) {
        if (empty($token)) {
            return false;
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Rate limiting kontrolü
     */
    public function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 300) {
        $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($identifier);
        
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data && $data['time'] > time() - $timeWindow) {
                if ($data['attempts'] >= $maxAttempts) {
                    return false;
                }
                $data['attempts']++;
            } else {
                $data = ['attempts' => 1, 'time' => time()];
            }
        } else {
            $data = ['attempts' => 1, 'time' => time()];
        }
        
        file_put_contents($cacheFile, json_encode($data));
        return true;
    }
    
    /**
     * Validation raporu oluştur
     */
    public function generateValidationReport() {
        $report = "=== VALIDATION RAPORU ===\n\n";
        
        if (empty($this->errors)) {
            $report .= "Tüm validasyonlar başarılı!\n";
        } else {
            $report .= "Validation Hataları:\n";
            foreach ($this->errors as $field => $fieldErrors) {
                $report .= "\n{$field}:\n";
                foreach ($fieldErrors as $error) {
                    $report .= "  - {$error}\n";
                }
            }
        }
        
        $report .= "\nSanitize Edilmiş Veri:\n";
        foreach ($this->sanitizedData as $field => $value) {
            $report .= "  {$field}: " . (is_string($value) ? $value : json_encode($value)) . "\n";
        }
        
        return $report;
    }
}

// Kullanım örneği
if (php_sapi_name() === 'cli') {
    $validator = new EnhancedInputValidator();
    
    // Test verisi
    $testData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'age' => '25',
        'website' => 'https://example.com'
    ];
    
    // Validation kuralları
    $rules = [
        'name' => ['required', ['min_length', 2], ['max_length', 50], 'alpha'],
        'email' => ['required', 'email'],
        'phone' => ['required', 'phone'],
        'age' => ['required', 'integer', ['min_value', 18], ['max_value', 100]],
        'website' => ['url']
    ];
    
    // Validation çalıştır
    $isValid = $validator->validate($testData, $rules);
    
    echo "Validation Test Completed!\n";
    echo "Is Valid: " . ($isValid ? 'Yes' : 'No') . "\n";
    
    if (!$isValid) {
        echo "Errors:\n";
        foreach ($validator->getErrors() as $field => $errors) {
            echo "  {$field}: " . implode(', ', $errors) . "\n";
        }
    }
    
    echo "Sanitized Data:\n";
    foreach ($validator->getSanitizedData() as $field => $value) {
        echo "  {$field}: {$value}\n";
    }
}
?>
