<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

// Set page metadata
$page_title = "Edit Service - Admin Dashboard - " . SITE_NAME;
$page_description = "Edit existing travel service details in the admin dashboard.";
$page_keywords = "edit service, admin, service management, update service";

// Include new header templates
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/admin-nav.php';

// Ensure user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: " . SITE_URL . "/user/login.php");
    exit;
}

// Initialize database connection
$db = Database::getInstance();
$pdo = $db->getConnection();

$message = '';
$error = '';
$service = null;
$categories = [];

// Get service ID from URL
$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$service_id) {
    header("Location: manage-services.php");
    exit;
}

// Fetch categories
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching categories: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category']);
    $location = trim($_POST['location']);

    // Validate inputs
    if (empty($name) || empty($description) || $price <= 0 || empty($category_id) || empty($location)) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            // Handle image upload if a new image is provided
            $image_url = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../assets/images/services/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $fileName = uniqid() . '.' . $fileExtension;
                    $targetPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $image_url = 'assets/images/services/' . $fileName;
                        
                        // Delete old image if it exists
                        $stmt = $pdo->prepare("SELECT image_url FROM services WHERE id = ?");
                        $stmt->execute([$service_id]);
                        $oldService = $stmt->fetch();
                        
                        if ($oldService && $oldService['image_url']) {
                            $oldImagePath = '../' . $oldService['image_url'];
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                    } else {
                        $error = "Failed to upload image.";
                    }
                } else {
                    $error = "Invalid image format. Allowed formats: JPG, JPEG, PNG, GIF";
                }
            }

            if (empty($error)) {
                // Update service
                if ($image_url) {
                    $stmt = $pdo->prepare("UPDATE services SET name = ?, description = ?, price = ?, 
                                         category_id = ?, location = ?, image_url = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $price, $category_id, $location, $image_url, $service_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE services SET name = ?, description = ?, price = ?, 
                                         category_id = ?, location = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $price, $category_id, $location, $service_id]);
                }
                
                $message = "Service updated successfully!";
            }
        } catch (PDOException $e) {
            $error = "Error updating service: " . $e->getMessage();
        }
    }
}

// Fetch service data
try {
    $stmt = $pdo->prepare("SELECT s.*, c.name as category_name 
                          FROM services s 
                          JOIN categories c ON s.category_id = c.id 
                          WHERE s.id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch();
    
    if (!$service) {
        header("Location: manage-services.php");
        exit;
    }
} catch (PDOException $e) {
    $error = "Error fetching service: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Service</h2>
    <a href="manage-services.php" class="btn btn-secondary">Back to Services</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($service): ?>
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label for="name" class="form-label">Service Name *</label>
            <input type="text" class="form-control" id="name" name="name" required 
                   value="<?php echo htmlspecialchars($service['name'] ?? ''); ?>">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description *</label>
            <textarea class="form-control" id="description" name="description" rows="4" required><?php 
                echo htmlspecialchars($service['description'] ?? ''); 
            ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Price (USD) *</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required 
                       value="<?php echo htmlspecialchars($service['price'] ?? ''); ?>">
            </div>

            <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category *</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($service['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="location" class="form-label">Location *</label>
                <input type="text" class="form-control" id="location" name="location" required 
                       value="<?php echo htmlspecialchars($service['location'] ?? ''); ?>">
            </div>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Service Image</label>
            <?php if (isset($service['image_url']) && $service['image_url']): ?>
                <div class="mb-2">
                    <img src="<?php 
                        echo strpos($service['image_url'], 'http') === 0 ? 
                            htmlspecialchars($service['image_url']) : 
                            '../' . htmlspecialchars($service['image_url']); 
                    ?>" 
                         alt="Current service image" class="img-thumbnail" style="max-height: 200px;">
                </div>
            <?php endif; ?>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <small class="form-text text-muted">Leave empty to keep the current image</small>
        </div>

        <button type="submit" class="btn btn-primary">Update Service</button>
        <a href="manage-services.php" class="btn btn-secondary">Cancel</a>
    </form>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>