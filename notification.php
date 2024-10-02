<?php
// Start session
session_start();

// Database connection parameters
$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root';
$pass = '';

// Connect to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch low stock products
$low_stock_stmt = $conn->prepare("SELECT name, stock FROM products WHERE stock <= low_stock_threshold");
$low_stock_stmt->execute();
$low_stock_alerts = $low_stock_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch pending orders
$pending_orders_stmt = $conn->prepare("SELECT order_id, order_date FROM orders WHERE status = 'pending'");
$pending_orders_stmt->execute();
$pending_orders = $pending_orders_stmt->fetchAll(PDO::FETCH_ASSOC);

// System messages (you can customize or fetch them dynamically)
$system_messages = [
    "Scheduled maintenance at midnight",
    "New updates available for the system",
    "Reminder: Backup your data regularly"
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-box, .order-box, .message-box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .alert-box h3, .order-box h3, .message-box h3 {
            margin-top: 0;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        ul li:last-child {
            border-bottom: none;
        }
        .alert {
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>System Notifications</h2>

    <!-- Low Stock Alerts -->
    <div class="alert-box">
        <h3>Low Stock Alerts</h3>
        <?php if (count($low_stock_alerts) > 0): ?>
            <ul>
                <?php foreach ($low_stock_alerts as $alert): ?>
                    <li>
                        <strong><?php echo $alert['name']; ?></strong>: Only <span class="alert"><?php echo $alert['stock']; ?></span> left in stock.
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No low stock alerts at the moment.</p>
        <?php endif; ?>
    </div>

    <!-- Pending Orders -->
    <div class="order-box">
        <h3>Pending Orders</h3>
        <?php if (count($pending_orders) > 0): ?>
            <ul>
                <?php foreach ($pending_orders as $order): ?>
                    <li>
                        Order ID: <strong><?php echo $order['order_id']; ?></strong>, placed on <?php echo date('F d, Y', strtotime($order['order_date'])); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No pending orders.</p>
        <?php endif; ?>
    </div>

    <!-- System Messages -->
    <div class="message-box">
        <h3>System Messages</h3>
        <ul>
            <?php foreach ($system_messages as $message): ?>
                <li><?php echo $message; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

</body>
</html>
