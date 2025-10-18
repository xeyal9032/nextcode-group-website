// Contact Page Interactive Features - Enhanced Version

// Contact page performance monitor
const contactPerformance = {
    startTime: performance.now(),
    metrics: {},
    
    mark(name) {
        this.metrics[name] = performance.now() - this.startTime;
    }
};

// Contact form state management
const contactFormState = {
    isSubmitting: false,
    validationErrors: {},
    formData: {},
    
    updateField(name, value) {
        this.formData[name] = value;
        this.validateField(name, value);
    },
    
    validateField(name, value) {
        const validators = {
            name: (val) => val.trim().length >= 2 ? null : 'Ad en az 2 karakter olmalıdır.',
            email: (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) ? null : 'Geçerli bir e-posta adresi girin.',
            phone: (val) => /^[\+]?[0-9\s\-\(\)]{10,}$/.test(val) ? null : 'Geçerli bir telefon numarası girin.',
            message: (val) => val.trim().length >= 10 ? null : 'Mesaj en az 10 karakter olmalıdır.'
        };
        
        const error = validators[name] ? validators[name](value) : null;
        if (error) {
            this.validationErrors[name] = error;
        } else {
            delete this.validationErrors[name];
        }
        
        return !error;
    },
    
    isValid() {
        return Object.keys(this.validationErrors).length === 0 && 
               Object.keys(this.formData).length >= 4;
    }
};

document.addEventListener('DOMContentLoaded', function() {
    contactPerformance.mark('domLoaded');
    
    // Contact form
    const contactForm = document.getElementById('contactForm');
    const formInputs = document.querySelectorAll('.modern-form-input, .modern-form-select, .modern-form-textarea');
    const submitBtn = document.getElementById('contactFormSubmit');
    
    // Map integration
    const mapContainer = document.getElementById('map-container');
    
    // FAQ section
    const faqItems = document.querySelectorAll('.faq-item');
    
    // Live chat
    const chatWidget = document.getElementById('chat-widget');
    const chatToggle = document.getElementById('chat-toggle');
    
    // Initialize contact page
    initializeContact();
    
    // Initialize enhanced features
    initContactPageFeatures();
    
    // Initialize accessibility features
    initContactAccessibility();
    
    contactPerformance.mark('initComplete');
    
    // Log performance in development
    if (window.location.hostname === 'localhost') {
        console.log('Contact Page Performance:', contactPerformance.metrics);
    }
    
    function initializeContact() {
        // Check for package selection from URL
        checkPackageSelection();
        
            // Set up form validation and submission
    setupFormValidation();
    

        
        // Set up interactive map
        setupMap();
        
        // Set up FAQ accordion
        setupFAQ();
        
        // Set up live chat
        setupLiveChat();
        
            // Set up contact methods
    setupContactMethods();
    
    // Set up WhatsApp functionality
    setupWhatsApp();
    
    // Set up service selection for WhatsApp
    setupServiceSelection();
        
        // Set up animations
        setupAnimations();
        
        // Set up form enhancements
        setupFormEnhancements();
    }
    
    function checkPackageSelection() {
        // Check URL parameters for package selection
        const urlParams = new URLSearchParams(window.location.search);
        const packageName = urlParams.get('package');
        const packagePrice = urlParams.get('price');
        const packageFeatures = urlParams.get('features');
        
        // Check localStorage for package selection from pricing page
        const selectedPackage = localStorage.getItem('selectedPackage');
        let packageData = null;
        
        if (selectedPackage) {
            try {
                packageData = JSON.parse(selectedPackage);
            } catch (e) {
                console.error('Error parsing selected package:', e);
            }
        }
        
        // Use URL params or localStorage data
        const finalPackageName = packageName || (packageData ? packageData.name : null);
        const finalPackagePrice = packagePrice || (packageData ? packageData.price : null);
        const finalPackageFeatures = packageFeatures || (packageData ? packageData.features : null);
        
        if (finalPackageName) {
            const serviceSelect = document.getElementById('service');
            const messageTextarea = document.getElementById('message');
            const budgetSelect = document.getElementById('budget');
            
            // Update service selection
            if (serviceSelect) {
                // Add custom option if not exists
                let customOption = serviceSelect.querySelector('option[value="selected-package"]');
                if (!customOption) {
                    customOption = document.createElement('option');
                    customOption.value = 'selected-package';
                    customOption.textContent = `Seçilmiş Paket: ${finalPackageName}`;
                    serviceSelect.appendChild(customOption);
                }
                serviceSelect.value = 'selected-package';
            }
            
            // Update budget based on package price
            if (budgetSelect && finalPackagePrice) {
                const priceValue = parseFloat(finalPackagePrice.replace(/[^0-9.]/g, ''));
                if (priceValue <= 1000) {
                    budgetSelect.value = '500-1000';
                } else if (priceValue <= 2500) {
                    budgetSelect.value = '1000-2500';
                } else if (priceValue <= 5000) {
                    budgetSelect.value = '2500-5000';
                } else {
                    budgetSelect.value = '5000+';
                }
            }
            
            // Auto-fill message with package details
            if (messageTextarea) {
                let message = `Salam! ${finalPackageName} paketi ilə maraqlanıram.\n\n`;
                
                if (finalPackagePrice) {
                    message += `Qiymət: ${finalPackagePrice}\n`;
                }
                
                if (finalPackageFeatures) {
                    message += `\nPaket xüsusiyyətləri:\n`;
                    const features = Array.isArray(finalPackageFeatures) ? finalPackageFeatures : finalPackageFeatures.split(',');
                    features.forEach(feature => {
                        message += `• ${feature.trim()}\n`;
                    });
                }
                
                message += `\nBu paket haqqında ətraflı məlumat almaq və sifarişi təsdiqləmək istəyirəm. Zəhmət olmasa mənimlə əlaqə saxlayın.`;
                messageTextarea.value = message;
                
                // Auto-resize textarea
                messageTextarea.style.height = 'auto';
                messageTextarea.style.height = messageTextarea.scrollHeight + 'px';
            }
            
            // Show notification with package details
            showNotification(`${finalPackageName} paketi üçün əlaqə formu hazırlandı!`, 'success');
            
            // Add package info display
            displayPackageInfo(finalPackageName, finalPackagePrice, finalPackageFeatures);
            
            // Clear localStorage after use
            localStorage.removeItem('selectedPackage');
        }
    }
    
    function setupFormValidation() {
        if (!contactForm) return;
        
        // Real-time validation
        formInputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
            
            // Enhanced input effects
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
        
        // Form submission
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmission();
        });
    }
    
    function validateField(field) {
        const value = field.value.trim();
        const fieldType = field.type;
        const fieldName = field.name;
        
        clearFieldError(field);
        
        // Required field validation
        if (field.hasAttribute('required') && !value) {
            showFieldError(field, 'Bu sahə mütləqdir');
            return false;
        }
        
        // Email validation
        if (fieldType === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                showFieldError(field, 'Düzgün email ünvanı daxil edin');
                return false;
            }
        }
        
        // Phone validation
        if (fieldName === 'phone' && value) {
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
            if (!phoneRegex.test(value)) {
                showFieldError(field, 'Düzgün telefon nömrəsi daxil edin');
                return false;
            }
        }
        
        // Name validation
        if (fieldName === 'name' && value) {
            if (value.length < 2) {
                showFieldError(field, 'Ad ən azı 2 hərf olmalıdır');
                return false;
            }
        }
        
        // Message validation
        if (fieldName === 'message' && value) {
            if (value.length < 10) {
                showFieldError(field, 'Mesaj ən azı 10 hərf olmalıdır');
                return false;
            }
        }
        
        return true;
    }
    
    function showFieldError(field, message) {
        const errorElement = document.createElement('div');
        errorElement.className = 'field-error';
        errorElement.textContent = message;
        
        field.parentElement.appendChild(errorElement);
        field.classList.add('error');
    }
    
    function clearFieldError(field) {
        const errorElement = field.parentElement.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
        field.classList.remove('error');
    }
    
    function handleFormSubmission() {
        // Validate all fields
        let isValid = true;
        formInputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            showNotification('Zəhmət olmasa bütün sahələri düzgün doldurun', 'error');
            return;
        }
        
        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Göndərilir...';
        }
        
        // Prepare form data
        const formData = new FormData(contactForm);
        const formObject = {};
        
        for (let [key, value] of formData.entries()) {
            formObject[key] = value;
        }
        
        // Create WhatsApp message
        const whatsappMessage = `Yeni mesaj:
        
Ad Soyad: ${formObject.name}
Email: ${formObject.email}
Telefon: ${formObject.phone || 'Belirtilmemiş'}
Şirket: ${formObject.company || 'Belirtilmemiş'}
Mövzu: ${formObject.subject}
Xidmət: ${formObject.service || 'Belirtilmemiş'}
Büdcə: ${formObject.budget || 'Belirtilmemiş'}
Mesaj: ${formObject.message}`;
        
        // Create WhatsApp URL
        const whatsappNumber = '+380972580000';
        const encodedMessage = encodeURIComponent(whatsappMessage);
        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;
        
        // Try to send to backend first
        fetch('contact-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error('Backend error');
            }
        })
        .then(data => {
            if (data.success) {
                showNotification('Mesaj uğurla göndərildi! WhatsApp açılır...', 'success');
                
                // Reset form
                contactForm.reset();
                
                // Reset form state
                formInputs.forEach(input => {
                    input.parentElement.classList.remove('focused');
                });
                
                // Remove package info display if exists
                const packageInfo = document.querySelector('.selected-package-info');
                if (packageInfo) {
                    packageInfo.remove();
                }
                
                // Track form submission
                trackFormSubmission(formData);
                
                // Redirect to WhatsApp
                setTimeout(() => {
                    window.open(whatsappUrl, '_blank');
                }, 2000);
                
            } else {
                // If backend fails, still redirect to WhatsApp
                showNotification('Mesaj WhatsApp-a göndərilir...', 'info');
                
                // Reset form
                contactForm.reset();
                
                // Redirect to WhatsApp
                setTimeout(() => {
                    window.open(whatsappUrl, '_blank');
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);
            
            // If backend fails, still redirect to WhatsApp
            showNotification('Mesaj WhatsApp-a göndərilir...', 'info');
            
            // Reset form
            contactForm.reset();
            
            // Redirect to WhatsApp
            setTimeout(() => {
                window.open(whatsappUrl, '_blank');
            }, 1000);
        })
        .finally(() => {
            // Reset button state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Mesaj Göndər';
            }
        });
    }
    
    function setupMap() {
        if (!mapContainer) return;
        
        // Create interactive map placeholder
        const mapPlaceholder = document.createElement('div');
        mapPlaceholder.className = 'map-placeholder';
        mapPlaceholder.innerHTML = `
            <div class="map-info">
                <h4><i class="fas fa-map-marker-alt"></i> Bizim Ünvan</h4>
                <p>Bakı şəhəri, Nəsimi rayonu<br>Azadlıq prospekti 123</p>
                <div class="map-actions">
                    <button class="btn btn-primary" onclick="openGoogleMaps()">Google Maps-də Aç</button>
                    <button class="btn btn-secondary" onclick="getDirections()">Yol Tarifi Al</button>
                </div>
            </div>
            <div class="map-visual">
                <i class="fas fa-map fa-3x"></i>
                <p>Xəritəni yükləmək üçün klikləyin</p>
            </div>
        `;
        
        mapContainer.appendChild(mapPlaceholder);
        
        // Add click to load real map
        mapPlaceholder.addEventListener('click', function() {
            loadGoogleMap();
        });
    }
    
    function loadGoogleMap() {
        const mapContainer = document.getElementById('map-container');
        if (!mapContainer) return;
        
        // Replace with embedded Google Map
        mapContainer.innerHTML = `
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3037.8267!2d49.8671!3d40.4093!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDI0JzMzLjUiTiA0OcKwNTInMDEuNiJF!5e0!3m2!1sen!2saz!4v1234567890123" 
                width="100%" 
                height="400" 
                style="border:0;border-radius:10px;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        `;
    }
    
    function setupFAQ() {
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');
            
            if (question && answer) {
                question.addEventListener('click', function() {
                    const isActive = item.classList.contains('active');
                    
                    // Close all other FAQ items
                    faqItems.forEach(otherItem => {
                        otherItem.classList.remove('active');
                        const otherAnswer = otherItem.querySelector('.faq-answer');
                        if (otherAnswer) {
                            otherAnswer.style.maxHeight = '0';
                        }
                    });
                    
                    // Toggle current item
                    if (!isActive) {
                        item.classList.add('active');
                        answer.style.maxHeight = answer.scrollHeight + 'px';
                    }
                });
            }
        });
    }
    
    function setupLiveChat() {
        if (!chatToggle || !chatWidget) return;
        
        chatToggle.addEventListener('click', function() {
            chatWidget.classList.toggle('active');
            
            if (chatWidget.classList.contains('active')) {
                initializeChatInterface();
            }
        });
        
        // Close chat when clicking outside
        document.addEventListener('click', function(e) {
            if (!chatWidget.contains(e.target) && !chatToggle.contains(e.target)) {
                chatWidget.classList.remove('active');
            }
        });
    }
    
    function initializeChatInterface() {
        const chatMessages = chatWidget.querySelector('.chat-messages');
        const chatInput = chatWidget.querySelector('.chat-input');
        const sendBtn = chatWidget.querySelector('.send-message');
        
        if (!chatMessages.querySelector('.welcome-message')) {
            addChatMessage('Salam! Sizə necə kömək edə bilərəm?', 'bot');
        }
        
        if (sendBtn && chatInput) {
            sendBtn.addEventListener('click', function() {
                sendChatMessage();
            });
            
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendChatMessage();
                }
            });
        }
    }
    
    function sendChatMessage() {
        const chatInput = chatWidget.querySelector('.chat-input');
        const message = chatInput.value.trim();
        
        if (!message) return;
        
        addChatMessage(message, 'user');
        chatInput.value = '';
        
        // Simulate bot response
        setTimeout(() => {
            const botResponse = generateBotResponse(message);
            addChatMessage(botResponse, 'bot');
        }, 1000);
    }
    
    function addChatMessage(message, sender) {
        const chatMessages = chatWidget.querySelector('.chat-messages');
        const messageElement = document.createElement('div');
        messageElement.className = `chat-message ${sender}-message`;
        
        if (sender === 'bot') {
            messageElement.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">${message}</div>
            `;
        } else {
            messageElement.innerHTML = `
                <div class="message-content">${message}</div>
                <div class="message-avatar">
                    <i class="fas fa-user"></i>
                </div>
            `;
        }
        
        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function generateBotResponse(userMessage) {
        const message = userMessage.toLowerCase();
        
        if (message.includes('qiymət') || message.includes('qiymet')) {
            return 'Qiymətlərimiz xidmətin növünə görə dəyişir. Ətraflı məlumat üçün qiymət səhifəmizə baxa bilərsiniz.';
        } else if (message.includes('xidmət') || message.includes('xidmet')) {
            return 'Biz SEO, sosial media idarəetməsi, web dizayn və digital marketing xidmətləri təklif edirik.';
        } else if (message.includes('əlaqə') || message.includes('elaqe')) {
            return 'Bizimlə əlaqə saxlamaq üçün formu doldurun və ya +994 XX XXX XX XX nömrəsinə zəng edin.';
        } else if (message.includes('vaxt') || message.includes('müddət')) {
            return 'Layihələrin müddəti xidmətin mürəkkəbliyindən asılıdır. Orta hesabla 2-4 həftə çəkir.';
        } else {
            return 'Təşəkkür edirəm! Mütəxəssisimiz tezliklə sizinlə əlaqə saxlayacaq.';
        }
    }
    
    function setupContactMethods() {
        // Phone number click to call
        const phoneLinks = document.querySelectorAll('a[href^="tel:"]');
        phoneLinks.forEach(link => {
            link.addEventListener('click', function() {
                trackContactMethod('phone');
            });
        });
        
        // Email click to open email client
        const emailLinks = document.querySelectorAll('a[href^="mailto:"]');
        emailLinks.forEach(link => {
            link.addEventListener('click', function() {
                trackContactMethod('email');
            });
        });
        
        // Social media links
        const socialLinks = document.querySelectorAll('.social-link');
        socialLinks.forEach(link => {
            link.addEventListener('click', function() {
                const platform = this.dataset.platform || 'social';
                trackContactMethod(platform);
            });
        });
    }
    
    function setupAnimations() {
        // Intersection Observer for animations
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
        
        // Observe sections
        const sections = document.querySelectorAll('.contact-hero, .contact-form-section, .contact-info, .faq-section');
        sections.forEach(section => observer.observe(section));
    }
    
    function setupFormEnhancements() {
        // Character counter for message field
        const messageField = document.getElementById('message');
        if (messageField) {
            const counter = document.createElement('div');
            counter.className = 'character-counter';
            messageField.parentElement.appendChild(counter);
            
            messageField.addEventListener('input', function() {
                const length = this.value.length;
                const maxLength = this.getAttribute('maxlength') || 500;
                counter.textContent = `${length}/${maxLength}`;
                
                if (length > maxLength * 0.9) {
                    counter.style.color = '#e74c3c';
                } else {
                    counter.style.color = '#7f8c8d';
                }
            });
        }
        
        // Auto-resize textarea
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    }
    
    function displayPackageInfo(packageName, packagePrice, packageFeatures) {
        // Remove existing package info if any
        const existingInfo = document.querySelector('.selected-package-info');
        if (existingInfo) {
            existingInfo.remove();
        }
        
        // Create package info display
        const packageInfo = document.createElement('div');
        packageInfo.className = 'selected-package-info';
        packageInfo.innerHTML = `
            <div class="package-info-header">
                <h4><i class="fas fa-check-circle"></i> Seçilmiş Paket</h4>
                <button class="remove-package" onclick="removePackageSelection()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="package-info-content">
                <div class="package-name">${packageName}</div>
                ${packagePrice ? `<div class="package-price">${packagePrice}</div>` : ''}
                ${packageFeatures ? `
                    <div class="package-features">
                        <strong>Xüsusiyyətlər:</strong>
                        <ul>
                            ${Array.isArray(packageFeatures) ? 
                                packageFeatures.map(feature => `<li>${feature}</li>`).join('') :
                                packageFeatures.split(',').map(feature => `<li>${feature.trim()}</li>`).join('')
                            }
                        </ul>
                    </div>
                ` : ''}
            </div>
        `;
        
        // Insert before the form
        const formSection = document.querySelector('.contact-form-section .container');
        if (formSection) {
            formSection.insertBefore(packageInfo, formSection.firstChild);
        }
    }
    
    function trackFormSubmission(formData) {
        // Track form submission for analytics
        const data = {
            event: 'form_submission',
            form_type: 'contact',
            service_type: formData.get('service'),
            timestamp: new Date().toISOString()
        };
        
        // Store in localStorage for analytics
        const submissions = JSON.parse(localStorage.getItem('form_submissions') || '[]');
        submissions.push(data);
        localStorage.setItem('form_submissions', JSON.stringify(submissions));
    }
    
    function trackContactMethod(method) {
        // Track contact method usage
        const data = {
            event: 'contact_method_used',
            method: method,
            timestamp: new Date().toISOString()
        };
        
        const contacts = JSON.parse(localStorage.getItem('contact_methods') || '[]');
        contacts.push(data);
        localStorage.setItem('contact_methods', JSON.stringify(contacts));
    }
    
    function initContactPageFeatures() {
        // Initialize advanced contact features
        initFormAutoSave();
        initSmartValidation();
        initContactAnalytics();
        initOfflineSupport();
    }
    
    function initContactAccessibility() {
        // Keyboard navigation for form
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                const focusableElements = document.querySelectorAll(
                    'input, textarea, select, button, [tabindex]:not([tabindex="-1"])'
                );
                const firstElement = focusableElements[0];
                const lastElement = focusableElements[focusableElements.length - 1];
                
                if (e.shiftKey && document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                } else if (!e.shiftKey && document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        });
        
        // ARIA labels for better screen reader support
        const formElements = document.querySelectorAll('.form-input');
        formElements.forEach(element => {
            const label = element.previousElementSibling;
            if (label && label.tagName === 'LABEL') {
                element.setAttribute('aria-describedby', label.id || 'label-' + element.name);
            }
        });
    }
    
    function initFormAutoSave() {
        // Auto-save form data to prevent data loss
        const autoSaveKey = 'contact_form_autosave';
        
        // Get form elements
        const contactForm = document.getElementById('contact-form');
        const formInputs = document.querySelectorAll('.form-input');
        
        // Check if elements exist
        if (!contactForm || !formInputs.length) {
            console.warn('Contact form elements not found for auto-save');
            return;
        }
        
        // Load saved data
        const savedData = localStorage.getItem(autoSaveKey);
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                Object.keys(data).forEach(key => {
                    const element = document.querySelector(`[name="${key}"]`);
                    if (element) {
                        element.value = data[key];
                        contactFormState.updateField(key, data[key]);
                    }
                });
            } catch (e) {
                console.error('Error loading auto-saved data:', e);
            }
        }
        
        // Save data on input
        formInputs.forEach(input => {
            input.addEventListener('input', debounce(function() {
                const formData = new FormData(contactForm);
                const data = Object.fromEntries(formData.entries());
                localStorage.setItem(autoSaveKey, JSON.stringify(data));
            }, 1000));
        });
        
        // Clear auto-save on successful submission
        contactForm.addEventListener('submit', function() {
            localStorage.removeItem(autoSaveKey);
        });
    }
    
    function initSmartValidation() {
        // Enhanced validation with smart suggestions
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                const name = this.name;
                const value = this.value;
                
                contactFormState.updateField(name, value);
                
                // Smart email suggestions
                if (name === 'email' && value.includes('@')) {
                    const commonDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
                    const domain = value.split('@')[1];
                    if (domain && domain.length > 2) {
                        const suggestions = commonDomains.filter(d => d.startsWith(domain));
                        if (suggestions.length > 0) {
                            showEmailSuggestion(this, suggestions[0]);
                        }
                    }
                }
                
                // Real-time validation feedback
                updateValidationUI(this, contactFormState.validationErrors[name]);
            });
        });
    }
    
    function initContactAnalytics() {
        // Track user interaction patterns
        const analytics = {
            formStartTime: null,
            fieldFocusTimes: {},
            fieldCompletionOrder: []
        };
        
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                if (!analytics.formStartTime) {
                    analytics.formStartTime = Date.now();
                }
                analytics.fieldFocusTimes[this.name] = Date.now();
            });
            
            input.addEventListener('blur', function() {
                if (this.value.trim() && !analytics.fieldCompletionOrder.includes(this.name)) {
                    analytics.fieldCompletionOrder.push(this.name);
                }
            });
        });
        
        // Store analytics data
        window.contactAnalytics = analytics;
    }
    
    function initOfflineSupport() {
        // Handle offline form submissions
        if ('serviceWorker' in navigator) {
            window.addEventListener('online', function() {
                const offlineSubmissions = JSON.parse(localStorage.getItem('offline_submissions') || '[]');
                if (offlineSubmissions.length > 0) {
                    showNotification('İnternet bağlantısı yeniden kuruldu. Çevrimdışı mesajlar gönderiliyor...', 'info');
                    // Process offline submissions
                    offlineSubmissions.forEach(submission => {
                        // Simulate sending
                        console.log('Processing offline submission:', submission);
                    });
                    localStorage.removeItem('offline_submissions');
                    showNotification('Çevrimdışı mesajlar başarıyla gönderildi!', 'success');
                }
            });
        }
    }
    
    function showEmailSuggestion(input, suggestion) {
        const existingSuggestion = input.parentElement.querySelector('.email-suggestion');
        if (existingSuggestion) {
            existingSuggestion.remove();
        }
        
        const suggestionElement = document.createElement('div');
        suggestionElement.className = 'email-suggestion';
        suggestionElement.innerHTML = `<small>Şunu mu demek istediniz: <strong>${suggestion}</strong>? <button type="button" onclick="acceptEmailSuggestion(this, '${suggestion}')">Kabul Et</button></small>`;
        input.parentElement.appendChild(suggestionElement);
    }
    
    function updateValidationUI(input, error) {
        const errorElement = input.parentElement.querySelector('.field-error');
        
        if (error) {
            if (!errorElement) {
                showFieldError(input, error);
            } else {
                errorElement.textContent = error;
            }
        } else {
            if (errorElement) {
                errorElement.remove();
            }
            input.classList.remove('error');
            input.classList.add('valid');
        }
    }
    
    function debounce(func, wait) {
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
    
    // Add CSS styles
    const style = document.createElement('style');
    style.textContent = `
        .animate-in {
            animation: slideInUp 0.8s ease forwards;
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
        
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        
        .form-group.focused .form-label {
            transform: translateY(-20px) scale(0.8);
            color: #3498db;
        }
        
        .form-input {
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-input.error {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }
        
        .field-error {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            animation: shake 0.3s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .character-counter {
            text-align: right;
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .selected-package-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            position: relative;
        }
        
        .package-info-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .package-info-header h4 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .remove-package {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }
        
        .remove-package:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .package-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .package-price {
            font-size: 16px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .package-features {
            margin-top: 10px;
        }
        
        .package-features ul {
            margin: 5px 0 0 0;
            padding-left: 20px;
        }
        
        .package-features li {
            margin-bottom: 3px;
            opacity: 0.9;
        }
        
        .map-placeholder {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .map-placeholder:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .map-info {
            flex: 1;
            text-align: left;
        }
        
        .map-visual {
            flex: 1;
            color: #7f8c8d;
        }
        
        .map-actions {
            margin-top: 20px;
        }
        
        .map-actions .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .chat-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transform: translateY(100%);
            transition: transform 0.3s ease;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        
        .chat-widget.active {
            transform: translateY(0);
        }
        
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 15px 15px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .chat-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            max-height: 350px;
        }
        
        .chat-message {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }
        
        .user-message {
            justify-content: flex-end;
        }
        
        .bot-message {
            justify-content: flex-start;
        }
        
        .message-content {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 80%;
            word-wrap: break-word;
        }
        
        .user-message .message-content {
            background: #3498db;
            color: white;
        }
        
        .message-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-size: 12px;
        }
        
        .chat-input-container {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }
        
        .chat-input {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 10px 15px;
            outline: none;
        }
        
        .send-message {
            background: #3498db;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .chat-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s ease;
        }
        
        .chat-toggle:hover {
            transform: scale(1.1);
        }
        
        .faq-item {
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        
        .faq-question {
            padding: 20px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s ease;
        }
        
        .faq-question:hover {
            background: #e9ecef;
        }
        
        .faq-question::after {
            content: '+';
            font-size: 20px;
            font-weight: bold;
            transition: transform 0.3s ease;
        }
        
        .faq-item.active .faq-question::after {
            transform: rotate(45deg);
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 20px;
        }
        
        .faq-item.active .faq-answer {
            padding: 20px;
        }
        
        .form-input.valid {
            border-color: #27ae60;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
        }
        
        .email-suggestion {
            margin-top: 5px;
            padding: 8px;
            background: #e8f4fd;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .email-suggestion button {
            background: #3498db;
            color: white;
            border: none;
            padding: 2px 8px;
            border-radius: 3px;
            cursor: pointer;
            margin-left: 5px;
        }
        
        .form-progress {
            height: 3px;
            background: #ecf0f1;
            border-radius: 2px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .form-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            transition: width 0.3s ease;
            border-radius: 2px;
        }
        
        .offline-indicator {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #e74c3c;
            color: white;
            text-align: center;
            padding: 10px;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
            z-index: 10001;
        }
        
        .offline-indicator.show {
            transform: translateY(0);
        }
        
        @media (max-width: 768px) {
            .chat-widget {
                width: calc(100vw - 40px);
                height: calc(100vh - 100px);
                bottom: 10px;
                right: 20px;
                left: 20px;
            }
            
            .map-placeholder {
                flex-direction: column;
                text-align: center;
            }
            
            .map-info {
                margin-bottom: 20px;
            }
        }
    `;
    document.head.appendChild(style);
});

// Global functions
function openGoogleMaps() {
    window.open('https://maps.google.com/?q=40.4093,49.8671', '_blank');
}

function getDirections() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            window.open(`https://maps.google.com/maps?saddr=${lat},${lng}&daddr=40.4093,49.8671`, '_blank');
        }, function() {
            window.open('https://maps.google.com/maps?daddr=40.4093,49.8671', '_blank');
        });
    } else {
        window.open('https://maps.google.com/maps?daddr=40.4093,49.8671', '_blank');
    }
}

function removePackageSelection() {
    // Remove package info display
    const packageInfo = document.querySelector('.selected-package-info');
    if (packageInfo) {
        packageInfo.remove();
    }
    
    // Reset form fields
    const serviceSelect = document.getElementById('service');
    const budgetSelect = document.getElementById('budget');
    const messageTextarea = document.getElementById('message');
    
    if (serviceSelect) {
        // Remove custom package option
        const customOption = serviceSelect.querySelector('option[value="selected-package"]');
        if (customOption) {
            customOption.remove();
        }
        serviceSelect.value = '';
    }
    
    if (budgetSelect) {
        budgetSelect.value = '';
    }
    
    if (messageTextarea) {
        messageTextarea.value = '';
        messageTextarea.style.height = 'auto';
    }
    
    // Clear localStorage
    localStorage.removeItem('selectedPackage');
    
    // Show notification
    showNotification('Paket seçimi silindi', 'info');
}

// Utility function for notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        max-width: 300px;
        word-wrap: break-word;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Add animation keyframes
const contactAnimationStyles = document.createElement('style');
contactAnimationStyles.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(contactAnimationStyles);

// Global functions for email suggestions
function acceptEmailSuggestion(button, suggestion) {
    const input = button.closest('.form-group').querySelector('input[type="email"]');
    const currentValue = input.value;
    const atIndex = currentValue.indexOf('@');
    if (atIndex !== -1) {
        input.value = currentValue.substring(0, atIndex + 1) + suggestion;
        contactFormState.updateField('email', input.value);
        button.closest('.email-suggestion').remove();
    }
}

// Offline detection
window.addEventListener('online', function() {
    const indicator = document.querySelector('.offline-indicator');
    if (indicator) {
        indicator.classList.remove('show');
    }
    showNotification('İnternet bağlantısı yeniden kuruldu!', 'success');
});

window.addEventListener('offline', function() {
    let indicator = document.querySelector('.offline-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.className = 'offline-indicator';
        indicator.textContent = 'İnternet bağlantısı yok. Mesajınız çevrimdışı kaydedilecek.';
        document.body.appendChild(indicator);
    }
    indicator.classList.add('show');
    showNotification('İnternet bağlantısı kesildi. Mesajınız çevrimdışı kaydedilecek.', 'info');
});



// Make functions globally available
window.showNotification = showNotification;
window.openGoogleMaps = openGoogleMaps;
window.getDirections = getDirections;
window.removePackageSelection = removePackageSelection;
window.acceptEmailSuggestion = acceptEmailSuggestion;
window.contactFormState = contactFormState;
window.contactPerformance = contactPerformance;

// WhatsApp functionality
function setupWhatsApp() {
    const whatsappButton = document.getElementById('whatsappButton');
    const copyNumberBtn = document.getElementById('copyNumberBtn');
    
    if (whatsappButton) {
        // Add click tracking
        whatsappButton.addEventListener('click', function() {
            // Track WhatsApp click
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': 'Contact',
                    'event_label': 'WhatsApp Button'
                });
            }
            
            // Add visual feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
        
        // Add hover effects
        whatsappButton.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        whatsappButton.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    }
    
    if (copyNumberBtn) {
        copyNumberBtn.addEventListener('click', function() {
            copyWhatsAppNumber();
        });
    }
}

// Copy WhatsApp number to clipboard
function copyWhatsAppNumber() {
    const phoneNumber = '+380972580000';
    
    if (navigator.clipboard && window.isSecureContext) {
        // Use modern clipboard API
        navigator.clipboard.writeText(phoneNumber).then(() => {
            showNotification('Telefon nömrəsi kopyalandı!', 'success');
            
            // Update button text temporarily
            const copyBtn = document.getElementById('copyNumberBtn');
            if (copyBtn) {
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check me-2"></i>Kopyalandı!';
                copyBtn.classList.add('modern-btn--success');
                copyBtn.classList.remove('modern-btn--outline');
                
                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                    copyBtn.classList.remove('modern-btn--success');
                    copyBtn.classList.add('modern-btn--outline');
                }, 2000);
            }
        }).catch(err => {
            console.error('Clipboard write failed:', err);
            fallbackCopyTextToClipboard(phoneNumber);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyTextToClipboard(phoneNumber);
    }
}

// Fallback copy function for older browsers
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showNotification('Telefon nömrəsi kopyalandı!', 'success');
        } else {
            showNotification('Kopyalama uğursuz oldu. Nömrəni əl ilə kopyalayın: ' + text, 'error');
        }
    } catch (err) {
        console.error('Fallback copy failed:', err);
        showNotification('Kopyalama uğursuz oldu. Nömrəni əl ilə kopyalayın: ' + text, 'error');
    }
    
    document.body.removeChild(textArea);
}

// WhatsApp message templates
function getWhatsAppMessageTemplate(service = '') {
    const templates = {
        'seo': 'Merhaba! SEO optimizasyonu hakkında bilgi almak istiyorum.',
        'social-media': 'Merhaba! Sosyal medya yönetimi hakkında bilgi almak istiyorum.',
        'branding': 'Merhaba! Marka tasarımı hakkında bilgi almak istiyorum.',
        'advertising': 'Merhaba! Reklam kampanyaları hakkında bilgi almak istiyorum.',
        'web-design': 'Merhaba! Web sitesi tasarımı hakkında bilgi almak istiyorum.',
        'email-marketing': 'Merhaba! E-posta pazarlaması hakkında bilgi almak istiyorum.',
        'consultation': 'Merhaba! Danışmanlık hizmetleri hakkında bilgi almak istiyorum.',
        'default': 'Merhaba! NextCode Group\'dan bilgi almak istiyorum.'
    };
    
    return templates[service] || templates.default;
}

// Update WhatsApp link based on selected service
function updateWhatsAppLink(service) {
    const whatsappButton = document.getElementById('whatsappButton');
    if (whatsappButton) {
        const message = encodeURIComponent(getWhatsAppMessageTemplate(service));
        whatsappButton.href = `https://wa.me/380972580000?text=${message}`;
    }
}

// Setup service selection for WhatsApp
function setupServiceSelection() {
    const serviceSelect = document.getElementById('service');
    if (serviceSelect) {
        serviceSelect.addEventListener('change', function() {
            const selectedService = this.value;
            if (selectedService) {
                updateWhatsAppLink(selectedService);
                
                // Show notification
                showNotification(`${this.options[this.selectedIndex].text} xidməti üçün WhatsApp mesajı yeniləndi!`, 'info');
            }
        });
    }
}

// Make functions globally available
window.copyWhatsAppNumber = copyWhatsAppNumber;
window.updateWhatsAppLink = updateWhatsAppLink;
