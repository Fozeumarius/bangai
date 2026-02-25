<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bangue");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit a request.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id      = $_SESSION['user_id']; // ✅ valid user id from session
    $issueTitle   = $_POST['issueTitle'];
    $issueDesc    = $_POST['issueDesc'];
    $apartmentNum = $_POST['apartmentNum'];
    $urgency      = $_POST['urgency'];
    $date         = date("Y-m-d");
    $status       = "Pending";

    $stmt = $conn->prepare("INSERT INTO maintenance (user_id, request, description, apartment, urgency, request_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississs", $user_id, $issueTitle, $issueDesc, $apartmentNum, $urgency, $date, $status);

    if ($stmt->execute()) {
    echo "<p style='color:green; text-align:center;'>Request submitted successfully!</p>";
} else {
    echo "Error: " . $stmt->error;
}

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Maintenance Requests</title>
  <link rel="stylesheet" href="../css/maintenance.css">
</head>
<body>
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

  <header class="page-header">
    <h1>MAINTENANCE REQUEST</h1>
    <p>Submit issues and track their progress.</p>
  </header>

  <section class="request-form">
    <h2>Submit a New Request</h2>
    <form method="POST" action="maintenance.php">
      <label>Problem :</label>
      <input type="text" name="issueTitle" required>

      <label>Description:</label>
      <textarea name="issueDesc" required></textarea>

      <label>Apartment Number:</label>
      <input type="text" name="apartmentNum" required>

      <label>Urgency:</label>
      <select name="urgency">
        <option>Low</option>
        <option>Medium</option>
        <option>High</option>
      </select>

      <button type="submit">Submit Request</button>
    </form>
  </section>
</body>
</html>
