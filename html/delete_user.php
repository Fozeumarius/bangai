<?php
$conn = new mysqli("localhost", "root", "", "bangue");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // delete payments linked to this user
    $conn->query("DELETE FROM payments WHERE user_id = $id");

    // delete maintenance linked to this user
    $conn->query("DELETE FROM maintenance WHERE user_id = $id");

    // now delete the user
    if ($conn->query("DELETE FROM users WHERE id = $id") === TRUE) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}
$conn->close();
?>
