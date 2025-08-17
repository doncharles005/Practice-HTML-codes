<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

// Include DB connection
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch transactions for this user
$sql = "SELECT t.id, t.amount, t.transaction_hash, t.status, t.created_at, t.from_address, t.to_address
        FROM transactions t
        WHERE t.from_address IN (SELECT address FROM wallets WHERE user_id = :user_id)
           OR t.to_address IN (SELECT address FROM wallets WHERE user_id = :user_id)
        ORDER BY t.created_at DESC
        LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$transactions = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'count' => count($transactions),
    'transactions' => $transactions
]);
