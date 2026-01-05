<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirm_password = $_POST["confirm_password"] ?? '';

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Everything looks good, hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");

        try {
            $stmt->execute([$username, $email, $hashedPassword]);
            $success = "✅ Registration successful! You can now <a href='login.html'>login</a>.";
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $error = "❌ Username or email already exists.";
            } else {
                $error = "❌ Something went wrong: " . htmlspecialchars($e->getMessage());
            }
        }
    }
} else {
    // If not POST, redirect to register form
    header("Location: register.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Registration Result</title>
<style>
  body, html {
    margin: 0; font-family: Arial, sans-serif; background: #f0f2f5;
    display: flex; justify-content: center; align-items: center; height: 100vh;
  }
  .message-box {
    background: white; padding: 30px 40px; border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 400px; text-align: center;
  }
  .message-box p {
    font-size: 18px; color: #555;
  }
  .error {
    color: #ef4444;
    font-weight: bold;
    margin-bottom: 20px;
  }
  .success {
    color: #22c55e;
    font-weight: bold;
    margin-bottom: 20px;
  }
  a {
    color: #3b82f6; text-decoration: none; font-weight: bold;
  }
  a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
  <div class="message-box">
    <?php if (!empty($error)): ?>
      <p class="error"><?php echo $error; ?></p>
      <p><a href="register.html">Back to Register</a></p>
    <?php elseif (!empty($success)): ?>
      <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
  </div>
</body>
</html>
