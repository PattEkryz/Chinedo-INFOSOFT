<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Reports</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>📑 Billing Statements - Alexis Construction Services</h1>
            <p>Your trusted partner for quality construction services.</p>
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
        <h2>💰 Billing Report</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total Amount (₱)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalAmount = 0; 

                $reports = $conn->query("
                    SELECT 
                        Bookings.id AS booking_id, 
                        Clients.name AS client_name, 
                        Bookings.date, 
                        COALESCE(SUM(Services.hourly_rate * BookingServices.hours), 0) AS total_amount
                    FROM Bookings
                    JOIN Clients ON Bookings.client_id = Clients.id
                    LEFT JOIN BookingServices ON Bookings.id = BookingServices.booking_id
                    LEFT JOIN Services ON BookingServices.service_id = Services.id
                    GROUP BY Bookings.id
                    ORDER BY Bookings.date DESC
                ");

                if ($reports->rowCount() > 0) {
                    while ($report = $reports->fetch(PDO::FETCH_ASSOC)) {
                        $totalAmount += $report['total_amount']; 
                        echo "<tr>
                                <td>{$report['booking_id']}</td>
                                <td>{$report['client_name']}</td>
                                <td>{$report['date']}</td>
                                <td>₱" . number_format($report['total_amount'], 2) . "</td>
                              </tr>";
                    }
                   
                    echo "<tr>
                            <td colspan='3'><strong>Grand Total:</strong></td>
                            <td><strong>₱" . number_format($totalAmount, 2) . "</strong></td>
                          </tr>";
                } else {
                    echo "<tr><td colspan='4'>No billing records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
