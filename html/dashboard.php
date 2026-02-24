<?php
session_start();

// Only allow access if logged in as manager
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') { 
    // Replace 'admin' with your manager username
    header("Location: index.php");
    exit();
}
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
      <img src="../img/7.webp" alt="Apartment Logo">
      <span>Bangue Manager</span>
    </div>
    <ul class="nav-links">
      <li><a href="dashboard.php" class="active">Dashboard</a></li>
      <li><a href="manage_users.php">Users</a></li>
      <li><a href="manage_payments.php">Payments</a></li>
      <li><a href="manage_maintenance.php">Maintenance</a></li>
      <li><a href="logout.php" class="logout-btn">Logout</a></li>
    </ul>
  </nav>

  <!-- Hero Section -->
  <header class="hero">
    <div class="overlay">
      <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
      <p>Here’s your overview of the Bangue Apartment System.</p>
    </div>
  </header>

  <!-- Dashboard Cards -->
  <section class="dashboard">
    <h2>Quick Actions</h2>
    <div class="cards">
      <div class="card">
        <h3>Manage Users</h3>
        <p>View, add, or update tenant accounts.</p>
        <a href="manage_users.php" class="btn">Go</a>
      </div>
      <div class="card">
        <h3>Payments</h3>
        <p>Track rent and utility payments.</p>
        <a href="manage_payments.php" class="btn">Go</a>
      </div>
      <div class="card">
        <h3>Maintenance</h3>
        <p>Review and assign maintenance requests.</p>
        <a href="manage_maintenance.php" class="btn">Go</a>
      </div>
      <div class="card">
        <h3>Reports</h3>
        <p>Generate monthly or annual reports.</p>
        <a href="reports.php" class="btn">Go</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; <?php echo date("Y"); ?> Bangue Apartment System — Manager Dashboard</p>
  </footer>
</body>
</html>
