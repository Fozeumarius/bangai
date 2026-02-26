<?php
$conn = new mysqli("localhost", "root", "", "bangue");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $sql = "DELETE FROM payments WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back with success message
        header("Location: manage_payment.php?msg=Payment+deleted+successfully");
        exit();
    } else {
        echo "Error deleting payment: " . $conn->error;
    }
} else {
    echo "Invalid payment ID.";
}
?>
