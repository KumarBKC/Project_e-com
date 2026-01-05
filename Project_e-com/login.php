<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION["user_id"] = $user['id'];
        $_SESSION["username"] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        // Styled error message
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <title>Login Error</title>
            <style>
                body, html {
                    margin: 0;
                    font-family: Arial, sans-serif;
                    background: #f0f2f5;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .message-box {
                    background: white;
                    padding: 30px 40px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    width: 400px;
                    text-align: center;
                }
                .error {
                    color: #ef4444;
                    font-weight: bold;
                    margin-bottom: 20px;
                }
                a {
                    color: #3b82f6;
                    text-decoration: none;
                    font-weight: bold;
                }
                a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <div class="message-box">
                <p class="error">❌ Invalid username or password.</p>
                <p><a href="login.html">Back to Login</a></p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
?>
