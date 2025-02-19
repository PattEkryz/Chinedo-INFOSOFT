<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Welcome to Alexis Construction Services</h1>
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
        <h2>Client Management</h2>

        <!-- Form Section -->
        <div class="form-container">
            <h3>Add New Client</h3>
            <form method="POST" action="">
                <label for="name">Client Name</label>
                <input type="text" name="name" id="name" required>

                <label for="contact_info">Contact Info</label>
                <input type="text" name="contact_info" id="contact_info" required>

                <button type="submit" name="add_client">➕ Add Client</button>
            </form>
        </div>

        <?php
        if (isset($_POST['add_client'])) {
            $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
            $contact_info = htmlspecialchars($_POST['contact_info'], ENT_QUOTES, 'UTF-8');

            $stmt = $conn->prepare("INSERT INTO Clients (name, contact_info) VALUES (?, ?)");
            $stmt->execute([$name, $contact_info]);

            echo "<p class='success-message'>✅ Client added successfully!</p>";
        }
        ?>

        <!-- Client List Table -->
        <h3>📋 Client List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client Name</th>
                    <th>Contact Info</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->query("SELECT * FROM Clients");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['contact_info']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>