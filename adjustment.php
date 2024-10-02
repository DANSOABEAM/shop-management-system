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

// Fetch all products for the stock adjustment dropdown
$product_stmt = $conn->prepare("SELECT * FROM products");
$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle stock adjustment form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product'];
    $adjustment = $_POST['adjustment'];
    $reason = $_POST['reason'];

    if (!empty($product_id) && !empty($adjustment)) {
        // Get the current stock level for the selected product
        $current_stock_stmt = $conn->prepare("SELECT stock FROM products WHERE id = :product_id");
        $current_stock_stmt->bindParam(':product_id', $product_id);
        $current_stock_stmt->execute();
        $current_stock = $current_stock_stmt->fetch(PDO::FETCH_ASSOC)['stock'];

        // Update stock level
        $new_stock = $current_stock + $adjustment;
        $update_stock_stmt = $conn->prepare("UPDATE products SET stock = :new_stock WHERE id = :product_id");
        $update_stock_stmt->bindParam(':new_stock', $new_stock);
        $update_stock_stmt->bindParam(':product_id', $product_id);

        if ($update_stock_stmt->execute()) {
            $success = "Stock updated successfully!";
        } else {
            $error = "Failed to update stock.";
        }
    } else {
        $error = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Adjustment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin-bottom: 10px;
        }
        .form-container select, .form-container input, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container button {
            padding: 10px 20px;
            background-color: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Stock Adjustment</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php elseif (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="product">Product:</label>
        <select id="product" name="product" required>
            <option value="">Select a product</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="adjustment">Adjustment Quantity:</label>
        <input type="number" id="adjustment" name="adjustment" required>

        <label for="reason">Reason for Adjustment (optional):</label>
        <textarea id="reason" name="reason" rows="3"></textarea>

        <button type="submit">Update Stock</button>
    </form>
</div>

</body>
</html>
