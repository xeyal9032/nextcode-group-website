class PortfolioManager {
    constructor() {
        this.portfolioItems = [];
        this.filteredItems = [];
        this.currentFilter = 'all';
        this.searchQuery = '';
        this.itemsPerPage = 6;
        this.currentPage = 1;
        this.isLoading = false;
        this.images = [];
        this.lightbox = null;
        this.currentImageIndex = 0;
        this.init();
    }

    init() {
        this.loadPortfolioData();
        this.setupEventListeners();
        this.setupIntersectionObserver();
        this.setupSearch();
        this.collectImages();
        this.createLightbox();
        this.initializeAnimations();
    }

    loadPortfolioData() {
        this.portfolioItems = document.querySelectorAll('.portfolio-item');
        this.filteredItems = Array.from(this.portfolioItems);
    }

    setupIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, { threshold: 0.1 });

        this.portfolioItems.forEach(item => {
            observer.observe(item);
        });
    }

    setupSearch() {
        const searchInput = document.querySelector('.portfolio-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchQuery = e.target.value;
                this.applyFilters();
            });
        }
    }

    applyFilters() {
        this.filteredItems = Array.from(this.portfolioItems).filter(item => {
            const itemCategory = item.dataset.category || 'all';
            const categoryMatch = this.currentFilter === 'all' || itemCategory === this.currentFilter;
            
            if (!categoryMatch) return false;
            
            if (this.searchQuery) {
                const { title, category } = this.extractItemInfo(item);
                const searchTerm = this.searchQuery.toLowerCase();
                return title.toLowerCase().includes(searchTerm) || 
                       category.toLowerCase().includes(searchTerm);
            }
            
            return true;
        });
        
        this.updateDisplay();
    }

    updateDisplay() {
        this.portfolioItems.forEach(item => {
            const shouldShow = this.filteredItems.includes(item);
            
            if (shouldShow) {
                item.style.display = 'block';
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0) scale(1)';
                }, 50);
            } else {
                item.style.opacity = '0';
                item.style.transform = 'translateY(-20px) scale(0.8)';
                setTimeout(() => {
                    item.style.display = 'none';
                }, 300);
            }
        });
        
        this.collectImages();
    }

    setupEventListeners() {
        // Filter buttons
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                button.classList.add('active');
                
                // Get filter value
                this.currentFilter = button.getAttribute('data-filter') || 'all';
                
                // Apply filter with animation
                this.applyFilters();
            });
        });

        // Load more button
        const loadMoreBtn = document.querySelector('.load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', () => {
                this.loadMoreItems();
            });
        }

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.searchQuery = e.target.value;
                    this.applyFilters();
                }, 300);
            });
        }

        // Portfolio items
        this.portfolioItems.forEach((item, index) => {
            this.handleItemHover(item);
            
            // View project button
            const viewBtn = item.querySelector('.view-project');
            if (viewBtn) {
                viewBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const projectId = viewBtn.dataset.project;
                    if (projectId) {
                        this.openProjectDetailModal(projectId);
                    } else {
                        this.openProjectModal(item, index);
                    }
                });
            }
            
            // Click to open lightbox
            item.addEventListener('click', (e) => {
                if (!e.target.closest('.portfolio-btn')) {
                    this.openItemLightbox(item, index);
                }
            });
        });
    }

    loadMoreItems() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        const loadMoreBtn = document.querySelector('.load-more-btn');
        const spinner = document.querySelector('.loading-spinner');
        
        if (loadMoreBtn) loadMoreBtn.disabled = true;
        if (spinner) spinner.style.display = 'flex';
        
        // Simulate loading delay
        setTimeout(() => {
            this.currentPage++;
            this.renderItems();
            
            this.isLoading = false;
            if (loadMoreBtn) loadMoreBtn.disabled = false;
            if (spinner) spinner.style.display = 'none';
        }, 1000);
    }

    renderItems() {
        const container = document.querySelector('.portfolio-items');
        if (!container) return;
        
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const itemsToShow = this.filteredItems.slice(startIndex, endIndex);
        
        itemsToShow.forEach((item, index) => {
            setTimeout(() => {
                item.style.display = 'block';
                item.style.opacity = '0';
                item.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 50);
            }, index * 100);
        });
        
        // Hide load more button if no more items
        const loadMoreBtn = document.querySelector('.load-more-btn');
        if (loadMoreBtn && endIndex >= this.filteredItems.length) {
            loadMoreBtn.style.display = 'none';
        }
    }

    handleItemHover(item) {
        item.addEventListener('mouseenter', () => {
            item.style.transform = 'translateY(-10px)';
            item.style.boxShadow = '0 20px 40px rgba(0,0,0,0.1)';
        });
        
        item.addEventListener('mouseleave', () => {
            item.style.transform = 'translateY(0)';
            item.style.boxShadow = '0 5px 15px rgba(0,0,0,0.08)';
        });
    }

    openItemLightbox(item, index) {
        const imgElement = item.querySelector('img');
        const imgSrc = imgElement ? imgElement.src : '';
        
        if (imgSrc) {
            const { title, category } = this.extractItemInfo(item);
            this.openLightbox(imgSrc, title, category, index);
        }
    }

    extractItemInfo(item) {
        let title = 'Untitled';
        let category = 'Uncategorized';
        
        try {
            // Extract title
            const titleSelectors = ['.portfolio-content h4', 'h4', '.portfolio-title'];
            for (const selector of titleSelectors) {
                const element = item.querySelector(selector);
                if (element && element.textContent.trim()) {
                    title = element.textContent.trim();
                    break;
                }
            }
            
            // Extract category
            const categorySelectors = ['.client-name', '.portfolio-category', '.portfolio-subtitle'];
            for (const selector of categorySelectors) {
                const element = item.querySelector(selector);
                if (element && element.textContent.trim()) {
                    category = element.textContent.trim();
                    break;
                }
            }
            
            // Fallback to dataset
            if (category === 'Uncategorized' && item.dataset.category) {
                category = item.dataset.category;
            }
        } catch (e) {
            console.warn('Error extracting item info:', e);
        }
        
        return { title, category };
    }

    collectImages() {
        this.images = Array.from(this.portfolioItems).map(item => {
            const img = item.querySelector('img');
            if (!img || !img.src) return null;
            
            const { title, category } = this.extractItemInfo(item);
            
            return {
                src: img.src,
                title: title,
                category: category
            };
        }).filter(item => item !== null);
    }

    filterPortfolio(category) {
        this.portfolioItems.forEach((item, index) => {
            const itemCategory = item.dataset.category || 'all';
            const shouldShow = category === 'all' || itemCategory === category;
            
            if (shouldShow) {
                item.style.display = 'block';
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0) scale(1)';
                }, index * 50);
            } else {
                item.style.opacity = '0';
                item.style.transform = 'translateY(-20px) scale(0.8)';
                setTimeout(() => {
                    item.style.display = 'none';
                }, 300);
            }
        });
        
        this.collectImages();
    }

    searchPortfolio(query) {
        const searchTerm = query.toLowerCase();
        
        this.portfolioItems.forEach(item => {
            const { title, category } = this.extractItemInfo(item);
            const matches = title.toLowerCase().includes(searchTerm) || 
                          category.toLowerCase().includes(searchTerm);
            
            item.style.display = matches ? 'block' : 'none';
        });
    }

    updateActiveFilter(activeBtn) {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        activeBtn.classList.add('active');
    }

    createLightbox() {
        this.lightbox = document.createElement('div');
        this.lightbox.className = 'portfolio-lightbox';
        this.lightbox.innerHTML = `
            <div class="lightbox-content">
                <button class="lightbox-close">&times;</button>
                <button class="lightbox-prev"><i class="fas fa-chevron-left"></i></button>
                <button class="lightbox-next"><i class="fas fa-chevron-right"></i></button>
                <div class="lightbox-image-container">
                    <img class="lightbox-image" src="" alt="">
                    <div class="lightbox-info">
                        <h3 class="lightbox-title"></h3>
                        <p class="lightbox-category"></p>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(this.lightbox);
        this.setupLightboxEvents();
    }

    setupLightboxEvents() {
        const closeBtn = this.lightbox.querySelector('.lightbox-close');
        const prevBtn = this.lightbox.querySelector('.lightbox-prev');
        const nextBtn = this.lightbox.querySelector('.lightbox-next');
        
        closeBtn.addEventListener('click', () => this.closeLightbox());
        prevBtn.addEventListener('click', () => this.prevImage());
        nextBtn.addEventListener('click', () => this.nextImage());
        
        this.lightbox.addEventListener('click', (e) => {
            if (e.target === this.lightbox) {
                this.closeLightbox();
            }
        });
        
        document.addEventListener('keydown', (e) => {
            if (this.lightbox.classList.contains('active')) {
                switch(e.key) {
                    case 'Escape':
                        this.closeLightbox();
                        break;
                    case 'ArrowLeft':
                        this.prevImage();
                        break;
                    case 'ArrowRight':
                        this.nextImage();
                        break;
                }
            }
        });
    }

    openLightbox(src, title, category, index) {
        this.currentImageIndex = index;
        this.updateLightboxContent(src, title, category);
        this.lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    closeLightbox() {
        this.lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    prevImage() {
        this.currentImageIndex = (this.currentImageIndex - 1 + this.images.length) % this.images.length;
        const image = this.images[this.currentImageIndex];
        this.updateLightboxContent(image.src, image.title, image.category);
    }

    nextImage() {
        this.currentImageIndex = (this.currentImageIndex + 1) % this.images.length;
        const image = this.images[this.currentImageIndex];
        this.updateLightboxContent(image.src, image.title, image.category);
    }

    updateLightboxContent(src, title, category) {
        const img = this.lightbox.querySelector('.lightbox-image');
        const titleEl = this.lightbox.querySelector('.lightbox-title');
        const categoryEl = this.lightbox.querySelector('.lightbox-category');
        
        img.src = src;
        img.alt = title;
        titleEl.textContent = title;
        categoryEl.textContent = category;
    }

    initializeAnimations() {
        // Animate portfolio items on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, { threshold: 0.1 });

        this.portfolioItems.forEach(item => {
            observer.observe(item);
        });
    }

    openProjectDetailModal(projectId) {
        // Implementation for opening project detail modal
        console.log('Opening project detail modal for:', projectId);
    }

    openProjectModal(item, index) {
        // Implementation for opening generic project modal
        console.log('Opening project modal for item:', index);
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
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

// Initialize portfolio when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new PortfolioManager();
});

// Add CSS for animations and lightbox
const style = document.createElement('style');
style.textContent = `
    .portfolio-lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .portfolio-lightbox.active {
        opacity: 1;
        visibility: visible;
    }
    
    .lightbox-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
    }
    
    .lightbox-image {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
    }
    
    .lightbox-close,
    .lightbox-prev,
    .lightbox-next {
        position: absolute;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.3s ease;
    }
    
    .lightbox-close {
        top: -20px;
        right: -20px;
    }
    
    .lightbox-prev {
        left: -60px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .lightbox-next {
        right: -60px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .lightbox-info {
        text-align: center;
        color: white;
        margin-top: 20px;
    }
    
    .portfolio-item {
        transition: all 0.3s ease;
        opacity: 0;
        transform: translateY(30px);
    }
    
    .portfolio-item.animate-in {
        opacity: 1;
        transform: translateY(0);
    }
    
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification-info {
        background: #007bff;
    }
    
    .notification-success {
        background: #28a745;
    }
    
    .notification-error {
        background: #dc3545;
    }
`;
document.head.appendChild(style);