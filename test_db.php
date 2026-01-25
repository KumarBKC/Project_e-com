<?php
require 'db.php';

try {
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Database is connected successfully!";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
?>
