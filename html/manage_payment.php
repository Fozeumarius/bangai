<?php
// connect to database
$conn = new mysqli("localhost", "root", "", "bangue");

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// fetch all users (since you only want name + email)
$query = "SELECT id, username, email FROM users";
$result = $conn->query($query);

if (!empty($_GET['msg'])) {
    echo "<p style='color:green'>" . htmlspecialchars($_GET['msg']) . "</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Payments</title>
    <link rel="stylesheet" href="../css/manage_payments.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .main-content {
            margin-left: 220px; /* espace pour la sidebar */
            padding: 20px;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        }
        .card img {
            height: 120px;
            width: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #2c3e50;
        }
        .card h3 {
            margin: 10px 0 5px;
            font-size: 18px;
            color: #333;
        }
        .card p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        .card button {
            margin-top: 15px;
            padding: 10px 16px;
            background: #007bff;
            color: white;
            font-size: 14px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .card button:hover {
            background: #0056b3;
        }
    </style>
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
            <li><a href="reports.php">Manage Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h2>Manage Payments</h2>
        <div class="card-container">
            <?php while($row = $result->fetch_assoc()) { ?>
                <div class="card">
                    <!-- Image par défaut -->
                    <img src="../img/icon.jpg" alt="Default User">
                    <h3><?= htmlspecialchars($row['username']) ?></h3>
                    <p><?= htmlspecialchars($row['email']) ?></p>
                    <!-- Bouton Add Payment -->
                    <form action="user_payments.php" method="get">
                       <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                       <button type="submit">Add Payment</button>
                    </form>

                </div>
            <?php } ?>
        </div>
    </div>

</body>
</html>
