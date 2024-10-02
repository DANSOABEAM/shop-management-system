<?php
// Database connection
$host = 'localhost';
$dbname = 'shop_managemen'; // Make sure this matches your database name
$username = 'root'; // Your database username
$password = ''; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Fetch products and categories
$sql = "SELECT p.id, p.name AS name, p.stock_quantity, p.reorder_level, c.name AS category
        FROM products p 
        LEFT JOIN categories c ON p.id = c.id";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory List</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;

        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
            text-align: center;
            font-size:14px;
        }
        th {
           
        }
        td:hover {
            background-color: #f1f1f1;
        }
        .alert {
            color: red;
            font-weight: bold;
        }

        thead{
           
            color:white;
          
            background-color: rgba(255, 0, 0, 0.711);
        }
    </style>
</head>
<body>

<h2>Inventory List</h2>

<table>
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Stock Quantity</th>
            <th>Reorder Level</th>
            <th>Category</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['stock_quantity']); ?></td>
                    <td><?php echo htmlspecialchars($product['reorder_level']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="alert">No products found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
