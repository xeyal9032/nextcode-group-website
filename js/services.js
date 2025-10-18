// Services Page Interactive Features
document.addEventListener('DOMContentLoaded', function() {
    // Service cards hover effects and animations
    const serviceCards = document.querySelectorAll('.service-card');
    const pricingCards = document.querySelectorAll('.pricing-card');
    const processSteps = document.querySelectorAll('.process-step');
    
    // Service detail buttons
    const serviceDetailBtns = document.querySelectorAll('.service-detail-btn');
    const serviceModal = document.getElementById('serviceDetailModal');
    
    // Service data
    const serviceData = {
        'seo': {
            title: 'SEO Optimizasiya',
            price: '300₼-dən başlayaraq',
            icon: 'fas fa-search',
            description: 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün peşəkar SEO xidmətləri. Biznesinizin onlayn görünürlüyünü artıraraq daha çox müştəri cəlb edin.',
            duration: '3-6 ay',
            features: [
                'Açar söz tədqiqatı və analizi',
                'Texniki SEO audit və optimizasiya',
                'Məzmun optimizasiyası və yaradılması',
                'Link building strategiyası',
                'Aylıq performans hesabatı',
                'Rəqib analizi',
                'Local SEO optimizasiya'
            ],
            benefits: [
                { icon: 'fas fa-arrow-up', text: 'Axtarış nəticələrində yüksək mövqe' },
                { icon: 'fas fa-chart-line', text: 'Orqanik trafik artımı' },
                { icon: 'fas fa-eye', text: 'Brend görünürlüyünün artması' },
                { icon: 'fas fa-users', text: 'Hədəf auditoriyaya çatma' }
            ]
        },
        'social-media': {
            title: 'Sosial Media İdarəçiliyi',
            price: '250₼-dən başlayaraq',
            icon: 'fas fa-share-alt',
            description: 'Sosial media platformalarında güclü varlıq yaratmaq və idarə etmək. Müştərilərinizlə əlaqə qurun və brend loyallığını artırın.',
            duration: 'Davamlı',
            features: [
                'Məzmun planlaması və strategiya',
                'Yaradıcı post dizaynı',
                'Cəmiyyət idarəçiliyi və müştəri xidməti',
                'Hədəfli reklam kampaniyaları',
                'Analitika və performans hesabatı',
                'İnfluencer əməkdaşlığı',
                'Krizis idarəetməsi'
            ],
            benefits: [
                { icon: 'fas fa-users', text: 'Cəmiyyət quruculuğu' },
                { icon: 'fas fa-heart', text: 'Brend loyallığının artması' },
                { icon: 'fas fa-comments', text: 'Müştəri əlaqələrinin güclənməsi' },
                { icon: 'fas fa-share', text: 'Viral məzmun yaradılması' }
            ]
        },
        'branding': {
            title: 'Brendinq və Dizayn',
            price: '500₼-dən başlayaraq',
            icon: 'fas fa-palette',
            description: 'Güclü brend kimliyi yaratmaq və vizual dizayn həlləri. Biznesinizi rəqiblərdən fərqləndirən unikal brend yaradın.',
            duration: '2-4 həftə',
            features: [
                'Logo dizaynı və brend kimliyi',
                'Rəng palitri və tipografiya',
                'Marketinq materialları dizaynı',
                'Brend strategiyası və pozisiyalaşdırma',
                'Dizayn sistemi və qaydalar',
                'Korporativ kimlik paketi',
                'Brend təlimatları'
            ],
            benefits: [
                { icon: 'fas fa-star', text: 'Güclü brend tanınması' },
                { icon: 'fas fa-trophy', text: 'Rəqabət üstünlüyü' },
                { icon: 'fas fa-handshake', text: 'Müştəri etibarının artması' },
                { icon: 'fas fa-gem', text: 'Premium brend imici' }
            ]
        },
        'advertising': {
            title: 'Reklam Kampaniyaları',
            price: '400₼-dən başlayaraq',
            icon: 'fas fa-bullhorn',
            description: 'Google Ads, Facebook Ads və digər platformalarda effektiv reklam kampaniyaları. Hədəf auditoriyaya çatın və satışları artırın.',
            duration: '1-3 ay',
            features: [
                'Kampaniya strategiyası və planlaşdırma',
                'Hədəf auditoriya təhlili',
                'Yaradıcı reklam materialları',
                'A/B testləri və optimizasiya',
                'ROI və performans izləməsi',
                'Remarketing kampaniyaları',
                'Çoxkanallı reklam strategiyası'
            ],
            benefits: [
                { icon: 'fas fa-bullseye', text: 'Dəqiq hədəfləmə' },
                { icon: 'fas fa-chart-bar', text: 'Yüksək ROI' },
                { icon: 'fas fa-rocket', text: 'Sürətli nəticələr' },
                { icon: 'fas fa-dollar-sign', text: 'Satış artımı' }
            ]
        },
        'web-development': {
            title: 'Veb Sayt Hazırlanması',
            price: '800₼-dən başlayaraq',
            icon: 'fas fa-code',
            description: 'Müasir və funksional veb saytların hazırlanması. Responsive dizayn və yüksək performansla biznesinizi onlayn dünyada təmsil edin.',
            duration: '2-8 həftə',
            features: [
                'Responsive və mobil uyğun dizayn',
                'CMS inteqrasiyası və idarəetmə',
                'E-commerce həlləri',
                'SEO optimizasiya',
                'Texniki dəstək və yeniləmələr',
                'SSL sertifikatı və təhlükəsizlik',
                'Performans optimizasiyası'
            ],
            benefits: [
                { icon: 'fas fa-mobile-alt', text: 'Mobil uyğunluq' },
                { icon: 'fas fa-tachometer-alt', text: 'Yüksək performans' },
                { icon: 'fas fa-shield-alt', text: 'Təhlükəsizlik' },
                { icon: 'fas fa-search', text: 'SEO dostu struktur' }
            ]
        },
        'email-marketing': {
            title: 'Email Marketinq',
            price: '200₼-dən başlayaraq',
            icon: 'fas fa-envelope',
            description: 'Effektiv email kampaniyaları və müştəri əlaqələri. Personalizə edilmiş mesajlarla müştəri loyallığını artırın.',
            duration: 'Davamlı',
            features: [
                'Email dizaynı və şablonlar',
                'Avtomatlaşdırma və drip kampaniyalar',
                'Müştəri seqmentasiyası',
                'A/B testləri və optimizasiya',
                'Performans analizi və hesabat',
                'CRM inteqrasiyası',
                'Personalizasiya strategiyası'
            ],
            benefits: [
                { icon: 'fas fa-envelope-open', text: 'Yüksək açılma dərəcəsi' },
                { icon: 'fas fa-mouse-pointer', text: 'Artırılmış kliklənmə' },
                { icon: 'fas fa-redo', text: 'Müştəri qaytarılması' },
                { icon: 'fas fa-coins', text: 'Aşağı xərc, yüksək gəlir' }
            ]
        }
    };
    
    // Service detail button click handlers
    serviceDetailBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const serviceType = this.getAttribute('data-service');
            const service = serviceData[serviceType];
            
            if (service) {
                openServiceModal(service);
            }
        });
    });
    
    // Modal close handlers
    if (serviceModal) {
        // Bootstrap close button
        const closeBtn = serviceModal.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeServiceModal);
        }
        
        // Bootstrap dismiss button
        const dismissBtn = serviceModal.querySelector('[data-bs-dismiss="modal"]');
        if (dismissBtn) {
            dismissBtn.addEventListener('click', closeServiceModal);
        }
        
        // Click outside modal to close
        serviceModal.addEventListener('click', function(e) {
            if (e.target === serviceModal) {
                closeServiceModal();
            }
        });
    }
    
    function openServiceModal(service) {
        // Update modal content
        document.getElementById('modal-service-title').textContent = service.title;
        document.getElementById('modal-service-price').textContent = service.price;
        document.getElementById('modal-service-description').textContent = service.description;
        document.getElementById('modal-service-duration').textContent = service.duration;
        
        // Update icon
        const modalIcon = document.querySelector('.service-modal-icon i');
        modalIcon.className = service.icon;
        
        // Update features
        const featuresList = document.getElementById('modal-service-features');
        featuresList.innerHTML = '';
        service.features.forEach(feature => {
            const li = document.createElement('li');
            li.innerHTML = `<i class="fas fa-check"></i> ${feature}`;
            featuresList.appendChild(li);
        });
        
        // Update benefits
        const benefitsContainer = document.getElementById('modal-service-benefits');
        benefitsContainer.innerHTML = '';
        service.benefits.forEach(benefit => {
            const div = document.createElement('div');
            div.className = 'benefit-item';
            div.innerHTML = `<i class="${benefit.icon} text-success"></i><span>${benefit.text}</span>`;
            benefitsContainer.appendChild(div);
        });
        
        // Show modal
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(serviceModal);
            modal.show();
        } else {
            // Fallback for manual modal display
            serviceModal.classList.add('show');
            serviceModal.style.display = 'block';
            document.body.classList.add('modal-open');
        }
    }
    
    function closeServiceModal() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getInstance(serviceModal);
            if (modal) {
                modal.hide();
            }
        } else {
            // Fallback for manual modal hide
            serviceModal.classList.remove('show');
            serviceModal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    }
    
    // Service card interactions
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-15px) scale(1.02)';
            this.style.boxShadow = '0 25px 50px rgba(0,0,0,0.15)';
            
            // Add glow effect
            const icon = this.querySelector('.service-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.filter = 'drop-shadow(0 0 20px rgba(52, 152, 219, 0.5))';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
            
            const icon = this.querySelector('.service-icon');
            if (icon) {
                icon.style.transform = 'scale(1)';
                icon.style.filter = 'none';
            }
        });
        
        // Click to expand details
        card.addEventListener('click', function() {
            const details = this.querySelector('.service-details');
            if (details) {
                details.classList.toggle('expanded');
                
                // Smooth scroll to card if expanded
                if (details.classList.contains('expanded')) {
                    setTimeout(() => {
                        this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                }
            }
        });
    });
    
    // Pricing card interactions
    pricingCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
    });
    
    // Process steps animation
    const animateProcessSteps = () => {
        processSteps.forEach((step, index) => {
            setTimeout(() => {
                step.classList.add('animate-in');
            }, index * 200);
        });
    };
    
    // Service comparison functionality
    const comparisonBtn = document.getElementById('compare-services');
    if (comparisonBtn) {
        comparisonBtn.addEventListener('click', function() {
            showServiceComparison();
        });
    }
    
    // Service calculator
    const calculatorForm = document.getElementById('service-calculator');
    if (calculatorForm) {
        calculatorForm.addEventListener('submit', function(e) {
            e.preventDefault();
            calculateServicePrice();
        });
    }
    
    // FAQ accordion functionality
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        
        question.addEventListener('click', function() {
            const isActive = item.classList.contains('active');
            
            // Close all other FAQ items
            faqItems.forEach(otherItem => {
                otherItem.classList.remove('active');
                otherItem.querySelector('.faq-answer').style.maxHeight = '0';
            });
            
            // Toggle current item
            if (!isActive) {
                item.classList.add('active');
                answer.style.maxHeight = answer.scrollHeight + 'px';
            }
        });
    });
    
    // Service request modal
    const requestBtns = document.querySelectorAll('.request-service-btn');
    const modal = document.getElementById('service-request-modal');
    const closeModal = document.querySelector('.close-modal');
    
    requestBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const serviceName = this.dataset.service || 'Xidmət';
            openServiceRequestModal(serviceName);
        });
    });
    
    if (closeModal) {
        closeModal.addEventListener('click', closeServiceRequestModal);
    }
    
    // Close modal on outside click
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeServiceRequestModal();
            }
        });
    }
    
    // Service request form submission
    const serviceRequestForm = document.getElementById('service-request-form');
    if (serviceRequestForm) {
        serviceRequestForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitServiceRequest();
        });
    }
    
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                
                // Special animation for process steps
                if (entry.target.classList.contains('process-section')) {
                    setTimeout(animateProcessSteps, 300);
                }
            }
        });
    }, observerOptions);
    
    // Observe sections for animation
    const sections = document.querySelectorAll('.services-grid, .pricing-section, .process-section');
    sections.forEach(section => {
        observer.observe(section);
    });
    
    // Service filtering
    const filterBtns = document.querySelectorAll('.service-filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter services
            filterServices(filter);
        });
    });
    
    // Functions
    function filterServices(filter) {
        serviceCards.forEach(card => {
            const category = card.dataset.category;
            
            if (filter === 'all' || category === filter) {
                card.style.display = 'block';
                card.style.animation = 'fadeInUp 0.5s ease';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    function showServiceComparison() {
        const comparisonModal = document.createElement('div');
        comparisonModal.className = 'comparison-modal';
        comparisonModal.innerHTML = `
            <div class="comparison-content">
                <div class="comparison-header">
                    <h3>Xidmət Müqayisəsi</h3>
                    <button class="close-comparison">&times;</button>
                </div>
                <div class="comparison-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Xüsusiyyət</th>
                                <th>Əsas Paket</th>
                                <th>Peşəkar Paket</th>
                                <th>Premium Paket</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SEO Optimizasiya</td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Sosial Media İdarəetmə</td>
                                <td><i class="fas fa-times text-danger"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Reklam Kampaniyaları</td>
                                <td><i class="fas fa-times text-danger"></i></td>
                                <td><i class="fas fa-times text-danger"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>24/7 Dəstək</td>
                                <td><i class="fas fa-times text-danger"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        
        document.body.appendChild(comparisonModal);
        
        // Close comparison modal
        const closeBtn = comparisonModal.querySelector('.close-comparison');
        closeBtn.addEventListener('click', () => {
            comparisonModal.remove();
        });
        
        comparisonModal.addEventListener('click', (e) => {
            if (e.target === comparisonModal) {
                comparisonModal.remove();
            }
        });
    }
    
    function calculateServicePrice() {
        const formData = new FormData(calculatorForm);
        const services = formData.getAll('services');
        const duration = formData.get('duration');
        const complexity = formData.get('complexity');
        
        let basePrice = 0;
        services.forEach(service => {
            switch(service) {
                case 'seo': basePrice += 500; break;
                case 'social-media': basePrice += 300; break;
                case 'web-design': basePrice += 1000; break;
                case 'advertising': basePrice += 800; break;
            }
        });
        
        // Duration multiplier
        const durationMultiplier = {
            '1': 1,
            '3': 0.9,
            '6': 0.8,
            '12': 0.7
        };
        
        // Complexity multiplier
        const complexityMultiplier = {
            'basic': 1,
            'intermediate': 1.3,
            'advanced': 1.6
        };
        
        const finalPrice = basePrice * (durationMultiplier[duration] || 1) * (complexityMultiplier[complexity] || 1);
        
        showPriceResult(finalPrice);
    }
    
    function showPriceResult(price) {
        const resultDiv = document.getElementById('price-result');
        if (resultDiv) {
            resultDiv.innerHTML = `
                <div class="price-result-content">
                    <h4>Təxmini Qiymət</h4>
                    <div class="price-amount">${price.toFixed(0)} AZN</div>
                    <p>Bu təxmini qiymətdir. Dəqiq qiymət üçün bizimlə əlaqə saxlayın.</p>
                    <button class="btn btn-primary" onclick="openServiceRequestModal('Kalkulyator Nəticəsi')">Təklif İstəyin</button>
                </div>
            `;
            resultDiv.style.display = 'block';
        }
    }
    
    function openServiceRequestModal(serviceName) {
        if (modal) {
            const serviceInput = modal.querySelector('input[name="service"]');
            if (serviceInput) {
                serviceInput.value = serviceName;
            }
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeServiceRequestModal() {
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    
    function submitServiceRequest() {
        const formData = new FormData(serviceRequestForm);
        const submitBtn = serviceRequestForm.querySelector('button[type="submit"]');
        
        // Show loading state
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Göndərilir...';
        submitBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            showNotification('Xidmət tələbiniz uğurla göndərildi! Tezliklə sizinlə əlaqə saxlayacağıq.', 'success');
            serviceRequestForm.reset();
            closeServiceRequestModal();
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }, 2000);
    }
    
    // Add CSS animations and styles
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
        
        .service-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .service-icon {
            transition: all 0.3s ease;
        }
        
        .service-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .service-details.expanded {
            max-height: 200px;
            padding-top: 15px;
        }
        
        .pricing-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .process-step {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }
        
        .process-step.animate-in {
            opacity: 1;
            transform: translateY(0);
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
        
        .comparison-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }
        
        .comparison-content {
            background: white;
            border-radius: 10px;
            max-width: 800px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .comparison-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .comparison-table {
            padding: 20px;
        }
        
        .comparison-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .comparison-table th,
        .comparison-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        
        .comparison-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .close-comparison {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        
        .text-success {
            color: #28a745;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        .price-result-content {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .price-amount {
            font-size: 36px;
            font-weight: bold;
            margin: 15px 0;
        }
        
        @media (max-width: 768px) {
            .comparison-content {
                width: 95%;
                margin: 20px;
            }
            
            .comparison-table {
                overflow-x: auto;
            }
        }
    `;
    document.head.appendChild(style);
    

});

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
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Add animation keyframes
const servicesAnimationStyles = document.createElement('style');
servicesAnimationStyles.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(servicesAnimationStyles);