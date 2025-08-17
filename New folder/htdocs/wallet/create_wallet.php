<?php
require 'db_connect.php'; // Your database connection file

session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Not logged in']));
}

$userId = $_SESSION['user_id'];

// Generate a unique wallet address
function generateWalletAddress() {
    return hash('sha256', uniqid(mt_rand(), true));
}

try {
    // Check if user already has a wallet
    $stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = ?");
    $stmt->execute([$userId]);
    
    if ($stmt->rowCount() > 0) {
        die(json_encode(['error' => 'Wallet already exists']));
    }
    
    // Create new wallet
    $address = generateWalletAddress();
    $stmt = $pdo->prepare("INSERT INTO wallets (user_id, balance, address) VALUES (?, 0, ?)");
    $stmt->execute([$userId, $address]);
    
    echo json_encode(['success' => true, 'address' => $address]);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database error: ' . $e->getMessage()]));
}
?>