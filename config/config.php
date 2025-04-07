<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'dev12');
define('DB_NAME', 'travel_booking');

// Site configuration
define('SITE_NAME', 'Travel Booking');
define('SITE_URL', 'http://localhost:8000');
define('SITE_EMAIL', 'noreply@travelbooking.com');
define('ADMIN_EMAIL', 'admin@travelbooking.com');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);

// Timezone
date_default_timezone_set('UTC');

// Session configuration
define('SESSION_NAME', 'travel_booking_session');
define('SESSION_LIFETIME', 3600); // 1 hour

// Upload configuration
define('UPLOAD_DIR', __DIR__ . '/../assets/images/');
define('MAX_FILE_SIZE', 5242880); // 5MB 