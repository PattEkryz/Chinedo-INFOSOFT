<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services | Alexis Construction</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        
        h1, h2 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        .success-message {
            color: green;
            text-align: center;
            font-weight: bold;
        }
        .error-message {
            color: red;
            text-align: center;
            font-weight: bold;
        }
        .action-links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 5px;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1>Service Management - Alexis Construction</h1>
        <p>Your one-stop solution for all construction needs.</p>
    </div>
</header>

<nav>
    <div class="container">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="clients.php">Clients</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="bookings.php">Bookings</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <li><a href="payments.php">Payments</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="weekly_schedule.php">Weekly Schedule</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2>➕ Add a New Service</h2>

    <form method="POST" action="">
        <label for="name">Service Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="hourly_rate">Hourly Rate (₱):</label>
        <input type="number" step="0.01" name="hourly_rate" id="hourly_rate" required>

        <button type="submit" name="add_service">✅ Add Service</button>
    </form>

    <?php
    if (isset($_POST['add_service'])) {
        $name = $_POST['name'];
        $hourly_rate = $_POST['hourly_rate'];

        if (!empty($name) && $hourly_rate > 0) {
            $stmt = $conn->prepare("INSERT INTO Services (name, hourly_rate) VALUES (?, ?)");
            $stmt->execute([$name, $hourly_rate]);
            echo "<p class='success-message'>✅ Service '$name' added successfully at ₱" . number_format($hourly_rate, 2) . " per hour!</p>";
        } else {
            echo "<p class='error-message'>⚠️ Please enter a valid service name and hourly rate.</p>";
        }
    }
    ?>

    <h2>📋 List of Services</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Service Name</th>
            <th>Hourly Rate (₱)</th>
            <th>Actions</th>
        </tr>
        <?php
        $stmt = $conn->query("SELECT * FROM Services ORDER BY id DESC");
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>₱" . number_format($row['hourly_rate'], 2) . "</td>
                        <td class='action-links'>
                            <a href='edit_service.php?id={$row['id']}'>✏ Edit</a> | 
                            <a href='delete_service.php?id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this service?\")'>🗑 Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No services available.</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
