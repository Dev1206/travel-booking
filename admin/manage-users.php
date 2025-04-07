<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

$page_title = "Manage Users - Admin Dashboard - " . SITE_NAME;
$page_description = "Admin interface for managing user accounts and permissions.";
$page_keywords = "admin, user management, accounts, permissions";

require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/admin-nav.php';

// Ensure user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: " . SITE_URL . "/user/login.php");
    exit;
}

$db = Database::getInstance();
$pdo = $db->getConnection();

$message = '';
$error = '';

// Handle user status updates
if (isset($_POST['update_status']) && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        if ($stmt->execute([$isActive, $userId])) {
            $message = "User status updated successfully.";
        } else {
            $error = "Error updating user status.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch all users
try {
    $stmt = $pdo->query("SELECT id, first_name, last_name, email, phone, is_admin, is_active, 
                         created_at, (SELECT COUNT(*) FROM bookings WHERE user_id = users.id) as booking_count 
                         FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
    $users = [];
}
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Users</h2>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Bookings</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo $user['phone'] ? htmlspecialchars($user['phone']) : '-'; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $user['is_admin'] ? 'primary' : 'secondary'; ?>">
                                        <?php echo $user['is_admin'] ? 'Admin' : 'User'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['booking_count'] > 0): ?>
                                        <a href="view-bookings.php?user_id=<?php echo $user['id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo $user['booking_count']; ?> bookings
                                        </a>
                                    <?php else: ?>
                                        No bookings
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <form method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to <?php echo $user['is_active'] ? 'deactivate' : 'activate'; ?> this user?');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <input type="hidden" name="is_active" value="<?php echo $user['is_active'] ? '0' : '1'; ?>">
                                        <button type="submit" name="update_status" 
                                                class="btn btn-sm btn-<?php echo $user['is_active'] ? 'warning' : 'success'; ?>">
                                            <?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </form>
                                    
                                    <button type="button" class="btn btn-sm btn-info" 
                                            data-bs-toggle="modal" data-bs-target="#userDetailsModal<?php echo $user['id']; ?>">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- User Details Modal -->
                            <div class="modal fade" id="userDetailsModal<?php echo $user['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">User Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                            <p><strong>Phone:</strong> <?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'Not provided'; ?></p>
                                            <p><strong>Role:</strong> <?php echo $user['is_admin'] ? 'Administrator' : 'Regular User'; ?></p>
                                            <p><strong>Status:</strong> <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?></p>
                                            <p><strong>Total Bookings:</strong> <?php echo $user['booking_count']; ?></p>
                                            <p><strong>Joined:</strong> <?php echo date('F d, Y H:i', strtotime($user['created_at'])); ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 