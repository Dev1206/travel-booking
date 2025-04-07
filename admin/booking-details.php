<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/booking-functions.php';

// Set page metadata
$page_title = "Booking Details - Admin Dashboard - " . SITE_NAME;
$page_description = "View and manage detailed booking information for a specific reservation.";
$page_keywords = "booking details, reservation information, admin, booking management";

// Include new header templates
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/admin-nav.php';

// Ensure user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: " . SITE_URL . "/user/login.php");
    exit;
}

$error = '';
$message = '';
$booking = null;

// Get booking ID from URL
$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$bookingId) {
    header("Location: view-bookings.php");
    exit;
}

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['status'])) {
    $status = trim($_POST['status']);
    
    if (updateBookingStatus($bookingId, $status)) {
        $message = "Booking status updated successfully.";
    } else {
        $error = "Error updating booking status.";
    }
}

// Fetch booking details
$booking = getBookingById($bookingId);

if (!$booking) {
    header("Location: view-bookings.php");
    exit;
}

// Calculate duration
$checkInDate = new DateTime($booking['check_in_date']);
$checkOutDate = new DateTime($booking['check_out_date']);
$duration = $checkInDate->diff($checkOutDate)->days;
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Booking Details #<?php echo $booking['id']; ?></h2>
        <a href="view-bookings.php" class="btn btn-secondary">Back to Bookings</a>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <!-- Booking Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Booking Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-<?php 
                                    echo $booking['status'] === 'confirmed' ? 'success' : 
                                        ($booking['status'] === 'pending' ? 'warning' : 
                                        ($booking['status'] === 'cancelled' ? 'danger' : 'info')); 
                                ?>">
                                    <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                </span>
                            </p>
                            <p><strong>Check-in:</strong> <?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></p>
                            <p><strong>Check-out:</strong> <?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></p>
                            <p><strong>Duration:</strong> <?php echo $duration; ?> night<?php echo $duration !== 1 ? 's' : ''; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Guests:</strong> <?php echo $booking['number_of_guests']; ?></p>
                            <p><strong>Total Price:</strong> $<?php echo number_format($booking['total_price'], 2); ?></p>
                            <p><strong>Booked on:</strong> <?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($booking['status'] !== 'cancelled' && $booking['status'] !== 'completed'): ?>
                        <hr>
                        <h6>Update Status</h6>
                        <form method="POST" class="d-flex gap-2">
                            <?php if ($booking['status'] === 'pending'): ?>
                                <button type="submit" name="update_status" value="confirmed" class="btn btn-success">
                                    Confirm Booking
                                </button>
                            <?php endif; ?>
                            <button type="submit" name="update_status" value="cancelled" 
                                    class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to cancel this booking?');">
                                Cancel Booking
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Service Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Service Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Service Image -->
                        <?php if (isset($booking['image_url']) && $booking['image_url']): ?>
                            <div class="text-center mb-4">
                                <img src="<?php 
                                    echo strpos($booking['image_url'], 'http') === 0 ? 
                                        htmlspecialchars($booking['image_url']) : 
                                        '../' . htmlspecialchars($booking['image_url']); 
                                ?>" 
                                     alt="<?php echo htmlspecialchars($booking['service_name']); ?>" 
                                     class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        <?php endif; ?>
                        <div class="col-md-8">
                            <h5><?php echo htmlspecialchars($booking['service_name']); ?></h5>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($booking['category_name']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($booking['location']); ?></p>
                            <p><strong>Base Price:</strong> $<?php echo number_format($booking['price'], 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['user_email']); ?></p>
                    <?php if ($booking['phone']): ?>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['phone']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Price Breakdown -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Price Breakdown</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Base Price:</td>
                            <td class="text-end">$<?php echo number_format($booking['price'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Number of Nights:</td>
                            <td class="text-end"><?php echo $duration; ?></td>
                        </tr>
                        <tr>
                            <td>Number of Guests:</td>
                            <td class="text-end"><?php echo $booking['number_of_guests']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Total:</strong></td>
                            <td class="text-end"><strong>$<?php echo number_format($booking['total_price'], 2); ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 