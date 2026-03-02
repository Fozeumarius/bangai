<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bangue");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier que l’utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer l’historique des paiements
$result = $conn->query("
    SELECT payment_date, account_name, account_number, amount, status
    FROM payments
    WHERE user_id = $user_id
    ORDER BY payment_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payments</title>
  <link rel="stylesheet" href="../css/payment.css">
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="logo">
      <a href="homepage.php"><img src="../img/7.webp" alt="Apartment Logo"></a>
    </div>
    <ul class="nav-links">
      <li><a href="homepage.php">Home</a></li>
      <li><a href="payments.php" class="active">Payments</a></li>
      <li><a href="maintenance.php">Maintenance</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="index.php" class="login-btn">Logout</a></li>
    </ul>
  </nav>

  <!-- Page Header -->
  <header class="page-header">
    <h1>PAYMENTS</h1>
    <p>Track your rent and payment history.</p>
  </header>

  <!-- Payment History Section -->
  <section class="payment-history">
    <h2>Your Payment History</h2>
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Account</th>
          <th>Amount</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0) { ?>
          <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
              <td><?= htmlspecialchars($row['payment_date']) ?></td>
              <td><?= htmlspecialchars($row['account_name']) ?> (<?= htmlspecialchars($row['account_number']) ?>)</td>
              <td><?= htmlspecialchars($row['amount']) ?> FCFA</td>
              <td><?= htmlspecialchars($row['status']) ?></td>
            </tr>
          <?php } ?>
        <?php } else { ?>
          <tr><td colspan="5">No payments recorded yet.</td></tr>
        <?php } ?>
      </tbody>
    </table>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; Bangue Apartment System — simplifying housing, payments, and maintenance for the Bangue community.</p>
  </footer>
</body>
</html>
