<?php
// Close the previous body tag from base-header.php
?>
</body>
</html>
<?php
// Start new content
?>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo SITE_URL; ?>/pages/index.php">
            <i class="fas fa-plane-departure me-2"></i><?php echo SITE_NAME; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/pages/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/pages/hotels.php">Hotels</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/pages/flights.php">Flights</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/pages/tours.php">Tours</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/help/help-home.php">Help</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/user/booking-history.php">
                            <i class="fas fa-history"></i> My Bookings
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="themeDropdown" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-palette"></i> Theme
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php 
                            $availableThemes = $themeManager->getAvailableThemes();
                            foreach ($availableThemes as $themeId => $theme): 
                            ?>
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> 
                            <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Account'; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/profile.php">Profile</a></li>
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/admin-dashboard.php">Admin Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/user/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/user/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Theme Switching JavaScript -->
<?php if (isset($_SESSION['user_id'])): ?>
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
});
</script>
<?php endif; ?> 