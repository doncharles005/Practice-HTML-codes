<?php
// config.php - Database configuration

$host = 'localhost';
$dbname = 'practice1';   // Your database
$username = 'root';      // Default in XAMPP
$password = '';          // Default is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    error_log("Database Connection Failed: " . $e->getMessage());
    die("Failed to connect to database. Check if MySQL is running and 'practice1' exists.");
}