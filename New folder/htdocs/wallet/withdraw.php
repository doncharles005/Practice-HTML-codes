<?php
require 'db_connect.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); 
    die(json_encode(['success' => false, 'error' => 'Not authenticated']));
}

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

// Get amount and paypal_email from the JSON body
$amount = isset($data['amount']) ? (float)$data['amount'] : 0;
$paypalEmail = isset($data['paypal_email']) ? trim($data['paypal_email']) : '';

// Validate the inputs
if ($amount <= 0) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Withdrawal amount must be a positive number']));
}
if (empty($paypalEmail) || !filter_var($paypalEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'A valid PayPal email is required']));
}

try {
    $pdo->beginTransaction();
    
    // 1. Get user's wallet and lock the row for the transaction
    $stmt = $pdo->prepare("SELECT address, balance FROM wallets WHERE user_id = ? FOR UPDATE");
    $stmt->execute([$userId]);
    $wallet = $stmt->fetch();

    if (!$wallet) {
        throw new Exception("Wallet not found for user.");
    }

    // 2. Check for sufficient funds
    if ($wallet['balance'] < $amount) {
        throw new Exception("Insufficient funds.");
    }
    
    // 3. Subtract the amount from the user's wallet
    $newBalance = $wallet['balance'] - $amount;
    $stmt = $pdo->prepare("UPDATE wallets SET balance = ? WHERE user_id = ?");
    $stmt->execute([$newBalance, $userId]);
    
    // 4. Record the withdrawal request, including the PayPal email
    $stmt = $pdo->prepare(
        "INSERT INTO withdrawals (user_id, amount, paypal_email, status) VALUES (?, ?, ?, 'pending')"
    );
    $stmt->execute([$userId, $amount, $paypalEmail]);
    
    // 5. Record the transaction in the main transactions table
    $txHash = hash('sha256', uniqid('wd_', true)); // Prefix for withdrawal
    $stmt = $pdo->prepare(
        "INSERT INTO transactions (from_address, to_address, amount, transaction_hash, status) 
         VALUES (?, ?, ?, ?, 'completed')"
    );
    $stmt->execute([
        $wallet['address'],
        'external_withdrawal', // A system identifier for withdrawals
        $amount,
        $txHash
    ]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Withdrawal request submitted successfully!',
        'new_balance' => number_format($newBalance, 8),
        'tx_hash' => $txHash
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    // Provide a user-friendly message but log the detailed error
    error_log('Withdrawal Error: ' . $e->getMessage());
    die(json_encode(['success' => false, 'error' => 'Withdrawal failed: ' . $e->getMessage()]));
}
?>
