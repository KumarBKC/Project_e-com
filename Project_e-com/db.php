<?php
// Database configuration
$host = '127.0.0.1';
$dbname = 'usersystemdb';
$username = 'root';
$password = '';

try {
    // Set Data Source Name (DSN)
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    // Create PDO instance with options
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Return associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false                    // Use real prepared statements
    ]);
} catch (PDOException $e) {
    // Stop execution and show error
    exit("❌ Database connection failed: " . $e->getMessage());
}
?>
