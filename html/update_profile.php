<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "bangue");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
// Assume logged-in user ID is stored in session
$userId = $_SESSION['user_id'] ?? 1; // fallback to 1 for testing

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data safely
    $name    = $_POST['name'] ?? '';
    $email   = $_POST['email'] ?? '';
    $phone   = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    // Update query with prepared statement
    $sql = "UPDATE users SET name=?, email=?, phone=?, address=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $phone, $address, $userId);

    if ($stmt->execute()) {
        // Redirect back to profile with success message
        $_SESSION['profile_message'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>
