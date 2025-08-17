<?php
require 'db_connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Not logged in']));
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT balance, address FROM wallets WHERE user_id = ?");
    $stmt->execute([$userId]);
    $wallet = $stmt->fetch();
    
    if (!$wallet) {
        die(json_encode(['error' => 'Wallet not found']));
    }
    
    echo json_encode([
        'success' => true,
        'balance' => $wallet['balance'],
        'address' => $wallet['address']
    ]);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database error: ' . $e->getMessage()]));
}
?>