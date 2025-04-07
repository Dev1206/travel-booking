<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

$page_title = "Manage Services - Admin Dashboard - " . SITE_NAME;
$page_description = "Admin interface for managing travel services and packages.";
$page_keywords = "admin, service management, travel packages, hotels, flights, tours";

require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/admin-nav.php';

// Ensure user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: " . SITE_URL . "/user/login.php");
    exit;
}

$message = '';
$error = '';

// Handle service deletion
if (isset($_POST['delete_service']) && isset($_POST['service_id'])) {
    $service_id = intval($_POST['service_id']);
    
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Get the image path before deleting the service
        $stmt = $conn->prepare("SELECT image_url FROM services WHERE id = ?");
        $stmt->execute([$service_id]);
        $service = $stmt->fetch();

        // Delete the service
        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$service_id]);

        // Delete the image file if it exists
        if ($service && $service['image_url']) {
            $imagePath = '../' . $service['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $message = "Service deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting service: " . $e->getMessage();
    }
}

// Fetch all services
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->query("SELECT s.*, c.name as category_name 
                         FROM services s 
                         JOIN categories c ON s.category_id = c.id 
                         ORDER BY c.name, s.name");
    $services = $stmt->fetchAll();

    // Group services by category
    $groupedServices = [];
    foreach ($services as $service) {
        $groupedServices[$service['category_name']][] = $service;
    }
} catch (PDOException $e) {
    $error = "Error fetching services: " . $e->getMessage();
    $groupedServices = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Services</h2>
    <a href="add-service.php" class="btn btn-primary">Add New Service</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php foreach ($groupedServices as $category => $categoryServices): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="h5 mb-0 text-capitalize"><?php echo htmlspecialchars($category); ?></h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Available</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categoryServices as $service): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($service['name']); ?></td>
                                <td><?php echo htmlspecialchars($service['location']); ?></td>
                                <td>$<?php echo number_format($service['price'], 2); ?></td>
                                <td><?php echo $service['is_available'] ? 'Yes' : 'No'; ?></td>
                                <td>
                                    <a href="edit-service.php?id=<?php echo $service['id']; ?>" 
                                       class="btn btn-sm btn-primary">Edit</a>
                                    <form method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this service?');">
                                        <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                        <button type="submit" name="delete_service" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 