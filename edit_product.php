<?php
// Start the session
session_start();

// Database connection parameters
$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root'; // Update if necessary
$pass = ''; // Update if necessary

// Connect to the database
$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

// Check if a product ID is provided for editing
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch the current product data
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the product doesn't exist, redirect to the product list page
    if (!$product) {
        header("Location: product_list.php");
        exit();
    }
} else {
    // Redirect if no product ID is provided
    header("Location: product_list.php");
    exit();
}

// Handle form submission for updating product details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated form data
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $supplier = $_POST['supplier'];

    // Validate form data (basic validation)
    if (empty($name) || empty($category) || empty($price) || empty($stock_quantity) || empty($supplier)) {
        $error = "All fields are required!";
    } else {
        // Update the product details in the database
        $stmt = $conn->prepare("UPDATE products SET name = :name, category = :category, price = :price, stock_quantity = :stock_quantity, supplier = :supplier WHERE id = :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock_quantity', $stock_quantity);
        $stmt->bindParam(':supplier', $supplier);
        $stmt->bindParam(':id', $product_id);

        if ($stmt->execute()) {
            $success = "<h6>Product updated successfully!</h6>";
        } else {
            $error = "Failed to update the product!";
        }
    }
}

// Fetch all categories for the dropdown
$category_stmt = $conn->prepare("SELECT * FROM categories");
$category_stmt->execute();
$categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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
    <h2>Edit Product</h2>

    <!-- Success or Error Message -->
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php elseif (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Edit Product Form -->
    <form method="POST" action="">
        <label for="name">Product Name:</label> <br>
        <input type="text" id="name" name="name" value="<?php echo $product['name']; ?>" required> <br>


        <label for="category">Category:</label> <br>
        <select id="category" name="category" required> <br>
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['name']; ?>" <?php echo ($category['name'] == $product['category']) ? 'selected' : ''; ?>>
                    <?php echo $category['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
<br>
        <label for="price">Price (in GHS):</label> <br>
        <input type="number" id="price" name="price" step="0.01" value="<?php echo $product['price']; ?>" required> <br>

        <label for="stock_quantity">Stock Quantity:</label> <br>
        <input type="number" id="stock_quantity" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" required> <br>

        <label for="supplier">Supplier:</label> <br>
        <input type="text" id="supplier" name="supplier" value="<?php echo $product['supplier']; ?>" required> <br>


        <button type="submit">Update Product</button>
    </form>
</div>

</body>
</html>
