<?php
require_once __DIR__ . '/../config/config.php';
?>
    </div> <!-- End of container -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>About Us</h5>
                    <p>Your trusted partner for all your travel needs. Book hotels, flights, and tours with ease.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>/help/help-home.php" class="text-white">Help Center</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/help/contact-support.php" class="text-white">Contact Us</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/help/faq.php" class="text-white">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <address>
                        <p>Email: <?php echo ADMIN_EMAIL; ?></p>
                        <p>Phone: +1 (123) 456-7890</p>
                    </address>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>
</html> 