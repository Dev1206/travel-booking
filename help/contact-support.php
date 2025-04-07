<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';

// Set page metadata
$page_title = "Contact Support - Help Center - " . SITE_NAME;
$page_description = "Get in touch with our support team for assistance with your travel bookings.";
$page_keywords = "contact support, customer service, help, assistance, travel support";

// Initialize variables
$messageSent = false;
$errorMessage = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $errorMessage = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } else {
        // In a real scenario, we would send the email here
        // For demonstration, we'll simulate a successful submission
        $messageSent = true;
        
        // Log the support request
        error_log("Support request from {$name} ({$email}): {$subject}");
    }
}

// Include new header templates
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/main-nav.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $priority = trim($_POST['priority'] ?? 'normal');

    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // TODO: Implement ticket creation in database
        // For now, just send an email
        $to = SITE_EMAIL;
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "X-Priority: " . ($priority === 'urgent' ? "1" : "3") . "\r\n";
        
        $email_message = "New Support Ticket\n\n";
        $email_message .= "Name: $name\n";
        $email_message .= "Email: $email\n";
        $email_message .= "Priority: $priority\n";
        $email_message .= "Subject: $subject\n\n";
        $email_message .= "Message:\n$message";

        if (mail($to, "Support Request: $subject", $email_message, $headers)) {
            $success = "Your support request has been submitted successfully. We'll get back to you soon.";
        } else {
            $error = "There was a problem submitting your request. Please try again later.";
        }
    }
}

?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="help-home.php">Help Center</a></li>
            <li class="breadcrumb-item active">Contact Support</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-4">Contact Support</h1>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" class="form-control" id="subject" name="subject" required
                                   value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="normal" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'normal') ? 'selected' : ''; ?>>Normal</option>
                                <option value="urgent" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'urgent') ? 'selected' : ''; ?>>Urgent</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="h5 mb-3">Before Contacting Support</h3>
                    <p>Please check if your question has already been answered in our:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none">
                                <i class="fas fa-question-circle"></i> Frequently Asked Questions
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="how-to-book.php" class="text-decoration-none">
                                <i class="fas fa-book"></i> Booking Guide
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="h5 mb-3">Support Hours</h3>
                    <p class="mb-2"><strong>Monday - Friday:</strong><br>9:00 AM - 6:00 PM</p>
                    <p class="mb-2"><strong>Saturday - Sunday:</strong><br>10:00 AM - 4:00 PM</p>
                    <hr>
                    <p class="mb-0"><small>For urgent matters outside of these hours, please select "Urgent" priority in your support request.</small></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 