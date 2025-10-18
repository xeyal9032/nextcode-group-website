// Pricing Page JavaScript
console.log('Pricing.js loading...');

// Global pricing toggle function - defined immediately
window.togglePricing = function(isYearly) {
    console.log('togglePricing called with:', isYearly);
    const monthlyPrices = document.querySelectorAll('.monthly-price');
    const yearlyPrices = document.querySelectorAll('.yearly-price');
    const pricingCards = document.querySelectorAll('.pricing-card');
    
    // Add flip animation
    pricingCards.forEach(card => {
        card.style.animation = 'priceFlip 0.6s ease';
    });
    
    setTimeout(() => {
        if (isYearly) {
            monthlyPrices.forEach(price => price.style.display = 'none');
            yearlyPrices.forEach(price => price.style.display = 'block');
        } else {
            monthlyPrices.forEach(price => price.style.display = 'block');
            yearlyPrices.forEach(price => price.style.display = 'none');
        }
    }, 300);
    
    setTimeout(() => {
        pricingCards.forEach(card => {
            card.style.animation = '';
        });
    }, 600);
    
    // Update toggle label
    const toggleLabel = document.querySelector('.pricing-toggle-label');
    if (toggleLabel) {
        toggleLabel.textContent = isYearly ? 'İllik (20% endirim)' : 'Aylıq';
    }
};

console.log('togglePricing defined:', typeof window.togglePricing);

// DOM ready event
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready in pricing.js');
    
    // Pricing toggle (monthly/yearly)
    const pricingToggle = document.getElementById('pricingToggle');
    
    if (pricingToggle) {
        console.log('pricingToggle element found');
        pricingToggle.addEventListener('change', function() {
            console.log('Toggle changed:', this.checked);
            window.togglePricing(this.checked);
        });
    } else {
        console.log('pricingToggle element not found');
    }
});

// Package modal functions
window.openPackageModal = function(packageType) {
    console.log('Opening package modal for:', packageType);
    const modal = new bootstrap.Modal(document.getElementById('packageModal'));
    modal.show();
    
    // Update modal content based on package type
    const modalTitle = document.querySelector('#packageModal .modal-title');
    const modalBody = document.querySelector('#packageModal .modal-body');
    
    if (modalTitle && modalBody) {
        switch(packageType) {
            case 'basic':
                modalTitle.textContent = 'Başlanğıc Paketi';
                modalBody.innerHTML = `
                    <h5>Başlanğıc Paketi Xüsusiyyətləri:</h5>
                    <ul>
                        <li>5 SEO Açar Sözü</li>
                        <li>2 Sosyal Medya Platformu</li>
                        <li>Aylıq Hesabat</li>
                        <li>Email Dəstək</li>
                    </ul>
                    <p><strong>Qiymət:</strong> ₼299/ay</p>
                `;
                break;
            case 'professional':
                modalTitle.textContent = 'Peşəkar Paketi';
                modalBody.innerHTML = `
                    <h5>Peşəkar Paketi Xüsusiyyətləri:</h5>
                    <ul>
                        <li>15 SEO Açar Sözü</li>
                        <li>4 Sosyal Medya Platformu</li>
                        <li>Həftəlik Hesabat</li>
                        <li>Google Ads</li>
                        <li>Brendinq Xidmətləri</li>
                        <li>Telefon + Email Dəstək</li>
                    </ul>
                    <p><strong>Qiymət:</strong> ₼599/ay</p>
                `;
                break;
            case 'enterprise':
                modalTitle.textContent = 'Korporativ Paketi';
                modalBody.innerHTML = `
                    <h5>Korporativ Paketi Xüsusiyyətləri:</h5>
                    <ul>
                        <li>Sınırsız SEO Açar Sözü</li>
                        <li>Bütün Sosyal Medya Platformları</li>
                        <li>Gündəlik Hesabat</li>
                        <li>Tam Reklam Paketi</li>
                        <li>Tam Brendinq</li>
                        <li>24/7 VIP Dəstək</li>
                        <li>Şəxsi Hesab Meneceri</li>
                    </ul>
                    <p><strong>Qiymət:</strong> ₼999/ay</p>
                `;
                break;
        }
    }
};

// Calculator functionality
function updateCalculator() {
    const seo = parseInt(document.getElementById('seoService')?.value || 0);
    const social = parseInt(document.getElementById('socialMedia')?.value || 0);
    const ads = parseInt(document.getElementById('advertising')?.value || 0);
    const branding = parseInt(document.getElementById('branding')?.value || 0);
    const web = parseInt(document.getElementById('webDesign')?.value || 0);
    const analytics = parseInt(document.getElementById('analytics')?.value || 0);
    
    const total = seo + social + ads + branding + web + analytics;
    const totalCostElement = document.getElementById('totalCost');
    if (totalCostElement) {
        totalCostElement.textContent = `₼${total}`;
    }
}

// Event listeners for calculator
document.addEventListener('DOMContentLoaded', function() {
    // Calculator event listeners
    const calculatorSelects = ['seoService', 'socialMedia', 'advertising', 'branding', 'webDesign', 'analytics'];
    calculatorSelects.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', updateCalculator);
        }
    });
    
    // Request quote button
    const requestQuoteBtn = document.getElementById('requestQuote');
    if (requestQuoteBtn) {
        requestQuoteBtn.addEventListener('click', function() {
            const totalCost = document.getElementById('totalCost')?.textContent || '₼0';
            window.location.href = `contact.html?package=custom&cost=${encodeURIComponent(totalCost)}`;
        });
    }
    
    // Plan buttons
    const planButtons = {
        'basicPlanBtn': 'basic',
        'professionalPlanBtn': 'professional', 
        'enterprisePlanBtn': 'enterprise'
    };
    
    Object.keys(planButtons).forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.addEventListener('click', function() {
                window.openPackageModal(planButtons[btnId]);
            });
        }
    });
});

console.log('Pricing.js loaded successfully');