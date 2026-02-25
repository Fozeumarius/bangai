<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bangue");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to make a payment.");
}

$successMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id       = $_SESSION['user_id'];
    $accountName   = $_POST['accountName'];
    $accountNumber = $_POST['accountNumber'];
    $amount        = $_POST['amountInput'];
    $method        = $_POST['paymentMethod'];
    $date          = date("Y-m-d");
    $status        = "Completed";

    $stmt = $conn->prepare("INSERT INTO payments (user_id, account_name, account_number, amount, method, payment_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdsss", $user_id, $accountName, $accountNumber, $amount, $method, $date, $status);

    if ($stmt->execute()) {
        $successMessage = "✅ Payment recorded successfully!";
    } else {
        $successMessage = "❌ Error: " . $stmt->error;
    }
}

// Fetch payment history for this user
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT payment_date, account_name, account_number, amount, method, status FROM payments WHERE user_id = $user_id ORDER BY payment_date DESC");
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
      <li><a href="payments.php">Payments</a></li>
      <li><a href="maintenance.php">Maintenance</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="index.php" class="login-btn">Login</a></li>
    </ul>
  </nav>

  <!-- Page Header -->
  <header class="page-header">
    <h1>PAYMENTS</h1>
    <p>Manage your rent and track payment history.</p>
  </header>

  <!-- Current Payment Section -->
  <section class="payment-section">
    <h2>Current Rent</h2>
    <div class="payment-card">
      <?php if ($successMessage): ?>
        <p style="color: green; text-align: center;"><?= $successMessage ?></p>
      <?php endif; ?>

      <form method="POST" action="payments.php">
        <label for="accountName">Account Name:</label>
        <input type="text" name="accountName" required>

        <label for="accountNumber">Account Number / Phone:</label>
        <input type="text" name="accountNumber" required>

        <label for="amountInput">Amount (FCFA):</label>
        <input type="number" step="0.01" name="amountInput" required>

        <label for="paymentMethod">Choose Payment Method:</label>
        <select name="paymentMethod">
          <option value="MTN">MTN Mobile Money</option>
          <option value="Orange">Orange Money</option>
          <option value="Bank">Bank Transfer</option>
        </select>

        <button type="submit" class="btn">Pay Now</button>
      </form>
    </div>
  </section>

  <!-- Payment History Section -->
  <section class="payment-history">
    <h2>Payment History</h2>
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Account</th>
          <th>Amount</th>
          <th>Payment Mode</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
          <td><?= $row['payment_date'] ?></td>
          <td><?= $row['account_name'] ?> (<?= $row['account_number'] ?>)</td>
          <td><?= $row['amount'] ?> FCFA</td>
          <td><?= $row['method'] ?></td>
          <td><?= $row['status'] ?></td>
        </tr>
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
