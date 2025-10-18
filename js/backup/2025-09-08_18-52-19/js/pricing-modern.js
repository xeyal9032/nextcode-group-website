// Modern Pricing Page JavaScript

class PricingManager {
    constructor() {
        this.isYearly = false;
        this.init();
    }

    init() {
        this.setupPricingToggle();
        this.setupPlanButtons();
        this.setupFAQ();
        this.setupCalculator();
        this.setupAnimations();
        this.setupComparison();
    }

    // Pricing Toggle Functionality
    setupPricingToggle() {
        const toggle = document.getElementById('pricingToggle');
        const monthlyLabels = document.querySelectorAll('.monthly-label');
        const yearlyLabels = document.querySelectorAll('.yearly-label');
        const prices = document.querySelectorAll('.monthly-price');

        if (toggle) {
            toggle.addEventListener('change', (e) => {
                this.isYearly = e.target.checked;
                this.updatePrices();
                this.updateLabels();
                this.animatePriceChange();
            });
        }
    }

    updatePrices() {
        const prices = document.querySelectorAll('.monthly-price');
        prices.forEach(price => {
            const monthlyPrice = parseInt(price.dataset.monthly);
            const yearlyPrice = parseInt(price.dataset.yearly);
            
            if (this.isYearly) {
                price.textContent = Math.round(yearlyPrice / 12);
            } else {
                price.textContent = monthlyPrice;
            }
        });
    }

    updateLabels() {
        const monthlyLabels = document.querySelectorAll('.monthly-label');
        const yearlyLabels = document.querySelectorAll('.yearly-label');
        
        monthlyLabels.forEach(label => {
            label.classList.toggle('active', !this.isYearly);
        });
        
        yearlyLabels.forEach(label => {
            label.classList.toggle('active', this.isYearly);
        });
    }

    animatePriceChange() {
        const priceContainers = document.querySelectorAll('.price-container');
        priceContainers.forEach(container => {
            container.style.transform = 'scale(1.1)';
            setTimeout(() => {
                container.style.transform = 'scale(1)';
            }, 200);
        });
    }

    // Plan Selection
    setupPlanButtons() {
        const planButtons = document.querySelectorAll('.plan-btn');
        planButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const planCard = e.target.closest('.pricing-card');
                const planTitle = planCard.querySelector('.plan-title').textContent;
                const planPrice = planCard.querySelector('.amount').textContent;
                
                this.selectPlan(planTitle, planPrice);
            });
        });
    }

    selectPlan(title, price) {
        // Show selection modal or redirect to contact
        const modal = document.getElementById('packageModal');
        if (modal) {
            const modalTitle = modal.querySelector('.modal-title');
            const selectedPlan = modal.querySelector('#selectedPlan');
            
            if (modalTitle) modalTitle.textContent = `${title} Paketi Seçimi`;
            if (selectedPlan) selectedPlan.value = `${title} - ₼${price}/ay`;
            
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }
    }

    // FAQ Accordion
    setupFAQ() {
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                
                // Close all other FAQ items
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });
                
                // Toggle current item
                item.classList.toggle('active', !isActive);
            });
        });
    }

    // Price Calculator
    setupCalculator() {
        const calculatorInputs = document.querySelectorAll('#calculatorForm select');
        const totalCostElement = document.getElementById('totalCost');
        
        calculatorInputs.forEach(input => {
            input.addEventListener('change', () => {
                this.calculateTotal();
            });
        });
    }

    calculateTotal() {
        const seoService = parseInt(document.getElementById('seoService')?.value || 0);
        const socialMedia = parseInt(document.getElementById('socialMedia')?.value || 0);
        const advertising = parseInt(document.getElementById('advertising')?.value || 0);
        const branding = parseInt(document.getElementById('branding')?.value || 0);
        const webDesign = parseInt(document.getElementById('webDesign')?.value || 0);
        const analytics = parseInt(document.getElementById('analytics')?.value || 0);
        
        const total = seoService + socialMedia + advertising + branding + webDesign + analytics;
        
        const totalCostElement = document.getElementById('totalCost');
        if (totalCostElement) {
            totalCostElement.textContent = `₼${total}`;
            
            // Animate the total change
            totalCostElement.style.transform = 'scale(1.2)';
            totalCostElement.style.color = '#667eea';
            setTimeout(() => {
                totalCostElement.style.transform = 'scale(1)';
                totalCostElement.style.color = '';
            }, 300);
        }
    }

    // Animations
    setupAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe pricing cards
        const pricingCards = document.querySelectorAll('.pricing-card');
        pricingCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            observer.observe(card);
        });

        // Observe FAQ items
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.05}s`;
            observer.observe(item);
        });
    }

    // Comparison Features
    setupComparison() {
        const compareBtn = document.getElementById('comparePackages');
        if (compareBtn) {
            compareBtn.addEventListener('click', () => {
                const modal = document.getElementById('comparisonModal');
                if (modal) {
                    const bootstrapModal = new bootstrap.Modal(modal);
                    bootstrapModal.show();
                }
            });
        }
    }

    // Utility Methods
    formatPrice(price) {
        return new Intl.NumberFormat('az-AZ', {
            style: 'currency',
            currency: 'AZN',
            minimumFractionDigits: 0
        }).format(price);
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
}

// Enhanced Form Handling
class FormHandler {
    constructor() {
        this.setupPackageForm();
        this.setupCalculatorForm();
    }

    setupPackageForm() {
        const submitBtn = document.getElementById('submitPackage');
        if (submitBtn) {
            submitBtn.addEventListener('click', () => {
                this.submitPackageSelection();
            });
        }
    }

    setupCalculatorForm() {
        const requestQuoteBtn = document.getElementById('requestQuote');
        if (requestQuoteBtn) {
            requestQuoteBtn.addEventListener('click', () => {
                this.requestCustomQuote();
            });
        }
    }

    async submitPackageSelection() {
        const form = document.getElementById('packageForm');
        const formData = new FormData(form);
        
        try {
            // Show loading state
            const submitBtn = document.getElementById('submitPackage');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Göndərilir...';
            submitBtn.disabled = true;
            
            // Simulate API call (replace with actual endpoint)
            await new Promise(resolve => setTimeout(resolve, 1500));
            
            // Success handling
            pricingManager.showNotification('Paket seçiminiz uğurla göndərildi! Tezliklə sizinlə əlaqə saxlayacağıq.', 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('packageModal'));
            modal.hide();
            
            // Reset form
            form.reset();
            
        } catch (error) {
            pricingManager.showNotification('Xəta baş verdi. Zəhmət olmasa yenidən cəhd edin.', 'error');
        } finally {
            // Reset button
            const submitBtn = document.getElementById('submitPackage');
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    }

    async requestCustomQuote() {
        const totalCost = document.getElementById('totalCost').textContent;
        
        try {
            // Show loading state
            const requestBtn = document.getElementById('requestQuote');
            const originalText = requestBtn.textContent;
            requestBtn.textContent = 'Göndərilir...';
            requestBtn.disabled = true;
            
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 1500));
            
            // Success handling
            pricingManager.showNotification(`${totalCost} məbləğində fərdi təklif sorğunuz göndərildi!`, 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('calculatorModal'));
            modal.hide();
            
        } catch (error) {
            pricingManager.showNotification('Xəta baş verdi. Zəhmət olmasa yenidən cəhd edin.', 'error');
        } finally {
            // Reset button
            const requestBtn = document.getElementById('requestQuote');
            requestBtn.textContent = originalText;
            requestBtn.disabled = false;
        }
    }
}

// Smooth Scrolling Enhancement
class SmoothScroll {
    constructor() {
        this.setupSmoothScrolling();
    }

    setupSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.pricingManager = new PricingManager();
    window.formHandler = new FormHandler();
    window.smoothScroll = new SmoothScroll();
    initializeTrustSection();
    initializeEnterpriseModals();
    initializeConversionOptimization();
    
    // Add loading animation completion
    document.body.classList.add('loaded');
});

// Conversion Optimization Functions
function initializeConversionOptimization() {
    // Initialize remaining spots counter
    initializeRemainingSpots();
    
    // Initialize urgency timer
    initializeUrgencyTimer();
    
    // Initialize social proof animations
    initializeSocialProofAnimations();
}

function initializeRemainingSpots() {
    const spotsElement = document.getElementById('remainingSpots');
    if (!spotsElement) return;
    
    let currentSpots = parseInt(spotsElement.textContent);
    
    // Simulate decreasing spots every 30-60 seconds
    setInterval(() => {
        if (currentSpots > 5) {
            currentSpots--;
            animateValue(spotsElement, parseInt(spotsElement.textContent), currentSpots, 1000);
        }
    }, Math.random() * 30000 + 30000); // Random between 30-60 seconds
}

function initializeUrgencyTimer() {
    const timerElements = document.querySelectorAll('.urgency-timer');
    
    timerElements.forEach(timer => {
        // Add pulsing effect on hover
        timer.addEventListener('mouseenter', () => {
            timer.style.animation = 'pulse 0.5s ease-in-out 3';
        });
        
        timer.addEventListener('mouseleave', () => {
            timer.style.animation = 'pulse 2s infinite';
        });
    });
}

function initializeSocialProofAnimations() {
    const proofItems = document.querySelectorAll('.proof-item');
    
    // Animate numbers on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const strongElement = entry.target.querySelector('strong');
                if (strongElement && !strongElement.classList.contains('animated')) {
                    strongElement.classList.add('animated');
                    animateProofNumber(strongElement);
                }
            }
        });
    }, { threshold: 0.5 });
    
    proofItems.forEach(item => observer.observe(item));
}

function animateValue(elementOrId, start, end, duration, formatter = null) {
    const element = typeof elementOrId === 'string' ? document.getElementById(elementOrId) : elementOrId;
    if (!element) return;
    
    const startTime = performance.now();
    
    function updateValue(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = start + (end - start) * easeOutQuart(progress);
        
        if (formatter && typeof formatter === 'function') {
            element.textContent = formatter(current);
        } else if (typeof formatter === 'string') {
            if (formatter === '%') {
                element.textContent = Math.round(current) + formatter;
            } else if (formatter === ' AZN') {
                element.textContent = Math.round(current).toLocaleString() + formatter;
            } else {
                element.textContent = Math.round(current) + formatter;
            }
        } else {
            element.textContent = Math.round(current);
        }
        
        if (progress < 1) {
            requestAnimationFrame(updateValue);
        }
    }
    
    requestAnimationFrame(updateValue);
}

function easeOutQuart(t) {
    return 1 - Math.pow(1 - t, 4);
}

function animateProofNumber(element) {
    const text = element.textContent;
    const isRating = text.includes('/');
    const isPercentage = text.includes('%');
    
    if (isRating) {
        // Animate rating from 0.0 to target
        const targetValue = parseFloat(text);
        animateValue(element, 0, targetValue, 2000, (value) => {
            return value.toFixed(1) + '/5';
        });
    } else if (isPercentage) {
        // Animate percentage from 0% to target
        const targetValue = parseFloat(text);
        animateValue(element, 0, targetValue, 2000, (value) => {
            return value.toFixed(1) + '%';
        });
    } else {
        // Animate regular numbers
        const targetValue = parseInt(text.replace(/,/g, ''));
        animateValue(element, 0, targetValue, 2000, (value) => {
            return Math.floor(value).toLocaleString();
        });
    }
}

// Enterprise Modal Functions
function openConsultationModal() {
    document.getElementById('consultationModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function openQuoteModal() {
    document.getElementById('quoteModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function openTeamModal() {
    // For now, redirect to quote modal with pre-selected team option
    openQuoteModal();
    // Pre-select dedicated team service
    const teamCheckbox = document.querySelector('input[value="custom-software"]');
    if (teamCheckbox) {
        teamCheckbox.checked = true;
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

function initializeEnterpriseModals() {
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const consultationModal = document.getElementById('consultationModal');
        const quoteModal = document.getElementById('quoteModal');
        
        if (event.target === consultationModal) {
            closeModal('consultationModal');
        }
        if (event.target === quoteModal) {
            closeModal('quoteModal');
        }
    });
    
    // Handle consultation form submission
    const consultationForm = document.getElementById('consultationForm');
    if (consultationForm) {
        consultationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                name: document.getElementById('consult-name').value,
                email: document.getElementById('consult-email').value,
                phone: document.getElementById('consult-phone').value,
                company: document.getElementById('consult-company').value,
                project: document.getElementById('consult-project').value,
                date: document.getElementById('consult-date').value,
                type: 'consultation'
            };
            
            // Simulate form submission
            submitEnterpriseForm(formData, 'consultation');
        });
    }
    
    // Handle quote form submission
    const quoteForm = document.getElementById('quoteForm');
    if (quoteForm) {
        quoteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedServices = [];
            document.querySelectorAll('#quoteModal input[type="checkbox"]:checked').forEach(checkbox => {
                selectedServices.push(checkbox.value);
            });
            
            const formData = {
                name: document.getElementById('quote-name').value,
                email: document.getElementById('quote-email').value,
                services: selectedServices,
                budget: document.getElementById('quote-budget').value,
                timeline: document.getElementById('quote-timeline').value,
                details: document.getElementById('quote-details').value,
                type: 'quote'
            };
            
            if (selectedServices.length === 0) {
                alert('Zəhmət olmasa ən azı bir xidmət seçin');
                return;
            }
            
            // Simulate form submission
            submitEnterpriseForm(formData, 'quote');
        });
    }
    
    // Set minimum date to today for consultation booking
    const consultDateInput = document.getElementById('consult-date');
    if (consultDateInput) {
        const today = new Date().toISOString().split('T')[0];
        consultDateInput.min = today;
    }
}

function submitEnterpriseForm(formData, type) {
    // Show loading state
    const submitBtn = document.querySelector(`#${type}Form .submit-btn`);
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Göndərilir...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        // Show success message
        if (type === 'consultation') {
            alert('Məsləhət tələbiniz uğurla göndərildi! Tezliklə sizinlə əlaqə saxlayacağıq.');
            closeModal('consultationModal');
            document.getElementById('consultationForm').reset();
        } else {
            alert('Qiymət tələbiniz uğurla göndərildi! 24 saat ərzində sizinlə əlaqə saxlayacağıq.');
            closeModal('quoteModal');
            document.getElementById('quoteForm').reset();
        }
        
        // In a real application, you would send this data to your backend
        console.log('Form submitted:', formData);
    }, 2000);
}



// Trust Section Functionality
function initializeTrustSection() {
    // Animated counter for statistics
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statNumbers = entry.target.querySelectorAll('.stat-number');
                statNumbers.forEach(statNumber => {
                    animateCounter(statNumber);
                });
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const trustStats = document.querySelector('.trust-stats');
    if (trustStats) {
        observer.observe(trustStats);
    }

    // Testimonial carousel functionality
    initializeTestimonialCarousel();
}

// Animate counter numbers
function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-count'));
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;

    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

// Testimonial carousel
function initializeTestimonialCarousel() {
    const testimonials = document.querySelectorAll('.testimonial-card');
    if (testimonials.length === 0) return;

    let currentIndex = 0;
    const totalTestimonials = testimonials.length;

    // Add animation classes
    testimonials.forEach((testimonial, index) => {
        testimonial.style.opacity = index === 0 ? '1' : '0.7';
        testimonial.style.transform = index === 0 ? 'scale(1)' : 'scale(0.95)';
    });

    // Auto-rotate testimonials
    setInterval(() => {
        testimonials[currentIndex].style.opacity = '0.7';
        testimonials[currentIndex].style.transform = 'scale(0.95)';
        
        currentIndex = (currentIndex + 1) % totalTestimonials;
        
        testimonials[currentIndex].style.opacity = '1';
        testimonials[currentIndex].style.transform = 'scale(1)';
    }, 5000);

    // Add hover effects
    testimonials.forEach(testimonial => {
        testimonial.addEventListener('mouseenter', () => {
            testimonial.style.opacity = '1';
            testimonial.style.transform = 'scale(1.02)';
        });
        
        testimonial.addEventListener('mouseleave', () => {
            const index = Array.from(testimonials).indexOf(testimonial);
            if (index !== currentIndex) {
                testimonial.style.opacity = '0.7';
                testimonial.style.transform = 'scale(0.95)';
            }
        });
    });
}

// Add CSS for notifications
const notificationStyles = `
<style>
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    padding: 16px 20px;
    transform: translateX(400px);
    transition: transform 0.3s ease;
    z-index: 10000;
    max-width: 400px;
}

.notification.show {
    transform: translateX(0);
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.notification-success {
    border-left: 4px solid #059669;
}

.notification-error {
    border-left: 4px solid #ef4444;
}

.notification-success i {
    color: #059669;
}

.notification-error i {
    color: #ef4444;
}

.animate-in {
    animation: slideInUp 0.6s ease-out forwards;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

body.loaded .pricing-card {
    animation: slideInUp 0.6s ease-out forwards;
}

body.loaded .faq-item {
    animation: slideInUp 0.6s ease-out forwards;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.urgency-timer {
    animation: pulse 2s infinite;
}

.proof-item strong {
    transition: color 0.3s ease;
}

.proof-item strong.animated {
    color: #667eea;
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', notificationStyles);

// Initialize pricing manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize PricingManager
    window.pricingManager = new PricingManager();
    
    // Initialize conversion optimization
    initializeConversionOptimization();
    
    // Initialize enterprise modals
    initializeEnterpriseModals();
    
    // Initialize trust section
    initializeTrustSection();
    
    console.log('Pricing page initialized successfully');
});