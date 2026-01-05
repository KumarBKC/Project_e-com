<?php
/**
 * Common Helper Functions
 */

/**
 * Escape HTML output to prevent XSS
 */
function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to another page
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Set flash message (for one-time display)
 */
function setFlash($message, $type = 'info') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 */
function isValidPassword($password) {
    return strlen($password) >= PASSWORD_MIN_LENGTH;
}

/**
 * Format price as currency
 */
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

/**
 * Sanitize filename for upload
 */
function sanitizeFilename($filename) {
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file is allowed upload type
 */
function isAllowedFile($filename, $allowed = ['jpg', 'jpeg', 'png', 'gif']) {
    return in_array(getFileExtension($filename), $allowed);
}

/**
 * Log error to file
 */
function logError($message) {
    $log_file = BASE_PATH . '/logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    error_log("[{$timestamp}] {$message}\n", 3, $log_file);
}

/**
 * Log activity
 */
function logActivity($user_id, $action) {
    // Optional: Log user activities for security/audit purposes
    logError("User {$user_id}: {$action}");
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

?>
