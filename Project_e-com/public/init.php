<?php
/**
 * Application Bootstrap
 * 
 * This file loads all required configuration and classes.
 * Include this at the top of every public PHP file.
 */

// Load configuration
require_once dirname(__DIR__) . '/app/config.php';
require_once dirname(__DIR__) . '/app/Database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_set_cookie_params([
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']),
        'samesite' => 'Strict'
    ]);
}

// Set security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

?>
