<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="inventory.css">
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
        <h2>🛠️ Inventory Management</h2>
        
        <div class="form-container">
            <h3>➕ Add New Tool</h3>
            <form method="POST" action="">
                <label for="service_id">Select Service</label>
                <select name="service_id" id="service_id" required>
                    <?php
                    $services = $conn->query("SELECT * FROM Services");
                    while ($service = $services->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$service['id']}'>{$service['name']}</option>";
                    }
                    ?>
                </select>

                <label for="tool_name">Tool Name</label>
                <input type="text" name="tool_name" id="tool_name" placeholder="Enter tool name" required>

                <button type="submit" name="add_tool">✅ Add Tool</button>
            </form>
        </div>

        <?php
        // Handle new tool addition
        if (isset($_POST['add_tool'])) {
            $service_id = $_POST['service_id'];
            $tool_name = $_POST['tool_name'];

            $stmt = $conn->prepare("INSERT INTO Inventory (service_id, tool_name) VALUES (?, ?)");
            if ($stmt->execute([$service_id, $tool_name])) {
                echo "<p class='success-message'>✅ Tool added successfully!</p>";
            } else {
                echo "<p class='error-message'>❌ Error adding tool. Try again.</p>";
            }
        }

        // Handle delete action
        if (isset($_POST['delete_tool'])) {
            $tool_id = $_POST['tool_id'];

            $stmt = $conn->prepare("DELETE FROM Inventory WHERE id = ?");
            if ($stmt->execute([$tool_id])) {
                echo "<p class='success-message'>🗑️ Tool deleted successfully!</p>";
            } else {
                echo "<p class='error-message'>❌ Error deleting tool. Try again.</p>";
            }
        }
        ?>

        <!-- Inventory List Table -->
        <h3>📋 Inventory List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Service</th>
                    <th>Tool Name</th>
                    <th>Action</th> <!-- New column for delete button -->
                </tr>
            </thead>
            <tbody>
                <?php
                $inventory = $conn->query("
                    SELECT Inventory.id, Services.name AS service_name, Inventory.tool_name
                    FROM Inventory
                    JOIN Services ON Inventory.service_id = Services.id
                ");
                while ($item = $inventory->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                            <td>{$item['id']}</td>
                            <td>{$item['service_name']}</td>
                            <td>{$item['tool_name']}</td>
                            <td>
                                <form method='POST' action='' onsubmit='return confirm(\"Are you sure you want to delete this tool?\");'>
                                    <input type='hidden' name='tool_id' value='{$item['id']}'>
                                    <button type='submit' name='delete_tool' class='delete-btn'>🗑️ Delete</button>
                                </form>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
