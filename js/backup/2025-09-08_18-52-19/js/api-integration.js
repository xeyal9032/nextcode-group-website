/**
 * Enhanced API Integration for Dynamic Content
 * Modern implementation with error handling, caching, and retry mechanisms
 */
class APIIntegration {
    constructor() {
        this.apiBase = window.location.origin + '/api';
        this.cache = new Map();
        this.retryAttempts = 3;
        this.retryDelay = 1000;
        this.requestTimeout = 10000;
        this.isOnline = navigator.onLine;
        
        this.init();
        this.setupNetworkListeners();
    }

    init() {
        try {
            // Check if API integration should be enabled
            if (this.shouldEnableAPI()) {
                this.loadDynamicContent();
                this.setupPageSpecificLoaders();
            } else {
                console.log('API integration disabled - using static content');
            }
        } catch (error) {
            console.error('API Integration initialization failed:', error);
            this.fallbackToStaticContent();
        }
    }
    
    /**
     * Check if API integration should be enabled
     */
    shouldEnableAPI() {
        // Enable API always for development and production
        return true;
    }
    
    /**
     * Setup page-specific content loaders
     */
    setupPageSpecificLoaders() {
        console.log('=== SETTING UP PAGE SPECIFIC LOADERS ===');
        
        // Check if we're on services page
        const servicesGrid = document.getElementById('services-grid');
        console.log('Services grid found:', !!servicesGrid);
        if (servicesGrid) {
            this.loadServicesPage();
        }
        
        // Check if we're on portfolio page
        const portfolioGrid = document.getElementById('portfolio-grid');
        console.log('Portfolio grid found:', !!portfolioGrid);
        if (portfolioGrid) {
            this.loadPortfolioPage();
        }
        
        // Check if we're on blog page
        const blogGrid = document.getElementById('blogGrid');
        console.log('Blog grid found:', !!blogGrid);
        if (blogGrid) {
            console.log('Blog grid element detected, calling loadBlogPage()');
            this.loadBlogPage();
        } else {
            console.log('No blogGrid element found on this page');
        }
    }
    
    /**
     * Setup network status listeners
     */
    setupNetworkListeners() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            console.log('Network connection restored');
            this.refreshContent();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
            console.log('Network connection lost');
        });
    }
    
    /**
     * Fallback to static content when API fails
     */
    fallbackToStaticContent() {
        console.log('Falling back to static content');
        // Implement static content loading here if needed
    }

    /**
     * Load dynamic content with enhanced error handling
     */
    async loadDynamicContent() {
        if (!this.isOnline) {
            console.log('Offline - using cached content');
            return this.loadCachedContent();
        }
        
        try {
            const loadingPromises = [
                this.loadServices(),
                this.loadPortfolio(),
                this.loadBlogPosts(),
                this.loadSiteSettings()
            ];
            
            // Use Promise.allSettled to handle partial failures gracefully
            const results = await Promise.allSettled(loadingPromises);
            
            results.forEach((result, index) => {
                if (result.status === 'rejected') {
                    const endpoints = ['services', 'portfolio', 'blog', 'settings'];
                    console.warn(`Failed to load ${endpoints[index]}:`, result.reason);
                }
            });
            
        } catch (error) {
            console.error('Error loading dynamic content:', error);
            this.fallbackToStaticContent();
        }
    }
    
    /**
     * Load cached content when offline
     */
    loadCachedContent() {
        this.cache.forEach((data, key) => {
            console.log(`Loading cached ${key}`);
            // Implement cached content loading based on key
        });
    }

    /**
     * Load services with enhanced error handling and caching
     */
    async loadServices() {
        const cacheKey = 'services';
        
        // Check cache first
        if (this.cache.has(cacheKey)) {
            const cachedData = this.cache.get(cacheKey);
            if (Date.now() - cachedData.timestamp < 300000) { // 5 minutes cache
                this.updateServicesSection(cachedData.data);
                return;
            }
        }
        
        try {
            const data = await this.fetchWithRetry(`${this.apiBase}/services.php`);
            if (data && data.success && data.data) {
                const services = data.data.items || data.data;
                this.updateServicesSection(services);
                
                // Cache the data
                this.cache.set(cacheKey, {
                    data: services,
                    timestamp: Date.now()
                });
            }
        } catch (error) {
            console.error('Error loading services:', error);
            this.handleAPIError('services', error);
        }
    }

    /**
     * Load portfolio with enhanced error handling and caching
     */
    async loadPortfolio() {
        const cacheKey = 'portfolio';
        
        // Check cache first
        if (this.cache.has(cacheKey)) {
            const cachedData = this.cache.get(cacheKey);
            if (Date.now() - cachedData.timestamp < 300000) { // 5 minutes cache
                this.updatePortfolioSection(cachedData.data);
                return;
            }
        }
        
        try {
            const data = await this.fetchWithRetry(`${this.apiBase}/portfolio.php`);
            if (data && data.success && data.data) {
                const portfolio = data.data.items || data.data;
                this.updatePortfolioSection(portfolio);
                
                // Cache the data
                this.cache.set(cacheKey, {
                    data: portfolio,
                    timestamp: Date.now()
                });
            }
        } catch (error) {
            console.error('Error loading portfolio:', error);
            this.handleAPIError('portfolio', error);
        }
    }

    /**
     * Load blog posts with enhanced error handling and caching
     */
    async loadBlogPosts() {
        const cacheKey = 'blog';
        
        // Check cache first
        if (this.cache.has(cacheKey)) {
            const cachedData = this.cache.get(cacheKey);
            if (Date.now() - cachedData.timestamp < 180000) { // 3 minutes cache for blog
                this.updateBlogSection(cachedData.data);
                return;
            }
        }
        
        try {
            const data = await this.fetchWithRetry(`${this.apiBase}/blog.php`);
            if (data && data.success && data.data) {
                this.updateBlogSection(data.data);
                
                // Cache the data
                this.cache.set(cacheKey, {
                    data: data.data,
                    timestamp: Date.now()
                });
            }
        } catch (error) {
            console.error('Error loading blog posts:', error);
            this.handleAPIError('blog', error);
        }
    }



    /**
     * Load site settings with enhanced error handling and caching
     */
    async loadSiteSettings() {
        const cacheKey = 'settings';
        
        // Check cache first
        if (this.cache.has(cacheKey)) {
            const cachedData = this.cache.get(cacheKey);
            if (Date.now() - cachedData.timestamp < 600000) { // 10 minutes cache for settings
                this.updateSiteSettings(cachedData.data);
                return;
            }
        }
        
        try {
            const data = await this.fetchWithRetry(`${this.apiBase}/settings.php`);
            if (data && data.success && data.data) {
                this.updateSiteSettings(data.data);
                
                // Cache the data
                this.cache.set(cacheKey, {
                    data: data.data,
                    timestamp: Date.now()
                });
            }
        } catch (error) {
            console.error('Error loading site settings:', error);
            this.handleAPIError('settings', error);
        }
    }
    
    /**
     * Enhanced fetch with retry mechanism and timeout
     */
    async fetchWithRetry(url, options = {}, attempt = 1) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.requestTimeout);
        
        try {
            const response = await fetch(url, {
                ...options,
                signal: controller.signal,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...options.headers
                }
            });
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return await response.json();
            
        } catch (error) {
            clearTimeout(timeoutId);
            
            if (attempt < this.retryAttempts && this.isOnline) {
                console.warn(`Attempt ${attempt} failed, retrying in ${this.retryDelay}ms...`);
                await this.delay(this.retryDelay * attempt); // Exponential backoff
                return this.fetchWithRetry(url, options, attempt + 1);
            }
            
            throw error;
        }
    }
    
    /**
     * Delay utility for retry mechanism
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
    
    /**
     * Handle API errors with appropriate fallbacks
     */
    handleAPIError(endpoint, error) {
        console.error(`API Error for ${endpoint}:`, error);
        
        // Try to load from cache as fallback
        if (this.cache.has(endpoint)) {
            console.log(`Loading stale cache for ${endpoint}`);
            const cachedData = this.cache.get(endpoint);
            
            switch (endpoint) {
                case 'services':
                    this.updateServicesSection(cachedData.data);
                    break;
                case 'portfolio':
                    this.updatePortfolioSection(cachedData.data);
                    break;
                case 'blog':
                    this.updateBlogSection(cachedData.data);
                    break;
                case 'settings':
                    this.updateSiteSettings(cachedData.data);
                    break;
            }
        } else {
            // Cache yoksa fallback data kullan
            this.loadFallbackData(endpoint);
        }
         
         // Show user-friendly error message
         this.showErrorNotification(`Failed to load ${endpoint}. Using fallback content.`);
     }
     
           /**
       * Load fallback data when API fails and no cache exists
       */
      loadFallbackData(endpoint) {
          switch (endpoint) {
              case 'services':
                  const fallbackServices = [
                      {
                          id: 1,
                          title: 'SEO Optimizasiya',
                          description: 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün peşəkar SEO xidmətləri',
                          icon: 'fas fa-search',
                          price: '300₼-dən başlayaraq',
                          duration: '3-6 ay'
                      },
                      {
                          id: 2,
                          title: 'Sosial Media İdarəçiliyi',
                          description: 'Sosial media platformalarında güclü varlıq yaratmaq və idarə etmək',
                          icon: 'fas fa-share-alt',
                          price: '250₼-dən başlayaraq',
                          duration: 'Davamlı'
                      },
                      {
                          id: 3,
                          title: 'Brendinq və Dizayn',
                          description: 'Güclü brend kimliyi yaratmaq və vizual dizayn həlləri',
                          icon: 'fas fa-palette',
                          price: '500₼-dən başlayaraq',
                          duration: '2-4 həftə'
                      },
                      {
                          id: 4,
                          title: 'Reklam Kampaniyaları',
                          description: 'Google Ads, Facebook Ads və digər platformalarda effektiv reklam kampaniyaları',
                          icon: 'fas fa-bullhorn',
                          price: '400₼-dən başlayaraq',
                          duration: '1-3 ay'
                      }
                  ];
                  this.updateServicesSection(fallbackServices);
                  break;
                  
              case 'blog':
                  const fallbackBlogPosts = [
                      {
                          id: 1,
                          title: 'SEO Optimizasiyası: Axtarış Nəticələrində Yüksəlmək',
                          slug: 'seo-optimizasiyasi',
                          content: 'SEO optimizasiyası haqqında ətraflı məlumat və praktik məsləhətlər...',
                          excerpt: 'Axtarış sistemlərində yüksək reytinq əldə etmək üçün SEO strategiyaları və texnikaları.',
                          featured_image: 'images/blog/seo-article.jpg',
                          category_id: 1,
                          category_name: 'SEO',
                          tags: 'SEO, Axtarış, Optimizasiya',
                          is_featured: 1,
                          status: 'published',
                          created_at: '2024-01-15 10:00:00',
                          read_time: '8'
                      },
                      {
                          id: 2,
                          title: 'Sosial Media Marketinqi: Brendinizi Gücləndirin',
                          slug: 'sosial-media-marketinqi',
                          content: 'Sosial media platformalarında effektiv marketinq strategiyaları...',
                          excerpt: 'Sosial media platformalarında brendinizi gücləndirmək üçün praktik məsləhətlər və strategiyalar.',
                          featured_image: 'images/blog/social-media.jpg',
                          category_id: 2,
                          category_name: 'Sosial Media',
                          tags: 'Sosial Media, Marketinq, Brend',
                          is_featured: 1,
                          status: 'published',
                          created_at: '2024-01-10 14:30:00',
                          read_time: '6'
                      },
                      {
                          id: 3,
                          title: 'Responsive Web Dizayn: Mobil İstifadəçi Təcrübəsi',
                          slug: 'responsive-web-dizayn',
                          content: 'Responsive web dizaynın vacibliyi və tətbiq metodları...',
                          excerpt: 'Müasir web saytlar üçün responsive dizayn prinsipləri və mobil istifadəçi təcrübəsi.',
                          featured_image: 'images/blog/web-design.jpg',
                          category_id: 3,
                          category_name: 'Web Dizayn',
                          tags: 'Web Dizayn, Responsive, UX',
                          is_featured: 0,
                          status: 'published',
                          created_at: '2024-01-05 09:15:00',
                          read_time: '7'
                      }
                  ];
                  this.updateBlogSection(fallbackBlogPosts);
                  break;
          }
      }
     
     /**
      * Show error notification to user
      */
     showErrorNotification(message) {
         // Check if notification system exists
         if (typeof showNotification === 'function') {
             showNotification(message, 'warning');
         } else {
             console.warn(message);
         }
     }

    updateServicesSection(services) {
        const servicesGrid = document.querySelector('.services-grid');
        if (!servicesGrid || !services || services.length === 0) return;

        const servicesHTML = services.slice(0, 4).map(service => `
            <div class="service-card fade-up">
                <div class="service-icon">
                    <i class="${service.icon || 'fas fa-cog'}"></i>
                </div>
                <h3 class="service-title">${service.title}</h3>
                <p class="service-description">
                    ${service.description}
                </p>
            </div>
        `).join('');

        servicesGrid.innerHTML = servicesHTML;
        
        // Re-initialize animations
        if (window.initScrollAnimations) {
            window.initScrollAnimations();
        }
    }

    updatePortfolioSection(portfolio) {
        // This will be used on portfolio page
        // For homepage, we might show featured projects
    }

    updateBlogSection(blogPosts) {
        // This will be used on blog page
        // For homepage, we might show recent posts
    }

    updateSiteSettings(settings) {
        // Update site-wide settings like contact info, social links, etc.
        if (settings.company_email) {
            const emailElements = document.querySelectorAll('[data-email]');
            emailElements.forEach(el => {
                el.textContent = settings.company_email;
                if (el.tagName === 'A') {
                    el.href = `mailto:${settings.company_email}`;
                }
            });
        }

        if (settings.company_phone) {
            const phoneElements = document.querySelectorAll('[data-phone]');
            phoneElements.forEach(el => {
                el.textContent = settings.company_phone;
                if (el.tagName === 'A') {
                    el.href = `tel:${settings.company_phone}`;
                }
            });
        }

        if (settings.company_address) {
            const addressElements = document.querySelectorAll('[data-address]');
            addressElements.forEach(el => {
                el.textContent = settings.company_address;
            });
        }
    }

    async loadServicesPage() {
        try {
            const response = await fetch(`${this.apiBase}/services.php`);
            if (response.ok) {
                const services = await response.json();
                this.updateServicesGrid(services);
            }
        } catch (error) {
            console.log('Services API not available, using static content');
        }
    }

    updateServicesGrid(services) {
        const servicesGrid = document.getElementById('services-grid');
        if (!servicesGrid || !services.length) return;

        servicesGrid.innerHTML = '';
        
        services.forEach(service => {
            const serviceCard = document.createElement('div');
            serviceCard.className = 'service-detail-card';
            serviceCard.innerHTML = `
                <div class="service-header">
                    <div class="service-icon">
                        <i class="${service.icon || 'fas fa-cog'}"></i>
                    </div>
                    <h3>${service.title}</h3>
                    <div class="service-price">${service.price || 'Qiymət üçün əlaqə saxlayın'}</div>
                </div>
                <div class="service-content">
                    <p>${service.description}</p>
                    <ul class="service-features">
                        ${service.features ? service.features.split(',').map(feature => 
                            `<li><i class="fas fa-check"></i> ${feature.trim()}</li>`
                        ).join('') : ''}
                    </ul>
                    <div class="service-duration">
                        <i class="fas fa-clock"></i> ${service.duration || 'Müddət müzakirə olunur'}
                    </div>
                    <button class="btn btn-primary service-detail-btn" data-service="${service.slug || service.id}">
                        Ətraflı
                    </button>
                </div>
            `;
            servicesGrid.appendChild(serviceCard);
        });
     }

    async loadPortfolioPage() {
        try {
            const response = await fetch(`${this.apiBase}/portfolio`);
            if (response.ok) {
                const portfolio = await response.json();
                this.updatePortfolioGrid(portfolio);
            }
        } catch (error) {
            console.log('Portfolio API not available, using static content');
        }
    }

    updatePortfolioGrid(projects) {
        const portfolioGrid = document.getElementById('portfolio-grid');
        if (!portfolioGrid || !projects.length) return;

        portfolioGrid.innerHTML = '';
        
        projects.forEach(project => {
            const projectCard = document.createElement('div');
            projectCard.className = 'col-lg-4 col-md-6 portfolio-item';
            projectCard.setAttribute('data-category', project.category || 'web');
            projectCard.setAttribute('data-project', project.slug || project.id);
            
            projectCard.innerHTML = `
                <div class="portfolio-card">
                    <img src="${project.image || 'https://trae-api-us.mchost.guru/api/ide/v1/text_to_image?prompt=modern%20web%20project%20professional%20design&image_size=landscape_4_3'}" alt="${project.title}" class="img-fluid" loading="lazy">
                    <div class="portfolio-overlay">
                        <div class="portfolio-content">
                            <h4>${project.title}</h4>
                            <p class="client-name">Müştəri: ${project.client || 'Konfidensial'}</p>
                            <p class="project-desc">${project.description}</p>
                            <div class="tech-stack">
                                ${project.technologies ? project.technologies.split(',').map(tech => 
                                    `<span class="tech-tag">${tech.trim()}</span>`
                                ).join('') : ''}
                            </div>
                            <div class="project-results">
                                <small>${project.results || 'Uğurlu nəticələr əldə edildi'}</small>
                            </div>
                            <div class="portfolio-buttons">
                                <a href="#" class="portfolio-btn primary view-project"><i class="fas fa-eye"></i> Görüntüle</a>
                                ${project.url ? `<a href="${project.url}" class="portfolio-btn" target="_blank"><i class="fas fa-external-link-alt"></i> Ziyaret Et</a>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            portfolioGrid.appendChild(projectCard);
        });
     }

    async loadBlogPage() {
        try {
            console.log('=== LOADING BLOG PAGE VIA API ===');
            console.log('API Base URL:', this.apiBase);
            console.log('Full URL:', `${this.apiBase}/blog`);
            
            const response = await fetch(`${this.apiBase}/blog.php`);
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            if (response.ok) {
                const result = await response.json();
                console.log('API response received:', result);
                console.log('Response success:', result.success);
                console.log('Response data length:', result.data ? result.data.length : 'No data');
                
                if (result.success && result.data) {
                    console.log('Calling updateBlogGrid with', result.data.length, 'posts');
                    this.updateBlogGrid(result.data);
                } else {
                    console.error('Invalid API response format:', result);
                }
            } else {
                console.error('API request failed:', response.status, response.statusText);
            }
        } catch (error) {
            console.error('Blog API error:', error);
            console.log('Blog API not available, using static content');
        }
    }

    updateBlogGrid(posts) {
        const blogGrid = document.getElementById('blogGrid');
        console.log('Updating blog grid with posts:', posts);
        if (!blogGrid) {
            console.error('blogGrid element not found');
            return;
        }
        if (!posts || !Array.isArray(posts) || posts.length === 0) {
            console.log('No posts to display');
            return;
        }

        blogGrid.innerHTML = '';
        console.log(`Rendering ${posts.length} blog posts...`);
        
        posts.forEach(post => {
            // Create Bootstrap column wrapper
            const colWrapper = document.createElement('div');
            colWrapper.className = 'col-lg-4 col-md-6 mb-4';
            
            // Create the blog card
            const postCard = document.createElement('article');
            postCard.className = 'blog-card h-100';
            postCard.setAttribute('data-category', post.category || 'marketing');
            
            const formattedDate = new Date(post.created_at).toLocaleDateString('az-AZ', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            postCard.innerHTML = `
                <div class="blog-image">
                    <img src="${post.featured_image || 'assets/images/blog-placeholder.svg'}" alt="${post.title}" loading="lazy">
                    <div class="blog-overlay">
                        <span class="blog-category">${post.category_name || 'Marketing'}</span>
                    </div>
                </div>
                <div class="blog-content">
                    <h3>${post.title}</h3>
                    <p>${post.excerpt || post.content.substring(0, 150) + '...'}</p>
                    <div class="blog-meta">
                        <span class="blog-date"><i class="fas fa-calendar"></i> ${formattedDate}</span>
                        <span class="read-time"><i class="fas fa-clock"></i> ${post.read_time || '5'} dəq</span>
                    </div>
                    <a href="blog-post.php?id=${post.id}" class="blog-link">Oxu <i class="fas fa-arrow-right"></i></a>
                </div>
            `;
            
            // Append card to column wrapper, then wrapper to grid
            colWrapper.appendChild(postCard);
            blogGrid.appendChild(colWrapper);
        });
    }

    // Method to refresh content (can be called when admin makes changes)
    /**
     * Refresh all content and clear cache
     */
    async refreshContent() {
        console.log('Refreshing all content...');
        this.cache.clear();
        await this.loadDynamicContent();
    }
    
    /**
     * Clear cache for specific endpoint
     */
    clearCache(endpoint = null) {
        if (endpoint) {
            this.cache.delete(endpoint);
            console.log(`Cache cleared for ${endpoint}`);
        } else {
            this.cache.clear();
            console.log('All cache cleared');
        }
    }
    
    /**
     * Get cache statistics
     */
    getCacheStats() {
        const stats = {
            size: this.cache.size,
            entries: []
        };
        
        this.cache.forEach((value, key) => {
            stats.entries.push({
                key,
                timestamp: value.timestamp,
                age: Date.now() - value.timestamp
            });
        });
        
        return stats;
    }
}

// Initialize API integration when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.apiIntegration = new APIIntegration();
});

// Make it globally available
window.APIIntegration = APIIntegration;