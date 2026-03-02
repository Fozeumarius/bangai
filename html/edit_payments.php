<?php
$conn = new mysqli("localhost", "root", "", "bangue");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$id = $_GET['id'] ?? null;
if (!$id) { echo "No payment selected."; exit(); }

// Fetch payment
$result = $conn->query("SELECT * FROM payments WHERE id=$id");
$payment = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountName = $_POST['account_name'];
    $accountNumber = $_POST['account_number'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];
    $dueDate = $_POST['due_date'];

    $stmt = $conn->prepare("UPDATE payments SET account_name=?, account_number=?, amount=?, status=?, due_date=? WHERE id=?");
    $stmt->bind_param("sssdsi", $accountName, $accountNumber, $amount, $status, $dueDate, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: user_payments.php?user_id=" . $payment['user_id'] . "&msg=updated");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/edit_payments.css">
    <title>Edit Payment</title>
</head>
<body>
    <h2>Edit Payment #<?= htmlspecialchars($payment['id']) ?></h2>
    <form method="post">
        <label>Account Name:</label>
        <input type="text" name="account_name" value="<?= htmlspecialchars($payment['account_name']) ?>" required><br>

        <label>Account Number:</label>
        <input type="text" name="account_number" value="<?= htmlspecialchars($payment['account_number']) ?>" required><br>

        <label>Amount (FCFA):</label>
        <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($payment['amount']) ?>" required><br>

        <label>Status:</label>
        <select name="status">
            <option <?= $payment['status']=="Completed"?"selected":"" ?>>Completed</option>
            <option <?= $payment['status']=="Pending"?"selected":"" ?>>Pending</option>
            <option <?= $payment['status']=="Unpaid"?"selected":"" ?>>Unpaid</option>
        </select><br>

        <label>Next Due Date:</label>
        <input type="date" name="due_date" value="<?= htmlspecialchars($payment['due_date']) ?>"><br>

        <button type="submit" style="background:#27ae60; color:#fff; padding:8px 16px; border:none; border-radius:4px;">Save Changes</button>
    </form>
</body>
</html>
