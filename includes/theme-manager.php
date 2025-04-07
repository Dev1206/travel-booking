<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/db.php';

class ThemeManager {
    private static $instance = null;
    private $current_theme;
    private $available_themes = [
        'default-theme' => [
            'name' => 'Default Theme',
            'path' => '/assets/css/themes/default-theme.css',
            'icon' => 'fas fa-palette',
            'preview' => '#4e73df'
        ],
        'dark-theme' => [
            'name' => 'Dark Theme',
            'path' => '/assets/css/themes/dark-theme.css',
            'icon' => 'fas fa-moon',
            'preview' => '#6f42c1'
        ],
        'nature-theme' => [
            'name' => 'Nature Theme',
            'path' => '/assets/css/themes/nature-theme.css',
            'icon' => 'fas fa-leaf',
            'preview' => '#2d6a4f'
        ]
    ];

    private function __construct() {
        $this->current_theme = $_SESSION['user_theme'] ?? 'default-theme';
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new ThemeManager();
        }
        return self::$instance;
    }

    public function getCurrentTheme() {
        return $this->current_theme;
    }

    public function getCurrentThemePath() {
        return $this->available_themes[$this->current_theme]['path'];
    }

    public function getAvailableThemes() {
        return $this->available_themes;
    }

    public function setTheme($theme_id) {
        if (array_key_exists($theme_id, $this->available_themes)) {
            $this->current_theme = $theme_id;
            $_SESSION['user_theme'] = $theme_id;
            
            if (isset($_SESSION['user_id'])) {
                try {
                    $db = Database::getInstance();
                    $conn = $db->getConnection();
                    
                    $stmt = $conn->prepare("UPDATE users SET theme = ? WHERE id = ?");
                    $stmt->execute([$theme_id, $_SESSION['user_id']]);
                } catch (PDOException $e) {
                    // Log error silently
                }
            }
            return true;
        }
        return false;
    }

    public function loadUserTheme($user_id) {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            $stmt = $conn->prepare("SELECT theme FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $theme = $stmt->fetchColumn();
            
            if ($theme && array_key_exists($theme, $this->available_themes)) {
                $this->current_theme = $theme;
                $_SESSION['user_theme'] = $theme;
            }
        } catch (PDOException $e) {
            // Use default theme if there's an error
            $this->current_theme = 'default-theme';
            $_SESSION['user_theme'] = 'default-theme';
        }
    }
} 