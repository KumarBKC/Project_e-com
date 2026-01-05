<?php
/**
 * Dashboard Page
 */

require_once 'init.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Include dashboard from views
include VIEWS_PATH . '/dashboard.php';
?>
