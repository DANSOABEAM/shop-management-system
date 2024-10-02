<?php
// Database connection
$host = 'localhost';
$db = 'shop_managemen'; // Ensure this matches your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password
$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle form submission for adding a new sale
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer'];
    $total_amount = 0;
    $sale_date = $_POST['sale_date'];

    // Insert the sale into the sales table first
    $stmt = $conn->prepare("INSERT INTO sales (customer_id, total_amount, sale_date) VALUES (:customer_id, :total_amount, :sale_date)");
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->bindParam(':total_amount', $total_amount);
    $stmt->bindParam(':sale_date', $sale_date);
    $stmt->execute();

    $last_sale_id = $conn->lastInsertId(); // Get last inserted sale ID for the receipt

    foreach ($_POST['product'] as $key => $product_id) {
        $quantity = $_POST['quantity'][$key];
        $sale_price = $_POST['sale_price'][$key];
        $item_total_price = $quantity * $sale_price; // Calculate total price for the item
        $total_amount += $item_total_price; // Update total amount for the sale

        // Insert sale items into the sale_items table
        $item_stmt = $conn->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, sale_price, price) VALUES (:sale_id, :product_id, :quantity, :sale_price, :price)");
        $item_stmt->bindParam(':sale_id', $last_sale_id);
        $item_stmt->bindParam(':product_id', $product_id);
        $item_stmt->bindParam(':quantity', $quantity);
        $item_stmt->bindParam(':sale_price', $sale_price);
        $item_stmt->bindParam(':price', $item_total_price); // Use calculated price
        $item_stmt->execute();
    }
    
    // Update total amount in sales table
    $update_stmt = $conn->prepare("UPDATE sales SET total_amount = :total_amount WHERE sale_id = :sale_id");
    $update_stmt->bindParam(':total_amount', $total_amount);
    $update_stmt->bindParam(':sale_id', $last_sale_id);
    $update_stmt->execute();

    $success = "Sale recorded successfully!";
}

// Delete transaction
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_stmt = $conn->prepare("DELETE FROM sales WHERE sale_id = :sale_id");
    $delete_stmt->bindParam(':sale_id', $delete_id);
    $delete_stmt->execute();
    header("Location: sales_transactions.php");
    exit;
}

// Retrieve sales transactions
$sales = $conn->query("SELECT s.sale_id, c.customer_name, s.total_amount, s.sale_date 
                        FROM sales s 
                        JOIN customers c ON s.customer_id = c.customer_id")->fetchAll(PDO::FETCH_ASSOC);

// Retrieve products for dropdown
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Handle receipt printing
if (isset($_GET['print_receipt'])) {
    $sale_id = $_GET['print_receipt'];

    // Retrieve sale information
    $sale_stmt = $conn->prepare("SELECT s.sale_id, s.sale_date, c.customer_name, s.total_amount
                                  FROM sales s
                                  JOIN customers c ON s.customer_id = c.customer_id
                                  WHERE s.sale_id = :sale_id");
    $sale_stmt->bindParam(':sale_id', $sale_id);
    $sale_stmt->execute();
    $sale = $sale_stmt->fetch(PDO::FETCH_ASSOC);

    // Retrieve sale items
    $items_stmt = $conn->prepare("SELECT p.product_name, si.quantity, si.sale_price, si.price
                                   FROM sale_items si
                                   JOIN products p ON si.product_id = p.product_id
                                   WHERE si.sale_id = :sale_id");
    $items_stmt->bindParam(':sale_id', $sale_id);
    $items_stmt->execute();
    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Print receipt view
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Receipt</title>
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            .receipt {
                width: 300px;
                margin: auto;
                border: 1px solid #000;
                padding: 10px;
            }
            h2 {
                text-align: center;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            table, th, td {
                border: 1px solid #000;
            }
            th, td {
                padding: 8px;
                text-align: left;
            }
            .total {
                font-weight: bold;
            }
            .signature {
                margin-top: 20px;
                text-align: center;
            }
        </style>
    </head>
    <body onload="window.print()">
        <div class="receipt">
            <h2>Receipt</h2>
            <p>Date: <?php echo $sale['sale_date']; ?></p>
            <p>Customer: <?php echo $sale['customer_name']; ?></p>
            <p>Sale ID: <?php echo $sale['sale_id']; ?></p>

            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) { ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item['price'], 2); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <p class="total">Total Amount: <?php echo number_format($sale['total_amount'], 2); ?></p>
            <div class="signature">_____________________<br>Signature</div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Transactions</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .success {
            color: green;
        }
        .items {
            margin-top: 20px;
        }
        .item {
            display: flex;
            justify-content: space-between;
        }
        .item select, .item input {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sales Transactions</h1>
        <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
        
        <form method="POST">
            <h2>Add New Sale</h2>
            <div class="items">
                <div class="item">
                    <select name="customer" required>
                        <option value="">Select Customer</option>
                        <?php
                        $customers = $conn->query("SELECT * FROM customers")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($customers as $customer) {
                            echo "<option value='{$customer['customer_id']}'>{$customer['customer_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="item">
                    <select name="product[]" required>
                        <option value="">Select Product</option>
                        <?php foreach ($products as $product) {
                            echo "<option value='{$product['product_id']}'>{$product['product_name']}</option>";
                        } ?>
                    </select>
                    <input type="number" name="quantity[]" placeholder="Quantity" required>
                    <input type="text" name="sale_price[]" placeholder="Sale Price" required>
                </div>
                <button type="button" onclick="addItem()">Add Item</button>
            </div>
            <button type="submit">Submit Sale</button>
        </form>

        <h2>Sales Records</h2>
        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Sale Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale) { ?>
                <tr>
                    <td><?php echo $sale['sale_id']; ?></td>
                    <td><?php echo $sale['customer_name']; ?></td>
                    <td><?php echo number_format($sale['total_amount'], 2); ?></td>
                    <td><?php echo $sale['sale_date']; ?></td>
                    <td>
                        <a href="sales_transactions.php?print_receipt=<?php echo $sale['sale_id']; ?>">Print Receipt</a>
                        <a href="sales_transactions.php?delete_id=<?php echo $sale['sale_id']; ?>" onclick="return confirm('Are you sure you want to delete this sale?');">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        function addItem() {
            const itemsContainer = document.querySelector('.items');
            const newItem = document.createElement('div');
            newItem.classList.add('item');
            newItem.innerHTML = `
                <select name="product[]" required>
                    <option value="">Select Product</option>
                    <?php foreach ($products as $product) {
                        echo "<option value='{$product['product_id']}'>{$product['product_name']}</option>";
                    } ?>
                </select>
                <input type="number" name="quantity[]" placeholder="Quantity" required>
                <input type="text" name="sale_price[]" placeholder="Sale Price" required>
            `;
            itemsContainer.appendChild(newItem);
        }
    </script>
</body>
</html>
