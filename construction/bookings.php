<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings</title>
    <link rel="stylesheet" href="bookings.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>📅 Booking Management - Alexis Construction Services</h1>
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
        <h2>📌 New Booking</h2>
        <form method="POST" action="">
            <label for="client_id">Client:</label>
            <select name="client_id" id="client_id" required>
                <?php
                $clients = $conn->query("SELECT * FROM Clients");
                while ($client = $clients->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$client['id']}'>{$client['name']}</option>";
                }
                ?>
            </select>

            <label for="date">Select Date:</label>
            <input type="date" name="date" id="date" required>

            <h3>🛠️ Select Services</h3>
            <div class="services-list">
                <?php
                $services = $conn->query("SELECT * FROM Services");
                while ($service = $services->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='service-item'>
                            <input type='checkbox' name='service_id[]' value='{$service['id']}'> 
                            <span>{$service['name']} (₱{$service['hourly_rate']}/hr)</span>
                            <input type='number' name='hours[]' min='1' value='1' class='hours-input'>
                          </div>";
                }
                ?>
            </div>

            <button type="submit" name="add_booking">📌 Book Now</button>
        </form>

        <?php
        if (isset($_POST['add_booking'])) {
            $client_id = $_POST['client_id'];
            $date = $_POST['date'];

            // Check if date is already booked
            $existing_booking = $conn->prepare("SELECT * FROM Bookings WHERE date = ?");
            $existing_booking->execute([$date]);

            if ($existing_booking->fetch()) {
                echo "<p class='error-message'>⚠️ Date already booked! Please choose another date.</p>";
            } else {
                // Insert booking
                $stmt = $conn->prepare("INSERT INTO Bookings (client_id, date) VALUES (?, ?)");
                $stmt->execute([$client_id, $date]);
                $booking_id = $conn->lastInsertId();

                // ✅ Check if services are selected before looping
                if (isset($_POST['service_id']) && is_array($_POST['service_id'])) {
                    foreach ($_POST['service_id'] as $key => $service_id) {
                        $hours = $_POST['hours'][$key];
                        $stmt = $conn->prepare("INSERT INTO BookingServices (booking_id, service_id, hours) VALUES (?, ?, ?)");
                        $stmt->execute([$booking_id, $service_id, $hours]);
                    }
                }

                echo "<p class='success-message'>✅ Booking created successfully!</p>";
            }
        }
        ?>

        <h2>📋 Booking List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total Amount (₱)</th>
                    <th>Action</th> <!-- Added column for cancel button -->
                </tr>
            </thead>
            <tbody>
                <?php
                $bookings = $conn->query("
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

                if ($bookings->rowCount() > 0) {
                    while ($booking = $bookings->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>{$booking['booking_id']}</td>
                                <td>{$booking['client_name']}</td>
                                <td>{$booking['date']}</td>
                                <td>₱" . number_format($booking['total_amount'], 2) . "</td>
                                <td>
                                    <form method='POST' action='' onsubmit='return confirm(\"Are you sure you want to cancel this booking?\")'>
                                        <input type='hidden' name='booking_id' value='{$booking['booking_id']}'>
                                        <button type='submit' name='cancel_booking' class='cancel-btn'>❌ Cancel</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No bookings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php
        // Handle cancel booking
        if (isset($_POST['cancel_booking'])) {
            $booking_id = $_POST['booking_id'];

            try {
                // Start transaction
                $conn->beginTransaction();

                // Step 1: Delete payments linked to the booking
                $stmt = $conn->prepare("DELETE FROM Payments WHERE booking_id = ?");
                $stmt->execute([$booking_id]);

                // Step 2: Delete associated services
                $stmt = $conn->prepare("DELETE FROM BookingServices WHERE booking_id = ?");
                $stmt->execute([$booking_id]);

                // Step 3: Delete the booking itself
                $stmt = $conn->prepare("DELETE FROM Bookings WHERE id = ?");
                $stmt->execute([$booking_id]);

                // Commit transaction
                $conn->commit();

                echo "<p class='success-message'>❌ Booking and related records canceled successfully!</p>";
            } catch (PDOException $e) {
                // Rollback transaction if an error occurs
                $conn->rollBack();
                echo "<p class='error-message'>⚠️ Error canceling booking: " . $e->getMessage() . "</p>";
            }
        }
        ?>

    </div>
</body>
</html>
