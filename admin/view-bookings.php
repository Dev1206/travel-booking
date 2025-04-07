<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/booking-functions.php';

$page_title = "View Bookings - Admin Dashboard - " . SITE_NAME;
$page_description = "Admin interface for managing and monitoring all travel bookings.";
$page_keywords = "admin, bookings management, reservations, booking status";

require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/admin-nav.php';

// Ensure user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: " . SITE_URL . "/user/login.php");
    exit;
}

$error = '';
$message = '';

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $bookingId = intval($_POST['booking_id']);
    $status = trim($_POST['status']);
    
    if (updateBookingStatus($bookingId, $status)) {
        $message = "Booking status updated successfully.";
    } else {
        $error = "Error updating booking status.";
    }
}

// Fetch all bookings
$bookings = getAllBookings();

// Group bookings by status
$groupedBookings = [
    'pending' => [],
    'confirmed' => [],
    'completed' => [],
    'cancelled' => []
];

foreach ($bookings as $booking) {
    $checkInDate = new DateTime($booking['check_in_date']);
    $checkOutDate = new DateTime($booking['check_out_date']);
    $today = new DateTime();
    
    if ($booking['status'] === 'cancelled') {
        $groupedBookings['cancelled'][] = $booking;
    } elseif ($booking['status'] === 'confirmed' && $checkOutDate < $today) {
        $groupedBookings['completed'][] = $booking;
    } elseif ($booking['status'] === 'confirmed') {
        $groupedBookings['confirmed'][] = $booking;
    } else {
        $groupedBookings['pending'][] = $booking;
    }
}
?>

<div class="container-fluid my-4">
    <h2>Manage Bookings</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <!-- Pending Bookings -->
    <div class="card mb-4">
        <div class="card-header bg-warning">
            <h5 class="card-title mb-0">Pending Bookings</h5>
        </div>
        <div class="card-body">
            <?php if (empty($groupedBookings['pending'])): ?>
                <p class="text-muted mb-0">No pending bookings.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service</th>
                                <th>User</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Guests</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupedBookings['pending'] as $booking): ?>
                                <tr>
                                    <td>#<?php echo $booking['id']; ?></td>
                                    <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking['user_email']); ?></small>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                                    <td><?php echo $booking['number_of_guests']; ?></td>
                                    <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td>
                                        <a href="booking-details.php?id=<?php echo $booking['id']; ?>" 
                                           class="btn btn-sm btn-info">View</a>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" name="update_status" class="btn btn-sm btn-success">
                                                Confirm
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" name="update_status" class="btn btn-sm btn-danger">
                                                Cancel
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Confirmed Bookings -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0">Confirmed Bookings</h5>
        </div>
        <div class="card-body">
            <?php if (empty($groupedBookings['confirmed'])): ?>
                <p class="text-muted mb-0">No confirmed bookings.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service</th>
                                <th>User</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Guests</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupedBookings['confirmed'] as $booking): ?>
                                <tr>
                                    <td>#<?php echo $booking['id']; ?></td>
                                    <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking['user_email']); ?></small>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                                    <td><?php echo $booking['number_of_guests']; ?></td>
                                    <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td>
                                        <a href="booking-details.php?id=<?php echo $booking['id']; ?>" 
                                           class="btn btn-sm btn-info">View</a>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" name="update_status" class="btn btn-sm btn-danger">
                                                Cancel
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Completed Bookings -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0">Completed Bookings</h5>
        </div>
        <div class="card-body">
            <?php if (empty($groupedBookings['completed'])): ?>
                <p class="text-muted mb-0">No completed bookings.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service</th>
                                <th>User</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Guests</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupedBookings['completed'] as $booking): ?>
                                <tr>
                                    <td>#<?php echo $booking['id']; ?></td>
                                    <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking['user_email']); ?></small>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                                    <td><?php echo $booking['number_of_guests']; ?></td>
                                    <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td>
                                        <a href="booking-details.php?id=<?php echo $booking['id']; ?>" 
                                           class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Cancelled Bookings -->
    <?php if (!empty($groupedBookings['cancelled'])): ?>
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">Cancelled Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service</th>
                                <th>User</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Guests</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupedBookings['cancelled'] as $booking): ?>
                                <tr>
                                    <td>#<?php echo $booking['id']; ?></td>
                                    <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking['user_email']); ?></small>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                                    <td><?php echo $booking['number_of_guests']; ?></td>
                                    <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td>
                                        <a href="booking-details.php?id=<?php echo $booking['id']; ?>" 
                                           class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 