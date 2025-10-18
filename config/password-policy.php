<?php
// NextCode Group - Enhanced Password Policy System
// Secure password validation and management

if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

class PasswordPolicy {
    private $minLength = 8;
    private $maxLength = 128;
    private $requireUppercase = true;
    private $requireLowercase = true;
    private $requireNumbers = true;
    private $requireSpecialChars = true;
    private $commonPasswords = [];
    private $previousPasswordsCount = 5;
    
    public function __construct($config = []) {
        // Load common passwords list
        $this->commonPasswords = $this->loadCommonPasswords();
        
        // Apply custom configuration
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * Validate password strength
     */
    public function validatePassword($password) {
        $errors = [];
        
        // Length check
        if (strlen($password) < $this->minLength) {
            $errors[] = "Password must be at least {$this->minLength} characters long";
        }
        
        if (strlen($password) > $this->maxLength) {
            $errors[] = "Password must be at most {$this->maxLength} characters long";
        }
        
        // Character requirements
        if ($this->requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        if ($this->requireLowercase && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        if ($this->requireNumbers && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        if ($this->requireSpecialChars && !preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        // Common password check
        if ($this->isCommonPassword($password)) {
            $errors[] = "Password is too common or easily guessable";
        }
        
        // Sequential characters check
        if ($this->hasSequentialChars($password)) {
            $errors[] = "Password cannot contain sequential characters";
        }
        
        // Repeated characters check
        if ($this->hasRepeatedChars($password)) {
            $errors[] = "Password cannot contain repeated characters";
        }
        
        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
            'strength' => $this->calculateStrength($password)
        ];
    }
    
    /**
     * Calculate password strength score
     */
    public function calculateStrength($password) {
        $score = 0;
        $length = strlen($password);
        
        // Length bonus
        $score += min($length * 0.5, 20);
        
        // Character type bonus
        if (preg_match('/[a-z]/', $password)) $score += 5;
        if (preg_match('/[A-Z]/', $password)) $score += 5;
        if (preg_match('/[0-9]/', $password)) $score += 5;
        if (preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) $score += 10;
        
        // Variety bonus
        $uniqueChars = count(array_unique(str_split($password)));
        $score += min($uniqueChars, 10);
        
        // Penalty for repeated characters
        $repeated = preg_match_all('/(.)\\1+/', $password);
        $score -= $repeated * 2;
        
        return min(max($score, 0), 100);
    }
    
    /**
     * Generate secure random password
     */
    public function generatePassword($length = null) {
        $length = $length ?: max($this->minLength, 12);
        
        // Define character sets
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $charset = $lowercase;
        if ($this->requireUppercase) $charset .= $uppercase;
        if ($this->requireNumbers) $charset .= $numbers;
        if ($this->requireSpecialChars) $charset .= $special;
        
        // Start with required characters
        $password = '';
        if ($this->requireLowercase) $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        if ($this->requireUppercase) $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        if ($this->requireNumbers) $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        if ($this->requireSpecialChars) $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Fill the rest with random characters
        $remaining = $length - strlen($password);
        for ($i = 0; $i < $remaining; $i++) {
            $password .= $charset[random_int(0, strlen($charset) - 1)];
        }
        
        // Shuffle the password
        $passwordArray = str_split($password);
        for ($i = count($passwordArray) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$passwordArray[$i], $passwordArray[$j]] = [$passwordArray[$j], $passwordArray[$i]];
        }
        
        return implode('', $passwordArray);
    }
    
    /**
     * Check if password has been used recently
     */
    public function checkPasswordHistory($userId, $newPassword, $hashedPasswords = []) {
        foreach (array_slice($hashedPasswords, 0, $this->previousPasswordsCount) as $oldHash) {
            if (password_verify($newPassword, $oldHash)) {
                return false; // Password was used before
            }
        }
        return true;
    }
    
    /**
     * Check for common passwords
     */
    private function isCommonPassword($password) {
        $passwordLower = strtolower($password);
        
        foreach ($this->commonPasswords as $common) {
            if ($passwordLower === $common || 
                strpos($passwordLower, $common) !== false ||
                strpos($common, $passwordLower) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check for sequential characters
     */
    private function hasSequentialChars($password) {
        $len = strlen($password);
        for ($i = 0; $i < $len - 2; $i++) {
            $a = ord($password[$i]);
            $b = ord($password[$i + 1]);
            $c = ord($password[$i + 2]);
            
            if (($b == $a + 1 && $c == $a + 2) ||
                ($b == $a - 1 && $c == $a - 2)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check for repeated characters
     */
    private function hasRepeatedChars($password) {
        return preg_match('/(.)\\1{2,}/', $password);
    }
    
    /**
     * Load common passwords list
     */
    private function loadCommonPasswords() {
        return [
            'password', '123456', 'password123', 'admin', 'letmein',
            'welcome', 'monkey', '1234567890', 'abc123', 'qwerty',
            'iloveyou', 'dragon', 'master', 'hello', 'shadow',
            'monkey', 'a', 'abc', 'password1', 'welcome1'
        ];
    }
}

// Password hashing utilities
class PasswordHash {
    /**
     * Hash password with current best practices
     */
    public static function hash($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3          // 3 threads
        ]);
    }
    
    /**
     * Verify password hash
     */
    public static function verify($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Check if hash needs rehashing
     */
    public static function needsRehash($hash) {
        return password_needs_rehash($hash, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
}

// Password policy instance
$passwordPolicy = new PasswordPolicy();

// Helper functions
function validate_password_policy($password) {
    global $passwordPolicy;
    return $passwordPolicy->validatePassword($password);
}

function generate_secure_password($length = 12) {
    global $passwordPolicy;
    return $passwordPolicy->generatePassword($length);
}

function check_password_history($userId, $newPassword, $recentHashes = []) {
    global $passwordPolicy;
    return $passwordPolicy->checkPasswordHistory($userId, $newPassword, $recentHashes);
}

// Rate limiting for password attempts
function check_password_rate_limit($identifier, $maxAttempts = 5, $timeWindow = 900) {
    global $cache;
    
    $cacheKey = 'password_attempts_' . md5($identifier);
    $attempts = $cache->get($cacheKey) ?: [];
    
    // Clean old attempts
    $attempts = array_filter($attempts, function($timestamp) use ($timeWindow) {
        return time() - $timestamp < $timeWindow;
    });
    
    if (count($attempts) >= $maxAttempts) {
        return false;
    }
    
    $attempts[] = time();
    $cache->set($cacheKey, $attempts, $timeWindow);
    
    return true;
}

?>

