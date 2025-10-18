<?php
/**
 * Admin Validation Functions
 * NextCode Group
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    die('Direct access not allowed');
}

/**
 * Validate email address
 */
function validateEmail($email) {
    if (empty($email)) {
        return ['valid' => false, 'message' => 'E-posta adresi gereklidir'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Geçerli bir e-posta adresi giriniz'];
    }
    
    if (strlen($email) > 255) {
        return ['valid' => false, 'message' => 'E-posta adresi çok uzun (max 255 karakter)'];
    }
    
    return ['valid' => true];
}

/**
 * Validate password
 */
function validatePassword($password, $confirmPassword = null) {
    if (empty($password)) {
        return ['valid' => false, 'message' => 'Şifre gereklidir'];
    }
    
    if (strlen($password) < 8) {
        return ['valid' => false, 'message' => 'Şifre en az 8 karakter olmalıdır'];
    }
    
    if (strlen($password) > 128) {
        return ['valid' => false, 'message' => 'Şifre çok uzun (max 128 karakter)'];
    }
    
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
        return ['valid' => false, 'message' => 'Şifre en az bir küçük harf, bir büyük harf ve bir rakam içermelidir'];
    }
    
    if ($confirmPassword !== null && $password !== $confirmPassword) {
        return ['valid' => false, 'message' => 'Şifreler eşleşmiyor'];
    }
    
    return ['valid' => true];
}

/**
 * Validate username
 */
function validateUsername($username) {
    if (empty($username)) {
        return ['valid' => false, 'message' => 'Kullanıcı adı gereklidir'];
    }
    
    if (strlen($username) < 3) {
        return ['valid' => false, 'message' => 'Kullanıcı adı en az 3 karakter olmalıdır'];
    }
    
    if (strlen($username) > 50) {
        return ['valid' => false, 'message' => 'Kullanıcı adı çok uzun (max 50 karakter)'];
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return ['valid' => false, 'message' => 'Kullanıcı adı sadece harf, rakam ve alt çizgi içerebilir'];
    }
    
    return ['valid' => true];
}

/**
 * Validate title
 */
function validateTitle($title, $fieldName = 'Başlık') {
    if (empty($title)) {
        return ['valid' => false, 'message' => $fieldName . ' gereklidir'];
    }
    
    if (strlen($title) < 3) {
        return ['valid' => false, 'message' => $fieldName . ' en az 3 karakter olmalıdır'];
    }
    
    if (strlen($title) > 255) {
        return ['valid' => false, 'message' => $fieldName . ' çok uzun (max 255 karakter)'];
    }
    
    return ['valid' => true];
}

/**
 * Validate content
 */
function validateContent($content, $fieldName = 'İçerik') {
    if (empty($content)) {
        return ['valid' => false, 'message' => $fieldName . ' gereklidir'];
    }
    
    if (strlen($content) < 10) {
        return ['valid' => false, 'message' => $fieldName . ' en az 10 karakter olmalıdır'];
    }
    
    if (strlen($content) > 10000) {
        return ['valid' => false, 'message' => $fieldName . ' çok uzun (max 10000 karakter)'];
    }
    
    return ['valid' => true];
}

/**
 * Validate URL
 */
function validateURL($url, $required = false) {
    if ($required && empty($url)) {
        return ['valid' => false, 'message' => 'URL gereklidir'];
    }
    
    if (!empty($url)) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['valid' => false, 'message' => 'Geçerli bir URL giriniz'];
        }
        
        if (strlen($url) > 500) {
            return ['valid' => false, 'message' => 'URL çok uzun (max 500 karakter)'];
        }
    }
    
    return ['valid' => true];
}

/**
 * Validate file upload
 */
function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) { // 5MB default
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['valid' => false, 'message' => 'Geçersiz dosya yükleme'];
    }
    
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['valid' => false, 'message' => 'Dosya seçilmedi'];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['valid' => false, 'message' => 'Dosya çok büyük'];
        default:
            return ['valid' => false, 'message' => 'Dosya yükleme hatası'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['valid' => false, 'message' => 'Dosya çok büyük (max ' . formatFileSize($maxSize) . ')'];
    }
    
    if (!empty($allowedTypes)) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['valid' => false, 'message' => 'Geçersiz dosya türü. İzin verilen türler: ' . implode(', ', $allowedTypes)];
        }
    }
    
    return ['valid' => true];
}

/**
 * Validate image upload
 */
function validateImageUpload($file, $maxSize = 2097152) { // 2MB default
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    return validateFileUpload($file, $allowedTypes, $maxSize);
}

/**
 * Validate date
 */
function validateDate($date, $format = 'Y-m-d H:i:s', $required = true) {
    if ($required && empty($date)) {
        return ['valid' => false, 'message' => 'Tarih gereklidir'];
    }
    
    if (!empty($date)) {
        $d = DateTime::createFromFormat($format, $date);
        if (!$d || $d->format($format) !== $date) {
            return ['valid' => false, 'message' => 'Geçerli bir tarih giriniz'];
        }
    }
    
    return ['valid' => true];
}

/**
 * Validate number
 */
function validateNumber($number, $min = null, $max = null, $required = true) {
    if ($required && empty($number)) {
        return ['valid' => false, 'message' => 'Sayı gereklidir'];
    }
    
    if (!empty($number)) {
        if (!is_numeric($number)) {
            return ['valid' => false, 'message' => 'Geçerli bir sayı giriniz'];
        }
        
        $num = (float)$number;
        
        if ($min !== null && $num < $min) {
            return ['valid' => false, 'message' => "Sayı en az $min olmalıdır"];
        }
        
        if ($max !== null && $num > $max) {
            return ['valid' => false, 'message' => "Sayı en fazla $max olmalıdır"];
        }
    }
    
    return ['valid' => true];
}

/**
 * Validate integer
 */
function validateInteger($number, $min = null, $max = null, $required = true) {
    if ($required && empty($number)) {
        return ['valid' => false, 'message' => 'Tam sayı gereklidir'];
    }
    
    if (!empty($number)) {
        if (!ctype_digit($number)) {
            return ['valid' => false, 'message' => 'Geçerli bir tam sayı giriniz'];
        }
        
        $num = (int)$number;
        
        if ($min !== null && $num < $min) {
            return ['valid' => false, 'message' => "Sayı en az $min olmalıdır"];
        }
        
        if ($max !== null && $num > $max) {
            return ['valid' => false, 'message' => "Sayı en fazla $max olmalıdır"];
        }
    }
    
    return ['valid' => true];
}

/**
 * Validate phone number
 */
function validatePhone($phone) {
    if (empty($phone)) {
        return ['valid' => true]; // Phone is optional
    }
    
    // Remove spaces, dashes, and parentheses
    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
    
    // Check if it's a valid phone number
    if (!preg_match('/^(\+90|0)?[5][0-9]{9}$/', $phone)) {
        return ['valid' => false, 'message' => 'Geçerli bir telefon numarası giriniz'];
    }
    
    return ['valid' => true];
}

/**
 * Validate slug
 */
function validateSlug($slug, $required = true) {
    if ($required && empty($slug)) {
        return ['valid' => false, 'message' => 'Slug gereklidir'];
    }
    
    if (!empty($slug)) {
        if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
            return ['valid' => false, 'message' => 'Slug sadece küçük harf, rakam ve tire içerebilir'];
        }
        
        if (strlen($slug) < 3) {
            return ['valid' => false, 'message' => 'Slug en az 3 karakter olmalıdır'];
        }
        
        if (strlen($slug) > 100) {
            return ['valid' => false, 'message' => 'Slug çok uzun (max 100 karakter)'];
        }
    }
    
    return ['valid' => true];
}

/**
 * Validate role
 */
function validateRole($role) {
    if (empty($role)) {
        return ['valid' => false, 'message' => 'Rol gereklidir'];
    }
    
    $allowedRoles = ['admin', 'editor', 'author', 'viewer', 'super_admin'];
    
    if (!in_array($role, $allowedRoles)) {
        return ['valid' => false, 'message' => 'Geçerli bir rol seçiniz'];
    }
    
    return ['valid' => true];
}

/**
 * Validate status
 */
function validateStatus($status, $allowedStatuses = ['active', 'inactive']) {
    if (empty($status)) {
        return ['valid' => false, 'message' => 'Durum gereklidir'];
    }
    
    if (!in_array($status, $allowedStatuses)) {
        return ['valid' => false, 'message' => 'Geçerli bir durum seçiniz'];
    }
    
    return ['valid' => true];
}

/**
 * Validate CSRF token
 */
function validateCSRF($token) {
    if (empty($token)) {
        return ['valid' => false, 'message' => 'CSRF token gereklidir'];
    }
    
    if (!verifyCSRFToken($token)) {
        return ['valid' => false, 'message' => 'Geçersiz CSRF token'];
    }
    
    return ['valid' => true];
}

/**
 * Validate array of data
 */
function validateArray($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = $data[$field] ?? null;
        $result = validateField($value, $rule);
        
        if (!$result['valid']) {
            $errors[$field] = $result['message'];
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Validate single field
 */
function validateField($value, $rule) {
    $type = $rule['type'] ?? 'string';
    $required = $rule['required'] ?? true;
    $message = $rule['message'] ?? null;
    
    switch ($type) {
        case 'email':
            $result = validateEmail($value);
            break;
        case 'password':
            $result = validatePassword($value, $rule['confirm'] ?? null);
            break;
        case 'username':
            $result = validateUsername($value);
            break;
        case 'title':
            $result = validateTitle($value, $rule['field_name'] ?? 'Başlık');
            break;
        case 'content':
            $result = validateContent($value, $rule['field_name'] ?? 'İçerik');
            break;
        case 'url':
            $result = validateURL($value, $required);
            break;
        case 'date':
            $result = validateDate($value, $rule['format'] ?? 'Y-m-d H:i:s', $required);
            break;
        case 'number':
            $result = validateNumber($value, $rule['min'] ?? null, $rule['max'] ?? null, $required);
            break;
        case 'integer':
            $result = validateInteger($value, $rule['min'] ?? null, $rule['max'] ?? null, $required);
            break;
        case 'phone':
            $result = validatePhone($value);
            break;
        case 'slug':
            $result = validateSlug($value, $required);
            break;
        case 'role':
            $result = validateRole($value);
            break;
        case 'status':
            $result = validateStatus($value, $rule['allowed'] ?? ['active', 'inactive']);
            break;
        case 'csrf':
            $result = validateCSRF($value);
            break;
        case 'file':
            $result = validateFileUpload($value, $rule['allowed_types'] ?? [], $rule['max_size'] ?? 5242880);
            break;
        case 'image':
            $result = validateImageUpload($value, $rule['max_size'] ?? 2097152);
            break;
        default:
            // String validation
            if ($required && empty($value)) {
                $result = ['valid' => false, 'message' => $message ?: 'Bu alan gereklidir'];
            } else {
                $maxLength = $rule['max_length'] ?? 255;
                if (strlen($value) > $maxLength) {
                    $result = ['valid' => false, 'message' => "Çok uzun (max $maxLength karakter)"];
                } else {
                    $result = ['valid' => true];
                }
            }
            break;
    }
    
    if ($message && !$result['valid']) {
        $result['message'] = $message;
    }
    
    return $result;
}

/**
 * Sanitize input data
 */
function sanitizeInputData($data, $rules) {
    $sanitized = [];
    
    foreach ($rules as $field => $rule) {
        $value = $data[$field] ?? null;
        $type = $rule['type'] ?? 'string';
        
        switch ($type) {
            case 'email':
                $sanitized[$field] = filter_var($value, FILTER_SANITIZE_EMAIL);
                break;
            case 'url':
                $sanitized[$field] = filter_var($value, FILTER_SANITIZE_URL);
                break;
            case 'integer':
                $sanitized[$field] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'float':
                $sanitized[$field] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                break;
            case 'string':
            default:
                $sanitized[$field] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
                break;
        }
    }
    
    return $sanitized;
}

/**
 * Get validation error messages
 */
function getValidationErrors($errors) {
    if (empty($errors)) {
        return '';
    }
    
    $messages = [];
    foreach ($errors as $field => $error) {
        $messages[] = ucfirst($field) . ': ' . $error;
    }
    
    return implode('; ', $messages);
}
?>
