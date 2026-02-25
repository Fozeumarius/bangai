<?php
// connect to database
$conn = new mysqli("localhost", "root", "", "bangue");

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Total payments
$totalPayments = $conn->query("SELECT SUM(amount) AS total_amount FROM payments")->fetch_assoc()['total_amount'];

// Monthly totals
$monthly = $conn->query("
    SELECT DATE_FORMAT(payment_date, '%Y-%m') AS month, SUM(amount) AS total
    FROM payments
    GROUP BY month
    ORDER BY month DESC
");

// Per-user totals
$perUser = $conn->query("
    SELECT user_id, SUM(amount) AS total
    FROM payments
    GROUP BY user_id
    ORDER BY total DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
    <link rel="stylesheet" href="../css/reports.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_payment.php">Manage Payments</a></li>
            <li><a href="manage_maintenance.php">Manage Maintenance</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h2>Reports</h2>

        <h3>Total Payments</h3>
        <p><strong><?= number_format($totalPayments, 2) ?> FCFA</strong></p>

        <h3>Monthly Totals</h3>
        <table>
            <tr><th>Month</th><th>Total Amount</th></tr>
            <?php while($row = $monthly->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['month']) ?></td>
                    <td><?= number_format($row['total'], 2) ?> FCFA</td>
                </tr>
            <?php } ?>
        </table>

        <h3>Payments by User</h3>
        <table>
            <tr><th>User ID</th><th>Total Amount</th></tr>
            <?php while($row = $perUser->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['user_id']) ?></td>
                    <td><?= number_format($row['total'], 2) ?> FCFA</td>
                </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>
