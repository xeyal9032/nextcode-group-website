// Blog Page Interactive Features
document.addEventListener('DOMContentLoaded', function() {
    // Blog post filtering and search
    const searchInput = document.getElementById('blog-search');
    const categoryFilter = document.getElementById('category-filter');
    const blogPosts = document.querySelectorAll('.blog-post');
    const loadMoreBtn = document.getElementById('load-more');
    
    let visiblePosts = 6; // Initial number of visible posts
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterPosts(searchTerm, categoryFilter ? categoryFilter.value : 'all');
        });
    }
    
    // Category filter functionality
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const category = this.value;
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            filterPosts(searchTerm, category);
        });
    }
    
    // Filter posts function
    function filterPosts(searchTerm, category) {
        let visibleCount = 0;
        
        blogPosts.forEach(post => {
            const title = post.querySelector('.post-title').textContent.toLowerCase();
            const excerpt = post.querySelector('.post-excerpt').textContent.toLowerCase();
            const postCategory = post.dataset.category || 'general';
            
            const matchesSearch = title.includes(searchTerm) || excerpt.includes(searchTerm);
            const matchesCategory = category === 'all' || postCategory === category;
            
            if (matchesSearch && matchesCategory) {
                post.style.display = 'block';
                post.style.animation = 'fadeInUp 0.5s ease';
                visibleCount++;
            } else {
                post.style.display = 'none';
            }
        });
        
        // Show/hide "no results" message
        showNoResultsMessage(visibleCount === 0);
        
        // Update load more button visibility
        if (loadMoreBtn) {
            loadMoreBtn.style.display = visibleCount > visiblePosts ? 'block' : 'none';
        }
    }
    
    // Show/hide no results message
    function showNoResultsMessage(show) {
        let noResultsMsg = document.getElementById('no-results-message');
        
        if (show && !noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'no-results-message';
            noResultsMsg.className = 'no-results';
            noResultsMsg.innerHTML = `
                <div class="no-results-content">
                    <i class="fas fa-search"></i>
                    <h3>Heç bir nəticə tapılmadı</h3>
                    <p>Axtarış kriteriyalarınızı dəyişdirərək yenidən cəhd edin.</p>
                </div>
            `;
            
            const blogGrid = document.querySelector('.blog-grid');
            if (blogGrid) {
                blogGrid.appendChild(noResultsMsg);
            }
        } else if (!show && noResultsMsg) {
            noResultsMsg.remove();
        }
    }
    
    // Load more functionality
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            visiblePosts += 6;
            
            blogPosts.forEach((post, index) => {
                if (index < visiblePosts && post.style.display !== 'none') {
                    post.style.display = 'block';
                }
            });
            
            // Hide load more button if all posts are visible
            const totalVisiblePosts = Array.from(blogPosts).filter(post => post.style.display !== 'none').length;
            if (totalVisiblePosts <= visiblePosts) {
                this.style.display = 'none';
            }
        });
    }
    
    // Blog post hover effects
    blogPosts.forEach(post => {
        post.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15)';
        });
        
        post.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
    });
    
    // Reading time calculator
    function calculateReadingTime(text) {
        const wordsPerMinute = 200;
        const words = text.trim().split(/\s+/).length;
        const readingTime = Math.ceil(words / wordsPerMinute);
        return readingTime;
    }
    
    // Add reading time to posts
    blogPosts.forEach(post => {
        const excerpt = post.querySelector('.post-excerpt');
        const meta = post.querySelector('.post-meta');
        
        if (excerpt && meta) {
            const readingTime = calculateReadingTime(excerpt.textContent);
            const readingTimeElement = document.createElement('span');
            readingTimeElement.className = 'reading-time';
            readingTimeElement.innerHTML = `<i class="fas fa-clock"></i> ${readingTime} dəq oxu`;
            meta.appendChild(readingTimeElement);
        }
    });
    
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
    
    // Observe blog posts for animation
    blogPosts.forEach(post => {
        observer.observe(post);
    });
    
    // Social sharing functionality
    function addSocialSharing() {
        blogPosts.forEach(post => {
            const postLink = post.querySelector('.post-title a');
            if (postLink) {
                const postUrl = encodeURIComponent(window.location.origin + postLink.getAttribute('href'));
                const postTitle = encodeURIComponent(postLink.textContent);
                
                const shareButtons = document.createElement('div');
                shareButtons.className = 'social-share';
                shareButtons.innerHTML = `
                    <span>Paylaş:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=${postUrl}" target="_blank" class="share-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=${postUrl}&text=${postTitle}" target="_blank" class="share-btn twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=${postUrl}" target="_blank" class="share-btn linkedin">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="whatsapp://send?text=${postTitle} ${postUrl}" class="share-btn whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                `;
                
                post.appendChild(shareButtons);
            }
        });
    }
    
    // Add social sharing buttons
    addSocialSharing();
    
    // Newsletter subscription
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = this.querySelector('input[type="email"]').value;
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Göndərilir...';
            submitBtn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                showNotification('Bülten abunəliyiniz uğurla tamamlandı!', 'success');
                this.reset();
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });
    }
    
    // Add CSS animations and styles
    const style = document.createElement('style');
    style.textContent = `
        .animate-in {
            animation: fadeInUp 0.8s ease forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .blog-post {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-results-content i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .no-results-content h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .reading-time {
            margin-left: 15px;
            color: #666;
            font-size: 14px;
        }
        
        .social-share {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .social-share span {
            font-size: 14px;
            color: #666;
            margin-right: 5px;
        }
        
        .share-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: transform 0.3s ease;
        }
        
        .share-btn:hover {
            transform: scale(1.1);
        }
        
        .share-btn.facebook { background: #3b5998; }
        .share-btn.twitter { background: #1da1f2; }
        .share-btn.linkedin { background: #0077b5; }
        .share-btn.whatsapp { background: #25d366; }
        
        .blog-filters {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        
        .filter-group {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group input,
        .filter-group select {
            padding: 12px 15px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #3498db;
        }
        
        #blog-search {
            flex: 1;
            min-width: 250px;
        }
        
        @media (max-width: 768px) {
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .social-share {
                flex-wrap: wrap;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Add scroll to top button
    const scrollTopBtn = document.createElement('button');
    scrollTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollTopBtn.style.cssText = `
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        opacity: 0;
        visibility: hidden;
        z-index: 1000;
    `;
    
    document.body.appendChild(scrollTopBtn);
    
    // Show/hide scroll to top button
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            scrollTopBtn.style.opacity = '1';
            scrollTopBtn.style.visibility = 'visible';
        } else {
            scrollTopBtn.style.opacity = '0';
            scrollTopBtn.style.visibility = 'hidden';
        }
    });
    
    // Scroll to top functionality
    scrollTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Add hover effect to scroll top button
    scrollTopBtn.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.1)';
        this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.3)';
    });
    
    scrollTopBtn.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
        this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
    });
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
    }, 3000);
}

// Add animation keyframes
const blogAnimationStyles = document.createElement('style');
blogAnimationStyles.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(blogAnimationStyles);