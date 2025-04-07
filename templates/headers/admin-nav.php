<?php
// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: " . SITE_URL . "/user/login.php");
    exit;
}
?>
<!-- Admin Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo SITE_URL; ?>/admin/admin-dashboard.php">
            <i class="fas fa-cogs me-2"></i>Admin Dashboard
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/manage-services.php">
                        <i class="fas fa-concierge-bell me-1"></i>Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/view-bookings.php">
                        <i class="fas fa-calendar-check me-1"></i>Bookings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/manage-users.php">
                        <i class="fas fa-users me-1"></i>Users
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="themeDropdown" role="button" 
                       data-bs-toggle="dropdown">
                        <i class="fas fa-palette"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php foreach ($themeManager->getAvailableThemes() as $themeId => $theme): ?>
                            <li>
                                <a class="dropdown-item theme-item <?php echo $themeId === $themeManager->getCurrentTheme() ? 'active' : ''; ?>" 
                                   href="#" data-theme="<?php echo $themeId; ?>">
                                    <span class="theme-preview" style="background-color: <?php echo $theme['preview']; ?>"></span>
                                    <span><?php echo $theme['name']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/pages/index.php">
                        <i class="fas fa-home me-1"></i>Main Site
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/user/logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Theme Switching JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeItems = document.querySelectorAll('.theme-item');
    
    themeItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const theme = this.dataset.theme;
            
            // Send AJAX request to change theme
            fetch('<?php echo SITE_URL; ?>/user/change-theme.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'theme=' + theme
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update theme stylesheet
                    document.getElementById('theme-style').href = '<?php echo SITE_URL; ?>' + data.themePath;
                    
                    // Update active state
                    themeItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});</script> 