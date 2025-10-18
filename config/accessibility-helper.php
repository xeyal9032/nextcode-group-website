<?php
/**
 * Accessibility Helper
 * ARIA labels, keyboard navigation ve erişilebilirlik iyileştirmeleri
 */

class AccessibilityHelper {
    public $ariaLabels;
    public $keyboardNavigation;
    
    public function __construct() {
        $this->ariaLabels = [
            'nav' => 'Ana navigasiya menüsü',
            'main' => 'Ana məzmuna keçmək',
            'footer' => 'Saytın alt hissəsi',
            'search' => 'Axtarış',
            'menu' => 'Menyu',
            'close' => 'Bağla',
            'open' => 'Aç',
            'next' => 'Növbəti',
            'previous' => 'Əvvəlki',
            'play' => 'Oynat',
            'pause' => 'Duraklat',
            'mute' => 'Səsi kəs',
            'unmute' => 'Səsi aç',
            'fullscreen' => 'Tam ekran',
            'exit-fullscreen' => 'Tam ekrandan çıx',
            'loading' => 'Yüklənir',
            'error' => 'Xəta',
            'success' => 'Uğurlu',
            'warning' => 'Xəbərdarlıq',
            'info' => 'Məlumat'
        ];
        
        $this->keyboardNavigation = [
            'tab' => 'Tab',
            'enter' => 'Enter',
            'escape' => 'Escape',
            'arrow-up' => 'Yuxarı ox',
            'arrow-down' => 'Aşağı ox',
            'arrow-left' => 'Sol ox',
            'arrow-right' => 'Sağ ox',
            'space' => 'Boşluq',
            'home' => 'Home',
            'end' => 'End',
            'page-up' => 'Page Up',
            'page-down' => 'Page Down'
        ];
    }
    
    /**
     * ARIA label al
     */
    public function getAriaLabel($key, $default = '') {
        return $this->ariaLabels[$key] ?? $default;
    }
    
    /**
     * Navigasyon menüsü için ARIA attributes
     */
    public function getNavigationAriaAttributes() {
        return [
            'role' => 'navigation',
            'aria-label' => $this->getAriaLabel('nav'),
            'aria-expanded' => 'false'
        ];
    }
    
    /**
     * Ana içerik için ARIA attributes
     */
    public function getMainContentAriaAttributes() {
        return [
            'role' => 'main',
            'aria-label' => $this->getAriaLabel('main')
        ];
    }
    
    /**
     * Footer için ARIA attributes
     */
    public function getFooterAriaAttributes() {
        return [
            'role' => 'contentinfo',
            'aria-label' => $this->getAriaLabel('footer')
        ];
    }
    
    /**
     * Buton için ARIA attributes
     */
    public function getButtonAriaAttributes($type = 'button', $pressed = null, $expanded = null) {
        $attributes = [
            'role' => 'button',
            'tabindex' => '0'
        ];
        
        if ($pressed !== null) {
            $attributes['aria-pressed'] = $pressed ? 'true' : 'false';
        }
        
        if ($expanded !== null) {
            $attributes['aria-expanded'] = $expanded ? 'true' : 'false';
        }
        
        return $attributes;
    }
    
    /**
     * Form için ARIA attributes
     */
    public function getFormAriaAttributes($name = '') {
        return [
            'role' => 'form',
            'aria-label' => $name ?: 'Form',
            'aria-live' => 'polite'
        ];
    }
    
    /**
     * Input için ARIA attributes
     */
    public function getInputAriaAttributes($type = 'text', $required = false, $invalid = false) {
        $attributes = [
            'aria-describedby' => $type . '-help'
        ];
        
        if ($required) {
            $attributes['aria-required'] = 'true';
        }
        
        if ($invalid) {
            $attributes['aria-invalid'] = 'true';
        }
        
        return $attributes;
    }
    
    /**
     * Modal için ARIA attributes
     */
    public function getModalAriaAttributes($title = '') {
        return [
            'role' => 'dialog',
            'aria-modal' => 'true',
            'aria-labelledby' => 'modal-title',
            'aria-describedby' => 'modal-description',
            'tabindex' => '-1'
        ];
    }
    
    /**
     * Tab panel için ARIA attributes
     */
    public function getTabPanelAriaAttributes($id, $selected = false) {
        return [
            'role' => 'tabpanel',
            'id' => $id,
            'aria-labelledby' => $id . '-tab',
            'aria-hidden' => $selected ? 'false' : 'true',
            'tabindex' => $selected ? '0' : '-1'
        ];
    }
    
    /**
     * Tab için ARIA attributes
     */
    public function getTabAriaAttributes($id, $selected = false, $controls = '') {
        return [
            'role' => 'tab',
            'id' => $id . '-tab',
            'aria-selected' => $selected ? 'true' : 'false',
            'aria-controls' => $controls,
            'tabindex' => $selected ? '0' : '-1'
        ];
    }
    
    /**
     * Accordion için ARIA attributes
     */
    public function getAccordionAriaAttributes($id, $expanded = false) {
        return [
            'role' => 'button',
            'aria-expanded' => $expanded ? 'true' : 'false',
            'aria-controls' => $id . '-content',
            'aria-labelledby' => $id . '-header',
            'tabindex' => '0'
        ];
    }
    
    /**
     * Accordion içerik için ARIA attributes
     */
    public function getAccordionContentAriaAttributes($id, $expanded = false) {
        return [
            'id' => $id . '-content',
            'aria-labelledby' => $id . '-header',
            'aria-hidden' => $expanded ? 'false' : 'true'
        ];
    }
    
    /**
     * Progress bar için ARIA attributes
     */
    public function getProgressBarAriaAttributes($value = 0, $max = 100, $label = '') {
        return [
            'role' => 'progressbar',
            'aria-valuenow' => $value,
            'aria-valuemin' => '0',
            'aria-valuemax' => $max,
            'aria-label' => $label ?: 'Progress bar'
        ];
    }
    
    /**
     * Alert için ARIA attributes
     */
    public function getAlertAriaAttributes($type = 'info', $live = 'polite') {
        return [
            'role' => 'alert',
            'aria-live' => $live,
            'aria-atomic' => 'true'
        ];
    }
    
    /**
     * Skip link oluştur
     */
    public function generateSkipLink($target = '#main-content', $text = 'Ana məzmuna keç') {
        return '<a href="' . $target . '" class="skip-link" tabindex="1">' . $text . '</a>';
    }
    
    /**
     * Focus trap JavaScript oluştur
     */
    public function generateFocusTrapJS() {
        return '
        function trapFocus(element) {
            const focusableElements = element.querySelectorAll(
                \'a[href], button, textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select\'
            );
            const firstFocusableElement = focusableElements[0];
            const lastFocusableElement = focusableElements[focusableElements.length - 1];
            
            element.addEventListener(\'keydown\', function(e) {
                if (e.key === \'Tab\') {
                    if (e.shiftKey) {
                        if (document.activeElement === firstFocusableElement) {
                            lastFocusableElement.focus();
                            e.preventDefault();
                        }
                    } else {
                        if (document.activeElement === lastFocusableElement) {
                            firstFocusableElement.focus();
                            e.preventDefault();
                        }
                    }
                }
            });
        }
        ';
    }
    
    /**
     * Keyboard navigation JavaScript oluştur
     */
    public function generateKeyboardNavigationJS() {
        return '
        document.addEventListener(\'keydown\', function(e) {
            // Escape tuşu ile modal\'ları kapat
            if (e.key === \'Escape\') {
                const modals = document.querySelectorAll(\'[role="dialog"]\');
                modals.forEach(modal => {
                    if (modal.style.display !== \'none\') {
                        modal.style.display = \'none\';
                        modal.setAttribute(\'aria-hidden\', \'true\');
                    }
                });
            }
            
            // Enter tuşu ile butonları aktifleştir
            if (e.key === \'Enter\' && e.target.getAttribute(\'role\') === \'button\') {
                e.target.click();
            }
            
            // Space tuşu ile butonları aktifleştir
            if (e.key === \' \' && e.target.getAttribute(\'role\') === \'button\') {
                e.preventDefault();
                e.target.click();
            }
        });
        ';
    }
    
    /**
     * ARIA live region oluştur
     */
    public function generateAriaLiveRegion($id = 'aria-live-region', $polite = true) {
        $live = $polite ? 'polite' : 'assertive';
        return '<div id="' . $id . '" aria-live="' . $live . '" aria-atomic="true" class="sr-only"></div>';
    }
    
    /**
     * Screen reader only CSS oluştur
     */
    public function generateScreenReaderOnlyCSS() {
        return '
        .sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
        
        .sr-only:focus {
            position: static !important;
            width: auto !important;
            height: auto !important;
            padding: inherit !important;
            margin: inherit !important;
            overflow: visible !important;
            clip: auto !important;
            white-space: normal !important;
        }
        
        .skip-link {
            position: absolute;
            top: -40px;
            left: 6px;
            background: #000;
            color: #fff;
            padding: 8px;
            text-decoration: none;
            z-index: 1000;
        }
        
        .skip-link:focus {
            top: 6px;
        }
        ';
    }
    
    /**
     * Focus indicator CSS oluştur
     */
    public function generateFocusIndicatorCSS() {
        return '
        *:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }
        
        button:focus,
        a:focus,
        input:focus,
        textarea:focus,
        select:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }
        
        .focus-visible {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }
        ';
    }
    
    /**
     * Color contrast checker
     */
    public function checkColorContrast($foreground, $background) {
        // Basit kontrast kontrolü
        $fg = $this->hexToRgb($foreground);
        $bg = $this->hexToRgb($background);
        
        if (!$fg || !$bg) {
            return false;
        }
        
        $l1 = $this->getLuminance($fg);
        $l2 = $this->getLuminance($bg);
        
        $max = max($l1, $l2);
        $min = min($l1, $l2);
        $contrast = $min > 0 ? $max / $min : 0;
        
        return [
            'ratio' => $contrast,
            'aa' => $contrast >= 4.5,
            'aaa' => $contrast >= 7,
            'level' => $contrast >= 7 ? 'AAA' : ($contrast >= 4.5 ? 'AA' : 'Fail')
        ];
    }
    
    /**
     * Hex renk kodunu RGB'ye çevir
     */
    private function hexToRgb($hex) {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        if (strlen($hex) === 6) {
            return [
                'r' => hexdec(substr($hex, 0, 2)),
                'g' => hexdec(substr($hex, 2, 2)),
                'b' => hexdec(substr($hex, 4, 2))
            ];
        }
        
        return false;
    }
    
    /**
     * Luminance hesapla
     */
    private function getLuminance($rgb) {
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;
        
        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);
        
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
    
    /**
     * Erişilebilirlik raporu oluştur
     */
    public function generateAccessibilityReport() {
        $report = "=== ERİŞİLEBİLİRLİK RAPORU ===\n\n";
        
        $report .= "ARIA Labels:\n";
        foreach ($this->ariaLabels as $key => $label) {
            $report .= "  {$key}: {$label}\n";
        }
        
        $report .= "\nKeyboard Navigation:\n";
        foreach ($this->keyboardNavigation as $key => $description) {
            $report .= "  {$key}: {$description}\n";
        }
        
        $report .= "\nÖneriler:\n";
        $report .= "  1. Tüm interaktif elementlerde ARIA labels kullanın\n";
        $report .= "  2. Keyboard navigation desteği ekleyin\n";
        $report .= "  3. Color contrast oranını kontrol edin\n";
        $report .= "  4. Screen reader testleri yapın\n";
        $report .= "  5. Focus management implementasyonu\n";
        
        return $report;
    }
}

// Kullanım örneği
if (php_sapi_name() === 'cli') {
    $accessibility = new AccessibilityHelper();
    
    echo "Accessibility Helper Created!\n";
    echo "ARIA Labels: " . count($accessibility->ariaLabels) . "\n";
    echo "Keyboard Navigation: " . count($accessibility->keyboardNavigation) . "\n";
    
    // Kontrast testi
    $contrast = $accessibility->checkColorContrast('#000000', '#ffffff');
    echo "Black on White Contrast: " . round($contrast['ratio'], 2) . " (" . $contrast['level'] . ")\n";
    
    // Rapor oluştur
    $report = $accessibility->generateAccessibilityReport();
    file_put_contents('accessibility_report.txt', $report);
    echo "Accessibility report saved to: accessibility_report.txt\n";
}
?>
