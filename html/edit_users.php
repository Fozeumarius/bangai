<?php
// connect to database
$conn = new mysqli("localhost", "root", "", "bangue");

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// get user id from URL
if (!isset($_GET['id'])) {
    die("No user ID provided.");
}
$id = intval($_GET['id']);

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $role     = $_POST['role'];

    // update user with prepared statement
    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $username, $email, $role, $id);

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error updating user: " . $conn->error;
    }
}

// fetch user data
$stmt = $conn->prepare("SELECT username, email, role FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/edit_users.css">
</head>
<body>
    <div class="main-content">
        <h2>Edit User</h2>
        <form method="post">
            <label>Username:</label><br>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

            <label>Role:</label><br>
            <input type="text" name="role" value="<?= htmlspecialchars($user['role']) ?>" required><br><br>

            <button type="submit">Update</button>
            <a href="manage_users.php">Cancel</a>
        </form>
    </div>
</body>
</html>
