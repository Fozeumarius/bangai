<?php
include __DIR__ . '/../db_connect.php';

if (isset($_POST['notif_id'])) {
    $notifId = (int)$_POST['notif_id'];
    $stmt = $conn->prepare("UPDATE admin_notifications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $notifId);
    $stmt->execute();
    $stmt->close();
}

header("Location: admin_notifications.php");
exit();
?>
