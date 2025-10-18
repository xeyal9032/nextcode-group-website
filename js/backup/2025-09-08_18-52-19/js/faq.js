// FAQ Page Interactive Features
document.addEventListener('DOMContentLoaded', function() {
    // FAQ accordion functionality
    const faqItems = document.querySelectorAll('.faq-item');
    const searchInput = document.getElementById('faq-search');
    const categoryFilters = document.querySelectorAll('.category-filter');
    const faqContainer = document.querySelector('.faq-container');
    
    // Initialize FAQ page
    initializeFAQ();
    
    function initializeFAQ() {
        // Set up accordion functionality
        setupAccordion();
        
        // Set up search functionality
        setupSearch();
        
        // Set up category filtering
        setupCategoryFilters();
        
        // Set up animations
        setupAnimations();
        
        // Set up helpful features
        setupHelpfulFeatures();
        
        // Set up contact integration
        setupContactIntegration();
    }
    
    function setupAccordion() {
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');
            const icon = item.querySelector('.faq-icon');
            
            if (question && answer) {
                question.addEventListener('click', function() {
                    const isActive = item.classList.contains('active');
                    
                    // Close all other FAQ items
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                            const otherAnswer = otherItem.querySelector('.faq-answer');
                            const otherIcon = otherItem.querySelector('.faq-icon');
                            if (otherAnswer) {
                                otherAnswer.style.maxHeight = '0';
                            }
                            if (otherIcon) {
                                otherIcon.style.transform = 'rotate(0deg)';
                            }
                        }
                    });
                    
                    // Toggle current item
                    if (!isActive) {
                        item.classList.add('active');
                        answer.style.maxHeight = answer.scrollHeight + 'px';
                        if (icon) {
                            icon.style.transform = 'rotate(45deg)';
                        }
                        
                        // Track FAQ interaction
                        trackFAQInteraction(question.textContent);
                        
                        // Smooth scroll to item
                        setTimeout(() => {
                            item.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'center' 
                            });
                        }, 100);
                    } else {
                        item.classList.remove('active');
                        answer.style.maxHeight = '0';
                        if (icon) {
                            icon.style.transform = 'rotate(0deg)';
                        }
                    }
                });
                
                // Add hover effects
                question.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });
                
                question.addEventListener('mouseleave', function() {
                    if (!item.classList.contains('active')) {
                        this.style.backgroundColor = '';
                    }
                });
            }
        });
    }
    
    function setupSearch() {
        if (!searchInput) return;
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                const answer = item.querySelector('.faq-answer');
                
                if (question && answer) {
                    const questionText = question.textContent.toLowerCase();
                    const answerText = answer.textContent.toLowerCase();
                    
                    if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                        item.style.display = 'block';
                        
                        // Highlight search terms
                        if (searchTerm) {
                            highlightSearchTerm(question, searchTerm);
                            highlightSearchTerm(answer, searchTerm);
                        } else {
                            removeHighlight(question);
                            removeHighlight(answer);
                        }
                    } else {
                        item.style.display = 'none';
                        item.classList.remove('active');
                        answer.style.maxHeight = '0';
                    }
                }
            });
            
            // Show no results message
            showNoResultsMessage(searchTerm);
        });
        
        // Clear search button
        const clearBtn = document.createElement('button');
        clearBtn.className = 'clear-search';
        clearBtn.innerHTML = '<i class="fas fa-times"></i>';
        clearBtn.style.display = 'none';
        searchInput.parentElement.appendChild(clearBtn);
        
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            this.style.display = 'none';
        });
        
        searchInput.addEventListener('input', function() {
            clearBtn.style.display = this.value ? 'block' : 'none';
        });
    }
    
    function setupCategoryFilters() {
        categoryFilters.forEach(filter => {
            filter.addEventListener('click', function() {
                const category = this.dataset.category;
                
                // Update active filter
                categoryFilters.forEach(f => f.classList.remove('active'));
                this.classList.add('active');
                
                // Filter FAQ items
                faqItems.forEach(item => {
                    const itemCategory = item.dataset.category;
                    
                    if (category === 'all' || itemCategory === category) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                        item.classList.remove('active');
                        const answer = item.querySelector('.faq-answer');
                        if (answer) {
                            answer.style.maxHeight = '0';
                        }
                    }
                });
                
                // Track category filter usage
                trackCategoryFilter(category);
            });
        });
    }
    
    function highlightSearchTerm(element, term) {
        if (!element || !term) return;
        
        const originalText = element.textContent;
        const regex = new RegExp(`(${term})`, 'gi');
        const highlightedText = originalText.replace(regex, '<mark>$1</mark>');
        element.innerHTML = highlightedText;
    }
    
    function removeHighlight(element) {
        if (!element) return;
        
        const text = element.textContent;
        element.innerHTML = text;
    }
    
    function showNoResultsMessage(searchTerm) {
        const visibleItems = Array.from(faqItems).filter(item => 
            item.style.display !== 'none'
        );
        
        let noResultsMsg = document.querySelector('.no-results-message');
        
        if (visibleItems.length === 0 && searchTerm) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-results-message';
                noResultsMsg.innerHTML = `
                    <div class="no-results-content">
                        <i class="fas fa-search fa-3x"></i>
                        <h3>Heç bir nəticə tapılmadı</h3>
                        <p>"${searchTerm}" üçün heç bir FAQ tapılmadı.</p>
                        <div class="no-results-actions">
                            <button class="btn btn-primary" onclick="clearSearch()">Axtarışı Təmizlə</button>
                            <button class="btn btn-secondary" onclick="contactSupport()">Dəstək ilə Əlaqə</button>
                        </div>
                    </div>
                `;
                faqContainer.appendChild(noResultsMsg);
            }
            noResultsMsg.style.display = 'block';
        } else if (noResultsMsg) {
            noResultsMsg.style.display = 'none';
        }
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
        
        // Observe FAQ items
        faqItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1}s`;
            observer.observe(item);
        });
        
        // Observe other sections
        const sections = document.querySelectorAll('.faq-hero, .faq-search-section, .faq-stats');
        sections.forEach(section => observer.observe(section));
    }
    
    function setupContactIntegration() {
        // Set up contact integration for FAQ page
        const contactButtons = document.querySelectorAll('.contact-support-btn');
        
        contactButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Track contact button click
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'faq_contact_support', {
                        'source': 'faq_page'
                    });
                }
                
                // Redirect to contact page
                window.location.href = '/contact.html';
            });
        });
        
        // Set up quick contact modal if exists
        const quickContactModal = document.getElementById('quickContactModal');
        if (quickContactModal) {
            const quickContactBtns = document.querySelectorAll('.quick-contact-btn');
            quickContactBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    quickContactModal.style.display = 'block';
                });
            });
            
            // Close modal functionality
            const closeBtn = quickContactModal.querySelector('.close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    quickContactModal.style.display = 'none';
                });
            }
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === quickContactModal) {
                    quickContactModal.style.display = 'none';
                }
            });
        }
    }
    
    function setupHelpfulFeatures() {
        // Add helpful/unhelpful buttons to FAQ items
        faqItems.forEach(item => {
            const answer = item.querySelector('.faq-answer');
            if (answer) {
                const helpfulSection = document.createElement('div');
                helpfulSection.className = 'faq-helpful';
                helpfulSection.innerHTML = `
                    <p>Bu cavab faydalı oldu?</p>
                    <div class="helpful-buttons">
                        <button class="btn-helpful" data-helpful="yes">
                            <i class="fas fa-thumbs-up"></i> Bəli
                        </button>
                        <button class="btn-helpful" data-helpful="no">
                            <i class="fas fa-thumbs-down"></i> Xeyr
                        </button>
                    </div>
                `;
                answer.appendChild(helpfulSection);
                
                // Add event listeners for helpful buttons
                const helpfulButtons = helpfulSection.querySelectorAll('.btn-helpful');
                helpfulButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const isHelpful = this.dataset.helpful === 'yes';
                        trackHelpfulFeedback(item.querySelector('.faq-question').textContent, isHelpful);
                        
                        // Update button states
                        helpfulButtons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Show thank you message
                        showThankYouMessage(helpfulSection);
                    });
                });
            }
        });
    }
    
    function showThankYouMessage(container) {
        const thankYou = document.createElement('div');
        thankYou.className = 'thank-you-message';
        thankYou.textContent = 'Rəyiniz üçün təşəkkür edirik!';
        container.appendChild(thankYou);
        
        setTimeout(() => {
            thankYou.remove();
        }, 3000);
    }
    
    // Analytics functions
    function trackFAQInteraction(question) {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'faq_interaction', {
                'question': question
            });
        }
    }
    
    function trackCategoryFilter(category) {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'faq_category_filter', {
                'category': category
            });
        }
    }
    
    function trackHelpfulFeedback(question, isHelpful) {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'faq_helpful_feedback', {
                'question': question,
                'helpful': isHelpful
            });
        }
    }
    
    // Global functions
    window.clearSearch = function() {
        const searchInput = document.querySelector('.faq-search input');
        if (searchInput) {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        }
    };
    
    window.contactSupport = function() {
        // Redirect to contact page or open contact modal
        window.location.href = '/contact.html';
    };
    
    // Initialize FAQ functionality when DOM is ready
    // initializeFAQ() is already called above
});