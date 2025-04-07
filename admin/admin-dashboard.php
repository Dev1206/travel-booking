<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

// Set page metadata
$page_title = "Admin Dashboard - " . SITE_NAME;
$page_description = "Manage your travel booking website.";
$page_keywords = "admin, dashboard, management, bookings, users";

// Include new header templates
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/admin-nav.php';

// Ensure user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: " . SITE_URL . "/user/login.php");
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Get total users
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE is_admin = 0");
    $totalUsers = $stmt->fetchColumn();

    // Get total bookings
    $stmt = $conn->query("SELECT COUNT(*) FROM bookings");
    $totalBookings = $stmt->fetchColumn();

    // Get total revenue
    $stmt = $conn->query("SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE status = 'confirmed'");
    $totalRevenue = $stmt->fetchColumn();

    // Get recent bookings
    $stmt = $conn->query("
        SELECT b.*, u.first_name, u.last_name, s.name as service_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN services s ON b.service_id = s.id
        ORDER BY b.created_at DESC
        LIMIT 5
    ");
    $recentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error in admin dashboard: " . $e->getMessage());
    $error = "An error occurred while loading the dashboard.";
}
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2>Dashboard</h2>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2 class="display-4"><?php echo number_format($totalUsers); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Bookings</h5>
                    <h2 class="display-4"><?php echo number_format($totalBookings); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <h2 class="display-4">$<?php echo number_format($totalRevenue, 2); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Bookings</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentBookings as $booking): ?>
                                        <tr>
                                            <td>#<?php echo $booking['id']; ?></td>
                                            <td><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
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
                                                <a href="booking-details.php?id=<?php echo $booking['id']; ?>" 
                                                   class="btn btn-sm btn-info">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (empty($recentBookings)): ?>
                            <p class="text-muted mb-0">No recent bookings found.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 