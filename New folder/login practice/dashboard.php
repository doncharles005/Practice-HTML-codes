<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['firstname']); ?>!</h2>
    <p>You are logged in as <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong>.</p>
    <a href="logout.php">Logout</a>
</body>
</html>