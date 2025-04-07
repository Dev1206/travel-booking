<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/booking-functions.php';

// Set page metadata
$page_title = "My Bookings - " . SITE_NAME;
$page_description = "View and manage your travel bookings history.";
$page_keywords = "bookings, travel history, reservations, booking management";

// Include header files
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/main-nav.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . SITE_URL . "/user/login.php");
    exit;
}

$error = '';
$bookings = [];

// Fetch user's bookings
$bookings = getUserBookings($_SESSION['user_id']);

// Group bookings by status
$groupedBookings = [
    'upcoming' => [],
    'completed' => [],
    'cancelled' => []
];

foreach ($bookings as $booking) {
    $checkInDate = new DateTime($booking['check_in_date']);
    $checkOutDate = new DateTime($booking['check_out_date']);
    $today = new DateTime();
    
    if ($booking['status'] === 'cancelled') {
        $groupedBookings['cancelled'][] = $booking;
    } elseif ($checkOutDate < $today) {
        $groupedBookings['completed'][] = $booking;
    } else {
        $groupedBookings['upcoming'][] = $booking;
    }
}
?>

<div class="container my-4">
    <h2>My Bookings</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (empty($bookings)): ?>
        <div class="alert alert-info">
            <p class="mb-0">You don't have any bookings yet. 
                <a href="../pages/index.php">Browse our services</a> to make your first booking!</p>
        </div>
    <?php else: ?>
        <!-- Upcoming Bookings -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Upcoming Bookings</h5>
            </div>
            <div class="card-body">
                <?php if (empty($groupedBookings['upcoming'])): ?>
                    <p class="text-muted mb-0">No upcoming bookings.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Location</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Guests</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($groupedBookings['upcoming'] as $booking): ?>
                                    <tr>
                                        <td>
                                            <?php if ($booking['image_url']): ?>
                                                <div class="col-md-3">
                                                    <img src="<?php 
                                                        $imageUrl = $booking['image_url'];
                                                        echo (strpos($imageUrl, 'http') === 0) ? 
                                                            htmlspecialchars($imageUrl) : 
                                                            '../' . htmlspecialchars($imageUrl); 
                                                    ?>" 
                                                    alt="<?php echo htmlspecialchars($booking['service_name']); ?>"
                                                    class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                                                </div>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($booking['service_name']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                                        <td><?php echo $booking['number_of_guests']; ?></td>
                                        <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $booking['status'] === 'confirmed' ? 'success' : 
                                                    ($booking['status'] === 'pending' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="booking-confirm.php?id=<?php echo $booking['id']; ?>" 
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
        
        <!-- Completed Bookings -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
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
                                    <th>Service</th>
                                    <th>Location</th>
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
                                        <td>
                                            <?php if ($booking['image_url']): ?>
                                                <div class="col-md-3">
                                                    <img src="<?php 
                                                        $imageUrl = $booking['image_url'];
                                                        echo (strpos($imageUrl, 'http') === 0) ? 
                                                            htmlspecialchars($imageUrl) : 
                                                            '../' . htmlspecialchars($imageUrl); 
                                                    ?>" 
                                                    alt="<?php echo htmlspecialchars($booking['service_name']); ?>"
                                                    class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                                                </div>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($booking['service_name']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                                        <td><?php echo $booking['number_of_guests']; ?></td>
                                        <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                        <td>
                                            <a href="booking-confirm.php?id=<?php echo $booking['id']; ?>" 
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
                                    <th>Service</th>
                                    <th>Location</th>
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
                                        <td>
                                            <?php if ($booking['image_url']): ?>
                                                <div class="col-md-3">
                                                    <img src="<?php 
                                                        $imageUrl = $booking['image_url'];
                                                        echo (strpos($imageUrl, 'http') === 0) ? 
                                                            htmlspecialchars($imageUrl) : 
                                                            '../' . htmlspecialchars($imageUrl); 
                                                    ?>" 
                                                    alt="<?php echo htmlspecialchars($booking['service_name']); ?>"
                                                    class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                                                </div>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($booking['service_name']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                                        <td><?php echo $booking['number_of_guests']; ?></td>
                                        <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                        <td>
                                            <a href="booking-confirm.php?id=<?php echo $booking['id']; ?>" 
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
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 