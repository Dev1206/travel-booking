<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

// Set page metadata
$page_title = "My Profile - " . SITE_NAME;
$page_description = "Manage your profile settings and preferences.";
$page_keywords = "profile, account settings, user preferences";

// Include header files
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/main-nav.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . SITE_URL . "/user/login.php");
    exit;
}

$success_message = '';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($first_name)) {
        $errors[] = "First name is required";
    }
    if (empty($last_name)) {
        $errors[] = "Last name is required";
    }
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Check if email is already taken by another user
    if (!empty($email)) {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $errors[] = "Email is already taken";
            }
        } catch (PDOException $e) {
            $errors[] = "An error occurred. Please try again later.";
        }
    }
    
    // Password change validation
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password";
        }
        if (empty($new_password)) {
            $errors[] = "New password is required";
        } elseif (strlen($new_password) < 8) {
            $errors[] = "New password must be at least 8 characters long";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }
        
        // Verify current password
        if (!empty($current_password)) {
            try {
                $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
                
                if (!$user || !password_verify($current_password, $user['password'])) {
                    $errors[] = "Current password is incorrect";
                }
            } catch (PDOException $e) {
                $errors[] = "An error occurred. Please try again later.";
            }
        }
    }
    
    // Update profile if no errors
    if (empty($errors)) {
        try {
            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?";
            $params = [$first_name, $last_name, $email, $phone, $address];
            
            if (!empty($new_password)) {
                $sql .= ", password = ?";
                $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $_SESSION['user_id'];
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            
            // Update session data
            $_SESSION['user_name'] = $first_name . ' ' . $last_name;
            $_SESSION['user_email'] = $email;
            
            $success_message = "Profile updated successfully";
        } catch (PDOException $e) {
            $errors[] = "An error occurred while updating your profile";
        }
    }
}

// Get current user data
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $errors[] = "An error occurred while loading your profile";
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">My Profile</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5>Change Password</h5>
                        <p class="text-muted small">Leave blank if you don't want to change your password</p>
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            <div class="form-text">Password must be at least 8 characters long</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 