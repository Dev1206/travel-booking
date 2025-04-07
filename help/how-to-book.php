<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';

// Set page metadata
$page_title = "How to Book - Help Center - " . SITE_NAME;
$page_description = "Step-by-step guide on how to book hotels, flights, and tours.";
$page_keywords = "booking guide, how to book, travel booking, hotel reservation, flight booking";

// Include new header templates
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/main-nav.php';

require_once __DIR__ . '/../includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Old header template reference removed
?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="help-home.php">Help Center</a></li>
            <li class="breadcrumb-item active">How to Make a Booking</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-4">How to Make a Booking</h1>
            
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i> You must be logged in to make a booking. If you don't have an account, please <a href="../user/register.php">register</a> first.
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Step 1: Browse Services</h2>
                    <p>Start by exploring our available services:</p>
                    <ul>
                        <li>Go to the <strong>Hotels</strong>, <strong>Flights</strong>, or <strong>Tours</strong> section from the main navigation</li>
                        <li>Browse listings or use filters to narrow down your search by location, price, etc.</li>
                        <li>Click on any service to view detailed information</li>
                    </ul>
                    <div class="alert alert-secondary p-2 small">
                        <strong>Quick Tip:</strong> You can access services directly from the main navigation bar at the top of every page.
                    </div>
                    <img src="https://images.unsplash.com/photo-1517840901100-8179e982acb7?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                         alt="Browse Services" class="img-fluid rounded mb-3 shadow-sm">
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Step 2: Select a Service</h2>
                    <p>When you find a service you're interested in:</p>
                    <ul>
                        <li>Click on the service card or the "View Details" button</li>
                        <li>Review all details including description, amenities, location, and price</li>
                        <li>Check availability for your preferred dates</li>
                        <li>Click "Book Now" to proceed with booking</li>
                    </ul>
                    <div class="alert alert-secondary p-2 small">
                        <strong>Note:</strong> Prices are displayed per night for hotels and per person for flights and tours.
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Step 3: Fill Booking Details</h2>
                    <p>On the booking form:</p>
                    <ul>
                        <li>Select your <strong>check-in</strong> and <strong>check-out dates</strong> using the date pickers</li>
                        <li>Specify the <strong>number of guests</strong></li>
                        <li>The system will automatically calculate the total price based on:</li>
                        <ul>
                            <li>Base price × Number of nights × Number of guests</li>
                        </ul>
                        <li>Review the booking details and total price in the price calculator</li>
                    </ul>
                    <div class="alert alert-warning p-2 small">
                        <strong>Important:</strong> Check-in date must be today or later, and check-out date must be after check-in date.
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Step 4: Confirm and Pay</h2>
                    <p>To complete your booking:</p>
                    <ul>
                        <li>Review all booking details one last time</li>
                        <li>Click the "Proceed to Payment" button</li>
                        <li>Enter your payment details securely</li>
                        <li>Confirm your payment to finalize the booking</li>
                    </ul>
                    <p>After successful payment:</p>
                    <ul>
                        <li>You'll receive a booking confirmation email with all details</li>
                        <li>Your booking will be added to your booking history</li>
                        <li>You can view and manage your booking in the "My Bookings" section</li>
                    </ul>
                    <div class="alert alert-success p-2 small">
                        <strong>Success:</strong> Your booking is confirmed once payment is processed successfully.
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Manage Your Booking</h2>
                    <p>After making a booking, you can:</p>
                    <ul>
                        <li>View all your bookings by clicking on "My Bookings" in the main navigation</li>
                        <li>Check the status of each booking (pending, confirmed, completed, or cancelled)</li>
                        <li>Cancel a booking if needed (subject to cancellation policy)</li>
                        <li>View booking details including dates, location, and total price</li>
                    </ul>
                    <div class="alert alert-info p-2 small">
                        <strong>Booking Status:</strong> Your booking will initially be marked as "pending" until confirmed by our team.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="h5 mb-3">FAQ: Booking Process</h3>
                    <div class="accordion" id="bookingFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Can I book without creating an account?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#bookingFAQ">
                                <div class="accordion-body">
                                    No, you need to be logged in to make a booking. This ensures your bookings are saved to your account and you can easily access them later.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    How do I know if a service is available?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#bookingFAQ">
                                <div class="accordion-body">
                                    The system automatically checks availability when you select dates. If the service is not available for your selected dates, you'll see an error message.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How is the total price calculated?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#bookingFAQ">
                                <div class="accordion-body">
                                    The total price is calculated as: (base price per night) × (number of nights) × (number of guests). The price calculator shows this breakdown in real-time as you select dates and guests.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="h5 mb-3">Need More Help?</h3>
                    <p>If you're having trouble with the booking process:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="contact-support.php" class="text-decoration-none">
                                <i class="fas fa-headset me-2"></i> Contact Support
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="../user/booking-history.php" class="text-decoration-none">
                                <i class="fas fa-history me-2"></i> View Your Bookings
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 