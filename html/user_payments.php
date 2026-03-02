<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bangue");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_GET['user_id'] ?? null;

if (!$userId) {
    echo "No user selected.";
    exit();
}

// Récupérer infos utilisateur
$userResult = $conn->query("SELECT username, email FROM users WHERE id=$userId");
$user = $userResult->fetch_assoc();

// Si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountName   = $_POST['accountName'];
    $accountNumber = $_POST['accountNumber'];
    $amount        = $_POST['amountInput'];

    $stmt = $conn->prepare("
        INSERT INTO payments (user_id, account_name, account_number, amount, payment_date, status)
        VALUES (?, ?, ?, ?, NOW(), ?)
    ");
    $status = "Completed"; 
    $stmt->bind_param("issds", $userId, $accountName, $accountNumber, $amount, $status);
    $stmt->execute();
    $stmt->close();

    echo "<p style='color:green'>Payment added successfully!</p>";
}

// Récupérer ses paiements
$paymentsResult = $conn->query("
    SELECT id, account_number, account_name, amount, payment_date, status
    FROM payments
    WHERE user_id=$userId
");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/user_payments.css">
    <title><?= htmlspecialchars($user['username']) ?> - Payments</title>
    
</head>
<body>
    <h2>Payments for <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)</h2>
     <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_payment.php">Manage Payments</a></li>
            <li><a href="manage_maintenance.php">Manage Maintenance</a></li>
            <li><a href="reports.php">Manage Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Formulaire d’ajout de paiement -->
    <form method="post">
        <label for="accountName">Account Name:</label>
        <input type="text" name="accountName" required>

        <label for="accountNumber">Account Number / Phone:</label>
        <input type="text" name="accountNumber" required>

        <label for="amountInput">Amount (FCFA):</label>
        <input type="number" step="0.01" name="amountInput" required>

        <button type="submit" class="btn">Pay Now</button>
    </form>

    <!-- Liste des paiements en tableau -->
    <?php if ($paymentsResult->num_rows > 0) { ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Account Name</th>
                    <th>Account Number</th>
                    <th>Amount (FCFA)</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($payment = $paymentsResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($payment['id']) ?></td>
                        <td><?= htmlspecialchars($payment['account_name']) ?></td>
                        <td><?= htmlspecialchars($payment['account_number']) ?></td>
                        <td><?= htmlspecialchars($payment['amount']) ?></td>
                        <td><?= htmlspecialchars($payment['payment_date']) ?></td>
                        <td><?= htmlspecialchars($payment['status']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No payments found for this user.</p>
    <?php } ?>
</body>
</html>
