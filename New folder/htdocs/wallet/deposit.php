<?php
require 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Not logged in']));
}

$userId = $_SESSION['user_id'];
$amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;

// Validate amount
if ($amount <= 0) {
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Amount must be greater than 0']));
}

try {
    $pdo->beginTransaction();
    
    // 1. Record the deposit
    $stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, status) VALUES (?, ?, 'completed')");
    $stmt->execute([$userId, $amount]);
    
    // 2. Update wallet balance
    $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE user_id = ?");
    $stmt->execute([$amount, $userId]);
    
    // 3. Record the transaction
    $walletStmt = $pdo->prepare("SELECT address FROM wallets WHERE user_id = ?");
    $walletStmt->execute([$userId]);
    $wallet = $walletStmt->fetch();
    
    $txHash = hash('sha256', uniqid(mt_rand(), true));
    $stmt = $pdo->prepare("INSERT INTO transactions 
                          (from_address, to_address, amount, transaction_hash, status) 
                          VALUES (?, ?, ?, ?, 'completed')");
    $stmt->execute([
        'system', // Special system address for deposits
        $wallet['address'],
        $amount,
        $txHash
    ]);
    
    // 4. Update deposit as completed
    $depositId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("UPDATE deposits SET status = 'completed', completed_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$depositId]);
    
    $pdo->commit();
    
    // Get updated balance
    $balanceStmt = $pdo->prepare("SELECT balance FROM wallets WHERE user_id = ?");
    $balanceStmt->execute([$userId]);
    $newBalance = $balanceStmt->fetchColumn();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'new_balance' => $newBalance,
        'tx_hash' => $txHash
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Deposit failed: ' . $e->getMessage()]));
}
?>