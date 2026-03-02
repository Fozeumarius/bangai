<?php
session_start();

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "bangue");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name      = trim($_POST['name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $apartment = trim($_POST['apartment']);
    $username  = trim($_POST['username']);
    $password  = $_POST['password'];

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert all fields into database
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, apartment, username, password) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $phone, $apartment, $username, $hashedPassword);

    if ($stmt->execute()) {
        // ✅ Log the user in immediately
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['username'] = $username;

        // ✅ Redirect straight to homepage
        header("Location: homepage.php");
        exit();
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="../css/sign.css">
</head>
<body>
  <section class="signup-container">
    <div class="signup-image">
      <img src="../img/n2.jpg" alt="Apartment View">
    </div>

    <div class="signup-form">
      <h2>Create Account</h2>
      <form method="post" action="">
        <label for="name">Full Name</label>
        <input type="text" name="name" required>

        <label for="email">Email</label>
        <input type="email" name="email" required>

        <label for="phone">Phone</label>
        <input type="text" name="phone" required>

        <label for="apartment">Apartment Number</label>
        <input type="text" name="apartment" required>

        <label for="username">Username</label>
        <input type="text" name="username" required>

        <label for="password">Password</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn">Sign Up</button>
      </form>
      <p style="color:green;"><?php echo $message; ?></p>
      <p class="login-text">Already have an account? <a href="login.php">Login</a></p>
    </div>
  </section>
</body>
</html>
