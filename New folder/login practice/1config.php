<?php
// Database configuration
$host = 'localhost';
$dbname = 'practice1';     // Your database name
$username = 'root';        // Default in XAMPP
$password = '';            // Default is empty in XAMPP

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO attributes
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Use real prepared statements

} catch (PDOException $e) {
    // Log the error for debugging (visible in error logs, not to users)
    error_log("Database Connection Failed: " . $e->getMessage());

    // Check if this is an AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // Send JSON response for AJAX
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed. Please try again later.'
        ]);
        exit;
    } else {
        // For direct access (e.g., visiting config.php in browser)
        http_response_code(500);
        die("<h1>Database Error</h1><p>Unable to connect to database.</p><p>Check:</p><ul><li>Is MySQL running in XAMPP?</li><li>Does database '<strong>practice1</strong>' exist?</li></ul>");
    }
}