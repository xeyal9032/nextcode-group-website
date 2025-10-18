<?php
/**
 * Input Validation Tests
 * Form validation ve data validation testleri
 */

namespace NextCode\Tests\Unit;

use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
    public function testEmailValidation()
    {
        $validEmails = [
            'test@example.com',
            'user.name@example.co.uk',
            'admin+tag@company.com',
            'test_user@example-domain.com'
        ];
        
        foreach ($validEmails as $email) {
            $this->assertTrue(
                filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
                "Email should be valid: $email"
            );
        }
        
        $invalidEmails = [
            'invalid.email',
            'invalid@',
            '@example.com',
            'test@',
            'test @example.com',
            'test@exam ple.com'
        ];
        
        foreach ($invalidEmails as $email) {
            $this->assertFalse(
                filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
                "Email should be invalid: $email"
            );
        }
    }
    
    public function testPhoneValidation()
    {
        $validPhones = [
            '+994501234567',
            '+380972580000',
            '+1234567890'
        ];
        
        foreach ($validPhones as $phone) {
            $pattern = '/^\+?[1-9]\d{10,14}$/';
            $this->assertMatchesRegularExpression($pattern, $phone);
        }
    }
    
    public function testRequiredFieldValidation()
    {
        $data = [
            'name' => '',
            'email' => 'test@example.com',
            'message' => 'Test message'
        ];
        
        $requiredFields = ['name', 'email', 'message'];
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "$field sahəsi mütləqdir";
            }
        }
        
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayNotHasKey('email', $errors);
    }
    
    public function testStringLengthValidation()
    {
        $shortString = 'ab';
        $validString = 'Valid Name';
        $longString = str_repeat('a', 300);
        
        // Minimum length (3 characters)
        $this->assertLessThan(3, strlen($shortString));
        $this->assertGreaterThanOrEqual(3, strlen($validString));
        
        // Maximum length (255 characters)
        $this->assertLessThanOrEqual(255, strlen($validString));
        $this->assertGreaterThan(255, strlen($longString));
    }
    
    public function testNumericValidation()
    {
        $this->assertTrue(is_numeric('123'));
        $this->assertTrue(is_numeric('123.45'));
        $this->assertTrue(is_numeric('-123'));
        
        $this->assertFalse(is_numeric('abc'));
        $this->assertFalse(is_numeric('12abc'));
    }
    
    public function testURLValidation()
    {
        $validURLs = [
            'https://example.com',
            'http://example.com',
            'https://sub.example.com/path',
            'https://example.com/path?query=value'
        ];
        
        foreach ($validURLs as $url) {
            $this->assertTrue(
                filter_var($url, FILTER_VALIDATE_URL) !== false,
                "URL should be valid: $url"
            );
        }
        
        $invalidURLs = [
            'not-a-url',
            'htp://example.com',
            'example.com'
        ];
        
        foreach ($invalidURLs as $url) {
            $this->assertFalse(
                filter_var($url, FILTER_VALIDATE_URL) !== false,
                "URL should be invalid: $url"
            );
        }
    }
    
    public function testDateValidation()
    {
        $validDates = [
            '2024-01-15',
            '2024-12-31',
            '2023-06-01'
        ];
        
        foreach ($validDates as $date) {
            $this->assertNotFalse(
                \DateTime::createFromFormat('Y-m-d', $date),
                "Date should be valid: $date"
            );
        }
        
        $invalidDates = [
            '2024-13-01', // Invalid month
            '2024-02-30', // Invalid day
            'not-a-date'
        ];
        
        foreach ($invalidDates as $date) {
            $result = \DateTime::createFromFormat('Y-m-d', $date);
            if ($result) {
                $errors = \DateTime::getLastErrors();
                $this->assertTrue(
                    $errors['warning_count'] > 0 || $errors['error_count'] > 0,
                    "Date should be invalid: $date"
                );
            } else {
                $this->assertFalse($result);
            }
        }
    }
}


