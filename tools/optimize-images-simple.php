<?php
// Simple Image Optimization Helper
// Bu script resim optimizasyonu için HTML/CSS çözümleri sağlar

class SimpleImageOptimizer {
    private $outputDir = 'css/optimized/';
    
    public function __construct() {
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    public function generateImageOptimizationCSS() {
        $css = '
/* Image Optimization CSS */
img {
    max-width: 100%;
    height: auto;
    display: block;
}

/* Lazy Loading Styles */
.lazy {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.loaded {
    opacity: 1;
}

/* Responsive Images */
.responsive-img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

/* Image Placeholder */
.img-placeholder {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* WebP Support Detection */
.webp .img-webp {
    display: block;
}

.no-webp .img-webp {
    display: none;
}

.webp .img-fallback {
    display: none;
}

.no-webp .img-fallback {
    display: block;
}

/* Image Optimization for Different Screen Sizes */
@media (max-width: 768px) {
    .hero-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
}

@media (min-width: 769px) {
    .hero-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }
}

/* Portfolio Images */
.portfolio-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 12px;
    transition: transform 0.3s ease;
}

.portfolio-image:hover {
    transform: scale(1.05);
}

/* Blog Images */
.blog-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}

/* Team Images */
.team-image {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto;
    display: block;
}

/* Optimized Image Loading */
.image-container {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
}

.image-container::before {
    content: "";
    display: block;
    padding-top: 56.25%; /* 16:9 aspect ratio */
}

.image-container img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
';
        
        file_put_contents($this->outputDir . 'image-optimization.css', $css);
        echo "✓ Generated image-optimization.css\n";
    }
    
    public function generateImageOptimizationJS() {
        $js = '
// Image Optimization JavaScript
class ImageOptimizer {
    constructor() {
        this.init();
    }
    
    init() {
        this.detectWebPSupport();
        this.initLazyLoading();
        this.optimizeImages();
    }
    
    detectWebPSupport() {
        const webp = new Image();
        webp.onload = webp.onerror = () => {
            document.documentElement.classList.add(webp.height === 2 ? "webp" : "no-webp");
        };
        webp.src = "data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA";
    }
    
    initLazyLoading() {
        if ("IntersectionObserver" in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        this.loadImage(img);
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: "50px 0px",
                threshold: 0.01
            });
            
            document.querySelectorAll("img[data-src]").forEach(img => {
                imageObserver.observe(img);
            });
        } else {
            // Fallback for older browsers
            document.querySelectorAll("img[data-src]").forEach(img => {
                this.loadImage(img);
            });
        }
    }
    
    loadImage(img) {
        const src = img.getAttribute("data-src");
        if (src) {
            img.src = src;
            img.removeAttribute("data-src");
            img.classList.remove("lazy");
            img.classList.add("loaded");
        }
    }
    
    optimizeImages() {
        // Add loading states to images
        document.querySelectorAll("img").forEach(img => {
            if (!img.complete) {
                img.classList.add("lazy");
                img.addEventListener("load", () => {
                    img.classList.remove("lazy");
                    img.classList.add("loaded");
                });
            } else {
                img.classList.add("loaded");
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    new ImageOptimizer();
});
';
        
        file_put_contents($this->outputDir . 'image-optimization.min.js', $js);
        echo "✓ Generated image-optimization.min.js\n";
    }
    
    public function generateResponsiveImageHTML() {
        $html = '
<!-- Responsive Image Examples -->
<!-- WebP with Fallback -->
<picture>
    <source srcset="images/webp/hero-image.webp" type="image/webp">
    <source srcset="images/optimized/hero-image.jpg" type="image/jpeg">
    <img src="images/optimized/hero-image.jpg" alt="Hero Image" class="hero-image" loading="lazy">
</picture>

<!-- Lazy Loading Image -->
<img data-src="images/portfolio/project-1.jpg" alt="Project 1" class="portfolio-image lazy" width="400" height="250">

<!-- Responsive Portfolio Grid -->
<div class="portfolio-grid">
    <div class="image-container">
        <img data-src="images/portfolio/project-1.jpg" alt="Project 1" class="lazy" loading="lazy">
    </div>
    <div class="image-container">
        <img data-src="images/portfolio/project-2.jpg" alt="Project 2" class="lazy" loading="lazy">
    </div>
</div>

<!-- Team Images -->
<div class="team-member">
    <img data-src="images/team/member-1.jpg" alt="Team Member" class="team-image lazy" loading="lazy">
</div>
';
        
        file_put_contents($this->outputDir . 'responsive-images.html', $html);
        echo "✓ Generated responsive-images.html\n";
    }
    
    public function optimizeAll() {
        echo "🚀 Starting Simple Image Optimization...\n\n";
        
        $this->generateImageOptimizationCSS();
        $this->generateImageOptimizationJS();
        $this->generateResponsiveImageHTML();
        
        echo "\n✅ Simple Image Optimization completed!\n";
        echo "📝 Note: For actual image compression, use online tools like TinyPNG or ImageOptim\n";
    }
}

// Script'i çalıştır
$optimizer = new SimpleImageOptimizer();
$optimizer->optimizeAll();
?>
