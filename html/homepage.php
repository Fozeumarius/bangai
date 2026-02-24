<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // or login.php depending on your setup
    exit();
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "bangue");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT name, email, phone, apartment FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Apartment Management System</title>
  <link rel="stylesheet" href="../css/home.css">
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="logo">
      <a href="home.php">
        <img src="../img/7.webp" alt="Apartment Logo">
      </a>
    </div>
    <ul class="nav-links">
      <li><a href="home.php">Home</a></li>
      <li><a href="payments.php">Payments</a></li>
      <li><a href="maintenance.php">Maintenance</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="logout.php" class="login-btn">Logout</a></li>
    </ul>
  </nav>

  <!-- Hero Section -->
  <header class="hero">
    <div class="overlay">
      <p>Welcome <?php echo htmlspecialchars($user['name']); ?> to Bangue, a vibrant quarter full of life and opportunity.</p>
    </div>
  </header>

  <!-- Apartments Section -->
  <section class="apartments">
    <h2>Explore Apartments in Bangue</h2>
    <div class="apartment-levels">
      <div class="level">
        <img src="../img/n2.jpg" alt="Beginner Apartment">
        <h3>Starter Homes</h3>
        <p>Affordable apartments with basic amenities, perfect for first-time renters.</p>
      </div>

      <div class="level">
        <img src="../img/1.jpg" alt="Intermediate Apartment">
        <h3>Comfort Living</h3>
        <p>Spacious apartments with modern features, ideal for families and professionals.</p>
      </div>

      <div class="level">
        <img src="../img/n1.jpg" alt="Advanced Apartment">
        <h3>Luxury Residences</h3>
        <p>Premium apartments with high-end finishes, offering the best of Bangue living.</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; Bangue Apartment System — simplifying housing, payments, and maintenance for the Bangue community.</p>
  </footer>
</body>
</html>
