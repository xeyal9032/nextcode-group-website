/**
 * Modern Interactions JavaScript
 * NextCode Group - Modern UI Interactions
 */

(function() {
    'use strict';

    // Modern Interactions Class
    class ModernInteractions {
        constructor() {
            this.init();
        }

        init() {
            this.setupAnimations();
            this.setupSmoothScrolling();
            this.setupFormEnhancements();
            this.setupCardInteractions();
            this.setupButtonEffects();
            this.setupLoadingStates();
            this.setupNotifications();
            this.setupModals();
            this.setupTooltips();
            this.setupProgressBars();
            this.setupThemeToggle();
        }

        // Animation Setup
        setupAnimations() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('modern-animate--visible');
                    }
                });
            }, observerOptions);

            // Observe all elements with animation classes
            document.querySelectorAll('.modern-animate--fadeInUp, .modern-animate--fadeInDown, .modern-animate--fadeInLeft, .modern-animate--fadeInRight, .modern-animate--scaleIn, .modern-animate--slideInUp').forEach(el => {
                observer.observe(el);
            });
        }

        // Smooth Scrolling
        setupSmoothScrolling() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        }

        // Form Enhancements
        setupFormEnhancements() {
            // Modern form inputs
            document.querySelectorAll('.modern-form-input, .modern-form-textarea, .modern-form-select').forEach(input => {
                // Focus effects
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('modern-form-group--focused');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('modern-form-group--focused');
                });

                // Auto-resize textareas
                if (input.tagName === 'TEXTAREA') {
                    input.addEventListener('input', function() {
                        this.style.height = 'auto';
                        this.style.height = this.scrollHeight + 'px';
                    });
                }
            });

            // Form validation
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const requiredFields = this.querySelectorAll('[required]');
                    let isValid = true;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('modern-form-input--error');
                        } else {
                            field.classList.remove('modern-form-input--error');
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        this.showNotification('Lütfen tüm gerekli alanları doldurun.', 'error');
                    }
                });
            });
        }

        // Card Interactions
        setupCardInteractions() {
            document.querySelectorAll('.modern-card').forEach(card => {
                // Hover effects
                card.addEventListener('mouseenter', function() {
                    this.classList.add('modern-card--hover');
                });

                card.addEventListener('mouseleave', function() {
                    this.classList.remove('modern-card--hover');
                });

                // Click effects
                card.addEventListener('click', function(e) {
                    if (!e.target.closest('a, button')) {
                        this.classList.add('modern-card--clicked');
                        setTimeout(() => {
                            this.classList.remove('modern-card--clicked');
                        }, 200);
                    }
                });
            });
        }

        // Button Effects
        setupButtonEffects() {
            document.querySelectorAll('.modern-btn').forEach(button => {
                // Ripple effect
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.classList.add('modern-btn__ripple');

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });

                // Loading state
                if (button.type === 'submit') {
                    button.addEventListener('click', function() {
                        if (this.form && this.form.checkValidity()) {
                            this.classList.add('modern-btn--loading');
                            this.disabled = true;
                        }
                    });
                }
            });
        }

        // Loading States
        setupLoadingStates() {
            // Add loading class to elements
            document.querySelectorAll('.modern-loading').forEach(element => {
                element.classList.add('modern-loading--active');
            });

            // Simulate loading for demo purposes
            setTimeout(() => {
                document.querySelectorAll('.modern-loading--active').forEach(element => {
                    element.classList.remove('modern-loading--active');
                });
            }, 2000);
        }

        // Notifications
        setupNotifications() {
            this.notificationContainer = document.createElement('div');
            this.notificationContainer.className = 'modern-notifications-container';
            this.notificationContainer.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                pointer-events: none;
            `;
            document.body.appendChild(this.notificationContainer);
        }

        showNotification(message, type = 'info', duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `modern-notification modern-notification--${type}`;
            notification.innerHTML = `
                <div class="modern-notification__content">
                    <span class="modern-notification__message">${message}</span>
                    <button class="modern-notification__close">&times;</button>
                </div>
            `;

            this.notificationContainer.appendChild(notification);

            // Show notification
            setTimeout(() => {
                notification.classList.add('modern-notification--show');
            }, 100);

            // Close button
            notification.querySelector('.modern-notification__close').addEventListener('click', () => {
                this.hideNotification(notification);
            });

            // Auto hide
            setTimeout(() => {
                this.hideNotification(notification);
            }, duration);
        }

        hideNotification(notification) {
            notification.classList.remove('modern-notification--show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }

        // Modals
        setupModals() {
            document.querySelectorAll('[data-modal]').forEach(trigger => {
                trigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    const modalId = trigger.getAttribute('data-modal');
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        this.openModal(modal);
                    }
                });
            });

            // Close modal on backdrop click
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('modern-modal')) {
                    this.closeModal(e.target);
                }
            });

            // Close modal on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    const openModal = document.querySelector('.modern-modal--active');
                    if (openModal) {
                        this.closeModal(openModal);
                    }
                }
            });
        }

        openModal(modal) {
            modal.classList.add('modern-modal--active');
            document.body.style.overflow = 'hidden';
        }

        closeModal(modal) {
            modal.classList.remove('modern-modal--active');
            document.body.style.overflow = '';
        }

        // Tooltips
        setupTooltips() {
            document.querySelectorAll('[data-tooltip]').forEach(element => {
                element.classList.add('modern-tooltip');
            });
        }

        // Progress Bars
        setupProgressBars() {
            document.querySelectorAll('.modern-progress').forEach(progress => {
                const bar = progress.querySelector('.modern-progress__bar');
                if (bar) {
                    const progressValue = progress.getAttribute('data-progress') || 0;
                    bar.style.width = progressValue + '%';
                }
            });
        }

        // Theme Toggle
        setupThemeToggle() {
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const currentTheme = document.documentElement.getAttribute('data-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    
                    // Update icon
                    const icon = themeToggle.querySelector('#themeIcon');
                    if (icon) {
                        icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                    }
                });
            }

            // Load saved theme
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
        }
    }

    // Portfolio Filter
    class PortfolioFilter {
        constructor() {
            this.init();
        }

        init() {
            this.setupFilters();
        }

        setupFilters() {
            const filterButtons = document.querySelectorAll('[data-filter]');
            const portfolioItems = document.querySelectorAll('[data-category]');

            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const filter = button.getAttribute('data-filter');
                    
                    // Update active button
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    // Filter items
                    portfolioItems.forEach(item => {
                        const category = item.getAttribute('data-category');
                        
                        if (filter === 'all' || category === filter) {
                            item.style.display = 'block';
                            item.classList.add('modern-animate--fadeInUp');
                        } else {
                            item.style.display = 'none';
                            item.classList.remove('modern-animate--fadeInUp');
                        }
                    });
                });
            });
        }
    }

    // Contact Form Handler
    class ContactFormHandler {
        constructor() {
            this.init();
        }

        init() {
            this.setupForm();
        }

        setupForm() {
            const form = document.getElementById('contactForm');
            if (form) {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleSubmit(form);
                });
            }
        }

        async handleSubmit(form) {
            const submitBtn = form.querySelector('#contactFormSubmit');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Gönderiliyor...';
            submitBtn.disabled = true;

            try {
                const formData = new FormData(form);
                const response = await fetch('contact-handler.php', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    this.showSuccess('Mesajınız başarıyla gönderildi!');
                    form.reset();
                } else {
                    throw new Error('Form submission failed');
                }
            } catch (error) {
                this.showError('Mesaj gönderilemedi. Lütfen tekrar deneyin.');
            } finally {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }

        showSuccess(message) {
            if (window.modernInteractions) {
                window.modernInteractions.showNotification(message, 'success');
            }
        }

        showError(message) {
            if (window.modernInteractions) {
                window.modernInteractions.showNotification(message, 'error');
            }
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        window.modernInteractions = new ModernInteractions();
        window.portfolioFilter = new PortfolioFilter();
        window.contactFormHandler = new ContactFormHandler();
    });

    // Export for global access
    window.ModernInteractions = ModernInteractions;
    window.PortfolioFilter = PortfolioFilter;
    window.ContactFormHandler = ContactFormHandler;

})();

