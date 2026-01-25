<?php
/**
 * Application Configuration
 * 
 * Centralized configuration for database and application settings.
 * Sensitive credentials should be moved to environment variables in production.
 */

// Define base path constants
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('APP_PATH', BASE_PATH . '/app');
define('SRC_PATH', BASE_PATH . '/src');
define('VIEWS_PATH', BASE_PATH . '/views');
define('INCLUDES_PATH', BASE_PATH . '/includes');

// For Windows compatibility
if (!defined('DIRECTORY_SEPARATOR')) {
    define('DS', DIRECTORY_SEPARATOR);
} else {
    define('DS', DIRECTORY_SEPARATOR);
}

// Database Configuration
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'usersystemdb');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'E-Commerce Platform');
define('APP_URL', 'http://localhost/Project_e-com');
define('UPLOAD_DIR', PUBLIC_PATH . '/uploads');
define('IMAGES_DIR', PUBLIC_PATH . '/images');

// Security Settings
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('PASSWORD_MIN_LENGTH', 8);

// Error Handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Never display errors to users in production
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/error.log');

// Create logs directory if it doesn't exist
$logs_dir = BASE_PATH . '/logs';
if (!is_dir($logs_dir)) {
    mkdir($logs_dir, 0755, true);
}

?>
