// İletişim bilgilerini dinamik olarak yükle
document.addEventListener('DOMContentLoaded', function() {
    loadContactInfo();
});

async function loadContactInfo() {
    try {
        const response = await fetch('/api/contact-info.php');
        
        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.warn('API response is not JSON:', text.substring(0, 100));
            return;
        }
        
        const result = await response.json();
        
        if (result.success) {
            updateContactInfo(result.data);
        } else {
            console.error('İletişim bilgileri yüklenemedi:', result.error);
        }
    } catch (error) {
        console.error('İletişim bilgileri yüklenirken hata:', error);
    }
}

function updateContactInfo(contactInfo) {
    // Telefon numaralarını güncelle
    const phoneElements = document.querySelectorAll('[data-phone]');
    phoneElements.forEach(element => {
        element.textContent = contactInfo.phone;
        // Telefon linkini de güncelle
        if (element.parentElement.tagName === 'A') {
            element.parentElement.href = `tel:${contactInfo.phone.replace(/\s/g, '')}`;
        }
    });
    
    // Email adreslerini güncelle
    const emailElements = document.querySelectorAll('[data-email]');
    emailElements.forEach(element => {
        element.textContent = contactInfo.email;
        // Email linkini de güncelle
        if (element.parentElement.tagName === 'A') {
            element.parentElement.href = `mailto:${contactInfo.email}`;
        }
    });
    
    // Adres bilgilerini güncelle
    const addressElements = document.querySelectorAll('[data-address]');
    addressElements.forEach(element => {
        element.textContent = contactInfo.address;
    });
    
    // Şirket adını güncelle
    const companyElements = document.querySelectorAll('[data-company]');
    companyElements.forEach(element => {
        element.textContent = contactInfo.company_name;
    });
    
    // Çalışma saatlerini güncelle
    const hoursElements = document.querySelectorAll('[data-hours]');
    hoursElements.forEach(element => {
        element.textContent = contactInfo.working_hours;
    });
    
    // Sosyal medya linklerini güncelle
    if (contactInfo.facebook) {
        updateSocialLink('facebook', contactInfo.facebook);
    }
    if (contactInfo.instagram) {
        updateSocialLink('instagram', contactInfo.instagram);
    }
    if (contactInfo.linkedin) {
        updateSocialLink('linkedin', contactInfo.linkedin);
    }
    if (contactInfo.twitter) {
        updateSocialLink('twitter', contactInfo.twitter);
    }
    if (contactInfo.whatsapp) {
        updateSocialLink('whatsapp', contactInfo.whatsapp);
    }
    if (contactInfo.telegram) {
        updateSocialLink('telegram', contactInfo.telegram);
    }
}

function updateSocialLink(platform, url) {
    const linkElement = document.querySelector(`[data-${platform}]`);
    if (linkElement) {
        linkElement.href = url;
        linkElement.style.display = 'inline-block';
    }
}

// Sayfa yüklendikten sonra tekrar kontrol et (AJAX ile yüklenen içerik için)
function refreshContactInfo() {
    setTimeout(loadContactInfo, 1000);
}

// Global fonksiyon olarak tanımla
window.refreshContactInfo = refreshContactInfo;
window.loadContactInfo = loadContactInfo;
