<?php
session_start();

// Only allow access if logged in as manager
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') { 
    header("Location: index.php");
    exit();
}

// connect to database
$conn = new mysqli("localhost", "root", "", "bangue");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// fetch unread notifications (limit 5 for dropdown)
$userId = $_SESSION['user_id'] ?? 1; // adjust depending on your session setup
$result = $conn->query("SELECT * FROM notifications WHERE user_id=$userId AND is_read=0 ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manager Dashboard</title>
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="logo">
      <span>Bangue Management</span>
    </div>
    <ul class="nav-links">
      <li><a href="dashboard.php" class="active">Dashboard</a></li>
      <li><a href="manage_users.php">Users</a></li>
      <li><a href="manage_payment.php">Payments</a></li>
      <li><a href="manage_maintenance.php">Maintenance</a></li>
      <li><a href="reports.php">Reports</a></li>
      <li class="notifications">
        <span class="bell">🔔</span>
        <div class="dropdown">
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <p class="<?= $row['type'] ?>">
                <?= htmlspecialchars($row['message']) ?>
              </p>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No new notifications</p>
          <?php endif; ?>
        </div>
      </li>
      <a href="logout.php" class="logout-btn">Logout</a>
    </ul>
  </nav>

  <!-- Hero Section -->
  <header class="hero">
    <div class="overlay">
      <h1>Welcome <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
      <p>Here’s your overview of the Bangue Apartment System.</p>
    </div>
  </header>

  <!-- Dashboard Cards -->
  <section class="dashboard">
    <!-- your cards here -->
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; <?= date("Y"); ?> Bangue Apartment System — Manager Dashboard</p>
  </footer>
</body>
</html>
