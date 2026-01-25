<?php
require_once 'init.php';

// Check if user is logged in and is admin
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Check admin status (adjust based on your users table)
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();

if ($user['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Fetch categories for the dropdown
$stmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category_select']) === '__new__' ? trim($_POST['category_new']) : trim($_POST['category_select']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $imageFile = $_FILES['image'];

    // Image upload handling
    $imageName = '';
    if ($imageFile['tmp_name']) {
        $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $imageName = uniqid('prod_', true) . '.' . $ext;
            move_uploaded_file($imageFile['tmp_name'], __DIR__ . '/../images/' . $imageName);
        } else {
            $message = "Invalid image format. Allowed: jpg, jpeg, png, gif.";
        }
    }

    if ($name && $category && $price && !$message) {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, price, description, image, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $category, $price, $description, $imageName]);
        $message = "✅ Product added successfully!";
    } elseif (!$message) {
        $message = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Product</title>
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
    padding: 0;
    background: #f0f2f5;
    min-width: 0;
    display: flex;
    justify-content: center;
    align-items: flex-start;
  }
  .add-product-form {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 6px 32px rgba(37,99,235,0.10), 0 1.5px 6px rgba(0,0,0,0.04);
    padding: 44px 48px 36px 48px;
    width: 100%;
    max-width: 480px;
    margin-top: 48px;
    margin-bottom: 48px;
    display: flex;
    flex-direction: column;
    gap: 18px;
    animation: fadeIn 0.7s;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px);}
    to { opacity: 1; transform: translateY(0);}
  }
  .add-product-form h2 {
    color: #2563eb;
    margin-bottom: 18px;
    font-size: 2rem;
    text-align: center;
    font-weight: 800;
    letter-spacing: 1px;
  }
  .form-group {
    margin-bottom: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  label {
    font-weight: 600;
    color: #222;
    font-size: 1.05rem;
    margin-bottom: 2px;
  }
  input[type="text"], input[type="number"], textarea, select {
    width: 100%;
    padding: 12px 14px;
    border: 1.5px solid #d1d5db;
    border-radius: 7px;
    font-size: 1rem;
    background: #f9fafb;
    transition: border-color 0.3s, box-shadow 0.3s;
    outline: none;
  }
  input[type="text"]:focus, input[type="number"]:focus, textarea:focus, select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px #dbeafe;
  }
  textarea {
    min-height: 80px;
    resize: vertical;
  }
  .form-group input[type="file"] {
    border: none;
    background: none;
    padding: 0;
    font-size: 1rem;
  }
  .form-actions {
    margin-top: 18px;
    text-align: center;
  }
  button {
    padding: 13px 0;
    width: 100%;
    background-color: #2563eb;
    border: none;
    color: white;
    font-size: 1.1rem;
    border-radius: 7px;
    cursor: pointer;
    font-weight: bold;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(37,99,235,0.08);
    transition: background-color 0.3s, box-shadow 0.2s;
  }
  button:hover {
    background-color: #1e40af;
    box-shadow: 0 4px 16px rgba(37,99,235,0.13);
  }
  .message {
    margin-bottom: 8px;
    text-align: center;
    font-size: 1.08rem;
    color: #16a34a;
    font-weight: 600;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 6px;
    padding: 10px 0;
  }
  .error {
    color: #ef4444;
    background: #fef2f2;
    border: 1px solid #fecaca;
  }
  #category_new {
    margin-top: 8px;
    padding: 10px 14px;
    border-radius: 7px;
    border: 1.5px solid #d1d5db;
    font-size: 1rem;
    background: #f9fafb;
    transition: border-color 0.3s;
  }
  #category_new:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px #dbeafe;
  }
  @media (max-width: 900px) {
    .dashboard-layout { flex-direction: column; }
    .sidebar { width: 100%; flex-direction: row; min-height: unset; padding: 0; }
    .sidebar h2 { display: none; }
    .sidebar a, .sidebar .logout-btn { flex: 1; text-align: center; padding: 14px 0; }
    .main-content { padding: 0 2vw; }
    .add-product-form { padding: 24px 8px; margin-top: 24px; }
  }
  @media (max-width: 600px) {
    .main-content { padding: 0 1vw; }
    .add-product-form { padding: 12px 2px; margin-top: 10px; }
    .add-product-form h2 { font-size: 1.2rem; }
    button { font-size: 1rem; }
  }
</style>
<script>
function toggleCategoryInput(sel) {
  var newCat = document.getElementById('category_new');
  if (sel.value === '__new__') {
    newCat.style.display = 'block';
    newCat.required = true;
  } else {
    newCat.style.display = 'none';
    newCat.required = false;
  }
}
</script>
</head>
<body>
<div class="dashboard-layout">
  <nav class="sidebar">
    <h2>E-Com Admin</h2>
    <a href="../dashboard.php">🏠 Dashboard</a>
    <a href="product_list.php">🛒 Product List</a>
    <a href="add_product.php" class="active">➕ Add Product</a>
    <a href="orders.php">📦 Orders</a>
    <a href="profile.php">👤 Profile</a>
    <a class="logout-btn" href="logout.php">Logout</a>
  </nav>
  <main class="main-content">
    <form class="add-product-form" method="post" enctype="multipart/form-data" autocomplete="off">
      <h2>Add New Product</h2>
      <?php if ($message): ?>
        <div class="message<?= strpos($message, 'Invalid') !== false || strpos($message, 'Please') !== false ? ' error' : '' ?>">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>
      <div class="form-group">
        <label for="name">Product Name *</label>
        <input type="text" id="name" name="name" required maxlength="100" placeholder="e.g. Calliber Sneakers">
      </div>
      <div class="form-group">
        <label for="category_select">Category *</label>
        <select id="category_select" name="category_select" onchange="toggleCategoryInput(this)" required>
          <option value="">-- Select Category --</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
          <?php endforeach; ?>
          <option value="__new__">+ Add New Category</option>
        </select>
        <input type="text" id="category_new" name="category_new" placeholder="Enter new category" style="display:none;">
      </div>
      <div class="form-group">
        <label for="price">Price (Rs) *</label>
        <input type="number" id="price" name="price" min="0" step="0.01" required placeholder="e.g. 3000">
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" maxlength="500" placeholder="Product details, features, etc."></textarea>
      </div>
      <div class="form-group">
        <label for="image">Product Image</label>
        <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.gif">
      </div>
      <div class="form-actions">
        <button type="submit">Add Product</button>
      </div>
    </form>
  </main>
</div>
</body>
</html>