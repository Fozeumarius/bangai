<?php
$conn = new mysqli("localhost", "root", "", "bangue");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $sql = "DELETE FROM maintenance WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back with success message
        header("Location: manage_maintenance.php?msg=Maintenance+record+deleted+successfully");
        exit();
    } else {
        echo "Error deleting maintenance record: " . $conn->error;
    }
} else {
    echo "Invalid maintenance ID.";
}
?>
