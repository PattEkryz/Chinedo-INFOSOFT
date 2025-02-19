<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments | Alexis Construction Services</title>
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
        input, select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }
        button {
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #218838;
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
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1>Payment Management - Alexis Construction Services</h1>
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
    <h2>💰 Process a Payment</h2>
    
    <form method="POST" action="">
        <label for="booking_id">Select Booking:</label>
        <select name="booking_id" id="booking_id" required>
            <option value="">-- Select Booking --</option>
            <?php
            $bookings = $conn->query("SELECT id, date FROM Bookings ORDER BY date DESC");
            while ($booking = $bookings->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$booking['id']}'>Booking #{$booking['id']} - Date: {$booking['date']}</option>";
            }
            ?>
        </select>

        <label for="amount_paid">Amount Paid (₱):</label>
        <input type="number" step="0.01" name="amount_paid" id="amount_paid" required>

        <label for="payment_date">Payment Date:</label>
        <input type="date" name="payment_date" id="payment_date" required>

        <button type="submit" name="process_payment">✅ Process Payment</button>
    </form>

    <?php
    if (isset($_POST['process_payment'])) {
        $booking_id = $_POST['booking_id'];
        $amount_paid = $_POST['amount_paid'];
        $payment_date = $_POST['payment_date'];

        if ($booking_id && $amount_paid > 0) {
            $stmt = $conn->prepare("INSERT INTO Payments (booking_id, amount_paid, payment_date) VALUES (?, ?, ?)");
            $stmt->execute([$booking_id, $amount_paid, $payment_date]);
            echo "<p class='success-message'>✅ Payment of ₱" . number_format($amount_paid, 2) . " processed successfully for Booking #$booking_id!</p>";
        } else {
            echo "<p class='error-message'>⚠️ Please fill out all fields correctly!</p>";
        }
    }
    ?>

    <h2>📋 Payment Records</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Booking ID</th>
            <th>Amount Paid (₱)</th>
            <th>Payment Date</th>
        </tr>
        <?php
        $payments = $conn->query("SELECT * FROM Payments ORDER BY payment_date DESC");
        if ($payments->rowCount() > 0) {
            while ($payment = $payments->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>{$payment['id']}</td>
                        <td>{$payment['booking_id']}</td>
                        <td>₱" . number_format($payment['amount_paid'], 2) . "</td>
                        <td>{$payment['payment_date']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No payments recorded yet.</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
