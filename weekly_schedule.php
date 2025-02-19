<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Schedule</title>
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
        <h1>📅 Weekly Schedule</h1>
        <table border="1">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Services</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $schedule = $conn->query("
                    SELECT 
                        Bookings.id, 
                        Bookings.date, 
                        Clients.name AS client_name, 
                        COALESCE(GROUP_CONCAT(Services.name SEPARATOR ', '), 'No services') AS services
                    FROM Bookings
                    JOIN Clients ON Bookings.client_id = Clients.id
                    LEFT JOIN BookingServices ON Bookings.id = BookingServices.booking_id
                    LEFT JOIN Services ON BookingServices.service_id = Services.id
                    WHERE Bookings.date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                    GROUP BY Bookings.id
                    ORDER BY Bookings.date ASC
                ");

                if ($schedule->rowCount() > 0) {
                    while ($row = $schedule->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>{$row['date']}</td>
                                <td>{$row['client_name']}</td>
                                <td>{$row['services']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No bookings for this week.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
