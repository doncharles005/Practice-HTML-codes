<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);

    // 🔍 Debug: Log the raw input
    error_log("🔍 LOGIN ATTEMPT");
    error_log("Email submitted: '$email'");
    error_log("Password length: " . strlen($password));

    if (!$email || empty($password)) {
        echo "Please enter valid email and password.";
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, firstname, password FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            error_log("❌ USER NOT FOUND for email: $email");
            echo "Invalid email or password.";
            exit;
        }

        // 🔍 Debug: Show password hash from DB
        error_log("✅ USER FOUND: ID=" . $user['id']);
        error_log("Hash from DB: " . $user['password']);
        error_log("Hash length: " . strlen($user['password']));

        // 🔐 Verify password
        if (password_verify($password, $user['password'])) {
            error_log("✅ PASSWORD VERIFIED SUCCESSFULLY");
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['email'] = $email;
            header("Location: dashboard.php");
            exit;
        } else {
            error_log("❌ PASSWORD VERIFY FAILED");
            error_log("Password submitted: '$password'");
            echo "Invalid email or password.";
        }

    } catch (Exception $e) {
        error_log("🔴 LOGIN ERROR: " . $e->getMessage());
        echo "An error occurred. Please try again.";
    }
} else {
    header("Location: login.php");
    exit;
}