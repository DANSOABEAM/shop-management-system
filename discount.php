<?php
// Start session and connect to the database
session_start();

$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission for adding a promotion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_promotion'])) {
    $discount_name = $_POST['discount_name'];
    $discount_percentage = $_POST['discount_percentage'];
    $product_id = $_POST['product_id'];

    // Validate product_id exists
    $checkProduct = $conn->prepare("SELECT COUNT(*) FROM products WHERE id = :product_id");
    $checkProduct->bindParam(':product_id', $product_id);
    $checkProduct->execute();

    if ($checkProduct->fetchColumn() > 0) {
        // Insert promotion
        $stmt = $conn->prepare("INSERT INTO promotions (discount_name, discount_percentage, product_id) VALUES (:discount_name, :discount_percentage, :product_id)");
        $stmt->bindParam(':discount_name', $discount_name);
        $stmt->bindParam(':discount_percentage', $discount_percentage);
        $stmt->bindParam(':product_id', $product_id);

        try {
            $stmt->execute();
            $success_message = "Promotion added successfully!";
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "Product ID does not exist!";
    }
}

// Fetch promotions
$promotionsQuery = $conn->query("SELECT p.*, pr.name FROM promotions p JOIN products pr ON p.id = pr.id");
$promotions = $promotionsQuery->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotion/Discount Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-container, .promotion-list {
            background-color: white;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>Promotion/Discount Management</h2>

    <div class="form-container">
        <form method="POST" action="">
            <h3>Add Promotion</h3>
            <?php if (isset($success_message)) echo "<p style='color:green;'>$success_message</p>"; ?>
            <?php if (isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>
            <input type="text" name="discount_name" placeholder="Discount Name" required>
            <input type="number" name="discount_percentage" placeholder="Discount Percentage" min="0" max="100" required>
            <select name="product_id" required>
                <option value="">Select Product</option>
                <?php
                // Fetch products to populate the dropdown
                $productsQuery = $conn->query("SELECT id, product_name FROM products");
                $products = $productsQuery->fetchAll(PDO::FETCH_ASSOC);
                foreach ($products as $product) {
                    echo "<option value='{$product['id']}'>{$product['product_name']}</option>";
                }
                ?>
            </select>
            <input type="submit" name="add_promotion" value="Add Promotion">
        </form>
    </div>

    <div class="promotion-list">
        <h3>Existing Promotions</h3>
        <table>
            <tr>
                <th>Promotion ID</th>
                <th>Discount Name</th>
                <th>Discount Percentage</th>
                <th>Product Name</th>
            </tr>
            <?php foreach ($promotions as $promotion): ?>
                <tr>
                    <td><?php echo $promotion['id']; ?></td>
                    <td><?php echo $promotion['discount_name']; ?></td>
                    <td><?php echo $promotion['discount_percentage']; ?>%</td>
                    <td><?php echo $promotion['product_name']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>
