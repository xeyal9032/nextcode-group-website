/**
 * NextCode Group - Portfolio Detail JavaScript
 * Modern ve interaktif portfolio detay sayfası fonksiyonları
 */

class PortfolioDetailManager {
    constructor() {
        this.currentImageIndex = 0;
        this.galleryImages = [];
        this.isLightboxOpen = false;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeAnimations();
        this.setupSmoothScrolling();
        this.initializeLightbox();
        this.setupTechStackInteractions();
        this.setupRelatedProjects();
        console.log('Portfolio Detail Manager initialized');
    }

    setupEventListeners() {
        // Gallery navigation
        const prevBtn = document.querySelector('.gallery-nav-prev');
        const nextBtn = document.querySelector('.gallery-nav-next');
        
        if (prevBtn) prevBtn.addEventListener('click', () => this.navigateGallery('prev'));
        if (nextBtn) nextBtn.addEventListener('click', () => this.navigateGallery('next'));

        // Tech stack interactions
        const techTags = document.querySelectorAll('.tech-tag');
        techTags.forEach(tag => {
            tag.addEventListener('click', () => this.handleTechTagClick(tag));
            tag.addEventListener('mouseenter', () => this.animateTechTag(tag, 'enter'));
            tag.addEventListener('mouseleave', () => this.animateTechTag(tag, 'leave'));
        });

        // Project stats animation
        this.animateProjectStats();

        // Smooth scroll for anchor links
        this.setupSmoothScrolling();
    }

    initializeAnimations() {
        // Intersection Observer for fade-in animations
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

        // Observe elements for animation
        const animateElements = document.querySelectorAll('.overview-item, .tech-category, .gallery-item, .related-card');
        animateElements.forEach(el => observer.observe(el));
    }

    setupSmoothScrolling() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    initializeLightbox() {
        const galleryItems = document.querySelectorAll('.gallery-item');
        galleryItems.forEach((item, index) => {
            item.addEventListener('click', () => this.openLightbox(index));
        });

        // Close lightbox on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isLightboxOpen) {
                this.closeLightbox();
            }
        });
    }

    openLightbox(imageIndex) {
        const galleryItems = document.querySelectorAll('.gallery-item img');
        if (!galleryItems[imageIndex]) return;

        this.currentImageIndex = imageIndex;
        this.isLightboxOpen = true;

        const lightbox = document.createElement('div');
        lightbox.className = 'lightbox-overlay';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <button class="lightbox-close" aria-label="Close lightbox">
                    <i class="fas fa-times"></i>
                </button>
                <button class="lightbox-nav lightbox-prev" aria-label="Previous image">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="lightbox-nav lightbox-next" aria-label="Next image">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="lightbox-image-container">
                    <img src="${galleryItems[imageIndex].src}" alt="Project image ${imageIndex + 1}" class="lightbox-image">
                </div>
                <div class="lightbox-counter">
                    ${imageIndex + 1} / ${galleryItems.length}
                </div>
            </div>
        `;

        // Add lightbox styles
        this.addLightboxStyles();

        document.body.appendChild(lightbox);
        document.body.style.overflow = 'hidden';

        // Setup lightbox event listeners
        this.setupLightboxEvents(lightbox);
    }

    addLightboxStyles() {
        if (document.getElementById('lightbox-styles')) return;

        const styles = document.createElement('style');
        styles.id = 'lightbox-styles';
        styles.textContent = `
            .lightbox-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.95);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                opacity: 0;
                animation: lightboxFadeIn 0.3s ease forwards;
            }

            .lightbox-content {
                position: relative;
                max-width: 90%;
                max-height: 90%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .lightbox-image-container {
                position: relative;
                margin: 20px 0;
            }

            .lightbox-image {
                max-width: 100%;
                max-height: 80vh;
                object-fit: contain;
                border-radius: 8px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            }

            .lightbox-close {
                position: absolute;
                top: -40px;
                right: 0;
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 18px;
                transition: all 0.3s ease;
            }

            .lightbox-close:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }

            .lightbox-nav {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 20px;
                transition: all 0.3s ease;
            }

            .lightbox-nav:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }

            .lightbox-prev {
                left: -60px;
            }

            .lightbox-next {
                right: -60px;
            }

            .lightbox-counter {
                color: white;
                font-size: 14px;
                margin-top: 10px;
                opacity: 0.8;
            }

            @keyframes lightboxFadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @media (max-width: 768px) {
                .lightbox-nav {
                    width: 40px;
                    height: 40px;
                    font-size: 16px;
                }
                
                .lightbox-prev { left: -50px; }
                .lightbox-next { right: -50px; }
            }
        `;

        document.head.appendChild(styles);
    }

    setupLightboxEvents(lightbox) {
        const closeBtn = lightbox.querySelector('.lightbox-close');
        const prevBtn = lightbox.querySelector('.lightbox-prev');
        const nextBtn = lightbox.querySelector('.lightbox-next');

        closeBtn.addEventListener('click', () => this.closeLightbox());
        prevBtn.addEventListener('click', () => this.navigateLightbox('prev'));
        nextBtn.addEventListener('click', () => this.navigateLightbox('next'));

        // Close on overlay click
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                this.closeLightbox();
            }
        });
    }

    navigateLightbox(direction) {
        const galleryItems = document.querySelectorAll('.gallery-item img');
        const totalImages = galleryItems.length;

        if (direction === 'prev') {
            this.currentImageIndex = this.currentImageIndex > 0 ? this.currentImageIndex - 1 : totalImages - 1;
        } else {
            this.currentImageIndex = this.currentImageIndex < totalImages - 1 ? this.currentImageIndex + 1 : 0;
        }

        // Update lightbox image
        const lightboxImage = document.querySelector('.lightbox-image');
        const lightboxCounter = document.querySelector('.lightbox-counter');
        
        if (lightboxImage) {
            lightboxImage.src = galleryItems[this.currentImageIndex].src;
        }
        
        if (lightboxCounter) {
            lightboxCounter.textContent = `${this.currentImageIndex + 1} / ${totalImages}`;
        }
    }

    closeLightbox() {
        const lightbox = document.querySelector('.lightbox-overlay');
        if (lightbox) {
            lightbox.style.animation = 'lightboxFadeOut 0.3s ease forwards';
            setTimeout(() => {
                document.body.removeChild(lightbox);
                document.body.style.overflow = '';
                this.isLightboxOpen = false;
            }, 300);
        }
    }

    setupTechStackInteractions() {
        const techCategories = document.querySelectorAll('.tech-category');
        
        techCategories.forEach(category => {
            category.addEventListener('mouseenter', () => {
                this.animateTechCategory(category, 'enter');
            });
            
            category.addEventListener('mouseleave', () => {
                this.animateTechCategory(category, 'leave');
            });
        });
    }

    animateTechCategory(category, action) {
        const techTags = category.querySelectorAll('.tech-tag');
        
        techTags.forEach((tag, index) => {
            if (action === 'enter') {
                tag.style.animationDelay = `${index * 0.1}s`;
                tag.classList.add('tech-tag-hover');
            } else {
                tag.classList.remove('tech-tag-hover');
            }
        });
    }

    animateTechTag(tag, action) {
        if (action === 'enter') {
            tag.style.transform = 'translateY(-3px) scale(1.05)';
            tag.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.3)';
        } else {
            tag.style.transform = 'translateY(0) scale(1)';
            tag.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.1)';
        }
    }

    animateProjectStats() {
        const statNumbers = document.querySelectorAll('.project-stat-number');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateNumber(entry.target);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(stat => observer.observe(stat));
    }

    animateNumber(element) {
        const finalValue = element.textContent;
        const isPercentage = finalValue.includes('%');
        const isCurrency = finalValue.includes('$');
        const isDuration = finalValue.includes('Ay') || finalValue.includes('Nəfər');
        
        let numericValue = 0;
        let suffix = '';
        
        if (isPercentage) {
            numericValue = parseInt(finalValue);
            suffix = '%';
        } else if (isCurrency) {
            numericValue = parseInt(finalValue.replace(/[^\d]/g, ''));
            suffix = finalValue.replace(/[\d]/g, '');
        } else if (isDuration) {
            numericValue = parseInt(finalValue);
            suffix = finalValue.replace(/[\d]/g, '');
        } else {
            numericValue = parseInt(finalValue);
        }

        const duration = 2000;
        const increment = numericValue / (duration / 16);
        let currentValue = 0;

        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= numericValue) {
                currentValue = numericValue;
                clearInterval(timer);
            }
            
            element.textContent = Math.floor(currentValue) + suffix;
        }, 16);
    }

    setupRelatedProjects() {
        const relatedCards = document.querySelectorAll('.related-card');
        
        relatedCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                this.animateRelatedCard(card, 'enter');
            });
            
            card.addEventListener('mouseleave', () => {
                this.animateRelatedCard(card, 'leave');
            });
        });
    }

    animateRelatedCard(card, action) {
        const image = card.querySelector('img');
        const content = card.querySelector('.related-card-content');
        
        if (action === 'enter') {
            card.style.transform = 'translateY(-10px) scale(1.02)';
            card.style.boxShadow = '0 20px 60px rgba(0, 0, 0, 0.15)';
            
            if (image) image.style.transform = 'scale(1.05)';
            if (content) content.style.transform = 'translateY(-5px)';
        } else {
            card.style.transform = 'translateY(0) scale(1)';
            card.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
            
            if (image) image.style.transform = 'scale(1)';
            if (content) content.style.transform = 'translateY(0)';
        }
    }

    // Utility functions
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Performance monitoring
    trackUserInteraction(action, element) {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'portfolio_interaction', {
                'event_category': 'portfolio_detail',
                'event_label': action,
                'value': 1
            });
        }
        
        console.log(`Portfolio interaction: ${action}`, element);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.portfolioDetailManager = new PortfolioDetailManager();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PortfolioDetailManager;
}
