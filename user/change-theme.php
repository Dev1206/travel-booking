<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/theme-manager.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get theme from POST data
$theme = $_POST['theme'] ?? null;

if (!$theme) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Theme not specified']);
    exit;
}

// Change theme
$themeManager = ThemeManager::getInstance();
$success = $themeManager->setTheme($theme);

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Theme updated successfully',
        'theme' => $theme,
        'themePath' => $themeManager->getCurrentThemePath()
    ]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid theme']);
} 