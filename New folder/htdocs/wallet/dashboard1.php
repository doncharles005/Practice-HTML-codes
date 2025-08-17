<?php
session_start();
require 'db_connect.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user info and wallet
$stmt = $pdo->prepare("SELECT u.username, w.address, w.balance 
                      FROM users u 
                      JOIN wallets w ON u.id = w.user_id 
                      WHERE u.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get all users for recipient dropdown (excluding current user)
$recipients = $pdo->query("
    SELECT u.id, u.username, u.email, w.address 
    FROM users u 
    JOIN wallets w ON u.id = w.user_id 
    WHERE u.id != " . $_SESSION['user_id'] . "
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Crypto Wallet</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px;
            line-height: 1.6;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .logout-btn {
            background-color: #f44336;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
        }
        .logout-btn:hover {
            background-color: #d32f2f;
        }
        .wallet-info { 
            background: #f5f5f5; 
            padding: 20px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
        }
        .transaction-form, .deposit-form { 
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .transaction-history { 
            margin-top: 30px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
        }
        th, td { 
            padding: 10px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        th {
            background-color: #f2f2f2;
            position: sticky;
            top: 0;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button, input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover, input[type="submit"]:hover {
            background-color: #45a049;
        }
        #refresh-balance, #refresh-tx {
            background-color: #2196F3;
            margin-bottom: 10px;
        }
        #refresh-balance:hover, #refresh-tx:hover {
            background-color: #0b7dda;
        }
        .success-message {
            color: #28a745;
            margin-top: 10px;
            padding: 10px;
            background-color: #e6ffed;
            border-radius: 4px;
        }
        .error-message {
            color: #dc3545;
            margin-top: 10px;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 4px;
        }
        .section-title {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-top: 0;
        }
        .balance-display {
            font-size: 1.2em;
            font-weight: bold;
        }
        .tx-direction {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 8px;
        }
        .incoming {
            background-color: #e6f7ff;
            color: #0366d6;
        }
        .outgoing {
            background-color: #ffeef0;
            color: #d73a49;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .confirmed {
            background-color: #28a745;
            color: white;
        }
        .pending {
            background-color: #ffc107;
            color: #212529;
        }
        .failed {
            background-color: #dc3545;
            color: white;
        }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .processing {
            color: #17a2b8;
            font-style: italic;
        }
        #transactions-table tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>My Crypto Wallet</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($user['username']); ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="wallet-info">
        <h2 class="section-title">Wallet Information</h2>
        <p><strong>Address:</strong> <span id="wallet-address"><?php echo htmlspecialchars($user['address']); ?></span></p>
        <p><strong>Balance:</strong> <span id="wallet-balance" class="balance-display"><?php echo number_format($user['balance'], 8); ?></span> <span id="currency-symbol">COIN</span></p>
        <button id="refresh-balance">Refresh Balance</button>
    </div>
    
    <div class="deposit-form">
        <h2 class="section-title">Top Up Balance</h2>
        <form id="deposit-form">
            <div>
                <label for="deposit-amount">Amount to Deposit:</label>
                <input type="number" id="deposit-amount" step="0.00000001" min="0.00000001" required>
            </div>
            <button type="submit">Deposit</button>
        </form>
        <div id="deposit-result"></div>
    </div>
    
    <div class="transaction-form">
        <h2 class="section-title">Send Transaction</h2>
        <form id="send-form">
            <div>
                <label for="recipient-type">Send to:</label>
                <select id="recipient-type" class="recipient-type">
                    <option value="address">Wallet Address</option>
                    <option value="email">Email Address</option>
                </select>
            </div>
            <div id="recipient-address-container">
                <label for="recipient-address">Recipient Wallet Address:</label>
                <input type="text" id="recipient-address" required>
            </div>
            <div id="recipient-email-container" style="display:none;">
                <label for="recipient-email">Recipient Email:</label>
                <select id="recipient-email">
                    <option value="">Select recipient</option>
                    <?php foreach ($recipients as $recipient): ?>
                        <option value="<?php echo htmlspecialchars($recipient['address']); ?>" 
                                data-email="<?php echo htmlspecialchars($recipient['email']); ?>">
                            <?php echo htmlspecialchars($recipient['username']); ?> (<?php echo htmlspecialchars($recipient['email']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="amount">Amount:</label>
                <input type="number" id="amount" step="0.00000001" min="0.00000001" required>
            </div>
            <button type="submit">Send</button>
        </form>
        <div id="transaction-result"></div>
    </div>
    
    <div class="transaction-history">
        <h2 class="section-title">Transaction History</h2>
        <button id="refresh-tx">Refresh History</button>
        <div style="overflow-x: auto;">
            <table id="transactions-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Counterparty</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>TX Hash</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="processing">Loading transactions...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle between address and email recipient fields
            document.getElementById('recipient-type').addEventListener('change', function() {
                const type = this.value;
                const isAddress = type === 'address';
                document.getElementById('recipient-address-container').style.display = isAddress ? 'block' : 'none';
                document.getElementById('recipient-address').required = isAddress;

                document.getElementById('recipient-email-container').style.display = isAddress ? 'none' : 'block';
                document.getElementById('recipient-email').required = !isAddress;
            });
            
            // Load wallet info
            function loadWalletInfo() {
                fetch('get_balance.php')
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            document.getElementById('wallet-balance').textContent = 
                                parseFloat(data.balance).toFixed(8);
                        } else {
                            showError('wallet-info', data.error || 'Failed to load wallet information');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('wallet-info', 'Failed to connect to server');
                    });
            }
            
            // Enhanced transaction history loader
            function loadTransactionHistory() {
                const tbody = document.querySelector('#transactions-table tbody');
                tbody.innerHTML = '<tr><td colspan="6" class="processing">Loading transactions...</td></tr>';
                
                fetch('transaction_history.php?limit=100')
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        tbody.innerHTML = '';
                        
                        if (!data.success || data.count === 0) {
                            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No transactions found</td></tr>';
                            return;
                        }
                        
                        const userAddress = document.getElementById('wallet-address').textContent.trim();

                        data.transactions.forEach(tx => {
                            const isIncoming = tx.to_address === userAddress;
                            const counterparty = isIncoming ? tx.from_address : tx.to_address;
                            const counterpartyDisplay = counterparty.length > 15 ? counterparty.substring(0, 15) + '...' : counterparty;
                            const formattedAmount = (isIncoming ? '+' : '-') + parseFloat(tx.amount).toFixed(8);
                            const formattedDate = new Date(tx.created_at).toLocaleString();
                            const statusClass = tx.status ? tx.status.toLowerCase() : 'pending';
                            const shortHash = tx.transaction_hash ? tx.transaction_hash.substring(0, 12) + '...' : '';

                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td><span class="tx-direction ${isIncoming ? 'incoming' : 'outgoing'}">${isIncoming ? 'IN' : 'OUT'}</span></td>
                                <td title="${counterparty}">${counterpartyDisplay}</td>
                                <td class="${isIncoming ? 'text-success' : 'text-danger'}">${formattedAmount}</td>
                                <td>${formattedDate}</td>
                                <td><span class="status-badge ${statusClass}">${tx.status || 'pending'}</span></td>
                                <td title="${tx.transaction_hash}">${shortHash}</td>
                            `;
                            tbody.appendChild(row);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading transactions:', error);
                        tbody.innerHTML = `<tr><td colspan="6" class="error-message">Error: ${error.message}</td></tr>`;
                    });
            }
            
            // Handle deposit form submission
            document.getElementById('deposit-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const amount = parseFloat(document.getElementById('deposit-amount').value);
                const resultDiv = document.getElementById('deposit-result');
                resultDiv.innerHTML = '<p class="processing">Processing deposit...</p>';
                
                fetch('deposit.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `amount=${amount}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resultDiv.innerHTML = `<p class="success-message">✓ Deposit successful! New balance: ${data.new_balance} COIN</p>`;
                        document.getElementById('deposit-amount').value = '';
                        loadWalletInfo();
                        loadTransactionHistory();
                    } else {
                        resultDiv.innerHTML = `<p class="error-message">Error: ${data.error || 'Deposit failed'}</p>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultDiv.innerHTML = `<p class="error-message">Error processing deposit: ${error.message}</p>`;
                });
            });
            
            // ** NEW: Handle send form submission with async/await **
            document.getElementById('send-form').addEventListener('submit', async function(e) {
                e.preventDefault(); // Stop normal form submit

                const resultDiv = document.getElementById('transaction-result');
                resultDiv.innerHTML = '<p class="processing">Sending...</p>';

                const recipientType = document.getElementById('recipient-type').value;
                const toAddress = recipientType === 'address' 
                    ? document.getElementById('recipient-address').value.trim()
                    : document.getElementById('recipient-email').value; // The value is the address
                const amount = parseFloat(document.getElementById('amount').value);

                if (!toAddress || isNaN(amount) || amount <= 0) {
                    resultDiv.innerHTML = 
                        '<p style="color:red;">Please fill in all fields correctly.</p>';
                    return;
                }

                try {
                    const response = await fetch('send_transaction.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            to_address: toAddress,
                            amount: amount
                        })
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        resultDiv.innerHTML = 
                            `<p style="color:green;">Transaction sent! TX Hash: ${result.tx_hash.substring(0,12)}...</p>`;
                        document.getElementById('send-form').reset();
                        loadWalletInfo();
                        loadTransactionHistory();
                    } else {
                        resultDiv.innerHTML = 
                            `<p style="color:red;">Error: ${result.error}</p>`;
                    }
                } catch (error) {
                    resultDiv.innerHTML = 
                        '<p style="color:red;">An error occurred while sending the transaction.</p>';
                    console.error('Fetch Error:', error);
                }
            });
            
            // Refresh buttons
            document.getElementById('refresh-balance').addEventListener('click', loadWalletInfo);
            document.getElementById('refresh-tx').addEventListener('click', loadTransactionHistory);
            
            // Helper functions
            function showError(containerId, message) {
                const container = document.getElementById(containerId);
                const resultDiv = container.querySelector('.error-message, .success-message, .processing');
                if(resultDiv) resultDiv.innerHTML = `<p class="error-message">${message}</p>`;
            }
            
            // Initial load
            loadWalletInfo();
            loadTransactionHistory();
        });
    </script>
</body>
</html>