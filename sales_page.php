<?php
// Start session
session_start();

// Database connection
$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root'; // Update if necessary
$pass = ''; // Update if necessary

// Connect to the database
$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

// Fetch all products for the sale form dropdown
$product_stmt = $conn->prepare("SELECT * FROM products");
$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for creating a new sale
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product'];
    $quantity = $_POST['quantity'];
    $sale_price = $_POST['sale_price'];
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $sale_date = $_POST['sale_date'];

    // Calculate total price
    $total_price = $quantity * $sale_price;

    // Validate form input
    if (!empty($product_id) && !empty($quantity) && !empty($sale_price) && !empty($customer_name) && !empty($sale_date)) {
        // Insert the sale into the sales table
        $stmt = $conn->prepare("INSERT INTO sales (product_id, quantity, sale_price, sale_date, customer_name, customer_email, total_price) 
                                VALUES (:product_id, :quantity, :sale_price, :sale_date, :customer_name, :customer_email, :total_price)");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':sale_price', $sale_price);
        $stmt->bindParam(':sale_date', $sale_date);
        $stmt->bindParam(':customer_name', $customer_name);
        $stmt->bindParam(':customer_email', $customer_email);
        $stmt->bindParam(':total_price', $total_price);

        if ($stmt->execute()) {
            // Redirect to a receipt generation page or show the receipt
            $success = "Sale recorded successfully!";
        } else {
            $error = "Failed to process the sale.";
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
    <title>Create Sale</title>
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="nav.css">
</head>
<body>

<ul>
    <li>   <a href="leave_apply.php">Leave Application <i class="fas fa-users"></i></a></li> 
    <li>  <a href="add_employee.php">Add Employee <i class="fas fa-calendar-alt"></i></a></li> 
    <li> <a href="attendance .php">Attendance <i class="fas fa-money-check-alt"></i></a></li> 
    <li>   <a href="view_emplyee.php">View Employees <i class="fas fa-clock"></i></a></li> 
    <li>  <a href="reporting.php">View Reports <i class="fas fa-chart-line"></i></a></li> 
    <li> <a href="leave.php">Leave Management <i class="fas fa-chart-line"></i></a>
        <li> <a href="payroll.php">Payroll <i class="fas fa-chart-line"></i></a></li> 
        <li> <a href="employee_payroll.php">Employee Payroll <i class="fas fa-chart-line"></i></a><br>
            <li> <a href="employee_attendance.php">employee attendance record <i class="fas fa-chart-line"></i></a><br>

    </ul>
<div class="form-container">
    <h2>Create Sale</h2>

    <!-- Success or Error Message -->
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php elseif (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Sale Form -->
    <form method="POST" action="">
        <label for="product">Product:</label>
        <select id="product" name="product" required>
            <option value="">Select a product</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>">
                    <?php echo $product['name']; ?> (GHS <?php echo $product['price']; ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" min="1" required>

        <label for="sale_price">Sale Price (per unit):</label>
        <input type="number" id="sale_price" name="sale_price" step="0.01" required>

        <label for="customer_name">Customer Name:</label>
        <input type="text" id="customer_name" name="customer_name" required>

        <label for="customer_email">Customer Email:</label>
        <input type="email" id="customer_email" name="customer_email">

        <label for="sale_date">Sale Date:</label>
        <input type="date" id="sale_date" name="sale_date" required>

        <button type="submit">Process Sale</button>
    </form>
</div>

<script>
    // Automatically populate the sale price based on the selected product
    document.getElementById('product').addEventListener('change', function() {
        var selectedProduct = this.options[this.selectedIndex];
        var price = selectedProduct.getAttribute('data-price');
        document.getElementById('sale_price').value = price;
    });
</script>

</body>
</html>
