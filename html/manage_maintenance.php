<?php
// connect to database
$conn = new mysqli("localhost", "root", "", "bangue");

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// fetch all maintenance requests
$result = $conn->query("SELECT id, user_id, request, request_date, status FROM maintenance");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Maintenance</title>
    <link rel="stylesheet" href="../css/manage_maintenance.css"> <!-- dedicated CSS -->
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_payment.php">Manage Payments</a></li>
            <li><a href="manage_maintenance.php">Manage Maintenance</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h2>Manage Maintenance Requests</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Request</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['user_id'] ?></td>
                <td><?= $row['request'] ?></td>
                <td><?= $row['request_date'] ?></td>
                <td><?= $row['status'] ?></td>
                <td>
                    <a href="edit_maintenance.php?id=<?= $row['id'] ?>">Edit</a> |
                    <a href="delete_maintenance.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this request?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>
