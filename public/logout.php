<?php
/**
 * Logout Page
 */

require_once 'init.php';

// Destroy session
session_destroy();

// Redirect to login
header("Location: login.php");
exit;
?>
