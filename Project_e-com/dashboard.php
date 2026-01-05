<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
require 'db.php';

// Fetch distinct categories
$stmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch a few products for showcase
$showcaseStmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
$showcaseProducts = $showcaseStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dashboard</title>
<style>
  body, html {
    margin: 0; padding: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f0f2f5;
    min-height: 100vh;
  }
  .dashboard-layout {
    display: flex;
    min-height: 100vh;
  }
  .sidebar {
    width: 230px;
    background: #2563eb;
    color: #fff;
    display: flex;
    flex-direction: column;
    padding: 32px 0 0 0;
    min-height: 100vh;
    box-shadow: 2px 0 8px rgba(0,0,0,0.04);
  }
  .sidebar h2 {
    text-align: center;
    margin-bottom: 32px;
    font-size: 26px;
    letter-spacing: 1px;
    font-weight: 700;
  }
  .sidebar a {
    color: #fff;
    text-decoration: none;
    padding: 14px 32px;
    display: block;
    font-size: 17px;
    border-left: 4px solid transparent;
    transition: background 0.2s, border-color 0.2s;
    margin-bottom: 4px;
  }
  .sidebar a.active, .sidebar a:hover {
    background: #1e40af;
    border-left: 4px solid #fff;
  }
  .sidebar .logout-btn {
    margin-top: auto;
    background: #ef4444;
    color: #fff;
    border-radius: 0;
    border: none;
    text-align: left;
    font-weight: bold;
    transition: background 0.2s;
  }
  .sidebar .logout-btn:hover {
    background: #b91c1c;
  }
  .main-content {
    flex: 1;
    padding: 40px 5vw 40px 5vw;
    background: #f0f2f5;
    min-width: 0;
  }
  .welcome-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
    flex-wrap: wrap;
  }
  .welcome-header h1 {
    color: #2563eb;
    font-size: 2.2rem;
    margin: 0;
    font-weight: 700;
  }
  .welcome-header .subtitle {
    color: #555;
    font-size: 1.1rem;
    margin-left: 12px;
    font-weight: 400;
  }
  .stats-row {
    display: flex;
    gap: 24px;
    margin-bottom: 36px;
    flex-wrap: wrap;
  }
  .stat-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 24px 32px;
    flex: 1;
    min-width: 180px;
    text-align: center;
  }
  .stat-card h3 {
    margin: 0 0 8px 0;
    color: #2563eb;
    font-size: 1.2rem;
    font-weight: 600;
  }
  .stat-card .stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #222;
  }
  .categories-section {
    margin-bottom: 40px;
  }
  .categories-section h2 {
    font-size: 1.3rem;
    color: #333;
    margin-bottom: 16px;
    font-weight: 600;
  }
  .category-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }
  .category-tag {
    background: #e0e7ff;
    color: #3730a3;
    padding: 9px 22px;
    border-radius: 20px;
    font-size: 15px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s;
    box-shadow: 0 1px 4px rgba(55,48,163,0.06);
  }
  .category-tag:hover {
    background: #c7d2fe;
  }
  .showcase-section {
    margin-bottom: 40px;
  }
  .showcase-section h2 {
    font-size: 1.3rem;
    color: #333;
    margin-bottom: 16px;
    font-weight: 600;
  }
  .showcase-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 22px;
  }
  .showcase-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    width: 220px;
    padding: 0 0 18px 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: box-shadow 0.2s;
    text-align: center;
  }
  .showcase-card img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
    background: #f3f4f6;
  }
  .showcase-card .product-name {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 12px 0 4px 0;
    color: #222;
  }
  .showcase-card .product-category {
    font-size: 0.95rem;
    color: #2563eb;
    margin-bottom: 6px;
  }
  .showcase-card .product-price {
    font-size: 1.05rem;
    color: #16a34a;
    font-weight: 600;
    margin-bottom: 4px;
  }
  .showcase-card .product-desc {
    font-size: 0.97rem;
    color: #555;
    margin-bottom: 0;
    padding: 0 10px;
  }
  @media (max-width: 900px) {
    .dashboard-layout { flex-direction: column; }
    .sidebar { width: 100%; flex-direction: row; min-height: unset; padding: 0; }
    .sidebar h2 { display: none; }
    .sidebar a, .sidebar .logout-btn { flex: 1; text-align: center; padding: 14px 0; }
    .main-content { padding: 30px 2vw; }
    .showcase-grid { justify-content: center; }
  }
  @media (max-width: 600px) {
    .main-content { padding: 18px 2vw; }
    .stats-row { flex-direction: column; gap: 14px; }
    .showcase-grid { flex-direction: column; gap: 16px; }
    .showcase-card { width: 100%; }
  }
</style>
</head>
<body>
<div class="dashboard-layout">
  <nav class="sidebar">
    <h2>E-Com Admin</h2>
    <a href="dashboard.php" class="active">🏠 Dashboard</a>
    <a href="public/product_list.php">📃 Product List</a> <!-- Changed icon to a list -->
    <a href="public/cart.php">🛒 Cart</a>
    <a href="public/add_product.php">➕ Add Product</a>
    <a href="orders.php">📦 Orders</a>
    <a href="profile.php">👤 Profile</a>
    <a class="logout-btn" href="logout.php">Logout</a>
  </nav>
  <main class="main-content">
    <div class="welcome-header">
      <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
      <span class="subtitle">Manage your store, products, and orders from here.</span>
    </div>

    <div class="stats-row">
      <div class="stat-card">
        <h3>Total Products</h3>
        <div class="stat-value">
          <?php
            $count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
            echo $count;
          ?>
        </div>
      </div>
      <div class="stat-card">
        <h3>Categories</h3>
        <div class="stat-value"><?php echo count($categories); ?></div>
      </div>
      <div class="stat-card">
        <h3>Orders</h3>
        <div class="stat-value">
          <?php
            // If you have an orders table, otherwise set to 0
            try {
              $orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
              echo $orderCount;
            } catch (Exception $e) {
              echo "0";
            }
          ?>
        </div>
      </div>
    </div>

    <div class="categories-section">
      <h2>Product Categories</h2>
      <div class="category-tags">
        <?php foreach ($categories as $cat): ?>
          <a class="category-tag" href="product_list.php?category=<?= urlencode($cat) ?>">
            <?= htmlspecialchars($cat) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="showcase-section">
      <h2>Latest Products</h2>
      <div class="showcase-grid">
        <?php foreach ($showcaseProducts as $product): ?>
          <div class="showcase-card">
            <?php if (!empty($product['image'])): ?>
             <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="Product Image" />
              <?php else: ?>
                 <img src="https://via.placeholder.com/220x140?text=No+Image" alt="No Image" />
              <?php endif; ?>
            <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
            <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
            <div class="product-price">Rs <?= htmlspecialchars($product['price']) ?></div>
            <div class="product-desc"><?= htmlspecialchars($product['description']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</div>
</body>
</html>