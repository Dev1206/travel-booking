<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

// Set page metadata
$page_title = "Add New Service - Admin Dashboard - " . SITE_NAME;
$page_description = "Add a new travel service to the system from the admin dashboard.";
$page_keywords = "add service, new service, admin, service management";

// Include new header templates
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/admin-nav.php';

// Ensure user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: " . SITE_URL . "/user/login.php");
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $duration = trim($_POST['duration']);
    $available_seats = intval($_POST['available_seats']);

    // Validate inputs
    if (empty($name) || empty($description) || $price <= 0 || empty($category) || empty($location)) {
        $error = "Please fill in all required fields.";
    } else {
        // Handle image upload
        $image = '';
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
                    $image = 'assets/images/services/' . $fileName;
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Invalid image format. Allowed formats: JPG, JPEG, PNG, GIF";
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO services (name, description, price, category, location, duration, available_seats, image) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $category, $location, $duration, $available_seats, $image]);
                $message = "Service added successfully!";
                
                // Clear form data
                $_POST = array();
            } catch (PDOException $e) {
                $error = "Error adding service: " . $e->getMessage();
            }
        }
    }
}
?>

<h2>Add New Service</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="mb-4">
    <div class="mb-3">
        <label for="name" class="form-label">Service Name *</label>
        <input type="text" class="form-control" id="name" name="name" required 
               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description *</label>
        <textarea class="form-control" id="description" name="description" rows="4" required><?php 
            echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; 
        ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="price" class="form-label">Price (USD) *</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required 
                   value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label for="category" class="form-label">Category *</label>
            <select class="form-select" id="category" name="category" required>
                <option value="">Select Category</option>
                <option value="hotel" <?php echo (isset($_POST['category']) && $_POST['category'] === 'hotel') ? 'selected' : ''; ?>>Hotel</option>
                <option value="flight" <?php echo (isset($_POST['category']) && $_POST['category'] === 'flight') ? 'selected' : ''; ?>>Flight</option>
                <option value="tour" <?php echo (isset($_POST['category']) && $_POST['category'] === 'tour') ? 'selected' : ''; ?>>Tour</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="location" class="form-label">Location *</label>
            <input type="text" class="form-control" id="location" name="location" required 
                   value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label for="duration" class="form-label">Duration</label>
            <input type="text" class="form-control" id="duration" name="duration" placeholder="e.g., 3 days, 2 nights" 
                   value="<?php echo isset($_POST['duration']) ? htmlspecialchars($_POST['duration']) : ''; ?>">
        </div>
    </div>

    <div class="mb-3">
        <label for="available_seats" class="form-label">Available Seats/Rooms</label>
        <input type="number" class="form-control" id="available_seats" name="available_seats" min="0" 
               value="<?php echo isset($_POST['available_seats']) ? htmlspecialchars($_POST['available_seats']) : ''; ?>">
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Service Image</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary">Add Service</button>
    <a href="manage-services.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 