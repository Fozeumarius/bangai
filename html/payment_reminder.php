<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bangue");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Today’s date
$today = date('Y-m-d');

// Check payments with due dates within 5, 3, or 1 day
$query = "
    SELECT p.id, p.user_id, p.due_date, u.username, u.email
    FROM payments p
    JOIN users u ON p.user_id = u.id
    WHERE DATE(p.due_date) IN (
        DATE_ADD('$today', INTERVAL 5 DAY),
        DATE_ADD('$today', INTERVAL 3 DAY),
        DATE_ADD('$today', INTERVAL 1 DAY)
    )
";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $userId   = $row['user_id'];
    $username = $row['username'];
    $email    = $row['email'];
    $dueDate  = $row['due_date'];

    // Determine how many days left
    $daysLeft = (strtotime($dueDate) - strtotime($today)) / 86400;

    // Message
    $message = "Reminder: Your payment is due on " . date('F j, Y', strtotime($dueDate)) . 
               " (in $daysLeft day" . ($daysLeft > 1 ? "s" : "") . ").";

    // Insert notification
    $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'warning')");
    $notifStmt->bind_param("is", $userId, $message);
    $notifStmt->execute();
    $notifStmt->close();

    // Send email
    $subject = "Payment Reminder - Due in $daysLeft day" . ($daysLeft > 1 ? "s" : "");
    $body    = "Dear $username,\n\n" .
               "This is a friendly reminder that your next payment is due on " . date('F j, Y', strtotime($dueDate)) . ".\n" .
               "Please ensure funds are ready.\n\n" .
               "Best regards,\nYour Management Team";

    $headers = "From: no-reply@bangue.com\r\n" .
               "Reply-To: no-reply@bangue.com\r\n" .
               "X-Mailer: PHP/" . phpversion();

    mail($email, $subject, $body, $headers);
}

$conn->close();
?>
