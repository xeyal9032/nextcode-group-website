<?php
// Görsel yönetimi helper fonksiyonları
// SECURE_ACCESS ve database.php zaten admin.php'de include edildi

/**
 * Görsel URL'sini al
 */
function getImageUrl($image_key, $default_url = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT image_url FROM site_images WHERE image_key = ? AND is_active = 1");
        $stmt->execute([$image_key]);
        $result = $stmt->fetch();
        
        return $result ? $result['image_url'] : $default_url;
    } catch (Exception $e) {
        error_log('Error getting image URL: ' . $e->getMessage());
        return $default_url;
    }
}

/**
 * Görsel alt text'ini al
 */
function getImageAlt($image_key, $default_alt = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT image_alt FROM site_images WHERE image_key = ? AND is_active = 1");
        $stmt->execute([$image_key]);
        $result = $stmt->fetch();
        
        return $result ? $result['image_alt'] : $default_alt;
    } catch (Exception $e) {
        error_log('Error getting image alt: ' . $e->getMessage());
        return $default_alt;
    }
}

/**
 * Görsel title'ını al
 */
function getImageTitle($image_key, $default_title = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT image_title FROM site_images WHERE image_key = ? AND is_active = 1");
        $stmt->execute([$image_key]);
        $result = $stmt->fetch();
        
        return $result ? $result['image_title'] : $default_title;
    } catch (Exception $e) {
        error_log('Error getting image title: ' . $e->getMessage());
        return $default_title;
    }
}

/**
 * Tam görsel HTML tag'ini al
 */
function getImageTag($image_key, $default_url = '', $default_alt = '', $attributes = []) {
    $url = getImageUrl($image_key, $default_url);
    $alt = getImageAlt($image_key, $default_alt);
    $title = getImageTitle($image_key, $alt);
    
    if (empty($url)) {
        return '';
    }
    
    $attr_string = '';
    foreach ($attributes as $key => $value) {
        $attr_string .= " $key=\"$value\"";
    }
    
    return "<img src=\"$url\" alt=\"$alt\" title=\"$title\"$attr_string>";
}

/**
 * Sayfa bölümüne göre görselleri al
 */
function getImagesBySection($page_section) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM site_images WHERE page_section = ? AND is_active = 1 ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$page_section]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error getting images by section: ' . $e->getMessage());
        return [];
    }
}

/**
 * Görsel tipine göre görselleri al
 */
function getImagesByType($image_type) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM site_images WHERE image_type = ? AND is_active = 1 ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$image_type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error getting images by type: ' . $e->getMessage());
        return [];
    }
}

/**
 * Tüm görselleri al
 */
function getAllImages() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM site_images WHERE is_active = 1 ORDER BY page_section ASC, sort_order ASC, id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error getting all images: ' . $e->getMessage());
        return [];
    }
}

/**
 * Görsel güncelle
 */
function updateImage($image_key, $image_url, $image_alt = '', $image_title = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE site_images SET image_url = ?, image_alt = ?, image_title = ?, updated_at = NOW() WHERE image_key = ?");
        $stmt->execute([$image_url, $image_alt, $image_title, $image_key]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log('Error updating image: ' . $e->getMessage());
        return false;
    }
}

/**
 * Yeni görsel ekle
 */
function addImage($image_key, $image_url, $image_alt = '', $image_title = '', $page_section = 'general', $image_type = 'image') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO site_images (image_key, image_url, image_alt, image_title, page_section, image_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$image_key, $image_url, $image_alt, $image_title, $page_section, $image_type]);
        return true;
    } catch (Exception $e) {
        error_log('Error adding image: ' . $e->getMessage());
        return false;
    }
}

/**
 * Görsel sil
 */
function deleteImage($image_key) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE site_images SET is_active = 0 WHERE image_key = ?");
        $stmt->execute([$image_key]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log('Error deleting image: ' . $e->getMessage());
        return false;
    }
}
?>
