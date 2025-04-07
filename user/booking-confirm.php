<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';

// Set page metadata
$page_title = "Booking Confirmation - " . SITE_NAME;
$page_description = "Confirm your travel booking details and complete your reservation.";
$page_keywords = "booking confirmation, reservation, travel, finalize booking";

// Include new header templates
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/main-nav.php';

// Include necessary functions
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/service-functions.php';
require_once __DIR__ . '/../includes/booking-functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$booking = null;

// Get booking ID from URL
$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$bookingId) {
    header("Location: booking-history.php");
    exit;
}

// Fetch booking details
$booking = getBookingById($bookingId);

// Verify booking belongs to current user
if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
    header("Location: booking-history.php");
    exit;
}
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">Booking Confirmed!</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">Thank you for your booking</h4>
                        <p>A confirmation email has been sent to <?php echo htmlspecialchars($booking['user_email']); ?></p>
                    </div>

                    <h5>Booking Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Booking ID:</strong> #<?php echo $booking['id']; ?></p>
                            <p><strong>Service:</strong> <?php echo htmlspecialchars($booking['service_name']); ?></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($booking['category_name']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($booking['location']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Check-in:</strong> <?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></p>
                            <p><strong>Check-out:</strong> <?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></p>
                            <p><strong>Guests:</strong> <?php echo $booking['number_of_guests']; ?></p>
                            <p><strong>Total Price:</strong> $<?php echo number_format($booking['total_price'], 2); ?></p>
                        </div>
                    </div>

                    <?php if ($booking['image_url']): ?>
                        <div class="text-center my-4">
                            <img src="<?php echo strpos($booking['image_url'], 'http') === 0 ? 
                                htmlspecialchars($booking['image_url']) : 
                                '../' . htmlspecialchars($booking['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($booking['service_name']); ?>" 
                                 class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="alert alert-info">
                        <h6>Important Information</h6>
                        <ul class="mb-0">
                            <li>Please keep this booking confirmation for your records.</li>
                            <li>You can view your booking details anytime in your booking history.</li>
                            <li>For any changes or cancellations, please contact us at least 24 hours before check-in.</li>
                            <li>Present your booking ID upon arrival.</li>
                        </ul>
                    </div>

                    <div class="text-center mt-4">
                        <a href="booking-history.php" class="btn btn-primary">View Booking History</a>
                        <a href="../pages/index.php" class="btn btn-secondary">Return to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 