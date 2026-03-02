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

    // Current payment date
    $paymentDate = date('Y-m-d');
    // Next due date = same day next month
    $dueDate = date('Y-m-d', strtotime('+1 month', strtotime($paymentDate)));

    $stmt = $conn->prepare("
        INSERT INTO payments (user_id, account_name, account_number, amount, payment_date, status, due_date)
        VALUES (?, ?, ?, ?, NOW(), ?, ?)
    ");
    $status = "Completed"; 
    $stmt->bind_param("issdss", $userId, $accountName, $accountNumber, $amount, $status, $dueDate);
    if (!$stmt->execute()) {
        die("Payment insert failed: " . $stmt->error);
    }
    $stmt->close();

    // ✅ Insert notification for this payment
    $message = "Payment of " . number_format($amount, 2) . " FCFA has been added successfully.";
    $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'success')");
    $notifStmt->bind_param("is", $userId, $message);
    $notifStmt->execute();
    $notifStmt->close();

    // ✅ Insert notification for next due date
    $nextMessage = "Your next payment is due on " . date('F j, Y', strtotime($dueDate)) . ".";
    $notifStmt2 = $conn->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'info')");
    $notifStmt2->bind_param("is", $userId, $nextMessage);
    $notifStmt2->execute();
    $notifStmt2->close();

    // 📧 Send email to user
    $to      = $user['email'];
    $subject = "Payment Confirmation & Next Due Date";
    $body    = "Dear " . $user['username'] . ",\n\n" .
               "We have received your payment of " . number_format($amount, 2) . " FCFA.\n" .
               "Account Name: " . $accountName . "\n" .
               "Account Number: " . $accountNumber . "\n" .
               "Status: " . $status . "\n\n" .
               "Your next payment is due on " . date('F j, Y', strtotime($dueDate)) . ".\n" .
               "We will remind you 5 days, 3 days, and 1 day before the due date.\n\n" .
               "Best regards,\nYour Management Team";

    $headers = "From: no-reply@bangue.com\r\n" .
               "Reply-To: no-reply@bangue.com\r\n" .
               "X-Mailer: PHP/" . phpversion();

    mail($to, $subject, $body, $headers);

    // 🚀 Redirect to avoid resubmission
    header("Location: user_payments.php?user_id=$userId&msg=success");
    exit();
}

// Récupérer ses paiements
$paymentsResult = $conn->query("
    SELECT id, account_number, account_name, amount, payment_date, status, due_date
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

    <!-- ⚠️ Step 1: Upcoming Payment Alert -->
    <?php
    $alertQuery = $conn->query("
        SELECT due_date 
        FROM payments 
        WHERE user_id=$userId 
        ORDER BY id DESC 
        LIMIT 1
    ");
    $alertRow = $alertQuery->fetch_assoc();

    if ($alertRow && !empty($alertRow['due_date'])) {
        $dueDate = $alertRow['due_date'];
        $daysLeft = (strtotime($dueDate) - strtotime(date('Y-m-d'))) / 86400;

        if ($daysLeft > 0 && $daysLeft <= 7) {
            echo "
            <div style='
                background: #f39c12;
                color: #fff;
                padding: 12px 20px;
                margin: 15px auto;
                border-radius: 6px;
                font-weight: bold;
                box-shadow: 0 2px 6px rgba(0,0,0,0.15);
                width: 500px;
                text-align: center;
            '>
                ⚠️ Reminder: Your next payment is due on " . date('F j, Y', strtotime($dueDate)) . 
                " (in $daysLeft day" . ($daysLeft > 1 ? "s" : "") . ").
            </div>
            ";
        }
    }
    ?>

    <!-- 🎨 Success message after redirect -->
    <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'success') { 
        $latestPayment = $conn->query("SELECT due_date FROM payments WHERE user_id=$userId ORDER BY id DESC LIMIT 1");
        $latest = $latestPayment->fetch_assoc();
        $nextDueDate = $latest ? date('F j, Y', strtotime($latest['due_date'])) : '';
    ?>
        <div style='
            background: #2ecc71;
            color: #fff;
            padding: 12px 20px;
            margin: 15px auto;
            border-radius: 6px;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            width: 400px;
            text-align: center;
        '>
            ✅ Payment has been added successfully! 
            <?php if ($nextDueDate) { ?>
                Next payment due on <?= htmlspecialchars($nextDueDate) ?>.
            <?php } ?>
        </div>
    <?php } ?>

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
                    <th>Next Due Date</th>
                    <th>Actions</th> <!-- ✅ New column -->
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
                        <td><?= $payment['due_date'] ? htmlspecialchars($payment['due_date']) : '—' ?></td>
                        <td>
                            <!-- ✅ Green Edit button -->
                            <a href="edit_payments.php?id=<?= $payment['id'] ?>" 
                               style="background:#27ae60; color:#fff; padding:6px 12px; border-radius:4px; text-decoration:none; font-weight:bold;">
                               ✏️ Edit
                            </a>
                        </td>
                    </tr>
                <?php } ?> <!-- closes while loop -->
            </tbody>
        </table>
    <?php } else { ?>
        <p>No payments found for this user.</p>
    <?php } ?> <!-- closes if block -->

    <?php
    $overdueQuery = $conn->query("
        SELECT due_date, status 
        FROM payments 
        WHERE user_id=$userId 
        ORDER BY id DESC 
        LIMIT 1
    ");
    $overdueRow = $overdueQuery->fetch_assoc();

    if ($overdueRow && !empty($overdueRow['due_date'])) {
        $dueDate = $overdueRow['due_date'];
        $status  = $overdueRow['status'];

        if (strtotime($dueDate) < strtotime(date('Y-m-d')) && $status !== 'Completed') {
            echo "
            <div style='
                background: #e74c3c;
                color: #fff;
                padding: 12px 20px;
                margin: 15px auto;
                border-radius: 6px;
                font-weight: bold;
                box-shadow: 0 2px 6px rgba(0,0,0,0.15);
                width: 500px;
                text-align: center;
            '>
                ❌ Alert: Your payment scheduled for " . date('F j, Y', strtotime($dueDate)) . " is overdue. Please pay immediately.
            </div>
            ";

            // 📧 Send overdue email
            $to      = $user['email'];
            $subject = "Overdue Payment Alert";
            $body    = "Dear " . $user['username'] . ",\n\n" .
                       "Our records show that your payment due on " . date('F j, Y', strtotime($dueDate)) . " has not been completed.\n" .
                       "Please make the payment immediately to avoid penalties.\n\n" .
                       "Best regards,\nYour Management Team";

            $headers = "From: no-reply@bangue.com\r\n" .
                       "Reply-To: no-reply@bangue.com\r\n" .
                       "X-Mailer: PHP/" . phpversion();

           file_put_contents("mail_log.txt", "To: $to\nSubject: $subject\n\n$body\n\n----------------------\n", FILE_APPEND );

            // Insert overdue notification in DB
            $overdueMessage = "Payment scheduled for " . date('F j, Y', strtotime($dueDate)) . " is overdue.";
            $notifStmt3 = $conn->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'error')");
            $notifStmt3->bind_param("is", $userId, $overdueMessage);
            $notifStmt3->execute();
            $notifStmt3->close();
        }
    }
    ?>

</body>
</html>
