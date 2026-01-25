<?php
require_once 'init.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$searchTerm = $_GET['q'] ?? '';

$query = "SELECT * FROM products WHERE name LIKE ? OR category LIKE ?";
$params = ['%' . $searchTerm . '%', '%' . $searchTerm . '%'];

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Products</title>
</head>
<body>
    <h2>Search Products</h2>
    <form method="get">
        <input type="text" name="q" placeholder="Search by name or category..." value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">Search</button>
    </form>

    <?php foreach ($products as $product): ?>
        <div>
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p><?= htmlspecialchars($product['category']) ?> | $<?= htmlspecialchars($product['price']) ?></p>
        </div>
    <?php endforeach; ?>
</body>
</html>
