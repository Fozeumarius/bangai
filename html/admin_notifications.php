<?php
include __DIR__ . '/db_connect.php';

// Fetch admin notifications
$query = "SELECT * FROM admin_notifications ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Notifications</title>
    <link rel="stylesheet" href="../css/admin_not.css">
</head>
<body>
    <h2>Admin Notifications</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="admin-alert <?php echo $row['type']; ?>">
                <?php echo htmlspecialchars($row['message']); ?>
                <span class="date"><?php echo $row['created_at']; ?></span>

                <?php if ($row['is_read'] == 0): ?>
                    <form method="post" action="mark_read.php" style="display:inline;">
                        <input type="hidden" name="notif_id" value="<?php echo $row['id']; ?>">
                        <button type="submit">Mark as Read</button>
                    </form>
                <?php else: ?>
                    <span class="status">✔ Read</span>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notifications found.</p>
    <?php endif; ?>
</body>
</html>
