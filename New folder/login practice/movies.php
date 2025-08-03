<?php
// Start session to check login status
session_start();

// Enforce login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Movies - Watch Video</title>
</head>
<body>
    <h2>🎬 Movie Video</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['firstname'] ?? 'User'); ?>! You're logged in.</p>

    <h3>Watch the Video:</h3>
    <!-- Embed YouTube Video -->
    <iframe 
        width="560" 
        height="315" 
        src="https://www.youtube.com/embed/jTNyOc8nbZM" 
        frameborder="0" 
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
        allowfullscreen>
    </iframe>

    <br><br>
    <a href="dashboard.php">← Back to Dashboard</a> |
    <a href="logout.php">Logout</a>
</body>
</html>
