<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize theme manager
require_once __DIR__ . '/../../includes/theme-manager.php';
$themeManager = ThemeManager::getInstance();
if (isset($_SESSION['user_id'])) {
    $themeManager->loadUserTheme($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : SITE_NAME; ?></title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : 'Book your perfect travel experience with ' . SITE_NAME; ?>">
    <meta name="keywords" content="travel, booking, hotels, flights, tours, <?php echo isset($page_keywords) ? htmlspecialchars($page_keywords) : ''; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/favicon.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <!-- Theme CSS -->
    <link id="theme-style" href="<?php echo SITE_URL . $themeManager->getCurrentThemePath(); ?>" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
    .theme-preview {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
        border: 2px solid transparent;
        transition: transform 0.2s ease;
    }
    
    .theme-preview:hover {
        transform: scale(1.2);
    }
    
    .theme-item.active .theme-preview {
        border-color: var(--primary-color);
    }
    
    .theme-item {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        transition: background-color 0.2s ease;
    }
    
    .theme-item:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    /* Common transitions */
    body {
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .card, .navbar, .btn {
        transition: all 0.3s ease;
    }
    </style>
</head>
<body>
    <?php
    // Display flash messages if any
    if (isset($_SESSION['flash_message'])) {
        echo '<div class="alert alert-' . $_SESSION['flash_type'] . ' alert-dismissible fade show m-3" role="alert">';
        echo htmlspecialchars($_SESSION['flash_message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
    ?>
</body>
</html> 