<?php
$conn = new mysqli("localhost", "root", "", "bangue");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

// Fetch maintenance record
$sql = "SELECT maintenance_date, description FROM maintenance WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$maintenance = $result->fetch_assoc();

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maintenance_date = $_POST['maintenance_date'];
    $description = $_POST['description'];

    $sql = "UPDATE maintenance SET maintenance_date=?, description=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $maintenance_date, $description, $id);
    $stmt->execute();

    header("Location: manage_maintenance.php?msg=Maintenance+record+updated+successfully");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Maintenance</title>
    <link rel="stylesheet" href="../css/update_maintenance.css">
</head>
<body>
<div class="update-container">
    <h2>Edit Maintenance Record</h2>
    <form method="POST">
        <label for="maintenance_date">Date:</label>
        <input type="date" id="maintenance_date" name="maintenance_date" value="<?= htmlspecialchars($maintenance['maintenance_date']) ?>" required>

        <label for="description">Description:</label>
        <input type="text" id="description" name="description" value="<?= htmlspecialchars($maintenance['description']) ?>">

        <button type="submit">Save Changes</button>
    </form>
</div>
</body>
</html>
