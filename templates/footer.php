    <footer class="footer mt-auto py-3 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>About <?php echo SITE_NAME; ?></h5>
                    <p class="text-muted">Your trusted partner for travel bookings. We offer the best deals on hotels, flights, and tours worldwide.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>/pages/hotels.php" class="text-muted">Hotels</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/flights.php" class="text-muted">Flights</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/tours.php" class="text-muted">Tours</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/help/help-home.php" class="text-muted">Help Center</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-phone me-2"></i>+1 234 567 8900</li>
                        <li><i class="fas fa-envelope me-2"></i>contact@travelbooking.com</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>123 Travel Street, City</li>
                    </ul>
                    <div class="social-links mt-3">
                        <a href="#" class="text-muted me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0 text-muted">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a href="#" class="text-muted">Privacy Policy</a>
                        </li>
                        <li class="list-inline-item">
                            <span class="text-muted">|</span>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" class="text-muted">Terms of Use</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
    // Add active class to current nav item
    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    });
    </script>
</body>
</html> 