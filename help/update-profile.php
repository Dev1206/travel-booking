<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';

// Set page metadata
$page_title = "Update Profile Guide - Help Center - " . SITE_NAME;
$page_description = "Learn how to update your profile information and account settings.";
$page_keywords = "profile update, account settings, user profile, personal information";

// Include new header templates
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/main-nav.php';

require_once __DIR__ . '/../includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="help-home.php">Help Center</a></li>
            <li class="breadcrumb-item active">Profile Management Guide</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-4">How to Manage Your Profile</h1>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Accessing Your Profile</h2>
                    <p>To access your profile settings:</p>
                    <ol>
                        <li>Click on your username in the top right corner</li>
                        <li>Select "Profile" from the dropdown menu</li>
                        <li>You'll be taken to your profile management page</li>
                    </ol>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Note: You must be logged in to access your profile settings.
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Updating Personal Information</h2>
                    <p>You can update the following information:</p>
                    <ul>
                        <li>Profile Picture</li>
                        <li>Full Name</li>
                        <li>Email Address</li>
                        <li>Phone Number</li>
                        <li>Address</li>
                    </ul>
                    <p>To update your information:</p>
                    <ol>
                        <li>Click the "Edit Profile" button</li>
                        <li>Make your desired changes</li>
                        <li>Click "Save Changes" to update your profile</li>
                    </ol>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Important: When changing your email address, you may need to verify the new email.
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Changing Your Password</h2>
                    <p>To change your password:</p>
                    <ol>
                        <li>Go to the "Security" section in your profile</li>
                        <li>Click "Change Password"</li>
                        <li>Enter your current password</li>
                        <li>Enter and confirm your new password</li>
                        <li>Click "Update Password"</li>
                    </ol>
                    <div class="alert alert-info">
                        <i class="fas fa-shield-alt"></i> Password Requirements:
                        <ul class="mb-0">
                            <li>At least 8 characters long</li>
                            <li>Contains at least one uppercase letter</li>
                            <li>Contains at least one number</li>
                            <li>Contains at least one special character</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Privacy Settings</h2>
                    <p>Manage your privacy preferences:</p>
                    <ul>
                        <li>Control what information is visible to other users</li>
                        <li>Manage email notification preferences</li>
                        <li>Set communication preferences</li>
                    </ul>
                    <div class="alert alert-success">
                        <i class="fas fa-lock"></i> Your privacy is important to us. We never share your personal information without your consent.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="h5 mb-3">Need More Help?</h3>
                    <p>If you're having trouble with your profile, try these resources:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="contact-support.php" class="text-decoration-none">
                                <i class="fas fa-headset"></i> Contact Support
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none">
                                <i class="fas fa-question-circle"></i> FAQ
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="h5 mb-3">Related Articles</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="change-theme.php" class="text-decoration-none">
                                <i class="fas fa-paint-brush"></i> Customizing Your Theme
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none">
                                <i class="fas fa-bell"></i> Managing Notifications
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 