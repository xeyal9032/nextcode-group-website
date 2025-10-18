<?php
// IP Whitelisting Sistemi - NextCode Group

if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

// İzin verilen IP adresleri (virgülle ayrılmış)
$allowed_ips = [
    '127.0.0.1',        // Localhost
    '::1',              // IPv6 localhost
    // Production IP'ler buraya eklenebilir
    // '192.168.1.100',
    // '10.0.0.50'
];

// IP kontrolü fonksiyonu
function checkIPWhitelist($strict_mode = false) {
    global $allowed_ips;
    
    // Proxy arkasındaki gerçek IP'yi al
    $client_ip = getClientIP();
    
    // Localhost her zaman izin ver (geliştirme için)
    if (in_array($client_ip, ['127.0.0.1', '::1'])) {
        return true;
    }
    
    // Whitelist kontrolü
    if (in_array($client_ip, $allowed_ips)) {
        return true;
    }
    
    // Strict mode aktifse IP whitelist zorunlu
    if ($strict_mode) {
        logSecurityEvent('IP_WHITELIST_VIOLATION', 'Unauthorized IP attempt: ' . $client_ip, 'CRITICAL');
        return false;
    }
    
    // Strict mode kapalıysa sadece uyarı ver
    logSecurityEvent('IP_WHITELIST_WARNING', 'Non-whitelisted IP access: ' . $client_ip, 'WARNING');
    return true;
}

// Gerçek client IP'sini al
function getClientIP() {
    $ip_keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip);
            
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

// IP whitelist yönetimi
function addToWhitelist($ip) {
    global $allowed_ips;
    
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        if (!in_array($ip, $allowed_ips)) {
            $allowed_ips[] = $ip;
            logSecurityEvent('IP_WHITELIST_ADDED', 'IP added to whitelist: ' . $ip, 'INFO');
            return true;
        }
    }
    return false;
}

function removeFromWhitelist($ip) {
    global $allowed_ips;
    
    $key = array_search($ip, $allowed_ips);
    if ($key !== false) {
        unset($allowed_ips[$key]);
        $allowed_ips = array_values($allowed_ips); // Re-index array
        logSecurityEvent('IP_WHITELIST_REMOVED', 'IP removed from whitelist: ' . $ip, 'INFO');
        return true;
    }
    return false;
}

// Admin paneli için IP kontrolü
function enforceIPWhitelist() {
    // Sadece admin paneli sayfalarında aktif
    if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
        if (!checkIPWhitelist(true)) {
            http_response_code(403);
            die('
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Erişim Reddedildi</title>
                    <style>
                        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
                        .error-container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
                        h1 { color: #e74c3c; margin-bottom: 20px; }
                        p { color: #666; line-height: 1.6; }
                        .ip-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; font-family: monospace; }
                    </style>
                </head>
                <body>
                    <div class="error-container">
                        <h1>🚫 Erişim Reddedildi</h1>
                        <p>Bu IP adresinden admin paneline erişim izniniz bulunmamaktadır.</p>
                        <div class="ip-info">
                            IP Adresiniz: ' . getClientIP() . '
                        </div>
                        <p>Erişim için sistem yöneticisi ile iletişime geçin.</p>
                    </div>
                </body>
                </html>
            ');
        }
    }
}
?>



