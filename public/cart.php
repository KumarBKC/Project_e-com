<?php
/**
 * Cart Page
 */

require_once 'init.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// Handle cart actions (add, remove, update quantity)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? '';
    $product_id = $_POST["product_id"] ?? '';

    if ($action === "add" && $product_id) {
        $quantity = intval($_POST["quantity"] ?? 1);
        $_SESSION["cart"][$product_id] = ($SESSION["cart"][$product_id] ?? 0) + $quantity;
    } elseif ($action === "remove" && $product_id) {
        unset($_SESSION["cart"][$product_id]);
    } elseif ($action === "update" && $product_id) {
        $quantity = intval($_POST["quantity"] ?? 0);
        if ($quantity <= 0) {
            unset($_SESSION["cart"][$product_id]);
        } else {
            $_SESSION["cart"][$product_id] = $quantity;
        }
    }

    header("Location: cart.php");
    exit;
}

// Include cart view
include VIEWS_PATH . '/cart.php';
?>
