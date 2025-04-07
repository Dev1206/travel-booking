<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';

// Set page metadata
$page_title = "Help Center - " . SITE_NAME;
$page_description = "Get help and support for your travel booking experience.";
$page_keywords = "help, support, faq, travel, booking, assistance";

// Include new header templates
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/main-nav.php';

require_once __DIR__ . '/../includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h1 class="display-4 mb-3">How can we help you?</h1>
            <div class="col-md-6 mx-auto">
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-lg" id="helpSearch" 
                           placeholder="Search help topics...">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="fas fa-book-open text-primary"></i> Getting Started
                    </h3>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="how-to-book.php" class="text-decoration-none">
                                <i class="fas fa-angle-right"></i> How to Make a Booking
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="update-profile.php" class="text-decoration-none">
                                <i class="fas fa-angle-right"></i> Managing Your Profile
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="change-theme.php" class="text-decoration-none">
                                <i class="fas fa-angle-right"></i> Customizing Your Theme
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="fas fa-question-circle text-primary"></i> FAQ
                    </h3>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How do I cancel my booking?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    You can cancel your booking by going to your booking history 
                                    and clicking the "Cancel" button. Please note our cancellation 
                                    policy and any applicable fees.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What payment methods do you accept?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We accept all major credit cards, debit cards, and PayPal. 
                                    All payments are processed securely through our payment gateway.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="fas fa-headset text-primary"></i> Need More Help?
                    </h3>
                    <p>Can't find what you're looking for? Our support team is here to help!</p>
                    <a href="contact-support.php" class="btn btn-primary">
                        <i class="fas fa-envelope"></i> Contact Support
                    </a>
                    <hr>
                    <h5>Operating Hours</h5>
                    <p class="mb-1">Monday - Friday: 9:00 AM - 6:00 PM</p>
                    <p>Saturday - Sunday: 10:00 AM - 4:00 PM</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('helpSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            // TODO: Implement real-time search functionality
            console.log('Searching for:', e.target.value);
        });
    }
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 