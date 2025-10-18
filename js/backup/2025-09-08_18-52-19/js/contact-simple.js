// Robust Contact Form with WhatsApp Integration
(function () {
    'use strict';

    // Wait for DOM to be ready
    function initContactForm() {
        console.log('Contact form script loaded');

        const contactForm = document.getElementById('contactForm');
        const submitBtn = document.getElementById('contactFormSubmit');

        if (!contactForm) {
            console.error('Contact form not found!');
            return;
        }

        if (!submitBtn) {
            console.error('Submit button not found!');
            return;
        }

        console.log('Form elements found, setting up event listener');

        // Remove any existing event listeners
        const newForm = contactForm.cloneNode(true);
        contactForm.parentNode.replaceChild(newForm, contactForm);

        // Add event listener to new form
        newForm.addEventListener('submit', handleFormSubmission);

        console.log('Contact form setup complete');
    }

    function handleFormSubmission(e) {
        e.preventDefault();
        console.log('Form submitted');

        const contactForm = document.getElementById('contactForm');
        const submitBtn = document.getElementById('contactFormSubmit');

        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Göndərilir...';
        }

        try {
            // Get form data
            const formData = new FormData(contactForm);
            const formObject = {};

            for (let [key, value] of formData.entries()) {
                formObject[key] = value;
                console.log(`${key}: ${value}`);
            }

            // Validate required fields
            const requiredFields = ['name', 'email', 'subject', 'message'];
            let isValid = true;

            requiredFields.forEach(field => {
                if (!formObject[field] || formObject[field].trim() === '') {
                    console.error(`Missing required field: ${field}`);
                    isValid = false;
                }
            });

            if (!isValid) {
                alert('Zəhmət olmasa bütün zorunlu sahələri doldurun!');
                resetButton();
                return;
            }

            console.log('Form validation passed');

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

            console.log('WhatsApp message created:', whatsappMessage);

            // Create WhatsApp URL
            const whatsappNumber = '+380972580000';
            const encodedMessage = encodeURIComponent(whatsappMessage);
            const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;

            console.log('WhatsApp URL created:', whatsappUrl);

            // Try to send to backend first
            sendToBackend(formData, whatsappUrl, contactForm);

        } catch (error) {
            console.error('Form submission error:', error);
            alert('Mesaj WhatsApp-a göndərilir...');

            // Reset form
            if (contactForm) contactForm.reset();

            // Open WhatsApp directly
            setTimeout(() => {
                window.open(whatsappUrl, '_blank');
            }, 1000);

            resetButton();
        }
    }

    function sendToBackend(formData, whatsappUrl, contactForm) {
        fetch('contact-handler.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                console.log('Backend response status:', response.status);
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }
            })
            .then(data => {
                console.log('Backend response data:', data);

                if (data.success) {
                    alert('Mesaj uğurla göndərildi! WhatsApp açılır...');

                    // Reset form
                    if (contactForm) contactForm.reset();

                    // Open WhatsApp
                    setTimeout(() => {
                        window.open(whatsappUrl, '_blank');
                    }, 2000);

                } else {
                    // Backend failed, but still open WhatsApp
                    alert('Mesaj WhatsApp-a göndərilir...');

                    // Reset form
                    if (contactForm) contactForm.reset();

                    // Open WhatsApp
                    setTimeout(() => {
                        window.open(whatsappUrl, '_blank');
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Backend error:', error);

                // Backend failed, but still open WhatsApp
                alert('Mesaj WhatsApp-a göndərilir...');

                // Reset form
                if (contactForm) contactForm.reset();

                // Open WhatsApp
                setTimeout(() => {
                    window.open(whatsappUrl, '_blank');
                }, 1000);
            })
            .finally(() => {
                resetButton();
            });
    }

    function resetButton() {
        const submitBtn = document.getElementById('contactFormSubmit');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Mesaj Göndər';
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initContactForm);
    } else {
        initContactForm();
    }

    // Also try to initialize after a short delay to ensure all scripts are loaded
    setTimeout(initContactForm, 1000);

})();
