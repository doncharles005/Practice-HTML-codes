<?php
require 'db_connect.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Find user
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? AND is_active = 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Login success
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // Check if wallet exists, if not create one
        $walletStmt = $pdo->prepare("SELECT id FROM wallets WHERE user_id = ?");
        $walletStmt->execute([$user['id']]);
        if ($walletStmt->rowCount() === 0) {
            $address = hash('sha256', uniqid(mt_rand(), true));
            $pdo->prepare("INSERT INTO wallets (user_id, address) VALUES (?, ?)")
                ->execute([$user['id'], $address]);
        }
        
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Crypto Wallet</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 8px; box-sizing: border-box;
            border: 1px solid #ddd; border-radius: 4px;
        }
        button { 
            padding: 10px 15px; 
            background: #4CAF50; 
            color: white; 
            border: none; 
            cursor: pointer;
            width: 100%;
            border-radius: 4px;
            font-size: 16px;
        }
        button:hover {
            background: #45a049;
        }
        .error { 
            color: red; 
            margin-bottom: 10px;
            padding: 10px;
            background: #ffeeee;
            border-left: 3px solid red;
        }
        .success { 
            color: green; 
            margin-bottom: 10px;
            padding: 10px;
            background: #eeffee;
            border-left: 3px solid green;
        }
        .login-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .footer-links {
            margin-top: 20px;
            text-align: center;
        }
        .footer-links a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 style="text-align: center; margin-bottom: 25px;">Login to Your Wallet</h1>
        
        <?php if (isset($_GET['registration']) && $_GET['registration'] === 'success'): ?>
            <div class="success">
                <p>Registration successful! Please login.</p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        
        <div class="footer-links">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p><a href="#">Forgot password?</a></p>
        </div>
    </div>
</body>
</html>