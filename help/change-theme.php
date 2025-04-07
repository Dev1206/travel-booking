<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';

// Set page metadata
$page_title = "Theme Customization - Help Center - " . SITE_NAME;
$page_description = "Learn how to customize the appearance of the website by changing themes.";
$page_keywords = "theme customization, website appearance, color themes, dark mode, light mode";

// Include new header templates
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/main-nav.php';

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/theme-manager.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get theme manager instance for displaying available themes
$themeManager = ThemeManager::getInstance();
$availableThemes = $themeManager->getAvailableThemes();
$currentTheme = $themeManager->getCurrentTheme();

// Old header template reference removed
?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="help-home.php">Help Center</a></li>
            <li class="breadcrumb-item active">Theme Customization</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-4">How to Customize Your Theme</h1>
            
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i> Theme customization is available to all registered users. You must be logged in to change your theme preference.
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Available Themes</h2>
                    <p>Our website offers the following themes to customize your browsing experience:</p>
                    
                    <div class="row">
                        <?php foreach ($availableThemes as $themeId => $theme): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="theme-preview mb-3 p-3 border rounded" style="background-color: <?php echo $theme['preview']; ?>;">
                                        <i class="<?php echo $theme['icon']; ?> fa-3x text-white"></i>
                                    </div>
                                    <h5 class="card-title"><?php echo $theme['name']; ?></h5>
                                    <span class="badge <?php echo ($themeId === $currentTheme) ? 'bg-primary' : 'bg-secondary'; ?> mb-2">
                                        <?php echo ($themeId === $currentTheme) ? 'Current Theme' : 'Available'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="alert alert-secondary mt-3 p-2 small">
                        <strong>Note:</strong> Each theme changes the color scheme and styling of the website while maintaining all functionality.
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">How to Change Your Theme</h2>
                    <p>Changing your theme is quick and easy:</p>
                    
                    <div class="mb-4">
                        <h5 class="h6">Method 1: Using the Theme Dropdown</h5>
                        <ol>
                            <li>Make sure you are logged in to your account</li>
                            <li>Look for the palette icon <i class="fas fa-palette"></i> or "Theme" dropdown in the navigation bar</li>
                            <li>Click on the dropdown to see available themes</li>
                            <li>Select your preferred theme from the list</li>
                            <li>The theme will be applied immediately without page refresh</li>
                        </ol>
                    </div>
                    
                    <div class="mb-2">
                        <h5 class="h6">Method 2: From User Profile</h5>
                        <ol>
                            <li>Go to your profile page by clicking on your username in the navigation bar</li>
                            <li>Navigate to the "Preferences" or "Appearance" section</li>
                            <li>Select your preferred theme from the options</li>
                            <li>Click "Save Changes" to apply your new theme</li>
                        </ol>
                    </div>
                    
                    <div class="alert alert-success p-2 small">
                        <strong>Tip:</strong> Your theme preference is saved to your account and will persist across different devices and browsing sessions.
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Theme Features</h2>
                    <p>Our themes offer various visual styles with these features:</p>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Theme</th>
                                    <th>Style</th>
                                    <th>Best For</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Default Theme</td>
                                    <td>Clean, professional blue</td>
                                    <td>General browsing, business travel</td>
                                </tr>
                                <tr>
                                    <td>Dark Theme</td>
                                    <td>Dark mode with purple accents</td>
                                    <td>Night-time browsing, reduced eye strain</td>
                                </tr>
                                <tr>
                                    <td>Nature Theme</td>
                                    <td>Green, nature-inspired colors</td>
                                    <td>Eco-tourism, nature retreats</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-warning p-2 small mt-3">
                        <strong>Note:</strong> Some third-party content may not reflect your chosen theme's color scheme.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="h5 mb-3">FAQ: Theme Customization</h3>
                    <div class="accordion" id="themeFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Will changing my theme affect my bookings?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#themeFAQ">
                                <div class="accordion-body">
                                    No, changing your theme is purely a visual preference and won't affect any of your existing bookings or account information.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Can I create my own custom theme?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#themeFAQ">
                                <div class="accordion-body">
                                    Currently, we offer a selection of pre-designed themes. Custom theme creation is not available, but we regularly add new theme options.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    My theme keeps resetting, what should I do?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#themeFAQ">
                                <div class="accordion-body">
                                    Make sure you are logged in when changing themes. If the issue persists, try clearing your browser cache or contact support for assistance.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="h5 mb-3">Need More Help?</h3>
                    <p>If you're having trouble with theme customization:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="contact-support.php" class="text-decoration-none">
                                <i class="fas fa-headset me-2"></i> Contact Support
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="../user/profile.php" class="text-decoration-none">
                                <i class="fas fa-user me-2"></i> Go to Your Profile
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 