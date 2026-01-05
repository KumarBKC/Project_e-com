<?php
require_once 'init.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$query = "SELECT * FROM products WHERE 1";
$params = [];

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}
if ($search) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Dynamically fetch all categories from the database
$catStmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Catalog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            min-height: 100vh;
        }
        .container {
            width: 95%;
            max-width: 1200px;
            margin: 40px auto 0 auto;
        }
        .catalog-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            gap: 12px;
            flex-wrap: wrap;
        }
        .catalog-left {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .dashboard-icon-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: #2563eb;
            border-radius: 50%;
            text-decoration: none;
            margin-right: 8px;
            box-shadow: 0 2px 8px rgba(37,99,235,0.08);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .dashboard-icon-link:hover {
            background: #1e40af;
            box-shadow: 0 4px 16px rgba(37,99,235,0.13);
        }
        .dashboard-icon-link span {
            font-size: 1.6rem;
            color: #fff;
        }
        .catalog-header h2 {
            color: #2563eb;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        .search-form {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
        }
        .search-form input[type="text"] {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1.5px solid #d1d5db;
            font-size: 1rem;
            background: #f9fafb;
            transition: border-color 0.3s;
        }
        .search-form input[type="text"]:focus {
            border-color: #2563eb;
            outline: none;
        }
        .search-form button {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .search-form button:hover {
            background: #1e40af;
        }
        .filter-form {
            background: #fff;
            border-radius: 8px;
            padding: 10px 18px;
            box-shadow: 0 2px 8px rgba(37,99,235,0.07);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-form label {
            font-weight: 600;
            color: #222;
            font-size: 1rem;
        }
        .filter-form select {
            padding: 7px 12px;
            border-radius: 5px;
            border: 1.5px solid #d1d5db;
            font-size: 1rem;
            background: #f9fafb;
            transition: border-color 0.3s;
        }
        .filter-form select:focus {
            border-color: #2563eb;
            outline: none;
        }
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
            justify-content: flex-start;
        }
        .product-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(37,99,235,0.10), 0 1.5px 6px rgba(0,0,0,0.04);
            width: 240px;
            padding: 0 0 18px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.2s;
            text-align: center;
            position: relative;
        }
        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 12px 12px 0 0;
            background: #f3f4f6;
        }
        .product-card .product-name {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 12px 0 4px 0;
            color: #222;
        }
        .product-card .product-category {
            font-size: 0.97rem;
            color: #2563eb;
            margin-bottom: 6px;
        }
        .product-card .product-price {
            font-size: 1.05rem;
            color: #16a34a;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .product-card .product-desc {
            font-size: 0.97rem;
            color: #555;
            margin-bottom: 0;
            padding: 0 10px;
        }
        @media (max-width: 900px) {
            .product-grid { justify-content: center; }
            .container { width: 99%; }
        }
        @media (max-width: 700px) {
            .catalog-header { flex-direction: column; align-items: stretch; gap: 12px; }
            .catalog-left { justify-content: flex-start; }
            .search-form { margin-left: 0; width: 100%; }
            .search-form input[type="text"] { width: 100%; }
        }
        @media (max-width: 600px) {
            .product-grid { flex-direction: column; gap: 18px; align-items: center; }
            .product-card { width: 98%; }
            .catalog-header { flex-direction: column; gap: 16px; }
            .catalog-left { flex-direction: column; align-items: flex-start; gap: 8px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="catalog-header">
        <div class="catalog-left">
            <a href="../dashboard.php" class="dashboard-icon-link" title="Go to Dashboard">
                <span>🏠</span>
            </a>
            <h2>Product Catalog</h2>
        </div>
        <form class="search-form" method="get" action="product_list.php">
            <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit">🔍 Search</button>
        </form>
        <form class="filter-form" method="get">
            <label for="category">Filter by Category:</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="">All</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $cat === $category ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="product-grid">
        <?php if (empty($products)): ?>
            <div style="width:100%;text-align:center;color:#888;font-size:1.2rem;padding:40px 0;">
                No products found.
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php if (!empty($product['image'])): ?>
                        <img src="../images/<?= htmlspecialchars($product['image']) ?>" alt="Product Image">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/240x150?text=No+Image" alt="Product Image">
                    <?php endif; ?>
                    <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                    <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
                    <div class="product-price">Rs <?= htmlspecialchars($product['price']) ?></div>
                    <div class="product-desc"><?= htmlspecialchars($product['description']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>