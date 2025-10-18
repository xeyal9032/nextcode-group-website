<?php
// Footer include file for all pages

// Include API router for handling API requests
require_once __DIR__ . '/../api-router.php';
?>
    <!-- Footer -->
    <footer class="footer">
        <div class="container-custom">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-brand">
                        <i class="fas fa-code"></i>
                        <span>NextCode</span>
                    </div>
                    <p class="footer-description">
                        Professional solutions for your success in the digital world.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Services</h4>
                    <ul class="footer-links">
                        <li><a href="services.php">SEO Optimization</a></li>
                        <li><a href="services.php">Social Media</a></li>
                        <li><a href="services.php">Branding</a></li>
                        <li><a href="services.php">Advertising Campaigns</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Company</h4>
                    <ul class="footer-links">
                        <li><a href="about.php">About</a></li>
                        <li><a href="portfolio.php">Portfolio</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Contact</h4>
                    <div class="footer-contact">
                        <p><i class="fas fa-envelope"></i> <span data-email>info@nextcode.com</span></p>
                        <p><i class="fas fa-phone"></i> <span data-phone>+380 97 258 00 00</span></p>
                        <p><i class="fas fa-map-marker-alt"></i> <span data-address>Bakı şəhəri, Nəsimi rayonu<br>28 May küçəsi 15</span></p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 NextCode Group. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Newsletter Modal -->
    <div class="modal fade" id="newsletterModal" tabindex="-1" aria-labelledby="newsletterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newsletterModalLabel">
                        <i class="fas fa-envelope me-2"></i>
                        Newsletter
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Subscribe to stay updated with the latest news and special offers in digital marketing.</p>
                    <form id="newsletterForm">
                        <div class="mb-3">
                            <label for="newsletterEmail" class="form-label">Your email address</label>
                            <input type="email" class="form-control" id="newsletterEmail" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="newsletterConsent" required>
                            <label class="form-check-label" for="newsletterConsent">
                                I accept the privacy policy
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="subscribeNewsletter">Subscribe</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Detail Modal -->
    <div class="modal fade" id="serviceDetailModal" tabindex="-1" aria-labelledby="serviceDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceDetailModalLabel">Service Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="service-detail-content" id="serviceDetailContent">
                        <!-- Service details will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='contact.php'">Request Quote</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Theme CSS Files -->
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/theme-variables.css">
    <link rel="stylesheet" href="css/theme-support.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/theme.js"></script>
    <script src="js/design-switcher.js"></script>
    <script src="js/performance-optimizer.js"></script>
    <script src="js/main.js"></script>
    <script src="js/modern-interactions.js"></script>
    <script src="js/cookie-consent.js"></script>
    <script src="js/analytics.js"></script>
    <script src="js/traffic-conversion.js"></script>
    <script src="js/performance-metrics.js"></script>
    <!-- Enhanced Performance JavaScript -->
    <script src="js/image-optimizer-enhanced.js"></script>
    <!-- Enhanced Smooth Scrolling -->
    <script src="js/smooth-scroll-enhanced.js"></script>

</body>
</html>