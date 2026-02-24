<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "bangue");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM users WHERE ADMIN = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify password (assuming hashed in DB)
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;

            // Redirect based on who is logging in
            // Replace "admin1" with your actual manager username or email
            if ($username === "admin1" || $row['email'] === "admin@gmail.com") {
                header("Location: dashboard.php");
            } else {
                header("Location: homepage.php");
            }
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <!-- Login Section -->
  <section class="login-container">
    <!-- Left Side: Image -->
    <div class="login-image">
      <img src="../img/1.jpg" alt="Apartment View">
    </div>

    <!-- Right Side: Login Form -->
    <div class="login-form">
      <h2>Welcome Back</h2>
      <form method="post" action="">
        <label for="username">Username</label>
        <input type="text" name="username" required>

        <label for="password">Password</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn">Login</button>
      </form>

      <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

      <!-- Integrated Sign Up link -->
      <p class="signup-text">
        Don’t have an account? <a href="sign_in.php">Sign up here</a>
      </p>
    </div>
  </section>
</body>
</html>
