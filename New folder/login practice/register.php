<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'config.php'; // Connects to practice1

    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    // Validate input
    if (empty($firstname) || empty($lastname) || !$email || empty($password)) {
        die("Please fill in all fields correctly.");
    }
    if (strlen($password) < 6) {
        die("Password must be at least 6 characters.");
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        die("This email is already registered. <a href='login.php'>Login here</a>");
    }

    // Hash the password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users table (match your columns)
    $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$firstname, $lastname, $email, $hashed]);

    // Success!
    echo "✅ Registration successful! <a href='login.php'>Click here to login</a>.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Create Account</h2>
    <form method="post">
        <label>First Name: <input type="text" name="firstname" required></label><br><br>

        <label>Last Name: <input type="text" name="lastname" required></label><br><br>

        <label>Email: <input type="email" name="email" required></label><br><br>

        <label>Password: <input type="password" name="password" required></label><br><br>

        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>