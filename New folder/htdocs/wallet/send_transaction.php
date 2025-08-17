<?php
session_start();
require 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['status' => 'error', 'message' => 'You must be logged in']));
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Extract and sanitize data
$toAddress = trim($input['to_address'] ?? '');
$amount = floatval($input['amount'] ?? 0);
$description = trim($input['description'] ?? '');

// Validation
if ($amount <= 0) {
    http_response_code(400);
    die(json_encode(['status' => 'error', 'message' => 'Amount must be greater than zero']));
}

if (empty($toAddress)) {
    http_response_code(400);
    die(json_encode(['status' => 'error', 'message' => 'Recipient wallet address is required']));
}

try {
    $pdo->beginTransaction();

    // Get sender's wallet
    $stmt = $pdo->prepare("SELECT w.id, w.balance, w.address FROM wallets w WHERE w.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $sender_wallet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sender_wallet) {
        throw new Exception('Sender wallet not found');
    }

    if ($sender_wallet['balance'] < $amount) {
        throw new Exception('Insufficient funds');
    }

    // Get recipient by wallet address
    $stmt = $pdo->prepare("
        SELECT w.id AS wallet_id, u.id AS user_id 
        FROM wallets w 
        JOIN users u ON w.user_id = u.id 
        WHERE w.address = ?
    ");
    $stmt->execute([$toAddress]);
    $recipient_wallet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipient_wallet) {
        throw new Exception('No user found with that wallet address');
    }

    if ($recipient_wallet['user_id'] == $_SESSION['user_id']) {
        throw new Exception('You cannot send money to yourself');
    }

    // Deduct from sender
    $stmt = $pdo->prepare("UPDATE wallets SET balance = balance - ? WHERE id = ?");
    $stmt->execute([$amount, $sender_wallet['id']]);

    // Add to recipient
    $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$amount, $recipient_wallet['wallet_id']]);

    // Generate a fake transaction hash
    $txHash = '0x' . bin2hex(random_bytes(32));

    // Record transaction
    $stmt = $pdo->prepare("
        INSERT INTO transactions (from_address, to_address, amount, transaction_hash, status, created_at)
        VALUES (?, ?, ?, ?, 'confirmed', NOW())
    ");
    $stmt->execute([
        $sender_wallet['address'],
        $toAddress,
        $amount,
        $txHash
    ]);

    
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Transaction completed successfully',
        'tx_hash' => $txHash,
        'new_balance' => number_format($sender_wallet['balance'] - $amount, 8)
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}