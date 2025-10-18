<?php
// NextCode Group - Enhanced Input Validation System
// Comprehensive input sanitization and validation

if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

class InputValidator {
    private $rules = [];
    private $errors = [];
    
    /**
     * Validate input data against rules
     */
    public function validate($data, $rules) {
        $this->rules = $rules;
        $this->errors = [];
        $validatedData = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $validatedData[$field] = $this->validateField($field, $value, $fieldRules);
        }
        
        return [
            'valid' => empty($this->errors),
            'data' => $validatedData,
            'errors' => $this->errors
        ];
    }
    
    /**
     * Validate individual field
     */
    private function validateField($field, $value, $rules) {
        foreach ($rules as $rule) {
            $result = $this->applyRule($field, $value, $rule);
            
            if (!$result['valid']) {
                $this->errors[$field] = $result['message'];
                break;
            }
            
            $value = $result['value'];
        }
        
        return $value;
    }
    
    /**
     * Apply validation rule
     */
    private function applyRule($field, $value, $rule) {
        if (is_string($rule)) {
            $method = 'rule_' . $rule;
        } else {
            $method = 'rule_' . $rule['type'];
            $params = $rule['params'] ?? [];
        }
        
        if (method_exists($this, $method)) {
            if (is_string($rule)) {
                return $this->$method($field, $value);
            } else {
                return $this->$method($field, $value, $params);
            }
        }
        
        return ['valid' => true, 'value' => $value];
    }
    
    // Validation rules
    private function rule_required($field, $value) {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return ['valid' => false, 'message' => ucfirst($field) . ' is required'];
        }
        return ['valid' => true, 'value' => $value];
    }
    
    private function rule_email($field, $value) {
        $value = trim($value);
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => ucfirst($field) . ' must be a valid email address'];
        }
        
        // Additional email security checks
        if (strlen($value) > 255) {
            return ['valid' => false, 'message' => ucfirst($field) . ' must not exceed 255 characters'];
        }
        
        // Check for suspect patterns
        $suspiciousParts = ['..', './', '\\', '<', '>'];
        foreach ($suspiciousParts as $suspicious) {
            if (strpos($value, $suspicious) !== false) {
                return ['valid' => false, 'message' => ucfirst($field) . ' contains invalid characters'];
            }
        }
        
        return ['valid' => true, 'value' => strtolower($value)];
    }
    
    private function rule_min_length($field, $value, $params = []) {
        $min = $params[0] ?? 1;
        if (strlen($value) < $min) {
            return ['valid' => false, 'message' => ucfirst($field) . " must be at least $min characters long"];
        }
        return ['valid' => true, 'value' => $value];
    }
    
    private function rule_max_length($field, $value, $params = []) {
        $max = $params[0] ?? 255;
        if (strlen($value) > $max) {
            return ['valid' => false, 'message' => ucfirst($field) . " must not exceed $max characters"];
        }
        return ['valid' => true, 'value' => $value];
    }
    
    private function rule_min($field, $value, $params = []) {
        $min = $params[0] ?? 0;
        if ($value < $min) {
            return ['valid' => false, 'message' => ucfirst($field) . " must be at least $min"];
        }
        return ['valid' => true, 'value' => $value];
    }
    
    private function rule_max($field, $value, $params = []) {
        $max = $params[0] ?? 999999;
        if ($value > $max) {
            return called ['valid' => false, 'message' => ucfirst($field) . " must not exceed $max"];
        }
        return ['valid' => true, 'value' => $value];
    }
    
    private function rule_integer($field, $value) {
        if (!is_numeric($value) || strpos($value, '.') !== false) {
            return ['valid' => false, 'message' => ucfirst($field) . ' must be an integer'];
        }
        return ['valid' => true, 'value' => (int)$value];
    }
    
    private function rule_numeric($field, $value) {
        if (!is_numeric($value)) {
            return ['valid' => false, 'message' => ucfirst($field) . ' must be a number'];
        }
        return ['valid' => true, 'value' => (float)$value];
    }
    
    private function rule_phone($field, $value) {
        $value = preg_replace('/[^0-9+\-\(\)]/', '', $value);
        
        // Basic phone validation patterns
        $patterns = [
            '/^\+?[1-9]\d{1,14}$/',  // International format
            '/^\(\d{3}\)\s?\d{3}-\d{4}$/',  // US format
            '/^\d{3}-\d{3}-\d{4}$/',  // Simple format
        ];
        
        $valid = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                $valid = true;
                break;
            }
        }
        
        if (!$valid) {
            return ['valid' => false, 'message' => ucfirst($field) . ' must be a valid phone number'];
        }
        
        return ['valid' => true, 'value' => $value];
    }
    
    private function rule_alpha($field, $value) {
        if (!ctype_alpha(str_replace(' ', '', $value))) {
            return ['valid' => false, 'message' => ucfirst($field) . ' can only contain letters'];
        }
        return ['valid' => true, 'value' => trim($value)];
    }
    
    private function rule_alphanumeric($field, $value) {
        if (!ctype_alnum(str_replace(' ', '', $value))) {
            return ['valid' => false, 'message' => ucfirst($field) . ' can only contain letters and numbers'];
        }
        return ['valid' => true, 'value' => trim($value)];
    }
    
    private function rule_url($field, $value) {
        $value = trim($value);
        
        // Basic URL validation
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return ['valid' => false, 'message' => ucfirst($field) . ' must be a valid URL'];
        }
        
        // Check for suspicious protocols
        $allowedProtocols = ['http', 'https'];
        $parsed = parse_url($value);
        if (!in_array($parsed['scheme'], $allowedProtocols)) {
            return ['valid' => false, 'message' => ucfirst($field) . ' must use HTTP or HTTPS protocol'];
        }
        
        return ['valid' => true, 'value' => $value];
    }
    
    private function rule_date($field, $value) {
        $value = trim($value);
        $timestamp = strtotime($value);
        
        if ($timestamp === false) {
            return ['valid' => false, 'message' => ucfirst($field) . ' must be a valid date'];
        }
        
        return ['valid' => true, 'value' => date('Y-m-d', $timestamp)];
    }
    
    private function rule_datetime($field, $value) {
        $value = trim($value);
        $timestamp = strtotime($value);
        
        if ($timestamp === false) {
            return ['valid' => false, 'message' => ucfirst($field) . ' must be a valid date and time'];
        }
        
        return ['valid' => true, 'value' => date('Y-m-d H:i:s', $timestamp)];
    }
    
    private function rule_sanitize_text($field, $value) {
        // Remove HTML tags and sanitize
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        
        // Remove control characters
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        return ['valid' => true, 'value' => trim($value)];
    }
    
    private function rule_sanitize_html($field, $value) {
        // Allow only safe HTML tags
        $allowedTags = '<p><br><strong><em><u><ol><ul><li><a><img>';
        $value = strip_tags($value, $allowedTags);
        
        // Sanitize attributes
        $value = preg_replace('/<(?!\/?(?:' . implode('|', ['p', 'br', 'strong', 'em', 'u', 'ol', 'ul', 'li', 'a', 'img']) . ')\b)[^>]*>/', '', $value);
        
        return ['valid' => true, 'value' => trim($value)];
    }
    
    private function rule_csrf($field, $value) {
        require_once __DIR__ . '/security.php';
        
        if (!verify_csrf_token($value)) {
            return ['valid' => false, 'message' => 'Invalid security token'];
        }
        
        return ['valid' => true, 'value' => $value];
    }
    
    private function rule_not_empty($field, $value) {
        if (empty($value)) {
            return ['valid' => false, 'message' => ucfirst($field) . ' cannot be empty'];
        }
        return ['valid' => true, 'value' => $value];
    }
    
    /**
     * Validate file upload
     */
    public function validateFile($file, $rules = []) {
        $errors = [];
        
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'message' => 'No file uploaded'];
        }
        
        // File size check
        $maxSize = $rules['max_size'] ?? 5242880; // 5MB default
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }
        
        // File type check
        $allowedTypes = $rules['allowed_types'] ?? ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'File type not allowed';
        }
        
        // MIME type check
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimeTypes = $rules['allowed_mime_types'] ?? [];
        if (!empty($allowedMimeTypes) && !in_array($mimeType, $allowedMimeTypes)) {
            $errors[] = 'Invalid file type detected';
        }
        
        // Security scan
        if (strpos($file['name'], '.php') !== false || strpos($file['name'], '..') !== false) {
            $errors[] = 'File name contains invalid characters';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'safe_name' => $this->sanitizeFileName($file['name'])
        ];
    }
    
    /**
     * Sanitize file name
     */
    private function sanitizeFileName($fileName) {
        $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
        $fileName = preg_replace('/_{2,}/', '_', $fileName);
        return trim($fileName, '_');
    }
}

// Global validator instance
$validator = new InputValidator();

// Helper functions for common validation tasks
function validate_contact_form($data) {
    global $validator;
    
    $rules = [
        'firstName' => ['required', 'alpha', 'min_length' => [2], 'max_length' => [50]],
        'lastName' => ['required', 'alpha', 'min_length' => [2], 'max_length' => [50]],
        'email' => ['required', 'email', 'max_length' => [255]],
        'phone' => ['phone', 'max_length' => [20]],
        'subject' => ['required', 'sanitize_text', 'min_length' => [5], 'max_length' => [200]],
        'message' => ['required', 'sanitize_html', 'min_length' => [10], 'max_length' => [2000]],
        'csrf_token' => ['csrf']
    ];
    
    return $validator->validate($data, $rules);
}

function validate_blog_post($data) {
    global $validator;
    
    $rules = [
        'title' => ['required', 'sanitize_text', 'min_length' => [10], 'max_length' => [255]],
        'content' => ['required', 'sanitize_html', 'min_length' => [100]],
        'excerpt' => ['sanitize_text', 'max_length' => [500]],
        'category_id' => ['integer', 'min' => [1]],
        'meta_title' => ['sanitize_text', 'max_length' => [60]],
        'meta_description' => ['sanitize_text', 'max_length' => [160]],
        'tags' => ['sanitize_text', 'max_length' => [255]]
    ];
    
    return $validator->validate($data, $rules);
}

function validate_user_signup($data) {
    global $validator;
    
    $rules = [
        'username' => ['required', 'alphanumeric', 'min_length' => [3], 'max_length' => [20]],
        'email' => ['required', 'email', 'max_length' => [255]],
        'password' => ['required', 'min_length' => [8]],
        'confirm_password' => ['required', 'matches:password'],
        'full_name' => ['required', 'alpha', 'min_length' => [2], 'max_length' => [100]],
        'agree_terms' => ['required']
    ];
    
    return $validator->validate($data, $rules);
}

// Enhanced sanitization function
function enhanced_sanitize_input($data, $type = 'string') {
    if (is_array($data)) {
        return array_map(function($item) use ($type) {
            return enhanced_sanitize_input($item, $type);
        }, $data);
    }
    
    switch ($type) {
        case 'string':
            return htmlspecialchars(trim(strip_tags($data)), ENT_QUOTES, 'UTF-8');
        case 'html':
            return strip_tags($data, '<p><br><strong><em><u><ol><ul><li>');
        case 'url':
            return filter_var($data, FILTER_SANITIZE_URL);
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case 'int':
            return (int)filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return (float)filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        default:
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

?>

